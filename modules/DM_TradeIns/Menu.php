<?php
/**
 * SuiteCRM Trade-In Manager - Module Menu Definitions
 * 
 * This file defines the navigation menu items for the Trade-In Manager module,
 * including create, view, and import actions available in the module dropdown.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('DM_TradeIns', 'edit', true)) {
    $module_menu[] = array(
        "index.php?module=DM_TradeIns&action=EditView&return_module=DM_TradeIns&return_action=DetailView",
        $mod_strings['LNK_NEW_RECORD'],
        "Create",
        'DM_TradeIns'
    );
}

if (ACLController::checkAccess('DM_TradeIns', 'list', true)) {
    $module_menu[] = array(
        "index.php?module=DM_TradeIns&action=index&return_module=DM_TradeIns&return_action=DetailView",
        $mod_strings['LNK_LIST'],
        "TradeInList",
        'DM_TradeIns'
    );
}

if (ACLController::checkAccess('DM_TradeIns', 'import', true)) {
    $module_menu[] = array(
        "index.php?module=Import&action=Step1&import_module=DM_TradeIns&return_module=DM_TradeIns&return_action=index",
        $app_strings['LBL_IMPORT'],
        "Import",
        'DM_TradeIns'
    );
}

?> 