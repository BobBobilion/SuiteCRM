{*
 * SuiteCRM Vehicle Inventory System - Features List Template
 *
 * This template displays vehicle features in an organized grid with management functionality.
 * Features are displayed in categories with visual icons and editing capabilities.
 *}

<div class="vehicle-features-list">
    <div class="features-header">
        <h4>{$MOD.LBL_VEHICLE_FEATURES}</h4>
        {if $fields.assigned_user_id.value == $USER_ID || $IS_ADMIN}
        <button type="button" class="btn btn-primary btn-sm" onclick="openFeatureEditor('{$fields.id.value}')">
            <i class="fa fa-plus"></i> {$MOD.LBL_MANAGE_FEATURES}
        </button>
        {/if}
    </div>
    
    <div class="features-container">
        {assign var="features" value=$fields.features.value}
        {if $features && $features != '[]'}
            {assign var="featureArray" value=$features|@json_decode:true}
            
            {* Group features by category *}
            {assign var="featureCategories" value=[]}
            {foreach from=$featureArray item=feature}
                {assign var="category" value=$feature.category|default:'General'}
                {if !isset($featureCategories[$category])}
                    {assign var="featureCategories[$category]" value=[]}
                {/if}
                {assign var="featureCategories[$category]" value=$featureCategories[$category]|@array_merge:[$feature]}
            {/foreach}
            
            <div class="features-grid">
                {foreach from=$featureCategories key=category item=categoryFeatures}
                <div class="feature-category">
                    <h5 class="category-title">
                        <i class="fa {$MOD.FEATURE_CATEGORY_ICONS[$category]|default:'fa-cog'}"></i>
                        {$category}
                    </h5>
                    
                    <div class="category-features">
                        {foreach from=$categoryFeatures item=feature}
                        <div class="feature-item {if $feature.highlighted}feature-highlighted{/if}">
                            <div class="feature-content">
                                {if $feature.icon}
                                <i class="fa {$feature.icon} feature-icon"></i>
                                {/if}
                                <span class="feature-name">{$feature.name}</span>
                                {if $feature.value}
                                <span class="feature-value">: {$feature.value}</span>
                                {/if}
                            </div>
                            {if $feature.description}
                            <div class="feature-description" title="{$feature.description}">
                                {$feature.description|truncate:60}
                            </div>
                            {/if}
                        </div>
                        {/foreach}
                    </div>
                </div>
                {/foreach}
            </div>
            
            {* Feature Summary Stats *}
            <div class="features-summary">
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-stat">
                            <span class="stat-number">{$featureArray|@count}</span>
                            <span class="stat-label">{$MOD.LBL_TOTAL_FEATURES}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-stat">
                            <span class="stat-number">{$featureCategories|@count}</span>
                            <span class="stat-label">{$MOD.LBL_CATEGORIES}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-stat">
                            {assign var="highlightedCount" value=0}
                            {foreach from=$featureArray item=feature}
                                {if $feature.highlighted}{assign var="highlightedCount" value=$highlightedCount+1}{/if}
                            {/foreach}
                            <span class="stat-number">{$highlightedCount}</span>
                            <span class="stat-label">{$MOD.LBL_KEY_FEATURES}</span>
                        </div>
                    </div>
                </div>
            </div>
            
        {else}
            <div class="no-features-message">
                <div class="text-center">
                    <i class="fa fa-list-ul fa-3x text-muted"></i>
                    <p class="text-muted">{$MOD.LBL_NO_FEATURES_AVAILABLE}</p>
                    {if $fields.assigned_user_id.value == $USER_ID || $IS_ADMIN}
                    <button type="button" class="btn btn-primary" onclick="openFeatureEditor('{$fields.id.value}')">
                        <i class="fa fa-plus"></i> {$MOD.LBL_ADD_FEATURES}
                    </button>
                    {/if}
                </div>
            </div>
        {/if}
    </div>
</div>

