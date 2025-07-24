<?php
/**
 * Vehicle Inventory Module Repair Script
 * 
 * This script can be run to fix any initialization issues with the 
 * DM_VehiclesInventory module and clear relevant caches.
 * 
 * Usage: Run from SuiteCRM root directory
 * php modules/DM_VehiclesInventory/repair_vehicle_inventory.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Load SuiteCRM environment
require_once('include/entryPoint.php');
require_once('modules/Administration/QuickRepairAndRebuild.php');

echo "Starting Vehicle Inventory Module Repair...\n";

// 1. Clear module cache
echo "Clearing module cache...\n";
if (is_dir('cache/modules/DM_VehiclesInventory')) {
    removeDirectory('cache/modules/DM_VehiclesInventory');
}

// 2. Clear application cache
echo "Clearing application cache...\n";
if (file_exists('cache/application/Ext/Include/modules.ext.php')) {
    @unlink('cache/application/Ext/Include/modules.ext.php');
}

// 3. Rebuild extensions
echo "Rebuilding extensions...\n";
require_once('ModuleInstall/ModuleInstaller.php');
$mi = new ModuleInstaller();
$mi->rebuild_extensions();

// 4. Quick Repair and Rebuild
echo "Running Quick Repair and Rebuild...\n";
$RAC = new RepairAndClear();
$RAC->clearVardefs();
$RAC->rebuildExtensions();
$RAC->clearLanguageCache();
$RAC->clearSmartyCache();
$RAC->clearThemeCache();
$RAC->clearJsCache();
$RAC->clearDashlets();
$RAC->clearImageCache();
$RAC->clearTpls();

// 4.5. Rebuild ACL Actions
echo "Rebuilding ACL actions...\n";
require_once('modules/ACLActions/ACLAction.php');
$aclAction = new ACLAction();
$aclAction->addActions('DM_VehiclesInventory');

// 5. Register module in moduleList if not present
echo "Verifying module registration...\n";
global $moduleList, $beanList, $beanFiles;

if (!in_array('DM_VehiclesInventory', $moduleList)) {
    $moduleList[] = 'DM_VehiclesInventory';
    echo "Added DM_VehiclesInventory to moduleList\n";
}

if (!isset($beanList['DM_VehiclesInventory'])) {
    $beanList['DM_VehiclesInventory'] = 'DM_VehiclesInventory';
    echo "Added DM_VehiclesInventory to beanList\n";
}

if (!isset($beanFiles['DM_VehiclesInventory'])) {
    $beanFiles['DM_VehiclesInventory'] = 'modules/DM_VehiclesInventory/DM_VehiclesInventory.php';
    echo "Added DM_VehiclesInventory to beanFiles\n";
}

// 6. Clear opcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Cleared OPcache\n";
}

// 7. Clear ACL cache
echo "Clearing ACL cache...\n";
$aclCacheDirs = array(
    'cache/modules/ACLActions',
    'cache/modules/ACLRoles'
);

foreach ($aclCacheDirs as $cacheDir) {
    if (is_dir($cacheDir)) {
        removeDirectory($cacheDir);
        echo "Cleared ACL cache: {$cacheDir}\n";
    }
}

echo "\nVehicle Inventory Module Repair Complete!\n";
echo "Please navigate to Admin > Repair > Quick Repair and Rebuild in SuiteCRM to finalize.\n";
echo "Then log out and log back in to refresh permissions.\n";

/**
 * Remove directory recursively
 */
function removeDirectory($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    removeDirectory($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
} 