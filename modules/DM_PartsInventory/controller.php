<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Controller
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

/**
 * DM_PartsInventory Controller
 * 
 * Handles custom actions and views for the Parts Inventory module
 */
class DM_PartsInventoryController extends SugarController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Pre-action hook for custom processing
     */
    protected function pre_action()
    {
        parent::pre_action();
        
        // Add custom JavaScript for parts inventory functionality
        if (in_array($this->action, array('EditView', 'DetailView'))) {
            echo '<script type="text/javascript" src="modules/DM_PartsInventory/js/DM_PartsInventory.js"></script>';
        }
    }

    /**
     * Custom action for low stock report
     */
    public function action_low_stock_report()
    {
        $this->view = 'low_stock_report';
    }

    /**
     * Custom action for parts lookup (for service orders)
     */
    public function action_parts_lookup()
    {
        $this->view = 'parts_lookup';
    }

    /**
     * Custom action for inventory report
     */
    public function action_inventory_report()
    {
        $this->view = 'inventory_report';
    }

    /**
     * AJAX action for parts search
     */
    public function action_search_parts()
    {
        $criteria = array();
        
        if (!empty($_POST['part_number'])) {
            $criteria['part_number'] = $_POST['part_number'];
        }
        
        if (!empty($_POST['description'])) {
            $criteria['description'] = $_POST['description'];
        }
        
        if (!empty($_POST['manufacturer'])) {
            $criteria['manufacturer'] = $_POST['manufacturer'];
        }
        
        if (!empty($_POST['category'])) {
            $criteria['category'] = $_POST['category'];
        }

        $parts = DM_PartsInventory::searchParts($criteria);

        $response = array(
            'success' => true,
            'parts' => $parts,
            'count' => count($parts)
        );

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for updating stock quantity
     */
    public function action_update_stock()
    {
        if (!empty($_POST['part_id']) && isset($_POST['quantity']) && !empty($_POST['operation'])) {
            $part = BeanFactory::getBean('DM_PartsInventory', $_POST['part_id']);
            
            if ($part && !$part->deleted) {
                $quantity = (int)$_POST['quantity'];
                $operation = $_POST['operation']; // 'add' or 'subtract'
                
                $part->updateStock($quantity, $operation);
                
                $response = array(
                    'success' => true,
                    'new_quantity' => $part->quantity_on_hand,
                    'stock_status' => $part->getStockStatus()
                );
            } else {
                $response = array(
                    'success' => false,
                    'error' => 'Part not found'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'error' => 'Missing required parameters'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for getting low stock parts
     */
    public function action_get_low_stock_parts()
    {
        $lowStockParts = DM_PartsInventory::getLowStockParts();

        $response = array(
            'success' => true,
            'parts' => $lowStockParts,
            'count' => count($lowStockParts)
        );

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for calculating markup
     */
    public function action_calculate_markup()
    {
        if (!empty($_POST['cost']) && !empty($_POST['retail_price'])) {
            $cost = (float)$_POST['cost'];
            $retail_price = (float)$_POST['retail_price'];
            
            if ($cost > 0) {
                $markup = (($retail_price - $cost) / $cost) * 100;
                $profit = $retail_price - $cost;
                
                $response = array(
                    'success' => true,
                    'markup_percentage' => round($markup, 2),
                    'profit_amount' => round($profit, 2)
                );
            } else {
                $response = array(
                    'success' => false,
                    'error' => 'Cost must be greater than zero'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'error' => 'Missing cost or retail price'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for getting parts by category
     */
    public function action_get_parts_by_category()
    {
        if (!empty($_POST['category'])) {
            $parts = DM_PartsInventory::getPartsByCategory($_POST['category']);

            $response = array(
                'success' => true,
                'parts' => $parts,
                'count' => count($parts)
            );
        } else {
            $response = array(
                'success' => false,
                'error' => 'Category not specified'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }
}
?>