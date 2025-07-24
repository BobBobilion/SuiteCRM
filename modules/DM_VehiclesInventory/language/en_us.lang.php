<?php
/**
 * SuiteCRM Vehicle Inventory System - English Language File
 * 
 * This file contains all language labels and terminology for the 
 * DM_VehiclesInventory module in the car dealership CRM system.
 * 
 * Includes comprehensive automotive terminology, field labels,
 * and user interface text for vehicle inventory management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // Module meta information
    'LBL_MODULE_NAME' => 'Vehicle Inventory',
    'LBL_MODULE_TITLE' => 'Vehicle Inventory Management',
    'LBL_MODULE_ID' => 'DM_VehiclesInventory',
    'LBL_SEARCH_FORM_TITLE' => 'Vehicle Search',
    'LBL_LIST_FORM_TITLE' => 'Vehicle Inventory List',
    'LBL_NEW_FORM_TITLE' => 'Add New Vehicle',
    
    // Standard SugarBean fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Vehicle Name',
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
    
    // Core Vehicle Identification Fields
    'LBL_VIN' => 'VIN Number',
    'LBL_VIN_FULL' => 'Vehicle Identification Number',
    'LBL_STOCK_NUMBER' => 'Stock Number',
    'LBL_STOCK_NUMBER_HELP' => 'Dealer internal inventory number',
    
    // Vehicle Specifications
    'LBL_YEAR' => 'Model Year',
    'LBL_MAKE' => 'Make',
    'LBL_MODEL' => 'Model',
    'LBL_TRIM' => 'Trim Level',
    'LBL_BODY_STYLE' => 'Body Style',
    'LBL_EXTERIOR_COLOR' => 'Exterior Color',
    'LBL_INTERIOR_COLOR' => 'Interior Color',
    'LBL_MILEAGE' => 'Mileage',
    'LBL_ODOMETER' => 'Odometer Reading',
    'LBL_ENGINE_TYPE' => 'Engine',
    'LBL_TRANSMISSION' => 'Transmission',
    'LBL_DRIVETRAIN' => 'Drivetrain',
    'LBL_FUEL_TYPE' => 'Fuel Type',
    
    // Inventory Status and Condition
    'LBL_STATUS' => 'Status',
    'LBL_INVENTORY_STATUS' => 'Inventory Status',
    'LBL_CONDITION_TYPE' => 'Condition',
    'LBL_VEHICLE_CONDITION' => 'Vehicle Condition',
    'LBL_LOCATION' => 'Lot Location',
    'LBL_LOT_LOCATION' => 'Lot Location',
    'LBL_PARKING_SPACE' => 'Parking Space',
    
    // Pricing and Financial Data
    'LBL_PURCHASE_DATE' => 'Purchase Date',
    'LBL_ACQUISITION_DATE' => 'Acquisition Date',
    'LBL_PURCHASE_PRICE' => 'Purchase Price',
    'LBL_ACQUISITION_COST' => 'Acquisition Cost',
    'LBL_LIST_PRICE' => 'List Price',
    'LBL_ASKING_PRICE' => 'Asking Price',
    'LBL_SALE_PRICE' => 'Sale Price',
    'LBL_FINAL_PRICE' => 'Final Sale Price',
    'LBL_MARKET_VALUE' => 'Market Value',
    'LBL_KBB_VALUE' => 'KBB Value',
    'LBL_TRADE_VALUE' => 'Trade Value',
    'LBL_MARKET_VALUE_DATE' => 'Market Value Date',
    'LBL_VALUATION_DATE' => 'Last Valuation Date',
    'LBL_CURRENCY' => 'Currency',
    'LBL_CURRENCY_RATE' => 'Currency Rate',
    
    // Business Intelligence Fields
    'LBL_DAYS_ON_LOT' => 'Days on Lot',
    'LBL_INVENTORY_AGE' => 'Inventory Age',
    'LBL_AGE_IN_DAYS' => 'Age (Days)',
    'LBL_SOURCE' => 'Source',
    'LBL_VEHICLE_SOURCE' => 'Vehicle Source',
    'LBL_ACQUISITION_SOURCE' => 'How Acquired',
    'LBL_FEATURES' => 'Features',
    'LBL_VEHICLE_FEATURES' => 'Vehicle Features',
    'LBL_EQUIPMENT' => 'Equipment',
    'LBL_PHOTOS' => 'Photos',
    'LBL_VEHICLE_PHOTOS' => 'Vehicle Photos',
    'LBL_IMAGES' => 'Images',
    'LBL_NOTES' => 'Notes',
    'LBL_INTERNAL_NOTES' => 'Internal Notes',
    'LBL_COMMENTS' => 'Comments',
    
    // List View Headers
    'LBL_LIST_VEHICLE_NAME' => 'Vehicle',
    'LBL_LIST_VIN' => 'VIN',
    'LBL_LIST_STOCK_NUMBER' => 'Stock #',
    'LBL_LIST_YEAR' => 'Year',
    'LBL_LIST_MAKE' => 'Make',
    'LBL_LIST_MODEL' => 'Model',
    'LBL_LIST_TRIM' => 'Trim',
    'LBL_LIST_MILEAGE' => 'Miles',
    'LBL_LIST_STATUS' => 'Status',
    'LBL_LIST_CONDITION' => 'Condition',
    'LBL_LIST_LIST_PRICE' => 'Price',
    'LBL_LIST_DAYS_ON_LOT' => 'Days',
    'LBL_LIST_LOCATION' => 'Location',
    'LBL_LIST_ASSIGNED_USER' => 'Assigned To',
    
    // Search Form Labels
    'LBL_SEARCH_VEHICLE' => 'Search Vehicles',
    'LBL_SEARCH_VIN' => 'Search by VIN',
    'LBL_SEARCH_MAKE_MODEL' => 'Make/Model',
    'LBL_SEARCH_YEAR_RANGE' => 'Year Range',
    'LBL_SEARCH_PRICE_RANGE' => 'Price Range',
    'LBL_SEARCH_MILEAGE_RANGE' => 'Mileage Range',
    'LBL_SEARCH_STATUS' => 'Status',
    'LBL_SEARCH_CONDITION' => 'Condition',
    'LBL_SEARCH_LOCATION' => 'Location',
    
    // Form Section Headers
    'LBL_VEHICLE_INFORMATION' => 'Vehicle Information',
    'LBL_VEHICLE_DETAILS' => 'Vehicle Details',
    'LBL_SPECIFICATIONS' => 'Specifications',
    'LBL_PRICING_INFORMATION' => 'Pricing Information',
    'LBL_FINANCIAL_DATA' => 'Financial Data',
    'LBL_INVENTORY_DATA' => 'Inventory Data',
    'LBL_BUSINESS_DATA' => 'Business Data',
    'LBL_ADDITIONAL_INFORMATION' => 'Additional Information',
    'LBL_PHOTOS_FEATURES' => 'Photos & Features',
    'LBL_HISTORY_TRACKING' => 'History & Tracking',
    
    // Action Labels
    'LBL_ADD_VEHICLE' => 'Add Vehicle',
    'LBL_EDIT_VEHICLE' => 'Edit Vehicle',
    'LBL_VIEW_VEHICLE' => 'View Vehicle',
    'LBL_DELETE_VEHICLE' => 'Delete Vehicle',
    'LBL_DUPLICATE_VEHICLE' => 'Duplicate Vehicle',
    'LBL_PRINT_DETAILS' => 'Print Details',
    'LBL_EXPORT_VEHICLE' => 'Export Vehicle',
    'LBL_MARK_SOLD' => 'Mark as Sold',
    'LBL_MARK_AVAILABLE' => 'Mark as Available',
    'LBL_UPDATE_PRICING' => 'Update Pricing',
    'LBL_ADD_PHOTOS' => 'Add Photos',
    'LBL_MANAGE_FEATURES' => 'Manage Features',
    'LBL_DECODE_VIN' => 'Decode VIN',
    'LBL_PRINT_WINDOW_STICKER' => 'Print Window Sticker',
    
    // Status Messages
    'LBL_VEHICLE_SAVED' => 'Vehicle saved successfully',
    'LBL_VEHICLE_DELETED' => 'Vehicle deleted successfully',
    'LBL_VEHICLE_MARKED_SOLD' => 'Vehicle marked as sold',
    'LBL_PRICING_UPDATED' => 'Pricing updated successfully',
    'LBL_PHOTOS_UPLOADED' => 'Photos uploaded successfully',
    'LBL_VIN_DECODED' => 'VIN decoded successfully',
    
    // Error Messages
    'ERR_INVALID_VIN' => 'Invalid VIN format. Please enter a valid 17-character VIN.',
    'ERR_DUPLICATE_VIN' => 'A vehicle with this VIN already exists in inventory.',
    'ERR_DUPLICATE_STOCK' => 'A vehicle with this stock number already exists.',
    'ERR_MISSING_REQUIRED' => 'Please fill in all required fields.',
    'ERR_INVALID_YEAR' => 'Please enter a valid model year.',
    'ERR_INVALID_MILEAGE' => 'Please enter a valid mileage.',
    'ERR_INVALID_PRICE' => 'Please enter a valid price.',
    'ERR_DELETE_RECORD' => 'You must specify a record number to delete the vehicle.',
    'ERR_ACCESS_DENIED' => 'Access denied. You do not have permission to perform this action.',
    
    // Help Text
    'LBL_VIN_HELP' => 'Enter the 17-character Vehicle Identification Number',
    'LBL_STOCK_HELP' => 'Internal dealer stock or inventory number',
    'LBL_FEATURES_HELP' => 'List vehicle features separated by commas',
    'LBL_PHOTOS_HELP' => 'Upload vehicle photos (interior, exterior, engine)',
    'LBL_PRICING_HELP' => 'Enter purchase price, list price, and market value',
    'LBL_CONDITION_HELP' => 'Select whether vehicle is new, used, or certified',
    'LBL_STATUS_HELP' => 'Current inventory status (Available, Sold, Pending, etc.)',
    
    // Dropdown Lists References
    'LBL_BODY_STYLE_OPTIONS' => 'Body Style Options',
    'LBL_TRANSMISSION_OPTIONS' => 'Transmission Options',
    'LBL_DRIVETRAIN_OPTIONS' => 'Drivetrain Options',
    'LBL_FUEL_TYPE_OPTIONS' => 'Fuel Type Options',
    'LBL_STATUS_OPTIONS' => 'Status Options',
    'LBL_CONDITION_OPTIONS' => 'Condition Options',
    'LBL_SOURCE_OPTIONS' => 'Source Options',
    
    // Dashboard and Reporting
    'LBL_INVENTORY_SUMMARY' => 'Inventory Summary',
    'LBL_TOTAL_VEHICLES' => 'Total Vehicles',
    'LBL_AVAILABLE_VEHICLES' => 'Available',
    'LBL_SOLD_VEHICLES' => 'Sold',
    'LBL_PENDING_VEHICLES' => 'Pending',
    'LBL_AVERAGE_DAYS_LOT' => 'Avg Days on Lot',
    'LBL_TOTAL_INVENTORY_VALUE' => 'Total Inventory Value',
    'LBL_AGING_REPORT' => 'Aging Report',
    'LBL_FRESH_INVENTORY' => 'Fresh (0-30 days)',
    'LBL_AGING_INVENTORY' => 'Aging (31-60 days)', 
    'LBL_STALE_INVENTORY' => 'Stale (60+ days)',
    
    // Subpanel Titles
    'LBL_OPPORTUNITIES_SUBPANEL_TITLE' => 'Sales Opportunities',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'Interested Contacts',
    'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Customer Accounts',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documents',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'History',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Activities',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Notes',
    
    // Import/Export
    'LBL_IMPORT_VEHICLES' => 'Import Vehicles',
    'LBL_EXPORT_VEHICLES' => 'Export Vehicles',
    'LBL_IMPORT_NOTE' => 'Import vehicles from CSV or Excel file',
    'LBL_EXPORT_NOTE' => 'Export vehicle inventory to file',
    'LBL_TEMPLATE_DOWNLOAD' => 'Download Import Template',
    
    // Advanced Features
    'LBL_VIN_DECODER' => 'VIN Decoder',
    'LBL_MARKET_VALUATION' => 'Market Valuation',
    'LBL_PHOTO_GALLERY' => 'Photo Gallery',
    'LBL_FEATURE_MANAGER' => 'Feature Manager',
    'LBL_PRICING_CALCULATOR' => 'Pricing Calculator',
    'LBL_PROFIT_ANALYZER' => 'Profit Analyzer',
    'LBL_AGING_ALERTS' => 'Aging Alerts',
    'LBL_INVENTORY_REPORTS' => 'Inventory Reports',
    'LBL_RECENT_ARRIVALS' => 'Recent Arrivals',
    
    // Mobile Labels
    'LBL_MOBILE_SCAN_VIN' => 'Scan VIN',
    'LBL_MOBILE_PHOTO_CAPTURE' => 'Capture Photo',
    'LBL_MOBILE_LOT_WALK' => 'Lot Walk',
    'LBL_MOBILE_QUICK_ADD' => 'Quick Add',
    
    // Workflow and Automation
    'LBL_AUTO_PRICING' => 'Auto Pricing',
    'LBL_AGING_ALERTS_SETUP' => 'Aging Alerts Setup',
    'LBL_MARKET_SYNC' => 'Market Value Sync',
    'LBL_INVENTORY_WORKFLOW' => 'Inventory Workflow',
    
    // Integration Labels
    'LBL_DMS_SYNC' => 'DMS Sync',
    'LBL_WEBSITE_SYNC' => 'Website Sync',
    'LBL_THIRD_PARTY_LISTINGS' => 'Third Party Listings',
    'LBL_API_INTEGRATION' => 'API Integration',
    
    // Photo Gallery Labels
    'LBL_VEHICLE_PHOTOS' => 'Vehicle Photos',
    'LBL_ADD_PHOTOS' => 'Add Photos',
    'LBL_ADD_FIRST_PHOTO' => 'Add First Photo',
    'LBL_NO_PHOTOS_AVAILABLE' => 'No photos available for this vehicle.',
    'LBL_VEHICLE_PHOTO' => 'Vehicle Photo',
    'LBL_UPLOAD_PHOTOS' => 'Upload Photos',
    'LBL_DRAG_DROP_PHOTOS' => 'Drag and drop photos here',
    'LBL_OR_CLICK_TO_SELECT' => 'or click to select files',
    
    // Features Labels
    'LBL_VEHICLE_FEATURES' => 'Vehicle Features',
    'LBL_MANAGE_FEATURES' => 'Manage Features',
    'LBL_NO_FEATURES_AVAILABLE' => 'No features selected for this vehicle.',
    'LBL_ADD_FEATURES' => 'Add Features',
    'LBL_MANAGE_VEHICLE_FEATURES' => 'Manage Vehicle Features',
    'LBL_SAFETY_FEATURES' => 'Safety',
    'LBL_COMFORT_FEATURES' => 'Comfort',
    'LBL_TECHNOLOGY_FEATURES' => 'Technology',
    'LBL_PERFORMANCE_FEATURES' => 'Performance',
    'LBL_CUSTOM_FEATURES' => 'Custom',
    'LBL_TOTAL_FEATURES' => 'Total Features',
    'LBL_CATEGORIES' => 'Categories',
    'LBL_KEY_FEATURES' => 'Key Features',
    'LBL_FEATURE_NAME' => 'Feature Name',
    'LBL_FEATURE_VALUE' => 'Feature Value',
    'LBL_FEATURE_DESCRIPTION' => 'Feature Description',
    'LBL_HIGHLIGHT_FEATURE' => 'Highlight as Key Feature',
    'LBL_ADD_CUSTOM_FEATURE' => 'Add Custom Feature',
    'LBL_SELECTED_FEATURES' => 'Selected Features',
    'LBL_SAVE_FEATURES' => 'Save Features',
    
    // Bulk Operations Labels
    'LBL_BULK_ACTIONS' => 'Bulk Actions',
    'LBL_SELECT_ACTION' => 'Select Action',
    'LBL_UPDATE_STATUS' => 'Update Status',
    'LBL_DELETE_SELECTED' => 'Delete Selected',
    'LBL_EXPORT_SELECTED' => 'Export Selected',
    'LBL_NEW_STATUS' => 'New Status',
    'LBL_STATUS_AVAILABLE' => 'Available',
    'LBL_STATUS_SOLD' => 'Sold',
    'LBL_STATUS_PENDING' => 'Pending',
    'LBL_STATUS_SERVICE' => 'Service',
    'LBL_EXECUTE' => 'Execute',
    'LBL_CANCEL' => 'Cancel',
    
    // VIN Decoder Labels
    'LBL_VIN_DECODER' => 'VIN Decoder',
    'LBL_DECODE_VIN' => 'Decode VIN',
    'LBL_DECODED_INFORMATION' => 'Decoded Information',
    'LBL_APPLY_DATA' => 'Apply Data',
    
    // Enhanced List View Labels
    'LBL_AVAILABLE_VEHICLES' => 'Available Vehicles',
    'LBL_NEW_VEHICLES' => 'New Vehicles',
    'LBL_AGING_INVENTORY' => 'Aging Inventory',
    'LBL_STALE_INVENTORY' => 'Stale Inventory',
    
    // Action Labels
    'LBL_MARK_SOLD' => 'Mark as Sold',
    'LBL_UPDATE_PRICING' => 'Update Pricing',
    'LBL_DECODE_VIN_ACTION' => 'Decode VIN',
    'LBL_PRINT_STICKER' => 'Print Window Sticker',
    'LBL_GENERATE_QR' => 'Generate QR Code',
    'LBL_SHARE_VEHICLE' => 'Share Vehicle',
    
    // Edit View Labels
    'LBL_EDIT_VEHICLE' => 'Edit Vehicle',
    'LBL_ADD_VEHICLE' => 'Add New Vehicle',
);

// Dropdown option lists for vehicle-specific enums
$app_list_strings['vehicle_body_style_list'] = array(
    'Sedan' => 'Sedan',
    'SUV' => 'SUV',
    'Truck' => 'Truck',
    'Coupe' => 'Coupe',
    'Convertible' => 'Convertible',
    'Hatchback' => 'Hatchback',
    'Wagon' => 'Wagon',
    'Van' => 'Van',
    'Crossover' => 'Crossover',
    'Pickup' => 'Pickup Truck',
    'Roadster' => 'Roadster',
    'Other' => 'Other',
);

$app_list_strings['vehicle_transmission_list'] = array(
    'Manual' => 'Manual',
    'Automatic' => 'Automatic',
    'CVT' => 'CVT (Continuously Variable)',
    'Semi-Automatic' => 'Semi-Automatic',
    'Dual-Clutch' => 'Dual-Clutch',
    'Other' => 'Other',
);

$app_list_strings['vehicle_drivetrain_list'] = array(
    'FWD' => 'Front-Wheel Drive (FWD)',
    'RWD' => 'Rear-Wheel Drive (RWD)',
    'AWD' => 'All-Wheel Drive (AWD)',
    '4WD' => 'Four-Wheel Drive (4WD)',
    'Other' => 'Other',
);

$app_list_strings['vehicle_fuel_type_list'] = array(
    'Gasoline' => 'Gasoline',
    'Diesel' => 'Diesel',
    'Hybrid' => 'Hybrid',
    'Electric' => 'Electric',
    'Plug-in Hybrid' => 'Plug-in Hybrid',
    'Flex Fuel' => 'Flex Fuel (E85)',
    'CNG' => 'Compressed Natural Gas',
    'Hydrogen' => 'Hydrogen',
    'Other' => 'Other',
);

$app_list_strings['vehicle_status_list'] = array(
    'Available' => 'Available',
    'Sold' => 'Sold',
    'Pending' => 'Pending Sale',
    'Hold' => 'On Hold',
    'Service' => 'In Service',
    'Transit' => 'In Transit',
    'Wholesale' => 'Wholesale',
    'Auction' => 'At Auction',
    'Other' => 'Other',
);

$app_list_strings['vehicle_condition_list'] = array(
    'New' => 'New',
    'Used' => 'Used',
    'Certified' => 'Certified Pre-Owned',
    'Demo' => 'Demo Vehicle',
    'Loaner' => 'Loaner Vehicle',
    'Rental' => 'Former Rental',
    'Fleet' => 'Former Fleet',
    'Other' => 'Other',
);

$app_list_strings['vehicle_source_list'] = array(
    'Trade-in' => 'Trade-in',
    'Auction' => 'Auction',
    'Purchase' => 'Direct Purchase',
    'Lease Return' => 'Lease Return',
    'Fleet' => 'Fleet Vehicle',
    'Rental Return' => 'Rental Return',
    'Demo' => 'Demo Vehicle',
    'Factory' => 'Factory Direct',
    'Wholesale' => 'Wholesale',
    'Other Dealer' => 'Other Dealer',
    'Private Party' => 'Private Party',
    'Other' => 'Other',
); 