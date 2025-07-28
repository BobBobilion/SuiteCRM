{*
 * SuiteCRM Trade-In Manager - Edit View Header Template
 * 
 * This template provides the header section for the Trade-In Manager edit view
 *}
<div class="moduleTitle">
    <h2>
        <span class="module-icon">
            <img src="{sugar_getimagepath file='icon_DM_TradeIns_32.png'}" alt="{$MOD.LBL_MODULE_NAME}" />
        </span>
        {$MOD.LBL_MODULE_NAME}
        {if $RECORD}
            <small>- {$MOD.LBL_EDIT_FORM_TITLE}</small>
        {else}
            <small>- {$MOD.LBL_NEW_FORM_TITLE}</small>
        {/if}
    </h2>
</div>

<div class="clear"></div>

{if $RECORD}
    <div class="edit-view-header-info">
        <div class="row">
            <div class="col-sm-6">
                <strong>{$MOD.LBL_CUSTOMER_NAME}:</strong> {$CUSTOMER_NAME}
            </div>
            <div class="col-sm-6">
                <strong>{$MOD.LBL_APPRAISAL_DATE}:</strong> {$APPRAISAL_DATE}
            </div>
        </div>
    </div>
{/if}