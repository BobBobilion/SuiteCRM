# Smart Data Import with Mapping & Validation - Implementation Plan

## Overview
Build an intelligent import system that automatically suggests field mappings, validates data, prevents duplicates, provides preview with error correction, and tracks import history with rollback capabilities.

## Architecture Overview

### Core Components
1. **Import Wizard UI** - Multi-step import interface
2. **Mapping Engine** - AI-powered field mapping suggestions
3. **Validation Framework** - Extensible validation rules
4. **Duplicate Detection** - Fuzzy matching algorithms
5. **Preview & Correction** - Interactive data preview
6. **History & Rollback** - Complete audit trail

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Import definitions
CREATE TABLE import_definitions (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    module varchar(50) NOT NULL,
    file_type enum('csv','xlsx','xls','json','xml') NOT NULL,
    mapping_data text, -- JSON field mappings
    validation_rules text, -- JSON validation configuration
    duplicate_handling enum('skip','update','create_new','merge') DEFAULT 'skip',
    created_by char(36),
    is_template tinyint(1) DEFAULT 0,
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Import jobs
CREATE TABLE import_jobs (
    id char(36) PRIMARY KEY,
    definition_id char(36),
    file_name varchar(255),
    file_path varchar(500),
    file_hash varchar(64),
    total_rows int,
    processed_rows int DEFAULT 0,
    successful_rows int DEFAULT 0,
    failed_rows int DEFAULT 0,
    duplicate_rows int DEFAULT 0,
    status enum('pending','mapping','validating','processing','completed','failed','rolled_back') DEFAULT 'pending',
    error_log text,
    mapping_snapshot text, -- JSON snapshot of mappings used
    started_at datetime,
    completed_at datetime,
    created_by char(36),
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (definition_id) REFERENCES import_definitions(id)
);

-- Import job records
CREATE TABLE import_job_records (
    id char(36) PRIMARY KEY,
    job_id char(36) NOT NULL,
    row_number int NOT NULL,
    record_id char(36), -- ID of created/updated record
    action enum('created','updated','skipped','failed') NOT NULL,
    validation_errors text, -- JSON array of errors
    raw_data text, -- JSON of original row data
    processed_data text, -- JSON of processed data
    date_entered datetime,
    FOREIGN KEY (job_id) REFERENCES import_jobs(id),
    INDEX idx_job_row (job_id, row_number)
);

-- Field mapping templates
CREATE TABLE import_mapping_templates (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    module varchar(50) NOT NULL,
    source_type varchar(50), -- e.g., 'salesforce', 'hubspot', 'generic'
    mapping_rules text, -- JSON mapping configuration
    popularity_score int DEFAULT 0, -- Track usage for suggestions
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Validation rule library
CREATE TABLE import_validation_rules (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    rule_type enum('required','format','range','custom','unique','reference') NOT NULL,
    module varchar(50),
    field varchar(100),
    parameters text, -- JSON parameters for the rule
    error_message varchar(500),
    is_system tinyint(1) DEFAULT 0,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0
);
```

### Phase 2: File Processing Engine

#### 2.1 File Parser Factory
Location: `modules/Import/Parsers/`

```php
class FileParserFactory {
    public static function getParser($fileType) {
        switch ($fileType) {
            case 'csv':
                return new CsvParser();
            case 'xlsx':
            case 'xls':
                return new ExcelParser();
            case 'json':
                return new JsonParser();
            case 'xml':
                return new XmlParser();
            default:
                throw new UnsupportedFileTypeException();
        }
    }
}
```

#### 2.2 Smart CSV Parser
Location: `modules/Import/Parsers/CsvParser.php`

```php
class CsvParser implements FileParserInterface {
    private $delimiter;
    private $enclosure;
    private $encoding;
    
    public function detectFormat($filePath) {
        // Auto-detect delimiter, encoding, and quote character
        $sample = file_get_contents($filePath, false, null, 0, 1024);
        
        $this->encoding = $this->detectEncoding($sample);
        $this->delimiter = $this->detectDelimiter($sample);
        $this->enclosure = $this->detectEnclosure($sample);
    }
    
    public function parse($filePath, $options = []) {
        $this->detectFormat($filePath);
        
        $handle = fopen($filePath, 'r');
        $headers = $this->parseHeaders($handle);
        
        // Stream large files to avoid memory issues
        while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure)) !== false) {
            yield array_combine($headers, $row);
        }
        
        fclose($handle);
    }
}
```

### Phase 3: Intelligent Mapping Engine

#### 3.1 Field Mapping Suggester
Location: `modules/Import/Mapping/MappingSuggester.php`

```php
class MappingSuggester {
    private $nlpService;
    private $templateMatcher;
    
