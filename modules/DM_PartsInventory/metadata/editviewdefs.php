<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Edit View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_PartsInventory']['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        'useTabs' => true,
        'tabDefs' => array(
            'LBL_PANEL_PART_INFO' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_INVENTORY' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_PRICING' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_SUPPLIER' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
        ),
    ),
    'panels' => array(
        'LBL_PANEL_PART_INFO' => array(
            array(
                'name',
                'part_number',
            ),
            array(
                'manufacturer',
                'category',
            ),
            array(
                array(
                    'name' => 'description',
                    'label' => 'LBL_DESCRIPTION',
                    'type' => 'textarea',
                    'rows' => 3,
                    'cols' => 60,
                ),
            ),
            array(
                'assigned_user_name',
                '',
            ),
        ),
        
        'LBL_PANEL_INVENTORY' => array(
            array(
                'quantity_on_hand',
                'reorder_point',
            ),
            array(
                'location',
                'last_ordered_date',
            ),
        ),
        
        'LBL_PANEL_PRICING' => array(
            array(
                'cost',
                'retail_price',
            ),
        ),
        
        'LBL_PANEL_SUPPLIER' => array(
            array(
                array(
                    'name' => 'supplier_name',
                    'label' => 'LBL_SUPPLIER_NAME',
                ),
                '',
            ),
            array(
                array(
                    'name' => 'notes',
                    'label' => 'LBL_NOTES',
                    'type' => 'textarea',
                    'rows' => 3,
                    'cols' => 60,
                ),
            ),
        ),
    ),
);
?>