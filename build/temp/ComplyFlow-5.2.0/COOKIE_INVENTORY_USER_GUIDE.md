# Cookie Inventory - Complete User Guide

## Overview

The Cookie Inventory is your central hub for managing all cookies and tracking technologies on your website. It helps you maintain GDPR, CCPA, and ePrivacy Directive compliance by providing complete visibility into data collection practices.

---

## Understanding the Interface

### Page Header
- **Title**: Cookie Inventory
- **Subtitle**: "Detect, categorize, and manage cookies and tracking technologies on your website"

### Information Banner (Blue)
At the top of the page, you'll see a comprehensive information banner explaining:
- What the Cookie Inventory does (tracks 25+ major services)
- Four main features with icons:
  - 🔍 **Scan for Cookies**: Auto-detect cookies
  - ✏️ **Edit Cookies**: Refine descriptions
  - ➕ **Add External Cookies**: Document manual entries
  - 📤 **Import CSV**: Bulk import capabilities
- **Important Note**: Orange MANUAL badges indicate manually-added or imported cookies

---

## Statistics Dashboard

Five colorful stat cards show your cookie inventory at a glance:

### 1. Total Cookies (Blue)
- **Icon**: Settings gear
- **What it shows**: Total number of cookies tracked
- **Why it matters**: Quick overview of your complete cookie footprint

### 2. Necessary Cookies (Green)
- **Icon**: Shield
- **What it shows**: Cookies essential for site functionality
- **Why it matters**: These can be used without explicit consent (but must still be disclosed)
- **Examples**: Session cookies, security tokens, load balancers

### 3. Functional Cookies (Blue)
- **Icon**: Tools
- **What it shows**: Cookies that enhance user experience
- **Why it matters**: Require consent under GDPR/ePrivacy
- **Examples**: Language preferences, video player settings, chat widgets

### 4. Analytics Cookies (Purple)
- **Icon**: Bar chart
- **What it shows**: Cookies used for measuring website performance
- **Why it matters**: Require consent; must be documented for transparency
- **Examples**: Google Analytics, Hotjar, Mixpanel, Matomo

### 5. Marketing Cookies (Pink)
- **Icon**: Megaphone
- **What it shows**: Cookies used for advertising and tracking
- **Why it matters**: Most sensitive category; requires explicit consent
- **Examples**: Facebook Pixel, Google Ads, TikTok Pixel, LinkedIn Insight

---

## Action Buttons

### Left Group (Primary Actions)

#### 1. Scan for Cookies
- **Icon**: 🔍 Search
- **Button Style**: Blue (Primary)
- **What it does**: 
  - Crawls your website homepage
  - Analyzes JavaScript files
  - Detects iframe embeds
  - Identifies 25+ tracking services automatically
  
- **Supported Services**:
  - **Google**: Analytics, Ads, Tag Manager, Optimize
  - **Social Media**: Facebook, TikTok, LinkedIn, Twitter, Pinterest, Snapchat
  - **Video**: YouTube, Vimeo
  - **Analytics**: Hotjar, HubSpot, Matomo, Mixpanel, Segment
  - **Chat**: Intercom, Drift, Zendesk
  - **Payments**: Stripe, PayPal
  - **Infrastructure**: Cloudflare, reCAPTCHA
  - **Email**: Mailchimp

- **How to use**:
  1. Click "Scan for Cookies"
  2. Modal shows "Scanning..." progress
  3. Wait 3-10 seconds (depends on site complexity)
  4. Page reloads with detected cookies
  5. New cookies appear in table with auto-generated descriptions

- **Best practices**:
  - Run initial scan after plugin activation
  - Re-scan after installing new tracking tools
  - Re-scan monthly to catch new third-party cookies
  - Run scan before compliance audits

#### 2. Add External Cookie
- **Icon**: ➕ Plus
- **Button Style**: White (Secondary)
- **What it does**: Opens modal for manually documenting cookies
- **When to use**:
  - Cookies from iframes (booking widgets, payment forms)
  - Third-party embeds (Calendly, Typeform, embedded apps)
  - Cookies scanner cannot detect automatically
  - External services loaded via CDN
  
