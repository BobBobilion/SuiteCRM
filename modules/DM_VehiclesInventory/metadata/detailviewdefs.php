<?php
/**
 * SuiteCRM Vehicle Inventory System - Detail View Definitions
 * 
 * This file defines the layout and fields for the vehicle inventory detail view.
 * Organized into logical tabs for comprehensive vehicle information display.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_VehiclesInventory'] = array(
    'DetailView' => array(
        'templateMeta' => array(
            'form' => array(
                'buttons' => array(
                    0 => 'EDIT',
                    1 => 'DUPLICATE',
                    2 => 'DELETE',
                    3 => 'FIND_DUPLICATES',
                    'MARK_SOLD' => array(
                        'customCode' => '<input type="button" class="button" onclick="markVehicleSold(\'{$fields.id.value}\');" value="{$MOD.LBL_MARK_SOLD}" {if $fields.status.value == "Sold"}disabled{/if}>',
                    ),
                    'UPDATE_PRICING' => array(
                        'customCode' => '<input type="button" class="button" onclick="updatePricing(\'{$fields.id.value}\');" value="{$MOD.LBL_UPDATE_PRICING}">',
                    ),
                    'DECODE_VIN' => array(
                        'customCode' => '<input type="button" class="button" onclick="decodeVIN(\'{$fields.vin.value}\');" value="{$MOD.LBL_DECODE_VIN}" {if empty($fields.vin.value)}disabled{/if}>',
                    ),
                    'PRINT_STICKER' => array(
                        'customCode' => '<input type="button" class="button" onclick="printWindowSticker(\'{$fields.id.value}\');" value="{$MOD.LBL_PRINT_WINDOW_STICKER}">',
                    ),
                ),
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
                'LBL_PHOTOS_FEATURES' => array(
                    'newTab' => true,
                    'panelDefault' => 'collapsed',
                ),
                'LBL_HISTORY_TRACKING' => array(
                    'newTab' => true,
                    'panelDefault' => 'collapsed',
                ),
            ),
        ),
        'panels' => array(
            // Panel 1: Vehicle Information
            'lbl_vehicle_information' => array(
                0 => array(
                    0 => array(
                        'name' => 'name',
                        'label' => 'LBL_NAME',
                        'displayParams' => array(
                            'enableConnectors' => true,
                            'module' => 'DM_VehiclesInventory',
                            'connectors' => array('ext_rest_googleplaces'),
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
                            'enableConnectors' => true,
                        ),
                    ),
                    1 => array(
                        'name' => 'stock_number',
                        'label' => 'LBL_STOCK_NUMBER',
                    ),
                ),
                2 => array(
                    0 => array(
                        'name' => 'year',
                        'label' => 'LBL_YEAR',
                    ),
                    1 => array(
                        'name' => 'make',
                        'label' => 'LBL_MAKE',
                    ),
                ),
                3 => array(
                    0 => array(
                        'name' => 'model',
                        'label' => 'LBL_MODEL',
                    ),
                    1 => array(
                        'name' => 'trim',
                        'label' => 'LBL_TRIM',
                    ),
                ),
                4 => array(
                    0 => array(
                        'name' => 'status',
                        'label' => 'LBL_STATUS',
                        'customCode' => '<span class="status-{$fields.status.value|lower}">{$fields.status.value}</span>',
                    ),
                    1 => array(
                        'name' => 'condition_type',
                        'label' => 'LBL_CONDITION_TYPE',
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
                        'customCode' => '{$fields.mileage.value|number_format} miles',
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
                        'customCode' => '<span class="currency">{$fields.purchase_price.value|string_format:"$%,.2f"}</span>',
                    ),
                    1 => array(
                        'name' => 'list_price',
                        'label' => 'LBL_LIST_PRICE',
                        'customCode' => '<span class="currency price-list">{$fields.list_price.value|string_format:"$%,.2f"}</span>',
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'market_value',
                        'label' => 'LBL_MARKET_VALUE',
                        'customCode' => '<span class="currency">{$fields.market_value.value|string_format:"$%,.2f"}</span>',
                    ),
                    1 => array(
                        'name' => 'sale_price',
                        'label' => 'LBL_SALE_PRICE',
                        'customCode' => '<span class="currency price-sale">{$fields.sale_price.value|string_format:"$%,.2f"}</span>',
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
                        'customCode' => '{$fields.market_value_date.value} {if !empty($fields.market_value_date.value)}<small>(Last Updated)</small>{/if}',
                    ),
                ),
                3 => array(
                    0 => array(
                        'name' => 'days_on_lot',
                        'label' => 'LBL_DAYS_ON_LOT',
                        'customCode' => '<span class="days-on-lot aging-{if $fields.days_on_lot.value <= 30}fresh{elseif $fields.days_on_lot.value <= 60}aging{else}stale{/if}">{$fields.days_on_lot.value} days</span>',
                    ),
                    1 => array(
                        'name' => 'calculated_profit',
                        'label' => 'LBL_CALCULATED_PROFIT',
                        'customCode' => '{if !empty($fields.sale_price.value) && !empty($fields.purchase_price.value)}<span class="profit">${math equation="x - y" x=$fields.sale_price.value y=$fields.purchase_price.value format="%,.2f"}</span>{else}N/A{/if}',
                    ),
                ),
            ),
            
            // Panel 4: Inventory Data
            'lbl_inventory_data' => array(
                0 => array(
                    0 => array(
                        'name' => 'location',
                        'label' => 'LBL_LOCATION',
                    ),
                    1 => array(
                        'name' => 'source',
                        'label' => 'LBL_SOURCE',
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'date_entered',
                        'label' => 'LBL_DATE_ENTERED',
                        'customCode' => '{$fields.date_entered.value} by {$fields.created_by_name.value}',
                    ),
                    1 => array(
                        'name' => 'date_modified',
                        'label' => 'LBL_DATE_MODIFIED',
                        'customCode' => '{$fields.date_modified.value} by {$fields.modified_by_name.value}',
                    ),
                ),
            ),
            
            // Panel 5: Photos & Features
            'lbl_photos_features' => array(
                0 => array(
                    0 => array(
                        'name' => 'photos',
                        'label' => 'LBL_PHOTOS',
                        'customCode' => '<div id="vehicle-photos">{include file="modules/DM_VehiclesInventory/tpls/PhotoGallery.tpl"}</div>',
                    ),
                    1 => '',
                ),
                1 => array(
                    0 => array(
                        'name' => 'features',
                        'label' => 'LBL_FEATURES',
                        'customCode' => '<div id="vehicle-features">{include file="modules/DM_VehiclesInventory/tpls/FeaturesList.tpl"}</div>',
                    ),
                    1 => '',
                ),
            ),
            
            // Panel 6: History & Tracking
            'lbl_history_tracking' => array(
                0 => array(
                    0 => array(
                        'name' => 'notes',
                        'label' => 'LBL_NOTES',
                    ),
                    1 => array(
                        'name' => 'description',
                        'label' => 'LBL_DESCRIPTION',
                    ),
                ),
                1 => array(
                    0 => array(
                        'name' => 'aging_category',
                        'label' => 'LBL_AGING_CATEGORY',
                        'customCode' => '{if $fields.days_on_lot.value <= 30}Fresh Inventory{elseif $fields.days_on_lot.value <= 60}Aging Inventory{else}Stale Inventory{/if}',
                    ),
                    1 => array(
                        'name' => 'profit_margin',
                        'label' => 'LBL_PROFIT_MARGIN',
                        'customCode' => '{if !empty($fields.sale_price.value) && !empty($fields.purchase_price.value)}<span class="profit-margin">{math equation="((x - y) / y) * 100" x=$fields.sale_price.value y=$fields.purchase_price.value format="%.1f"}%</span>{else}N/A{/if}',
                    ),
                ),
            ),
        ),
    ),
); 