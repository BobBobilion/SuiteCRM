<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Module
 * 
 * This class defines the DM_PartsInventory SugarBean entity for managing 
 * automotive parts inventory, stock levels, pricing, and supplier information 
 * within dealership service operations.
 * 
 * Features:
 * - Parts catalog management
 * - Inventory tracking and reorder points
 * - Supplier relationship management
 * - Cost and pricing management
 * - Stock level monitoring
 * - Parts lookup and search functionality
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_PartsInventory Bean Class
 * 
 * Manages parts inventory with stock tracking, supplier relationships,
 * pricing management, and integration with service order workflows
 */
#[\AllowDynamicProperties]
class DM_PartsInventory extends Basic
{
    public $table_name = "dm_partsinventory";
    public $object_name = "DM_PartsInventory";
    public $module_dir = "DM_PartsInventory";
    public $module_name = "DM_PartsInventory";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for parts inventory (compliance requirement)
    public $audited = true;
    
    // Core parts identification fields
    public $id;
    public $name;                          // Display name (Part Number - Description)
    public $part_number;                   // Manufacturer part number
    
    // Part information
    public $manufacturer;                  // Part manufacturer
    public $category;                      // Part category
    public $description;                   // Part description
    public $location;                      // Bin/shelf location
    
    // Inventory tracking
    public $quantity_on_hand;              // Current stock level
    public $reorder_point;                 // When to reorder
    public $last_ordered_date;             // Last order date
    
    // Pricing
    public $cost;                          // Current cost
    public $retail_price;                  // Customer price
    
    // Supplier relationship
    public $supplier_id;                   // Primary supplier
    
    // Additional information
    public $notes;                         // Additional notes
    
