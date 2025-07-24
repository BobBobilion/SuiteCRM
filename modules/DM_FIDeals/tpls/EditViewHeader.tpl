{*
 * F&I Deal Center - Edit View Header Template
 * Provides calculator tools, payment scenarios, and quick actions
 *}

<div class="fi-deal-header" style="background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
    <div class="row">
        <div class="col-md-8">
            <h3 style="margin: 0; color: #2c3e50;">
                <i class="fa fa-calculator"></i> F&I Deal Center
                {if $bean->deal_number}
                    - Deal #{$bean->deal_number}
                {else}
                    - New Deal
                {/if}
            </h3>
            <p style="margin: 5px 0 0 0; color: #6c757d;">
                Real-time financial calculations and deal structuring
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-info btn-sm" onclick="SUGAR.DM_FIDeals.generatePaymentScenarios();">
                    <i class="fa fa-table"></i> Payment Scenarios
                </button>
                <button type="button" class="btn btn-success btn-sm" onclick="SUGAR.DM_FIDeals.recalculateAll();">
                    <i class="fa fa-refresh"></i> Recalculate
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="showCalculatorHelp();">
                    <i class="fa fa-question-circle"></i> Help
                </button>
            </div>
        </div>
    </div>
    
    {* Quick Deal Summary Row *}
    <div class="row" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
        <div class="col-md-3">
            <div class="deal-summary-item">
                <label style="font-weight: bold; color: #495057;">Sales Price:</label>
                <span id="header-sales-price" style="font-size: 16px; color: #2c3e50;">$0.00</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="deal-summary-item">
                <label style="font-weight: bold; color: #495057;">Monthly Payment:</label>
                <span id="header-monthly-payment" style="font-size: 16px; color: #2c3e50;">$0.00</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="deal-summary-item">
                <label style="font-weight: bold; color: #495057;">Backend Gross:</label>
                <span id="header-backend-gross" style="font-size: 16px; color: #2c3e50;">$0.00</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="deal-summary-item">
                <label style="font-weight: bold; color: #495057;">Total Gross:</label>
                <span id="header-total-gross" style="font-size: 16px; font-weight: bold; color: #28a745;">$0.00</span>
            </div>
        </div>
    </div>
</div>

{* Payment Scenarios Modal *}
<div id="payment-scenarios-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-table"></i> Payment Scenarios Comparison
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="payment-scenarios"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="selectPaymentScenario();">
                    Apply Selected
                </button>
            </div>
        </div>
    </div>
</div>

{* Calculator Help Modal *}
<div id="calculator-help-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-question-circle"></i> F&I Calculator Help
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="help-section">
                    <h5><i class="fa fa-calculator"></i> Automatic Calculations</h5>
                    <p>The F&I calculator automatically updates as you enter data:</p>
                    <ul>
                        <li><strong>Amount Financed:</strong> Calculated from sales price, down payment, trade values, rebates, and fees</li>
                        <li><strong>Monthly Payment:</strong> Based on amount financed, interest rate, and term</li>
                        <li><strong>Finance Reserve:</strong> Dealer profit from rate markup</li>
                        <li><strong>Backend Gross:</strong> Total F&I profit including product commissions</li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h5><i class="fa fa-money"></i> Tax Calculations</h5>
                    <p>Taxes are calculated automatically based on customer state:</p>
                    <ul>
                        <li>State and local tax rates are applied</li>
                        <li>Trade-in allowance reduces taxable amount in most states</li>
                        <li>Government fees include calculated taxes plus license fees</li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h5><i class="fa fa-cogs"></i> Finance Methods</h5>
                    <ul>
                        <li><strong>Cash:</strong> No financing calculations</li>
                        <li><strong>Finance:</strong> Standard loan payment calculations</li>
                        <li><strong>Lease:</strong> Lease payment with residual value</li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h5><i class="fa fa-warning"></i> Validation Rules</h5>
                    <ul>
                        <li>Interest rates: 0% to 29.99%</li>
                        <li>Loan terms: 12 to 96 months</li>
                        <li>Sales price must be greater than zero</li>
                        <li>Fields with errors will be highlighted in red</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Got It</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
{literal}
function showCalculatorHelp() {
    $('#calculator-help-modal').modal('show');
}

function selectPaymentScenario() {
    // Function to apply selected payment scenario
    const selectedRow = document.querySelector('#payment-scenarios tr.selected');
    if (selectedRow) {
        const term = selectedRow.dataset.term;
        const payment = selectedRow.dataset.payment;
        
        document.getElementById('term_months').value = term;
        document.getElementById('monthly_payment').value = payment;
        
        $('#payment-scenarios-modal').modal('hide');
        SUGAR.DM_FIDeals.recalculateAll();
    }
}

// Update header summary when calculations change
function updateHeaderSummary() {
    const salesPrice = SUGAR.DM_FIDeals.getFieldValue('sales_price');
    const monthlyPayment = SUGAR.DM_FIDeals.getFieldValue('monthly_payment');
    const backendGross = SUGAR.DM_FIDeals.getFieldValue('backend_gross');
    const totalGross = SUGAR.DM_FIDeals.getFieldValue('total_gross');
    
    document.getElementById('header-sales-price').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(salesPrice);
    document.getElementById('header-monthly-payment').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(monthlyPayment);
    document.getElementById('header-backend-gross').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(backendGross);
    document.getElementById('header-total-gross').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(totalGross);
        
    // Color code total gross
    const totalElement = document.getElementById('header-total-gross');
    if (totalGross >= 4000) {
        totalElement.style.color = '#28a745'; // Green
    } else if (totalGross >= 2000) {
        totalElement.style.color = '#ffc107'; // Yellow
    } else {
        totalElement.style.color = '#dc3545'; // Red
    }
}

// Override the original recalculate function to update header
if (typeof SUGAR !== 'undefined' && SUGAR.DM_FIDeals) {
    const originalRecalculate = SUGAR.DM_FIDeals.recalculateAll;
    SUGAR.DM_FIDeals.recalculateAll = function() {
        originalRecalculate.call(this);
        setTimeout(updateHeaderSummary, 100);
    };
}
{/literal}
</script>

<style>
{literal}
.deal-summary-item {
    text-align: center;
    padding: 10px;
    background: white;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.deal-summary-item label {
    display: block;
    font-size: 12px;
    margin-bottom: 5px;
}

.modal-lg {
    max-width: 900px;
}

.help-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.help-section:last-child {
    border-bottom: none;
}

.help-section h5 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.help-section ul {
    margin-bottom: 0;
}

#payment-scenarios table {
    margin-top: 15px;
}

#payment-scenarios tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

#payment-scenarios tr.selected {
    background-color: #e3f2fd;
    font-weight: bold;
}

.calculated-field {
    background-color: #f8f9fa !important;
    font-weight: bold;
}

.field-error {
    margin-top: 5px;
}

.error {
    border-color: #dc3545 !important;
}

.financing-field, .lease-field {
    transition: all 0.3s ease;
}

.btn-group .btn {
    margin-left: 5px;
}

.fi-deal-header {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
{/literal}
</style> 