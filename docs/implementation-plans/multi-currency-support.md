# Multi-Currency Support with Real-time Exchange Rates - Implementation Plan

## Overview
Implement comprehensive multi-currency functionality that allows SuiteCRM to handle transactions in multiple currencies with automatic conversion, real-time exchange rate updates, historical tracking, and currency-specific reporting.

## Architecture Overview

### Core Components
1. **Currency Management System** - Core currency configuration and storage
2. **Exchange Rate Service** - Real-time rate fetching and caching
3. **Conversion Engine** - Automatic currency conversion logic
4. **Historical Tracking** - Exchange rate history and audit trail
5. **Reporting Integration** - Multi-currency aware reporting

## Detailed Implementation Steps

### Phase 1: Database Schema Design

#### 1.1 Create Database Tables
```sql
-- Currency definitions
CREATE TABLE currencies (
    id char(36) PRIMARY KEY,
    name varchar(36) NOT NULL,
    symbol varchar(36) NOT NULL,
    iso4217 varchar(3) NOT NULL UNIQUE,
    conversion_rate decimal(26,8) DEFAULT 1.00000000,
    status enum('Active','Inactive') DEFAULT 'Active',
    is_base tinyint(1) DEFAULT 0, -- Only one base currency allowed
    decimal_places tinyint DEFAULT 2,
    decimal_separator varchar(1) DEFAULT '.',
    thousands_separator varchar(1) DEFAULT ',',
    symbol_position enum('before','after') DEFAULT 'before',
    negative_format varchar(10) DEFAULT '-',
    date_entered datetime,
    date_modified datetime,
    created_by char(36),
    deleted tinyint(1) DEFAULT 0,
    INDEX idx_iso4217 (iso4217),
    INDEX idx_status (status)
);

-- Exchange rate history
CREATE TABLE currency_rates_history (
    id char(36) PRIMARY KEY,
    currency_id char(36) NOT NULL,
    rate decimal(26,8) NOT NULL,
    rate_date date NOT NULL,
    source varchar(50), -- 'manual', 'api_ecb', 'api_fixer', etc.
    base_currency_id char(36),
    date_entered datetime,
    created_by char(36),
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (currency_id) REFERENCES currencies(id),
    UNIQUE KEY unique_currency_date (currency_id, rate_date),
    INDEX idx_rate_date (rate_date)
);

-- Currency rate providers configuration
CREATE TABLE currency_rate_providers (
    id char(36) PRIMARY KEY,
    name varchar(100) NOT NULL,
    provider_key varchar(50) UNIQUE NOT NULL,
    api_endpoint varchar(500),
    api_key varchar(255), -- Encrypted
    is_active tinyint(1) DEFAULT 1,
    priority int DEFAULT 0, -- Lower number = higher priority
    last_sync_date datetime,
    last_sync_status enum('success','failed','partial'),
    config text, -- JSON configuration
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0
);

-- Module currency fields tracking
CREATE TABLE currency_field_tracking (
    id char(36) PRIMARY KEY,
    module_name varchar(50) NOT NULL,
    field_name varchar(50) NOT NULL,
    currency_field_name varchar(50), -- Field that stores currency_id
    base_rate_field varchar(50), -- Field that stores conversion rate at time of creation
    base_amount_field varchar(50), -- Field that stores amount in base currency
    date_entered datetime,
    deleted tinyint(1) DEFAULT 0,
    UNIQUE KEY unique_module_field (module_name, field_name)
);

-- User currency preferences
CREATE TABLE user_currency_preferences (
    id char(36) PRIMARY KEY,
    user_id char(36) NOT NULL,
    currency_id char(36) NOT NULL,
    show_preferred_currency tinyint(1) DEFAULT 1,
    date_entered datetime,
    date_modified datetime,
    deleted tinyint(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (currency_id) REFERENCES currencies(id),
    UNIQUE KEY unique_user (user_id)
);
```

