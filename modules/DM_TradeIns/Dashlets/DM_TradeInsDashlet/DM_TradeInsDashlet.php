<?php
/**
 * SuiteCRM Trade-In Manager - Dashboard Widget
 * 
 * This dashlet displays key trade-in metrics and recent activity
 * on the SuiteCRM dashboard for quick overview.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/Dashlets/DashletGeneric.php');

/**
 * DM_TradeIns Dashlet Class
 * 
 * Provides a dashboard widget with key trade-in statistics
 */
class DM_TradeInsDashlet extends DashletGeneric
{
    public function __construct($id, $def = null)
    {
        global $current_user, $app_strings, $dashletData;
        require('modules/DM_TradeIns/Dashlets/DM_TradeInsDashlet/DM_TradeInsDashlet.data.php');

        parent::__construct($id, $def);

        if (empty($def['title'])) {
            $this->title = translate('LBL_DASHLET_MY_TRADES', 'DM_TradeIns');
        }

        $this->searchFields = $dashletData['DM_TradeInsDashlet']['searchFields'];
        $this->columns = $dashletData['DM_TradeInsDashlet']['columns'];

        $this->seedBean = BeanFactory::newBean('DM_TradeIns');
        
        $GLOBALS['log']->info("DM_TradeInsDashlet: Initialized dashlet for user: " . $current_user->user_name);
    }

    /**
     * Display the dashlet with enhanced trade-in statistics
     */
    public function display($text = '')
    {
        global $current_user, $mod_strings;
        
        $GLOBALS['log']->info("DM_TradeInsDashlet: Displaying dashlet");
        
        // Get summary statistics for current user's trade-ins
        $summaryStats = $this->getSummaryStatistics();
        
        // Get recent trade-ins
        $recentTradeIns = $this->getRecentTradeIns();
        
        // Get pending appraisals
        $pendingAppraisals = $this->getPendingAppraisals();
        
        $text .= '<div class="dashletPanelBody" style="padding: 10px;">';
        
        // Summary Statistics Section
        $text .= '<div style="margin-bottom: 15px;">';
        $text .= '<h4 style="margin: 0 0 10px 0; color: #333;">Quick Stats (Last 30 Days)</h4>';
        $text .= '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">';
        $text .= '<div style="text-align: center; padding: 8px; background-color: #f0f8ff; border-radius: 4px; min-width: 80px;">';
        $text .= '<div style="font-size: 18px; font-weight: bold; color: #2c5aa0;">' . $summaryStats['total_count'] . '</div>';
        $text .= '<div style="font-size: 11px; color: #666;">Total</div>';
        $text .= '</div>';
        $text .= '<div style="text-align: center; padding: 8px; background-color: #f0fff0; border-radius: 4px; min-width: 80px;">';
        $text .= '<div style="font-size: 18px; font-weight: bold; color: #228b22;">$' . number_format($summaryStats['avg_appraised_value'], 0) . '</div>';
        $text .= '<div style="font-size: 11px; color: #666;">Avg Value</div>';
        $text .= '</div>';
        $text .= '<div style="text-align: center; padding: 8px; background-color: #fff5ee; border-radius: 4px; min-width: 80px;">';
        $text .= '<div style="font-size: 18px; font-weight: bold; color: #ff4500;">' . $summaryStats['pending_count'] . '</div>';
        $text .= '<div style="font-size: 11px; color: #666;">Pending</div>';
        $text .= '</div>';
        $text .= '</div>';
        $text .= '</div>';
        
        // Recent Trade-Ins Section
        if (!empty($recentTradeIns)) {
            $text .= '<div style="margin-bottom: 15px;">';
            $text .= '<h4 style="margin: 0 0 8px 0; color: #333;">Recent Trade-Ins</h4>';
            $text .= '<div style="max-height: 150px; overflow-y: auto;">';
            
            foreach ($recentTradeIns as $tradeIn) {
                $statusColor = $this->getStatusColor($tradeIn['status']);
                $text .= '<div style="padding: 6px; margin-bottom: 4px; border-left: 3px solid ' . $statusColor . '; background-color: #fafafa; font-size: 12px;">';
                $text .= '<div style="font-weight: bold;"><a href="index.php?module=DM_TradeIns&action=DetailView&record=' . $tradeIn['id'] . '" style="text-decoration: none; color: #2c5aa0;">' . htmlspecialchars($tradeIn['name']) . '</a></div>';
                $text .= '<div style="color: #666; font-size: 11px;">';
                $text .= htmlspecialchars($tradeIn['customer_name'] ?: 'No Customer') . ' • ';
                $text .= '<span style="color: ' . $statusColor . '; font-weight: bold;">' . htmlspecialchars($tradeIn['status'] ?: 'New') . '</span>';
                if ($tradeIn['appraised_value'] > 0) {
                    $text .= ' • $' . number_format($tradeIn['appraised_value'], 0);
                }
                $text .= '</div>';
                $text .= '</div>';
            }
            
            $text .= '</div>';
            $text .= '</div>';
        }
        
        // Pending Appraisals Section
        if (!empty($pendingAppraisals)) {
            $text .= '<div style="margin-bottom: 15px;">';
            $text .= '<h4 style="margin: 0 0 8px 0; color: #333;">Pending Appraisals</h4>';
            $text .= '<div style="max-height: 120px; overflow-y: auto;">';
            
            foreach ($pendingAppraisals as $appraisal) {
                $text .= '<div style="padding: 6px; margin-bottom: 4px; border-left: 3px solid #ff4500; background-color: #fff5ee; font-size: 12px;">';
                $text .= '<div style="font-weight: bold;"><a href="index.php?module=DM_TradeIns&action=DetailView&record=' . $appraisal['id'] . '" style="text-decoration: none; color: #2c5aa0;">' . htmlspecialchars($appraisal['name']) . '</a></div>';
                $text .= '<div style="color: #666; font-size: 11px;">';
                if ($appraisal['appraisal_scheduled_date']) {
                    $text .= 'Scheduled: ' . date('M j', strtotime($appraisal['appraisal_scheduled_date']));
                } else {
                    $text .= 'Not yet scheduled';
                }
                $text .= ' • Customer asking: $' . number_format($appraisal['customer_asking'], 0);
                $text .= '</div>';
                $text .= '</div>';
            }
            
            $text .= '</div>';
            $text .= '</div>';
        }
        
        // Quick Actions
        $text .= '<div style="text-align: center; margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">';
        $text .= '<a href="index.php?module=DM_TradeIns&action=EditView" style="margin-right: 10px; padding: 5px 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 3px; font-size: 11px;">New Trade-In</a>';
        $text .= '<a href="index.php?module=DM_TradeIns&action=index" style="margin-right: 10px; padding: 5px 10px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 3px; font-size: 11px;">View All</a>';
        $text .= '<a href="index.php?module=DM_TradeIns&action=reports" style="padding: 5px 10px; background-color: #FF9800; color: white; text-decoration: none; border-radius: 3px; font-size: 11px;">Reports</a>';
        $text .= '</div>';
        
        $text .= '</div>';
        
        return parent::display($text);
    }

