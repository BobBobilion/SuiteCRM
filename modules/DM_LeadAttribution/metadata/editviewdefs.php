<?php
/**
 * SuiteCRM Lead Attribution Center - EditView Metadata
 * 
 * Defines the layout and fields for the EditView of Lead Attribution records
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_LeadAttribution']['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            0 => array('label' => '10', 'field' => '30'),
            1 => array('label' => '10', 'field' => '30')
        ),
        'javascript' => '<script type="text/javascript" src="modules/DM_LeadAttribution/js/DM_LeadAttribution.js"></script>',
        'useTabs' => true,
        'tabDefs' => array(
            'LBL_PANEL_ATTRIBUTION_INFO' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_FIRST_TOUCH' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_LAST_TOUCH' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_UTM_PARAMETERS' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_ROI_ANALYSIS' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_EXTERNAL_INTEGRATION' => array(
                'newTab' => true,
                'panelDefault' => 'collapsed',
            ),
        ),
    ),
    'panels' => array(
        'LBL_PANEL_ATTRIBUTION_INFO' => array(
            array(
                'name',
                array(
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                ),
            ),
            array(
                array(
                    'name' => 'lead_name',
                    'label' => 'LBL_LEAD_NAME',
                ),
                array(
                    'name' => 'customer_name',
                    'label' => 'LBL_CUSTOMER_NAME',
                ),
            ),
            array(
                array(
                    'name' => 'opportunity_name',
                    'label' => 'LBL_OPPORTUNITY_NAME',
                ),
                array(
                    'name' => 'attribution_model',
                    'label' => 'LBL_ATTRIBUTION_MODEL',
                ),
            ),
            array(
                array(
                    'name' => 'touch_count',
                    'label' => 'LBL_TOUCH_COUNT',
                ),
                '',
            ),
        ),
        
        'LBL_PANEL_FIRST_TOUCH' => array(
            array(
                array(
                    'name' => 'first_touch_source',
                    'label' => 'LBL_FIRST_TOUCH_SOURCE',
                ),
                array(
                    'name' => 'first_touch_medium',
                    'label' => 'LBL_FIRST_TOUCH_MEDIUM',
                ),
            ),
            array(
                array(
                    'name' => 'first_touch_campaign',
                    'label' => 'LBL_FIRST_TOUCH_CAMPAIGN',
                    'displayParams' => array('size' => 30),
                ),
                array(
                    'name' => 'first_touch_date',
                    'label' => 'LBL_FIRST_TOUCH_DATE',
                ),
            ),
        ),
        
        'LBL_PANEL_LAST_TOUCH' => array(
            array(
                array(
                    'name' => 'last_touch_source',
                    'label' => 'LBL_LAST_TOUCH_SOURCE',
                ),
                array(
                    'name' => 'last_touch_medium',
                    'label' => 'LBL_LAST_TOUCH_MEDIUM',
                ),
            ),
            array(
                array(
                    'name' => 'last_touch_campaign',
                    'label' => 'LBL_LAST_TOUCH_CAMPAIGN',
                    'displayParams' => array('size' => 30),
                ),
                array(
                    'name' => 'last_touch_date',
                    'label' => 'LBL_LAST_TOUCH_DATE',
                ),
            ),
        ),
        
        'LBL_PANEL_UTM_PARAMETERS' => array(
            array(
                array(
                    'name' => 'utm_source',
                    'label' => 'LBL_UTM_SOURCE',
                ),
                array(
                    'name' => 'utm_medium',
                    'label' => 'LBL_UTM_MEDIUM',
                ),
            ),
            array(
                array(
                    'name' => 'utm_campaign',
                    'label' => 'LBL_UTM_CAMPAIGN',
                    'displayParams' => array('size' => 30),
                ),
                array(
                    'name' => 'utm_content',
                    'label' => 'LBL_UTM_CONTENT',
                ),
            ),
            array(
                array(
                    'name' => 'utm_term',
                    'label' => 'LBL_UTM_TERM',
                ),
                '',
            ),
            array(
                array(
                    'name' => 'referrer_url',
                    'label' => 'LBL_REFERRER_URL',
                    'type' => 'text',
                    'displayParams' => array('rows' => 2, 'cols' => 50),
                ),
            ),
            array(
                array(
                    'name' => 'landing_page',
                    'label' => 'LBL_LANDING_PAGE',
                    'type' => 'text',
                    'displayParams' => array('rows' => 2, 'cols' => 50),
                ),
            ),
        ),
        
        'LBL_PANEL_ROI_ANALYSIS' => array(
            array(
                array(
                    'name' => 'conversion_value',
                    'label' => 'LBL_CONVERSION_VALUE',
                ),
                array(
                    'name' => 'total_cost',
                    'label' => 'LBL_TOTAL_COST',
                ),
            ),
            array(
                array(
                    'name' => 'roi_percentage',
                    'label' => 'LBL_ROI_PERCENTAGE',
                    'customCode' => '{$fields.roi_percentage.value}%',
                ),
                '',
            ),
        ),
        
        'LBL_PANEL_EXTERNAL_INTEGRATION' => array(
            array(
                array(
                    'name' => 'ga_client_id',
                    'label' => 'LBL_GA_CLIENT_ID',
                ),
                array(
                    'name' => 'ga_session_id',
                    'label' => 'LBL_GA_SESSION_ID',
                ),
            ),
            array(
                array(
                    'name' => 'fb_lead_id',
                    'label' => 'LBL_FB_LEAD_ID',
                ),
                array(
                    'name' => 'fb_form_id',
                    'label' => 'LBL_FB_FORM_ID',
                ),
            ),
            array(
                array(
                    'name' => 'fb_ad_id',
                    'label' => 'LBL_FB_AD_ID',
                ),
                '',
            ),
            array(
                array(
                    'name' => 'external_ids',
                    'label' => 'LBL_EXTERNAL_IDS',
                    'type' => 'text',
                    'displayParams' => array('rows' => 3, 'cols' => 50),
                ),
            ),
            array(
                array(
                    'name' => 'channel_journey',
                    'label' => 'LBL_CHANNEL_JOURNEY',
                    'type' => 'text',
                    'displayParams' => array('rows' => 4, 'cols' => 50),
                ),
            ),
        ),
        
        'LBL_PANEL_DEFAULT' => array(
            array(
                array(
                    'name' => 'notes',
                    'label' => 'LBL_NOTES',
                    'type' => 'text',
                    'displayParams' => array('rows' => 4, 'cols' => 50),
                ),
            ),
        ),
    ),
);