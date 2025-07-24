{*
 * F&I Deal Center - Edit View Footer Template
 * Provides save options, validation summary, and deal completion tools
 *}

<div class="fi-deal-footer" style="background: #f8f9fa; padding: 15px; margin-top: 20px; border-radius: 5px; border-top: 3px solid #2c3e50;">
    
    {* Validation Summary *}
    <div id="validation-summary" class="alert alert-danger" style="display: none;">
        <h5><i class="fa fa-exclamation-triangle"></i> Please Fix These Issues:</h5>
        <ul id="validation-errors"></ul>
    </div>
    
    {* Deal Status & Actions *}
    <div class="row">
        <div class="col-md-6">
            <div class="deal-status-panel">
                <h5 style="margin-bottom: 15px; color: #2c3e50;">
                    <i class="fa fa-check-circle"></i> Deal Status & Completion
                </h5>
                
                <div class="form-group">
                    <label for="deal_status_footer">Deal Status:</label>
                    <select id="deal_status_footer" class="form-control" onchange="updateDealStatus(this.value);">
                        <option value="Draft">Draft - Work in Progress</option>
                        <option value="Submitted">Submitted - Pending Approval</option>
                        <option value="Approved">Approved - Ready to Fund</option>
                        <option value="Funded">Funded - Deal Complete</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="completion-checklist">
                    <label style="font-weight: bold;">Completion Checklist:</label>
                    <div class="checklist-items">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check-customer-info">
                            <label class="form-check-label" for="check-customer-info">Customer Information Complete</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check-vehicle-selected">
                            <label class="form-check-label" for="check-vehicle-selected">Vehicle Selected</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check-pricing-complete">
                            <label class="form-check-label" for="check-pricing-complete">Pricing Complete</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check-financing-approved">
                            <label class="form-check-label" for="check-financing-approved">Financing Approved</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check-products-selected">
                            <label class="form-check-label" for="check-products-selected">F&I Products Selected</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="deal-actions-panel">
                <h5 style="margin-bottom: 15px; color: #2c3e50;">
                    <i class="fa fa-cogs"></i> Deal Actions & Tools
                </h5>
                
                <div class="action-buttons">
                    <div class="btn-group-vertical" style="width: 100%;">
                        <button type="button" class="btn btn-info btn-block" onclick="printDealWorksheet();">
                            <i class="fa fa-print"></i> Print Deal Worksheet
                        </button>
                        <button type="button" class="btn btn-warning btn-block" onclick="emailDealSummary();">
                            <i class="fa fa-envelope"></i> Email Deal Summary
                        </button>
                        <button type="button" class="btn btn-success btn-block" onclick="generateContracts();">
                            <i class="fa fa-file-text"></i> Generate Contracts
                        </button>
                        <button type="button" class="btn btn-primary btn-block" onclick="submitToLender();">
                            <i class="fa fa-bank"></i> Submit to Lender
                        </button>
                    </div>
                </div>
                
                <div class="deal-notes" style="margin-top: 15px;">
                    <label for="footer-notes" style="font-weight: bold;">Quick Notes:</label>
                    <textarea id="footer-notes" class="form-control" rows="3" placeholder="Add deal notes, customer preferences, or follow-up items..."></textarea>
                </div>
            </div>
        </div>
    </div>
    
    {* Profit Summary Bar *}
    <div class="profit-summary-bar" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
        <div class="row">
            <div class="col-md-2">
                <div class="profit-item">
                    <label>Frontend:</label>
                    <span id="footer-frontend" class="profit-value">$0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="profit-item">
                    <label>Finance Reserve:</label>
                    <span id="footer-reserve" class="profit-value">$0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="profit-item">
                    <label>Product Profit:</label>
                    <span id="footer-products" class="profit-value">$0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="profit-item">
                    <label>Backend Total:</label>
                    <span id="footer-backend" class="profit-value">$0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="profit-item">
                    <label>Deal Total:</label>
                    <span id="footer-total" class="profit-value total">$0</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="profit-item">
                    <label>PRU Goal:</label>
                    <span id="footer-pru" class="profit-value">${literal}$2,500{/literal}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
{literal}
function updateDealStatus(status) {
    if (document.getElementById('deal_status')) {
        document.getElementById('deal_status').value = status;
    }
    
    // Update completion requirements based on status
    updateCompletionRequirements(status);
}

function updateCompletionRequirements(status) {
    const checks = document.querySelectorAll('.checklist-items input[type="checkbox"]');
    
    switch(status) {
        case 'Submitted':
            // All basic info must be complete
            checks[0].required = true;
            checks[1].required = true;
            checks[2].required = true;
            break;
        case 'Approved':
            // Financing must be approved
            checks[3].required = true;
            break;
        case 'Funded':
            // Everything must be complete
            checks.forEach(check => check.required = true);
            break;
    }
}

function validateDealCompletion() {
    const errors = [];
    const salesPrice = SUGAR.DM_FIDeals.getFieldValue('sales_price');
    const customerId = SUGAR.DM_FIDeals.getFieldValue('customer_id');
    const vehicleId = SUGAR.DM_FIDeals.getFieldValue('vehicle_id');
    const dealStatus = document.getElementById('deal_status').value;
    
    // Basic validation
    if (salesPrice <= 0) {
        errors.push('Sales price is required');
    }
    
    if (!customerId) {
        errors.push('Customer must be selected');
    }
    
    if (!vehicleId) {
        errors.push('Vehicle must be selected');
    }
    
    // Status-specific validation
    if (dealStatus === 'Submitted' || dealStatus === 'Approved' || dealStatus === 'Funded') {
        const termMonths = SUGAR.DM_FIDeals.getFieldValue('term_months');
        const interestRate = SUGAR.DM_FIDeals.getFieldValue('interest_rate');
        
        if (termMonths <= 0) {
            errors.push('Loan term is required for financed deals');
        }
        
        if (interestRate < 0 || interestRate > 29.99) {
            errors.push('Valid interest rate is required');
        }
    }
    
    return errors;
}

