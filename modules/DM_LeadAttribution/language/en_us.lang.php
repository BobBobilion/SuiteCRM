<?php
/**
 * SuiteCRM Lead Attribution Center - English Language Pack
 * 
 * This file contains all language strings for the Lead Attribution Center module
 * including field labels, module names, and interface text.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // Module Names
    'LBL_MODULE_NAME' => 'Lead Attribution',
    'LBL_MODULE_TITLE' => 'Lead Attribution Center',
    'LBL_MODULE_NAME_SINGULAR' => 'Lead Attribution',
    'LBL_HOMEPAGE_TITLE' => 'My Lead Attribution',
    'LNK_NEW_RECORD' => 'Create Lead Attribution',
    'LNK_LIST' => 'View Lead Attribution',
    'LNK_ROI_DASHBOARD' => 'ROI Dashboard',
    'LNK_ATTRIBUTION_REPORT' => 'Attribution Report',
    'LBL_SEARCH_FORM_TITLE' => 'Lead Attribution Search',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'View History',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Activities',
    'LBL_NEW_FORM_TITLE' => 'New Lead Attribution',
    'LBL_LIST_FORM_TITLE' => 'Lead Attribution List',
    
    // Standard Fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Name',
    'LBL_DATE_ENTERED' => 'Date Created',
    'LBL_DATE_MODIFIED' => 'Date Modified',
    'LBL_MODIFIED' => 'Modified By',
    'LBL_MODIFIED_ID' => 'Modified By Id',
    'LBL_MODIFIED_NAME' => 'Modified By Name',
    'LBL_CREATED' => 'Created By',
    'LBL_CREATED_ID' => 'Created By Id',
    'LBL_DESCRIPTION' => 'Description',
    'LBL_DELETED' => 'Deleted',
    'LBL_ASSIGNED_TO' => 'Assigned to',
    'LBL_ASSIGNED_TO_ID' => 'Assigned User Id',
    'LBL_ASSIGNED_TO_NAME' => 'Assigned to',
    
    // Lead Attribution Specific Fields
    'LBL_LEAD_ID' => 'Lead ID',
    'LBL_LEAD_NAME' => 'Lead Name',
    'LBL_CUSTOMER_ID' => 'Customer ID',
    'LBL_CUSTOMER_NAME' => 'Customer Name',
    'LBL_OPPORTUNITY_ID' => 'Opportunity ID',
    'LBL_OPPORTUNITY_NAME' => 'Opportunity Name',
    
    // First Touch Attribution
    'LBL_FIRST_TOUCH_SOURCE' => 'First Touch Source',
    'LBL_FIRST_TOUCH_MEDIUM' => 'First Touch Medium',
    'LBL_FIRST_TOUCH_CAMPAIGN' => 'First Touch Campaign',
    'LBL_FIRST_TOUCH_DATE' => 'First Touch Date',
    
    // Last Touch Attribution
    'LBL_LAST_TOUCH_SOURCE' => 'Last Touch Source',
    'LBL_LAST_TOUCH_MEDIUM' => 'Last Touch Medium',
    'LBL_LAST_TOUCH_CAMPAIGN' => 'Last Touch Campaign',
    'LBL_LAST_TOUCH_DATE' => 'Last Touch Date',
    
    // UTM Parameters
    'LBL_UTM_SOURCE' => 'UTM Source',
    'LBL_UTM_MEDIUM' => 'UTM Medium',
    'LBL_UTM_CAMPAIGN' => 'UTM Campaign',
    'LBL_UTM_CONTENT' => 'UTM Content',
    'LBL_UTM_TERM' => 'UTM Term',
    
    // Referrer and Landing Page
    'LBL_REFERRER_URL' => 'Referrer URL',
    'LBL_LANDING_PAGE' => 'Landing Page',
    
    // ROI and Cost Analysis
    'LBL_CONVERSION_VALUE' => 'Conversion Value',
    'LBL_TOTAL_COST' => 'Total Cost',
    'LBL_ROI_PERCENTAGE' => 'ROI Percentage',
    
    // Attribution Analysis
    'LBL_TOUCH_COUNT' => 'Touch Count',
    'LBL_ATTRIBUTION_MODEL' => 'Attribution Model',
    'LBL_CHANNEL_JOURNEY' => 'Channel Journey',
    
    // External Platform Integration
    'LBL_EXTERNAL_IDS' => 'External IDs',
    'LBL_GA_CLIENT_ID' => 'GA Client ID',
    'LBL_GA_SESSION_ID' => 'GA Session ID',
    'LBL_FB_LEAD_ID' => 'Facebook Lead ID',
    'LBL_FB_FORM_ID' => 'Facebook Form ID',
    'LBL_FB_AD_ID' => 'Facebook Ad ID',
    
    // Additional Fields
    'LBL_NOTES' => 'Notes',
    
    // Panel Names
    'LBL_PANEL_ATTRIBUTION_INFO' => 'Attribution Information',
    'LBL_PANEL_FIRST_TOUCH' => 'First Touch Attribution',
    'LBL_PANEL_LAST_TOUCH' => 'Last Touch Attribution',
    'LBL_PANEL_UTM_PARAMETERS' => 'UTM Parameters',
    'LBL_PANEL_ROI_ANALYSIS' => 'ROI Analysis',
    'LBL_PANEL_EXTERNAL_INTEGRATION' => 'External Platform Integration',
    'LBL_PANEL_JOURNEY_ANALYSIS' => 'Journey Analysis',
    
    // Dashboard and Reporting
    'LBL_ROI_DASHBOARD_TITLE' => 'ROI Dashboard',
    'LBL_ATTRIBUTION_REPORT_TITLE' => 'Attribution Report',
    'LBL_TOTAL_LEADS' => 'Total Leads',
    'LBL_TOTAL_REVENUE' => 'Total Revenue',
    'LBL_TOTAL_COST_LABEL' => 'Total Cost',
    'LBL_AVERAGE_ROI' => 'Average ROI',
    'LBL_CONVERTED_LEADS' => 'Converted Leads',
    'LBL_CONVERSION_RATE' => 'Conversion Rate',
    'LBL_CHANNEL_PERFORMANCE' => 'Channel Performance',
    'LBL_CAMPAIGN_ROI' => 'Campaign ROI',
    'LBL_TOP_CAMPAIGNS' => 'Top Performing Campaigns',
    'LBL_ATTRIBUTION_SUMMARY' => 'Attribution Summary',
    
    // Filter Labels
    'LBL_FILTER_DATE_FROM' => 'Date From',
    'LBL_FILTER_DATE_TO' => 'Date To',
    'LBL_FILTER_SOURCE' => 'Source',
    'LBL_FILTER_CAMPAIGN' => 'Campaign',
    'LBL_APPLY_FILTERS' => 'Apply Filters',
    'LBL_RESET_FILTERS' => 'Reset Filters',
    
    // Actions
    'LBL_CAPTURE_UTM' => 'Capture UTM Parameters',
    'LBL_UPDATE_ATTRIBUTION' => 'Update Attribution',
    'LBL_CALCULATE_ROI' => 'Calculate ROI',
    'LBL_VIEW_JOURNEY' => 'View Customer Journey',
    'LBL_EXPORT_REPORT' => 'Export Report',
    
    // Messages
    'LBL_NO_DATA' => 'No attribution data available',
    'LBL_UTM_CAPTURED' => 'UTM parameters captured successfully',
    'LBL_ATTRIBUTION_UPDATED' => 'Attribution data updated successfully',
    'LBL_ROI_CALCULATED' => 'ROI calculated successfully',
    'LBL_ERROR_OCCURRED' => 'An error occurred',
    'LBL_INVALID_DATA' => 'Invalid data provided',
    'LBL_SUCCESS' => 'Operation completed successfully',
    
    // Help Text
    'LBL_HELP_FIRST_TOUCH' => 'The first marketing touchpoint that brought the visitor to your site',
    'LBL_HELP_LAST_TOUCH' => 'The last marketing touchpoint before the lead converted',
    'LBL_HELP_UTM_PARAMETERS' => 'UTM parameters from the URL that track campaign performance',
    'LBL_HELP_ROI_CALCULATION' => 'ROI = ((Revenue - Cost) / Cost) * 100',
    'LBL_HELP_ATTRIBUTION_MODEL' => 'The model used to attribute conversion credit to touchpoints',
    'LBL_HELP_CHANNEL_JOURNEY' => 'Complete timeline of all marketing touchpoints for this lead',
    
    // Error Messages
    'ERR_LEAD_NOT_FOUND' => 'Lead not found',
    'ERR_INVALID_UTM_DATA' => 'Invalid UTM data provided',
    'ERR_API_CONNECTION' => 'Unable to connect to external API',
    'ERR_INSUFFICIENT_DATA' => 'Insufficient data for attribution analysis',
    'ERR_PERMISSION_DENIED' => 'Permission denied for this operation',
    
    // Attribution Model Options
    'LBL_ATTRIBUTION_FIRST_TOUCH' => 'First-touch',
    'LBL_ATTRIBUTION_LAST_TOUCH' => 'Last-touch',
    'LBL_ATTRIBUTION_LINEAR' => 'Linear',
    'LBL_ATTRIBUTION_TIME_DECAY' => 'Time Decay',
    'LBL_ATTRIBUTION_POSITION_BASED' => 'Position Based',
    
    // Source Types
    'LBL_SOURCE_GOOGLE' => 'Google',
    'LBL_SOURCE_FACEBOOK' => 'Facebook',
    'LBL_SOURCE_DIRECT' => 'Direct',
    'LBL_SOURCE_EMAIL' => 'Email',
    'LBL_SOURCE_REFERRAL' => 'Referral',
    'LBL_SOURCE_ORGANIC' => 'Organic Search',
    'LBL_SOURCE_PAID' => 'Paid Search',
    'LBL_SOURCE_SOCIAL' => 'Social Media',
    
    // Medium Types
    'LBL_MEDIUM_CPC' => 'Cost Per Click',
    'LBL_MEDIUM_ORGANIC' => 'Organic',
    'LBL_MEDIUM_SOCIAL' => 'Social',
    'LBL_MEDIUM_EMAIL' => 'Email',
    'LBL_MEDIUM_REFERRAL' => 'Referral',
    'LBL_MEDIUM_DIRECT' => 'Direct',
    'LBL_MEDIUM_DISPLAY' => 'Display',
    'LBL_MEDIUM_AFFILIATE' => 'Affiliate',
    
    // JavaScript Messages
    'JS_CONFIRM_DELETE' => 'Are you sure you want to delete this attribution record?',
    'JS_UTM_CAPTURING' => 'Capturing UTM parameters...',
    'JS_CALCULATING_ROI' => 'Calculating ROI...',
    'JS_LOADING_REPORT' => 'Loading attribution report...',
    'JS_EXPORT_COMPLETE' => 'Export completed successfully',
    
    // Validation Messages
    'LBL_REQUIRED_FIELD' => 'This field is required',
    'LBL_INVALID_EMAIL' => 'Please enter a valid email address',
    'LBL_INVALID_DATE' => 'Please enter a valid date',
    'LBL_INVALID_NUMBER' => 'Please enter a valid number',
    'LBL_INVALID_URL' => 'Please enter a valid URL',
);

// Add dropdown lists
$app_list_strings['attribution_model_list'] = array(
    'First-touch' => 'First-touch',
    'Last-touch' => 'Last-touch',
    'Linear' => 'Linear',
    'Time-decay' => 'Time Decay',
    'Position-based' => 'Position Based',
);

$app_list_strings['utm_source_list'] = array(
    'google' => 'Google',
    'facebook' => 'Facebook',
    'linkedin' => 'LinkedIn',
    'twitter' => 'Twitter',
    'instagram' => 'Instagram',
    'youtube' => 'YouTube',
    'email' => 'Email',
    'direct' => 'Direct',
    'referral' => 'Referral',
    'other' => 'Other',
);

$app_list_strings['utm_medium_list'] = array(
    'cpc' => 'Cost Per Click',
    'organic' => 'Organic',
    'social' => 'Social',
    'email' => 'Email',
    'referral' => 'Referral',
    'direct' => 'Direct',
    'display' => 'Display',
    'affiliate' => 'Affiliate',
    'content' => 'Content Marketing',
    'video' => 'Video',
    'other' => 'Other',
);
?>