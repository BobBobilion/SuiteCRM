<?php
/**
 * SuiteCRM Deal Documentation Suite - PDF Preview View
 * 
 * Displays PDF content for preview in iframe
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_DealDocumentsViewPreview_pdf extends SugarView
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
        
        // Set appropriate headers for PDF display
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $document->name . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output the PDF content
        echo base64_decode($document->pdf_content);
    }
}