{* Feature Editor Modal *}
<div id="featureEditorModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{$MOD.LBL_MANAGE_VEHICLE_FEATURES}</h4>
            </div>
            <div class="modal-body">
                
                {* Feature Categories Tabs *}
                <ul class="nav nav-tabs" role="tablist" id="featureCategoryTabs">
                    <li role="presentation" class="active">
                        <a href="#safetyTab" aria-controls="safetyTab" role="tab" data-toggle="tab">
                            <i class="fa fa-shield"></i> {$MOD.LBL_SAFETY_FEATURES}
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#comfortTab" aria-controls="comfortTab" role="tab" data-toggle="tab">
                            <i class="fa fa-car"></i> {$MOD.LBL_COMFORT_FEATURES}
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#technologyTab" aria-controls="technologyTab" role="tab" data-toggle="tab">
                            <i class="fa fa-mobile"></i> {$MOD.LBL_TECHNOLOGY_FEATURES}
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#performanceTab" aria-controls="performanceTab" role="tab" data-toggle="tab">
                            <i class="fa fa-tachometer"></i> {$MOD.LBL_PERFORMANCE_FEATURES}
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#customTab" aria-controls="customTab" role="tab" data-toggle="tab">
                            <i class="fa fa-plus-circle"></i> {$MOD.LBL_CUSTOM_FEATURES}
                        </a>
                    </li>
                </ul>
                
                {* Tab Content *}
                <div class="tab-content feature-tabs-content">
                    <div role="tabpanel" class="tab-pane active" id="safetyTab">
                        <div class="feature-checklist" data-category="Safety">
                            {* Safety features will be populated by JavaScript *}
                        </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="comfortTab">
                        <div class="feature-checklist" data-category="Comfort">
                            {* Comfort features will be populated by JavaScript *}
                        </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="technologyTab">
                        <div class="feature-checklist" data-category="Technology">
                            {* Technology features will be populated by JavaScript *}
                        </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="performanceTab">
                        <div class="feature-checklist" data-category="Performance">
                            {* Performance features will be populated by JavaScript *}
                        </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="customTab">
                        <div class="custom-feature-form">
                            <div class="form-group">
                                <label>{$MOD.LBL_FEATURE_NAME}</label>
                                <input type="text" class="form-control" id="customFeatureName" placeholder="Enter feature name">
                            </div>
                            <div class="form-group">
                                <label>{$MOD.LBL_FEATURE_VALUE}</label>
                                <input type="text" class="form-control" id="customFeatureValue" placeholder="Enter feature value (optional)">
                            </div>
                            <div class="form-group">
                                <label>{$MOD.LBL_FEATURE_DESCRIPTION}</label>
                                <textarea class="form-control" id="customFeatureDescription" rows="3" placeholder="Enter feature description (optional)"></textarea>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="customFeatureHighlighted"> {$MOD.LBL_HIGHLIGHT_FEATURE}
                                </label>
                            </div>
                            <button type="button" class="btn btn-success" onclick="addCustomFeature()">
                                <i class="fa fa-plus"></i> {$MOD.LBL_ADD_CUSTOM_FEATURE}
                            </button>
                        </div>
                    </div>
                </div>
                
                {* Selected Features Summary *}
                <div class="selected-features-summary">
                    <h5>{$MOD.LBL_SELECTED_FEATURES}</h5>
                    <div id="selectedFeaturesList" class="selected-features-list">
                        {* Selected features will be displayed here *}
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
                <button type="button" class="btn btn-primary" onclick="saveVehicleFeatures()">
                    <i class="fa fa-save"></i> {$MOD.LBL_SAVE_FEATURES}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.vehicle-features-list {
    margin: 15px 0;
}

.features-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.features-container {
    min-height: 200px;
}

.features-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.feature-category {
    flex: 1 1 300px;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 15px;
    background: #f9f9f9;
}

.category-title {
    color: #666;
    margin-bottom: 10px;
    font-size: 16px;
    font-weight: bold;
}

