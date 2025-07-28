<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Trade-In Manager - Main Bean Class
 * 
 * This class defines the DM_TradeIns SugarBean entity for managing vehicle 
 * trade-in evaluations and processing within automotive dealership operations.
 * 
 * Features:
 * - Trade-in vehicle information management
 * - Market valuation integration with external APIs
 * - Condition assessment and appraisal workflow
 * - Customer payoff and financing integration
 * - Sales opportunity linkage
 * - Basic reporting and status tracking
 * - Integration with customer records and vehicle inventory
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_TradeIns Bean Class
 * 
 * Manages trade-in vehicles with valuation, condition assessment, and 
 * integration with sales processes for automotive dealership operations
 */
#[\AllowDynamicProperties]
class DM_TradeIns extends Basic
{
    public $table_name = "dm_tradeins";
    public $object_name = "DM_TradeIns";
    public $module_dir = "DM_TradeIns";
    public $module_name = "DM_TradeIns";
    
    // Disable row-level security and security groups for now
    public $disable_row_level_security = true;
    public $disable_security_groups = true;
    
    // Enable auditing for trade-in valuations (financial compliance requirement)
    public $audited = true;
    
    // Core trade-in identification fields
    public $id;
    public $name;                          // Display name (Year Make Model)
    
    // Relationship links
    public $customer_id;                   // Link to customer account
    public $opportunity_id;                // Link to sales opportunity
    
    // Vehicle identification and specifications
    public $vin;                          // Vehicle Identification Number
    public $year;                         // Model year
    public $make;                         // Vehicle manufacturer
    public $model;                        // Vehicle model
    public $trim;                         // Trim level/package
    public $mileage;                      // Current odometer reading
    public $exterior_color;               // Exterior paint color
    
    // Condition and assessment
    public $condition_overall;            // Overall condition (Excellent/Good/Fair/Poor)
    public $condition_notes;              // Detailed condition assessment notes
    
    // Customer information and expectations
    public $customer_asking;              // Customer's expected trade value
    
    // Market valuations (populated via API)
    public $market_value_retail;          // Retail market value
    public $market_value_trade;           // Trade-in market value
    public $market_value_private;         // Private party market value
    public $valuation_date;               // When values were last retrieved
    public $valuation_source;             // API source used for valuation
    
    // Dealer appraisal and final values
    public $appraised_value;              // Dealer's final appraisal amount
    public $appraisal_date;               // Date of dealer appraisal
    public $appraisal_notes;              // Appraiser notes and adjustments
    
    // Payoff and lien information
    public $payoff_amount;                // Loan payoff amount
    public $payoff_bank;                  // Lender/lienholder information
    public $payoff_date;                  // Payoff good through date
    public $payoff_verified;              // Whether payoff has been verified
    
    // Trade-in workflow and status
    public $status;                       // New/Appraised/Approved/Used/Rejected
    public $appraisal_scheduled_date;     // When physical appraisal is scheduled
    public $photos_taken;                 // Whether photos have been captured
    
    // Integration and processing
    public $used_in_deal;                 // Whether trade was used in a deal
    public $deal_id;                      // Link to F&I deal if used
    public $disposal_method;              // How trade was disposed (Retail/Wholesale/Auction)
    
    // Profitability tracking
    public $trade_allowance;              // Amount credited to customer
    public $actual_cash_value;            // ACV for accounting purposes
    public $reconditioning_cost;          // Estimated reconditioning costs
    public $estimated_profit;             // Projected profit on trade
    
