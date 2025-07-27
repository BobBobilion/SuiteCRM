<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * Vehicle Features Module Menu
 * 
 * This file defines the module menu items for the vehicle features module.
 */

global $mod_strings, $app_strings, $sugar_config;

$module_menu = array();

// Create Vehicle Feature - Available to users with edit access
if (ACLController::checkAccess('DM_VehicleFeatures', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehicleFeatures&action=EditView&return_module=DM_VehicleFeatures&return_action=ListView", 
        $mod_strings['LNK_NEW_RECORD'],
        "Create", 
        'DM_VehicleFeatures'
    );
}

// View Vehicle Features List - Available to users with list access
if (ACLController::checkAccess('DM_VehicleFeatures', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehicleFeatures&action=ListView", 
        $mod_strings['LNK_LIST'],
        "List", 
        'DM_VehicleFeatures'
    );
}

// Import Vehicle Features - Available to users with import access
if (ACLController::checkAccess('DM_VehicleFeatures', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_VehicleFeatures&return_module=DM_VehicleFeatures&return_action=ListView", 
        $mod_strings['LNK_IMPORT_DM_VEHICLEFEATURES'],
        "Import", 
        'DM_VehicleFeatures'
    );
}

// Standard Features Quick Filter
if (ACLController::checkAccess('DM_VehicleFeatures', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehicleFeatures&action=ListView&query=true&is_standard=1", 
        $mod_strings['LBL_STANDARD_FEATURES'] ?? 'Standard Features',
        "List", 
        'DM_VehicleFeatures'
    );
}

// Active Features Filter
if (ACLController::checkAccess('DM_VehicleFeatures', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehicleFeatures&action=ListView&query=true&is_active=1", 
        $mod_strings['LBL_ACTIVE_FEATURES'] ?? 'Active Features',
        "List", 
        'DM_VehicleFeatures'
    );
}

// Safety Features Filter
if (ACLController::checkAccess('DM_VehicleFeatures', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehicleFeatures&action=ListView&query=true&category=safety", 
        $mod_strings['LBL_SAFETY_FEATURES'] ?? 'Safety Features',
        "List", 
        'DM_VehicleFeatures'
    );
}

// Technology Features Filter
if (ACLController::checkAccess('DM_VehicleFeatures', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_VehicleFeatures&action=ListView&query=true&category=technology", 
        $mod_strings['LBL_TECHNOLOGY_FEATURES'] ?? 'Technology Features',
        "List", 
        'DM_VehicleFeatures'
    );
}
?> 