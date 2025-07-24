# Custom Dashboard Builder with Real-time Updates - Implementation Plan

## Overview
Create a powerful drag-and-drop dashboard builder that allows users to design custom dashboards with various widgets, real-time data updates via WebSockets, and responsive layouts for all devices.

## Architecture Overview

### Core Components
1. **Dashboard Designer** - Drag-and-drop interface for layout creation
2. **Widget Framework** - Extensible widget system
3. **Real-time Engine** - WebSocket-based live updates
4. **Data Service Layer** - Efficient data fetching and caching
5. **Permission System** - Granular dashboard sharing controls

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Dashboard definitions
CREATE TABLE dashboards (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    description text,
    layout_config text, -- JSON grid layout configuration
    theme varchar(50) DEFAULT 'default',
    is_default tinyint(1) DEFAULT 0,
    is_global tinyint(1) DEFAULT 0,
    refresh_interval int DEFAULT 30, -- seconds
    created_by char(36),
    assigned_user_id char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_user_id) REFERENCES users(id)
);

-- Dashboard widgets
CREATE TABLE dashboard_widgets (
    id char(36) PRIMARY KEY,
    dashboard_id char(36) NOT NULL,
    widget_type varchar(100) NOT NULL,
    title varchar(255),
    config text, -- JSON widget configuration
    position_config text, -- JSON position {x, y, w, h}
    refresh_interval int, -- Override dashboard interval
    cache_timeout int DEFAULT 300, -- seconds
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (dashboard_id) REFERENCES dashboards(id)
);

