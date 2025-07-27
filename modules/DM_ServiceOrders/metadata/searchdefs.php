<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Search Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$searchdefs['DM_ServiceOrders'] = array(
    'layout' => array(
        'basic_search' => array(
            'service_order_number' => array(
                'name' => 'service_order_number',
                'type' => 'varchar',
                'label' => 'LBL_SEARCH_SERVICE_ORDER_NUMBER',
                'width' => '10%',
                'default' => true,
            ),
            'customer_name' => array(
                'name' => 'customer_name',
                'type' => 'relate',
                'link' => 'customer_link',
                'label' => 'LBL_SEARCH_CUSTOMER_NAME',
                'id' => 'customer_id',
                'width' => '10%',
                'default' => true,
            ),
            'vin' => array(
                'name' => 'vin',
                'type' => 'varchar',
                'label' => 'LBL_SEARCH_VIN',
                'width' => '10%',
                'default' => true,
            ),
            'service_status' => array(
                'name' => 'service_status',
                'type' => 'enum',
                'label' => 'LBL_SEARCH_SERVICE_STATUS',
                'width' => '10%',
                'default' => true,
            ),
        ),
        'advanced_search' => array(
            'service_order_number' => array(
                'name' => 'service_order_number',
                'type' => 'varchar',
                'label' => 'LBL_SERVICE_ORDER_NUMBER',
                'width' => '10%',
                'default' => true,
            ),
            'customer_name' => array(
                'name' => 'customer_name',
                'type' => 'relate',
                'link' => 'customer_link',
                'label' => 'LBL_CUSTOMER_NAME',
                'id' => 'customer_id',
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
            'vin' => array(
                'name' => 'vin',
                'type' => 'varchar',
                'label' => 'LBL_VIN',
                'width' => '10%',
                'default' => true,
            ),
            'service_type' => array(
                'name' => 'service_type',
                'type' => 'enum',
                'label' => 'LBL_SERVICE_TYPE',
                'width' => '10%',
                'default' => true,
            ),
            'service_status' => array(
                'name' => 'service_status',
                'type' => 'enum',
                'label' => 'LBL_SERVICE_STATUS',
                'width' => '10%',
                'default' => true,
            ),
            'service_advisor_name' => array(
                'name' => 'service_advisor_name',
                'type' => 'relate',
                'link' => 'service_advisor_link',
                'label' => 'LBL_SERVICE_ADVISOR_NAME',
                'id' => 'service_advisor_id',
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
            'appointment_date' => array(
                'name' => 'appointment_date',
                'type' => 'datetime',
                'label' => 'LBL_APPOINTMENT_DATE',
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
            'date_modified' => array(
                'name' => 'date_modified',
                'type' => 'datetime',
                'label' => 'LBL_DATE_MODIFIED',
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
?>