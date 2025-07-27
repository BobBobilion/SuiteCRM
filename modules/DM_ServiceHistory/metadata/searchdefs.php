<?php
/**
 * SuiteCRM Service & Parts Hub - Service History Search Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$searchdefs['DM_ServiceHistory'] = array(
    'layout' => array(
        'basic_search' => array(
            'service_date' => array(
                'name' => 'service_date',
                'type' => 'date',
                'label' => 'LBL_SEARCH_SERVICE_DATE',
                'width' => '10%',
                'default' => true,
            ),
            'vehicle_name' => array(
                'name' => 'vehicle_name',
                'type' => 'relate',
                'link' => 'vehicle_link',
                'label' => 'LBL_SEARCH_VEHICLE_NAME',
                'id' => 'vehicle_id',
                'width' => '10%',
                'default' => true,
            ),
            'service_type' => array(
                'name' => 'service_type',
                'type' => 'varchar',
                'label' => 'LBL_SEARCH_SERVICE_TYPE',
                'width' => '10%',
                'default' => true,
            ),
            'technician_name' => array(
                'name' => 'technician_name',
                'type' => 'relate',
                'link' => 'technician_link',
                'label' => 'LBL_SEARCH_TECHNICIAN_NAME',
                'id' => 'technician_id',
                'width' => '10%',
                'default' => true,
            ),
        ),
        'advanced_search' => array(
            'service_date' => array(
                'name' => 'service_date',
                'type' => 'date',
                'label' => 'LBL_SERVICE_DATE',
                'width' => '10%',
                'default' => true,
            ),
            'vehicle_name' => array(
                'name' => 'vehicle_name',
                'type' => 'relate',
                'link' => 'vehicle_link',
                'label' => 'LBL_VEHICLE_NAME',
                'id' => 'vehicle_id',
                'width' => '10%',
                'default' => true,
            ),
            'service_order_name' => array(
                'name' => 'service_order_name',
                'type' => 'relate',
                'link' => 'service_order_link',
                'label' => 'LBL_SERVICE_ORDER_NAME',
                'id' => 'service_order_id',
                'width' => '10%',
                'default' => true,
            ),
            'service_type' => array(
                'name' => 'service_type',
                'type' => 'varchar',
                'label' => 'LBL_SERVICE_TYPE',
                'width' => '10%',
                'default' => true,
            ),
            'technician_name' => array(
                'name' => 'technician_name',
                'type' => 'relate',
                'link' => 'technician_link',
                'label' => 'LBL_TECHNICIAN_NAME',
                'id' => 'technician_id',
                'width' => '10%',
                'default' => true,
            ),
            'mileage' => array(
                'name' => 'mileage',
                'type' => 'int',
                'label' => 'LBL_MILEAGE',
                'width' => '10%',
                'default' => true,
            ),
            'labor_hours' => array(
                'name' => 'labor_hours',
                'type' => 'decimal',
                'label' => 'LBL_LABOR_HOURS',
                'width' => '10%',
                'default' => true,
            ),
            'total_cost' => array(
                'name' => 'total_cost',
                'type' => 'currency',
                'label' => 'LBL_TOTAL_COST',
                'width' => '10%',
                'default' => true,
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'enum',
                'label' => 'LBL_ASSIGNED_TO',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'width' => '10%',
                'default' => true,
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'type' => 'datetime',
                'label' => 'LBL_DATE_ENTERED',
                'width' => '10%',
                'default' => true,
            ),
        ),
    ),
    'templateMeta' => array(
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => array(
            'label' => '10',
            'field' => '30',
        ),
    ),
);