- **How to use**:
  1. Click "Add External Cookie"
  2. Modal opens with yellow information banner
  3. Fill in required fields (marked with *):
     - **Cookie Name**: Exact name (e.g., `_stripe_mid`)
     - **Provider**: Service name (e.g., "Stripe")
     - **Category**: Select from dropdown
     - **Type**: Select cookie type
     - **Purpose**: Detailed description
     - **Expiry**: Human-readable (e.g., "1 year", "Session")
  4. Click "Add Cookie"
  5. Page reloads with new cookie
  6. Cookie displays orange MANUAL badge

- **Best practices**:
  - Check browser DevTools → Application → Cookies to find exact names
  - Write clear, user-friendly purpose descriptions
  - Document iframe cookies immediately after adding widget
  - Include external payment gateway cookies

### Right Group (Data Management)

#### 3. Import CSV
- **Icon**: 📤 Upload
- **Button Style**: White (Secondary)
- **What it does**: Bulk imports cookies from CSV file
- **When to use**:
  - Migrating from another compliance tool
  - Importing previous cookie audits
  - Transferring data between environments
  - Bulk adding multiple cookies at once

- **CSV Format**:
  ```csv
  Cookie Name,Provider,Category,Type,Purpose,Expiry
  _ga,Google,analytics,tracking,Analytics tracking,2 years
  session_id,Custom,necessary,session,User session,Session
  fbp,Facebook,marketing,persistent,Facebook Pixel tracking,90 days
  ```

- **Required Columns**:
  - Cookie Name (required)
  - Provider (required)
  - Category (required): necessary, functional, analytics, marketing
  
- **Optional Columns**:
  - Type: http, session, persistent, tracking (defaults to "http")
  - Purpose: Description (defaults to provider name)
  - Expiry: Human-readable time

- **How to use**:
  1. Click "Import CSV"
  2. Modal opens with format instructions and example
  3. Click "Select CSV File"
  4. Choose your .csv file
  5. Click "Import"
  6. Button shows "Importing..." with spinner
  7. Results appear showing:
     - Green success: "Imported: X cookies"
     - Red errors: Specific rows that failed with reasons
  8. Click "Reload Page" to see imported cookies

- **Validation Rules**:
  - File must be .csv format
  - Minimum 3 columns required
  - Valid category values only
  - Valid type values only
  - Duplicate names update existing cookies
  - Empty rows are skipped

- **Error Handling**:
  - Invalid categories: Shows error with row number
  - Invalid types: Shows error with row number
  - Missing required columns: Shows error
  - Malformed CSV: Shows parsing error
  - Successful imports still complete even if some rows fail

#### 4. Export CSV
- **Icon**: 📥 Download
- **Button Style**: White (Secondary)
- **What it does**: Exports all cookies to CSV file
- **When to use**:
  - Creating backups
  - Sharing with compliance team
  - Transferring to another environment
  - Generating compliance documentation
  - Excel analysis of cookie usage

- **How to use**:
  1. Click "Export CSV"
  2. Button shows "Exporting..." with spinner
  3. CSV file downloads automatically
  4. Filename: `complyflow-cookies-YYYY-MM-DD-HHMMSS.csv`

- **File Contents**:
  - All cookies (scanned and manual)
  - All fields: name, provider, category, type, purpose, expiry
  - CSV format compatible with Excel, Google Sheets
  - UTF-8 encoding for international characters

---

## Cookie Table

### Table Columns

#### 1. Checkbox (Leftmost)
- Select individual cookies for bulk actions
- "Select All" checkbox in header
- Use with bulk category update

