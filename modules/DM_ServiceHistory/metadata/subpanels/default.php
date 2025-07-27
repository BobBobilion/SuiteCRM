<?php
/**
 * SuiteCRM Service & Parts Hub - Service History Subpanel Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$subpanel_layout['list_fields'] = array(
    'service_date' => array(
        'type' => 'date',
        'vname' => 'LBL_SERVICE_DATE',
        'width' => '12%',
        'default' => true,
    ),
    'service_type' => array(
        'type' => 'varchar',
        'vname' => 'LBL_SERVICE_TYPE',
        'width' => '15%',
        'default' => true,
    ),
    'mileage' => array(
        'type' => 'int',
        'vname' => 'LBL_MILEAGE',
        'width' => '10%',
        'default' => true,
    ),
    'services_performed' => array(
        'type' => 'text',
        'vname' => 'LBL_SERVICES_PERFORMED',
        'width' => '25%',
        'default' => true,
    ),
    'technician_name' => array(
        'type' => 'relate',
        'link' => 'technician_link',
        'vname' => 'LBL_TECHNICIAN_NAME',
        'id' => 'technician_id',
        'width' => '12%',
        'default' => true,
    ),
    'labor_hours' => array(
        'type' => 'decimal',
        'vname' => 'LBL_LABOR_HOURS',
        'width' => '8%',
        'default' => true,
    ),
    'total_cost' => array(
        'type' => 'currency',
        'vname' => 'LBL_TOTAL_COST',
        'width' => '10%',
        'default' => true,
    ),
    'edit_button' => array(
        'vname' => 'LBL_EDIT_BUTTON',
        'widget_class' => 'SubPanelEditButton',
        'module' => 'DM_ServiceHistory',
        'width' => '4%',
        'default' => true,
    ),
    'remove_button' => array(
        'vname' => 'LBL_REMOVE',
        'widget_class' => 'SubPanelRemoveButton',
        'module' => 'DM_ServiceHistory',
        'width' => '4%',
        'default' => true,
    ),
);
?>