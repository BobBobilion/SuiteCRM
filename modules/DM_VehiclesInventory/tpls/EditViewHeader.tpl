{*
 * SuiteCRM Vehicle Inventory System - Edit View Header Template
 *
 * This template provides an enhanced header for the vehicle inventory edit form
 *}

<div class="vehicle-edit-header">
    <h1 class="module-title">
        {if $fields.id.value}
            {$MOD.LBL_EDIT_VEHICLE}: {$fields.name.value|default:''}
        {else}
            {$MOD.LBL_ADD_VEHICLE}
        {/if}
    </h1>
    
    {if $fields.id.value}
    <div class="vehicle-info-bar">
        <span class="info-item">
            <i class="fa fa-tag"></i> {$MOD.LBL_STOCK_NUMBER}: <strong>{$fields.stock_number.value|default:'N/A'}</strong>
        </span>
        <span class="info-item">
            <i class="fa fa-barcode"></i> {$MOD.LBL_VIN}: <strong>{$fields.vin.value|default:'N/A'}</strong>
        </span>
        <span class="info-item">
            <i class="fa fa-calendar"></i> {$MOD.LBL_DAYS_ON_LOT}: <strong>{$fields.days_on_lot.value|default:'0'}</strong>
        </span>
    </div>
    {/if}
</div>

<style>
.vehicle-edit-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px 8px 0 0;
    margin: -20px -20px 20px -20px;
}

.module-title {
    margin: 0;
    font-size: 24px;
    font-weight: 300;
}

.vehicle-info-bar {
    margin-top: 10px;
    display: flex;
    gap: 20px;
    font-size: 14px;
    opacity: 0.9;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.info-item i {
    opacity: 0.7;
}
</style> 