    public function suggestMappings($sourceFields, $targetModule) {
        $suggestions = [];
        $moduleFields = $this->getModuleFields($targetModule);
        
        foreach ($sourceFields as $sourceField) {
            $suggestion = $this->findBestMatch($sourceField, $moduleFields);
            $suggestions[$sourceField] = $suggestion;
        }
        
        return $suggestions;
    }
    
    private function findBestMatch($sourceField, $targetFields) {
        $scores = [];
        
        foreach ($targetFields as $targetField) {
            $score = 0;
            
            // 1. Exact match
            if (strcasecmp($sourceField, $targetField['name']) === 0) {
                $score += 100;
            }
            
            // 2. Label match
            if (strcasecmp($sourceField, $targetField['label']) === 0) {
                $score += 90;
            }
            
            // 3. Fuzzy match
            $score += $this->fuzzyMatch($sourceField, $targetField['name']) * 50;
            $score += $this->fuzzyMatch($sourceField, $targetField['label']) * 40;
            
            // 4. Semantic similarity (using NLP)
            $score += $this->nlpService->semanticSimilarity($sourceField, $targetField['label']) * 30;
            
            // 5. Historical mapping data
            $score += $this->getHistoricalMappingScore($sourceField, $targetField['name']) * 20;
            
            $scores[$targetField['name']] = $score;
        }
        
        arsort($scores);
        $bestMatch = array_key_first($scores);
        
        return [
            'field' => $bestMatch,
            'confidence' => $scores[$bestMatch] / 100,
            'alternatives' => array_slice(array_keys($scores), 1, 3)
        ];
    }
}
```

#### 3.2 Machine Learning Integration
Location: `modules/Import/ML/MappingPredictor.php`

```php
class MappingPredictor {
    private $model;
    
    public function __construct() {
        // Load pre-trained model for field mapping
        $this->model = $this->loadModel('mapping_model.pkl');
    }
    
    public function predict($sourceField, $context) {
        $features = $this->extractFeatures($sourceField, $context);
        return $this->model->predict($features);
    }
    
    public function train($historicalMappings) {
        // Retrain model with new mapping data
        $trainingData = $this->prepareTrainingData($historicalMappings);
        $this->model->fit($trainingData['features'], $trainingData['labels']);
        $this->saveModel('mapping_model.pkl');
    }
}
```

### Phase 4: Validation Framework

#### 4.1 Validation Engine
Location: `modules/Import/Validation/ValidationEngine.php`

```php
class ValidationEngine {
    private $validators = [];
    
    public function __construct() {
        $this->registerDefaultValidators();
    }
    
    public function validate($data, $rules, $module) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                $validator = $this->getValidator($rule['type']);
                $result = $validator->validate($data[$field], $rule['parameters'], $module);
                
                if (!$result->isValid()) {
                    $errors[$field][] = $result->getError();
                }
            }
        }
        
        return new ValidationResult($errors);
    }
}
```

#### 4.2 Custom Validators
Location: `modules/Import/Validation/Validators/`

```php
// EmailValidator.php
class EmailValidator implements ValidatorInterface {
    public function validate($value, $parameters = [], $context = null) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ValidationResult::error('Invalid email format');
        }
        
        // Check DNS records if strict mode
        if ($parameters['strict'] ?? false) {
            $domain = substr(strrchr($value, "@"), 1);
            if (!checkdnsrr($domain, 'MX')) {
                return ValidationResult::error('Email domain does not exist');
            }
        }
        
        return ValidationResult::success();
    }
}

// PhoneValidator.php
class PhoneValidator implements ValidatorInterface {
    public function validate($value, $parameters = [], $context = null) {
        // Use libphonenumber for international phone validation
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        
        try {
            $number = $phoneUtil->parse($value, $parameters['defaultCountry'] ?? 'US');
            if (!$phoneUtil->isValidNumber($number)) {
                return ValidationResult::error('Invalid phone number');
            }
        } catch (\Exception $e) {
            return ValidationResult::error('Invalid phone format');
        }
        
        return ValidationResult::success();
    }
}
```

### Phase 5: Duplicate Detection System

#### 5.1 Duplicate Detector
Location: `modules/Import/Duplicate/DuplicateDetector.php`

```php
class DuplicateDetector {
    private $strategies = [];
    
