<?php
/**
 * SuiteCRM Deal Documentation Suite - Detail View
 * 
 * Custom detail view for Deal Documents with PDF preview and signature display
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.detail.php');

class DM_DealDocumentsViewDetail extends ViewDetail
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Add CSS for document display
        echo '<style>
            .document-preview {
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 15px;
                margin: 10px 0;
                background-color: #f9f9f9;
            }
            .signature-display {
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 10px;
                background-color: white;
                text-align: center;
            }
            .status-badge {
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: bold;
            }
            .status-draft { background-color: #6c757d; color: white; }
            .status-generated { background-color: #17a2b8; color: white; }
            .status-signed { background-color: #ffc107; color: #212529; }
            .status-complete { background-color: #28a745; color: white; }
        </style>';
    }
    
    public function display()
    {
        // Add JavaScript for document actions
        echo '<script>
            function generateDocumentPDF() {
                if (confirm("Generate PDF document for this record?")) {
                    window.location.href = "index.php?module=DM_DealDocuments&action=generate_pdf&record=' . $this->bean->id . '";
                }
            }
            
            function regenerateDocumentPDF() {
                if (confirm("Regenerate PDF document? This will overwrite the existing PDF.")) {
                    window.location.href = "index.php?module=DM_DealDocuments&action=generate_pdf&record=' . $this->bean->id . '&regenerate=1";
                }
            }
            
            function downloadPDF() {
                window.open("index.php?module=DM_DealDocuments&action=download_pdf&record=' . $this->bean->id . '", "_blank");
            }
            
            function showSignatureModal() {
                // Open signature modal/popup
                var signatureWindow = window.open(
                    "index.php?module=DM_DealDocuments&action=signature_capture&record=' . $this->bean->id . '",
                    "signature",
                    "width=600,height=400,scrollbars=no,resizable=no"
                );
                
                // Refresh page when signature window closes
                var checkClosed = setInterval(function() {
                    if (signatureWindow.closed) {
                        clearInterval(checkClosed);
                        window.location.reload();
                    }
                }, 1000);
            }
        </script>';
        
        // Prepare additional field data for display
        $this->prepareDisplayData();
        
        parent::display();
    }
    
    /**
     * Prepare additional data for display
     */
    private function prepareDisplayData()
    {
        // Add status badge color information
        $statusInfo = $this->bean->getStatusInfo();
        $this->ss->assign('status_badge_color', $statusInfo['color']);
        
        // Prepare signature data if available
        if (!empty($this->bean->signature_data)) {
            $signatureData = json_decode($this->bean->signature_data, true);
            if ($signatureData) {
                $this->ss->assign('signature_image', $signatureData['signature_image'] ?? '');
                $this->ss->assign('signature_date', $signatureData['signed_date'] ?? '');
            }
        }
        
        // Check if PDF exists
        $this->ss->assign('has_pdf', !empty($this->bean->pdf_content));
        $this->ss->assign('has_signature', !empty($this->bean->signature_data));
    }
}