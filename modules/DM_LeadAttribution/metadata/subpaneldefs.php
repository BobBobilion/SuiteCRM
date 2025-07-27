<?php
/**
 * SuiteCRM Lead Attribution Center - Subpanel Definitions
 * 
 * This file defines how Lead Attribution records appear in subpanels
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$layout_defs['DM_LeadAttribution'] = array(
    'subpanel_setup' => array(
        'default' => array(
            'order' => 100,
            'module' => 'DM_LeadAttribution',
            'sort_order' => 'desc',
            'sort_by' => 'date_entered',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'function:get_lead_attribution_subpanel_data',
            'add_subpanel_data' => 'lead_id',
            'title_key' => 'LBL_LEAD_ATTRIBUTION_SUBPANEL_TITLE',
            'top_buttons' => array(
                0 => array(
                    'widget_class' => 'SubPanelTopCreateButton',
                ),
                1 => array(
                    'widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'DM_LeadAttribution',
                ),
            ),
        ),
    ),
);

// Subpanel definition for display in other modules
$subpanel_layout['list_fields'] = array(
    'name' => array(
        'vname' => 'LBL_NAME',
        'widget_class' => 'SubPanelDetailViewLink',
        'width' => '20%',
        'default' => true,
    ),
    'first_touch_source' => array(
        'type' => 'varchar',
        'vname' => 'LBL_FIRST_TOUCH_SOURCE',
        'width' => '12%',
        'default' => true,
    ),
    'first_touch_campaign' => array(
        'type' => 'varchar',
        'vname' => 'LBL_FIRST_TOUCH_CAMPAIGN',
        'width' => '15%',
        'default' => true,
    ),
    'utm_source' => array(
        'type' => 'varchar',
        'vname' => 'LBL_UTM_SOURCE',
        'width' => '10%',
        'default' => true,
    ),
    'utm_campaign' => array(
        'type' => 'varchar',
        'vname' => 'LBL_UTM_CAMPAIGN',
        'width' => '12%',
        'default' => false,
    ),
    'conversion_value' => array(
        'type' => 'currency',
        'vname' => 'LBL_CONVERSION_VALUE',
        'width' => '10%',
        'default' => true,
    ),
    'roi_percentage' => array(
        'type' => 'decimal',
        'vname' => 'LBL_ROI_PERCENTAGE',
        'width' => '8%',
        'default' => true,
    ),
    'attribution_model' => array(
        'type' => 'enum',
        'vname' => 'LBL_ATTRIBUTION_MODEL',
        'width' => '10%',
        'default' => false,
    ),
    'date_entered' => array(
        'type' => 'datetime',
        'vname' => 'LBL_DATE_ENTERED',
        'width' => '10%',
        'default' => true,
    ),
    'edit_button' => array(
        'vname' => 'LBL_EDIT_BUTTON',
        'widget_class' => 'SubPanelEditButton',
        'module' => 'DM_LeadAttribution',
        'width' => '4%',
        'default' => true,
    ),
    'remove_button' => array(
        'vname' => 'LBL_REMOVE',
        'widget_class' => 'SubPanelRemoveButton',
        'module' => 'DM_LeadAttribution',
        'width' => '5%',
        'default' => true,
    ),
);
?>