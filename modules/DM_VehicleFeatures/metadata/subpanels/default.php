<?php
/**
 * Default subpanel definition for DM_VehicleFeatures
 * 
 * This file defines how DM_VehicleFeatures appears as a subpanel in other modules
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$module_name = 'DM_VehicleFeatures';
$subpanel_layout = array(
    'top_buttons' => array(
        array('widget_class' => 'SubPanelTopCreateButton'),
        array('widget_class' => 'SubPanelTopSelectButton'),
    ),
    'where' => '',
    'list_fields' => array(
        'name' => array(
            'vname' => 'LBL_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '25%',
        ),
        'category' => array(
            'vname' => 'LBL_CATEGORY',
            'width' => '15%',
        ),
        'feature_type' => array(
            'vname' => 'LBL_FEATURE_TYPE',
            'width' => '15%',
        ),
        'vehicle_type' => array(
            'vname' => 'LBL_VEHICLE_TYPE',
            'width' => '15%',
        ),
        'is_standard' => array(
            'vname' => 'LBL_IS_STANDARD',
            'width' => '10%',
        ),
        'is_active' => array(
            'vname' => 'LBL_IS_ACTIVE',
            'width' => '10%',
        ),
        'date_modified' => array(
            'vname' => 'LBL_DATE_MODIFIED',
            'width' => '10%',
        ),
        'edit_button' => array(
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => $module_name,
            'width' => '4%',
        ),
        'remove_button' => array(
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => $module_name,
            'width' => '5%',
        ),
    ),
);
?> 