#### 1.2 Extend Existing Tables
```sql
-- Add currency fields to relevant modules
ALTER TABLE opportunities ADD COLUMN currency_id char(36);
ALTER TABLE opportunities ADD COLUMN base_rate decimal(26,8);
ALTER TABLE opportunities ADD COLUMN amount_usdollar decimal(26,6);

ALTER TABLE quotes ADD COLUMN currency_id char(36);
ALTER TABLE quotes ADD COLUMN base_rate decimal(26,8);
ALTER TABLE quotes ADD COLUMN total_usdollar decimal(26,6);

ALTER TABLE contracts ADD COLUMN currency_id char(36);
ALTER TABLE contracts ADD COLUMN base_rate decimal(26,8);
ALTER TABLE contracts ADD COLUMN total_contract_value_usdollar decimal(26,6);

-- Add indexes
ALTER TABLE opportunities ADD INDEX idx_currency (currency_id);
ALTER TABLE quotes ADD INDEX idx_currency (currency_id);
ALTER TABLE contracts ADD INDEX idx_currency (currency_id);
```

### Phase 2: Exchange Rate Service

#### 2.1 Rate Provider Interface
Location: `modules/Currencies/RateProviders/RateProviderInterface.php`

```php
interface RateProviderInterface {
    /**
     * Fetch current exchange rates
     * @param string $baseCurrency Base currency code (e.g., 'USD')
     * @param array $targetCurrencies Array of target currency codes
     * @return array ['EUR' => 0.85, 'GBP' => 0.73, ...]
     */
    public function fetchRates($baseCurrency, array $targetCurrencies);
    
    /**
     * Fetch historical rates for a specific date
     */
    public function fetchHistoricalRates($baseCurrency, array $targetCurrencies, $date);
    
    /**
     * Check if provider is available
     */
    public function isAvailable();
    
    /**
     * Get provider configuration requirements
     */
    public function getConfigurationSchema();
}
```

#### 2.2 ECB (European Central Bank) Provider
Location: `modules/Currencies/RateProviders/ECBProvider.php`

```php
class ECBProvider implements RateProviderInterface {
    const API_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    
    public function fetchRates($baseCurrency, array $targetCurrencies) {
        if ($baseCurrency !== 'EUR') {
            throw new UnsupportedBaseCurrencyException('ECB only supports EUR as base');
        }
        
        $xml = simplexml_load_file(self::API_URL);
        $rates = ['EUR' => 1.0];
        
        foreach ($xml->Cube->Cube->Cube as $rate) {
            $currency = (string) $rate['currency'];
            if (in_array($currency, $targetCurrencies)) {
                $rates[$currency] = (float) $rate['rate'];
            }
        }
        
        return $rates;
    }
}
```

#### 2.3 Multiple Provider Manager
Location: `modules/Currencies/Services/ExchangeRateService.php`

```php
class ExchangeRateService {
    private $providers = [];
    private $cache;
    
    public function __construct() {
        $this->loadProviders();
        $this->cache = new CurrencyRateCache();
    }
    
    public function updateRates($forceUpdate = false) {
        $currencies = $this->getActiveCurrencies();
        $baseCurrency = $this->getBaseCurrency();
        
        // Check cache first
        if (!$forceUpdate && $this->cache->hasValidRates()) {
            return $this->cache->getRates();
        }
        
        // Try each provider in priority order
        foreach ($this->providers as $provider) {
            try {
                if (!$provider->isAvailable()) continue;
                
                $rates = $provider->fetchRates(
                    $baseCurrency->iso4217,
                    array_column($currencies, 'iso4217')
                );
                
                // Save to database and cache
                $this->saveRates($rates);
                $this->cache->setRates($rates);
                
                return $rates;
                
            } catch (\Exception $e) {
                $GLOBALS['log']->error("Currency provider {$provider->getName()} failed: " . $e->getMessage());
                continue;
            }
        }
        
        throw new NoAvailableProviderException('All currency providers failed');
    }
}
```

### Phase 3: Conversion Engine

#### 3.1 Currency Converter
Location: `modules/Currencies/CurrencyConverter.php`

