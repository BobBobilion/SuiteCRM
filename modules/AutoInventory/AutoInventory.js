/**
 * SuiteCRM Auto Inventory System - JavaScript Functions
 * 
 * This file contains client-side JavaScript functionality for the
 * AutoInventory module including form validation and dynamic behavior.
 */

/**
 * Validate VIN number format
 * @param {string} vin - The VIN number to validate
 * @returns {boolean} - True if VIN is valid format
 */
function validateVIN(vin) {
    // Remove any spaces and convert to uppercase
    vin = vin.replace(/\s/g, '').toUpperCase();
    
    // Check length
    if (vin.length !== 17) {
        return false;
    }
    
    // Check for invalid characters (I, O, Q are not allowed in VINs)
    if (/[IOQ]/.test(vin)) {
        return false;
    }
    
    // Check alphanumeric
    if (!/^[A-HJ-NPR-Z0-9]{17}$/.test(vin)) {
        return false;
    }
    
    return true;
}

/**
 * Format VIN number input
 * @param {HTMLElement} element - The VIN input element
 */
function formatVINInput(element) {
    if (element && element.value) {
        // Remove spaces and convert to uppercase
        element.value = element.value.replace(/\s/g, '').toUpperCase();
        
        // Validate format
        if (element.value.length > 0 && !validateVIN(element.value) && element.value.length === 17) {
            // Show warning for invalid VIN
            if (typeof addToValidate === 'function') {
                addToValidate('EditView', element.name, 'varchar', false, 'Invalid VIN format. Please enter a valid 17-character VIN.');
            }
        }
    }
}

/**
 * Validate year field
 * @param {HTMLElement} element - The year input element
 */
function validateYear(element) {
    if (element && element.value) {
        var year = parseInt(element.value);
        var currentYear = new Date().getFullYear();
        
        if (isNaN(year) || year < 1900 || year > (currentYear + 1)) {
            if (typeof addToValidate === 'function') {
                addToValidate('EditView', element.name, 'int', false, 'Please enter a valid year between 1900 and ' + (currentYear + 1) + '.');
            }
            return false;
        }
    }
    return true;
}

/**
 * Validate odometer reading
 * @param {HTMLElement} element - The odometer input element
 */
function validateOdometer(element) {
    if (element && element.value) {
        var mileage = parseInt(element.value);
        
        if (isNaN(mileage) || mileage < 0 || mileage > 999999) {
            if (typeof addToValidate === 'function') {
                addToValidate('EditView', element.name, 'int', false, 'Please enter a valid odometer reading between 0 and 999,999.');
            }
            return false;
        }
    }
    return true;
}

/**
 * Format currency input fields
 * @param {HTMLElement} element - The currency input element
 */
function formatCurrencyInput(element) {
    if (element && element.value) {
        // Remove any non-numeric characters except decimal point
        var value = element.value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        var parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limit to 2 decimal places
        if (parts[1] && parts[1].length > 2) {
            value = parts[0] + '.' + parts[1].substring(0, 2);
        }
        
        element.value = value;
    }
}

/**
 * Initialize AutoInventory form functionality
 */
function initAutoInventoryForm() {
    console.log('Initializing AutoInventory form functionality...');
    
    // Add event listeners when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setupAutoInventoryEventListeners();
        });
    } else {
        setupAutoInventoryEventListeners();
    }
}

/**
 * Set up event listeners for form fields
 */
function setupAutoInventoryEventListeners() {
    console.log('Setting up AutoInventory event listeners...');
    
    // VIN number field
    var vinField = document.getElementById('vin_number') || document.getElementsByName('vin_number')[0];
    if (vinField) {
        vinField.addEventListener('blur', function() {
            formatVINInput(this);
        });
        vinField.addEventListener('input', function() {
            // Convert to uppercase as user types
            this.value = this.value.toUpperCase();
        });
    }
    
    // Model year field
    var yearField = document.getElementById('model_year') || document.getElementsByName('model_year')[0];
    if (yearField) {
        yearField.addEventListener('blur', function() {
            validateYear(this);
        });
    }
    
    // Odometer field
    var odometerField = document.getElementById('odometer') || document.getElementsByName('odometer')[0];
    if (odometerField) {
        odometerField.addEventListener('blur', function() {
            validateOdometer(this);
        });
    }
    
    // Currency fields
    var currencyFields = ['cost_basis', 'asking_price', 'final_price', 'market_valuation'];
    currencyFields.forEach(function(fieldName) {
        var field = document.getElementById(fieldName) || document.getElementsByName(fieldName)[0];
        if (field) {
            field.addEventListener('input', function() {
                formatCurrencyInput(this);
            });
        }
    });
    
    console.log('AutoInventory event listeners set up successfully');
}

/**
 * Auto-generate vehicle name based on year, make, model, and trim
 */
function generateVehicleName() {
    var nameField = document.getElementById('name') || document.getElementsByName('name')[0];
    var yearField = document.getElementById('model_year') || document.getElementsByName('model_year')[0];
    var makeField = document.getElementById('manufacturer') || document.getElementsByName('manufacturer')[0];
    var modelField = document.getElementById('vehicle_model') || document.getElementsByName('vehicle_model')[0];
    var trimField = document.getElementById('trim_level') || document.getElementsByName('trim_level')[0];
    
    if (nameField && yearField && makeField && modelField) {
        var nameParts = [];
        
        if (yearField.value) nameParts.push(yearField.value);
        if (makeField.value) nameParts.push(makeField.value);
        if (modelField.value) nameParts.push(modelField.value);
        if (trimField && trimField.value) nameParts.push(trimField.value);
        
        if (nameParts.length > 0) {
            nameField.value = nameParts.join(' ');
        }
    }
}

/**
 * Set up auto-generation of vehicle name
 */
function setupAutoNameGeneration() {
    var fields = ['model_year', 'manufacturer', 'vehicle_model', 'trim_level'];
    
    fields.forEach(function(fieldName) {
        var field = document.getElementById(fieldName) || document.getElementsByName(fieldName)[0];
        if (field) {
            field.addEventListener('blur', function() {
                generateVehicleName();
            });
        }
    });
}

// Initialize when script loads
initAutoInventoryForm();

// Also set up auto name generation
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupAutoNameGeneration);
} else {
    setupAutoNameGeneration();
} 