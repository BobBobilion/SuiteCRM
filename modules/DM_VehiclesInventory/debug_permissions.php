<?php
/**
 * Comprehensive Permission Debug Script
 * 
 * Access via: http://your-suitecrm-url/modules/DM_VehiclesInventory/debug_permissions.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__) . '/../..');
require_once('include/entryPoint.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Permission Debug Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Vehicle Inventory Permission Debug Report</h1>
    <pre>
<?php

global $current_user, $moduleList, $beanList, $beanFiles;

echo "=== CURRENT USER INFO ===\n";
if ($current_user) {
    echo "User ID: " . $current_user->id . "\n";
    echo "Username: " . $current_user->user_name . "\n";
    echo "Full Name: " . $current_user->full_name . "\n";
    echo "Is Admin: " . ($current_user->isAdmin() ? "YES" : "NO") . "\n";
    echo "Status: " . $current_user->status . "\n";
    echo "Employee Status: " . $current_user->employee_status . "\n";
} else {
    echo "<span class='error'>NO CURRENT USER!</span>\n";
}

echo "\n=== MODULE REGISTRATION CHECK ===\n";
echo "DM_VehiclesInventory in moduleList: " . (in_array('DM_VehiclesInventory', $moduleList) ? "YES" : "NO") . "\n";
echo "DM_VehiclesInventory in beanList: " . (isset($beanList['DM_VehiclesInventory']) ? "YES" : "NO") . "\n";
echo "DM_VehiclesInventory in beanFiles: " . (isset($beanFiles['DM_VehiclesInventory']) ? "YES" : "NO") . "\n";

echo "\nDM_VehicleFeatures in moduleList: " . (in_array('DM_VehicleFeatures', $moduleList) ? "YES" : "NO") . "\n";
echo "DM_VehicleFeatures in beanList: " . (isset($beanList['DM_VehicleFeatures']) ? "YES" : "NO") . "\n";
echo "DM_VehicleFeatures in beanFiles: " . (isset($beanFiles['DM_VehicleFeatures']) ? "YES" : "NO") . "\n";

echo "\n=== DATABASE ACL ACTIONS CHECK ===\n";
$modules_to_check = array('DM_VehiclesInventory', 'DM_VehicleFeatures');

foreach ($modules_to_check as $module) {
    echo "\n--- $module ---\n";
    $query = "SELECT name, aclaccess, acltype FROM acl_actions WHERE category = '$module' AND deleted = 0 ORDER BY name";
    $result = $GLOBALS['db']->query($query);
    
    $actions_found = 0;
    echo "ACL Actions in database:\n";
    while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
        $actions_found++;
        $access_level = '';
        switch($row['aclaccess']) {
            case 75: $access_level = 'Owner'; break;
            case 90: $access_level = 'All'; break;
            case 89: $access_level = 'Not Set'; break;
            case -98: $access_level = 'None'; break;
            default: $access_level = $row['aclaccess'];
        }
        echo "  - {$row['name']}: {$access_level} ({$row['aclaccess']})\n";
    }
    
    if ($actions_found == 0) {
        echo "<span class='error'>  NO ACL ACTIONS FOUND!</span>\n";
    }
}

echo "\n=== CURRENT USER ROLES ===\n";
if ($current_user) {
    $query = "SELECT r.id, r.name, r.description 
              FROM acl_roles r 
              JOIN acl_roles_users ru ON r.id = ru.role_id 
              WHERE ru.user_id = '{$current_user->id}' AND r.deleted = 0 AND ru.deleted = 0";
    $result = $GLOBALS['db']->query($query);
    
    $roles_found = 0;
    echo "User roles:\n";
    while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
        $roles_found++;
        echo "  - {$row['name']} (ID: {$row['id']})\n";
        
        // Check role permissions for our modules
        foreach ($modules_to_check as $module) {
            echo "    $module permissions:\n";
            $perm_query = "SELECT ra.name, ra.aclaccess 
                          FROM acl_roles_actions ra 
                          WHERE ra.role_id = '{$row['id']}' 
                          AND ra.category = '$module' 
                          AND ra.deleted = 0
                          ORDER BY ra.name";
            $perm_result = $GLOBALS['db']->query($perm_query);
            
            while ($perm_row = $GLOBALS['db']->fetchByAssoc($perm_result)) {
                $access_level = '';
                switch($perm_row['aclaccess']) {
                    case 75: $access_level = 'Owner'; break;
                    case 90: $access_level = 'All'; break;
                    case 89: $access_level = 'Not Set'; break;
                    case -98: $access_level = 'None'; break;
                    default: $access_level = $perm_row['aclaccess'];
                }
                echo "      {$perm_row['name']}: {$access_level}\n";
            }
        }
    }
    
    if ($roles_found == 0) {
        echo "<span class='error'>USER HAS NO ROLES ASSIGNED!</span>\n";
    }
}

echo "\n=== ACL CONTROLLER TEST ===\n";
require_once('modules/ACLController/ACLController.php');

foreach ($modules_to_check as $module) {
    echo "\n--- Testing $module ---\n";
    $permissions = array('access', 'view', 'list', 'edit', 'delete');
    
    foreach ($permissions as $perm) {
        $hasAccess = ACLController::checkAccess($module, $perm, true);
        $result = $hasAccess ? "<span class='success'>GRANTED</span>" : "<span class='error'>DENIED</span>";
        echo "  $perm: $result\n";
    }
}

echo "\n=== TAB CONTROLLER CHECK ===\n";
require_once('modules/MySettings/TabController.php');
$tabController = new TabController();
$tabs = $tabController->get_tabs($current_user);

echo "Available tabs for user:\n";
foreach ($tabs[0] as $tab) {
    if (strpos($tab, 'DM_') !== false) {
        echo "  - <span class='success'>$tab</span>\n";
    }
}

echo "\nHidden tabs:\n";
if (isset($tabs[1])) {
    foreach ($tabs[1] as $tab) {
        if (strpos($tab, 'DM_') !== false) {
            echo "  - <span class='warning'>$tab (HIDDEN)</span>\n";
        }
    }
}

echo "\n=== DIRECT URL TEST ===\n";
echo "Try these URLs directly:\n";
echo "  - index.php?module=DM_VehiclesInventory&action=index\n";
echo "  - index.php?module=DM_VehiclesInventory&action=ListView\n";
echo "  - index.php?module=DM_VehiclesInventory&action=listview\n";

echo "\n=== CACHE STATUS ===\n";
$cache_dirs = array(
    'cache/modules/DM_VehiclesInventory',
    'cache/modules/DM_VehicleFeatures', 
    'cache/modules/ACLActions',
    'cache/modules/ACLRoles'
);

foreach ($cache_dirs as $dir) {
    echo "$dir: " . (is_dir($dir) ? "EXISTS" : "NOT FOUND") . "\n";
}

?>
    </pre>
    
    <h2>Quick Fixes</h2>
    <p><a href="force_fix_permissions.php" style="background: red; color: white; padding: 10px; text-decoration: none;">🔧 Force Fix Permissions (Nuclear Option)</a></p>
    <p><a href="../../index.php?module=Administration&action=repair" style="background: blue; color: white; padding: 10px; text-decoration: none;">🔨 Quick Repair & Rebuild</a></p>
    <p><a href="../../index.php?module=DM_VehiclesInventory&action=index" style="background: green; color: white; padding: 10px; text-decoration: none;">📋 Try Vehicle Inventory</a></p>
</body>
</html> 