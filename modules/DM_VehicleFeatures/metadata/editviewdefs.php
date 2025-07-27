<?php
/**
 * Edit View definitions for DM_VehicleFeatures module
 * 
 * This file defines the layout and fields for the vehicle features edit form.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_VehicleFeatures'] = array(
    'EditView' => array(
        'templateMeta' => array(
            'form' => array(
                'buttons' => array(
                    0 => 'SAVE',
                    1 => 'CANCEL',
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
                        'displayParams' => array(
                            'required' => true,
                        ),
                    ),
                    1 => array(
                        'name' => 'category',
                        'label' => 'LBL_CATEGORY',
                        'displayParams' => array(
                            'required' => true,
                        ),
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
                        'displayParams' => array(
                            'cols' => 80,
                            'rows' => 4,
                        ),
                    ),
                ),
                5 => array(
                    0 => array(
                        'name' => 'assigned_user_name',
                        'label' => 'LBL_ASSIGNED_TO_NAME',
                    ),
                    1 => '',
                ),
            ),
        ),
    ),
);
?> 