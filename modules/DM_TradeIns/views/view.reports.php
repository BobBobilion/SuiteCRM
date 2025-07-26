<?php
/**
 * SuiteCRM Trade-In Manager - Reports View
 * 
 * This view provides basic trade-in reports and analytics including
 * summary statistics, value analysis, and status tracking.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

/**
 * DM_TradeIns Reports View Class
 * 
 * Provides basic reporting functionality for trade-in analysis
 */
class DM_TradeInsViewReports extends SugarView
{
    public function __construct()
    {
        parent::__construct();
        $this->options['show_header'] = true;
        $this->options['show_subpanels'] = false;
        $this->options['show_search'] = false;
    }

    /**
     * Display the reports page
     */
    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        $GLOBALS['log']->info("DM_TradeInsViewReports: Displaying trade-in reports");
        
        // Get report parameters
        $date_from = $_REQUEST['date_from'] ?? date('Y-m-01'); // First day of current month
        $date_to = $_REQUEST['date_to'] ?? date('Y-m-t'); // Last day of current month
        $status_filter = $_REQUEST['status_filter'] ?? '';
        $make_filter = $_REQUEST['make_filter'] ?? '';
        
        // Generate reports
        $summaryData = $this->generateSummaryReport($date_from, $date_to, $status_filter, $make_filter);
        $valueAnalysis = $this->generateValueAnalysis($date_from, $date_to, $status_filter, $make_filter);
        $statusBreakdown = $this->generateStatusBreakdown($date_from, $date_to, $make_filter);
        $makeModelAnalysis = $this->generateMakeModelAnalysis($date_from, $date_to, $status_filter);
        $appraiserActivity = $this->generateAppraiserActivity($date_from, $date_to);
        
        // Get available filter options
        $statusOptions = $this->getStatusOptions();
        $makeOptions = $this->getMakeOptions();
        
        $this->ss->assign('MOD', $mod_strings);
        $this->ss->assign('APP', $app_strings);
        $this->ss->assign('date_from', $date_from);
        $this->ss->assign('date_to', $date_to);
        $this->ss->assign('status_filter', $status_filter);
        $this->ss->assign('make_filter', $make_filter);
        $this->ss->assign('summaryData', $summaryData);
        $this->ss->assign('valueAnalysis', $valueAnalysis);
        $this->ss->assign('statusBreakdown', $statusBreakdown);
        $this->ss->assign('makeModelAnalysis', $makeModelAnalysis);
        $this->ss->assign('appraiserActivity', $appraiserActivity);
        $this->ss->assign('statusOptions', $statusOptions);
        $this->ss->assign('makeOptions', $makeOptions);
        
