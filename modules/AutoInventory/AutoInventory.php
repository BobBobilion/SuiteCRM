<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Auto Inventory System - Main Bean Class
 * 
 * This class defines the AutoInventory SugarBean entity for comprehensive
 * automotive inventory management in car dealership operations.
 * 
 * Features:
 * - Complete vehicle data management
 * - VIN validation and processing
 * - Pricing and valuation tracking
 * - Lot management and analytics
 * - Photo and feature management
 * - Sales workflow integration
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * AutoInventory Bean Class
 * 
 * Manages automotive inventory records with comprehensive vehicle data
 * including specifications, pricing, photos, and business workflow integration
 */
#[\AllowDynamicProperties]
class AutoInventory extends Basic
{
    public $table_name = "auto_inventory";
    public $object_name = "AutoInventory";
    public $module_dir = "AutoInventory";
    public $module_name = "AutoInventory";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for vehicle records
    public $audited = true;
    
    // Core vehicle identification fields
    public $id;
    public $name;                      // Vehicle display name (Year Make Model)
    public $vin_number;                // Vehicle Identification Number (17 chars)
    public $stock_id;                  // Dealer internal stock number
    
    // Vehicle specifications
    public $model_year;                // Model year
    public $manufacturer;              // Manufacturer (Ford, Toyota, etc.)
    public $vehicle_model;             // Model name (Camry, F-150, etc.)
    public $trim_level;                // Trim level (LE, XLT, etc.)
    public $body_type;                 // Sedan, SUV, Truck, Coupe, etc.
    public $paint_color;               // Exterior paint color
    public $interior_color;            // Interior color/material
    public $odometer;                  // Current odometer reading
    public $engine_info;               // Engine description (2.5L 4-Cyl, 5.0L V8, etc.)
    public $transmission_type;         // Transmission type (Automatic, Manual, CVT)
    public $drive_type;                // FWD, RWD, AWD, 4WD
    public $fuel_system;               // Gasoline, Diesel, Hybrid, Electric
    
    // Inventory status and condition
    public $inventory_status;          // Available, Sold, Pending, Service, Hold
    public $vehicle_condition;         // New, Used, Certified Pre-Owned
    public $lot_position;              // Lot location or space number
    
    // Pricing and financial data
    public $acquisition_date;          // Date vehicle was acquired
    public $cost_basis;                // Acquisition cost
    public $asking_price;              // Current asking price
    public $final_price;               // Final sale price (when sold)
    public $days_in_inventory;         // Auto-calculated days since acquisition
    
    // Market valuation and business intelligence
    public $market_valuation;          // Current market value (KBB/Edmunds)
    public $valuation_date;            // Last valuation update date
    public $acquisition_source;        // Trade-in, Auction, Purchase, etc.
    
    // Extended data (JSON stored)
    public $vehicle_features;          // JSON array of vehicle features
    public $photo_gallery;             // JSON array of photo URLs
    
    // Standard SugarBean fields
    public $date_entered;
    public $date_modified;
    public $created_by;
    public $modified_user_id;
    public $assigned_user_id;
    public $deleted;
    public $description;
    
    /**
     * Constructor - Initialize the bean with default values
     */
    public function __construct()
    {
        parent::__construct();
        
        // Set default values
        $this->inventory_status = 'Available';
        $this->vehicle_condition = 'Used';
        $this->days_in_inventory = 0;
        $this->deleted = 0;
        
        // Initialize JSON fields as empty arrays
        if (empty($this->vehicle_features)) {
            $this->vehicle_features = '[]';
        }
        if (empty($this->photo_gallery)) {
            $this->photo_gallery = '[]';
        }
        
        $GLOBALS['log']->debug("AutoInventory: Bean constructor initialized with defaults");
    }
    
    /**
     * Save override to handle custom business logic
     */
    public function save($check_notify = false)
    {
        $GLOBALS['log']->debug("AutoInventory: Starting save process for record: " . $this->id);
        
        // Generate display name before saving
        $this->name = $this->generateVehicleName();
        $GLOBALS['log']->debug("AutoInventory: Generated vehicle name: " . $this->name);
        
        // Validate VIN if provided
        if (!empty($this->vin_number) && !$this->validateVIN($this->vin_number)) {
            $GLOBALS['log']->error("AutoInventory: Invalid VIN provided: " . $this->vin_number);
            throw new Exception("Invalid VIN format. Please provide a valid 17-character VIN.");
        }
        
        // Calculate days in inventory
        $this->calculateInventoryDays();
        
        // Normalize JSON fields
        $this->normalizeDataFields();
        
        // Call parent save
        $result = parent::save($check_notify);
        
        $GLOBALS['log']->debug("AutoInventory: Save completed for record: " . $result);
        
        return $result;
    }
    
    /**
     * Generate a display name for the vehicle
     * Format: "2020 Ford F-150 XLT" or "Stock# ABC123"
     */
    private function generateVehicleName()
    {
        $nameParts = array();
        
        if (!empty($this->model_year)) {
            $nameParts[] = $this->model_year;
        }
        if (!empty($this->manufacturer)) {
            $nameParts[] = $this->manufacturer;
        }
        if (!empty($this->vehicle_model)) {
            $nameParts[] = $this->vehicle_model;
        }
        if (!empty($this->trim_level)) {
            $nameParts[] = $this->trim_level;
        }
        
        if (!empty($nameParts)) {
            $vehicleName = implode(' ', $nameParts);
        } else if (!empty($this->stock_id)) {
            $vehicleName = "Stock# " . $this->stock_id;
        } else {
            $vehicleName = "Auto Record";
        }
        
        $GLOBALS['log']->debug("AutoInventory: Generated vehicle name: " . $vehicleName);
        
        return $vehicleName;
    }
    
