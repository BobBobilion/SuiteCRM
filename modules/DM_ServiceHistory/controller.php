<?php
/**
 * SuiteCRM Service & Parts Hub - Service History Controller
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

/**
 * DM_ServiceHistory Controller
 * 
 * Handles custom actions and views for the Service History module
 */
class DM_ServiceHistoryController extends SugarController
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
        
        // Add custom JavaScript for service history functionality
        if (in_array($this->action, array('EditView', 'DetailView'))) {
            echo '<script type="text/javascript" src="modules/DM_ServiceHistory/js/DM_ServiceHistory.js"></script>';
        }
    }

    /**
     * Custom action for service summary report
     */
    public function action_service_summary_report()
    {
        $this->view = 'service_summary_report';
    }

    /**
     * Custom action for technician performance report
     */
    public function action_technician_performance_report()
    {
        $this->view = 'technician_performance_report';
    }

    /**
     * Custom action for vehicle history report
     */
    public function action_vehicle_history_report()
    {
        $this->view = 'vehicle_history_report';
    }

    /**
     * AJAX action for getting vehicle service history
     */
    public function action_get_vehicle_history()
    {
        if (!empty($_POST['vehicle_id'])) {
            $vehicle_id = $_POST['vehicle_id'];
            $limit = !empty($_POST['limit']) ? (int)$_POST['limit'] : null;
            
            $history = DM_ServiceHistory::getVehicleServiceHistory($vehicle_id, $limit);

            $response = array(
                'success' => true,
                'history' => $history,
                'count' => count($history)
            );
        } else {
            $response = array(
                'success' => false,
                'error' => 'Vehicle ID not specified'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for getting service history by date range
     */
    public function action_get_history_by_date_range()
    {
        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $vehicle_id = !empty($_POST['vehicle_id']) ? $_POST['vehicle_id'] : null;
            
            $history = DM_ServiceHistory::getServiceHistoryByDateRange($start_date, $end_date, $vehicle_id);

            $response = array(
                'success' => true,
                'history' => $history,
                'count' => count($history),
                'date_range' => array(
                    'start' => $start_date,
                    'end' => $end_date
                )
            );
        } else {
            $response = array(
                'success' => false,
                'error' => 'Start date and end date are required'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for getting technician performance data
     */
    public function action_get_technician_performance()
    {
        if (!empty($_POST['technician_id'])) {
            $technician_id = $_POST['technician_id'];
            $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            
            $performance = DM_ServiceHistory::getTechnicianPerformance($technician_id, $start_date, $end_date);

            $response = array(
                'success' => true,
                'performance' => $performance,
                'technician_id' => $technician_id
            );
        } else {
            $response = array(
                'success' => false,
                'error' => 'Technician ID not specified'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for getting common services
     */
    public function action_get_common_services()
    {
        $limit = !empty($_POST['limit']) ? (int)$_POST['limit'] : 10;
        
        $services = DM_ServiceHistory::getCommonServices($limit);

        $response = array(
            'success' => true,
            'services' => $services,
            'count' => count($services)
        );

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for adding parts to service history
     */
    public function action_add_part_to_history()
    {
        if (!empty($_POST['history_id']) && !empty($_POST['part_id'])) {
            $serviceHistory = BeanFactory::getBean('DM_ServiceHistory', $_POST['history_id']);
            
            if ($serviceHistory && !$serviceHistory->deleted) {
                $part_id = $_POST['part_id'];
                $part_number = !empty($_POST['part_number']) ? $_POST['part_number'] : '';
                $description = !empty($_POST['description']) ? $_POST['description'] : '';
                $quantity = !empty($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                $cost = !empty($_POST['cost']) ? (float)$_POST['cost'] : 0;
                
                $serviceHistory->addPartUsed($part_id, $part_number, $description, $quantity, $cost);
                $serviceHistory->save();
                
                $response = array(
                    'success' => true,
                    'message' => 'Part added to service history successfully',
                    'parts_used' => $serviceHistory->getPartsUsedList()
                );
            } else {
                $response = array(
                    'success' => false,
                    'error' => 'Service history record not found'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'error' => 'Missing required parameters'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }

    /**
     * AJAX action for creating service history from service order
     */
    public function action_create_from_service_order()
    {
        if (!empty($_POST['service_order_id'])) {
            $serviceOrder = BeanFactory::getBean('DM_ServiceOrders', $_POST['service_order_id']);
            
            if ($serviceOrder && !$serviceOrder->deleted) {
                $serviceHistory = BeanFactory::newBean('DM_ServiceHistory');
                
                $serviceHistory->name = 'Service for RO #' . $serviceOrder->service_order_number;
                $serviceHistory->vehicle_id = $serviceOrder->vehicle_id;
                $serviceHistory->service_order_id = $serviceOrder->id;
                $serviceHistory->service_date = date('Y-m-d');
                $serviceHistory->mileage = $serviceOrder->mileage_in;
                $serviceHistory->service_type = $serviceOrder->service_type;
                $serviceHistory->services_performed = $serviceOrder->work_performed;
                $serviceHistory->labor_hours = $serviceOrder->labor_hours;
                $serviceHistory->total_cost = $serviceOrder->total_amount;
                $serviceHistory->technician_id = $serviceOrder->technician_id;
                $serviceHistory->notes = $serviceOrder->notes;
                $serviceHistory->assigned_user_id = $serviceOrder->assigned_user_id;
                
                $serviceHistory->save();
                
                $response = array(
                    'success' => true,
                    'history_id' => $serviceHistory->id,
                    'message' => 'Service history created successfully'
                );
            } else {
                $response = array(
                    'success' => false,
                    'error' => 'Service order not found'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'error' => 'Service order ID not specified'
            );
        }

        echo json_encode($response);
        sugar_cleanup(true);
    }
}
?>