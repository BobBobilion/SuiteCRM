<?php
/**
 * Vehicle Inventory ACL Permissions Fix Script
 * 
 * This script fixes ACL permissions for the DM_VehiclesInventory module
 * Run this when getting "You do not have permission to access this module" errors
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');
require_once('modules/ACLActions/ACLAction.php');
require_once('modules/ACLRoles/ACLRole.php');

echo "<h2>Vehicle Inventory ACL Permissions Fix</h2>";
echo "<pre>";

// 1. Check if module exists in ACL Actions
echo "=== CHECKING ACL ACTIONS ===\n";

$aclActionBean = BeanFactory::getBean('ACLActions');
$aclActions = $aclActionBean->get_full_list('', "category = 'DM_VehiclesInventory'");

if (empty($aclActions)) {
    echo "No ACL actions found for DM_VehiclesInventory. Creating default actions...\n";
    
    // Create default ACL actions for the module
    $actions = array('access', 'view', 'list', 'edit', 'delete', 'import', 'export');
    
    foreach ($actions as $action) {
        $aclAction = BeanFactory::getBean('ACLActions');
        $aclAction->name = $action;
        $aclAction->category = 'DM_VehiclesInventory';
        $aclAction->acltype = 'module';
        $aclAction->aclaccess = 89; // Default: Not Set
        $aclAction->deleted = 0;
        $aclAction->save();
        echo "Created ACL action: {$action} for DM_VehiclesInventory\n";
    }
} else {
    echo "Found " . count($aclActions) . " ACL actions for DM_VehiclesInventory:\n";
    foreach ($aclActions as $action) {
        echo "- {$action->name} (access level: {$action->aclaccess})\n";
    }
}

// 2. Check current user's access
echo "\n=== CHECKING CURRENT USER ACCESS ===\n";
global $current_user;

if ($current_user) {
    echo "Current user: {$current_user->user_name}\n";
    echo "Is admin: " . ($current_user->isAdmin() ? "YES" : "NO") . "\n";
    
    // Check specific permissions
    require_once('modules/ACLController/ACLController.php');
    $permissions = array('access', 'view', 'list', 'edit', 'delete', 'import', 'export');
    
    foreach ($permissions as $permission) {
        $hasAccess = ACLController::checkAccess('DM_VehiclesInventory', $permission, true);
        echo "Permission '{$permission}': " . ($hasAccess ? "GRANTED" : "DENIED") . "\n";
    }
    
    // If user is admin, grant all permissions
    if ($current_user->isAdmin()) {
        echo "\nUser is admin - should have full access. If still getting errors, check module registration.\n";
    }
} else {
    echo "No current user context available\n";
}

// 3. Check if module is in admin role
echo "\n=== CHECKING ADMIN ROLE PERMISSIONS ===\n";

$adminRole = BeanFactory::getBean('ACLRoles');
$adminRoles = $adminRole->get_full_list('', "name = 'Administrator' OR name = 'Admin'");

if (!empty($adminRoles)) {
    foreach ($adminRoles as $role) {
        echo "Found admin role: {$role->name} (ID: {$role->id})\n";
        
        // Check if this role has permissions for our module
        $roleActions = $role->getRoleActions($role->id);
        
        $hasVehicleInventory = false;
        foreach ($roleActions as $category => $actions) {
            if ($category == 'DM_VehiclesInventory') {
                $hasVehicleInventory = true;
                echo "Role has DM_VehiclesInventory permissions:\n";
                foreach ($actions as $actionName => $actionData) {
                    echo "  - {$actionName}: " . $actionData['aclaccess'] . "\n";
                }
                break;
            }
        }
        
        if (!$hasVehicleInventory) {
            echo "Role does NOT have DM_VehiclesInventory permissions\n";
            echo "Adding full permissions for admin role...\n";
            
            // Add permissions for admin role
            $moduleActions = array('access', 'view', 'list', 'edit', 'delete', 'import', 'export');
            foreach ($moduleActions as $actionName) {
                $roleAction = BeanFactory::getBean('ACLActions');
                $existingAction = $roleAction->retrieve_by_string_fields(array(
                    'role_id' => $role->id,
                    'category' => 'DM_VehiclesInventory',
                    'name' => $actionName
                ));
                
                if (!$existingAction) {
                    $roleAction->role_id = $role->id;
                    $roleAction->name = $actionName;
                    $roleAction->category = 'DM_VehiclesInventory';
                    $roleAction->acltype = 'module';
                    $roleAction->aclaccess = 90; // Allow
                    $roleAction->deleted = 0;
                    $roleAction->save();
                    echo "  Added {$actionName} permission to admin role\n";
                }
            }
        }
    }
} else {
    echo "No admin role found\n";
}

// 4. Force refresh ACL cache
echo "\n=== CLEARING ACL CACHE ===\n";
if (function_exists('apc_clear_cache')) {
    apc_clear_cache('user');
    echo "Cleared APC cache\n";
}

// Clear file-based cache
$cacheFiles = array(
    'cache/modules/ACLActions',
    'cache/modules/ACLRoles'
);

foreach ($cacheFiles as $cacheDir) {
    if (is_dir($cacheDir)) {
        removeDirectoryRecursive($cacheDir);
        echo "Cleared cache: {$cacheDir}\n";
    }
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Log out and log back in to refresh permissions\n";
echo "2. Go to Admin > Role Management and verify module permissions\n";
echo "3. Check that your user is assigned to a role with Vehicle Inventory access\n";
echo "4. Run Quick Repair and Rebuild if needed\n";

/**
 * Remove directory recursively
 */
function removeDirectoryRecursive($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    removeDirectoryRecursive($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}

echo "</pre>"; 