    public function detectDuplicates($record, $module, $config) {
        $potentialDuplicates = [];
        
        // 1. Exact match on key fields
        $exactMatches = $this->findExactMatches($record, $module, $config['key_fields']);
        
        // 2. Fuzzy matching
        if ($config['fuzzy_matching']) {
            $fuzzyMatches = $this->findFuzzyMatches($record, $module, $config);
        }
        
        // 3. Phonetic matching for names
        if ($config['phonetic_matching']) {
            $phoneticMatches = $this->findPhoneticMatches($record, $module);
        }
        
        // 4. Score and rank duplicates
        return $this->rankDuplicates(
            array_merge($exactMatches, $fuzzyMatches ?? [], $phoneticMatches ?? [])
        );
    }
    
    private function findFuzzyMatches($record, $module, $config) {
        $query = new FuzzySearchQuery();
        
        foreach ($config['fuzzy_fields'] as $field) {
            $query->addCondition($field, $record[$field], $config['threshold']);
        }
        
        return $query->execute($module);
    }
}
```

#### 5.2 Fuzzy Matching Algorithms
Location: `modules/Import/Duplicate/Algorithms/`

```php
class FuzzyMatcher {
    public function calculateSimilarity($str1, $str2, $algorithm = 'combined') {
        switch ($algorithm) {
            case 'levenshtein':
                return $this->levenshteinSimilarity($str1, $str2);
            case 'jaro_winkler':
                return $this->jaroWinklerSimilarity($str1, $str2);
            case 'metaphone':
                return $this->metaphoneSimilarity($str1, $str2);
            case 'combined':
                return $this->combinedSimilarity($str1, $str2);
        }
    }
    
    private function combinedSimilarity($str1, $str2) {
        $scores = [
            $this->levenshteinSimilarity($str1, $str2) * 0.4,
            $this->jaroWinklerSimilarity($str1, $str2) * 0.4,
            $this->metaphoneSimilarity($str1, $str2) * 0.2
        ];
        
        return array_sum($scores);
    }
}
```

### Phase 6: Interactive Preview UI

#### 6.1 Import Wizard React App
Location: `modules/Import/javascript/import-wizard/`

```jsx
// Components structure
import-wizard/
├── src/
│   ├── components/
│   │   ├── FileUpload.jsx
│   │   ├── MappingEditor.jsx
│   │   ├── ValidationPreview.jsx
│   │   ├── DuplicateResolver.jsx
│   │   └── ImportProgress.jsx
│   ├── steps/
│   │   ├── UploadStep.jsx
│   │   ├── MappingStep.jsx
│   │   ├── ValidationStep.jsx
│   │   ├── PreviewStep.jsx
│   │   └── ProcessStep.jsx
│   └── services/
│       ├── importApi.js
│       └── validationService.js
```

#### 6.2 Data Preview Grid
```jsx
// PreviewGrid.jsx
const PreviewGrid = ({ data, validationErrors, onCellEdit }) => {
    return (
        <DataGrid
            rows={data}
            columns={columns}
            cellRenderer={({ value, row, column }) => (
                <EditableCell
                    value={value}
                    error={validationErrors[row.id]?.[column.field]}
                    onChange={(newValue) => onCellEdit(row.id, column.field, newValue)}
                />
            )}
            rowClassRules={{
                'has-errors': (params) => hasValidationErrors(params.data),
                'is-duplicate': (params) => params.data.isDuplicate
            }}
        />
    );
};
```

### Phase 7: Import Processing Engine

#### 7.1 Batch Processor
Location: `modules/Import/Engine/BatchProcessor.php`

```php
class BatchProcessor {
    private $batchSize = 100;
    private $progressTracker;
    
    public function processImport($jobId) {
        $job = ImportJob::find($jobId);
        $records = $this->getRecordBatch($job);
        
        while (!empty($records)) {
            DB::beginTransaction();
            
            try {
                foreach ($records as $record) {
                    $this->processRecord($record, $job);
                    $this->progressTracker->increment($job);
                }
                
                DB::commit();
                $this->notifyProgress($job);
                
            } catch (\Exception $e) {
                DB::rollback();
                $this->handleBatchError($e, $job, $records);
            }
            
            $records = $this->getRecordBatch($job);
        }
    }
}
```

#### 7.2 Real-time Progress Updates
Location: `modules/Import/Services/ProgressService.php`

```php
class ProgressService {
    private $websocket;
    
