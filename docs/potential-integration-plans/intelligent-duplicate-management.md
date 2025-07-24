# Intelligent Duplicate Management System - Implementation Plan

## Overview
Build a comprehensive duplicate detection and management system using fuzzy matching algorithms, bulk merge operations, prevention rules, and analytics to maintain data quality across all CRM modules.

## Architecture Overview

### Core Components
1. **Detection Engine** - Multi-algorithm duplicate detection
2. **Matching Algorithms** - Fuzzy, phonetic, and ML-based matching
3. **Merge Interface** - Intelligent conflict resolution UI
4. **Prevention System** - Real-time duplicate blocking
5. **Analytics Dashboard** - Duplicate trends and insights
6. **Bulk Operations** - Mass merge and cleanup tools

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Duplicate detection rules
CREATE TABLE duplicate_rules (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    module varchar(50) NOT NULL,
    status enum('active','inactive','testing') DEFAULT 'active',
    rule_type enum('exact','fuzzy','phonetic','machine_learning','custom') DEFAULT 'fuzzy',
    match_criteria text, -- JSON field configurations
    threshold_score decimal(3,2) DEFAULT 0.80, -- 0-1 similarity threshold
    action enum('block','warn','flag','none') DEFAULT 'warn',
    priority int DEFAULT 0,
    created_by char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    INDEX idx_module_status (module, status)
);

-- Duplicate detection results
CREATE TABLE duplicate_results (
    id char(36) PRIMARY KEY,
    rule_id char(36) NOT NULL,
    module varchar(50) NOT NULL,
    record1_id char(36) NOT NULL,
    record2_id char(36) NOT NULL,
    match_score decimal(3,2) NOT NULL,
    match_details text, -- JSON matching field details
    status enum('pending','merged','ignored','false_positive') DEFAULT 'pending',
    merge_winner_id char(36),
    reviewed_by char(36),
    review_date datetime,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (rule_id) REFERENCES duplicate_rules(id),
    INDEX idx_status (status),
    INDEX idx_module_records (module, record1_id, record2_id)
);

-- Merge history
CREATE TABLE merge_history (
    id char(36) PRIMARY KEY,
    module varchar(50) NOT NULL,
    master_record_id char(36) NOT NULL,
    merged_record_id char(36) NOT NULL,
    merge_data text, -- JSON of merged fields
    field_conflicts text, -- JSON of conflicting fields
    resolution_data text, -- JSON of how conflicts were resolved
    performed_by char(36),
    merge_type enum('manual','automatic','bulk') DEFAULT 'manual',
    date_entered datetime,
    INDEX idx_master (master_record_id),
    INDEX idx_merged (merged_record_id)
);

-- Field matching configuration
CREATE TABLE duplicate_field_config (
    id char(36) PRIMARY KEY,
    rule_id char(36) NOT NULL,
    field_name varchar(100) NOT NULL,
    match_type enum('exact','fuzzy','phonetic','normalized','custom') NOT NULL,
    weight decimal(3,2) DEFAULT 1.00, -- Field importance weight
    fuzzy_algorithm varchar(50), -- levenshtein, jaro_winkler, etc.
    normalization_rules text, -- JSON normalization config
    custom_function varchar(255),
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (rule_id) REFERENCES duplicate_rules(id)
);

-- Duplicate prevention cache
CREATE TABLE duplicate_prevention_cache (
    id char(36) PRIMARY KEY,
    module varchar(50) NOT NULL,
    field_hash varchar(64) NOT NULL, -- Hash of key fields
    record_id char(36) NOT NULL,
    field_values text, -- JSON of hashed field values
    date_entered datetime,
    date_modified datetime,
    UNIQUE KEY unique_hash (module, field_hash),
    INDEX idx_record (record_id)
);

-- ML training data
CREATE TABLE duplicate_ml_training (
    id char(36) PRIMARY KEY,
    module varchar(50) NOT NULL,
    record1_data text NOT NULL, -- JSON record data
    record2_data text NOT NULL,
    is_duplicate tinyint(1) NOT NULL,
    confidence_score decimal(3,2),
    verified_by char(36),
    feature_vector text, -- JSON ML features
    date_entered datetime,
    INDEX idx_module (module)
);