-- Widget library
CREATE TABLE widget_definitions (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    widget_type varchar(100) UNIQUE NOT NULL,
    category enum('chart','kpi','list','calendar','map','custom') NOT NULL,
    description text,
    icon varchar(100),
    config_schema text, -- JSON schema for configuration
    data_sources text, -- JSON array of required data sources
    min_width int DEFAULT 1,
    min_height int DEFAULT 1,
    max_width int DEFAULT 12,
    max_height int DEFAULT 12,
    is_active tinyint(1) DEFAULT 1,
    is_system tinyint(1) DEFAULT 0,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Dashboard sharing
CREATE TABLE dashboard_shares (
    id char(36) PRIMARY KEY,
    dashboard_id char(36) NOT NULL,
    shared_with_type enum('user','role','team','public') NOT NULL,
    shared_with_id char(36), -- User/Role/Team ID
    permission_level enum('view','edit','admin') DEFAULT 'view',
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (dashboard_id) REFERENCES dashboards(id)
);

-- KPI goals and thresholds
CREATE TABLE dashboard_kpi_goals (
    id char(36) PRIMARY KEY,
    widget_id char(36) NOT NULL,
    metric_name varchar(100) NOT NULL,
    target_value decimal(26,6),
    min_threshold decimal(26,6),
    max_threshold decimal(26,6),
    threshold_type enum('value','percentage','custom') DEFAULT 'value',
    alert_enabled tinyint(1) DEFAULT 0,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (widget_id) REFERENCES dashboard_widgets(id)
);

-- Real-time subscriptions
CREATE TABLE dashboard_subscriptions (
    id char(36) PRIMARY KEY,
    user_id char(36) NOT NULL,
    dashboard_id char(36) NOT NULL,
    connection_id varchar(255), -- WebSocket connection ID
    last_active datetime,
    date_entered datetime,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (dashboard_id) REFERENCES dashboards(id),
    UNIQUE KEY unique_user_dashboard (user_id, dashboard_id)
);
```

### Phase 2: Widget Framework

#### 2.1 Base Widget Class
Location: `modules/DashboardBuilder/Widgets/BaseWidget.php`

```php
abstract class BaseWidget {
    protected $id;
    protected $config;
    protected $dataService;
    
    abstract public function getType();
    abstract public function getData($params = []);
    abstract public function getConfigSchema();
    abstract public function render();
    
    public function __construct($id, $config) {
        $this->id = $id;
        $this->config = $config;
        $this->dataService = new DataService();
    }
    
    public function getRealtimeChannels() {
        // Return channels this widget should subscribe to
        return [];
    }
    
    public function processRealtimeUpdate($channel, $data) {
        // Handle real-time data updates
        return $this->transformDataForClient($data);
    }
    
    protected function getCacheKey() {
        return "widget_{$this->id}_" . md5(json_encode($this->config));
    }
}
```

#### 2.2 Chart Widget Implementation
Location: `modules/DashboardBuilder/Widgets/ChartWidget.php`

```php
class ChartWidget extends BaseWidget {
    public function getType() {
        return 'chart';
    }
    
    public function getData($params = []) {
        $chartType = $this->config['chartType'];
        $dataSource = $this->config['dataSource'];
        
        // Check cache first
        $cacheKey = $this->getCacheKey();
        $cachedData = Cache::get($cacheKey);
        
        if ($cachedData !== null) {
            return $cachedData;
        }
        
        // Fetch fresh data
        $data = $this->fetchChartData($dataSource, $params);
        
        // Transform for specific chart type
        $transformedData = $this->transformForChartType($data, $chartType);
        
        // Cache the result
        Cache::set($cacheKey, $transformedData, $this->config['cacheTimeout'] ?? 300);
        
        return $transformedData;
    }
    
    private function fetchChartData($dataSource, $params) {
        switch ($dataSource['type']) {
            case 'module':
                return $this->fetchModuleData($dataSource, $params);
            case 'report':
                return $this->fetchReportData($dataSource['reportId']);
            case 'custom':
                return $this->fetchCustomData($dataSource['query']);
        }
    }
}
```

#### 2.3 KPI Widget with Goal Tracking
Location: `modules/DashboardBuilder/Widgets/KpiWidget.php`

```php
class KpiWidget extends BaseWidget {
    public function getData($params = []) {
        $value = $this->calculateKpiValue();
        $goal = $this->getGoal();
        $trend = $this->calculateTrend();
        
        return [
            'value' => $value,
            'goal' => $goal,
            'progress' => $goal ? ($value / $goal['target']) * 100 : null,
            'trend' => $trend,
            'status' => $this->evaluateStatus($value, $goal),
            'sparkline' => $this->getSparklineData()
        ];
    }
    
    private function evaluateStatus($value, $goal) {
        if (!$goal) return 'neutral';
        
        if ($value >= $goal['max_threshold']) return 'danger';
        if ($value <= $goal['min_threshold']) return 'danger';
        if ($value >= $goal['target'] * 0.9) return 'success';
        if ($value >= $goal['target'] * 0.7) return 'warning';
        
        return 'danger';
    }
}
```

### Phase 3: Dashboard Designer UI

#### 3.1 React Dashboard Builder
Location: `modules/DashboardBuilder/javascript/designer/`

```jsx
// Main Designer Component
import React, { useState, useCallback } from 'react';
import GridLayout from 'react-grid-layout';
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';

const DashboardDesigner = () => {
    const [layout, setLayout] = useState([]);
    const [widgets, setWidgets] = useState([]);
    const [isDragging, setIsDragging] = useState(false);
    
    const onLayoutChange = useCallback((newLayout) => {
        setLayout(newLayout);
        // Debounced save to backend
        debouncedSave(newLayout);
    }, []);
    
    const onDropWidget = useCallback((widgetType, position) => {
        const newWidget = {
            id: generateId(),
            type: widgetType,
            ...getDefaultDimensions(widgetType),
            x: position.x,
            y: position.y
        };
        
        setWidgets([...widgets, newWidget]);
    }, [widgets]);
    
    return (
        <DndProvider backend={HTML5Backend}>
            <div className="dashboard-designer">
                <WidgetLibrary onSelectWidget={onDropWidget} />
                
                <GridLayout
                    className="dashboard-grid"
                    layout={layout}
                    onLayoutChange={onLayoutChange}
                    cols={12}
                    rowHeight={60}
                    width={1200}
                    draggableHandle=".widget-header"
                    compactType="vertical"
                    preventCollision={false}
                >
                    {widgets.map(widget => (
                        <div key={widget.id} className="dashboard-widget">
                            <WidgetContainer
                                widget={widget}
                                onConfigure={() => openConfigModal(widget)}
                                onDelete={() => removeWidget(widget.id)}
                            />
                        </div>
                    ))}
                </GridLayout>
            </div>
        </DndProvider>
    );
};
```

#### 3.2 Widget Library Component
```jsx
const WidgetLibrary = ({ onSelectWidget }) => {
    const [categories, setCategories] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    
    return (
        <div className="widget-library">
            <SearchInput
                value={searchTerm}
                onChange={setSearchTerm}
                placeholder="Search widgets..."
            />
            
            {categories.map(category => (
                <WidgetCategory key={category.id} title={category.name}>
                    {category.widgets
                        .filter(w => w.name.toLowerCase().includes(searchTerm))
                        .map(widget => (
                            <DraggableWidget
                                key={widget.type}
                                widget={widget}
                                onDrop={onSelectWidget}
                            />
                        ))
                    }
                </WidgetCategory>
            ))}
        </div>
    );
};
```

### Phase 4: Real-time Updates via WebSockets

#### 4.1 WebSocket Server
Location: `websocket/DashboardWebSocketServer.php`

```php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class DashboardWebSocketServer implements MessageComponentInterface {
    protected $clients;
    protected $subscriptions;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
    }
    
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $conn->resourceId = uniqid();
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        switch ($data['action']) {
            case 'subscribe':
                $this->subscribeToDashboard($from, $data['dashboardId']);
                break;
                
            case 'unsubscribe':
                $this->unsubscribeFromDashboard($from, $data['dashboardId']);
                break;
                
            case 'updateWidget':
                $this->broadcastWidgetUpdate($data['dashboardId'], $data['widgetId'], $data['data']);
                break;
        }
    }
    
    protected function broadcastWidgetUpdate($dashboardId, $widgetId, $data) {
        $message = json_encode([
            'type' => 'widgetUpdate',
            'dashboardId' => $dashboardId,
            'widgetId' => $widgetId,
            'data' => $data,
            'timestamp' => time()
        ]);
        
        // Send to all subscribed clients
        foreach ($this->subscriptions[$dashboardId] ?? [] as $client) {
            $client->send($message);
        }
    }
}
```

#### 4.2 Real-time Data Publisher
Location: `modules/DashboardBuilder/Services/RealtimePublisher.php`

```php
class RealtimePublisher {
    private $redisClient;
    