        echo $this->ss->fetch('modules/DM_TradeIns/tpls/reports.tpl');
    }

    /**
     * Generate summary report data
     */
    private function generateSummaryReport($date_from, $date_to, $status_filter, $make_filter)
    {
        global $db;
        
        $GLOBALS['log']->info("DM_TradeInsViewReports: Generating summary report from {$date_from} to {$date_to}");
        
        $whereClause = "WHERE t.deleted = 0 AND t.date_entered BETWEEN '{$date_from}' AND '{$date_to} 23:59:59'";
        
        if (!empty($status_filter)) {
            $whereClause .= " AND t.status = '{$status_filter}'";
        }
        
        if (!empty($make_filter)) {
            $whereClause .= " AND t.make = '{$make_filter}'";
        }
        
        $query = "
            SELECT 
                COUNT(*) as total_tradeins,
                AVG(t.customer_asking) as avg_customer_asking,
                AVG(t.market_value_trade) as avg_market_value_trade,
                AVG(t.appraised_value) as avg_appraised_value,
                AVG(t.payoff_amount) as avg_payoff_amount,
                SUM(CASE WHEN t.appraised_value > t.customer_asking THEN 1 ELSE 0 END) as below_asking_count,
                SUM(CASE WHEN t.payoff_amount > 0 THEN 1 ELSE 0 END) as with_payoff_count,
                AVG(t.mileage) as avg_mileage
            FROM dm_tradeins t
            {$whereClause}
        ";
        
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);
        
        // Calculate additional metrics
        $below_asking_percentage = $row['total_tradeins'] > 0 ? 
            round(($row['below_asking_count'] / $row['total_tradeins']) * 100, 1) : 0;
        $with_payoff_percentage = $row['total_tradeins'] > 0 ? 
            round(($row['with_payoff_count'] / $row['total_tradeins']) * 100, 1) : 0;
        
        return array(
            'total_tradeins' => $row['total_tradeins'] ?? 0,
            'avg_customer_asking' => round($row['avg_customer_asking'] ?? 0, 2),
            'avg_market_value_trade' => round($row['avg_market_value_trade'] ?? 0, 2),
            'avg_appraised_value' => round($row['avg_appraised_value'] ?? 0, 2),
            'avg_payoff_amount' => round($row['avg_payoff_amount'] ?? 0, 2),
            'below_asking_count' => $row['below_asking_count'] ?? 0,
            'below_asking_percentage' => $below_asking_percentage,
            'with_payoff_count' => $row['with_payoff_count'] ?? 0,
            'with_payoff_percentage' => $with_payoff_percentage,
            'avg_mileage' => round($row['avg_mileage'] ?? 0, 0),
        );
    }

    /**
     * Generate value analysis report
     */
    private function generateValueAnalysis($date_from, $date_to, $status_filter, $make_filter)
    {
        global $db;
        
        $whereClause = "WHERE t.deleted = 0 AND t.date_entered BETWEEN '{$date_from}' AND '{$date_to} 23:59:59'";
        
        if (!empty($status_filter)) {
            $whereClause .= " AND t.status = '{$status_filter}'";
        }
        
        if (!empty($make_filter)) {
            $whereClause .= " AND t.make = '{$make_filter}'";
        }
        
        $query = "
            SELECT 
                t.name,
                t.year,
                t.make,
                t.model,
                t.customer_asking,
                t.market_value_trade,
                t.appraised_value,
                t.payoff_amount,
                (t.customer_asking - t.market_value_trade) as asking_vs_market_variance,
                (COALESCE(t.appraised_value, t.market_value_trade) - t.payoff_amount) as equity
            FROM dm_tradeins t
            {$whereClause}
            AND (t.customer_asking > 0 OR t.market_value_trade > 0 OR t.appraised_value > 0)
            ORDER BY t.date_entered DESC
            LIMIT 10
        ";
        
        $result = $db->query($query);
        $analysis = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $analysis[] = array(
                'name' => $row['name'],
                'year' => $row['year'],
                'make' => $row['make'],
                'model' => $row['model'],
                'customer_asking' => round($row['customer_asking'] ?? 0, 2),
                'market_value_trade' => round($row['market_value_trade'] ?? 0, 2),
                'appraised_value' => round($row['appraised_value'] ?? 0, 2),
                'payoff_amount' => round($row['payoff_amount'] ?? 0, 2),
                'asking_vs_market_variance' => round($row['asking_vs_market_variance'] ?? 0, 2),
                'equity' => round($row['equity'] ?? 0, 2),
            );
        }
        
        return $analysis;
    }

    /**
     * Generate status breakdown report
     */
    private function generateStatusBreakdown($date_from, $date_to, $make_filter)
    {
        global $db;
        
        $whereClause = "WHERE t.deleted = 0 AND t.date_entered BETWEEN '{$date_from}' AND '{$date_to} 23:59:59'";
        
        if (!empty($make_filter)) {
            $whereClause .= " AND t.make = '{$make_filter}'";
        }
        
        $query = "
            SELECT 
                t.status,
                COUNT(*) as count,
                AVG(t.appraised_value) as avg_appraised_value,
                AVG(t.market_value_trade) as avg_market_value
            FROM dm_tradeins t
            {$whereClause}
            GROUP BY t.status
            ORDER BY count DESC
        ";
        
        $result = $db->query($query);
        $breakdown = array();
        $total = 0;
        
        while ($row = $db->fetchByAssoc($result)) {
            $breakdown[] = array(
                'status' => $row['status'] ?: 'Not Set',
                'count' => $row['count'],
                'avg_appraised_value' => round($row['avg_appraised_value'] ?? 0, 2),
                'avg_market_value' => round($row['avg_market_value'] ?? 0, 2),
            );
            $total += $row['count'];
        }
        
        // Calculate percentages
        foreach ($breakdown as &$item) {
            $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0;
        }
        
        return $breakdown;
    }

    /**
     * Generate make/model analysis
     */
    private function generateMakeModelAnalysis($date_from, $date_to, $status_filter)
    {
        global $db;
        
        $whereClause = "WHERE t.deleted = 0 AND t.date_entered BETWEEN '{$date_from}' AND '{$date_to} 23:59:59'";
        
        if (!empty($status_filter)) {
            $whereClause .= " AND t.status = '{$status_filter}'";
        }
        
        $query = "
            SELECT 
                t.make,
                t.model,
                COUNT(*) as count,
                AVG(t.year) as avg_year,
                AVG(t.mileage) as avg_mileage,
                AVG(t.customer_asking) as avg_customer_asking,
                AVG(t.market_value_trade) as avg_market_value_trade,
                AVG(t.appraised_value) as avg_appraised_value
            FROM dm_tradeins t
            {$whereClause}
            AND t.make IS NOT NULL AND t.make != ''
            GROUP BY t.make, t.model
            HAVING count >= 2
            ORDER BY count DESC, t.make
            LIMIT 15
        ";
        
        $result = $db->query($query);
        $analysis = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $analysis[] = array(
                'make' => $row['make'],
                'model' => $row['model'] ?: 'Various',
                'count' => $row['count'],
                'avg_year' => round($row['avg_year'] ?? 0, 0),
                'avg_mileage' => round($row['avg_mileage'] ?? 0, 0),
                'avg_customer_asking' => round($row['avg_customer_asking'] ?? 0, 2),
                'avg_market_value_trade' => round($row['avg_market_value_trade'] ?? 0, 2),
                'avg_appraised_value' => round($row['avg_appraised_value'] ?? 0, 2),
            );
        }
        
        return $analysis;
    }

    /**
     * Generate appraiser activity report
     */
    private function generateAppraiserActivity($date_from, $date_to)
    {
        global $db;
        
        $query = "
            SELECT 
                u.user_name,
                CONCAT(u.first_name, ' ', u.last_name) as full_name,
                COUNT(*) as total_tradeins,
                SUM(CASE WHEN t.status = 'Appraised' THEN 1 ELSE 0 END) as appraised_count,
                SUM(CASE WHEN t.status = 'Approved' THEN 1 ELSE 0 END) as approved_count,
                AVG(t.appraised_value) as avg_appraised_value
            FROM dm_tradeins t
            LEFT JOIN users u ON t.assigned_user_id = u.id
            WHERE t.deleted = 0 
            AND t.date_entered BETWEEN '{$date_from}' AND '{$date_to} 23:59:59'
            AND u.deleted = 0
            GROUP BY t.assigned_user_id, u.user_name, u.first_name, u.last_name
            HAVING total_tradeins > 0
            ORDER BY total_tradeins DESC
        ";
        
        $result = $db->query($query);
        $activity = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $activity[] = array(
                'user_name' => $row['user_name'],
                'full_name' => $row['full_name'] ?: $row['user_name'],
                'total_tradeins' => $row['total_tradeins'],
                'appraised_count' => $row['appraised_count'],
                'approved_count' => $row['approved_count'],
                'avg_appraised_value' => round($row['avg_appraised_value'] ?? 0, 2),
                'appraisal_rate' => $row['total_tradeins'] > 0 ? 
                    round(($row['appraised_count'] / $row['total_tradeins']) * 100, 1) : 0,
            );
        }
        
        return $activity;
    }

    /**
     * Get available status options for filter
     */
    private function getStatusOptions()
    {
        global $app_list_strings;
        return $app_list_strings['tradein_status_list'] ?? array();
    }

    /**
     * Get available make options for filter
     */
    private function getMakeOptions()
    {
        global $db;
        
        $query = "
            SELECT DISTINCT make 
            FROM dm_tradeins 
            WHERE deleted = 0 
            AND make IS NOT NULL 
            AND make != '' 
            ORDER BY make
        ";
        
        $result = $db->query($query);
        $options = array('' => 'All Makes');
        
        while ($row = $db->fetchByAssoc($result)) {
            $options[$row['make']] = $row['make'];
        }
        
        return $options;
    }
} 