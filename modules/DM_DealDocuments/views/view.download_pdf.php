<?php
/**
 * SuiteCRM Deal Documentation Suite - PDF Download View
 * 
 * Handles PDF download requests
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_DealDocumentsViewDownload_pdf extends SugarView
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
        
        if (empty($recordId)) {
            http_response_code(400);
            echo 'Invalid record ID';
            return;
        }
        
        $document = BeanFactory::getBean('DM_DealDocuments', $recordId);
        
        if (!$document || empty($document->pdf_content)) {
            http_response_code(404);
            echo 'PDF not found';
            return;
        }
        
        // Create safe filename
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document->name) . '.pdf';
        
        // Set appropriate headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen(base64_decode($document->pdf_content)));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output the PDF content
        echo base64_decode($document->pdf_content);
    }
}