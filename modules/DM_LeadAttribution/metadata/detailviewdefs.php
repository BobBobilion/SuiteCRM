<?php
/**
 * SuiteCRM Lead Attribution Center - DetailView Metadata
 * 
 * Defines the layout and fields for the DetailView of Lead Attribution records
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_LeadAttribution']['DetailView'] = array(
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
            'LBL_PANEL_JOURNEY_ANALYSIS' => array(
                'newTab' => true,
                'panelDefault' => 'collapsed',
            ),
            'LBL_PANEL_EXTERNAL_INTEGRATION' => array(
                'newTab' => true,
                'panelDefault' => 'collapsed',
            ),
        ),
        'form' => array(
            'buttons' => array(
                'EDIT',
                'DUPLICATE',
                'DELETE',
                array(
                    'customCode' => '<input title="{$MOD.LBL_UPDATE_ATTRIBUTION}" class="button" onclick="updateAttribution(\'{$fields.id.value}\');" type="button" name="update_attribution" value="{$MOD.LBL_UPDATE_ATTRIBUTION}">',
                ),
                array(
                    'customCode' => '<input title="{$MOD.LBL_CALCULATE_ROI}" class="button" onclick="calculateROI(\'{$fields.id.value}\');" type="button" name="calculate_roi" value="{$MOD.LBL_CALCULATE_ROI}">',
                ),
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
                array(
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                ),
            ),
            array(
                array(
                    'name' => 'created_by_name',
                    'label' => 'LBL_CREATED',
                ),
                array(
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                ),
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
                    'type' => 'url',
                ),
            ),
            array(
                array(
                    'name' => 'landing_page',
                    'label' => 'LBL_LANDING_PAGE',
                ),
            ),
        ),
        
        'LBL_PANEL_ROI_ANALYSIS' => array(
            array(
                array(
                    'name' => 'conversion_value',
                    'label' => 'LBL_CONVERSION_VALUE',
                    'customCode' => '{$fields.conversion_value.value|number_format:2}',
                ),
                array(
                    'name' => 'total_cost',
                    'label' => 'LBL_TOTAL_COST',
                    'customCode' => '{$fields.total_cost.value|number_format:2}',
                ),
            ),
            array(
                array(
                    'name' => 'roi_percentage',
                    'label' => 'LBL_ROI_PERCENTAGE',
                    'customCode' => '<span class="roi-indicator {if $fields.roi_percentage.value > 0}positive{elseif $fields.roi_percentage.value < 0}negative{else}neutral{/if}">{$fields.roi_percentage.value}%</span>',
                ),
                '',
            ),
        ),
        
        'LBL_PANEL_JOURNEY_ANALYSIS' => array(
            array(
                array(
                    'name' => 'channel_journey',
                    'label' => 'LBL_CHANNEL_JOURNEY',
                    'customCode' => '<div id="journey-visualization">{$JOURNEY_VISUALIZATION}</div>',
                ),
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
                    'customCode' => '<pre>{$fields.external_ids.value|escape:"html"}</pre>',
                ),
            ),
        ),
        
        'LBL_PANEL_DEFAULT' => array(
            array(
                array(
                    'name' => 'notes',
                    'label' => 'LBL_NOTES',
                ),
            ),
        ),
    ),
);