#### 2. Cookie Name
- Displays exact cookie name as set in browser
- **MANUAL Badge**: Orange badge if cookie was manually added or imported
  - Hover tooltip: "Manually Documented (Not Scanned)"
  - Color: Amber (#f59e0b)
  - Font size: 11px
- Auto-detected cookies have no badge

#### 3. Provider
- Organization/service that sets the cookie
- Examples: "Google", "Facebook", "Stripe", "WordPress"
- Editable via Edit button

#### 4. Category
- Dropdown with 4 options:
  - **Necessary** (Green): Essential for site function
  - **Functional** (Blue): Enhance user experience
  - **Analytics** (Purple): Track usage and performance
  - **Marketing** (Pink): Advertising and targeting
- Changes save automatically on selection
- Color-coded badges for visual clarity

#### 5. Type
- Badge showing cookie type:
  - **HTTP Cookie**: Standard browser cookie
  - **Session**: Temporary, deleted when browser closes
  - **Persistent**: Stored for specific duration
  - **Tracking** (Pink badge): Used for user tracking
- Editable via Edit button

#### 6. Purpose
- Human-readable description of cookie usage
- Auto-generated for scanned cookies
- Should be clear for privacy policy
- Editable via Edit button

#### 7. Expiry
- How long cookie persists
- Examples: "Session", "1 year", "2 years", "30 days"
- Human-readable (not technical timestamps)
- Editable via Edit button

#### 8. Actions
- **Edit Button** (Pencil icon):
  - Opens edit modal
  - Modify provider, type, purpose, expiry
  - Cookie name is read-only
  - Changes save immediately
  
- **Delete Button** (Trash icon):
  - Shows confirmation dialog
  - Removes cookie from inventory
  - Cannot be undone
  - Row fades out on successful delete

### Bulk Actions

Located above the table:

1. **Select bulk action dropdown**:
   - Change category to: Necessary
   - Change category to: Functional
   - Change category to: Analytics
   - Change category to: Marketing

2. **Select cookies**: Check boxes for cookies to update

3. **Click "Apply"**: All selected cookies update to chosen category

---

## Cookie Workflow: Best Practices

### Initial Setup (First Time)
1. **Run Scan**: Click "Scan for Cookies" to auto-detect
2. **Review Results**: Check detected cookies in table
3. **Categorize**: Ensure categories are correct (necessary vs marketing)
4. **Edit Descriptions**: Refine auto-generated purposes for clarity
5. **Add External**: Document any iframe/external cookies
6. **Export Backup**: Save initial state as CSV

### Regular Maintenance (Monthly)
1. **Re-scan**: Check for new cookies from updates/plugins
2. **Review New**: Categorize any newly detected cookies
3. **Update Descriptions**: Improve clarity based on privacy policy needs
4. **Check Compliance**: Ensure all cookies have clear purposes

### Before Compliance Audit
1. **Full Scan**: Get latest cookie inventory
2. **Complete Documentation**: Fill in all purpose fields
3. **Categorize All**: No cookies should be uncategorized
4. **Export Report**: Generate CSV for auditor
5. **Cross-Reference**: Match with privacy policy disclosures

### After Adding New Tools
1. **Immediate Scan**: Detect new tracking cookies
2. **Categorize Properly**: Marketing tools need marketing category
3. **Document Purpose**: Explain why the tool is used
4. **Update Consent**: Adjust consent banner if needed

---

## Understanding Cookie Sources

### Auto-Scanned Cookies
- **Source**: Automated scanner detection
- **Badge**: None (no visual indicator)
- **Database**: `is_manual = 0`, `source = 'scanner'`
- **Characteristics**:
  - Detected by scanning your website
  - Auto-categorized based on known patterns
  - Purpose auto-generated from service database
  - Provider identified from script URLs

### Manually Added Cookies
- **Source**: User clicked "Add External Cookie"
- **Badge**: Orange "MANUAL" badge
- **Database**: `is_manual = 1`, `source = 'manual'`
- **Characteristics**:
  - User-entered data
  - Custom categorization
  - Custom purpose description
  - Used for cookies scanner cannot detect

### Imported Cookies
- **Source**: CSV import
- **Badge**: Orange "MANUAL" badge
- **Database**: `is_manual = 1`, `source = 'import'`
- **Characteristics**:
  - Imported from CSV file
  - Bulk addition from other tools
  - May have come from previous audits
  - Treated same as manually added

---

## Modal Dialogs

### 1. Scan Progress Modal
- **Title**: "Scanning for Cookies..."
- **Content**: Loading spinner with status text
- **Status Messages**:
  - "Analyzing your website..."
  - "Scan complete! Reloading..."
- **Behavior**: Auto-closes on completion, page reloads

### 2. Edit Cookie Modal
- **Title**: "Edit Cookie Details"
- **Close**: X button or overlay click
- **Form Fields**:
  - Cookie Name (read-only, grayed out)
  - Provider (text input)
  - Type (dropdown: http, session, persistent, tracking)
  - Purpose (textarea, 3 rows)
  - Expiry (text input with help text)
- **Buttons**:
  - Cancel (gray)
  - Save Changes (blue)
- **Behavior**: Loads current data, saves updates, reloads page

### 3. Add Manual Cookie Modal
- **Title**: "Add External Cookie"
- **Close**: X button or overlay click
- **Information Banner**: Yellow/amber banner explaining use case
- **Form Fields**:
  - Cookie Name* (required)
  - Provider* (required)
  - Category* (required dropdown)
  - Type* (required dropdown)
  - Purpose* (required textarea)
  - Expiry (optional text)
- **Buttons**:
  - Cancel (gray)
  - Add Cookie (blue)
- **Behavior**: Empty form, validates required fields, adds with MANUAL badge

### 4. Import CSV Modal
- **Title**: "Import Cookies from CSV"
- **Close**: X button or overlay click
- **Information Banner**: Blue banner with format instructions
- **CSV Format Example**: Shows proper column structure
- **Validation Info**: Lists valid categories and types
- **File Input**: Accepts .csv files only
- **Results Section**: Shows after import (hidden initially)
  - Green success message with count
  - Red error list (if any failures)
  - "Reload Page" button
- **Buttons**:
  - Cancel (gray)
  - Import (blue with upload icon)
- **Behavior**: Validates file, uploads, shows results, allows reload

---

## Dashboard Integration

The Cookie Inventory is also summarized on the main dashboard:

### Cookie Inventory Widget
Located in the dashboard widgets section:

**Header**:
- Title: "Cookie Inventory"
- Icon: List view

**Total Cookies**:
- Large number showing total count
- Label: "Total Cookies"

**Scanned vs Manual Breakdown**:
- Two-column display with gradient background
- **Left**: Auto-Scanned count (blue)
- **Right**: Manual/Import count (orange)
- Visual separator between columns

**Category Chart**:
- Doughnut chart showing distribution
- Colors match category badges
- Responsive height (200px)
- Accessible with ARIA labels

**Help Text**:
- Explains chart purpose
- Mentions consent category assignment

**Action Link**:
- "Manage Cookies →" button
- Links to full Cookie Inventory page

### Quick Actions Section

**Scan Cookies Button**:
- Located in Quick Actions grid
- Icon: Refresh/update
- Triggers same scan as Cookie Inventory page
- Shows results in modal with:
  - Total detected count
  - Breakdown by category (4 stat cards)
  - Note about enhanced scanner (25+ services)
  - Sample cookie list by category
  - "View Full Details" button → links to Cookie Inventory

---

## Compliance Applications

### GDPR Compliance
**Article 30 - Records of Processing**:
- Complete inventory = documentation requirement
- Categories show consent necessity
- Purposes fulfill transparency obligation

**Article 13 - Information to Data Subjects**:
- Cookie purposes → privacy policy content
- Provider info → third-party disclosure
- Expiry → retention period disclosure

### CCPA Compliance
**Section 1798.100 - Right to Know**:
- Cookie inventory = data collection practices
- Categories show data types collected
- Third-party providers disclosed

### ePrivacy Directive
**Article 5(3) - Consent Requirement**:
- Necessary cookies identified (exempt)
- Non-necessary cookies flagged (require consent)
- Complete list for cookie banner

---

## Troubleshooting

### Scanner doesn't detect any cookies
**Problem**: Scan completes but finds 0 cookies

**Solutions**:
1. Verify tracking scripts are active on your site
2. Check if you have Google Analytics, Facebook Pixel, etc. installed
3. Try scanning after visiting your homepage in a browser
4. Disable cookie blockers/ad blockers temporarily
5. Check browser console for JavaScript errors

### MANUAL badge not showing
**Problem**: Manually added cookie doesn't have orange badge

**Solutions**:
1. Refresh page (hard refresh: Ctrl+F5)
2. Check database: `is_manual` should be `1`
3. Clear browser cache
4. Check CSS isn't being overridden

### CSV import fails completely
**Problem**: All rows fail to import

**Solutions**:
1. Check CSV encoding (must be UTF-8)
2. Verify file has minimum 3 columns
3. Check column headers match exactly: "Cookie Name,Provider,Category"
4. Remove any special characters from column names
5. Try with a simple 1-row test file first

### Edit modal doesn't open
**Problem**: Clicking Edit button does nothing

**Solutions**:
1. Check browser console for JavaScript errors
2. Verify jQuery is loaded: Open console, type `jQuery.fn.jquery`
3. Check if AJAX endpoint is accessible
4. Hard refresh page (Ctrl+F5)
5. Disable other plugins to check for conflicts

### Changes don't persist
**Problem**: Edit cookie, but changes disappear

**Solutions**:
1. Check browser console for AJAX errors
2. Verify database write permissions
3. Check nonce verification isn't failing
4. Ensure current user has `manage_options` capability
5. Check WordPress debug log for errors

---

## Keyboard Shortcuts

- **Tab**: Navigate through form fields
- **Enter**: Submit form when focused on button
- **Esc**: Close modal (when implemented)
- **Space**: Toggle checkbox selection

---

## Accessibility Features

- **Screen Reader Support**: All buttons have descriptive labels
- **ARIA Labels**: Charts have proper descriptions
- **Keyboard Navigation**: Full keyboard control
- **Color Contrast**: WCAG AA compliant colors
- **Focus Indicators**: Clear visual focus states
- **Modal Focus Trap**: Focus stays within modal when open

---

## Tips & Tricks

### Pro Tips
1. **Regular Scans**: Set calendar reminder for monthly cookie scans
2. **Version Control**: Export CSV before major site updates
3. **Documentation**: Keep exported CSVs as audit trail
4. **Clear Descriptions**: Write purposes in plain language for privacy policy
5. **Category Accuracy**: Correct categorization is critical for legal compliance

### Time Savers
1. Use bulk category update for multiple similar cookies
2. Export CSV to Excel for quick analysis
3. Import CSV when setting up staging/development sites
4. Copy-paste cookie names from browser DevTools

### Common Mistakes to Avoid
1. ❌ Don't categorize marketing cookies as "necessary"
2. ❌ Don't use technical jargon in purpose descriptions
3. ❌ Don't forget to document iframe cookies
4. ❌ Don't skip monthly scans (new cookies appear)
5. ❌ Don't delete cookies without checking if they're in use

---

## Data Privacy Note

**What data is stored**:
- Cookie names, providers, categories, types, purposes, expiry times
- Whether cookie was scanned or manually added
- Timestamp of last update

**What data is NOT stored**:
- Actual cookie values
- User-specific cookie data
- Personal information from cookies
- Cookie contents or payloads

**Database table**: `wp_complyflow_cookies`

**Data export**: All data can be exported via CSV at any time

**Data deletion**: Uninstalling plugin removes all cookie inventory data

---

## Support & Resources

### Documentation
- **COOKIE_INVENTORY_ENHANCEMENTS.md**: Technical implementation details
- **COOKIE_INVENTORY_TESTING.md**: QA testing guide
- **CHANGELOG.md**: Version history and updates

### Getting Help
- Check troubleshooting section above
- Review browser console for errors
- Check WordPress debug.log
- Verify all prerequisites (WordPress 6.4+, PHP 8.0+)

### Feature Requests
- Enhanced scanner for additional services
- Bulk edit capabilities
- Cookie history tracking
- Automated scan scheduling
- REST API access

---

**Version**: 4.6.0  
**Last Updated**: December 26, 2024  
**Plugin**: ComplyFlow - WordPress Compliance Suite
