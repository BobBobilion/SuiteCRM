<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Menu
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('DM_PartsInventory', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=DM_PartsInventory&action=EditView&return_module=DM_PartsInventory&return_action=DetailView",
        $mod_strings['LNK_NEW_RECORD'],
        "DM_PartsInventory",
        'DM_PartsInventory'
    );
}

if (ACLController::checkAccess('DM_PartsInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_PartsInventory&action=index&return_module=DM_PartsInventory&return_action=DetailView",
        $mod_strings['LNK_LIST'],
        "DM_PartsInventory",
        'DM_PartsInventory'
    );
}

if (ACLController::checkAccess('DM_PartsInventory', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_PartsInventory&action=low_stock_report",
        $mod_strings['LBL_LOW_STOCK_REPORT'],
        "DM_PartsInventory",
        'DM_PartsInventory'
    );
}

if (ACLController::checkAccess('DM_PartsInventory', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_PartsInventory&return_module=DM_PartsInventory&return_action=index",
        $app_strings['LBL_IMPORT'],
        "Import",
        'DM_PartsInventory'
    );
}
?>