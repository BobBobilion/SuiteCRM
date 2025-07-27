/**
 * SuiteCRM Lead Attribution Center - JavaScript Functions
 * 
 * Client-side functionality for UTM capture, ROI calculations, and attribution analysis
 */

/**
 * Capture UTM parameters from current page
 */
function captureUTMParameters(leadId) {
    if (!leadId) {
        alert('Lead ID is required for UTM capture');
        return;
    }
    
    // Get UTM parameters from URL
    var urlParams = new URLSearchParams(window.location.search);
    var utmData = {};
    
    // Standard UTM parameters
    var utmFields = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
    utmFields.forEach(function(field) {
        var value = urlParams.get(field);
        if (value) {
            utmData[field] = value;
        }
    });
    
    // Add referrer and current page
    utmData.referrer_url = document.referrer;
    utmData.landing_page = window.location.pathname + window.location.search;
    utmData.lead_id = leadId;
    
    // Send AJAX request to capture UTM data
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?module=DM_LeadAttribution&action=captureUTM', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        console.log('UTM parameters captured successfully');
                        if (window.SUGAR && window.SUGAR.util) {
                            SUGAR.util.doWhen('typeof(SUGAR.util.showStatus) !== "undefined"', function() {
                                SUGAR.util.showStatus('UTM parameters captured successfully', 'success');
                            });
                        }
                    } else {
                        console.error('Failed to capture UTM parameters:', response.message);
                    }
                } catch (e) {
                    console.error('Error parsing UTM capture response:', e);
                }
            }
        }
    };
    
    // Prepare form data
    var formData = Object.keys(utmData).map(function(key) {
        return encodeURIComponent(key) + '=' + encodeURIComponent(utmData[key]);
    }).join('&');
    
    xhr.send(formData);
}

/**
 * Auto-capture UTM parameters when page loads (for lead forms)
 */
function autoCapture() {
    // Check if we're on a lead form or detail page
    var leadIdField = document.querySelector('input[name="record"]') || 
                     document.querySelector('input[name="lead_id"]') ||
                     document.querySelector('#lead_id');
    
    if (leadIdField && leadIdField.value) {
        // Only capture if UTM parameters are present
        var urlParams = new URLSearchParams(window.location.search);
        var hasUTM = ['utm_source', 'utm_medium', 'utm_campaign'].some(function(param) {
            return urlParams.has(param);
        });
        
        if (hasUTM) {
            setTimeout(function() {
                captureUTMParameters(leadIdField.value);
            }, 1000); // Delay to ensure page is fully loaded
        }
    }
}

/**
 * Update attribution data for a record
 */
function updateAttribution(recordId) {
    if (!recordId) {
        alert('Record ID is required');
        return;
    }
    
    if (confirm('Update attribution data for this record?')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php?module=DM_LeadAttribution&action=updateAttribution', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert('Attribution data updated successfully');
                            window.location.reload();
                        } else {
                            alert('Failed to update attribution data: ' + response.message);
                        }
                    } catch (e) {
                        alert('Error updating attribution data');
                    }
                }
            }
        };
        
        xhr.send('record_id=' + encodeURIComponent(recordId));
    }
}

/**
 * Calculate ROI for a record
 */
function calculateROI(recordId) {
    if (!recordId) {
        alert('Record ID is required');
        return;
    }
    
    var conversionValue = prompt('Enter conversion value:');
    var totalCost = prompt('Enter total cost:');
    
    if (conversionValue !== null && totalCost !== null) {
        conversionValue = parseFloat(conversionValue) || 0;
        totalCost = parseFloat(totalCost) || 0;
        
        if (totalCost === 0) {
            alert('Total cost cannot be zero');
            return;
        }
        
        var roi = ((conversionValue - totalCost) / totalCost) * 100;
        
        if (confirm('Calculated ROI: ' + roi.toFixed(2) + '%\n\nSave this ROI calculation?')) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php?module=DM_LeadAttribution&action=calculateROI', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                alert('ROI calculated and saved successfully');
                                window.location.reload();
                            } else {
                                alert('Failed to save ROI: ' + response.message);
                            }
                        } catch (e) {
                            alert('Error saving ROI calculation');
                        }
                    }
                }
            };
            
            xhr.send('record_id=' + encodeURIComponent(recordId) + 
                    '&conversion_value=' + encodeURIComponent(conversionValue) + 
                    '&total_cost=' + encodeURIComponent(totalCost) + 
                    '&roi_percentage=' + encodeURIComponent(roi));
        }
    }
}

/**
 * Visualize customer journey
 */
