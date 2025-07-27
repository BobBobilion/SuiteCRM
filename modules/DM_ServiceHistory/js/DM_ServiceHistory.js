/**
 * SuiteCRM Service & Parts Hub - Service History JavaScript
 * 
 * Client-side functionality for Service History module including
 * parts tracking, timeline displays, and reporting functions
 */

var DM_ServiceHistory = {
    
    /**
     * Initialize module functionality
     */
    init: function() {
        console.log('DM_ServiceHistory: Initializing JavaScript functionality');
        
        // Bind event handlers
        this.bindValidationHandlers();
        this.bindLookupHandlers();
        this.bindPartsHandlers();
        
        // Initialize displays
        this.formatPartsUsed();
        this.validateServiceData();
    },
    
    /**
     * Bind validation event handlers
     */
    bindValidationHandlers: function() {
        var self = this;
        
        // Service date validation
        $(document).on('blur', '#service_date', function() {
            self.validateServiceDate($(this).val());
        });
        
        // Mileage validation
        $(document).on('blur', '#mileage', function() {
            self.validateMileage($(this).val());
        });
        
        // Labor hours validation
        $(document).on('blur', '#labor_hours', function() {
            self.validateLaborHours($(this).val());
        });
        
        // Total cost validation
        $(document).on('blur', '#total_cost', function() {
            self.validateTotalCost($(this).val());
        });
    },
    
    /**
     * Bind lookup handlers
     */
    bindLookupHandlers: function() {
        var self = this;
        
        // Vehicle selection - load vehicle history
        $(document).on('change', '#vehicle_id', function() {
            var vehicleId = $(this).val();
            if (vehicleId) {
                self.loadVehicleHistory(vehicleId);
            }
        });
        
        // Service order selection - populate fields
        $(document).on('change', '#service_order_id', function() {
            var serviceOrderId = $(this).val();
            if (serviceOrderId) {
                self.loadServiceOrderDetails(serviceOrderId);
            }
        });
    },
    
    /**
     * Bind parts management handlers
     */
    bindPartsHandlers: function() {
        var self = this;
        
        // Parts used text area changes
        $(document).on('blur', '#parts_used', function() {
            self.formatPartsUsed();
        });
    },
    
    /**
     * Validate service date
     */
    validateServiceDate: function(serviceDate) {
        if (serviceDate) {
            var date = new Date(serviceDate);
            var today = new Date();
            
            if (date > today) {
                alert('Service date cannot be in the future');
                $('#service_date').focus();
                return false;
            }
        }
        return true;
    },
    
    /**
     * Validate mileage
     */
    validateMileage: function(mileage) {
        if (mileage && (isNaN(mileage) || mileage < 0)) {
            alert('Please enter a valid mileage value');
            $('#mileage').focus();
            return false;
        }
        return true;
    },
    
    /**
     * Validate labor hours
     */
    validateLaborHours: function(laborHours) {
        if (laborHours && (isNaN(laborHours) || laborHours < 0)) {
            alert('Please enter a valid number of labor hours');
            $('#labor_hours').focus();
            return false;
        }
        return true;
    },
    
    /**
     * Validate total cost
     */
    validateTotalCost: function(totalCost) {
        if (totalCost && (isNaN(totalCost) || totalCost < 0)) {
            alert('Please enter a valid total cost');
            $('#total_cost').focus();
            return false;
        }
        return true;
    },
    
    /**
     * Load vehicle service history
     */
    loadVehicleHistory: function(vehicleId, limit) {
        var self = this;
        
        $.ajax({
            url: 'index.php?module=DM_ServiceHistory&action=get_vehicle_history',
            type: 'POST',
            data: {
                vehicle_id: vehicleId,
                limit: limit || 10
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.history) {
                    self.displayVehicleHistory(response.history);
                    console.log('DM_ServiceHistory: Loaded ' + response.count + ' history records for vehicle');
                }
            },
            error: function() {
                console.error('DM_ServiceHistory: Failed to load vehicle history');
            }
        });
    },
    
    /**
     * Display vehicle history in a timeline or list
     */
    displayVehicleHistory: function(history) {
        if (!history || history.length === 0) {
            return;
        }
        
        // Create history display (if container exists)
        var container = $('#vehicle_history_container');
        if (container.length === 0) {
            return;
        }
        
        var html = '<div class="service-history-timeline">';
        html += '<h4>Recent Service History</h4>';
        
        for (var i = 0; i < history.length; i++) {
            var record = history[i];
            html += '<div class="history-item">';
            html += '<div class="history-date">' + this.formatDate(record.service_date) + '</div>';
            html += '<div class="history-type">' + (record.service_type || 'Service') + '</div>';
            html += '<div class="history-mileage">@ ' + (record.mileage || 'N/A') + ' miles</div>';
            if (record.services_performed) {
                html += '<div class="history-description">' + record.services_performed.substring(0, 100) + '...</div>';
            }
            html += '</div>';
        }
        
        html += '</div>';
        container.html(html);
    },
    
    /**
     * Load service order details
     */
    loadServiceOrderDetails: function(serviceOrderId) {
        $.ajax({
            url: 'index.php?module=DM_ServiceOrders&action=get_service_order_details',
            type: 'POST',
            data: {
                service_order_id: serviceOrderId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.service_order) {
                    var so = response.service_order;
                    
                    // Populate fields from service order
                    if (so.vehicle_id) $('#vehicle_id').val(so.vehicle_id);
                    if (so.mileage_in) $('#mileage').val(so.mileage_in);
                    if (so.service_type) $('#service_type').val(so.service_type);
                    if (so.work_performed) $('#services_performed').val(so.work_performed);
                    if (so.labor_hours) $('#labor_hours').val(so.labor_hours);
                    if (so.total_amount) $('#total_cost').val(so.total_amount);
                    if (so.technician_id) $('#technician_id').val(so.technician_id);
                    if (so.notes) $('#notes').val(so.notes);
                    
                    console.log('DM_ServiceHistory: Populated fields from service order');
                }
            },
            error: function() {
                console.error('DM_ServiceHistory: Failed to load service order details');
            }
        });
    },
    
    /**
     * Format and validate parts used field
     */
    formatPartsUsed: function() {
        var partsUsed = $('#parts_used').val();
        
        if (partsUsed) {
            try {
                // Try to parse as JSON
                var parsed = JSON.parse(partsUsed);
                if (Array.isArray(parsed)) {
                    console.log('DM_ServiceHistory: Parts used is valid JSON array');
                    return;
                }
            } catch (e) {
                // Not JSON, treat as text list
                console.log('DM_ServiceHistory: Parts used is text format');
            }
        }
    },
    
    /**
     * Add part to parts used list
     */
    addPartToHistory: function(partData) {
        var historyId = $('input[name="record"]').val();
        
        if (!historyId) {
            alert('Please save the service history record first');
            return;
        }
        
        $.ajax({
            url: 'index.php?module=DM_ServiceHistory&action=add_part_to_history',
            type: 'POST',
            data: {
                history_id: historyId,
                part_id: partData.id,
                part_number: partData.part_number,
                description: partData.description,
                quantity: partData.quantity || 1,
                cost: partData.cost || 0
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update parts used display
                    DM_ServiceHistory.updatePartsUsedDisplay(response.parts_used);
                    alert('Part added to service history successfully');
                } else {
                    alert('Error adding part: ' + response.error);
                }
            },
            error: function() {
                alert('Failed to add part to service history');
            }
        });
    },
    
    /**
     * Update parts used display
     */
    updatePartsUsedDisplay: function(partsUsed) {
        if (partsUsed && Array.isArray(partsUsed)) {
            var display = '';
            for (var i = 0; i < partsUsed.length; i++) {
                var part = partsUsed[i];
                display += part.part_number + ' - ' + part.description + ' (Qty: ' + part.quantity + ')\n';
            }
            $('#parts_used').val(display);
        }
    },
    
    /**
     * Get service history by date range
     */
    getHistoryByDateRange: function(startDate, endDate, vehicleId, callback) {
        $.ajax({
            url: 'index.php?module=DM_ServiceHistory&action=get_history_by_date_range',
            type: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate,
                vehicle_id: vehicleId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (callback && typeof callback === 'function') {
                        callback(response.history);
                    }
                    console.log('DM_ServiceHistory: Found ' + response.count + ' records in date range');
                }
            },
            error: function() {
                console.error('DM_ServiceHistory: Failed to get history by date range');
            }
        });
    },
    
    /**
     * Get technician performance data
     */
    getTechnicianPerformance: function(technicianId, startDate, endDate, callback) {
        $.ajax({
            url: 'index.php?module=DM_ServiceHistory&action=get_technician_performance',
            type: 'POST',
            data: {
                technician_id: technicianId,
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (callback && typeof callback === 'function') {
                        callback(response.performance);
                    }
                    console.log('DM_ServiceHistory: Retrieved technician performance data');
                }
            },
            error: function() {
                console.error('DM_ServiceHistory: Failed to get technician performance');
            }
        });
    },
    
    /**
     * Get common services report
     */
    getCommonServices: function(limit, callback) {
        $.ajax({
            url: 'index.php?module=DM_ServiceHistory&action=get_common_services',
            type: 'POST',
            data: {
                limit: limit || 10
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (callback && typeof callback === 'function') {
                        callback(response.services);
                    }
                    console.log('DM_ServiceHistory: Retrieved common services data');
                }
            },
            error: function() {
                console.error('DM_ServiceHistory: Failed to get common services');
            }
        });
    },
    
    /**
     * Create service history from service order
     */
    createFromServiceOrder: function(serviceOrderId, callback) {
        $.ajax({
            url: 'index.php?module=DM_ServiceHistory&action=create_from_service_order',
            type: 'POST',
            data: {
                service_order_id: serviceOrderId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (callback && typeof callback === 'function') {
                        callback(response.history_id);
                    }
                    alert('Service history created successfully');
                } else {
                    alert('Error creating service history: ' + response.error);
                }
            },
            error: function() {
                alert('Failed to create service history from service order');
            }
        });
    },
    
    /**
     * Open parts lookup for adding to service history
     */
    openPartsLookup: function() {
        var self = this;
        
        // Open parts inventory lookup
        if (typeof DM_PartsInventory !== 'undefined') {
            DM_PartsInventory.openPartsLookup(function(partData) {
                self.addPartToHistory(partData);
            });
        } else {
            // Fallback to simple popup
            var url = 'index.php?module=DM_PartsInventory&action=parts_lookup';
            window.open(url, 'PartsLookup', 'width=900,height=700,scrollbars=yes,resizable=yes');
        }
    },
    
    /**
     * Validate all service data
     */
    validateServiceData: function() {
        // Perform any initial validation
        var serviceDate = $('#service_date').val();
        var mileage = $('#mileage').val();
        
        if (serviceDate) {
            this.validateServiceDate(serviceDate);
        }
        
        if (mileage) {
            this.validateMileage(mileage);
        }
    },
    
    /**
     * Format date for display
     */
    formatDate: function(dateString) {
        if (!dateString) return 'N/A';
        
        var date = new Date(dateString);
        return date.toLocaleDateString();
    },
    
    /**
     * Show service timeline
     */
    showServiceTimeline: function(vehicleId) {
        this.loadVehicleHistory(vehicleId, 20);
    }
};

// Initialize when document is ready
$(document).ready(function() {
    if (typeof SUGAR !== 'undefined' && SUGAR.App) {
        // SuiteCRM 7.x initialization
        SUGAR.App.view.invokeParent = function() {
            DM_ServiceHistory.init();
        };
    } else {
        // SuiteCRM 8.x or fallback initialization
        DM_ServiceHistory.init();
    }
});

// Legacy support for older versions
if (typeof addForm !== 'undefined') {
    addForm('EditView');
    addForm('DetailView');
}