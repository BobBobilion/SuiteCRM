{*
 * SuiteCRM Trade-In Manager - Reports Template
 * 
 * This template displays comprehensive trade-in reports and analytics
 * with filtering options and exportable data.
 *}

<div class="moduleTitle">
    <h2>{$MOD.LBL_TRADE_SUMMARY} - {$MOD.LBL_REPORTS}</h2>
</div>

<!-- Report Filters -->
<div class="search">
    <form name="report_filters" method="post" action="index.php">
        <input type="hidden" name="module" value="DM_TradeIns">
        <input type="hidden" name="action" value="reports">
        
        <table cellpadding="4" cellspacing="0" border="0" width="100%" class="edit view">
            <tr>
                <td scope="row" style="white-space: nowrap; background-color: #f4f4f4; padding: 10px;">
                    <strong>{$MOD.LBL_DATE_RANGE}:</strong>
                </td>
                <td style="padding: 10px;">
                    <input type="date" name="date_from" value="{$date_from}" class="date">
                    <span style="margin: 0 10px;">to</span>
                    <input type="date" name="date_to" value="{$date_to}" class="date">
                </td>
                <td style="padding: 10px;">
                    <strong>{$MOD.LBL_STATUS}:</strong>
                    <select name="status_filter">
                        <option value="">All Statuses</option>
                        {foreach from=$statusOptions key=key item=label}
                            <option value="{$key}" {if $status_filter == $key}selected{/if}>{$label}</option>
                        {/foreach}
                    </select>
                </td>
                <td style="padding: 10px;">
                    <strong>{$MOD.LBL_MAKE}:</strong>
                    <select name="make_filter">
                        {foreach from=$makeOptions key=key item=label}
                            <option value="{$key}" {if $make_filter == $key}selected{/if}>{$label}</option>
                        {/foreach}
                    </select>
                </td>
                <td style="padding: 10px;">
                    <input type="submit" value="{$APP.LBL_SEARCH_BUTTON_TITLE}" class="button">
                    <input type="button" value="{$APP.LBL_EXPORT}" onclick="exportReports()" class="button">
                </td>
            </tr>
        </table>
    </form>
</div>

<!-- Summary Statistics -->
<div style="margin: 20px 0;">
    <h3>{$MOD.LBL_TRADE_SUMMARY}</h3>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="list view">
        <tr height="20">
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Metric</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Value</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Metric</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Value</th>
        </tr>
        <tr>
            <td>Total Trade-Ins</td>
            <td><strong>{$summaryData.total_tradeins}</strong></td>
            <td>Avg Customer Asking</td>
            <td><strong>${$summaryData.avg_customer_asking|number_format:2}</strong></td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td>Avg Market Trade Value</td>
            <td><strong>${$summaryData.avg_market_value_trade|number_format:2}</strong></td>
            <td>Avg Appraised Value</td>
            <td><strong>${$summaryData.avg_appraised_value|number_format:2}</strong></td>
        </tr>
        <tr>
            <td>Avg Payoff Amount</td>
            <td><strong>${$summaryData.avg_payoff_amount|number_format:2}</strong></td>
            <td>Avg Mileage</td>
            <td><strong>{$summaryData.avg_mileage|number_format:0} miles</strong></td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td>Below Asking Price</td>
            <td><strong>{$summaryData.below_asking_count} ({$summaryData.below_asking_percentage}%)</strong></td>
            <td>With Payoff</td>
            <td><strong>{$summaryData.with_payoff_count} ({$summaryData.with_payoff_percentage}%)</strong></td>
        </tr>
    </table>
</div>

<!-- Status Breakdown -->
<div style="margin: 20px 0;">
    <h3>{$MOD.LBL_STATUS} Breakdown</h3>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="list view">
        <tr height="20">
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Status</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Count</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Percentage</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Avg Appraised Value</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Avg Market Value</th>
        </tr>
        {foreach from=$statusBreakdown item=status name=statusLoop}
        <tr {if $smarty.foreach.statusLoop.iteration is even}style="background-color: #f9f9f9;"{/if}>
            <td><strong>{$status.status}</strong></td>
            <td>{$status.count}</td>
            <td>{$status.percentage}%</td>
            <td>${$status.avg_appraised_value|number_format:2}</td>
            <td>${$status.avg_market_value|number_format:2}</td>
        </tr>
        {/foreach}
    </table>
</div>