-- Duplicate analytics
CREATE TABLE duplicate_analytics (
    id char(36) PRIMARY KEY,
    module varchar(50) NOT NULL,
    metric_date date NOT NULL,
    total_records int,
    duplicate_count int,
    merge_count int,
    prevention_count int,
    avg_match_score decimal(3,2),
    top_duplicate_sources text, -- JSON source analysis
    date_entered datetime,
    UNIQUE KEY unique_module_date (module, metric_date)
);
```

### Phase 2: Detection Engine

#### 2.1 Core Detection Service
Location: `modules/DuplicateCheck/Engine/DetectionEngine.php`

```php
class DuplicateDetectionEngine {
    private $algorithms = [];
    private $rules = [];
    private $cache;
    
    public function __construct() {
        $this->registerAlgorithms();
        $this->cache = new DuplicateCache();
    }
    
    public function detectDuplicates($module, $record, $mode = 'full') {
        $rules = $this->getActiveRules($module);
        $candidates = [];
        
        foreach ($rules as $rule) {
            $matches = $this->applyRule($rule, $record, $mode);
            $candidates = array_merge($candidates, $matches);
        }
        
        // De-duplicate and sort by score
        $results = $this->consolidateResults($candidates);
        
        // Apply ML post-processing if available
        if ($this->hasMLModel($module)) {
            $results = $this->applyMLScoring($results, $record);
        }
        
        return $results;
    }
    
    private function applyRule($rule, $record, $mode) {
        $algorithm = $this->algorithms[$rule->rule_type];
        $candidates = [];
        
        // Get potential matches based on blocking strategy
        $blocks = $this->getBlockingCandidates($rule, $record);
        
        foreach ($blocks as $candidate) {
            $score = $algorithm->calculateSimilarity($record, $candidate, $rule);
            
            if ($score >= $rule->threshold_score) {
                $candidates[] = [
                    'record' => $candidate,
                    'score' => $score,
                    'rule' => $rule,
                    'match_details' => $algorithm->getMatchDetails()
                ];
            }
        }
        
        return $candidates;
    }
    
    private function getBlockingCandidates($rule, $record) {
        // Use blocking to reduce comparison space
        $blocks = [];
        
        foreach ($rule->getBlockingFields() as $field) {
            $value = $record[$field] ?? '';
            if (empty($value)) continue;
            
            // Generate blocking keys
            $blockKeys = $this->generateBlockKeys($field, $value);
            
            foreach ($blockKeys as $key) {
                $blocks = array_merge($blocks, $this->cache->getBlock($key));
            }
        }
        
        return array_unique($blocks, SORT_REGULAR);
    }
}
```

#### 2.2 Fuzzy Matching Algorithm
Location: `modules/DuplicateCheck/Algorithms/FuzzyMatcher.php`

```php
class FuzzyMatcher implements MatchingAlgorithmInterface {
    private $fieldScores = [];
    
    public function calculateSimilarity($record1, $record2, $rule) {
        $fieldConfigs = $rule->getFieldConfigurations();
        $totalWeight = 0;
        $weightedScore = 0;
        
        foreach ($fieldConfigs as $config) {
            $field = $config->field_name;
            $value1 = $this->normalizeValue($record1[$field] ?? '', $config);
            $value2 = $this->normalizeValue($record2[$field] ?? '', $config);
            
            if (empty($value1) && empty($value2)) {
                continue;
            }
            
            $fieldScore = $this->calculateFieldScore($value1, $value2, $config);
            $this->fieldScores[$field] = $fieldScore;
            
            $weightedScore += $fieldScore * $config->weight;
            $totalWeight += $config->weight;
        }
        
        return $totalWeight > 0 ? $weightedScore / $totalWeight : 0;
    }
    
    private function calculateFieldScore($value1, $value2, $config) {
        switch ($config->fuzzy_algorithm) {
            case 'levenshtein':
                return $this->levenshteinSimilarity($value1, $value2);
                
            case 'jaro_winkler':
                return $this->jaroWinklerSimilarity($value1, $value2);
                
            case 'token_sort':
                return $this->tokenSortSimilarity($value1, $value2);
                
            case 'ngram':
                return $this->ngramSimilarity($value1, $value2, 3);
                
            default:
                return $this->combinedSimilarity($value1, $value2);
        }
    }
    
