<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Edit View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_ServiceOrders']['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        'useTabs' => true,
        'tabDefs' => array(
            'LBL_PANEL_SERVICE_INFO' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_VEHICLE_INFO' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_SCHEDULING' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_PERSONNEL' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_LABOR' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_FINANCIAL' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_SERVICE_DETAILS' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
        ),
    ),
    'panels' => array(
        'LBL_PANEL_SERVICE_INFO' => array(
            array(
                'name',
                'service_order_number',
            ),
            array(
                'service_type',
                'service_status',
            ),
            array(
                'assigned_user_name',
                '',
            ),
        ),
        
        'LBL_PANEL_VEHICLE_INFO' => array(
            array(
                array(
                    'name' => 'customer_name',
                    'label' => 'LBL_CUSTOMER_NAME',
                ),
                array(
                    'name' => 'vehicle_name',
                    'label' => 'LBL_VEHICLE_NAME',
                ),
            ),
            array(
                'vin',
                'mileage_in',
            ),
        ),
        
        'LBL_PANEL_SCHEDULING' => array(
            array(
                'appointment_date',
                'promise_time',
            ),
        ),
        
        'LBL_PANEL_PERSONNEL' => array(
            array(
                array(
                    'name' => 'service_advisor_name',
                    'label' => 'LBL_SERVICE_ADVISOR_NAME',
                ),
                array(
                    'name' => 'technician_name',
                    'label' => 'LBL_TECHNICIAN_NAME',
                ),
            ),
        ),
        
        'LBL_PANEL_LABOR' => array(
            array(
                'labor_hours',
                'labor_rate',
            ),
            array(
                'labor_total',
                '',
            ),
        ),
        
        'LBL_PANEL_FINANCIAL' => array(
            array(
                'parts_total',
                'tax_amount',
            ),
            array(
                'total_amount',
                '',
            ),
        ),
        
        'LBL_PANEL_SERVICE_DETAILS' => array(
            array(
                array(
                    'name' => 'customer_concern',
                    'label' => 'LBL_CUSTOMER_CONCERN',
                    'type' => 'textarea',
                    'rows' => 3,
                    'cols' => 60,
                ),
            ),
            array(
                array(
                    'name' => 'work_performed',
                    'label' => 'LBL_WORK_PERFORMED',
                    'type' => 'textarea',
                    'rows' => 4,
                    'cols' => 60,
                ),
            ),
            array(
                array(
                    'name' => 'notes',
                    'label' => 'LBL_NOTES',
                    'type' => 'textarea',
                    'rows' => 3,
                    'cols' => 60,
                ),
            ),
        ),
    ),
);
?>