    /**
     * Get summary statistics for the current user
     */
    private function getSummaryStatistics()
    {
        global $current_user, $db;
        
        $userId = $current_user->id;
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        
        $query = "
            SELECT 
                COUNT(*) as total_count,
                AVG(appraised_value) as avg_appraised_value,
                SUM(CASE WHEN status IN ('New', 'Appraised') THEN 1 ELSE 0 END) as pending_count
            FROM dm_tradeins 
            WHERE deleted = 0 
            AND assigned_user_id = '{$userId}'
            AND date_entered >= '{$thirtyDaysAgo}'
        ";
        
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);
        
        return array(
            'total_count' => $row['total_count'] ?? 0,
            'avg_appraised_value' => $row['avg_appraised_value'] ?? 0,
            'pending_count' => $row['pending_count'] ?? 0,
        );
    }

    /**
     * Get recent trade-ins for the current user
     */
    private function getRecentTradeIns()
    {
        global $current_user, $db;
        
        $userId = $current_user->id;
        
        $query = "
            SELECT 
                t.id,
                t.name,
                t.status,
                t.appraised_value,
                t.date_entered,
                a.name as customer_name
            FROM dm_tradeins t
            LEFT JOIN accounts a ON t.customer_id = a.id AND a.deleted = 0
            WHERE t.deleted = 0 
            AND t.assigned_user_id = '{$userId}'
            ORDER BY t.date_entered DESC
            LIMIT 5
        ";
        
        $result = $db->query($query);
        $tradeIns = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $tradeIns[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'status' => $row['status'],
                'appraised_value' => $row['appraised_value'] ?? 0,
                'customer_name' => $row['customer_name'],
                'date_entered' => $row['date_entered'],
            );
        }
        
        return $tradeIns;
    }

    /**
     * Get pending appraisals for the current user
     */
    private function getPendingAppraisals()
    {
        global $current_user, $db;
        
        $userId = $current_user->id;
        
        $query = "
            SELECT 
                t.id,
                t.name,
                t.customer_asking,
                t.appraisal_scheduled_date
            FROM dm_tradeins t
            WHERE t.deleted = 0 
            AND t.assigned_user_id = '{$userId}'
            AND t.status IN ('New', 'Appraised')
            AND (t.appraised_value IS NULL OR t.appraised_value = 0)
            ORDER BY t.appraisal_scheduled_date ASC, t.date_entered DESC
            LIMIT 3
        ";
        
        $result = $db->query($query);
        $appraisals = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $appraisals[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'customer_asking' => $row['customer_asking'] ?? 0,
                'appraisal_scheduled_date' => $row['appraisal_scheduled_date'],
            );
        }
        
        return $appraisals;
    }

    /**
     * Get color for trade-in status
     */
    private function getStatusColor($status)
    {
        $colors = array(
            'New' => '#2196F3',
            'Appraised' => '#FF9800', 
            'Approved' => '#4CAF50',
            'Used' => '#9C27B0',
            'Rejected' => '#F44336',
        );
        
        return $colors[$status] ?? '#757575';
    }
} 