    private function combinedSimilarity($str1, $str2) {
        // Combine multiple algorithms for better accuracy
        $scores = [
            $this->levenshteinSimilarity($str1, $str2) * 0.3,
            $this->jaroWinklerSimilarity($str1, $str2) * 0.4,
            $this->tokenSortSimilarity($str1, $str2) * 0.3
        ];
        
        return array_sum($scores);
    }
    
    private function normalizeValue($value, $config) {
        $normalized = $value;
        
        // Apply normalization rules
        if ($config->normalization_rules) {
            $rules = json_decode($config->normalization_rules, true);
            
            if ($rules['lowercase'] ?? false) {
                $normalized = strtolower($normalized);
            }
            
            if ($rules['remove_punctuation'] ?? false) {
                $normalized = preg_replace('/[^\w\s]/', '', $normalized);
            }
            
            if ($rules['remove_stopwords'] ?? false) {
                $normalized = $this->removeStopwords($normalized);
            }
            
            if ($rules['standardize_company'] ?? false) {
                $normalized = $this->standardizeCompanyName($normalized);
            }
        }
        
        return trim($normalized);
    }
}
```

#### 2.3 Phonetic Matching
Location: `modules/DuplicateCheck/Algorithms/PhoneticMatcher.php`

```php
class PhoneticMatcher implements MatchingAlgorithmInterface {
    private $algorithms = [];
    
    public function __construct() {
        $this->algorithms = [
            'soundex' => new SoundexAlgorithm(),
            'metaphone' => new MetaphoneAlgorithm(),
            'double_metaphone' => new DoubleMetaphoneAlgorithm(),
            'nysiis' => new NYSIISAlgorithm()
        ];
    }
    
    public function calculateSimilarity($record1, $record2, $rule) {
        $phoneticFields = $this->getPhoneticFields($rule);
        $scores = [];
        
        foreach ($phoneticFields as $field => $algorithm) {
            $value1 = $record1[$field] ?? '';
            $value2 = $record2[$field] ?? '';
            
            if (empty($value1) || empty($value2)) {
                continue;
            }
            
            $phonetic1 = $this->algorithms[$algorithm]->encode($value1);
            $phonetic2 = $this->algorithms[$algorithm]->encode($value2);
            
            $scores[$field] = $phonetic1 === $phonetic2 ? 1.0 : 0.0;
        }
        
        return empty($scores) ? 0 : array_sum($scores) / count($scores);
    }
}
```

### Phase 3: Machine Learning Integration

#### 3.1 ML Model Training
Location: `modules/DuplicateCheck/ML/ModelTrainer.php`

```php
class DuplicateMLTrainer {
    private $featureExtractor;
    private $model;
    
    public function trainModel($module) {
        // Load training data
        $trainingData = $this->loadTrainingData($module);
        
        // Extract features
        $features = [];
        $labels = [];
        
        foreach ($trainingData as $pair) {
            $features[] = $this->featureExtractor->extract(
                $pair['record1_data'],
                $pair['record2_data']
            );
            $labels[] = $pair['is_duplicate'] ? 1 : 0;
        }
        
        // Train model
        $this->model = new GradientBoostingClassifier();
        $this->model->fit($features, $labels);
        
        // Evaluate model
        $metrics = $this->evaluateModel($features, $labels);
        
        // Save model if performance is good
        if ($metrics['accuracy'] > 0.9) {
            $this->saveModel($module);
        }
        
        return $metrics;
    }
    
