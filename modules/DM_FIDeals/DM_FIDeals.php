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
    
    /**
     * Calculate state and local taxes based on customer location
     * 
     * @param string $state Customer's state
     * @param float $taxableAmount Amount subject to tax
     * @return array Tax breakdown
     */
    public function calculateTaxes($state = null, $taxableAmount = null)
    {
        $state = $state ?: $this->customer_state ?: 'DEFAULT';
        $taxableAmount = $taxableAmount ?: max(0, ($this->sales_price ?: 0) - ($this->trade_allowance ?: 0));
        
        // Tax rates by state (should be configurable via Admin)
        $taxRates = array(
            'CA' => array('state' => 7.25, 'avgLocal' => 3.33),
            'TX' => array('state' => 6.25, 'avgLocal' => 2.00),
            'FL' => array('state' => 6.00, 'avgLocal' => 1.05),
            'NY' => array('state' => 4.00, 'avgLocal' => 4.49),
            'IL' => array('state' => 6.25, 'avgLocal' => 2.55),
            'PA' => array('state' => 6.00, 'avgLocal' => 0.34),
            'OH' => array('state' => 5.75, 'avgLocal' => 2.25),
            'GA' => array('state' => 4.00, 'avgLocal' => 3.29),
            'NC' => array('state' => 4.75, 'avgLocal' => 2.22),
            'MI' => array('state' => 6.00, 'avgLocal' => 0.00),
            'DEFAULT' => array('state' => 6.00, 'avgLocal' => 2.50)
        );
        
        $rates = $taxRates[$state] ?: $taxRates['DEFAULT'];
        $stateTaxRate = $rates['state'] / 100;
        $localTaxRate = $rates['avgLocal'] / 100;
        $totalTaxRate = $stateTaxRate + $localTaxRate;
        
        $stateTax = $taxableAmount * $stateTaxRate;
        $localTax = $taxableAmount * $localTaxRate;
        $totalTax = $taxableAmount * $totalTaxRate;
        
        $GLOBALS['log']->debug("DM_FIDeals: Calculated taxes for $state - Total: $totalTax");
        
        return array(
            'state_tax' => $stateTax,
            'local_tax' => $localTax,
            'total_tax' => $totalTax,
            'state_rate' => $stateTaxRate,
            'local_rate' => $localTaxRate,
            'total_rate' => $totalTaxRate,
            'taxable_amount' => $taxableAmount
        );
    }
    
    /**
     * Calculate state-specific fees (documentation, title, license, etc.)
     * 
     * @param string $state Customer's state
     * @return array Fee breakdown
     */
    public function calculateStateFees($state = null)
    {
        $state = $state ?: $this->customer_state ?: 'DEFAULT';
        
        // Fee schedules by state (should be configurable via Admin)
        $feeSchedules = array(
            'CA' => array(
                'doc_fee' => 85.00,
                'title_fee' => 23.00,
                'license_fee' => 46.00,
                'smog_fee' => 8.25,
                'other_fees' => 20.00
            ),
            'TX' => array(
                'doc_fee' => 150.00,
                'title_fee' => 33.00,
                'license_fee' => 51.75,
                'inspection_fee' => 7.00,
                'other_fees' => 25.00
            ),
            'FL' => array(
                'doc_fee' => 995.00,
                'title_fee' => 77.25,
                'license_fee' => 225.00,
                'other_fees' => 50.00
            ),
            'DEFAULT' => array(
                'doc_fee' => 300.00,
                'title_fee' => 50.00,
                'license_fee' => 100.00,
                'other_fees' => 25.00
            )
        );
        
        $fees = $feeSchedules[$state] ?: $feeSchedules['DEFAULT'];
        $totalFees = array_sum($fees);
        
        $GLOBALS['log']->debug("DM_FIDeals: Calculated state fees for $state - Total: $totalFees");
        
        return array_merge($fees, array('total_fees' => $totalFees));
    }
    
    /**
     * Calculate lease payment using standard lease formula
     * 
     * @param float $vehiclePrice MSRP or selling price
     * @param float $residualPercent Residual value as percentage
     * @param float $moneyFactor Lease rate (equivalent to interest rate / 2400)
     * @param int $termMonths Lease term in months
     * @param float $downPayment Cash down payment
     * @return array Lease calculation breakdown
     */
    public function calculateLeasePayment($vehiclePrice = null, $residualPercent = null, $moneyFactor = null, $termMonths = null, $downPayment = null)
    {
        $vehiclePrice = $vehiclePrice ?: ($this->sales_price ?: 0);
        $residualPercent = $residualPercent ?: ($this->residual_percent ?: 55); // Default 55%
        $moneyFactor = $moneyFactor ?: ($this->money_factor ?: 0.0025); // Default money factor
        $termMonths = $termMonths ?: ($this->term_months ?: 36); // Default 36 months
        $downPayment = $downPayment ?: ($this->down_payment ?: 0);
        
        if ($vehiclePrice <= 0 || $termMonths <= 0) {
            return array(
                'monthly_payment' => 0,
                'depreciation_payment' => 0,
                'finance_payment' => 0,
                'residual_value' => 0,
                'total_payments' => 0,
                'cap_cost' => 0
            );
        }
        
        // Calculate lease components
        $residualValue = $vehiclePrice * ($residualPercent / 100);
        $capCost = $vehiclePrice - $downPayment; // Capitalized cost
        $depreciationAmount = $capCost - $residualValue;
        
        // Monthly depreciation payment
        $depreciationPayment = $depreciationAmount / $termMonths;
        
        // Monthly finance payment (rent charge)
        $financePayment = ($capCost + $residualValue) * $moneyFactor;
        
        // Total monthly payment
        $monthlyPayment = $depreciationPayment + $financePayment;
        $totalPayments = $monthlyPayment * $termMonths + $downPayment;
        
        $GLOBALS['log']->debug("DM_FIDeals: Calculated lease payment - Monthly: $monthlyPayment");
        
        return array(
            'monthly_payment' => $monthlyPayment,
            'depreciation_payment' => $depreciationPayment,
            'finance_payment' => $financePayment,
            'residual_value' => $residualValue,
            'total_payments' => $totalPayments,
            'cap_cost' => $capCost,
            'depreciation_amount' => $depreciationAmount
        );
    }
    
    /**
     * Generate amortization schedule for a loan
     * 
     * @param float $principal Loan amount
     * @param float $annualRate Annual interest rate (as percentage)
     * @param int $termMonths Loan term in months
     * @return array Monthly payment schedule
     */
    public function generateAmortizationSchedule($principal = null, $annualRate = null, $termMonths = null)
    {
        $principal = $principal ?: ($this->amount_financed ?: 0);
        $annualRate = $annualRate ?: ($this->interest_rate ?: 0);
        $termMonths = $termMonths ?: ($this->term_months ?: 60);
        
        if ($principal <= 0 || $termMonths <= 0) {
            return array();
        }
        
        $monthlyRate = ($annualRate / 100) / 12;
        $schedule = array();
        $balance = $principal;
        
        // Calculate monthly payment
        if ($monthlyRate > 0) {
            $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        } else {
            $monthlyPayment = $principal / $termMonths;
        }
        
        for ($month = 1; $month <= $termMonths; $month++) {
            $interestPayment = $balance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interestPayment;
            $balance -= $principalPayment;
            
            // Ensure balance doesn't go negative on final payment
            if ($month == $termMonths && $balance < 0) {
                $principalPayment += $balance;
                $balance = 0;
            }
            
            $schedule[] = array(
                'payment_number' => $month,
                'payment_amount' => $monthlyPayment,
                'principal_payment' => $principalPayment,
                'interest_payment' => $interestPayment,
                'remaining_balance' => max(0, $balance)
            );
        }
        
        $GLOBALS['log']->debug("DM_FIDeals: Generated amortization schedule for $termMonths months");
        
        return $schedule;
    }
    
    /**
     * Calculate balloon payment option
     * 
     * @param float $balloonPercent Percentage of principal due at end
     * @param int $balloonTerm Term before balloon payment
     * @return array Balloon payment calculation
     */
    public function calculateBalloonPayment($balloonPercent = null, $balloonTerm = null)
    {
        $principal = $this->amount_financed ?: 0;
        $annualRate = $this->interest_rate ?: 0;
        $balloonPercent = $balloonPercent ?: 25; // Default 25% balloon
        $balloonTerm = $balloonTerm ?: ($this->term_months ?: 60);
        
        if ($principal <= 0 || $balloonTerm <= 0) {
            return array(
                'monthly_payment' => 0,
                'balloon_amount' => 0,
                'total_interest' => 0,
                'total_payments' => 0
            );
        }
        
        $balloonAmount = $principal * ($balloonPercent / 100);
        $financedAmount = $principal - $balloonAmount;
        $monthlyRate = ($annualRate / 100) / 12;
        
        // Calculate monthly payment for financed portion
        if ($monthlyRate > 0) {
            $monthlyPayment = $financedAmount * ($monthlyRate * pow(1 + $monthlyRate, $balloonTerm)) / (pow(1 + $monthlyRate, $balloonTerm) - 1);
        } else {
            $monthlyPayment = $financedAmount / $balloonTerm;
        }
        
        $totalInterest = ($monthlyPayment * $balloonTerm) - $financedAmount;
        $totalPayments = ($monthlyPayment * $balloonTerm) + $balloonAmount;
        
        $GLOBALS['log']->debug("DM_FIDeals: Calculated balloon payment - Monthly: $monthlyPayment, Balloon: $balloonAmount");
        
        return array(
            'monthly_payment' => $monthlyPayment,
            'balloon_amount' => $balloonAmount,
            'financed_amount' => $financedAmount,
            'total_interest' => $totalInterest,
            'total_payments' => $totalPayments
        );
    }
    
    /**
     * Calculate comprehensive product commission breakdown
     * 
     * @return array Commission details for all F&I products
     */
    public function calculateProductCommissions()
    {
        // Commission rates (should be configurable via Admin)
        $commissionRates = array(
            'warranty' => 0.25,      // 25% commission on extended warranty
            'gap' => 0.50,           // 50% commission on GAP insurance
            'etch' => 0.80,          // 80% commission on theft protection
            'maintenance' => 0.30,   // 30% commission on maintenance plans
            'life_disability' => 0.40, // 40% commission on credit life/disability
            'other_products' => 0.40  // 40% commission on other products
        );
        
        $warrantyCommission = ($this->warranty_total ?: 0) * $commissionRates['warranty'];
        $gapCommission = ($this->gap_amount ?: 0) * $commissionRates['gap'];
        $etchCommission = ($this->etch_amount ?: 0) * $commissionRates['etch'];
        $maintenanceCommission = ($this->maintenance_amount ?: 0) * $commissionRates['maintenance'];
        $otherCommission = ($this->other_products_total ?: 0) * $commissionRates['other_products'];
        
        $totalProductCommission = $warrantyCommission + $gapCommission + $etchCommission + 
                                 $maintenanceCommission + $otherCommission;
        
        $GLOBALS['log']->debug("DM_FIDeals: Calculated product commissions - Total: $totalProductCommission");
        
        return array(
            'warranty_commission' => $warrantyCommission,
            'gap_commission' => $gapCommission,
            'etch_commission' => $etchCommission,
            'maintenance_commission' => $maintenanceCommission,
            'other_commission' => $otherCommission,
            'total_product_commission' => $totalProductCommission,
            'commission_rates' => $commissionRates
        );
    }
    
    /**
     * Calculate cash vs finance comparison
     * 
     * @param float $cashPrice Alternative cash price
     * @return array Comparison analysis
     */
    public function calculateCashVsFinanceComparison($cashPrice = null)
    {
        $cashPrice = $cashPrice ?: (($this->sales_price ?: 0) * 0.95); // Default 5% cash discount
        $financePrice = $this->sales_price ?: 0;
        $totalOfPayments = $this->total_of_payments ?: 0;
        $downPayment = $this->down_payment ?: 0;
        
        // Calculate cash scenario
        $cashTotal = $cashPrice + ($this->government_fees ?: 0) + ($this->dealer_fees ?: 0);
        $cashSavings = $totalOfPayments - $cashTotal;
        
        // Calculate finance scenario
        $financeTotal = $totalOfPayments + $downPayment;
        $monthlyPayment = $this->monthly_payment ?: 0;
        
        // Investment opportunity calculation (what if cash was invested instead)
        $assumedReturnRate = 0.07; // 7% annual return
        $termYears = ($this->term_months ?: 60) / 12;
        $investmentValue = $cashTotal * pow(1 + $assumedReturnRate, $termYears);
        $opportunityCost = $investmentValue - $cashTotal;
        
        $GLOBALS['log']->debug("DM_FIDeals: Calculated cash vs finance comparison");
        
        return array(
            'cash_price' => $cashPrice,
            'cash_total' => $cashTotal,
            'finance_total' => $financeTotal,
            'cash_savings' => $cashSavings,
            'monthly_payment' => $monthlyPayment,
            'opportunity_cost' => $opportunityCost,
            'investment_value' => $investmentValue,
            'net_advantage_cash' => $cashSavings - $opportunityCost,
            'break_even_rate' => ($totalOfPayments - $cashTotal) / $cashTotal / $termYears
        );
    }
    
    /**
     * Validate deal data for completeness and accuracy
     * 
     * @return array Validation results
     */
    public function validateDealData()
    {
        $errors = array();
        $warnings = array();
        
        // Required field validation
        if (empty($this->sales_price) || $this->sales_price <= 0) {
            $errors[] = "Sales price is required and must be greater than zero";
        }
        
        if (empty($this->customer_id)) {
            $errors[] = "Customer must be selected";
        }
        
        if (empty($this->vehicle_id)) {
            $errors[] = "Vehicle must be selected";
        }
        
        // Finance method specific validation
        if ($this->finance_method === 'Finance') {
            if (empty($this->term_months) || $this->term_months < 12 || $this->term_months > 96) {
                $errors[] = "Loan term must be between 12 and 96 months";
            }
            
            if ($this->interest_rate < 0 || $this->interest_rate > 29.99) {
                $errors[] = "Interest rate must be between 0% and 29.99%";
            }
            
            if (($this->amount_financed ?: 0) <= 0) {
                $warnings[] = "Amount financed should be greater than zero for financed deals";
            }
        }
        
        // Business rule validation
        if (($this->trade_allowance ?: 0) > ($this->sales_price ?: 0)) {
            $warnings[] = "Trade allowance exceeds sales price";
        }
        
        if (($this->down_payment ?: 0) > ($this->sales_price ?: 0)) {
            $warnings[] = "Down payment exceeds sales price";
        }
        
        // Profitability warnings
        $totalGross = $this->total_gross ?: 0;
        if ($totalGross < 1000) {
            $warnings[] = "Total gross profit is below recommended minimum ($1,000)";
        }
        
        $GLOBALS['log']->debug("DM_FIDeals: Validation completed - " . count($errors) . " errors, " . count($warnings) . " warnings");
        
        return array(
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'error_count' => count($errors),
            'warning_count' => count($warnings)
        );
    }
} 