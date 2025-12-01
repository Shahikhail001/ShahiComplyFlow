# Screenshot Guide for ComplyFlow v4.3.0

This guide provides detailed instructions for capturing professional screenshots for the CodeCanyon listing.

## Setup Requirements

### WordPress Environment
- **WordPress Version**: 6.7 (latest)
- **Theme**: Twenty Twenty-Four (default, clean appearance)
- **PHP Version**: 8.2 or 8.3
- **Screen Resolution**: 1920x1080
- **Browser**: Chrome latest (for consistent rendering)
- **Browser Zoom**: 100% (no zoom in/out)

### Sample Data Setup
Before taking screenshots, populate the plugin with realistic demo data:

#### Accessibility Scanner Data
```
- Scan 10 pages (Home, About, Services, Contact, Blog posts)
- Ensure mix of severity levels:
  * 8 Critical issues (missing alt text, form labels)
  * 15 Serious issues (color contrast, heading order)
  * 22 Moderate issues (link text, ARIA attributes)
  * 35 Minor issues (redundant links, language)
```

#### Consent Logs
```
- Generate 50+ consent logs:
  * 35 accepted (all categories)
  * 10 accepted (partial - analytics/marketing rejected)
  * 5 rejected (all categories)
  * Timestamps: Past 30 days
  * Mix of geo-locations (EU, California, Brazil, Canada)
```

#### DSR Requests
```
- Create 5 requests with different statuses:
  * 1 Pending (awaiting email verification)
  * 1 Verified (email confirmed, awaiting processing)
  * 1 In Progress (admin is processing)
  * 2 Completed (data exported and sent)
- Request types: Access, Erasure, Portability, Rectification
```

#### Cookie Inventory
```
- Scan website to detect cookies (20+ total):
  * 5 Necessary: wordpress_*, PHPSESSID, complyflow_consent
  * 5 Functional: wp-settings, comment_author
  * 5 Analytics: _ga, _gid, _gat
  * 5 Marketing: _fbp, fr, TikTok_pixel
- Categories assigned and color-coded
```

#### Compliance Score
```
- Target score: 78/100 (Grade B+)
- Breakdown visible on dashboard:
  * Accessibility: 72% (room for improvement)
  * Consent: 95% (excellent)
  * DSR: 80% (good)
  * Cookies: 88% (good)
```

## Screenshot Specifications

### Image Format
- **File Format**: PNG (lossless)
- **Resolution**: 1920x1080 pixels (Full HD)
- **Color Depth**: 24-bit RGB
- **Compression**: Optimized PNG (use TinyPNG or similar)
- **File Size**: Target 200-400KB per image (max 500KB)
- **Naming**: Sequential with descriptive names

### Browser Settings
1. **Clear Browser Cache**: Ensure fresh page loads
2. **Disable Extensions**: Turn off browser extensions that might interfere
3. **Full Screen**: Press F11 for full-screen browser mode
4. **Hide Bookmarks Bar**: Clean interface without browser UI clutter
5. **Consistent Zoom**: Ensure 100% zoom level (Ctrl+0)

## Screenshot List

### Screenshot 1: Compliance Dashboard
**Filename**: `01-dashboard-overview.png`

**What to Show**:
- Full ComplyFlow Dashboard page
- Compliance score widget showing 78/100 (Grade B+)
- Donut chart with 4 colored segments
- 4 dashboard widgets visible:
  * Compliance Overview (top-left)
  * Quick Actions (top-right)
  * Recent Activity feed (bottom-left)
  * Accessibility Issues summary (bottom-right)
- Recent activity showing 3-5 entries with timestamps
- WordPress admin menu on left showing ComplyFlow menu items

**Navigation**: `ComplyFlow → Dashboard`

**Preparation**:
1. Ensure compliance score is calculated (78/100, Grade B+)
2. Populate recent activity with varied actions:
   - "New DSR request received from user@example.com"
   - "Accessibility scan completed on Contact page"
   - "Consent banner accepted by visitor from Germany"
   - "Cookie inventory updated: 3 new cookies detected"
3. Quick Actions should show 2-3 pending items
4. Accessibility Issues widget: 80 total issues (8 critical, 15 serious, 22 moderate, 35 minor)

**Screenshot Steps**:
1. Navigate to `ComplyFlow → Dashboard`
2. Wait for all widgets to load (donut chart animation complete)
3. Scroll to top of page
4. Press F11 for full-screen browser
5. Capture: Windows Snipping Tool or Snagit
6. Press F11 to exit full-screen
7. Save as `01-dashboard-overview.png`

