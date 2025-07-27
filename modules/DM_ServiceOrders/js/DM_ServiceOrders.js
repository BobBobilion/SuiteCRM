/**
 * SuiteCRM Service & Parts Hub - Service Orders JavaScript
 * 
 * Client-side functionality for Service Orders module including
 * automatic calculations, field validations, and AJAX calls
 */

var DM_ServiceOrders = {
    
    /**
     * Initialize module functionality
     */
    init: function() {
        console.log('DM_ServiceOrders: Initializing JavaScript functionality');
        
        // Bind event handlers
        this.bindCalculationHandlers();
        this.bindValidationHandlers();
        this.bindLookupHandlers();
        
        // Initialize on page load
        this.calculateTotals();
    },
    
    /**
     * Bind calculation event handlers
     */
    bindCalculationHandlers: function() {
        var self = this;
        
        // Labor calculation
        $(document).on('change blur', '#labor_hours, #labor_rate, #parts_total, #tax_amount', function() {
            self.calculateTotals();
        });
        
        // Auto-calculate when labor fields change
        $(document).on('keyup', '#labor_hours, #labor_rate', function() {
            self.debounce(function() {
                self.calculateTotals();
            }, 500);
        });
    },
    
    /**
     * Bind validation event handlers
     */
    bindValidationHandlers: function() {
        var self = this;
        
        // VIN validation
        $(document).on('blur', '#vin', function() {
            self.validateVIN($(this).val());
        });
        
        // Mileage validation
        $(document).on('blur', '#mileage_in', function() {
            self.validateMileage($(this).val());
        });
    },
    
    /**
     * Bind lookup handlers
     */
    bindLookupHandlers: function() {
        var self = this;
        
        // Customer selection - populate vehicle dropdown
        $(document).on('change', '#customer_id', function() {
            var customerId = $(this).val();
            if (customerId) {
                self.loadCustomerVehicles(customerId);
            }
        });
        
        // Vehicle selection - populate VIN and details
        $(document).on('change', '#vehicle_id', function() {
            var vehicleId = $(this).val();
            if (vehicleId) {
                self.loadVehicleDetails(vehicleId);
            }
        });
    },
    
    /**
     * Calculate labor and total amounts
     */
    calculateTotals: function() {
        var laborHours = parseFloat($('#labor_hours').val()) || 0;
        var laborRate = parseFloat($('#labor_rate').val()) || 0;
        var partsTotal = parseFloat($('#parts_total').val()) || 0;
        var taxAmount = parseFloat($('#tax_amount').val()) || 0;
        
        // Calculate labor total
        var laborTotal = laborHours * laborRate;
        $('#labor_total').val(laborTotal.toFixed(2));
        
        // Calculate grand total
        var totalAmount = laborTotal + partsTotal + taxAmount;
        $('#total_amount').val(totalAmount.toFixed(2));
        
        console.log('DM_ServiceOrders: Calculated totals - Labor: ' + laborTotal + ', Total: ' + totalAmount);
    },
    
    /**
     * Validate VIN number
     */
    validateVIN: function(vin) {
        if (vin && vin.length !== 17) {
            alert('VIN must be exactly 17 characters long');
            $('#vin').focus();
            return false;
        }
        return true;
    },
    
    /**
     * Validate mileage input
     */
    validateMileage: function(mileage) {
        if (mileage && (isNaN(mileage) || mileage < 0)) {
            alert('Please enter a valid mileage value');
            $('#mileage_in').focus();
            return false;
        }
        return true;
    },
    
    /**
     * Load vehicles for selected customer
     */
    loadCustomerVehicles: function(customerId) {
        var self = this;
        
        $.ajax({
            url: 'index.php?module=DM_ServiceOrders&action=get_customer_vehicles',
            type: 'POST',
            data: {
                customer_id: customerId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.vehicles) {
                    self.populateVehicleDropdown(response.vehicles);
                }
            },
            error: function() {
                console.error('DM_ServiceOrders: Failed to load customer vehicles');
            }
        });
    },
    
    /**
     * Load vehicle details when vehicle is selected
     */
    loadVehicleDetails: function(vehicleId) {
        $.ajax({
            url: 'index.php?module=DM_ServiceOrders&action=get_vehicle_details',
            type: 'POST',
            data: {
                vehicle_id: vehicleId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.vehicle) {
                    $('#vin').val(response.vehicle.vin || '');
                    // Populate other vehicle fields as needed
                }
            },
            error: function() {
                console.error('DM_ServiceOrders: Failed to load vehicle details');
            }
        });
    },
    
    /**
     * Populate vehicle dropdown with options
     */
    populateVehicleDropdown: function(vehicles) {
        var vehicleSelect = $('#vehicle_id');
        vehicleSelect.empty();
        vehicleSelect.append('<option value="">-- Select Vehicle --</option>');
        
        $.each(vehicles, function(id, name) {
            vehicleSelect.append('<option value="' + id + '">' + name + '</option>');
        });
    },
    
    /**
     * Generate new RO number
     */
    generateRONumber: function() {
        $.ajax({
            url: 'index.php?module=DM_ServiceOrders&action=generate_ro_number',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.ro_number) {
                    $('#service_order_number').val(response.ro_number);
                }
            },
            error: function() {
                console.error('DM_ServiceOrders: Failed to generate RO number');
            }
        });
    },
    
    /**
     * Open parts lookup popup
     */
    openPartsLookup: function() {
        var url = 'index.php?module=DM_ServiceOrders&action=parts_lookup';
        window.open(url, 'PartsLookup', 'width=800,height=600,scrollbars=yes,resizable=yes');
    },
    
    /**
     * Add selected part to service order
     */
    addPart: function(partId, partNumber, description, price) {
        // Add logic to handle adding parts to the service order
        console.log('DM_ServiceOrders: Adding part - ' + partNumber + ': ' + description + ' ($' + price + ')');
        
        // Update parts total
        var currentPartsTotal = parseFloat($('#parts_total').val()) || 0;
        var newPartsTotal = currentPartsTotal + parseFloat(price);
        $('#parts_total').val(newPartsTotal.toFixed(2));
        
        // Recalculate totals
        this.calculateTotals();
    },
    
    /**
     * Debounce function for delayed execution
     */
    debounce: function(func, delay) {
        var timeoutId;
        return function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(func, delay);
        };
    }
};

// Initialize when document is ready
$(document).ready(function() {
    if (typeof SUGAR !== 'undefined' && SUGAR.App) {
        // SuiteCRM 7.x initialization
        SUGAR.App.view.invokeParent = function() {
            DM_ServiceOrders.init();
        };
    } else {
        // SuiteCRM 8.x or fallback initialization
        DM_ServiceOrders.init();
    }
});

// Legacy support for older versions
if (typeof addForm !== 'undefined') {
    addForm('EditView');
    addForm('DetailView');
}