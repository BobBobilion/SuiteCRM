<?php
/**
 * SuiteCRM F&I Deal Center - Controller
 * 
 * This controller handles custom actions for the F&I Deal Center module
 * including reports, analytics, product selection, and other specialized views.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/Controller/SugarController.php');

class DM_FIDealsController extends SugarController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Controller initialized");
    }

    /**
     * Pre-action processing
     */
    public function pre_action()
    {
        parent::pre_action();
        
        // Check if user has access to this module
        if (!ACLController::checkAccess('DM_FIDeals', 'view', true)) {
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
    }

    /**
     * Handle reports action
     */
    public function action_reports()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Reports action called");
        
        // Set up the view
        $this->view = 'reports';
        $this->view_object_map['reports'] = 'modules/DM_FIDeals/views/view.reports.php';
    }

    /**
     * Handle analytics action  
     */
    public function action_analytics()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Analytics action called");
        
        // Set up the view
        $this->view = 'analytics';
        $this->view_object_map['analytics'] = 'modules/DM_FIDeals/views/view.analytics.php';
    }

    /**
     * Handle product selection action
     */
    public function action_product_selection()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Product selection action called");
        
        // Set up the view
        $this->view = 'product_selection';
        $this->view_object_map['product_selection'] = 'modules/DM_FIDeals/views/view.product_selection.php';
    }

    /**
     * Handle approval tracking action
     */
    public function action_approval_tracking()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Approval tracking action called");
        
        // Set up the view
        $this->view = 'approval_tracking';
        $this->view_object_map['approval_tracking'] = 'modules/DM_FIDeals/views/view.approval_tracking.php';
    }

    /**
     * Handle lender submission action
     */
    public function action_submission()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Lender submission action called");
        
        // Set up the view
        $this->view = 'submission';
        $this->view_object_map['submission'] = 'modules/DM_FIDeals/views/view.submission.php';
    }

    /**
     * Handle calculator action
     */
    public function action_calculator()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Calculator action called");
        
        // Set up the view for standalone calculator
        $this->view = 'calculator';
        $this->view_object_map['calculator'] = 'modules/DM_FIDeals/views/view.calculator.php';
    }

    /**
     * Handle payment scenarios AJAX action
     */
    public function action_payment_scenarios()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Payment scenarios AJAX action called");
        
        // Get parameters from request
        $sales_price = $_REQUEST['sales_price'] ?? 0;
        $down_payment = $_REQUEST['down_payment'] ?? 0;
        $trade_allowance = $_REQUEST['trade_allowance'] ?? 0;
        $interest_rate = $_REQUEST['interest_rate'] ?? 5.5;
        
        // Calculate amount financed
        $amount_financed = $sales_price - $down_payment - $trade_allowance;
        
        // Generate scenarios for different terms
        $terms = [36, 48, 60, 72, 84];
        $scenarios = array();
        
        foreach ($terms as $term) {
            $monthly_payment = $this->calculatePayment($amount_financed, $interest_rate, $term);
            $total_payments = $monthly_payment * $term;
            $finance_charge = $total_payments - $amount_financed;
            
            $scenarios[] = array(
                'term' => $term,
                'monthly_payment' => number_format($monthly_payment, 2),
                'total_payments' => number_format($total_payments, 2),
                'finance_charge' => number_format($finance_charge, 2)
            );
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(array('scenarios' => $scenarios));
        exit();
    }

    /**
     * Calculate loan payment using standard formula
     */
    private function calculatePayment($principal, $annual_rate, $term_months)
    {
        if ($principal <= 0 || $annual_rate <= 0 || $term_months <= 0) {
            return 0;
        }
        
        $monthly_rate = ($annual_rate / 100) / 12;
        $payment = $principal * ($monthly_rate * pow(1 + $monthly_rate, $term_months)) / 
                   (pow(1 + $monthly_rate, $term_months) - 1);
        
        return $payment;
    }

    /**
     * Handle F&I product data AJAX action
     */
    public function action_product_data()
    {
        $GLOBALS['log']->debug("F&I Deal Center: Product data AJAX action called");
        
        // Load F&I product recommendations based on deal characteristics
        require_once('modules/DM_FIDeals/FIProduct.php');
        
        $vehicle_price = $_REQUEST['vehicle_price'] ?? 0;
        $finance_method = $_REQUEST['finance_method'] ?? 'Finance';
        
        $products = FIProduct::getRecommendedProducts($vehicle_price, $finance_method);
        
        header('Content-Type: application/json');
        echo json_encode(array('products' => $products));
        exit();
    }
}
?> 