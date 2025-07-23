# SuiteCRM Project Documentation

## Overview

SuiteCRM is an open-source Customer Relationship Management (CRM) system built on PHP. This documentation provides a comprehensive overview of the project's structure, containing approximately 800,000 lines of code organized into a modular, extensible architecture.

## Project Statistics
- **Total Lines of Code**: ~800,000
- **Primary Language**: PHP
- **Frontend Technologies**: JavaScript, jQuery, Smarty Templates
- **Database**: MySQL/MariaDB
- **Architecture**: MVC (Model-View-Controller)

## Documentation Structure

This documentation is organized in a hierarchical tree structure, mirroring the project layout:

```
docs/
├── README.md                    # This file - comprehensive project overview
├── vendor/                      # Third-party dependencies documentation
│   ├── README.md
│   └── rector/
│       └── rector/
│           ├── README.md
│           └── vendor/
│               ├── README.md
│               └── doctrine/
│                   └── inflector/
│                       ├── README.md
│                       └── lib/Doctrine/Inflector/
│                           ├── README.md
│                           └── Rules/
│                               ├── README.md
│                               └── English/README.md
├── modules/                     # Business modules documentation
│   ├── README.md
│   ├── Accounts/
│   │   └── README.md
│   └── metadata/
│       └── README.md
├── include/                     # Core framework documentation
│   └── README.md
├── Api/                         # REST API documentation
│   └── README.md
├── themes/                      # Theme system documentation
│   └── README.md
├── custom/                      # Customization framework documentation
│   └── README.md
└── other-directories/           # Additional directories documentation
    └── README.md
```

## Directory Structure Overview

### Core Application Directories

#### /modules/
The heart of SuiteCRM's functionality, containing 100+ business modules:
- **CRM Modules**: Accounts, Contacts, Leads, Opportunities
- **Communication**: Emails, Calls, Meetings, Tasks
- **Sales**: Quotes, Contracts, Invoices, Products
- **Marketing**: Campaigns, Target Lists, Email Marketing
- **Support**: Cases, Knowledge Base, Portal
- **Projects**: Project Management, Tasks, Templates
- **Workflow**: Automated processes and business logic
- **Reports**: Advanced reporting and analytics

[Detailed documentation](./modules/)

#### /include/
Core framework components and utilities:
- **MVC Framework**: Controllers, Views, Models
- **UI Components**: ListView, DetailView, EditView, SearchForm
- **JavaScript Libraries**: jQuery, YUI, TinyMCE
- **Database Layer**: Abstraction, queries, relationships
- **Utilities**: Caching, logging, security, email

[Detailed documentation](./include/)

#### /Api/
RESTful API implementation (V8):
- **OAuth 2.0 Authentication**
- **JSON:API Compliant**
- **CRUD Operations**
- **Relationship Management**
- **Metadata Access**

[Detailed documentation](./Api/)

### Customization & Extensions

#### /custom/
Upgrade-safe customizations:
- **Field Customizations**: Custom fields added via Studio
- **Logic Hooks**: Event-driven programming
- **Layout Modifications**: View customizations
- **Module Extensions**: Additional functionality

[Detailed documentation](./custom/)

#### /themes/
Visual presentation layer:
- **SuiteP**: Modern responsive theme
- **Default**: Legacy theme support
- **Customizable**: Colors, fonts, layouts

[Detailed documentation](./themes/)

### Dependencies & Libraries

#### /vendor/
Third-party packages (Composer-managed):
- **Frameworks**: Symfony components, Slim
- **Utilities**: Monolog, Carbon, PHPMailer
- **Security**: OAuth, JWT, HTMLPurifier
- **Development**: PHPUnit, Rector, PHPStan

[Detailed documentation](./vendor/)

### System Directories

#### /cache/
Temporary files and performance optimization

#### /data/
Core data access layer and SugarBean classes

#### /upload/
User-uploaded files and documents

#### /service/
Legacy web service endpoints (SOAP/REST)

[Detailed documentation](./other-directories/)

## Architecture Patterns

### MVC Implementation
```
Request → index.php → Controller → Model → View → Response
                          ↓          ↓        ↓
                      (Actions)  (Database) (Templates)
```

