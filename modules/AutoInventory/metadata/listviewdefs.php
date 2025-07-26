<?php
$module_name = 'AutoInventory';
$listViewDefs [$module_name] = array(
    'NAME' => array(
        'width' => '25%',
        'label' => 'LBL_LIST_AUTO_NAME',
        'default' => true,
        'link' => true
    ),
    'VIN_NUMBER' => array(
        'width' => '15%',
        'label' => 'LBL_LIST_VIN_NUMBER',
        'default' => true
    ),
    'STOCK_ID' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_STOCK_ID',
        'default' => true
    ),
    'MODEL_YEAR' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_MODEL_YEAR',
        'default' => true
    ),
    'MANUFACTURER' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_MANUFACTURER',
        'default' => true
    ),
    'VEHICLE_MODEL' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_VEHICLE_MODEL',
        'default' => true
    ),
    'INVENTORY_STATUS' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_INVENTORY_STATUS',
        'default' => true
    ),
    'ASKING_PRICE' => array(
        'width' => '12%',
        'label' => 'LBL_LIST_ASKING_PRICE',
        'default' => true,
        'currency_format' => true,
        'align' => 'right'
    ),
    'ODOMETER' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_ODOMETER',
        'default' => false,
        'align' => 'right'
    ),
    'DAYS_IN_INVENTORY' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_DAYS_IN_INVENTORY',
        'default' => false,
        'align' => 'right'
    ),
    'ASSIGNED_USER_NAME' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_ASSIGNED_USER_NAME',
        'default' => false,
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID'
    ),
    'DATE_ENTERED' => array(
        'width' => '10%',
        'label' => 'LBL_DATE_ENTERED',
        'default' => false
    )
);
?> 