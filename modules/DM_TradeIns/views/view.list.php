<?php
/**
 * SuiteCRM Trade-In Manager - List View
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.list.php');

class DM_TradeInsViewList extends ViewList
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
     * Display the list view
     */
    public function display()
    {
        parent::display();
    }
}
?>