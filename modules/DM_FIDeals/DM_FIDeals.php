<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM F&I Deal Center - Main Bean Class
 * 
 * This class defines the DM_FIDeals SugarBean entity for managing the financial
 * aspects of vehicle sales, including loan calculations, lender management, 
 * insurance products, warranty offerings, and deal profitability analysis.
 * 
 * Features:
 * - Complete F&I deal structure management
 * - Multi-lender financing integration
 * - Insurance and warranty product sales
 * - Real-time payment calculations
 * - Deal profitability tracking
 * - Compliance and documentation workflow
 * - Integration with vehicle inventory and customer records
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_FIDeals Bean Class
 * 
 * Manages F&I deals with comprehensive financial calculations, product management,
 * lender integration, and compliance tracking for automotive dealership operations
 */
#[\AllowDynamicProperties]
class DM_FIDeals extends Basic
{
    public $table_name = "dm_fideals";
    public $object_name = "DM_FIDeals";
    public $module_dir = "DM_FIDeals";
    public $module_name = "DM_FIDeals";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for F&I deals (financial compliance requirement)
    public $audited = true;
    
    // Core deal identification fields
    public $id;
    public $name;                          // Deal display name
    public $deal_number;                   // Unique deal number
    
    // Relationship links
    public $opportunity_id;                // Link to sales opportunity
    public $customer_id;                   // Link to customer account
    public $vehicle_id;                    // Link to vehicle inventory
    public $tradein_id;                    // Link to trade-in if applicable
    
    // Vehicle pricing structure
    public $sales_price;                   // Vehicle selling price
    public $down_payment;                  // Cash down payment
    public $trade_allowance;               // Trade-in credit amount
    public $trade_payoff;                  // Amount owed on trade
    public $rebates;                       // Manufacturer rebates
    public $dealer_fees;                   // Documentation/dealer fees
    public $government_fees;               // Tax, title, license fees
    
    // Finance structure
    public $finance_method;                // Cash/Finance/Lease
    public $amount_financed;               // Total amount to finance
    public $term_months;                   // Loan term in months
    public $interest_rate;                 // Annual percentage rate (APR)
    public $monthly_payment;               // Calculated monthly payment
    public $total_of_payments;             // Total payments over loan term
    public $finance_charge;                // Total interest paid
    
    // Lender information
    public $lender_id;                     // Selected lender
    public $lender_approval_number;        // Lender approval reference
    public $buy_rate;                      // Lender's actual rate
    public $sell_rate;                     // Customer's rate
    public $rate_markup;                   // Dealer rate markup
    public $finance_reserve;               // Dealer profit on financing
    
    // F&I Product sales
    public $warranty_total;                // Extended warranty amount
    public $gap_amount;                    // GAP insurance amount
    public $etch_amount;                   // Theft protection amount
    public $maintenance_amount;            // Prepaid maintenance amount
    public $other_products_total;          // Other F&I products total
    
    // Profitability tracking
    public $backend_gross;                 // F&I department profit
    public $frontend_gross;                // Vehicle department profit
    public $total_gross;                   // Total deal profit
    
    // Deal workflow and status
    public $deal_status;                   // Draft/Submitted/Approved/Funded/Complete
    public $credit_app_id;                 // Link to credit application
    public $stips_required;                // Lender stipulations (JSON)
    public $funding_date;                  // When funded by lender
    public $contract_date;                 // When contracts signed
    
    // Staff assignments
    public $fi_manager_id;                 // F&I manager assigned
    public $salesperson_id;                // Salesperson assigned
    
    // Additional information
    public $notes;                         // Deal notes and comments
    
    // Standard SugarBean fields
    public $date_entered;
    public $date_modified;
    public $created_by;
    public $modified_user_id;
    public $assigned_user_id;
    public $deleted;
    
