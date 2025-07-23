# Advanced Reporting Engine with Scheduled Reports - Implementation Plan

## Overview
Build a powerful drag-and-drop report builder with cross-module data joins, calculated fields, conditional formatting, scheduled delivery, and report subscriptions for comprehensive business intelligence.

## Architecture Overview

### Core Components
1. **Visual Report Builder** - Drag-and-drop interface for report creation
2. **Query Engine** - Advanced SQL generation with joins and aggregations  
3. **Calculation Engine** - Custom formulas and calculated fields
4. **Formatting Engine** - Conditional formatting and styling
5. **Scheduler Service** - Automated report generation and delivery
6. **Distribution System** - Email, dashboard, and export delivery

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Report definitions
CREATE TABLE reports_advanced (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    description text,
    report_type enum('tabular','summary','matrix','chart','composite') DEFAULT 'tabular',
    primary_module varchar(50) NOT NULL,
    query_definition text, -- JSON query structure
    layout_definition text, -- JSON layout configuration
    filters text, -- JSON filter definitions
    calculated_fields text, -- JSON calculated field definitions
    formatting_rules text, -- JSON conditional formatting
    chart_config text, -- JSON chart configuration
    is_published tinyint(1) DEFAULT 0,
    created_by char(36),
    assigned_user_id char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    INDEX idx_module (primary_module),
    INDEX idx_type (report_type)
);

-- Report data sources (for joins)
CREATE TABLE report_data_sources (
    id char(36) PRIMARY KEY,
    report_id char(36) NOT NULL,
    module_name varchar(50) NOT NULL,
    alias varchar(50),
    join_type enum('inner','left','right','cross') DEFAULT 'left',
    join_conditions text, -- JSON join conditions
    fields_selected text, -- JSON array of fields
    sort_order int DEFAULT 0,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (report_id) REFERENCES reports_advanced(id)
);

-- Calculated fields library
CREATE TABLE report_calculated_fields (
    id char(36) PRIMARY KEY,
    report_id char(36),
    field_name varchar(100) NOT NULL,
    display_name varchar(255),
    formula text NOT NULL,
    return_type enum('number','string','date','boolean') DEFAULT 'string',
    format_string varchar(100),
    is_global tinyint(1) DEFAULT 0, -- Available across reports
    dependencies text, -- JSON array of dependent fields
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (report_id) REFERENCES reports_advanced(id)
);

-- Report schedules
CREATE TABLE report_schedules (
    id char(36) PRIMARY KEY,
    report_id char(36) NOT NULL,
    schedule_name varchar(255),
    schedule_type enum('once','hourly','daily','weekly','monthly','custom') NOT NULL,
    cron_expression varchar(100), -- For custom schedules
    time_of_day time,
    day_of_week varchar(20), -- For weekly
    day_of_month int, -- For monthly
    timezone varchar(50),
    is_active tinyint(1) DEFAULT 1,
    last_run datetime,
    next_run datetime,
    created_by char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (report_id) REFERENCES reports_advanced(id)
);

-- Report subscriptions
CREATE TABLE report_subscriptions (
    id char(36) PRIMARY KEY,
    schedule_id char(36) NOT NULL,
    subscriber_type enum('user','role','email_list') NOT NULL,
    subscriber_id varchar(255), -- User ID, Role ID, or email
    delivery_format enum('pdf','excel','csv','inline','dashboard') DEFAULT 'pdf',
    delivery_options text, -- JSON delivery preferences
    is_active tinyint(1) DEFAULT 1,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (schedule_id) REFERENCES report_schedules(id)
);

-- Report execution history
CREATE TABLE report_execution_history (
    id char(36) PRIMARY KEY,
    report_id char(36) NOT NULL,
    schedule_id char(36),
    execution_type enum('manual','scheduled','api') NOT NULL,
    start_time datetime,
    end_time datetime,
    duration_ms int,
    row_count int,
    status enum('running','completed','failed','cancelled') DEFAULT 'running',
    error_message text,
    output_file varchar(500),
    parameters_used text, -- JSON parameters at execution time
    executed_by char(36),
    date_entered datetime,
    FOREIGN KEY (report_id) REFERENCES reports_advanced(id),
    INDEX idx_report_date (report_id, date_entered)
);

