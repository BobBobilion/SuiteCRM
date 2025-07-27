<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Service & Parts Hub - Service History Module
 * 
 * This class defines the DM_ServiceHistory SugarBean entity for maintaining 
 * historical records of all service work performed on vehicles within 
 * automotive dealership service operations.
 * 
 * Features:
 * - Complete service history tracking
 * - Vehicle service timeline
 * - Parts usage tracking
 * - Labor and cost history
 * - Technician performance tracking
 * - Service pattern analysis
 * - Warranty and maintenance records
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_ServiceHistory Bean Class
 * 
 * Manages service history records with complete tracking of services performed,
 * parts used, labor costs, and integration with vehicles and service orders
 */
#[\AllowDynamicProperties]
class DM_ServiceHistory extends Basic
{
    public $table_name = "dm_servicehistory";
    public $object_name = "DM_ServiceHistory";
    public $module_dir = "DM_ServiceHistory";
    public $module_name = "DM_ServiceHistory";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for service history (compliance requirement)
    public $audited = true;
    
    // Core service history identification fields
    public $id;
    public $name;                          // Display name (Service Date - Vehicle)
    
    // Relationship links
    public $vehicle_id;                    // Link to vehicle
    public $service_order_id;              // Link to service order
    
    // Service information
    public $service_date;                  // When service was performed
    public $mileage;                       // Odometer reading at service
    public $service_type;                  // Type of service performed
    public $services_performed;            // Detailed list of services
    public $parts_used;                    // JSON array of parts used
    
    // Labor and cost tracking
    public $labor_hours;                   // Labor hours spent
    public $total_cost;                    // Total cost of service
    
    // Personnel
    public $technician_id;                 // Who performed the work
    
    // Additional information
    public $notes;                         // Service notes
    
    // Standard SugarBean fields
    public $assigned_user_id;              // Assigned service advisor
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
        
