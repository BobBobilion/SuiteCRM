<?php
/**
 * SuiteCRM F&I Deal Center - Edit View Definitions
 * 
 * This file defines the layout and panels for the F&I Deal Center edit view,
 * organized for efficient data entry and real-time calculations.
 */

$viewdefs['DM_FIDeals']['EditView'] = array(
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
            'LBL_PANEL_WORKFLOW' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
        ),
        'form' => array(
            'headerTpl' => 'modules/DM_FIDeals/tpls/EditViewHeader.tpl',
            'footerTpl' => 'modules/DM_FIDeals/tpls/EditViewFooter.tpl',
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
                array(
                    'name' => 'customer_name',
                    'displayParams' => array(
                        'required' => true,
                        'initial_filter' => '&account_type=Customer',
                    ),
                ),
                array(
                    'name' => 'vehicle_name',
                    'displayParams' => array(
                        'required' => true,
                        'initial_filter' => '&status=Available',
                    ),
                ),
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
                array(
                    'name' => 'sales_price',
                    'displayParams' => array(
                        'required' => true,
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
                array(
                    'name' => 'down_payment',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'trade_allowance',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
                array(
                    'name' => 'trade_payoff',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'rebates',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
                array(
                    'name' => 'dealer_fees',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'government_fees',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
                '',
            ),
        ),
        'LBL_PANEL_FINANCING' => array(
            array(
                array(
                    'name' => 'amount_financed',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
                array(
                    'name' => 'term_months',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculatePayment();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'monthly_payment',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
                array(
                    'name' => 'total_of_payments',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'finance_charge',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
                'interest_rate',
            ),
            array(
                array(
                    'name' => 'lender_name',
                    'displayParams' => array(
                        'initial_filter' => '&account_type=Lender',
                    ),
                ),
                'lender_approval_number',
            ),
            array(
                array(
                    'name' => 'buy_rate',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.calculateRateMarkup();',
                    ),
                ),
                array(
                    'name' => 'sell_rate',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.calculateRateMarkup(); SUGAR.DM_FIDeals.recalculatePayment();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'rate_markup',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
                array(
                    'name' => 'finance_reserve',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
            ),
        ),
        'LBL_PANEL_PRODUCTS' => array(
            array(
                array(
                    'name' => 'warranty_total',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
                array(
                    'name' => 'gap_amount',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'etch_amount',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
                array(
                    'name' => 'maintenance_amount',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'other_products_total',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateAll();',
                    ),
                ),
                '',
            ),
            array(
                array(
                    'name' => 'frontend_gross',
                    'displayParams' => array(
                        'onchange' => 'SUGAR.DM_FIDeals.recalculateProfitability();',
                    ),
                ),
                array(
                    'name' => 'backend_gross',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'total_gross',
                    'displayParams' => array(
                        'readonly' => true,
                        'class' => 'calculated-field',
                    ),
                ),
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
                        'rows' => 4,
                        'cols' => 60,
                    ),
                ),
                '',
            ),
            array(
                array(
                    'name' => 'notes',
                    'displayParams' => array(
                        'rows' => 4,
                        'cols' => 60,
                    ),
                ),
                '',
            ),
        ),
    ),
); 