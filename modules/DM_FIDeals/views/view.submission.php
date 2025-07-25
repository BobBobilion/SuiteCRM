<?php
/**
 * SuiteCRM F&I Deal Center - Manual Deal Submission View
 * 
 * This view generates printable deal summaries for manual submission
 * to lenders via fax or email as part of Phase 3 MVP implementation.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_FIDealsViewSubmission extends SugarView
{
    public $type = 'submission';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Manual submission view initialized");
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
     * Display the manual deal submission form
     */
    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        $GLOBALS['log']->debug("F&I Deal Center: Displaying manual deal submission view");
        
        // Get the deal ID from request
        $dealId = $_REQUEST['record'] ?? '';
        
        if (empty($dealId)) {
            $this->displayError('Deal ID is required for submission');
            return;
        }
        
        // Load the deal
        $deal = BeanFactory::getBean('DM_FIDeals', $dealId);
        if (!$deal || $deal->deleted) {
            $this->displayError('Deal not found or has been deleted');
            return;
        }
        
        // Load related records
        $customer = null;
        $vehicle = null;
        $lender = null;
        
        if (!empty($deal->customer_id)) {
            $customer = BeanFactory::getBean('Accounts', $deal->customer_id);
        }
        
        if (!empty($deal->vehicle_id)) {
            $vehicle = BeanFactory::getBean('DM_VehiclesInventory', $deal->vehicle_id);
        }
        
        if (!empty($deal->lender_id)) {
            $lender = BeanFactory::getBean('Accounts', $deal->lender_id);
        }
        
        // Get submission format from request
        $format = $_REQUEST['format'] ?? 'html';
        
        if ($format === 'pdf') {
            $this->generatePdfSubmission($deal, $customer, $vehicle, $lender);
        } else {
            $this->generateHtmlSubmission($deal, $customer, $vehicle, $lender);
        }
    }
    
    /**
     * Generate HTML submission form
     */
    private function generateHtmlSubmission($deal, $customer, $vehicle, $lender)
    {
        global $mod_strings, $sugar_config;
        
        $GLOBALS['log']->debug("F&I Deal Center: Generating HTML submission for deal: " . $deal->id);
        
        // Set page title
        $this->ss->assign('PAGE_TITLE', 'F&I Deal Submission - ' . $deal->deal_number);
        
        // Calculate payment scenarios for different terms
        $paymentScenarios = $deal->getPaymentScenarios([36, 48, 60, 72, 84]);
        
        // Generate submission data
        $submissionData = array(
            'deal' => $deal,
            'customer' => $customer,
            'vehicle' => $vehicle,
            'lender' => $lender,
            'payment_scenarios' => $paymentScenarios,
            'submission_date' => date('m/d/Y'),
            'submission_time' => date('h:i A'),
            'dealership_name' => $sugar_config['site_name'] ?? 'Dealership',
            'dealership_phone' => $sugar_config['default_phone_number'] ?? '',
            'fi_manager' => $deal->fi_manager_name ?? $deal->assigned_user_name,
        );
        
        // Assign to smarty
        $this->ss->assign('submission_data', $submissionData);
        $this->ss->assign('mod_strings', $mod_strings);
        
        // Set CSS for print formatting
        echo '<style type="text/css">
            @media print {
                .no-print { display: none !important; }
                body { font-size: 12px; }
                .submission-header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                .deal-section { margin-bottom: 15px; page-break-inside: avoid; }
                .payment-table { width: 100%; border-collapse: collapse; }
                .payment-table th, .payment-table td { border: 1px solid #000; padding: 5px; text-align: right; }
                .payment-table th { background-color: #f0f0f0; }
            }
            @media screen {
                .submission-container { max-width: 800px; margin: 0 auto; padding: 20px; }
                .no-print { margin: 20px 0; text-align: center; }
                .btn { padding: 10px 20px; margin: 0 10px; }
            }
        </style>';
        
        // Display the submission form
        $this->displaySubmissionTemplate($submissionData);
    }
    
    /**
     * Display the submission template
     */
    private function displaySubmissionTemplate($data)
    {
        extract($data);
        
        echo '<div class="submission-container">';
        
        // Print controls
        echo '<div class="no-print">';
        echo '<h2>F&I Deal Submission</h2>';
        echo '<button class="btn btn-primary" onclick="window.print()">Print Submission</button>';
        echo '<button class="btn btn-secondary" onclick="window.close()">Close</button>';
        echo '<a href="index.php?module=DM_FIDeals&action=submission&record=' . $deal->id . '&format=pdf" class="btn btn-info">Download PDF</a>';
        echo '</div>';
        
        // Submission header
        echo '<div class="submission-header">';
        echo '<h1 style="margin: 0; text-align: center;">CREDIT APPLICATION SUBMISSION</h1>';
        echo '<div style="text-align: center; margin-top: 10px;">';
        echo '<strong>' . htmlspecialchars($dealership_name) . '</strong><br>';
        if ($dealership_phone) {
            echo 'Phone: ' . htmlspecialchars($dealership_phone) . '<br>';
        }
        echo 'Submission Date: ' . $submission_date . ' at ' . $submission_time;
        echo '</div>';
        echo '</div>';
        
        // Deal information
        echo '<div class="deal-section">';
        echo '<h3>Deal Information</h3>';
        echo '<table style="width: 100%;">';
        echo '<tr><td><strong>Deal Number:</strong></td><td>' . htmlspecialchars($deal->deal_number) . '</td>';
        echo '<td><strong>Deal Status:</strong></td><td>' . htmlspecialchars($deal->deal_status) . '</td></tr>';
        echo '<tr><td><strong>F&I Manager:</strong></td><td>' . htmlspecialchars($fi_manager) . '</td>';
        echo '<td><strong>Finance Method:</strong></td><td>' . htmlspecialchars($deal->finance_method) . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        // Customer information
        if ($customer) {
            echo '<div class="deal-section">';
            echo '<h3>Customer Information</h3>';
            echo '<table style="width: 100%;">';
            echo '<tr><td><strong>Name:</strong></td><td>' . htmlspecialchars($customer->name) . '</td>';
            echo '<td><strong>Phone:</strong></td><td>' . htmlspecialchars($customer->phone_office) . '</td></tr>';
            echo '<tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($customer->email1) . '</td>';
            echo '<td><strong>Type:</strong></td><td>' . htmlspecialchars($customer->account_type) . '</td></tr>';
            if ($customer->billing_address_street) {
                echo '<tr><td><strong>Address:</strong></td><td colspan="3">';
                echo htmlspecialchars($customer->billing_address_street) . '<br>';
                echo htmlspecialchars($customer->billing_address_city . ', ' . $customer->billing_address_state . ' ' . $customer->billing_address_postalcode);
                echo '</td></tr>';
            }
            echo '</table>';
            echo '</div>';
        }
        
        // Vehicle information
        if ($vehicle) {
            echo '<div class="deal-section">';
            echo '<h3>Vehicle Information</h3>';
            echo '<table style="width: 100%;">';
            echo '<tr><td><strong>Vehicle:</strong></td><td>' . htmlspecialchars($vehicle->name) . '</td>';
            echo '<td><strong>VIN:</strong></td><td>' . htmlspecialchars($vehicle->vin ?? 'N/A') . '</td></tr>';
            echo '<tr><td><strong>Year:</strong></td><td>' . htmlspecialchars($vehicle->year ?? 'N/A') . '</td>';
            echo '<td><strong>Make:</strong></td><td>' . htmlspecialchars($vehicle->make ?? 'N/A') . '</td></tr>';
            echo '<tr><td><strong>Model:</strong></td><td>' . htmlspecialchars($vehicle->model ?? 'N/A') . '</td>';
            echo '<td><strong>Mileage:</strong></td><td>' . number_format($vehicle->mileage ?? 0) . '</td></tr>';
            echo '</table>';
            echo '</div>';
        }
        
        // Financial details
        echo '<div class="deal-section">';
        echo '<h3>Financial Details</h3>';
        echo '<table style="width: 100%;">';
        echo '<tr><td><strong>Sales Price:</strong></td><td>$' . number_format($deal->sales_price, 2) . '</td>';
        echo '<td><strong>Down Payment:</strong></td><td>$' . number_format($deal->down_payment, 2) . '</td></tr>';
        echo '<tr><td><strong>Trade Allowance:</strong></td><td>$' . number_format($deal->trade_allowance, 2) . '</td>';
        echo '<td><strong>Trade Payoff:</strong></td><td>$' . number_format($deal->trade_payoff, 2) . '</td></tr>';
        echo '<tr><td><strong>Amount Financed:</strong></td><td>$' . number_format($deal->amount_financed, 2) . '</td>';
        echo '<td><strong>Term Requested:</strong></td><td>' . $deal->term_months . ' months</td></tr>';
        echo '<tr><td><strong>Rate Requested:</strong></td><td>' . number_format($deal->sell_rate, 2) . '%</td>';
        echo '<td><strong>Monthly Payment:</strong></td><td>$' . number_format($deal->monthly_payment, 2) . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        // Payment scenarios
        if (!empty($payment_scenarios)) {
            echo '<div class="deal-section">';
            echo '<h3>Payment Scenarios</h3>';
            echo '<table class="payment-table">';
            echo '<tr><th>Term</th><th>Monthly Payment</th><th>Total of Payments</th><th>Finance Charge</th></tr>';
            foreach ($payment_scenarios as $scenario) {
                echo '<tr>';
                echo '<td>' . $scenario['term_months'] . ' months</td>';
                echo '<td>$' . number_format($scenario['monthly_payment'], 2) . '</td>';
                echo '<td>$' . number_format($scenario['total_of_payments'], 2) . '</td>';
                echo '<td>$' . number_format($scenario['finance_charge'], 2) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }
        
        // Lender information
        if ($lender) {
            echo '<div class="deal-section">';
            echo '<h3>Lender Information</h3>';
            echo '<table style="width: 100%;">';
            echo '<tr><td><strong>Lender:</strong></td><td>' . htmlspecialchars($lender->name) . '</td>';
            echo '<td><strong>Contact:</strong></td><td>' . htmlspecialchars($lender->fi_contact_name ?? 'N/A') . '</td></tr>';
            echo '<tr><td><strong>Phone:</strong></td><td>' . htmlspecialchars($lender->fi_contact_phone ?? $lender->phone_office) . '</td>';
            echo '<td><strong>Email:</strong></td><td>' . htmlspecialchars($lender->submission_email ?? $lender->email1) . '</td></tr>';
            if ($lender->dealer_number) {
                echo '<tr><td><strong>Dealer Number:</strong></td><td>' . htmlspecialchars($lender->dealer_number) . '</td><td colspan="2"></td></tr>';
            }
            echo '</table>';
            echo '</div>';
        }
        
        // Submission footer
        echo '<div class="deal-section" style="margin-top: 30px; text-align: center; font-size: 11px;">';
        echo '<p><strong>This application is submitted for credit approval consideration.</strong></p>';
        echo '<p>For questions regarding this application, please contact the F&I department.</p>';
        echo '<p>Generated on ' . date('m/d/Y \a\t h:i A') . ' by ' . htmlspecialchars($GLOBALS['current_user']->full_name) . '</p>';
        echo '</div>';
        
        echo '</div>'; // Close submission-container
        
        $GLOBALS['log']->debug("F&I Deal Center: HTML submission generated successfully");
    }
    
    /**
     * Generate PDF submission (basic implementation)
     */
    private function generatePdfSubmission($deal, $customer, $vehicle, $lender)
    {
        $GLOBALS['log']->debug("F&I Deal Center: PDF submission requested for deal: " . $deal->id);
        
        // For MVP, we'll use HTML to PDF conversion
        // In a full implementation, this would use proper PDF generation libraries
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="deal-submission-' . $deal->deal_number . '.pdf"');
        
        // Simple HTML to PDF approach (requires additional libraries for production)
        echo "PDF generation will be implemented in a future enhancement.\n";
        echo "For now, please use the Print function from the HTML view to generate PDFs.";
        
        $GLOBALS['log']->debug("F&I Deal Center: PDF submission note displayed");
    }
    
    /**
     * Display error message
     */
    private function displayError($message)
    {
        echo '<div class="alert alert-danger" style="margin: 20px; padding: 15px;">';
        echo '<h3>Error</h3>';
        echo '<p>' . htmlspecialchars($message) . '</p>';
        echo '<a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>';
        echo '</div>';
        
        $GLOBALS['log']->error("F&I Deal Center: Submission error - " . $message);
    }
} 