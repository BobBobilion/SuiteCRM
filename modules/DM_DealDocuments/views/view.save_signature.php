<?php
/**
 * SuiteCRM Deal Documentation Suite - Save Signature View
 * 
 * Handles signature saving requests and returns JSON response
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_DealDocumentsViewSave_signature extends SugarView
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
        $signatureData = $_REQUEST['signature_data'] ?? '';
        
        header('Content-Type: application/json');
        
        try {
            if (empty($recordId)) {
                throw new Exception('Invalid record ID');
            }
            
            if (empty($signatureData)) {
                throw new Exception('No signature data provided');
            }
            
            // Load the document record
            $document = BeanFactory::getBean('DM_DealDocuments', $recordId);
            if (!$document) {
                throw new Exception('Document record not found');
            }
            
            // Validate that PDF exists
            if (empty($document->pdf_content)) {
                throw new Exception('PDF must be generated before signing');
            }
            
            // Prepare signature information
            $signatureInfo = array(
                'signer_name' => $GLOBALS['current_user']->full_name,
                'signer_id' => $GLOBALS['current_user']->id,
                'document_type' => $document->document_type,
            );
            
            // Save the signature
            $result = $document->saveSignature($signatureData, $signatureInfo);
            
            if (!$result) {
                throw new Exception('Failed to save signature');
            }
            
            // Return success response
            echo json_encode(array(
                'success' => true,
                'message' => 'Signature saved successfully'
            ));
            
        } catch (Exception $e) {
            $GLOBALS['log']->error("DM_DealDocuments Signature Save Error: " . $e->getMessage());
            
            echo json_encode(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }
    }
}