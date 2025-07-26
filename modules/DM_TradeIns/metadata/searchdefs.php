<?php
/**
 * SuiteCRM Trade-In Manager - Search Definitions
 * 
 * This file defines the search form layout and available search criteria
 * for the Trade-In Manager module.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$searchdefs['DM_TradeIns'] = array(
    'templateMeta' => array(
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => array(
            'label' => '10',
            'field' => '30'
        ),
    ),
    'layout' => array(
        'basic_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'customer_name' => array(
                'name' => 'customer_name',
                'default' => true,
                'width' => '10%',
            ),
            'vin' => array(
                'name' => 'vin',
                'default' => true,
                'width' => '10%',
            ),
            'status' => array(
                'name' => 'status',
                'default' => true,
                'width' => '10%',
            ),
        ),
        'advanced_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'customer_name' => array(
                'name' => 'customer_name',
                'default' => true,
                'width' => '10%',
            ),
            'opportunity_name' => array(
                'name' => 'opportunity_name',
                'default' => true,
                'width' => '10%',
            ),
            'vin' => array(
                'name' => 'vin',
                'default' => true,
                'width' => '10%',
            ),
            'year' => array(
                'name' => 'year',
                'default' => true,
                'width' => '10%',
            ),
            'make' => array(
                'name' => 'make',
                'default' => true,
                'width' => '10%',
            ),
            'model' => array(
                'name' => 'model',
                'default' => true,
                'width' => '10%',
            ),
            'status' => array(
                'name' => 'status',
                'default' => true,
                'width' => '10%',
            ),
            'condition_overall' => array(
                'name' => 'condition_overall',
                'default' => true,
                'width' => '10%',
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'enum',
                'function' => 'get_user_array',
                'default' => true,
                'width' => '10%',
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'default' => true,
                'width' => '10%',
            ),
            'appraised_value' => array(
                'name' => 'appraised_value',
                'default' => false,
                'width' => '10%',
            ),
            'customer_asking' => array(
                'name' => 'customer_asking',
                'default' => false,
                'width' => '10%',
            ),
            'market_value_trade' => array(
                'name' => 'market_value_trade',
                'default' => false,
                'width' => '10%',
            ),
            'payoff_amount' => array(
                'name' => 'payoff_amount',
                'default' => false,
                'width' => '10%',
            ),
            'used_in_deal' => array(
                'name' => 'used_in_deal',
                'default' => false,
                'width' => '10%',
            ),
        ),
    ),
);
?> 