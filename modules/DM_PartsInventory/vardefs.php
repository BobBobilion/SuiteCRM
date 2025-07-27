<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_PartsInventory module in the service & parts management system.
 * 
 * Includes all parts inventory fields, relationships with suppliers,
 * and comprehensive metadata for the Parts Inventory functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_PartsInventory'] = array(
    'table' => 'dm_partsinventory',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Parts Inventory for managing automotive parts stock, pricing, suppliers, and inventory levels',
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
            'comment' => 'Parts inventory display name (Part Number - Description)',
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
            'comment' => 'User ID assigned to parts inventory (parts manager)',
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
        
        // === PARTS INVENTORY SPECIFIC FIELDS ===
        
        // Part Identification
        'part_number' => array(
            'name' => 'part_number',
            'vname' => 'LBL_PART_NUMBER',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Manufacturer part number',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'manufacturer' => array(
            'name' => 'manufacturer',
            'vname' => 'LBL_MANUFACTURER',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Part manufacturer',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'category' => array(
            'name' => 'category',
            'vname' => 'LBL_CATEGORY',
            'type' => 'enum',
            'options' => 'parts_category_list',
            'len' => '100',
            'comment' => 'Part category (Engine, Brake, etc.)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'default' => 'General',
        ),
        
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Detailed part description',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'location' => array(
            'name' => 'location',
            'vname' => 'LBL_LOCATION',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Bin/shelf location in inventory',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Inventory Tracking
        'quantity_on_hand' => array(
            'name' => 'quantity_on_hand',
            'vname' => 'LBL_QUANTITY_ON_HAND',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Current stock quantity',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
            'default' => '0',
        ),
        
        'reorder_point' => array(
            'name' => 'reorder_point',
            'vname' => 'LBL_REORDER_POINT',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Stock level when reorder is needed',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
            'default' => '5',
        ),
        
        'last_ordered_date' => array(
            'name' => 'last_ordered_date',
            'vname' => 'LBL_LAST_ORDERED_DATE',
            'type' => 'date',
            'comment' => 'Date when part was last ordered',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Pricing
        'cost' => array(
            'name' => 'cost',
            'vname' => 'LBL_COST',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Current cost of part',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'retail_price' => array(
            'name' => 'retail_price',
            'vname' => 'LBL_RETAIL_PRICE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Customer retail price',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Additional Information
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'text',
            'comment' => 'Additional notes and information',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // === RELATIONSHIPS ===
        
        // Supplier relationship
        'supplier_id' => array(
            'name' => 'supplier_id',
            'vname' => 'LBL_SUPPLIER_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Primary supplier account ID',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'supplier_name' => array(
            'name' => 'supplier_name',
            'rname' => 'name',
            'id_name' => 'supplier_id',
            'vname' => 'LBL_SUPPLIER_NAME',
            'type' => 'relate',
            'table' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'link' => 'supplier_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Primary supplier name',
        ),
    ),
    
    // === RELATIONSHIPS DEFINITION ===
    'relationships' => array(
        // Supplier relationship
        'dm_partsinventory_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_PartsInventory',
            'rhs_table' => 'dm_partsinventory',
            'rhs_key' => 'supplier_id',
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
        
        'supplier_link' => array(
            'name' => 'supplier_link',
            'type' => 'one',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'source' => 'non-db',
            'vname' => 'LBL_SUPPLIER',
        ),
    ),
    
    // === INDICES ===
    'indices' => array(
        array(
            'name' => 'idx_dm_partsinventory_part_number',
            'type' => 'index',
            'fields' => array('part_number')
        ),
        array(
            'name' => 'idx_dm_partsinventory_manufacturer',
            'type' => 'index',
            'fields' => array('manufacturer')
        ),
        array(
            'name' => 'idx_dm_partsinventory_category',
            'type' => 'index',
            'fields' => array('category')
        ),
        array(
            'name' => 'idx_dm_partsinventory_supplier',
            'type' => 'index',
            'fields' => array('supplier_id')
        ),
        array(
            'name' => 'idx_dm_partsinventory_quantity',
            'type' => 'index',
            'fields' => array('quantity_on_hand')
        ),
        array(
            'name' => 'idx_dm_partsinventory_assigned_user',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_dm_partsinventory_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        ),
    ),
);

// Add module to the global bean lists
VardefManager::createVardef('DM_PartsInventory', 'DM_PartsInventory', array('basic', 'assignable', 'security_groups'));
?>