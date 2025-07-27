<?php
/**
 * SuiteCRM Deal Documentation Suite - Controller
 * 
 * This file handles custom actions for the Deal Documents module.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

class DM_DealDocumentsController extends SugarController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Handle PDF generation action
     */
    public function action_generate_pdf()
    {
        $this->view = 'generate_pdf';
    }
    
    /**
     * Handle PDF preview action
     */
    public function action_preview_pdf()
    {
        $this->view = 'preview_pdf';
    }
    
    /**
     * Handle PDF download action
     */
    public function action_download_pdf()
    {
        $this->view = 'download_pdf';
    }
    
    /**
     * Handle signature saving action
     */
    public function action_save_signature()
    {
        $this->view = 'save_signature';
    }
    
    /**
     * Handle signature capture popup
     */
    public function action_signature_capture()
    {
        $this->view = 'signature_capture';
    }
    
    /**
     * Get customer data from deal (AJAX endpoint)
     */
    public function action_get_customer()
    {
        $recordId = $_REQUEST['record'] ?? '';
        
        header('Content-Type: application/json');
        
        if (empty($recordId)) {
            echo json_encode(array('error' => 'No record ID provided'));
            return;
        }
        
        try {
            $deal = BeanFactory::getBean('DM_FIDeals', $recordId);
            
            if (!$deal) {
                echo json_encode(array('error' => 'Deal not found'));
                return;
            }
            
            $customerData = array(
                'customer_id' => $deal->customer_id,
                'customer_name' => ''
            );
            
            // Get customer name
            if (!empty($deal->customer_id)) {
                $customer = BeanFactory::getBean('Accounts', $deal->customer_id);
                if ($customer) {
                    $customerData['customer_name'] = $customer->name;
                }
            }
            
            echo json_encode($customerData);
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_DealDocuments Get Customer Error: " . $e->getMessage());
            echo json_encode(array('error' => $e->getMessage()));
        }
        
        // Prevent further processing
        sugar_cleanup();
        exit();
    }
    
    /**
     * Complete document workflow action
     */
    public function action_complete_document()
    {
        $recordId = $_REQUEST['record'] ?? '';
        
        if (empty($recordId)) {
            SugarApplication::redirect('index.php?module=DM_DealDocuments&action=index');
            return;
        }
        
        try {
            $document = BeanFactory::getBean('DM_DealDocuments', $recordId);
            
            if (!$document) {
                throw new Exception('Document not found');
            }
            
            // Validate document can be completed
            if (empty($document->pdf_content)) {
                throw new Exception('PDF must be generated before completing document');
            }
            
            if (empty($document->signature_data)) {
                throw new Exception('Document must be signed before completing');
            }
            
            // Update status to complete
            $document->document_status = 'Complete';
            $document->save();
            
            $_SESSION['deal_documents_message'] = 'Document completed successfully';
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_DealDocuments Complete Document Error: " . $e->getMessage());
            $_SESSION['deal_documents_error'] = $e->getMessage();
        }
        
        // Redirect back to detail view
        SugarApplication::redirect('index.php?module=DM_DealDocuments&action=DetailView&record=' . $recordId);
    }
    
    /**
     * Bulk document generation action
     */
    public function action_bulk_generate()
    {
        $selectedRecords = $_REQUEST['uid'] ?? array();
        
        if (empty($selectedRecords)) {
            $_SESSION['deal_documents_error'] = 'No records selected';
            SugarApplication::redirect('index.php?module=DM_DealDocuments&action=index');
            return;
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($selectedRecords as $recordId) {
            try {
                $document = BeanFactory::getBean('DM_DealDocuments', $recordId);
                
                if ($document && empty($document->pdf_content)) {
                    $document->generatePDF();
                    $document->save();
                    $successCount++;
                }
                
            } catch (Exception $e) {
                $GLOBALS['log']->error("DM_DealDocuments Bulk Generate Error for record $recordId: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $message = "Bulk generation completed: $successCount successful";
        if ($errorCount > 0) {
            $message .= ", $errorCount errors";
        }
        
        $_SESSION['deal_documents_message'] = $message;
        SugarApplication::redirect('index.php?module=DM_DealDocuments&action=index');
    }
    
    /**
     * Pre-process actions before view
     */
    public function pre_editview()
    {
        parent::pre_editview();
        
        // Auto-populate fields from URL parameters
        if (!empty($_REQUEST['deal_id'])) {
            $this->bean->deal_id = $_REQUEST['deal_id'];
            
            // Auto-populate customer from deal
            $deal = BeanFactory::getBean('DM_FIDeals', $_REQUEST['deal_id']);
            if ($deal && !empty($deal->customer_id)) {
                $this->bean->customer_id = $deal->customer_id;
            }
        }
        
        if (!empty($_REQUEST['customer_id'])) {
            $this->bean->customer_id = $_REQUEST['customer_id'];
        }
        
        if (!empty($_REQUEST['document_type'])) {
            $this->bean->document_type = $_REQUEST['document_type'];
            $this->bean->template_name = $_REQUEST['document_type'];
        }
    }
    
    /**
     * Pre-process actions before detail view
     */
    public function pre_detailview()
    {
        parent::pre_detailview();
        
        // Add any session messages to display
        if (!empty($_SESSION['deal_documents_message'])) {
            $GLOBALS['app_strings']['LBL_SUCCESS_MESSAGE'] = $_SESSION['deal_documents_message'];
            unset($_SESSION['deal_documents_message']);
        }
        
        if (!empty($_SESSION['deal_documents_error'])) {
            $GLOBALS['app_strings']['LBL_ERROR_MESSAGE'] = $_SESSION['deal_documents_error'];
            unset($_SESSION['deal_documents_error']);
        }
    }
}