-- Conditional formatting rules
CREATE TABLE report_formatting_rules (
    id char(36) PRIMARY KEY,
    report_id char(36) NOT NULL,
    rule_name varchar(255),
    target_type enum('cell','row','column','group') DEFAULT 'cell',
    target_field varchar(100),
    conditions text, -- JSON condition definitions
    formatting text, -- JSON formatting options
    priority int DEFAULT 0,
    is_active tinyint(1) DEFAULT 1,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (report_id) REFERENCES reports_advanced(id)
);
```

### Phase 2: Visual Report Builder

#### 2.1 Drag-and-Drop Interface
Location: `modules/Reports/javascript/ReportBuilder/`

```jsx
const ReportBuilder = () => {
    const [dataSources, setDataSources] = useState([]);
    const [selectedFields, setSelectedFields] = useState([]);
    const [filters, setFilters] = useState([]);
    const [groupings, setGroupings] = useState([]);
    const [calculations, setCalculations] = useState([]);
    
    return (
        <DndProvider backend={HTML5Backend}>
            <div className="report-builder">
                <div className="builder-sidebar">
                    <DataSourcePanel
                        modules={availableModules}
                        onAddDataSource={addDataSource}
                        dataSources={dataSources}
                    />
                    
                    <FieldSelector
                        dataSources={dataSources}
                        onFieldDrop={handleFieldDrop}
                    />
                    
                    <CalculationBuilder
                        fields={selectedFields}
                        onAddCalculation={addCalculation}
                    />
                </div>
                
                <div className="builder-canvas">
                    <ReportCanvas
                        fields={selectedFields}
                        groupings={groupings}
                        onFieldReorder={reorderFields}
                        onGroupingChange={updateGrouping}
                    />
                    
                    <FilterBuilder
                        fields={getAllAvailableFields()}
                        filters={filters}
                        onFilterUpdate={updateFilters}
                    />
                    
                    <PreviewPane
                        reportDefinition={buildReportDefinition()}
                        sampleSize={100}
                    />
                </div>
                
                <div className="builder-properties">
                    <FormatPanel
                        selectedElement={selectedElement}
                        onFormatChange={updateFormatting}
                    />
                    
                    <ChartConfiguration
                        visible={reportType === 'chart'}
                        data={previewData}
                        onConfigChange={updateChartConfig}
                    />
                </div>
            </div>
        </DndProvider>
    );
};
```

#### 2.2 Field Selector Component
```jsx
const FieldSelector = ({ dataSources, onFieldDrop }) => {
    const [expandedModules, setExpandedModules] = useState({});
    const [searchTerm, setSearchTerm] = useState('');
    
    const renderFieldTree = (dataSource) => {
        const fields = getFieldsForModule(dataSource.module);
        const filteredFields = filterFields(fields, searchTerm);
        
        return (
            <div className="field-tree">
                <div className="module-header" onClick={() => toggleModule(dataSource.id)}>
                    <Icon name={expandedModules[dataSource.id] ? 'chevron-down' : 'chevron-right'} />
                    <span>{dataSource.alias || dataSource.module}</span>
                </div>
                
                {expandedModules[dataSource.id] && (
                    <div className="field-list">
                        {Object.entries(groupFieldsByCategory(filteredFields)).map(([category, fields]) => (
                            <FieldCategory key={category} name={category}>
                                {fields.map(field => (
                                    <DraggableField
                                        key={field.name}
                                        field={field}
                                        dataSource={dataSource}
                                        onDrop={onFieldDrop}
                                    />
                                ))}
                            </FieldCategory>
                        ))}
                    </div>
                )}
            </div>
        );
    };
    
    return (
        <div className="field-selector">
            <SearchInput
                value={searchTerm}
                onChange={setSearchTerm}
                placeholder="Search fields..."
            />
            {dataSources.map(renderFieldTree)}
        </div>
    );
};
```

### Phase 3: Query Engine

#### 3.1 SQL Query Builder
Location: `modules/Reports/QueryEngine/QueryBuilder.php`

```php
class QueryBuilder {
    private $dataSources = [];
    private $fields = [];
    private $joins = [];
    private $filters = [];
    private $groupBy = [];
    private $orderBy = [];
    private $calculations = [];
    