```php
class CurrencyConverter {
    private $baseCurrency;
    private $rates;
    
    public function __construct() {
        $this->baseCurrency = Currency::getBaseCurrency();
        $this->rates = $this->loadCurrentRates();
    }
    
    /**
     * Convert amount from one currency to another
     */
    public function convert($amount, $fromCurrency, $toCurrency, $date = null) {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        // Get rates for specific date if provided
        $rates = $date ? $this->getHistoricalRates($date) : $this->rates;
        
        // Convert to base currency first
        $baseAmount = $amount;
        if ($fromCurrency !== $this->baseCurrency->iso4217) {
            $fromRate = $rates[$fromCurrency] ?? null;
            if (!$fromRate) {
                throw new UnknownCurrencyException("Unknown currency: $fromCurrency");
            }
            $baseAmount = $amount / $fromRate;
        }
        
        // Convert from base to target currency
        if ($toCurrency === $this->baseCurrency->iso4217) {
            return $baseAmount;
        }
        
        $toRate = $rates[$toCurrency] ?? null;
        if (!$toRate) {
            throw new UnknownCurrencyException("Unknown currency: $toCurrency");
        }
        
        return $baseAmount * $toRate;
    }
    
    /**
     * Format amount in specified currency
     */
    public function format($amount, $currencyCode, $includeSymbol = true) {
        $currency = Currency::retrieveByISO($currencyCode);
        
        $formatted = number_format(
            $amount,
            $currency->decimal_places,
            $currency->decimal_separator,
            $currency->thousands_separator
        );
        
        if ($includeSymbol) {
            if ($currency->symbol_position === 'before') {
                $formatted = $currency->symbol . ' ' . $formatted;
            } else {
                $formatted = $formatted . ' ' . $currency->symbol;
            }
        }
        
        if ($amount < 0) {
            $formatted = str_replace('-', $currency->negative_format, $formatted);
        }
        
        return $formatted;
    }
}
```

#### 3.2 SugarBean Integration
Location: `modules/Currencies/CurrencyAwareTrait.php`

```php
trait CurrencyAwareTrait {
    /**
     * Save currency fields automatically
     */
    public function save($check_notify = false) {
        // Update base currency amount before saving
        $this->updateBaseCurrencyAmounts();
        
        return parent::save($check_notify);
    }
    
    protected function updateBaseCurrencyAmounts() {
        $currencyFields = $this->getCurrencyFields();
        
        foreach ($currencyFields as $field => $config) {
            if (!empty($this->$field) && !empty($this->currency_id)) {
                $converter = new CurrencyConverter();
                
                // Store the conversion rate at time of save
                $this->base_rate = $converter->getRate($this->currency_id);
                
                // Calculate base currency amount
                $baseField = $config['base_amount_field'];
                $this->$baseField = $converter->convert(
                    $this->$field,
                    $this->currency_id,
                    Currency::getBaseCurrency()->id
                );
            }
        }
    }
    
    /**
     * Get amount in user's preferred currency
     */
    public function getAmountInUserCurrency($field) {
        global $current_user;
        
        $userCurrency = $current_user->getPreference('currency_id');
        if (!$userCurrency) {
            $userCurrency = Currency::getBaseCurrency()->id;
        }
        
        $converter = new CurrencyConverter();
        return $converter->convert(
            $this->$field,
            $this->currency_id,
            $userCurrency,
            $this->date_entered // Use historical rate
        );
    }
}
```

### Phase 4: UI Components

#### 4.1 Currency Selector Widget
Location: `modules/Currencies/javascript/CurrencySelector.js`

