<?php
/**
 * SuiteCRM Trade-In Manager - Detail View
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.detail.php');

class DM_TradeInsViewDetail extends ViewDetail
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Pre-display setup
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Add custom JavaScript for Trade-Ins
        echo '<script type="text/javascript" src="modules/DM_TradeIns/js/DM_TradeIns.js"></script>';
    }

    /**
     * Display the detail view
     */
    public function display()
    {
        parent::display();
    }
}
?>