    public function buildQuery($reportDefinition) {
        $this->parseDefinition($reportDefinition);
        
        $sql = $this->buildSelect();
        $sql .= $this->buildFrom();
        $sql .= $this->buildJoins();
        $sql .= $this->buildWhere();
        $sql .= $this->buildGroupBy();
        $sql .= $this->buildHaving();
        $sql .= $this->buildOrderBy();
        
        return [
            'sql' => $sql,
            'params' => $this->getParameters(),
            'metadata' => $this->getQueryMetadata()
        ];
    }
    
    private function buildSelect() {
        $selectClauses = [];
        
        // Regular fields
        foreach ($this->fields as $field) {
            $alias = $this->getFieldAlias($field);
            $selectClauses[] = "{$field['table_alias']}.{$field['column']} AS {$alias}";
        }
        
        // Calculated fields
        foreach ($this->calculations as $calc) {
            $formula = $this->parseFormula($calc['formula']);
            $selectClauses[] = "({$formula}) AS {$calc['alias']}";
        }
        
        // Aggregations
        foreach ($this->getAggregations() as $agg) {
            $selectClauses[] = $this->buildAggregation($agg);
        }
        
        return "SELECT " . implode(",\n       ", $selectClauses) . "\n";
    }
    
    private function buildJoins() {
        $joinClauses = [];
        
        foreach ($this->joins as $join) {
            $joinType = strtoupper($join['type']);
            $conditions = $this->buildJoinConditions($join['conditions']);
            
            $joinClauses[] = "{$joinType} JOIN {$join['table']} {$join['alias']} ON {$conditions}";
        }
        
        return implode("\n", $joinClauses) . "\n";
    }
    
    private function optimizeQuery($sql) {
        // Add query hints for performance
        if ($this->hasLargeDataset()) {
            $sql = "/*+ USE_INDEX(idx_primary) PARALLEL(4) */ " . $sql;
        }
        
        // Add row limiting for preview
        if ($this->isPreview) {
            $sql .= " LIMIT 1000";
        }
        
        return $sql;
    }
}
```

#### 3.2 Cross-Module Join Handler
Location: `modules/Reports/QueryEngine/JoinHandler.php`

```php
class JoinHandler {
    private $relationshipCache = [];
    
    public function buildJoinPath($fromModule, $toModule) {
        // Check direct relationships first
        $directPath = $this->findDirectRelationship($fromModule, $toModule);
        if ($directPath) {
            return [$directPath];
        }
        
        // Find shortest path through relationships
        return $this->findShortestPath($fromModule, $toModule);
    }
    
    public function generateJoinConditions($relationship) {
        $conditions = [];
        
        switch ($relationship['type']) {
            case 'one-to-many':
                $conditions[] = sprintf(
                    "%s.%s = %s.id",
                    $relationship['rhs_alias'],
                    $relationship['rhs_key'],
                    $relationship['lhs_alias']
                );
                break;
                
            case 'many-to-many':
                // Requires intermediate table
                $conditions[] = sprintf(
                    "%s.id = %s.%s",
                    $relationship['lhs_alias'],
                    $relationship['join_alias'],
                    $relationship['join_key_lhs']
                );
                $conditions[] = sprintf(
                    "%s.%s = %s.id",
                    $relationship['join_alias'],
                    $relationship['join_key_rhs'],
                    $relationship['rhs_alias']
                );
                break;
        }
        
        // Add deleted flag check
        $conditions[] = sprintf("%s.deleted = 0", $relationship['rhs_alias']);
        
        return implode(' AND ', $conditions);
    }
}
```

### Phase 4: Calculation Engine

#### 4.1 Formula Parser
Location: `modules/Reports/Calculations/FormulaParser.php`

```php
class FormulaParser {
    private $functions = [];
    private $variables = [];
    
