<?php
/**
 * SuiteCRM F&I Deal Center - Menu Configuration
 * 
 * This file defines the module menu items and navigation links
 * for the F&I Deal Center module.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

// Check if user has access to this module
if (ACLController::checkAccess('DM_FIDeals', 'edit', true)) {
    $module_menu = array(
        array(
            "index.php?module=DM_FIDeals&action=EditView&return_module=DM_FIDeals&return_action=DetailView",
            $mod_strings['LNK_NEW_RECORD'],
            "DM_FIDeals",
            'DM_FIDeals'
        ),
        array(
            "index.php?module=DM_FIDeals&action=index",
            $mod_strings['LNK_LIST'],
            "DM_FIDeals",
            'DM_FIDeals'
        ),
    );
}

// Add import link if user has import access
if (ACLController::checkAccess('DM_FIDeals', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_FIDeals&return_module=DM_FIDeals&return_action=index",
        $mod_strings['LNK_IMPORT_DM_FIDEALS'],
        "Import",
        'DM_FIDeals'
    );
}

// Add additional menu items for F&I specific features
if (ACLController::checkAccess('DM_FIDeals', 'list', true)) {
    // Add calculator menu item
    $module_menu[] = array(
        "index.php?module=DM_FIDeals&action=calculator",
        $mod_strings['LBL_PAYMENT_CALCULATOR'],
        "DM_FIDeals",
        'DM_FIDeals'
    );
    
    // Add F&I reports menu item
    $module_menu[] = array(
        "index.php?module=DM_FIDeals&action=reports",
        $mod_strings['LBL_FI_REPORTS'],
        "DM_FIDeals",
        'DM_FIDeals'
    );
    
    // Add F&I dashboard menu item
    $module_menu[] = array(
        "index.php?module=DM_FIDeals&action=reports&report_type=dashboard",
        $mod_strings['LBL_FI_DASHBOARD'],
        "DM_FIDeals",
        'DM_FIDeals'
    );
    
    // Add F&I analytics menu item
    $module_menu[] = array(
        "index.php?module=DM_FIDeals&action=analytics",
        $mod_strings['LBL_FI_ANALYTICS'],
        "DM_FIDeals",
        'DM_FIDeals'
    );
}

// Add admin-only menu items
if (is_admin($GLOBALS['current_user'])) {
    $module_menu[] = array(
        "index.php?module=Administration&action=index&view=fi_settings",
        'F&I Settings',
        "Administration",
        'DM_FIDeals'
    );
} 