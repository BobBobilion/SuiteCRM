<?php
/**
 * SuiteCRM Vehicle Inventory System - List View Definitions
 * 
 * This file defines the columns and layout for the vehicle inventory list view.
 * Optimized for automotive dealership operations with key vehicle data visible.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$listViewDefs['DM_VehiclesInventory'] = array(
    'NAME' => array(
        'width' => '20%',
        'label' => 'LBL_LIST_VEHICLE_NAME',
        'link' => true,
        'default' => true,
        'orderBy' => 'name',
    ),
    'YEAR' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_YEAR',
        'default' => true,
        'orderBy' => 'year',
    ),
    'MAKE' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_MAKE',
        'default' => true,
        'orderBy' => 'make',
    ),
    'MODEL' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_MODEL',
        'default' => true,
        'orderBy' => 'model',
    ),
    'VIN' => array(
        'width' => '15%',
        'label' => 'LBL_LIST_VIN',
        'default' => true,
        'orderBy' => 'vin',
    ),
    'STOCK_NUMBER' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_STOCK_NUMBER',
        'default' => true,
        'orderBy' => 'stock_number',
    ),
    'MILEAGE' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_MILEAGE',
        'default' => true,
        'orderBy' => 'mileage',
        'align' => 'right',
    ),
    'STATUS' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_STATUS',
        'default' => true,
        'orderBy' => 'status',
    ),
    'LIST_PRICE' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_LIST_PRICE',
        'default' => true,
        'orderBy' => 'list_price',
        'align' => 'right',
        'currency_format' => true,
    ),
    'DAYS_ON_LOT' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_DAYS_ON_LOT',
        'default' => true,
        'orderBy' => 'days_on_lot',
        'align' => 'right',
    ),
    'ASSIGNED_USER_NAME' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true,
        'orderBy' => 'assigned_user_name',
    ),
    'TRIM' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_TRIM',
        'default' => false,
        'orderBy' => 'trim',
    ),
    'BODY_STYLE' => array(
        'width' => '10%',
        'label' => 'LBL_BODY_STYLE',
        'default' => false,
        'orderBy' => 'body_style',
    ),
    'EXTERIOR_COLOR' => array(
        'width' => '10%',
        'label' => 'LBL_EXTERIOR_COLOR',
        'default' => false,
        'orderBy' => 'exterior_color',
    ),
    'CONDITION_TYPE' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_CONDITION',
        'default' => false,
        'orderBy' => 'condition_type',
    ),
    'LOCATION' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_LOCATION',
        'default' => false,
        'orderBy' => 'location',
    ),
    'PURCHASE_DATE' => array(
        'width' => '10%',
        'label' => 'LBL_PURCHASE_DATE',
        'default' => false,
        'orderBy' => 'purchase_date',
    ),
    'PURCHASE_PRICE' => array(
        'width' => '10%',
        'label' => 'LBL_PURCHASE_PRICE',
        'default' => false,
        'orderBy' => 'purchase_price',
        'align' => 'right',
        'currency_format' => true,
    ),
    'MARKET_VALUE' => array(
        'width' => '10%',
        'label' => 'LBL_MARKET_VALUE',
        'default' => false,
        'orderBy' => 'market_value',
        'align' => 'right',
        'currency_format' => true,
    ),
    'SOURCE' => array(
        'width' => '10%',
        'label' => 'LBL_SOURCE',
        'default' => false,
        'orderBy' => 'source',
    ),
    'TRANSMISSION' => array(
        'width' => '10%',
        'label' => 'LBL_TRANSMISSION',
        'default' => false,
        'orderBy' => 'transmission',
    ),
    'FUEL_TYPE' => array(
        'width' => '10%',
        'label' => 'LBL_FUEL_TYPE',
        'default' => false,
        'orderBy' => 'fuel_type',
    ),
    'ENGINE_TYPE' => array(
        'width' => '10%',
        'label' => 'LBL_ENGINE_TYPE',
        'default' => false,
        'orderBy' => 'engine_type',
    ),
    'DATE_ENTERED' => array(
        'width' => '10%',
        'label' => 'LBL_DATE_ENTERED',
        'default' => false,
        'orderBy' => 'date_entered',
    ),
    'DATE_MODIFIED' => array(
        'width' => '10%',
        'label' => 'LBL_DATE_MODIFIED',
        'default' => false,
        'orderBy' => 'date_modified',
    ),
); 