    /**
     * Constructor - Initialize the bean with default values
     */
    public function __construct()
    {
        parent::__construct();
        
        // Set default values for new F&I deals
        $this->deal_status = 'Draft';
        $this->finance_method = 'Finance';
        $this->term_months = 60; // Default 5-year term
        $this->interest_rate = 0.00;
        $this->rate_markup = 0.00;
        
        // Initialize monetary fields to zero
        $this->sales_price = 0.00;
        $this->down_payment = 0.00;
        $this->trade_allowance = 0.00;
        $this->trade_payoff = 0.00;
        $this->rebates = 0.00;
        $this->dealer_fees = 0.00;
        $this->government_fees = 0.00;
        $this->amount_financed = 0.00;
        $this->monthly_payment = 0.00;
        $this->total_of_payments = 0.00;
        $this->finance_charge = 0.00;
        $this->finance_reserve = 0.00;
        $this->warranty_total = 0.00;
        $this->gap_amount = 0.00;
        $this->etch_amount = 0.00;
        $this->maintenance_amount = 0.00;
        $this->other_products_total = 0.00;
        $this->backend_gross = 0.00;
        $this->frontend_gross = 0.00;
        $this->total_gross = 0.00;
        
        $GLOBALS['log']->debug("DM_FIDeals: Bean constructor initialized with default values");
    }
    
    /**
     * Calculate the amount to be financed based on deal structure
     * 
     * @return float The calculated amount to finance
     */
    public function calculateAmountFinanced()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Calculating amount financed for deal: " . $this->id);
        
        // Base calculation: Sales Price + Dealer Fees + Government Fees - Down Payment - Trade Allowance + Trade Payoff - Rebates
        $amount = (float)$this->sales_price 
                + (float)$this->dealer_fees 
                + (float)$this->government_fees 
                - (float)$this->down_payment 
                - (float)$this->trade_allowance 
                + (float)$this->trade_payoff 
                - (float)$this->rebates;
        
        // Add F&I products to amount financed
        $amount += (float)$this->warranty_total;
        $amount += (float)$this->gap_amount;
        $amount += (float)$this->etch_amount;
        $amount += (float)$this->maintenance_amount;
        $amount += (float)$this->other_products_total;
        
        // Ensure amount is not negative
        $amount = max(0, $amount);
        
        $this->amount_financed = number_format($amount, 2, '.', '');
        
        $GLOBALS['log']->debug("DM_FIDeals: Amount financed calculated as: $" . $this->amount_financed);
        
