<?php
/**
 * SuiteCRM Service & Parts Hub - Service History Menu
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('DM_ServiceHistory', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=DM_ServiceHistory&action=EditView&return_module=DM_ServiceHistory&return_action=DetailView",
        $mod_strings['LNK_NEW_RECORD'],
        "DM_ServiceHistory",
        'DM_ServiceHistory'
    );
}

if (ACLController::checkAccess('DM_ServiceHistory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_ServiceHistory&action=index&return_module=DM_ServiceHistory&return_action=DetailView",
        $mod_strings['LNK_LIST'],
        "DM_ServiceHistory",
        'DM_ServiceHistory'
    );
}

if (ACLController::checkAccess('DM_ServiceHistory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_ServiceHistory&action=service_summary_report",
        $mod_strings['LBL_SERVICE_SUMMARY_REPORT'],
        "DM_ServiceHistory",
        'DM_ServiceHistory'
    );
}

if (ACLController::checkAccess('DM_ServiceHistory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_ServiceHistory&action=technician_performance_report",
        $mod_strings['LBL_TECHNICIAN_PERFORMANCE_REPORT'],
        "DM_ServiceHistory",
        'DM_ServiceHistory'
    );
}

if (ACLController::checkAccess('DM_ServiceHistory', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_ServiceHistory&return_module=DM_ServiceHistory&return_action=index",
        $app_strings['LBL_IMPORT'],
        "Import",
        'DM_ServiceHistory'
    );
}
?>