<?php
/**
 * List View definitions for DM_VehicleFeatures module
 * 
 * This file defines which fields are displayed in the list view
 * and their properties
 */

$module_name = 'DM_VehicleFeatures';
$listViewDefs[$module_name] = array(
    'NAME' => array(
        'width' => '25%',
        'label' => 'LBL_NAME',
        'default' => true,
        'link' => true,
    ),
    'CATEGORY' => array(
        'type' => 'enum',
        'label' => 'LBL_CATEGORY',
        'width' => '15%',
        'default' => true,
    ),
    'FEATURE_TYPE' => array(
        'type' => 'enum',
        'label' => 'LBL_FEATURE_TYPE',
        'width' => '15%',
        'default' => true,
    ),
    'VEHICLE_TYPE' => array(
        'type' => 'enum',
        'label' => 'LBL_VEHICLE_TYPE',
        'width' => '15%',
        'default' => true,
    ),
    'IS_STANDARD' => array(
        'type' => 'bool',
        'label' => 'LBL_IS_STANDARD',
        'width' => '10%',
        'default' => true,
    ),
    'IS_ACTIVE' => array(
        'type' => 'bool',
        'label' => 'LBL_IS_ACTIVE',
        'width' => '10%',
        'default' => true,
    ),
    'DATE_MODIFIED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_MODIFIED',
        'width' => '10%',
        'default' => true,
    ),
);
?> 