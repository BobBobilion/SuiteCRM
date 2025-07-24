<?php
/**
 * SuiteCRM F&I Deal Center - English Language Definitions
 * 
 * This file contains all language strings for the F&I Deal Center module,
 * including field labels, module names, list options, and user interface text.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // Module Labels
    'LBL_MODULE_NAME' => 'F&I Deal Center',
    'LBL_MODULE_TITLE' => 'F&I Deal Center',
    'LBL_SEARCH_FORM_TITLE' => 'F&I Deal Search',
    'LBL_LIST_FORM_TITLE' => 'F&I Deals List',
    'LBL_NEW_FORM_TITLE' => 'Create F&I Deal',
    'LBL_HOMEPAGE_TITLE' => 'My F&I Deals',
    'LNK_NEW_RECORD' => 'Create F&I Deal',
    'LNK_LIST' => 'F&I Deals',
    'LNK_IMPORT_DM_FIDEALS' => 'Import F&I Deals',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'F&I Deals',
    
    // Standard Fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Deal Name',
    'LBL_DATE_ENTERED' => 'Date Created',
    'LBL_DATE_MODIFIED' => 'Date Modified',
    'LBL_MODIFIED' => 'Modified By',
    'LBL_MODIFIED_NAME' => 'Modified By Name',
    'LBL_CREATED' => 'Created By',
    'LBL_CREATED_BY' => 'Created By',
    'LBL_DESCRIPTION' => 'Description',
    'LBL_DELETED' => 'Deleted',
    'LBL_ASSIGNED_TO' => 'Assigned To',
    'LBL_ASSIGNED_USER_NAME' => 'Assigned User',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'Assigned User',
    'LBL_LIST_ASSIGNED_USER' => 'Assigned User',
    
    // Core F&I Deal Fields
    'LBL_DEAL_NUMBER' => 'Deal Number',
    'LBL_DEAL_NUMBER_SHORT' => 'Deal #',
    'LBL_LIST_DEAL_NUMBER' => 'Deal Number',
    
    // Relationship Fields
    'LBL_OPPORTUNITY_ID' => 'Opportunity ID',
    'LBL_OPPORTUNITY_NAME' => 'Opportunity',
    'LBL_LIST_OPPORTUNITY_NAME' => 'Opportunity',
    'LBL_CUSTOMER_ID' => 'Customer ID',
    'LBL_CUSTOMER_NAME' => 'Customer',
    'LBL_LIST_CUSTOMER_NAME' => 'Customer',
    'LBL_VEHICLE_ID' => 'Vehicle ID',
    'LBL_VEHICLE_NAME' => 'Vehicle',
    'LBL_LIST_VEHICLE_NAME' => 'Vehicle',
    'LBL_TRADEIN_ID' => 'Trade-in ID',
    'LBL_TRADEIN_NAME' => 'Trade-in Vehicle',
    
    // Vehicle Pricing Structure
    'LBL_SALES_PRICE' => 'Sales Price',
    'LBL_LIST_SALES_PRICE' => 'Sales Price',
    'LBL_DOWN_PAYMENT' => 'Down Payment',
    'LBL_TRADE_ALLOWANCE' => 'Trade Allowance',
    'LBL_TRADE_PAYOFF' => 'Trade Payoff',
    'LBL_REBATES' => 'Rebates',
    'LBL_DEALER_FEES' => 'Dealer Fees',
    'LBL_GOVERNMENT_FEES' => 'Government Fees',
    'LBL_TAX_TITLE_LICENSE' => 'Tax, Title & License',
    
    // Finance Structure
    'LBL_FINANCE_METHOD' => 'Finance Method',
    'LBL_LIST_FINANCE_METHOD' => 'Finance Method',
    'LBL_AMOUNT_FINANCED' => 'Amount Financed',
    'LBL_LIST_AMOUNT_FINANCED' => 'Amount Financed',
    'LBL_TERM_MONTHS' => 'Term (Months)',
    'LBL_LIST_TERM_MONTHS' => 'Term',
    'LBL_INTEREST_RATE' => 'Interest Rate (%)',
    'LBL_MONTHLY_PAYMENT' => 'Monthly Payment',
    'LBL_LIST_MONTHLY_PAYMENT' => 'Monthly Payment',
    'LBL_TOTAL_PAYMENTS' => 'Total of Payments',
    'LBL_FINANCE_CHARGE' => 'Finance Charge',
    
    // Lender Information
    'LBL_LENDER_ID' => 'Lender ID',
    'LBL_LENDER_NAME' => 'Lender',
    'LBL_LIST_LENDER_NAME' => 'Lender',
    'LBL_APPROVAL_NUMBER' => 'Approval Number',
    'LBL_BUY_RATE' => 'Buy Rate (%)',
    'LBL_SELL_RATE' => 'Sell Rate (%)',
    'LBL_RATE_MARKUP' => 'Rate Markup (%)',
    'LBL_FINANCE_RESERVE' => 'Finance Reserve',
    'LBL_LIST_FINANCE_RESERVE' => 'Finance Reserve',
    
    // F&I Product Sales
    'LBL_WARRANTY_TOTAL' => 'Extended Warranty',
    'LBL_GAP_AMOUNT' => 'GAP Insurance',
    'LBL_ETCH_AMOUNT' => 'Theft Protection',
    'LBL_MAINTENANCE_AMOUNT' => 'Maintenance Plan',
    'LBL_OTHER_PRODUCTS' => 'Other Products',
    'LBL_TOTAL_PRODUCTS' => 'Total Products',
    
    // Profitability Tracking
    'LBL_BACKEND_GROSS' => 'Backend Gross',
    'LBL_LIST_BACKEND_GROSS' => 'Backend Gross',
    'LBL_FRONTEND_GROSS' => 'Frontend Gross',
    'LBL_TOTAL_GROSS' => 'Total Gross',
    'LBL_LIST_TOTAL_GROSS' => 'Total Gross',
    'LBL_PROFIT_MARGIN' => 'Profit Margin',
    
    // Deal Workflow and Status
    'LBL_DEAL_STATUS' => 'Deal Status',
    'LBL_LIST_DEAL_STATUS' => 'Status',
    'LBL_CREDIT_APP_ID' => 'Credit Application ID',
    'LBL_STIPS_REQUIRED' => 'Stipulations Required',
    'LBL_FUNDING_DATE' => 'Funding Date',
    'LBL_LIST_FUNDING_DATE' => 'Funding Date',
    'LBL_CONTRACT_DATE' => 'Contract Date',
    'LBL_LIST_CONTRACT_DATE' => 'Contract Date',
    
    // Staff Assignments
    'LBL_FI_MANAGER' => 'F&I Manager',
    'LBL_LIST_FI_MANAGER' => 'F&I Manager',
    'LBL_SALESPERSON' => 'Salesperson',
    'LBL_LIST_SALESPERSON' => 'Salesperson',
    
    // Additional Information
    'LBL_NOTES' => 'Notes',
    'LBL_DEAL_NOTES' => 'Deal Notes',
    
    // Panel Headers
    'LBL_PANEL_OVERVIEW' => 'Deal Overview',
    'LBL_PANEL_VEHICLE_PRICING' => 'Vehicle Pricing',
    'LBL_PANEL_FINANCING' => 'Financing Details',
    'LBL_PANEL_LENDER_INFO' => 'Lender Information',
    'LBL_PANEL_PRODUCTS' => 'F&I Products',
    'LBL_PANEL_PROFITABILITY' => 'Profitability',
    'LBL_PANEL_WORKFLOW' => 'Deal Workflow',
    'LBL_PANEL_STAFF' => 'Staff Assignments',
    
    // Calculation Labels
    'LBL_CALCULATIONS' => 'Calculations',
    'LBL_PAYMENT_CALCULATOR' => 'Payment Calculator',
    'LBL_PROFIT_CALCULATOR' => 'Profit Calculator',
    'LBL_RECALCULATE' => 'Recalculate',
    'LBL_CALCULATE_PAYMENT' => 'Calculate Payment',
    'LBL_PAYMENT_SCENARIOS' => 'Payment Scenarios',
    
    // Action Labels
    'LBL_CREATE_DEAL' => 'Create Deal',
    'LBL_EDIT_DEAL' => 'Edit Deal',
    'LBL_VIEW_DEAL' => 'View Deal',
    'LBL_DELETE_DEAL' => 'Delete Deal',
    'LBL_DUPLICATE_DEAL' => 'Duplicate Deal',
    'LBL_SUBMIT_DEAL' => 'Submit Deal',
    'LBL_APPROVE_DEAL' => 'Approve Deal',
    'LBL_FUND_DEAL' => 'Fund Deal',
    'LBL_COMPLETE_DEAL' => 'Complete Deal',
    'LBL_PRINT_CONTRACT' => 'Print Contract',
    'LBL_PRINT_WORKSHEET' => 'Print Worksheet',
    'LBL_EXPORT_DEAL' => 'Export Deal',
    
    // Error Messages
    'ERR_DEAL_NOT_FOUND' => 'F&I Deal not found',
    'ERR_CALCULATION_FAILED' => 'Payment calculation failed',
    'ERR_INVALID_AMOUNT' => 'Invalid amount entered',
    'ERR_INVALID_RATE' => 'Invalid interest rate',
    'ERR_INVALID_TERM' => 'Invalid loan term',
    'ERR_MISSING_CUSTOMER' => 'Customer is required',
    'ERR_MISSING_VEHICLE' => 'Vehicle is required',
    'ERR_MISSING_LENDER' => 'Lender is required for financed deals',
    'ERR_NEGATIVE_AMOUNT' => 'Amount cannot be negative',
    
    // Success Messages
    'MSG_DEAL_CREATED' => 'F&I Deal created successfully',
    'MSG_DEAL_UPDATED' => 'F&I Deal updated successfully',
    'MSG_DEAL_DELETED' => 'F&I Deal deleted successfully',
    'MSG_CALCULATIONS_COMPLETE' => 'All calculations completed',
    'MSG_DEAL_SUBMITTED' => 'Deal submitted for approval',
    'MSG_DEAL_APPROVED' => 'Deal approved successfully',
    'MSG_DEAL_FUNDED' => 'Deal funded successfully',
    
    // Help Text
    'HELP_SALES_PRICE' => 'Enter the agreed-upon selling price of the vehicle',
    'HELP_DOWN_PAYMENT' => 'Enter the cash down payment amount from customer',
    'HELP_TRADE_ALLOWANCE' => 'Enter the credit amount for trade-in vehicle',
    'HELP_TRADE_PAYOFF' => 'Enter the amount still owed on trade-in vehicle',
    'HELP_REBATES' => 'Enter total manufacturer rebates and incentives',
    'HELP_DEALER_FEES' => 'Enter documentation and dealer processing fees',
    'HELP_GOVERNMENT_FEES' => 'Enter tax, title, license, and registration fees',
    'HELP_AMOUNT_FINANCED' => 'Automatically calculated based on deal structure',
    'HELP_TERM_MONTHS' => 'Enter loan term in months (12-96)',
    'HELP_BUY_RATE' => 'Enter the rate from the lender (dealer cost)',
    'HELP_SELL_RATE' => 'Enter the rate charged to customer',
    'HELP_RATE_MARKUP' => 'Difference between sell rate and buy rate',
    'HELP_FINANCE_RESERVE' => 'Dealer profit from rate markup (calculated)',
    'HELP_BACKEND_GROSS' => 'Total F&I department profit (calculated)',
    'HELP_TOTAL_GROSS' => 'Total deal profit front and back end combined',
    
    // Tooltips
    'TIP_DEAL_NUMBER' => 'Unique identifier for this F&I deal',
    'TIP_MONTHLY_PAYMENT' => 'Calculated based on amount financed, term, and rate',
    'TIP_FINANCE_CHARGE' => 'Total interest paid over the life of the loan',
    'TIP_FINANCE_RESERVE' => 'Dealer profit from interest rate markup',
    'TIP_BACKEND_GROSS' => 'Profit from financing and F&I products',
    'TIP_TOTAL_GROSS' => 'Combined profit from vehicle sale and F&I',
    
    // Search Labels
    'LBL_SEARCH_CUSTOMER' => 'Search Customer',
    'LBL_SEARCH_VEHICLE' => 'Search Vehicle',
    'LBL_SEARCH_LENDER' => 'Search Lender',
    'LBL_SEARCH_DEAL_NUMBER' => 'Search Deal Number',
    'LBL_SEARCH_STATUS' => 'Search Status',
    'LBL_SEARCH_DATE_RANGE' => 'Search Date Range',
    
    // Quick Filter Labels
    'LBL_FILTER_ALL' => 'All Deals',
    'LBL_FILTER_DRAFT' => 'Draft Deals',
    'LBL_FILTER_SUBMITTED' => 'Submitted Deals',
    'LBL_FILTER_APPROVED' => 'Approved Deals',
    'LBL_FILTER_FUNDED' => 'Funded Deals',
    'LBL_FILTER_COMPLETE' => 'Complete Deals',
    'LBL_FILTER_MY_DEALS' => 'My Deals',
    'LBL_FILTER_TEAM_DEALS' => 'Team Deals',
    'LBL_FILTER_THIS_MONTH' => 'This Month',
    'LBL_FILTER_LAST_MONTH' => 'Last Month',
    'LBL_FILTER_THIS_QUARTER' => 'This Quarter',
    
    // Report Labels
    'LBL_REPORTS' => 'Reports',
    'LBL_DEAL_SUMMARY_REPORT' => 'Deal Summary Report',
    'LBL_PROFITABILITY_REPORT' => 'Profitability Report',
    'LBL_LENDER_PERFORMANCE' => 'Lender Performance',
    'LBL_PRODUCT_PENETRATION' => 'Product Penetration',
    'LBL_FI_MANAGER_PERFORMANCE' => 'F&I Manager Performance',
    'LBL_MONTHLY_SUMMARY' => 'Monthly Summary',
    'LBL_QUARTERLY_SUMMARY' => 'Quarterly Summary',
    'LBL_ANNUAL_SUMMARY' => 'Annual Summary',
    
    // Dashboard Labels
    'LBL_DASHBOARD' => 'Dashboard',
    'LBL_DEALS_TODAY' => 'Deals Today',
    'LBL_DEALS_THIS_WEEK' => 'Deals This Week',
    'LBL_DEALS_THIS_MONTH' => 'Deals This Month',
    'LBL_TOTAL_PROFIT' => 'Total Profit',
    'LBL_AVERAGE_PROFIT' => 'Average Profit',
    'LBL_PENDING_APPROVALS' => 'Pending Approvals',
    'LBL_FUNDED_TODAY' => 'Funded Today',
    'LBL_TOP_PERFORMERS' => 'Top Performers',
    'LBL_PRODUCT_STATS' => 'Product Statistics',
    
    // Import/Export Labels
    'LBL_IMPORT' => 'Import F&I Deals',
    'LBL_EXPORT' => 'Export F&I Deals',
    'LBL_IMPORT_TEMPLATE' => 'Download Import Template',
    'LBL_EXPORT_SELECTED' => 'Export Selected',
    'LBL_EXPORT_ALL' => 'Export All',
    
    // Validation Messages
    'VAL_REQUIRED_FIELD' => 'This field is required',
    'VAL_INVALID_EMAIL' => 'Please enter a valid email address',
    'VAL_INVALID_NUMBER' => 'Please enter a valid number',
    'VAL_INVALID_DATE' => 'Please enter a valid date',
    'VAL_AMOUNT_TOO_HIGH' => 'Amount is too high',
    'VAL_AMOUNT_TOO_LOW' => 'Amount is too low',
    'VAL_RATE_OUT_OF_RANGE' => 'Interest rate must be between 0% and 50%',
    'VAL_TERM_OUT_OF_RANGE' => 'Loan term must be between 12 and 96 months',
    
    // Confirmation Messages
    'CONF_DELETE_DEAL' => 'Are you sure you want to delete this F&I deal?',
    'CONF_SUBMIT_DEAL' => 'Are you sure you want to submit this deal for approval?',
    'CONF_APPROVE_DEAL' => 'Are you sure you want to approve this deal?',
    'CONF_FUND_DEAL' => 'Are you sure you want to mark this deal as funded?',
    'CONF_COMPLETE_DEAL' => 'Are you sure you want to complete this deal?',
    
    // Workflow Status Messages
    'STATUS_DRAFT' => 'This deal is in draft status',
    'STATUS_SUBMITTED' => 'This deal has been submitted for approval',
    'STATUS_APPROVED' => 'This deal has been approved',
    'STATUS_FUNDED' => 'This deal has been funded',
    'STATUS_COMPLETE' => 'This deal is complete',
    'STATUS_CANCELLED' => 'This deal has been cancelled',
    
    // Currency Formatting
    'LBL_CURRENCY_SYMBOL' => '$',
    'LBL_PERCENTAGE_SYMBOL' => '%',
    'LBL_MONTHS_SUFFIX' => ' months',
    'LBL_YEARS_SUFFIX' => ' years',
    
    // Quick Create Labels
    'LBL_QUICK_CREATE' => 'Quick Create F&I Deal',
    'LBL_FULL_FORM' => 'Full Form',
    'LBL_BASIC_INFO' => 'Basic Information',
    'LBL_REQUIRED_INFO' => 'Required Information',
    
    // Mobile Labels
    'LBL_MOBILE_TITLE' => 'F&I Deals',
    'LBL_MOBILE_SEARCH' => 'Search Deals',
    'LBL_MOBILE_CREATE' => 'New Deal',
    'LBL_MOBILE_CALCULATOR' => 'Calculator',
    
    // Integration Labels
    'LBL_INTEGRATION' => 'Integration',
    'LBL_EXPORT_TO_DMS' => 'Export to DMS',
    'LBL_SYNC_WITH_LENDER' => 'Sync with Lender',
    'LBL_UPDATE_FROM_CREDIT_APP' => 'Update from Credit App',
    
    // Audit Trail Labels
    'LBL_AUDIT_TRAIL' => 'Audit Trail',
    'LBL_CHANGES_MADE' => 'Changes Made',
    'LBL_PREVIOUS_VALUE' => 'Previous Value',
    'LBL_NEW_VALUE' => 'New Value',
    'LBL_CHANGED_BY' => 'Changed By',
    'LBL_CHANGE_DATE' => 'Change Date',
);

// Finance Method Options
$app_list_strings['fi_finance_method_list'] = array(
    'Cash' => 'Cash',
    'Finance' => 'Finance',
    'Lease' => 'Lease',
);

// Deal Status Options
$app_list_strings['fi_deal_status_list'] = array(
    'Draft' => 'Draft',
    'Submitted' => 'Submitted',
    'Approved' => 'Approved',
    'Funded' => 'Funded',
    'Complete' => 'Complete',
    'Cancelled' => 'Cancelled',
);

// Loan Term Options (in months)
$app_list_strings['fi_loan_terms_list'] = array(
    '12' => '12 months (1 year)',
    '24' => '24 months (2 years)',
    '36' => '36 months (3 years)',
    '48' => '48 months (4 years)',
    '60' => '60 months (5 years)',
    '72' => '72 months (6 years)',
    '84' => '84 months (7 years)',
    '96' => '96 months (8 years)',
);

// Module name for module list
$app_list_strings['moduleList']['DM_FIDeals'] = 'F&I Deal Center';
$app_list_strings['moduleListSingular']['DM_FIDeals'] = 'F&I Deal';

// For global search
$app_list_strings['parent_type_display']['DM_FIDeals'] = 'F&I Deal';

// For activities (tasks, calls, meetings, emails)
$app_list_strings['record_type_display']['DM_FIDeals'] = 'F&I Deal';
$app_list_strings['record_type_display_notes']['DM_FIDeals'] = 'F&I Deal';

// For workflows and reports
$app_list_strings['aow_moduleList']['DM_FIDeals'] = 'F&I Deal Center';
$app_list_strings['aor_moduleList']['DM_FIDeals'] = 'F&I Deal Center'; 