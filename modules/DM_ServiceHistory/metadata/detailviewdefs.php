<?php
/**
 * SuiteCRM Service & Parts Hub - Service History Detail View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_ServiceHistory']['DetailView'] = array(
    'templateMeta' => array(
        'form' => array(
            'buttons' => array('EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES'),
        ),
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
                array(
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                    'label' => 'LBL_DATE_ENTERED',
                ),
            ),
            array(
                '',
                array(
                    'name' => 'date_modified',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                    'label' => 'LBL_DATE_MODIFIED',
                ),
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
                    'type' => 'text',
                ),
            ),
            array(
                array(
                    'name' => 'parts_used',
                    'label' => 'LBL_PARTS_USED',
                    'type' => 'text',
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
                    'type' => 'text',
                ),
            ),
        ),
    ),
);