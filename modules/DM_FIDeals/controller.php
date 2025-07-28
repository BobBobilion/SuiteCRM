<?php
/**
 * SuiteCRM F&I Deal Center - Controller
 * 
 * This controller handles custom actions for the F&I Deal Center module
 * including reports, analytics, product selection, and other specialized views.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

class DM_FIDealsController extends SugarController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Controller initialized");
    }

    /**
     * Pre-action processing
     */
    public function pre_action()
    {
        parent::pre_action();
        
        // Check if user has access to this module
        if (!ACLController::checkAccess('DM_FIDeals', 'view', true)) {
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
    }

    /**
     * Handle reports action
     */
    public function action_reports()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Reports action called");
        
        // Set up the view
        $this->view = 'reports';
        $this->view_object_map['reports'] = 'modules/DM_FIDeals/views/view.reports.php';
    }

    /**
     * Handle PDF download action
     */
    public function action_downloadPDF()
    {
        $GLOBALS['log']->debug("F&I Deal Center: PDF download action called");
        
        $recordId = $_REQUEST['record'] ?? '';
        
        if (empty($recordId)) {
            echo "Error: No record ID provided for PDF generation.";
            return;
        }
        
        // Load the deal record
        $deal = BeanFactory::retrieveBean('DM_FIDeals', $recordId);
        
        if (!$deal || empty($deal->id)) {
            echo "Error: Deal record not found.";
            return;
        }
        
        // Generate PDF content
        $this->generateDealWorksheetPDF($deal);
    }
    
    /**
     * Generate PDF worksheet for the deal
     */
    private function generateDealWorksheetPDF($deal)
    {
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="deal_worksheet_' . $deal->id . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Simple PDF content (you could integrate TCPDF or other PDF library here)
        $html = $this->buildDealWorksheetHTML($deal);
        
        // For now, convert HTML to a simple text-based PDF
        // In a real implementation, you'd use a proper PDF library
        $this->outputSimplePDF($html, $deal);
    }
    
    /**
     * Build HTML content for the deal worksheet
     */
    private function buildDealWorksheetHTML($deal)
    {
        $html = '
        <html>
        <head>
            <title>Deal Worksheet - ' . htmlspecialchars($deal->name ?? 'Deal') . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .section { margin-bottom: 20px; }
                .label { font-weight: bold; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>F&I Deal Worksheet</h1>
                <h2>' . htmlspecialchars($deal->name ?? 'Deal') . '</h2>
                <p>Generated: ' . date('Y-m-d H:i:s') . '</p>
            </div>
            
            <div class="section">
                <h3>Deal Information</h3>
                <table>
                    <tr><td class="label">Deal ID:</td><td>' . htmlspecialchars($deal->id) . '</td></tr>
                    <tr><td class="label">Customer:</td><td>' . htmlspecialchars($deal->customer_name ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Vehicle:</td><td>' . htmlspecialchars($deal->vehicle_name ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Sales Price:</td><td>$' . number_format($deal->sales_price ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Trade Value:</td><td>$' . number_format($deal->trade_value ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Cash Down:</td><td>$' . number_format($deal->cash_down ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Amount Financed:</td><td>$' . number_format($deal->amount_financed ?? 0, 2) . '</td></tr>
                </table>
            </div>
            
            <div class="section">
                <h3>Financing Details</h3>
                <table>
                    <tr><td class="label">Term (Months):</td><td>' . ($deal->term_months ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Interest Rate:</td><td>' . number_format($deal->interest_rate ?? 0, 3) . '%</td></tr>
                    <tr><td class="label">Monthly Payment:</td><td>$' . number_format($deal->monthly_payment ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Finance Reserve:</td><td>$' . number_format($deal->finance_reserve ?? 0, 2) . '</td></tr>
                </table>
            </div>
            
            <div class="section">
                <h3>F&I Products</h3>
                <table>
                    <tr><td class="label">Extended Warranty:</td><td>$' . number_format($deal->warranty_total ?? 0, 2) . '</td></tr>
                    <tr><td class="label">GAP Insurance:</td><td>$' . number_format($deal->gap_amount ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Maintenance Plan:</td><td>$' . number_format($deal->maintenance_amount ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Etch Protection:</td><td>$' . number_format($deal->etch_amount ?? 0, 2) . '</td></tr>
                </table>
            </div>
            
            <div class="section">
                <h3>Deal Summary</h3>
                <table>
                    <tr><td class="label">Frontend Gross:</td><td>$' . number_format($deal->frontend_gross ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Backend Gross:</td><td>$' . number_format($deal->backend_gross ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Total Gross:</td><td>$' . number_format($deal->total_gross ?? 0, 2) . '</td></tr>
                    <tr><td class="label">Deal Status:</td><td>' . htmlspecialchars($deal->deal_status ?? 'Draft') . '</td></tr>
                </table>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Output a simple PDF (basic implementation)
     */
    private function outputSimplePDF($html, $deal)
    {
        // For a basic implementation, we'll use wkhtmltopdf if available
        // or fall back to HTML output with PDF headers
        
        $tempFile = tempnam(sys_get_temp_dir(), 'deal_worksheet_');
        file_put_contents($tempFile . '.html', $html);
        
        // Try to use wkhtmltopdf if available
        $wkhtmltopdf = 'wkhtmltopdf';
        $pdfFile = $tempFile . '.pdf';
        
        $command = $wkhtmltopdf . ' ' . escapeshellarg($tempFile . '.html') . ' ' . escapeshellarg($pdfFile) . ' 2>&1';
        $output = shell_exec($command);
        
        if (file_exists($pdfFile) && filesize($pdfFile) > 0) {
            // Success - output the PDF
            readfile($pdfFile);
            unlink($pdfFile);
        } else {
            // Fallback - output HTML with PDF content type
            header('Content-Type: text/html');
            header('Content-Disposition: attachment; filename="deal_worksheet_' . $deal->id . '.html"');
            echo $html;
        }
        
        // Cleanup
        if (file_exists($tempFile . '.html')) {
            unlink($tempFile . '.html');
        }
        
        sugar_cleanup(true);
    }

    /**
     * Handle analytics action  
     */
    public function action_analytics()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Analytics action called");
        
        // Set up the view
        $this->view = 'analytics';
        $this->view_object_map['analytics'] = 'modules/DM_FIDeals/views/view.analytics.php';
    }

    /**
     * Handle product selection action
     */
    public function action_product_selection()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Product selection action called");
        
        // Set up the view
        $this->view = 'product_selection';
        $this->view_object_map['product_selection'] = 'modules/DM_FIDeals/views/view.product_selection.php';
    }

    /**
     * Handle approval tracking action
     */
    public function action_approval_tracking()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Approval tracking action called");
        
        // Set up the view
        $this->view = 'approval_tracking';
        $this->view_object_map['approval_tracking'] = 'modules/DM_FIDeals/views/view.approval_tracking.php';
    }

    /**
     * Handle lender submission action
     */
    public function action_submission()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Lender submission action called");
        
        // Set up the view
        $this->view = 'submission';
        $this->view_object_map['submission'] = 'modules/DM_FIDeals/views/view.submission.php';
    }

    /**
     * Handle calculator action
     */
    public function action_calculator()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Calculator action called");
        
        // Set up the view for standalone calculator
        $this->view = 'calculator';
        $this->view_object_map['calculator'] = 'modules/DM_FIDeals/views/view.calculator.php';
    }

    /**
     * Handle payment scenarios AJAX action
     */
    public function action_payment_scenarios()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Payment scenarios AJAX action called");
        
        // Get parameters from request
        $sales_price = $_REQUEST['sales_price'] ?? 0;
        $down_payment = $_REQUEST['down_payment'] ?? 0;
        $trade_allowance = $_REQUEST['trade_allowance'] ?? 0;
        $interest_rate = $_REQUEST['interest_rate'] ?? 5.5;
        
        // Calculate amount financed
        $amount_financed = $sales_price - $down_payment - $trade_allowance;
        
        // Generate scenarios for different terms
        $terms = [36, 48, 60, 72, 84];
        $scenarios = array();
        
        foreach ($terms as $term) {
            $monthly_payment = $this->calculatePayment($amount_financed, $interest_rate, $term);
            $total_payments = $monthly_payment * $term;
            $finance_charge = $total_payments - $amount_financed;
            
            $scenarios[] = array(
                'term' => $term,
                'monthly_payment' => number_format($monthly_payment, 2),
                'total_payments' => number_format($total_payments, 2),
                'finance_charge' => number_format($finance_charge, 2)
            );
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(array('scenarios' => $scenarios));
        exit();
    }

    /**
     * Calculate loan payment using standard formula
     */
    private function calculatePayment($principal, $annual_rate, $term_months)
    {
        if ($principal <= 0 || $annual_rate <= 0 || $term_months <= 0) {
            return 0;
        }
        
        $monthly_rate = ($annual_rate / 100) / 12;
        $payment = $principal * ($monthly_rate * pow(1 + $monthly_rate, $term_months)) / 
                   (pow(1 + $monthly_rate, $term_months) - 1);
        
        return $payment;
    }

    /**
     * Handle F&I product data AJAX action
     */
    public function action_product_data()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Product data AJAX action called");
        
        // Load F&I product recommendations based on deal characteristics
        require_once('modules/DM_FIDeals/FIProduct.php');
        
        $vehicle_price = $_REQUEST['vehicle_price'] ?? 0;
        $finance_method = $_REQUEST['finance_method'] ?? 'Finance';
        
        $products = FIProduct::getRecommendedProducts($vehicle_price, $finance_method);
        
        header('Content-Type: application/json');
        echo json_encode(array('products' => $products));
        exit();
    }
}
?> 