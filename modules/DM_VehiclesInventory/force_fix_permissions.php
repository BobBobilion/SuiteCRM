<?php
/**
 * NUCLEAR OPTION: Force Fix All Permissions
 * 
 * This script forcefully fixes ALL permission issues by directly manipulating the database
 * Access via: http://your-suitecrm-url/modules/DM_VehiclesInventory/force_fix_permissions.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__) . '/../..');
require_once('include/entryPoint.php');

global $current_user;
if (!$current_user || !$current_user->isAdmin()) {
    die('Access denied. Admin privileges required.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>NUCLEAR OPTION: Force Fix Permissions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
        .nuclear { background: red; color: white; padding: 20px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="nuclear">
        <h1>⚠️ NUCLEAR OPTION: FORCE FIX PERMISSIONS ⚠️</h1>
        <p>This script will aggressively fix ALL permission issues by directly manipulating the database.</p>
    </div>
    
    <pre>
<?php

try {
    echo "🚨 STARTING NUCLEAR PERMISSION FIX 🚨\n\n";
    
    // Step 1: Delete ALL existing ACL actions for our modules
    echo "=== STEP 1: CLEARING EXISTING ACL DATA ===\n";
    $modules = array('DM_VehiclesInventory', 'DM_VehicleFeatures');
    
    foreach ($modules as $module) {
        echo "Clearing ACL data for $module...\n";
        
        // Delete existing ACL actions
        $query = "DELETE FROM acl_actions WHERE category = '$module'";
        $GLOBALS['db']->query($query);
        echo "  ✓ Deleted ACL actions\n";
        
        // Delete existing role actions
        $query = "DELETE FROM acl_roles_actions WHERE category = '$module'";
        $GLOBALS['db']->query($query);
        echo "  ✓ Deleted role actions\n";
    }
    
    // Step 2: Create fresh ACL actions
    echo "\n=== STEP 2: CREATING FRESH ACL ACTIONS ===\n";
    $actions = array('access', 'view', 'list', 'edit', 'delete', 'import', 'export');
    
    foreach ($modules as $module) {
        echo "Creating ACL actions for $module...\n";
        
        foreach ($actions as $action) {
            $id = create_guid();
            $query = "INSERT INTO acl_actions (id, name, category, acltype, aclaccess, deleted, date_entered, date_modified) 
                     VALUES ('$id', '$action', '$module', 'module', 89, 0, NOW(), NOW())";
            $GLOBALS['db']->query($query);
            echo "  ✓ Created $action action\n";
        }
    }
    
    // Step 3: Find ALL admin roles and grant permissions
    echo "\n=== STEP 3: GRANTING ADMIN PERMISSIONS ===\n";
    
    // Find all roles that might be admin
    $admin_query = "SELECT id, name FROM acl_roles 
                   WHERE (name LIKE '%admin%' OR name LIKE '%Admin%' OR name = 'Administrator') 
                   AND deleted = 0";
    $admin_result = $GLOBALS['db']->query($admin_query);
    
    while ($admin_role = $GLOBALS['db']->fetchByAssoc($admin_result)) {
        echo "Granting permissions to role: {$admin_role['name']}\n";
        
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $id = create_guid();
                $query = "INSERT INTO acl_roles_actions (id, role_id, action_id, access_override, date_modified) 
                         SELECT '$id', '{$admin_role['id']}', aa.id, 90, NOW()
                         FROM acl_actions aa 
                         WHERE aa.name = '$action' AND aa.category = '$module' AND aa.deleted = 0";
                $GLOBALS['db']->query($query);
                echo "    ✓ Granted $action for $module\n";
            }
        }
    }
    
    // Step 4: Ensure current user has an admin role
    echo "\n=== STEP 4: ENSURING USER HAS ADMIN ROLE ===\n";
    
    // Check if user has any admin role
    $user_role_query = "SELECT r.name 
                       FROM acl_roles r 
                       JOIN acl_roles_users ru ON r.id = ru.role_id 
                       WHERE ru.user_id = '{$current_user->id}' 
                       AND (r.name LIKE '%admin%' OR r.name LIKE '%Admin%' OR r.name = 'Administrator')
                       AND r.deleted = 0 AND ru.deleted = 0";
    $user_role_result = $GLOBALS['db']->query($user_role_query);
    $has_admin_role = $GLOBALS['db']->fetchByAssoc($user_role_result);
    
    if (!$has_admin_role) {
        echo "User doesn't have admin role. Adding to first available admin role...\n";
        
        $first_admin_query = "SELECT id, name FROM acl_roles 
                             WHERE (name LIKE '%admin%' OR name LIKE '%Admin%' OR name = 'Administrator') 
                             AND deleted = 0 LIMIT 1";
        $first_admin_result = $GLOBALS['db']->query($first_admin_query);
        $first_admin = $GLOBALS['db']->fetchByAssoc($first_admin_result);
        
        if ($first_admin) {
            $id = create_guid();
            $query = "INSERT INTO acl_roles_users (id, role_id, user_id, date_modified) 
                     VALUES ('$id', '{$first_admin['id']}', '{$current_user->id}', NOW())";
            $GLOBALS['db']->query($query);
            echo "  ✓ Added user to {$first_admin['name']} role\n";
        }
    } else {
        echo "  ✓ User already has admin role: {$has_admin_role['name']}\n";
    }
    
    // Step 5: Force rebuild all caches
    echo "\n=== STEP 5: NUCLEAR CACHE CLEARING ===\n";
    
    // Delete cache directories
    $cache_dirs = array(
        'cache',
        'custom/application/Ext/Include',
        'custom/application/Ext/Language',
        'custom/modules/DM_VehiclesInventory/Ext',
        'custom/modules/DM_VehicleFeatures/Ext'
    );
    
    foreach ($cache_dirs as $dir) {
        if (is_dir($dir)) {
            // Just delete cache files, not the entire directory structure
            clearDirectory($dir);
            echo "  ✓ Cleared $dir\n";
        }
    }
    
    // Step 6: Force module registration
    echo "\n=== STEP 6: FORCE MODULE REGISTRATION ===\n";
    
    // Rebuild the module ext file manually
    $ext_content = "<?php\n";
    $ext_content .= "// Auto-generated by force fix\n";
    
    foreach ($modules as $module) {
        $ext_content .= "\$beanList['$module'] = '$module';\n";
        $ext_content .= "\$beanFiles['$module'] = 'modules/$module/$module.php';\n";
        $ext_content .= "\$moduleList[] = '$module';\n";
        $ext_content .= "\$modules_exempt_from_availability_check['$module'] = '$module';\n";
        $ext_content .= "\$report_include_modules['$module'] = '$module';\n";
        $ext_content .= "\n";
    }
    $ext_content .= "?>";
    
    if (!is_dir('custom/application/Ext/Include')) {
        mkdir('custom/application/Ext/Include', 0755, true);
    }
    
    file_put_contents('custom/application/Ext/Include/modules.ext.php', $ext_content);
    echo "  ✓ Created modules.ext.php\n";
    
    // Step 7: Final rebuild
    echo "\n=== STEP 7: FINAL SYSTEM REBUILD ===\n";
    
    require_once('ModuleInstall/ModuleInstaller.php');
    $moduleInstaller = new ModuleInstaller();
    $moduleInstaller->rebuild_all(true);
    echo "  ✓ Rebuilt all extensions\n";
    
    // Clear all possible caches
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "  ✓ Cleared OPCache\n";
    }
    
    if (function_exists('apc_clear_cache')) {
        apc_clear_cache('user');
        echo "  ✓ Cleared APC cache\n";
    }
    
    echo "\n🎉 <span class='success'>NUCLEAR FIX COMPLETED!</span> 🎉\n";
    echo "\n<span class='warning'>CRITICAL: You MUST log out and log back in now!</span>\n";
    echo "\nAfter logging back in, the Vehicle Inventory module should be fully accessible.\n";
    
} catch (Exception $e) {
    echo "<span class='error'>💥 NUCLEAR FIX FAILED: " . $e->getMessage() . "</span>\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

/**
 * Clear directory contents but keep the directory structure
 */
function clearDirectory($dir) {
    if (!is_dir($dir)) return;
    
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        } elseif (is_dir($file) && basename($file) !== '.' && basename($file) !== '..') {
            clearDirectory($file);
            @rmdir($file);
        }
    }
}

?>
    </pre>
    
    <div class="nuclear">
        <h2>⚠️ IMPORTANT NEXT STEPS ⚠️</h2>
        <ol>
            <li><strong>LOG OUT IMMEDIATELY</strong></li>
            <li><strong>LOG BACK IN</strong></li>
            <li><strong>Try accessing Vehicle Inventory</strong></li>
        </ol>
        <p><a href="../../index.php?action=Logout" style="background: red; color: white; padding: 15px; text-decoration: none; font-weight: bold;">🚪 LOG OUT NOW</a></p>
    </div>
</body>
</html> 