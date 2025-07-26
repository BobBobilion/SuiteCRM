<?php
/**
 * SuiteCRM Auto Inventory System - Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * AutoInventory module in the car dealership CRM system.
 * 
 * Includes all automotive specifications, pricing data, business intelligence
 * fields, and relationships with other SuiteCRM modules.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['AutoInventory'] = array(
    'table' => 'auto_inventory',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Auto inventory management for automotive dealerships including specifications, pricing, photos, and business workflow integration',
    'fields' => array(
        // Core Vehicle Identification
        'vin_number' => array(
            'name' => 'vin_number',
            'vname' => 'LBL_VIN_NUMBER',
            'type' => 'varchar',
            'len' => '17',
            'size' => '20',
            'required' => false,
            'unified_search' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
                'boost' => 1.8
            ),
            'comment' => 'Vehicle Identification Number (17 characters)',
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'duplicate_merge_dom_value' => '1',
            'audited' => true,
            'reportable' => true,
        ),
        'stock_id' => array(
            'name' => 'stock_id',
            'vname' => 'LBL_STOCK_ID',
            'type' => 'varchar',
            'len' => '50',
            'size' => '20',
            'required' => false,
            'unified_search' => true,
            'comment' => 'Dealer internal stock/inventory identifier',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'audited' => true,
            'reportable' => true,
        ),
        
        // Vehicle Specifications
        'model_year' => array(
            'name' => 'model_year',
            'vname' => 'LBL_MODEL_YEAR',
            'type' => 'int',
            'len' => '4',
            'size' => '6',
            'required' => false,
            'unified_search' => true,
            'comment' => 'Model year of the automobile',
            'importable' => 'true',
            'reportable' => true,
            'range_search' => true,
            'enable_range_search' => true,
        ),
        'manufacturer' => array(
            'name' => 'manufacturer',
            'vname' => 'LBL_MANUFACTURER',
            'type' => 'varchar',
            'len' => '100',
            'size' => '20',
            'required' => false,
            'unified_search' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
                'boost' => 1.5
            ),
            'comment' => 'Vehicle manufacturer (Ford, Toyota, Honda, etc.)',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'reportable' => true,
        ),
        'vehicle_model' => array(
            'name' => 'vehicle_model',
            'vname' => 'LBL_VEHICLE_MODEL',
            'type' => 'varchar',
            'len' => '100',
            'size' => '20',
            'required' => false,
            'unified_search' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
                'boost' => 1.5
            ),
            'comment' => 'Vehicle model name (Camry, F-150, Civic, etc.)',
            'importable' => 'true',
            'duplicate_merge' => 'enabled',
            'reportable' => true,
        ),
        'trim_level' => array(
            'name' => 'trim_level',
            'vname' => 'LBL_TRIM_LEVEL',
            'type' => 'varchar',
            'len' => '100',
            'size' => '20',
            'required' => false,
            'comment' => 'Trim level or package (LE, XLT, Sport, etc.)',
            'importable' => 'true',
            'reportable' => true,
        ),
        'body_type' => array(
            'name' => 'body_type',
            'vname' => 'LBL_BODY_TYPE',
            'type' => 'enum',
            'options' => 'auto_body_type_list',
            'len' => '50',
            'required' => false,
            'comment' => 'Vehicle body style',
            'importable' => 'true',
            'reportable' => true,
        ),
        'paint_color' => array(
            'name' => 'paint_color',
            'vname' => 'LBL_PAINT_COLOR',
            'type' => 'varchar',
            'len' => '50',
            'size' => '20',
            'required' => false,
            'comment' => 'Exterior paint color',
            'importable' => 'true',
            'reportable' => true,
        ),
        'interior_color' => array(
            'name' => 'interior_color',
            'vname' => 'LBL_INTERIOR_COLOR',
            'type' => 'varchar',
            'len' => '50',
            'size' => '20',
            'required' => false,
            'comment' => 'Interior color and material',
            'importable' => 'true',
            'reportable' => true,
        ),
        'odometer' => array(
            'name' => 'odometer',
            'vname' => 'LBL_ODOMETER',
            'type' => 'int',
            'len' => '10',
            'size' => '12',
            'required' => false,
            'comment' => 'Current odometer reading',
            'importable' => 'true',
            'reportable' => true,
            'range_search' => true,
            'enable_range_search' => true,
        ),
        'engine_info' => array(
            'name' => 'engine_info',
            'vname' => 'LBL_ENGINE_INFO',
            'type' => 'varchar',
            'len' => '100',
            'size' => '30',
            'required' => false,
            'comment' => 'Engine description (2.5L 4-Cyl, 5.0L V8, etc.)',
            'importable' => 'true',
            'reportable' => true,
        ),
        'transmission_type' => array(
            'name' => 'transmission_type',
            'vname' => 'LBL_TRANSMISSION_TYPE',
            'type' => 'enum',
            'options' => 'auto_transmission_list',
            'len' => '50',
            'required' => false,
            'comment' => 'Transmission type',
            'importable' => 'true',
            'reportable' => true,
        ),
        'drive_type' => array(
            'name' => 'drive_type',
            'vname' => 'LBL_DRIVE_TYPE',
            'type' => 'enum',
            'options' => 'auto_drive_type_list',
            'len' => '50',
            'required' => false,
            'comment' => 'Drivetrain configuration',
            'importable' => 'true',
            'reportable' => true,
        ),
        'fuel_system' => array(
            'name' => 'fuel_system',
            'vname' => 'LBL_FUEL_SYSTEM',
            'type' => 'enum',
            'options' => 'auto_fuel_type_list',
            'len' => '50',
            'required' => false,
            'comment' => 'Fuel system type',
            'importable' => 'true',
            'reportable' => true,
        ),
        
        // Inventory Status and Condition
        'inventory_status' => array(
            'name' => 'inventory_status',
            'vname' => 'LBL_INVENTORY_STATUS',
            'type' => 'enum',
            'options' => 'auto_status_list',
            'len' => '50',
            'required' => false,
            'default' => 'Available',
            'comment' => 'Current inventory status',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
        ),
        'vehicle_condition' => array(
            'name' => 'vehicle_condition',
            'vname' => 'LBL_VEHICLE_CONDITION',
            'type' => 'enum',
            'options' => 'auto_condition_list',
            'len' => '50',
            'required' => false,
            'default' => 'Used',
            'comment' => 'Vehicle condition type',
            'importable' => 'true',
            'reportable' => true,
        ),
        'lot_position' => array(
            'name' => 'lot_position',
            'vname' => 'LBL_LOT_POSITION',
            'type' => 'varchar',
            'len' => '100',
            'size' => '20',
            'required' => false,
            'comment' => 'Lot location or space identifier',
            'importable' => 'true',
            'reportable' => true,
        ),
        
        // Pricing and Financial Data
        'acquisition_date' => array(
            'name' => 'acquisition_date',
            'vname' => 'LBL_ACQUISITION_DATE',
            'type' => 'date',
            'required' => false,
            'comment' => 'Date vehicle was acquired',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
        ),
        'cost_basis' => array(
            'name' => 'cost_basis',
            'vname' => 'LBL_COST_BASIS',
            'type' => 'currency',
            'len' => '26,6',
            'size' => '20',
            'required' => false,
            'comment' => 'Acquisition cost basis',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
            'range_search' => true,
            'enable_range_search' => true,
        ),
        'asking_price' => array(
            'name' => 'asking_price',
            'vname' => 'LBL_ASKING_PRICE',
            'type' => 'currency',
            'len' => '26,6',
            'size' => '20',
            'required' => false,
            'comment' => 'Current asking price',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
            'range_search' => true,
            'enable_range_search' => true,
        ),
        'final_price' => array(
            'name' => 'final_price',
            'vname' => 'LBL_FINAL_PRICE',
            'type' => 'currency',
            'len' => '26,6',
            'size' => '20',
            'required' => false,
            'comment' => 'Final sale price (when sold)',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
        ),
        'days_in_inventory' => array(
            'name' => 'days_in_inventory',
            'vname' => 'LBL_DAYS_IN_INVENTORY',
            'type' => 'int',
            'len' => '10',
            'size' => '6',
            'required' => false,
            'default' => '0',
            'comment' => 'Auto-calculated days since acquisition',
            'reportable' => true,
            'calculated' => true,
            'range_search' => true,
            'enable_range_search' => true,
        ),
        
        // Market Valuation and Business Intelligence
        'market_valuation' => array(
            'name' => 'market_valuation',
            'vname' => 'LBL_MARKET_VALUATION',
            'type' => 'currency',
            'len' => '26,6',
            'size' => '20',
            'required' => false,
            'comment' => 'Current market valuation (KBB/Edmunds)',
            'importable' => 'true',
            'reportable' => true,
        ),
        'valuation_date' => array(
            'name' => 'valuation_date',
            'vname' => 'LBL_VALUATION_DATE',
            'type' => 'datetime',
            'required' => false,
            'comment' => 'Last market valuation update',
            'importable' => 'true',
            'reportable' => true,
        ),
        'acquisition_source' => array(
            'name' => 'acquisition_source',
            'vname' => 'LBL_ACQUISITION_SOURCE',
            'type' => 'enum',
            'options' => 'auto_acquisition_list',
            'len' => '100',
            'required' => false,
            'comment' => 'How vehicle was acquired',
            'importable' => 'true',
            'reportable' => true,
        ),
        
        // Extended Data (JSON stored)
        'vehicle_features' => array(
            'name' => 'vehicle_features',
            'vname' => 'LBL_VEHICLE_FEATURES',
            'type' => 'text',
            'required' => false,
            'comment' => 'JSON array of vehicle features and options',
            'rows' => 4,
            'cols' => 80,
        ),
        'photo_gallery' => array(
            'name' => 'photo_gallery',
            'vname' => 'LBL_PHOTO_GALLERY',
            'type' => 'text',
            'required' => false,
            'comment' => 'JSON array of photo URLs',
            'rows' => 4,
            'cols' => 80,
        ),
    ),
    'relationships' => array(
        'auto_inventory_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'AutoInventory',
            'rhs_table' => 'auto_inventory',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'auto_inventory_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'AutoInventory',
            'rhs_table' => 'auto_inventory',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'auto_inventory_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'AutoInventory',
            'rhs_table' => 'auto_inventory',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'optimistic_locking' => true,
    'unified_search' => true,
    'indices' => array(
        array(
            'name' => 'auto_inventory_pk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_auto_inventory_vin',
            'type' => 'index',
            'fields' => array('vin_number')
        ),
        array(
            'name' => 'idx_auto_inventory_stock',
            'type' => 'index',
            'fields' => array('stock_id')
        ),
        array(
            'name' => 'idx_auto_inventory_make_model',
            'type' => 'index',
            'fields' => array('manufacturer', 'vehicle_model', 'model_year')
        ),
        array(
            'name' => 'idx_auto_inventory_status',
            'type' => 'index',
            'fields' => array('inventory_status', 'deleted')
        ),
        array(
            'name' => 'idx_auto_inventory_assigned',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_auto_inventory_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        ),
    )
);

// Use VardefManager to apply standard templates
VardefManager::createVardef('AutoInventory', 'AutoInventory', array('basic', 'assignable'));
?> 