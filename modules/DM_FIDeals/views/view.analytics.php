<?php
/**
 * SuiteCRM F&I Deal Center - Analytics Dashboard View
 * 
 * This view provides executive-level analytics and insights for F&I operations
 * including KPIs, trends, and performance benchmarks.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_FIDealsViewAnalytics extends SugarView
{
    public $type = 'analytics';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Analytics dashboard view initialized");
    }
    
    /**
     * Display the analytics dashboard
     */
    public function display()
    {
        global $mod_strings, $current_user;
        
        $GLOBALS['log']->debug("F&I Deal Center: Displaying analytics dashboard");
        
        // Get date range (default to last 90 days)
        $dateTo = date('Y-m-d');
        $dateFrom = date('Y-m-d', strtotime('-90 days'));
        
        // Get analytics data
        $analytics = $this->getAnalyticsData($dateFrom, $dateTo);
        
        // Display the dashboard
        $this->displayAnalyticsDashboard($analytics, $dateFrom, $dateTo);
    }
    
    /**
     * Get comprehensive analytics data
     */
    private function getAnalyticsData($dateFrom, $dateTo)
    {
        $data = array(
            'overview' => $this->getOverviewMetrics($dateFrom, $dateTo),
            'trends' => $this->getTrendAnalysis($dateFrom, $dateTo),
            'products' => $this->getProductAnalytics($dateFrom, $dateTo),
            'profitability' => $this->getProfitabilityAnalysis($dateFrom, $dateTo),
            'benchmarks' => $this->getBenchmarkData($dateFrom, $dateTo),
        );
        
        return $data;
    }
    
    /**
     * Get overview metrics
     */
    private function getOverviewMetrics($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    COUNT(*) as total_deals,
                    SUM(amount_financed) as total_financed,
                    SUM(total_gross) as total_profit,
                    SUM(backend_gross) as fi_profit,
                    SUM(fi_product_profit) as product_profit,
                    SUM(fi_product_commission) as total_commission,
                    AVG(backend_gross) as avg_fi_profit,
                    COUNT(CASE WHEN warranty_total > 0 THEN 1 END) as warranty_sales,
                    COUNT(CASE WHEN gap_amount > 0 THEN 1 END) as gap_sales
                  FROM dm_fideals 
                  WHERE deleted = 0 
                  AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'";
        
        $result = $GLOBALS['db']->query($query);
        $overview = $GLOBALS['db']->fetchByAssoc($result);
        
        // Calculate key ratios
        $overview['profit_per_deal'] = $overview['total_deals'] > 0 ? $overview['total_profit'] / $overview['total_deals'] : 0;
        $overview['fi_penetration'] = $overview['total_deals'] > 0 ? ($overview['fi_profit'] / $overview['total_financed']) * 100 : 0;
        $overview['product_penetration'] = $overview['total_deals'] > 0 ? (($overview['warranty_sales'] + $overview['gap_sales']) / ($overview['total_deals'] * 2)) * 100 : 0;
        
        return $overview;
    }
    
    /**
     * Get trend analysis data
     */
    private function getTrendAnalysis($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    DATE_FORMAT(date_entered, '%Y-%m') as month,
                    COUNT(*) as deal_count,
                    SUM(total_gross) as total_profit,
                    SUM(backend_gross) as fi_profit,
                    SUM(fi_product_profit) as product_profit,
                    AVG(monthly_payment) as avg_payment
                  FROM dm_fideals 
                  WHERE deleted = 0 
                  AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  GROUP BY DATE_FORMAT(date_entered, '%Y-%m')
                  ORDER BY month";
        
        $result = $GLOBALS['db']->query($query);
        $trends = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $row['profit_per_deal'] = $row['deal_count'] > 0 ? $row['total_profit'] / $row['deal_count'] : 0;
            $trends[] = $row;
        }
        
        return $trends;
    }
    
    /**
     * Get product analytics
     */
    private function getProductAnalytics($dateFrom, $dateTo)
    {
        require_once('modules/DM_FIDeals/FIProduct.php');
        $productCatalog = FIProduct::getDefaultProducts();
        
        $analytics = array();
        
        foreach ($productCatalog as $productKey => $product) {
            $field = $this->mapProductToField($productKey);
            
            if ($field) {
                $query = "SELECT 
                            COUNT(*) as total_opportunities,
                            COUNT(CASE WHEN $field > 0 THEN 1 END) as sales_count,
                            SUM($field) as total_revenue,
                            AVG(CASE WHEN $field > 0 THEN $field END) as avg_sale_amount
                          FROM dm_fideals 
                          WHERE deleted = 0 
                          AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                          AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'";
                
                $result = $GLOBALS['db']->query($query);
                $data = $GLOBALS['db']->fetchByAssoc($result);
                
                $analytics[$productKey] = array(
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'penetration_rate' => $data['total_opportunities'] > 0 ? ($data['sales_count'] / $data['total_opportunities']) * 100 : 0,
                    'total_revenue' => $data['total_revenue'] ?: 0,
                    'avg_sale_amount' => $data['avg_sale_amount'] ?: 0,
                    'sales_count' => $data['sales_count'],
                );
            }
        }
        
        return $analytics;
    }
    
    /**
     * Map product keys to database fields
     */
    private function mapProductToField($productKey)
    {
        $mapping = array(
            'extended_warranty' => 'warranty_total',
            'gap_insurance' => 'gap_amount',
            'theft_protection' => 'etch_amount',
            'maintenance_package' => 'maintenance_amount',
        );
        
        return $mapping[$productKey] ?? null;
    }
    
    /**
     * Get profitability analysis
     */
    private function getProfitabilityAnalysis($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    u.first_name, u.last_name,
                    COUNT(d.id) as deal_count,
                    SUM(d.total_gross) as total_profit,
                    SUM(d.backend_gross) as fi_profit,
                    SUM(d.fi_product_profit) as product_profit,
                    AVG(d.total_gross) as avg_profit_per_deal,
                    SUM(d.warranty_total + d.gap_amount + d.etch_amount + d.maintenance_amount) as product_sales
                  FROM dm_fideals d
                  LEFT JOIN users u ON d.assigned_user_id = u.id
                  WHERE d.deleted = 0 
                  AND d.date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND d.date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  GROUP BY u.id, u.first_name, u.last_name
                  ORDER BY total_profit DESC
                  LIMIT 10";
        
        $result = $GLOBALS['db']->query($query);
        $profitability = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $row['manager_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
            $profitability[] = $row;
        }
        
        return $profitability;
    }
    
    /**
     * Get benchmark data for comparison
     */
    private function getBenchmarkData($dateFrom, $dateTo)
    {
        // Industry benchmarks (these would typically come from external sources)
        $benchmarks = array(
            'avg_fi_profit_per_deal' => 2500,
            'target_warranty_penetration' => 65,
            'target_gap_penetration' => 45,
            'target_backend_gross_percentage' => 15,
            'target_deals_per_month' => 50,
        );
        
        // Get current performance for comparison
        $current = $this->getOverviewMetrics($dateFrom, $dateTo);
        
        $comparison = array(
            'fi_profit_vs_benchmark' => $current['avg_fi_profit'] - $benchmarks['avg_fi_profit_per_deal'],
            'warranty_penetration_vs_target' => $current['product_penetration'] - $benchmarks['target_warranty_penetration'],
            'performance_rating' => $this->calculatePerformanceRating($current, $benchmarks),
        );
        
        return array(
            'benchmarks' => $benchmarks,
            'current' => $current,
            'comparison' => $comparison,
        );
    }
    
    /**
     * Calculate overall performance rating
     */
    private function calculatePerformanceRating($current, $benchmarks)
    {
        $score = 0;
        $maxScore = 100;
        
        // F&I profit per deal (25 points)
        if ($current['avg_fi_profit'] >= $benchmarks['avg_fi_profit_per_deal']) {
            $score += 25;
        } else {
            $score += ($current['avg_fi_profit'] / $benchmarks['avg_fi_profit_per_deal']) * 25;
        }
        
        // Product penetration (25 points)
        if ($current['product_penetration'] >= $benchmarks['target_warranty_penetration']) {
            $score += 25;
        } else {
            $score += ($current['product_penetration'] / $benchmarks['target_warranty_penetration']) * 25;
        }
        
        // Deal volume (25 points) - simplified calculation
        $monthsInPeriod = max(1, (strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime('-90 days')))) / (30 * 24 * 3600));
        $dealsPerMonth = $current['total_deals'] / $monthsInPeriod;
        
        if ($dealsPerMonth >= $benchmarks['target_deals_per_month']) {
            $score += 25;
        } else {
            $score += ($dealsPerMonth / $benchmarks['target_deals_per_month']) * 25;
        }
        
        // Overall profitability (25 points)
        if ($current['profit_per_deal'] >= $benchmarks['avg_fi_profit_per_deal']) {
            $score += 25;
        } else {
            $score += ($current['profit_per_deal'] / $benchmarks['avg_fi_profit_per_deal']) * 25;
        }
        
        return min(100, max(0, $score));
    }
    
    /**
     * Display the analytics dashboard
     */
    private function displayAnalyticsDashboard($analytics, $dateFrom, $dateTo)
    {
        echo '<div style="max-width: 1600px; margin: 0 auto; padding: 20px; background: #f8f9fa;">';
        
        // Dashboard header
        echo '<div style="margin-bottom: 30px; text-align: center;">';
        echo '<h1 style="color: #2c3e50; margin-bottom: 10px;">F&I Analytics Dashboard</h1>';
        echo '<p style="color: #7f8c8d; font-size: 16px;">Performance Period: ' . date('M j, Y', strtotime($dateFrom)) . ' - ' . date('M j, Y', strtotime($dateTo)) . '</p>';
        echo '</div>';
        
        // Key Performance Indicators
        $this->displayKPICards($analytics['overview']);
        
        // Performance rating
        $this->displayPerformanceRating($analytics['benchmarks']);
        
        // Charts and trends section
        echo '<div style="display: flex; gap: 20px; margin-bottom: 30px;">';
        
        // Trend analysis
        echo '<div style="flex: 2; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        echo '<h3 style="margin-top: 0; color: #2c3e50;">Monthly Trends</h3>';
        $this->displayTrendChart($analytics['trends']);
        echo '</div>';
        
        // Product performance
        echo '<div style="flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        echo '<h3 style="margin-top: 0; color: #2c3e50;">Product Performance</h3>';
        $this->displayProductChart($analytics['products']);
        echo '</div>';
        
        echo '</div>';
        
        // Manager performance table
        echo '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">';
        echo '<h3 style="margin-top: 0; color: #2c3e50;">Manager Performance</h3>';
        $this->displayManagerPerformanceTable($analytics['profitability']);
        echo '</div>';
        
        // Action buttons
        echo '<div style="text-align: center; margin-top: 30px;">';
        echo '<a href="index.php?module=DM_FIDeals&action=reports" class="btn btn-primary" style="background: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-right: 15px;">Detailed Reports</a>';
        echo '<a href="index.php?module=DM_FIDeals&action=index" class="btn btn-secondary" style="background: #95a5a6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">Back to Deals</a>';
        echo '</div>';
        
        echo '</div>';
        
        $GLOBALS['log']->debug("F&I Deal Center: Analytics dashboard displayed successfully");
    }
    
    /**
     * Display KPI cards
     */
    private function displayKPICards($overview)
    {
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">';
        
        $kpis = array(
            array(
                'title' => 'Total Deals',
                'value' => number_format($overview['total_deals']),
                'color' => '#3498db',
                'icon' => '📋'
            ),
            array(
                'title' => 'Total Profit',
                'value' => '$' . number_format($overview['total_profit'], 0),
                'color' => '#2ecc71',
                'icon' => '💰'
            ),
            array(
                'title' => 'F&I Profit',
                'value' => '$' . number_format($overview['fi_profit'], 0),
                'color' => '#e74c3c',
                'icon' => '📈'
            ),
            array(
                'title' => 'Product Profit',
                'value' => '$' . number_format($overview['product_profit'], 0),
                'color' => '#f39c12',
                'icon' => '🛡️'
            ),
            array(
                'title' => 'Avg Profit/Deal',
                'value' => '$' . number_format($overview['profit_per_deal'], 0),
                'color' => '#9b59b6',
                'icon' => '⚡'
            ),
            array(
                'title' => 'Product Penetration',
                'value' => number_format($overview['product_penetration'], 1) . '%',
                'color' => '#1abc9c',
                'icon' => '🎯'
            ),
        );
        
        foreach ($kpis as $kpi) {
            echo '<div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid ' . $kpi['color'] . ';">';
            echo '<div style="display: flex; align-items: center; justify-content: space-between;">';
            echo '<div>';
            echo '<h4 style="margin: 0; color: #7f8c8d; font-size: 14px; font-weight: normal;">' . $kpi['title'] . '</h4>';
            echo '<div style="font-size: 28px; font-weight: bold; color: ' . $kpi['color'] . '; margin-top: 8px;">' . $kpi['value'] . '</div>';
            echo '</div>';
            echo '<div style="font-size: 32px; opacity: 0.7;">' . $kpi['icon'] . '</div>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Display performance rating
     */
    private function displayPerformanceRating($benchmarks)
    {
        $rating = $benchmarks['comparison']['performance_rating'];
        $ratingColor = $rating >= 80 ? '#2ecc71' : ($rating >= 60 ? '#f39c12' : '#e74c3c');
        $ratingLabel = $rating >= 80 ? 'Excellent' : ($rating >= 60 ? 'Good' : 'Needs Improvement');
        
        echo '<div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin-top: 0; color: #2c3e50;">Overall Performance Rating</h3>';
        echo '<div style="display: flex; align-items: center; justify-content: center; gap: 20px;">';
        echo '<div style="width: 100px; height: 100px; border-radius: 50%; background: conic-gradient(' . $ratingColor . ' ' . ($rating * 3.6) . 'deg, #ecf0f1 0deg); display: flex; align-items: center; justify-content: center; position: relative;">';
        echo '<div style="width: 80px; height: 80px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; color: ' . $ratingColor . ';">' . number_format($rating, 0) . '%</div>';
        echo '</div>';
        echo '<div>';
        echo '<div style="font-size: 24px; font-weight: bold; color: ' . $ratingColor . ';">' . $ratingLabel . '</div>';
        echo '<div style="color: #7f8c8d; margin-top: 5px;">Based on industry benchmarks</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Display trend chart (simplified table for MVP)
     */
    private function displayTrendChart($trends)
    {
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f8f9fa;">';
        echo '<th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Month</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Deals</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Total Profit</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">F&I Profit</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Profit/Deal</th>';
        echo '</tr>';
        
        foreach ($trends as $trend) {
            echo '<tr>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4;">' . date('M Y', strtotime($trend['month'] . '-01')) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">' . number_format($trend['deal_count']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($trend['total_profit'], 0) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($trend['fi_profit'], 0) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($trend['profit_per_deal'], 0) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
    
    /**
     * Display product performance chart
     */
    private function displayProductChart($products)
    {
        foreach ($products as $product) {
            $penetrationWidth = min(100, max(5, $product['penetration_rate']));
            $color = $product['penetration_rate'] >= 50 ? '#2ecc71' : ($product['penetration_rate'] >= 30 ? '#f39c12' : '#e74c3c');
            
            echo '<div style="margin-bottom: 15px;">';
            echo '<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">';
            echo '<span style="font-weight: 500; color: #2c3e50;">' . htmlspecialchars($product['name']) . '</span>';
            echo '<span style="color: #7f8c8d;">' . number_format($product['penetration_rate'], 1) . '%</span>';
            echo '</div>';
            echo '<div style="background: #ecf0f1; border-radius: 10px; height: 8px; overflow: hidden;">';
            echo '<div style="background: ' . $color . '; height: 100%; width: ' . $penetrationWidth . '%; border-radius: 10px; transition: width 0.3s ease;"></div>';
            echo '</div>';
            echo '<div style="font-size: 12px; color: #7f8c8d; margin-top: 2px;">$' . number_format($product['total_revenue'], 0) . ' total</div>';
            echo '</div>';
        }
    }
    
    /**
     * Display manager performance table
     */
    private function displayManagerPerformanceTable($profitability)
    {
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f8f9fa;">';
        echo '<th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Manager</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Deals</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Total Profit</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">F&I Profit</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Avg/Deal</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Product Sales</th>';
        echo '</tr>';
        
        foreach ($profitability as $manager) {
            echo '<tr>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; font-weight: 500;">' . htmlspecialchars($manager['manager_name']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">' . number_format($manager['deal_count']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($manager['total_profit'], 0) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($manager['fi_profit'], 0) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($manager['avg_profit_per_deal'], 0) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($manager['product_sales'], 0) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
} 