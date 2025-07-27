<?php
$module_name = 'AutoInventory';
$searchdefs [$module_name] = array(
    'layout' => array(
        'basic_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%'
            ),
            'vin_number' => array(
                'name' => 'vin_number',
                'default' => true,
                'width' => '10%'
            ),
            'stock_id' => array(
                'name' => 'stock_id',
                'default' => true,
                'width' => '10%'
            ),
            'manufacturer' => array(
                'name' => 'manufacturer',
                'default' => true,
                'width' => '10%'
            )
        ),
        'advanced_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%'
            ),
            'vin_number' => array(
                'name' => 'vin_number',
                'default' => true,
                'width' => '10%'
            ),
            'stock_id' => array(
                'name' => 'stock_id',
                'default' => true,
                'width' => '10%'
            ),
            'manufacturer' => array(
                'name' => 'manufacturer',
                'default' => true,
                'width' => '10%'
            ),
            'vehicle_model' => array(
                'name' => 'vehicle_model',
                'default' => true,
                'width' => '10%'
            ),
            'model_year' => array(
                'name' => 'model_year',
                'default' => true,
                'width' => '10%'
            ),
            'inventory_status' => array(
                'name' => 'inventory_status',
                'default' => true,
                'width' => '10%'
            ),
            'vehicle_condition' => array(
                'name' => 'vehicle_condition',
                'default' => true,
                'width' => '10%'
            ),
            'asking_price' => array(
                'name' => 'asking_price',
                'default' => true,
                'width' => '10%'
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'relate',
                'default' => true,
                'width' => '10%'
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'default' => true,
                'width' => '10%'
            ),
            'date_modified' => array(
                'name' => 'date_modified',
                'default' => true,
                'width' => '10%'
            )
        )
    ),
    'templateMeta' => array(
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => array(
            'label' => '10',
            'field' => '30'
        )
    )
);
?> 