        return $this->amount_financed;
    }
    
    /**
     * Calculate monthly payment based on amount financed, term, and interest rate
     * 
     * @return float The calculated monthly payment
     */
    public function calculateMonthlyPayment()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Calculating monthly payment for deal: " . $this->id);
        
        $principal = (float)$this->amount_financed;
        $term = (int)$this->term_months;
        $rate = (float)$this->sell_rate / 100; // Convert percentage to decimal
        
        // Handle cash deals (no financing)
        if ($this->finance_method === 'Cash' || $principal <= 0 || $term <= 0 || $rate <= 0) {
            $this->monthly_payment = 0.00;
            $this->total_of_payments = $principal;
            $this->finance_charge = 0.00;
            
            $GLOBALS['log']->debug("DM_FIDeals: Cash deal or zero values - no payment calculation needed");
            return $this->monthly_payment;
        }
        
        // Calculate monthly interest rate
        $monthlyRate = $rate / 12;
        
        // Standard loan payment formula: P * [r(1+r)^n] / [(1+r)^n - 1]
        if ($monthlyRate > 0) {
            $payment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $term)) / (pow(1 + $monthlyRate, $term) - 1);
        } else {
            // Zero interest rate - simple division
            $payment = $principal / $term;
        }
        
        $this->monthly_payment = number_format($payment, 2, '.', '');
        $this->total_of_payments = number_format($payment * $term, 2, '.', '');
        $this->finance_charge = number_format(($payment * $term) - $principal, 2, '.', '');
        
        $GLOBALS['log']->debug("DM_FIDeals: Monthly payment calculated - Payment: $" . $this->monthly_payment . 
                              ", Total: $" . $this->total_of_payments . 
                              ", Finance Charge: $" . $this->finance_charge);
        
        return $this->monthly_payment;
    }
    
    /**
     * Calculate dealer finance reserve based on rate markup
     * 
     * @return float The calculated finance reserve profit
     */
    public function calculateFinanceReserve()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Calculating finance reserve for deal: " . $this->id);
        
        $principal = (float)$this->amount_financed;
        $term = (int)$this->term_months;
        $markup = (float)$this->rate_markup;
        
        if ($principal <= 0 || $term <= 0 || $markup <= 0) {
            $this->finance_reserve = 0.00;
            $GLOBALS['log']->debug("DM_FIDeals: No finance reserve - zero values");
            return $this->finance_reserve;
        }
        
        // Calculate reserve based on markup percentage and loan term
        // Formula: (Principal * Markup% * Term) / 12 / 100
        $reserve = ($principal * $markup * $term) / 12 / 100;
        
        $this->finance_reserve = number_format($reserve, 2, '.', '');
        
        $GLOBALS['log']->debug("DM_FIDeals: Finance reserve calculated as: $" . $this->finance_reserve);
        
        return $this->finance_reserve;
    }
    
    /**
     * Calculate total backend gross profit (F&I department profit)
     * 
     * @return float The calculated backend gross profit
     */
    public function calculateBackendGross()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Calculating backend gross profit for deal: " . $this->id);
        
        // Backend gross = Finance Reserve + Product Profits
        $backend = (float)$this->finance_reserve;
        
        // Add estimated profit from F&I products (typically 20-40% markup)
        // For now, using conservative 25% profit margin estimate
        $productProfit = ((float)$this->warranty_total + 
                         (float)$this->gap_amount + 
                         (float)$this->etch_amount + 
                         (float)$this->maintenance_amount + 
                         (float)$this->other_products_total) * 0.25;
        
        $backend += $productProfit;
        
        $this->backend_gross = number_format($backend, 2, '.', '');
        
        $GLOBALS['log']->debug("DM_FIDeals: Backend gross calculated as: $" . $this->backend_gross);
        
        return $this->backend_gross;
    }
    
    /**
     * Calculate total deal gross profit
     * 
     * @return float The calculated total gross profit
     */
    public function calculateTotalGross()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Calculating total gross profit for deal: " . $this->id);
        
        $total = (float)$this->frontend_gross + (float)$this->backend_gross;
        
        $this->total_gross = number_format($total, 2, '.', '');
        
        $GLOBALS['log']->debug("DM_FIDeals: Total gross calculated as: $" . $this->total_gross);
        
        return $this->total_gross;
    }
    
    /**
     * Recalculate all financial aspects of the deal
     * Called whenever deal structure changes
     */
    public function recalculateAll()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Recalculating all financial aspects for deal: " . $this->id);
        
        // Calculate in proper order due to dependencies
        $this->calculateAmountFinanced();
        $this->calculateMonthlyPayment();
        $this->calculateFinanceReserve();
        $this->calculateBackendGross();
        $this->calculateTotalGross();
        
        $GLOBALS['log']->debug("DM_FIDeals: All calculations completed for deal: " . $this->id);
    }
    
    /**
     * Generate a unique deal number
     * 
     * @return string Formatted deal number
     */
    public function generateDealNumber()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Generating deal number");
        
        // Format: FI-YYYYMMDD-#### (F&I + Date + Sequential)
        $date = date('Ymd');
        
        // Find the next sequential number for today
        $query = "SELECT COUNT(*) as count FROM {$this->table_name} 
                 WHERE deal_number LIKE 'FI-{$date}-%' 
                 AND deleted = 0";
        
        $result = $this->db->query($query);
        $row = $this->db->fetchByAssoc($result);
        $sequence = ((int)$row['count']) + 1;
        
        $dealNumber = sprintf('FI-%s-%04d', $date, $sequence);
        
        $GLOBALS['log']->debug("DM_FIDeals: Generated deal number: " . $dealNumber);
        
        return $dealNumber;
    }
    
    /**
     * Override save to perform calculations and validations
     */
    public function save($check_notify = false)
    {
        $GLOBALS['log']->debug("DM_FIDeals: Saving deal with ID: " . $this->id);
        
        // Generate deal number for new records
        if (empty($this->deal_number)) {
            $this->deal_number = $this->generateDealNumber();
            $GLOBALS['log']->debug("DM_FIDeals: Generated new deal number: " . $this->deal_number);
        }
        
        // Generate name if empty (format: "Deal XXXX - Customer Name")
        if (empty($this->name) && !empty($this->deal_number)) {
            $this->name = "Deal " . $this->deal_number;
            
            // Try to append customer name if available
            if (!empty($this->customer_id)) {
                $customer = BeanFactory::getBean('Accounts', $this->customer_id);
                if ($customer && !empty($customer->name)) {
                    $this->name .= " - " . $customer->name;
                }
            }
            
            $GLOBALS['log']->debug("DM_FIDeals: Generated deal name: " . $this->name);
        }
        
        // Recalculate all financial aspects before saving
        $this->recalculateAll();
        
        // Validate required fields for certain statuses
        if ($this->deal_status !== 'Draft') {
            $this->validateRequiredFields();
        }
        
        $GLOBALS['log']->debug("DM_FIDeals: Pre-save calculations and validations complete");
        
        return parent::save($check_notify);
    }
    
    /**
     * Validate required fields based on deal status
     * 
     * @throws Exception if validation fails
     */
    private function validateRequiredFields()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Validating required fields for status: " . $this->deal_status);
        
        $errors = array();
        
        // Required for all non-draft deals
        if (empty($this->customer_id)) {
            $errors[] = "Customer is required";
        }
        
        if (empty($this->vehicle_id)) {
            $errors[] = "Vehicle is required";
        }
        
        if (empty($this->sales_price) || (float)$this->sales_price <= 0) {
            $errors[] = "Valid sales price is required";
        }
        
        // Additional validation for financed deals
        if ($this->finance_method === 'Finance' || $this->finance_method === 'Lease') {
            if (empty($this->lender_id)) {
                $errors[] = "Lender is required for financed deals";
            }
            
            if (empty($this->term_months) || (int)$this->term_months <= 0) {
                $errors[] = "Valid loan term is required for financed deals";
            }
            
            if (empty($this->sell_rate) || (float)$this->sell_rate < 0) {
                $errors[] = "Valid interest rate is required for financed deals";
            }
        }
        
        if (!empty($errors)) {
            $errorMessage = "F&I Deal validation failed: " . implode(", ", $errors);
            $GLOBALS['log']->error("DM_FIDeals: " . $errorMessage);
            throw new Exception($errorMessage);
        }
        
        $GLOBALS['log']->debug("DM_FIDeals: All validations passed");
    }
    
    /**
     * Get deal summary for reporting and display
     * 
     * @return array Deal summary data
     */
    public function getDealSummary()
    {
        $GLOBALS['log']->debug("DM_FIDeals: Getting deal summary for deal: " . $this->id);
        
        return array(
            'deal_number' => $this->deal_number,
            'status' => $this->deal_status,
            'finance_method' => $this->finance_method,
            'sales_price' => $this->sales_price,
            'amount_financed' => $this->amount_financed,
            'monthly_payment' => $this->monthly_payment,
            'term_months' => $this->term_months,
            'interest_rate' => $this->sell_rate,
            'frontend_gross' => $this->frontend_gross,
            'backend_gross' => $this->backend_gross,
            'total_gross' => $this->total_gross,
            'date_entered' => $this->date_entered
        );
    }
    
    /**
     * Get payment scenarios for customer presentation
     * 
     * @param array $terms Array of term options (months)
     * @return array Payment calculations for different terms
     */
    public function getPaymentScenarios($terms = array(36, 48, 60, 72, 84))
    {
        $GLOBALS['log']->debug("DM_FIDeals: Generating payment scenarios for deal: " . $this->id);
        
        $scenarios = array();
        $originalTerm = $this->term_months;
        
        foreach ($terms as $term) {
            $this->term_months = $term;
            $this->calculateMonthlyPayment();
            
            $scenarios[] = array(
                'term_months' => $term,
                'monthly_payment' => $this->monthly_payment,
                'total_of_payments' => $this->total_of_payments,
                'finance_charge' => $this->finance_charge
            );
        }
        
        // Restore original term
        $this->term_months = $originalTerm;
        $this->calculateMonthlyPayment();
        
        $GLOBALS['log']->debug("DM_FIDeals: Generated " . count($scenarios) . " payment scenarios");
        
        return $scenarios;
    }
} 