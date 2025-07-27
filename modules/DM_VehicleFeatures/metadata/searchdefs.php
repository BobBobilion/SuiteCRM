<?php
/**
 * Search definitions for DM_VehicleFeatures module
 * 
 * This file defines the search form fields for vehicle features.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$module_name = 'DM_VehicleFeatures';
$searchdefs[$module_name] = array(
    'layout' => array(
        'basic_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'category' => array(
                'name' => 'category',
                'default' => true,
                'width' => '10%',
            ),
            'feature_type' => array(
                'name' => 'feature_type',
                'default' => true,
                'width' => '10%',
            ),
            'current_user_only' => array(
                'name' => 'current_user_only',
                'label' => 'LBL_CURRENT_USER_FILTER',
                'type' => 'bool',
                'default' => true,
                'width' => '10%',
            ),
        ),
        'advanced_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'category' => array(
                'name' => 'category',
                'default' => true,
                'width' => '10%',
            ),
            'feature_type' => array(
                'name' => 'feature_type',
                'default' => true,
                'width' => '10%',
            ),
            'vehicle_type' => array(
                'name' => 'vehicle_type',
                'default' => true,
                'width' => '10%',
            ),
            'is_standard' => array(
                'name' => 'is_standard',
                'default' => true,
                'width' => '10%',
            ),
            'is_active' => array(
                'name' => 'is_active',
                'default' => true,
                'width' => '10%',
            ),
            'description' => array(
                'name' => 'description',
                'default' => true,
                'width' => '10%',
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'label' => 'LBL_ASSIGNED_TO',
                'type' => 'enum',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'default' => true,
                'width' => '10%',
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'default' => true,
                'width' => '10%',
            ),
            'date_modified' => array(
                'name' => 'date_modified',
                'default' => true,
                'width' => '10%',
            ),
            'created_by' => array(
                'name' => 'created_by',
                'label' => 'LBL_CREATED',
                'type' => 'enum',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'default' => true,
                'width' => '10%',
            ),
            'modified_user_id' => array(
                'name' => 'modified_user_id',
                'label' => 'LBL_MODIFIED',
                'type' => 'enum',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'default' => true,
                'width' => '10%',
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