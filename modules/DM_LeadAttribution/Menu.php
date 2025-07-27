<?php
/**
 * SuiteCRM Lead Attribution Center - Module Menu Definitions
 * 
 * This file defines the navigation menu items for the Lead Attribution Center module,
 * including create, view, import, and reporting actions available in the module dropdown.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('DM_LeadAttribution', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=DM_LeadAttribution&action=EditView&return_module=DM_LeadAttribution&return_action=DetailView",
        $mod_strings['LNK_NEW_RECORD'],
        "Create",
        'DM_LeadAttribution'
    );
}

if (ACLController::checkAccess('DM_LeadAttribution', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_LeadAttribution&action=index&return_module=DM_LeadAttribution&return_action=DetailView",
        $mod_strings['LNK_LIST'],
        "AttributionList",
        'DM_LeadAttribution'
    );
}

if (ACLController::checkAccess('DM_LeadAttribution', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_LeadAttribution&action=ROIDashboard&return_module=DM_LeadAttribution&return_action=index",
        $mod_strings['LNK_ROI_DASHBOARD'],
        "ROIDashboard",
        'DM_LeadAttribution'
    );
}

if (ACLController::checkAccess('DM_LeadAttribution', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_LeadAttribution&action=AttributionReport&return_module=DM_LeadAttribution&return_action=index",
        $mod_strings['LNK_ATTRIBUTION_REPORT'],
        "AttributionReport",
        'DM_LeadAttribution'
    );
}

if (ACLController::checkAccess('DM_LeadAttribution', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_LeadAttribution&return_module=DM_LeadAttribution&return_action=index",
        $app_strings['LBL_IMPORT'],
        "Import",
        'DM_LeadAttribution'
    );
}

?>