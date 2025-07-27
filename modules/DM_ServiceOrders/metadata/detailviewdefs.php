<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Detail View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_ServiceOrders']['DetailView'] = array(
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
                    'type' => 'text',
                ),
            ),
            array(
                array(
                    'name' => 'work_performed',
                    'label' => 'LBL_WORK_PERFORMED',
                    'type' => 'text',
                ),
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
?>