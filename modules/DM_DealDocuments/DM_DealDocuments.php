<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Deal Documentation Suite - Main Bean Class
 * 
 * This class defines the DM_DealDocuments SugarBean entity for managing 
 * document generation, PDF creation, and signature capture for F&I deals
 * in automotive dealership operations.
 * 
 * Features:
 * - Deal document generation and management
 * - PDF creation using TCPDF integration
 * - Digital signature capture and storage
 * - Document workflow and status tracking
 * - Integration with F&I Deal Center
 * - Template-based document creation
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_DealDocuments Bean Class
 * 
 * Manages deal documentation with PDF generation, signature capture, and 
 * workflow integration for automotive F&I operations
 */
#[\AllowDynamicProperties]
class DM_DealDocuments extends Basic
{
    public $table_name = "dm_dealdocuments";
    public $object_name = "DM_DealDocuments";
    public $module_dir = "DM_DealDocuments";
    public $module_name = "DM_DealDocuments";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for document compliance
    public $audited = true;
    
    // Core document fields
    public $id;
    public $name;                          // Document display name
    public $deal_id;                       // Link to F&I deal
    public $customer_id;                   // Link to customer account
    public $document_type;                 // Purchase Agreement, Warranty, GAP, etc.
    public $document_status;               // Draft, Generated, Signed, Complete
    public $template_name;                 // Which template was used
    public $pdf_content;                   // Generated PDF content as base64
    public $signature_data;                // JSON signature information
    public $generation_date;               // When PDF was created
    public $notes;                         // Additional notes
    
    // Standard SugarBean fields
    public $assigned_user_id;
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
        
        // Set default values
        $this->document_status = 'Draft';
        
