<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * SuiteCRM AutoInventory Controller
 * 
 * This controller handles actions for the AutoInventory module including
 * proper save redirections and custom actions.
 */

require_once('include/MVC/Controller/SugarController.php');

class AutoInventoryController extends SugarController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $GLOBALS['log']->debug('AutoInventoryController: Constructor called for action: ' . $this->action);
    }

    /**
     * Handle the save action with proper redirection
     */
    public function action_save()
    {
        $GLOBALS['log']->debug('AutoInventoryController: Save action called');
        
        // Get the bean
        $bean = BeanFactory::getBean('AutoInventory');
        
        if (!$bean) {
            $GLOBALS['log']->error('AutoInventoryController: Could not create AutoInventory bean');
            $this->redirectToError('Could not create AutoInventory record');
            return;
        }

        // Populate the bean from the request
        $bean = $this->populateBeanFromRequest($bean);
        
        try {
            // Save the bean
            $bean->save();
            
            $GLOBALS['log']->debug('AutoInventoryController: Successfully saved AutoInventory record with ID: ' . $bean->id);
            
            // Handle redirection based on the save action
            $this->handleSaveRedirection($bean);
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('AutoInventoryController: Error saving AutoInventory record: ' . $e->getMessage());
            $this->redirectToError('Error saving record: ' . $e->getMessage());
        }
    }

    /**
     * Populate bean from request data
     */
    private function populateBeanFromRequest($bean)
    {
        // Standard fields
        if (isset($_POST['name'])) $bean->name = $_POST['name'];
        if (isset($_POST['vin_number'])) $bean->vin_number = $_POST['vin_number'];
        if (isset($_POST['stock_id'])) $bean->stock_id = $_POST['stock_id'];
        if (isset($_POST['model_year'])) $bean->model_year = $_POST['model_year'];
        if (isset($_POST['manufacturer'])) $bean->manufacturer = $_POST['manufacturer'];
        if (isset($_POST['vehicle_model'])) $bean->vehicle_model = $_POST['vehicle_model'];
        if (isset($_POST['trim_level'])) $bean->trim_level = $_POST['trim_level'];
        if (isset($_POST['body_type'])) $bean->body_type = $_POST['body_type'];
        if (isset($_POST['paint_color'])) $bean->paint_color = $_POST['paint_color'];
        if (isset($_POST['interior_color'])) $bean->interior_color = $_POST['interior_color'];
        if (isset($_POST['odometer'])) $bean->odometer = $_POST['odometer'];
        if (isset($_POST['engine_info'])) $bean->engine_info = $_POST['engine_info'];
        if (isset($_POST['transmission_type'])) $bean->transmission_type = $_POST['transmission_type'];
        if (isset($_POST['drive_type'])) $bean->drive_type = $_POST['drive_type'];
        if (isset($_POST['fuel_system'])) $bean->fuel_system = $_POST['fuel_system'];
        if (isset($_POST['inventory_status'])) $bean->inventory_status = $_POST['inventory_status'];
        if (isset($_POST['vehicle_condition'])) $bean->vehicle_condition = $_POST['vehicle_condition'];
        if (isset($_POST['lot_position'])) $bean->lot_position = $_POST['lot_position'];
        if (isset($_POST['acquisition_date'])) $bean->acquisition_date = $_POST['acquisition_date'];
        if (isset($_POST['cost_basis'])) $bean->cost_basis = $_POST['cost_basis'];
        if (isset($_POST['asking_price'])) $bean->asking_price = $_POST['asking_price'];
        if (isset($_POST['final_price'])) $bean->final_price = $_POST['final_price'];
        if (isset($_POST['market_valuation'])) $bean->market_valuation = $_POST['market_valuation'];
        if (isset($_POST['acquisition_source'])) $bean->acquisition_source = $_POST['acquisition_source'];
        if (isset($_POST['vehicle_features'])) $bean->vehicle_features = $_POST['vehicle_features'];
        if (isset($_POST['photo_gallery'])) $bean->photo_gallery = $_POST['photo_gallery'];
        if (isset($_POST['description'])) $bean->description = $_POST['description'];
        if (isset($_POST['assigned_user_id'])) $bean->assigned_user_id = $_POST['assigned_user_id'];
        
        // Set the ID if this is an update
        if (isset($_POST['record']) && !empty($_POST['record'])) {
            $bean->id = $_POST['record'];
        }
        
        $GLOBALS['log']->debug('AutoInventoryController: Populated bean with data from request');
        
        return $bean;
    }

    /**
     * Handle redirection after save
     */
    private function handleSaveRedirection($bean)
    {
        // Determine where to redirect based on the save button clicked
        $saveType = isset($_POST['_save']) ? 'save' : (isset($_POST['save_and_continue']) ? 'continue' : 'save');
        
        $GLOBALS['log']->debug('AutoInventoryController: Save type: ' . $saveType);
        
        // Build redirect URL
        $redirectUrl = '';
        
        if ($saveType === 'continue') {
            // Save and Continue - redirect to edit view
            $redirectUrl = "index.php?module=AutoInventory&action=EditView&record=" . $bean->id;
        } else {
            // Regular save - redirect to detail view
            $redirectUrl = "index.php?module=AutoInventory&action=DetailView&record=" . $bean->id;
        }
        
        // Check for return module/action (for when coming from other modules)
        if (isset($_POST['return_module']) && isset($_POST['return_action'])) {
            $returnModule = $_POST['return_module'];
            $returnAction = $_POST['return_action'];
            
            if ($returnModule === 'AutoInventory' && $returnAction === 'ListView') {
                $redirectUrl = "index.php?module=AutoInventory&action=ListView";
            }
        }
        
        $GLOBALS['log']->debug('AutoInventoryController: Redirecting to: ' . $redirectUrl);
        
        // Perform the redirect
        $this->redirect($redirectUrl);
    }

    /**
     * Redirect to an error page
     */
    protected function redirectToError($message)
    {
        $errorUrl = "index.php?module=AutoInventory&action=ListView&error=" . urlencode($message);
        $this->redirect($errorUrl);
    }

    /**
     * Perform the actual redirect
     */
    protected function redirect($url = '')
    {
        if (empty($url)) {
            $url = "index.php?module=AutoInventory&action=ListView";
        }
        
        if (headers_sent()) {
            echo '<script>window.location.href = "' . $url . '";</script>';
        } else {
            header('Location: ' . $url);
        }
        exit();
    }

    /**
     * Pre-action hook
     */
    public function pre_action()
    {
        parent::pre_action();
        
        $GLOBALS['log']->debug('AutoInventoryController: Pre-action for: ' . $this->action);
    }

    /**
     * Post-action hook  
     */
    public function post_action()
    {
        parent::post_action();
        
        $GLOBALS['log']->debug('AutoInventoryController: Post-action for: ' . $this->action);
    }
}
?> 