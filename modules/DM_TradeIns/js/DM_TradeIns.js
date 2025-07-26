/**
 * SuiteCRM Trade-In Manager - JavaScript Functions
 * 
 * This file contains JavaScript functions for real-time calculations,
 * VIN decoding, and interactive features in the Trade-In Manager module.
 */

/**
 * Decode VIN using NHTSA API and populate vehicle fields
 */
function decodeVIN(vinField) {
    console.log('DM_TradeIns: decodeVIN() called for VIN:', vinField.value);
    
    var vin = vinField.value.trim().toUpperCase();
    
    // Basic VIN validation
    if (vin.length !== 17) {
        if (vin.length > 0) {
            alert('VIN must be exactly 17 characters');
        }
        return;
    }
    
    // Show loading indicator
    var getValuesBtn = document.getElementById('get_values_btn');
    if (getValuesBtn) {
        getValuesBtn.value = 'Decoding VIN...';
        getValuesBtn.disabled = true;
    }
    
    // Call NHTSA VIN Decoder API (free)
    var apiUrl = 'https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/' + vin + '?format=json';
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            console.log('DM_TradeIns: NHTSA API response:', data);
            
            if (data.Results && data.Results.length > 0) {
                var results = data.Results;
                
                // Extract vehicle information
                var vehicleInfo = {};
                results.forEach(function(item) {
                    switch(item.Variable) {
                        case 'Model Year':
                            vehicleInfo.year = item.Value;
                            break;
                        case 'Make':
                            vehicleInfo.make = item.Value;
                            break;
                        case 'Model':
                            vehicleInfo.model = item.Value;
                            break;
                        case 'Trim':
                            vehicleInfo.trim = item.Value;
                            break;
                        case 'Vehicle Type':
                            vehicleInfo.vehicleType = item.Value;
                            break;
                    }
                });
                
                // Populate fields if values found
                if (vehicleInfo.year && vehicleInfo.year !== 'Not Applicable') {
                    setFieldValue('year', vehicleInfo.year);
                }
                if (vehicleInfo.make && vehicleInfo.make !== 'Not Applicable') {
                    setFieldValue('make', vehicleInfo.make);
                }
                if (vehicleInfo.model && vehicleInfo.model !== 'Not Applicable') {
                    setFieldValue('model', vehicleInfo.model);
                }
                if (vehicleInfo.trim && vehicleInfo.trim !== 'Not Applicable') {
                    setFieldValue('trim', vehicleInfo.trim);
                }
                
                console.log('DM_TradeIns: VIN decoded successfully:', vehicleInfo);
                
                // Show success message
                if (Object.keys(vehicleInfo).length > 0) {
                    showMessage('VIN decoded successfully', 'success');
                    
                    // Auto-generate name
                    generateDisplayName();
                } else {
                    showMessage('VIN decoded but no vehicle information found', 'warning');
                }
                
            } else {
                console.log('DM_TradeIns: No results from NHTSA API');
                showMessage('Could not decode VIN. Please verify VIN is correct.', 'error');
            }
        })
        .catch(error => {
            console.error('DM_TradeIns: Error decoding VIN:', error);
            showMessage('Error decoding VIN. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button
            if (getValuesBtn) {
                getValuesBtn.value = 'Get Market Values';
                getValuesBtn.disabled = false;
            }
        });
}

/**
 * Get market values for the vehicle using Vehicle Databases API
 */
