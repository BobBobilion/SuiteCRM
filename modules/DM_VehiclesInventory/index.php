<?php
/**
 * SuiteCRM Vehicle Inventory System - Index Action Handler
 *
 * This file handles the default index action and redirects to the ListView
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

// Log the index action
$GLOBALS['log']->debug("DM_VehiclesInventory: Index action called, redirecting to ListView");

// Redirect to ListView action
header("Location: index.php?module=DM_VehiclesInventory&action=ListView");
exit; 