    public function __construct() {
        $this->registerBuiltInFunctions();
    }
    
    public function parse($formula, $context = []) {
        $this->variables = $context;
        
        // Tokenize formula
        $tokens = $this->tokenize($formula);
        
        // Build AST
        $ast = $this->buildAST($tokens);
        
        // Generate SQL
        return $this->generateSQL($ast);
    }
    
    private function registerBuiltInFunctions() {
        // Date functions
        $this->functions['DATEDIFF'] = function($date1, $date2, $unit = 'day') {
            return "TIMESTAMPDIFF({$unit}, {$date2}, {$date1})";
        };
        
        $this->functions['DATEADD'] = function($date, $interval, $unit) {
            return "DATE_ADD({$date}, INTERVAL {$interval} {$unit})";
        };
        
        // Math functions
        $this->functions['ROUND'] = function($value, $decimals = 0) {
            return "ROUND({$value}, {$decimals})";
        };
        
        $this->functions['PERCENTAGE'] = function($part, $whole) {
            return "CASE WHEN {$whole} = 0 THEN 0 ELSE ({$part} / {$whole}) * 100 END";
        };
        
        // String functions
        $this->functions['CONCAT'] = function(...$args) {
            return "CONCAT(" . implode(', ', $args) . ")";
        };
        
        // Conditional functions
        $this->functions['IF'] = function($condition, $trueValue, $falseValue) {
            return "CASE WHEN {$condition} THEN {$trueValue} ELSE {$falseValue} END";
        };
        
        // Aggregate functions
        $this->functions['RUNNING_TOTAL'] = function($field) {
            return "SUM({$field}) OVER (ORDER BY {$this->getOrderByClause()})";
        };
    }
}
```

#### 4.2 Calculated Field Manager
```php
class CalculatedFieldManager {
    private $fields = [];
    private $dependencies = [];
    
    public function addField($name, $formula, $type = 'number') {
        $field = new CalculatedField();
        $field->name = $name;
        $field->formula = $formula;
        $field->type = $type;
        
        // Parse dependencies
        $field->dependencies = $this->extractDependencies($formula);
        
        // Validate no circular dependencies
        if ($this->hasCircularDependency($name, $field->dependencies)) {
            throw new CircularDependencyException();
        }
        
        $this->fields[$name] = $field;
        $this->updateDependencyGraph();
    }
    
    public function getFieldsInOrder() {
        // Topological sort to ensure dependencies are calculated first
        return $this->topologicalSort($this->fields);
    }
}
```

### Phase 5: Conditional Formatting

#### 5.1 Formatting Engine
Location: `modules/Reports/Formatting/FormattingEngine.php`

```php
class FormattingEngine {
    private $rules = [];
    
