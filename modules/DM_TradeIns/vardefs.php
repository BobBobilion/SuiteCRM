<?php
/**
 * SuiteCRM Trade-In Manager - Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_TradeIns module in the car dealership CRM system.
 * 
 * Includes all trade-in fields, relationships with other modules,
 * and comprehensive metadata for the Trade-In Manager functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_TradeIns'] = array(
    'table' => 'dm_tradeins',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Trade-In Manager for managing vehicle trade-in evaluations, market valuations, and appraisal workflow with customer and sales integration',
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
            'comment' => 'Trade-in display name (Year Make Model - Customer)',
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
            'id_name' => 'modified_user_id',
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
        
        'assigned_user_id' => array(
            'name' => 'assigned_user_id',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO_ID',
            'group' => 'assigned_user_name',
            'type' => 'relate',
            'table' => 'users',
            'module' => 'Users',
            'reportable' => true,
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'audited' => true,
            'comment' => 'User ID assigned to trade-in (sales person or appraiser)',
            'duplicate_merge' => 'disabled'
        ),
        
        'assigned_user_name' => array(
            'name' => 'assigned_user_name',
            'link' => 'assigned_user_link',
            'vname' => 'LBL_ASSIGNED_TO_NAME',
            'rname' => 'user_name',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'assigned_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled'
        ),
        
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator'
        ),
        
        // === TRADE-IN SPECIFIC FIELDS ===
        
        // Vehicle Identification and Specifications
        'vin' => array(
            'name' => 'vin',
            'vname' => 'LBL_VIN',
            'type' => 'varchar',
            'len' => '17',
            'comment' => 'Vehicle Identification Number',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'year' => array(
            'name' => 'year',
            'vname' => 'LBL_YEAR',
            'type' => 'int',
            'len' => '4',
            'comment' => 'Vehicle model year',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'make' => array(
            'name' => 'make',
            'vname' => 'LBL_MAKE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Vehicle manufacturer (e.g., Toyota, Ford)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'model' => array(
            'name' => 'model',
            'vname' => 'LBL_MODEL',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Vehicle model name (e.g., Camry, F-150)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'trim' => array(
            'name' => 'trim',
            'vname' => 'LBL_TRIM',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Vehicle trim level or package',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'mileage' => array(
            'name' => 'mileage',
            'vname' => 'LBL_MILEAGE',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Current odometer reading',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'exterior_color' => array(
            'name' => 'exterior_color',
            'vname' => 'LBL_EXTERIOR_COLOR',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Vehicle exterior paint color',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Condition Assessment
        'condition_overall' => array(
            'name' => 'condition_overall',
            'vname' => 'LBL_CONDITION_OVERALL',
            'type' => 'enum',
            'options' => 'tradein_condition_list',
            'len' => '20',
            'comment' => 'Overall vehicle condition rating',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'default' => 'Good',
        ),
        
        'condition_notes' => array(
            'name' => 'condition_notes',
            'vname' => 'LBL_CONDITION_NOTES',
            'type' => 'text',
            'comment' => 'Detailed condition assessment notes',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Customer Information
        'customer_asking' => array(
            'name' => 'customer_asking',
            'vname' => 'LBL_CUSTOMER_ASKING',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Customer expected trade-in value',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Market Valuations
        'market_value_retail' => array(
            'name' => 'market_value_retail',
            'vname' => 'LBL_MARKET_VALUE_RETAIL',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Market retail value from API',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'market_value_trade' => array(
            'name' => 'market_value_trade',
            'vname' => 'LBL_MARKET_VALUE_TRADE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Market trade-in value from API',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'market_value_private' => array(
            'name' => 'market_value_private',
            'vname' => 'LBL_MARKET_VALUE_PRIVATE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Market private party value from API',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'valuation_date' => array(
            'name' => 'valuation_date',
            'vname' => 'LBL_VALUATION_DATE',
            'type' => 'datetime',
            'comment' => 'When market values were last retrieved',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'valuation_source' => array(
            'name' => 'valuation_source',
            'vname' => 'LBL_VALUATION_SOURCE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'API source used for market valuation',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        // Dealer Appraisal
        'appraised_value' => array(
            'name' => 'appraised_value',
            'vname' => 'LBL_APPRAISED_VALUE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Dealer final appraisal amount',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'appraisal_date' => array(
            'name' => 'appraisal_date',
            'vname' => 'LBL_APPRAISAL_DATE',
            'type' => 'datetime',
            'comment' => 'Date of dealer appraisal',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'appraisal_notes' => array(
            'name' => 'appraisal_notes',
            'vname' => 'LBL_APPRAISAL_NOTES',
            'type' => 'text',
            'comment' => 'Appraiser notes and adjustments',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Payoff and Lien Information
        'payoff_amount' => array(
            'name' => 'payoff_amount',
            'vname' => 'LBL_PAYOFF_AMOUNT',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Loan payoff amount',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'payoff_bank' => array(
            'name' => 'payoff_bank',
            'vname' => 'LBL_PAYOFF_BANK',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Lender or lienholder information',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'payoff_date' => array(
            'name' => 'payoff_date',
            'vname' => 'LBL_PAYOFF_DATE',
            'type' => 'date',
            'comment' => 'Payoff good through date',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'payoff_verified' => array(
            'name' => 'payoff_verified',
            'vname' => 'LBL_PAYOFF_VERIFIED',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'Whether payoff amount has been verified',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Trade-in Status and Workflow
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'tradein_status_list',
            'len' => '50',
            'comment' => 'Trade-in workflow status',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'default' => 'New',
        ),
        
        'appraisal_scheduled_date' => array(
            'name' => 'appraisal_scheduled_date',
            'vname' => 'LBL_APPRAISAL_SCHEDULED_DATE',
            'type' => 'datetime',
            'comment' => 'When physical appraisal is scheduled',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'photos_taken' => array(
            'name' => 'photos_taken',
            'vname' => 'LBL_PHOTOS_TAKEN',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'Whether photos have been captured',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Integration and Processing
        'used_in_deal' => array(
            'name' => 'used_in_deal',
            'vname' => 'LBL_USED_IN_DEAL',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'Whether trade was used in a deal',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'deal_id' => array(
            'name' => 'deal_id',
            'vname' => 'LBL_DEAL_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Link to F&I deal if used',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'disposal_method' => array(
            'name' => 'disposal_method',
            'vname' => 'LBL_DISPOSAL_METHOD',
            'type' => 'enum',
            'options' => 'tradein_disposal_list',
            'len' => '50',
            'comment' => 'How trade was disposed (Retail/Wholesale/Auction)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Profitability Tracking
        'trade_allowance' => array(
            'name' => 'trade_allowance',
            'vname' => 'LBL_TRADE_ALLOWANCE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Amount credited to customer in deal',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'actual_cash_value' => array(
            'name' => 'actual_cash_value',
            'vname' => 'LBL_ACTUAL_CASH_VALUE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'ACV for accounting purposes',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'reconditioning_cost' => array(
            'name' => 'reconditioning_cost',
            'vname' => 'LBL_RECONDITIONING_COST',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Estimated reconditioning costs',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'estimated_profit' => array(
            'name' => 'estimated_profit',
            'vname' => 'LBL_ESTIMATED_PROFIT',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Projected profit on trade',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // === RELATIONSHIPS ===
        
        // Customer relationship
        'customer_id' => array(
            'name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Customer account ID',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'customer_name' => array(
            'name' => 'customer_name',
            'rname' => 'name',
            'id_name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_NAME',
            'type' => 'relate',
            'table' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'link' => 'customer_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Customer account name',
        ),
        
        // Opportunity relationship
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'vname' => 'LBL_OPPORTUNITY_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Sales opportunity ID',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'opportunity_name' => array(
            'name' => 'opportunity_name',
            'rname' => 'name',
            'id_name' => 'opportunity_id',
            'vname' => 'LBL_OPPORTUNITY_NAME',
            'type' => 'relate',
            'table' => 'opportunities',
            'isnull' => 'true',
            'module' => 'Opportunities',
            'dbType' => 'varchar',
            'link' => 'opportunity_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Sales opportunity name',
        ),
    ),
    
    // === RELATIONSHIPS DEFINITION ===
    'relationships' => array(
        // Customer relationship
        'dm_tradeins_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_TradeIns',
            'rhs_table' => 'dm_tradeins',
            'rhs_key' => 'customer_id',
            'relationship_type' => 'one-to-many',
        ),
        
        // Opportunity relationship
        'dm_tradeins_opportunities' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_TradeIns',
            'rhs_table' => 'dm_tradeins',
            'rhs_key' => 'opportunity_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    
    // === LINKS ===
    'links' => array(
        'assigned_user_link' => array(
            'name' => 'assigned_user_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_ASSIGNED_TO_USER',
        ),
        
        'modified_user_link' => array(
            'name' => 'modified_user_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_MODIFIED_BY_USER',
        ),
        
        'created_by_link' => array(
            'name' => 'created_by_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_CREATED_BY_USER',
        ),
        
        'customer_link' => array(
            'name' => 'customer_link',
            'type' => 'one',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'source' => 'non-db',
            'vname' => 'LBL_CUSTOMER',
        ),
        
        'opportunity_link' => array(
            'name' => 'opportunity_link',
            'type' => 'one',
            'module' => 'Opportunities',
            'bean_name' => 'Opportunity',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITY',
        ),
    ),
    
    // === INDICES ===
    'indices' => array(
        array(
            'name' => 'dm_tradeins_pk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_dm_tradeins_customer',
            'type' => 'index',
            'fields' => array('customer_id')
        ),
        array(
            'name' => 'idx_dm_tradeins_opportunity',
            'type' => 'index',
            'fields' => array('opportunity_id')
        ),
        array(
            'name' => 'idx_dm_tradeins_vin',
            'type' => 'index',
            'fields' => array('vin')
        ),
        array(
            'name' => 'idx_dm_tradeins_status',
            'type' => 'index',
            'fields' => array('status')
        ),
        array(
            'name' => 'idx_dm_tradeins_date_entered',
            'type' => 'index',
            'fields' => array('date_entered')
        ),
        array(
            'name' => 'idx_dm_tradeins_assigned_user',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_dm_tradeins_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        ),
    ),
);

// Add module to the global bean lists
VardefManager::createVardef('DM_TradeIns', 'DM_TradeIns', array('basic', 'assignable', 'security_groups'));
?> 