    public function trackProgress($jobId) {
        $job = ImportJob::find($jobId);
        
        $progress = [
            'total' => $job->total_rows,
            'processed' => $job->processed_rows,
            'successful' => $job->successful_rows,
            'failed' => $job->failed_rows,
            'percentage' => ($job->processed_rows / $job->total_rows) * 100,
            'estimatedTimeRemaining' => $this->estimateTimeRemaining($job)
        ];
        
        // Send via WebSocket for real-time updates
        $this->websocket->send("import.progress.{$jobId}", $progress);
        
        return $progress;
    }
}
```

### Phase 8: History & Rollback System

#### 8.1 Import History Manager
Location: `modules/Import/History/HistoryManager.php`

```php
class HistoryManager {
    public function createSnapshot($jobId) {
        $job = ImportJob::find($jobId);
        $records = ImportJobRecord::where('job_id', $jobId)
            ->where('action', 'created')
            ->get();
        
        foreach ($records as $record) {
            $snapshot = new ImportSnapshot();
            $snapshot->job_id = $jobId;
            $snapshot->record_id = $record->record_id;
            $snapshot->module = $job->definition->module;
            $snapshot->data = $this->captureRecordState($record->record_id);
            $snapshot->save();
        }
    }
}
```

#### 8.2 Rollback Engine
Location: `modules/Import/Rollback/RollbackEngine.php`

```php
class RollbackEngine {
    public function rollbackImport($jobId) {
        $job = ImportJob::find($jobId);
        
        if ($job->status === 'rolled_back') {
            throw new AlreadyRolledBackException();
        }
        
        DB::beginTransaction();
        
        try {
            // 1. Delete created records
            $this->deleteCreatedRecords($jobId);
            
            // 2. Restore updated records
            $this->restoreUpdatedRecords($jobId);
            
            // 3. Update job status
            $job->status = 'rolled_back';
            $job->save();
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            throw new RollbackFailedException($e->getMessage());
        }
    }
}
```

### Phase 9: Testing Strategy

#### 9.1 Unit Tests
Location: `tests/unit/modules/Import/`

**Test Coverage:**
- File parser detection and parsing
- Mapping suggestion accuracy
- Validation rule execution
- Duplicate detection algorithms
- Progress tracking accuracy

#### 9.2 Integration Tests
**Test Scenarios:**
1. **Large File Import**
   - Import 100,000+ records
   - Monitor memory usage
   - Verify batch processing

2. **Complex Mapping**
   - Multi-level JSON import
   - Custom field transformations
   - Relationship imports

3. **Error Recovery**
   - Network failure during import
   - Invalid data handling
   - Partial rollback scenarios

### Phase 10: Performance Optimization

#### 10.1 Optimization Strategies
- Stream file reading for large files
- Batch database operations
- Implement job queuing for background processing
- Cache field mappings and validation rules
- Use database indexes for duplicate detection

#### 10.2 Scalability Considerations
- Horizontal scaling with job distribution
- Chunked file uploads for large files
- Progressive validation during mapping
- Lazy loading for preview data

## Development Timeline

### Week 1-2: Core Infrastructure
- Database schema creation
- Basic file parser implementation
- Module structure setup

### Week 3-4: Mapping Engine
- Field mapping suggester
- Template system
- ML integration preparation

### Week 5-6: Validation Framework
- Validation engine
- Custom validators
- Rule configuration UI

### Week 7-8: Duplicate Detection
- Detection algorithms
- Fuzzy matching implementation
- Resolution interface

### Week 9-10: Import Wizard UI
- React application setup
- Step-by-step wizard
- Preview grid implementation

### Week 11-12: Processing & History
- Batch processor
- Progress tracking
- Rollback system

### Week 13-14: Testing & Optimization
- Performance testing
- Large file handling
- Final polish

## Technical Dependencies
- React 17+ for UI
- AG-Grid or similar for data preview
- PHP ML library for predictions
- Elasticsearch for fuzzy matching (optional)
- WebSockets for real-time updates

## Success Metrics
1. 90% accuracy in field mapping suggestions
2. Support for 1M+ row imports
3. <2 second validation for 1000 rows
4. 95% duplicate detection accuracy
5. Complete rollback capability