    public function applyFormatting($data, $rules) {
        $formattedData = $data;
        
        // Sort rules by priority
        usort($rules, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        
        foreach ($rules as $rule) {
            $formattedData = $this->applyRule($formattedData, $rule);
        }
        
        return $formattedData;
    }
    
    private function applyRule($data, $rule) {
        foreach ($data as &$row) {
            if ($this->evaluateCondition($row, $rule['conditions'])) {
                $this->applyFormattingToElement($row, $rule);
            }
        }
        
        return $data;
    }
    
    public function generateCSS($rules) {
        $css = [];
        
        foreach ($rules as $rule) {
            $selector = $this->buildSelector($rule);
            $styles = $this->buildStyles($rule['formatting']);
            
            $css[] = "{$selector} { {$styles} }";
        }
        
        return implode("\n", $css);
    }
}
```

#### 5.2 Conditional Formatting UI
```jsx
const ConditionalFormattingPanel = ({ field, onRuleAdd }) => {
    const [conditions, setConditions] = useState([]);
    const [formatting, setFormatting] = useState({});
    
    const conditionTypes = {
        'number': ['equals', 'not_equals', 'greater_than', 'less_than', 'between'],
        'string': ['equals', 'not_equals', 'contains', 'starts_with', 'ends_with'],
        'date': ['equals', 'before', 'after', 'between', 'last_n_days']
    };
    
    return (
        <div className="formatting-panel">
            <h3>Conditional Formatting for {field.displayName}</h3>
            
            <ConditionBuilder
                fieldType={field.type}
                conditions={conditionTypes[field.type]}
                onConditionAdd={(condition) => setConditions([...conditions, condition])}
            />
            
            <FormattingOptions
                formatting={formatting}
                onChange={setFormatting}
                options={['backgroundColor', 'textColor', 'fontWeight', 'fontSize', 'border']}
            />
            
            <IconPicker
                selected={formatting.icon}
                onChange={(icon) => setFormatting({ ...formatting, icon })}
            />
            
            <Button onClick={() => onRuleAdd({ conditions, formatting })}>
                Add Rule
            </Button>
        </div>
    );
};
```

### Phase 6: Report Scheduler

#### 6.1 Schedule Service
Location: `modules/Reports/Scheduler/ScheduleService.php`

```php
class ReportScheduleService {
    private $scheduler;
    private $queue;
    
    public function __construct() {
        $this->scheduler = new CronScheduler();
        $this->queue = new ReportQueue();
    }
    
    public function scheduleReport($reportId, $schedule) {
        $scheduleRecord = new ReportSchedule();
        $scheduleRecord->report_id = $reportId;
        $scheduleRecord->schedule_type = $schedule['type'];
        
        // Calculate next run time
        $cronExpression = $this->buildCronExpression($schedule);
        $scheduleRecord->cron_expression = $cronExpression;
        $scheduleRecord->next_run = $this->calculateNextRun($cronExpression);
        
        $scheduleRecord->save();
        
        // Register with scheduler
        $this->scheduler->register(
            "report_schedule_{$scheduleRecord->id}",
            $cronExpression,
            [$this, 'executeScheduledReport'],
            [$scheduleRecord->id]
        );
        
        return $scheduleRecord;
    }
    
    public function executeScheduledReport($scheduleId) {
        $schedule = ReportSchedule::find($scheduleId);
        
        if (!$schedule || !$schedule->is_active) {
            return;
        }
        
        // Queue report execution
        $job = new ReportExecutionJob();
        $job->schedule_id = $scheduleId;
        $job->report_id = $schedule->report_id;
        $job->parameters = $this->buildParameters($schedule);
        
        $this->queue->push($job);
        
        // Update next run time
        $schedule->last_run = date('Y-m-d H:i:s');
        $schedule->next_run = $this->calculateNextRun($schedule->cron_expression);
        $schedule->save();
    }
}
```

#### 6.2 Report Distribution
Location: `modules/Reports/Distribution/DistributionService.php`

```php
class ReportDistributionService {
    private $generators = [];
    private $distributors = [];
    
    public function __construct() {
        $this->registerGenerators();
        $this->registerDistributors();
    }
    
    public function distribute($reportData, $subscriptions) {
        foreach ($subscriptions as $subscription) {
            try {
                // Generate report in requested format
                $output = $this->generateOutput(
                    $reportData,
                    $subscription->delivery_format,
                    $subscription->delivery_options
                );
                
                // Distribute via appropriate channel
                $this->sendToSubscriber($output, $subscription);
                
                // Log successful delivery
                $this->logDelivery($subscription, 'success');
                
            } catch (\Exception $e) {
                $this->logDelivery($subscription, 'failed', $e->getMessage());
            }
        }
    }
    
