<?php
/**
 * SuiteCRM Deal Documentation Suite - Subpanel Definitions
 * 
 * This file defines how Deal Documents appear in subpanels of other modules.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$subpanel_layout['list_fields'] = array(
    'name' => array(
        'vname' => 'LBL_NAME',
        'widget_class' => 'SubPanelDetailViewLink',
        'width' => '25%',
        'default' => true,
    ),
    'document_type' => array(
        'type' => 'enum',
        'vname' => 'LBL_DOCUMENT_TYPE',
        'width' => '15%',
        'default' => true,
    ),
    'document_status' => array(
        'type' => 'enum',
        'vname' => 'LBL_DOCUMENT_STATUS',
        'width' => '12%',
        'default' => true,
    ),
    'generation_date' => array(
        'type' => 'datetime',
        'vname' => 'LBL_GENERATION_DATE',
        'width' => '15%',
        'default' => true,
    ),
    'assigned_user_name' => array(
        'vname' => 'LBL_ASSIGNED_TO_NAME',
        'widget_class' => 'SubPanelDetailViewLink',
        'target_record_key' => 'assigned_user_id',
        'target_module' => 'Users',
        'width' => '10%',
        'default' => true,
    ),
    'date_modified' => array(
        'vname' => 'LBL_DATE_MODIFIED',
        'width' => '15%',
        'default' => true,
    ),
    'edit_button' => array(
        'vname' => 'LBL_EDIT_BUTTON',
        'widget_class' => 'SubPanelEditButton',
        'module' => 'DM_DealDocuments',
        'width' => '4%',
        'default' => true,
    ),
    'remove_button' => array(
        'vname' => 'LBL_REMOVE',
        'widget_class' => 'SubPanelRemoveButton',
        'module' => 'DM_DealDocuments',
        'width' => '5%',
        'default' => true,
    ),
);