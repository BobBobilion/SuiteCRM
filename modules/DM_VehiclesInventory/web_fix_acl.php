<?php
/**
 * Web-based ACL Fix for Vehicle Inventory Module
 * 
 * Access this file via: 
 * http://your-suitecrm-url/modules/DM_VehiclesInventory/web_fix_acl.php
 */

// Allow direct access for this fix script
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Set working directory to SuiteCRM root
chdir(dirname(__FILE__) . '/../..');
require_once('include/entryPoint.php');

// Only allow admin users to run this script
global $current_user;
if (!$current_user || !$current_user->isAdmin()) {
    die('Access denied. Admin privileges required.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Inventory ACL Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Vehicle Inventory Module - ACL Permission Fix</h1>
    <pre>
<?php

try {
    echo "Starting ACL fix process...\n\n";
    
    // Step 1: Clear ACL cache
    echo "=== Step 1: Clearing ACL Cache ===\n";
    
    // Clear file-based cache
    $cacheDirectories = array(
        'cache/modules/DM_VehiclesInventory',
        'cache/modules/DM_VehicleFeatures',
        'cache/modules/ACLActions',
        'cache/modules/ACLRoles',
        'cache/data'
    );
    
    foreach ($cacheDirectories as $dir) {
        if (is_dir($dir)) {
            deleteDirectory($dir);
            echo "<span class='success'>✓ Cleared cache directory: $dir</span>\n";
        }
    }
    
    // Step 2: Rebuild ACL actions
    echo "\n=== Step 2: Rebuilding ACL Actions ===\n";
    
    require_once('modules/ACLActions/actiondefs.php');
    require_once('modules/ACLActions/ACLAction.php');
    
    // Create default ACL actions for DM_VehiclesInventory
    $modules = array('DM_VehiclesInventory', 'DM_VehicleFeatures');
    
    foreach ($modules as $module) {
        echo "\nProcessing module: $module\n";
        
        // Define default actions
        $actions = array(
            'access' => array('aclaccess' => 89),
            'view' => array('aclaccess' => 89),
            'list' => array('aclaccess' => 89),
            'edit' => array('aclaccess' => 89),
            'delete' => array('aclaccess' => 89),
            'import' => array('aclaccess' => 89),
            'export' => array('aclaccess' => 89)
        );
        
        foreach ($actions as $action => $params) {
            // Check if action already exists
            $aclAction = new ACLAction();
            $aclAction->clearSessionCache();
            
            $query = "SELECT id FROM acl_actions WHERE name = '$action' AND category = '$module' AND deleted = 0";
            $result = $GLOBALS['db']->query($query);
            $row = $GLOBALS['db']->fetchByAssoc($result);
            
            if (!$row) {
                // Create new action
                $newAction = new ACLAction();
                $newAction->name = $action;
                $newAction->category = $module;
                $newAction->acltype = 'module';
                $newAction->aclaccess = $params['aclaccess'];
                $newAction->deleted = 0;
                $newAction->save();
                echo "<span class='success'>✓ Created ACL action: $action for $module</span>\n";
            } else {
                echo "<span class='info'>- ACL action already exists: $action for $module</span>\n";
            }
        }
    }
    
    // Step 3: Grant permissions to admin role
    echo "\n=== Step 3: Granting Admin Permissions ===\n";
    
    require_once('modules/ACLRoles/ACLRole.php');
    
    // Find admin roles
    $adminRole = new ACLRole();
    $adminRoles = $adminRole->get_full_list('', "(name = 'Administrator' OR name = 'Admin') AND deleted = 0");
    
    if ($adminRoles) {
        foreach ($adminRoles as $role) {
            echo "\nUpdating role: {$role->name}\n";
            
            foreach ($modules as $module) {
                // Set all permissions to allow (90) for admin
                foreach ($actions as $action => $params) {
                    $role->setAction($role->id, $action, $module, 'module', 90);
                    echo "<span class='success'>✓ Granted $action permission for $module to {$role->name}</span>\n";
                }
            }
        }
    } else {
        echo "<span class='error'>✗ No admin role found!</span>\n";
    }
    
    // Step 4: Run additional repairs
    echo "\n=== Step 4: Running Additional Repairs ===\n";
    
    // Rebuild extensions
    require_once('ModuleInstall/ModuleInstaller.php');
    $moduleInstaller = new ModuleInstaller();
    $moduleInstaller->rebuild_all(true);
    echo "<span class='success'>✓ Rebuilt all extensions</span>\n";
    
    // Clear vardefs cache
    VardefManager::clearVardef();
    echo "<span class='success'>✓ Cleared vardef cache</span>\n";
    
    // Clear additional caches
    if (function_exists('sugar_cache_clear')) {
        sugar_cache_clear('modules/DM_VehiclesInventory');
        sugar_cache_clear('modules/DM_VehicleFeatures');
        echo "<span class='success'>✓ Cleared sugar cache</span>\n";
    }
    
    echo "\n<span class='success'><strong>✓ ACL fix completed successfully!</strong></span>\n";
    echo "\n<strong>Next steps:</strong>\n";
    echo "1. Log out and log back in\n";
    echo "2. Navigate to Vehicle Inventory module\n";
    echo "3. If still having issues, run Quick Repair and Rebuild from Admin panel\n";
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span>\n";
}

/**
 * Recursively delete a directory
 */
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}

?>
    </pre>
    
    <hr>
    <p><a href="../../index.php?module=DM_VehiclesInventory&action=index">Go to Vehicle Inventory Module</a></p>
    <p><a href="../../index.php?module=Administration&action=index">Back to Admin Panel</a></p>
</body>
</html> 