<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Service & Parts Hub - Service Orders Module
 * 
 * This class defines the DM_ServiceOrders SugarBean entity for managing 
 * service appointments, repair orders, and maintenance scheduling within 
 * automotive dealership service operations.
 * 
 * Features:
 * - Service order creation and management
 * - Customer and vehicle linking
 * - Parts inventory integration
 * - Labor tracking and pricing
 * - Service history generation
 * - Appointment scheduling
 * - Basic reporting and status tracking
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_ServiceOrders Bean Class
 * 
 * Manages service orders with customer vehicle linking, parts tracking,
 * labor management, and integration with service workflow processes
 */
#[\AllowDynamicProperties]
class DM_ServiceOrders extends Basic
{
    public $table_name = "dm_serviceorders";
    public $object_name = "DM_ServiceOrders";
    public $module_dir = "DM_ServiceOrders";
    public $module_name = "DM_ServiceOrders";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for service orders (compliance requirement)
    public $audited = true;
    
    // Core service order identification fields
    public $id;
    public $name;                          // Display name (RO# - Customer)
    public $service_order_number;          // Unique RO number
    
    // Relationship links
    public $customer_id;                   // Link to customer account
    public $vehicle_id;                    // Link to vehicle inventory
    
    // Vehicle identification
    public $vin;                          // Vehicle Identification Number
    public $mileage_in;                   // Odometer at check-in
    
    // Scheduling and timeline
    public $appointment_date;             // Scheduled appointment
    public $promise_time;                 // Promised completion
    
    // Personnel assignments
    public $service_advisor_id;           // Assigned advisor
    public $technician_id;                // Primary technician
    
    // Service categorization
    public $service_type;                 // Maintenance/Repair/Warranty
    public $service_status;               // Scheduled/In-Progress/Complete/Picked-up
    
    // Labor tracking
    public $labor_hours;                  // Hours worked
    public $labor_rate;                   // Hourly rate
    public $labor_total;                  // Total labor charges
    
    // Financial tracking
    public $parts_total;                  // Total parts charges
    public $tax_amount;                   // Sales tax
    public $total_amount;                 // Grand total
    
    // Service details
    public $customer_concern;             // What brought them in
    public $work_performed;               // What was done
    public $notes;                        // Additional notes
    
    // Standard SugarBean fields
    public $assigned_user_id;             // Assigned service advisor
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
        