---

### Screenshot 2: Accessibility Scanner Results
**Filename**: `02-accessibility-scanner.png`

**What to Show**:
- Accessibility Scanner results table
- Mix of severity levels with colored badges:
  * Red badge: Critical (8 issues)
  * Orange badge: Serious (15 issues)
  * Yellow badge: Moderate (22 issues)
  * Blue badge: Minor (35 issues)
- Table columns: Page, Issue Type, Element, Severity, Detected Date
- Filter dropdown showing "All Severities"
- Page filter showing "All Pages"
- "Export PDF" button visible
- "Scan Now" button visible
- Pagination showing "Page 1 of 4"

**Navigation**: `ComplyFlow → Accessibility Scanner`

**Preparation**:
1. Run accessibility scan on 10 pages
2. Ensure variety of issue types:
   - Missing alt text on images
   - Color contrast failures (text too light)
   - Missing form labels
   - Heading level skipped (h1 → h3)
   - Empty link text
   - Missing language attribute
   - Insufficient color contrast (buttons)
   - Redundant link text ("click here")
3. Ensure different pages represented (Home, About, Services, Contact, Blog)

**Screenshot Steps**:
1. Navigate to `ComplyFlow → Accessibility Scanner`
2. Ensure results table is populated
3. Scroll to show first 10-15 issues
4. Press F11 for full-screen
5. Capture full page
6. Save as `02-accessibility-scanner.png`

---

### Screenshot 3: Consent Banner (Frontend)
**Filename**: `03-consent-banner-frontend.png`

**What to Show**:
- Website frontend with consent banner at bottom
- Banner showing all 4 cookie categories:
  * ✅ Necessary Cookies (locked, always on)
  * ☐ Functional Cookies (toggle switch)
  * ☐ Analytics Cookies (toggle switch)
  * ☐ Marketing Cookies (toggle switch)
- Three buttons visible:
  * "Accept All" (primary, green)
  * "Reject All" (secondary, gray)
  * "Save Preferences" (secondary, blue)
- "Manage Preferences" link visible
- Banner headline: "We value your privacy"
- Short description text visible
- Banner overlay (semi-transparent background behind banner)
- Clean website content visible above banner

**Navigation**: Frontend homepage (logged out or incognito mode)

**Preparation**:
1. Go to `ComplyFlow → Settings → Consent Management`
2. Enable consent banner
3. Set banner position: Bottom
4. Configure cookie categories (all 4 enabled)
5. Set banner text:
   - Headline: "We value your privacy"
   - Description: "We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. Choose which cookies you want to accept."
6. Save settings
7. Clear browser cookies (to trigger banner on frontend)

**Screenshot Steps**:
1. Open frontend homepage in Incognito/Private browsing mode
2. Wait for consent banner to appear (bottom of screen)
3. Ensure all 4 categories visible with toggle switches
4. DO NOT click any buttons (banner should be in initial state)
5. Press F11 for full-screen
6. Capture full page showing website + banner
7. Save as `03-consent-banner-frontend.png`

**Note**: The banner should overlay the bottom of the page. Ensure some website content (header, hero section) is visible above the banner for context.

---

### Screenshot 4: DSR Request Management
**Filename**: `04-dsr-request-management.png`

**What to Show**:
- DSR Requests admin page
- Table with 5 sample requests showing:
  * Requester email (e.g., john.doe@example.com)
  * Request type (Access, Erasure, Portability, Rectification, Restriction)
  * Status badges with colors:
    - Gray: Pending
    - Blue: Verified
    - Orange: In Progress
    - Green: Completed
  * Request date (timestamps)
  * Actions column (View, Process, Export, Delete icons)
- Status filter dropdown showing "All Statuses"
- Request type filter showing "All Types"
- "Bulk Actions" dropdown
- Search box for filtering by email
- Pagination showing "5 of 5 requests"

**Navigation**: `ComplyFlow → DSR Requests`

**Preparation**:
1. Create 5 DSR requests with varied data:

**Request 1** (Pending):
- Email: john.doe@example.com
- Type: Access Request
- Status: Pending
- Created: 2 days ago
- Notes: Awaiting email verification

**Request 2** (Verified):
- Email: sarah.smith@example.com
- Type: Erasure Request
- Status: Verified
- Created: 1 day ago
- Notes: Email verified, ready to process

