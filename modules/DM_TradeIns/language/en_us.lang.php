<?php
/**
 * SuiteCRM Trade-In Manager - English Language Labels
 * 
 * This file contains all English language labels and text used throughout
 * the Trade-In Manager module interface, including field labels, panel titles,
 * dropdown options, and user messages.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // === MODULE IDENTIFICATION ===
    'LBL_MODULE_NAME' => 'Trade-Ins',
    'LBL_MODULE_TITLE' => 'Trade-In Manager',
    'LBL_MODULE_ID' => 'DM_TradeIns',
    'LBL_NEW_FORM_TITLE' => 'New Trade-In',
    'LBL_SEARCH_FORM_TITLE' => 'Search Trade-Ins',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'View History',
    
    // === STANDARD SUGARBEAN FIELDS ===
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
    'LBL_ASSIGNED_TO_ID' => 'Assigned User Id',
    'LBL_ASSIGNED_TO_NAME' => 'Assigned To',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Assigned User',
    'LBL_ASSIGNED_TO_USER' => 'Assigned to User',
    'LBL_MODIFIED_BY_USER' => 'Modified by User',
    'LBL_CREATED_BY_USER' => 'Created by User',
    
    // === VEHICLE IDENTIFICATION FIELDS ===
    'LBL_VIN' => 'VIN',
    'LBL_VIN_FULL' => 'Vehicle Identification Number',
    'LBL_YEAR' => 'Year',
    'LBL_MAKE' => 'Make',
    'LBL_MODEL' => 'Model',
    'LBL_TRIM' => 'Trim',
    'LBL_MILEAGE' => 'Mileage',
    'LBL_EXTERIOR_COLOR' => 'Exterior Color',
    
    // === CONDITION ASSESSMENT ===
    'LBL_CONDITION_OVERALL' => 'Overall Condition',
    'LBL_CONDITION_NOTES' => 'Condition Notes',
    
    // === CUSTOMER INFORMATION ===
    'LBL_CUSTOMER_ASKING' => 'Customer Asking Price',
    'LBL_CUSTOMER_ID' => 'Customer ID',
    'LBL_CUSTOMER_NAME' => 'Customer',
    'LBL_CUSTOMER' => 'Customer',
    
    // === MARKET VALUATIONS ===
    'LBL_MARKET_VALUE_RETAIL' => 'Market Value (Retail)',
    'LBL_MARKET_VALUE_TRADE' => 'Market Value (Trade)',
    'LBL_MARKET_VALUE_PRIVATE' => 'Market Value (Private)',
    'LBL_VALUATION_DATE' => 'Valuation Date',
    'LBL_VALUATION_SOURCE' => 'Valuation Source',
    'LBL_MARKET_VALUES' => 'Market Values',
    
    // === DEALER APPRAISAL ===
    'LBL_APPRAISED_VALUE' => 'Appraised Value',
    'LBL_APPRAISAL_DATE' => 'Appraisal Date',
    'LBL_APPRAISAL_NOTES' => 'Appraisal Notes',
    'LBL_APPRAISAL_INFO' => 'Appraisal Information',
    
    // === PAYOFF INFORMATION ===
    'LBL_PAYOFF_AMOUNT' => 'Payoff Amount',
    'LBL_PAYOFF_BANK' => 'Payoff Bank/Lender',
    'LBL_PAYOFF_DATE' => 'Payoff Good Through',
    'LBL_PAYOFF_VERIFIED' => 'Payoff Verified',
    'LBL_PAYOFF_INFO' => 'Payoff Information',
    
    // === STATUS AND WORKFLOW ===
    'LBL_STATUS' => 'Status',
    'LBL_APPRAISAL_SCHEDULED_DATE' => 'Appraisal Scheduled',
    'LBL_PHOTOS_TAKEN' => 'Photos Taken',
    'LBL_WORKFLOW' => 'Workflow',
    
    // === INTEGRATION FIELDS ===
    'LBL_OPPORTUNITY_ID' => 'Opportunity ID',
    'LBL_OPPORTUNITY_NAME' => 'Opportunity',
    'LBL_OPPORTUNITY' => 'Sales Opportunity',
    'LBL_USED_IN_DEAL' => 'Used in Deal',
    'LBL_DEAL_ID' => 'Deal ID',
    'LBL_DISPOSAL_METHOD' => 'Disposal Method',
    
    // === PROFITABILITY TRACKING ===
    'LBL_TRADE_ALLOWANCE' => 'Trade Allowance',
    'LBL_ACTUAL_CASH_VALUE' => 'Actual Cash Value (ACV)',
    'LBL_RECONDITIONING_COST' => 'Reconditioning Cost',
    'LBL_ESTIMATED_PROFIT' => 'Estimated Profit',
    'LBL_PROFITABILITY' => 'Profitability',
    
    // === PANEL TITLES ===
    'LBL_PANEL_OVERVIEW' => 'Trade-In Overview',
    'LBL_PANEL_VEHICLE_INFO' => 'Vehicle Information',
    'LBL_PANEL_CONDITION' => 'Condition Assessment',
    'LBL_PANEL_VALUATION' => 'Market Valuation',
    'LBL_PANEL_APPRAISAL' => 'Dealer Appraisal',
    'LBL_PANEL_PAYOFF' => 'Payoff Information',
    'LBL_PANEL_WORKFLOW' => 'Workflow & Status',
    'LBL_PANEL_PROFITABILITY' => 'Profitability Analysis',
    'LBL_PANEL_INTEGRATION' => 'Sales Integration',
    
    // === LIST VIEW COLUMNS ===
    'LBL_LIST_NAME' => 'Trade-In',
    'LBL_LIST_CUSTOMER_NAME' => 'Customer',
    'LBL_LIST_YEAR' => 'Year',
    'LBL_LIST_MAKE' => 'Make',
    'LBL_LIST_MODEL' => 'Model',
    'LBL_LIST_MILEAGE' => 'Mileage',
    'LBL_LIST_STATUS' => 'Status',
    'LBL_LIST_APPRAISED_VALUE' => 'Appraised Value',
    'LBL_LIST_MARKET_VALUE_TRADE' => 'Market Trade Value',
    'LBL_LIST_CUSTOMER_ASKING' => 'Customer Asking',
    'LBL_LIST_DATE_ENTERED' => 'Date Created',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Assigned To',
    
    // === SEARCH FORM ===
    'LBL_SEARCH_CUSTOMER' => 'Customer',
    'LBL_SEARCH_VIN' => 'VIN',
    'LBL_SEARCH_YEAR' => 'Year',
    'LBL_SEARCH_MAKE' => 'Make',
    'LBL_SEARCH_MODEL' => 'Model',
    'LBL_SEARCH_STATUS' => 'Status',
    
    // === BUTTONS AND ACTIONS ===
    'LBL_NEW_TRADEIN' => 'New Trade-In',
    'LBL_EDIT_TRADEIN' => 'Edit Trade-In',
    'LBL_SAVE_TRADEIN' => 'Save Trade-In',
    'LBL_DELETE_TRADEIN' => 'Delete Trade-In',
    'LBL_DUPLICATE_TRADEIN' => 'Duplicate Trade-In',
    'LBL_GET_MARKET_VALUES' => 'Get Market Values',
    'LBL_REFRESH_VALUES' => 'Refresh Market Values',
    'LBL_SCHEDULE_APPRAISAL' => 'Schedule Appraisal',
    'LBL_COMPLETE_APPRAISAL' => 'Complete Appraisal',
    'LBL_APPROVE_TRADE' => 'Approve Trade',
    'LBL_REJECT_TRADE' => 'Reject Trade',
    'LBL_USE_IN_DEAL' => 'Use in Deal',
    
    // === MESSAGES AND ALERTS ===
    'MSG_MARKET_VALUES_RETRIEVED' => 'Market values successfully retrieved from API',
    'MSG_MARKET_VALUES_ERROR' => 'Error retrieving market values. Please try again.',
    'MSG_VIN_DECODED' => 'VIN successfully decoded',
    'MSG_VIN_DECODE_ERROR' => 'Error decoding VIN. Please verify VIN is correct.',
    'MSG_APPRAISAL_SCHEDULED' => 'Appraisal has been scheduled',
    'MSG_APPRAISAL_COMPLETED' => 'Appraisal has been completed',
    'MSG_TRADE_APPROVED' => 'Trade-in has been approved',
    'MSG_TRADE_REJECTED' => 'Trade-in has been rejected',
    'MSG_PAYOFF_VERIFIED' => 'Payoff amount has been verified',
    'MSG_PHOTOS_UPLOADED' => 'Photos have been uploaded successfully',
    
    // === HELP TEXT ===
    'LBL_HELP_VIN' => 'Enter the 17-character Vehicle Identification Number',
    'LBL_HELP_MARKET_VALUES' => 'Market values are retrieved automatically via API integration',
    'LBL_HELP_CONDITION' => 'Select the overall condition based on visual inspection',
    'LBL_HELP_PAYOFF' => 'Contact lender to verify exact payoff amount and good-through date',
    'LBL_HELP_APPRAISAL' => 'Final dealer appraisal should consider condition, market, and reconditioning costs',
    
    // === CALCULATED FIELDS ===
    'LBL_TRADE_EQUITY' => 'Trade Equity',
    'LBL_VALUE_VARIANCE' => 'Value Variance',
    'LBL_PROFIT_POTENTIAL' => 'Profit Potential',
    'LBL_NEEDS_APPRAISAL' => 'Needs Appraisal',
    
    // === REPORTS AND ANALYTICS ===
    'LBL_TRADE_SUMMARY' => 'Trade-In Summary',
    'LBL_VALUE_ANALYSIS' => 'Value Analysis',
    'LBL_PROFITABILITY_REPORT' => 'Profitability Report',
    'LBL_APPRAISAL_ACTIVITY' => 'Appraisal Activity',
    
    // === INTEGRATION MESSAGES ===
    'LBL_LINKED_TO_OPPORTUNITY' => 'Linked to Sales Opportunity',
    'LBL_LINKED_TO_DEAL' => 'Used in F&I Deal',
    'LBL_AVAILABLE_FOR_DEAL' => 'Available for Deal',
    'LBL_NOT_APPROVED' => 'Not Yet Approved',
    
    // === VALIDATION MESSAGES ===
    'ERROR_VIN_INVALID' => 'VIN must be exactly 17 characters',
    'ERROR_YEAR_INVALID' => 'Year must be between 1980 and current year + 1',
    'ERROR_MILEAGE_INVALID' => 'Mileage must be a positive number',
    'ERROR_VALUE_NEGATIVE' => 'Values cannot be negative',
    'ERROR_PAYOFF_DATE_PAST' => 'Payoff date cannot be in the past',
    'ERROR_CUSTOMER_REQUIRED' => 'Customer is required for trade-in',
    
    // === SUBPANEL TITLES ===
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Activities',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'History',
    'LBL_NOTES_SUBPANEL_TITLE' => 'Notes',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documents',
    
    // === QUICK CREATE ===
    'LBL_QUICK_CREATE' => 'Quick Create Trade-In',
    'LBL_QUICK_CUSTOMER' => 'Customer',
    'LBL_QUICK_VEHICLE' => 'Vehicle',
    'LBL_QUICK_VIN' => 'VIN',
    'LBL_QUICK_ASKING' => 'Customer Asking',
    
    // === DASHLET LABELS ===
    'LBL_DASHLET_RECENT_TRADES' => 'Recent Trade-Ins',
    'LBL_DASHLET_PENDING_APPRAISALS' => 'Pending Appraisals',
    'LBL_DASHLET_VALUE_SUMMARY' => 'Trade Value Summary',
    'LBL_DASHLET_MY_TRADES' => 'My Trade-Ins',
);

// === DROPDOWN OPTIONS ===

// Trade-In Status Options
$app_list_strings['tradein_status_list'] = array(
    'New' => 'New',
    'Appraised' => 'Appraised',
    'Approved' => 'Approved',
    'Used' => 'Used in Deal',
    'Rejected' => 'Rejected',
);

// Overall Condition Options
$app_list_strings['tradein_condition_list'] = array(
    'Excellent' => 'Excellent',
    'Good' => 'Good',
    'Fair' => 'Fair',
    'Poor' => 'Poor',
);

// Disposal Method Options
$app_list_strings['tradein_disposal_list'] = array(
    'Retail' => 'Retail Sale',
    'Wholesale' => 'Wholesale',
    'Auction' => 'Auction',
    'Export' => 'Export',
    'Parts' => 'Parts/Scrap',
);

// Valuation Source Options
$app_list_strings['tradein_valuation_source_list'] = array(
    'Vehicle_Databases' => 'Vehicle Databases API',
    'CarsXE' => 'CarsXE API',
    'Manual' => 'Manual Entry',
    'KBB' => 'Kelley Blue Book',
    'NADA' => 'NADA Guides',
    'Edmunds' => 'Edmunds',
);

// Module Name for Global Use
$app_list_strings['moduleList']['DM_TradeIns'] = 'Trade-Ins';
$app_list_strings['moduleListSingular']['DM_TradeIns'] = 'Trade-In';

?> 