### Module Structure
Each module typically contains:
```
modules/[ModuleName]/
├── [ModuleName].php          # Bean class (Model)
├── controller.php            # Controller
├── views/                    # View controllers
├── metadata/                 # UI configurations
├── language/                 # Translations
└── Dashlets/                # Dashboard widgets
```

### Extension Framework
```
custom/Extension/
├── application/             # Global extensions
└── modules/[ModuleName]/   # Module-specific
    └── Ext/
        ├── Vardefs/        # Field definitions
        ├── Layoutdefs/     # Layout modifications
        └── LogicHooks/     # Event handlers
```

## Key Technologies

### Backend
- **PHP 7.x/8.x**: Core application language
- **MySQL/MariaDB**: Primary database
- **Composer**: Dependency management
- **Smarty**: Template engine
- **TCPDF**: PDF generation

### Frontend
- **jQuery**: DOM manipulation and AJAX
- **Bootstrap**: Responsive UI framework
- **TinyMCE**: Rich text editing
- **Chart.js**: Data visualization
- **LESS/CSS**: Styling

### Development Tools
- **PHPUnit**: Unit testing
- **Codeception**: Acceptance testing
- **PHPStan**: Static analysis
- **Rector**: Code modernization
- **PHP CS Fixer**: Code style

## Customization Approach

### Safe Customization
1. **Use Studio**: For fields and layouts
2. **Use Module Builder**: For new modules
3. **Use Custom Directory**: For code customizations
4. **Use Logic Hooks**: For business logic
5. **Use Extension Framework**: For core modifications

### Development Workflow
```
1. Development in custom/
2. Test in sandbox environment
3. Deploy via Module Loader
4. Clear cache/
5. Run Quick Repair & Rebuild
```

## Security Architecture

### Access Control
- **Role-based permissions**: Granular access control
- **Security Groups**: Team-based security
- **Field-level security**: Sensitive data protection
- **Row-level security**: Record ownership

### Data Protection
- **Input sanitization**: XSS prevention
- **SQL injection prevention**: Prepared statements
- **CSRF protection**: Token validation
- **Password policies**: Configurable rules

## Performance Optimization

### Caching Layers
1. **File Cache**: Default caching mechanism
2. **Redis/Memcached**: Advanced caching
3. **Database Query Cache**: SQL optimization
4. **Smarty Template Cache**: View rendering

### Best Practices
- Enable OpCode caching (OPcache)
- Optimize database indices
- Use ListView pagination
- Implement lazy loading
- Regular cache cleanup

## Integration Capabilities

### APIs
- **REST API V8**: Modern JSON:API
- **Legacy APIs**: V4.1 SOAP/REST
- **Web Services**: Custom endpoints

### External Systems
- **Email Integration**: IMAP/SMTP
- **Calendar Sync**: CalDAV support
- **Document Management**: WebDAV
- **Authentication**: LDAP/SAML/OAuth

## Maintenance & Administration

### Regular Tasks
- **System Backups**: Database and files
- **Cache Management**: Periodic cleanup
- **Log Rotation**: Prevent disk space issues
- **Security Updates**: Regular patching

### Monitoring
- **System Health**: Admin diagnostics
- **Performance Metrics**: Slow query logs
- **User Activity**: Tracker module
- **Error Logging**: suitecrm.log

## Development Guidelines

### Coding Standards
- Follow PSR-1/PSR-2/PSR-12
- Use meaningful variable names
- Document complex logic
- Write testable code

### Version Control
- Track custom/ directory
- Ignore cache/ and upload/
- Document all changes
- Use feature branches

## Upgrade Path

### Upgrade Safety
1. Custom directory preserved
2. Database migrations automated
3. Theme compatibility maintained
4. API backward compatibility

### Upgrade Process
1. Backup everything
2. Test in staging
3. Run upgrade wizard
4. Clear caches
5. Test customizations

## Conclusion

SuiteCRM's architecture provides a robust, scalable CRM platform with extensive customization capabilities. The modular design, combined with the upgrade-safe custom directory approach, enables organizations to tailor the system to their needs while maintaining upgradeability.

For detailed information about specific components, refer to the linked documentation for each directory.