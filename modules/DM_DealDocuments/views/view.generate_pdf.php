<?php
/**
 * SuiteCRM Deal Documentation Suite - PDF Generation View
 * 
 * Handles PDF generation requests and returns JSON response
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_DealDocumentsViewGenerate_pdf extends SugarView
{
    public function __construct()
    {
        parent::__construct();
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
    }
    
    public function display()
    {
        $recordId = $_REQUEST['record'] ?? '';
        $dealId = $_REQUEST['deal_id'] ?? '';
        $customerId = $_REQUEST['customer_id'] ?? '';
        $documentType = $_REQUEST['document_type'] ?? '';
        $regenerate = $_REQUEST['regenerate'] ?? false;
        
        header('Content-Type: application/json');
        
        try {
            // Load or create the document record
            if ($recordId) {
                $document = BeanFactory::getBean('DM_DealDocuments', $recordId);
                if (!$document) {
                    throw new Exception('Document record not found');
                }
            } else {
                // Create new document record
                $document = BeanFactory::newBean('DM_DealDocuments');
                $document->deal_id = $dealId;
                $document->customer_id = $customerId;
                $document->document_type = $documentType;
                $document->assigned_user_id = $GLOBALS['current_user']->id;
                $document->save();
                $recordId = $document->id;
            }
            
            // Generate PDF if not exists or if regenerating
            if (empty($document->pdf_content) || $regenerate) {
                $pdfContent = $document->generatePDF();
                $document->save();
                
                if (empty($pdfContent)) {
                    throw new Exception('Failed to generate PDF content');
                }
            }
            
            // Return success response
            echo json_encode(array(
                'success' => true,
                'message' => 'PDF generated successfully',
                'record_id' => $recordId,
                'pdf_url' => "index.php?module=DM_DealDocuments&action=preview_pdf&record=" . $recordId
            ));
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_DealDocuments PDF Generation Error: " . $e->getMessage());
            
            echo json_encode(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }
    }
}