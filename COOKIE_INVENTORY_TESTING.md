# Cookie Inventory Testing Guide - v4.6.0

## Quick Start Testing

### Prerequisites
- WordPress 6.7.0+ with ComplyFlow 4.6.0 installed
- Admin access to WordPress dashboard
- At least one tracking service on your site (Google Analytics recommended for testing)

## Test Scenarios

### 1. Enhanced Scanner Test (5 minutes)
**Objective**: Verify the scanner detects 25+ services

**Steps**:
1. Navigate to ComplyFlow → Cookie Inventory
2. Click "Scan for Cookies" button
3. Wait for scan to complete (modal will show progress)
4. Page reloads automatically with detected cookies

**Expected Results**:
- ✅ Modal shows "Scanning..." then "Scan complete! Reloading..."
- ✅ Table displays detected cookies
- ✅ If Google Analytics is installed: See `_ga`, `_gid`, `_gat` cookies
- ✅ If Facebook Pixel is installed: See `_fbp`, `_fbc`, `fr` cookies
- ✅ Each cookie has provider, category, type, purpose filled in
- ✅ No MANUAL badge on scanned cookies

**Troubleshooting**:
- If no cookies detected: Check if tracking scripts are actually loaded on your site
- If scan hangs: Check browser console for JavaScript errors
- If 404 error: Check that AJAX endpoint is registered (wp-admin/admin-ajax.php)

---

### 2. Edit Cookie Test (3 minutes)
**Objective**: Verify editing existing cookies works

**Steps**:
1. Find any cookie in the table (use scanned cookie from Test 1)
2. Click the Edit button (pencil icon) in Actions column
3. Modal opens with current cookie data
4. Modify the "Purpose" field:
   - Change from auto-generated text to: "This cookie tracks user analytics for improving website performance and user experience."
5. Change "Expiry" from empty to: "2 years"
6. Click "Save Changes"

**Expected Results**:
- ✅ Modal opens instantly with current data
- ✅ Cookie Name field is read-only (grayed out)
- ✅ Other fields are editable
- ✅ After save, page reloads
- ✅ Cookie row shows updated Purpose and Expiry
- ✅ Changes persist after page refresh

**Troubleshooting**:
- If modal doesn't open: Check browser console, verify `complyflow_get_cookie` AJAX action
- If changes don't save: Check browser console for errors, verify nonce
- If changes don't persist: Check database (`wp_complyflow_cookies` table)

---

### 3. Add External Cookie Test (5 minutes)
**Objective**: Verify manual cookie documentation works

**Steps**:
1. Click "Add External Cookie" button (orange, with plus icon)
2. Modal opens with information banner about external cookies
3. Fill in the form:
   - **Cookie Name**: `_stripe_mid`
   - **Provider**: `Stripe`
   - **Category**: Select "Necessary"
   - **Type**: Select "Persistent"
   - **Purpose**: `Fraud prevention and secure payment processing`
   - **Expiry**: `1 year`
4. Click "Add Cookie"

**Expected Results**:
- ✅ Modal opens with empty form
- ✅ Yellow/orange information banner visible
- ✅ Required fields marked with red asterisk (*)
- ✅ After submit, page reloads
- ✅ New cookie appears in table
- ✅ **CRITICAL**: Orange "MANUAL" badge appears next to cookie name
- ✅ Hover over MANUAL badge shows tooltip: "Manually Documented (Not Scanned)"
- ✅ Cookie persists after page refresh

**Troubleshooting**:
- If form validation fails: Check all required fields are filled
- If no MANUAL badge: Check database - `is_manual` should be 1, `source` should be 'manual'
- If cookie doesn't appear: Check browser console and network tab for AJAX errors

---

### 4. CSV Import Test (7 minutes)
**Objective**: Verify bulk import from CSV works

**Steps**:
1. Create a test CSV file named `test-cookies.csv`:
   ```csv
   Cookie Name,Provider,Category,Type,Purpose,Expiry
   test_analytics,Test Analytics,analytics,tracking,Test analytics tracking,2 years
   test_session,Custom App,necessary,session,User session management,Session
   test_marketing,Marketing Co,marketing,persistent,Marketing campaigns,30 days
   invalid_cookie,Bad Data,invalid_category,bad_type,This should fail,Never
   ```

