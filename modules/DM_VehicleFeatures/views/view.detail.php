<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * DM_VehicleFeatures DetailView
 * 
 * Custom DetailView for the DM_VehicleFeatures module to display
 * detailed vehicle feature information
 */

require_once('include/MVC/View/views/view.detail.php');

class DM_VehicleFeaturesViewDetail extends ViewDetail
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $GLOBALS['log']->debug('DM_VehicleFeaturesViewDetail: Constructor called');
    }

    /**
     * Pre-display setup
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        $GLOBALS['log']->debug('DM_VehicleFeaturesViewDetail: PreDisplay called');
        
        // Disable subpanels to avoid errors
        $this->options['show_subpanels'] = false;
    }

    /**
     * Display the detail view
     */
    public function display()
    {
        $GLOBALS['log']->debug('DM_VehicleFeaturesViewDetail: Display called');
        
        // Disable subpanels to avoid errors
        $this->dv->showSubPanels = false;
        
        parent::display();
    }
}
?> 