# SuiteCRM Service & Parts Hub - Implementation Guide

## Overview

The Service & Parts Hub is a comprehensive three-module system designed for automotive dealership service operations within SuiteCRM. It provides complete management of service orders, parts inventory, and service history tracking.

## Modules Created

### 1. DM_ServiceOrders - Service Order Management
**Purpose**: Manage service appointments, repair orders, labor tracking, and customer service workflow.

**Key Features**:
- Auto-generated RO (Repair Order) numbers
- Customer and vehicle integration
- Appointment scheduling and promise time tracking
- Labor hours and rate calculation
- Parts integration and pricing
- Service advisor and technician assignment
- Automatic service history creation upon completion

**Database Table**: `dm_serviceorders`

**Key Fields**:
- `service_order_number` - Auto-generated unique RO number
- `customer_id` - Link to Accounts module
- `vehicle_id` - Link to vehicle inventory
- `vin` - Vehicle identification number
- `appointment_date` - Scheduled appointment
- `promise_time` - Promised completion time
- `service_advisor_id` - Assigned service advisor
- `technician_id` - Primary technician
- `service_type` - Maintenance/Repair/Warranty
- `service_status` - Scheduled/In-Progress/Complete/Picked-up
- `labor_hours` - Hours worked
- `labor_rate` - Hourly rate
- `labor_total` - Total labor charges
- `parts_total` - Total parts charges
- `tax_amount` - Sales tax
- `total_amount` - Grand total

### 2. DM_PartsInventory - Parts Inventory Management
**Purpose**: Manage automotive parts catalog, stock levels, pricing, and supplier relationships.

**Key Features**:
- Parts catalog with detailed specifications
- Real-time inventory tracking
- Automatic reorder point alerts
- Cost and retail price management
- Markup calculation
- Supplier relationship management
- Parts lookup functionality for service orders
- Low stock reporting
- Stock level monitoring and alerts

**Database Table**: `dm_partsinventory`

**Key Fields**:
- `part_number` - Manufacturer part number
- `manufacturer` - Part manufacturer
- `category` - Part category (Engine, Brake, etc.)
- `description` - Detailed part description
- `location` - Bin/shelf location
- `quantity_on_hand` - Current stock level
- `reorder_point` - Reorder threshold
- `cost` - Current cost
- `retail_price` - Customer price
- `supplier_id` - Primary supplier link
- `last_ordered_date` - Last order date

### 3. DM_ServiceHistory - Service History Tracking
**Purpose**: Maintain complete historical records of all service work performed on vehicles.

**Key Features**:
- Complete service timeline for vehicles
- Parts usage tracking (JSON format)
- Labor and cost history
- Technician performance tracking
- Service pattern analysis
- Automatic creation from completed service orders
- Comprehensive reporting capabilities

**Database Table**: `dm_servicehistory`

**Key Fields**:
- `vehicle_id` - Link to vehicle
- `service_order_id` - Link to originating service order
- `service_date` - Date service performed
- `mileage` - Odometer reading
- `service_type` - Type of service
- `services_performed` - Detailed work description
- `parts_used` - JSON array of parts used
- `labor_hours` - Labor time spent
- `total_cost` - Total service cost
- `technician_id` - Performing technician

## Module Relationships

### Service Orders → Customer & Vehicle
- **Customer Integration**: Links to Accounts module for customer information
- **Vehicle Integration**: Links to DM_VehiclesInventory or AutoInventory modules

### Service Orders → Service History
- **Automatic Creation**: Service history records are automatically created when service orders are completed
- **One-to-Many**: Each service order can generate multiple history records

### Service Orders ↔ Parts Inventory
- **Parts Lookup**: Service orders can search and select parts from inventory
- **Stock Updates**: Parts usage updates inventory quantities
- **Cost Integration**: Parts pricing flows into service order totals

### Service History → Vehicle Timeline
- **Complete History**: All service records for a vehicle are linked and trackable
- **Maintenance Patterns**: Historical data enables maintenance pattern analysis

## File Structure

Each module follows standard SuiteCRM patterns:

