<?php
/**
 * Vehicle Inventory Permission Fixer - Admin Integration
 * 
 * This creates a proper admin page to fix vehicle inventory permissions
 * Access via: Admin -> Vehicle Inventory Permission Fix
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $current_user;

// Only allow admin users
if (!$current_user || !$current_user->isAdmin()) {
    sugar_die('Unauthorized access. Admin privileges required.');
}

// Process the fix if requested
$fix_completed = false;
$fix_results = array();

// Check CSRF token and process fix
if (!empty($_POST['run_fix']) && !empty($_POST['csrf_token'])) {
    // Verify CSRF token
    if ($_POST['csrf_token'] === $_SESSION['csrf_token']) {
        $fix_completed = true;
        
        try {
            $fix_results[] = "🚨 Starting Vehicle Inventory Permission Fix...";
            
            // Step 1: Clear existing ACL data
            $fix_results[] = "=== Clearing existing ACL data ===";
            $modules = array('DM_VehiclesInventory', 'DM_VehicleFeatures');
            
            foreach ($modules as $module) {
                $GLOBALS['db']->query("DELETE FROM acl_actions WHERE category = '$module'");
                $GLOBALS['db']->query("DELETE FROM acl_roles_actions WHERE category = '$module'");
                $fix_results[] = "✓ Cleared ACL data for $module";
            }
            
            // Step 2: Create fresh ACL actions
            $fix_results[] = "=== Creating fresh ACL actions ===";
            $actions = array('access', 'view', 'list', 'edit', 'delete', 'import', 'export');
            
            foreach ($modules as $module) {
                foreach ($actions as $action) {
                    $id = create_guid();
                    $query = "INSERT INTO acl_actions (id, name, category, acltype, aclaccess, deleted, date_entered, date_modified) 
                             VALUES ('$id', '$action', '$module', 'module', 89, 0, NOW(), NOW())";
                    $GLOBALS['db']->query($query);
                }
                $fix_results[] = "✓ Created ACL actions for $module";
            }
            
            // Step 3: Grant admin permissions
            $fix_results[] = "=== Granting admin permissions ===";
            $admin_query = "SELECT id, name FROM acl_roles 
                           WHERE (name LIKE '%admin%' OR name LIKE '%Admin%' OR name = 'Administrator') 
                           AND deleted = 0";
            $admin_result = $GLOBALS['db']->query($admin_query);
            
            while ($admin_role = $GLOBALS['db']->fetchByAssoc($admin_result)) {
                foreach ($modules as $module) {
                    foreach ($actions as $action) {
                        $id = create_guid();
                        $query = "INSERT INTO acl_roles_actions (id, role_id, action_id, access_override, date_modified) 
                                 SELECT '$id', '{$admin_role['id']}', aa.id, 90, NOW()
                                 FROM acl_actions aa 
                                 WHERE aa.name = '$action' AND aa.category = '$module' AND aa.deleted = 0";
                        $GLOBALS['db']->query($query);
                    }
                }
                $fix_results[] = "✓ Granted permissions to {$admin_role['name']}";
            }
            
            // Step 4: Ensure user has admin role
            $fix_results[] = "=== Ensuring user has admin role ===";
            $user_role_query = "SELECT r.name 
                               FROM acl_roles r 
                               JOIN acl_roles_users ru ON r.id = ru.role_id 
                               WHERE ru.user_id = '{$current_user->id}' 
                               AND (r.name LIKE '%admin%' OR r.name LIKE '%Admin%' OR r.name = 'Administrator')
                               AND r.deleted = 0 AND ru.deleted = 0";
            $user_role_result = $GLOBALS['db']->query($user_role_query);
            $has_admin_role = $GLOBALS['db']->fetchByAssoc($user_role_result);
            
            if (!$has_admin_role) {
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
                    $fix_results[] = "✓ Added user to {$first_admin['name']} role";
                }
            } else {
                $fix_results[] = "✓ User already has admin role: {$has_admin_role['name']}";
            }
            
            // Step 5: Clear caches
            $fix_results[] = "=== Clearing caches ===";
            
            // Clear specific cache directories
            $cache_dirs = array('cache/modules/DM_VehiclesInventory', 'cache/modules/DM_VehicleFeatures');
            foreach ($cache_dirs as $dir) {
                if (is_dir($dir)) {
                    removeDir($dir);
                    $fix_results[] = "✓ Cleared $dir";
                }
            }
            
            // Step 6: Rebuild extensions
            $fix_results[] = "=== Rebuilding extensions ===";
            require_once('ModuleInstall/ModuleInstaller.php');
            $moduleInstaller = new ModuleInstaller();
            $moduleInstaller->rebuild_all(true);
            $fix_results[] = "✓ Rebuilt all extensions";
            
            $fix_results[] = "🎉 Fix completed successfully!";
            $fix_results[] = "⚠️ IMPORTANT: Please log out and log back in for changes to take effect.";
            
        } catch (Exception $e) {
            $fix_results[] = "❌ Error: " . $e->getMessage();
        }
    } else {
        $fix_results[] = "❌ Security token mismatch. Please refresh the page and try again.";
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = create_guid();
}

// Function to remove directory recursively
function removeDir($dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? removeDir("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Inventory Permission Fixer</title>
    <link rel="stylesheet" type="text/css" href="themes/SuiteP/css/style.css">
    <style>
        .fix-container { max-width: 800px; margin: 20px auto; padding: 20px; background: white; border: 1px solid #ddd; }
        .fix-results { background: #f8f9fa; padding: 15px; margin: 20px 0; border: 1px solid #dee2e6; max-height: 400px; overflow-y: auto; font-family: monospace; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .big-button { 
            background: #dc3545; 
            color: white; 
            padding: 15px 30px; 
            font-size: 16px; 
            border: none; 
            cursor: pointer; 
            margin: 10px 0;
            border-radius: 4px;
        }
        .big-button:hover { background: #c82333; }
        .logout-button { 
            background: #007bff; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            display: inline-block; 
            margin: 10px 5px;
            border-radius: 4px;
        }
        .alert { padding: 15px; margin: 20px 0; border: 1px solid transparent; border-radius: 4px; }
        .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeaa7; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
    </style>
</head>
<body>

<div class="fix-container">
    <h1>🔧 Vehicle Inventory Permission Fixer</h1>
    
    <?php if (!$fix_completed): ?>
        <div class="alert alert-warning">
            <h3>⚠️ Warning</h3>
            <p>This tool will fix permission issues with the Vehicle Inventory module by:</p>
            <ul>
                <li>Clearing existing ACL data for Vehicle modules</li>
                <li>Creating fresh ACL actions in the database</li>
                <li>Granting full permissions to admin roles</li>
                <li>Ensuring your user has admin access</li>
                <li>Clearing all related caches</li>
                <li>Rebuilding system extensions</li>
            </ul>
            <p><strong>After running this fix, you MUST log out and log back in.</strong></p>
        </div>
        
        <form method="post" onsubmit="return confirm('Are you sure you want to run the permission fix? This will modify your database.');">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" name="run_fix" value="1" class="big-button">
                🚨 Run Permission Fix (Nuclear Option)
            </button>
        </form>
        
    <?php else: ?>
        <div class="fix-results">
            <h3>Fix Results:</h3>
            <?php foreach ($fix_results as $result): ?>
                <div><?php echo htmlspecialchars($result); ?></div>
            <?php endforeach; ?>
        </div>
        
        <div class="alert alert-success">
            <h3>✅ Next Steps</h3>
            <ol>
                <li><strong>Log out immediately</strong> - This is critical for the changes to take effect</li>
                <li><strong>Log back in</strong></li>
                <li><strong>Try accessing Vehicle Inventory module</strong></li>
            </ol>
            
            <a href="index.php?action=Logout" class="logout-button">🚪 Log Out Now</a>
            <a href="index.php?module=DM_VehiclesInventory&action=index" class="logout-button">📋 Test Vehicle Inventory</a>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 40px;">
        <a href="index.php?module=Administration&action=index">← Back to Administration</a>
    </div>
</div>

</body>
</html> 