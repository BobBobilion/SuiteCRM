<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM Vehicle Inventory System - Main Bean Class
 * 
 * This class defines the DM_VehiclesInventory SugarBean entity with all necessary
 * methods and variables for managing automotive inventory in a car dealership CRM.
 * 
 * Features:
 * - Complete vehicle data management
 * - VIN validation and decoding
 * - Pricing and valuation tracking
 * - Days on lot calculation
 * - Photo and feature management
 * - Integration with sales workflow
 */

require_once("include/SugarObjects/templates/basic/Basic.php");

/**
 * DM_VehiclesInventory Bean Class
 * 
 * Manages vehicle inventory records with comprehensive automotive data
 * including specifications, pricing, photos, and business workflow integration
 */
#[\AllowDynamicProperties]
class DM_VehiclesInventory extends Basic
{
    public $table_name = "dm_vehiclesinventory";
    public $object_name = "DM_VehiclesInventory";
    public $module_dir = "DM_VehiclesInventory";
    public $module_name = "DM_VehiclesInventory";
    
    // Disable row-level security for now
    public $disable_row_level_security = true;
    
    // Enable auditing for vehicle records
    public $audited = true;
    
    // Core vehicle identification fields
    public $id;
    public $name;                      // Vehicle display name (Year Make Model)
    public $vin;                       // Vehicle Identification Number (17 chars)
    public $stock_number;              // Dealer internal stock number
    
    // Vehicle specifications
    public $year;                      // Model year
    public $make;                      // Manufacturer (Ford, Toyota, etc.)
    public $model;                     // Model name (Camry, F-150, etc.)
    public $trim;                      // Trim level (LE, XLT, etc.)
    public $body_style;                // Sedan, SUV, Truck, Coupe, etc.
    public $exterior_color;            // Exterior paint color
    public $interior_color;            // Interior color/material
    public $mileage;                   // Current odometer reading
    public $engine_type;               // Engine description (2.5L 4-Cyl, 5.0L V8, etc.)
    public $transmission;              // Transmission type (Automatic, Manual, CVT)
    public $drivetrain;                // FWD, RWD, AWD, 4WD
    public $fuel_type;                 // Gasoline, Diesel, Hybrid, Electric
    
    // Inventory status and condition
    public $status;                    // Available, Sold, Pending, Service, Hold
    public $condition_type;            // New, Used, Certified Pre-Owned
    public $location;                  // Lot location or space number
    
    // Pricing and financial data
    public $purchase_date;             // Date vehicle was acquired
    public $purchase_price;            // What dealer paid for vehicle
    public $list_price;                // Current asking price
    public $sale_price;                // Final sale price (when sold)
    public $market_value;              // Current market value (KBB/Edmunds)
    public $market_value_date;         // Last market valuation date
    
    // Business intelligence fields
    public $days_on_lot;               // Auto-calculated field
    public $source;                    // Trade-in, Auction, Purchase, etc.
    public $features;                  // JSON array of vehicle features
    public $photos;                    // JSON array of photo URLs
    public $notes;                     // Internal notes about vehicle
    
    // Standard SugarBean fields
    public $date_entered;
    public $date_modified;
    public $created_by;
    public $modified_user_id;
    public $assigned_user_id;
    public $deleted;
    
    /**
     * Constructor - Initialize the bean with default values
     */
    public function __construct()
    {
        parent::__construct();
        
        // Set default values for new records
        $this->status = 'Available';
        $this->condition_type = 'Used';
        $this->photos = '[]';  // Empty JSON array
        $this->features = '[]'; // Empty JSON array
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: Bean constructor initialized with defaults");
    }
    
