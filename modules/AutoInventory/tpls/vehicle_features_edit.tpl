{* SuiteCRM AutoInventory Vehicle Features Edit Template *}
<div id="vehicle_features_container">
    <div class="features-input-section">
        <label>{$MOD.LBL_VEHICLE_FEATURES}:</label>
        
        {* Common Features Checkboxes *}
        <div class="common-features" style="margin-top: 10px;">
            <h4 style="margin-bottom: 10px; color: #333;">Common Features:</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px;">
                <label><input type="checkbox" class="feature-checkbox" value="Air Conditioning"> Air Conditioning</label>
                <label><input type="checkbox" class="feature-checkbox" value="Power Windows"> Power Windows</label>
                <label><input type="checkbox" class="feature-checkbox" value="Power Locks"> Power Locks</label>
                <label><input type="checkbox" class="feature-checkbox" value="Power Steering"> Power Steering</label>
                <label><input type="checkbox" class="feature-checkbox" value="Cruise Control"> Cruise Control</label>
                <label><input type="checkbox" class="feature-checkbox" value="Bluetooth"> Bluetooth</label>
                <label><input type="checkbox" class="feature-checkbox" value="USB Port"> USB Port</label>
                <label><input type="checkbox" class="feature-checkbox" value="Backup Camera"> Backup Camera</label>
                <label><input type="checkbox" class="feature-checkbox" value="Sunroof"> Sunroof</label>
                <label><input type="checkbox" class="feature-checkbox" value="Leather Seats"> Leather Seats</label>
                <label><input type="checkbox" class="feature-checkbox" value="Heated Seats"> Heated Seats</label>
                <label><input type="checkbox" class="feature-checkbox" value="Remote Start"> Remote Start</label>
                <label><input type="checkbox" class="feature-checkbox" value="Navigation System"> Navigation System</label>
                <label><input type="checkbox" class="feature-checkbox" value="Premium Sound"> Premium Sound</label>
                <label><input type="checkbox" class="feature-checkbox" value="Alloy Wheels"> Alloy Wheels</label>
                <label><input type="checkbox" class="feature-checkbox" value="Tinted Windows"> Tinted Windows</label>
                <label><input type="checkbox" class="feature-checkbox" value="Keyless Entry"> Keyless Entry</label>
                <label><input type="checkbox" class="feature-checkbox" value="Anti-lock Brakes"> Anti-lock Brakes</label>
                <label><input type="checkbox" class="feature-checkbox" value="Stability Control"> Stability Control</label>
                <label><input type="checkbox" class="feature-checkbox" value="Airbags"> Airbags</label>
            </div>
        </div>
        
        {* Custom Features Input *}
        <div class="custom-features" style="margin-top: 20px;">
            <h4 style="margin-bottom: 10px; color: #333;">Additional Features:</h4>
            <textarea id="custom_features_input" placeholder="Enter additional features, one per line..." 
                      style="width: 100%; height: 100px; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
            <small style="color: #666; display: block; margin-top: 5px;">
                Enter one feature per line. These will be added to the selected features above.
            </small>
        </div>
        
        {* Selected Features Preview *}
        <div class="selected-features-preview" style="margin-top: 15px;">
            <h4 style="margin-bottom: 10px; color: #333;">Selected Features:</h4>
            <div id="features_preview" style="min-height: 50px; padding: 10px; border: 1px solid #ddd; 
                                               border-radius: 4px; background-color: #f9f9f9;">
                <span style="color: #999; font-style: italic;">No features selected</span>
            </div>
        </div>
    </div>
    
    <textarea name="vehicle_features" id="vehicle_features" style="display: none;">{$fields.vehicle_features.value}</textarea>
</div>

<script type="text/javascript">
{literal}
var currentFeatures = [];

// Initialize features from existing data
document.addEventListener('DOMContentLoaded', function() {
    var featuresData = document.getElementById('vehicle_features').value;
    if (featuresData && featuresData !== '[]') {
        try {
            currentFeatures = JSON.parse(featuresData);
        } catch(e) {
            currentFeatures = [];
        }
    }
    
    // Check corresponding checkboxes
    updateCheckboxes();
    updateFeaturesPreview();
    
    // Add event listeners
    var checkboxes = document.querySelectorAll('.feature-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                if (currentFeatures.indexOf(this.value) === -1) {
                    currentFeatures.push(this.value);
                }
            } else {
                var index = currentFeatures.indexOf(this.value);
                if (index !== -1) {
                    currentFeatures.splice(index, 1);
                }
            }
            updateFeaturesPreview();
            updateHiddenField();
        });
    });
    
    // Custom features input
    document.getElementById('custom_features_input').addEventListener('blur', function() {
        var customText = this.value.trim();
        if (customText) {
            var customFeatures = customText.split('\n').map(function(feature) {
                return feature.trim();
            }).filter(function(feature) {
                return feature.length > 0 && currentFeatures.indexOf(feature) === -1;
            });
            
            currentFeatures = currentFeatures.concat(customFeatures);
            this.value = '';
            updateFeaturesPreview();
            updateHiddenField();
        }
    });
});

function updateCheckboxes() {
    var checkboxes = document.querySelectorAll('.feature-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = currentFeatures.indexOf(checkbox.value) !== -1;
    });
}

function updateFeaturesPreview() {
    var preview = document.getElementById('features_preview');
    if (currentFeatures.length === 0) {
        preview.innerHTML = '<span style="color: #999; font-style: italic;">No features selected</span>';
    } else {
        var html = '';
        for (var i = 0; i < currentFeatures.length; i++) {
            html += '<span class="feature-tag" style="display: inline-block; background: #007cba; color: white; ';
            html += 'padding: 4px 8px; margin: 2px; border-radius: 4px; font-size: 12px;">';
            html += currentFeatures[i];
            html += ' <button type="button" onclick="removeFeature(' + i + ')" ';
            html += 'style="background: none; border: none; color: white; margin-left: 5px; cursor: pointer;">×</button>';
            html += '</span>';
        }
        preview.innerHTML = html;
    }
}

function removeFeature(index) {
    currentFeatures.splice(index, 1);
    updateCheckboxes();
    updateFeaturesPreview();
    updateHiddenField();
}

function updateHiddenField() {
    document.getElementById('vehicle_features').value = JSON.stringify(currentFeatures);
}
{/literal}
</script>

<style type="text/css">
{literal}
.common-features label {
    display: flex;
    align-items: center;
    font-size: 14px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.common-features label:hover {
    background-color: #f0f0f0;
}

.common-features input[type="checkbox"] {
    margin-right: 8px;
}

.feature-tag button:hover {
    background: rgba(255,255,255,0.2) !important;
}

h4 {
    border-bottom: 2px solid #007cba;
    padding-bottom: 5px;
}
{/literal}
</style> 