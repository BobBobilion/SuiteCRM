<?php
/**
 * SuiteCRM Deal Documentation Suite - List View Definitions
 * 
 * This file defines the layout and columns for the Deal Documents list view.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$listViewDefs['DM_DealDocuments'] = array(
    'NAME' => array(
        'width' => '25%',
        'label' => 'LBL_NAME',
        'default' => true,
        'link' => true,
    ),
    'DOCUMENT_TYPE' => array(
        'type' => 'enum',
        'studio' => 'visible',
        'label' => 'LBL_DOCUMENT_TYPE',
        'width' => '15%',
        'default' => true,
    ),
    'DOCUMENT_STATUS' => array(
        'type' => 'enum',
        'studio' => 'visible',
        'label' => 'LBL_DOCUMENT_STATUS',
        'width' => '10%',
        'default' => true,
    ),
    'DEAL_NAME' => array(
        'type' => 'relate',
        'studio' => 'visible',
        'label' => 'LBL_DEAL_NAME',
        'id' => 'DEAL_ID',
        'link' => true,
        'width' => '15%',
        'default' => true,
    ),
    'CUSTOMER_NAME' => array(
        'type' => 'relate',
        'studio' => 'visible',
        'label' => 'LBL_CUSTOMER_NAME',
        'id' => 'CUSTOMER_ID',
        'link' => true,
        'width' => '15%',
        'default' => true,
    ),
    'GENERATION_DATE' => array(
        'type' => 'datetime',
        'label' => 'LBL_GENERATION_DATE',
        'width' => '10%',
        'default' => true,
    ),
    'ASSIGNED_USER_NAME' => array(
        'width' => '9%',
        'label' => 'LBL_ASSIGNED_TO_NAME',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true,
    ),
    'DATE_ENTERED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_ENTERED',
        'width' => '10%',
        'default' => false,
    ),
    'DATE_MODIFIED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_MODIFIED',
        'width' => '10%',
        'default' => false,
    ),
    'TEMPLATE_NAME' => array(
        'type' => 'varchar',
        'label' => 'LBL_TEMPLATE_NAME',
        'width' => '10%',
        'default' => false,
    ),
);