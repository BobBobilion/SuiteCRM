# F&I Deal Center User Guide

## Professional Automotive Finance & Insurance Management System

---

**Version:** 1.0  
**Last Updated:** July 2025  
**System Status:** Production Ready - All Features Operational

---

## Table of Contents

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [Deal Creation & Management](#deal-creation--management)
4. [Payment Calculator Tools](#payment-calculator-tools)
5. [Lender Management](#lender-management)
6. [F&I Product Sales](#fi-product-sales)
7. [Reports & Analytics](#reports--analytics)
8. [Advanced Features](#advanced-features)
9. [Troubleshooting](#troubleshooting)

---

## Introduction

The F&I Deal Center is a comprehensive finance and insurance management system designed specifically for automotive dealerships. This system provides professional tools for structuring deals, managing lender relationships, selling F&I products, and tracking profitability.

### Key Features
- ✅ **Real-time Payment Calculations** - Advanced loan and lease calculators
- ✅ **Lender Management** - Comprehensive lender database and submission tools
- ✅ **F&I Product Sales** - Complete product catalog with profit tracking
- ✅ **Professional Reporting** - Executive dashboards and detailed analytics
- ✅ **Deal Workflow** - Complete deal lifecycle management

### User Roles
- **F&I Managers** - Complete deal structuring and product sales
- **Sales Staff** - Payment calculations and basic deal creation
- **Management** - Reporting, analytics, and performance tracking
- **Administrators** - System configuration and user management

---

## Getting Started

### Accessing the F&I Deal Center

1. **Login to SuiteCRM** using your credentials
2. **Navigate** to the F&I Deal Center module via:
   - Main navigation menu: **"F&I Deal Center"**
   - Or direct URL: `index.php?module=DM_FIDeals&action=index`

### Main Navigation Options
- **"Create F&I Deal"** - Start a new deal
- **"Payment Calculator"** - Standalone calculator tool
- **"F&I Reports"** - Access reporting dashboard
- **"Analytics"** - Executive analytics and KPIs

### User Interface Overview
The system uses a professional tabbed interface with real-time calculations and responsive design optimized for desktop use.

---

## Deal Creation & Management

### Creating a New Deal

#### Step 1: Basic Deal Information
1. **Click** "Create F&I Deal" from the main menu
2. **Enter** required information:
   - **Deal Number** - Auto-generated or manual entry
   - **Customer** - Select from existing contacts or create new
   - **Vehicle** - Link to inventory or enter details manually
   - **Salesperson** - Assign responsible sales team member

#### Step 2: Vehicle & Pricing Details
Navigate to the **"Vehicle & Pricing"** tab:

**Vehicle Information:**
- Sales Price (including add-ons)
- Year, Make, Model, Trim
- VIN (if available)
- Condition (New/Used/Certified)

**Trade-In Information:**
- Trade Allowance (what customer receives)
- Trade Payoff (amount owed)
- Net Trade Equity (calculated automatically)

**Additional Costs:**
- Documentation Fees
- Government Fees (tax, title, license)
- Dealer Add-ons

#### Step 3: Financing Details
Navigate to the **"Financing"** tab:

**Finance Method:**
- **Cash** - No financing required
- **Finance** - Traditional loan structure
- **Lease** - Lease payment calculations

**Loan Terms:**
- Amount Financed (calculated automatically)
- Interest Rate (APR)
- Term (months)
- Down Payment

**Real-time Calculations:**
- Monthly Payment
- Total of Payments
- Finance Charge
- Payment-to-Income Ratio

#### Step 4: Save and Continue
- **Click** "Save" to preserve deal data
- Use **"Payment Scenarios"** for term comparisons
- Proceed to F&I product selection

### Deal Status Management

**Deal Status Options:**
- **Draft** - Deal in progress
- **Submitted** - Sent to lender for approval
- **Approved** - Lender approved with conditions
- **Funded** - Deal completed and funded
- **Declined** - Lender declined application

**Status Updates:**
- Status automatically updates based on workflow actions
- Manual status changes available for F&I managers
- Email notifications sent on status changes

---

## Payment Calculator Tools

### Standalone Calculator

#### Accessing the Calculator
1. **Navigate** to F&I Deal Center
2. **Click** "Payment Calculator" in module menu
3. **Or** use direct URL: `index.php?module=DM_FIDeals&action=calculator`

#### Using the Calculator

**Vehicle & Finance Input:**
1. **Enter** vehicle sales price
2. **Add** down payment amount
3. **Include** trade-in details (allowance and payoff)
4. **Set** interest rate and term
5. **Add** taxes and fees

**Real-time Results:**
- Amount Financed updates automatically
- Monthly Payment displays prominently
- Total of Payments calculated instantly
- Finance Charge shown for comparison

#### Payment Scenarios Feature
1. **Click** "Payment Scenarios" button
2. **Review** comparison table with multiple terms
3. **Select** preferred payment structure
4. **Apply** selected scenario to form

#### Creating Deals from Calculator
1. **Complete** all calculator fields
2. **Click** "Create F&I Deal" button
3. **System** automatically transfers all data
4. **Continue** with full deal creation process

### EditView Calculator Integration

When editing deals, the calculator engine provides:
- **Real-time field updates** as you type
- **Automatic validation** of financial data
- **Currency formatting** for professional presentation
- **Error highlighting** for invalid entries

---

## Lender Management

### Setting Up Lenders

#### Adding Lender Information
1. **Navigate** to Accounts module
2. **Create** new account with type "Lender"
3. **Complete** lender-specific fields:

**Basic Information:**
- Lender Name and Contact Details
- Lender Code and Type (Bank, Credit Union, etc.)
- F&I Contact Information

**Rate Information:**
- Prime Rate and Subprime Rate
- Maximum Loan Term
- Loan-to-Value Limits
- Reserve Percentage

**Performance Metrics:**
- Approval Rate
- Turnaround Times
- Total Deals Submitted/Approved
- Amount Funded

### Deal Submission Process

#### Manual Submission
1. **Open** deal in DetailView
2. **Click** "Submit to Lender" button
3. **Review** generated submission form
4. **Print** or email to lender
5. **Update** submission status

**Submission Package Includes:**
- Complete deal summary
- Payment scenarios
- Customer credit application
- Required documentation checklist

#### Tracking Submissions
1. **Click** "Approval Tracking" button
2. **Update** deal status as needed:
   - Submitted
   - Under Review
   - Approved with Conditions
   - Funded
   - Declined

3. **Add** lender notes and stipulations
4. **Schedule** follow-up actions
5. **Track** approval timeline

### Lender Performance Management

**Performance Metrics:**
- Approval percentage by lender
- Average turnaround time
- Deal volume and funding amounts
- Rate competitiveness

**Monthly Reviews:**
- Generate lender performance reports
- Compare approval rates across lenders
- Identify top-performing partnerships
- Plan rate negotiations

---

## F&I Product Sales

### Product Catalog Overview

The system includes comprehensive F&I products:

**Protection Products:**
- Extended Warranties (multiple tiers)
- GAP Insurance
- Paint Protection
- Interior Protection
- Theft Deterrent Systems

**Maintenance Products:**
- Prepaid Maintenance Plans
- Tire & Wheel Protection
- Roadside Assistance

**Insurance Products:**
- Credit Life Insurance
- Disability Insurance
- Unemployment Protection

### Product Selection Process

#### Accessing Products
1. **Open** deal in DetailView
2. **Click** "F&I Products" button
3. **Review** product recommendations

#### Product Recommendations
The system automatically recommends products based on:
- Vehicle price range
- Finance method (cash/finance/lease)
- Customer profile
- Historical penetration rates

**Priority Levels:**
- **High Priority** - Strongly recommended
- **Medium Priority** - Consider offering
- **Standard** - Available if customer requests

#### Adding Products to Deal
1. **Select** product category
2. **Choose** coverage tier (Good/Better/Best)
3. **Review** cost and profit margins
4. **Add** to deal package
5. **Calculate** total product profit

### Profit Tracking

**Cost Management:**
- Product cost vs selling price
- Dealer profit margins
- Commission calculations
- Backend gross profit

**Performance Metrics:**
- Product penetration rates
- Average product profit per deal
- Top-performing products
- Seasonal trends

---

## Reports & Analytics

### F&I Reports Dashboard

#### Accessing Reports
1. **Navigate** to any F&I deal
2. **Click** "F&I Reports" button
3. **Or** use module menu: "F&I Reports"

#### Available Reports

**Deal Summary Reports:**
- Monthly deal volume
- Average deal size
- Profit margins by month
- Deal status pipeline

**F&I Performance Reports:**
- Product penetration analysis
- Profit per deal tracking
- Manager performance metrics
- Commission calculations

**Lender Performance:**
- Approval rates by lender
- Funding turnaround times
- Rate competitiveness
- Deal volume distribution

### Executive Analytics Dashboard

#### KPI Dashboard
1. **Click** "Analytics" in module menu
2. **Review** key performance indicators:
   - Total deals closed
   - Average profit per deal
   - F&I penetration rate
   - Customer satisfaction scores

**Visual Analytics:**
- Performance trend charts
- Benchmark comparisons
- Goal vs actual tracking
- Forecasting models

#### Benchmark Analysis
- Industry standard comparisons
- Peer dealership analysis
- Performance rating system
- Improvement recommendations

### Export Capabilities

**Excel Export:**
- Detailed deal data
- Custom date ranges
- Filtered results
- Pivot table ready format

**PDF Reports:**
- Executive summaries
- Presentation-ready charts
- Professional formatting
- Management distribution

---

## Advanced Features

### Integration with Other Modules

**Vehicle Inventory Integration:**
- Automatic vehicle data population
- Inventory status updates
- Cost basis tracking
- Age and turn analysis

**Customer Management:**
- Credit profile integration
- Purchase history
- Communication tracking
- Follow-up scheduling

**Accounting Integration:**
- Deal posting automation
- Commission calculations
- Tax reporting
- Profit distribution

### Data Import/Export

**Import Capabilities:**
- Lender rate sheets
- Product pricing updates
- Customer credit data
- Vehicle inventory feeds

**Export Options:**
- Deal data for accounting
- Lender submission files
- Management reports
- Regulatory compliance reports

### Security & Permissions

**User Access Levels:**
- View-only access for sales staff
- Edit permissions for F&I managers
- Administrative controls for management
- Audit trail for all transactions

**Data Protection:**
- Encrypted customer data
- Secure transmission protocols
- Regular backup procedures
- Compliance monitoring

---

## Troubleshooting

### Common Issues

#### Calculator Not Working
**Symptoms:** Payment scenarios button doesn't respond
**Solution:**
1. Clear browser cache and cookies
2. Refresh the page
3. Check browser console for JavaScript errors
4. Ensure all required fields are completed

#### Reports Not Loading
**Symptoms:** "Action not found" errors
**Solution:**
1. Verify user permissions for reports module
2. Check database connectivity
3. Clear system cache from Admin panel
4. Contact system administrator if persistent

#### Lender Fields Missing
**Symptoms:** Lender-specific fields not visible in Accounts
**Solution:**
1. Verify account type is set to "Lender"
2. Run Quick Repair & Rebuild from Admin
3. Check field permissions for user role
4. Refresh browser cache

### Performance Optimization

**Best Practices:**
- Regularly clear browser cache
- Close unused browser tabs
- Use recommended browsers (Chrome, Firefox, Edge)
- Ensure stable internet connection

**System Maintenance:**
- Regular database cleanup
- Index optimization
- Cache management
- User permission audits

### Getting Help

**Internal Support:**
- Contact your F&I manager
- Review training materials
- Check system documentation
- Submit support tickets

**System Administration:**
- Database maintenance issues
- User permission problems
- Integration difficulties
- Performance concerns

---

## Quick Reference

### Keyboard Shortcuts
- **Ctrl+S** - Save current deal
- **Tab** - Navigate between fields
- **Enter** - Trigger calculations
- **Esc** - Close modal dialogs

### Important URLs
- **Main Module:** `index.php?module=DM_FIDeals&action=index`
- **Calculator:** `index.php?module=DM_FIDeals&action=calculator`
- **Reports:** `index.php?module=DM_FIDeals&action=reports`
- **Analytics:** `index.php?module=DM_FIDeals&action=analytics`

### Default Settings
- **Loan Term:** 60 months
- **Interest Rate:** 7.99% APR
- **Documentation Fee:** $699
- **Currency:** USD ($)

### Support Contacts
- **System Administrator:** [Contact Information]
- **F&I Training:** [Training Resources]
- **Technical Support:** [Support Channels]

---

**© 2025 F&I Deal Center - Professional Automotive Finance Management**

*This document is proprietary and confidential. Distribution limited to authorized dealership personnel only.* 