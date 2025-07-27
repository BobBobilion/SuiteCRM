<?php
/**
 * SuiteCRM Lead Attribution Center - Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_LeadAttribution module in the lead tracking and attribution system.
 * 
 * Features:
 * - UTM parameter tracking and attribution
 * - Google Analytics 4 API integration
 * - Facebook Lead Ads webhook support
 * - Multi-touch attribution modeling
 * - ROI calculation and reporting
 * - Lead journey tracking and analysis
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_LeadAttribution'] = array(
    'table' => 'dm_leadattribution',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Lead Attribution Center for tracking marketing touchpoints, campaign effectiveness, and ROI across multiple channels with Google Analytics and Facebook integration',
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
            'comment' => 'Attribution record display name (Lead Name - Campaign)',
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
            'comment' => 'User ID assigned to attribution analysis',
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
        
        // === LEAD ATTRIBUTION SPECIFIC FIELDS ===
        
        // Lead and Customer Relationships
        'lead_id' => array(
            'name' => 'lead_id',
            'vname' => 'LBL_LEAD_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Link to Leads module',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'lead_name' => array(
            'name' => 'lead_name',
            'rname' => 'name',
            'id_name' => 'lead_id',
            'vname' => 'LBL_LEAD_NAME',
            'type' => 'relate',
            'table' => 'leads',
            'isnull' => 'true',
            'module' => 'Leads',
            'dbType' => 'varchar',
            'link' => 'lead_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Lead name',
        ),
        
        'customer_id' => array(
            'name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Link to Accounts when converted',
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
        
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'vname' => 'LBL_OPPORTUNITY_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Link to Opportunities if converted to sale',
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
        
        // First Touch Attribution
        'first_touch_source' => array(
            'name' => 'first_touch_source',
            'vname' => 'LBL_FIRST_TOUCH_SOURCE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Initial source (Google, Facebook, Direct, etc.)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'first_touch_medium' => array(
            'name' => 'first_touch_medium',
            'vname' => 'LBL_FIRST_TOUCH_MEDIUM',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Initial medium (cpc, organic, social, etc.)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'first_touch_campaign' => array(
            'name' => 'first_touch_campaign',
            'vname' => 'LBL_FIRST_TOUCH_CAMPAIGN',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Initial campaign name',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'first_touch_date' => array(
            'name' => 'first_touch_date',
            'vname' => 'LBL_FIRST_TOUCH_DATE',
            'type' => 'datetime',
            'comment' => 'First interaction timestamp',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Last Touch Attribution
        'last_touch_source' => array(
            'name' => 'last_touch_source',
            'vname' => 'LBL_LAST_TOUCH_SOURCE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Last source before conversion',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'last_touch_medium' => array(
            'name' => 'last_touch_medium',
            'vname' => 'LBL_LAST_TOUCH_MEDIUM',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Last medium before conversion',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'last_touch_campaign' => array(
            'name' => 'last_touch_campaign',
            'vname' => 'LBL_LAST_TOUCH_CAMPAIGN',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Last campaign before conversion',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'last_touch_date' => array(
            'name' => 'last_touch_date',
            'vname' => 'LBL_LAST_TOUCH_DATE',
            'type' => 'datetime',
            'comment' => 'Last interaction timestamp',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // UTM Parameter Tracking
        'utm_source' => array(
            'name' => 'utm_source',
            'vname' => 'LBL_UTM_SOURCE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'UTM source parameter',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'utm_medium' => array(
            'name' => 'utm_medium',
            'vname' => 'LBL_UTM_MEDIUM',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'UTM medium parameter',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'utm_campaign' => array(
            'name' => 'utm_campaign',
            'vname' => 'LBL_UTM_CAMPAIGN',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'UTM campaign name',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'utm_content' => array(
            'name' => 'utm_content',
            'vname' => 'LBL_UTM_CONTENT',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'UTM content variant',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'utm_term' => array(
            'name' => 'utm_term',
            'vname' => 'LBL_UTM_TERM',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'UTM keyword term',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Referrer and Landing Page
        'referrer_url' => array(
            'name' => 'referrer_url',
            'vname' => 'LBL_REFERRER_URL',
            'type' => 'text',
            'comment' => 'Referring website URL',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'landing_page' => array(
            'name' => 'landing_page',
            'vname' => 'LBL_LANDING_PAGE',
            'type' => 'text',
            'comment' => 'First page visited',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // ROI and Cost Analysis
        'conversion_value' => array(
            'name' => 'conversion_value',
            'vname' => 'LBL_CONVERSION_VALUE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Revenue attributed to this lead',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'total_cost' => array(
            'name' => 'total_cost',
            'vname' => 'LBL_TOTAL_COST',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Cost to acquire this lead',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'roi_percentage' => array(
            'name' => 'roi_percentage',
            'vname' => 'LBL_ROI_PERCENTAGE',
            'type' => 'decimal',
            'len' => '5,2',
            'comment' => 'Return on investment percentage',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Attribution Analysis
        'touch_count' => array(
            'name' => 'touch_count',
            'vname' => 'LBL_TOUCH_COUNT',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Number of touchpoints',
            'required' => false,
            'audited' => false,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'attribution_model' => array(
            'name' => 'attribution_model',
            'vname' => 'LBL_ATTRIBUTION_MODEL',
            'type' => 'enum',
            'options' => 'attribution_model_list',
            'len' => '50',
            'comment' => 'Attribution model used (First-touch, Last-touch, Linear)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'default' => 'Last-touch',
        ),
        
        'channel_journey' => array(
            'name' => 'channel_journey',
            'vname' => 'LBL_CHANNEL_JOURNEY',
            'type' => 'text',
            'comment' => 'JSON of full customer journey',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // External Platform Integration
        'external_ids' => array(
            'name' => 'external_ids',
            'vname' => 'LBL_EXTERNAL_IDS',
            'type' => 'text',
            'comment' => 'JSON of external platform IDs (GA, Facebook, etc.)',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        // Google Analytics Integration
        'ga_client_id' => array(
            'name' => 'ga_client_id',
            'vname' => 'LBL_GA_CLIENT_ID',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Google Analytics Client ID',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'ga_session_id' => array(
            'name' => 'ga_session_id',
            'vname' => 'LBL_GA_SESSION_ID',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Google Analytics Session ID',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        // Facebook Lead Ads Integration
        'fb_lead_id' => array(
            'name' => 'fb_lead_id',
            'vname' => 'LBL_FB_LEAD_ID',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Facebook Lead Ads Lead ID',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'fb_form_id' => array(
            'name' => 'fb_form_id',
            'vname' => 'LBL_FB_FORM_ID',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Facebook Lead Form ID',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'fb_ad_id' => array(
            'name' => 'fb_ad_id',
            'vname' => 'LBL_FB_AD_ID',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Facebook Ad ID',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        // Additional tracking fields
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'text',
            'comment' => 'Additional attribution notes',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
    ),
    
    // === RELATIONSHIPS DEFINITION ===
    'relationships' => array(
        // Lead relationship
        'dm_leadattribution_leads' => array(
            'lhs_module' => 'Leads',
            'lhs_table' => 'leads',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_LeadAttribution',
            'rhs_table' => 'dm_leadattribution',
            'rhs_key' => 'lead_id',
            'relationship_type' => 'one-to-many',
        ),
        
        // Customer relationship
        'dm_leadattribution_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_LeadAttribution',
            'rhs_table' => 'dm_leadattribution',
            'rhs_key' => 'customer_id',
            'relationship_type' => 'one-to-many',
        ),
        
        // Opportunity relationship
        'dm_leadattribution_opportunities' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_LeadAttribution',
            'rhs_table' => 'dm_leadattribution',
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
        
        'lead_link' => array(
            'name' => 'lead_link',
            'type' => 'one',
            'module' => 'Leads',
            'bean_name' => 'Lead',
            'source' => 'non-db',
            'vname' => 'LBL_LEAD',
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
            'name' => 'idx_dm_leadattribution_lead',
            'type' => 'index',
            'fields' => array('lead_id')
        ),
        array(
            'name' => 'idx_dm_leadattribution_customer',
            'type' => 'index',
            'fields' => array('customer_id')
        ),
        array(
            'name' => 'idx_dm_leadattribution_opportunity',
            'type' => 'index',
            'fields' => array('opportunity_id')
        ),
        array(
            'name' => 'idx_dm_leadattribution_utm_source',
            'type' => 'index',
            'fields' => array('utm_source')
        ),
        array(
            'name' => 'idx_dm_leadattribution_utm_campaign',
            'type' => 'index',
            'fields' => array('utm_campaign')
        ),
        array(
            'name' => 'idx_dm_leadattribution_first_touch_date',
            'type' => 'index',
            'fields' => array('first_touch_date')
        ),
        array(
            'name' => 'idx_dm_leadattribution_last_touch_date',
            'type' => 'index',
            'fields' => array('last_touch_date')
        ),
        array(
            'name' => 'idx_dm_leadattribution_ga_client_id',
            'type' => 'index',
            'fields' => array('ga_client_id')
        ),
        array(
            'name' => 'idx_dm_leadattribution_fb_lead_id',
            'type' => 'index',
            'fields' => array('fb_lead_id')
        ),
        array(
            'name' => 'idx_dm_leadattribution_date_entered',
            'type' => 'index',
            'fields' => array('date_entered')
        ),
        array(
            'name' => 'idx_dm_leadattribution_assigned_user',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_dm_leadattribution_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        ),
    ),
);

// Add module to the global bean lists
VardefManager::createVardef('DM_LeadAttribution', 'DM_LeadAttribution', array('basic', 'assignable', 'security_groups'));
?>