    /**
     * Bean save override to implement business logic
     * 
     * @param boolean $check_notify - Whether to send notifications
     * @return string - The ID of the saved record
     */
    public function save($check_notify = false)
    {
        $GLOBALS['log']->debug("DM_VehiclesInventory: Starting save process for record: " . $this->id);
        
        // Auto-generate display name if not set
        if (empty($this->name)) {
            $this->name = $this->generateDisplayName();
            $GLOBALS['log']->debug("DM_VehiclesInventory: Generated display name: " . $this->name);
        }
        
        // Validate VIN if provided
        if (!empty($this->vin)) {
            if (!$this->validateVIN($this->vin)) {
                $GLOBALS['log']->error("DM_VehiclesInventory: Invalid VIN provided: " . $this->vin);
                throw new Exception("Invalid VIN format. Please provide a valid 17-character VIN.");
            }
        }
        
        // Calculate days on lot before saving
        $this->calculateDaysOnLot();
        
        // Ensure JSON fields are properly formatted
        $this->normalizeJSONFields();
        
        $result = parent::save($check_notify);
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: Save completed for record: " . $result);
        
        return $result;
    }
    
    /**
     * Generate display name for vehicle (Year Make Model)
     * 
     * @return string - Formatted display name
     */
    protected function generateDisplayName()
    {
        $nameParts = array();
        
        if (!empty($this->year)) {
            $nameParts[] = $this->year;
        }
        if (!empty($this->make)) {
            $nameParts[] = $this->make;
        }
        if (!empty($this->model)) {
            $nameParts[] = $this->model;
        }
        if (!empty($this->trim)) {
            $nameParts[] = $this->trim;
        }
        
        $displayName = implode(' ', $nameParts);
        $GLOBALS['log']->debug("DM_VehiclesInventory: Generated display name: " . $displayName);
        
        return $displayName ?: 'Unknown Vehicle';
    }
    
