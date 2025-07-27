<?php
/**
 * SuiteCRM Lead Attribution Center - Search Form Metadata
 * 
 * Defines the search form layout for Lead Attribution records
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$searchdefs['DM_LeadAttribution'] = array(
    'templateMeta' => array(
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => array('label' => '10', 'field' => '30'),
    ),
    'layout' => array(
        'basic_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%',
            ),
            'lead_name' => array(
                'name' => 'lead_name',
                'default' => true,
                'width' => '10%',
            ),
            'first_touch_source' => array(
                'name' => 'first_touch_source',
                'default' => true,
                'width' => '10%',
            ),
            'utm_campaign' => array(
                'name' => 'utm_campaign',
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
            'lead_name' => array(
                'name' => 'lead_name',
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
            'first_touch_source' => array(
                'name' => 'first_touch_source',
                'default' => true,
                'width' => '10%',
            ),
            'first_touch_medium' => array(
                'name' => 'first_touch_medium',
                'default' => true,
                'width' => '10%',
            ),
            'first_touch_campaign' => array(
                'name' => 'first_touch_campaign',
                'default' => true,
                'width' => '10%',
            ),
            'utm_source' => array(
                'name' => 'utm_source',
                'default' => true,
                'width' => '10%',
            ),
            'utm_medium' => array(
                'name' => 'utm_medium',
                'default' => true,
                'width' => '10%',
            ),
            'utm_campaign' => array(
                'name' => 'utm_campaign',
                'default' => true,
                'width' => '10%',
            ),
            'attribution_model' => array(
                'name' => 'attribution_model',
                'default' => true,
                'width' => '10%',
            ),
            'conversion_value' => array(
                'name' => 'conversion_value',
                'default' => true,
                'width' => '10%',
            ),
            'roi_percentage' => array(
                'name' => 'roi_percentage',
                'default' => true,
                'width' => '10%',
            ),
            'touch_count' => array(
                'name' => 'touch_count',
                'default' => true,
                'width' => '10%',
            ),
            'first_touch_date' => array(
                'name' => 'first_touch_date',
                'default' => true,
                'width' => '10%',
            ),
            'last_touch_date' => array(
                'name' => 'last_touch_date',
                'default' => true,
                'width' => '10%',
            ),
            'ga_client_id' => array(
                'name' => 'ga_client_id',
                'default' => true,
                'width' => '10%',
            ),
            'fb_lead_id' => array(
                'name' => 'fb_lead_id',
                'default' => true,
                'width' => '10%',
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'enum',
                'label' => 'LBL_ASSIGNED_TO',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'default' => true,
                'width' => '10%',
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'default' => true,
                'width' => '10%',
            ),
            'date_modified' => array(
                'name' => 'date_modified',
                'default' => true,
                'width' => '10%',
            ),
            'created_by' => array(
                'name' => 'created_by',
                'type' => 'enum',
                'label' => 'LBL_CREATED',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'default' => true,
                'width' => '10%',
            ),
            'modified_user_id' => array(
                'name' => 'modified_user_id',
                'type' => 'enum',
                'label' => 'LBL_MODIFIED',
                'function' => array(
                    'name' => 'get_user_array',
                    'params' => array(false),
                ),
                'default' => true,
                'width' => '10%',
            ),
        ),
    ),
);
?>