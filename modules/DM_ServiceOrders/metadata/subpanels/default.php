<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Subpanel Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$subpanel_layout['list_fields'] = array(
    'service_order_number' => array(
        'type' => 'varchar',
        'vname' => 'LBL_SERVICE_ORDER_NUMBER',
        'width' => '10%',
        'default' => true,
    ),
    'service_date' => array(
        'type' => 'date',
        'vname' => 'LBL_APPOINTMENT_DATE',
        'width' => '10%',
        'default' => true,
    ),
    'service_type' => array(
        'type' => 'enum',
        'vname' => 'LBL_SERVICE_TYPE',
        'width' => '10%',
        'default' => true,
    ),
    'service_status' => array(
        'type' => 'enum',
        'vname' => 'LBL_SERVICE_STATUS',
        'width' => '10%',
        'default' => true,
    ),
    'technician_name' => array(
        'type' => 'relate',
        'link' => 'technician_link',
        'vname' => 'LBL_TECHNICIAN_NAME',
        'id' => 'technician_id',
        'width' => '15%',
        'default' => true,
    ),
    'total_amount' => array(
        'type' => 'currency',
        'vname' => 'LBL_TOTAL_AMOUNT',
        'width' => '10%',
        'default' => true,
    ),
    'edit_button' => array(
        'vname' => 'LBL_EDIT_BUTTON',
        'widget_class' => 'SubPanelEditButton',
        'module' => 'DM_ServiceOrders',
        'width' => '5%',
        'default' => true,
    ),
    'remove_button' => array(
        'vname' => 'LBL_REMOVE',
        'widget_class' => 'SubPanelRemoveButton',
        'module' => 'DM_ServiceOrders',
        'width' => '5%',
        'default' => true,
    ),
);
?>