<!-- Make/Model Analysis -->
<div style="margin: 20px 0;">
    <h3>{$MOD.LBL_MAKE}/{$MOD.LBL_MODEL} Analysis (2+ occurrences)</h3>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="list view">
        <tr height="20">
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Make</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Model</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Count</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Avg Year</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Avg Mileage</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Avg Asking</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Avg Trade Value</th>
        </tr>
        {foreach from=$makeModelAnalysis item=analysis name=makeLoop}
        <tr {if $smarty.foreach.makeLoop.iteration is even}style="background-color: #f9f9f9;"{/if}>
            <td><strong>{$analysis.make}</strong></td>
            <td>{$analysis.model}</td>
            <td>{$analysis.count}</td>
            <td>{$analysis.avg_year}</td>
            <td>{$analysis.avg_mileage|number_format:0}</td>
            <td>${$analysis.avg_customer_asking|number_format:2}</td>
            <td>${$analysis.avg_market_value_trade|number_format:2}</td>
        </tr>
        {/foreach}
    </table>
</div>

<!-- Value Analysis -->
<div style="margin: 20px 0;">
    <h3>{$MOD.LBL_VALUE_ANALYSIS} (Recent 10)</h3>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="list view">
        <tr height="20">
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Trade-In</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Vehicle</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Customer Asking</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Market Trade</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Appraised</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Variance</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Equity</th>
        </tr>
        {foreach from=$valueAnalysis item=analysis name=valueLoop}
        <tr {if $smarty.foreach.valueLoop.iteration is even}style="background-color: #f9f9f9;"{/if}>
            <td>{$analysis.name}</td>
            <td>{$analysis.year} {$analysis.make} {$analysis.model}</td>
            <td>${$analysis.customer_asking|number_format:2}</td>
            <td>${$analysis.market_value_trade|number_format:2}</td>
            <td>${$analysis.appraised_value|number_format:2}</td>
            <td style="color: {if $analysis.asking_vs_market_variance > 0}red{else}green{/if};">
                ${$analysis.asking_vs_market_variance|number_format:2}
            </td>
            <td style="color: {if $analysis.equity < 0}red{else}green{/if};">
                ${$analysis.equity|number_format:2}
            </td>
        </tr>
        {/foreach}
    </table>
</div>

<!-- Appraiser Activity -->
<div style="margin: 20px 0;">
    <h3>{$MOD.LBL_APPRAISAL_ACTIVITY}</h3>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="list view">
        <tr height="20">
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Appraiser</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Total Trade-Ins</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Appraised</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Approved</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Appraisal Rate</th>
            <th scope="col" style="background-color: #e6e6fa; font-weight: bold;">Avg Appraised Value</th>
        </tr>
        {foreach from=$appraiserActivity item=activity name=appraiserLoop}
        <tr {if $smarty.foreach.appraiserLoop.iteration is even}style="background-color: #f9f9f9;"{/if}>
            <td><strong>{$activity.full_name}</strong></td>
            <td>{$activity.total_tradeins}</td>
            <td>{$activity.appraised_count}</td>
            <td>{$activity.approved_count}</td>
            <td>{$activity.appraisal_rate}%</td>
            <td>${$activity.avg_appraised_value|number_format:2}</td>
        </tr>
        {/foreach}
    </table>
</div>

<script type="text/javascript">
/**
 * Export reports functionality
 */
function exportReports() {
    var params = new URLSearchParams(window.location.search);
    var exportUrl = 'index.php?module=DM_TradeIns&action=export_reports';
    
    // Add current filter parameters
    exportUrl += '&date_from=' + encodeURIComponent(document.getElementsByName('date_from')[0].value);
    exportUrl += '&date_to=' + encodeURIComponent(document.getElementsByName('date_to')[0].value);
    exportUrl += '&status_filter=' + encodeURIComponent(document.getElementsByName('status_filter')[0].value);
    exportUrl += '&make_filter=' + encodeURIComponent(document.getElementsByName('make_filter')[0].value);
    
    window.open(exportUrl, '_blank');
}

console.log('DM_TradeIns: Reports page loaded successfully');
</script>

<style>
.list.view th {
    text-align: left;
    padding: 8px;
    border: 1px solid #ccc;
}

.list.view td {
    padding: 6px 8px;
    border: 1px solid #ddd;
}

.edit.view td {
    border: 1px solid #ddd;
}

.button {
    background-color: #4CAF50;
    color: white;
    padding: 8px 16px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
}

.button:hover {
    background-color: #45a049;
}

.date {
    padding: 4px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

select {
    padding: 4px;
    border: 1px solid #ccc;
    border-radius: 3px;
}
</style> 