{*
 * SuiteCRM Service Orders - Service Summary Report Template
 *}
<div class="moduleTitle">
    <h2>Service Summary Report</h2>
</div>

<div class="clear"></div>

{literal}
<style>
.stat-box {
    text-align: center;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    background-color: #f9f9f9;
}

.stat-box h3 {
    margin: 0 0 5px 0;
    font-size: 2em;
    color: #333;
}

.stat-box p {
    margin: 0;
    color: #666;
}

.report-section {
    margin-bottom: 30px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
}

.report-section h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.table tr:hover {
    background-color: #f9f9f9;
}

@media print {
    .btn-group {
        display: none;
    }
}
</style>
{/literal}

<form name="service_summary_report" method="POST">
    <div class="report-section">
        <h3>Service Statistics Overview</h3>
        
        <!-- Debug Information -->
        <div class="alert alert-info">
            <strong>Debug Info:</strong> 
            Total Orders Found: {$statistics.total_orders|default:0} | 
            Service Orders Array Count: {if $service_orders}{$service_orders|@count}{else}0{/if}
        </div>
        
        <div class="row">
            <div class="col-md-2">
                <div class="stat-box">
                    <h3 class="text-primary">{$statistics.total_orders}</h3>
                    <p>Total Service Orders</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-box">
                    <h3 class="text-success">{$statistics.completed_orders}</h3>
                    <p>Completed Orders</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-box">
                    <h3 class="text-warning">{$statistics.pending_orders}</h3>
                    <p>Pending Orders</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-box">
                    <h3 class="text-info">${$statistics.total_revenue|number_format:2}</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-box">
                    <h3 class="text-info">{$statistics.total_labor_hours|number_format:1}</h3>
                    <p>Total Labor Hours</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-box">
                    <h3 class="text-info">${$statistics.average_order_value|number_format:2}</h3>
                    <p>Average Order Value</p>
                </div>
            </div>
        </div>
    </div>

    <div class="report-section">
        <h3>Recent Service Orders</h3>
        <small>Report generated on: {$report_date}</small>
        
        {if $service_orders}
        
        <!-- Debug: Show first record details -->
        {if $service_orders[0]}
        <div class="alert alert-warning">
            <strong>First Record Debug:</strong><br>
            ID: {$service_orders[0].id|default:'missing'}<br>
            Name: {$service_orders[0].name|default:'missing'}<br>
            Customer: {$service_orders[0].customer_name|default:'missing'}<br>
            Status: {$service_orders[0].service_status|default:'missing'}
        </div>
        {/if}
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Service Order #</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Technician</th>
                    <th>Service Type</th>
                    <th>Status</th>
                    <th>Total Cost</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$service_orders item=order}
                <tr>
                    <td>
                        <a href="index.php?module=DM_ServiceOrders&action=DetailView&record={$order.id}" target="_blank">
                            {$order.service_order_number|default:$order.name}
                        </a>
                    </td>
                    <td>{$order.customer_name}</td>
                    <td>{$order.vehicle_name}</td>
                    <td>{$order.technician_name}</td>
                    <td>{$order.service_type}</td>
                    <td>
                        {if $order.service_status == 'Completed'}
                            <span class="label label-success">{$order.service_status}</span>
                        {elseif $order.service_status == 'In Progress'}
                            <span class="label label-warning">{$order.service_status}</span>
                        {else}
                            <span class="label label-default">{$order.service_status}</span>
                        {/if}
                    </td>
                    <td>${$order.total_cost|number_format:2}</td>
                    <td>{$order.formatted_date}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        {else}
        <div class="alert alert-info">
            <h4>No Service Orders Found</h4>
            <p>There are currently no service orders in the system to display in this report.</p>
        </div>
        {/if}
    </div>

    <div class="report-section">
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="window.print();">
                Print Report
            </button>
            <button type="button" class="btn btn-default" onclick="window.location.reload();">
                Refresh Report
            </button>
            <a href="index.php?module=DM_ServiceOrders&action=index" class="btn btn-default">
                Back to Service Orders
            </a>
        </div>
    </div>
</form>