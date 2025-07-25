# F&I Deal Center Troubleshooting & FAQ

## Common Issues and Solutions

---

**Last Updated:** July 2025  
**Covers:** All F&I Deal Center Features  
**Difficulty:** Beginner to Advanced

---

## Table of Contents

1. [Common Technical Issues](#common-technical-issues)
2. [Feature-Specific Problems](#feature-specific-problems)
3. [Frequently Asked Questions](#frequently-asked-questions)
4. [Performance Issues](#performance-issues)
5. [Data and Calculation Problems](#data-and-calculation-problems)
6. [User Access and Permissions](#user-access-and-permissions)
7. [Getting Additional Help](#getting-additional-help)

---

## Common Technical Issues

### Payment Scenarios Button Not Working

**Problem:** Click "Payment Scenarios" but nothing happens

**Causes & Solutions:**
- **JavaScript Disabled**
  - Enable JavaScript in browser settings
  - Refresh the page after enabling
  
- **Browser Cache Issues**
  - Clear browser cache and cookies
  - Try Ctrl+F5 for hard refresh
  - Test in private/incognito window
  
- **Missing Data**
  - Ensure Sales Price is entered
  - Check that Interest Rate is set
  - Verify Amount Financed is calculated
  
- **Browser Compatibility**
  - Use Chrome, Firefox, or Edge (recommended)
  - Update browser to latest version
  - Disable ad blockers that might block scripts

**Quick Fix:**
```
1. Press F12 to open browser console
2. Look for red error messages
3. Refresh page if you see JavaScript errors
4. Contact support if errors persist
```

### "Action Not Found" Errors

**Problem:** Getting "There is no action by that name: [action]"

**Common Actions Affected:**
- reports
- analytics  
- calculator
- product_selection

**Solutions:**
1. **Clear System Cache**
   - Go to Admin → Repair → Quick Repair & Rebuild
   - Click "Execute" and wait for completion
   - Clear browser cache after system repair

2. **Check URL Structure**
   - Correct format: `index.php?module=DM_FIDeals&action=reports`
   - Verify module name is correct: `DM_FIDeals`
   - Check for typos in action names

3. **User Permissions**
   - Verify you have access to the module
   - Check with administrator about role permissions
   - Try logging out and back in

### Database Connection Errors

**Problem:** "Database failure. Please refer to suitecrm.log"

**Immediate Steps:**
1. **Check Database Status**
   - Verify database server is running
   - Test connection to MySQL/MariaDB
   - Check for disk space issues

2. **Review Error Logs**
   - Check `suitecrm.log` in root directory
   - Look for specific SQL error messages
   - Note timestamps of errors

3. **Common SQL Issues**
   - Missing table columns
   - Invalid data types
   - Constraint violations
   - Connection timeouts

**Contact Administrator If:**
- Multiple users affected
- Errors persist after page refresh
- Database server issues suspected

---

## Feature-Specific Problems

### Calculator Issues

#### Numbers Not Formatting Correctly

**Problem:** Currency fields show wrong format or won't accept input

**Solutions:**
- **Clear Field and Re-enter:** Delete all content, type numbers only
- **Use Decimal Point:** Enter 25000.00 not 25,000
- **Tab Out of Field:** Click elsewhere to trigger formatting
- **Check Regional Settings:** Ensure browser locale is set to US

#### Calculations Seem Wrong

**Problem:** Monthly payments or totals don't match expectations

**Verification Steps:**
1. **Check Interest Rate:** Should be annual rate (e.g., 6.5 not 0.065)
2. **Verify Term:** Ensure months not years (60 months not 5 years)
3. **Review Amount Financed:** 
   - Sales Price - Down Payment - Net Trade = Amount Financed
   - Add taxes and fees to amount financed
4. **Manual Calculation:** Use online calculator to verify

**Common Mistakes:**
- Entering monthly rate instead of annual APR
- Forgetting to include taxes/fees in financed amount
- Using wrong term length (years vs months)

### Lender Management Issues

#### Lender Fields Not Visible

**Problem:** Don't see lender-specific fields in Accounts module

**Solutions:**
1. **Check Account Type**
   - Edit account record
   - Set "Account Type" to "Lender"
   - Save and refresh page

2. **Field Permissions**
   - Contact administrator to check field-level security
   - Verify role has access to custom fields
   - Check if fields are hidden in layout

3. **Database Schema**
   - Run Quick Repair & Rebuild from Admin
   - Check if custom fields exist in database
   - Verify custom field definitions

#### Submission Forms Not Generating

**Problem:** "Submit to Lender" button doesn't create proper forms

**Troubleshooting:**
1. **Required Data Check**
   - Ensure deal has customer information
   - Verify vehicle and financial data is complete
   - Check that lender is selected

2. **Template Issues**
   - Check if submission templates exist
   - Verify template file permissions
   - Look for template syntax errors

### Product Selection Problems

#### Product Recommendations Not Showing

**Problem:** F&I Products button shows empty or error page

**Solutions:**
1. **Check Deal Data**
   - Ensure sales price is entered
   - Verify financing method is selected
   - Check that deal is saved

2. **Product Database**
   - Verify F&I products are configured in system
   - Check product pricing and availability
   - Ensure product catalog is up to date

#### Pricing Calculations Wrong

**Problem:** Product costs or profits don't calculate correctly

**Verification:**
1. **Check Product Setup**
   - Verify cost vs retail pricing
   - Check markup percentages
   - Review commission structures

2. **Deal Integration**
   - Ensure products are properly linked to deal
   - Verify vehicle price affects product pricing
   - Check finance method impacts on pricing

---

## Frequently Asked Questions

### General Usage

**Q: Can I use the calculator without creating a deal?**
A: Yes! Use the standalone Payment Calculator from the module menu. It works independently and can transfer data to a new deal when ready.

**Q: What's the difference between Amount Financed and Sales Price?**
A: Amount Financed = Sales Price - Down Payment - Net Trade Equity + Taxes/Fees. It's the actual amount being borrowed.

**Q: How do I calculate Net Trade Equity?**
A: Net Trade Equity = Trade Allowance - Trade Payoff. If the customer owes more than the trade is worth, this will be negative (negative equity).

**Q: Why do my payment calculations differ from other calculators?**
A: Ensure you're using the same:
- Annual percentage rate (APR)
- Term in months
- Amount financed (not sales price)
- Payment calculation method (standard loan formula)

### Deal Management

**Q: Can I edit a deal after it's been submitted to a lender?**
A: Yes, but changes should be communicated to the lender. The system tracks all modifications with timestamps.

**Q: How do I link a deal to vehicle inventory?**
A: Use the vehicle lookup field in the deal. If the vehicle isn't in inventory, you can still manually enter vehicle details.

**Q: What happens if I delete a deal?**
A: Deals are soft-deleted (marked as deleted but data preserved). Contact administrator if you need to recover a deleted deal.

**Q: Can multiple users work on the same deal?**
A: Yes, but only one user can edit at a time. The system will warn if another user is currently editing.

### Lender Integration

**Q: Do lenders receive deals automatically?**
A: No, this system uses manual submission. Generate the submission form and fax/email to lenders. Electronic integration is planned for future releases.

**Q: How do I track deal approval status?**
A: Use the Approval Tracking feature. Update status manually as you receive information from lenders.

**Q: Can I submit to multiple lenders?**
A: Yes, but track submissions carefully to avoid confusion. Update the primary lender field when you receive approval.

### F&I Products

**Q: How are product recommendations determined?**
A: Based on vehicle price, finance method, customer profile, and historical data. The system suggests products with highest profit potential and penetration rates.

**Q: Can I customize product pricing?**
A: Yes, F&I managers can adjust pricing within configured limits. Contact administrator to modify base pricing or commission structures.

**Q: How are product profits calculated?**
A: Profit = Selling Price - Cost. Commission = Profit × Commission Rate. The system tracks both dealer profit and sales commission.

### Reporting

**Q: How often are reports updated?**
A: Reports reflect real-time data. Analytics dashboard may have slight delays (typically under 5 minutes) due to caching.

**Q: Can I export report data?**
A: Yes, most reports support Excel export. PDF export is available for presentation-ready formats.

**Q: Who can access reports?**
A: Depends on user permissions. Typically sales staff see their own data, managers see department data, and executives see all data.

---

## Performance Issues

### System Running Slowly

**Common Causes:**
- **Large Data Sets:** Many deals or complex calculations
- **Browser Issues:** Too many tabs, low memory
- **Network Problems:** Slow internet connection
- **Server Load:** High usage by multiple users

**Solutions:**
1. **Browser Optimization**
   - Close unnecessary tabs
   - Clear cache and cookies
   - Restart browser
   - Use recommended browsers

2. **Data Management**
   - Archive old deals periodically
   - Limit large report date ranges
   - Use filters to reduce data sets

3. **Network Troubleshooting**
   - Test internet speed
   - Check for network congestion
   - Use wired connection if possible

### Timeouts and Errors

**Problem:** Pages timeout or show server errors

**Immediate Actions:**
1. Wait 30 seconds and try again
2. Check if other users experiencing issues
3. Try different features to isolate problem
4. Contact administrator if widespread

**Prevention:**
- Don't leave edit pages open for extended periods
- Save work frequently
- Use "Save" before running reports
- Avoid multiple complex operations simultaneously

---

## Data and Calculation Problems

### Incorrect Financial Calculations

**Verification Checklist:**
- [ ] Interest rate entered as annual percentage (6.5 not 0.065)
- [ ] Term entered in months (60 not 5)
- [ ] Amount financed includes taxes and fees
- [ ] Down payment and trade equity calculated correctly
- [ ] Payment calculation method matches lender requirements

**Common Errors:**
- **Monthly vs Annual Rate:** Always use annual APR
- **Term Confusion:** 5 years = 60 months
- **Missing Fees:** Include all costs in amount financed
- **Trade Calculation:** Payoff reduces net trade equity

### Data Not Saving

**Problem:** Changes don't persist after saving

**Troubleshooting:**
1. **Check Required Fields**
   - Ensure all mandatory fields are completed
   - Look for validation error messages
   - Fix any highlighted field errors

2. **Permission Issues**
   - Verify edit permissions for record
   - Check field-level security settings
   - Ensure not trying to edit locked records

3. **Browser Problems**
   - Disable browser extensions
   - Try different browser
   - Check for JavaScript errors in console

### Inconsistent Data Display

**Problem:** Same data shows differently in different views

**Common Causes:**
- **Caching Issues:** Clear browser and system cache
- **Permission Differences:** Different views may have different field access
- **Display Formatting:** Currency and number formatting variations
- **Data Timing:** Real-time vs cached data differences

---

## User Access and Permissions

### Can't Access F&I Deal Center

**Problem:** Module not visible or access denied

**Solutions:**
1. **Check User Role**
   - Contact administrator to verify module access
   - Ensure role includes F&I Deal Center permissions
   - Check if module is enabled for your user type

2. **License Issues**
   - Verify sufficient user licenses
   - Check module licensing status
   - Contact administrator about license allocation

### Limited Functionality

**Problem:** Can view but can't edit, or missing features

**Permission Levels:**
- **View Only:** Can see deals but not modify
- **Edit:** Can create and modify own deals
- **Manager:** Can access reports and all deals
- **Admin:** Full system access and configuration

**Solutions:**
- Request permission upgrade from administrator
- Verify role assignment matches job function
- Check temporary permission restrictions

### Can't Access Reports

**Problem:** Reports button doesn't work or shows access denied

**Troubleshooting:**
1. **User Role Check**
   - Reports typically require manager-level access
   - Some reports may be restricted to executives
   - Contact administrator about reporting permissions

2. **Module Configuration**
   - Verify reporting module is installed
   - Check that reports are properly configured
   - Ensure database access for reporting

---

## Getting Additional Help

### Before Contacting Support

**Information to Gather:**
1. **Error Details**
   - Exact error message (screenshot if possible)
   - Steps that led to the error
   - Time and date of issue
   - Browser and version being used

2. **System Information**
   - Your username and role
   - Deal or record numbers involved
   - What you were trying to accomplish
   - Whether others have same issue

3. **Troubleshooting Tried**
   - List what you've already attempted
   - Note if problem is intermittent or consistent
   - Mention if it worked before or never worked

### Internal Support Contacts

**For System Issues:**
- System Administrator: [Contact Info]
- Database Issues: [DBA Contact]
- User Training: [Training Contact]

**For Process Questions:**
- F&I Manager: [Manager Contact]
- Sales Manager: [Sales Contact]
- Accounting: [Accounting Contact]

### Creating Effective Support Tickets

**Include:**
- Clear problem description
- Steps to reproduce
- Expected vs actual behavior
- System information (browser, OS)
- Screenshots of errors
- Urgency level (critical, high, normal, low)

**Best Practices:**
- Be specific about what's not working
- Include any error messages verbatim
- Mention if problem affects productivity
- Suggest temporary workarounds if known

---

## Emergency Procedures

### System Down or Critical Errors

**Immediate Actions:**
1. **Check System Status**
   - Verify if issue affects all users
   - Test basic functionality (login, navigation)
   - Check server status with IT department

2. **Temporary Workarounds**
   - Use backup calculator (Excel, online tools)
   - Print/save critical data before fixes
   - Document manual processes used

3. **Communication**
   - Notify management of system issues
   - Inform customers of potential delays
   - Coordinate with IT for resolution timeline

### Data Recovery

**If Data is Lost:**
1. **Stop Additional Changes**
   - Don't continue editing affected records
   - Avoid creating new data until recovery
   - Document what was lost

2. **Contact Administrator Immediately**
   - Provide specific records affected
   - Include approximate time of data loss
   - Request database restoration if necessary

---

**Remember:** Most issues can be resolved with simple troubleshooting. When in doubt, clear your cache, refresh the page, and try again. For persistent problems, don't hesitate to contact support with detailed information about the issue.

---

*This troubleshooting guide is updated regularly. For the latest version, check the documentation folder or contact your system administrator.* 