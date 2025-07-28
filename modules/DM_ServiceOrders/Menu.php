<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Menu
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('DM_ServiceOrders', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=DM_ServiceOrders&action=EditView&return_module=DM_ServiceOrders&return_action=DetailView",
        $mod_strings['LNK_NEW_RECORD'],
        "DM_ServiceOrders",
        'DM_ServiceOrders'
    );
}

if (ACLController::checkAccess('DM_ServiceOrders', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_ServiceOrders&action=index&return_module=DM_ServiceOrders&return_action=DetailView",
        $mod_strings['LNK_LIST'],
        "DM_ServiceOrders",
        'DM_ServiceOrders'
    );
}

if (ACLController::checkAccess('DM_ServiceOrders', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_ServiceOrders&return_module=DM_ServiceOrders&return_action=index",
        $app_strings['LBL_IMPORT'],
        "Import",
        'DM_ServiceOrders'
    );
}

// Service Summary Report
if (ACLController::checkAccess('DM_ServiceOrders', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_ServiceOrders&action=service_summary_report",
        "Service Summary Report",
        "Reports",
        'DM_ServiceOrders'
    );
}
?>