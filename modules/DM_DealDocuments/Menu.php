<?php
/**
 * SuiteCRM Deal Documentation Suite - Menu Definitions
 * 
 * This file defines the menu items for the Deal Documents module.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings;

if (ACLController::checkAccess('DM_DealDocuments', 'edit', true)) {
    $module_menu[] = array(
        'index.php?module=DM_DealDocuments&action=EditView&return_module=DM_DealDocuments&return_action=DetailView',
        $mod_strings['LNK_NEW_RECORD'],
        'CreateDealDocuments',
        'DM_DealDocuments'
    );
}

if (ACLController::checkAccess('DM_DealDocuments', 'list', true)) {
    $module_menu[] = array(
        'index.php?module=DM_DealDocuments&action=index&return_module=DM_DealDocuments&return_action=DetailView',
        $mod_strings['LNK_LIST'],
        'DealDocuments',
        'DM_DealDocuments'
    );
}

if (ACLController::checkAccess('DM_DealDocuments', 'import', true)) {
    $module_menu[] = array(
        'index.php?module=Import&action=Step1&import_module=DM_DealDocuments&return_module=DM_DealDocuments&return_action=index',
        $app_strings['LBL_IMPORT'],
        'Import',
        'DM_DealDocuments'
    );
}