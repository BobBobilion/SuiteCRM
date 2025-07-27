<?php
/**
 * SuiteCRM Deal Documentation Suite - Search View Definitions
 * 
 * This file defines the search fields and layout for the Deal Documents module.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$searchdefs['DM_DealDocuments'] = array(
    'layout' => array(
        'basic_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'document_type' => array(
                'name' => 'document_type',
                'type' => 'enum',
                'options' => 'document_type_list',
                'default' => true,
                'width' => '10%',
            ),
            'document_status' => array(
                'name' => 'document_status',
                'type' => 'enum',
                'options' => 'document_status_list',
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
            'document_type' => array(
                'name' => 'document_type',
                'type' => 'enum',
                'options' => 'document_type_list',
                'default' => true,
                'width' => '10%',
            ),
            'document_status' => array(
                'name' => 'document_status',
                'type' => 'enum',
                'options' => 'document_status_list',
                'default' => true,
                'width' => '10%',
            ),
            'deal_name' => array(
                'name' => 'deal_name',
                'type' => 'relate',
                'label' => 'LBL_DEAL_NAME',
                'width' => '10%',
                'default' => true,
            ),
            'customer_name' => array(
                'name' => 'customer_name',
                'type' => 'relate',
                'label' => 'LBL_CUSTOMER_NAME',
                'width' => '10%',
                'default' => true,
            ),
            'generation_date' => array(
                'name' => 'generation_date',
                'type' => 'datetime',
                'label' => 'LBL_GENERATION_DATE',
                'width' => '10%',
                'default' => true,
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'enum',
                'label' => 'LBL_ASSIGNED_TO',
                'function' => 'get_user_array',
                'default' => true,
                'width' => '10%',
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'type' => 'datetime',
                'label' => 'LBL_DATE_ENTERED',
                'width' => '10%',
                'default' => false,
            ),
            'date_modified' => array(
                'name' => 'date_modified',
                'type' => 'datetime',
                'label' => 'LBL_DATE_MODIFIED',
                'width' => '10%',
                'default' => false,
            ),
        ),
    ),
    'templateMeta' => array(
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => array(
            'label' => '10',
            'field' => '30',
        ),
    ),
);