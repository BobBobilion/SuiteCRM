# Other Important Directories

This document covers the remaining essential directories in SuiteCRM that support various system functions.

## cache/
Temporary file storage for performance optimization:

### Purpose
- Stores compiled templates
- Caches module data
- Maintains performance data
- Temporary file processing

### Subdirectories
- **dashlets/** - Cached dashlet data
- **htmlclean/** - HTML purification cache
- **images/** - Image cache
- **include/** - Cached include files
- **javascript/** - Combined JavaScript files
- **jsLanguage/** - JavaScript language files
- **layout/** - Layout caches
- **modules/** - Module-specific caches
- **themes/** - Theme compilation cache

### Important Notes
- Can be safely cleared for troubleshooting
- Automatically regenerated
- Not included in version control

## data/
SugarBean base classes and data layer:

### Key Files
- **BeanFactory.php** - Bean instantiation
- **SugarBean.php** - Base data object class
- **SugarACL.php** - Access control implementation
- **Relationships/** - Relationship handling classes

### Purpose
- Core data access layer
- Object-relational mapping
- Relationship management
- ACL enforcement

## install/
Installation and upgrade scripts:

### Contents
- Installation wizard files
- Database schema files
- Initial data seeds
- Upgrade scripts
- System requirements checks

### Security
- Should be removed/protected after installation
- Contains sensitive setup scripts

## jssource/
JavaScript source files:

### Purpose
- Unminified JavaScript sources
- Development versions of JS files
- Source maps for debugging

### Contents
- Core JavaScript libraries
- Module-specific scripts
- UI component sources

## lib/
Third-party libraries not managed by Composer:

### Common Libraries
- Search implementations
- Parser libraries
- Utility classes
- Legacy dependencies

## metadata/
Global metadata definitions:

### Contents
- Relationship definitions
- Field type definitions
- Studio configurations
- Module templates

## ModuleInstall/
Module installation framework:

### Purpose
- Handles module package installation
- Module upgrade management
- Package validation
- Dependency checking

## service/
Web service endpoints:

### Services
- **v2/** - Version 2 REST API (legacy)
- **v3/** - Version 3 REST API (legacy)
- **v4/** - Version 4 REST API (legacy)
- **v4_1/** - Version 4.1 REST API (current legacy)

### Purpose
- SOAP/REST service endpoints
- Mobile app integration
- Third-party integrations

## soap/
SOAP web service implementation:

### Contents
- WSDL definitions
- SOAP server implementation
- Service method definitions

## tests/
Testing framework and test suites:

### Structure
- **unit/** - Unit tests
- **integration/** - Integration tests
- **acceptance/** - Acceptance tests
- **fixtures/** - Test data

### Frameworks
- PHPUnit tests
- Codeception tests
- Testing utilities

## upload/
File upload directory:

### Purpose
- Document storage
- Email attachments
- Import files
- User uploads

### Security
- Protected directory
- Files accessed through SuiteCRM
- Not directly web accessible

## XTemplate/
Legacy template engine:

### Purpose
- Backward compatibility
- Legacy module support
- Alternative to Smarty

## Zend/
Zend Framework components:

### Usage
- OAuth implementation
- Various utility classes
- Legacy framework components

## build/
Build artifacts and tools:

### Contents
- Build scripts
- Deployment tools
- Development utilities

## Root Configuration Files

### .htaccess
Apache configuration:
- URL rewriting rules
- Security settings
- Performance optimizations

### composer.json
PHP dependency management:
- Package requirements
- Autoload configuration
- Scripts and hooks

### config.php
Main configuration file:
- Database settings
- System preferences
- Security configurations

### config_override.php
Configuration overrides:
- Environment-specific settings
- Development/production switches

### index.php
Application entry point:
- Bootstrap process
- Request routing
- Environment setup

### suitecrm.log
System log file:
- Error logging
- Debug information
- Audit trail

## Development Files

### .env files
Environment configuration:
- Development settings
- API keys
- Local overrides

### package.json
JavaScript dependencies:
- Node.js packages
- Build tools
- Development dependencies

## Directory Permissions

Important directories requiring write permissions:
- **cache/** - Web server writable
- **custom/** - Web server writable
- **data/** - Web server writable
- **modules/** - Web server writable
- **upload/** - Web server writable
- **themes/** - Web server writable

## Maintenance

### Regular Cleanup
- Clear cache/ periodically
- Archive old logs
- Remove orphaned uploads
- Clean temporary files

### Backup Priorities
Critical directories for backup:
1. **custom/** - All customizations
2. **upload/** - User files
3. **config.php** - Configuration
4. Database - Most critical

These directories work together to provide the complete SuiteCRM functionality, from core operations to customizations, caching, and external integrations.