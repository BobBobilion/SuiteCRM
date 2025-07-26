<?php
$module_name = 'AutoInventory';
$viewdefs [$module_name] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 'FIND_DUPLICATES',
        ),
      ),
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'includes' => 
      array (
        0 => 
        array (
          'file' => 'modules/AutoInventory/AutoInventory.js',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'label' => 'LBL_NAME',
          ),
          1 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'vin_number',
            'label' => 'LBL_VIN_NUMBER',
          ),
          1 => 
          array (
            'name' => 'stock_id',
            'label' => 'LBL_STOCK_ID',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'model_year',
            'label' => 'LBL_MODEL_YEAR',
          ),
          1 => 
          array (
            'name' => 'manufacturer',
            'label' => 'LBL_MANUFACTURER',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'vehicle_model',
            'label' => 'LBL_VEHICLE_MODEL',
          ),
          1 => 
          array (
            'name' => 'trim_level',
            'label' => 'LBL_TRIM_LEVEL',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'body_type',
            'label' => 'LBL_BODY_TYPE',
          ),
          1 => 
          array (
            'name' => 'vehicle_condition',
            'label' => 'LBL_VEHICLE_CONDITION',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'paint_color',
            'label' => 'LBL_PAINT_COLOR',
          ),
          1 => 
          array (
            'name' => 'interior_color',
            'label' => 'LBL_INTERIOR_COLOR',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'odometer',
            'label' => 'LBL_ODOMETER',
          ),
          1 => 
          array (
            'name' => 'engine_info',
            'label' => 'LBL_ENGINE_INFO',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'transmission_type',
            'label' => 'LBL_TRANSMISSION_TYPE',
          ),
          1 => 
          array (
            'name' => 'drive_type',
            'label' => 'LBL_DRIVE_TYPE',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'fuel_system',
            'label' => 'LBL_FUEL_SYSTEM',
          ),
          1 => 
          array (
            'name' => 'inventory_status',
            'label' => 'LBL_INVENTORY_STATUS',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'lot_position',
            'label' => 'LBL_LOT_POSITION',
          ),
          1 => 
          array (
            'name' => 'acquisition_date',
            'label' => 'LBL_ACQUISITION_DATE',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'cost_basis',
            'label' => 'LBL_COST_BASIS',
          ),
          1 => 
          array (
            'name' => 'asking_price',
            'label' => 'LBL_ASKING_PRICE',
          ),
        ),
        11 => 
        array (
          0 => 
          array (
            'name' => 'market_valuation',
            'label' => 'LBL_MARKET_VALUATION',
          ),
          1 => 
          array (
            'name' => 'acquisition_source',
            'label' => 'LBL_ACQUISITION_SOURCE',
          ),
        ),
        12 => 
        array (
          0 => 
          array (
            'name' => 'days_in_inventory',
            'label' => 'LBL_DAYS_IN_INVENTORY',
          ),
          1 => 
          array (
            'name' => 'final_price',
            'label' => 'LBL_FINAL_PRICE',
          ),
        ),
        13 => 
        array (
          0 => 
          array (
            'name' => 'vehicle_features',
            'label' => 'LBL_VEHICLE_FEATURES',
            'customCode' => '{include file="modules/AutoInventory/tpls/vehicle_features_detail.tpl"}',
          ),
          1 => 
          array (
            'name' => 'photo_gallery',
            'label' => 'LBL_PHOTO_GALLERY',
            'customCode' => '{include file="modules/AutoInventory/tpls/photo_gallery_detail.tpl"}',
          ),
        ),
        14 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
          1 => '',
        ),
        15 => 
        array (
          0 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
            'label' => 'LBL_DATE_ENTERED',
          ),
          1 => 
          array (
            'name' => 'date_modified',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),
      ),
    ),
  ),
);
?> 