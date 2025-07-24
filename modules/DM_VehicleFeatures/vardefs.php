<?php
/**
 * SuiteCRM Vehicle Features Module - Field Definitions
 * 
 * This file defines the database fields and relationships for the DM_VehicleFeatures module.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['DM_VehicleFeatures'] = array(
    'table' => 'dm_vehiclefeatures',
    'audited' => true,
    'activity_enabled' => false,
    'duplicate_merge' => true,
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'comment' => 'Unique identifier',
        ),
        
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => '255',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'disabled',
            'comment' => 'Feature name',
        ),
        
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Feature description',
            'rows' => 4,
            'cols' => 80,
        ),
        
        'category' => array(
            'name' => 'category',
            'vname' => 'LBL_CATEGORY',
            'type' => 'enum',
            'options' => 'dm_feature_category_list',
            'len' => 50,
            'required' => true,
            'default' => 'general',
            'comment' => 'Feature category (safety, performance, etc.)',
        ),
        
        'feature_type' => array(
            'name' => 'feature_type',
            'vname' => 'LBL_FEATURE_TYPE',
            'type' => 'enum',
            'options' => 'dm_feature_type_list',
            'len' => 50,
            'default' => 'standard',
            'comment' => 'Type of feature (standard, optional, etc.)',
        ),
        
        'vehicle_type' => array(
            'name' => 'vehicle_type',
            'vname' => 'LBL_VEHICLE_TYPE',
            'type' => 'enum',
            'options' => 'dm_vehicle_type_list',
            'len' => 50,
            'comment' => 'Applicable vehicle type (sedan, suv, truck, etc.)',
        ),
        
        'is_standard' => array(
            'name' => 'is_standard',
            'vname' => 'LBL_IS_STANDARD',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'Whether this is a standard feature',
        ),
        
        'is_active' => array(
            'name' => 'is_active',
            'vname' => 'LBL_IS_ACTIVE',
            'type' => 'bool',
            'default' => '1',
            'comment' => 'Whether this feature is active/available',
        ),
        
        'sort_order' => array(
            'name' => 'sort_order',
            'vname' => 'LBL_SORT_ORDER',
            'type' => 'int',
            'len' => '11',
            'default' => '999',
            'comment' => 'Sort order for display',
        ),
        
        'feature_code' => array(
            'name' => 'feature_code',
            'vname' => 'LBL_FEATURE_CODE',
            'type' => 'varchar',
            'len' => '50',
            'comment' => 'Unique feature code/identifier',
        ),
        
        'icon_class' => array(
            'name' => 'icon_class',
            'vname' => 'LBL_ICON_CLASS',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'CSS icon class for display',
        ),
        
        // Standard audit fields
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
            'dbType' => 'id',
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
            'id_name' => 'created_by',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'group' => 'created_by_name',
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
            'dbType' => 'id',
            'audited' => true,
            'comment' => 'User ID assigned to record',
            'duplicate_merge' => 'disabled',
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
            'duplicate_merge' => 'disabled',
        ),
        
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ),
        
        // Link fields for relationships
        'assigned_user_link' => array(
            'name' => 'assigned_user_link',
            'type' => 'link',
            'relationship' => 'dm_vehiclefeatures_assigned_user',
            'vname' => 'LBL_ASSIGNED_TO_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'duplicate_merge' => 'enabled',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'table' => 'users',
        ),
        
        'modified_user_link' => array(
            'name' => 'modified_user_link',
            'type' => 'link',
            'relationship' => 'dm_vehiclefeatures_modified_user',
            'vname' => 'LBL_MODIFIED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'duplicate_merge' => 'enabled',
            'readonly' => true,
        ),
        
        'created_by_link' => array(
            'name' => 'created_by_link',
            'type' => 'link',
            'relationship' => 'dm_vehiclefeatures_created_by',
            'vname' => 'LBL_CREATED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'duplicate_merge' => 'enabled',
            'readonly' => true,
        ),
    ),
    
    'relationships' => array(
        'dm_vehiclefeatures_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_VehicleFeatures',
            'rhs_table' => 'dm_vehiclefeatures',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'dm_vehiclefeatures_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_VehicleFeatures',
            'rhs_table' => 'dm_vehiclefeatures',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'dm_vehiclefeatures_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DM_VehicleFeatures',
            'rhs_table' => 'dm_vehiclefeatures',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ),
    ),
    
    'indices' => array(
        array(
            'name' => 'dm_vehiclefeaturespk',
            'type' => 'primary',
            'fields' => array('id'),
        ),
        array(
            'name' => 'idx_dm_vehiclefeatures_name',
            'type' => 'index',
            'fields' => array('name'),
        ),
        array(
            'name' => 'idx_dm_vehiclefeatures_category',
            'type' => 'index',
            'fields' => array('category'),
        ),
        array(
            'name' => 'idx_dm_vehiclefeatures_type',
            'type' => 'index',
            'fields' => array('feature_type'),
        ),
        array(
            'name' => 'idx_dm_vehiclefeatures_code',
            'type' => 'index',
            'fields' => array('feature_code'),
        ),
        array(
            'name' => 'idx_dm_vehiclefeatures_assigned',
            'type' => 'index',
            'fields' => array('assigned_user_id'),
        ),
        array(
            'name' => 'idx_dm_vehiclefeatures_deleted',
            'type' => 'index',
            'fields' => array('deleted'),
        ),
    ),
    
    'templates' => array(
        'basic' => 1,
        'assignable' => 1,
    ),
);

VardefManager::createVardef('DM_VehicleFeatures', 'DM_VehicleFeatures', array('basic', 'assignable'));
?> 