function visualizeJourney(journeyData) {
    try {
        var journey = typeof journeyData === 'string' ? JSON.parse(journeyData) : journeyData;
        
        if (!journey || !Array.isArray(journey)) {
            return '<p>No journey data available</p>';
        }
        
        var html = '<div class="journey-timeline">';
        
        journey.forEach(function(touchpoint, index) {
            html += '<div class="touchpoint">';
            html += '<div class="touchpoint-number">' + (index + 1) + '</div>';
            html += '<div class="touchpoint-details">';
            html += '<strong>' + (touchpoint.source || 'Unknown') + '</strong>';
            if (touchpoint.medium) {
                html += ' (' + touchpoint.medium + ')';
            }
            if (touchpoint.campaign) {
                html += '<br><span class="campaign">' + touchpoint.campaign + '</span>';
            }
            html += '<br><small>' + new Date(touchpoint.timestamp).toLocaleDateString() + '</small>';
            html += '</div>';
            html += '</div>';
            
            if (index < journey.length - 1) {
                html += '<div class="journey-arrow">→</div>';
            }
        });
        
        html += '</div>';
        
        // Add CSS for journey visualization
        html += '<style>';
        html += '.journey-timeline { display: flex; align-items: center; flex-wrap: wrap; margin: 20px 0; }';
        html += '.touchpoint { background: #f8f9fa; border: 2px solid #007bff; border-radius: 8px; padding: 10px; margin: 5px; min-width: 150px; text-align: center; }';
        html += '.touchpoint-number { background: #007bff; color: white; border-radius: 50%; width: 25px; height: 25px; line-height: 25px; margin: 0 auto 5px; font-size: 12px; font-weight: bold; }';
        html += '.touchpoint-details { font-size: 12px; }';
        html += '.campaign { color: #666; font-style: italic; }';
        html += '.journey-arrow { font-size: 20px; color: #007bff; margin: 0 10px; }';
        html += '</style>';
        
        return html;
    } catch (e) {
        return '<p>Error displaying journey data</p>';
    }
}

/**
 * Google Analytics integration
 */
function initGoogleAnalytics() {
    // Initialize GA4 if gtag is available
    if (typeof gtag !== 'undefined') {
        // Send custom event for lead attribution
        gtag('event', 'lead_attribution_view', {
            'custom_parameter_1': 'attribution_module',
            'custom_parameter_2': window.location.pathname
        });
    }
}

/**
 * Facebook Pixel integration
 */
function initFacebookPixel() {
    // Initialize Facebook Pixel if fbq is available
    if (typeof fbq !== 'undefined') {
        // Send custom event for lead attribution
        fbq('trackCustom', 'LeadAttributionView', {
            module: 'DM_LeadAttribution',
            page: window.location.pathname
        });
    }
}

/**
 * Initialize attribution tracking
 */
function initAttribution() {
    // Auto-capture UTM parameters
    autoCapture();
    
    // Initialize analytics integrations
    initGoogleAnalytics();
    initFacebookPixel();
    
    // Enhance form submissions with attribution data
    enhanceFormSubmissions();
}

/**
 * Enhance form submissions with attribution data
 */
function enhanceFormSubmissions() {
    var forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        // Only enhance lead-related forms
        if (form.querySelector('input[name="module"][value="Leads"]') || 
            form.querySelector('input[name="module"][value="DM_LeadAttribution"]')) {
            
            form.addEventListener('submit', function(e) {
                // Add UTM parameters as hidden fields
                var urlParams = new URLSearchParams(window.location.search);
                var utmFields = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
                
                utmFields.forEach(function(field) {
                    var value = urlParams.get(field);
                    if (value && !form.querySelector('input[name="' + field + '"]')) {
                        var hiddenField = document.createElement('input');
                        hiddenField.type = 'hidden';
                        hiddenField.name = field;
                        hiddenField.value = value;
                        form.appendChild(hiddenField);
                    }
                });
                
                // Add referrer and landing page
                if (!form.querySelector('input[name="referrer_url"]')) {
                    var referrerField = document.createElement('input');
                    referrerField.type = 'hidden';
                    referrerField.name = 'referrer_url';
                    referrerField.value = document.referrer;
                    form.appendChild(referrerField);
                }
                
                if (!form.querySelector('input[name="landing_page"]')) {
                    var landingField = document.createElement('input');
                    landingField.type = 'hidden';
                    landingField.name = 'landing_page';
                    landingField.value = window.location.pathname + window.location.search;
                    form.appendChild(landingField);
                }
            });
        }
    });
}

/**
 * Export attribution data
 */
function exportAttributionData(format) {
    format = format || 'csv';
    
    var exportUrl = 'index.php?module=DM_LeadAttribution&action=export&format=' + format;
    
    // Add current filters if any
    var urlParams = new URLSearchParams(window.location.search);
    var filters = ['date_from', 'date_to', 'source', 'campaign'];
    
    filters.forEach(function(filter) {
        var value = urlParams.get(filter);
        if (value) {
            exportUrl += '&' + filter + '=' + encodeURIComponent(value);
        }
    });
    
    window.open(exportUrl, '_blank');
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAttribution);
} else {
    initAttribution();
}

// Also initialize when SUGAR is ready (for SuiteCRM compatibility)
if (typeof SUGAR !== 'undefined' && SUGAR.util) {
    SUGAR.util.doWhen('document.readyState === "complete"', initAttribution);
}