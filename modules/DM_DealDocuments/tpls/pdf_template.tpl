{* 
SuiteCRM Deal Documentation Suite - PDF Template
This template provides a basic structure for PDF documents
*}

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        line-height: 1.4;
        color: #333;
    }
    .header {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 30px;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
    }
    .section {
        margin-bottom: 20px;
    }
    .section-title {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 10px;
        color: #2c3e50;
        border-bottom: 1px solid #bdc3c7;
        padding-bottom: 5px;
    }
    .field-row {
        margin-bottom: 8px;
        overflow: hidden;
    }
    .field-label {
        font-weight: bold;
        float: left;
        width: 150px;
        margin-right: 15px;
    }
    .field-value {
        float: left;
        max-width: 300px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    .table th,
    .table td {
        border: 1px solid #333;
        padding: 8px;
        text-align: left;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .signature-section {
        margin-top: 50px;
        border-top: 1px solid #333;
        padding-top: 20px;
    }
    .signature-box {
        border: 1px solid #333;
        height: 80px;
        margin: 10px 0;
        padding: 5px;
    }
    .footer {
        margin-top: 30px;
        font-size: 10px;
        text-align: center;
        color: #666;
    }
</style>

<div class="header">
    {$document_title|default:"DEAL DOCUMENT"}
</div>

<div class="section">
    <div class="section-title">Document Information</div>
    <div class="field-row">
        <div class="field-label">Document Type:</div>
        <div class="field-value">{$document_type}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Deal Number:</div>
        <div class="field-value">{$deal_number}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Date:</div>
        <div class="field-value">{$current_date}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Customer:</div>
        <div class="field-value">{$customer_name}</div>
    </div>
</div>

{if $vehicle_info}
<div class="section">
    <div class="section-title">Vehicle Information</div>
    <div class="field-row">
        <div class="field-label">Year:</div>
        <div class="field-value">{$vehicle_year}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Make:</div>
        <div class="field-value">{$vehicle_make}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Model:</div>
        <div class="field-value">{$vehicle_model}</div>
    </div>
    <div class="field-row">
        <div class="field-label">VIN:</div>
        <div class="field-value">{$vehicle_vin}</div>
    </div>
</div>
{/if}

{if $financial_info}
<div class="section">
    <div class="section-title">Financial Information</div>
    <table class="table">
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Vehicle Sale Price</td>
            <td>${$sales_price|number_format:2}</td>
        </tr>
        {if $trade_allowance > 0}
        <tr>
            <td>Trade Allowance</td>
            <td>($({$trade_allowance|number_format:2})</td>
        </tr>
        {/if}
        {if $down_payment > 0}
        <tr>
            <td>Down Payment</td>
            <td>($({$down_payment|number_format:2})</td>
        </tr>
        {/if}
        <tr>
            <td>Dealer Fees</td>
            <td>${$dealer_fees|number_format:2}</td>
        </tr>
        <tr>
            <td>Government Fees</td>
            <td>${$government_fees|number_format:2}</td>
        </tr>
        <tr>
            <td><strong>Amount Financed</strong></td>
            <td><strong>${$amount_financed|number_format:2}</strong></td>
        </tr>
    </table>
</div>
{/if}

{if $finance_terms}
<div class="section">
    <div class="section-title">Finance Terms</div>
    <div class="field-row">
        <div class="field-label">Term (Months):</div>
        <div class="field-value">{$term_months}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Annual Percentage Rate:</div>
        <div class="field-value">{$interest_rate}%</div>
    </div>
    <div class="field-row">
        <div class="field-label">Monthly Payment:</div>
        <div class="field-value">${$monthly_payment|number_format:2}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Total of Payments:</div>
        <div class="field-value">${$total_of_payments|number_format:2}</div>
    </div>
    <div class="field-row">
        <div class="field-label">Finance Charge:</div>
        <div class="field-value">${$finance_charge|number_format:2}</div>
    </div>
</div>
{/if}

{if $product_info}
<div class="section">
    <div class="section-title">F&I Products</div>
    {if $warranty_total > 0}
    <div class="field-row">
        <div class="field-label">Extended Warranty:</div>
        <div class="field-value">${$warranty_total|number_format:2}</div>
    </div>
    {/if}
    {if $gap_amount > 0}
    <div class="field-row">
        <div class="field-label">GAP Insurance:</div>
        <div class="field-value">${$gap_amount|number_format:2}</div>
    </div>
    {/if}
    {if $etch_amount > 0}
    <div class="field-row">
        <div class="field-label">Theft Protection:</div>
        <div class="field-value">${$etch_amount|number_format:2}</div>
    </div>
    {/if}
    {if $maintenance_amount > 0}
    <div class="field-row">
        <div class="field-label">Maintenance Package:</div>
        <div class="field-value">${$maintenance_amount|number_format:2}</div>
    </div>
    {/if}
</div>
{/if}

{$custom_content}

<div class="signature-section">
    <div class="section-title">Signatures</div>
    
    <div style="float: left; width: 45%;">
        <div><strong>Customer Signature:</strong></div>
        <div class="signature-box"></div>
        <div>Date: _______________</div>
    </div>
    
    <div style="float: right; width: 45%;">
        <div><strong>Dealer Representative:</strong></div>
        <div class="signature-box"></div>
        <div>Date: _______________</div>
    </div>
    
    <div style="clear: both;"></div>
</div>

<div class="footer">
    <p>This document is an integral part of the vehicle sale transaction. Please retain for your records.</p>
    <p>Generated by SuiteCRM Deal Documentation Suite on {$current_date}</p>
</div>