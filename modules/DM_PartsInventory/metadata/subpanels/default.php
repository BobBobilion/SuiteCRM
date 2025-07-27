<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Subpanel Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$subpanel_layout['list_fields'] = array(
    'part_number' => array(
        'type' => 'varchar',
        'vname' => 'LBL_PART_NUMBER',
        'width' => '15%',
        'default' => true,
    ),
    'description' => array(
        'type' => 'text',
        'vname' => 'LBL_DESCRIPTION',
        'width' => '25%',
        'default' => true,
    ),
    'manufacturer' => array(
        'type' => 'varchar',
        'vname' => 'LBL_MANUFACTURER',
        'width' => '15%',
        'default' => true,
    ),
    'category' => array(
        'type' => 'enum',
        'vname' => 'LBL_CATEGORY',
        'width' => '10%',
        'default' => true,
    ),
    'quantity_on_hand' => array(
        'type' => 'int',
        'vname' => 'LBL_QUANTITY_ON_HAND',
        'width' => '8%',
        'default' => true,
    ),
    'retail_price' => array(
        'type' => 'currency',
        'vname' => 'LBL_RETAIL_PRICE',
        'width' => '10%',
        'default' => true,
    ),
    'edit_button' => array(
        'vname' => 'LBL_EDIT_BUTTON',
        'widget_class' => 'SubPanelEditButton',
        'module' => 'DM_PartsInventory',
        'width' => '5%',
        'default' => true,
    ),
    'remove_button' => array(
        'vname' => 'LBL_REMOVE',
        'widget_class' => 'SubPanelRemoveButton',
        'module' => 'DM_PartsInventory',
        'width' => '5%',
        'default' => true,
    ),
);
?>