function getMarketValues() {
    console.log('DM_TradeIns: getMarketValues() called');
    
    var year = getFieldValue('year');
    var make = getFieldValue('make');
    var model = getFieldValue('model');
    var mileage = getFieldValue('mileage');
    
    if (!year || !make || !model) {
        alert('Please enter Year, Make, and Model before getting market values');
        return;
    }
    
    // Show loading indicator
    var btn = document.getElementById('get_values_btn');
    if (btn) {
        btn.value = 'Getting Values...';
        btn.disabled = true;
    }
    
    // For now, simulate API call with demo values
    // In real implementation, this would call Vehicle Databases API
    setTimeout(function() {
        var baseValue = calculateBaseValue(year, make, model);
        var mileageAdjustment = calculateMileageAdjustment(mileage, year);
        
        var retailValue = Math.round(baseValue * 1.15 - mileageAdjustment);
        var tradeValue = Math.round(baseValue * 0.85 - mileageAdjustment);
        var privateValue = Math.round(baseValue - mileageAdjustment);
        
        // Populate market value fields
        setFieldValue('market_value_retail', retailValue);
        setFieldValue('market_value_trade', tradeValue);
        setFieldValue('market_value_private', privateValue);
        setFieldValue('valuation_source', 'Demo Calculation');
        setFieldValue('valuation_date', getCurrentDateTime());
        
        console.log('DM_TradeIns: Market values calculated - Retail:', retailValue, 'Trade:', tradeValue, 'Private:', privateValue);
        
        showMessage('Market values retrieved successfully', 'success');
        
        // Calculate variances
        calculateVariances();
        
        // Reset button
        if (btn) {
            btn.value = 'Refresh Market Values';
            btn.disabled = false;
        }
    }, 2000); // Simulate API delay
}

/**
 * Calculate base value for demo purposes
 */
function calculateBaseValue(year, make, model) {
    var currentYear = new Date().getFullYear();
    var age = currentYear - parseInt(year);
    
    // Demo calculation - in real implementation this would use API
    var baseValues = {
        'TOYOTA': 25000,
        'HONDA': 23000,
        'FORD': 20000,
        'CHEVROLET': 19000,
        'NISSAN': 18000,
        'BMW': 35000,
        'MERCEDES-BENZ': 40000,
        'AUDI': 32000
    };
    
    var baseValue = baseValues[make.toUpperCase()] || 20000;
    
    // Depreciation calculation
    var depreciationRate = 0.15; // 15% per year
    var depreciatedValue = baseValue * Math.pow(1 - depreciationRate, age);
    
    return Math.max(depreciatedValue, baseValue * 0.1); // Minimum 10% of original value
}

/**
 * Calculate mileage adjustment
 */
function calculateMileageAdjustment(mileage, year) {
    if (!mileage || !year) return 0;
    
    var currentYear = new Date().getFullYear();
    var age = currentYear - parseInt(year);
    var expectedMileage = age * 12000; // 12,000 miles per year average
    var mileageDifference = parseInt(mileage) - expectedMileage;
    
    // $0.10 per mile adjustment
    return mileageDifference * 0.10;
}

/**
 * Calculate variances between customer asking and market values
 */
function calculateVariances() {
    console.log('DM_TradeIns: calculateVariances() called');
    
    var customerAsking = parseFloat(getFieldValue('customer_asking')) || 0;
    var marketTrade = parseFloat(getFieldValue('market_value_trade')) || 0;
    var marketRetail = parseFloat(getFieldValue('market_value_retail')) || 0;
    
    if (customerAsking > 0 && marketTrade > 0) {
        var variance = customerAsking - marketTrade;
        var percentVariance = (variance / marketTrade) * 100;
        
        console.log('DM_TradeIns: Customer asking vs Trade variance:', variance, '(' + percentVariance.toFixed(1) + '%)');
        
        // Visual indicators could be added here
        if (Math.abs(percentVariance) > 15) {
            console.log('DM_TradeIns: Large variance detected - may need appraisal');
        }
    }
}

/**
 * Calculate trade equity (trade value vs payoff)
 */
function calculateTradeEquity() {
    console.log('DM_TradeIns: calculateTradeEquity() called');
    
    var appraisedValue = parseFloat(getFieldValue('appraised_value')) || 0;
    var marketTrade = parseFloat(getFieldValue('market_value_trade')) || 0;
    var payoffAmount = parseFloat(getFieldValue('payoff_amount')) || 0;
    
    var tradeValue = appraisedValue > 0 ? appraisedValue : marketTrade;
    var equity = tradeValue - payoffAmount;
    
    console.log('DM_TradeIns: Trade equity calculated - Value:', tradeValue, 'Payoff:', payoffAmount, 'Equity:', equity);
    
    // Visual indicators for equity status
    if (equity < 0) {
        console.log('DM_TradeIns: Negative equity detected:', equity);
    }
}

/**
 * Calculate profitability estimates
 */
