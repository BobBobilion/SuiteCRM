# Smart Quote & Proposal Generator - Implementation Plan

## Overview
Build a comprehensive quote and proposal generation system with template-based creation, product catalog management, pricing rules, discount matrices, e-signature integration, and complete tracking analytics.

## Architecture Overview

### Core Components
1. **Template Engine** - Flexible document template system
2. **Product Catalog** - Centralized product/service management
3. **Pricing Engine** - Dynamic pricing rules and calculations
4. **Approval Workflow** - Multi-level discount approvals
5. **E-Signature Integration** - Digital signature capabilities
6. **Analytics Dashboard** - Proposal tracking and insights

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Quote templates
CREATE TABLE quote_templates (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    description text,
    category varchar(100),
    template_type enum('quote','proposal','contract') DEFAULT 'quote',
    header_template text,
    body_template text,
    footer_template text,
    css_styles text,
    variables_schema text, -- JSON schema of available variables
    is_active tinyint(1) DEFAULT 1,
    is_default tinyint(1) DEFAULT 0,
    created_by char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Product catalog
CREATE TABLE product_catalog (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    sku varchar(100) UNIQUE,
    category_id char(36),
    description text,
    product_type enum('product','service','subscription') DEFAULT 'product',
    unit_price decimal(26,6),
    cost decimal(26,6),
    currency_id char(36),
    tax_class varchar(50),
    status enum('active','inactive','discontinued') DEFAULT 'active',
    inventory_tracking tinyint(1) DEFAULT 0,
    current_inventory int DEFAULT 0,
    specifications text, -- JSON product specifications
    images text, -- JSON array of image URLs
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    INDEX idx_sku (sku),
    INDEX idx_category (category_id)
);

-- Product categories
CREATE TABLE product_categories (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    parent_id char(36),
    description text,
    display_order int DEFAULT 0,
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id)
);

