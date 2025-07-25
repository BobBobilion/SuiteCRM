<?php
/**
 * SuiteCRM F&I Deal Center - Reports View
 * 
 * This view provides comprehensive F&I reporting functionality including
 * deal summaries, profit analysis, product penetration, and export capabilities.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_FIDealsViewReports extends SugarView
{
    public $type = 'reports';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Reports view initialized");
    }
    
    /**
     * Preprocess the view
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Check if user has access to this module
        if (!ACLController::checkAccess('DM_FIDeals', 'list', true)) {
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
    }
    
    /**
     * Display the reports interface
     */
    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        $GLOBALS['log']->debug("F&I Deal Center: Displaying reports view");
        
        // Get report type and date range from request
        $reportType = $_REQUEST['report_type'] ?? 'dashboard';
        $dateFrom = $_REQUEST['date_from'] ?? date('Y-m-01');
        $dateTo = $_REQUEST['date_to'] ?? date('Y-m-d');
        $exportFormat = $_REQUEST['export'] ?? '';
        
        // Handle export requests
        if (!empty($exportFormat)) {
            $this->handleExportRequest($reportType, $dateFrom, $dateTo, $exportFormat);
            return;
        }
        
        // Get report data based on type
        $reportData = $this->generateReportData($reportType, $dateFrom, $dateTo);
        
        // Display the reports interface
        $this->displayReportsInterface($reportType, $dateFrom, $dateTo, $reportData);
    }
    
    /**
     * Generate report data based on type and date range
     */
    private function generateReportData($reportType, $dateFrom, $dateTo)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Generating report data - Type: $reportType, From: $dateFrom, To: $dateTo");
        
        switch ($reportType) {
            case 'deal_summary':
                return $this->getDealSummaryData($dateFrom, $dateTo);
            case 'profit_analysis':
                return $this->getProfitAnalysisData($dateFrom, $dateTo);
            case 'fi_performance':
                return $this->getFIPerformanceData($dateFrom, $dateTo);
            case 'lender_performance':
                return $this->getLenderPerformanceData($dateFrom, $dateTo);
            case 'dashboard':
            default:
                return $this->getDashboardData($dateFrom, $dateTo);
        }
    }
    
    /**
     * Get dashboard overview data
     */
    private function getDashboardData($dateFrom, $dateTo)
    {
        $data = array(
            'summary' => $this->getBasicSummary($dateFrom, $dateTo),
            'deals' => $this->getRecentDeals($dateFrom, $dateTo),
            'products' => $this->getProductSummary($dateFrom, $dateTo),
            'lenders' => $this->getLenderSummary($dateFrom, $dateTo),
        );
        
        return $data;
    }
    
    /**
     * Get basic summary statistics
     */
    private function getBasicSummary($dateFrom, $dateTo)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Getting basic summary data");
        
        $query = "SELECT 
                    COUNT(*) as total_deals,
                    COUNT(CASE WHEN deal_status = 'Complete' THEN 1 END) as completed_deals,
                    COUNT(CASE WHEN deal_status = 'Approved' THEN 1 END) as approved_deals,
                    COUNT(CASE WHEN deal_status = 'Funded' THEN 1 END) as funded_deals,
                    SUM(sales_price) as total_sales_volume,
                    SUM(amount_financed) as total_financed,
                    SUM(frontend_gross) as total_frontend_gross,
                    SUM(backend_gross) as total_backend_gross,
                    SUM(total_gross) as total_gross_profit,
                    AVG(monthly_payment) as avg_monthly_payment,
                    SUM(warranty_total) as total_warranty_sales,
                    SUM(gap_amount) as total_gap_sales,
                    SUM(fi_product_profit) as total_product_profit,
                    SUM(fi_product_commission) as total_commission
                  FROM dm_fideals 
                  WHERE deleted = 0 
                  AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'";
        
        $result = $GLOBALS['db']->query($query);
        $summary = $GLOBALS['db']->fetchByAssoc($result);
        
        // Calculate additional metrics
        $summary['completion_rate'] = $summary['total_deals'] > 0 ? 
                                     ($summary['completed_deals'] / $summary['total_deals']) * 100 : 0;
        $summary['avg_deal_profit'] = $summary['total_deals'] > 0 ? 
                                     $summary['total_gross_profit'] / $summary['total_deals'] : 0;
        $summary['product_penetration'] = $summary['total_deals'] > 0 ? 
                                         (($summary['total_warranty_sales'] + $summary['total_gap_sales']) > 0 ? 
                                          (count(array_filter([$summary['total_warranty_sales'], $summary['total_gap_sales']])) / $summary['total_deals']) * 100 : 0) : 0;
        
        return $summary;
    }
    
    /**
     * Get recent deals data
     */
    private function getRecentDeals($dateFrom, $dateTo, $limit = 10)
    {
        $query = "SELECT 
                    d.id, d.deal_number, d.deal_status, d.sales_price, 
                    d.total_gross, d.monthly_payment, d.date_entered,
                    c.name as customer_name,
                    l.name as lender_name,
                    u.first_name, u.last_name
                  FROM dm_fideals d
                  LEFT JOIN accounts c ON d.customer_id = c.id
                  LEFT JOIN accounts l ON d.lender_id = l.id
                  LEFT JOIN users u ON d.assigned_user_id = u.id
                  WHERE d.deleted = 0 
                  AND d.date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND d.date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  ORDER BY d.date_entered DESC
                  LIMIT $limit";
        
        $result = $GLOBALS['db']->query($query);
        $deals = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $row['manager_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
            $deals[] = $row;
        }
        
        return $deals;
    }
    
    /**
     * Get product summary data
     */
    private function getProductSummary($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    COUNT(CASE WHEN warranty_total > 0 THEN 1 END) as warranty_count,
                    SUM(warranty_total) as warranty_total,
                    COUNT(CASE WHEN gap_amount > 0 THEN 1 END) as gap_count,
                    SUM(gap_amount) as gap_total,
                    COUNT(CASE WHEN etch_amount > 0 THEN 1 END) as etch_count,
                    SUM(etch_amount) as etch_total,
                    COUNT(CASE WHEN maintenance_amount > 0 THEN 1 END) as maintenance_count,
                    SUM(maintenance_amount) as maintenance_total,
                    SUM(fi_product_profit) as total_product_profit,
                    COUNT(*) as total_deals
                  FROM dm_fideals 
                  WHERE deleted = 0 
                  AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'";
        
        $result = $GLOBALS['db']->query($query);
        $products = $GLOBALS['db']->fetchByAssoc($result);
        
        // Calculate penetration rates
        if ($products['total_deals'] > 0) {
            $products['warranty_penetration'] = ($products['warranty_count'] / $products['total_deals']) * 100;
            $products['gap_penetration'] = ($products['gap_count'] / $products['total_deals']) * 100;
            $products['etch_penetration'] = ($products['etch_count'] / $products['total_deals']) * 100;
            $products['maintenance_penetration'] = ($products['maintenance_count'] / $products['total_deals']) * 100;
        } else {
            $products['warranty_penetration'] = 0;
            $products['gap_penetration'] = 0;
            $products['etch_penetration'] = 0;
            $products['maintenance_penetration'] = 0;
        }
        
        return $products;
    }
    
    /**
     * Get lender summary data
     */
    private function getLenderSummary($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    l.name as lender_name,
                    COUNT(d.id) as deal_count,
                    SUM(d.amount_financed) as total_financed,
                    AVG(d.interest_rate) as avg_rate,
                    SUM(d.finance_reserve) as total_reserve
                  FROM dm_fideals d
                  LEFT JOIN accounts l ON d.lender_id = l.id
                  WHERE d.deleted = 0 
                  AND d.lender_id IS NOT NULL
                  AND d.date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND d.date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  GROUP BY l.id, l.name
                  ORDER BY deal_count DESC
                  LIMIT 10";
        
        $result = $GLOBALS['db']->query($query);
        $lenders = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $lenders[] = $row;
        }
        
        return $lenders;
    }
    
    /**
     * Get deal summary report data
     */
    private function getDealSummaryData($dateFrom, $dateTo)
    {
        $data = array(
            'summary' => $this->getBasicSummary($dateFrom, $dateTo),
            'monthly_breakdown' => $this->getMonthlyBreakdown($dateFrom, $dateTo),
            'status_breakdown' => $this->getStatusBreakdown($dateFrom, $dateTo),
            'manager_performance' => $this->getManagerPerformance($dateFrom, $dateTo),
        );
        
        return $data;
    }
    
    /**
     * Get monthly breakdown data
     */
    private function getMonthlyBreakdown($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    DATE_FORMAT(date_entered, '%Y-%m') as month,
                    COUNT(*) as deal_count,
                    SUM(sales_price) as total_sales,
                    SUM(total_gross) as total_profit,
                    AVG(monthly_payment) as avg_payment
                  FROM dm_fideals 
                  WHERE deleted = 0 
                  AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  GROUP BY DATE_FORMAT(date_entered, '%Y-%m')
                  ORDER BY month";
        
        $result = $GLOBALS['db']->query($query);
        $breakdown = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $breakdown[] = $row;
        }
        
        return $breakdown;
    }
    
    /**
     * Get status breakdown data
     */
    private function getStatusBreakdown($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    deal_status,
                    COUNT(*) as count,
                    SUM(total_gross) as total_profit
                  FROM dm_fideals 
                  WHERE deleted = 0 
                  AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  GROUP BY deal_status
                  ORDER BY count DESC";
        
        $result = $GLOBALS['db']->query($query);
        $breakdown = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $breakdown[] = $row;
        }
        
        return $breakdown;
    }
    
    /**
     * Get manager performance data
     */
    private function getManagerPerformance($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    u.first_name, u.last_name,
                    COUNT(d.id) as deal_count,
                    SUM(d.total_gross) as total_profit,
                    AVG(d.total_gross) as avg_profit,
                    SUM(d.fi_product_profit) as product_profit,
                    SUM(d.fi_product_commission) as commission_earned
                  FROM dm_fideals d
                  LEFT JOIN users u ON d.assigned_user_id = u.id
                  WHERE d.deleted = 0 
                  AND d.date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND d.date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  GROUP BY u.id, u.first_name, u.last_name
                  ORDER BY total_profit DESC";
        
        $result = $GLOBALS['db']->query($query);
        $performance = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $row['manager_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
            $performance[] = $row;
        }
        
        return $performance;
    }
    
    /**
     * Get F&I performance data
     */
    private function getFIPerformanceData($dateFrom, $dateTo)
    {
        $data = array(
            'product_penetration' => $this->getDetailedProductPenetration($dateFrom, $dateTo),
            'commission_analysis' => $this->getCommissionAnalysis($dateFrom, $dateTo),
            'profit_trends' => $this->getProfitTrends($dateFrom, $dateTo),
            'top_performers' => $this->getTopPerformers($dateFrom, $dateTo),
        );
        
        return $data;
    }
    
    /**
     * Get detailed product penetration rates
     */
    private function getDetailedProductPenetration($dateFrom, $dateTo)
    {
        // Include F&I Product class for product analysis
        require_once('modules/DM_FIDeals/FIProduct.php');
        $products = FIProduct::getDefaultProducts();
        
        $penetration = array();
        
        foreach ($products as $productKey => $productInfo) {
            // Map product keys to database fields
            $field = $this->mapProductToField($productKey);
            
            if ($field) {
                $query = "SELECT 
                            COUNT(*) as total_deals,
                            COUNT(CASE WHEN $field > 0 THEN 1 END) as product_sales,
                            SUM($field) as total_amount,
                            AVG(CASE WHEN $field > 0 THEN $field END) as avg_amount
                          FROM dm_fideals 
                          WHERE deleted = 0 
                          AND date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                          AND date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'";
                
                $result = $GLOBALS['db']->query($query);
                $data = $GLOBALS['db']->fetchByAssoc($result);
                
                $penetration[$productKey] = array(
                    'name' => $productInfo['name'],
                    'category' => $productInfo['category'],
                    'total_deals' => $data['total_deals'],
                    'product_sales' => $data['product_sales'],
                    'penetration_rate' => $data['total_deals'] > 0 ? ($data['product_sales'] / $data['total_deals']) * 100 : 0,
                    'total_amount' => $data['total_amount'] ?: 0,
                    'avg_amount' => $data['avg_amount'] ?: 0,
                );
            }
        }
        
        return $penetration;
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
     * Get commission analysis data
     */
    private function getCommissionAnalysis($dateFrom, $dateTo)
    {
        $query = "SELECT 
                    u.first_name, u.last_name,
                    SUM(d.fi_product_commission) as total_commission,
                    COUNT(d.id) as deal_count,
                    AVG(d.fi_product_commission) as avg_commission,
                    SUM(d.warranty_total) as warranty_sales,
                    SUM(d.gap_amount) as gap_sales
                  FROM dm_fideals d
                  LEFT JOIN users u ON d.assigned_user_id = u.id
                  WHERE d.deleted = 0 
                  AND d.fi_product_commission > 0
                  AND d.date_entered >= '" . $GLOBALS['db']->quote($dateFrom) . "'
                  AND d.date_entered <= '" . $GLOBALS['db']->quote($dateTo . ' 23:59:59') . "'
                  GROUP BY u.id, u.first_name, u.last_name
                  ORDER BY total_commission DESC";
        
        $result = $GLOBALS['db']->query($query);
        $analysis = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $row['manager_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
            $analysis[] = $row;
        }
        
        return $analysis;
    }
    
    /**
     * Display the reports interface
     */
    private function displayReportsInterface($reportType, $dateFrom, $dateTo, $reportData)
    {
        echo '<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">';
        
        // Page header with navigation
        $this->displayReportHeader($reportType, $dateFrom, $dateTo);
        
        // Report content based on type
        switch ($reportType) {
            case 'deal_summary':
                $this->displayDealSummaryReport($reportData);
                break;
            case 'fi_performance':
                $this->displayFIPerformanceReport($reportData);
                break;
            case 'dashboard':
            default:
                $this->displayDashboard($reportData);
                break;
        }
        
        echo '</div>';
        
        $GLOBALS['log']->debug("F&I Deal Center: Reports interface displayed successfully");
    }
    
    /**
     * Display report header with navigation
     */
    private function displayReportHeader($reportType, $dateFrom, $dateTo)
    {
        echo '<div style="border-bottom: 2px solid #ddd; padding-bottom: 15px; margin-bottom: 20px;">';
        echo '<h2>F&I Deal Center Reports</h2>';
        
        // Date range form
        echo '<form method="GET" style="display: inline-block; margin-right: 20px;">';
        echo '<input type="hidden" name="module" value="DM_FIDeals">';
        echo '<input type="hidden" name="action" value="reports">';
        echo '<input type="hidden" name="report_type" value="' . htmlspecialchars($reportType) . '">';
        echo '<label>From: </label>';
        echo '<input type="date" name="date_from" value="' . htmlspecialchars($dateFrom) . '" style="margin-right: 10px;">';
        echo '<label>To: </label>';
        echo '<input type="date" name="date_to" value="' . htmlspecialchars($dateTo) . '" style="margin-right: 10px;">';
        echo '<button type="submit" style="background: #007cba; color: white; padding: 5px 15px; border: none; border-radius: 3px;">Update</button>';
        echo '</form>';
        
        // Export buttons
        echo '<div style="display: inline-block;">';
        echo '<a href="index.php?module=DM_FIDeals&action=reports&report_type=' . $reportType . '&date_from=' . $dateFrom . '&date_to=' . $dateTo . '&export=excel" class="btn btn-secondary" style="margin-right: 10px;">Export Excel</a>';
        echo '<a href="index.php?module=DM_FIDeals&action=reports&report_type=' . $reportType . '&date_from=' . $dateFrom . '&date_to=' . $dateTo . '&export=pdf" class="btn btn-secondary">Export PDF</a>';
        echo '</div>';
        
        // Report type navigation
        echo '<div style="margin-top: 15px;">';
        $reports = array(
            'dashboard' => 'Dashboard',
            'deal_summary' => 'Deal Summary',
            'fi_performance' => 'F&I Performance',
        );
        
        foreach ($reports as $key => $name) {
            $active = ($key === $reportType) ? 'style="background: #007cba; color: white;"' : 'style="background: #f5f5f5; color: #333;"';
            echo '<a href="index.php?module=DM_FIDeals&action=reports&report_type=' . $key . '&date_from=' . $dateFrom . '&date_to=' . $dateTo . '" ' . $active . ' style="padding: 8px 16px; margin-right: 5px; text-decoration: none; border-radius: 3px; display: inline-block;">' . $name . '</a>';
        }
        echo '</div>';
        
        echo '</div>';
    }
    
    /**
     * Display dashboard overview
     */
    private function displayDashboard($data)
    {
        $summary = $data['summary'];
        
        // Key metrics cards
        echo '<div style="display: flex; gap: 20px; margin-bottom: 30px;">';
        
        $metrics = array(
            array('title' => 'Total Deals', 'value' => number_format($summary['total_deals']), 'color' => '#3498db'),
            array('title' => 'Completed Deals', 'value' => number_format($summary['completed_deals']), 'color' => '#2ecc71'),
            array('title' => 'Total Gross Profit', 'value' => '$' . number_format($summary['total_gross_profit'], 2), 'color' => '#e74c3c'),
            array('title' => 'Product Commission', 'value' => '$' . number_format($summary['total_commission'], 2), 'color' => '#f39c12'),
        );
        
        foreach ($metrics as $metric) {
            echo '<div style="flex: 1; background: white; padding: 20px; border-radius: 5px; border-left: 4px solid ' . $metric['color'] . '; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
            echo '<h3 style="margin: 0; color: #666; font-size: 14px;">' . $metric['title'] . '</h3>';
            echo '<div style="font-size: 24px; font-weight: bold; color: ' . $metric['color'] . '; margin-top: 5px;">' . $metric['value'] . '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        
        // Two-column layout for additional data
        echo '<div style="display: flex; gap: 20px;">';
        
        // Recent deals
        echo '<div style="flex: 1;">';
        echo '<h3>Recent Deals</h3>';
        echo '<div style="background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f8f9fa;">';
        echo '<th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Deal #</th>';
        echo '<th style="padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6;">Customer</th>';
        echo '<th style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">Profit</th>';
        echo '<th style="padding: 12px; text-align: center; border-bottom: 1px solid #dee2e6;">Status</th>';
        echo '</tr>';
        
        foreach ($data['deals'] as $deal) {
            echo '<tr>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4;">' . htmlspecialchars($deal['deal_number']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4;">' . htmlspecialchars($deal['customer_name'] ?: 'N/A') . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($deal['total_gross'], 2) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: center;">' . htmlspecialchars($deal['deal_status']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</div>';
        echo '</div>';
        
        // Product performance
        echo '<div style="flex: 1;">';
        echo '<h3>Product Performance</h3>';
        echo '<div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
        
        $products = $data['products'];
        echo '<div style="margin-bottom: 15px;"><strong>Warranty:</strong> ' . number_format($products['warranty_penetration'], 1) . '% penetration ($' . number_format($products['warranty_total'], 2) . ')</div>';
        echo '<div style="margin-bottom: 15px;"><strong>GAP Insurance:</strong> ' . number_format($products['gap_penetration'], 1) . '% penetration ($' . number_format($products['gap_total'], 2) . ')</div>';
        echo '<div style="margin-bottom: 15px;"><strong>Theft Protection:</strong> ' . number_format($products['etch_penetration'], 1) . '% penetration ($' . number_format($products['etch_total'], 2) . ')</div>';
        echo '<div style="margin-bottom: 15px;"><strong>Maintenance:</strong> ' . number_format($products['maintenance_penetration'], 1) . '% penetration ($' . number_format($products['maintenance_total'], 2) . ')</div>';
        
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
    }
    
    /**
     * Display deal summary report
     */
    private function displayDealSummaryReport($data)
    {
        echo '<h3>Deal Summary Report</h3>';
        
        // Summary metrics
        $summary = $data['summary'];
        echo '<div style="background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
        echo '<div style="display: flex; justify-content: space-between;">';
        echo '<div><strong>Total Deals:</strong> ' . number_format($summary['total_deals']) . '</div>';
        echo '<div><strong>Completion Rate:</strong> ' . number_format($summary['completion_rate'], 1) . '%</div>';
        echo '<div><strong>Avg Deal Profit:</strong> $' . number_format($summary['avg_deal_profit'], 2) . '</div>';
        echo '<div><strong>Total Volume:</strong> $' . number_format($summary['total_sales_volume'], 2) . '</div>';
        echo '</div>';
        echo '</div>';
        
        // Monthly breakdown
        if (!empty($data['monthly_breakdown'])) {
            echo '<h4>Monthly Breakdown</h4>';
            echo '<div style="background: white; border-radius: 5px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
            echo '<table style="width: 100%; border-collapse: collapse;">';
            echo '<tr style="background: #f8f9fa;">';
            echo '<th style="padding: 12px; text-align: left;">Month</th>';
            echo '<th style="padding: 12px; text-align: right;">Deals</th>';
            echo '<th style="padding: 12px; text-align: right;">Sales Volume</th>';
            echo '<th style="padding: 12px; text-align: right;">Total Profit</th>';
            echo '<th style="padding: 12px; text-align: right;">Avg Payment</th>';
            echo '</tr>';
            
            foreach ($data['monthly_breakdown'] as $month) {
                echo '<tr>';
                echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4;">' . date('M Y', strtotime($month['month'] . '-01')) . '</td>';
                echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">' . number_format($month['deal_count']) . '</td>';
                echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($month['total_sales'], 2) . '</td>';
                echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($month['total_profit'], 2) . '</td>';
                echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($month['avg_payment'], 2) . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            echo '</div>';
        }
    }
    
    /**
     * Display F&I performance report
     */
    private function displayFIPerformanceReport($data)
    {
        echo '<h3>F&I Performance Report</h3>';
        
        // Product penetration
        echo '<h4>Product Penetration Rates</h4>';
        echo '<div style="background: white; border-radius: 5px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f8f9fa;">';
        echo '<th style="padding: 12px; text-align: left;">Product</th>';
        echo '<th style="padding: 12px; text-align: right;">Sales Count</th>';
        echo '<th style="padding: 12px; text-align: right;">Penetration Rate</th>';
        echo '<th style="padding: 12px; text-align: right;">Total Amount</th>';
        echo '<th style="padding: 12px; text-align: right;">Avg Amount</th>';
        echo '</tr>';
        
        foreach ($data['product_penetration'] as $product) {
            echo '<tr>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4;">' . htmlspecialchars($product['name']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">' . number_format($product['product_sales']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">' . number_format($product['penetration_rate'], 1) . '%</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($product['total_amount'], 2) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($product['avg_amount'], 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</div>';
        
        // Commission analysis
        echo '<h4>Commission Analysis</h4>';
        echo '<div style="background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f8f9fa;">';
        echo '<th style="padding: 12px; text-align: left;">Manager</th>';
        echo '<th style="padding: 12px; text-align: right;">Total Commission</th>';
        echo '<th style="padding: 12px; text-align: right;">Deal Count</th>';
        echo '<th style="padding: 12px; text-align: right;">Avg Commission</th>';
        echo '</tr>';
        
        foreach ($data['commission_analysis'] as $manager) {
            echo '<tr>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4;">' . htmlspecialchars($manager['manager_name']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($manager['total_commission'], 2) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">' . number_format($manager['deal_count']) . '</td>';
            echo '<td style="padding: 10px; border-bottom: 1px solid #f1f3f4; text-align: right;">$' . number_format($manager['avg_commission'], 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</div>';
    }
    
    /**
     * Handle export requests
     */
    private function handleExportRequest($reportType, $dateFrom, $dateTo, $format)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Handling export request - Type: $reportType, Format: $format");
        
        if ($format === 'excel') {
            $this->exportToExcel($reportType, $dateFrom, $dateTo);
        } elseif ($format === 'pdf') {
            $this->exportToPDF($reportType, $dateFrom, $dateTo);
        }
    }
    
    /**
     * Export report to Excel (CSV format for MVP)
     */
    private function exportToExcel($reportType, $dateFrom, $dateTo)
    {
        $filename = "fi_report_" . $reportType . "_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        if ($reportType === 'deal_summary') {
            // Export deal summary
            fputcsv($output, ['Deal Number', 'Customer', 'Sales Price', 'Total Gross', 'Status', 'Date']);
            
            $deals = $this->getRecentDeals($dateFrom, $dateTo, 1000);
            foreach ($deals as $deal) {
                fputcsv($output, [
                    $deal['deal_number'],
                    $deal['customer_name'],
                    $deal['sales_price'],
                    $deal['total_gross'],
                    $deal['deal_status'],
                    $deal['date_entered']
                ]);
            }
        }
        
        fclose($output);
        
        $GLOBALS['log']->debug("F&I Deal Center: Excel export completed");
    }
    
    /**
     * Export report to PDF (basic implementation)
     */
    private function exportToPDF($reportType, $dateFrom, $dateTo)
    {
        // For MVP, we'll provide a simple PDF note
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="fi_report_' . $reportType . '_' . date('Y-m-d') . '.pdf"');
        
        echo "PDF export functionality will be enhanced in future versions.\n";
        echo "For now, please use the Print function from your browser to generate PDFs.";
        
        $GLOBALS['log']->debug("F&I Deal Center: PDF export note displayed");
    }
} 