    // Standard SugarBean fields
    public $assigned_user_id;             // Assigned sales person or appraiser
    public $date_entered;
    public $date_modified;
    public $created_by;
    public $modified_user_id;
    public $deleted;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Initialize logging for trade-in operations
        $GLOBALS['log']->info("DM_TradeIns: Initializing Trade-In Bean");
    }

    /**
     * Generate display name for trade-in record
     * Format: "YYYY Make Model - Customer Name"
     */
    public function save($check_notify = false)
    {
        // Log the save operation
        $GLOBALS['log']->info("DM_TradeIns: Saving trade-in record ID: " . $this->id);
        
        // Auto-generate name if not provided
        if (empty($this->name) && !empty($this->year) && !empty($this->make) && !empty($this->model)) {
            $this->name = $this->year . ' ' . $this->make . ' ' . $this->model;
            
            // Add customer name if available
            if (!empty($this->customer_id)) {
                $customer = BeanFactory::getBean('Accounts', $this->customer_id);
                if ($customer && !empty($customer->name)) {
                    $this->name .= ' - ' . $customer->name;
                }
            }
            
            $GLOBALS['log']->info("DM_TradeIns: Auto-generated name: " . $this->name);
        }
        
        // Update valuation date when market values are changed
        if (!empty($this->market_value_trade) || !empty($this->market_value_retail)) {
            if (empty($this->valuation_date)) {
                $this->valuation_date = date('Y-m-d H:i:s');
                $GLOBALS['log']->info("DM_TradeIns: Updated valuation date: " . $this->valuation_date);
            }
        }
        
        // Set appraisal date when appraised value is set
        if (!empty($this->appraised_value) && empty($this->appraisal_date)) {
            $this->appraisal_date = date('Y-m-d H:i:s');
            $GLOBALS['log']->info("DM_TradeIns: Set appraisal date: " . $this->appraisal_date);
        }
        
        return parent::save($check_notify);
    }

    /**
     * Calculate trade equity (customer value vs payoff)
     * 
     * @return float Trade equity (positive = equity, negative = negative equity)
     */
    public function calculateTradeEquity()
    {
        $equity = 0;
        
        // Use appraised value if available, otherwise use trade market value
        $tradeValue = !empty($this->appraised_value) ? $this->appraised_value : $this->market_value_trade;
        $payoff = !empty($this->payoff_amount) ? $this->payoff_amount : 0;
        
        if (!empty($tradeValue)) {
            $equity = $tradeValue - $payoff;
            $GLOBALS['log']->info("DM_TradeIns: Calculated trade equity - Value: $tradeValue, Payoff: $payoff, Equity: $equity");
        }
        
        return $equity;
    }

    /**
     * Get market value comparison summary
     * 
     * @return array Comparison of market values and customer asking price
     */
    public function getValueComparison()
    {
        $comparison = array(
            'customer_asking' => $this->customer_asking,
            'market_retail' => $this->market_value_retail,
            'market_trade' => $this->market_value_trade,
            'market_private' => $this->market_value_private,
            'appraised_value' => $this->appraised_value,
            'customer_vs_trade' => 0,
            'customer_vs_retail' => 0,
            'appraised_vs_trade' => 0
        );
        
        // Calculate variances
        if (!empty($this->customer_asking) && !empty($this->market_value_trade)) {
            $comparison['customer_vs_trade'] = $this->customer_asking - $this->market_value_trade;
        }
        
        if (!empty($this->customer_asking) && !empty($this->market_value_retail)) {
            $comparison['customer_vs_retail'] = $this->customer_asking - $this->market_value_retail;
        }
        
        if (!empty($this->appraised_value) && !empty($this->market_value_trade)) {
            $comparison['appraised_vs_trade'] = $this->appraised_value - $this->market_value_trade;
        }
        
        $GLOBALS['log']->info("DM_TradeIns: Generated value comparison for trade-in ID: " . $this->id);
        
        return $comparison;
    }

    /**
     * Check if trade-in needs appraisal
     * 
     * @return bool True if appraisal is needed
     */
    public function needsAppraisal()
    {
        // Needs appraisal if no appraised value or if market values are significantly different from asking
        if (empty($this->appraised_value)) {
            return true;
        }
        
        // Check if customer asking is significantly off from market value
        if (!empty($this->customer_asking) && !empty($this->market_value_trade)) {
            $variance = abs($this->customer_asking - $this->market_value_trade);
            $percentVariance = ($variance / $this->market_value_trade) * 100;
            
            // If customer asking is more than 15% off market value, needs appraisal
            if ($percentVariance > 15) {
                $GLOBALS['log']->info("DM_TradeIns: Trade needs appraisal - variance: " . $percentVariance . "%");
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get status display with color coding information
     * 
     * @return array Status info with display color
     */
    public function getStatusInfo()
    {
        $statusInfo = array(
            'status' => $this->status,
            'color' => 'default',
            'description' => ''
        );
        
        switch ($this->status) {
            case 'New':
                $statusInfo['color'] = 'info';
                $statusInfo['description'] = 'New trade-in, pending evaluation';
                break;
            case 'Appraised':
                $statusInfo['color'] = 'warning';
                $statusInfo['description'] = 'Appraised, awaiting approval';
                break;
            case 'Approved':
                $statusInfo['color'] = 'success';
                $statusInfo['description'] = 'Approved for trade allowance';
                break;
            case 'Used':
                $statusInfo['color'] = 'primary';
                $statusInfo['description'] = 'Used in completed deal';
                break;
            case 'Rejected':
                $statusInfo['color'] = 'danger';
                $statusInfo['description'] = 'Rejected for trade';
                break;
            default:
                $statusInfo['description'] = 'Status not set';
        }
        
        return $statusInfo;
    }

    /**
     * Bean relationship setup
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
} 