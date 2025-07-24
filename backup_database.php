<?php
/**
 * Database Backup Script for Nuclear Permission Fix
 * 
 * This creates a backup of ACL-related tables before running the nuclear fix
 * Run this before the nuclear option for complete safety
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

global $current_user;
if (!$current_user || !$current_user->isAdmin()) {
    die('Access denied. Admin privileges required.');
}

echo "<h1>🛡️ Database Backup for Nuclear Permission Fix</h1>\n";
echo "<pre>\n";

try {
    echo "Starting database backup...\n\n";
    
    // Tables to backup before nuclear fix
    $tables_to_backup = array(
        'acl_actions',
        'acl_roles_actions', 
        'acl_roles_users',
        'acl_roles'
    );
    
    $backup_timestamp = date('Y-m-d_H-i-s');
    $backup_dir = "database_backups";
    
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
        echo "✓ Created backup directory: $backup_dir\n";
    }
    
    foreach ($tables_to_backup as $table) {
        echo "Backing up table: $table\n";
        
        // Get all data from table
        $query = "SELECT * FROM $table";
        $result = $GLOBALS['db']->query($query);
        
        $backup_data = array();
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $backup_data[] = $row;
        }
        
        // Save to JSON file
        $backup_file = "$backup_dir/{$table}_backup_{$backup_timestamp}.json";
        file_put_contents($backup_file, json_encode($backup_data, JSON_PRETTY_PRINT));
        echo "  ✓ Saved " . count($backup_data) . " records to: $backup_file\n";
    }
    
    // Create restore script
    $restore_script = "<?php
/**
 * Database Restore Script
 * Generated: $backup_timestamp
 * 
 * EMERGENCY USE ONLY - Restores database to state before nuclear fix
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

global \$current_user;
if (!\$current_user || !\$current_user->isAdmin()) {
    die('Access denied. Admin privileges required.');
}

echo '<h1>🚨 EMERGENCY DATABASE RESTORE</h1>';
echo '<pre>';

try {
    echo \"Starting emergency restore...\\n\\n\";
    
    // WARNING: This will delete current data!
    \$tables = array('acl_actions', 'acl_roles_actions', 'acl_roles_users', 'acl_roles');
    
    foreach (\$tables as \$table) {
        echo \"Restoring table: \$table\\n\";
        
        // Load backup data
        \$backup_file = \"database_backups/{\$table}_backup_{$backup_timestamp}.json\";
        if (!file_exists(\$backup_file)) {
            echo \"  ❌ Backup file not found: \$backup_file\\n\";
            continue;
        }
        
        \$backup_data = json_decode(file_get_contents(\$backup_file), true);
        
        // Clear table
        \$GLOBALS['db']->query(\"DELETE FROM \$table\");
        echo \"  ✓ Cleared table\\n\";
        
        // Restore data
        foreach (\$backup_data as \$row) {
            \$columns = array_keys(\$row);
            \$values = array_map(function(\$val) {
                return \$val === null ? 'NULL' : \"'\" . \$GLOBALS['db']->quote(\$val) . \"'\";
            }, array_values(\$row));
            
            \$sql = \"INSERT INTO \$table (\" . implode(', ', \$columns) . \") VALUES (\" . implode(', ', \$values) . \")\";
            \$GLOBALS['db']->query(\$sql);
        }
        
        echo \"  ✓ Restored \" . count(\$backup_data) . \" records\\n\";
    }
    
    echo \"\\n🎉 Emergency restore completed!\\n\";
    echo \"\\n⚠️ You should log out and log back in now.\\n\";
    
} catch (Exception \$e) {
    echo \"❌ Restore failed: \" . \$e->getMessage() . \"\\n\";
}

echo '</pre>';
?>";

    file_put_contents("$backup_dir/EMERGENCY_RESTORE_{$backup_timestamp}.php", $restore_script);
    echo "\n✓ Created emergency restore script: $backup_dir/EMERGENCY_RESTORE_{$backup_timestamp}.php\n";
    
    echo "\n🎉 Database backup completed successfully!\n";
    echo "\n📋 Backup Summary:\n";
    echo "   - Backup timestamp: $backup_timestamp\n";
    echo "   - Tables backed up: " . implode(', ', $tables_to_backup) . "\n";
    echo "   - Backup location: $backup_dir/\n";
    echo "   - Emergency restore: $backup_dir/EMERGENCY_RESTORE_{$backup_timestamp}.php\n";
    
    echo "\n✅ You can now safely run the nuclear permission fix!\n";
    
} catch (Exception $e) {
    echo "❌ Backup failed: " . $e->getMessage() . "\n";
}

echo "</pre>\n";
?> 