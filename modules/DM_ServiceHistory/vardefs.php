<?php
/**
 * SuiteCRM Service & Parts Hub - Service History Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_ServiceHistory module in the service & parts management system.
 * 
 * Includes all service history fields, relationships with vehicles and service orders,
 * and comprehensive metadata for the Service History functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_ServiceHistory'] = array(
    'table' => 'dm_servicehistory',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Service History for tracking all service work performed on vehicles with complete parts and labor tracking',
    'fields' => array(
        
        // Standard SugarBean fields - inherited from Basic template
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'char',
            'len' => '36',
            'required' => true,
            'reportable' => false,
            'comment' => 'Unique identifier'
        ),
        
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Service history display name (Service Date - Vehicle)',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'massupdate' => true,
            'audited' => true,
        ),
        
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'group' => 'created_by_name',
            'comment' => 'Date record created',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'group' => 'modified_by_name',
            'comment' => 'Date record last modified',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        
        'modified_user_id' => array(
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'group' => 'modified_by_name',
            'dbType' => 'char',
            'len' => '36',
            'reportable' => true,
            'comment' => 'User who last modified record',
        ),
        
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED_NAME',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'user_name',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'link' => 'modified_user_link',
            'duplicate_merge' => 'disabled',
        ),
        
        'created_by' => array(
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'comment' => 'User who created record',
        ),
        
        'created_by_name' => array(
            'name' => 'created_by_name',
            'vname' => 'LBL_CREATED',
            'type' => 'relate',
            'reportable' => false,
            'link' => 'created_by_link',
            'rname' => 'user_name',
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'created_by',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'importable' => 'false',
        ),
        
        'assigned_user_id' => array(
            'name' => 'assigned_user_id',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO_ID',
            'group' => 'assigned_user_name',
            'type' => 'relate',
            'table' => 'users',
            'module' => 'Users',
            'reportable' => true,
            'isnull' => 'false',
            'dbType' => 'char',
            'len' => '36',
            'audited' => true,
            'comment' => 'User ID assigned to service history (service advisor)',
            'duplicate_merge' => 'disabled'
        ),
        
        'assigned_user_name' => array(
            'name' => 'assigned_user_name',
            'link' => 'assigned_user_link',
            'vname' => 'LBL_ASSIGNED_TO_NAME',
            'rname' => 'user_name',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'assigned_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled'
        ),
        
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator'
        ),
        
        // === SERVICE HISTORY SPECIFIC FIELDS ===
        
        // Service Information
        'service_date' => array(
            'name' => 'service_date',
            'vname' => 'LBL_SERVICE_DATE',
            'type' => 'date',
            'comment' => 'Date when service was performed',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'mileage' => array(
            'name' => 'mileage',
            'vname' => 'LBL_MILEAGE',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Vehicle odometer reading at service',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'service_type' => array(
            'name' => 'service_type',
            'vname' => 'LBL_SERVICE_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Type of service performed',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'services_performed' => array(
            'name' => 'services_performed',
            'vname' => 'LBL_SERVICES_PERFORMED',
            'type' => 'text',
            'comment' => 'Detailed list of services performed',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'parts_used' => array(
            'name' => 'parts_used',
            'vname' => 'LBL_PARTS_USED',
            'type' => 'text',
            'comment' => 'JSON array of parts used in service',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // Labor and Cost Tracking
        'labor_hours' => array(
            'name' => 'labor_hours',
            'vname' => 'LBL_LABOR_HOURS',
            'type' => 'decimal',
            'len' => '5,2',
            'comment' => 'Total labor hours spent on service',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'total_cost' => array(
            'name' => 'total_cost',
            'vname' => 'LBL_TOTAL_COST',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Total cost of service including parts and labor',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Personnel
        'technician_id' => array(
            'name' => 'technician_id',
            'vname' => 'LBL_TECHNICIAN_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Technician who performed the work',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'technician_name' => array(
            'name' => 'technician_name',
            'rname' => 'user_name',
            'id_name' => 'technician_id',
            'vname' => 'LBL_TECHNICIAN_NAME',
            'type' => 'relate',
            'table' => 'users',
            'isnull' => 'true',
            'module' => 'Users',
            'dbType' => 'varchar',
            'link' => 'technician_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Technician name',
        ),
        
        // Additional Information
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'text',
            'comment' => 'Additional service notes and comments',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // === RELATIONSHIPS ===
        
        // Vehicle relationship
        'vehicle_id' => array(
            'name' => 'vehicle_id',
            'vname' => 'LBL_VEHICLE_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Vehicle ID that was serviced',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'vehicle_name' => array(
            'name' => 'vehicle_name',
            'rname' => 'name',
            'id_name' => 'vehicle_id',
            'vname' => 'LBL_VEHICLE_NAME',
            'type' => 'relate',
            'table' => 'dm_vehiclesinventory',
            'isnull' => 'true',
            'module' => 'DM_VehiclesInventory',
            'dbType' => 'varchar',
            'link' => 'vehicle_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Vehicle name/description',
        ),
        
        // Service Order relationship
        'service_order_id' => array(
            'name' => 'service_order_id',
            'vname' => 'LBL_SERVICE_ORDER_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Related service order ID',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'service_order_name' => array(
            'name' => 'service_order_name',
            'rname' => 'name',
            'id_name' => 'service_order_id',
            'vname' => 'LBL_SERVICE_ORDER_NAME',
            'type' => 'relate',
            'table' => 'dm_serviceorders',
            'isnull' => 'true',
            'module' => 'DM_ServiceOrders',
            'dbType' => 'varchar',
            'link' => 'service_order_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Service order name',
        ),
    ),
    
    // === RELATIONSHIPS DEFINITION ===
    'relationships' => array(
        // Vehicle relationship
        'dm_servicehistory_vehicles' => array(
            'lhs_module' => 'DM_VehiclesInventory',
            'lhs_table' => 'dm_vehiclesinventory',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_ServiceHistory',
            'rhs_table' => 'dm_servicehistory',
            'rhs_key' => 'vehicle_id',
            'relationship_type' => 'one-to-many',
        ),
        
        // Service Order relationship
        'dm_servicehistory_serviceorders' => array(
            'lhs_module' => 'DM_ServiceOrders',
            'lhs_table' => 'dm_serviceorders',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_ServiceHistory',
            'rhs_table' => 'dm_servicehistory',
            'rhs_key' => 'service_order_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    
    // === LINKS ===
    'links' => array(
        'assigned_user_link' => array(
            'name' => 'assigned_user_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_ASSIGNED_TO_USER',
        ),
        
        'modified_user_link' => array(
            'name' => 'modified_user_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_MODIFIED_BY_USER',
        ),
        
        'created_by_link' => array(
            'name' => 'created_by_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_CREATED_BY_USER',
        ),
        
        'vehicle_link' => array(
            'name' => 'vehicle_link',
            'type' => 'one',
            'module' => 'DM_VehiclesInventory',
            'bean_name' => 'DM_VehiclesInventory',
            'source' => 'non-db',
            'vname' => 'LBL_VEHICLE',
        ),
        
        'service_order_link' => array(
            'name' => 'service_order_link',
            'type' => 'one',
            'module' => 'DM_ServiceOrders',
            'bean_name' => 'DM_ServiceOrders',
            'source' => 'non-db',
            'vname' => 'LBL_SERVICE_ORDER',
        ),
        
        'technician_link' => array(
            'name' => 'technician_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_TECHNICIAN',
        ),
    ),
    
    // === INDICES ===
    'indices' => array(
        array(
            'name' => 'idx_dm_servicehistory_vehicle',
            'type' => 'index',
            'fields' => array('vehicle_id')
        ),
        array(
            'name' => 'idx_dm_servicehistory_service_order',
            'type' => 'index',
            'fields' => array('service_order_id')
        ),
        array(
            'name' => 'idx_dm_servicehistory_service_date',
            'type' => 'index',
            'fields' => array('service_date')
        ),
        array(
            'name' => 'idx_dm_servicehistory_technician',
            'type' => 'index',
            'fields' => array('technician_id')
        ),
        array(
            'name' => 'idx_dm_servicehistory_service_type',
            'type' => 'index',
            'fields' => array('service_type')
        ),
        array(
            'name' => 'idx_dm_servicehistory_assigned_user',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_dm_servicehistory_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        ),
    ),
);

// Add module to the global bean lists
VardefManager::createVardef('DM_ServiceHistory', 'DM_ServiceHistory', array('basic', 'assignable', 'security_groups'));
?>