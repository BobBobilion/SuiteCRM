# Include Directory

The include directory contains core framework components, utilities, and shared resources used throughout SuiteCRM. It provides the fundamental building blocks for the application's functionality.

## Major Components

### MVC Framework (MVC/)
The Model-View-Controller implementation:
- **Controller/** - Request routing and controller base classes
  - SugarController.php - Base controller class
  - ControllerFactory.php - Controller instantiation
  - entry_point_registry.php - URL endpoint definitions
- **View/** - View layer components
  - SugarView.php - Base view class
  - ViewFactory.php - View instantiation
  - views/ - Standard view implementations (list, detail, edit, etc.)
  - tpls/ - View templates
- **SugarApplication.php** - Main application handler
- **SugarModule.php** - Module loader

### User Interface Components

#### ListView/
List view rendering system:
- ListViewDisplay.php - Display logic
- ListViewData.php - Data retrieval
- ListViewSmarty.php - Smarty integration
- Templates for pagination, filtering, and bulk actions

#### DetailView/
Detail view components:
- DetailView.php - Read-only view logic
- DetailView2.php - Enhanced detail view
- Templates for layout rendering

#### EditView/
Edit form components:
- EditView.php - Form generation
- QuickCreate.php - Quick create forms
- SubpanelQuickCreate.php - Subpanel inline creation
- SugarVCR.php - Record navigation

#### SearchForm/
Search functionality:
- SearchForm.php - Basic search
- SearchForm2.php - Advanced search
- SugarSpot.php - Global search
- Search templates

#### SubPanel/
Related record display:
- SubPanel.php - Subpanel logic
- SubPanelTiles.php - Subpanel layout
- SubPanelDefinitions.php - Configuration

### Data & Database

#### database/
Database abstraction layer:
- DBManager.php - Base database class
- DBManagerFactory.php - Database driver selection
- MysqlManager.php, MysqliManager.php - MySQL drivers
- MssqlManager.php, SqlsrvManager.php - SQL Server drivers

#### SugarObjects/
Object-relational mapping:
- VardefManager.php - Field definition management
- SugarRegistry.php - Object registry
- SugarSession.php - Session management
- templates/ - Bean templates

### Field Handling (SugarFields/)
Custom field type implementations:
- **Fields/** - Individual field types
  - Base/ - Base field class
  - Address/, Bool/, Currency/, Datetime/, etc.
- **SugarFieldHandler.php** - Field type factory

### Communication

#### Emails & Email Integration
- **EmailInterface.php** - Email interface
- **SugarPHPMailer.php** - PHPMailer wrapper
- **Imap/** - IMAP email handling
- **OutboundEmail/** - Outbound email configuration

#### External APIs (externalAPI/)
- **Base/** - API base classes
- **ExternalAPIFactory.php** - API connector factory

### JavaScript & Client-Side

#### javascript/
Client-side functionality:
- **jquery/** - jQuery and plugins
- **yui/** - Yahoo UI library
- **sugar_3.js** - Core SuiteCRM JavaScript
- **ajaxUI.js** - AJAX user interface
- **quicksearch.js** - Quick search functionality
- **tiny_mce/** - TinyMCE editor

#### SugarTheme/
Theme management system:
- SugarTheme.php - Theme handling
- SugarThemeRegistry.php - Theme registration
- SugarSprites.php - CSS sprite generation

### Templates & Rendering

#### Smarty/
Smarty template engine integration:
- **plugins/** - Custom Smarty plugins and modifiers
- Sugar-specific template functions

#### Sugar_Smarty.php
Extended Smarty class with SuiteCRM functionality

### Utilities

#### utils/
General utility functions:
- **activity_utils.php** - Activity management
- **array_utils.php** - Array manipulation
- **db_utils.php** - Database utilities
- **file_utils.php** - File operations
- **layout_utils.php** - Layout helpers
- **logic_utils.php** - Business logic utilities
- **security_utils.php** - Security functions

#### Other Utilities
- **TimeDate.php** - Date/time handling
- **UploadFile.php** - File upload management
- **SugarCache/** - Caching system
- **SugarLogger/** - Logging framework

### Integration & Services

#### connectors/
Third-party service connectors:
- ConnectorFactory.php - Connector management
- sources/ - Connector implementations
- formatters/ - Data formatting

#### nusoap/
SOAP web services:
- nusoap.php - NuSOAP library
- SOAP client and server classes

#### Services/
Background services:
- Batch/ - Batch processing
- NormalizeRecords/ - Data normalization
- ScriptLoader/ - JavaScript loading

### Security & Access Control
- **SugarOAuthServer.php** - OAuth server
- **Zend_Oauth_Provider.php** - OAuth provider
- **HtmlSanitizer.php** - HTML cleaning
- **SugarSQLValidate.php** - SQL injection prevention

### Charting & Visualization
- **SugarCharts/** - Chart generation
  - Jit/ - JavaScript InfoVis Toolkit
  - Chart factory and renderers

### Import/Export
- **export_utils.php** - Data export utilities
- **Import functionality** - Handled by Import module

### Language & Localization
- **language/** - Core language files
- **Localization/** - Localization handling

### Other Components
- **Dashlets/** - Dashboard widget framework
- **MySugar/** - Personal dashboard
- **tcpdf/** - PDF generation
- **fonts/** - Font files for PDF
- **images/** - System images and icons

## Key Files
- **entryPoint.php** - Application entry point handler
- **utils.php** - Core utility functions
- **modules.php** - Module definitions
- **clean.php** - Input sanitization

The include directory serves as the foundation layer, providing essential services, utilities, and frameworks that modules build upon to deliver CRM functionality.