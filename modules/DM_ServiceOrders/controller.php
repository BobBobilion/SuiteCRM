<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Controller
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

/**
 * DM_ServiceOrders Controller
 * 
 * Handles custom actions and views for the Service Orders module
 */
class DM_ServiceOrdersController extends SugarController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Pre-action hook for custom processing
     */
    protected function pre_action()
    {
        parent::pre_action();
        
        // Add custom JavaScript for service order functionality
        if (in_array($this->action, array('EditView', 'DetailView'))) {
            echo '<script type="text/javascript" src="modules/DM_ServiceOrders/js/DM_ServiceOrders.js"></script>';
        }
    }

    /**
     * Custom action for parts lookup
     */
    public function action_parts_lookup()
    {
        $this->view = 'parts_lookup';
    }

    /**
     * Custom action for vehicle lookup
     */
    public function action_vehicle_lookup()
    {
        $this->view = 'vehicle_lookup';
    }

    /**
     * Custom action for service history
     */
    public function action_service_history()
    {
        $this->view = 'service_history';
    }

    /**
     * Custom action for scheduling
     */
    public function action_scheduling()
    {
        $this->view = 'scheduling';
    }

    /**
     * AJAX action for calculating totals
     */
    public function action_calculate_totals()
    {
        if (!empty($_POST['labor_hours']) && !empty($_POST['labor_rate'])) {
            $labor_total = (float)$_POST['labor_hours'] * (float)$_POST['labor_rate'];
            $parts_total = !empty($_POST['parts_total']) ? (float)$_POST['parts_total'] : 0;
            $tax_amount = !empty($_POST['tax_amount']) ? (float)$_POST['tax_amount'] : 0;
            $total_amount = $labor_total + $parts_total + $tax_amount;

            $response = array(
                'labor_total' => $labor_total,
                'total_amount' => $total_amount,
                'success' => true
            );
        } else {
            $response = array(
                'success' => false,
                'error' => 'Missing required fields for calculation'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for generating service order number
     */
    public function action_generate_ro_number()
    {
        $serviceOrder = BeanFactory::newBean('DM_ServiceOrders');
        $ro_number = $serviceOrder->generateServiceOrderNumber();

        $response = array(
            'ro_number' => $ro_number,
            'success' => true
        );

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * Action for service summary report
     */
    public function action_service_summary_report()
    {
        $this->view = 'service_summary_report';
    }
}
?>