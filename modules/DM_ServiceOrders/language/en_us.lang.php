<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Language File (English)
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // Module Names
    'LBL_MODULE_NAME' => 'Service Orders',
    'LBL_MODULE_TITLE' => 'Service Orders: Home',
    'LBL_MODULE_ID' => 'Service Orders',
    'LBL_SEARCH_FORM_TITLE' => 'Service Order Search',
    'LBL_LIST_FORM_TITLE' => 'Service Orders List',
    'LBL_NEW_FORM_TITLE' => 'Create Service Order',
    'LBL_HOMEPAGE_TITLE' => 'My Service Orders',
    'LNK_NEW_RECORD' => 'Create Service Order',
    'LNK_LIST' => 'View Service Orders',
    'LNK_IMPORT_DM_SERVICEORDERS' => 'Import Service Orders',

    // Standard Fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Name',
    'LBL_DATE_ENTERED' => 'Date Created',
    'LBL_DATE_MODIFIED' => 'Date Modified',
    'LBL_MODIFIED' => 'Modified By',
    'LBL_MODIFIED_ID' => 'Modified By Id',
    'LBL_MODIFIED_NAME' => 'Modified By Name',
    'LBL_CREATED' => 'Created By',
    'LBL_CREATED_ID' => 'Created By Id',
    'LBL_CREATED_NAME' => 'Created By Name',
    'LBL_ASSIGNED_TO_ID' => 'Assigned User Id',
    'LBL_ASSIGNED_TO_NAME' => 'Assigned to',
    'LBL_DELETED' => 'Deleted',

    // Service Order Specific Fields
    'LBL_SERVICE_ORDER_NUMBER' => 'RO Number',
    'LBL_VIN' => 'VIN',
    'LBL_MILEAGE_IN' => 'Mileage In',
    'LBL_APPOINTMENT_DATE' => 'Appointment Date',
    'LBL_PROMISE_TIME' => 'Promise Time',
    'LBL_SERVICE_ADVISOR_ID' => 'Service Advisor ID',
    'LBL_SERVICE_ADVISOR_NAME' => 'Service Advisor',
    'LBL_TECHNICIAN_ID' => 'Technician ID',
    'LBL_TECHNICIAN_NAME' => 'Technician',
    'LBL_SERVICE_TYPE' => 'Service Type',
    'LBL_SERVICE_STATUS' => 'Status',
    'LBL_LABOR_HOURS' => 'Labor Hours',
    'LBL_LABOR_RATE' => 'Labor Rate',
    'LBL_LABOR_TOTAL' => 'Labor Total',
    'LBL_PARTS_TOTAL' => 'Parts Total',
    'LBL_TAX_AMOUNT' => 'Tax Amount',
    'LBL_TOTAL_AMOUNT' => 'Total Amount',
    'LBL_CUSTOMER_CONCERN' => 'Customer Concern',
    'LBL_WORK_PERFORMED' => 'Work Performed',
    'LBL_NOTES' => 'Notes',

    // Relationship Fields
    'LBL_CUSTOMER_ID' => 'Customer ID',
    'LBL_CUSTOMER_NAME' => 'Customer',
    'LBL_VEHICLE_ID' => 'Vehicle ID',
    'LBL_VEHICLE_NAME' => 'Vehicle',
    'LBL_CUSTOMER' => 'Customer',
    'LBL_VEHICLE' => 'Vehicle',
    'LBL_SERVICE_ADVISOR' => 'Service Advisor',
    'LBL_TECHNICIAN' => 'Technician',

    // User Links
    'LBL_ASSIGNED_TO_USER' => 'Assigned to User',
    'LBL_MODIFIED_BY_USER' => 'Modified by User',
    'LBL_CREATED_BY_USER' => 'Created by User',

    // Panel Labels
    'LBL_PANEL_SERVICE_INFO' => 'Service Information',
    'LBL_PANEL_VEHICLE_INFO' => 'Vehicle Information',
    'LBL_PANEL_SCHEDULING' => 'Scheduling',
    'LBL_PANEL_PERSONNEL' => 'Personnel',
    'LBL_PANEL_LABOR' => 'Labor & Pricing',
    'LBL_PANEL_FINANCIAL' => 'Financial Information',
    'LBL_PANEL_SERVICE_DETAILS' => 'Service Details',

    // List View Labels
    'LBL_LIST_SERVICE_ORDER_NUMBER' => 'RO #',
    'LBL_LIST_CUSTOMER_NAME' => 'Customer',
    'LBL_LIST_VEHICLE_NAME' => 'Vehicle',
    'LBL_LIST_SERVICE_STATUS' => 'Status',
    'LBL_LIST_APPOINTMENT_DATE' => 'Appointment',
    'LBL_LIST_TOTAL_AMOUNT' => 'Total',
    'LBL_LIST_SERVICE_ADVISOR_NAME' => 'Advisor',

    // Search Labels
    'LBL_SEARCH_SERVICE_ORDER_NUMBER' => 'RO Number',
    'LBL_SEARCH_CUSTOMER_NAME' => 'Customer',
    'LBL_SEARCH_VIN' => 'VIN',
    'LBL_SEARCH_SERVICE_STATUS' => 'Status',

    // Subpanel Labels
    'LBL_SUBPANEL_SERVICE_ORDERS' => 'Service Orders',

    // Help Text
    'LBL_HELP_SERVICE_ORDER_NUMBER' => 'Unique repair order number (auto-generated)',
    'LBL_HELP_CUSTOMER_CONCERN' => 'Description of the customer reported problem',
    'LBL_HELP_WORK_PERFORMED' => 'Detailed description of work completed',

    // Error Messages
    'ERR_DELETE_RECORD' => 'You must specify a record number to delete the service order.',
    'ERR_INVALID_VIN' => 'Please enter a valid VIN number.',

    // Tooltips
    'LBL_TOOLTIP_LABOR_RATE' => 'Shop hourly labor rate',
    'LBL_TOOLTIP_PROMISE_TIME' => 'Promised completion date and time',
);

// Dropdown options
$app_list_strings['service_type_list'] = array(
    'Maintenance' => 'Maintenance',
    'Repair' => 'Repair',
    'Warranty' => 'Warranty',
    'Inspection' => 'Inspection',
    'Recall' => 'Recall',
);

$app_list_strings['service_status_list'] = array(
    'Scheduled' => 'Scheduled',
    'In-Progress' => 'In Progress',
    'Complete' => 'Complete',
    'Picked-up' => 'Picked Up',
    'Cancelled' => 'Cancelled',
    'On-Hold' => 'On Hold',
);

$app_list_strings['dm_serviceorders_type_dom'] = $app_list_strings['service_type_list'];
$app_list_strings['dm_serviceorders_status_dom'] = $app_list_strings['service_status_list'];
?>