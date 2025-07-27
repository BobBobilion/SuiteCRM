<?php
/**
 * SuiteCRM Lead Attribution Center - ListView Metadata
 * 
 * Defines the layout and fields for the ListView of Lead Attribution records
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$listViewDefs['DM_LeadAttribution'] = array(
    'NAME' => array(
        'width' => '20%',
        'label' => 'LBL_NAME',
        'default' => true,
        'link' => true,
    ),
    'LEAD_NAME' => array(
        'type' => 'relate',
        'studio' => 'visible',
        'label' => 'LBL_LEAD_NAME',
        'id' => 'LEAD_ID',
        'link' => true,
        'width' => '15%',
        'default' => true,
    ),
    'FIRST_TOUCH_SOURCE' => array(
        'type' => 'varchar',
        'label' => 'LBL_FIRST_TOUCH_SOURCE',
        'width' => '10%',
        'default' => true,
    ),
    'FIRST_TOUCH_CAMPAIGN' => array(
        'type' => 'varchar',
        'label' => 'LBL_FIRST_TOUCH_CAMPAIGN',
        'width' => '15%',
        'default' => true,
    ),
    'UTM_SOURCE' => array(
        'type' => 'varchar',
        'label' => 'LBL_UTM_SOURCE',
        'width' => '10%',
        'default' => true,
    ),
    'UTM_CAMPAIGN' => array(
        'type' => 'varchar',
        'label' => 'LBL_UTM_CAMPAIGN',
        'width' => '12%',
        'default' => false,
    ),
    'CONVERSION_VALUE' => array(
        'type' => 'currency',
        'label' => 'LBL_CONVERSION_VALUE',
        'currency_format' => true,
        'width' => '10%',
        'default' => true,
    ),
    'ROI_PERCENTAGE' => array(
        'type' => 'decimal',
        'label' => 'LBL_ROI_PERCENTAGE',
        'width' => '8%',
        'default' => true,
        'customCode' => '{$ROI_PERCENTAGE}%',
    ),
    'TOUCH_COUNT' => array(
        'type' => 'int',
        'label' => 'LBL_TOUCH_COUNT',
        'width' => '5%',
        'default' => false,
    ),
    'ATTRIBUTION_MODEL' => array(
        'type' => 'enum',
        'studio' => 'visible',
        'label' => 'LBL_ATTRIBUTION_MODEL',
        'width' => '10%',
        'default' => false,
    ),
    'FIRST_TOUCH_DATE' => array(
        'type' => 'datetime',
        'label' => 'LBL_FIRST_TOUCH_DATE',
        'width' => '10%',
        'default' => false,
    ),
    'LAST_TOUCH_DATE' => array(
        'type' => 'datetime',
        'label' => 'LBL_LAST_TOUCH_DATE',
        'width' => '10%',
        'default' => false,
    ),
    'CUSTOMER_NAME' => array(
        'type' => 'relate',
        'studio' => 'visible',
        'label' => 'LBL_CUSTOMER_NAME',
        'id' => 'CUSTOMER_ID',
        'link' => true,
        'width' => '15%',
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
    'TOTAL_COST' => array(
        'type' => 'currency',
        'label' => 'LBL_TOTAL_COST',
        'currency_format' => true,
        'width' => '10%',
        'default' => false,
    ),
    'UTM_MEDIUM' => array(
        'type' => 'varchar',
        'label' => 'LBL_UTM_MEDIUM',
        'width' => '10%',
        'default' => false,
    ),
    'UTM_CONTENT' => array(
        'type' => 'varchar',
        'label' => 'LBL_UTM_CONTENT',
        'width' => '12%',
        'default' => false,
    ),
    'UTM_TERM' => array(
        'type' => 'varchar',
        'label' => 'LBL_UTM_TERM',
        'width' => '10%',
        'default' => false,
    ),
    'FIRST_TOUCH_MEDIUM' => array(
        'type' => 'varchar',
        'label' => 'LBL_FIRST_TOUCH_MEDIUM',
        'width' => '10%',
        'default' => false,
    ),
    'LAST_TOUCH_SOURCE' => array(
        'type' => 'varchar',
        'label' => 'LBL_LAST_TOUCH_SOURCE',
        'width' => '10%',
        'default' => false,
    ),
    'LAST_TOUCH_MEDIUM' => array(
        'type' => 'varchar',
        'label' => 'LBL_LAST_TOUCH_MEDIUM',
        'width' => '10%',
        'default' => false,
    ),
    'LAST_TOUCH_CAMPAIGN' => array(
        'type' => 'varchar',
        'label' => 'LBL_LAST_TOUCH_CAMPAIGN',
        'width' => '15%',
        'default' => false,
    ),
    'GA_CLIENT_ID' => array(
        'type' => 'varchar',
        'label' => 'LBL_GA_CLIENT_ID',
        'width' => '12%',
        'default' => false,
    ),
    'FB_LEAD_ID' => array(
        'type' => 'varchar',
        'label' => 'LBL_FB_LEAD_ID',
        'width' => '12%',
        'default' => false,
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
        'default' => true,
    ),
    'DATE_MODIFIED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_MODIFIED',
        'width' => '10%',
        'default' => false,
    ),
    'CREATED_BY_NAME' => array(
        'type' => 'relate',
        'label' => 'LBL_CREATED',
        'width' => '10%',
        'default' => false,
    ),
    'MODIFIED_BY_NAME' => array(
        'type' => 'relate',
        'label' => 'LBL_MODIFIED',
        'width' => '10%',
        'default' => false,
    ),
);
?>