<?php
/**
 * SuiteCRM Trade-In Manager - List View Definitions
 * 
 * This file defines the columns and layout for the Trade-In Manager list view,
 * displaying key trade-in information in a tabular format for easy browsing.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$listViewDefs['DM_TradeIns'] = array(
    'NAME' => array(
        'width' => '20%',
        'label' => 'LBL_LIST_NAME',
        'default' => true,
        'link' => true
    ),
    'CUSTOMER_NAME' => array(
        'type' => 'relate',
        'studio' => 'visible',
        'label' => 'LBL_LIST_CUSTOMER_NAME',
        'id' => 'CUSTOMER_ID',
        'link' => true,
        'width' => '15%',
        'default' => true,
    ),
    'YEAR' => array(
        'type' => 'int',
        'label' => 'LBL_LIST_YEAR',
        'width' => '5%',
        'default' => true,
    ),
    'MAKE' => array(
        'type' => 'varchar',
        'label' => 'LBL_LIST_MAKE',
        'width' => '10%',
        'default' => true,
    ),
    'MODEL' => array(
        'type' => 'varchar',
        'label' => 'LBL_LIST_MODEL',
        'width' => '10%',
        'default' => true,
    ),
    'MILEAGE' => array(
        'type' => 'int',
        'label' => 'LBL_LIST_MILEAGE',
        'width' => '8%',
        'default' => true,
    ),
    'STATUS' => array(
        'type' => 'enum',
        'studio' => 'visible',
        'label' => 'LBL_LIST_STATUS',
        'width' => '8%',
        'default' => true,
    ),
    'APPRAISED_VALUE' => array(
        'type' => 'currency',
        'label' => 'LBL_LIST_APPRAISED_VALUE',
        'currency_format' => true,
        'width' => '10%',
        'default' => true,
    ),
    'CUSTOMER_ASKING' => array(
        'type' => 'currency',
        'label' => 'LBL_LIST_CUSTOMER_ASKING',
        'currency_format' => true,
        'width' => '10%',
        'default' => false,
    ),
    'MARKET_VALUE_TRADE' => array(
        'type' => 'currency',
        'label' => 'LBL_LIST_MARKET_VALUE_TRADE',
        'currency_format' => true,
        'width' => '10%',
        'default' => false,
    ),
    'ASSIGNED_USER_NAME' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true,
    ),
    'DATE_ENTERED' => array(
        'type' => 'datetime',
        'label' => 'LBL_LIST_DATE_ENTERED',
        'width' => '10%',
        'default' => false,
    ),
    'VIN' => array(
        'type' => 'varchar',
        'label' => 'LBL_VIN',
        'width' => '10%',
        'default' => false,
    ),
    'CONDITION_OVERALL' => array(
        'type' => 'enum',
        'studio' => 'visible',
        'label' => 'LBL_CONDITION_OVERALL',
        'width' => '8%',
        'default' => false,
    ),
    'PAYOFF_AMOUNT' => array(
        'type' => 'currency',
        'label' => 'LBL_PAYOFF_AMOUNT',
        'currency_format' => true,
        'width' => '10%',
        'default' => false,
    ),
    'OPPORTUNITY_NAME' => array(
        'type' => 'relate',
        'studio' => 'visible',
        'label' => 'LBL_OPPORTUNITY_NAME',
        'id' => 'OPPORTUNITY_ID',
        'link' => true,
        'width' => '15%',
        'default' => false,
    ),
    'APPRAISAL_DATE' => array(
        'type' => 'datetime',
        'label' => 'LBL_APPRAISAL_DATE',
        'width' => '10%',
        'default' => false,
    ),
    'USED_IN_DEAL' => array(
        'type' => 'bool',
        'label' => 'LBL_USED_IN_DEAL',
        'width' => '5%',
        'default' => false,
    ),
);
?> 