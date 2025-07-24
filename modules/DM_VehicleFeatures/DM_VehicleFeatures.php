<?php
/**
 * SuiteCRM Vehicle Features Module
 * 
 * This module manages vehicle features catalog and mappings.
 * It provides a standardized way to define and categorize vehicle features.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('data/SugarBean.php');
require_once('include/SubPanel/SubPanelDefinitions.php');

/**
 * DM_VehicleFeatures class
 * 
 * Handles vehicle features catalog management including:
 * - Feature definitions and categories
 * - Standard feature lists for different vehicle types
 * - Feature descriptions and metadata
 */
class DM_VehicleFeatures extends SugarBean
{
    /**
     * Module properties
     */
    public $module_name = 'DM_VehicleFeatures';
    public $module_dir = 'DM_VehicleFeatures';
    public $object_name = 'DM_VehicleFeatures';
    public $table_name = 'dm_vehiclefeatures';
    public $importable = false;
    public $disable_row_level_security = true;
    
    /**
     * Field properties
     */
    public $id;
    public $name;
    public $description;
    public $category;
    public $feature_type;
    public $is_standard;
    public $sort_order;
    
    // Standard fields
    public $date_entered;
    public $date_modified;
    public $assigned_user_id;
    public $assigned_user_name;
    public $created_by;
    public $created_by_name;
    public $modified_user_id;
    public $modified_by_name;
    public $deleted;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Set default values
        $this->disable_row_level_security = true;
    }

    /**
     * Override bean_implements to specify what this bean implements
     */
    public function bean_implements($interface)
    {
        switch($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * Get feature categories
     * 
     * @return array List of available feature categories
     */
    public function getFeatureCategories()
    {
        return array(
            'safety' => 'Safety Features',
            'performance' => 'Performance Features',
            'comfort' => 'Comfort & Convenience',
            'technology' => 'Technology Features',
            'exterior' => 'Exterior Features',
            'interior' => 'Interior Features',
            'entertainment' => 'Entertainment System',
            'mechanical' => 'Mechanical Features'
        );
    }

    /**
     * Get feature types
     * 
     * @return array List of feature types
     */
    public function getFeatureTypes()
    {
        return array(
            'standard' => 'Standard Equipment',
            'optional' => 'Optional Equipment',
            'aftermarket' => 'Aftermarket Addition',
            'package' => 'Option Package'
        );
    }

    /**
     * Get standard features for a vehicle type
     * 
     * @param string $vehicleType The type of vehicle (sedan, suv, truck, etc.)
     * @return array Array of standard features
     */
    public function getStandardFeatures($vehicleType = '')
    {
        $query = "SELECT * FROM {$this->table_name} WHERE deleted = 0 AND is_standard = 1";
        
        if (!empty($vehicleType)) {
            $query .= " AND (vehicle_type = '{$this->db->quote($vehicleType)}' OR vehicle_type IS NULL OR vehicle_type = '')";
        }
        
        $query .= " ORDER BY category, sort_order, name";
        
        $result = $this->db->query($query);
        $features = array();
        
        while ($row = $this->db->fetchByAssoc($result)) {
            $features[] = $row;
        }
        
        return $features;
    }

    /**
     * Get features by category
     * 
     * @param string $category The feature category
     * @return array Array of features in the category
     */
    public function getFeaturesByCategory($category)
    {
        $query = "SELECT * FROM {$this->table_name} 
                  WHERE deleted = 0 AND category = '{$this->db->quote($category)}'
                  ORDER BY sort_order, name";
        
        $result = $this->db->query($query);
        $features = array();
        
        while ($row = $this->db->fetchByAssoc($result)) {
            $features[] = $row;
        }
        
        return $features;
    }

    /**
     * Save bean with additional validation
     */
    public function save($check_notify = false)
    {
        // Set default sort order if not provided
        if (empty($this->sort_order)) {
            $this->sort_order = 999;
        }
        
        // Ensure category is set
        if (empty($this->category)) {
            $this->category = 'general';
        }
        
        // Set default feature type
        if (empty($this->feature_type)) {
            $this->feature_type = 'standard';
        }
        
        return parent::save($check_notify);
    }

    /**
     * Override create_export_query to customize export functionality
     */
    public function create_export_query($order_by, $where)
    {
        $query = parent::create_export_query($order_by, $where);
        return $query;
    }

    /**
     * Override fill_in_additional_list_fields
     */
    public function fill_in_additional_list_fields()
    {
        parent::fill_in_additional_list_fields();
        
        // Add any additional list field processing here
        if (!empty($this->category)) {
            $categories = $this->getFeatureCategories();
            $this->category_display = isset($categories[$this->category]) ? $categories[$this->category] : $this->category;
        }
        
        if (!empty($this->feature_type)) {
            $types = $this->getFeatureTypes();
            $this->feature_type_display = isset($types[$this->feature_type]) ? $types[$this->feature_type] : $this->feature_type;
        }
    }

    /**
     * Override fill_in_additional_detail_fields
     */
    public function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();
        
        // Add category and type display names
        $this->fill_in_additional_list_fields();
    }
}
?> 