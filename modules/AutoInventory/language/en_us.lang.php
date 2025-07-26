<?php
/**
 * SuiteCRM Auto Inventory System - English Language File
 * 
 * This file contains all language labels and terminology for the 
 * AutoInventory module in the car dealership CRM system.
 * 
 * Includes comprehensive automotive terminology, field labels,
 * and user interface text for auto inventory management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // Module meta information
    'LBL_MODULE_NAME' => 'Auto Inventory',
    'LBL_MODULE_TITLE' => 'Auto Inventory Management',
    'LBL_MODULE_ID' => 'AutoInventory',
    'LBL_SEARCH_FORM_TITLE' => 'Auto Search',
    'LBL_LIST_FORM_TITLE' => 'Auto Inventory List',
    'LBL_NEW_FORM_TITLE' => 'Add New Auto',
    
    // Standard SugarBean fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Auto Name',
    'LBL_DATE_ENTERED' => 'Date Created',
    'LBL_DATE_MODIFIED' => 'Date Modified',
    'LBL_MODIFIED' => 'Modified By',
    'LBL_MODIFIED_ID' => 'Modified By Id',
    'LBL_MODIFIED_NAME' => 'Modified By Name',
    'LBL_CREATED' => 'Created By',
    'LBL_CREATED_ID' => 'Created By Id',
    'LBL_CREATED_USER' => 'Created By User',
    'LBL_CREATED_BY_NAME' => 'Created By Name',
    'LBL_DESCRIPTION' => 'Description',
    'LBL_DELETED' => 'Deleted',
    'LBL_ASSIGNED_TO_ID' => 'Assigned User Id',
    'LBL_ASSIGNED_TO_NAME' => 'Assigned to',
    'LBL_ASSIGNED_TO_USER' => 'Assigned User',
    'LBL_ASSIGNED_USER_ID' => 'Assigned User Id',
    'LBL_ASSIGNED_USER_NAME' => 'Assigned User Name',
    'LBL_MODIFIED_BY_USER' => 'Modified By User',
    'LBL_CREATED_BY_USER' => 'Created By User',
    
    // Core Auto Identification Fields
    'LBL_VIN_NUMBER' => 'VIN Number',
    'LBL_VIN_NUMBER_FULL' => 'Vehicle Identification Number',
    'LBL_STOCK_ID' => 'Stock ID',
    'LBL_STOCK_ID_HELP' => 'Dealer internal inventory identifier',
    
    // Vehicle Specifications
    'LBL_MODEL_YEAR' => 'Year',
    'LBL_MANUFACTURER' => 'Manufacturer',
    'LBL_VEHICLE_MODEL' => 'Model',
    'LBL_TRIM_LEVEL' => 'Trim',
    'LBL_BODY_TYPE' => 'Body Type',
    'LBL_PAINT_COLOR' => 'Paint Color',
    'LBL_INTERIOR_COLOR' => 'Interior Color',
    'LBL_ODOMETER' => 'Odometer',
    'LBL_ENGINE_INFO' => 'Engine',
    'LBL_TRANSMISSION_TYPE' => 'Transmission',
    'LBL_DRIVE_TYPE' => 'Drive Type',
    'LBL_FUEL_SYSTEM' => 'Fuel System',
    
    // Inventory Status and Condition
    'LBL_INVENTORY_STATUS' => 'Status',
    'LBL_VEHICLE_CONDITION' => 'Condition',
    'LBL_LOT_POSITION' => 'Lot Position',
    
    // Pricing and Financial Data
    'LBL_ACQUISITION_DATE' => 'Acquisition Date',
    'LBL_COST_BASIS' => 'Cost Basis',
    'LBL_ASKING_PRICE' => 'Asking Price',
    'LBL_FINAL_PRICE' => 'Final Price',
    'LBL_DAYS_IN_INVENTORY' => 'Days in Inventory',
    
    // Market Valuation
    'LBL_MARKET_VALUATION' => 'Market Valuation',
    'LBL_VALUATION_DATE' => 'Valuation Date',
    'LBL_ACQUISITION_SOURCE' => 'Source',
    
    // Extended Data
    'LBL_VEHICLE_FEATURES' => 'Features',
    'LBL_PHOTO_GALLERY' => 'Photos',
    
    // Navigation and Menu Links
    'LNK_NEW_RECORD' => 'Create Auto',
    'LNK_LIST' => 'View Autos',
    'LNK_IMPORT_AUTOINVENTORY' => 'Import Autos',
    'LBL_ADD_AUTO' => 'Add Auto',
    'LBL_AVAILABLE_AUTOS' => 'Available Autos',
    'LBL_NEW_AUTOS' => 'New Autos',
    'LBL_USED_AUTOS' => 'Used Autos',
    'LBL_AGING_INVENTORY' => 'Aging Inventory',
    'LBL_RECENT_ARRIVALS' => 'Recent Arrivals',
    'LBL_INVENTORY_REPORTS' => 'Inventory Reports',
    
    // List View Labels
    'LBL_LIST_AUTO_NAME' => 'Auto',
    'LBL_LIST_VIN_NUMBER' => 'VIN',
    'LBL_LIST_STOCK_ID' => 'Stock ID',
    'LBL_LIST_MODEL_YEAR' => 'Year',
    'LBL_LIST_MANUFACTURER' => 'Make',
    'LBL_LIST_VEHICLE_MODEL' => 'Model',
    'LBL_LIST_INVENTORY_STATUS' => 'Status',
    'LBL_LIST_ASKING_PRICE' => 'Price',
    'LBL_LIST_ODOMETER' => 'Miles',
    'LBL_LIST_DAYS_IN_INVENTORY' => 'Days',
    'LBL_LIST_ASSIGNED_USER_NAME' => 'Assigned',
    
    // Detail View Labels
    'LBL_AUTO_INFORMATION' => 'Auto Information',
    'LBL_SPECIFICATIONS' => 'Specifications',
    'LBL_PRICING_INFORMATION' => 'Pricing Information',
    'LBL_INVENTORY_STATUS_INFO' => 'Inventory Status',
    'LBL_MARKET_INFORMATION' => 'Market Information',
    'LBL_PHOTOS_FEATURES' => 'Photos & Features',
    
    // Search Form Labels
    'LBL_SEARCH_VIN_NUMBER' => 'VIN',
    'LBL_SEARCH_STOCK_ID' => 'Stock ID',
    'LBL_SEARCH_MAKE_MODEL' => 'Make/Model',
    'LBL_SEARCH_YEAR_FROM' => 'Year From',
    'LBL_SEARCH_YEAR_TO' => 'Year To',
    'LBL_SEARCH_PRICE_FROM' => 'Price From',
    'LBL_SEARCH_PRICE_TO' => 'Price To',
    'LBL_SEARCH_STATUS' => 'Status',
    'LBL_SEARCH_CONDITION' => 'Condition',
    
    // Error Messages
    'ERR_INVALID_VIN' => 'Invalid VIN format. Please enter a valid 17-character VIN.',
    'ERR_DUPLICATE_VIN' => 'This VIN already exists in the system.',
    'ERR_DUPLICATE_STOCK' => 'This stock ID already exists.',
    'ERR_REQUIRED_FIELD' => 'This field is required.',
    'ERR_INVALID_YEAR' => 'Please enter a valid year.',
    'ERR_INVALID_PRICE' => 'Please enter a valid price.',
    'ERR_INVALID_MILEAGE' => 'Please enter a valid mileage.',
    
    // Success Messages
    'MSG_AUTO_SAVED' => 'Auto saved successfully.',
    'MSG_AUTO_DELETED' => 'Auto deleted successfully.',
    'MSG_STATUS_UPDATED' => 'Auto status updated successfully.',
    
    // Action Labels
    'LBL_DECODE_VIN' => 'Decode VIN',
    'LBL_UPDATE_MARKET_VALUE' => 'Update Market Value',
    'LBL_MARK_AS_SOLD' => 'Mark as Sold',
    'LBL_PRINT_WINDOW_STICKER' => 'Print Window Sticker',
    'LBL_UPLOAD_PHOTOS' => 'Upload Photos',
    'LBL_BULK_UPDATE' => 'Bulk Update',
    
    // Help Text
    'LBL_HELP_VIN_NUMBER' => 'Enter the 17-character Vehicle Identification Number',
    'LBL_HELP_STOCK_ID' => 'Internal dealer stock or lot identifier',
    'LBL_HELP_VEHICLE_FEATURES' => 'List auto features and options',
    'LBL_HELP_PHOTO_GALLERY' => 'Upload auto photos (interior, exterior, engine)',
);

// Dropdown lists
$app_list_strings['auto_status_list'] = array(
    'Available' => 'Available',
    'Sold' => 'Sold',
    'Pending' => 'Pending',
    'Service' => 'In Service',
    'Hold' => 'On Hold',
    'Reserved' => 'Reserved',
);

$app_list_strings['auto_condition_list'] = array(
    'New' => 'New',
    'Used' => 'Used',
    'Certified' => 'Certified Pre-Owned',
    'Damaged' => 'Damaged',
    'Salvage' => 'Salvage',
);

$app_list_strings['auto_body_type_list'] = array(
    'Sedan' => 'Sedan',
    'Coupe' => 'Coupe',
    'Hatchback' => 'Hatchback',
    'SUV' => 'SUV',
    'Truck' => 'Truck',
    'Van' => 'Van',
    'Convertible' => 'Convertible',
    'Wagon' => 'Wagon',
    'Crossover' => 'Crossover',
);

$app_list_strings['auto_transmission_list'] = array(
    'Automatic' => 'Automatic',
    'Manual' => 'Manual',
    'CVT' => 'CVT',
    'Semi-Automatic' => 'Semi-Automatic',
);

$app_list_strings['auto_drive_type_list'] = array(
    'FWD' => 'Front Wheel Drive',
    'RWD' => 'Rear Wheel Drive',
    'AWD' => 'All Wheel Drive',
    '4WD' => '4 Wheel Drive',
);

$app_list_strings['auto_fuel_type_list'] = array(
    'Gasoline' => 'Gasoline',
    'Diesel' => 'Diesel',
    'Hybrid' => 'Hybrid',
    'Electric' => 'Electric',
    'Flex Fuel' => 'Flex Fuel',
    'CNG' => 'Compressed Natural Gas',
);

$app_list_strings['auto_acquisition_list'] = array(
    'Trade-In' => 'Trade-In',
    'Auction' => 'Auction',
    'Purchase' => 'Direct Purchase',
    'Dealer Exchange' => 'Dealer Exchange',
    'Lease Return' => 'Lease Return',
    'Manufacturer' => 'Manufacturer',
    'Consignment' => 'Consignment',
);
?> 