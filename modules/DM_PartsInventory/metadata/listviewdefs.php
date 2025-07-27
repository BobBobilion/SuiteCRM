<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory List View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$listViewDefs['DM_PartsInventory'] = array(
    'PART_NUMBER' => array(
        'type' => 'varchar',
        'label' => 'LBL_LIST_PART_NUMBER',
        'width' => '12%',
        'default' => true,
        'link' => true,
    ),
    'DESCRIPTION' => array(
        'type' => 'text',
        'label' => 'LBL_LIST_DESCRIPTION',
        'width' => '25%',
        'default' => true,
    ),
    'MANUFACTURER' => array(
        'type' => 'varchar',
        'label' => 'LBL_LIST_MANUFACTURER',
        'width' => '12%',
        'default' => true,
    ),
    'CATEGORY' => array(
        'type' => 'enum',
        'label' => 'LBL_LIST_CATEGORY',
        'width' => '10%',
        'default' => true,
    ),
    'QUANTITY_ON_HAND' => array(
        'type' => 'int',
        'label' => 'LBL_LIST_QUANTITY_ON_HAND',
        'width' => '8%',
        'default' => true,
        'align' => 'right',
    ),
    'REORDER_POINT' => array(
        'type' => 'int',
        'label' => 'LBL_REORDER_POINT',
        'width' => '8%',
        'default' => false,
        'align' => 'right',
    ),
    'RETAIL_PRICE' => array(
        'type' => 'currency',
        'label' => 'LBL_LIST_RETAIL_PRICE',
        'width' => '10%',
        'default' => true,
        'align' => 'right',
    ),
    'SUPPLIER_NAME' => array(
        'type' => 'relate',
        'link' => 'supplier_link',
        'label' => 'LBL_LIST_SUPPLIER_NAME',
        'id' => 'SUPPLIER_ID',
        'width' => '12%',
        'default' => true,
    ),
    'LOCATION' => array(
        'type' => 'varchar',
        'label' => 'LBL_LOCATION',
        'width' => '8%',
        'default' => false,
    ),
    'ASSIGNED_USER_NAME' => array(
        'width' => '9%',
        'label' => 'LBL_ASSIGNED_TO_NAME',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => false,
    ),
    'DATE_ENTERED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_ENTERED',
        'width' => '9%',
        'default' => false,
    ),
    'DATE_MODIFIED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_MODIFIED',
        'width' => '9%',
        'default' => false,
    ),
);
?>