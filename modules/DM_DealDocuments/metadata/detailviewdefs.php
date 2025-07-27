<?php
/**
 * SuiteCRM Deal Documentation Suite - Detail View Definitions
 * 
 * This file defines the layout and panels for the Deal Documents detail view,
 * organized for efficient document viewing and workflow management.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$viewdefs['DM_DealDocuments']['DetailView'] = array(
    'templateMeta' => array(
        'form' => array(
            'buttons' => array(
                'EDIT',
                'DUPLICATE',
                'DELETE',
                array(
                    'customCode' => '<input type="button" class="button" title="{$MOD.LBL_GENERATE_PDF}" onclick="generateDocumentPDF();" name="generate_pdf" value="{$MOD.LBL_GENERATE_PDF}" {if $fields.pdf_content.value}style="display:none;"{/if}>',
                ),
                array(
                    'customCode' => '<input type="button" class="button" title="{$MOD.LBL_REGENERATE_PDF}" onclick="regenerateDocumentPDF();" name="regenerate_pdf" value="{$MOD.LBL_REGENERATE_PDF}" {if !$fields.pdf_content.value}style="display:none;"{/if}>',
                ),
                array(
                    'customCode' => '<input type="button" class="button" title="{$MOD.LBL_DOWNLOAD_PDF}" onclick="downloadPDF();" name="download_pdf" value="{$MOD.LBL_DOWNLOAD_PDF}" {if !$fields.pdf_content.value}style="display:none;"{/if}>',
                ),
                array(
                    'customCode' => '<input type="button" class="button" title="{$MOD.LBL_SIGN_DOCUMENT}" onclick="showSignatureModal();" name="sign_document" value="{$MOD.LBL_SIGN_DOCUMENT}" {if !$fields.pdf_content.value || $fields.signature_data.value}style="display:none;"{/if}>',
                ),
            ),
        ),
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
            'LBL_PANEL_SIGNATURE' => array(
                'newTab' => false,
                'panelDefault' => 'expanded',
            ),
        ),
    ),
    'panels' => array(
        'LBL_PANEL_DEFAULT' => array(
            array(
                'name',
                'assigned_user_name',
            ),
            array(
                'deal_name',
                'customer_name',
            ),
            array(
                'date_entered',
                'date_modified',
            ),
        ),
        
        'LBL_PANEL_DOCUMENT_DETAILS' => array(
            array(
                'document_type',
                array(
                    'name' => 'document_status',
                    'customCode' => '
                    <span class="badge badge-{$fields.document_status.badge_color}">{$fields.document_status.value}</span>
                    ',
                ),
            ),
            array(
                'template_name',
                'generation_date',
            ),
            array(
                array(
                    'name' => 'notes',
                    'label' => 'LBL_NOTES',
                    'customCode' => '{$fields.notes.value}',
                ),
            ),
        ),
        
        'LBL_PANEL_GENERATION' => array(
            array(
                array(
                    'customCode' => '
                    {if $fields.pdf_content.value}
                    <div style="margin-bottom: 15px;">
                        <div style="margin-bottom: 10px;">
                            <strong>{$MOD.LBL_DOCUMENT_PREVIEW}:</strong>
                            <a href="index.php?module=DM_DealDocuments&action=download_pdf&record={$fields.id.value}" class="button" style="margin-left: 10px;" target="_blank">{$MOD.LBL_DOWNLOAD_PDF}</a>
                        </div>
                        <iframe src="index.php?module=DM_DealDocuments&action=preview_pdf&record={$fields.id.value}" width="100%" height="500px" style="border: 1px solid #ccc;"></iframe>
                    </div>
                    {else}
                    <div style="padding: 20px; text-align: center; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                        <p><em>PDF document has not been generated yet.</em></p>
                        <input type="button" class="button" value="{$MOD.LBL_GENERATE_PDF}" onclick="generateDocumentPDF();">
                    </div>
                    {/if}',
                    'label' => '',
                ),
            ),
        ),
        
        'LBL_PANEL_SIGNATURE' => array(
            array(
                array(
                    'customCode' => '
                    {if $fields.signature_data.value}
                    <div style="margin-bottom: 15px;">
                        <div style="margin-bottom: 10px;">
                            <strong>{$MOD.LBL_SIGNATURE_PREVIEW}:</strong>
                            <span style="margin-left: 15px; color: #28a745;">✓ Document Signed</span>
                        </div>
                        <div id="signature_display" style="border: 1px solid #ccc; padding: 10px; background-color: #f8f9fa;">
                            <img src="data:image/png;base64,{$fields.signature_image.value}" alt="Customer Signature" style="max-width: 300px;">
                            <div style="margin-top: 10px; font-size: 12px; color: #666;">
                                Signed on: {$fields.signature_date.value}
                            </div>
                        </div>
                    </div>
                    {else}
                    <div style="padding: 20px; text-align: center; background-color: #fff3cd; border: 1px solid #ffeaa7;">
                        <p><em>Document has not been signed yet.</em></p>
                        {if $fields.pdf_content.value}
                        <input type="button" class="button" value="{$MOD.LBL_SIGN_DOCUMENT}" onclick="showSignatureModal();">
                        {/if}
                    </div>
                    {/if}',
                    'label' => '',
                ),
            ),
        ),
    ),
);