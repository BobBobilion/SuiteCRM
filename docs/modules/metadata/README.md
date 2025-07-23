# Module Metadata Directory

The metadata directory within each module contains configuration files that define how the module behaves, displays data, and interacts with the SuiteCRM framework.

## Standard Metadata Files

### View Definitions
- **detailviewdefs.php** - Defines the layout and fields displayed in the Detail (read-only) view
- **editviewdefs.php** - Defines the layout and fields displayed in the Edit view
- **listviewdefs.php** - Defines columns and fields shown in List views
- **quickcreatedefs.php** - Defines fields for the Quick Create forms
- **popupdefs.php** - Defines the layout for popup record selectors

### Search Configuration
- **searchdefs.php** - Defines basic and advanced search form layouts
- **SearchFields.php** - Defines search field types and operators

### Relationships & Subpanels
- **subpaneldefs.php** - Defines which related modules appear as subpanels
- **subpanels/** - Directory containing individual subpanel configurations
  - **default.php** - Default subpanel layout
  - **For[Module].php** - Custom subpanel layouts for specific relationships

### Access Control
- **acldefs.php** - Defines module-specific access control rules

### Studio Configuration
- **studio.php** - Controls which elements can be customized in Studio

### Additional Configuration
- **metafiles.php** - Maps metadata files to specific actions
- **additionalDetails.php** - Defines hover-over preview information
- **fieldGroups.php** - Groups related fields together

## File Structure

Each metadata file typically returns a PHP array with specific structure:

### Example detailviewdefs.php structure:
```php
$viewdefs['ModuleName']['DetailView'] = array(
    'templateMeta' => array(...),
    'panels' => array(
        'panel_name' => array(
            array('field1', 'field2'),
            array('field3', 'field4'),
        )
    )
);
```

### Example listviewdefs.php structure:
```php
$listViewDefs['ModuleName'] = array(
    'FIELD_NAME' => array(
        'type' => 'field_type',
        'label' => 'LBL_FIELD_LABEL',
        'width' => '10%',
        'default' => true,
    ),
    // More field definitions...
);
```

## Customization
- Metadata files can be customized through Studio or by creating custom versions
- Custom metadata files are stored in custom/modules/[ModuleName]/metadata/
- Custom files override the default module metadata

## Purpose
Metadata files provide a declarative way to:
- Control UI layouts without modifying code
- Define field visibility and positioning
- Configure search functionality
- Manage module relationships
- Enable administrative customization through Studio

This separation of configuration from code allows for easier upgrades and customization management.