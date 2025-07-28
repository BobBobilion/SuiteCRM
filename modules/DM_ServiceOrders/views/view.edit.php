<?php
/**
 * SuiteCRM Service Orders - Edit View
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.edit.php');

class DM_ServiceOrdersViewEdit extends ViewEdit
{
    public function __construct()
    {
        parent::__construct();
    }

    public function preDisplay()
    {
        parent::preDisplay();
        echo '<script type="text/javascript" src="modules/DM_ServiceOrders/js/DM_ServiceOrders.js"></script>';
    }

    public function display()
    {
        parent::display();
    }
}
?>