    public function extractFeatures($record1, $record2) {
        $features = [];
        
        // String similarity features
        $features[] = $this->getStringSimilarity($record1['name'], $record2['name']);
        $features[] = $this->getStringSimilarity($record1['email'], $record2['email']);
        
        // Phonetic similarity
        $features[] = $this->getPhoneticSimilarity($record1['name'], $record2['name']);
        
        // Address similarity
        $features[] = $this->getAddressSimilarity(
            $this->getAddress($record1),
            $this->getAddress($record2)
        );
        
        // Numeric features
        $features[] = $this->getPhoneMatchScore($record1['phone'], $record2['phone']);
        
        // Date proximity
        $features[] = $this->getDateProximity($record1['date_entered'], $record2['date_entered']);
        
        // Domain similarity for emails
        $features[] = $this->getDomainSimilarity($record1['email'], $record2['email']);
        
        return $features;
    }
}
```

### Phase 4: Merge Interface

#### 4.1 Intelligent Merge UI
Location: `modules/DuplicateCheck/javascript/MergeInterface/`

```jsx
const DuplicateMergeInterface = ({ duplicates, module }) => {
    const [selectedMaster, setSelectedMaster] = useState(null);
    const [mergeStrategy, setMergeStrategy] = useState({});
    const [preview, setPreview] = useState(null);
    
    const generateMergePreview = useCallback(() => {
        const preview = {
            master: selectedMaster,
            fields: {}
        };
        
        // For each field, determine the value to keep
        const allFields = getAllFieldsFromRecords(duplicates);
        
        allFields.forEach(field => {
            const values = duplicates.map(dup => ({
                record: dup,
                value: dup[field],
                metadata: getFieldMetadata(dup, field)
            }));
            
            preview.fields[field] = {
                values,
                selected: mergeStrategy[field] || autoSelectBestValue(values),
                hasConflict: hasConflict(values)
            };
        });
        
        setPreview(preview);
    }, [duplicates, selectedMaster, mergeStrategy]);
    
    return (
        <div className="merge-interface">
            <MasterRecordSelector
                records={duplicates}
                selected={selectedMaster}
                onSelect={setSelectedMaster}
            />
            
            <div className="merge-comparison">
                <MergeFieldTable
                    preview={preview}
                    onFieldStrategyChange={(field, recordId) => {
                        setMergeStrategy({ ...mergeStrategy, [field]: recordId });
                    }}
                    highlightConflicts={true}
                />
                
                <MergePreview
                    preview={preview}
                    module={module}
                />
            </div>
            
            <MergeActions
                onMerge={() => executeMerge(preview)}
                onCancel={() => cancelMerge()}
                canMerge={selectedMaster !== null}
            />
        </div>
    );
};

const MergeFieldTable = ({ preview, onFieldStrategyChange, highlightConflicts }) => {
    return (
        <table className="merge-field-table">
            <thead>
                <tr>
                    <th>Field</th>
                    {preview.master && preview.fields && 
                        Object.values(preview.fields)[0]?.values.map((v, idx) => (
                            <th key={idx}>
                                Record {idx + 1}
                                {v.record.id === preview.master.id && ' (Master)'}
                            </th>
                        ))
                    }
                    <th>Keep Value From</th>
                </tr>
            </thead>
            <tbody>
                {Object.entries(preview?.fields || {}).map(([field, data]) => (
                    <FieldComparisonRow
                        key={field}
                        field={field}
                        data={data}
                        highlight={highlightConflicts && data.hasConflict}
                        onSelectValue={(recordId) => onFieldStrategyChange(field, recordId)}
                    />
                ))}
            </tbody>
        </table>
    );
};
```

#### 4.2 Conflict Resolution Engine
Location: `modules/DuplicateCheck/Services/ConflictResolver.php`

```php
class ConflictResolver {
    private $rules = [];
    
    public function resolveConflicts($masterRecord, $duplicates, $strategy) {
        $resolved = $masterRecord;
        
        foreach ($strategy as $field => $sourceRecordId) {
            if ($sourceRecordId === 'auto') {
                $resolved[$field] = $this->autoResolve($field, $masterRecord, $duplicates);
            } elseif ($sourceRecordId === 'combine') {
                $resolved[$field] = $this->combineValues($field, $masterRecord, $duplicates);
            } else {
                $sourceRecord = $this->findRecord($sourceRecordId, $duplicates);
                $resolved[$field] = $sourceRecord[$field];
            }
        }
        
        return $resolved;
    }
    
    private function autoResolve($field, $master, $duplicates) {
        $allRecords = array_merge([$master], $duplicates);
        $fieldType = $this->getFieldType($field);
        
        switch ($fieldType) {
            case 'email':
                return $this->selectBestEmail($allRecords, $field);
                
            case 'phone':
                return $this->selectBestPhone($allRecords, $field);
                
            case 'address':
                return $this->selectMostCompleteAddress($allRecords, $field);
                
            case 'date':
                return $this->selectMostRecentDate($allRecords, $field);
                
            case 'text':
                return $this->selectLongestText($allRecords, $field);
                
            default:
                return $this->selectMostFrequent($allRecords, $field);
        }
    }
    