    public function __construct() {
        $this->redisClient = new Redis();
        $this->redisClient->connect('127.0.0.1', 6379);
    }
    
    public function publishUpdate($channel, $data) {
        $message = json_encode([
            'channel' => $channel,
            'data' => $data,
            'timestamp' => microtime(true)
        ]);
        
        $this->redisClient->publish('dashboard_updates', $message);
    }
    
    public function publishModuleUpdate($module, $action, $recordId) {
        $affectedWidgets = $this->findAffectedWidgets($module);
        
        foreach ($affectedWidgets as $widget) {
            $this->publishWidgetUpdate($widget->id, [
                'action' => $action,
                'module' => $module,
                'recordId' => $recordId
            ]);
        }
    }
}
```

### Phase 5: Custom Chart Builder

#### 5.1 Chart Configuration UI
Location: `modules/DashboardBuilder/javascript/chart-builder/`

```jsx
const ChartBuilder = ({ widget, onSave }) => {
    const [chartConfig, setChartConfig] = useState(widget.config || {});
    const [previewData, setPreviewData] = useState(null);
    
    return (
        <div className="chart-builder">
            <Tabs>
                <TabPanel label="Data Source">
                    <DataSourceSelector
                        value={chartConfig.dataSource}
                        onChange={(ds) => updateConfig({ dataSource: ds })}
                    />
                </TabPanel>
                
                <TabPanel label="Chart Type">
                    <ChartTypeSelector
                        value={chartConfig.chartType}
                        onChange={(type) => updateConfig({ chartType: type })}
                        options={['line', 'bar', 'pie', 'donut', 'area', 'scatter']}
                    />
                </TabPanel>
                
                <TabPanel label="Appearance">
                    <ChartStyleEditor
                        config={chartConfig.style}
                        onChange={(style) => updateConfig({ style })}
                    />
                </TabPanel>
                
                <TabPanel label="Preview">
                    <ChartPreview
                        config={chartConfig}
                        data={previewData}
                    />
                </TabPanel>
            </Tabs>
        </div>
    );
};
```

#### 5.2 Dynamic Chart Renderer
```jsx
const ChartRenderer = ({ type, data, config }) => {
    const chartRef = useRef(null);
    
    useEffect(() => {
        if (!chartRef.current || !data) return;
        
        const chartInstance = new Chart(chartRef.current, {
            type: mapChartType(type),
            data: transformDataForChart(data, type),
            options: {
                ...getDefaultOptions(type),
                ...config.options,
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        return () => chartInstance.destroy();
    }, [type, data, config]);
    
    return <canvas ref={chartRef} />;
};
```

### Phase 6: Mobile Responsive Design

#### 6.1 Responsive Grid System
Location: `modules/DashboardBuilder/javascript/responsive/`

```jsx
const ResponsiveDashboard = ({ dashboard }) => {
    const [breakpoint, setBreakpoint] = useState('lg');
    
    const layouts = {
        lg: dashboard.layout,
        md: compactLayout(dashboard.layout, 8),
        sm: compactLayout(dashboard.layout, 4),
        xs: stackedLayout(dashboard.layout)
    };
    
    return (
        <ResponsiveGridLayout
            layouts={layouts}
            breakpoints={{ lg: 1200, md: 996, sm: 768, xs: 480 }}
            cols={{ lg: 12, md: 8, sm: 4, xs: 1 }}
            onBreakpointChange={setBreakpoint}
        >
            {dashboard.widgets.map(widget => (
                <div key={widget.id}>
                    <ResponsiveWidget
                        widget={widget}
                        breakpoint={breakpoint}
                    />
                </div>
            ))}
        </ResponsiveGridLayout>
    );
};
```

### Phase 7: Dashboard Sharing & Permissions

#### 7.1 Permission Service
Location: `modules/DashboardBuilder/Services/PermissionService.php`

```php
class DashboardPermissionService {
    public function canView($userId, $dashboardId) {
        $dashboard = Dashboard::find($dashboardId);
        
        // Owner can always view
        if ($dashboard->created_by === $userId) {
            return true;
        }
        
        // Check direct shares
        $share = DashboardShare::where('dashboard_id', $dashboardId)
            ->where('shared_with_type', 'user')
            ->where('shared_with_id', $userId)
            ->first();
            
        if ($share) return true;
        
        // Check role-based shares
        $userRoles = $this->getUserRoles($userId);
        $roleShares = DashboardShare::where('dashboard_id', $dashboardId)
            ->where('shared_with_type', 'role')
            ->whereIn('shared_with_id', $userRoles)
            ->exists();
            
        return $roleShares;
    }
}
```

### Phase 8: Performance Optimization

#### 8.1 Data Caching Strategy
Location: `modules/DashboardBuilder/Cache/CacheManager.php`

```php
class DashboardCacheManager {
    private $cacheStore;
    private $cacheKeyPrefix = 'dashboard:';
    
    public function getWidgetData($widgetId, $params = []) {
        $cacheKey = $this->buildCacheKey($widgetId, $params);
        
        // Try multi-tier cache
        $data = $this->getFromMemory($cacheKey);
        if ($data !== null) return $data;
        
        $data = $this->getFromRedis($cacheKey);
        if ($data !== null) {
            $this->setInMemory($cacheKey, $data);
            return $data;
        }
        
        // Generate fresh data
        $data = $this->generateWidgetData($widgetId, $params);
        $this->setInCache($cacheKey, $data);
        
        return $data;
    }
    
    public function invalidateWidget($widgetId) {
        $pattern = $this->cacheKeyPrefix . "widget:{$widgetId}:*";
        $this->cacheStore->deletePattern($pattern);
    }
}
```

### Phase 9: Testing Strategy

#### 9.1 Frontend Testing
Location: `tests/javascript/dashboard-builder/`

```javascript
// Widget drag-drop test
describe('Dashboard Designer', () => {
    it('should allow dragging widgets from library to canvas', () => {
        const { getByText, getByTestId } = render(<DashboardDesigner />);
        
        const chartWidget = getByText('Line Chart');
        const canvas = getByTestId('dashboard-canvas');
        
        fireEvent.dragStart(chartWidget);
        fireEvent.drop(canvas);
        
        expect(getByTestId('widget-configuration-modal')).toBeInTheDocument();
    });
});
```

#### 9.2 WebSocket Testing
```php
class WebSocketTest extends TestCase {
    public function testRealtimeUpdates() {
        $client = new WebSocketClient('ws://localhost:8080');
        
        $client->send(json_encode([
            'action' => 'subscribe',
            'dashboardId' => 'test-dashboard'
        ]));
        
        // Trigger an update
        $publisher = new RealtimePublisher();
        $publisher->publishWidgetUpdate('widget-1', ['value' => 42]);
        
        $message = $client->receive();
        $data = json_decode($message, true);
        
        $this->assertEquals('widgetUpdate', $data['type']);
        $this->assertEquals(42, $data['data']['value']);
    }
}
```

### Phase 10: Analytics & Usage Tracking

#### 10.1 Dashboard Analytics
Location: `modules/DashboardBuilder/Analytics/UsageTracker.php`

```php
class DashboardUsageTracker {
    public function trackView($dashboardId, $userId) {
        $this->record('dashboard_view', [
            'dashboard_id' => $dashboardId,
            'user_id' => $userId,
            'timestamp' => time()
        ]);
    }
    
    public function trackWidgetInteraction($widgetId, $action, $userId) {
        $this->record('widget_interaction', [
            'widget_id' => $widgetId,
            'action' => $action,
            'user_id' => $userId,
            'timestamp' => time()
        ]);
    }
    
    public function getPopularDashboards($limit = 10) {
        return DB::table('dashboard_analytics')
            ->select('dashboard_id', DB::raw('COUNT(*) as views'))
            ->where('event_type', 'dashboard_view')
            ->where('timestamp', '>', strtotime('-30 days'))
            ->groupBy('dashboard_id')
            ->orderBy('views', 'DESC')
            ->limit($limit)
            ->get();
    }
}
```

## Development Timeline

### Week 1-2: Core Infrastructure
- Database schema setup
- Basic dashboard CRUD operations
- Widget framework foundation

### Week 3-4: Widget Development
- Chart widget implementation
- KPI widget with goals
- List and calendar widgets

### Week 5-6: Dashboard Designer
- React grid layout setup
- Drag-and-drop functionality
- Widget configuration modals

### Week 7-8: Real-time Features
- WebSocket server setup
- Real-time data publishing
- Client-side subscriptions

### Week 9-10: Chart Builder
- Visual chart configuration
- Data source selection
- Preview functionality

### Week 11-12: Mobile & Permissions
- Responsive layouts
- Permission system
- Sharing functionality

### Week 13-14: Performance & Polish
- Caching implementation
- Performance optimization
- Final testing

## Technical Dependencies
- React 17+ with TypeScript
- react-grid-layout for drag-drop
- Chart.js or D3.js for visualizations
- Ratchet PHP for WebSockets
- Redis for caching and pub/sub
- AG-Grid for data tables

## Success Metrics
1. Dashboard load time <2 seconds
2. Real-time updates <100ms latency
3. Support 50+ concurrent users per dashboard
4. 90% user satisfaction with builder interface
5. Mobile usage reaches 40% of total