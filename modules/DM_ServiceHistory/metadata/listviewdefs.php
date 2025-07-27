<?php
/**
 * SuiteCRM Service & Parts Hub - Service History List View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$listViewDefs['DM_ServiceHistory'] = array(
    'SERVICE_DATE' => array(
        'type' => 'date',
        'label' => 'LBL_LIST_SERVICE_DATE',
        'width' => '12%',
        'default' => true,
        'link' => true,
    ),
    'VEHICLE_NAME' => array(
        'type' => 'relate',
        'link' => 'vehicle_link',
        'label' => 'LBL_LIST_VEHICLE_NAME',
        'id' => 'VEHICLE_ID',
        'width' => '18%',
        'default' => true,
    ),
    'SERVICE_TYPE' => array(
        'type' => 'varchar',
        'label' => 'LBL_LIST_SERVICE_TYPE',
        'width' => '12%',
        'default' => true,
    ),
    'MILEAGE' => array(
        'type' => 'int',
        'label' => 'LBL_LIST_MILEAGE',
        'width' => '8%',
        'default' => true,
        'align' => 'right',
    ),
    'TECHNICIAN_NAME' => array(
        'type' => 'relate',
        'link' => 'technician_link',
        'label' => 'LBL_LIST_TECHNICIAN_NAME',
        'id' => 'TECHNICIAN_ID',
        'width' => '12%',
        'default' => true,
    ),
    'LABOR_HOURS' => array(
        'type' => 'decimal',
        'label' => 'LBL_LIST_LABOR_HOURS',
        'width' => '8%',
        'default' => true,
        'align' => 'right',
    ),
    'TOTAL_COST' => array(
        'type' => 'currency',
        'label' => 'LBL_LIST_TOTAL_COST',
        'width' => '10%',
        'default' => true,
        'align' => 'right',
    ),
    'SERVICE_ORDER_NAME' => array(
        'type' => 'relate',
        'link' => 'service_order_link',
        'label' => 'LBL_SERVICE_ORDER_NAME',
        'id' => 'SERVICE_ORDER_ID',
        'width' => '12%',
        'default' => false,
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
);