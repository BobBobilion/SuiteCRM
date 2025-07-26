<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Auto Inventory System - Module Menu
 * 
 * This file defines the module menu items for the auto inventory system.
 * Includes standard CRUD operations and automotive-specific actions.
 */

global $mod_strings, $app_strings, $sugar_config;

$module_menu = array();

// Add New Auto - Available to users with edit access
if (ACLController::checkAccess('AutoInventory', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=AutoInventory&action=EditView&return_module=AutoInventory&return_action=ListView", 
        $mod_strings['LBL_ADD_AUTO'],
        "Create", 
        'AutoInventory'
    );
}

// View Auto Inventory List - Available to users with list access
if (ACLController::checkAccess('AutoInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=AutoInventory&action=ListView", 
        $mod_strings['LBL_LIST_FORM_TITLE'],
        "List", 
        'AutoInventory'
    );
}

// Import Autos - Available to users with import access
if (ACLController::checkAccess('AutoInventory', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=AutoInventory&return_module=AutoInventory&return_action=ListView", 
        $mod_strings['LNK_IMPORT_AUTOINVENTORY'],
        "Import", 
        'AutoInventory'
    );
}

// Available Autos Quick Filter
if (ACLController::checkAccess('AutoInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=AutoInventory&action=ListView&query=true&inventory_status=Available", 
        $mod_strings['LBL_AVAILABLE_AUTOS'],
        "List", 
        'AutoInventory'
    );
}

// New Autos Filter
if (ACLController::checkAccess('AutoInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=AutoInventory&action=ListView&query=true&vehicle_condition=New", 
        $mod_strings['LBL_NEW_AUTOS'],
        "List", 
        'AutoInventory'
    );
}

// Used Autos Filter
if (ACLController::checkAccess('AutoInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=AutoInventory&action=ListView&query=true&vehicle_condition=Used", 
        $mod_strings['LBL_USED_AUTOS'],
        "List", 
        'AutoInventory'
    );
}

// Aging Inventory Report
if (ACLController::checkAccess('AutoInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=AutoInventory&action=ListView&query=true&days_in_inventory_range_min=30", 
        $mod_strings['LBL_AGING_INVENTORY'],
        "List", 
        'AutoInventory'
    );
}

// Recent Arrivals (Last 7 days)
if (ACLController::checkAccess('AutoInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=AutoInventory&action=ListView&query=true&date_entered_range=last_7_days", 
        $mod_strings['LBL_RECENT_ARRIVALS'],
        "List", 
        'AutoInventory'
    );
}

// Auto Reports - If user has access to Reports module
if (ACLController::checkAccess('Reports', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=Reports&action=index&query_module=AutoInventory", 
        $mod_strings['LBL_INVENTORY_REPORTS'],
        "Reports", 
        'AutoInventory'
    );
}
?> 