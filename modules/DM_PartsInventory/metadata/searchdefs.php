<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Search Definitions
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$searchdefs['DM_PartsInventory'] = array(
    'layout' => array(
        'basic_search' => array(
            'part_number' => array(
                'name' => 'part_number',
                'type' => 'varchar',
                'label' => 'LBL_SEARCH_PART_NUMBER',
                'width' => '10%',
                'default' => true,
            ),
            'description' => array(
                'name' => 'description',
                'type' => 'text',
                'label' => 'LBL_SEARCH_DESCRIPTION',
                'width' => '10%',
                'default' => true,
            ),
            'manufacturer' => array(
                'name' => 'manufacturer',
                'type' => 'varchar',
                'label' => 'LBL_SEARCH_MANUFACTURER',
                'width' => '10%',
                'default' => true,
            ),
            'category' => array(
                'name' => 'category',
                'type' => 'enum',
                'label' => 'LBL_SEARCH_CATEGORY',
                'width' => '10%',
                'default' => true,
            ),
        ),
        'advanced_search' => array(
            'part_number' => array(
                'name' => 'part_number',
                'type' => 'varchar',
                'label' => 'LBL_PART_NUMBER',
                'width' => '10%',
                'default' => true,
            ),
            'description' => array(
                'name' => 'description',
                'type' => 'text',
                'label' => 'LBL_DESCRIPTION',
                'width' => '10%',
                'default' => true,
            ),
            'manufacturer' => array(
                'name' => 'manufacturer',
                'type' => 'varchar',
                'label' => 'LBL_MANUFACTURER',
                'width' => '10%',
                'default' => true,
            ),
            'category' => array(
                'name' => 'category',
                'type' => 'enum',
                'label' => 'LBL_CATEGORY',
                'width' => '10%',
                'default' => true,
            ),
            'supplier_name' => array(
                'name' => 'supplier_name',
                'type' => 'relate',
                'link' => 'supplier_link',
                'label' => 'LBL_SUPPLIER_NAME',
                'id' => 'supplier_id',
                'width' => '10%',
                'default' => true,
            ),
            'quantity_on_hand' => array(
                'name' => 'quantity_on_hand',
                'type' => 'int',
                'label' => 'LBL_QUANTITY_ON_HAND',
                'width' => '10%',
                'default' => true,
            ),
            'reorder_point' => array(
                'name' => 'reorder_point',
                'type' => 'int',
                'label' => 'LBL_REORDER_POINT',
                'width' => '10%',
                'default' => true,
            ),
            'cost' => array(
                'name' => 'cost',
                'type' => 'currency',
                'label' => 'LBL_COST',
                'width' => '10%',
                'default' => true,
            ),
            'retail_price' => array(
                'name' => 'retail_price',
                'type' => 'currency',
                'label' => 'LBL_RETAIL_PRICE',
                'width' => '10%',
                'default' => true,
            ),
            'location' => array(
                'name' => 'location',
                'type' => 'varchar',
                'label' => 'LBL_LOCATION',
                'width' => '10%',
                'default' => true,
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'enum',
                'label' => 'LBL_ASSIGNED_TO',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'width' => '10%',
                'default' => true,
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'type' => 'datetime',
                'label' => 'LBL_DATE_ENTERED',
                'width' => '10%',
                'default' => true,
            ),
            'date_modified' => array(
                'name' => 'date_modified',
                'type' => 'datetime',
                'label' => 'LBL_DATE_MODIFIED',
                'width' => '10%',
                'default' => true,
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