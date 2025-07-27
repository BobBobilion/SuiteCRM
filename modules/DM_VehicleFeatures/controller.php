<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * Controller for DM_VehicleFeatures module
 * 
 * Handles actions and routing for vehicle features
 */

require_once('include/MVC/Controller/SugarController.php');

class DM_VehicleFeaturesController extends SugarController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $GLOBALS['log']->debug('DM_VehicleFeaturesController: Constructor called');
    }
    
    /**
     * Pre-process actions
     */
    public function preProcess()
    {
        parent::preProcess();
        
        $GLOBALS['log']->debug('DM_VehicleFeaturesController: PreProcess called for action: ' . $this->action);
    }
    
    /**
     * Main action dispatcher
     */
    public function process()
    {
        $GLOBALS['log']->debug('DM_VehicleFeaturesController: Process called for action: ' . $this->action);
        
        parent::process();
    }
}
?> 