```javascript
class CurrencySelector {
    constructor(options) {
        this.fieldName = options.fieldName;
        this.amountField = options.amountField;
        this.defaultCurrency = options.defaultCurrency || 'USD';
        this.showConversion = options.showConversion || false;
        
        this.init();
    }
    
    init() {
        this.loadCurrencies();
        this.bindEvents();
        this.createConversionDisplay();
    }
    
    loadCurrencies() {
        $.ajax({
            url: 'index.php?module=Currencies&action=GetActiveCurrencies',
            success: (data) => {
                this.currencies = data.currencies;
                this.renderSelector();
            }
        });
    }
    
    renderSelector() {
        const select = $(`#${this.fieldName}`);
        select.empty();
        
        this.currencies.forEach(currency => {
            select.append(
                `<option value="${currency.id}" data-rate="${currency.conversion_rate}">
                    ${currency.symbol} - ${currency.name} (${currency.iso4217})
                </option>`
            );
        });
        
        select.val(this.defaultCurrency);
    }
    
    bindEvents() {
        $(`#${this.fieldName}`).on('change', () => {
            this.updateConversion();
            this.triggerRateUpdate();
        });
        
        $(`#${this.amountField}`).on('input', () => {
            this.updateConversion();
        });
    }
    
    updateConversion() {
        if (!this.showConversion) return;
        
        const amount = parseFloat($(`#${this.amountField}`).val()) || 0;
        const selectedCurrency = $(`#${this.fieldName}`).val();
        const rate = $(`#${this.fieldName} option:selected`).data('rate');
        
        // Show conversion to base currency
        const baseAmount = amount / rate;
        $('#currency_conversion_display').html(
            `≈ ${this.formatCurrency(baseAmount, this.getBaseCurrency())}`
        );
    }
}
```

#### 4.2 Multi-Currency Display Component
Location: `modules/Currencies/javascript/MultiCurrencyDisplay.jsx`

```jsx
const MultiCurrencyDisplay = ({ amount, currencyId, showUserCurrency = true }) => {
    const [conversions, setConversions] = useState([]);
    const [userCurrency, setUserCurrency] = useState(null);
    
    useEffect(() => {
        fetchConversions();
        fetchUserPreferences();
    }, [amount, currencyId]);
    
    const fetchConversions = async () => {
        const response = await fetch('/api/v8/currencies/convert', {
            method: 'POST',
            body: JSON.stringify({
                amount,
                from_currency: currencyId,
                to_currencies: ['USD', 'EUR', 'GBP'] // Common currencies
            })
        });
        
        const data = await response.json();
        setConversions(data.conversions);
    };
    
    return (
        <div className="multi-currency-display">
            <div className="primary-amount">
                {formatCurrency(amount, currencyId)}
            </div>
            
            {showUserCurrency && userCurrency && userCurrency !== currencyId && (
                <div className="user-currency-amount">
                    ≈ {formatCurrency(conversions[userCurrency], userCurrency)}
                    <span className="currency-label">Your currency</span>
                </div>
            )}
            
            <div className="currency-conversions">
                {conversions.map(({ currency, amount }) => (
                    <span key={currency} className="conversion-item">
                        {formatCurrency(amount, currency)}
                    </span>
                ))}
            </div>
        </div>
    );
};
```

### Phase 5: Historical Rate Tracking

#### 5.1 Rate History Service
Location: `modules/Currencies/Services/RateHistoryService.php`

```php
class RateHistoryService {
    /**
     * Get historical rate for a specific date
     */
    public function getHistoricalRate($currencyId, $date) {
        // Try exact date first
        $rate = CurrencyRateHistory::where('currency_id', $currencyId)
            ->where('rate_date', $date)
            ->first();
            
        if ($rate) {
            return $rate->rate;
        }
        
        // Fall back to nearest available date
        $rate = CurrencyRateHistory::where('currency_id', $currencyId)
            ->where('rate_date', '<=', $date)
            ->orderBy('rate_date', 'DESC')
            ->first();
            
        return $rate ? $rate->rate : null;
    }
    
    /**
     * Backfill historical rates
     */
    public function backfillRates($startDate, $endDate) {
        $provider = new HistoricalRateProvider();
        $currencies = Currency::getActive();
        $baseCurrency = Currency::getBaseCurrency();
        
        $currentDate = new DateTime($startDate);
        $end = new DateTime($endDate);
        
        while ($currentDate <= $end) {
            try {
                $rates = $provider->fetchHistoricalRates(
                    $baseCurrency->iso4217,
                    array_column($currencies, 'iso4217'),
                    $currentDate->format('Y-m-d')
                );
                
                $this->saveHistoricalRates($rates, $currentDate);
                
            } catch (\Exception $e) {
                $GLOBALS['log']->warn("Failed to fetch rates for {$currentDate->format('Y-m-d')}");
            }
            
            $currentDate->modify('+1 day');
        }
    }
}
```

### Phase 6: Reporting Integration

#### 6.1 Multi-Currency Report Engine
Location: `modules/Reports/MultiCurrencyReportEngine.php`

```php
class MultiCurrencyReportEngine extends ReportEngine {
    protected $displayCurrency;
    protected $conversionDate;
    