    private function combineValues($field, $master, $duplicates) {
        $allRecords = array_merge([$master], $duplicates);
        $values = array_filter(array_column($allRecords, $field));
        
        $fieldType = $this->getFieldType($field);
        
        switch ($fieldType) {
            case 'multiselect':
                return array_unique(array_merge(...$values));
                
            case 'text':
                return $this->mergeTextFields($values);
                
            case 'tags':
                return array_unique(array_merge(...$values));
                
            default:
                return implode(', ', array_unique($values));
        }
    }
}
```

### Phase 5: Real-time Prevention

#### 5.1 Duplicate Prevention Service
Location: `modules/DuplicateCheck/Prevention/PreventionService.php`

```php
class DuplicatePreventionService {
    private $cache;
    private $detector;
    
    public function checkForDuplicates($module, $data, $excludeId = null) {
        // Quick cache check first
        $cacheKey = $this->generateCacheKey($module, $data);
        $cachedResult = $this->cache->get($cacheKey);
        
        if ($cachedResult !== null) {
            return $cachedResult;
        }
        
        // Real-time detection
        $duplicates = $this->detector->detectDuplicates($module, $data, 'quick');
        
        // Filter out the current record if updating
        if ($excludeId) {
            $duplicates = array_filter($duplicates, function($dup) use ($excludeId) {
                return $dup['record']['id'] !== $excludeId;
            });
        }
        
        // Cache result
        $this->cache->set($cacheKey, $duplicates, 300); // 5 minute cache
        
        return $duplicates;
    }
    
    public function enforceUniqueness($module, $data, $rules) {
        foreach ($rules as $rule) {
            if ($rule->action === 'block') {
                $duplicates = $this->checkForDuplicates($module, $data);
                
                if (!empty($duplicates)) {
                    throw new DuplicateBlockedException(
                        'Duplicate record detected',
                        $duplicates
                    );
                }
            }
        }
        
        return true;
    }
}
```

#### 5.2 Real-time Detection UI
Location: `modules/DuplicateCheck/javascript/RealTimeDetection.jsx`

```jsx
const DuplicateDetectionWidget = ({ module, formData, onChange }) => {
    const [duplicates, setDuplicates] = useState([]);
    const [checking, setChecking] = useState(false);
    const [showDetails, setShowDetails] = useState(false);
    
    // Debounced duplicate check
    const checkDuplicates = useDebounce(async (data) => {
        setChecking(true);
        
        try {
            const response = await fetch('/api/v8/duplicates/check', {
                method: 'POST',
                body: JSON.stringify({ module, data })
            });
            
            const result = await response.json();
            setDuplicates(result.duplicates);
            
            if (result.action === 'block' && result.duplicates.length > 0) {
                onChange({ ...formData, _blocked: true });
            }
        } finally {
            setChecking(false);
        }
    }, 500);
    
    useEffect(() => {
        if (formData && !formData._skipDuplicateCheck) {
            checkDuplicates(formData);
        }
    }, [formData]);
    
    if (duplicates.length === 0) {
        return null;
    }
    
    return (
        <div className="duplicate-detection-widget">
            <Alert type="warning" icon="exclamation-triangle">
                <strong>{duplicates.length} potential duplicate(s) found</strong>
                
                <div className="duplicate-list">
                    {duplicates.slice(0, 3).map(dup => (
                        <DuplicateItem
                            key={dup.record.id}
                            duplicate={dup}
                            onView={() => window.open(`/index.php?module=${module}&action=DetailView&record=${dup.record.id}`)}
                            onMerge={() => initiateMerge(dup.record.id)}
                        />
                    ))}
                </div>
                
                {duplicates.length > 3 && (
                    <Button size="small" onClick={() => setShowDetails(true)}>
                        View all {duplicates.length} duplicates
                    </Button>
                )}
            </Alert>
            
            {showDetails && (
                <DuplicateDetailsModal
                    duplicates={duplicates}
                    onClose={() => setShowDetails(false)}
                />
            )}
        </div>
    );
};
```

### Phase 6: Bulk Operations

#### 6.1 Bulk Merge Engine
Location: `modules/DuplicateCheck/Bulk/BulkMergeEngine.php`

```php
class BulkMergeEngine {
    private $batchSize = 100;
    private $progressTracker;
    