    /**
     * Validate VIN format (basic validation)
     * 
     * @param string $vin - VIN to validate
     * @return boolean - True if valid, false otherwise
     */
    protected function validateVIN($vin)
    {
        // Remove any whitespace and convert to uppercase
        $vin = strtoupper(trim($vin));
        
        // Basic VIN validation - 17 characters, alphanumeric except I, O, Q
        if (strlen($vin) !== 17) {
            $GLOBALS['log']->debug("DM_VehiclesInventory: VIN validation failed - incorrect length: " . strlen($vin));
            return false;
        }
        
        // Check for invalid characters (I, O, Q are not allowed in VINs)
        if (preg_match('/[IOQ]/', $vin)) {
            $GLOBALS['log']->debug("DM_VehiclesInventory: VIN validation failed - contains invalid characters");
            return false;
        }
        
        // Check that it's alphanumeric
        if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $vin)) {
            $GLOBALS['log']->debug("DM_VehiclesInventory: VIN validation failed - invalid format");
            return false;
        }
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: VIN validation passed for: " . $vin);
        return true;
    }
    
    /**
     * Calculate days on lot based on purchase date or date entered
     */
    protected function calculateDaysOnLot()
    {
        $startDate = null;
        
        // Use purchase_date if available, otherwise use date_entered
        if (!empty($this->purchase_date)) {
            $startDate = new DateTime($this->purchase_date);
        } elseif (!empty($this->date_entered)) {
            $startDate = new DateTime($this->date_entered);
        }
        
        if ($startDate) {
            $currentDate = new DateTime();
            $interval = $currentDate->diff($startDate);
            $this->days_on_lot = $interval->days;
            
            $GLOBALS['log']->debug("DM_VehiclesInventory: Calculated days on lot: " . $this->days_on_lot);
        } else {
            $this->days_on_lot = 0;
            $GLOBALS['log']->debug("DM_VehiclesInventory: No start date found, setting days on lot to 0");
        }
    }
    
    /**
     * Ensure JSON fields are properly formatted
     */
    protected function normalizeJSONFields()
    {
        // Normalize photos field
        if (empty($this->photos) || $this->photos === '') {
            $this->photos = '[]';
        } elseif (!$this->isValidJSON($this->photos)) {
            // If it's not valid JSON, try to make it an array
            $this->photos = json_encode(array($this->photos));
        }
        
        // Normalize features field
        if (empty($this->features) || $this->features === '') {
            $this->features = '[]';
        } elseif (!$this->isValidJSON($this->features)) {
            // If it's not valid JSON, try to make it an array
            $this->features = json_encode(array($this->features));
        }
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: JSON fields normalized");
    }
    
    /**
     * Check if a string is valid JSON
     * 
     * @param string $string - String to check
     * @return boolean - True if valid JSON, false otherwise
     */
    protected function isValidJSON($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    /**
     * Get photos as array
     * 
     * @return array - Array of photo URLs
     */
    public function getPhotosArray()
    {
        if (empty($this->photos)) {
            return array();
        }
        
        $photos = json_decode($this->photos, true);
        return is_array($photos) ? $photos : array();
    }
    
    /**
     * Get features as array
     * 
     * @return array - Array of vehicle features
     */
    public function getFeaturesArray()
    {
        if (empty($this->features)) {
            return array();
        }
        
        $features = json_decode($this->features, true);
        return is_array($features) ? $features : array();
    }
    
    /**
     * Add a photo URL to the vehicle
     * 
     * @param string $photoUrl - URL of the photo to add
     * @return boolean - Success status
     */
    public function addPhoto($photoUrl)
    {
        $photos = $this->getPhotosArray();
        
        if (!in_array($photoUrl, $photos)) {
            $photos[] = $photoUrl;
            $this->photos = json_encode($photos);
            
            $GLOBALS['log']->debug("DM_VehiclesInventory: Added photo URL: " . $photoUrl);
            return true;
        }
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: Photo URL already exists: " . $photoUrl);
        return false;
    }
    
    /**
     * Add a feature to the vehicle
     * 
     * @param string $feature - Feature to add
     * @return boolean - Success status
     */
    public function addFeature($feature)
    {
        $features = $this->getFeaturesArray();
        
        if (!in_array($feature, $features)) {
            $features[] = $feature;
            $this->features = json_encode($features);
            
            $GLOBALS['log']->debug("DM_VehiclesInventory: Added feature: " . $feature);
            return true;
        }
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: Feature already exists: " . $feature);
        return false;
    }
    
    /**
     * Check if vehicle is available for sale
     * 
     * @return boolean - True if available, false otherwise
     */
    public function isAvailable()
    {
        return $this->status === 'Available';
    }
    
    /**
     * Check if vehicle is sold
     * 
     * @return boolean - True if sold, false otherwise
     */
    public function isSold()
    {
        return $this->status === 'Sold';
    }
    
    /**
     * Mark vehicle as sold with sale price
     * 
     * @param float $salePrice - Final sale price
     * @return boolean - Success status
     */
    public function markAsSold($salePrice = null)
    {
        $this->status = 'Sold';
        
        if ($salePrice !== null) {
            $this->sale_price = $salePrice;
        }
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: Marked vehicle as sold. Sale price: " . $salePrice);
        
        return $this->save();
    }
    
    /**
     * Calculate profit margin if both purchase and sale prices are available
     * 
     * @return float|null - Profit margin or null if not calculable
     */
    public function calculateProfitMargin()
    {
        if (empty($this->purchase_price) || empty($this->sale_price)) {
            return null;
        }
        
        $profit = $this->sale_price - $this->purchase_price;
        $margin = ($profit / $this->purchase_price) * 100;
        
        $GLOBALS['log']->debug("DM_VehiclesInventory: Calculated profit margin: " . $margin . "%");
        
        return round($margin, 2);
    }
    
    /**
     * Get aging category based on days on lot
     * 
     * @return string - Aging category (Fresh, Aging, Stale)
     */
    public function getAgingCategory()
    {
        if ($this->days_on_lot <= 30) {
            return 'Fresh';
        } elseif ($this->days_on_lot <= 60) {
            return 'Aging';
        } else {
            return 'Stale';
        }
    }
    
    /**
     * Override the retrieve method to calculate days on lot on load
     * 
     * @param string $id - Record ID to retrieve (default -1)
     * @param boolean $encode - Whether to encode (default true)
     * @param boolean $deleted - Whether to include deleted records (default true)
     * @return boolean - Success status
     */
    public function retrieve($id = -1, $encode = true, $deleted = true)
    {
        $result = parent::retrieve($id, $encode, $deleted);
        
        if ($result) {
            // Recalculate days on lot when record is loaded
            $this->calculateDaysOnLot();
            $GLOBALS['log']->debug("DM_VehiclesInventory: Retrieved record and calculated days on lot: " . $this->days_on_lot);
        }
        
        return $result;
    }
} 