    /**
     * Execute report with currency conversion
     */
    public function execute($reportDef, $displayCurrency = null, $conversionDate = null) {
        $this->displayCurrency = $displayCurrency ?: Currency::getBaseCurrency()->id;
        $this->conversionDate = $conversionDate ?: date('Y-m-d');
        
        // Execute base query
        $results = parent::execute($reportDef);
        
        // Apply currency conversions
        return $this->applyCurrencyConversions($results, $reportDef);
    }
    
    protected function applyCurrencyConversions($results, $reportDef) {
        $converter = new CurrencyConverter();
        $currencyFields = $this->identifyCurrencyFields($reportDef);
        
        foreach ($results as &$row) {
            foreach ($currencyFields as $field => $config) {
                if (isset($row[$field]) && isset($row[$config['currency_field']])) {
                    // Convert to display currency
                    $row[$field . '_converted'] = $converter->convert(
                        $row[$field],
                        $row[$config['currency_field']],
                        $this->displayCurrency,
                        $this->conversionDate
                    );
                    
                    // Format for display
                    $row[$field . '_display'] = $converter->format(
                        $row[$field . '_converted'],
                        $this->displayCurrency
                    );
                }
            }
        }
        
        return $results;
    }
}
```

#### 6.2 Currency Summary Dashlet
Location: `modules/Home/Dashlets/CurrencySummaryDashlet/`

```php
class CurrencySummaryDashlet extends DashletGeneric {
    public function display() {
        $converter = new CurrencyConverter();
        $opportunities = $this->getOpportunitiesByCurrency();
        
        $summary = [];
        foreach ($opportunities as $opp) {
            $currency = $opp['currency_id'];
            if (!isset($summary[$currency])) {
                $summary[$currency] = [
                    'count' => 0,
                    'total' => 0,
                    'total_base' => 0
                ];
            }
            
            $summary[$currency]['count']++;
            $summary[$currency]['total'] += $opp['amount'];
            $summary[$currency]['total_base'] += $converter->convert(
                $opp['amount'],
                $currency,
                Currency::getBaseCurrency()->id
            );
        }
        
        $this->ss->assign('currency_summary', $summary);
        return parent::display();
    }
}
```

### Phase 7: Currency Management UI

#### 7.1 Currency Administration
Location: `modules/Currencies/views/view.admin.php`

```php
class CurrenciesViewAdmin extends ViewEdit {
    public function display() {
        $this->ss->assign('currencies', Currency::getAllCurrencies());
        $this->ss->assign('providers', CurrencyRateProvider::getAll());
        $this->ss->assign('lastSync', $this->getLastSyncInfo());
        
        // Add JavaScript for real-time updates
        echo '<script src="modules/Currencies/javascript/CurrencyAdmin.js"></script>';
        
        parent::display();
    }
}
```

### Phase 8: Testing Strategy

#### 8.1 Unit Tests
Location: `tests/unit/modules/Currencies/`

```php
class CurrencyConverterTest extends PHPUnit_Framework_TestCase {
    public function testConversionAccuracy() {
        $converter = new CurrencyConverter();
        
        // Test conversion with known rates
        $converter->setTestRates([
            'USD' => 1.0,
            'EUR' => 0.85,
            'GBP' => 0.73
        ]);
        
        $amount = 100;
        $result = $converter->convert($amount, 'USD', 'EUR');
        $this->assertEquals(85, $result);
        
        // Test reverse conversion
        $result = $converter->convert($result, 'EUR', 'USD');
        $this->assertEquals(100, $result, '', 0.01); // Allow small rounding difference
    }
    
