<?php
/**
 * SuiteCRM Trade-In Manager - Detail View Definitions
 * 
 * This file defines the layout and panels for the Trade-In Manager detail view,
 * organized for efficient information display and workflow management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_TradeIns']['DetailView'] = array(
    'templateMeta' => array(
        'form' => array(
            'buttons' => array(
                'EDIT',
                'DUPLICATE',
                'DELETE',
                array(
                    'customCode' => '<input type="submit" class="button" title="{$APP.LBL_GET_MARKET_VALUES}" onclick="this.form.action.value=\'get_market_values\'; this.form.return_module.value=\'{$smarty.request.return_module}\'; this.form.return_action.value=\'{$smarty.request.return_action}\'; this.form.return_id.value=\'{$smarty.request.return_id}\';" name="get_values" value="{$APP.LBL_GET_MARKET_VALUES}">',
                ),
                array(
                    'customCode' => '<input type="submit" class="button" title="{$APP.LBL_SCHEDULE_APPRAISAL}" onclick="this.form.action.value=\'schedule_appraisal\'; this.form.return_module.value=\'{$smarty.request.return_module}\'; this.form.return_action.value=\'{$smarty.request.return_action}\'; this.form.return_id.value=\'{$smarty.request.return_id}\';" name="schedule_appraisal" value="{$APP.LBL_SCHEDULE_APPRAISAL}">',
                ),
            ),
        ),
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
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
    ),
    'panels' => array(
        'LBL_PANEL_OVERVIEW' => array(
            array(
                'name',
                'assigned_user_name',
            ),
            array(
                'customer_name',
                'opportunity_name',
            ),
            array(
                'date_entered',
                'date_modified',
            ),
        ),
        
        'LBL_PANEL_VEHICLE_INFO' => array(
            array(
                'vin',
                '',
            ),
            array(
                'year',
                'make',
            ),
            array(
                'model',
                'trim',
            ),
            array(
                'mileage',
                'exterior_color',
            ),
            array(
                'condition_overall',
                '',
            ),
            array(
                array(
                    'name' => 'condition_notes',
                    'label' => 'LBL_CONDITION_NOTES',
                    'customCode' => '{$fields.condition_notes.value}',
                ),
            ),
        ),
        
        'LBL_PANEL_VALUATION' => array(
            array(
                'customer_asking',
                'valuation_date',
            ),
            array(
                'market_value_retail',
                'market_value_trade',
            ),
            array(
                'market_value_private',
                'valuation_source',
            ),
        ),
        
        'LBL_PANEL_APPRAISAL' => array(
            array(
                'appraised_value',
                'appraisal_date',
            ),
            array(
                'appraisal_scheduled_date',
                'photos_taken',
            ),
            array(
                array(
                    'name' => 'appraisal_notes',
                    'label' => 'LBL_APPRAISAL_NOTES',
                    'customCode' => '{$fields.appraisal_notes.value}',
                ),
            ),
        ),
        
        'LBL_PANEL_PAYOFF' => array(
            array(
                'payoff_amount',
                'payoff_bank',
            ),
            array(
                'payoff_date',
                'payoff_verified',
            ),
        ),
        
        'LBL_PANEL_WORKFLOW' => array(
            array(
                'status',
                'used_in_deal',
            ),
            array(
                'deal_id',
                'disposal_method',
            ),
            array(
                'trade_allowance',
                'actual_cash_value',
            ),
            array(
                'reconditioning_cost',
                'estimated_profit',
            ),
        ),
    ),
);
?> 