    private function generateOutput($data, $format, $options) {
        $generator = $this->generators[$format];
        
        switch ($format) {
            case 'pdf':
                return $generator->generatePDF($data, $options);
                
            case 'excel':
                return $generator->generateExcel($data, $options);
                
            case 'csv':
                return $generator->generateCSV($data, $options);
                
            case 'dashboard':
                return $generator->generateDashboardWidget($data, $options);
        }
    }
}
```

### Phase 7: Report Execution Engine

#### 7.1 Asynchronous Execution
Location: `modules/Reports/Execution/AsyncExecutor.php`

```php
class AsyncReportExecutor {
    private $queue;
    private $cache;
    
    public function executeReport($reportId, $parameters = [], $options = []) {
        $executionId = $this->createExecution($reportId, $parameters);
        
        // For large reports, queue for background processing
        if ($this->shouldQueueReport($reportId)) {
            $this->queueExecution($executionId);
            return ['status' => 'queued', 'execution_id' => $executionId];
        }
        
        // Execute immediately for small reports
        return $this->executeImmediate($executionId);
    }
    
    private function executeImmediate($executionId) {
        $execution = ReportExecution::find($executionId);
        $report = Report::find($execution->report_id);
        
        try {
            // Build and execute query
            $queryBuilder = new QueryBuilder();
            $query = $queryBuilder->buildQuery($report->query_definition);
            
            // Add performance monitoring
            $startTime = microtime(true);
            $results = $this->executeQuery($query);
            $duration = (microtime(true) - $startTime) * 1000;
            
            // Apply calculations
            if ($report->calculated_fields) {
                $calculator = new CalculationProcessor();
                $results = $calculator->process($results, $report->calculated_fields);
            }
            
            // Apply formatting
            if ($report->formatting_rules) {
                $formatter = new FormattingEngine();
                $results = $formatter->applyFormatting($results, $report->formatting_rules);
            }
            
            // Update execution record
            $execution->status = 'completed';
            $execution->end_time = date('Y-m-d H:i:s');
            $execution->duration_ms = $duration;
            $execution->row_count = count($results);
            $execution->save();
            
            // Cache results
            $this->cacheResults($executionId, $results);
            
            return [
                'status' => 'completed',
                'execution_id' => $executionId,
                'data' => $results,
                'metadata' => $this->getExecutionMetadata($execution)
            ];
            
        } catch (\Exception $e) {
            $execution->status = 'failed';
            $execution->error_message = $e->getMessage();
            $execution->save();
            
            throw $e;
        }
    }
}
```

### Phase 8: Performance Optimization

#### 8.1 Query Optimization
Location: `modules/Reports/Optimization/QueryOptimizer.php`

```php
class QueryOptimizer {
    private $indexAnalyzer;
    private $statisticsCache;
    
    public function optimizeQuery($query, $reportDefinition) {
        // Analyze query execution plan
        $explainPlan = $this->getExplainPlan($query);
        
        // Suggest missing indexes
        $indexSuggestions = $this->suggestIndexes($explainPlan);
        
        // Rewrite query for better performance
        $optimizedQuery = $this->rewriteQuery($query, $explainPlan);
        
        // Add query hints
        $optimizedQuery = $this->addQueryHints($optimizedQuery, $reportDefinition);
        
        return [
            'query' => $optimizedQuery,
            'suggestions' => $indexSuggestions,
            'estimated_cost' => $this->estimateQueryCost($explainPlan)
        ];
    }
    