**Request 3** (In Progress):
- Email: michael.jones@example.com
- Type: Data Portability
- Status: In Progress
- Created: 5 days ago
- Notes: Collecting data from WooCommerce

**Request 4** (Completed):
- Email: emily.wilson@example.com
- Type: Rectification Request
- Status: Completed
- Created: 10 days ago
- Notes: Data updated and confirmed

**Request 5** (Completed):
- Email: david.brown@example.com
- Type: Access Request
- Status: Completed
- Created: 15 days ago
- Notes: Data exported and sent to requester

**Screenshot Steps**:
1. Navigate to `ComplyFlow → DSR Requests`
2. Ensure all 5 requests visible in table
3. Scroll to show entire table (no scrolling within page)
4. Press F11 for full-screen
5. Capture full page
6. Save as `04-dsr-request-management.png`

---

### Screenshot 5: Settings Panel
**Filename**: `05-settings-panel.png`

**What to Show**:
- ComplyFlow Settings page
- Tabbed interface with 6 tabs visible:
  * General (active/selected)
  * Consent Management
  * Accessibility Scanner
  * DSR Portal
  * Cookie Inventory
  * Document Generator
- General tab content showing:
  * Plugin status toggle (enabled)
  * License key field (blurred/masked)
  * Email notification settings
  * Data retention period (dropdown: 12 months)
  * Anonymize IP addresses (checkbox: checked)
  * Enable debug mode (checkbox: unchecked)
  * Import/Export configuration buttons
- "Save Settings" button (primary, blue) at bottom
- Settings organized in sections with headings
- Inline help text under each setting

**Navigation**: `ComplyFlow → Settings`

**Preparation**:
1. Navigate to `ComplyFlow → Settings`
2. Ensure General tab is active (first tab)
3. Configure realistic settings:
   - Plugin Status: Enabled
   - Email Notifications: Enabled
   - Data Retention: 12 months
   - Anonymize IPs: Checked
   - Debug Mode: Unchecked

**Screenshot Steps**:
1. Navigate to `ComplyFlow → Settings`
2. Click "General" tab (should be default)
3. Scroll to show top of settings form
4. Ensure all 6 tabs visible in tab bar
5. Press F11 for full-screen
6. Capture full page
7. Save as `05-settings-panel.png`

---

### Screenshot 6: Cookie Inventory (Optional/Bonus)
**Filename**: `06-cookie-inventory.png`

**What to Show**:
- Cookie Inventory page
- Table with 20+ cookies showing:
  * Cookie name (e.g., _ga, _fbp, wordpress_*, PHPSESSID)
  * Category badge with color:
    - Green: Necessary
    - Blue: Functional
    - Yellow: Analytics
    - Red: Marketing
  * Provider (e.g., Google Analytics, Facebook, WordPress, Session)
  * Expiration (e.g., 2 years, Session, 1 month)
  * First/Third party indicator
  * Actions (Edit, Delete)
- "Scan Website" button (primary)
- "Export CSV" button (secondary)
- Last scan timestamp: "Last scan: 2 hours ago"
- Category filter dropdown: "All Categories"
- Provider filter dropdown: "All Providers"
- Bulk actions checkbox for each row

**Navigation**: `ComplyFlow → Cookie Inventory`

**Preparation**:
1. Run cookie scan on your site
2. Manually categorize cookies if needed:
   - Necessary: wordpress_*, PHPSESSID, complyflow_consent
   - Functional: wp-settings-*, comment_author
   - Analytics: _ga, _gid, _gat
   - Marketing: _fbp, fr, IDE
3. Ensure 20+ cookies detected (install Google Analytics and Facebook Pixel for realistic data)

**Screenshot Steps**:
1. Navigate to `ComplyFlow → Cookie Inventory`
2. Ensure table shows 20+ cookies
3. Scroll to show first 15-20 rows
4. Press F11 for full-screen
5. Capture full page
6. Save as `06-cookie-inventory.png`

---

### Screenshot 7: Document Generator (Optional/Bonus)
**Filename**: `07-document-generator.png`

**What to Show**:
- Document Generator page
- 3 document cards:
  * Privacy Policy (with "Generate" or "Edit" button)
  * Terms of Service (with "Generate" button)
  * Cookie Policy (with "Generate" or "Edit" button if cookies scanned)
