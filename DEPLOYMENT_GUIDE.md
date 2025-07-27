# SuiteCRM Dealership Management Modules - Deployment Guide

**Version:** 1.0  
**Date:** 2025-07-27  
**Target System:** SuiteCRM 7.x with Dealership Management Extensions  

## Table of Contents

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Installation Steps](#installation-steps)
3. [Configuration Requirements](#configuration-requirements)
4. [Testing Procedures](#testing-procedures)
5. [User Training Guidelines](#user-training-guidelines)
6. [Troubleshooting](#troubleshooting)
7. [Post-Deployment Validation](#post-deployment-validation)
8. [Rollback Procedures](#rollback-procedures)

---

## Pre-Deployment Checklist

### **System Requirements Verification**

#### ✅ **PHP Environment:**
```bash
# Verify PHP version (7.4+ required)
php -v

# Check required extensions
php -m | grep -E "(curl|json|mbstring|mysql|zip)"

# Verify TCPDF library exists
ls vendor/tecnickcom/tcpdf/tcpdf.php
```

#### ✅ **Database Requirements:**
```sql
-- Verify MySQL/MariaDB version
SELECT VERSION();

-- Check available storage space (minimum 100MB recommended)
SELECT 
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'Database Size (MB)'
FROM information_schema.tables 
WHERE table_schema = 'your_suitecrm_database';
```

#### ✅ **SuiteCRM Environment:**
- [ ] SuiteCRM version 7.10 or higher
- [ ] Admin access to SuiteCRM instance
- [ ] Existing modules operational: DM_FIDeals, DM_VehiclesInventory, AutoInventory
- [ ] Write permissions on SuiteCRM directories
- [ ] Apache/Nginx web server configured properly

#### ✅ **Backup Requirements:**
```bash
# Create full database backup
mysqldump -u username -p database_name > suitecrm_backup_$(date +%Y%m%d_%H%M%S).sql

# Create file system backup
tar -czf suitecrm_files_backup_$(date +%Y%m%d_%H%M%S).tar.gz /path/to/suitecrm/
```

#### ✅ **Dependencies Check:**
- [ ] TCPDF library installed
- [ ] cURL extension enabled
- [ ] JSON extension enabled
- [ ] MySQL/MariaDB connection stable
- [ ] Sufficient disk space (minimum 500MB free)

---

## Installation Steps

### **Phase 1: Core Module Installation**

#### **Step 1: File Deployment**
```bash
# Navigate to SuiteCRM root directory
cd /path/to/suitecrm/

# Verify all module files are present
ls -la modules/DM_*

# Expected modules:
# - DM_DealDocuments
# - DM_LeadAttribution  
# - DM_ServiceOrders
# - DM_PartsInventory
# - DM_ServiceHistory
```

#### **Step 2: Permission Configuration**
```bash
# Set proper permissions for module directories
find modules/DM_* -type f -exec chmod 644 {} \;
find modules/DM_* -type d -exec chmod 755 {} \;

# Set permissions for custom extensions
find custom/Extension/ -type f -exec chmod 644 {} \;
find custom/Extension/ -type d -exec chmod 755 {} \;
```

#### **Step 3: Quick Repair and Rebuild**
1. **Access Admin Panel:**
   - Navigate to `Admin > Repair`
   - Click "Quick Repair and Rebuild"

2. **Execute Repairs:**
   ```
   ✅ Rebuild Extensions
   ✅ Rebuild Relationships
   ✅ Rebuild Vardefs
   ✅ Clear Theme Cache
   ✅ Rebuild Language Files
   ```

3. **Verify Database Changes:**
   ```sql
   -- Check if new tables were created
   SHOW TABLES LIKE 'dm_%';
   
   -- Expected tables:
   -- dm_dealdocuments
   -- dm_leadattribution
   -- dm_serviceorders
   -- dm_partsinventory
   -- dm_servicehistory
   ```

#### **Step 4: Module Activation**
1. **Admin Panel Navigation:**
   - Go to `Admin > Module Loader`
   - Verify modules appear in available modules list

2. **Module Configuration:**
   - Navigate to `Admin > Display Modules and Subpanels`
   - Enable all DM_ modules
   - Set appropriate tab groupings

### **Phase 2: Database Schema Validation**

#### **Step 1: Table Structure Verification**
```sql
-- Verify DM_DealDocuments table
DESCRIBE dm_dealdocuments;

-- Key fields to verify:
-- - id (char 36)
-- - name (varchar 255)
-- - deal_id (char 36)
-- - customer_id (char 36)
-- - document_type (varchar 100)
-- - document_status (varchar 50)
-- - pdf_content (longtext)
-- - signature_data (text)

-- Verify indexes
SHOW INDEX FROM dm_dealdocuments;
```

#### **Step 2: Relationship Validation**
```sql
-- Check relationship tables exist
SELECT * FROM relationships WHERE lhs_module LIKE 'DM_%' OR rhs_module LIKE 'DM_%';

-- Verify foreign key constraints are working
-- (Test with sample data insertion)
```

### **Phase 3: Application-Level Configuration**

#### **Step 1: Language Files Compilation**
```bash
# Force rebuild of language files
rm -rf cache/language/*
# Access admin panel and run Quick Repair again
```

#### **Step 2: Cache Clearing**
```bash
# Clear all caches
rm -rf cache/modules/*
rm -rf cache/themes/*
rm -rf cache/jsLanguage/*
rm -rf cache/smarty/templates_c/*
```

#### **Step 3: Menu Configuration**
1. **Verify Module Menus:**
   - Check each DM module appears in navigation
   - Verify "Create", "List", "Import" options available
   - Test navigation links functionality

---

## Configuration Requirements

### **1. DM_DealDocuments Configuration**

#### **PDF Generation Setup:**
```php
// In custom/modules/DM_DealDocuments/config.php (create if doesn't exist)
<?php
$config['pdf'] = array(
    'tcpdf_path' => 'vendor/tecnickcom/tcpdf/tcpdf.php',
    'default_font' => 'helvetica',
    'default_font_size' => 10,
    'page_orientation' => 'P', // Portrait
    'page_format' => 'LETTER',
    'margins' => array(15, 15, 15, 15), // top, right, bottom, left
);
?>
```

#### **Document Templates:**
```php
// Configure available document types
$config['document_types'] = array(
    'Purchase Agreement' => 'purchase_agreement_template.php',
    'Finance Contract' => 'finance_contract_template.php',
    'Warranty Contract' => 'warranty_contract_template.php',
    'GAP Insurance' => 'gap_insurance_template.php',
);
```

### **2. DM_LeadAttribution Configuration**

#### **Google Analytics Setup:**
```php
// In custom/modules/DM_LeadAttribution/config.php
<?php
$config['google_analytics'] = array(
    'enabled' => true,
    'property_id' => 'GA_PROPERTY_ID', // Replace with actual GA4 property ID
    'api_key' => 'YOUR_GA4_API_KEY',   // Replace with actual API key
    'measurement_id' => 'G-XXXXXXXXXX', // Replace with actual measurement ID
);
?>
```

#### **Facebook Lead Ads Setup:**
```php
$config['facebook'] = array(
    'enabled' => true,
    'app_id' => 'YOUR_FB_APP_ID',
    'app_secret' => 'YOUR_FB_APP_SECRET',
    'access_token' => 'YOUR_FB_ACCESS_TOKEN',
    'webhook_verify_token' => 'YOUR_WEBHOOK_VERIFY_TOKEN',
);
?>
```

### **3. Service Modules Configuration**

#### **DM_ServiceOrders Settings:**
```php
// Service order workflow configuration
$config['service_orders'] = array(
    'default_labor_rate' => 150.00, // per hour
    'tax_rate' => 8.25, // percentage
    'warranty_period' => 90, // days
    'auto_notifications' => true,
);
```

#### **DM_PartsInventory Settings:**
```php
// Parts inventory configuration
$config['parts_inventory'] = array(
    'low_stock_threshold' => 5,
    'reorder_point_days' => 30,
    'auto_reorder_enabled' => false,
    'supplier_integration' => false,
);
```

### **4. Email Configuration**

#### **SMTP Settings for Notifications:**
```php
// In Admin > Email Settings
$sugar_config['mail_smtpserver'] = 'your.smtp.server.com';
$sugar_config['mail_smtpport'] = 587;
$sugar_config['mail_smtpuser'] = 'your-email@domain.com';
$sugar_config['mail_smtppass'] = 'your-email-password';
$sugar_config['mail_smtpauth_req'] = true;
$sugar_config['mail_smtpssl'] = 1; // For TLS
```

---

## Testing Procedures

### **Phase 1: Smoke Testing**

#### **Basic Module Access Test:**
```bash
# Test script to verify module accessibility
#!/bin/bash
echo "Testing module access..."

modules=("DM_DealDocuments" "DM_LeadAttribution" "DM_ServiceOrders" "DM_PartsInventory" "DM_ServiceHistory")

for module in "${modules[@]}"; do
    echo "Testing $module..."
    curl -s "http://your-suitecrm-url/index.php?module=$module&action=index" | grep -q "error" 
    if [ $? -eq 1 ]; then
        echo "✅ $module accessible"
    else
        echo "❌ $module has errors"
    fi
done
```

#### **Database Connectivity Test:**
```sql
-- Test basic CRUD operations for each module
INSERT INTO dm_dealdocuments (id, name, document_type, document_status, date_entered) 
VALUES (UUID(), 'Test Document', 'Purchase Agreement', 'Draft', NOW());

SELECT * FROM dm_dealdocuments WHERE name = 'Test Document';

UPDATE dm_dealdocuments SET document_status = 'Generated' WHERE name = 'Test Document';

DELETE FROM dm_dealdocuments WHERE name = 'Test Document';
```

### **Phase 2: Functional Testing**

#### **DM_DealDocuments Testing:**
1. **PDF Generation Test:**
   ```
   - Create new deal document
   - Select document type "Purchase Agreement"
   - Link to existing F&I deal
   - Generate PDF
   - Verify PDF downloads correctly
   - Check PDF content accuracy
   ```

2. **Signature Capture Test:**
   ```
   - Generate PDF document
   - Open signature capture interface
   - Add test signature
   - Save signature data
   - Verify signature appears in document
   ```

#### **DM_LeadAttribution Testing:**
1. **UTM Tracking Test:**
   ```
   - Create test URL with UTM parameters
   - Access SuiteCRM via test URL
   - Create new lead
   - Verify UTM data captured in attribution record
   ```

2. **ROI Calculation Test:**
   ```
   - Create attribution record with cost and value
   - Verify ROI calculation accuracy
   - Test different attribution models
   ```

#### **Service Modules Testing:**
1. **Service Order Workflow:**
   ```
   - Create new service order
   - Add parts and labor
   - Progress through workflow states
   - Generate service history record
   - Verify inventory updates
   ```

### **Phase 3: Integration Testing**

#### **Module Relationship Testing:**
```sql
-- Test data flow between modules
-- Create test customer, vehicle, deal, and service records
-- Verify relationships work correctly
-- Test subpanel displays
-- Verify related record creation
```

#### **Performance Testing:**
```bash
# Load testing script
#!/bin/bash
echo "Performance testing..."

# Test with 100 concurrent users
ab -n 1000 -c 100 "http://your-suitecrm-url/index.php?module=DM_ServiceOrders&action=index"

# Monitor response times and error rates
```

---

## User Training Guidelines

### **Training Schedule (Recommended 2-week rollout):**

#### **Week 1: Core Users (Managers, Super Users)**
- **Day 1-2:** System overview and navigation
- **Day 3-4:** DM_DealDocuments and DM_LeadAttribution
- **Day 5:** Service modules overview

#### **Week 2: End Users (Sales Staff, Service Advisors)**
- **Day 1-2:** Basic module usage
- **Day 3-4:** Workflow processes
- **Day 5:** Q&A and hands-on practice

### **Training Materials:**

#### **1. Quick Start Guides:**
```
DM_DealDocuments:
- Creating documents
- Generating PDFs
- Capturing signatures
- Managing document workflow

DM_LeadAttribution:
- Understanding attribution models
- Viewing lead journey
- ROI analysis
- Campaign effectiveness

Service Modules:
- Creating service orders
- Managing parts inventory
- Tracking service history
- Customer communication
```

#### **2. Video Tutorials:**
- Module navigation (15 minutes)
- Document generation process (20 minutes)
- Service order workflow (25 minutes)
- Reporting and analytics (20 minutes)

#### **3. Hands-on Exercises:**
```
Exercise 1: Create and generate a purchase agreement
Exercise 2: Track a lead through attribution journey
Exercise 3: Process a complete service order
Exercise 4: Generate customer service history report
```

### **User Support Resources:**

#### **Documentation:**
- User manuals for each module
- FAQ documents
- Troubleshooting guides
- Best practices documentation

#### **Support Channels:**
- Help desk ticket system
- Internal user forum
- Weekly office hours
- Escalation procedures

---

## Troubleshooting

### **Common Issues and Solutions:**

#### **1. Module Not Appearing in Navigation**
```bash
# Solution:
1. Check Admin > Display Modules and Subpanels
2. Verify module is enabled
3. Clear browser cache
4. Run Quick Repair and Rebuild
5. Check user role permissions
```

#### **2. PDF Generation Failing**
```php
// Check TCPDF library
if (!file_exists('vendor/tecnickcom/tcpdf/tcpdf.php')) {
    echo "TCPDF library missing - install via Composer";
}

// Check memory limits
ini_set('memory_limit', '256M');

// Check file permissions
chmod 755 modules/DM_DealDocuments/tpls/
```

#### **3. Database Connection Errors**
```sql
-- Verify table existence
SHOW TABLES LIKE 'dm_%';

-- Check table structure
DESCRIBE dm_dealdocuments;

-- Verify relationships
SELECT * FROM relationships WHERE lhs_module = 'DM_DealDocuments';
```

#### **4. Attribution Data Not Capturing**
```javascript
// Debug UTM parameter capture
console.log(window.location.search);

// Check if parameters are being passed correctly
// Verify JavaScript is enabled
// Check browser console for errors
```

#### **5. Performance Issues**
```sql
-- Check for missing indexes
SHOW INDEX FROM dm_leadattribution;

-- Analyze slow queries
SHOW PROCESSLIST;

-- Optimize database
OPTIMIZE TABLE dm_dealdocuments;
```

### **Error Log Monitoring:**

#### **Key Log Files to Monitor:**
```bash
# SuiteCRM error logs
tail -f suitecrm.log

# PHP error logs  
tail -f /var/log/php/error.log

# Apache/Nginx error logs
tail -f /var/log/apache2/error.log

# MySQL error logs
tail -f /var/log/mysql/error.log
```

#### **Common Error Patterns:**
```
Pattern: "TCPDF Error"
Solution: Check TCPDF library installation

Pattern: "Relationship not found"
Solution: Run relationship rebuild

Pattern: "Permission denied"
Solution: Check file/directory permissions

Pattern: "Memory limit exceeded"
Solution: Increase PHP memory_limit
```

---

## Post-Deployment Validation

### **24-Hour Validation Checklist:**

#### **✅ System Stability:**
- [ ] No critical errors in logs
- [ ] All modules accessible
- [ ] Database performance stable
- [ ] User login/logout working
- [ ] Background processes running

#### **✅ Functionality Verification:**
- [ ] Document generation working
- [ ] Attribution tracking active
- [ ] Service orders processing
- [ ] Parts inventory updating
- [ ] Relationships functioning

#### **✅ Integration Verification:**
- [ ] Existing modules unaffected
- [ ] Data flow between modules
- [ ] Reporting functionality
- [ ] Email notifications working
- [ ] External API connections

### **1-Week Validation:**

#### **Performance Metrics:**
```sql
-- Monitor database performance
SELECT 
    table_name,
    table_rows,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema = 'your_database' 
AND table_name LIKE 'dm_%';
```

#### **User Feedback Collection:**
```
Survey Questions:
1. How easy is it to navigate the new modules? (1-5)
2. Are the new features meeting your needs? (Yes/No)
3. What issues have you encountered? (Open text)
4. What additional training do you need? (Open text)
5. Overall satisfaction with new modules? (1-5)
```

#### **Error Rate Analysis:**
```bash
# Analyze error patterns
grep -i "error" suitecrm.log | grep "DM_" | wc -l

# Check for specific module errors
grep -i "DM_DealDocuments" suitecrm.log | grep -i "error"
```

### **30-Day Review:**

#### **Success Metrics:**
- User adoption rate > 80%
- Error rate < 1% of transactions
- Performance within acceptable limits
- No critical bugs reported
- Training completion > 90%

#### **Optimization Opportunities:**
- Query performance optimization
- User interface improvements
- Additional automation features
- Extended reporting capabilities
- Mobile responsiveness enhancements

---

## Rollback Procedures

### **Emergency Rollback (if critical issues arise):**

#### **Step 1: Immediate Module Deactivation**
```bash
# Disable modules via admin panel
# Or directly in database:
UPDATE config SET value = 0 WHERE name = 'DM_DealDocuments_enabled';
UPDATE config SET value = 0 WHERE name = 'DM_LeadAttribution_enabled';
UPDATE config SET value = 0 WHERE name = 'DM_ServiceOrders_enabled';
UPDATE config SET value = 0 WHERE name = 'DM_PartsInventory_enabled';
UPDATE config SET value = 0 WHERE name = 'DM_ServiceHistory_enabled';
```

#### **Step 2: Database Rollback**
```bash
# Restore from backup (if necessary)
mysql -u username -p database_name < suitecrm_backup_YYYYMMDD_HHMMSS.sql
```

#### **Step 3: File System Rollback**
```bash
# Remove module files (if necessary)
rm -rf modules/DM_DealDocuments
rm -rf modules/DM_LeadAttribution
rm -rf modules/DM_ServiceOrders
rm -rf modules/DM_PartsInventory
rm -rf modules/DM_ServiceHistory

# Restore custom extensions
rm -rf custom/Extension/modules/DM_*
rm -rf custom/Extension/application/Ext/Include/DealershipManagement.php
```

#### **Step 4: System Cleanup**
```bash
# Clear all caches
rm -rf cache/*

# Run repair
# Access admin panel and perform Quick Repair and Rebuild
```

### **Partial Rollback (module-specific issues):**

#### **Individual Module Deactivation:**
```sql
-- Disable specific module
UPDATE config SET value = 0 WHERE name = 'ModuleName_enabled';

-- Hide from navigation
UPDATE modules SET visible = 0 WHERE module_name = 'ModuleName';
```

#### **Data Preservation:**
```sql
-- Backup module data before removal
CREATE TABLE dm_dealdocuments_backup AS SELECT * FROM dm_dealdocuments;
CREATE TABLE dm_leadattribution_backup AS SELECT * FROM dm_leadattribution;
-- etc.
```

---

## Support and Maintenance

### **Ongoing Maintenance Tasks:**

#### **Daily:**
- Monitor error logs
- Check system performance
- Verify backup completion
- Review user feedback

#### **Weekly:**
- Database optimization
- Performance analysis
- User training sessions
- Update documentation

#### **Monthly:**
- Security assessment
- Feature usage analysis
- System updates
- User satisfaction survey

### **Support Contacts:**

#### **Technical Support:**
- **Level 1:** Help Desk - tickets@company.com
- **Level 2:** System Admin - admin@company.com  
- **Level 3:** Development Team - dev@company.com

#### **Business Support:**
- **Module Training:** training@company.com
- **Process Questions:** business@company.com
- **Feature Requests:** features@company.com

---

## Conclusion

This deployment guide provides comprehensive procedures for implementing the SuiteCRM Dealership Management modules. Following these steps ensures a successful rollout with minimal disruption to existing operations.

### **Key Success Factors:**
1. **Thorough pre-deployment testing**
2. **Proper user training and support**
3. **Continuous monitoring and optimization**
4. **Clear escalation and rollback procedures**

### **Expected Benefits:**
- Streamlined document generation and management
- Enhanced lead attribution and ROI tracking
- Improved service operations efficiency
- Better customer service history tracking
- Integrated parts inventory management

For additional support or questions, refer to the technical documentation or contact the support team.

---

*Deployment Guide prepared by: Claude Code*  
*Date: 2025-07-27*  
*Version: 1.0*  
*Next Update: Post-deployment feedback incorporation*