    // Standard SugarBean fields
    public $assigned_user_id;              // Assigned parts manager
    public $date_entered;
    public $date_modified;
    public $created_by;
    public $modified_user_id;
    public $deleted;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Initialize logging for parts inventory operations
        $GLOBALS['log']->info("DM_PartsInventory: Initializing Parts Inventory Bean");
    }

    /**
     * Generate display name and handle inventory updates
     */
    public function save($check_notify = false)
    {
        // Log the save operation
        $GLOBALS['log']->info("DM_PartsInventory: Saving parts inventory record ID: " . $this->id);
        
        // Auto-generate name if not provided
        if (empty($this->name) && !empty($this->part_number)) {
            $this->name = $this->part_number;
            
            // Add description if available
            if (!empty($this->description)) {
                $this->name .= ' - ' . substr($this->description, 0, 50);
                if (strlen($this->description) > 50) {
                    $this->name .= '...';
                }
            }
            
            $GLOBALS['log']->info("DM_PartsInventory: Auto-generated name: " . $this->name);
        }
        
        // Check for low stock conditions
        $this->checkStockLevels();
        
        return parent::save($check_notify);
    }

    /**
     * Check stock levels and trigger alerts if needed
     */
    private function checkStockLevels()
    {
        if (!empty($this->quantity_on_hand) && !empty($this->reorder_point)) {
            if ($this->quantity_on_hand <= $this->reorder_point) {
                $GLOBALS['log']->info("DM_PartsInventory: Low stock alert for part: " . $this->part_number . 
                                    " (Stock: {$this->quantity_on_hand}, Reorder Point: {$this->reorder_point})");
                
                // Could trigger email alerts or other notifications here
                $this->triggerLowStockAlert();
            }
        }
    }

    /**
     * Trigger low stock alert (placeholder for notification system)
     */
    private function triggerLowStockAlert()
    {
        // Placeholder for low stock alert system
        // Could send emails, create tasks, etc.
        $GLOBALS['log']->info("DM_PartsInventory: Low stock alert triggered for part: " . $this->part_number);
    }

    /**
     * Update stock quantity (used when parts are consumed)
     */
    public function updateStock($quantity_used, $operation = 'subtract')
    {
        if ($operation == 'subtract') {
            $this->quantity_on_hand = max(0, $this->quantity_on_hand - $quantity_used);
        } else if ($operation == 'add') {
            $this->quantity_on_hand += $quantity_used;
        }
        
        $this->save();
        
        $GLOBALS['log']->info("DM_PartsInventory: Updated stock for part {$this->part_number} - Operation: {$operation}, Quantity: {$quantity_used}, New Stock: {$this->quantity_on_hand}");
    }

    /**
     * Calculate markup percentage
     */
    public function getMarkupPercentage()
    {
        if (!empty($this->cost) && !empty($this->retail_price) && $this->cost > 0) {
            $markup = (($this->retail_price - $this->cost) / $this->cost) * 100;
            return round($markup, 2);
        }
        return 0;
    }

    /**
     * Get stock status information
     */
    public function getStockStatus()
    {
        $status = array(
            'quantity' => $this->quantity_on_hand,
            'reorder_point' => $this->reorder_point,
            'status' => 'In Stock',
            'color' => 'success',
            'needs_reorder' => false
        );
        
        if (empty($this->quantity_on_hand) || $this->quantity_on_hand == 0) {
            $status['status'] = 'Out of Stock';
            $status['color'] = 'danger';
            $status['needs_reorder'] = true;
        } else if (!empty($this->reorder_point) && $this->quantity_on_hand <= $this->reorder_point) {
            $status['status'] = 'Low Stock';
            $status['color'] = 'warning';
            $status['needs_reorder'] = true;
        }
        
        return $status;
    }

    /**
     * Get parts summary for reporting
     */
    public function getPartsSummary()
    {
        return array(
            'part_number' => $this->part_number,
            'description' => $this->description,
            'manufacturer' => $this->manufacturer,
            'category' => $this->category,
            'quantity_on_hand' => $this->quantity_on_hand,
            'cost' => $this->cost,
            'retail_price' => $this->retail_price,
            'markup_percentage' => $this->getMarkupPercentage(),
            'stock_status' => $this->getStockStatus()
        );
    }

    /**
     * Search parts by various criteria
     */
    public static function searchParts($criteria = array())
    {
        $db = DBManagerFactory::getInstance();
        $where_clauses = array("deleted = 0");
        
        if (!empty($criteria['part_number'])) {
            $where_clauses[] = "part_number LIKE '%" . $db->quote($criteria['part_number']) . "%'";
        }
        
        if (!empty($criteria['description'])) {
            $where_clauses[] = "description LIKE '%" . $db->quote($criteria['description']) . "%'";
        }
        
        if (!empty($criteria['manufacturer'])) {
            $where_clauses[] = "manufacturer LIKE '%" . $db->quote($criteria['manufacturer']) . "%'";
        }
        
        if (!empty($criteria['category'])) {
            $where_clauses[] = "category = '" . $db->quote($criteria['category']) . "'";
        }
        
        $where_clause = implode(' AND ', $where_clauses);
        
        $query = "SELECT id, part_number, description, manufacturer, category, quantity_on_hand, retail_price 
                  FROM dm_partsinventory 
                  WHERE {$where_clause} 
                  ORDER BY part_number";
        
        $result = $db->query($query);
        $parts = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $parts[] = $row;
        }
        
        $GLOBALS['log']->info("DM_PartsInventory: Found " . count($parts) . " parts matching search criteria");
        
        return $parts;
    }

    /**
     * Get parts by category
     */
    public static function getPartsByCategory($category)
    {
        return self::searchParts(array('category' => $category));
    }

    /**
     * Get low stock parts
     */
    public static function getLowStockParts()
    {
        $db = DBManagerFactory::getInstance();
        
        $query = "SELECT id, part_number, description, manufacturer, category, location, 
                         quantity_on_hand, reorder_point, cost, retail_price
                  FROM dm_partsinventory 
                  WHERE deleted = 0 
                  AND (quantity_on_hand <= reorder_point OR quantity_on_hand = 0)
                  ORDER BY quantity_on_hand ASC";
        
        $result = $db->query($query);
        $parts = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $parts[] = $row;
        }
        
        $GLOBALS['log']->info("DM_PartsInventory: Found " . count($parts) . " parts with low stock");
        
        return $parts;
    }

    /**
     * Bean relationship setup
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}