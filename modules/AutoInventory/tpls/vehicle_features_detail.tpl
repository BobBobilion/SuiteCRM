{* SuiteCRM AutoInventory Vehicle Features Detail Template *}
<div id="vehicle_features_detail_container">
    {if $fields.vehicle_features.value && $fields.vehicle_features.value != '[]'}
        {assign var="features" value=$fields.vehicle_features.value|@json_decode:true}
        {if $features && is_array($features) && count($features) > 0}
            <div class="features-display-grid">
                {foreach from=$features item=feature}
                    <span class="feature-badge" style="display: inline-block; background: #007cba; color: white; 
                                                      padding: 6px 12px; margin: 3px; border-radius: 6px; 
                                                      font-size: 13px; font-weight: 500;">
                        {$feature}
                    </span>
                {/foreach}
            </div>
            <div style="margin-top: 15px; color: #666; font-size: 12px;">
                <strong>Total Features:</strong> {count($features)}
            </div>
        {else}
            <span style="color: #999; font-style: italic;">No features specified</span>
        {/if}
    {else}
        <span style="color: #999; font-style: italic;">No features specified</span>
    {/if}
</div>

<style type="text/css">
{literal}
.features-display-grid {
    line-height: 1.8;
}

.feature-badge {
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.feature-badge:hover {
    background: #005a8b !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Different colors for different types of features */
.feature-badge:nth-child(3n+1) { background: #007cba; }
.feature-badge:nth-child(3n+2) { background: #28a745; }
.feature-badge:nth-child(3n+3) { background: #dc3545; }
{/literal}
</style> 