<?php
/**
 * SuiteCRM Service & Parts Hub - Low Stock Report View
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_PartsInventoryViewLow_stock_report extends SugarView
{
    /**
     * Display the low stock report
     */
    public function display()
    {
        global $mod_strings, $app_strings;
        
        $this->ss->assign('MOD', $mod_strings);
        $this->ss->assign('APP', $app_strings);
        
        // Get low stock parts
        $lowStockParts = DM_PartsInventory::getLowStockParts();
        
        // Calculate statistics
        $totalParts = count($lowStockParts);
        $outOfStock = 0;
        $lowStock = 0;
        $totalReorderValue = 0;
        
        foreach ($lowStockParts as $part) {
            if ($part['quantity_on_hand'] == 0) {
                $outOfStock++;
            } else {
                $lowStock++;
            }
            
            // Calculate reorder value (quantity needed to reach reorder point)
            $reorderQty = max(0, $part['reorder_point'] - $part['quantity_on_hand']);
            $partCost = !empty($part['cost']) ? floatval($part['cost']) : 0;
            $totalReorderValue += $reorderQty * $partCost;
        }
        
        $statistics = array(
            'total_parts' => $totalParts,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'estimated_reorder_value' => $totalReorderValue
        );
        
        $this->ss->assign('low_stock_parts', $lowStockParts);
        $this->ss->assign('statistics', $statistics);
        $this->ss->assign('report_date', date('Y-m-d H:i:s'));
        
        echo $this->ss->fetch('modules/DM_PartsInventory/tpls/low_stock_report.tpl');
    }
}