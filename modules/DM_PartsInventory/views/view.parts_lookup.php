<?php
/**
 * SuiteCRM Service & Parts Hub - Parts Lookup View
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_PartsInventoryViewParts_lookup extends SugarView
{
    /**
     * Display the parts lookup popup
     */
    public function display()
    {
        global $mod_strings, $app_strings;
        
        $this->ss->assign('MOD', $mod_strings);
        $this->ss->assign('APP', $app_strings);
        
        // Get search criteria if provided
        $search_criteria = array();
        if (!empty($_REQUEST['part_number'])) {
            $search_criteria['part_number'] = $_REQUEST['part_number'];
        }
        if (!empty($_REQUEST['description'])) {
            $search_criteria['description'] = $_REQUEST['description'];
        }
        if (!empty($_REQUEST['manufacturer'])) {
            $search_criteria['manufacturer'] = $_REQUEST['manufacturer'];
        }
        if (!empty($_REQUEST['category'])) {
            $search_criteria['category'] = $_REQUEST['category'];
        }
        
        // Search for parts if criteria provided
        $parts = array();
        if (!empty($search_criteria)) {
            $parts = DM_PartsInventory::searchParts($search_criteria);
        } else {
            // Default: get recent parts with stock
            $query = "SELECT id, part_number, description, manufacturer, category, quantity_on_hand, retail_price 
                      FROM dm_partsinventory 
                      WHERE deleted = 0 AND quantity_on_hand > 0 
                      ORDER BY date_modified DESC 
                      LIMIT 50";
            
            $result = $GLOBALS['db']->query($query);
            while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
                $parts[] = $row;
            }
        }
        
        $this->ss->assign('parts', $parts);
        $this->ss->assign('search_criteria', $search_criteria);
        
        // Get categories for dropdown
        global $app_list_strings;
        $categories = $app_list_strings['parts_category_list'];
        $this->ss->assign('categories', $categories);
        
        echo $this->ss->fetch('modules/DM_PartsInventory/tpls/parts_lookup.tpl');
    }
}