.category-features {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.feature-item {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    margin: 2px;
    transition: all 0.2s ease;
}

.feature-item:hover {
    border-color: #428bca;
    box-shadow: 0 2px 4px rgba(66, 139, 202, 0.2);
}

.feature-highlighted {
    border-color: #f0ad4e;
    background: #fcf8e3;
    font-weight: bold;
}

.feature-highlighted .feature-icon {
    color: #f0ad4e;
}

.feature-content {
    display: flex;
    align-items: center;
    gap: 6px;
}

.feature-icon {
    color: #666;
    font-size: 14px;
}

.feature-name {
    font-size: 13px;
    color: #333;
}

.feature-value {
    font-size: 12px;
    color: #666;
    font-style: italic;
}

.feature-description {
    font-size: 11px;
    color: #999;
    margin-top: 4px;
    line-height: 1.3;
}

.features-summary {
    margin-top: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 4px;
}

.feature-stat {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #428bca;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.no-features-message {
    padding: 40px;
    text-align: center;
}

.feature-tabs-content {
    margin-top: 15px;
    min-height: 300px;
}

.feature-checklist {
    padding: 15px 0;
}

.feature-checkbox-item {
    margin-bottom: 10px;
    padding: 8px;
    border: 1px solid #eee;
    border-radius: 4px;
    background: #fafafa;
}

.feature-checkbox-item:hover {
    background: #f0f0f0;
}

.feature-checkbox-item label {
    margin: 0;
    font-weight: normal;
    cursor: pointer;
    width: 100%;
}

.custom-feature-form {
    padding: 15px 0;
}

.selected-features-summary {
    margin-top: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.selected-features-list {
    max-height: 120px;
    overflow-y: auto;
}

.selected-feature-tag {
    display: inline-block;
    background: #428bca;
    color: white;
    padding: 4px 8px;
    margin: 2px;
    border-radius: 12px;
    font-size: 12px;
}

.selected-feature-tag .remove-feature {
    margin-left: 5px;
    cursor: pointer;
    color: #ccc;
}

.selected-feature-tag .remove-feature:hover {
    color: white;
}
</style>

<script type="text/javascript">
{literal}
// Feature management variables
var selectedFeatures = [];
var availableFeatures = {
    'Safety': [
        { name: 'Anti-lock Braking System (ABS)', icon: 'fa-shield' },
        { name: 'Electronic Stability Control', icon: 'fa-shield' },
        { name: 'Traction Control', icon: 'fa-shield' },
        { name: 'Airbags (Multiple)', icon: 'fa-shield' },
        { name: 'Backup Camera', icon: 'fa-video-camera' },
        { name: 'Blind Spot Monitoring', icon: 'fa-eye' },
        { name: 'Lane Departure Warning', icon: 'fa-road' },
        { name: 'Forward Collision Warning', icon: 'fa-exclamation-triangle' },
        { name: 'Automatic Emergency Braking', icon: 'fa-hand-paper-o' },
        { name: 'Adaptive Cruise Control', icon: 'fa-tachometer' }
    ],
    'Comfort': [
        { name: 'Air Conditioning', icon: 'fa-snowflake-o' },
        { name: 'Climate Control (Dual Zone)', icon: 'fa-thermometer-half' },
        { name: 'Heated Seats', icon: 'fa-fire' },
        { name: 'Cooled Seats', icon: 'fa-snowflake-o' },
        { name: 'Power Seats', icon: 'fa-arrows' },
        { name: 'Memory Seats', icon: 'fa-bookmark' },
        { name: 'Leather Interior', icon: 'fa-leaf' },
        { name: 'Sunroof/Moonroof', icon: 'fa-sun-o' },
        { name: 'Power Windows', icon: 'fa-window-maximize' },
        { name: 'Remote Start', icon: 'fa-key' }
    ],
    'Technology': [
        { name: 'Bluetooth Connectivity', icon: 'fa-bluetooth' },
        { name: 'USB Ports', icon: 'fa-usb' },
        { name: 'Wireless Charging', icon: 'fa-battery' },
        { name: 'Touch Screen Display', icon: 'fa-tablet' },
        { name: 'Navigation System', icon: 'fa-map' },
        { name: 'Apple CarPlay', icon: 'fa-apple' },
        { name: 'Android Auto', icon: 'fa-android' },
        { name: 'Premium Sound System', icon: 'fa-volume-up' },
        { name: 'Satellite Radio', icon: 'fa-satellite' },
        { name: 'WiFi Hotspot', icon: 'fa-wifi' }
    ],
    'Performance': [
        { name: 'Turbo/Supercharged Engine', icon: 'fa-tachometer' },
        { name: 'All-Wheel Drive', icon: 'fa-road' },
        { name: 'Sport Mode', icon: 'fa-flag-checkered' },
        { name: 'Manual Transmission', icon: 'fa-cogs' },
        { name: 'Paddle Shifters', icon: 'fa-hand-rock-o' },
        { name: 'Limited Slip Differential', icon: 'fa-cogs' },
        { name: 'Performance Suspension', icon: 'fa-wrench' },
        { name: 'Performance Brakes', icon: 'fa-stop-circle' },
        { name: 'Sport Exhaust', icon: 'fa-volume-up' },
        { name: 'Launch Control', icon: 'fa-rocket' }
    ]
};

function openFeatureEditor(vehicleId) {
    // Load current features
    loadCurrentFeatures();
    
    // Populate feature checklists
    populateFeatureChecklists();
    
    // Show modal
    $('#featureEditorModal').modal('show');
}

function loadCurrentFeatures() {
    // Parse current features from the vehicle record
    var currentFeaturesJson = $('input[name="features"]').val() || '[]';
    try {
        selectedFeatures = JSON.parse(currentFeaturesJson);
    } catch (e) {
        selectedFeatures = [];
    }
    
    updateSelectedFeaturesList();
}

function populateFeatureChecklists() {
    Object.keys(availableFeatures).forEach(function(category) {
        var container = $('.feature-checklist[data-category="' + category + '"]');
        container.empty();
        
        availableFeatures[category].forEach(function(feature, index) {
            var isSelected = selectedFeatures.some(function(selected) {
                return selected.name === feature.name;
            });
            
            var checkboxHtml = '<div class="feature-checkbox-item">' +
                '<label>' +
                '<input type="checkbox" class="feature-checkbox" ' +
                'data-category="' + category + '" ' +
                'data-feature="' + feature.name + '" ' +
                'data-icon="' + feature.icon + '"' +
                (isSelected ? ' checked' : '') + '> ' +
                '<i class="fa ' + feature.icon + '"></i> ' + feature.name +
                '</label>' +
                '</div>';
            
            container.append(checkboxHtml);
        });
    });
    
    // Bind checkbox events
    $('.feature-checkbox').on('change', function() {
        updateSelectedFeatures();
    });
}

function updateSelectedFeatures() {
    selectedFeatures = [];
    
    $('.feature-checkbox:checked').each(function() {
        var checkbox = $(this);
        selectedFeatures.push({
            name: checkbox.data('feature'),
            category: checkbox.data('category'),
            icon: checkbox.data('icon'),
            highlighted: false
        });
    });
    
    updateSelectedFeaturesList();
}

function updateSelectedFeaturesList() {
    var container = $('#selectedFeaturesList');
    container.empty();
    
    selectedFeatures.forEach(function(feature, index) {
        var tagHtml = '<span class="selected-feature-tag">' +
            '<i class="fa ' + feature.icon + '"></i> ' + feature.name +
            '<span class="remove-feature" onclick="removeSelectedFeature(' + index + ')">&times;</span>' +
            '</span>';
        container.append(tagHtml);
    });
}

function removeSelectedFeature(index) {
    var feature = selectedFeatures[index];
    selectedFeatures.splice(index, 1);
    
    // Uncheck the corresponding checkbox
    $('.feature-checkbox[data-feature="' + feature.name + '"]').prop('checked', false);
    
    updateSelectedFeaturesList();
}

function addCustomFeature() {
    var name = $('#customFeatureName').val().trim();
    var value = $('#customFeatureValue').val().trim();
    var description = $('#customFeatureDescription').val().trim();
    var highlighted = $('#customFeatureHighlighted').is(':checked');
    
    if (!name) {
        alert('Please enter a feature name.');
        return;
    }
    
    var customFeature = {
        name: name,
        category: 'Custom',
        icon: 'fa-star',
        highlighted: highlighted
    };
    
    if (value) customFeature.value = value;
    if (description) customFeature.description = description;
    
    selectedFeatures.push(customFeature);
    updateSelectedFeaturesList();
    
    // Clear form
    $('#customFeatureName').val('');
    $('#customFeatureValue').val('');
    $('#customFeatureDescription').val('');
    $('#customFeatureHighlighted').prop('checked', false);
}

function saveVehicleFeatures() {
    // Convert features to JSON and save
    var featuresJson = JSON.stringify(selectedFeatures);
    console.log('Saving features:', featuresJson);
    
    // TODO: Implement AJAX save functionality
    // For now, just close the modal
    $('#featureEditorModal').modal('hide');
    
    // Reload the page to show updated features
    // location.reload();
}
{/literal}
</script> 