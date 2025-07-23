# Modules Directory

The modules directory contains all the functional modules of SuiteCRM. Each module represents a specific business entity or functionality within the CRM system.

## Module Structure

Each module typically follows a standard directory structure:

### Core Files
- **[ModuleName].php** - Main bean class containing business logic
- **[ModuleName]_sugar.php** - SugarCRM generated base class
- **vardefs.php** - Field definitions, relationships, and indices
- **Menu.php** - Module menu items
- **controller.php** - Custom controller actions

### Standard Directories
- **Dashlets/** - Dashboard widgets specific to the module
- **language/** - Internationalization files
- **metadata/** - View definitions and configurations
- **views/** - MVC view controllers
- **tpls/** - Template files

## Core CRM Modules

### Contact Management
- **Accounts** - Company/organization records
- **Contacts** - Individual contact records
- **Leads** - Potential customer records
- **Prospects** - Target contacts for campaigns

### Sales & Opportunities
- **Opportunities** - Sales opportunity tracking
- **Quotes** (AOS_Quotes) - Sales quotations
- **Contracts** (AOS_Contracts) - Contract management
- **Invoices** (AOS_Invoices) - Invoice generation

### Customer Service
- **Cases** - Customer support tickets
- **Bugs** - Bug tracking
- **AOP_Case_Updates** - Portal case updates
- **AOP_Case_Events** - Case activity tracking

### Marketing
- **Campaigns** - Marketing campaign management
- **EmailMarketing** - Email campaign tools
- **ProspectLists** - Target lists for campaigns
- **CampaignLog** - Campaign activity tracking

### Project Management
- **Project** - Project tracking
- **ProjectTask** - Task management
- **AM_ProjectTemplates** - Project templates
- **AM_TaskTemplates** - Task templates

### Product Management
- **AOS_Products** - Product catalog
- **AOS_Product_Categories** - Product categorization
- **AOS_Products_Quotes** - Product line items

### Communication
- **Emails** - Email integration
- **Calls** - Phone call tracking
- **Meetings** - Meeting scheduling
- **Tasks** - Task management
- **Notes** - Note attachments

### Workflow & Automation
- **AOW_WorkFlow** - Workflow automation
- **AOW_Actions** - Workflow actions
- **AOW_Conditions** - Workflow conditions
- **AOW_Processed** - Workflow audit trail

### Reporting & Analytics
- **AOR_Reports** - Advanced reporting
- **AOR_Charts** - Chart generation
- **AOR_Conditions** - Report conditions
- **AOR_Fields** - Report fields
- **AOR_Scheduled_Reports** - Scheduled report delivery

### Knowledge Management
- **AOK_KnowledgeBase** - Knowledge base articles
- **AOK_Knowledge_Base_Categories** - KB categorization

### Events & Calendar
- **FP_events** - Event management
- **FP_Event_Locations** - Event locations
- **Calendar** - Calendar functionality
- **Reminders** - Event reminders

### Document Management
- **Documents** - Document storage
- **DocumentRevisions** - Version control

### Email Templates & Marketing
- **EmailTemplates** - Email template management
- **AOS_PDF_Templates** - PDF template creation

### Security & Access
- **ACL** - Access control lists
- **ACLActions** - ACL action definitions
- **ACLRoles** - Role-based permissions
- **SecurityGroups** - Security group management
- **Roles** - User role definitions

### Administration
- **Administration** - System administration
- **Configurator** - System configuration
- **Schedulers** - Cron job scheduling
- **SchedulersJobs** - Scheduled job execution
- **OutboundEmailAccounts** - Email account configuration

### User Management
- **Users** - User accounts
- **Employees** - Employee records
- **Teams** - Team organization

### System Modules
- **Import** - Data import functionality
- **MergeRecords** - Duplicate record merging
- **Audit** - Audit trail
- **Trackers** - User activity tracking
- **History** - Activity history
- **Activities** - Activity management

### Integration & APIs
- **EAPM** - External API management
- **Connectors** - Third-party connectors
- **OAuth2Clients** - OAuth2 client apps
- **OAuth2Tokens** - OAuth2 tokens
- **ExternalOAuthConnection** - External OAuth connections

### Search & Indexing
- **AOD_Index** - Search indexing
- **AOD_IndexEvent** - Index events
- **Home** - Global search and homepage

### Surveys
- **Surveys** - Survey creation
- **SurveyQuestions** - Survey questions
- **SurveyQuestionOptions** - Question options
- **SurveyResponses** - Response tracking
- **SurveyQuestionResponses** - Individual responses

### Mapping
- **jjwg_Maps** - Google Maps integration
- **jjwg_Markers** - Map markers
- **jjwg_Areas** - Geographic areas
- **jjwg_Address_Cache** - Address caching

### Business Hours
- **AOBH_BusinessHours** - Business hours configuration

### Development Tools
- **ModuleBuilder** - Module creation tool
- **Studio** - UI customization tool
- **DynamicFields** - Custom field management

### Other System Files
- **TableDictionary.php** - Database table definitions
- **BeanDictionary.php** - Bean class registry

## Module Naming Conventions
- Core modules use simple names (Accounts, Contacts)
- Advanced modules use prefixes:
  - AOS_ - Advanced OpenSales
  - AOR_ - Advanced OpenReports
  - AOW_ - Advanced OpenWorkflow
  - AOP_ - Advanced OpenPortal
  - AOK_ - Advanced OpenKnowledge
  - AOD_ - Advanced OpenDiscovery
  - FP_ - Full Page
  - jjwg_ - JJW Google Maps

Each module is self-contained with its own business logic, database schema, views, and language files, following the MVC pattern.