- Progress indicator showing questionnaire completion
- Preview panel showing generated document
- Version history showing 2-3 previous versions
- "Download PDF" button
- "Publish to Page" button
- Live preview toggle

**Navigation**: `ComplyFlow → Document Generator`

**Preparation**:
1. Generate at least Privacy Policy (complete questionnaire)
2. If possible, generate Cookie Policy (requires cookie scan first)
3. Create 2-3 versions by re-generating with slight changes

**Screenshot Steps**:
1. Navigate to `ComplyFlow → Document Generator`
2. Ensure at least one document generated (Privacy Policy)
3. Show document list or questionnaire interface
4. Press F11 for full-screen
5. Capture full page
6. Save as `07-document-generator.png`

---

## Post-Processing

After capturing all screenshots:

### 1. Optimization
```bash
# Use TinyPNG, ImageOptim, or similar tools to compress PNG files
# Target: 200-400KB per image (max 500KB)
# Preserve quality while reducing file size
```

### 2. Quality Check
- ✅ Resolution: Exactly 1920x1080 pixels
- ✅ Format: PNG (not JPEG)
- ✅ File size: 200-500KB each
- ✅ No browser UI visible (address bar, bookmarks, extensions)
- ✅ Clear, readable text (no blurriness)
- ✅ Realistic demo data (no "lorem ipsum" or obviously fake data)
- ✅ Consistent styling across all screenshots
- ✅ No personal/sensitive information visible

### 3. File Naming
```
01-dashboard-overview.png
02-accessibility-scanner.png
03-consent-banner-frontend.png
04-dsr-request-management.png
05-settings-panel.png
06-cookie-inventory.png (optional)
07-document-generator.png (optional)
```

### 4. Storage Location
Save all screenshots to:
```
d:\ShahiComplyFlow\screenshots\
```

### 5. Validation
Check each screenshot against requirements:
- [ ] All 5 required screenshots captured (1-5)
- [ ] Optional screenshots captured if desired (6-7)
- [ ] All images optimized to <500KB
- [ ] All images exactly 1920x1080 resolution
- [ ] Filenames match convention (01-, 02-, etc.)
- [ ] No duplicate or blurry images
- [ ] Demo data looks realistic and professional

## CodeCanyon Requirements

According to CodeCanyon standards:
- **Minimum**: 3 screenshots
- **Recommended**: 5-7 screenshots
- **Maximum**: 10 screenshots
- **Format**: PNG or JPEG
- **Resolution**: At least 1280x720, recommended 1920x1080
- **File Size**: Under 1MB per image
- **Content**: Should showcase main features, admin interface, frontend display

## Tips for Professional Screenshots

1. **Consistency**: Use same theme, browser, and zoom level for all screenshots
2. **Clean Data**: Use realistic names, not "test123" or "asdf"
3. **Variety**: Show different aspects of the plugin (admin, frontend, reports)
4. **Context**: Include enough WordPress admin UI to orient users
5. **Highlights**: Use subtle annotations (arrows, highlights) if needed (optional)
6. **No Clutter**: Hide unnecessary WordPress admin notices or plugins
7. **Fresh State**: Clear caches, use incognito for frontend, ensure fresh page loads

## Troubleshooting

### Issue: Consent banner doesn't appear on frontend
**Solution**: Clear browser cookies, use incognito mode, ensure banner is enabled in settings

### Issue: Dashboard looks empty
**Solution**: Generate sample data first (run scans, create DSR requests, scan cookies)

### Issue: Screenshots are blurry
**Solution**: Ensure 100% browser zoom (Ctrl+0), use PNG format, don't resize images

### Issue: File sizes too large
**Solution**: Use TinyPNG or ImageOptim to compress, remove unnecessary alpha channels

### Issue: Colors look washed out
**Solution**: Use sRGB color profile, ensure 24-bit RGB color depth, export from Photoshop/Snagit

## Next Steps

After screenshots are captured and optimized:
1. ✅ Review all images for quality and consistency
2. ✅ Update README.txt screenshot descriptions if needed
3. ✅ Prepare screenshot descriptions for CodeCanyon listing
4. ✅ Create promotional banner (1920x1080) using screenshot 1 as base
5. ✅ Move to Task 4: User Guide PDF (can include these screenshots)

---

**Document Version**: 1.0  
**Created**: Phase 9, Task 3  
**Last Updated**: 2024-01-15  
**Author**: ComplyFlow Team
