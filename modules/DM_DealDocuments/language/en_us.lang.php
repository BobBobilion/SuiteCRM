<?php
/**
 * SuiteCRM Deal Documentation Suite - English Language File
 * 
 * This file contains all labels, strings, and dropdown options for the 
 * DM_DealDocuments module in English.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // Module Information
    'LBL_MODULE_NAME' => 'Deal Documents',
    'LBL_MODULE_TITLE' => 'Deal Documents: Home',
    'LBL_MODULE_NAME_SINGULAR' => 'Deal Document',
    'LBL_SEARCH_FORM_TITLE' => 'Deal Document Search',
    'LBL_LIST_FORM_TITLE' => 'Deal Documents List',
    'LBL_NEW_FORM_TITLE' => 'Create Deal Document',
    'LBL_HOMEPAGE_TITLE' => 'My Deal Documents',
    'LNK_NEW_RECORD' => 'Create Deal Document',
    'LNK_LIST' => 'View Deal Documents',
    'LNK_IMPORT_DM_DEALDOCUMENTS' => 'Import Deal Documents',
    
    // Standard Fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Document Name',
    'LBL_DATE_ENTERED' => 'Date Created',
    'LBL_DATE_MODIFIED' => 'Date Modified',
    'LBL_MODIFIED' => 'Modified By',
    'LBL_MODIFIED_ID' => 'Modified By Id',
    'LBL_MODIFIED_NAME' => 'Modified By Name',
    'LBL_CREATED' => 'Created By',
    'LBL_CREATED_ID' => 'Created By Id',
    'LBL_CREATED_BY_NAME' => 'Created By Name',
    'LBL_DESCRIPTION' => 'Description',
    'LBL_DELETED' => 'Deleted',
    'LBL_ASSIGNED_TO_ID' => 'Assigned User Id',
    'LBL_ASSIGNED_TO_NAME' => 'Assigned To',
    'LBL_ASSIGNED_USER' => 'Assigned User',
    'LBL_ASSIGNED_TO_USER' => 'Assigned to User',
    'LBL_CREATED_BY_USER' => 'Created by User',
    'LBL_MODIFIED_BY_USER' => 'Modified by User',
    
    // Deal Documents Specific Fields
    'LBL_DEAL_ID' => 'Deal ID',
    'LBL_DEAL_NAME' => 'F&I Deal',
    'LBL_DEAL' => 'Deal',
    'LBL_CUSTOMER_ID' => 'Customer ID',
    'LBL_CUSTOMER_NAME' => 'Customer',
    'LBL_CUSTOMER' => 'Customer',
    'LBL_DOCUMENT_TYPE' => 'Document Type',
    'LBL_DOCUMENT_STATUS' => 'Status',
    'LBL_TEMPLATE_NAME' => 'Template',
    'LBL_PDF_CONTENT' => 'PDF Content',
    'LBL_SIGNATURE_DATA' => 'Signature Data',
    'LBL_GENERATION_DATE' => 'Generated Date',
    'LBL_NOTES' => 'Notes',
    
    // Actions and Buttons
    'LBL_GENERATE_PDF' => 'Generate PDF',
    'LBL_DOWNLOAD_PDF' => 'Download PDF',
    'LBL_PREVIEW_PDF' => 'Preview PDF',
    'LBL_SIGN_DOCUMENT' => 'Sign Document',
    'LBL_VIEW_SIGNATURE' => 'View Signature',
    'LBL_REGENERATE_PDF' => 'Regenerate PDF',
    'LBL_COMPLETE_DOCUMENT' => 'Complete Document',
    
    // Status Messages
    'LBL_PDF_GENERATED_SUCCESS' => 'PDF document generated successfully',
    'LBL_PDF_GENERATION_ERROR' => 'Error generating PDF document',
    'LBL_SIGNATURE_SAVED_SUCCESS' => 'Signature saved successfully',
    'LBL_SIGNATURE_SAVE_ERROR' => 'Error saving signature',
    'LBL_DOCUMENT_COMPLETED' => 'Document completed successfully',
    
    // Validation Messages
    'LBL_ERROR_NO_DEAL' => 'Please select an F&I Deal before generating document',
    'LBL_ERROR_NO_CUSTOMER' => 'Please select a Customer before generating document',
    'LBL_ERROR_NO_DOCUMENT_TYPE' => 'Please select a Document Type',
    'LBL_ERROR_PDF_NOT_GENERATED' => 'PDF must be generated before signing',
    
    // Help Text
    'LBL_HELP_DOCUMENT_TYPE' => 'Select the type of document to generate from available templates',
    'LBL_HELP_TEMPLATE' => 'Template used for document generation - automatically selected based on document type',
    'LBL_HELP_STATUS' => 'Current status of the document in the workflow process',
    'LBL_HELP_SIGNATURE' => 'Digital signature capture and storage for document completion',
    
    // Panel Titles
    'LBL_PANEL_DEFAULT' => 'Document Information',
    'LBL_PANEL_DOCUMENT_DETAILS' => 'Document Details',
    'LBL_PANEL_GENERATION' => 'PDF Generation',
    'LBL_PANEL_SIGNATURE' => 'Digital Signature',
    'LBL_PANEL_WORKFLOW' => 'Workflow Status',
    
    // Subpanel Titles
    'LBL_SUBPANEL_DOCUMENTS' => 'Deal Documents',
    
    // Search
    'LBL_ANY_EMAIL' => 'Any Email',
    'LBL_SEARCH' => 'Search',
    'LBL_CLEAR' => 'Clear',
    
    // ListView
    'LBL_LIST_DOCUMENT_TYPE' => 'Type',
    'LBL_LIST_DOCUMENT_STATUS' => 'Status',
    'LBL_LIST_DEAL_NAME' => 'Deal',
    'LBL_LIST_CUSTOMER_NAME' => 'Customer',
    'LBL_LIST_GENERATION_DATE' => 'Generated',
    'LBL_LIST_ASSIGNED_USER' => 'Assigned To',
    
    // DetailView specific
    'LBL_DOCUMENT_PREVIEW' => 'Document Preview',
    'LBL_SIGNATURE_PREVIEW' => 'Signature Preview',
    'LBL_DOWNLOAD_ORIGINAL' => 'Download Original PDF',
    'LBL_DOCUMENT_HISTORY' => 'Document History',
);

// Document Type Dropdown Options
$app_list_strings['document_type_list'] = array(
    '' => '',
    'Purchase Agreement' => 'Purchase Agreement',
    'Finance Contract' => 'Finance Contract',
    'Warranty Contract' => 'Warranty Contract',
    'GAP Insurance' => 'GAP Insurance',
    'Extended Service Contract' => 'Extended Service Contract',
    'Credit Life Insurance' => 'Credit Life Insurance',
    'Theft Protection' => 'Theft Protection',
    'Maintenance Agreement' => 'Maintenance Agreement',
    'Trade-In Agreement' => 'Trade-In Agreement',
    'Disclosure Statement' => 'Disclosure Statement',
    'Lender Documents' => 'Lender Documents',
    'Other' => 'Other',
);

// Document Status Dropdown Options
$app_list_strings['document_status_list'] = array(
    '' => '',
    'Draft' => 'Draft',
    'Generated' => 'Generated',
    'Signed' => 'Signed',
    'Complete' => 'Complete',
    'Archived' => 'Archived',
    'Cancelled' => 'Cancelled',
);

// Template Names (for future expansion)
$app_list_strings['document_template_list'] = array(
    '' => '',
    'standard_purchase_agreement' => 'Standard Purchase Agreement',
    'finance_contract_standard' => 'Standard Finance Contract',
    'warranty_basic' => 'Basic Warranty Contract',
    'warranty_premium' => 'Premium Warranty Contract',
    'gap_insurance_standard' => 'Standard GAP Insurance',
    'theft_protection_basic' => 'Basic Theft Protection',
    'maintenance_basic' => 'Basic Maintenance Agreement',
    'custom' => 'Custom Template',
);

?>