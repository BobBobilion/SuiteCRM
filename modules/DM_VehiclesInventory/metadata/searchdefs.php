<?php
/**
 * SuiteCRM Vehicle Inventory System - Search Definitions
 * 
 * This file defines the search fields and functionality for the vehicle inventory.
 * Includes both basic and advanced search options optimized for automotive data.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$searchdefs['DM_VehiclesInventory'] = array(
    'templateMeta' => array(
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => array(
            'label' => '10',
            'field' => '30',
        ),
    ),
    'layout' => array(
        'basic_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'make_model' => array(
                'name' => 'make_model',
                'label' => 'LBL_SEARCH_MAKE_MODEL',
                'type' => 'varchar',
                'default' => true,
                'width' => '10%',
                'customCode' => '<input type="text" name="make_model" id="make_model" size="30" value="{$fields.make_model.value}" placeholder="Enter Make or Model">',
            ),
            'vin' => array(
                'name' => 'vin',
                'default' => true,
                'width' => '10%',
            ),
            'status' => array(
                'name' => 'status',
                'default' => true,
                'width' => '10%',
            ),
            'current_user_only' => array(
                'name' => 'current_user_only',
                'label' => 'LBL_CURRENT_USER_FILTER',
                'type' => 'bool',
                'default' => true,
                'width' => '10%',
            ),
            'favorites_only' => array(
                'name' => 'favorites_only',
                'label' => 'LBL_FAVORITES_FILTER',
                'type' => 'bool',
            ),
        ),
        'advanced_search' => array(
            // Row 1: Basic vehicle identification
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'vin' => array(
                'name' => 'vin',
                'default' => true,
                'width' => '10%',
            ),
            'stock_number' => array(
                'name' => 'stock_number',
                'default' => true,
                'width' => '10%',
            ),
            
            // Row 2: Vehicle specifications
            'year_range' => array(
                'name' => 'year_range',
                'label' => 'LBL_SEARCH_YEAR_RANGE',
                'type' => 'int_range',
                'enable_range_search' => true,
                'options' => 'numeric_range_search_dom',
                'default' => true,
                'width' => '10%',
            ),
            'make' => array(
                'name' => 'make',
                'default' => true,
                'width' => '10%',
            ),
            'model' => array(
                'name' => 'model',
                'default' => true,
                'width' => '10%',
            ),
            
            // Row 3: Additional specs
            'body_style' => array(
                'name' => 'body_style',
                'default' => true,
                'width' => '10%',
            ),
            'transmission' => array(
                'name' => 'transmission',
                'default' => true,
                'width' => '10%',
            ),
            'fuel_type' => array(
                'name' => 'fuel_type',
                'default' => true,
                'width' => '10%',
            ),
            
            // Row 4: Colors and condition
            'exterior_color' => array(
                'name' => 'exterior_color',
                'default' => true,
                'width' => '10%',
            ),
            'interior_color' => array(
                'name' => 'interior_color',
                'default' => false,
                'width' => '10%',
            ),
            'condition_type' => array(
                'name' => 'condition_type',
                'default' => true,
                'width' => '10%',
            ),
            
            // Row 5: Pricing
            'list_price_range' => array(
                'name' => 'list_price_range',
                'label' => 'LBL_SEARCH_PRICE_RANGE',
                'type' => 'currency_range',
                'enable_range_search' => true,
                'options' => 'numeric_range_search_dom',
                'default' => true,
                'width' => '10%',
            ),
            'purchase_price_range' => array(
                'name' => 'purchase_price_range',
                'label' => 'LBL_PURCHASE_PRICE_RANGE',
                'type' => 'currency_range',
                'enable_range_search' => true,
                'options' => 'numeric_range_search_dom',
                'default' => false,
                'width' => '10%',
            ),
            'market_value_range' => array(
                'name' => 'market_value_range',
                'label' => 'LBL_MARKET_VALUE_RANGE',
                'type' => 'currency_range',
                'enable_range_search' => true,
                'options' => 'numeric_range_search_dom',
                'default' => false,
                'width' => '10%',
            ),
            
            // Row 6: Mileage and dates
            'mileage_range' => array(
                'name' => 'mileage_range',
                'label' => 'LBL_SEARCH_MILEAGE_RANGE',
                'type' => 'int_range',
                'enable_range_search' => true,
                'options' => 'numeric_range_search_dom',
                'default' => true,
                'width' => '10%',
            ),
            'days_on_lot_range' => array(
                'name' => 'days_on_lot_range',
                'label' => 'LBL_DAYS_ON_LOT_RANGE',
                'type' => 'int_range',
                'enable_range_search' => true,
                'options' => 'numeric_range_search_dom',
                'default' => false,
                'width' => '10%',
            ),
            'purchase_date_range' => array(
                'name' => 'purchase_date_range',
                'label' => 'LBL_PURCHASE_DATE_RANGE',
                'type' => 'date_range',
                'enable_range_search' => true,
                'options' => 'date_range_search_dom',
                'default' => false,
                'width' => '10%',
            ),
            
            // Row 7: Inventory and business data
            'status' => array(
                'name' => 'status',
                'default' => true,
                'width' => '10%',
            ),
            'location' => array(
                'name' => 'location',
                'default' => false,
                'width' => '10%',
            ),
            'source' => array(
                'name' => 'source',
                'default' => false,
                'width' => '10%',
            ),
            
            // Row 8: Assignment and dates
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'assigned_user_name',
                'label' => 'LBL_ASSIGNED_TO',
                'default' => false,
                'width' => '10%',
            ),
            'date_entered_range' => array(
                'name' => 'date_entered_range',
                'label' => 'LBL_DATE_ENTERED',
                'type' => 'date_range',
                'enable_range_search' => true,
                'options' => 'date_range_search_dom',
                'default' => false,
                'width' => '10%',
            ),
            'date_modified_range' => array(
                'name' => 'date_modified_range',
                'label' => 'LBL_DATE_MODIFIED',
                'type' => 'date_range',
                'enable_range_search' => true,
                'options' => 'date_range_search_dom',
                'default' => false,
                'width' => '10%',
            ),
            
            // Row 9: Engine and drivetrain
            'engine_type' => array(
                'name' => 'engine_type',
                'default' => false,
                'width' => '10%',
            ),
            'drivetrain' => array(
                'name' => 'drivetrain',
                'default' => false,
                'width' => '10%',
            ),
            'trim' => array(
                'name' => 'trim',
                'default' => false,
                'width' => '10%',
            ),
            
            // Row 10: Text search fields
            'features_search' => array(
                'name' => 'features_search',
                'label' => 'LBL_FEATURES_SEARCH',
                'type' => 'varchar',
                'default' => false,
                'width' => '10%',
                'customCode' => '<input type="text" name="features_search" id="features_search" size="30" value="{$fields.features_search.value}" placeholder="Search vehicle features">',
            ),
            'notes_search' => array(
                'name' => 'notes_search',
                'label' => 'LBL_NOTES_SEARCH',
                'type' => 'varchar',
                'default' => false,
                'width' => '10%',
                'customCode' => '<input type="text" name="notes_search" id="notes_search" size="30" value="{$fields.notes_search.value}" placeholder="Search notes">',
            ),
            'aging_category' => array(
                'name' => 'aging_category',
                'label' => 'LBL_AGING_CATEGORY',
                'type' => 'enum',
                'options' => 'vehicle_aging_category_list',
                'default' => false,
                'width' => '10%',
            ),
        ),
    ),
);

// Define custom dropdown options for search
global $app_list_strings;

$app_list_strings['vehicle_aging_category_list'] = array(
    '' => '',
    'fresh' => 'Fresh (0-30 days)',
    'aging' => 'Aging (31-60 days)',
    'stale' => 'Stale (60+ days)',
);

// Quick filter buttons for common searches
$searchdefs['DM_VehiclesInventory']['templateMeta']['quickFilters'] = array(
    'available_vehicles' => array(
        'label' => 'Available Vehicles',
        'filters' => array(
            'status' => 'Available',
        ),
    ),
    'new_vehicles' => array(
        'label' => 'New Vehicles',
        'filters' => array(
            'condition_type' => 'New',
        ),
    ),
    'used_vehicles' => array(
        'label' => 'Used Vehicles', 
        'filters' => array(
            'condition_type' => 'Used',
        ),
    ),
    'aging_inventory' => array(
        'label' => 'Aging Inventory (30+ days)',
        'filters' => array(
            'days_on_lot_range_min' => '30',
        ),
    ),
    'high_mileage' => array(
        'label' => 'High Mileage (100k+ miles)',
        'filters' => array(
            'mileage_range_min' => '100000',
        ),
    ),
    'recent_arrivals' => array(
        'label' => 'Recent Arrivals (7 days)',
        'filters' => array(
            'date_entered_range' => 'last_7_days',
        ),
    ),
); 