function calculateProfitability() {
    console.log('DM_TradeIns: calculateProfitability() called');
    
    var tradeAllowance = parseFloat(getFieldValue('trade_allowance')) || 0;
    var actualCashValue = parseFloat(getFieldValue('actual_cash_value')) || 0;
    var reconditioningCost = parseFloat(getFieldValue('reconditioning_cost')) || 0;
    
    if (actualCashValue > 0 && tradeAllowance > 0) {
        var grossProfit = actualCashValue - tradeAllowance - reconditioningCost;
        setFieldValue('estimated_profit', grossProfit.toFixed(2));
        
        console.log('DM_TradeIns: Profitability calculated - Gross profit:', grossProfit);
    }
}

/**
 * Update workflow fields based on status changes
 */
function updateWorkflowFields() {
    console.log('DM_TradeIns: updateWorkflowFields() called');
    
    var status = getFieldValue('status');
    
    switch(status) {
        case 'Appraised':
            if (!getFieldValue('appraisal_date')) {
                setFieldValue('appraisal_date', getCurrentDateTime());
            }
            break;
        case 'Used':
            setFieldValue('used_in_deal', '1');
            break;
        case 'Rejected':
            setFieldValue('used_in_deal', '0');
            break;
    }
}

/**
 * Generate display name based on vehicle info and customer
 */
function generateDisplayName() {
    var year = getFieldValue('year');
    var make = getFieldValue('make');
    var model = getFieldValue('model');
    
    if (year && make && model) {
        var displayName = year + ' ' + make + ' ' + model;
        
        // Add customer name if available
        var customerName = getFieldValue('customer_name');
        if (customerName) {
            displayName += ' - ' + customerName;
        }
        
        setFieldValue('name', displayName);
        console.log('DM_TradeIns: Generated display name:', displayName);
    }
}

/**
 * Helper function to get field value
 */
function getFieldValue(fieldName) {
    var field = document.getElementsByName(fieldName)[0];
    return field ? field.value : '';
}

/**
 * Helper function to set field value
 */
function setFieldValue(fieldName, value) {
    var field = document.getElementsByName(fieldName)[0];
    if (field) {
        field.value = value;
        
        // Trigger change event for dependent calculations
        if (field.onchange) {
            field.onchange();
        }
    }
}

/**
 * Get current date/time in MySQL format
 */
function getCurrentDateTime() {
    var now = new Date();
    return now.getFullYear() + '-' + 
           String(now.getMonth() + 1).padStart(2, '0') + '-' + 
           String(now.getDate()).padStart(2, '0') + ' ' +
           String(now.getHours()).padStart(2, '0') + ':' + 
           String(now.getMinutes()).padStart(2, '0') + ':' + 
           String(now.getSeconds()).padStart(2, '0');
}

/**
 * Show user messages
 */
function showMessage(message, type) {
    console.log('DM_TradeIns: ' + type.toUpperCase() + ' - ' + message);
    
    // For now just use alert, could be enhanced with better UI
    if (type === 'error') {
        alert('Error: ' + message);
    } else if (type === 'success') {
        // Could show a green success message
        console.log('Success: ' + message);
    } else if (type === 'warning') {
        console.log('Warning: ' + message);
    }
}

/**
 * Initialize form when page loads
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DM_TradeIns: JavaScript initialized');
    
    // Auto-calculate when fields change
    var fieldsToWatch = ['customer_asking', 'payoff_amount', 'appraised_value', 'trade_allowance', 'actual_cash_value', 'reconditioning_cost'];
    
    fieldsToWatch.forEach(function(fieldName) {
        var field = document.getElementsByName(fieldName)[0];
        if (field) {
            field.addEventListener('change', function() {
                calculateVariances();
                calculateTradeEquity();
                calculateProfitability();
            });
        }
    });
    
    // Auto-generate name when vehicle info changes
    var vehicleFields = ['year', 'make', 'model', 'customer_name'];
    vehicleFields.forEach(function(fieldName) {
        var field = document.getElementsByName(fieldName)[0];
        if (field) {
            field.addEventListener('change', generateDisplayName);
        }
    });
});

console.log('DM_TradeIns: JavaScript file loaded successfully'); 