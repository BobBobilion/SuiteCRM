<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * DM_VehicleFeatures EditView
 * 
 * Custom EditView for the DM_VehicleFeatures module to edit
 * vehicle feature information
 */

require_once('include/MVC/View/views/view.edit.php');

class DM_VehicleFeaturesViewEdit extends ViewEdit
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $GLOBALS['log']->debug('DM_VehicleFeaturesViewEdit: Constructor called');
    }

    /**
     * Pre-display setup
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        $GLOBALS['log']->debug('DM_VehicleFeaturesViewEdit: PreDisplay called');
    }

    /**
     * Display the edit view
     */
    public function display()
    {
        $GLOBALS['log']->debug('DM_VehicleFeaturesViewEdit: Display called');
        
        parent::display();
    }
}
?> 