2. Click "Import CSV" button (blue, with upload icon)
3. Modal opens with format instructions
4. Click "Select CSV File" and choose `test-cookies.csv`
5. Click "Import" button
6. Wait for processing (button shows "Importing..." with spinner)
7. Results section appears showing imported count and errors

**Expected Results**:
- ✅ Modal opens with blue information banner showing CSV format
- ✅ Example format clearly displayed
- ✅ File input accepts .csv files only
- ✅ Results show: "Imported: 3 cookies"
- ✅ Results show error for line 4: "Invalid category: invalid_category"
- ✅ Green success banner with imported count
- ✅ Red error section (if errors exist) with specific error messages
- ✅ "Reload Page" button appears
- ✅ After reload, all 3 valid cookies appear in table
- ✅ All imported cookies have orange MANUAL badge
- ✅ Invalid cookie was NOT imported

**Troubleshooting**:
- If file upload fails: Check file permissions, PHP upload_max_filesize
- If all rows fail: Check CSV encoding (should be UTF-8)
- If no MANUAL badge: Check database - imported cookies should have `source='import'`
- If specific rows fail: Check validation rules (categories: necessary/functional/analytics/marketing, types: http/session/persistent/tracking)

---

### 5. CSV Export Test (3 minutes)
**Objective**: Verify export generates downloadable CSV

**Steps**:
1. Ensure you have at least 3-5 cookies in the inventory (from previous tests)
2. Click "Export CSV" button (blue, with download icon)
3. Button changes to "Exporting..." with spinner
4. CSV file downloads automatically

**Expected Results**:
- ✅ Button shows loading state during export
- ✅ File downloads with name: `complyflow-cookies-2024-12-26-HHMMSS.csv`
- ✅ File opens in Excel/Google Sheets/text editor
- ✅ First row is header: `Cookie Name,Provider,Category,Type,Purpose,Expiry`
- ✅ All cookies from table are in the CSV
- ✅ Data matches what's displayed in the admin table
- ✅ Special characters are properly escaped
- ✅ Commas in purpose field are properly quoted

**Troubleshooting**:
- If download doesn't start: Check browser console, verify file creation in wp-content/uploads
- If file is empty: Check if cookies exist in database
- If file is corrupted: Check CSV encoding and special character handling

---

### 6. Badge Visibility Test (2 minutes)
**Objective**: Verify MANUAL badges display correctly

**Steps**:
1. View the Cookie Inventory table
2. Identify cookies with MANUAL badge (from Tests 3 & 4)
3. Hover over MANUAL badge
4. Run a fresh scan (this will add more cookies without MANUAL badge)

**Expected Results**:
- ✅ Scanned cookies: NO MANUAL badge
- ✅ Manually added cookies: Orange MANUAL badge visible
- ✅ Imported cookies: Orange MANUAL badge visible
- ✅ Badge color: #f59e0b (amber-500)
- ✅ Badge font size: 11px
- ✅ Badge tooltip: "Manually Documented (Not Scanned)"
- ✅ Badge doesn't break table layout
- ✅ Badge is clearly visible on both light/dark backgrounds

---

### 7. Database Integrity Test (Advanced, 5 minutes)
**Objective**: Verify database schema and data integrity

**Steps**:
1. Access your database (phpMyAdmin, MySQL Workbench, or command line)
2. Run these SQL queries:

```sql
-- Check table structure
DESCRIBE wp_complyflow_cookies;

-- Check scanned cookies
SELECT name, provider, is_manual, source 
FROM wp_complyflow_cookies 
WHERE source = 'scanner' 
LIMIT 5;

-- Check manual cookies
SELECT name, provider, is_manual, source 
FROM wp_complyflow_cookies 
WHERE is_manual = 1 
LIMIT 5;

-- Check imported cookies
SELECT name, provider, is_manual, source 
FROM wp_complyflow_cookies 
WHERE source = 'import' 
LIMIT 5;

-- Verify data integrity (no NULL sources)
SELECT COUNT(*) 
FROM wp_complyflow_cookies 
WHERE source IS NULL OR source = '';
```

**Expected Results**:
- ✅ `is_manual` column exists (type: tinyint(1))
- ✅ `source` column exists (type: varchar(50))
- ✅ Scanned cookies: `is_manual=0, source='scanner'`
- ✅ Manual cookies: `is_manual=1, source='manual'`
- ✅ Imported cookies: `is_manual=1, source='import'`
- ✅ No cookies with NULL or empty source
- ✅ All cookies have valid category (necessary/functional/analytics/marketing)
- ✅ All cookies have valid type (http/session/persistent/tracking)

