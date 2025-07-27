/**
 * SuiteCRM Service & Parts Hub - Parts Inventory JavaScript
 * 
 * Client-side functionality for Parts Inventory module including
 * stock calculations, price markup calculations, and inventory management
 */

var DM_PartsInventory = {
    
    /**
     * Initialize module functionality
     */
    init: function() {
        console.log('DM_PartsInventory: Initializing JavaScript functionality');
        
        // Bind event handlers
        this.bindCalculationHandlers();
        this.bindValidationHandlers();
        this.bindStockHandlers();
        
        // Initialize on page load
        this.calculateMarkup();
        this.checkStockLevels();
    },
    
    /**
     * Bind calculation event handlers
     */
    bindCalculationHandlers: function() {
        var self = this;
        
        // Markup calculation
        $(document).on('change blur', '#cost, #retail_price', function() {
            self.calculateMarkup();
        });
        
        // Auto-calculate when price fields change
        $(document).on('keyup', '#cost, #retail_price', function() {
            self.debounce(function() {
                self.calculateMarkup();
            }, 500);
        });
    },
    
    /**
     * Bind validation event handlers
     */
    bindValidationHandlers: function() {
        var self = this;
        
        // Part number validation
        $(document).on('blur', '#part_number', function() {
            self.validatePartNumber($(this).val());
        });
        
        // Quantity validation
        $(document).on('blur', '#quantity_on_hand, #reorder_point', function() {
            self.validateQuantity($(this).val(), $(this).attr('id'));
        });
        
        // Price validation
        $(document).on('blur', '#cost, #retail_price', function() {
            self.validatePrice($(this).val(), $(this).attr('id'));
        });
    },
    
    /**
     * Bind stock management handlers
     */
    bindStockHandlers: function() {
        var self = this;
        
        // Stock level changes
        $(document).on('change', '#quantity_on_hand, #reorder_point', function() {
            self.checkStockLevels();
        });
        
        // Category change - load related parts
        $(document).on('change', '#category', function() {
            var category = $(this).val();
            if (category) {
                self.loadPartsByCategory(category);
            }
        });
    },
    
    /**
     * Calculate markup percentage and profit
     */
    calculateMarkup: function() {
        var cost = parseFloat($('#cost').val()) || 0;
        var retailPrice = parseFloat($('#retail_price').val()) || 0;
        
        if (cost > 0 && retailPrice > 0) {
            var markup = ((retailPrice - cost) / cost) * 100;
            var profit = retailPrice - cost;
            
            // Display markup information (if markup display elements exist)
            if ($('#markup_percentage').length) {
                $('#markup_percentage').text(markup.toFixed(2) + '%');
            }
            
            if ($('#profit_amount').length) {
                $('#profit_amount').text('$' + profit.toFixed(2));
            }
            
            console.log('DM_PartsInventory: Calculated markup - ' + markup.toFixed(2) + '%, Profit: $' + profit.toFixed(2));
        }
    },
    
    /**
     * Check stock levels and display warnings
     */
    checkStockLevels: function() {
        var quantityOnHand = parseInt($('#quantity_on_hand').val()) || 0;
        var reorderPoint = parseInt($('#reorder_point').val()) || 0;
        
        // Remove existing stock warnings
        $('.stock-warning').remove();
        
        var stockStatus = 'In Stock';
        var stockClass = 'success';
        
        if (quantityOnHand === 0) {
            stockStatus = 'Out of Stock';
            stockClass = 'danger';
            this.showStockWarning('This part is out of stock!', 'danger');
        } else if (quantityOnHand <= reorderPoint && reorderPoint > 0) {
            stockStatus = 'Low Stock';
            stockClass = 'warning';
            this.showStockWarning('This part is below the reorder point.', 'warning');
        }
        
        // Update stock status display (if status display element exists)
        if ($('#stock_status').length) {
            $('#stock_status')
                .removeClass('label-success label-warning label-danger')
                .addClass('label-' + stockClass)
                .text(stockStatus);
        }
        
        console.log('DM_PartsInventory: Stock status - ' + stockStatus + ' (Qty: ' + quantityOnHand + ', Reorder: ' + reorderPoint + ')');
    },
    
    /**
     * Show stock warning message
     */
    showStockWarning: function(message, type) {
        var alertClass = 'alert-' + type;
        var warning = '<div class="alert ' + alertClass + ' stock-warning" role="alert">' +
                     '<strong>Stock Alert:</strong> ' + message +
                     '</div>';
        
        // Insert warning after quantity field
        $('#quantity_on_hand').closest('.form-group, .slot').after(warning);
    },
    
    /**
     * Validate part number
     */
    validatePartNumber: function(partNumber) {
        if (partNumber && partNumber.length < 3) {
            alert('Part number should be at least 3 characters long');
            $('#part_number').focus();
            return false;
        }
        return true;
    },
    
    /**
     * Validate quantity values
     */
    validateQuantity: function(quantity, fieldId) {
        if (quantity && (isNaN(quantity) || quantity < 0)) {
            alert('Please enter a valid quantity (must be 0 or greater)');
            $('#' + fieldId).focus();
            return false;
        }
        return true;
    },
    
    /**
     * Validate price values
     */
    validatePrice: function(price, fieldId) {
        if (price && (isNaN(price) || price < 0)) {
            alert('Please enter a valid price (must be 0 or greater)');
            $('#' + fieldId).focus();
            return false;
        }
        return true;
    },
    
    /**
     * Load parts by category
     */
    loadPartsByCategory: function(category) {
        $.ajax({
            url: 'index.php?module=DM_PartsInventory&action=get_parts_by_category',
            type: 'POST',
            data: {
                category: category
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.parts) {
                    console.log('DM_PartsInventory: Found ' + response.count + ' parts in category: ' + category);
                    // Could populate a related parts list here
                }
            },
            error: function() {
                console.error('DM_PartsInventory: Failed to load parts by category');
            }
        });
    },
    
    /**
     * Search parts
     */
    searchParts: function(criteria) {
        $.ajax({
            url: 'index.php?module=DM_PartsInventory&action=search_parts',
            type: 'POST',
            data: criteria,
            dataType: 'json',
            success: function(response) {
                if (response.success && response.parts) {
                    console.log('DM_PartsInventory: Found ' + response.count + ' parts matching search criteria');
                    return response.parts;
                }
            },
            error: function() {
                console.error('DM_PartsInventory: Failed to search parts');
            }
        });
    },
    
    /**
     * Update stock quantity
     */
    updateStock: function(partId, quantity, operation) {
        if (!partId || !quantity || !operation) {
            alert('Missing required parameters for stock update');
            return;
        }
        
        $.ajax({
            url: 'index.php?module=DM_PartsInventory&action=update_stock',
            type: 'POST',
            data: {
                part_id: partId,
                quantity: quantity,
                operation: operation
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#quantity_on_hand').val(response.new_quantity);
                    DM_PartsInventory.checkStockLevels();
                    alert('Stock updated successfully. New quantity: ' + response.new_quantity);
                } else {
                    alert('Error updating stock: ' + response.error);
                }
            },
            error: function() {
                alert('Failed to update stock. Please try again.');
            }
        });
    },
    
    /**
     * Open parts lookup popup for service orders
     */
    openPartsLookup: function(callback) {
        var url = 'index.php?module=DM_PartsInventory&action=parts_lookup';
        var popup = window.open(url, 'PartsLookup', 'width=900,height=700,scrollbars=yes,resizable=yes');
        
        // Store callback for when part is selected
        if (callback && typeof callback === 'function') {
            window.partsLookupCallback = callback;
        }
        
        return popup;
    },
    
    /**
     * Select part from lookup (called from popup)
     */
    selectPart: function(partId, partNumber, description, retailPrice) {
        var partData = {
            id: partId,
            part_number: partNumber,
            description: description,
            retail_price: retailPrice
        };
        
        // Call callback function if available
        if (window.partsLookupCallback && typeof window.partsLookupCallback === 'function') {
            window.partsLookupCallback(partData);
        }
        
        // Close popup
        if (window.opener) {
            window.close();
        }
        
        console.log('DM_PartsInventory: Selected part - ' + partNumber + ': ' + description);
    },
    
    /**
     * Get low stock parts
     */
    getLowStockParts: function(callback) {
        $.ajax({
            url: 'index.php?module=DM_PartsInventory&action=get_low_stock_parts',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (callback && typeof callback === 'function') {
                        callback(response.parts);
                    }
                    console.log('DM_PartsInventory: Found ' + response.count + ' parts with low stock');
                }
            },
            error: function() {
                console.error('DM_PartsInventory: Failed to get low stock parts');
            }
        });
    },
    
    /**
     * Show low stock alert
     */
    showLowStockAlert: function() {
        this.getLowStockParts(function(parts) {
            if (parts && parts.length > 0) {
                var message = 'You have ' + parts.length + ' parts with low stock:\n\n';
                for (var i = 0; i < Math.min(parts.length, 5); i++) {
                    message += '• ' + parts[i].part_number + ' - Qty: ' + parts[i].quantity_on_hand + '\n';
                }
                if (parts.length > 5) {
                    message += '... and ' + (parts.length - 5) + ' more';
                }
                alert(message);
            }
        });
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
            DM_PartsInventory.init();
        };
    } else {
        // SuiteCRM 8.x or fallback initialization
        DM_PartsInventory.init();
    }
});

// Legacy support for older versions
if (typeof addForm !== 'undefined') {
    addForm('EditView');
    addForm('DetailView');
}