function showValidationSummary(errors) {
    const summaryDiv = document.getElementById('validation-summary');
    const errorsList = document.getElementById('validation-errors');
    
    if (errors.length > 0) {
        errorsList.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorsList.appendChild(li);
        });
        summaryDiv.style.display = 'block';
        summaryDiv.scrollIntoView({ behavior: 'smooth' });
        return false;
    } else {
        summaryDiv.style.display = 'none';
        return true;
    }
}

function updateProfitSummary() {
    const frontend = SUGAR.DM_FIDeals.getFieldValue('frontend_gross');
    const reserve = SUGAR.DM_FIDeals.getFieldValue('finance_reserve');
    const warranty = SUGAR.DM_FIDeals.getFieldValue('warranty_total') * 0.25;
    const gap = SUGAR.DM_FIDeals.getFieldValue('gap_amount') * 0.50;
    const etch = SUGAR.DM_FIDeals.getFieldValue('etch_amount') * 0.80;
    const maintenance = SUGAR.DM_FIDeals.getFieldValue('maintenance_amount') * 0.30;
    const other = SUGAR.DM_FIDeals.getFieldValue('other_products_total') * 0.40;
    
    const productProfit = warranty + gap + etch + maintenance + other;
    const backend = reserve + productProfit;
    const total = frontend + backend;
    
    // Update footer display
    document.getElementById('footer-frontend').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(frontend);
    document.getElementById('footer-reserve').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(reserve);
    document.getElementById('footer-products').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(productProfit);
    document.getElementById('footer-backend').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(backend);
    document.getElementById('footer-total').textContent = 
        SUGAR.DM_FIDeals.config.currencyFormatter.format(total);
    
    // Color code based on performance
    const totalElement = document.getElementById('footer-total');
    if (total >= 4000) {
        totalElement.className = 'profit-value total profit-excellent';
    } else if (total >= 2500) {
        totalElement.className = 'profit-value total profit-good';
    } else if (total >= 1500) {
        totalElement.className = 'profit-value total profit-fair';
    } else {
        totalElement.className = 'profit-value total profit-poor';
    }
}

// Quick action functions
function printDealWorksheet() {
    window.open('index.php?module=DM_FIDeals&action=PrintWorksheet&record=' + 
        document.getElementById('record').value, '_blank');
}

function emailDealSummary() {
    // Open email composer with deal summary
    alert('Email functionality coming soon!');
}

function generateContracts() {
    // Generate finance contracts
    alert('Contract generation functionality coming soon!');
}

function submitToLender() {
    // Submit to lender portal
    alert('Lender submission functionality coming soon!');
}

// Override the form submit to include validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('EditView');
    if (form) {
        form.addEventListener('submit', function(e) {
            const errors = validateDealCompletion();
            if (!showValidationSummary(errors)) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Update profit summary when calculations change
    if (typeof SUGAR !== 'undefined' && SUGAR.DM_FIDeals) {
        const originalRecalculate = SUGAR.DM_FIDeals.recalculateAll;
        SUGAR.DM_FIDeals.recalculateAll = function() {
            originalRecalculate.call(this);
            setTimeout(updateProfitSummary, 100);
        };
    }
    
    // Sync footer status with main deal status
    const mainStatus = document.getElementById('deal_status');
    const footerStatus = document.getElementById('deal_status_footer');
    
    if (mainStatus && footerStatus) {
        footerStatus.value = mainStatus.value;
        
        mainStatus.addEventListener('change', function() {
            footerStatus.value = this.value;
        });
        
        footerStatus.addEventListener('change', function() {
            mainStatus.value = this.value;
        });
    }
});
{/literal}
</script>

<style>
{literal}
.fi-deal-footer {
    box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
}

.deal-status-panel, .deal-actions-panel {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    height: 100%;
}

.completion-checklist {
    margin-top: 15px;
}

.checklist-items {
    max-height: 120px;
    overflow-y: auto;
}

.form-check {
    margin-bottom: 5px;
}

.action-buttons .btn {
    margin-bottom: 5px;
}

.profit-summary-bar {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.profit-item {
    text-align: center;
    padding: 10px 5px;
}

.profit-item label {
    display: block;
    font-size: 11px;
    font-weight: bold;
    color: #6c757d;
    margin-bottom: 5px;
}

.profit-value {
    display: block;
    font-size: 14px;
    font-weight: bold;
    color: #2c3e50;
}

.profit-value.total {
    font-size: 16px;
}

.profit-excellent {
    color: #28a745 !important;
}

.profit-good {
    color: #17a2b8 !important;
}

.profit-fair {
    color: #ffc107 !important;
}

.profit-poor {
    color: #dc3545 !important;
}

.deal-notes textarea {
    resize: vertical;
    min-height: 60px;
}

#validation-summary {
    margin-bottom: 20px;
}

#validation-summary ul {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .profit-summary-bar .row {
        text-align: center;
    }
    
    .profit-item {
        margin-bottom: 10px;
    }
    
    .action-buttons .btn-group-vertical {
        width: 100%;
    }
}
{/literal}
</style> 