        // Initialize logging for service order operations
        $GLOBALS['log']->info("DM_ServiceOrders: Initializing Service Order Bean");
    }

    /**
     * Generate display name and service order number
     */
    public function save($check_notify = false)
    {
        // Log the save operation
        $GLOBALS['log']->info("DM_ServiceOrders: Saving service order record ID: " . $this->id);
        
        // Auto-generate service order number if not provided
        if (empty($this->service_order_number)) {
            $this->service_order_number = $this->generateServiceOrderNumber();
            $GLOBALS['log']->info("DM_ServiceOrders: Generated RO number: " . $this->service_order_number);
        }
        
        // Auto-generate name if not provided
        if (empty($this->name) && !empty($this->service_order_number)) {
            $this->name = 'RO #' . $this->service_order_number;
            
            // Add customer name if available
            if (!empty($this->customer_id)) {
                $customer = BeanFactory::getBean('Accounts', $this->customer_id);
                if ($customer && !empty($customer->name)) {
                    $this->name .= ' - ' . $customer->name;
                }
            }
            
            $GLOBALS['log']->info("DM_ServiceOrders: Auto-generated name: " . $this->name);
        }
        
        // Calculate totals
        $this->calculateTotals();
        
        $result = parent::save($check_notify);
        
        // Auto-create service history when service order is completed
        if ($this->service_status == 'Complete' && !empty($this->id)) {
            $this->createServiceHistory();
        }
        
        return $result;
    }

    /**
     * Generate unique service order number
     */
    private function generateServiceOrderNumber()
    {
        $prefix = 'RO';
        $date = date('ymd');
        
        // Find the highest number for today
        $query = "SELECT service_order_number FROM {$this->table_name} 
                  WHERE service_order_number LIKE '{$prefix}{$date}%' 
                  AND deleted = 0 
                  ORDER BY service_order_number DESC LIMIT 1";
        
        $result = $this->db->query($query);
        $row = $this->db->fetchByAssoc($result);
        
        if ($row && !empty($row['service_order_number'])) {
            // Extract the sequence number and increment
            $lastNumber = substr($row['service_order_number'], -3);
            $nextSequence = str_pad((int)$lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // First order of the day
            $nextSequence = '001';
        }
        
        return $prefix . $date . $nextSequence;
    }

    /**
     * Calculate labor and total amounts
     */
    public function calculateTotals()
    {
        // Calculate labor total
        if (!empty($this->labor_hours) && !empty($this->labor_rate)) {
            $this->labor_total = $this->labor_hours * $this->labor_rate;
        }
        
        // Calculate grand total
        $subtotal = (float)$this->labor_total + (float)$this->parts_total;
        $this->total_amount = $subtotal + (float)$this->tax_amount;
        
        $GLOBALS['log']->info("DM_ServiceOrders: Calculated totals - Labor: {$this->labor_total}, Parts: {$this->parts_total}, Tax: {$this->tax_amount}, Total: {$this->total_amount}");
    }

    /**
     * Create service history record when service order is completed
     */
    private function createServiceHistory()
    {
        if (empty($this->vehicle_id)) {
            return false;
        }
        
        $serviceHistory = BeanFactory::newBean('DM_ServiceHistory');
        if ($serviceHistory) {
            $serviceHistory->name = 'Service for RO #' . $this->service_order_number;
            $serviceHistory->vehicle_id = $this->vehicle_id;
            $serviceHistory->service_order_id = $this->id;
            $serviceHistory->service_date = date('Y-m-d');
            $serviceHistory->mileage = $this->mileage_in;
            $serviceHistory->service_type = $this->service_type;
            $serviceHistory->services_performed = $this->work_performed;
            $serviceHistory->labor_hours = $this->labor_hours;
            $serviceHistory->total_cost = $this->total_amount;
            $serviceHistory->technician_id = $this->technician_id;
            $serviceHistory->notes = $this->notes;
            $serviceHistory->assigned_user_id = $this->assigned_user_id;
            
            $serviceHistory->save();
            
            $GLOBALS['log']->info("DM_ServiceOrders: Created service history record for RO: " . $this->service_order_number);
            return true;
        }
        
        return false;
    }

    /**
     * Get status display with color coding information
     */
    public function getStatusInfo()
    {
        $statusInfo = array(
            'status' => $this->service_status,
            'color' => 'default',
            'description' => ''
        );
        
        switch ($this->service_status) {
            case 'Scheduled':
                $statusInfo['color'] = 'info';
                $statusInfo['description'] = 'Appointment scheduled';
                break;
            case 'In-Progress':
                $statusInfo['color'] = 'warning';
                $statusInfo['description'] = 'Work in progress';
                break;
            case 'Complete':
                $statusInfo['color'] = 'success';
                $statusInfo['description'] = 'Service completed';
                break;
            case 'Picked-up':
                $statusInfo['color'] = 'primary';
                $statusInfo['description'] = 'Vehicle picked up';
                break;
            default:
                $statusInfo['description'] = 'Status not set';
        }
        
        return $statusInfo;
    }

    /**
     * Get service profitability summary
     */
    public function getProfitabilitySummary()
    {
        return array(
            'labor_total' => $this->labor_total,
            'parts_total' => $this->parts_total,
            'tax_amount' => $this->tax_amount,
            'total_amount' => $this->total_amount,
            'labor_hours' => $this->labor_hours,
            'labor_rate' => $this->labor_rate
        );
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