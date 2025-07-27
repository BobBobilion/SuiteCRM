<?php
/**
 * SuiteCRM Lead Attribution Center - ROI Dashboard View
 * 
 * This view displays comprehensive ROI metrics and attribution analytics
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_LeadAttributionViewRoi_dashboard extends SugarView
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
        
        // Get ROI metrics from controller
        $roiMetrics = $this->view_object_map['roiMetrics'] ?? array();
        $channelPerformance = $this->view_object_map['channelPerformance'] ?? array();
        $campaignROI = $this->view_object_map['campaignROI'] ?? array();
        
        // Set page title
        $GLOBALS['app']->headerDisplayed = true;
        
        echo '
        <style>
        .roi-dashboard {
            padding: 20px;
        }
        .metric-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: inline-block;
            width: 200px;
            vertical-align: top;
        }
        .metric-value {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
        .metric-label {
            color: #666;
            margin-top: 5px;
        }
        .positive { color: #28a745; }
        .negative { color: #dc3545; }
        .neutral { color: #6c757d; }
        .chart-container {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f2f2f2;
        }
        </style>
        
        <div class="roi-dashboard">
            <h2>' . $mod_strings['LBL_ROI_DASHBOARD_TITLE'] . '</h2>
            
            <!-- Key Metrics Cards -->
            <div class="metrics-row">
                <div class="metric-card">
                    <div class="metric-value">' . number_format($roiMetrics['total_leads'] ?? 0) . '</div>
                    <div class="metric-label">' . $mod_strings['LBL_TOTAL_LEADS'] . '</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">$' . number_format($roiMetrics['total_revenue'] ?? 0, 2) . '</div>
                    <div class="metric-label">' . $mod_strings['LBL_TOTAL_REVENUE'] . '</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">$' . number_format($roiMetrics['total_cost'] ?? 0, 2) . '</div>
                    <div class="metric-label">' . $mod_strings['LBL_TOTAL_COST_LABEL'] . '</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value ' . (($roiMetrics['avg_roi'] ?? 0) > 0 ? 'positive' : (($roiMetrics['avg_roi'] ?? 0) < 0 ? 'negative' : 'neutral')) . '">' . number_format($roiMetrics['avg_roi'] ?? 0, 1) . '%</div>
                    <div class="metric-label">' . $mod_strings['LBL_AVERAGE_ROI'] . '</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">' . number_format($roiMetrics['converted_leads'] ?? 0) . '</div>
                    <div class="metric-label">' . $mod_strings['LBL_CONVERTED_LEADS'] . '</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">' . number_format($roiMetrics['conversion_rate'] ?? 0, 1) . '%</div>
                    <div class="metric-label">' . $mod_strings['LBL_CONVERSION_RATE'] . '</div>
                </div>
            </div>
            
            <!-- Channel Performance Chart -->
            <div class="chart-container">
                <h3>' . $mod_strings['LBL_CHANNEL_PERFORMANCE'] . '</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Channel</th>
                            <th>Leads</th>
                            <th>Revenue</th>
                            <th>Cost</th>
                            <th>ROI</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($channelPerformance as $channel) {
            $roi = number_format($channel['avg_roi'] ?? 0, 1);
            $roiClass = ($channel['avg_roi'] ?? 0) > 0 ? 'positive' : (($channel['avg_roi'] ?? 0) < 0 ? 'negative' : 'neutral');
            
            echo '<tr>
                    <td>' . htmlspecialchars($channel['channel'] ?? '') . '</td>
                    <td>' . number_format($channel['leads'] ?? 0) . '</td>
                    <td>$' . number_format($channel['revenue'] ?? 0, 2) . '</td>
                    <td>$' . number_format($channel['cost'] ?? 0, 2) . '</td>
                    <td class="' . $roiClass . '">' . $roi . '%</td>
                  </tr>';
        }
        
        echo '      </tbody>
                </table>
            </div>
            
            <!-- Top Campaigns -->
            <div class="chart-container">
                <h3>' . $mod_strings['LBL_TOP_CAMPAIGNS'] . '</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Leads</th>
                            <th>Revenue</th>
                            <th>Cost</th>
                            <th>ROI</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($campaignROI as $campaign) {
            $roi = number_format($campaign['avg_roi'] ?? 0, 1);
            $roiClass = ($campaign['avg_roi'] ?? 0) > 0 ? 'positive' : (($campaign['avg_roi'] ?? 0) < 0 ? 'negative' : 'neutral');
            
            echo '<tr>
                    <td>' . htmlspecialchars($campaign['campaign'] ?? '') . '</td>
                    <td>' . number_format($campaign['leads'] ?? 0) . '</td>
                    <td>$' . number_format($campaign['revenue'] ?? 0, 2) . '</td>
                    <td>$' . number_format($campaign['cost'] ?? 0, 2) . '</td>
                    <td class="' . $roiClass . '">' . $roi . '%</td>
                  </tr>';
        }
        
        echo '      </tbody>
                </table>
            </div>
            
            <!-- Actions -->
            <div style="margin-top: 30px;">
                <a href="index.php?module=DM_LeadAttribution&action=AttributionReport" class="button">' . $mod_strings['LBL_ATTRIBUTION_REPORT'] . '</a>
                <a href="index.php?module=DM_LeadAttribution&action=index" class="button">' . $mod_strings['LNK_LIST'] . '</a>
                <a href="index.php?module=DM_LeadAttribution&action=EditView" class="button">' . $mod_strings['LNK_NEW_RECORD'] . '</a>
            </div>
        </div>
        
        <script>
        // Auto-refresh dashboard every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
        </script>';
    }
}