```
modules/
├── DM_ServiceOrders/
│   ├── DM_ServiceOrders.php           # Main Bean class
│   ├── vardefs.php                    # Database field definitions
│   ├── Menu.php                       # Module menu items
│   ├── controller.php                 # Custom controller actions
│   ├── language/
│   │   └── en_us.lang.php            # English language labels
│   ├── metadata/
│   │   ├── listviewdefs.php          # List view layout
│   │   ├── editviewdefs.php          # Edit view layout
│   │   ├── detailviewdefs.php        # Detail view layout
│   │   ├── searchdefs.php            # Search form layout
│   │   └── subpanels/
│   │       └── default.php           # Subpanel layout
│   ├── js/
│   │   └── DM_ServiceOrders.js       # Client-side functionality
│   └── views/                        # Custom view classes
│
├── DM_PartsInventory/
│   ├── DM_PartsInventory.php         # Main Bean class
│   ├── vardefs.php                   # Database field definitions
│   ├── Menu.php                      # Module menu items
│   ├── controller.php                # Custom controller actions
│   ├── language/
│   │   └── en_us.lang.php           # English language labels
│   ├── metadata/
│   │   ├── listviewdefs.php         # List view layout
│   │   ├── editviewdefs.php         # Edit view layout
│   │   ├── detailviewdefs.php       # Detail view layout
│   │   ├── searchdefs.php           # Search form layout
│   │   └── subpanels/
│   │       └── default.php          # Subpanel layout
│   ├── js/
│   │   └── DM_PartsInventory.js     # Client-side functionality
│   ├── views/
│   │   ├── view.parts_lookup.php    # Parts lookup popup
│   │   └── view.low_stock_report.php # Low stock report
│   └── tpls/
│       ├── parts_lookup.tpl         # Parts lookup template
│       └── low_stock_report.tpl     # Low stock report template
│
└── DM_ServiceHistory/
    ├── DM_ServiceHistory.php        # Main Bean class
    ├── vardefs.php                  # Database field definitions
    ├── Menu.php                     # Module menu items
    ├── controller.php               # Custom controller actions
    ├── language/
    │   └── en_us.lang.php          # English language labels
    ├── metadata/
    │   ├── listviewdefs.php        # List view layout
    │   ├── editviewdefs.php        # Edit view layout
    │   ├── detailviewdefs.php      # Detail view layout
    │   ├── searchdefs.php          # Search form layout
    │   └── subpanels/
    │       └── default.php         # Subpanel layout
    ├── js/
    │   └── DM_ServiceHistory.js    # Client-side functionality
    └── views/                      # Custom view classes
```

## Key Features Implementation

### 1. Auto-Generated Service Order Numbers
- Format: RO + YYMMDD + 3-digit sequence (e.g., RO250127001)
- Unique per day with automatic increment
- Implemented in `DM_ServiceOrders::generateServiceOrderNumber()`

### 2. Parts Lookup Integration
- Popup window for parts selection
- Real-time search by part number, description, manufacturer, category
- Stock level validation
- Price integration into service orders
- Template: `modules/DM_PartsInventory/tpls/parts_lookup.tpl`

### 3. Automatic Service History Creation
- Triggered when service order status changes to "Complete"
- Copies relevant data from service order
- Maintains complete vehicle service timeline
- Implemented in `DM_ServiceOrders::save()` method

### 4. Inventory Management
- Real-time stock tracking
- Automatic low stock alerts
- Reorder point monitoring
- Stock update capabilities via AJAX
- Markup percentage calculations

### 5. JavaScript Integration
- Client-side validation and calculations
- Real-time total calculations
- AJAX integration for dynamic updates
- Parts lookup integration
- Form validation and user feedback

## Installation Steps

### 1. File Deployment
Copy all module files to the SuiteCRM modules directory:
```bash
cp -r DM_ServiceOrders/ /path/to/suitecrm/modules/
cp -r DM_PartsInventory/ /path/to/suitecrm/modules/
cp -r DM_ServiceHistory/ /path/to/suitecrm/modules/
```

### 2. Database Setup
1. Navigate to Admin → Repair → Quick Repair and Rebuild
2. Execute any database changes displayed
3. Run a second Quick Repair to ensure all relationships are created

### 3. Module Registration
1. Go to Admin → Module Loader
2. If using a package, upload and install
3. Or manually register modules via Admin → Developer Tools → Module Builder

### 4. Role and Permission Setup
1. Navigate to Admin → Role Management
2. Assign appropriate permissions for each module:
   - Service advisors: Full access to ServiceOrders and ServiceHistory
   - Parts managers: Full access to PartsInventory
   - Technicians: View/Edit access to ServiceOrders and ServiceHistory
   - Managers: Full access to all modules and reports

### 5. Initial Configuration
1. **Parts Categories**: Verify/customize parts categories in Admin → Dropdown Editor
2. **Service Types**: Configure service types if needed
3. **User Assignments**: Set up service advisors and technicians in Users module
4. **Vehicle Integration**: Ensure vehicle inventory module is properly linked

## Integration Points