    public function testHistoricalRates() {
        $service = new RateHistoryService();
        
        $rate = $service->getHistoricalRate('EUR', '2023-01-15');
        $this->assertNotNull($rate);
        $this->assertGreaterThan(0, $rate);
    }
}
```

#### 8.2 Integration Tests
```php
class MultiCurrencyIntegrationTest extends IntegrationTestCase {
    public function testOpportunityWithCurrency() {
        // Create opportunity in EUR
        $opp = new Opportunity();
        $opp->name = 'Test Opportunity';
        $opp->amount = 1000;
        $opp->currency_id = 'EUR';
        $opp->save();
        
        // Verify base currency amount was calculated
        $this->assertNotNull($opp->amount_usdollar);
        $this->assertGreaterThan(1000, $opp->amount_usdollar); // EUR > USD
        
        // Test currency change
        $opp->currency_id = 'GBP';
        $opp->save();
        
        // Verify base amount was recalculated
        $this->assertGreaterThan($opp->amount_usdollar, 1000 / 0.73);
    }
}
```

### Phase 9: Performance Optimization

#### 9.1 Caching Strategy
Location: `modules/Currencies/Cache/CurrencyCache.php`

```php
class CurrencyCache {
    private $redis;
    private $ttl = 3600; // 1 hour default
    
    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }
    
    public function getRates($baseCurrency = null) {
        $key = "currency_rates:" . ($baseCurrency ?: 'default');
        $rates = $this->redis->get($key);
        
        return $rates ? json_decode($rates, true) : null;
    }
    
    public function setRates($rates, $baseCurrency = null) {
        $key = "currency_rates:" . ($baseCurrency ?: 'default');
        $this->redis->setex($key, $this->ttl, json_encode($rates));
    }
    
    public function invalidate() {
        $keys = $this->redis->keys('currency_rates:*');
        if ($keys) {
            $this->redis->del($keys);
        }
    }
}
```

### Phase 10: Scheduled Tasks

#### 10.1 Rate Update Scheduler
Location: `modules/Schedulers/Jobs/UpdateCurrencyRates.php`

```php
function updateCurrencyRates() {
    $service = new ExchangeRateService();
    
    try {
        $rates = $service->updateRates();
        
        // Log successful update
        $GLOBALS['log']->info("Currency rates updated successfully: " . count($rates) . " rates");
        
        // Send notification if rates changed significantly
        $monitor = new RateChangeMonitor();
        $monitor->checkForSignificantChanges($rates);
        
        return true;
        
    } catch (\Exception $e) {
        $GLOBALS['log']->error("Currency rate update failed: " . $e->getMessage());
        
        // Send admin notification
        $this->sendAdminNotification('Currency rate update failed', $e->getMessage());
        
        return false;
    }
}
```

## Development Timeline

### Week 1-2: Core Infrastructure
- Database schema implementation
- Basic currency CRUD operations
- Module field modifications

### Week 3-4: Exchange Rate Service
- Provider interface implementation
- ECB and Fixer.io providers
- Rate caching system

### Week 5-6: Conversion Engine
- Conversion logic implementation
- SugarBean integration
- Historical rate support

### Week 7-8: UI Components
- Currency selector widget
- Multi-currency display
- Admin interface

### Week 9-10: Reporting Integration
- Multi-currency report engine
- Currency dashlets
- Export functionality

### Week 11-12: Testing & Optimization
- Comprehensive testing
- Performance optimization
- Documentation

## Technical Dependencies
- External APIs: ECB, Fixer.io, CurrencyLayer
- Redis for caching
- Scheduled job support
- AJAX for real-time updates

## Security Considerations
1. Encrypt API keys in database
2. Validate all currency inputs
3. Implement rate limiting for API calls
4. Audit trail for all conversions
5. Permission checks for currency management

## Success Metrics
1. 99.9% accuracy in conversions
2. <100ms conversion calculation time
3. Support for 150+ currencies
4. Automatic rate updates within 1 hour
5. Zero data loss during currency changes