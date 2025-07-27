<?php
/**
 * SuiteCRM Deal Documentation Suite - Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_DealDocuments module in the car dealership CRM system.
 * 
 * Includes all document fields, relationships with other modules,
 * and comprehensive metadata for the Deal Documentation functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_DealDocuments'] = array(
    'table' => 'dm_dealdocuments',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Deal Documentation Suite for managing document generation, PDF creation, and signature capture with F&I Deal integration',
    'fields' => array(
        
        // Standard SugarBean fields - inherited from Basic template
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'char',
            'len' => '36',
            'required' => true,
            'reportable' => false,
            'comment' => 'Unique identifier'
        ),
        
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Document display name',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'massupdate' => true,
            'audited' => true,
        ),
        
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'group' => 'created_by_name',
            'comment' => 'Date record created',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'group' => 'modified_by_name',
            'comment' => 'Date record last modified',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'modified_user_id' => array(
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'group' => 'modified_by_name',
            'dbType' => 'char',
            'len' => '36',
            'reportable' => true,
            'comment' => 'User who last modified record',
        ),
        
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED_NAME',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'user_name',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'link' => 'modified_user_link',
            'duplicate_merge' => 'disabled',
        ),
        
        'created_by' => array(
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'comment' => 'User who created record',
        ),
        
        'created_by_name' => array(
            'name' => 'created_by_name',
            'vname' => 'LBL_CREATED',
            'type' => 'relate',
            'reportable' => false,
            'link' => 'created_by_link',
            'rname' => 'user_name',
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'created_by',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'importable' => 'false',
        ),
        
        'assigned_user_id' => array(
            'name' => 'assigned_user_id',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO_ID',
            'group' => 'assigned_user_name',
            'type' => 'relate',
            'table' => 'users',
            'module' => 'Users',
            'reportable' => true,
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'audited' => true,
            'comment' => 'User ID assigned to document (F&I manager or staff)',
            'duplicate_merge' => 'disabled'
        ),
        
        'assigned_user_name' => array(
            'name' => 'assigned_user_name',
            'link' => 'assigned_user_link',
            'vname' => 'LBL_ASSIGNED_TO_NAME',
            'rname' => 'user_name',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'assigned_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled'
        ),
        
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator'
        ),
        
        // === DEAL DOCUMENTS SPECIFIC FIELDS ===
        
        // Relationship fields
        'deal_id' => array(
            'name' => 'deal_id',
            'vname' => 'LBL_DEAL_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Link to F&I deal',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'deal_name' => array(
            'name' => 'deal_name',
            'rname' => 'name',
            'id_name' => 'deal_id',
            'vname' => 'LBL_DEAL_NAME',
            'type' => 'relate',
            'table' => 'dm_fideals',
            'isnull' => 'true',
            'module' => 'DM_FIDeals',
            'dbType' => 'varchar',
            'link' => 'deal_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'F&I deal name',
        ),
        
        'customer_id' => array(
            'name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Customer account ID',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'customer_name' => array(
            'name' => 'customer_name',
            'rname' => 'name',
            'id_name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_NAME',
            'type' => 'relate',
            'table' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'link' => 'customer_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Customer account name',
        ),
        
        // Document fields
        'document_type' => array(
            'name' => 'document_type',
            'vname' => 'LBL_DOCUMENT_TYPE',
            'type' => 'enum',
            'options' => 'document_type_list',
            'len' => '100',
            'comment' => 'Type of document (Purchase Agreement, Warranty, GAP, etc.)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'document_status' => array(
            'name' => 'document_status',
            'vname' => 'LBL_DOCUMENT_STATUS',
            'type' => 'enum',
            'options' => 'document_status_list',
            'len' => '50',
            'comment' => 'Document workflow status',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'default' => 'Draft',
        ),
        
        'template_name' => array(
            'name' => 'template_name',
            'vname' => 'LBL_TEMPLATE_NAME',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Template used for document generation',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'pdf_content' => array(
            'name' => 'pdf_content',
            'vname' => 'LBL_PDF_CONTENT',
            'type' => 'longtext',
            'comment' => 'Generated PDF content as base64',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'false',
            'studio' => 'hidden',
        ),
        
        'signature_data' => array(
            'name' => 'signature_data',
            'vname' => 'LBL_SIGNATURE_DATA',
            'type' => 'text',
            'comment' => 'JSON signature information if signed',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'false',
            'studio' => 'hidden',
        ),
        
        'generation_date' => array(
            'name' => 'generation_date',
            'vname' => 'LBL_GENERATION_DATE',
            'type' => 'datetime',
            'comment' => 'When PDF was created',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'text',
            'comment' => 'Additional notes about the document',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
    ),
    
    // === RELATIONSHIPS DEFINITION ===
    'relationships' => array(
        // F&I Deal relationship
        'dm_dealdocuments_dm_fideals' => array(
            'lhs_module' => 'DM_FIDeals',
            'lhs_table' => 'dm_fideals',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_DealDocuments',
            'rhs_table' => 'dm_dealdocuments',
            'rhs_key' => 'deal_id',
            'relationship_type' => 'one-to-many',
        ),
        
        // Customer relationship
        'dm_dealdocuments_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_DealDocuments',
            'rhs_table' => 'dm_dealdocuments',
            'rhs_key' => 'customer_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    
    // === LINKS ===
    'links' => array(
        'assigned_user_link' => array(
            'name' => 'assigned_user_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_ASSIGNED_TO_USER',
        ),
        
        'modified_user_link' => array(
            'name' => 'modified_user_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_MODIFIED_BY_USER',
        ),
        
        'created_by_link' => array(
            'name' => 'created_by_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_CREATED_BY_USER',
        ),
        
        'deal_link' => array(
            'name' => 'deal_link',
            'type' => 'one',
            'module' => 'DM_FIDeals',
            'bean_name' => 'DM_FIDeals',
            'source' => 'non-db',
            'vname' => 'LBL_DEAL',
        ),
        
        'customer_link' => array(
            'name' => 'customer_link',
            'type' => 'one',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'source' => 'non-db',
            'vname' => 'LBL_CUSTOMER',
        ),
    ),
    
    // === INDICES ===
    'indices' => array(
        array(
            'name' => 'idx_dm_dealdocuments_deal',
            'type' => 'index',
            'fields' => array('deal_id')
        ),
        array(
            'name' => 'idx_dm_dealdocuments_customer',
            'type' => 'index',
            'fields' => array('customer_id')
        ),
        array(
            'name' => 'idx_dm_dealdocuments_type',
            'type' => 'index',
            'fields' => array('document_type')
        ),
        array(
            'name' => 'idx_dm_dealdocuments_status',
            'type' => 'index',
            'fields' => array('document_status')
        ),
        array(
            'name' => 'idx_dm_dealdocuments_date_entered',
            'type' => 'index',
            'fields' => array('date_entered')
        ),
        array(
            'name' => 'idx_dm_dealdocuments_assigned_user',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_dm_dealdocuments_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        ),
    ),
);

// Add module to the global bean lists
VardefManager::createVardef('DM_DealDocuments', 'DM_DealDocuments', array('basic', 'assignable', 'security_groups'));
?>