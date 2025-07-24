<?php
/**
 * SuiteCRM F&I Deal Center - Detail View Definitions
 * 
 * This file defines the layout and panels for the F&I Deal Center detail view,
 * organized into logical sections for easy review and analysis.
 */

$viewdefs['DM_FIDeals']['DetailView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        'includes' => array(
            array(
                'file' => 'modules/DM_FIDeals/js/DM_FIDeals.js',
            ),
        ),
        'useTabs' => true,
        'tabDefs' => array(
            'LBL_PANEL_OVERVIEW' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_VEHICLE_PRICING' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_FINANCING' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_PRODUCTS' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_PROFITABILITY' => array(
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
                'deal_status',
            ),
            array(
                'deal_number',
                'finance_method',
            ),
            array(
                'customer_name',
                'vehicle_name',
            ),
            array(
                'opportunity_name',
                'tradein_id',
            ),
            array(
                'fi_manager_name',
                'salesperson_name',
            ),
            array(
                'assigned_user_name',
                '',
            ),
        ),
        'LBL_PANEL_VEHICLE_PRICING' => array(
            array(
                'sales_price',
                'down_payment',
            ),
            array(
                'trade_allowance',
                'trade_payoff',
            ),
            array(
                'rebates',
                'dealer_fees',
            ),
            array(
                'government_fees',
                '',
            ),
        ),
        'LBL_PANEL_FINANCING' => array(
            array(
                'amount_financed',
                'term_months',
            ),
            array(
                'monthly_payment',
                'total_of_payments',
            ),
            array(
                'finance_charge',
                'interest_rate',
            ),
            array(
                'lender_name',
                'lender_approval_number',
            ),
            array(
                'buy_rate',
                'sell_rate',
            ),
            array(
                'rate_markup',
                'finance_reserve',
            ),
        ),
        'LBL_PANEL_PRODUCTS' => array(
            array(
                'warranty_total',
                'gap_amount',
            ),
            array(
                'etch_amount',
                'maintenance_amount',
            ),
            array(
                'other_products_total',
                '',
            ),
        ),
        'LBL_PANEL_PROFITABILITY' => array(
            array(
                'frontend_gross',
                'backend_gross',
            ),
            array(
                'total_gross',
                '',
            ),
        ),
        'LBL_PANEL_WORKFLOW' => array(
            array(
                'credit_app_id',
                'funding_date',
            ),
            array(
                'contract_date',
                '',
            ),
            array(
                array(
                    'name' => 'stips_required',
                    'displayParams' => array(
                        'nl2br' => true,
                    ),
                ),
                '',
            ),
            array(
                array(
                    'name' => 'notes',
                    'displayParams' => array(
                        'nl2br' => true,
                    ),
                ),
                '',
            ),
            array(
                'date_entered',
                'date_modified',
            ),
            array(
                'created_by_name',
                'modified_by_name',
            ),
        ),
    ),
); 