-- Pricing rules
CREATE TABLE pricing_rules (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    rule_type enum('volume','customer','date','bundle','custom') NOT NULL,
    priority int DEFAULT 0,
    conditions text, -- JSON conditions
    actions text, -- JSON pricing actions
    start_date date,
    end_date date,
    is_active tinyint(1) DEFAULT 1,
    created_by char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Discount matrices
CREATE TABLE discount_matrices (
    id char(36) PRIMARY KEY,
    name varchar(255) NOT NULL,
    matrix_type enum('volume','customer_tier','product_category') NOT NULL,
    matrix_data text, -- JSON matrix configuration
    requires_approval tinyint(1) DEFAULT 0,
    max_discount_percent decimal(5,2),
    created_by char(36),
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Quote versions
CREATE TABLE quote_versions (
    id char(36) PRIMARY KEY,
    quote_id char(36) NOT NULL,
    version_number int NOT NULL,
    status enum('draft','sent','viewed','accepted','rejected','expired') DEFAULT 'draft',
    document_data text, -- JSON complete quote data
    pdf_path varchar(500),
    sent_date datetime,
    viewed_date datetime,
    response_date datetime,
    expiry_date datetime,
    created_by char(36),
    date_entered datetime,
    FOREIGN KEY (quote_id) REFERENCES quotes(id),
    UNIQUE KEY unique_quote_version (quote_id, version_number)
);

-- E-signature tracking
CREATE TABLE quote_signatures (
    id char(36) PRIMARY KEY,
    quote_version_id char(36) NOT NULL,
    signer_name varchar(255),
    signer_email varchar(255),
    signer_role varchar(100),
    signature_request_id varchar(255), -- External provider ID
    signature_status enum('pending','sent','viewed','signed','declined') DEFAULT 'pending',
    signed_date datetime,
    ip_address varchar(45),
    signature_data text, -- Encrypted signature image/data
    certificate_id varchar(255),
    date_entered datetime,
    FOREIGN KEY (quote_version_id) REFERENCES quote_versions(id)
);

-- Quote analytics
CREATE TABLE quote_analytics (
    id char(36) PRIMARY KEY,
    quote_version_id char(36) NOT NULL,
    event_type enum('sent','opened','downloaded','forwarded','signed') NOT NULL,
    event_data text, -- JSON event details
    ip_address varchar(45),
    user_agent varchar(500),
    referrer varchar(500),
    duration int, -- Time spent in seconds for 'opened' events
    date_entered datetime,
    FOREIGN KEY (quote_version_id) REFERENCES quote_versions(id),
    INDEX idx_quote_event (quote_version_id, event_type)
);

-- Approval workflow
CREATE TABLE quote_approvals (
    id char(36) PRIMARY KEY,
    quote_id char(36) NOT NULL,
    approval_type enum('discount','terms','credit','custom') NOT NULL,
    required_level int DEFAULT 1,
    current_level int DEFAULT 0,
    status enum('pending','approved','rejected','escalated') DEFAULT 'pending',
    approval_data text, -- JSON approval details
    comments text,
    created_by char(36),
    date_entered datetime,
    date_modified datetime,
    FOREIGN KEY (quote_id) REFERENCES quotes(id)
);
```

### Phase 2: Template Engine

#### 2.1 Template Processor
Location: `modules/Quotes/TemplateEngine/TemplateProcessor.php`

```php
class TemplateProcessor {
    private $twig;
    private $variableResolver;
    private $pdfGenerator;
    
    public function __construct() {
        $loader = new \Twig\Loader\ArrayLoader();
        $this->twig = new \Twig\Environment($loader, [
            'cache' => 'cache/templates',
            'auto_reload' => true
        ]);
        
        $this->registerCustomFunctions();
        $this->variableResolver = new VariableResolver();
        $this->pdfGenerator = new PdfGenerator();
    }
    
    public function processTemplate($templateId, $quoteData) {
        $template = QuoteTemplate::find($templateId);
        
        // Resolve all variables
        $variables = $this->variableResolver->resolve($quoteData);
        
        // Process sections
        $sections = [
            'header' => $this->processSection($template->header_template, $variables),
            'body' => $this->processSection($template->body_template, $variables),
            'footer' => $this->processSection($template->footer_template, $variables)
        ];
        
        // Combine with CSS
        $html = $this->combineDocument($sections, $template->css_styles);
        
        return [
            'html' => $html,
            'pdf' => $this->pdfGenerator->generate($html)
        ];
    }
    
    private function registerCustomFunctions() {
        // Currency formatting
        $this->twig->addFilter(new \Twig\TwigFilter('currency', function ($amount, $currency = 'USD') {
            return CurrencyFormatter::format($amount, $currency);
        }));
        
        // Date formatting
        $this->twig->addFilter(new \Twig\TwigFilter('date_format', function ($date, $format = 'Y-m-d') {
            return date($format, strtotime($date));
        }));
        
        // Conditional content
        $this->twig->addFunction(new \Twig\TwigFunction('when', function ($condition, $content) {
            return $condition ? $content : '';
        }));
    }
}
```

#### 2.2 Dynamic Content Builder
Location: `modules/Quotes/TemplateEngine/ContentBuilder.php`

```php
class ContentBuilder {
    public function buildLineItems($quote) {
        $items = [];
        $subtotal = 0;
        
        foreach ($quote->line_items as $item) {
            $product = $this->getProduct($item->product_id);
            $pricing = $this->calculatePricing($item, $quote);
            
            $lineItem = [
                'product' => $product,
                'quantity' => $item->quantity,
                'unit_price' => $pricing['unit_price'],
                'discount' => $pricing['discount'],
                'tax' => $pricing['tax'],
                'total' => $pricing['total']
            ];
            
            $items[] = $lineItem;
            $subtotal += $pricing['total'];
        }
        
        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'tax_total' => $this->calculateTotalTax($items),
            'grand_total' => $this->calculateGrandTotal($items)
        ];
    }
}
```

### Phase 3: Product Catalog Management

#### 3.1 Product Service
Location: `modules/ProductCatalog/Services/ProductService.php`

```php
class ProductService {
    private $cache;
    private $searchEngine;
    
    public function __construct() {
        $this->cache = new ProductCache();
        $this->searchEngine = new ProductSearchEngine();
    }
    
    public function searchProducts($criteria) {
        // Check cache first
        $cacheKey = $this->buildCacheKey($criteria);
        $cached = $this->cache->get($cacheKey);
        
        if ($cached) {
            return $cached;
        }
        
        // Search using Elasticsearch or similar
        $results = $this->searchEngine->search([
            'query' => $criteria['query'] ?? '',
            'category' => $criteria['category'] ?? null,
            'price_range' => $criteria['price_range'] ?? null,
            'attributes' => $criteria['attributes'] ?? []
        ]);
        
        $this->cache->set($cacheKey, $results, 300);
        
        return $results;
    }
    
    public function getProductWithPricing($productId, $context = []) {
        $product = Product::find($productId);
        
        if (!$product) {
            throw new ProductNotFoundException();
        }
        
        // Apply pricing rules
        $pricingEngine = new PricingEngine();
        $pricing = $pricingEngine->calculatePrice($product, $context);
        
        return [
            'product' => $product,
            'pricing' => $pricing,
            'inventory' => $this->getInventoryStatus($product)
        ];
    }
}
```

#### 3.2 Product Catalog UI
Location: `modules/ProductCatalog/javascript/ProductSelector.jsx`

```jsx
const ProductSelector = ({ onSelect, context }) => {
    const [products, setProducts] = useState([]);
    const [categories, setCategories] = useState([]);
    const [filters, setFilters] = useState({});
    const [loading, setLoading] = useState(false);
    
    const searchProducts = useCallback(async (searchTerm) => {
        setLoading(true);
        
        const response = await fetch('/api/v8/products/search', {
            method: 'POST',
            body: JSON.stringify({
                query: searchTerm,
                ...filters,
                context // Customer, date, etc. for pricing
            })
        });
        
        const data = await response.json();
        setProducts(data.products);
        setLoading(false);
    }, [filters, context]);
    
    return (
        <div className="product-selector">
            <div className="search-bar">
                <SearchInput
                    onSearch={searchProducts}
                    placeholder="Search products by name, SKU, or description..."
                />
            </div>
            
            <div className="catalog-layout">
                <CategoryTree
                    categories={categories}
                    onSelectCategory={(cat) => setFilters({ ...filters, category: cat })}
                />
                
                <ProductGrid
                    products={products}
                    loading={loading}
                    onSelectProduct={onSelect}
                    renderProduct={(product) => (
                        <ProductCard
                            product={product}
                            showPricing={true}
                            showInventory={true}
                        />
                    )}
                />
            </div>
        </div>
    );
};
```

### Phase 4: Pricing Engine

#### 4.1 Pricing Rule Engine
Location: `modules/Quotes/PricingEngine/PricingEngine.php`

```php
class PricingEngine {
    private $rules = [];
    private $discountMatrices = [];
    
    public function __construct() {
        $this->loadActiveRules();
        $this->loadDiscountMatrices();
    }
    
    public function calculatePrice($product, $context) {
        $basePrice = $product->unit_price;
        $finalPrice = $basePrice;
        $appliedRules = [];
        
        // Sort rules by priority
        $applicableRules = $this->getApplicableRules($product, $context);
        
        foreach ($applicableRules as $rule) {
            $result = $this->applyRule($rule, $finalPrice, $product, $context);
            
            if ($result['applied']) {
                $finalPrice = $result['price'];
                $appliedRules[] = [
                    'rule' => $rule,
                    'discount' => $basePrice - $finalPrice,
                    'reason' => $result['reason']
                ];
            }
        }
        
        return [
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'discount_amount' => $basePrice - $finalPrice,
            'discount_percent' => (($basePrice - $finalPrice) / $basePrice) * 100,
            'applied_rules' => $appliedRules,
            'requires_approval' => $this->requiresApproval($appliedRules)
        ];
    }
    
    private function applyRule($rule, $currentPrice, $product, $context) {
        switch ($rule->rule_type) {
            case 'volume':
                return $this->applyVolumeDiscount($rule, $currentPrice, $context['quantity']);
                
            case 'customer':
                return $this->applyCustomerDiscount($rule, $currentPrice, $context['customer']);
                
            case 'bundle':
                return $this->applyBundleDiscount($rule, $currentPrice, $context['cart']);
                
            case 'date':
                return $this->applyDateBasedDiscount($rule, $currentPrice);
                
            case 'custom':
                return $this->evaluateCustomRule($rule, $currentPrice, $product, $context);
        }
    }
}
```

#### 4.2 Discount Matrix Implementation
Location: `modules/Quotes/PricingEngine/DiscountMatrix.php`

```php
class DiscountMatrix {
    private $matrix;
    
    public function __construct($matrixData) {
        $this->matrix = json_decode($matrixData, true);
    }
    
    public function calculateDiscount($params) {
        switch ($this->matrix['type']) {
            case 'volume_tiered':
                return $this->volumeTieredDiscount($params['quantity']);
                
            case 'customer_value':
                return $this->customerValueDiscount($params['customer_ltv']);
                
            case 'product_mix':
                return $this->productMixDiscount($params['products']);
        }
    }
    
    private function volumeTieredDiscount($quantity) {
        $tiers = $this->matrix['tiers'];
        $discount = 0;
        
        foreach ($tiers as $tier) {
            if ($quantity >= $tier['min_qty'] && 
                ($tier['max_qty'] === null || $quantity <= $tier['max_qty'])) {
                $discount = $tier['discount_percent'];
            }
        }
        
        return [
            'discount_percent' => $discount,
            'requires_approval' => $discount > $this->matrix['auto_approve_limit']
        ];
    }
}
```

### Phase 5: Approval Workflow

#### 5.1 Approval Engine
Location: `modules/Quotes/Approval/ApprovalEngine.php`

```php
class ApprovalEngine {
    private $workflowDefinitions;
    private $notificationService;
    
    public function initiateApproval($quote, $type, $data) {
        $workflow = $this->getWorkflowDefinition($type, $data);
        
        $approval = new QuoteApproval();
        $approval->quote_id = $quote->id;
        $approval->approval_type = $type;
        $approval->required_level = $workflow->getRequiredLevel($data);
        $approval->approval_data = json_encode($data);
        $approval->save();
        
        // Notify first approver
        $this->notifyApprover($approval, 1);
        
        return $approval;
    }
    
    public function processApproval($approvalId, $decision, $comments = '') {
        $approval = QuoteApproval::find($approvalId);
        
        if ($decision === 'approve') {
            $approval->current_level++;
            
            if ($approval->current_level >= $approval->required_level) {
                $approval->status = 'approved';
                $this->onApprovalComplete($approval);
            } else {
                // Notify next level
                $this->notifyApprover($approval, $approval->current_level + 1);
            }
        } else {
            $approval->status = 'rejected';
            $this->onApprovalRejected($approval);
        }
        
        $approval->comments = $comments;
        $approval->save();
        
        return $approval;
    }
}
```

### Phase 6: E-Signature Integration

#### 6.1 E-Signature Service
Location: `modules/Quotes/ESignature/ESignatureService.php`

```php
class ESignatureService {
    private $providers = [];
    
    public function __construct() {
        $this->registerProviders();
    }
    
    private function registerProviders() {
        $this->providers['docusign'] = new DocuSignProvider();
        $this->providers['hellosign'] = new HelloSignProvider();
        $this->providers['adobe_sign'] = new AdobeSignProvider();
    }
    
    public function sendForSignature($quoteVersion, $signers) {
        $provider = $this->getActiveProvider();
        $document = $this->prepareDocument($quoteVersion);
        
        // Create signature request
        $request = $provider->createSignatureRequest([
            'document' => $document,
            'signers' => $signers,
            'callback_url' => $this->getCallbackUrl($quoteVersion),
            'metadata' => [
                'quote_version_id' => $quoteVersion->id,
                'quote_number' => $quoteVersion->quote->quote_num
            ]
        ]);
        
        // Track signature request
        foreach ($signers as $signer) {
            $signature = new QuoteSignature();
            $signature->quote_version_id = $quoteVersion->id;
            $signature->signer_name = $signer['name'];
            $signature->signer_email = $signer['email'];
            $signature->signer_role = $signer['role'];
            $signature->signature_request_id = $request->id;
            $signature->signature_status = 'sent';
            $signature->save();
        }
        
        return $request;
    }
    
    public function handleCallback($payload) {
        $event = $payload['event'];
        $requestId = $payload['signature_request_id'];
        
        switch ($event['type']) {
            case 'signature_completed':
                $this->onSignatureCompleted($requestId, $event);
                break;
                
            case 'signature_declined':
                $this->onSignatureDeclined($requestId, $event);
                break;
                
            case 'signature_viewed':
                $this->onDocumentViewed($requestId, $event);
                break;
        }
    }
}
```

### Phase 7: Quote Builder UI

#### 7.1 Interactive Quote Builder
Location: `modules/Quotes/javascript/QuoteBuilder/`

```jsx
const QuoteBuilder = ({ quoteId }) => {
    const [quote, setQuote] = useState(null);
    const [template, setTemplate] = useState(null);
    const [lineItems, setLineItems] = useState([]);
    const [preview, setPreview] = useState(null);
    
    const addProduct = useCallback((product) => {
        const newItem = {
            id: generateId(),
            product_id: product.id,
            product,
            quantity: 1,
            unit_price: product.pricing.final_price,
            discount: 0,
            tax: calculateTax(product),
            total: product.pricing.final_price
        };
        
        setLineItems([...lineItems, newItem]);
        recalculateTotals();
    }, [lineItems]);
    
    const updateLineItem = useCallback((itemId, updates) => {
        setLineItems(items =>
            items.map(item =>
                item.id === itemId
                    ? { ...item, ...updates, total: calculateLineTotal(item, updates) }
                    : item
            )
        );
        recalculateTotals();
    }, []);
    
    return (
        <div className="quote-builder">
            <QuoteHeader
                quote={quote}
                onUpdate={(updates) => setQuote({ ...quote, ...updates })}
            />
            
            <div className="quote-content">
                <LineItemsTable
                    items={lineItems}
                    onUpdate={updateLineItem}
                    onDelete={(id) => setLineItems(items => items.filter(i => i.id !== id))}
                    onAddProduct={() => setShowProductSelector(true)}
                />
                
                <QuoteTotals
                    subtotal={calculateSubtotal(lineItems)}
                    tax={calculateTotalTax(lineItems)}
                    discount={quote?.discount || 0}
                    total={calculateGrandTotal(lineItems, quote)}
                />
                
                <QuoteTerms
                    terms={quote?.terms}
                    onUpdate={(terms) => setQuote({ ...quote, terms })}
                />
            </div>
            
            <QuoteActions
                onPreview={() => generatePreview()}
                onSave={() => saveQuote()}
                onSend={() => setShowSendDialog(true)}
            />
            
            {showProductSelector && (
                <ProductSelectorModal
                    onSelect={addProduct}
                    onClose={() => setShowProductSelector(false)}
                    context={{
                        customer: quote?.account_id,
                        date: quote?.date_quote_expected_closed
                    }}
                />
            )}
        </div>
    );
};
```

### Phase 8: Analytics & Tracking

#### 8.1 Quote Analytics Service
Location: `modules/Quotes/Analytics/QuoteAnalytics.php`

```php
class QuoteAnalytics {
    public function trackEvent($quoteVersionId, $eventType, $eventData = []) {
        $analytics = new QuoteAnalytic();
        $analytics->quote_version_id = $quoteVersionId;
        $analytics->event_type = $eventType;
        $analytics->event_data = json_encode($eventData);
        $analytics->ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $analytics->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $analytics->save();
        
        // Real-time notification
        $this->notifyRealtime($quoteVersionId, $eventType, $eventData);
    }
    
    public function getQuoteMetrics($quoteId) {
        $versions = QuoteVersion::where('quote_id', $quoteId)->get();
        
        $metrics = [
            'versions_created' => count($versions),
            'times_sent' => 0,
            'times_viewed' => 0,
            'avg_time_to_decision' => 0,
            'conversion_rate' => 0,
            'viewing_patterns' => []
        ];
        
        foreach ($versions as $version) {
            $events = QuoteAnalytic::where('quote_version_id', $version->id)->get();
            
            foreach ($events as $event) {
                switch ($event->event_type) {
                    case 'sent':
                        $metrics['times_sent']++;
                        break;
                    case 'opened':
                        $metrics['times_viewed']++;
                        $metrics['viewing_patterns'][] = [
                            'timestamp' => $event->date_entered,
                            'duration' => $event->event_data['duration'] ?? 0
                        ];
                        break;
                }
            }
        }
        
        return $metrics;
    }
}
```

#### 8.2 Analytics Dashboard
Location: `modules/Quotes/javascript/Analytics/QuoteAnalyticsDashboard.jsx`

```jsx
const QuoteAnalyticsDashboard = () => {
    const [metrics, setMetrics] = useState({});
    const [timeRange, setTimeRange] = useState('30days');
    
    return (
        <div className="quote-analytics-dashboard">
            <MetricCards>
                <MetricCard
                    title="Quote Conversion Rate"
                    value={`${metrics.conversionRate}%`}
                    trend={metrics.conversionTrend}
                    icon="chart-line"
                />
                <MetricCard
                    title="Avg. Time to Close"
                    value={metrics.avgTimeToClose}
                    format="duration"
                    icon="clock"
                />
                <MetricCard
                    title="Quotes Sent"
                    value={metrics.totalSent}
                    trend={metrics.sentTrend}
                    icon="paper-plane"
                />
                <MetricCard
                    title="View-to-Sign Rate"
                    value={`${metrics.viewToSignRate}%`}
                    icon="eye"
                />
            </MetricCards>
            
            <QuoteFunnel
                data={metrics.funnelData}
                stages={['Created', 'Sent', 'Viewed', 'Signed']}
            />
            
            <QuoteActivityTimeline
                activities={metrics.recentActivity}
                onQuoteClick={(quoteId) => navigateToQuote(quoteId)}
            />
        </div>
    );
};
```

### Phase 9: Testing Strategy

#### 9.1 Unit Tests
Location: `tests/unit/modules/Quotes/`

```php
class PricingEngineTest extends TestCase {
    public function testVolumeDiscounts() {
        $engine = new PricingEngine();
        $product = Product::factory()->create(['unit_price' => 100]);
        
        // Test tier 1: 10+ units = 5% discount
        $result = $engine->calculatePrice($product, ['quantity' => 15]);
        $this->assertEquals(95, $result['final_price']);
        
        // Test tier 2: 50+ units = 10% discount
        $result = $engine->calculatePrice($product, ['quantity' => 75]);
        $this->assertEquals(90, $result['final_price']);
    }
    
    public function testApprovalRequired() {
        $engine = new PricingEngine();
        $product = Product::factory()->create(['unit_price' => 1000]);
        
        // Test discount > 20% requires approval
        $result = $engine->calculatePrice($product, [
            'quantity' => 100,
            'requested_discount' => 25
        ]);
        
        $this->assertTrue($result['requires_approval']);
    }
}
```

### Phase 10: Performance Optimization

#### 10.1 PDF Generation Optimization
Location: `modules/Quotes/Services/PdfOptimizer.php`

```php
class PdfOptimizer {
    private $cache;
    
    public function generateOptimized($html, $options = []) {
        // Cache compiled templates
        $templateHash = md5($html);
        $cachedPdf = $this->cache->get("pdf_template:{$templateHash}");
        
        if ($cachedPdf && !$options['force_regenerate']) {
            return $this->personalizeDocument($cachedPdf, $options['variables']);
        }
        
        // Use PDF generation queue for large documents
        if (strlen($html) > 100000) {
            return $this->queueGeneration($html, $options);
        }
        
        // Generate with optimizations
        $pdf = new \TCPDF();
        $pdf->SetCompression(true);
        $pdf->SetImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Optimize images
        $html = $this->optimizeImages($html);
        
        $pdf->writeHTML($html);
        
        return $pdf->Output('', 'S');
    }
}
```

## Development Timeline

### Week 1-2: Database & Core Structure
- Create database schema
- Basic quote template system
- Product catalog foundation

### Week 3-4: Template Engine
- Implement Twig integration
- Dynamic content builder
- Variable resolution system

### Week 5-6: Pricing Engine
- Pricing rule implementation
- Discount matrix system
- Approval workflow basics

### Week 7-8: Product Catalog
- Product search functionality
- Category management
- Inventory integration

### Week 9-10: Quote Builder UI
- React-based builder interface
- Line item management
- Real-time calculations

### Week 11-12: E-Signature Integration
- Provider implementations
- Signature tracking
- Callback handling

### Week 13-14: Analytics & Polish
- Analytics dashboard
- Performance optimization
- Comprehensive testing

## Technical Dependencies
- Twig template engine
- TCPDF for PDF generation
- DocuSign/HelloSign API
- React for quote builder
- Elasticsearch for product search
- Redis for caching

## Success Metrics
1. 50% reduction in quote creation time
2. 80% of quotes use templates
3. <3 second PDF generation
4. 90% e-signature completion rate
5. 30% improvement in quote-to-close ratio