        // Initialize logging for service history operations
        $GLOBALS['log']->info("DM_ServiceHistory: Initializing Service History Bean");
    }

    /**
     * Generate display name and handle service history processing
     */
    public function save($check_notify = false)
    {
        // Log the save operation
        $GLOBALS['log']->info("DM_ServiceHistory: Saving service history record ID: " . $this->id);
        
        // Auto-generate name if not provided
        if (empty($this->name)) {
            $this->name = 'Service';
            
            // Add service date if available
            if (!empty($this->service_date)) {
                $this->name = 'Service ' . date('m/d/Y', strtotime($this->service_date));
            }
            
            // Add vehicle information if available
            if (!empty($this->vehicle_id)) {
                $vehicle = BeanFactory::getBean('DM_VehiclesInventory', $this->vehicle_id);
                if ($vehicle && !empty($vehicle->name)) {
                    $this->name .= ' - ' . $vehicle->name;
                } else {
                    // Try AutoInventory if DM_VehiclesInventory doesn't exist
                    $vehicle = BeanFactory::getBean('AutoInventory', $this->vehicle_id);
                    if ($vehicle && !empty($vehicle->name)) {
                        $this->name .= ' - ' . $vehicle->name;
                    }
                }
            }
            
            $GLOBALS['log']->info("DM_ServiceHistory: Auto-generated name: " . $this->name);
        }
        
        // Process parts used if it's a JSON string
        if (!empty($this->parts_used) && is_string($this->parts_used)) {
            $this->validatePartsUsedFormat();
        }
        
        return parent::save($check_notify);
    }

    /**
     * Validate and format parts used JSON
     */
    private function validatePartsUsedFormat()
    {
        if (!empty($this->parts_used)) {
            // If it's already a valid JSON string, leave it
            $decoded = json_decode($this->parts_used, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If not valid JSON, assume it's a simple text list and convert
                $parts_array = array();
                $lines = explode("\n", $this->parts_used);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $parts_array[] = array(
                            'description' => $line,
                            'quantity' => 1,
                            'timestamp' => date('Y-m-d H:i:s')
                        );
                    }
                }
                $this->parts_used = json_encode($parts_array);
            }
        }
    }

    /**
     * Get formatted parts used list
     */
    public function getPartsUsedList()
    {
        if (empty($this->parts_used)) {
            return array();
        }
        
        $parts = json_decode($this->parts_used, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback to text parsing
            $parts_array = array();
            $lines = explode("\n", $this->parts_used);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $parts_array[] = array(
                        'description' => $line,
                        'quantity' => 1
                    );
                }
            }
            return $parts_array;
        }
        
        return $parts;
    }

    /**
     * Add a part to the parts used list
     */
    public function addPartUsed($part_id, $part_number, $description, $quantity = 1, $cost = 0)
    {
        $parts = $this->getPartsUsedList();
        
        $new_part = array(
            'part_id' => $part_id,
            'part_number' => $part_number,
            'description' => $description,
            'quantity' => $quantity,
            'cost' => $cost,
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        $parts[] = $new_part;
        $this->parts_used = json_encode($parts);
        
        $GLOBALS['log']->info("DM_ServiceHistory: Added part to service history - Part: {$part_number}, Qty: {$quantity}");
    }

    /**
     * Get service summary for reporting
     */
    public function getServiceSummary()
    {
        return array(
            'service_date' => $this->service_date,
            'service_type' => $this->service_type,
            'mileage' => $this->mileage,
            'services_performed' => $this->services_performed,
            'labor_hours' => $this->labor_hours,
            'total_cost' => $this->total_cost,
            'parts_count' => count($this->getPartsUsedList()),
            'technician_id' => $this->technician_id
        );
    }

    /**
     * Get vehicle service history
     */
    public static function getVehicleServiceHistory($vehicle_id, $limit = null)
    {
        $db = DBManagerFactory::getInstance();
        
        $query = "SELECT id, name, service_date, service_type, mileage, labor_hours, total_cost, services_performed 
                  FROM dm_servicehistory 
                  WHERE vehicle_id = '" . $db->quote($vehicle_id) . "' 
                  AND deleted = 0 
                  ORDER BY service_date DESC";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $result = $db->query($query);
        $history = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $history[] = $row;
        }
        
        $GLOBALS['log']->info("DM_ServiceHistory: Found " . count($history) . " service records for vehicle ID: " . $vehicle_id);
        
        return $history;
    }

    /**
     * Get service history by date range
     */
    public static function getServiceHistoryByDateRange($start_date, $end_date, $vehicle_id = null)
    {
        $db = DBManagerFactory::getInstance();
        
        $where_clauses = array();
        $where_clauses[] = "deleted = 0";
        $where_clauses[] = "service_date >= '" . $db->quote($start_date) . "'";
        $where_clauses[] = "service_date <= '" . $db->quote($end_date) . "'";
        
        if ($vehicle_id) {
            $where_clauses[] = "vehicle_id = '" . $db->quote($vehicle_id) . "'";
        }
        
        $where_clause = implode(' AND ', $where_clauses);
        
        $query = "SELECT id, name, service_date, service_type, mileage, labor_hours, total_cost, vehicle_id 
                  FROM dm_servicehistory 
                  WHERE {$where_clause} 
                  ORDER BY service_date DESC";
        
        $result = $db->query($query);
        $history = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $history[] = $row;
        }
        
        $GLOBALS['log']->info("DM_ServiceHistory: Found " . count($history) . " service records for date range: {$start_date} to {$end_date}");
        
        return $history;
    }

    /**
     * Get technician performance data
     */
    public static function getTechnicianPerformance($technician_id, $start_date = null, $end_date = null)
    {
        $db = DBManagerFactory::getInstance();
        
        $where_clauses = array();
        $where_clauses[] = "deleted = 0";
        $where_clauses[] = "technician_id = '" . $db->quote($technician_id) . "'";
        
        if ($start_date) {
            $where_clauses[] = "service_date >= '" . $db->quote($start_date) . "'";
        }
        
        if ($end_date) {
            $where_clauses[] = "service_date <= '" . $db->quote($end_date) . "'";
        }
        
        $where_clause = implode(' AND ', $where_clauses);
        
        $query = "SELECT COUNT(*) as service_count, 
                         SUM(labor_hours) as total_hours, 
                         SUM(total_cost) as total_revenue,
                         AVG(labor_hours) as avg_hours_per_service
                  FROM dm_servicehistory 
                  WHERE {$where_clause}";
        
        $result = $db->query($query);
        $performance = $db->fetchByAssoc($result);
        
        $GLOBALS['log']->info("DM_ServiceHistory: Retrieved performance data for technician ID: " . $technician_id);
        
        return $performance;
    }

    /**
     * Get most common services performed
     */
    public static function getCommonServices($limit = 10)
    {
        $db = DBManagerFactory::getInstance();
        
        $query = "SELECT service_type, COUNT(*) as frequency 
                  FROM dm_servicehistory 
                  WHERE deleted = 0 
                  AND service_type IS NOT NULL 
                  AND service_type != '' 
                  GROUP BY service_type 
                  ORDER BY frequency DESC 
                  LIMIT " . (int)$limit;
        
        $result = $db->query($query);
        $services = array();
        
        while ($row = $db->fetchByAssoc($result)) {
            $services[] = $row;
        }
        
        $GLOBALS['log']->info("DM_ServiceHistory: Found " . count($services) . " common service types");
        
        return $services;
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