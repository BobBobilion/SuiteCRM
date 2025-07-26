<?php
/**
 * SuiteCRM Trade-In Manager - Dashlet Data Configuration
 * 
 * This file defines the search fields and columns for the Trade-In dashlet.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $dashletData;

$dashletData['DM_TradeInsDashlet']['searchFields'] = array(
    'name' => array(
        'default' => ''
    ),
    'customer_name' => array(
        'default' => ''
    ),
    'status' => array(
        'default' => ''
    ),
    'assigned_user_id' => array(
        'default' => '',
        'type' => 'assigned_user_name'
    ),
    'date_entered' => array(
        'default' => ''
    ),
);

$dashletData['DM_TradeInsDashlet']['columns'] = array(
    'name' => array(
        'width' => '25%',
        'label' => 'LBL_LIST_NAME',
        'link' => true,
        'default' => true
    ),
    'customer_name' => array(
        'width' => '20%',
        'label' => 'LBL_LIST_CUSTOMER_NAME',
        'link' => true,
        'related_fields' => array('customer_id'),
        'default' => true
    ),
    'year' => array(
        'width' => '8%',
        'label' => 'LBL_LIST_YEAR',
        'default' => true
    ),
    'make' => array(
        'width' => '12%',
        'label' => 'LBL_LIST_MAKE',
        'default' => true
    ),
    'model' => array(
        'width' => '12%',
        'label' => 'LBL_LIST_MODEL',
        'default' => true
    ),
    'status' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_STATUS',
        'default' => true
    ),
    'appraised_value' => array(
        'width' => '13%',
        'label' => 'LBL_LIST_APPRAISED_VALUE',
        'default' => false
    ),
    'date_entered' => array(
        'width' => '15%',
        'label' => 'LBL_LIST_DATE_ENTERED',
        'default' => false
    ),
);
?> 