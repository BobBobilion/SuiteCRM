<?php
/**
 * SuiteCRM Vehicle Inventory System - Edit View Definitions
 * 
 * This file defines the layout and fields for the vehicle inventory edit form.
 * Organized into logical sections for efficient vehicle data entry and management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_VehiclesInventory'] = array(
    'EditView' => array(
        'templateMeta' => array(
            'form' => array(
                'buttons' => array(
                    0 => 'SAVE',
                    1 => 'CANCEL',
                    2 => array(
                        'customCode' => '<input type="button" class="button" onclick="SUGAR.ajaxUI.loadContent(\'index.php?module=DM_VehiclesInventory&action=DetailView&record={$fields.id.value}\');" name="view_change_button" id="view_change_button" title="{$APP.LBL_VIEW_BUTTON_TITLE}" accessKey="{$APP.LBL_VIEW_BUTTON_KEY}" value="{$APP.LBL_VIEW_BUTTON_LABEL}"/>',
                        'sugar_html' => array(
                            'type' => 'button',
                            'value' => '{$APP.LBL_VIEW_BUTTON_LABEL}',
                            'htmlOptions' => array(
                                'title' => '{$APP.LBL_VIEW_BUTTON_TITLE}',
                                'accessKey' => '{$APP.LBL_VIEW_BUTTON_KEY}',
                                'class' => 'button',
                                'onclick' => 'SUGAR.ajaxUI.loadContent(\'index.php?module=DM_VehiclesInventory&action=DetailView&record={$fields.id.value}\');',
                                'name' => 'view_change_button',
                                'id' => 'view_change_button',
                            ),
                        ),
                    ),
                ),
                'headerTpl' => 'modules/DM_VehiclesInventory/tpls/EditViewHeader.tpl',
            ),
            'maxColumns' => '2',
            'widths' => array(
                0 => array(
                    'label' => '10',
                    'field' => '30',
                ),
                1 => array(
                    'label' => '10',
                    'field' => '30',
                ),
            ),
            'includes' => array(
                0 => array(
                    'file' => 'modules/DM_VehiclesInventory/js/DM_VehiclesInventory.js',
                ),
            ),
            'useTabs' => true,
            'tabDefs' => array(
                'LBL_VEHICLE_INFORMATION' => array(
                    'newTab' => true,
                    'panelDefault' => 'expanded',
                ),
                'LBL_SPECIFICATIONS' => array(
                    'newTab' => true,
                    'panelDefault' => 'expanded',
                ),
                'LBL_PRICING_INFORMATION' => array(
                    'newTab' => true,
                    'panelDefault' => 'expanded',
                ),
                'LBL_INVENTORY_DATA' => array(
                    'newTab' => true,
                    'panelDefault' => 'expanded',
                ),
                'LBL_ADDITIONAL_INFORMATION' => array(
                    'newTab' => true,
                    'panelDefault' => 'collapsed',
                ),
            ),
        ),
        'panels' => array(
            // Panel 1: Vehicle Information (Basic identification and naming)
            'lbl_vehicle_information' => array(
                0 => array(
                    0 => array(
                        'name' => 'name',
                        'label' => 'LBL_NAME',
                        'displayParams' => array(
                            'required' => true,
                        ),
                    ),
                    1 => array(
                        'name' => 'assigned_user_name',
                        'label' => 'LBL_ASSIGNED_TO_NAME',
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'vin',
                        'label' => 'LBL_VIN',
                        'displayParams' => array(
                            'required' => false,
                            'maxlength' => 17,
                        ),
                    ),
                    1 => array(
                        'name' => 'stock_number',
                        'label' => 'LBL_STOCK_NUMBER',
                        'displayParams' => array(
                            'required' => false,
                        ),
                    ),
                ),
                2 => array(
                    0 => array(
                        'name' => 'year',
                        'label' => 'LBL_YEAR',
                        'displayParams' => array(
                            'required' => false,
                        ),
                    ),
                    1 => array(
                        'name' => 'make',
                        'label' => 'LBL_MAKE',
                        'displayParams' => array(
                            'required' => false,
                        ),
                    ),
                ),
                3 => array(
                    0 => array(
                        'name' => 'model',
                        'label' => 'LBL_MODEL',
                        'displayParams' => array(
                            'required' => false,
                        ),
                    ),
                    1 => array(
                        'name' => 'trim',
                        'label' => 'LBL_TRIM',
                    ),
                ),
            ),
            
            // Panel 2: Vehicle Specifications
            'lbl_specifications' => array(
                0 => array(
                    0 => array(
                        'name' => 'body_style',
                        'label' => 'LBL_BODY_STYLE',
                    ),
                    1 => array(
                        'name' => 'mileage',
                        'label' => 'LBL_MILEAGE',
                        'displayParams' => array(
                            'size' => 10,
                        ),
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'exterior_color',
                        'label' => 'LBL_EXTERIOR_COLOR',
                    ),
                    1 => array(
                        'name' => 'interior_color',
                        'label' => 'LBL_INTERIOR_COLOR',
                    ),
                ),
                2 => array(
                    0 => array(
                        'name' => 'engine_type',
                        'label' => 'LBL_ENGINE_TYPE',
                    ),
                    1 => array(
                        'name' => 'transmission',
                        'label' => 'LBL_TRANSMISSION',
                    ),
                ),
                3 => array(
                    0 => array(
                        'name' => 'drivetrain',
                        'label' => 'LBL_DRIVETRAIN',
                    ),
                    1 => array(
                        'name' => 'fuel_type',
                        'label' => 'LBL_FUEL_TYPE',
                    ),
                ),
            ),
            
            // Panel 3: Pricing Information
            'lbl_pricing_information' => array(
                0 => array(
                    0 => array(
                        'name' => 'purchase_price',
                        'label' => 'LBL_PURCHASE_PRICE',
                        'displayParams' => array(
                            'size' => 15,
                        ),
                    ),
                    1 => array(
                        'name' => 'list_price',
                        'label' => 'LBL_LIST_PRICE',
                        'displayParams' => array(
                            'size' => 15,
                        ),
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'market_value',
                        'label' => 'LBL_MARKET_VALUE',
                        'displayParams' => array(
                            'size' => 15,
                        ),
                    ),
                    1 => array(
                        'name' => 'sale_price',
                        'label' => 'LBL_SALE_PRICE',
                        'displayParams' => array(
                            'size' => 15,
                        ),
                    ),
                ),
                2 => array(
                    0 => array(
                        'name' => 'purchase_date',
                        'label' => 'LBL_PURCHASE_DATE',
                    ),
                    1 => array(
                        'name' => 'market_value_date',
                        'label' => 'LBL_MARKET_VALUE_DATE',
                    ),
                ),
            ),
            
            // Panel 4: Inventory Data
            'lbl_inventory_data' => array(
                0 => array(
                    0 => array(
                        'name' => 'status',
                        'label' => 'LBL_STATUS',
                        'displayParams' => array(
                            'required' => false,
                        ),
                    ),
                    1 => array(
                        'name' => 'condition_type',
                        'label' => 'LBL_CONDITION_TYPE',
                        'displayParams' => array(
                            'required' => false,
                        ),
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'location',
                        'label' => 'LBL_LOCATION',
                    ),
                    1 => array(
                        'name' => 'source',
                        'label' => 'LBL_SOURCE',
                    ),
                ),
                2 => array(
                    0 => array(
                        'name' => 'days_on_lot',
                        'label' => 'LBL_DAYS_ON_LOT',
                        'displayParams' => array(
                            'readonly' => true,
                            'size' => 10,
                        ),
                    ),
                    1 => '',
                ),
            ),
            
            // Panel 5: Additional Information
            'lbl_additional_information' => array(
                0 => array(
                    0 => array(
                        'name' => 'features',
                        'label' => 'LBL_FEATURES',
                        'displayParams' => array(
                            'rows' => 4,
                            'cols' => 50,
                        ),
                    ),
                    1 => array(
                        'name' => 'photos',
                        'label' => 'LBL_PHOTOS',
                        'displayParams' => array(
                            'rows' => 4,
                            'cols' => 50,
                        ),
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'notes',
                        'label' => 'LBL_NOTES',
                        'displayParams' => array(
                            'rows' => 4,
                            'cols' => 50,
                        ),
                    ),
                    1 => array(
                        'name' => 'description',
                        'label' => 'LBL_DESCRIPTION',
                        'displayParams' => array(
                            'rows' => 4,
                            'cols' => 50,
                        ),
                    ),
                ),
                2 => array(
                    0 => array(
                        'name' => 'date_entered',
                        'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                        'label' => 'LBL_DATE_ENTERED',
                    ),
                    1 => array(
                        'name' => 'date_modified',
                        'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                        'label' => 'LBL_DATE_MODIFIED',
                    ),
                ),
            ),
        ),
    ),
); 