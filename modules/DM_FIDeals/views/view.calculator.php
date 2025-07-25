<?php
/**
 * SuiteCRM F&I Deal Center - Standalone Calculator View
 * 
 * This view provides a standalone payment calculator interface
 * for quick calculations without requiring a specific deal record.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_FIDealsViewCalculator extends SugarView
{
    public $type = 'calculator';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Calculator view initialized");
    }
    
    /**
     * Preprocess the view
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Check if user has access to this module
        if (!ACLController::checkAccess('DM_FIDeals', 'view', true)) {
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
    }
    
    /**
     * Display the calculator interface
     */
    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        $GLOBALS['log']->debug("F&I Deal Center: Displaying calculator view");
        
        // Set page title
        $this->ss->assign('MOD', $mod_strings);
        $this->ss->assign('APP', $app_strings);
        
        // Include JavaScript
        echo '<script type="text/javascript" src="modules/DM_FIDeals/js/DM_FIDeals.js"></script>';
        
        // Display the calculator interface
        $this->displayCalculatorInterface();
    }
    
    /**
     * Display the main calculator interface
     */
    private function displayCalculatorInterface()
    {
        echo '
        <div class="moduleTitle">
            <h2><i class="fa fa-calculator"></i> F&I Payment Calculator</h2>
            <p>Professional automotive financing calculator for quick payment estimates</p>
        </div>
        
        <div class="clear"></div>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-car"></i> Vehicle & Finance Details</h3>
                        </div>
                        <div class="panel-body">
                            <form id="calculator-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="calc_sales_price">Vehicle Sales Price:</label>
                                            <input type="text" id="calc_sales_price" class="form-control currency-field" 
                                                   placeholder="$25,000.00" onchange="calculatePayments();">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="calc_down_payment">Down Payment:</label>
                                            <input type="text" id="calc_down_payment" class="form-control currency-field" 
                                                   placeholder="$5,000.00" onchange="calculatePayments();">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="calc_trade_allowance">Trade-In Allowance:</label>
                                            <input type="text" id="calc_trade_allowance" class="form-control currency-field" 
                                                   placeholder="$8,000.00" onchange="calculatePayments();">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="calc_trade_payoff">Trade-In Payoff:</label>
                                            <input type="text" id="calc_trade_payoff" class="form-control currency-field" 
                                                   placeholder="$6,000.00" onchange="calculatePayments();">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="calc_interest_rate">Interest Rate (APR):</label>
                                            <div class="input-group">
                                                <input type="number" id="calc_interest_rate" class="form-control" 
                                                       placeholder="7.99" step="0.01" min="0" max="30" onchange="calculatePayments();">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="calc_term_months">Loan Term:</label>
                                            <select id="calc_term_months" class="form-control" onchange="calculatePayments();">
                                                <option value="36">36 months</option>
                                                <option value="48">48 months</option>
                                                <option value="60" selected>60 months</option>
                                                <option value="72">72 months</option>
                                                <option value="84">84 months</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="calc_taxes_fees">Taxes & Fees:</label>
                                            <input type="text" id="calc_taxes_fees" class="form-control currency-field" 
                                                   placeholder="$2,500.00" onchange="calculatePayments();">
                                        </div>
                                        
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary btn-block" onclick="showAllScenarios();">
                                                <i class="fa fa-table"></i> Payment Scenarios
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-dollar"></i> Payment Results</h3>
                        </div>
                        <div class="panel-body">
                            <div class="result-item">
                                <label>Amount Financed:</label>
                                <div class="result-value" id="calc_amount_financed">$0.00</div>
                            </div>
                            
                            <div class="result-item">
                                <label>Monthly Payment:</label>
                                <div class="result-value highlight" id="calc_monthly_payment">$0.00</div>
                            </div>
                            
                            <div class="result-item">
                                <label>Total of Payments:</label>
                                <div class="result-value" id="calc_total_payments">$0.00</div>
                            </div>
                            
                            <div class="result-item">
                                <label>Finance Charge:</label>
                                <div class="result-value" id="calc_finance_charge">$0.00</div>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <button type="button" class="btn btn-success btn-sm" onclick="createDealFromCalculator();">
                                    <i class="fa fa-plus"></i> Create F&I Deal
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-info-circle"></i> Calculator Tips</h3>
                        </div>
                        <div class="panel-body">
                            <ul class="tips-list">
                                <li>Enter vehicle price including any dealer add-ons</li>
                                <li>Trade allowance minus payoff = net trade equity</li>
                                <li>Include all taxes, title, and license fees</li>
                                <li>Interest rate should be the customer rate (not buy rate)</li>
                                <li>All calculations update automatically</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .result-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .result-item label {
            font-weight: bold;
            margin: 0;
        }
        
        .result-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .result-value.highlight {
            font-size: 20px;
            color: #27ae60;
        }
        
        .tips-list {
            padding-left: 20px;
        }
        
        .tips-list li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .currency-field {
            text-align: right;
        }
        </style>
        
        <script>
        // Initialize calculator when page loads
        $(document).ready(function() {
            // Format currency fields
            $(".currency-field").on("blur", function() {
                let value = parseFloat(this.value.replace(/[^0-9.-]/g, ""));
                if (!isNaN(value)) {
                    this.value = "$" + value.toLocaleString("en-US", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            });
            
            // Calculate initial values
            calculatePayments();
        });
        
        function calculatePayments() {
            const salesPrice = parseFloat($("#calc_sales_price").val().replace(/[^0-9.-]/g, "")) || 0;
            const downPayment = parseFloat($("#calc_down_payment").val().replace(/[^0-9.-]/g, "")) || 0;
            const tradeAllowance = parseFloat($("#calc_trade_allowance").val().replace(/[^0-9.-]/g, "")) || 0;
            const tradePayoff = parseFloat($("#calc_trade_payoff").val().replace(/[^0-9.-]/g, "")) || 0;
            const taxesFees = parseFloat($("#calc_taxes_fees").val().replace(/[^0-9.-]/g, "")) || 0;
            const interestRate = parseFloat($("#calc_interest_rate").val()) || 0;
            const termMonths = parseInt($("#calc_term_months").val()) || 60;
            
            // Calculate amount financed
            const netTrade = tradeAllowance - tradePayoff;
            const amountFinanced = salesPrice - downPayment - netTrade + taxesFees;
            
            // Calculate monthly payment
            let monthlyPayment = 0;
            let totalPayments = 0;
            let financeCharge = 0;
            
            if (amountFinanced > 0) {
                if (interestRate > 0) {
                    const monthlyRate = interestRate / 100 / 12;
                    const termPower = Math.pow(1 + monthlyRate, termMonths);
                    monthlyPayment = amountFinanced * (monthlyRate * termPower) / (termPower - 1);
                } else {
                    monthlyPayment = amountFinanced / termMonths;
                }
                
                totalPayments = monthlyPayment * termMonths;
                financeCharge = totalPayments - amountFinanced;
            }
            
            // Update display
            $("#calc_amount_financed").text("$" + amountFinanced.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            
            $("#calc_monthly_payment").text("$" + monthlyPayment.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            
            $("#calc_total_payments").text("$" + totalPayments.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            
            $("#calc_finance_charge").text("$" + financeCharge.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }
        
        function showAllScenarios() {
            if (SUGAR.DM_FIDeals && SUGAR.DM_FIDeals.generatePaymentScenarios) {
                // Get current values and temporarily set them for scenarios
                const amountFinanced = parseFloat($("#calc_amount_financed").text().replace(/[^0-9.-]/g, ""));
                const interestRate = parseFloat($("#calc_interest_rate").val()) || 7.99;
                
                if (amountFinanced > 0) {
                    SUGAR.DM_FIDeals.tempScenarioData = {
                        principal: amountFinanced,
                        rate: interestRate
                    };
                    SUGAR.DM_FIDeals.generatePaymentScenarios();
                } else {
                    alert("Please enter vehicle and finance details first.");
                }
            }
        }
        
        function createDealFromCalculator() {
            // Collect all calculator values
            const dealData = {
                sales_price: $("#calc_sales_price").val(),
                down_payment: $("#calc_down_payment").val(),
                trade_allowance: $("#calc_trade_allowance").val(),
                trade_payoff: $("#calc_trade_payoff").val(),
                interest_rate: $("#calc_interest_rate").val(),
                term_months: $("#calc_term_months").val(),
                taxes_fees: $("#calc_taxes_fees").val()
            };
            
            // Build URL with parameters
            let url = "index.php?module=DM_FIDeals&action=EditView";
            Object.keys(dealData).forEach(key => {
                if (dealData[key]) {
                    url += "&" + key + "=" + encodeURIComponent(dealData[key]);
                }
            });
            
            // Open new deal with pre-filled data
            window.location.href = url;
        }
        </script>
        ';
    }
}
?> 