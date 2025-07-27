<?php
/**
 * SuiteCRM F&I Deal Center - Subpanel Definitions
 * 
 * This file defines the subpanels for the F&I Deal Center,
 * including the Deal Documents subpanel.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$layout_defs['DM_FIDeals']['subpanel_setup']['dm_dealdocuments'] = array(
    'order' => 100,
    'module' => 'DM_DealDocuments',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'id',
    'title_key' => 'LBL_SUBPANEL_DOCUMENTS',
    'get_subpanel_data' => 'dm_dealdocuments',
    'top_buttons' => array(
        array(
            'widget_class' => 'SubPanelTopCreateButton',
            'mode' => 'MultiSelect',
        ),
        array(
            'widget_class' => 'SubPanelTopSelectButton',
            'mode' => 'MultiSelect',
        ),
    ),
);