    private function addQueryHints($query, $definition) {
        $hints = [];
        
        // Use parallel execution for large datasets
        if ($this->estimateRowCount($definition) > 100000) {
            $hints[] = 'PARALLEL(AUTO)';
        }
        
        // Force index usage for known patterns
        foreach ($this->analyzeJoinPatterns($query) as $pattern) {
            if ($index = $this->getBestIndex($pattern)) {
                $hints[] = "USE_INDEX({$pattern['table']} {$index})";
            }
        }
        
        if (!empty($hints)) {
            $query = "SELECT /*+ " . implode(' ', $hints) . " */ " . substr($query, 6);
        }
        
        return $query;
    }
}
```

### Phase 9: Testing Strategy

#### 9.1 Report Builder Tests
Location: `tests/unit/modules/Reports/`

```php
class ReportBuilderTest extends TestCase {
    public function testCrossModuleJoins() {
        $builder = new QueryBuilder();
        
        $definition = [
            'primary_module' => 'Accounts',
            'data_sources' => [
                ['module' => 'Accounts', 'alias' => 'a'],
                ['module' => 'Opportunities', 'alias' => 'o', 'join_on' => 'account_id'],
                ['module' => 'Contacts', 'alias' => 'c', 'join_through' => 'accounts_contacts']
            ]
        ];
        
        $query = $builder->buildQuery($definition);
        
        $this->assertStringContainsString('LEFT JOIN opportunities o ON o.account_id = a.id', $query['sql']);
        $this->assertStringContainsString('LEFT JOIN accounts_contacts ac ON ac.account_id = a.id', $query['sql']);
        $this->assertStringContainsString('LEFT JOIN contacts c ON c.id = ac.contact_id', $query['sql']);
    }
    
    public function testCalculatedFields() {
        $parser = new FormulaParser();
        
        $formula = 'IF([opportunities.amount] > 10000, "Large", "Small")';
        $sql = $parser->parse($formula);
        
        $expected = 'CASE WHEN opportunities.amount > 10000 THEN "Large" ELSE "Small" END';
        $this->assertEquals($expected, $sql);
    }
}
```

### Phase 10: Report Templates & Sharing

#### 10.1 Template Library
Location: `modules/Reports/Templates/TemplateLibrary.php`

```php
class ReportTemplateLibrary {
    private $templates = [];
    
    public function __construct() {
        $this->loadSystemTemplates();
        $this->loadUserTemplates();
    }
    
    private function loadSystemTemplates() {
        $this->templates['sales_pipeline'] = [
            'name' => 'Sales Pipeline Report',
            'description' => 'Opportunities by stage with conversion metrics',
            'category' => 'Sales',
            'definition' => $this->getSalesPipelineDefinition()
        ];
        
        $this->templates['activity_summary'] = [
            'name' => 'Activity Summary Report',
            'description' => 'Calls, meetings, and tasks by user',
            'category' => 'Activity',
            'definition' => $this->getActivitySummaryDefinition()
        ];
        
        // More templates...
    }
    
    public function createFromTemplate($templateId, $customizations = []) {
        $template = $this->templates[$templateId];
        $report = new Report();
        
        // Merge template with customizations
        $definition = array_merge_recursive(
            $template['definition'],
            $customizations
        );
        
        $report->name = $template['name'] . ' (Copy)';
        $report->query_definition = json_encode($definition);
        $report->save();
        
        return $report;
    }
}
```

## Development Timeline

### Week 1-2: Core Infrastructure
- Database schema implementation
- Basic report CRUD operations
- Module metadata loading

### Week 3-4: Visual Builder
- Drag-and-drop interface
- Field selector
- Basic query generation

### Week 5-6: Query Engine
- Cross-module joins
- Filter implementation
- Aggregation support

### Week 7-8: Calculations
- Formula parser
- Calculated field engine
- Function library

### Week 9-10: Formatting & Visualization
- Conditional formatting
- Chart integration
- Export formats

### Week 11-12: Scheduling & Distribution
- Schedule management
- Email distribution
- Dashboard integration

### Week 13-14: Optimization & Polish
- Query optimization
- Performance tuning
- Template library

## Technical Dependencies
- React DnD for drag-and-drop
- Apache Echarts for visualizations
- PHPSpreadsheet for Excel export
- TCPDF for PDF generation
- Cron for scheduling
- Redis for caching

## Success Metrics
1. Report creation time reduced by 70%
2. Support for 10+ module joins
3. <5 second execution for 100k rows
4. 99% scheduled report delivery success
5. 50+ report templates available