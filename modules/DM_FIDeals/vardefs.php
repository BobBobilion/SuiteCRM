<?php
/**
 * SuiteCRM F&I Deal Center - Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_FIDeals module in the car dealership CRM system.
 * 
 * Includes all financial fields, relationships with other modules,
 * and comprehensive metadata for the F&I Deal Center functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_FIDeals'] = array(
    'table' => 'dm_fideals',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'F&I Deal Center for managing automotive financing, insurance, and warranty sales with comprehensive calculations and profitability tracking',
    'fields' => array(
        
        // Standard SugarBean fields - inherited from Basic template
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'char',
            'len' => '36',
            'required' => true,
            'reportable' => false,
            'comment' => 'Unique identifier'
        ),
        
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Deal display name (Deal Number - Customer)',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'massupdate' => true,
            'audited' => true,
        ),
        
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'group' => 'created_by_name',
            'comment' => 'Date record created',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'group' => 'modified_by_name',
            'comment' => 'Date record last modified',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'modified_user_id' => array(
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'group' => 'modified_by_name',
            'dbType' => 'char',
            'len' => '36',
            'reportable' => true,
            'comment' => 'User who last modified record',
        ),
        
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED_NAME',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'user_name',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'link' => 'modified_user_link',
            'duplicate_merge' => 'disabled',
        ),
        
        'created_by' => array(
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'created_by',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'comment' => 'User who created record',
        ),
        
        'created_by_name' => array(
            'name' => 'created_by_name',
            'vname' => 'LBL_CREATED',
            'type' => 'relate',
            'reportable' => false,
            'link' => 'created_by_link',
            'rname' => 'user_name',
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'created_by',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'importable' => 'false',
        ),
        
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ),
        
        // Core F&I Deal Fields
        'deal_number' => array(
            'name' => 'deal_number',
            'vname' => 'LBL_DEAL_NUMBER',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Unique F&I deal number (FI-YYYYMMDD-####)',
            'required' => true,
            'audited' => true,
            'unified_search' => true,
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'massupdate' => false,
            'importable' => 'required',
        ),
        
        // Relationship Links
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'type' => 'char',
            'len' => '36',
            'vname' => 'LBL_OPPORTUNITY_ID',
            'comment' => 'Link to sales opportunity',
            'audited' => true,
        ),
        
        'opportunity_name' => array(
            'name' => 'opportunity_name',
            'rname' => 'name',
            'id_name' => 'opportunity_id',
            'vname' => 'LBL_OPPORTUNITY_NAME',
            'type' => 'relate',
            'table' => 'opportunities',
            'module' => 'Opportunities',
            'source' => 'non-db',
            'len' => '255',
            'link' => 'opportunity_link',
            'comment' => 'Related sales opportunity',
        ),
        
        'customer_id' => array(
            'name' => 'customer_id',
            'type' => 'char',
            'len' => '36',
            'vname' => 'LBL_CUSTOMER_ID',
            'comment' => 'Link to customer account',
            'required' => true,
            'audited' => true,
        ),
        
        'customer_name' => array(
            'name' => 'customer_name',
            'rname' => 'name',
            'id_name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_NAME',
            'type' => 'relate',
            'table' => 'accounts',
            'module' => 'Accounts',
            'source' => 'non-db',
            'len' => '255',
            'link' => 'customer_link',
            'comment' => 'Customer account name',
            'required' => true,
        ),
        
        'vehicle_id' => array(
            'name' => 'vehicle_id',
            'type' => 'char',
            'len' => '36',
            'vname' => 'LBL_VEHICLE_ID',
            'comment' => 'Link to vehicle inventory',
            'required' => true,
            'audited' => true,
        ),
        
        'vehicle_name' => array(
            'name' => 'vehicle_name',
            'rname' => 'name',
            'id_name' => 'vehicle_id',
            'vname' => 'LBL_VEHICLE_NAME',
            'type' => 'relate',
            'table' => 'dm_vehiclesinventory',
            'module' => 'DM_VehiclesInventory',
            'source' => 'non-db',
            'len' => '255',
            'link' => 'vehicle_link',
            'comment' => 'Vehicle description',
            'required' => true,
        ),
        
        'tradein_id' => array(
            'name' => 'tradein_id',
            'type' => 'char',
            'len' => '36',
            'vname' => 'LBL_TRADEIN_ID',
            'comment' => 'Link to trade-in vehicle (if applicable)',
            'audited' => true,
        ),
        
        // Vehicle Pricing Structure
        'sales_price' => array(
            'name' => 'sales_price',
            'vname' => 'LBL_SALES_PRICE',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Vehicle selling price',
            'required' => true,
            'audited' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'massupdate' => true,
        ),
        
        'down_payment' => array(
            'name' => 'down_payment',
            'vname' => 'LBL_DOWN_PAYMENT',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Cash down payment amount',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'trade_allowance' => array(
            'name' => 'trade_allowance',
            'vname' => 'LBL_TRADE_ALLOWANCE',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Trade-in vehicle credit amount',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'trade_payoff' => array(
            'name' => 'trade_payoff',
            'vname' => 'LBL_TRADE_PAYOFF',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Amount owed on trade-in vehicle',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'rebates' => array(
            'name' => 'rebates',
            'vname' => 'LBL_REBATES',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Manufacturer rebates and incentives',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'dealer_fees' => array(
            'name' => 'dealer_fees',
            'vname' => 'LBL_DEALER_FEES',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Documentation and dealer processing fees',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'government_fees' => array(
            'name' => 'government_fees',
            'vname' => 'LBL_GOVERNMENT_FEES',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Tax, title, license, and registration fees',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        // Finance Structure
        'finance_method' => array(
            'name' => 'finance_method',
            'vname' => 'LBL_FINANCE_METHOD',
            'type' => 'enum',
            'options' => 'fi_finance_method_list',
            'len' => '50',
            'comment' => 'Payment method: Cash, Finance, or Lease',
            'default' => 'Finance',
            'required' => true,
            'audited' => true,
            'massupdate' => true,
            'importable' => 'required',
        ),
        
        'amount_financed' => array(
            'name' => 'amount_financed',
            'vname' => 'LBL_AMOUNT_FINANCED',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Total amount to be financed (calculated)',
            'calculated' => true,
            'audited' => true,
            'massupdate' => false,
        ),
        
        'term_months' => array(
            'name' => 'term_months',
            'vname' => 'LBL_TERM_MONTHS',
            'type' => 'int',
            'len' => '3',
            'comment' => 'Loan term in months',
            'default' => '60',
            'audited' => true,
            'massupdate' => true,
            'validation' => array('type' => 'range', 'min' => 12, 'max' => 96),
        ),
        
        'interest_rate' => array(
            'name' => 'interest_rate',
            'vname' => 'LBL_INTEREST_RATE',
            'type' => 'decimal',
            'len' => '5,2',
            'comment' => 'Annual percentage rate (APR) - for display only',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'monthly_payment' => array(
            'name' => 'monthly_payment',
            'vname' => 'LBL_MONTHLY_PAYMENT',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Calculated monthly payment amount',
            'calculated' => true,
            'audited' => true,
            'massupdate' => false,
        ),
        
        'total_of_payments' => array(
            'name' => 'total_of_payments',
            'vname' => 'LBL_TOTAL_PAYMENTS',
            'type' => 'currency',
            'len' => '12,2',
            'comment' => 'Total amount of all payments over loan term',
            'calculated' => true,
            'audited' => true,
            'massupdate' => false,
        ),
        
        'finance_charge' => array(
            'name' => 'finance_charge',
            'vname' => 'LBL_FINANCE_CHARGE',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Total interest paid over loan term',
            'calculated' => true,
            'audited' => true,
            'massupdate' => false,
        ),
        
        // Lender Information
        'lender_id' => array(
            'name' => 'lender_id',
            'type' => 'char',
            'len' => '36',
            'vname' => 'LBL_LENDER_ID',
            'comment' => 'Selected financing lender',
            'audited' => true,
        ),
        
        'lender_name' => array(
            'name' => 'lender_name',
            'rname' => 'name',
            'id_name' => 'lender_id',
            'vname' => 'LBL_LENDER_NAME',
            'type' => 'relate',
            'table' => 'accounts',
            'module' => 'Accounts',
            'source' => 'non-db',
            'len' => '255',
            'link' => 'lender_link',
            'comment' => 'Financing lender name',
        ),
        
        'lender_approval_number' => array(
            'name' => 'lender_approval_number',
            'vname' => 'LBL_APPROVAL_NUMBER',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Lender approval reference number',
            'audited' => true,
            'massupdate' => false,
        ),
        
        'buy_rate' => array(
            'name' => 'buy_rate',
            'vname' => 'LBL_BUY_RATE',
            'type' => 'decimal',
            'len' => '5,2',
            'comment' => 'Lender rate (what dealer pays)',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'sell_rate' => array(
            'name' => 'sell_rate',
            'vname' => 'LBL_SELL_RATE',
            'type' => 'decimal',
            'len' => '5,2',
            'comment' => 'Customer rate (what customer pays)',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'rate_markup' => array(
            'name' => 'rate_markup',
            'vname' => 'LBL_RATE_MARKUP',
            'type' => 'decimal',
            'len' => '5,2',
            'comment' => 'Dealer rate markup percentage',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'finance_reserve' => array(
            'name' => 'finance_reserve',
            'vname' => 'LBL_FINANCE_RESERVE',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Dealer profit from financing (calculated)',
            'calculated' => true,
            'audited' => true,
            'massupdate' => false,
        ),
        
        // F&I Product Sales
        'warranty_total' => array(
            'name' => 'warranty_total',
            'vname' => 'LBL_WARRANTY_TOTAL',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Extended warranty product amount',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'gap_amount' => array(
            'name' => 'gap_amount',
            'vname' => 'LBL_GAP_AMOUNT',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'GAP insurance product amount',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'etch_amount' => array(
            'name' => 'etch_amount',
            'vname' => 'LBL_ETCH_AMOUNT',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Vehicle theft protection/etching amount',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'maintenance_amount' => array(
            'name' => 'maintenance_amount',
            'vname' => 'LBL_MAINTENANCE_AMOUNT',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Prepaid maintenance plan amount',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'other_products_total' => array(
            'name' => 'other_products_total',
            'vname' => 'LBL_OTHER_PRODUCTS',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Other F&I products total amount',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        // Profitability Tracking
        'backend_gross' => array(
            'name' => 'backend_gross',
            'vname' => 'LBL_BACKEND_GROSS',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'F&I department gross profit (calculated)',
            'calculated' => true,
            'audited' => true,
            'massupdate' => false,
        ),
        
        'frontend_gross' => array(
            'name' => 'frontend_gross',
            'vname' => 'LBL_FRONTEND_GROSS',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Vehicle department gross profit',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'total_gross' => array(
            'name' => 'total_gross',
            'vname' => 'LBL_TOTAL_GROSS',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Total deal gross profit (calculated)',
            'calculated' => true,
            'audited' => true,
            'massupdate' => false,
        ),
        
        // Deal Workflow and Status
        'deal_status' => array(
            'name' => 'deal_status',
            'vname' => 'LBL_DEAL_STATUS',
            'type' => 'enum',
            'options' => 'fi_deal_status_list',
            'len' => '50',
            'comment' => 'Current deal status in workflow',
            'default' => 'Draft',
            'required' => true,
            'audited' => true,
            'massupdate' => true,
            'importable' => 'required',
        ),
        
        'credit_app_id' => array(
            'name' => 'credit_app_id',
            'type' => 'char',
            'len' => '36',
            'vname' => 'LBL_CREDIT_APP_ID',
            'comment' => 'Link to credit application',
            'audited' => true,
        ),
        
        'stips_required' => array(
            'name' => 'stips_required',
            'vname' => 'LBL_STIPS_REQUIRED',
            'type' => 'text',
            'comment' => 'Lender stipulations required (JSON format)',
            'audited' => true,
        ),
        
        'funding_date' => array(
            'name' => 'funding_date',
            'vname' => 'LBL_FUNDING_DATE',
            'type' => 'date',
            'comment' => 'Date when deal was funded by lender',
            'audited' => true,
            'massupdate' => true,
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'contract_date' => array(
            'name' => 'contract_date',
            'vname' => 'LBL_CONTRACT_DATE',
            'type' => 'date',
            'comment' => 'Date when contracts were signed',
            'audited' => true,
            'massupdate' => true,
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        // Staff Assignments
        'fi_manager_id' => array(
            'name' => 'fi_manager_id',
            'rname' => 'user_name',
            'id_name' => 'fi_manager_id',
            'vname' => 'LBL_FI_MANAGER',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'comment' => 'F&I manager assigned to deal',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'fi_manager_name' => array(
            'name' => 'fi_manager_name',
            'rname' => 'user_name',
            'id_name' => 'fi_manager_id',
            'vname' => 'LBL_FI_MANAGER',
            'type' => 'relate',
            'table' => 'users',
            'module' => 'Users',
            'source' => 'non-db',
            'len' => '255',
            'link' => 'fi_manager_link',
            'comment' => 'F&I manager name',
        ),
        
        'salesperson_id' => array(
            'name' => 'salesperson_id',
            'rname' => 'user_name',
            'id_name' => 'salesperson_id',
            'vname' => 'LBL_SALESPERSON',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'comment' => 'Salesperson assigned to deal',
            'audited' => true,
            'massupdate' => true,
        ),
        
        'salesperson_name' => array(
            'name' => 'salesperson_name',
            'rname' => 'user_name',
            'id_name' => 'salesperson_id',
            'vname' => 'LBL_SALESPERSON',
            'type' => 'relate',
            'table' => 'users',
            'module' => 'Users',
            'source' => 'non-db',
            'len' => '255',
            'link' => 'salesperson_link',
            'comment' => 'Salesperson name',
        ),
        
        // Standard assigned user field
        'assigned_user_id' => array(
            'name' => 'assigned_user_id',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'comment' => 'User assigned to record',
            'audited' => true,
            'massupdate' => true,
            'required' => true,
        ),
        
        'assigned_user_name' => array(
            'name' => 'assigned_user_name',
            'link' => 'assigned_user_link',
            'vname' => 'LBL_ASSIGNED_TO',
            'rname' => 'user_name',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'assigned_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
        ),
        
        // F&I Product Management (Phase 4)
        'fi_product_details' => array(
            'name' => 'fi_product_details',
            'vname' => 'LBL_FI_PRODUCT_DETAILS',
            'type' => 'text',
            'comment' => 'JSON-encoded F&I product calculation details',
            'audited' => true,
            'massupdate' => false,
            'studio' => false,
        ),
        
        'fi_product_cost' => array(
            'name' => 'fi_product_cost',
            'vname' => 'LBL_FI_PRODUCT_COST',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Total cost of F&I products sold',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => false,
            'readonly' => true,
        ),
        
        'fi_product_profit' => array(
            'name' => 'fi_product_profit',
            'vname' => 'LBL_FI_PRODUCT_PROFIT',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Total profit from F&I products',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => false,
            'readonly' => true,
        ),
        
        'fi_product_commission' => array(
            'name' => 'fi_product_commission',
            'vname' => 'LBL_FI_PRODUCT_COMMISSION',
            'type' => 'currency',
            'len' => '10,2',
            'comment' => 'Total commission on F&I products',
            'default' => '0.00',
            'audited' => true,
            'massupdate' => false,
            'readonly' => true,
        ),
        
        'product_recommendations' => array(
            'name' => 'product_recommendations',
            'vname' => 'LBL_PRODUCT_RECOMMENDATIONS',
            'type' => 'text',
            'comment' => 'JSON-encoded product recommendations for this deal',
            'audited' => false,
            'massupdate' => false,
            'studio' => false,
        ),

        // Additional Information
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'text',
            'comment' => 'F&I deal notes and comments',
            'audited' => true,
            'massupdate' => false,
        ),
    ),
    
    // Define relationships
    'relationships' => array(
        'dm_fideals_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_customer' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'customer_id',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_vehicle' => array(
            'lhs_module' => 'DM_VehiclesInventory',
            'lhs_table' => 'dm_vehiclesinventory',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'vehicle_id',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_opportunity' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'opportunity_id',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_lender' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'lender_id',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_fi_manager' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'fi_manager_id',
            'relationship_type' => 'one-to-many',
        ),
        'dm_fideals_salesperson' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_FIDeals',
            'rhs_table' => 'dm_fideals',
            'rhs_key' => 'salesperson_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    
    // Define database indices for performance
    'indices' => array(
        array(
            'name' => 'idx_dm_fideals_deal_number',
            'type' => 'unique',
            'fields' => array('deal_number', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_customer',
            'type' => 'index',
            'fields' => array('customer_id', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_vehicle',
            'type' => 'index',
            'fields' => array('vehicle_id', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_status',
            'type' => 'index',
            'fields' => array('deal_status', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_assigned',
            'type' => 'index',
            'fields' => array('assigned_user_id', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_fi_manager',
            'type' => 'index',
            'fields' => array('fi_manager_id', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_date_entered',
            'type' => 'index',
            'fields' => array('date_entered', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_funding_date',
            'type' => 'index',
            'fields' => array('funding_date', 'deleted'),
        ),
        array(
            'name' => 'idx_dm_fideals_deleted',
            'type' => 'index',
            'fields' => array('deleted'),
        ),
    ),
    
    // Enable optimistic locking for concurrent access
    'optimistic_locking' => true,
);

// Use VardefManager to create the standardized vardefs
VardefManager::createVardef('DM_FIDeals', 'DM_FIDeals', array('basic', 'assignable')); 