    /**
     * Validate VIN format (basic validation)
     * A valid VIN is exactly 17 characters, alphanumeric, excluding I, O, Q
     */
    private function validateVIN($vin)
    {
        // Check length
        if (strlen($vin) !== 17) {
            $GLOBALS['log']->debug("AutoInventory: VIN validation failed - incorrect length: " . strlen($vin));
            return false;
        }
        
        // Check for invalid characters (I, O, Q are not allowed in VINs)
        if (preg_match('/[IOQ]/', strtoupper($vin))) {
            $GLOBALS['log']->debug("AutoInventory: VIN validation failed - contains invalid characters");
            return false;
        }
        
        // Check alphanumeric
        if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', strtoupper($vin))) {
            $GLOBALS['log']->debug("AutoInventory: VIN validation failed - invalid format");
            return false;
        }
        
        $GLOBALS['log']->debug("AutoInventory: VIN validation passed for: " . $vin);
        return true;
    }
    
    /**
     * Calculate days in inventory based on acquisition date
     */
    private function calculateInventoryDays()
    {
        if (!empty($this->acquisition_date)) {
            $acquisitionDate = new DateTime($this->acquisition_date);
            $currentDate = new DateTime();
            $interval = $currentDate->diff($acquisitionDate);
            $this->days_in_inventory = $interval->days;
            
            $GLOBALS['log']->debug("AutoInventory: Calculated days in inventory: " . $this->days_in_inventory);
        } else {
            $this->days_in_inventory = 0;
            $GLOBALS['log']->debug("AutoInventory: No acquisition date found, setting days in inventory to 0");
        }
    }
    
    /**
     * Normalize JSON fields to ensure they're properly formatted
     */
    private function normalizeDataFields()
    {
        // Handle vehicle features field
        if (empty($this->vehicle_features) || $this->vehicle_features === 'null') {
            $this->vehicle_features = '[]';
        } else if (is_array($this->vehicle_features)) {
            $this->vehicle_features = json_encode($this->vehicle_features);
        } else if (!$this->isValidJSON($this->vehicle_features)) {
            $this->vehicle_features = '[]';
        }
        
        // Handle photo gallery field
        if (empty($this->photo_gallery) || $this->photo_gallery === 'null') {
            $this->photo_gallery = '[]';
        } else if (is_array($this->photo_gallery)) {
            $this->photo_gallery = json_encode($this->photo_gallery);
        } else if (!$this->isValidJSON($this->photo_gallery)) {
            $this->photo_gallery = '[]';
        }
        
        $GLOBALS['log']->debug("AutoInventory: Data fields normalized");
    }
    
    /**
     * Check if a string is valid JSON
     */
    private function isValidJSON($str)
    {
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Add a photo URL to the vehicle's photo gallery
     */
    public function addPhoto($photoUrl)
    {
        $photos = json_decode($this->photo_gallery, true);
        if (!is_array($photos)) {
            $photos = array();
        }
        
        if (!in_array($photoUrl, $photos)) {
            $photos[] = $photoUrl;
            $this->photo_gallery = json_encode($photos);
            $GLOBALS['log']->debug("AutoInventory: Added photo URL: " . $photoUrl);
        } else {
            $GLOBALS['log']->debug("AutoInventory: Photo URL already exists: " . $photoUrl);
        }
    }
    
    /**
     * Add a feature to the vehicle's feature array
     */
    public function addFeature($feature)
    {
        $features = json_decode($this->vehicle_features, true);
        if (!is_array($features)) {
            $features = array();
        }
        
        if (!in_array($feature, $features)) {
            $features[] = $feature;
            $this->vehicle_features = json_encode($features);
            $GLOBALS['log']->debug("AutoInventory: Added feature: " . $feature);
        } else {
            $GLOBALS['log']->debug("AutoInventory: Feature already exists: " . $feature);
        }
    }
    
    /**
     * Get photos as an array
     */
    public function getPhotosArray()
    {
        $photos = json_decode($this->photo_gallery, true);
        return is_array($photos) ? $photos : array();
    }
    
    /**
     * Get features as an array
     */
    public function getFeaturesArray()
    {
        $features = json_decode($this->vehicle_features, true);
        return is_array($features) ? $features : array();
    }
    
    /**
     * Mark vehicle as sold
     */
    public function markAsSold($salePrice = null)
    {
        $this->inventory_status = 'Sold';
        if ($salePrice !== null) {
            $this->final_price = $salePrice;
        }
        $GLOBALS['log']->debug("AutoInventory: Marked vehicle as sold. Sale price: " . $salePrice);
    }
    
    /**
     * Calculate profit margin percentage
     */
    public function getProfitMargin()
    {
        if (empty($this->cost_basis) || empty($this->final_price)) {
            return 0;
        }
        
        $profit = $this->final_price - $this->cost_basis;
        $margin = ($profit / $this->cost_basis) * 100;
        
        $GLOBALS['log']->debug("AutoInventory: Calculated profit margin: " . $margin . "%");
        
        return round($margin, 2);
    }
    
    /**
     * Override retrieve to calculate days in inventory
     */
    public function retrieve($id = -1, $encode = true, $deleted = true)
    {
        $result = parent::retrieve($id, $encode, $deleted);
        
        if ($result) {
            // Recalculate days in inventory when retrieving
            $this->calculateInventoryDays();
            $GLOBALS['log']->debug("AutoInventory: Retrieved record and calculated days in inventory: " . $this->days_in_inventory);
        }
        
        return $result;
    }
}
?> 