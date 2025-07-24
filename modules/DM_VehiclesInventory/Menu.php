<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Vehicle Inventory System - Module Menu
 * 
 * This file defines the module menu items for the vehicle inventory system.
 * Includes standard CRUD operations and vehicle-specific actions.
 */

global $mod_strings, $app_strings, $sugar_config;

$module_menu = array();

// Add New Vehicle - Available to users with edit access
if (ACLController::checkAccess('DM_VehiclesInventory', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehiclesInventory&action=EditView&return_module=DM_VehiclesInventory&return_action=ListView", 
        $mod_strings['LBL_ADD_VEHICLE'],
        "Create", 
        'DM_VehiclesInventory'
    );
}

// View Vehicle Inventory List - Available to users with list access
if (ACLController::checkAccess('DM_VehiclesInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehiclesInventory&action=ListView", 
        $mod_strings['LBL_LIST_FORM_TITLE'],
        "List", 
        'DM_VehiclesInventory'
    );
}

// Import Vehicles - Available to users with import access
if (ACLController::checkAccess('DM_VehiclesInventory', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_VehiclesInventory&return_module=DM_VehiclesInventory&return_action=ListView", 
        $mod_strings['LBL_IMPORT_VEHICLES'],
        "Import", 
        'DM_VehiclesInventory'
    );
}

// Available Vehicles Quick Filter
if (ACLController::checkAccess('DM_VehiclesInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehiclesInventory&action=ListView&query=true&status=Available", 
        $mod_strings['LBL_AVAILABLE_VEHICLES'],
        "List", 
        'DM_VehiclesInventory'
    );
}

// Aging Inventory Report
if (ACLController::checkAccess('DM_VehiclesInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehiclesInventory&action=ListView&query=true&days_on_lot_range_min=30", 
        $mod_strings['LBL_AGING_INVENTORY'],
        "List", 
        'DM_VehiclesInventory'
    );
}

// Recent Arrivals (Last 7 days)
if (ACLController::checkAccess('DM_VehiclesInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehiclesInventory&action=ListView&query=true&date_entered_range=last_7_days", 
        $mod_strings['LBL_RECENT_ARRIVALS'],
        "List", 
        'DM_VehiclesInventory'
    );
}

// Vehicle Reports - If user has access to Reports module
if (ACLController::checkAccess('Reports', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=Reports&action=index&query_module=DM_VehiclesInventory", 
        $mod_strings['LBL_INVENTORY_REPORTS'],
        "Reports", 
        'DM_VehiclesInventory'
    );
} 