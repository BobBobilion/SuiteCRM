{*
 * SuiteCRM Service & Parts Hub - Low Stock Report Template
 *}
<div class="moduleTitle">
    <h2>{$MOD.LBL_LOW_STOCK_REPORT}</h2>
</div>

<div class="clear"></div>

<form name="low_stock_report" method="POST">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="edit view">
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td valign="top" width="100%">
                            
                            <!-- Report Summary -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Report Summary</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <h3 class="text-danger">{$statistics.total_parts}</h3>
                                                <p>Total Parts Need Attention</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <h3 class="text-danger">{$statistics.out_of_stock}</h3>
                                                <p>Out of Stock</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <h3 class="text-warning">{$statistics.low_stock}</h3>
                                                <p>Low Stock</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <h3 class="text-info">${$statistics.estimated_reorder_value|number_format:2}</h3>
                                                <p>Estimated Reorder Value</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Low Stock Parts Table -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Parts Requiring Attention</h4>
                                    <small>Generated on: {$report_date}</small>
                                </div>
                                <div class="panel-body">
                                    {if $low_stock_parts}
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Part Number</th>
                                                <th>Description</th>
                                                <th>Manufacturer</th>
                                                <th>Category</th>
                                                <th>Current Stock</th>
                                                <th>Reorder Point</th>
                                                <th>Cost per Part</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$low_stock_parts item=part}
                                            <tr>
                                                <td>
                                                    <a href="index.php?module=DM_PartsInventory&action=DetailView&record={$part.id}" target="_blank">
                                                        {$part.part_number}
                                                    </a>
                                                </td>
                                                <td>{$part.description|truncate:40}</td>
                                                <td>{$part.manufacturer}</td>
                                                <td>{$part.category}</td>
                                                <td class="text-right">
                                                    {if $part.quantity_on_hand == 0}
                                                        <span class="label label-danger">{$part.quantity_on_hand}</span>
                                                    {else}
                                                        <span class="label label-warning">{$part.quantity_on_hand}</span>
                                                    {/if}
                                                </td>
                                                <td class="text-right">{$part.reorder_point}</td>
                                                <td class="text-right">${$part.cost|number_format:2}</td>
                                                <td class="text-center">
                                                    {if $part.quantity_on_hand == 0}
                                                        <span class="label label-danger">Out of Stock</span>
                                                    {else}
                                                        <span class="label label-warning">Low Stock</span>
                                                    {/if}
                                                </td>
                                                <td class="text-center">
                                                    <a href="index.php?module=DM_PartsInventory&action=EditView&record={$part.id}" 
                                                       class="btn btn-sm btn-primary" target="_blank">
                                                        Update Stock
                                                    </a>
                                                </td>
                                            </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                    {else}
                                    <div class="alert alert-success">
                                        <h4>Great News!</h4>
                                        <p>All parts are currently at or above their reorder points. No immediate action required.</p>
                                    </div>
                                    {/if}
                                </div>
                            </div>

                            <!-- Report Actions -->
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary" onclick="window.print();">
                                            Print Report
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="window.location.reload();">
                                            Refresh Report
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="exportToCSV();">
                                            Export to CSV
                                        </button>
                                        <a href="index.php?module=DM_PartsInventory&action=index" class="btn btn-default">
                                            Back to Parts Inventory
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

{literal}
<style>
.stat-box {
    text-align: center;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
}

.stat-box h3 {
    margin: 0 0 5px 0;
    font-size: 2em;
}

.stat-box p {
    margin: 0;
    color: #666;
}

.panel {
    margin-bottom: 20px;
}

.panel-heading {
    background-color: #f5f5f5;
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
}

.panel-body {
    padding: 15px;
}

.table {
    margin-bottom: 0;
}

@media print {
    .btn-group {
        display: none;
    }
}
</style>
{/literal}

{literal}
<script type="text/javascript">
function exportToCSV() {
    var csv = 'Part Number,Description,Manufacturer,Category,Current Stock,Reorder Point,Cost per Part,Status\n';
    
{/literal}
    {if $low_stock_parts}
    {foreach from=$low_stock_parts item=part}
    csv += '{$part.part_number|escape:"quotes"},' +
           '"{$part.description|escape:"quotes"}",' +
           '{$part.manufacturer|escape:"quotes"},' +
           '{$part.category|escape:"quotes"},' +
           '{$part.quantity_on_hand},' +
           '{$part.reorder_point},' +
           '{$part.cost},' +
           '{if $part.quantity_on_hand == 0}"Out of Stock"{else}"Low Stock"{/if}\n';
    {/foreach}
    {/if}
{literal}
    
    var blob = new Blob([csv], { type: 'text/csv' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'low_stock_report_' + new Date().toISOString().slice(0,10) + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
{/literal}