    public function executeBulkMerge($module, $mergeGroups, $options = []) {
        $this->progressTracker = new ProgressTracker();
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        // Process in batches
        $batches = array_chunk($mergeGroups, $this->batchSize);
        
        foreach ($batches as $batchIndex => $batch) {
            DB::beginTransaction();
            
            try {
                foreach ($batch as $group) {
                    $result = $this->mergeGroup($module, $group, $options);
                    
                    if ($result['success']) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        $results['errors'][] = $result['error'];
                    }
                    
                    $this->progressTracker->update($batchIndex * $this->batchSize + $results['success']);
                }
                
                DB::commit();
                
            } catch (\Exception $e) {
                DB::rollback();
                $results['errors'][] = "Batch {$batchIndex} failed: " . $e->getMessage();
            }
        }
        
        return $results;
    }
    
    private function mergeGroup($module, $group, $options) {
        try {
            // Determine master record
            $master = $this->selectMasterRecord($group, $options['master_selection'] ?? 'newest');
            
            // Merge each duplicate into master
            foreach ($group as $duplicate) {
                if ($duplicate['id'] === $master['id']) continue;
                
                $this->mergeSingleRecord($module, $master, $duplicate, $options);
            }
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
```

### Phase 7: Analytics Dashboard

#### 7.1 Duplicate Analytics Service
Location: `modules/DuplicateCheck/Analytics/AnalyticsService.php`

```php
class DuplicateAnalyticsService {
    public function generateAnalytics($module, $dateRange) {
        $analytics = [
            'overview' => $this->getOverviewMetrics($module, $dateRange),
            'trends' => $this->getTrendData($module, $dateRange),
            'sources' => $this->analyzeDuplicateSources($module, $dateRange),
            'merge_activity' => $this->getMergeActivity($module, $dateRange),
            'prevention_stats' => $this->getPreventionStats($module, $dateRange)
        ];
        
        // Store in analytics table
        $this->storeAnalytics($module, $analytics);
        
        return $analytics;
    }
    
    private function analyzeDuplicateSources($module, $dateRange) {
        $sources = [];
        
        // Analyze by creation source
        $sourceAnalysis = DB::table('duplicate_results')
            ->select('match_details')
            ->where('module', $module)
            ->whereBetween('date_entered', $dateRange)
            ->get();
            
        foreach ($sourceAnalysis as $result) {
            $details = json_decode($result->match_details, true);
            
            // Identify patterns
            if ($this->isImportDuplicate($details)) {
                $sources['import']++;
            } elseif ($this->isManualEntryDuplicate($details)) {
                $sources['manual_entry']++;
            } elseif ($this->isIntegrationDuplicate($details)) {
                $sources['integration']++;
            }
        }
        
        return $sources;
    }
}
```

#### 7.2 Analytics Dashboard UI
Location: `modules/DuplicateCheck/javascript/Analytics/DuplicateAnalyticsDashboard.jsx`

```jsx
const DuplicateAnalyticsDashboard = () => {
    const [metrics, setMetrics] = useState({});
    const [selectedModule, setSelectedModule] = useState('all');
    const [dateRange, setDateRange] = useState('last_30_days');
    
    return (
        <div className="duplicate-analytics-dashboard">
            <DashboardHeader>
                <ModuleSelector
                    value={selectedModule}
                    onChange={setSelectedModule}
                    includeAll={true}
                />
                <DateRangeSelector
                    value={dateRange}
                    onChange={setDateRange}
                />
            </DashboardHeader>
            
            <MetricsRow>
                <MetricCard
                    title="Total Duplicates"
                    value={metrics.totalDuplicates}
                    trend={metrics.duplicateTrend}
                    icon="copy"
                />
                <MetricCard
                    title="Merge Rate"
                    value={`${metrics.mergeRate}%`}
                    subtitle="Of detected duplicates"
                    icon="code-branch"
                />
                <MetricCard
                    title="Prevention Success"
                    value={`${metrics.preventionRate}%`}
                    subtitle="Blocked at creation"
                    icon="shield-check"
                />
                <MetricCard
                    title="Data Quality Score"
                    value={metrics.qualityScore}
                    format="score"
                    icon="award"
                />
            </MetricsRow>
            
            <ChartsRow>
                <DuplicateTrendChart
                    data={metrics.trendData}
                    title="Duplicate Detection Trend"
                />
                <DuplicateSourcePieChart
                    data={metrics.sourceData}
                    title="Duplicate Sources"
                />
            </ChartsRow>
            
            <ModuleDuplicateTable
                modules={metrics.moduleBreakdown}
                onModuleClick={(module) => navigateToModule(module)}
            />
            
            <RecentMergeActivity
                activities={metrics.recentMerges}
                limit={10}
            />
        </div>
    );
};
```

### Phase 8: Testing Strategy

#### 8.1 Algorithm Testing
Location: `tests/unit/modules/DuplicateCheck/`

```php
class DuplicateAlgorithmTest extends TestCase {
    public function testFuzzyNameMatching() {
        $matcher = new FuzzyMatcher();
        
        $testCases = [
            ['John Smith', 'John Smith', 1.0],
            ['John Smith', 'Smith, John', 0.85],
            ['IBM Corporation', 'I.B.M. Corp', 0.9],
            ['McDonald\'s', 'McDonalds', 0.95]
        ];
        
        foreach ($testCases as [$name1, $name2, $expectedScore]) {
            $score = $matcher->calculateSimilarity(
                ['name' => $name1],
                ['name' => $name2],
                $this->createRule('fuzzy')
            );
            
            $this->assertEqualsWithDelta($expectedScore, $score, 0.05);
        }
    }
    
    public function testPhoneticMatching() {
        $matcher = new PhoneticMatcher();
        
        $testCases = [
            ['Smith', 'Smythe', true],
            ['Johnson', 'Jonson', true],
            ['Catherine', 'Katherine', true]
        ];
        
        foreach ($testCases as [$name1, $name2, $shouldMatch]) {
            $score = $matcher->calculateSimilarity(
                ['last_name' => $name1],
                ['last_name' => $name2],
                $this->createRule('phonetic')
            );
            
            if ($shouldMatch) {
                $this->assertGreaterThan(0.8, $score);
            } else {
                $this->assertLessThan(0.5, $score);
            }
        }
    }
}
```

### Phase 9: Performance Optimization

#### 9.1 Blocking and Indexing
Location: `modules/DuplicateCheck/Optimization/BlockingStrategy.php`

```php
class BlockingStrategy {
    private $indexManager;
    
    public function createBlockingIndices($module) {
        $fields = $this->getBlockingFields($module);
        
        foreach ($fields as $field) {
            // Create standard index
            $this->indexManager->createIndex($module, $field);
            
            // Create trigram index for fuzzy matching
            $this->indexManager->createTrigramIndex($module, $field);
            
            // Create phonetic index
            $this->indexManager->createPhoneticIndex($module, $field);
        }
    }
    
    public function generateBlockKeys($field, $value) {
        $keys = [];
        
        // Exact blocking
        $keys[] = $this->normalizeForBlocking($value);
        
        // Prefix blocking
        if (strlen($value) > 3) {
            $keys[] = substr($this->normalizeForBlocking($value), 0, 3);
        }
        
        // Soundex blocking
        $keys[] = soundex($value);
        
        // Token-based blocking
        $tokens = explode(' ', $value);
        foreach ($tokens as $token) {
            if (strlen($token) > 2) {
                $keys[] = $this->normalizeForBlocking($token);
            }
        }
        
        return array_unique($keys);
    }
}
```

## Development Timeline

### Week 1-2: Core Infrastructure
- Database schema setup
- Basic detection engine
- Rule configuration system

### Week 3-4: Matching Algorithms
- Fuzzy matching implementation
- Phonetic algorithms
- Normalization rules

### Week 5-6: Machine Learning
- Feature extraction
- Model training pipeline
- ML integration

### Week 7-8: Merge Interface
- Comparison UI
- Conflict resolution
- Merge execution

### Week 9-10: Prevention System
- Real-time detection
- Blocking rules
- Cache implementation

### Week 11-12: Bulk Operations
- Bulk merge engine
- Progress tracking
- Error handling

### Week 13-14: Analytics & Polish
- Analytics dashboard
- Performance optimization
- Comprehensive testing

## Technical Dependencies
- PHP ML library for machine learning
- Elasticsearch for efficient searching
- React for interactive UI
- Redis for caching
- Background job processing

## Success Metrics
1. 95% duplicate detection accuracy
2. <500ms real-time detection
3. 80% reduction in duplicate records
4. 90% user satisfaction with merge UI
5. Support for 1M+ record datasets