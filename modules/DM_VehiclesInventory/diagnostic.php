<?php
/**
 * Vehicle Inventory Module Diagnostic Script
 * 
 * This script helps diagnose issues with the DM_VehiclesInventory module
 * Run this when experiencing "no action by that name" errors
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h2>Vehicle Inventory Module Diagnostic Report</h2>";
echo "<pre>";

// 1. Check module registration
echo "=== MODULE REGISTRATION CHECK ===\n";
global $moduleList, $beanList, $beanFiles;

echo "Module in moduleList: " . (in_array('DM_VehiclesInventory', $moduleList) ? "YES" : "NO") . "\n";
echo "Module in beanList: " . (isset($beanList['DM_VehiclesInventory']) ? "YES" : "NO") . "\n";
echo "Module in beanFiles: " . (isset($beanFiles['DM_VehiclesInventory']) ? "YES" : "NO") . "\n";

// 2. Check controller file
echo "\n=== CONTROLLER FILE CHECK ===\n";
$controllerPath = 'modules/DM_VehiclesInventory/controller.php';
if (file_exists($controllerPath)) {
    echo "Controller file exists: YES\n";
    echo "Controller file size: " . filesize($controllerPath) . " bytes\n";
    echo "Controller file permissions: " . substr(sprintf('%o', fileperms($controllerPath)), -4) . "\n";
    
    // Check if controller is loadable
    try {
        require_once($controllerPath);
        if (class_exists('DM_VehiclesInventoryController')) {
            echo "Controller class exists: YES\n";
            
            // Check action mappings
            $controller = new DM_VehiclesInventoryController();
            $reflection = new ReflectionClass($controller);
            $remapProp = $reflection->getProperty('action_remap');
            $remapProp->setAccessible(true);
            $actionRemap = $remapProp->getValue($controller);
            
            echo "Action remap contains 'index': " . (isset($actionRemap['index']) ? "YES (maps to: " . $actionRemap['index'] . ")" : "NO") . "\n";
        } else {
            echo "Controller class exists: NO\n";
        }
    } catch (Exception $e) {
        echo "Error loading controller: " . $e->getMessage() . "\n";
    }
} else {
    echo "Controller file exists: NO\n";
}

// 3. Check bean file
echo "\n=== BEAN FILE CHECK ===\n";
$beanPath = 'modules/DM_VehiclesInventory/DM_VehiclesInventory.php';
if (file_exists($beanPath)) {
    echo "Bean file exists: YES\n";
    echo "Bean file size: " . filesize($beanPath) . " bytes\n";
} else {
    echo "Bean file exists: NO\n";
}

// 4. Check cache status
echo "\n=== CACHE STATUS CHECK ===\n";
$cacheDir = 'cache/modules/DM_VehiclesInventory';
echo "Module cache directory exists: " . (is_dir($cacheDir) ? "YES" : "NO") . "\n";

$extFile = 'custom/application/Ext/Include/modules.ext.php';
if (file_exists($extFile)) {
    echo "Extension file exists: YES\n";
    $content = file_get_contents($extFile);
    echo "Extension file contains DM_VehiclesInventory: " . (strpos($content, 'DM_VehiclesInventory') !== false ? "YES" : "NO") . "\n";
} else {
    echo "Extension file exists: NO\n";
}

// 5. Check permissions
echo "\n=== PERMISSIONS CHECK ===\n";
if (isset($current_user)) {
    echo "Current user: " . $current_user->user_name . "\n";
    echo "Is admin: " . ($current_user->isAdmin() ? "YES" : "NO") . "\n";
    
    // Check ACL
    require_once('modules/ACLController/ACLController.php');
    echo "Has list access: " . (ACLController::checkAccess('DM_VehiclesInventory', 'list', true) ? "YES" : "NO") . "\n";
    echo "Has view access: " . (ACLController::checkAccess('DM_VehiclesInventory', 'view', true) ? "YES" : "NO") . "\n";
    echo "Has edit access: " . (ACLController::checkAccess('DM_VehiclesInventory', 'edit', true) ? "YES" : "NO") . "\n";
} else {
    echo "No current user context\n";
}

// 6. Check for common issues
echo "\n=== COMMON ISSUES CHECK ===\n";

// Check opcache
if (function_exists('opcache_get_status')) {
    $opcacheStatus = opcache_get_status();
    echo "OPcache enabled: " . ($opcacheStatus['opcache_enabled'] ? "YES" : "NO") . "\n";
    if ($opcacheStatus['opcache_enabled']) {
        echo "OPcache memory usage: " . round($opcacheStatus['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n";
    }
} else {
    echo "OPcache not available\n";
}

// Check file permissions
$dirs = ['cache', 'custom', 'modules/DM_VehiclesInventory'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        echo "Directory '$dir' writable: " . (is_writable($dir) ? "YES" : "NO") . "\n";
    }
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Run Quick Repair and Rebuild from Admin panel\n";
echo "2. Clear browser cache and cookies\n";
echo "3. If using OPcache, restart web server or clear OPcache\n";
echo "4. Run: php modules/DM_VehiclesInventory/repair_vehicle_inventory.php\n";

echo "</pre>"; 