<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Inventory Language File (English)
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$mod_strings = array(
    // Module Names
    'LBL_MODULE_NAME' => 'Parts Inventory',
    'LBL_MODULE_TITLE' => 'Parts Inventory: Home',
    'LBL_MODULE_ID' => 'Parts Inventory',
    'LBL_SEARCH_FORM_TITLE' => 'Parts Inventory Search',
    'LBL_LIST_FORM_TITLE' => 'Parts Inventory List',
    'LBL_NEW_FORM_TITLE' => 'Create Parts Inventory Item',
    'LBL_HOMEPAGE_TITLE' => 'My Parts Inventory',
    'LNK_NEW_RECORD' => 'Create Parts Inventory Item',
    'LNK_LIST' => 'View Parts Inventory',
    'LNK_IMPORT_DM_PARTSINVENTORY' => 'Import Parts Inventory',

    // Standard Fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Name',
    'LBL_DATE_ENTERED' => 'Date Created',
    'LBL_DATE_MODIFIED' => 'Date Modified',
    'LBL_MODIFIED' => 'Modified By',
    'LBL_MODIFIED_ID' => 'Modified By Id',
    'LBL_MODIFIED_NAME' => 'Modified By Name',
    'LBL_CREATED' => 'Created By',
    'LBL_CREATED_ID' => 'Created By Id',
    'LBL_CREATED_NAME' => 'Created By Name',
    'LBL_ASSIGNED_TO_ID' => 'Assigned User Id',
    'LBL_ASSIGNED_TO_NAME' => 'Assigned to',
    'LBL_DELETED' => 'Deleted',

    // Parts Inventory Specific Fields
    'LBL_PART_NUMBER' => 'Part Number',
    'LBL_MANUFACTURER' => 'Manufacturer',
    'LBL_CATEGORY' => 'Category',
    'LBL_DESCRIPTION' => 'Description',
    'LBL_LOCATION' => 'Location',
    'LBL_QUANTITY_ON_HAND' => 'Quantity On Hand',
    'LBL_REORDER_POINT' => 'Reorder Point',
    'LBL_LAST_ORDERED_DATE' => 'Last Ordered Date',
    'LBL_COST' => 'Cost',
    'LBL_RETAIL_PRICE' => 'Retail Price',
    'LBL_NOTES' => 'Notes',

    // Relationship Fields
    'LBL_SUPPLIER_ID' => 'Supplier ID',
    'LBL_SUPPLIER_NAME' => 'Supplier',
    'LBL_SUPPLIER' => 'Supplier',

    // User Links
    'LBL_ASSIGNED_TO_USER' => 'Assigned to User',
    'LBL_MODIFIED_BY_USER' => 'Modified by User',
    'LBL_CREATED_BY_USER' => 'Created by User',

    // Panel Labels
    'LBL_PANEL_PART_INFO' => 'Part Information',
    'LBL_PANEL_INVENTORY' => 'Inventory Details',
    'LBL_PANEL_PRICING' => 'Pricing Information',
    'LBL_PANEL_SUPPLIER' => 'Supplier Information',

    // List View Labels
    'LBL_LIST_PART_NUMBER' => 'Part #',
    'LBL_LIST_DESCRIPTION' => 'Description',
    'LBL_LIST_MANUFACTURER' => 'Manufacturer',
    'LBL_LIST_CATEGORY' => 'Category',
    'LBL_LIST_QUANTITY_ON_HAND' => 'Qty',
    'LBL_LIST_RETAIL_PRICE' => 'Price',
    'LBL_LIST_SUPPLIER_NAME' => 'Supplier',

    // Search Labels
    'LBL_SEARCH_PART_NUMBER' => 'Part Number',
    'LBL_SEARCH_DESCRIPTION' => 'Description',
    'LBL_SEARCH_MANUFACTURER' => 'Manufacturer',
    'LBL_SEARCH_CATEGORY' => 'Category',

    // Status Labels
    'LBL_STOCK_STATUS' => 'Stock Status',
    'LBL_IN_STOCK' => 'In Stock',
    'LBL_LOW_STOCK' => 'Low Stock',
    'LBL_OUT_OF_STOCK' => 'Out of Stock',
    'LBL_NEEDS_REORDER' => 'Needs Reorder',

    // Subpanel Labels
    'LBL_SUBPANEL_PARTS_INVENTORY' => 'Parts Inventory',

    // Help Text
    'LBL_HELP_PART_NUMBER' => 'Manufacturer part number for identification',
    'LBL_HELP_REORDER_POINT' => 'Stock level that triggers reorder alert',
    'LBL_HELP_LOCATION' => 'Physical location in inventory (bin, shelf, etc.)',

    // Error Messages
    'ERR_DELETE_RECORD' => 'You must specify a record number to delete the parts inventory item.',
    'ERR_INVALID_PART_NUMBER' => 'Please enter a valid part number.',
    'ERR_NEGATIVE_QUANTITY' => 'Quantity cannot be negative.',

    // Tooltips
    'LBL_TOOLTIP_MARKUP' => 'Calculated markup percentage based on cost and retail price',
    'LBL_TOOLTIP_STOCK_LEVEL' => 'Current stock level - Red: Out of Stock, Yellow: Low Stock, Green: In Stock',

    // Actions
    'LBL_UPDATE_STOCK' => 'Update Stock',
    'LBL_REORDER_PARTS' => 'Reorder Parts',
    'LBL_VIEW_STOCK_REPORT' => 'View Stock Report',
    'LBL_LOW_STOCK_REPORT' => 'Low Stock Report',

    // Messages
    'MSG_STOCK_UPDATED' => 'Stock quantity has been updated successfully.',
    'MSG_LOW_STOCK_ALERT' => 'This part is below the reorder point.',
    'MSG_OUT_OF_STOCK' => 'This part is out of stock.',
);

// Note: Dropdown options are now defined in custom/include/language/en_us.lang.php
?>