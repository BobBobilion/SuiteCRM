<?php
/**
 * SuiteCRM Deal Documentation Suite - Edit View Definitions
 * 
 * This file defines the layout and panels for the Deal Documents edit view,
 * organized for efficient document creation and management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_DealDocuments']['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        'includes' => array(
            array(
                'file' => 'modules/DM_DealDocuments/js/DM_DealDocuments.js',
            ),
        ),
        'useTabs' => true,
        'tabDefs' => array(
            'LBL_PANEL_DEFAULT' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_DOCUMENT_DETAILS' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_GENERATION' => array(
                'newTab' => true,
                'panelDefault' => 'expanded',
            ),
            'LBL_PANEL_WORKFLOW' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
        ),
        'form' => array(
            'buttons' => array(
                'SAVE',
                'CANCEL',
                array(
                    'customCode' => '<input type="button" class="button" value="{$MOD.LBL_GENERATE_PDF}" onclick="generateDocumentPDF();" id="generate_pdf_btn">',
                ),
            ),
        ),
    ),
    'panels' => array(
        'LBL_PANEL_DEFAULT' => array(
            array(
                array(
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'displayParams' => array(
                        'required' => false,
                        'size' => 40,
                    ),
                ),
                array(
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'displayParams' => array(
                        'required' => false,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'deal_name',
                    'label' => 'LBL_DEAL_NAME',
                    'displayParams' => array(
                        'required' => true,
                        'call_back_function' => 'populateCustomerFromDeal',
                    ),
                ),
                array(
                    'name' => 'customer_name',
                    'label' => 'LBL_CUSTOMER_NAME',
                    'displayParams' => array(
                        'required' => true,
                    ),
                ),
            ),
        ),
        
        'LBL_PANEL_DOCUMENT_DETAILS' => array(
            array(
                array(
                    'name' => 'document_type',
                    'label' => 'LBL_DOCUMENT_TYPE',
                    'displayParams' => array(
                        'required' => true,
                        'onChange' => 'updateTemplateOptions();',
                    ),
                ),
                array(
                    'name' => 'document_status',
                    'label' => 'LBL_DOCUMENT_STATUS',
                    'displayParams' => array(
                        'required' => false,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'template_name',
                    'label' => 'LBL_TEMPLATE_NAME',
                    'displayParams' => array(
                        'required' => false,
                        'size' => 30,
                    ),
                ),
                array(
                    'name' => 'generation_date',
                    'label' => 'LBL_GENERATION_DATE',
                    'displayParams' => array(
                        'showFormats' => true,
                        'readonly' => true,
                    ),
                ),
            ),
            array(
                array(
                    'name' => 'notes',
                    'label' => 'LBL_NOTES',
                    'displayParams' => array(
                        'rows' => 4,
                        'cols' => 60,
                    ),
                ),
            ),
        ),
        
        'LBL_PANEL_GENERATION' => array(
            array(
                array(
                    'customCode' => '
                    <div id="pdf_preview_container" style="display:none;">
                        <div style="margin-bottom: 10px;">
                            <strong>{$MOD.LBL_DOCUMENT_PREVIEW}:</strong>
                            <input type="button" class="button" value="{$MOD.LBL_DOWNLOAD_PDF}" onclick="downloadPDF();" style="margin-left: 10px;">
                        </div>
                        <iframe id="pdf_preview" src="" width="100%" height="400px" style="border: 1px solid #ccc;"></iframe>
                    </div>
                    <div id="pdf_generation_message" style="display:none; padding: 10px; margin: 10px 0; background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d;">
                        PDF document has been generated successfully.
                    </div>',
                    'label' => '',
                ),
            ),
        ),
        
        'LBL_PANEL_WORKFLOW' => array(
            array(
                array(
                    'customCode' => '
                    <div id="signature_section" style="display:none;">
                        <div style="margin-bottom: 10px;">
                            <strong>{$MOD.LBL_SIGN_DOCUMENT}:</strong>
                        </div>
                        <canvas id="signature_pad" width="400" height="200" style="border: 1px solid #000; background-color: white;"></canvas>
                        <div style="margin-top: 10px;">
                            <input type="button" class="button" value="Clear" onclick="clearSignature();">
                            <input type="button" class="button" value="Save Signature" onclick="saveSignature();">
                        </div>
                    </div>',
                    'label' => '',
                ),
            ),
        ),
    ),
);