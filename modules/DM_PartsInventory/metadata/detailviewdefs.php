<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Detail View Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_PartsInventory']['DetailView'] = array(
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
                    'type' => 'text',
                ),
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
                    'type' => 'text',
                ),
            ),
        ),
    ),
);
?>