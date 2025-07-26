<?php
/**
 * SuiteCRM Trade-In Manager - Edit View Definitions
 * 
 * This file defines the layout and panels for the Trade-In Manager edit view,
 * organized for efficient data entry and real-time calculations.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_TradeIns']['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        'includes' => array(
            array(
                'file' => 'modules/DM_TradeIns/js/DM_TradeIns.js',
            ),
        ),
        'useTabs' => true,
        'tabDefs' => array(
            'LBL_PANEL_OVERVIEW' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_VEHICLE_INFO' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_VALUATION' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_APPRAISAL' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_PAYOFF' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_WORKFLOW' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
        ),
        'form' => array(
            'headerTpl' => 'modules/DM_TradeIns/tpls/EditViewHeader.tpl',
            'footerTpl' => 'modules/DM_TradeIns/tpls/EditViewFooter.tpl',
        ),
    ),
    'panels' => array(
        'LBL_PANEL_OVERVIEW' => array(
            array(
                array(
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'displayParams' => array(
                        'required' => false,
                    ),
                ),
                array(
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'displayParams' => array(
                        'required' => false,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'customer_name',
                    'label' => 'LBL_CUSTOMER_NAME',
                    'displayParams' => array(
                        'required' => true,
                    ),
                ),
                array(
                    'name' => 'opportunity_name',
                    'label' => 'LBL_OPPORTUNITY_NAME',
                    'displayParams' => array(
                        'required' => false,
                    ),
                ),
            ),
        ),
        
        'LBL_PANEL_VEHICLE_INFO' => array(
            array(
                array(
                    'name' => 'vin',
                    'label' => 'LBL_VIN',
                    'displayParams' => array(
                        'size' => 17,
                        'maxlength' => 17,
                        'onBlur' => 'decodeVIN(this);',
                    ),
                ),
                array(
                    'customCode' => '<input type="button" class="button" value="{$APP.LBL_GET_MARKET_VALUES}" onclick="getMarketValues();" id="get_values_btn">',
                    'label' => '',
                ),
            ),
            array(
                array(
                    'name' => 'year',
                    'label' => 'LBL_YEAR',
                    'displayParams' => array(
                        'size' => 4,
                        'maxlength' => 4,
                    ),
                ),
                array(
                    'name' => 'make',
                    'label' => 'LBL_MAKE',
                    'displayParams' => array(
                        'size' => 30,
                        'maxlength' => 100,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'model',
                    'label' => 'LBL_MODEL',
                    'displayParams' => array(
                        'size' => 30,
                        'maxlength' => 100,
                    ),
                ),
                array(
                    'name' => 'trim',
                    'label' => 'LBL_TRIM',
                    'displayParams' => array(
                        'size' => 30,
                        'maxlength' => 100,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'mileage',
                    'label' => 'LBL_MILEAGE',
                    'displayParams' => array(
                        'size' => 10,
                        'onChange' => 'calculateMarketValues();',
                    ),
                ),
                array(
                    'name' => 'exterior_color',
                    'label' => 'LBL_EXTERIOR_COLOR',
                    'displayParams' => array(
                        'size' => 20,
                        'maxlength' => 50,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'condition_overall',
                    'label' => 'LBL_CONDITION_OVERALL',
                    'displayParams' => array(
                        'onChange' => 'calculateMarketValues();',
                    ),
                ),
                '',
            ),
            array(
                array(
                    'name' => 'condition_notes',
                    'label' => 'LBL_CONDITION_NOTES',
                    'displayParams' => array(
                        'rows' => 3,
                        'cols' => 50,
                    ),
                ),
            ),
        ),
        
        'LBL_PANEL_VALUATION' => array(
            array(
                array(
                    'name' => 'customer_asking',
                    'label' => 'LBL_CUSTOMER_ASKING',
                    'displayParams' => array(
                        'size' => 15,
                        'onChange' => 'calculateVariances();',
                    ),
                ),
                array(
                    'name' => 'valuation_date',
                    'label' => 'LBL_VALUATION_DATE',
                    'displayParams' => array(
                        'showFormats' => true,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'market_value_retail',
                    'label' => 'LBL_MARKET_VALUE_RETAIL',
                    'displayParams' => array(
                        'size' => 15,
                        'readonly' => true,
                        'class' => 'readonly',
                    ),
                ),
                array(
                    'name' => 'market_value_trade',
                    'label' => 'LBL_MARKET_VALUE_TRADE',
                    'displayParams' => array(
                        'size' => 15,
                        'readonly' => true,
                        'class' => 'readonly',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'market_value_private',
                    'label' => 'LBL_MARKET_VALUE_PRIVATE',
                    'displayParams' => array(
                        'size' => 15,
                        'readonly' => true,
                        'class' => 'readonly',
                    ),
                ),
                array(
                    'name' => 'valuation_source',
                    'label' => 'LBL_VALUATION_SOURCE',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'readonly',
                    ),
                ),
            ),
        ),
        
        'LBL_PANEL_APPRAISAL' => array(
            array(
                array(
                    'name' => 'appraised_value',
                    'label' => 'LBL_APPRAISED_VALUE',
                    'displayParams' => array(
                        'size' => 15,
                        'onChange' => 'calculateProfitability();',
                    ),
                ),
                array(
                    'name' => 'appraisal_date',
                    'label' => 'LBL_APPRAISAL_DATE',
                    'displayParams' => array(
                        'showFormats' => true,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'appraisal_scheduled_date',
                    'label' => 'LBL_APPRAISAL_SCHEDULED_DATE',
                    'displayParams' => array(
                        'showFormats' => true,
                    ),
                ),
                array(
                    'name' => 'photos_taken',
                    'label' => 'LBL_PHOTOS_TAKEN',
                ),
            ),
            array(
                array(
                    'name' => 'appraisal_notes',
                    'label' => 'LBL_APPRAISAL_NOTES',
                    'displayParams' => array(
                        'rows' => 3,
                        'cols' => 50,
                    ),
                ),
            ),
        ),
        
        'LBL_PANEL_PAYOFF' => array(
            array(
                array(
                    'name' => 'payoff_amount',
                    'label' => 'LBL_PAYOFF_AMOUNT',
                    'displayParams' => array(
                        'size' => 15,
                        'onChange' => 'calculateTradeEquity();',
                    ),
                ),
                array(
                    'name' => 'payoff_bank',
                    'label' => 'LBL_PAYOFF_BANK',
                    'displayParams' => array(
                        'size' => 30,
                        'maxlength' => 255,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'payoff_date',
                    'label' => 'LBL_PAYOFF_DATE',
                    'displayParams' => array(
                        'showFormats' => true,
                    ),
                ),
                array(
                    'name' => 'payoff_verified',
                    'label' => 'LBL_PAYOFF_VERIFIED',
                ),
            ),
        ),
        
        'LBL_PANEL_WORKFLOW' => array(
            array(
                array(
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'displayParams' => array(
                        'onChange' => 'updateWorkflowFields();',
                    ),
                ),
                array(
                    'name' => 'used_in_deal',
                    'label' => 'LBL_USED_IN_DEAL',
                ),
            ),
            array(
                array(
                    'name' => 'deal_id',
                    'label' => 'LBL_DEAL_ID',
                    'displayParams' => array(
                        'size' => 36,
                        'maxlength' => 36,
                    ),
                ),
                array(
                    'name' => 'disposal_method',
                    'label' => 'LBL_DISPOSAL_METHOD',
                ),
            ),
            array(
                array(
                    'name' => 'trade_allowance',
                    'label' => 'LBL_TRADE_ALLOWANCE',
                    'displayParams' => array(
                        'size' => 15,
                        'onChange' => 'calculateProfitability();',
                    ),
                ),
                array(
                    'name' => 'actual_cash_value',
                    'label' => 'LBL_ACTUAL_CASH_VALUE',
                    'displayParams' => array(
                        'size' => 15,
                        'onChange' => 'calculateProfitability();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'reconditioning_cost',
                    'label' => 'LBL_RECONDITIONING_COST',
                    'displayParams' => array(
                        'size' => 15,
                        'onChange' => 'calculateProfitability();',
                    ),
                ),
                array(
                    'name' => 'estimated_profit',
                    'label' => 'LBL_ESTIMATED_PROFIT',
                    'displayParams' => array(
                        'size' => 15,
                        'readonly' => true,
                        'class' => 'readonly',
                    ),
                ),
            ),
        ),
    ),
);
?> 