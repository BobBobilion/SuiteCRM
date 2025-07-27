<?php
/**
 * SuiteCRM Service & Parts Hub - Service History Edit View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_ServiceHistory']['EditView'] = array(
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
            'LBL_PANEL_VEHICLE_SERVICE' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_WORK_PERFORMED' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_COST_LABOR' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
        ),
    ),
    'panels' => array(
        'LBL_PANEL_SERVICE_INFO' => array(
            array(
                'name',
                'service_date',
            ),
            array(
                'service_type',
                'mileage',
            ),
            array(
                'assigned_user_name',
                '',
            ),
        ),
        
        'LBL_PANEL_VEHICLE_SERVICE' => array(
            array(
                array(
                    'name' => 'vehicle_name',
                    'label' => 'LBL_VEHICLE_NAME',
                ),
                array(
                    'name' => 'service_order_name',
                    'label' => 'LBL_SERVICE_ORDER_NAME',
                ),
            ),
            array(
                array(
                    'name' => 'technician_name',
                    'label' => 'LBL_TECHNICIAN_NAME',
                ),
                '',
            ),
        ),
        
        'LBL_PANEL_WORK_PERFORMED' => array(
            array(
                array(
                    'name' => 'services_performed',
                    'label' => 'LBL_SERVICES_PERFORMED',
                    'type' => 'textarea',
                    'rows' => 4,
                    'cols' => 60,
                ),
            ),
            array(
                array(
                    'name' => 'parts_used',
                    'label' => 'LBL_PARTS_USED',
                    'type' => 'textarea',
                    'rows' => 3,
                    'cols' => 60,
                ),
            ),
        ),
        
        'LBL_PANEL_COST_LABOR' => array(
            array(
                'labor_hours',
                'total_cost',
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