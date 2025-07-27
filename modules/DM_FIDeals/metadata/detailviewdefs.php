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
        'form' => array(
            'buttons' => array(
                'EDIT',
                'DUPLICATE',
                'DELETE',
                array(
                    'customCode' => '<input type="button" class="button" value="Submit to Lender" onclick="window.open(\'index.php?module=DM_FIDeals&action=submission&record={$fields.id.value}\', \'_blank\', \'width=900,height=700,scrollbars=yes\');" />',
                ),
                array(
                    'customCode' => '<input type="button" class="button" value="Payment Calculator" onclick="SUGAR.DM_FIDeals.showPaymentScenarios();" />',
                ),
                array(
                    'customCode' => '<input type="button" class="button" value="Approval Tracking" onclick="window.open(\'index.php?module=DM_FIDeals&action=approval_tracking&record={$fields.id.value}\', \'_blank\', \'width=1200,height=800,scrollbars=yes\');" />',
                ),
                array(
                    'customCode' => '<input type="button" class="button" value="F&I Products" onclick="window.open(\'index.php?module=DM_FIDeals&action=product_selection&record={$fields.id.value}\', \'_blank\', \'width=1400,height=900,scrollbars=yes\');" />',
                ),
                array(
                    'customCode' => '<input type="button" class="button" value="F&I Reports" onclick="window.open(\'index.php?module=DM_FIDeals&action=reports\', \'_blank\', \'width=1600,height=1000,scrollbars=yes\');" />',
                ),
                array(
                    'customCode' => '<input type="button" class="button" value="Generate Documents" onclick="window.open(\'index.php?module=DM_DealDocuments&action=EditView&deal_id={$fields.id.value}&customer_id={$fields.customer_id.value}\', \'_blank\', \'width=1200,height=800,scrollbars=yes\');" />',
                ),
            )
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
            array(
                'fi_product_profit',
                'fi_product_commission',
            ),
            array(
                'fi_product_cost',
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