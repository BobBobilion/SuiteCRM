<?php
/**
 * SuiteCRM Vehicle Inventory System - Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_VehiclesInventory module in the car dealership CRM system.
 * 
 * Includes all vehicle specifications, pricing data, business intelligence
 * fields, and relationships with other SuiteCRM modules.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_VehiclesInventory'] = array(
    'table' => 'dm_vehiclesinventory',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Vehicle inventory management for automotive dealerships including specifications, pricing, photos, and business workflow integration',
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
            'comment' => 'Vehicle display name (Year Make Model)',
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
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'group' => 'modified_by_name',
            'dbType' => 'id',
            'reportable' => true,
            'comment' => 'User who last modified record',
        ),
        
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'user_name',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'link' => 'modified_user_link',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        
        'created_by' => array(
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'created_by',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'group' => 'created_by_name',
            'comment' => 'User who created record',
            'massupdate' => false,
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
            'massupdate' => false,
        ),
        
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Full text of the note',
            'rows' => 6,
            'cols' => 80,
        ),
        
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator'
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
            'dbType' => 'id',
            'audited' => true,
            'comment' => 'User ID assigned to record',
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
        
        // Core Vehicle Identification Fields
        'vin' => array(
            'name' => 'vin',
            'vname' => 'LBL_VIN',
            'type' => 'varchar',
            'len' => '17',
            'comment' => 'Vehicle Identification Number',
            'required' => false,
            'audited' => true,
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'stock_number' => array(
            'name' => 'stock_number',
            'vname' => 'LBL_STOCK_NUMBER',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Dealer internal stock number',
            'required' => false,
            'audited' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        // Vehicle Specifications
        'year' => array(
            'name' => 'year',
            'vname' => 'LBL_YEAR',
            'type' => 'int',
            'len' => '4',
            'comment' => 'Model year',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'unified_search' => true,
            'enable_range_search' => true,
        ),
        
        'make' => array(
            'name' => 'make',
            'vname' => 'LBL_MAKE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Vehicle manufacturer',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'model' => array(
            'name' => 'model',
            'vname' => 'LBL_MODEL',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Vehicle model name',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'trim' => array(
            'name' => 'trim',
            'vname' => 'LBL_TRIM',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Vehicle trim level',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'body_style' => array(
            'name' => 'body_style',
            'vname' => 'LBL_BODY_STYLE',
            'type' => 'enum',
            'options' => 'vehicle_body_style_list',
            'len' => '50',
            'comment' => 'Vehicle body style',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'exterior_color' => array(
            'name' => 'exterior_color',
            'vname' => 'LBL_EXTERIOR_COLOR',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Exterior paint color',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'interior_color' => array(
            'name' => 'interior_color',
            'vname' => 'LBL_INTERIOR_COLOR',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Interior color and material',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'mileage' => array(
            'name' => 'mileage',
            'vname' => 'LBL_MILEAGE',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Current odometer reading',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'engine_type' => array(
            'name' => 'engine_type',
            'vname' => 'LBL_ENGINE_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Engine description',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'transmission' => array(
            'name' => 'transmission',
            'vname' => 'LBL_TRANSMISSION',
            'type' => 'enum',
            'options' => 'vehicle_transmission_list',
            'len' => '50',
            'comment' => 'Transmission type',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'drivetrain' => array(
            'name' => 'drivetrain',
            'vname' => 'LBL_DRIVETRAIN',
            'type' => 'enum',
            'options' => 'vehicle_drivetrain_list',
            'len' => '50',
            'comment' => 'Drivetrain configuration',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'fuel_type' => array(
            'name' => 'fuel_type',
            'vname' => 'LBL_FUEL_TYPE',
            'type' => 'enum',
            'options' => 'vehicle_fuel_type_list',
            'len' => '50',
            'comment' => 'Fuel type',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        // Inventory Status and Condition
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'vehicle_status_list',
            'len' => '50',
            'comment' => 'Current inventory status',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'default' => 'Available',
        ),
        
        'condition_type' => array(
            'name' => 'condition_type',
            'vname' => 'LBL_CONDITION_TYPE',
            'type' => 'enum',
            'options' => 'vehicle_condition_list',
            'len' => '50',
            'comment' => 'Vehicle condition category',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'default' => 'Used',
        ),
        
        'location' => array(
            'name' => 'location',
            'vname' => 'LBL_LOCATION',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Lot location or space number',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        // Pricing and Financial Data
        'purchase_date' => array(
            'name' => 'purchase_date',
            'vname' => 'LBL_PURCHASE_DATE',
            'type' => 'date',
            'comment' => 'Date vehicle was acquired',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'purchase_price' => array(
            'name' => 'purchase_price',
            'vname' => 'LBL_PURCHASE_PRICE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'precision' => 2,
            'comment' => 'Vehicle acquisition cost',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'list_price' => array(
            'name' => 'list_price',
            'vname' => 'LBL_LIST_PRICE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'precision' => 2,
            'comment' => 'Current asking price',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'sale_price' => array(
            'name' => 'sale_price',
            'vname' => 'LBL_SALE_PRICE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'precision' => 2,
            'comment' => 'Final sale price when sold',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'market_value' => array(
            'name' => 'market_value',
            'vname' => 'LBL_MARKET_VALUE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'precision' => 2,
            'comment' => 'Current market value from KBB/Edmunds',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'market_value_date' => array(
            'name' => 'market_value_date',
            'vname' => 'LBL_MARKET_VALUE_DATE',
            'type' => 'datetime',
            'comment' => 'Last market valuation update date',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        // Business Intelligence Fields
        'days_on_lot' => array(
            'name' => 'days_on_lot',
            'vname' => 'LBL_DAYS_ON_LOT',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Number of days vehicle has been on lot',
            'required' => false,
            'audited' => false,
            'importable' => 'false',
            'enable_range_search' => true,
            'calculated' => true,
        ),
        
        'source' => array(
            'name' => 'source',
            'vname' => 'LBL_SOURCE',
            'type' => 'enum',
            'options' => 'vehicle_source_list',
            'len' => '100',
            'comment' => 'How vehicle was acquired',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'features' => array(
            'name' => 'features',
            'vname' => 'LBL_FEATURES',
            'type' => 'text',
            'comment' => 'JSON array of vehicle features',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        'photos' => array(
            'name' => 'photos',
            'vname' => 'LBL_PHOTOS',
            'type' => 'text',
            'comment' => 'JSON array of photo URLs',
            'required' => false,
            'audited' => true,
            'importable' => 'false',
        ),
        
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'text',
            'comment' => 'Internal notes about vehicle',
            'required' => false,
            'audited' => true,
            'importable' => 'true',
        ),
        
        // Currency fields for multi-currency support
        'currency_id' => array(
            'name' => 'currency_id',
            'type' => 'id',
            'dbType' => 'id',
            'vname' => 'LBL_CURRENCY',
            'len' => '36',
            'audited' => true,
            'comment' => 'Currency used for prices',
            'function' => 'getCurrencyDropDown',
            'function_bean' => 'Currencies',
        ),
        
        'base_rate' => array(
            'name' => 'base_rate',
            'vname' => 'LBL_CURRENCY_RATE',
            'type' => 'text',
            'studio' => 'false',
            'audited' => true,
            'comment' => 'Currency conversion rate'
        ),
        
        // Links to other modules will be defined here
        'assigned_user_link' => array(
            'name' => 'assigned_user_link',
            'type' => 'link',
            'relationship' => 'dm_vehiclesinventory_assigned_user',
            'vname' => 'LBL_ASSIGNED_TO_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'duplicate_merge' => 'enabled',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'table' => 'users',
        ),
        
        'modified_user_link' => array(
            'name' => 'modified_user_link',
            'type' => 'link',
            'relationship' => 'dm_vehiclesinventory_modified_user',
            'vname' => 'LBL_MODIFIED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'table' => 'users',
        ),
        
        'created_by_link' => array(
            'name' => 'created_by_link',
            'type' => 'link',
            'relationship' => 'dm_vehiclesinventory_created_by',
            'vname' => 'LBL_CREATED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'rname' => 'user_name',
            'id_name' => 'created_by',
            'table' => 'users',
        ),
    ),
    
    'relationships' => array(
        'dm_vehiclesinventory_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_VehiclesInventory',
            'rhs_table' => 'dm_vehiclesinventory',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'dm_vehiclesinventory_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_VehiclesInventory',
            'rhs_table' => 'dm_vehiclesinventory',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'dm_vehiclesinventory_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_VehiclesInventory',
            'rhs_table' => 'dm_vehiclesinventory',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ),
    ),
    
    'indices' => array(
        array(
            'name' => 'dm_vehiclesinventorypk',
            'type' => 'primary',
            'fields' => array('id'),
        ),
        array(
            'name' => 'idx_dm_vehiclesinventory_vin',
            'type' => 'index',
            'fields' => array('vin'),
        ),
        array(
            'name' => 'idx_dm_vehiclesinventory_stock',
            'type' => 'index',
            'fields' => array('stock_number'),
        ),
        array(
            'name' => 'idx_dm_vehiclesinventory_make_model',
            'type' => 'index',
            'fields' => array('make', 'model', 'year'),
        ),
        array(
            'name' => 'idx_dm_vehiclesinventory_status',
            'type' => 'index',
            'fields' => array('status'),
        ),
        array(
            'name' => 'idx_dm_vehiclesinventory_assigned',
            'type' => 'index',
            'fields' => array('assigned_user_id'),
        ),
        array(
            'name' => 'idx_dm_vehiclesinventory_deleted',
            'type' => 'index',
            'fields' => array('deleted'),
        ),
    ),
    
    'templates' => array(
        'basic' => 1,
        'assignable' => 1,
    ),
);

VardefManager::createVardef('DM_VehiclesInventory', 'DM_VehiclesInventory', array('basic', 'assignable')); 