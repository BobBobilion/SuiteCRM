<?php
/**
 * SuiteCRM Service & Parts Hub - Service Orders Variable Definitions
 * 
 * This file defines the database schema and field definitions for the 
 * DM_ServiceOrders module in the service & parts management system.
 * 
 * Includes all service order fields, relationships with customers, vehicles,
 * and comprehensive metadata for the Service Orders functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_ServiceOrders'] = array(
    'table' => 'dm_serviceorders',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Service Orders for managing automotive service appointments, repair orders, labor tracking, and parts integration',
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
            'comment' => 'Service order display name (RO# - Customer)',
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
            'comment' => 'User ID assigned to service order (service advisor)',
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
        
        // === SERVICE ORDER SPECIFIC FIELDS ===
        
        // Service Order Identification
        'service_order_number' => array(
            'name' => 'service_order_number',
            'vname' => 'LBL_SERVICE_ORDER_NUMBER',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Unique RO number (auto-generated)',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        // Vehicle Identification
        'vin' => array(
            'name' => 'vin',
            'vname' => 'LBL_VIN',
            'type' => 'varchar',
            'len' => '17',
            'comment' => 'Vehicle Identification Number',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'unified_search' => true,
        ),
        
        'mileage_in' => array(
            'name' => 'mileage_in',
            'vname' => 'LBL_MILEAGE_IN',
            'type' => 'int',
            'len' => '10',
            'comment' => 'Odometer reading at check-in',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Scheduling and Timeline
        'appointment_date' => array(
            'name' => 'appointment_date',
            'vname' => 'LBL_APPOINTMENT_DATE',
            'type' => 'datetime',
            'comment' => 'Scheduled appointment date and time',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'promise_time' => array(
            'name' => 'promise_time',
            'vname' => 'LBL_PROMISE_TIME',
            'type' => 'datetime',
            'comment' => 'Promised completion date and time',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Personnel Assignments
        'service_advisor_id' => array(
            'name' => 'service_advisor_id',
            'vname' => 'LBL_SERVICE_ADVISOR_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Assigned service advisor',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'service_advisor_name' => array(
            'name' => 'service_advisor_name',
            'rname' => 'user_name',
            'id_name' => 'service_advisor_id',
            'vname' => 'LBL_SERVICE_ADVISOR_NAME',
            'type' => 'relate',
            'table' => 'users',
            'isnull' => 'true',
            'module' => 'Users',
            'dbType' => 'varchar',
            'link' => 'service_advisor_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Service advisor name',
        ),
        
        'technician_id' => array(
            'name' => 'technician_id',
            'vname' => 'LBL_TECHNICIAN_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Primary technician assigned',
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
            'comment' => 'Primary technician name',
        ),
        
        // Service Categorization
        'service_type' => array(
            'name' => 'service_type',
            'vname' => 'LBL_SERVICE_TYPE',
            'type' => 'enum',
            'options' => 'service_type_list',
            'len' => '50',
            'comment' => 'Type of service (Maintenance/Repair/Warranty)',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'default' => 'Maintenance',
        ),
        
        'service_status' => array(
            'name' => 'service_status',
            'vname' => 'LBL_SERVICE_STATUS',
            'type' => 'enum',
            'options' => 'service_status_list',
            'len' => '50',
            'comment' => 'Current status of service order',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'default' => 'Scheduled',
        ),
        
        // Labor Tracking
        'labor_hours' => array(
            'name' => 'labor_hours',
            'vname' => 'LBL_LABOR_HOURS',
            'type' => 'decimal',
            'len' => '5,2',
            'comment' => 'Total labor hours worked',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'labor_rate' => array(
            'name' => 'labor_rate',
            'vname' => 'LBL_LABOR_RATE',
            'type' => 'decimal',
            'len' => '8,2',
            'comment' => 'Hourly labor rate',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'labor_total' => array(
            'name' => 'labor_total',
            'vname' => 'LBL_LABOR_TOTAL',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Total labor charges',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Financial Tracking
        'parts_total' => array(
            'name' => 'parts_total',
            'vname' => 'LBL_PARTS_TOTAL',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Total parts charges',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'tax_amount' => array(
            'name' => 'tax_amount',
            'vname' => 'LBL_TAX_AMOUNT',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Sales tax amount',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        'total_amount' => array(
            'name' => 'total_amount',
            'vname' => 'LBL_TOTAL_AMOUNT',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Grand total amount',
            'required' => false,
            'audited' => true,
            'massupdate' => true,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
            'enable_range_search' => true,
        ),
        
        // Service Details
        'customer_concern' => array(
            'name' => 'customer_concern',
            'vname' => 'LBL_CUSTOMER_CONCERN',
            'type' => 'text',
            'comment' => 'Customer reported problem or concern',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'work_performed' => array(
            'name' => 'work_performed',
            'vname' => 'LBL_WORK_PERFORMED',
            'type' => 'text',
            'comment' => 'Detailed description of work performed',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'text',
            'comment' => 'Additional notes and comments',
            'required' => false,
            'audited' => false,
            'massupdate' => false,
            'duplicate_merge' => 'enabled',
            'importable' => 'true',
        ),
        
        // === RELATIONSHIPS ===
        
        // Customer relationship
        'customer_id' => array(
            'name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Customer account ID',
            'required' => false,
            'audited' => true,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => 'true',
        ),
        
        'customer_name' => array(
            'name' => 'customer_name',
            'rname' => 'name',
            'id_name' => 'customer_id',
            'vname' => 'LBL_CUSTOMER_NAME',
            'type' => 'relate',
            'table' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'link' => 'customer_link',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'Customer account name',
        ),
        
        // Vehicle relationship
        'vehicle_id' => array(
            'name' => 'vehicle_id',
            'vname' => 'LBL_VEHICLE_ID',
            'type' => 'char',
            'len' => '36',
            'comment' => 'Vehicle inventory ID',
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
    ),
    
    // === RELATIONSHIPS DEFINITION ===
    'relationships' => array(
        // Customer relationship
        'dm_serviceorders_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_ServiceOrders',
            'rhs_table' => 'dm_serviceorders',
            'rhs_key' => 'customer_id',
            'relationship_type' => 'one-to-many',
        ),
        
        // Vehicle relationship
        'dm_serviceorders_vehicles' => array(
            'lhs_module' => 'DM_VehiclesInventory',
            'lhs_table' => 'dm_vehiclesinventory',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_ServiceOrders',
            'rhs_table' => 'dm_serviceorders',
            'rhs_key' => 'vehicle_id',
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
        
        'customer_link' => array(
            'name' => 'customer_link',
            'type' => 'one',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'source' => 'non-db',
            'vname' => 'LBL_CUSTOMER',
        ),
        
        'vehicle_link' => array(
            'name' => 'vehicle_link',
            'type' => 'one',
            'module' => 'DM_VehiclesInventory',
            'bean_name' => 'DM_VehiclesInventory',
            'source' => 'non-db',
            'vname' => 'LBL_VEHICLE',
        ),
        
        'service_advisor_link' => array(
            'name' => 'service_advisor_link',
            'type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'vname' => 'LBL_SERVICE_ADVISOR',
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
            'name' => 'idx_dm_serviceorders_customer',
            'type' => 'index',
            'fields' => array('customer_id')
        ),
        array(
            'name' => 'idx_dm_serviceorders_vehicle',
            'type' => 'index',
            'fields' => array('vehicle_id')
        ),
        array(
            'name' => 'idx_dm_serviceorders_vin',
            'type' => 'index',
            'fields' => array('vin')
        ),
        array(
            'name' => 'idx_dm_serviceorders_ro_number',
            'type' => 'index',
            'fields' => array('service_order_number')
        ),
        array(
            'name' => 'idx_dm_serviceorders_status',
            'type' => 'index',
            'fields' => array('service_status')
        ),
        array(
            'name' => 'idx_dm_serviceorders_appointment',
            'type' => 'index',
            'fields' => array('appointment_date')
        ),
        array(
            'name' => 'idx_dm_serviceorders_assigned_user',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_dm_serviceorders_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        ),
    ),
);

// Add module to the global bean lists
VardefManager::createVardef('DM_ServiceOrders', 'DM_ServiceOrders', array('basic', 'assignable', 'security_groups'));
?>