        // Initialize logging for document operations
        $GLOBALS['log']->info("DM_DealDocuments: Initializing Deal Documents Bean");
    }

    /**
     * Generate display name for document record
     */
    public function save($check_notify = false)
    {
        // Log the save operation
        $GLOBALS['log']->info("DM_DealDocuments: Saving document record ID: " . $this->id);
        
        // Auto-generate name if not provided
        if (empty($this->name) && !empty($this->document_type)) {
            $this->name = $this->document_type;
            
            // Add deal number if available
            if (!empty($this->deal_id)) {
                $deal = BeanFactory::getBean('DM_FIDeals', $this->deal_id);
                if ($deal && !empty($deal->deal_number)) {
                    $this->name .= ' - Deal ' . $deal->deal_number;
                }
            }
            
            // Add customer name if available
            if (!empty($this->customer_id)) {
                $customer = BeanFactory::getBean('Accounts', $this->customer_id);
                if ($customer && !empty($customer->name)) {
                    $this->name .= ' - ' . $customer->name;
                }
            }
            
            $GLOBALS['log']->info("DM_DealDocuments: Auto-generated name: " . $this->name);
        }
        
        // Set generation date when PDF is created
        if (!empty($this->pdf_content) && empty($this->generation_date)) {
            $this->generation_date = date('Y-m-d H:i:s');
            $GLOBALS['log']->info("DM_DealDocuments: Set generation date: " . $this->generation_date);
        }
        
        return parent::save($check_notify);
    }

    /**
     * Generate PDF document using TCPDF
     * 
     * @param string $template Template name to use
     * @param array $data Data to populate in template
     * @return string Base64 encoded PDF content
     */
    public function generatePDF($template = null, $data = null)
    {
        $GLOBALS['log']->info("DM_DealDocuments: Generating PDF for document ID: " . $this->id);
        
        // Include TCPDF library
        require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
        
        // Get deal data if not provided
        if ($data === null) {
            $data = $this->getDealData();
        }
        
        // Determine template to use
        $template = $template ?: $this->template_name ?: $this->document_type;
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('SuiteCRM Deal Documentation Suite');
        $pdf->SetAuthor('Dealership F&I Department');
        $pdf->SetTitle($this->name ?: 'Deal Document');
        $pdf->SetSubject('Automotive Deal Documentation');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        
        // Add a page
        $pdf->AddPage();
        
        // Generate content based on template
        $content = $this->generateTemplateContent($template, $data);
        
        // Write content to PDF
        $pdf->writeHTML($content, true, false, true, false, '');
        
        // Get PDF content as string
        $pdfContent = $pdf->Output('', 'S');
        
        // Encode as base64 for storage
        $this->pdf_content = base64_encode($pdfContent);
        $this->template_name = $template;
        $this->document_status = 'Generated';
        $this->generation_date = date('Y-m-d H:i:s');
        
        $GLOBALS['log']->info("DM_DealDocuments: PDF generated successfully - Size: " . strlen($this->pdf_content) . " characters");
        
        return $this->pdf_content;
    }

    /**
     * Generate template content based on document type
     * 
     * @param string $template Template name
     * @param array $data Deal data
     * @return string HTML content for PDF
     */
    private function generateTemplateContent($template, $data)
    {
        $GLOBALS['log']->debug("DM_DealDocuments: Generating template content for: " . $template);
        
        switch ($template) {
            case 'Purchase Agreement':
                return $this->generatePurchaseAgreement($data);
            case 'Warranty Contract':
                return $this->generateWarrantyContract($data);
            case 'GAP Insurance':
                return $this->generateGAPInsurance($data);
            case 'Finance Contract':
                return $this->generateFinanceContract($data);
            default:
                return $this->generateGenericDocument($data);
        }
    }

    /**
     * Generate Purchase Agreement template
     * 
     * @param array $data Deal data
     * @return string HTML content
     */
    private function generatePurchaseAgreement($data)
    {
        $html = '
        <style>
            .header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
            .section { margin-bottom: 15px; }
            .label { font-weight: bold; }
            .table { width: 100%; border-collapse: collapse; }
            .table td, .table th { border: 1px solid #000; padding: 8px; }
            .signature-box { border: 1px solid #000; height: 60px; margin-top: 20px; }
        </style>
        
        <div class="header">VEHICLE PURCHASE AGREEMENT</div>
        
        <div class="section">
            <span class="label">Deal Number:</span> ' . ($data['deal_number'] ?? '') . '<br>
            <span class="label">Date:</span> ' . date('M d, Y') . '<br>
            <span class="label">Customer:</span> ' . ($data['customer_name'] ?? '') . '<br>
        </div>
        
        <div class="section">
            <span class="label">Vehicle Information:</span><br>
            Year: ' . ($data['vehicle_year'] ?? '') . '<br>
            Make: ' . ($data['vehicle_make'] ?? '') . '<br>
            Model: ' . ($data['vehicle_model'] ?? '') . '<br>
            VIN: ' . ($data['vehicle_vin'] ?? '') . '<br>
        </div>
        
        <table class="table">
            <tr><th>Description</th><th>Amount</th></tr>
            <tr><td>Vehicle Sale Price</td><td>$' . number_format($data['sales_price'] ?? 0, 2) . '</td></tr>
            <tr><td>Trade Allowance</td><td>($' . number_format($data['trade_allowance'] ?? 0, 2) . ')</td></tr>
            <tr><td>Down Payment</td><td>($' . number_format($data['down_payment'] ?? 0, 2) . ')</td></tr>
            <tr><td>Dealer Fees</td><td>$' . number_format($data['dealer_fees'] ?? 0, 2) . '</td></tr>
            <tr><td>Government Fees</td><td>$' . number_format($data['government_fees'] ?? 0, 2) . '</td></tr>
            <tr><td><strong>Amount Financed</strong></td><td><strong>$' . number_format($data['amount_financed'] ?? 0, 2) . '</strong></td></tr>
        </table>
        
        <div class="section" style="margin-top: 30px;">
            <span class="label">Customer Signature:</span>
            <div class="signature-box"></div>
            <p>Date: _______________</p>
        </div>
        
        <div class="section">
            <span class="label">Dealer Representative:</span>
            <div class="signature-box"></div>
            <p>Date: _______________</p>
        </div>';
        
        return $html;
    }

    /**
     * Generate Warranty Contract template
     * 
     * @param array $data Deal data
     * @return string HTML content
     */
    private function generateWarrantyContract($data)
    {
        $html = '
        <style>
            .header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
            .section { margin-bottom: 15px; }
            .label { font-weight: bold; }
            .signature-box { border: 1px solid #000; height: 60px; margin-top: 20px; }
        </style>
        
        <div class="header">EXTENDED WARRANTY CONTRACT</div>
        
        <div class="section">
            <span class="label">Contract Number:</span> EW-' . ($data['deal_number'] ?? '') . '<br>
            <span class="label">Date:</span> ' . date('M d, Y') . '<br>
            <span class="label">Customer:</span> ' . ($data['customer_name'] ?? '') . '<br>
        </div>
        
        <div class="section">
            <span class="label">Vehicle Coverage:</span><br>
            ' . ($data['vehicle_year'] ?? '') . ' ' . ($data['vehicle_make'] ?? '') . ' ' . ($data['vehicle_model'] ?? '') . '<br>
            VIN: ' . ($data['vehicle_vin'] ?? '') . '<br>
        </div>
        
        <div class="section">
            <span class="label">Warranty Details:</span><br>
            Coverage Amount: $' . number_format($data['warranty_total'] ?? 0, 2) . '<br>
            Term: 36 months / 36,000 miles (whichever comes first)<br>
            Deductible: $100 per repair visit<br>
        </div>
        
        <div class="section">
            <p><strong>Coverage Includes:</strong> Engine, Transmission, Electrical System, Air Conditioning, Power Steering, Brakes, and other covered components as detailed in the warranty booklet.</p>
        </div>
        
        <div class="section" style="margin-top: 30px;">
            <span class="label">Customer Acceptance:</span>
            <div class="signature-box"></div>
            <p>Date: _______________</p>
        </div>';
        
        return $html;
    }

    /**
     * Generate GAP Insurance template
     * 
     * @param array $data Deal data
     * @return string HTML content
     */
    private function generateGAPInsurance($data)
    {
        $html = '
        <style>
            .header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
            .section { margin-bottom: 15px; }
            .label { font-weight: bold; }
            .signature-box { border: 1px solid #000; height: 60px; margin-top: 20px; }
        </style>
        
        <div class="header">GAP INSURANCE AGREEMENT</div>
        
        <div class="section">
            <span class="label">Policy Number:</span> GAP-' . ($data['deal_number'] ?? '') . '<br>
            <span class="label">Date:</span> ' . date('M d, Y') . '<br>
            <span class="label">Insured:</span> ' . ($data['customer_name'] ?? '') . '<br>
        </div>
        
        <div class="section">
            <span class="label">Vehicle Information:</span><br>
            ' . ($data['vehicle_year'] ?? '') . ' ' . ($data['vehicle_make'] ?? '') . ' ' . ($data['vehicle_model'] ?? '') . '<br>
            VIN: ' . ($data['vehicle_vin'] ?? '') . '<br>
        </div>
        
        <div class="section">
            <span class="label">Coverage Details:</span><br>
            GAP Coverage Amount: $' . number_format($data['gap_amount'] ?? 0, 2) . '<br>
            Loan Amount: $' . number_format($data['amount_financed'] ?? 0, 2) . '<br>
        </div>
        
        <div class="section">
            <p><strong>GAP Insurance</strong> covers the difference between what you owe on your vehicle loan and the actual cash value of your vehicle in the event of total loss due to theft or accident.</p>
        </div>
        
        <div class="section" style="margin-top: 30px;">
            <span class="label">Customer Signature:</span>
            <div class="signature-box"></div>
            <p>Date: _______________</p>
        </div>';
        
        return $html;
    }

    /**
     * Generate Finance Contract template
     * 
     * @param array $data Deal data
     * @return string HTML content
     */
    private function generateFinanceContract($data)
    {
        $html = '
        <style>
            .header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
            .section { margin-bottom: 15px; }
            .label { font-weight: bold; }
            .table { width: 100%; border-collapse: collapse; }
            .table td, .table th { border: 1px solid #000; padding: 8px; }
            .signature-box { border: 1px solid #000; height: 60px; margin-top: 20px; }
        </style>
        
        <div class="header">RETAIL INSTALLMENT SALES CONTRACT</div>
        
        <div class="section">
            <span class="label">Contract Number:</span> ' . ($data['deal_number'] ?? '') . '<br>
            <span class="label">Date:</span> ' . date('M d, Y') . '<br>
            <span class="label">Buyer:</span> ' . ($data['customer_name'] ?? '') . '<br>
        </div>
        
        <table class="table">
            <tr><th>Finance Terms</th><th>Amount</th></tr>
            <tr><td>Amount Financed</td><td>$' . number_format($data['amount_financed'] ?? 0, 2) . '</td></tr>
            <tr><td>Annual Percentage Rate</td><td>' . number_format($data['interest_rate'] ?? 0, 2) . '%</td></tr>
            <tr><td>Term (Months)</td><td>' . ($data['term_months'] ?? 0) . '</td></tr>
            <tr><td>Monthly Payment</td><td>$' . number_format($data['monthly_payment'] ?? 0, 2) . '</td></tr>
            <tr><td>Total of Payments</td><td>$' . number_format($data['total_of_payments'] ?? 0, 2) . '</td></tr>
            <tr><td>Finance Charge</td><td>$' . number_format($data['finance_charge'] ?? 0, 2) . '</td></tr>
        </table>
        
        <div class="section" style="margin-top: 20px;">
            <p><strong>Payment Schedule:</strong> ' . ($data['term_months'] ?? 0) . ' monthly payments of $' . number_format($data['monthly_payment'] ?? 0, 2) . ' beginning one month from contract date.</p>
        </div>
        
        <div class="section" style="margin-top: 30px;">
            <span class="label">Buyer Signature:</span>
            <div class="signature-box"></div>
            <p>Date: _______________</p>
        </div>';
        
        return $html;
    }

    /**
     * Generate generic document template
     * 
     * @param array $data Deal data
     * @return string HTML content
     */
    private function generateGenericDocument($data)
    {
        $html = '
        <style>
            .header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
            .section { margin-bottom: 15px; }
            .label { font-weight: bold; }
            .signature-box { border: 1px solid #000; height: 60px; margin-top: 20px; }
        </style>
        
        <div class="header">DEAL DOCUMENT</div>
        
        <div class="section">
            <span class="label">Document Type:</span> ' . ($this->document_type ?? 'General') . '<br>
            <span class="label">Deal Number:</span> ' . ($data['deal_number'] ?? '') . '<br>
            <span class="label">Date:</span> ' . date('M d, Y') . '<br>
            <span class="label">Customer:</span> ' . ($data['customer_name'] ?? '') . '<br>
        </div>
        
        <div class="section">
            <p>This document is part of the automotive sales transaction and contains important information regarding the purchase agreement.</p>
        </div>
        
        <div class="section" style="margin-top: 30px;">
            <span class="label">Customer Signature:</span>
            <div class="signature-box"></div>
            <p>Date: _______________</p>
        </div>';
        
        return $html;
    }

    /**
     * Get deal data for document generation
     * 
     * @return array Deal and customer data
     */
    private function getDealData()
    {
        $data = array();
        
        // Get F&I deal data
        if (!empty($this->deal_id)) {
            $deal = BeanFactory::getBean('DM_FIDeals', $this->deal_id);
            if ($deal) {
                $data = array(
                    'deal_number' => $deal->deal_number,
                    'sales_price' => $deal->sales_price,
                    'down_payment' => $deal->down_payment,
                    'trade_allowance' => $deal->trade_allowance,
                    'amount_financed' => $deal->amount_financed,
                    'monthly_payment' => $deal->monthly_payment,
                    'term_months' => $deal->term_months,
                    'interest_rate' => $deal->sell_rate,
                    'total_of_payments' => $deal->total_of_payments,
                    'finance_charge' => $deal->finance_charge,
                    'dealer_fees' => $deal->dealer_fees,
                    'government_fees' => $deal->government_fees,
                    'warranty_total' => $deal->warranty_total,
                    'gap_amount' => $deal->gap_amount,
                );
            }
        }
        
        // Get customer data
        if (!empty($this->customer_id)) {
            $customer = BeanFactory::getBean('Accounts', $this->customer_id);
            if ($customer) {
                $data['customer_name'] = $customer->name;
                $data['customer_address'] = $customer->billing_address_street;
                $data['customer_city'] = $customer->billing_address_city;
                $data['customer_state'] = $customer->billing_address_state;
                $data['customer_zip'] = $customer->billing_address_postalcode;
            }
        }
        
        // Get vehicle data (if available)
        if (!empty($deal->vehicle_id ?? null)) {
            $vehicle = BeanFactory::getBean('AutoInventory', $deal->vehicle_id);
            if ($vehicle) {
                $data['vehicle_year'] = $vehicle->year;
                $data['vehicle_make'] = $vehicle->make;
                $data['vehicle_model'] = $vehicle->model;
                $data['vehicle_vin'] = $vehicle->vin;
                $data['vehicle_mileage'] = $vehicle->mileage;
            }
        }
        
        $GLOBALS['log']->debug("DM_DealDocuments: Retrieved deal data for document generation");
        
        return $data;
    }

    /**
     * Save signature data
     * 
     * @param string $signatureData Base64 encoded signature image
     * @param array $signatureInfo Additional signature information
     * @return bool Success status
     */
    public function saveSignature($signatureData, $signatureInfo = array())
    {
        $GLOBALS['log']->info("DM_DealDocuments: Saving signature for document ID: " . $this->id);
        
        $signatureRecord = array(
            'signature_image' => $signatureData,
            'signed_date' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'additional_info' => $signatureInfo
        );
        
        $this->signature_data = json_encode($signatureRecord);
        $this->document_status = 'Signed';
        
        $result = $this->save();
        
        $GLOBALS['log']->info("DM_DealDocuments: Signature saved successfully");
        
        return $result;
    }

    /**
     * Get document status with display information
     * 
     * @return array Status info with color and description
     */
    public function getStatusInfo()
    {
        $statusInfo = array(
            'status' => $this->document_status,
            'color' => 'default',
            'description' => ''
        );
        
        switch ($this->document_status) {
            case 'Draft':
                $statusInfo['color'] = 'secondary';
                $statusInfo['description'] = 'Document in draft status';
                break;
            case 'Generated':
                $statusInfo['color'] = 'info';
                $statusInfo['description'] = 'PDF generated, awaiting signature';
                break;
            case 'Signed':
                $statusInfo['color'] = 'warning';
                $statusInfo['description'] = 'Document signed, pending completion';
                break;
            case 'Complete':
                $statusInfo['color'] = 'success';
                $statusInfo['description'] = 'Document complete and archived';
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