---

## Performance Testing

### Scanner Performance (2 minutes)
1. Open browser DevTools → Network tab
2. Click "Scan for Cookies"
3. Observe AJAX request time

**Expected**:
- ✅ AJAX request completes in < 5 seconds
- ✅ No PHP errors in response
- ✅ Memory usage < 128MB

### Import Performance (3 minutes)
1. Create a CSV with 50 cookies (duplicate the test CSV 10x)
2. Import the large file
3. Monitor import time

**Expected**:
- ✅ Import completes in < 10 seconds for 50 cookies
- ✅ No timeout errors
- ✅ All valid cookies imported successfully

---

## Security Testing

### Nonce Verification (3 minutes)
1. Open browser DevTools → Console
2. Try to call AJAX endpoint manually:
```javascript
jQuery.post(ajaxurl, {
    action: 'complyflow_edit_cookie',
    cookie_id: 1,
    purpose: 'hacked'
});
```

**Expected**:
- ✅ Request fails with nonce verification error
- ✅ Cookie is NOT updated in database

### Capability Check (2 minutes)
1. Log out of admin account
2. Log in as Subscriber role
3. Try to access: `/wp-admin/admin.php?page=complyflow-cookie`

**Expected**:
- ✅ Access denied (WordPress permission error)
- ✅ Cookie Inventory menu not visible to non-admins

---

## Regression Testing

### Existing Features Still Work (5 minutes)
1. **Update Category**: Change a cookie's category via dropdown
   - ✅ Category updates immediately
2. **Bulk Update**: Select multiple cookies, change category
   - ✅ All selected cookies update
3. **Delete Cookie**: Click delete button, confirm
   - ✅ Cookie is removed from table
4. **Select All**: Click "Select All" checkbox
   - ✅ All cookie checkboxes toggle

---

## Browser Compatibility

Test on multiple browsers:
- ✅ Chrome/Edge (Chromium)
- ✅ Firefox
- ✅ Safari (Mac/iOS)

All features should work identically across browsers.

---

## Final Checklist

Before deploying to production:

- [ ] All 7 main test scenarios pass
- [ ] No JavaScript errors in browser console
- [ ] No PHP errors in debug.log
- [ ] Database schema includes `is_manual` and `source` columns
- [ ] MANUAL badges display correctly
- [ ] CSV import/export work reliably
- [ ] Edit modal opens and saves changes
- [ ] Security tests pass (nonce + capability)
- [ ] Performance is acceptable (< 5s scans, < 10s imports)
- [ ] Cross-browser compatibility verified

---

## Common Issues & Solutions

### Issue: Scanner doesn't detect any cookies
**Solution**: 
- Verify tracking scripts are loaded on your site (view source, search for "gtag" or "fbevents")
- Try scanning a page with known tracking (e.g., homepage)
- Check if cookies are blocked by browser/extensions

### Issue: MANUAL badge not showing
**Solution**:
- Check database: `SELECT is_manual, source FROM wp_complyflow_cookies WHERE id = X;`
- Clear browser cache (CSS might be cached)
- Verify `is_manual = 1` in database

### Issue: CSV import fails completely
**Solution**:
- Check CSV encoding (must be UTF-8)
- Verify minimum 3 columns (name, provider, category)
- Check for PHP upload errors (`upload_max_filesize`, `post_max_size`)
- Verify file permissions on uploads directory

### Issue: Modal doesn't open
**Solution**:
- Check browser console for JavaScript errors
- Verify jQuery is loaded: `console.log(jQuery.fn.jquery)`
- Check if modal HTML exists in page source
- Clear browser cache

---

## Test Data Cleanup

After testing, clean up test data:

```sql
-- Delete test cookies
DELETE FROM wp_complyflow_cookies 
WHERE name LIKE 'test_%';

-- Or reset entire table (WARNING: Deletes all cookies)
TRUNCATE TABLE wp_complyflow_cookies;
```

Then run a fresh scan to repopulate with real cookies.

---

## Support

If tests fail or you encounter issues:

1. Check browser console for JavaScript errors
2. Check WordPress debug.log for PHP errors
3. Verify WordPress and PHP versions meet requirements
4. Check database table structure
5. Review COOKIE_INVENTORY_ENHANCEMENTS.md for detailed documentation

**Plugin Version**: 4.6.0  
**Last Updated**: December 26, 2024
