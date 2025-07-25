/**
 * F&I Deal Center JavaScript Calculation Engine
 * Handles real-time financial calculations, validation, and UI updates
 */

if (typeof SUGAR === 'undefined') {
    SUGAR = {};
}

SUGAR.DM_FIDeals = {
    
    // Configuration and constants
    config: {
        maxLoanTerm: 96,
        minLoanTerm: 12,
        maxInterestRate: 29.99,
        minInterestRate: 0.00,
        defaultTerm: 60,
        defaultRate: 7.99,
        currencyFormatter: new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }),
        percentFormatter: new Intl.NumberFormat('en-US', {
            style: 'percent',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    },

    // Tax rates by state (expandable)
    taxRates: {
        'AL': { state: 2.00, avgLocal: 5.21 },
        'AK': { state: 0.00, avgLocal: 1.43 },
        'AZ': { state: 5.60, avgLocal: 2.77 },
        'AR': { state: 6.50, avgLocal: 2.93 },
        'CA': { state: 7.25, avgLocal: 3.33 },
        'CO': { state: 2.90, avgLocal: 4.73 },
        'CT': { state: 6.35, avgLocal: 0.00 },
        'DE': { state: 0.00, avgLocal: 0.00 },
        'FL': { state: 6.00, avgLocal: 1.05 },
        'GA': { state: 4.00, avgLocal: 3.29 },
        // Add more states as needed
        'DEFAULT': { state: 6.00, avgLocal: 2.50 }
    },

    // Fee schedules by state
    feeSchedules: {
        'CA': {
            docFee: 85.00,
            titleFee: 23.00,
            licenseFee: 46.00,
            smogFee: 8.25
        },
        'TX': {
            docFee: 150.00,
            titleFee: 33.00,
            licenseFee: 51.75,
            inspectionFee: 7.00
        },
        'FL': {
            docFee: 995.00,
            titleFee: 77.25,
            licenseFee: 225.00
        },
        'DEFAULT': {
            docFee: 300.00,
            titleFee: 50.00,
            licenseFee: 100.00
        }
    },

    /**
     * Initialize the F&I calculator
     */
    init: function() {
        console.log('F&I Deal Center: Initializing calculator engine...');
        
        // Bind event handlers
        this.bindEventHandlers();
        
        // Load customer state tax rates
        this.loadStateTaxRates();
        
        // Set up real-time validation
        this.setupValidation();
        
        // Initialize calculation fields
        this.initializeCalculationFields();
        
        console.log('F&I Deal Center: Calculator engine initialized successfully');
    },

    /**
     * Bind all event handlers for real-time calculations
     */
    bindEventHandlers: function() {
        const self = this;
        
        // Core financial fields that trigger recalculation
        const calcFields = [
            'sales_price', 'down_payment', 'trade_allowance', 'trade_payoff',
            'rebates', 'dealer_fees', 'government_fees', 'term_months', 
            'interest_rate', 'rate_markup', 'warranty_total', 'gap_amount',
            'etch_amount', 'maintenance_amount', 'other_products_total'
        ];
        
        calcFields.forEach(function(fieldName) {
            const field = document.getElementById(fieldName);
            if (field) {
                field.addEventListener('blur', function() {
                    // First ensure we have a clean numeric value, then validate and format
                    const rawValue = this.value.replace(/[$,\s%]/g, '').trim();
                    const numValue = parseFloat(rawValue) || 0;
                    
                    // Update field with clean numeric value before validation
                    if (numValue > 0) {
                        this.value = numValue;
                    } else {
                        this.value = '';
                    }
                    
                    self.validateField(this);
                    self.formatCurrencyField(this); // Format only on blur, not on input
                    self.recalculateAll();
                });
                
                // Allow natural typing without cursor jumping
                field.addEventListener('focus', function() {
                    // Remove formatting when user focuses on field for easier editing
                    if (self.isCurrencyField(this.id)) {
                        const value = self.getFieldValue(this.id);
                        this.value = value > 0 ? value : '';
                        // Select all text for easy replacement
                        this.select();
                    }
                });
                
                // Add input filtering for numeric fields
                if (self.isCurrencyField(fieldName) || self.isPercentField(fieldName)) {
                    field.addEventListener('keypress', function(e) {
                        // Allow: backspace, delete, tab, escape, enter, decimal point
                        if ([8, 9, 27, 13, 46, 110, 190].indexOf(e.keyCode) !== -1 ||
                            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                            (e.keyCode === 65 && e.ctrlKey === true) ||
                            (e.keyCode === 67 && e.ctrlKey === true) ||
                            (e.keyCode === 86 && e.ctrlKey === true) ||
                            (e.keyCode === 88 && e.ctrlKey === true) ||
                            // Allow: home, end, left, right
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                            return;
                        }
                        // Ensure that it is a number and stop the keypress
                        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                            e.preventDefault();
                        }
                        // Only allow one decimal point
                        if (e.key === '.' && this.value.indexOf('.') !== -1) {
                            e.preventDefault();
                        }
                    });
                }
            }
        });

        // Finance method change handler
        const financeMethod = document.getElementById('finance_method');
        if (financeMethod) {
            financeMethod.addEventListener('change', function() {
                self.handleFinanceMethodChange(this.value);
            });
        }

        // Customer state change handler
        const customerState = document.getElementById('customer_state');
        if (customerState) {
            customerState.addEventListener('change', function() {
                self.calculateTaxes();
                self.recalculateAll();
            });
        }
    },

    /**
     * Initialize calculation fields with proper formatting
     */
    initializeCalculationFields: function() {
        // Set readonly styling for calculated fields
        const calculatedFields = [
            'amount_financed', 'monthly_payment', 'total_of_payments', 
            'finance_charge', 'finance_reserve', 'backend_gross', 
            'frontend_gross', 'total_gross'
        ];
        
        calculatedFields.forEach(function(fieldName) {
            const field = document.getElementById(fieldName);
            if (field) {
                field.classList.add('calculated-field');
                field.style.backgroundColor = '#f8f9fa';
                field.style.fontWeight = 'bold';
            }
        });

        // Trigger initial calculation
        this.recalculateAll();
    },

    /**
     * Handle finance method changes (Cash/Finance/Lease)
     */
    handleFinanceMethodChange: function(method) {
        const financingFields = document.querySelectorAll('.financing-field');
        const leaseFields = document.querySelectorAll('.lease-field');
        
        if (method === 'Cash') {
            // Hide financing and lease fields
            financingFields.forEach(field => field.style.display = 'none');
            leaseFields.forEach(field => field.style.display = 'none');
            
            // Clear financing values
            this.setFieldValue('term_months', 0);
            this.setFieldValue('interest_rate', 0);
            this.setFieldValue('monthly_payment', 0);
            
        } else if (method === 'Finance') {
            // Show financing fields, hide lease fields
            financingFields.forEach(field => field.style.display = 'block');
            leaseFields.forEach(field => field.style.display = 'none');
            
            // Set default financing values
            this.setFieldValue('term_months', this.config.defaultTerm);
            this.setFieldValue('interest_rate', this.config.defaultRate);
            
        } else if (method === 'Lease') {
            // Show lease fields, hide standard financing
            leaseFields.forEach(field => field.style.display = 'block');
            financingFields.forEach(field => field.style.display = 'none');
            
            // Set default lease values
            this.setFieldValue('term_months', 36); // Typical lease term
            this.setFieldValue('interest_rate', 0); // Lease factor handled separately
        }
        
        this.recalculateAll();
    },

    /**
     * Master recalculation function
     */
    recalculateAll: function() {
        console.log('F&I Deal Center: Starting complete recalculation...');
        
        try {
            // Step 1: Calculate taxes and fees
            this.calculateTaxes();
            this.calculateFees();
            
            // Step 2: Calculate amount financed
            this.calculateAmountFinanced();
            
            // Step 3: Calculate payments based on finance method
            const financeMethod = this.getFieldValue('finance_method');
            if (financeMethod === 'Finance') {
                this.calculateLoanPayment();
            } else if (financeMethod === 'Lease') {
                this.calculateLeasePayment();
            } else if (financeMethod === 'Cash') {
                this.calculateCashDeal();
            }
            
            // Step 4: Calculate profitability
            this.calculateProfitability();
            
            // Step 5: Update deal summary
            this.updateDealSummary();
            
            console.log('F&I Deal Center: Recalculation completed successfully');
            
        } catch (error) {
            console.error('F&I Deal Center: Error during recalculation:', error);
            this.showError('Calculation error: ' + error.message);
        }
    },

    /**
     * Calculate state and local taxes
     */
    calculateTaxes: function() {
        const salesPrice = this.getFieldValue('sales_price');
        const tradeAllowance = this.getFieldValue('trade_allowance');
        const customerState = this.getFieldValue('customer_state') || 'DEFAULT';
        
        // Get tax rates for customer's state
        const taxInfo = this.taxRates[customerState] || this.taxRates['DEFAULT'];
        const stateTaxRate = taxInfo.state / 100;
        const localTaxRate = taxInfo.avgLocal / 100;
        const totalTaxRate = stateTaxRate + localTaxRate;
        
        // Calculate taxable amount (sales price minus trade allowance in most states)
        const taxableAmount = Math.max(0, salesPrice - tradeAllowance);
        
        // Calculate taxes
        const stateTax = taxableAmount * stateTaxRate;
        const localTax = taxableAmount * localTaxRate;
        const totalTax = taxableAmount * totalTaxRate;
        
        // Update hidden tax fields
        this.setFieldValue('state_tax', stateTax);
        this.setFieldValue('local_tax', localTax);
        this.setFieldValue('total_tax', totalTax);
        
        // Update government fees to include calculated tax
        const currentGovFees = this.getFieldValue('government_fees');
        const nonTaxFees = this.getFieldValue('license_fees') || 0;
        this.setFieldValue('government_fees', totalTax + nonTaxFees);
        
        return totalTax;
    },

    /**
     * Calculate state-specific fees
     */
    calculateFees: function() {
        const customerState = this.getFieldValue('customer_state') || 'DEFAULT';
        const fees = this.feeSchedules[customerState] || this.feeSchedules['DEFAULT'];
        
        // Auto-populate standard fees if not manually set
        if (!this.getFieldValue('dealer_fees') || this.getFieldValue('dealer_fees') === 0) {
            this.setFieldValue('dealer_fees', fees.docFee);
        }
        
        // Calculate total license fees
        const titleFee = fees.titleFee || 0;
        const licenseFee = fees.licenseFee || 0;
        const otherFees = fees.smogFee || fees.inspectionFee || 0;
        const totalLicenseFees = titleFee + licenseFee + otherFees;
        
        this.setFieldValue('license_fees', totalLicenseFees);
        
        return totalLicenseFees;
    },

    /**
     * Calculate amount to be financed
     */
    calculateAmountFinanced: function() {
        const salesPrice = this.getFieldValue('sales_price');
        const downPayment = this.getFieldValue('down_payment');
        const tradeAllowance = this.getFieldValue('trade_allowance');
        const tradePayoff = this.getFieldValue('trade_payoff');
        const rebates = this.getFieldValue('rebates');
        const dealerFees = this.getFieldValue('dealer_fees');
        const governmentFees = this.getFieldValue('government_fees');
        const warrantyTotal = this.getFieldValue('warranty_total');
        const gapAmount = this.getFieldValue('gap_amount');
        const etchAmount = this.getFieldValue('etch_amount');
        const maintenanceAmount = this.getFieldValue('maintenance_amount');
        const otherProductsTotal = this.getFieldValue('other_products_total');
        
        // Calculate net trade value
        const netTradeValue = tradeAllowance - tradePayoff;
        
        // Calculate total amount financed
        const amountFinanced = salesPrice 
            - downPayment 
            - netTradeValue 
            - rebates 
            + dealerFees 
            + governmentFees 
            + warrantyTotal 
            + gapAmount 
            + etchAmount 
            + maintenanceAmount 
            + otherProductsTotal;
        
        this.setFieldValue('amount_financed', Math.max(0, amountFinanced));
        
        return amountFinanced;
    },

    /**
     * Calculate standard loan payment
     */
    calculateLoanPayment: function() {
        const principal = this.getFieldValue('amount_financed');
        const annualRate = this.getFieldValue('interest_rate') / 100;
        const termMonths = this.getFieldValue('term_months');
        
        if (principal <= 0 || termMonths <= 0) {
            this.setFieldValue('monthly_payment', 0);
            this.setFieldValue('total_of_payments', 0);
            this.setFieldValue('finance_charge', 0);
            return;
        }
        
        let monthlyPayment = 0;
        
        if (annualRate === 0) {
            // No interest calculation
            monthlyPayment = principal / termMonths;
        } else {
            // Standard loan payment calculation: PMT = P * [r(1+r)^n] / [(1+r)^n - 1]
            const monthlyRate = annualRate / 12;
            const termPower = Math.pow(1 + monthlyRate, termMonths);
            monthlyPayment = principal * (monthlyRate * termPower) / (termPower - 1);
        }
        
        const totalOfPayments = monthlyPayment * termMonths;
        const financeCharge = totalOfPayments - principal;
        
        this.setFieldValue('monthly_payment', monthlyPayment);
        this.setFieldValue('total_of_payments', totalOfPayments);
        this.setFieldValue('finance_charge', financeCharge);
        
        // Calculate finance reserve (dealer profit from rate markup)
        this.calculateFinanceReserve();
        
        return monthlyPayment;
    },

    /**
     * Calculate lease payment
     */
    calculateLeasePayment: function() {
        const vehiclePrice = this.getFieldValue('sales_price');
        const downPayment = this.getFieldValue('down_payment');
        const termMonths = this.getFieldValue('term_months');
        const residualPercent = this.getFieldValue('residual_percent') || 55; // Default 55%
        const moneyFactor = this.getFieldValue('money_factor') || 0.0025; // Default money factor
        
        if (vehiclePrice <= 0 || termMonths <= 0) {
            this.setFieldValue('monthly_payment', 0);
            return;
        }
        
        // Calculate lease values
        const residualValue = vehiclePrice * (residualPercent / 100);
        const depreciationAmount = vehiclePrice - residualValue;
        const depreciationPayment = depreciationAmount / termMonths;
        const financePayment = (vehiclePrice + residualValue) * moneyFactor;
        const monthlyPayment = depreciationPayment + financePayment;
        
        // Adjust for down payment (reduces depreciation)
        const adjustedPayment = monthlyPayment - (downPayment / termMonths);
        
        this.setFieldValue('monthly_payment', Math.max(0, adjustedPayment));
        this.setFieldValue('residual_value', residualValue);
        this.setFieldValue('total_of_payments', adjustedPayment * termMonths);
        
        return adjustedPayment;
    },

    /**
     * Calculate cash deal (no financing)
     */
    calculateCashDeal: function() {
        this.setFieldValue('monthly_payment', 0);
        this.setFieldValue('total_of_payments', 0);
        this.setFieldValue('finance_charge', 0);
        this.setFieldValue('finance_reserve', 0);
    },

    /**
     * Calculate finance reserve (dealer profit from rate markup)
     */
    calculateFinanceReserve: function() {
        const principal = this.getFieldValue('amount_financed');
        const sellRate = this.getFieldValue('interest_rate');
        const buyRate = this.getFieldValue('buy_rate') || sellRate;
        const rateMarkup = sellRate - buyRate;
        const termMonths = this.getFieldValue('term_months');
        
        if (principal <= 0 || termMonths <= 0 || rateMarkup <= 0) {
            this.setFieldValue('finance_reserve', 0);
            return;
        }
        
        // Simple reserve calculation: (Rate Markup / 100) * Principal * (Term / 12) * Reserve Factor
        const reserveFactor = 0.75; // Typical reserve factor
        const financeReserve = (rateMarkup / 100) * principal * (termMonths / 12) * reserveFactor;
        
        this.setFieldValue('finance_reserve', financeReserve);
        
        return financeReserve;
    },

    /**
     * Calculate total deal profitability
     */
    calculateProfitability: function() {
        const salesPrice = this.getFieldValue('sales_price');
        const cost = this.getFieldValue('vehicle_cost') || (salesPrice * 0.85); // Default 15% margin
        const financeReserve = this.getFieldValue('finance_reserve');
        const warrantyCommission = this.getFieldValue('warranty_total') * 0.25; // 25% commission
        const gapCommission = this.getFieldValue('gap_amount') * 0.50; // 50% commission
        const etchCommission = this.getFieldValue('etch_amount') * 0.80; // 80% commission
        const maintenanceCommission = this.getFieldValue('maintenance_amount') * 0.30; // 30% commission
        const otherCommission = this.getFieldValue('other_products_total') * 0.40; // 40% commission
        
        // Calculate front-end gross (vehicle profit)
        const frontendGross = salesPrice - cost;
        
        // Calculate back-end gross (F&I profit)
        const backendGross = financeReserve + warrantyCommission + gapCommission + 
                           etchCommission + maintenanceCommission + otherCommission;
        
        // Calculate total gross profit
        const totalGross = frontendGross + backendGross;
        
        this.setFieldValue('frontend_gross', frontendGross);
        this.setFieldValue('backend_gross', backendGross);
        this.setFieldValue('total_gross', totalGross);
        
        // Update profitability indicators
        this.updateProfitabilityIndicators(frontendGross, backendGross, totalGross);
        
        return {
            frontend: frontendGross,
            backend: backendGross,
            total: totalGross
        };
    },

    /**
     * Update profitability visual indicators
     */
    updateProfitabilityIndicators: function(frontend, backend, total) {
        // Color coding based on profit levels
        const frontendField = document.getElementById('frontend_gross');
        const backendField = document.getElementById('backend_gross');
        const totalField = document.getElementById('total_gross');
        
        // Frontend profit indicators
        if (frontendField) {
            if (frontend >= 3000) {
                frontendField.style.backgroundColor = '#d4edda'; // Green
            } else if (frontend >= 1500) {
                frontendField.style.backgroundColor = '#fff3cd'; // Yellow
            } else {
                frontendField.style.backgroundColor = '#f8d7da'; // Red
            }
        }
        
        // Backend profit indicators
        if (backendField) {
            if (backend >= 2000) {
                backendField.style.backgroundColor = '#d4edda'; // Green
            } else if (backend >= 1000) {
                backendField.style.backgroundColor = '#fff3cd'; // Yellow
            } else {
                backendField.style.backgroundColor = '#f8d7da'; // Red
            }
        }
        
        // Total profit indicators
        if (totalField) {
            if (total >= 4000) {
                totalField.style.backgroundColor = '#d4edda'; // Green
            } else if (total >= 2000) {
                totalField.style.backgroundColor = '#fff3cd'; // Yellow
            } else {
                totalField.style.backgroundColor = '#f8d7da'; // Red
            }
        }
    },

    /**
     * Generate payment scenarios for different terms
     */
    generatePaymentScenarios: function(terms = [36, 48, 60, 72, 84]) {
        let principal, rate;
        
        // Check if we have temporary scenario data (from standalone calculator)
        if (this.tempScenarioData) {
            principal = this.tempScenarioData.principal;
            rate = this.tempScenarioData.rate / 100;
        } else {
            // Use form field values (from EditView)
            principal = this.getFieldValue('amount_financed');
            rate = this.getFieldValue('interest_rate') / 100;
        }
        
        const scenarios = [];
        
        terms.forEach(term => {
            if (rate === 0) {
                const payment = principal / term;
                scenarios.push({
                    term: term,
                    payment: payment,
                    totalPayments: payment * term,
                    totalInterest: 0
                });
            } else {
                const monthlyRate = rate / 12;
                const termPower = Math.pow(1 + monthlyRate, term);
                const payment = principal * (monthlyRate * termPower) / (termPower - 1);
                const totalPayments = payment * term;
                const totalInterest = totalPayments - principal;
                
                scenarios.push({
                    term: term,
                    payment: payment,
                    totalPayments: totalPayments,
                    totalInterest: totalInterest
                });
            }
        });
        
        this.displayPaymentScenarios(scenarios);
        
        // Clear temporary data if it was used
        if (this.tempScenarioData) {
            delete this.tempScenarioData;
        }
        
        return scenarios;
    },

    /**
     * Display payment scenarios in a popup or panel
     */
    displayPaymentScenarios: function(scenarios) {
        let html = '<table class="table table-striped table-hover">';
        html += '<thead><tr><th>Term</th><th>Monthly Payment</th><th>Total Payments</th><th>Total Interest</th><th>Select</th></tr></thead><tbody>';
        
        scenarios.forEach((scenario, index) => {
            html += `<tr data-term="${scenario.term}" data-payment="${scenario.payment}">
                <td><strong>${scenario.term} months</strong></td>
                <td>${this.config.currencyFormatter.format(scenario.payment)}</td>
                <td>${this.config.currencyFormatter.format(scenario.totalPayments)}</td>
                <td>${this.config.currencyFormatter.format(scenario.totalInterest)}</td>
                <td><input type="radio" name="scenario-select" value="${index}" ${index === 2 ? 'checked' : ''}></td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        html += '<p class="text-muted"><small><i class="fa fa-info-circle"></i> Click a row to select, then click "Apply Selected" to use these terms.</small></p>';
        
        // Display in the modal
        const scenarioDiv = document.getElementById('payment-scenarios');
        if (scenarioDiv) {
            scenarioDiv.innerHTML = html;
            
            // Add click handlers for row selection
            const rows = scenarioDiv.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('click', function() {
                    rows.forEach(r => r.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });
            
            // Show the modal
            $('#payment-scenarios-modal').modal('show');
        } else {
            console.error('F&I Deal Center: payment-scenarios div not found');
        }
    },

    /**
     * Apply selected payment scenario to the deal
     */
    selectPaymentScenario: function() {
        const selectedRow = document.querySelector('#payment-scenarios tr.selected');
        if (!selectedRow) {
            alert('Please select a payment scenario first.');
            return;
        }

        const term = selectedRow.getAttribute('data-term');
        const payment = selectedRow.getAttribute('data-payment');

        // Update the form fields
        this.setFieldValue('term_months', term);
        this.setFieldValue('monthly_payment', payment);

        // Recalculate other dependent fields
        this.recalculateAll();

        // Close the modal
        $('#payment-scenarios-modal').modal('hide');

        // Show success message
        this.showMessage('Payment scenario applied successfully!', 'success');
    },

    /**
     * Show payment scenarios (alias for DetailView button)
     */
    showPaymentScenarios: function() {
        this.generatePaymentScenarios();
    },

    /**
     * Show calculator help modal
     */
    showHelp: function() {
        // Show help modal if it exists, or create a simple alert
        const helpModal = document.getElementById('calculator-help-modal');
        if (helpModal) {
            $('#calculator-help-modal').modal('show');
        } else {
            alert('F&I Deal Center Calculator Help\n\n' +
                  '• Enter vehicle price, down payment, and trade details\n' +
                  '• Select financing terms and interest rate\n' +
                  '• Click "Payment Scenarios" to compare different terms\n' +
                  '• All calculations update in real-time\n' +
                  '• Use "Recalculate" to refresh all fields');
        }
    },

    /**
     * Utility functions
     */
    getFieldValue: function(fieldName) {
        const field = document.getElementById(fieldName);
        if (!field) {
            console.log('F&I Deal Center: Field not found:', fieldName);
            return 0;
        }
        
        let value = field.value;
        
        // Handle null or undefined values
        if (value === null || value === undefined) {
            return 0;
        }
        
        // Remove currency formatting and other characters, keep only numbers and decimal
        if (typeof value === 'string') {
            const originalValue = value;
            value = value.replace(/[$,\s%]/g, '').trim();
            
            // Handle empty or invalid input
            if (value === '' || value === '.' || value === '-') {
                return 0;
            }
            
            // Debug for sales_price specifically
            if (fieldName === 'sales_price' && originalValue !== value) {
                console.log('F&I Deal Center: Cleaned sales_price value:', {
                    original: originalValue,
                    cleaned: value
                });
            }
        }
        
        const numValue = parseFloat(value);
        const result = isNaN(numValue) ? 0 : numValue;
        
        // Debug for sales_price
        if (fieldName === 'sales_price') {
            console.log('F&I Deal Center: getFieldValue result for sales_price:', {
                input: field.value,
                cleaned: value,
                parsed: numValue,
                final: result
            });
        }
        
        return result;
    },

    setFieldValue: function(fieldName, value) {
        const field = document.getElementById(fieldName);
        if (!field) return;
        
        // Format currency fields
        if (this.isCurrencyField(fieldName)) {
            field.value = this.formatCurrency(value);
        } else if (this.isPercentField(fieldName)) {
            field.value = this.formatPercent(value);
        } else {
            field.value = value;
        }
    },

    formatCurrency: function(value) {
        return this.config.currencyFormatter.format(value);
    },

    formatPercent: function(value) {
        return this.config.percentFormatter.format(value / 100);
    },

    formatCurrencyField: function(field) {
        if (this.isCurrencyField(field.id)) {
            const value = this.getFieldValue(field.id);
            // Only format if the field has a value and is not currently focused
            if (value > 0 && document.activeElement !== field) {
                field.value = this.formatCurrency(value);
            } else if (value === 0) {
                field.value = '';
            }
        }
    },

    isCurrencyField: function(fieldName) {
        const currencyFields = [
            'sales_price', 'down_payment', 'trade_allowance', 'trade_payoff',
            'rebates', 'dealer_fees', 'government_fees', 'amount_financed',
            'monthly_payment', 'total_of_payments', 'finance_charge',
            'warranty_total', 'gap_amount', 'etch_amount', 'maintenance_amount',
            'other_products_total', 'finance_reserve', 'backend_gross',
            'frontend_gross', 'total_gross', 'vehicle_cost'
        ];
        return currencyFields.includes(fieldName);
    },

    isPercentField: function(fieldName) {
        const percentFields = ['interest_rate', 'buy_rate', 'rate_markup', 'residual_percent'];
        return percentFields.includes(fieldName);
    },

    validateField: function(field) {
        const value = this.getFieldValue(field.id);
        let isValid = true;
        let errorMessage = '';
        
        // Debug logging for sales_price validation
        if (field.id === 'sales_price') {
            console.log('F&I Deal Center: Validating sales_price:', {
                fieldValue: field.value,
                parsedValue: value,
                fieldId: field.id
            });
        }
        
        // Validation rules
        switch (field.id) {
            case 'interest_rate':
            case 'buy_rate':
                if (value < 0 || value > this.config.maxInterestRate) {
                    isValid = false;
                    errorMessage = `Interest rate must be between 0% and ${this.config.maxInterestRate}%`;
                }
                break;
            case 'term_months':
                if (value < this.config.minLoanTerm || value > this.config.maxLoanTerm) {
                    isValid = false;
                    errorMessage = `Loan term must be between ${this.config.minLoanTerm} and ${this.config.maxLoanTerm} months`;
                }
                break;
            case 'sales_price':
                if (value <= 0) {
                    isValid = false;
                    errorMessage = 'Sales price must be greater than zero';
                }
                break;
        }
        
        // Update field styling
        if (isValid) {
            field.classList.remove('error');
            field.style.borderColor = '';
            this.clearFieldError(field); // Clear any existing error message
        } else {
            field.classList.add('error');
            field.style.borderColor = '#dc3545';
            this.showFieldError(field, errorMessage);
        }
        
        return isValid;
    },

    showFieldError: function(field, message) {
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '12px';
        errorDiv.innerHTML = message;
        field.parentNode.appendChild(errorDiv);
    },

    clearFieldError: function(field) {
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    },

    showError: function(message) {
        console.error('F&I Deal Center Error:', message);
        // Could implement a toast notification or modal here
    },

    updateDealSummary: function() {
        // Update any deal summary displays
        console.log('F&I Deal Center: Deal summary updated');
    },

    loadStateTaxRates: function() {
        // In a real implementation, this would load from database
        console.log('F&I Deal Center: State tax rates loaded');
    },

    setupValidation: function() {
        // Set up additional validation rules
        console.log('F&I Deal Center: Validation rules configured');
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof SUGAR !== 'undefined' && SUGAR.DM_FIDeals) {
        SUGAR.DM_FIDeals.init();
    }
});

// Global functions for template onclick handlers
function selectPaymentScenario() {
    if (SUGAR.DM_FIDeals && SUGAR.DM_FIDeals.selectPaymentScenario) {
        SUGAR.DM_FIDeals.selectPaymentScenario();
    }
}

function showCalculatorHelp() {
    if (SUGAR.DM_FIDeals && SUGAR.DM_FIDeals.showHelp) {
        SUGAR.DM_FIDeals.showHelp();
    }
}

// Legacy support for EditView
if (typeof EditView !== 'undefined') {
    EditView.DM_FIDeals = SUGAR.DM_FIDeals;
} 