### Vehicle Inventory Integration
The system is designed to integrate with either:
- **DM_VehiclesInventory** (custom module)
- **AutoInventory** (existing module)

The vardefs include relationships for both, with fallback logic in the Bean classes.

### Customer Integration
- Direct integration with standard Accounts module
- Customer information flows into service orders
- Service history accessible from customer records via subpanels

### User Integration
- Service advisors and technicians are Users module records
- Assignment and tracking built into workflow
- Performance reporting by technician

## Reporting Capabilities

### 1. Low Stock Report
- **Location**: DM_PartsInventory → Low Stock Report
- **Features**: 
  - Parts below reorder point
  - Out of stock items
  - Estimated reorder values
  - Export to CSV
  - Print functionality

### 2. Service Summary Reports
- **Location**: DM_ServiceHistory → Reports
- **Available Reports**:
  - Service revenue by date range
  - Technician performance metrics
  - Common services performed
  - Vehicle service history
  - Parts usage analysis

### 3. Standard SuiteCRM Reports
All modules support standard SuiteCRM reporting:
- Custom report builder compatibility
- Advanced search and filtering
- Export capabilities
- Dashlet integration

## Customization Options

### 1. Field Additions
Add custom fields through Admin → Studio:
- Service order custom fields
- Parts inventory tracking fields
- Service history additional data

### 2. Workflow Automation
Implement SuiteCRM Workflow to:
- Auto-assign service orders based on criteria
- Send email notifications for low stock
- Create follow-up tasks for warranty services
- Generate automatic service reminders

### 3. Layout Modifications
Customize layouts via Admin → Studio:
- Rearrange fields and panels
- Add/remove fields from views
- Customize list view columns
- Modify search forms

### 4. Additional Integrations
The modular design supports additional integrations:
- External parts suppliers APIs
- Vehicle history services (Carfax, etc.)
- Payment processing systems
- Shop management software

## Best Practices

### 1. Data Entry Workflow
1. **Customer Setup**: Ensure customer exists in Accounts
2. **Vehicle Registration**: Link vehicles to customers
3. **Service Order Creation**: Create RO with complete information
4. **Parts Selection**: Use parts lookup for accurate inventory tracking
5. **Work Progress**: Update status as work progresses
6. **Completion**: Mark complete to auto-create service history

### 2. Inventory Management
1. **Regular Stock Checks**: Monitor low stock report weekly
2. **Accurate Costs**: Keep part costs current for accurate job costing
3. **Supplier Management**: Maintain supplier relationships in Accounts
4. **Location Tracking**: Use location field for efficient parts picking

### 3. Performance Optimization
1. **Database Indexing**: Key fields are indexed for performance
2. **Regular Cleanup**: Archive old service history as needed
3. **User Training**: Proper training reduces data entry errors
4. **Backup Strategy**: Regular backups of critical service data

## Troubleshooting

### Common Issues

1. **Missing Relationships**: Run Quick Repair twice if relationships don't appear
2. **JavaScript Errors**: Check browser console and verify file paths
3. **Permission Denied**: Verify role permissions for all three modules
4. **Parts Lookup Issues**: Ensure popup blockers allow the parts lookup window

### Debug Information
Enable debug logging in SuiteCRM to troubleshoot:
- Bean operations logged with 'DM_ServiceOrders:', 'DM_PartsInventory:', 'DM_ServiceHistory:' prefixes
- JavaScript console logging for client-side issues
- AJAX error handling in controllers

## Support and Maintenance

### Regular Maintenance Tasks
1. **Weekly**: Review low stock report and reorder parts
2. **Monthly**: Clean up completed service orders older than 1 year
3. **Quarterly**: Review and optimize database performance
4. **Annually**: Archive service history data older than 3 years

### Performance Monitoring
- Monitor database table sizes
- Check index performance on high-volume tables
- Review slow query logs for optimization opportunities

## Future Enhancements

The modular design allows for future enhancements:
1. **Mobile App Integration**: API endpoints for mobile service apps
2. **Advanced Scheduling**: Calendar integration for appointment booking
3. **Electronic Signatures**: Customer signature capture for work orders
4. **Photo Integration**: Attach photos to service orders and history
5. **Barcode Scanning**: Parts scanning for faster inventory management
6. **Integration APIs**: Connect with external shop management systems

## Conclusion

The Service & Parts Hub provides a comprehensive foundation for automotive service operations within SuiteCRM. The three-module system offers complete workflow management from initial service order creation through parts inventory management to comprehensive service history tracking.

The implementation follows SuiteCRM best practices and provides a solid foundation for future enhancements and customizations specific to your dealership's needs.