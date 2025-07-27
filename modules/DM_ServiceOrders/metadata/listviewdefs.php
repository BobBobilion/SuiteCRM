<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders List View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$listViewDefs['DM_ServiceOrders'] = array(
    'SERVICE_ORDER_NUMBER' => array(
        'type' => 'varchar',
        'label' => 'LBL_LIST_SERVICE_ORDER_NUMBER',
        'width' => '10%',
        'default' => true,
        'link' => true,
    ),
    'CUSTOMER_NAME' => array(
        'type' => 'relate',
        'link' => 'customer_link',
        'label' => 'LBL_LIST_CUSTOMER_NAME',
        'id' => 'CUSTOMER_ID',
        'width' => '20%',
        'default' => true,
    ),
    'VEHICLE_NAME' => array(
        'type' => 'relate',
        'link' => 'vehicle_link',
        'label' => 'LBL_LIST_VEHICLE_NAME',
        'id' => 'VEHICLE_ID',
        'width' => '15%',
        'default' => true,
    ),
    'VIN' => array(
        'type' => 'varchar',
        'label' => 'LBL_VIN',
        'width' => '12%',
        'default' => true,
    ),
    'SERVICE_STATUS' => array(
        'type' => 'enum',
        'label' => 'LBL_LIST_SERVICE_STATUS',
        'width' => '10%',
        'default' => true,
    ),
    'APPOINTMENT_DATE' => array(
        'type' => 'datetime',
        'label' => 'LBL_LIST_APPOINTMENT_DATE',
        'width' => '12%',
        'default' => true,
    ),
    'SERVICE_ADVISOR_NAME' => array(
        'type' => 'relate',
        'link' => 'service_advisor_link',
        'label' => 'LBL_LIST_SERVICE_ADVISOR_NAME',
        'id' => 'SERVICE_ADVISOR_ID',
        'width' => '12%',
        'default' => true,
    ),
    'TOTAL_AMOUNT' => array(
        'type' => 'currency',
        'label' => 'LBL_LIST_TOTAL_AMOUNT',
        'width' => '10%',
        'default' => true,
        'align' => 'right',
    ),
    'ASSIGNED_USER_NAME' => array(
        'width' => '9%',
        'label' => 'LBL_ASSIGNED_TO_NAME',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => false,
    ),
    'DATE_ENTERED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_ENTERED',
        'width' => '9%',
        'default' => false,
    ),
    'DATE_MODIFIED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_MODIFIED',
        'width' => '9%',
        'default' => false,
    ),
);
?>