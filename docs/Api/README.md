# API Directory

The Api directory contains the RESTful API implementation for SuiteCRM, providing programmatic access to CRM data and functionality. The API follows RESTful principles and JSON:API specification.

## Directory Structure

### Core/
Core API framework components built on Slim Framework:

#### Config/
- **ApiConfig.php** - API configuration settings
- **slim.php** - Slim framework configuration

#### Loader/
- **ContainerLoader.php** - Dependency injection container setup
- **CustomLoader.php** - Custom module loading
- **RouteLoader.php** - API route registration

#### Resolver/
- **ConfigResolver.php** - Configuration resolution

#### app.php
Main API application bootstrap file

### V8/
Version 8 API implementation (current version):

#### BeanDecorator/
SugarBean decoration for API responses:
- **BeanManager.php** - Bean management and operations
- **BeanListRequest.php** - List request handling
- **BeanListResponse.php** - List response formatting

#### Config/
API configuration and dependency injection:
- **routes.php** - API route definitions
- **services.php** - Service container configuration
- **services/** - Service definition files:
  - beanAliases.php - Module name mappings
  - controllers.php - Controller services
  - factories.php - Factory services
  - globals.php - Global dependencies
  - helpers.php - Helper services
  - middlewares.php - Middleware services
  - params.php - Parameter services
  - services.php - Core services
  - validators.php - Validation services

#### Controller/
API endpoint controllers:
- **BaseController.php** - Base controller class
- **ListViewController.php** - List view endpoints
- **ListViewSearchController.php** - Search functionality
- **LogoutController.php** - Authentication logout
- **MetaController.php** - Metadata endpoints
- **ModuleController.php** - Module CRUD operations
- **RelationshipController.php** - Relationship management
- **UserController.php** - User operations
- **UserPreferencesController.php** - User preferences

#### Controller/InvocationStrategy/
- **SuiteInvocationStrategy.php** - Custom invocation handling

#### Factory/
Factory classes for creating services:
- **ParamsMiddlewareFactory.php** - Parameter middleware creation
- **ValidatorFactory.php** - Validator instantiation

#### Helper/
Utility helpers:
- **ModuleListProvider.php** - Available modules list
- **OsHelper.php** - Operating system utilities
- **VarDefHelper.php** - Variable definition helpers

#### JsonApi/
JSON:API specification implementation:

##### Helper/
- **AttributeObjectHelper.php** - Attribute formatting
- **PaginationObjectHelper.php** - Pagination handling
- **RelationshipObjectHelper.php** - Relationship formatting

##### Repository/
- **Filter.php** - Data filtering
- **Sort.php** - Data sorting

##### Response/
Response object builders:
- **AttributeResponse.php** - Attribute responses
- **DataResponse.php** - Data payload responses
- **DocumentResponse.php** - Document responses
- **ErrorResponse.php** - Error responses
- **LinksResponse.php** - Link responses
- **MetaResponse.php** - Metadata responses
- **PaginationResponse.php** - Pagination info
- **RelationshipResponse.php** - Relationship data

#### Middleware/
Request/response middleware:
- **ParamsMiddleware.php** - Parameter processing

#### OAuth2/
OAuth 2.0 authentication implementation:

##### Entity/
- **AccessTokenEntity.php** - Access tokens
- **ClientEntity.php** - OAuth clients
- **RefreshTokenEntity.php** - Refresh tokens
- **UserEntity.php** - User entities

##### Repository/
- **AccessTokenRepository.php** - Token storage
- **ClientRepository.php** - Client management
- **RefreshTokenRepository.php** - Refresh token storage
- **ScopeRepository.php** - OAuth scopes
- **UserRepository.php** - User authentication

#### Param/
Request parameter handling:
- **BaseParam.php** - Base parameter class
- **CreateModuleDataParams.php** - Create record params
- **CreateModuleParams.php** - Module creation params
- **CreateRelationshipParams.php** - Relationship params
- **DeleteModuleParams.php** - Delete params
- **GetModuleParams.php** - Retrieve params
- **ListViewSearchParams.php** - Search params
- **UpdateModuleParams.php** - Update params
- And more parameter classes...

#### Param/Options/
Parameter options:
- **Attributes.php** - Attribute selection
- **Fields.php** - Field filtering
- **Filter.php** - Query filters
- **Page.php** - Pagination
- **Sort.php** - Sorting options

#### Service/
Business logic services:
- **ListViewService.php** - List operations
- **ListViewSearchService.php** - Search operations
- **LogoutService.php** - Logout handling
- **MetaService.php** - Metadata operations
- **ModuleService.php** - Module operations
- **RelationshipService.php** - Relationship handling
- **UserPreferencesService.php** - Preference management
- **UserService.php** - User operations

### docs/
API documentation:

#### postman/
- **SalesAgility.postman_collection.json** - Postman collection for API testing

#### swagger/
- **swagger.json** - OpenAPI/Swagger specification

### index.php
API entry point that bootstraps the application

## Key Features

1. **RESTful Design** - Standard HTTP methods (GET, POST, PATCH, DELETE)
2. **JSON:API Compliant** - Follows JSON:API specification for consistent responses
3. **OAuth 2.0** - Secure authentication using OAuth 2.0
4. **Modular Architecture** - Clean separation of concerns
5. **Dependency Injection** - Service container for dependency management
6. **Middleware Support** - Request/response processing pipeline
7. **Comprehensive Documentation** - Swagger/OpenAPI specification

## API Endpoints

Common endpoints include:
- `/Api/V8/module` - Module operations
- `/Api/V8/module/{module}` - Specific module CRUD
- `/Api/V8/module/{module}/relationships/{link}` - Relationships
- `/Api/V8/meta/modules` - Module metadata
- `/Api/V8/meta/fields/{module}` - Field definitions
- `/Api/V8/logout` - User logout

## Authentication

The API uses OAuth 2.0 for authentication with:
- Client credentials grant
- Password grant
- Refresh token support

This provides secure, token-based access to the CRM data through a modern RESTful interface.