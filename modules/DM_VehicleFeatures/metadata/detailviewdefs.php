<?php
/**
 * Detail View definitions for DM_VehicleFeatures module
 * 
 * This file defines the layout and fields for the vehicle features detail view.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_VehicleFeatures'] = array(
    'DetailView' => array(
        'templateMeta' => array(
            'form' => array(
                'buttons' => array(
                    0 => 'EDIT',
                    1 => 'DUPLICATE',
                    2 => 'DELETE',
                    3 => 'FIND_DUPLICATES',
                ),
            ),
            'maxColumns' => '2',
            'widths' => array(
                0 => array(
                    'label' => '10',
                    'field' => '30',
                ),
                1 => array(
                    'label' => '10',
                    'field' => '30',
                ),
            ),
            'useTabs' => false,
        ),
        'panels' => array(
            'DEFAULT' => array(
                0 => array(
                    0 => array(
                        'name' => 'name',
                        'label' => 'LBL_NAME',
                    ),
                    1 => array(
                        'name' => 'category',
                        'label' => 'LBL_CATEGORY',
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'feature_type',
                        'label' => 'LBL_FEATURE_TYPE',
                    ),
                    1 => array(
                        'name' => 'vehicle_type',
                        'label' => 'LBL_VEHICLE_TYPE',
                    ),
                ),
                2 => array(
                    0 => array(
                        'name' => 'is_standard',
                        'label' => 'LBL_IS_STANDARD',
                    ),
                    1 => array(
                        'name' => 'is_active',
                        'label' => 'LBL_IS_ACTIVE',
                    ),
                ),
                3 => array(
                    0 => array(
                        'name' => 'sort_order',
                        'label' => 'LBL_SORT_ORDER',
                    ),
                    1 => '',
                ),
                4 => array(
                    0 => array(
                        'name' => 'description',
                        'label' => 'LBL_DESCRIPTION',
                        'type' => 'text',
                    ),
                ),
                5 => array(
                    0 => array(
                        'name' => 'assigned_user_name',
                        'label' => 'LBL_ASSIGNED_TO_NAME',
                    ),
                    1 => '',
                ),
                6 => array(
                    0 => array(
                        'name' => 'date_entered',
                        'label' => 'LBL_DATE_ENTERED',
                        'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                    ),
                    1 => array(
                        'name' => 'date_modified',
                        'label' => 'LBL_DATE_MODIFIED',
                        'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                    ),
                ),
            ),
        ),
    ),
);
?> 