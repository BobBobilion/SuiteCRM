<?php
/**
 * SuiteCRM Service Orders - Service Summary Report View
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_ServiceOrdersViewService_summary_report extends SugarView
{
    public function display()
    {
        global $mod_strings, $app_strings;
        
        $this->ss->assign('MOD', $mod_strings);
        $this->ss->assign('APP', $app_strings);
        
        // Get service orders data
        $serviceOrders = $this->getServiceOrdersData();
        
        // Calculate statistics
        $statistics = $this->calculateStatistics($serviceOrders);
        
        $this->ss->assign('service_orders', $serviceOrders);
        $this->ss->assign('statistics', $statistics);
        $this->ss->assign('report_date', date('Y-m-d H:i:s'));
        
        echo $this->ss->fetch('modules/DM_ServiceOrders/tpls/service_summary_report.tpl');
    }
    
    private function getServiceOrdersData()
    {
        $db = DBManagerFactory::getInstance();
        
        // First try a simple count query to test table existence
        try {
            $countQuery = "SELECT COUNT(*) as total FROM dm_serviceorders";
            $countResult = $db->query($countQuery);
            $countRow = $db->fetchByAssoc($countResult);
            $GLOBALS['log']->debug("Service Summary Report: Table count query result: " . $countRow['total']);
        } catch (Exception $e) {
            $GLOBALS['log']->error("Service Summary Report: Table query failed: " . $e->getMessage());
        }
        
        // Try a simpler query first
        $query = "SELECT id, name, customer_name, vehicle_name, technician_name, 
                         service_type, service_status, total_cost, date_entered
                  FROM dm_serviceorders 
                  WHERE (deleted = 0 OR deleted IS NULL)
                  ORDER BY date_entered DESC
                  LIMIT 100";
        
        $result = $db->query($query);
        $orders = array();
        
        // Debug: Log the query and check if we get results
        $GLOBALS['log']->debug("Service Summary Report Query: " . $query);
        
        $rowCount = 0;
        while ($row = $db->fetchByAssoc($result)) {
            $rowCount++;
            $GLOBALS['log']->debug("Service Summary Report: Found row " . $rowCount . " - ID: " . $row['id']);
            // Format the date for display
            if (!empty($row['date_entered'])) {
                $row['formatted_date'] = date('Y-m-d', strtotime($row['date_entered']));
            } else {
                $row['formatted_date'] = '';
            }
            $orders[] = $row;
        }
        
        $GLOBALS['log']->debug("Service Summary Report: Total rows found: " . $rowCount);
        
        return $orders;
    }
    
    private function calculateStatistics($orders)
    {
        $totalOrders = count($orders);
        $completedOrders = 0;
        $pendingOrders = 0;
        $totalRevenue = 0;
        $totalLaborHours = 0;
        
        foreach ($orders as $order) {
            if ($order['service_status'] == 'Completed') {
                $completedOrders++;
            } elseif ($order['service_status'] == 'In Progress' || $order['service_status'] == 'Scheduled') {
                $pendingOrders++;
            }
            
            $totalRevenue += floatval($order['total_cost']);
            $totalLaborHours += floatval($order['labor_hours']);
        }
        
        return array(
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'total_revenue' => $totalRevenue,
            'total_labor_hours' => $totalLaborHours,
            'average_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0
        );
    }
}
?>