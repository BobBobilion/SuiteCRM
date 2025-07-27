<?php
/**
 * SuiteCRM Lead Attribution Center - Attribution Report View
 * 
 * This view displays detailed attribution reports with filtering capabilities
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_LeadAttributionViewAttribution_report extends SugarView
{
    public function __construct()
    {
        parent::__construct();
    }

    public function preDisplay()
    {
        $this->ss->assign('MOD', return_module_language($GLOBALS['current_language'], 'DM_LeadAttribution'));
        $this->ss->assign('APP', $GLOBALS['app_strings']);
    }

    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        // Get report data from controller
        $reportData = $this->view_object_map['reportData'] ?? array();
        $filters = $this->view_object_map['filters'] ?? array();
        
        // Set page title
        $GLOBALS['app']->headerDisplayed = true;
        
        echo '
        <style>
        .attribution-report {
            padding: 20px;
        }
        .filter-panel {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .filter-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-field label {
            font-weight: bold;
            color: #333;
        }
        .filter-field input, .filter-field select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 150px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .report-table tr:hover {
            background-color: #f5f5f5;
        }
        .roi-positive { color: #28a745; font-weight: bold; }
        .roi-negative { color: #dc3545; font-weight: bold; }
        .roi-neutral { color: #6c757d; }
        .export-section {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .button {
            padding: 8px 16px;
            margin: 5px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background: #0056b3;
        }
        .button.secondary {
            background: #6c757d;
        }
        .button.secondary:hover {
            background: #545b62;
        }
        </style>
        
        <div class="attribution-report">
            <h2>' . $mod_strings['LBL_ATTRIBUTION_REPORT_TITLE'] . '</h2>
            
            <!-- Filter Panel -->
            <div class="filter-panel">
                <h3>Report Filters</h3>
                <form method="GET" action="index.php">
                    <input type="hidden" name="module" value="DM_LeadAttribution">
                    <input type="hidden" name="action" value="AttributionReport">
                    
                    <div class="filter-row">
                        <div class="filter-field">
                            <label for="date_from">' . $mod_strings['LBL_FILTER_DATE_FROM'] . '</label>
                            <input type="date" name="date_from" id="date_from" value="' . htmlspecialchars($filters['date_from'] ?? '') . '">
                        </div>
                        
                        <div class="filter-field">
                            <label for="date_to">' . $mod_strings['LBL_FILTER_DATE_TO'] . '</label>
                            <input type="date" name="date_to" id="date_to" value="' . htmlspecialchars($filters['date_to'] ?? '') . '">
                        </div>
                        
                        <div class="filter-field">
                            <label for="source">' . $mod_strings['LBL_FILTER_SOURCE'] . '</label>
                            <input type="text" name="source" id="source" value="' . htmlspecialchars($filters['source'] ?? '') . '" placeholder="e.g., google, facebook">
                        </div>
                        
                        <div class="filter-field">
                            <label for="campaign">' . $mod_strings['LBL_FILTER_CAMPAIGN'] . '</label>
                            <input type="text" name="campaign" id="campaign" value="' . htmlspecialchars($filters['campaign'] ?? '') . '" placeholder="Campaign name">
                        </div>
                    </div>
                    
                    <div class="filter-row">
                        <button type="submit" class="button">' . $mod_strings['LBL_APPLY_FILTERS'] . '</button>
                        <a href="index.php?module=DM_LeadAttribution&action=AttributionReport" class="button secondary">' . $mod_strings['LBL_RESET_FILTERS'] . '</a>
                    </div>
                </form>
            </div>
            
            <!-- Report Summary -->
            <div style="margin: 20px 0;">
                <strong>Report Summary:</strong> 
                ' . count($reportData) . ' attribution records found
                ' . (!empty($filters['date_from']) ? 'from ' . $filters['date_from'] : '') . '
                ' . (!empty($filters['date_to']) ? 'to ' . $filters['date_to'] : '') . '
                ' . (!empty($filters['source']) ? 'for source: ' . $filters['source'] : '') . '
                ' . (!empty($filters['campaign']) ? 'in campaign: ' . $filters['campaign'] : '') . '
            </div>
            
            <!-- Report Table -->';
        
        if (!empty($reportData)) {
            echo '
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Lead</th>
                        <th>First Touch Source</th>
                        <th>First Touch Campaign</th>
                        <th>First Touch Date</th>
                        <th>Conversion Value</th>
                        <th>Total Cost</th>
                        <th>ROI %</th>
                        <th>Touch Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($reportData as $record) {
                $roi = $record['roi_percentage'] ?? 0;
                $roiClass = $roi > 0 ? 'roi-positive' : ($roi < 0 ? 'roi-negative' : 'roi-neutral');
                
                echo '<tr>
                        <td><a href="index.php?module=DM_LeadAttribution&action=DetailView&record=' . $record['id'] . '">' . htmlspecialchars($record['name'] ?? '') . '</a></td>
                        <td>' . ($record['lead_id'] ? '<a href="index.php?module=Leads&action=DetailView&record=' . $record['lead_id'] . '">View Lead</a>' : '-') . '</td>
                        <td>' . htmlspecialchars($record['first_touch_source'] ?? '-') . '</td>
                        <td>' . htmlspecialchars($record['first_touch_campaign'] ?? '-') . '</td>
                        <td>' . date('M j, Y', strtotime($record['first_touch_date'] ?? '')) . '</td>
                        <td>$' . number_format($record['conversion_value'] ?? 0, 2) . '</td>
                        <td>$' . number_format($record['total_cost'] ?? 0, 2) . '</td>
                        <td class="' . $roiClass . '">' . number_format($roi, 1) . '%</td>
                        <td>' . ($record['touch_count'] ?? 1) . '</td>
                        <td>
                            <a href="index.php?module=DM_LeadAttribution&action=DetailView&record=' . $record['id'] . '" style="margin-right: 5px;">View</a>
                            <a href="index.php?module=DM_LeadAttribution&action=EditView&record=' . $record['id'] . '">Edit</a>
                        </td>
                      </tr>';
            }
            
            echo '</tbody>
            </table>';
        } else {
            echo '<div style="text-align: center; padding: 40px; color: #666;">
                    <h3>' . $mod_strings['LBL_NO_DATA'] . '</h3>
                    <p>No attribution records match your current filters.</p>
                    <a href="index.php?module=DM_LeadAttribution&action=EditView" class="button">Create First Attribution Record</a>
                  </div>';
        }
        
        echo '
            <!-- Export Section -->
            <div class="export-section">
                <h3>' . $mod_strings['LBL_EXPORT_REPORT'] . '</h3>
                <p>Export this report data for further analysis:</p>
                <button onclick="exportToCSV()" class="button">Export to CSV</button>
                <button onclick="printReport()" class="button secondary">Print Report</button>
            </div>
            
            <!-- Navigation -->
            <div style="margin-top: 30px;">
                <a href="index.php?module=DM_LeadAttribution&action=ROIDashboard" class="button">' . $mod_strings['LBL_ROI_DASHBOARD'] . '</a>
                <a href="index.php?module=DM_LeadAttribution&action=index" class="button">' . $mod_strings['LNK_LIST'] . '</a>
                <a href="index.php?module=DM_LeadAttribution&action=EditView" class="button">' . $mod_strings['LNK_NEW_RECORD'] . '</a>
            </div>
        </div>
        
        <script>
        function exportToCSV() {
            var table = document.querySelector(".report-table");
            if (!table) {
                alert("No data to export");
                return;
            }
            
            var csv = [];
            var rows = table.querySelectorAll("tr");
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (var j = 0; j < cols.length - 1; j++) { // Skip actions column
                    var cellText = cols[j].innerText.replace(/"/g, \'"\');
                    row.push(\'"" + cellText + \'"");
                }
                
                csv.push(row.join(","));
            }
            
            var csvContent = csv.join("\\n");
            var blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
            var link = document.createElement("a");
            
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", "attribution_report_" + new Date().getTime() + ".csv");
                link.style.visibility = "hidden";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
        
        function printReport() {
            var printWindow = window.open("", "_blank");
            var reportHtml = document.querySelector(".attribution-report").innerHTML;
            
            printWindow.document.write(`
                <html>
                <head>
                    <title>Attribution Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .filter-panel, .export-section { display: none; }
                        .report-table { width: 100%; border-collapse: collapse; }
                        .report-table th, .report-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .report-table th { background-color: #f2f2f2; }
                        .roi-positive { color: #28a745; font-weight: bold; }
                        .roi-negative { color: #dc3545; font-weight: bold; }
                        .roi-neutral { color: #6c757d; }
                    </style>
                </head>
                <body>
                    ${reportHtml}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
        </script>';
    }
}