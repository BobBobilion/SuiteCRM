# Accounts Module

The Accounts module manages company/organization records in SuiteCRM. It serves as a central entity that can be related to contacts, opportunities, cases, and other business records.

## Core Files

### Main Business Logic
- **Account.php** - Core Account bean class that extends Company base class, contains business logic, relationships, and data handling
- **Account.js** - Client-side JavaScript functionality for Account-specific UI behaviors
- **AccountFormBase.php** - Base form handling class for Account create/edit operations
- **vardefs.php** - Variable definitions including fields, relationships, indices, and module configuration

### Controllers & Actions
- **Menu.php** - Module menu definition and navigation items
- **Save.php** - Custom save logic and data processing
- **ShowDuplicates.php** - Duplicate detection and merge functionality
- **Popup_picker.html** - Template for popup record selector

### List View Components
- **AccountsListViewSmarty.php** - Custom list view logic and data processing
- **AccountsQuickCreate.php** - Quick create form functionality

### Integration Hooks
- **AccountsJjwg_MapsLogicHook.php** - Logic hook for JJW Google Maps integration

## Directory Structure

### Dashlets/
Dashboard widgets for Accounts:
- **MyAccountsDashlet/** - "My Accounts" dashboard widget showing user's accounts

### language/
Language files for internationalization:
- **en_us.lang.php** - English language strings and labels

### metadata/
Module configuration and view definitions:
- **detailviewdefs.php** - Detail view layout configuration
- **editviewdefs.php** - Edit view layout configuration
- **listviewdefs.php** - List view column configuration
- **searchdefs.php** - Search form configuration
- **quickcreatedefs.php** - Quick create form configuration
- **popupdefs.php** - Popup selector configuration
- **SearchFields.php** - Search field definitions
- **acldefs.php** - Access control list definitions
- **additionalDetails.php** - Hover detail information
- **fieldGroups.php** - Field grouping configuration
- **metafiles.php** - Metadata file mappings
- **studio.php** - Studio customization settings
- **subpaneldefs.php** - Subpanel relationship definitions

### metadata/subpanels/
Subpanel configurations for related modules:
- **default.php** - Default subpanel layout
- **ForEmails.php** - Email relationship subpanel
- **ForProspectLists.php** - Target list relationship subpanel

### tpls/
Smarty template files:
- **QuickCreate.tpl** - Quick create form template

### views/
MVC view classes:
- **view.detail.php** - Detail view controller
- **view.edit.php** - Edit view controller  
- **view.list.php** - List view controller

## Key Features
- Company/organization management
- Contact relationships
- Opportunity tracking
- Case associations
- Email integration
- Address management
- Industry categorization
- Revenue tracking
- Parent/subsidiary relationships

## Field Arrays
The **field_arrays.php** file defines various field configurations and arrays used throughout the module for different purposes.