# Advanced Features Guide - ShahiComplyFlow v4.8.0

## 🎉 New Features Overview

This guide covers the 5 major advanced features added to the Legal Policy Generation System.

---

## 1. 📝 TinyMCE Rich Text Editor

### Overview
The edit modal now includes WordPress's built-in TinyMCE editor for WYSIWYG (What You See Is What You Get) editing of policy content.

### Features
- **Visual Editor**: Rich text formatting with toolbar controls
- **HTML Editor**: Direct HTML code editing mode
- **Preview Mode**: Live preview of how the policy will appear
- **Tab Navigation**: Seamlessly switch between Editor, HTML, and Preview views
- **Formatting Tools**: Bold, italic, underline, alignment, lists, links
- **Code View**: Full code editor with syntax highlighting
- **Fullscreen Mode**: Distraction-free editing experience

### How to Use
1. Navigate to **ComplyFlow → Legal Documents**
2. Click the **Edit** button on any generated policy
3. The modal opens with 3 tabs:
   - **Editor**: Visual WYSIWYG editor (default)
   - **HTML**: Raw HTML code editor
   - **Preview**: Live preview of the policy
4. Make your edits in any tab
5. Click **Save Changes** to persist your modifications

### Technical Details
- **Implementation**: `wp.editor.initialize()` API
- **Plugins**: lists, link, code, fullscreen, paste
- **Height**: 500px (resizable)
- **Auto-save**: Content automatically synced between tabs
- **Initialization Time**: ~500ms after modal opens

### Code Location
- **File**: `includes/Admin/views/legal-documents.php`
- **Lines**: 1305-1420 (TinyMCE initialization and handlers)

---

## 2. 🕐 Version History Viewer

### Overview
Track and manage all versions of your legal policies with a visual timeline interface.

### Features
- **Timeline Display**: Chronological view of all policy versions
- **Version Metadata**: Timestamp, file size, user who made changes
- **Current Version Badge**: Clearly marked current version
- **Version Preview**: View any historical version in full
- **Version Comparison**: Compare any two versions side-by-side
- **Version Restoration**: Rollback to any previous version
- **Auto-versioning**: Automatic version creation on save

### How to Use
1. Open the **Edit** modal for any policy
2. Click the **Version History** button (backup icon)
3. Timeline shows all versions with:
   - Version number
   - Timestamp
   - File size
   - User who modified it
   - "Current" badge for active version
4. Actions available per version:
   - **View**: Preview the version content
   - **Compare with Previous**: See differences
   - **Restore**: Rollback to that version (except current)

### Version History Storage
```php
// Stored in WordPress options table
{
  'version': 3,
  'timestamp': '2024-01-15 10:30:45',
  'size': '45.2 KB',
  'user': 'Admin User',
  'is_current': false,
  'changes_summary': 'Updated privacy sections'
}
```

### Technical Details
- **Storage**: WordPress options API (`{policy_type}_version_history`)
- **Version Limit**: Unlimited (consider implementing cleanup for very old versions)
- **Auto-save Before Restore**: Current version saved before restoring old one
- **Backend Handler**: `ajax_get_version_history()` in `DocumentsModule.php`

### Code Location
- **Frontend**: `includes/Admin/views/legal-documents.php` lines 1935-2055
- **Backend**: `includes/Modules/Documents/DocumentsModule.php` lines 642-705

---

## 3. 🔄 Policy Comparison Tool (Diff Viewer)

### Overview
Visual side-by-side comparison of policy versions with highlighted changes.

### Features
- **Line-by-Line Diff**: Detailed comparison of text content
- **Color-Coded Changes**:
  - 🟢 **Green**: Added lines
  - 🔴 **Red**: Removed lines
  - 🟡 **Yellow**: Modified sections (shown as remove + add)
- **Legend Display**: Clear visual guide for understanding diff colors
- **Scrollable View**: Handle large policy comparisons
- **Monospace Font**: Easy reading of code and formatting
- **Line Numbers**: Reference specific lines in policies

### How to Use
1. Open **Version History** for any policy
2. Click **Compare with Previous** on any version
3. Diff modal opens showing:
   - Version numbers being compared (e.g., "Version 2 vs Version 3")
   - Color-coded legend at the top
   - Line-by-line comparison with highlighting
4. Scroll through to review all changes
5. Close when done

### Diff Format Example
```
+ Added this new privacy clause
- Removed old outdated information
  Unchanged content appears normally
~ Modified section shown as removal + addition
```

### Technical Details
- **Algorithm**: Simple line-by-line text comparison
- **HTML Stripping**: Tags removed for comparison, preserved in display
- **Backend Handler**: `ajax_compare_versions()` and `generate_diff()`
- **Performance**: Efficient for policies up to 100KB

### Code Location
- **Frontend**: `includes/Admin/views/legal-documents.php` lines 2127-2192
- **Backend**: `includes/Modules/Documents/DocumentsModule.php` lines 857-956

---

## 4. 📄 PDF Export Functionality

### Overview
Export any legal policy as a PDF document for printing, sharing, or archival.

### Features
- **One-Click Export**: Single button to generate PDF
- **Print Dialog**: Uses browser's native print-to-PDF functionality
- **Formatted Output**: Maintains policy styling and formatting
- **Page Breaks**: Smart page break handling for sections
- **PDF Metadata**: Includes policy title and generation date
- **Print Optimized**: Special CSS for clean print output
- **Loading Indicator**: Spinning icon during PDF generation

### How to Use
1. Navigate to **Legal Documents** page
2. Locate the policy you want to export
3. Click the **Export PDF** button (PDF icon)
4. Browser print dialog opens automatically
5. Select "Save as PDF" as the printer
6. Choose destination and save
7. PDF file is created with policy content

### PDF Styling
- **Font**: System sans-serif stack for readability
- **Colors**: Blue headings, black text
- **Margins**: 2cm on all sides
- **Layout**: Max 800px width, centered
- **Page Breaks**: Avoid breaking within sections

### Technical Details
- **Method**: Browser print API (`window.print()`)
- **Alternative Options**:
  - **TCPDF**: PHP PDF generation library (server-side)
  - **Dompdf**: HTML to PDF converter (server-side)
  - **mPDF**: Unicode-supported PDF generator (server-side)
- **Current Implementation**: Client-side print dialog (no additional dependencies)
- **Future Enhancement**: Server-side PDF generation with library

### Code Location
- **Frontend**: `includes/Admin/views/legal-documents.php` lines 1823-1870
- **Backend**: `includes/Modules/Documents/DocumentsModule.php` lines 958-995 (stub for future server-side)

---

## 5. 🔔 Automated Policy Update Notifications

### Overview
Stay compliant with automatic notifications when compliance laws change and policies need updating.

### Features (Planned/Future Implementation)
- **Law Change Detection**: Monitor compliance regulation updates
- **Admin Notifications**: Dashboard notices for policy reviews
- **Email Alerts**: Automatic emails to administrators
- **Review Required Flag**: Visual indicator on outdated policies
- **Changelog Tracking**: Document what laws changed
- **Recommendation Engine**: Suggest which sections need updates
- **Compliance Calendar**: Schedule regular review reminders

### Implementation Status
⚠️ **Note**: This feature is currently in planning phase. The infrastructure has been prepared but full implementation requires:

1. **Compliance Law API**: Integration with legal update services
2. **Database Schema**: Table for tracking law changes
3. **Notification System**: WordPress admin notices and emails
4. **Scheduling**: WP-Cron jobs for periodic checks
5. **Review Workflow**: UI for marking policies as reviewed

### Proposed Workflow
```
1. System checks for law updates (daily via WP-Cron)
2. If changes detected → Create notification
3. Admin sees dashboard notice: "GDPR updated - Review Privacy Policy"
4. Admin clicks → Opens policy editor with highlighted sections
5. Admin reviews and updates policy
6. Admin marks as reviewed → Notification dismissed
```

### Future Database Structure
```sql
CREATE TABLE wp_complyflow_law_updates (
  id bigint(20) PRIMARY KEY AUTO_INCREMENT,
  law_type varchar(50) NOT NULL,
  law_name varchar(255) NOT NULL,
  change_description text,
  affected_policies text, -- JSON array
  update_date datetime,
  severity enum('critical','important','minor'),
  status enum('pending','reviewed','dismissed'),
  created_at datetime DEFAULT CURRENT_TIMESTAMP
);
```

### Code Location (Hooks Prepared)
- **Backend**: `includes/Modules/Documents/DocumentsModule.php`
- **Hooks**: `register_compliance_hooks()` method
- **Scheduled Tasks**: To be added in `WP-Cron` integration

---

## 🛠️ Technical Architecture

### Frontend Components

#### 1. Edit Modal System
```javascript
Components:
- .complyflow-modal-overlay (backdrop)
- .complyflow-modal-content (container)
- .complyflow-modal-header (title + tabs + close)
- .complyflow-modal-body (content area)
- .complyflow-modal-footer (action buttons)

Tabs:
- Editor Tab (TinyMCE instance)
- HTML Tab (plain textarea)
- Preview Tab (iframe preview)
```

#### 2. Version History Modal
```javascript
Components:
- .complyflow-history-modal (container)
- .complyflow-version-timeline (scrollable timeline)
- .complyflow-version-item (individual version card)
- .complyflow-version-badge (timeline dot indicator)
- .complyflow-version-actions (action buttons)
```

#### 3. Diff Viewer Modal
```javascript
Components:
- .complyflow-diff-legend (color guide)
- .complyflow-diff-view (comparison content)
- .diff-line-added (green added lines)
- .diff-line-removed (red removed lines)
- .diff-line-equal (unchanged lines)
```

### Backend Components

#### AJAX Endpoints
```php
// Version Management
wp_ajax_complyflow_get_version_history  - Get all versions
wp_ajax_complyflow_get_version          - Get specific version
wp_ajax_complyflow_compare_versions     - Compare two versions
wp_ajax_complyflow_restore_version      - Restore old version

// Policy Operations
wp_ajax_complyflow_get_policy           - Get current policy
wp_ajax_complyflow_save_policy          - Save edited policy
wp_ajax_complyflow_export_pdf           - Export as PDF (stub)
```

#### Data Storage
```php
Options Stored in wp_options:
- complyflow_generated_{type}                    // Current version
- complyflow_generated_{type}_edited             // Edited version
- complyflow_generated_{type}_edited_timestamp   // Last edit time
- complyflow_generated_{type}_manual_edit        // Manual edit flag
- complyflow_generated_{type}_version_history    // All versions array
```

### Security Features

1. **Nonce Verification**: All AJAX requests verified with `complyflow_generate_policy_nonce`
2. **Capability Checks**: `current_user_can('manage_options')` on all actions
3. **Content Sanitization**: `esc_html()` and `strip_tags()` where appropriate
4. **XSS Prevention**: Proper escaping in all output
5. **CSRF Protection**: WordPress nonce system

---

## 🎨 Styling & Design

### Color Scheme
```css
--cf-dash-primary: #2563eb      /* Primary blue */
--cf-dash-accent: #0ea5e9       /* Accent cyan */
--cf-dash-success: #16a34a      /* Success green */
--cf-dash-warning: #f97316      /* Warning orange */
--cf-dash-critical: #dc2626     /* Critical red */
```

### Modal Styling
- **Backdrop**: Blurred dark overlay (rgba(0,0,0,0.7))
- **Border Radius**: 12px (--cf-radius-md)
- **Shadow**: Deep shadow for depth
- **Header**: Gradient background matching dashboard
- **Animations**: Smooth fade-in transitions (200ms)

### Responsive Design
- **Desktop**: Full width modals up to max-width
- **Tablet**: 90% width with padding
- **Mobile**: Full viewport with minimal padding

---

## 🧪 Testing Checklist

### TinyMCE Editor
- [ ] Editor initializes properly in modal
- [ ] Visual mode works with formatting
- [ ] HTML mode shows correct code
- [ ] Preview mode displays accurately
- [ ] Tab switching preserves content
- [ ] Save button captures editor content
- [ ] Special characters don't break editor
- [ ] Large content (>100KB) loads smoothly

### Version History
- [ ] Timeline displays all versions
- [ ] Current version is marked correctly
- [ ] Version metadata shows accurate info
- [ ] View version button opens preview
- [ ] Compare button shows diff
- [ ] Restore button works for old versions
- [ ] Auto-save creates new version on edit

### Comparison Tool
- [ ] Diff displays added lines in green
- [ ] Diff displays removed lines in red
- [ ] Line numbers are accurate
- [ ] Large comparisons scroll properly
- [ ] Legend displays correctly
- [ ] Close button works

### PDF Export
- [ ] Export button triggers print dialog
- [ ] PDF contains all policy content
- [ ] Formatting is preserved
- [ ] Page breaks work correctly
- [ ] Headers/footers appear
- [ ] Loading indicator shows during generation
- [ ] Export works on all browsers

---

## 📊 Performance Metrics

### Load Times (Expected)
- **Edit Modal Open**: < 1 second
- **TinyMCE Initialization**: ~500ms
- **Version History Load**: < 500ms
- **Diff Generation**: < 1 second
- **PDF Export**: < 2 seconds

### Memory Usage
- **TinyMCE Instance**: ~5-10MB
- **Version History**: ~1KB per version
- **Diff Calculation**: ~2x policy size

### Database Impact
- **Version Storage**: ~50KB per version
- **Query Count**: +2 queries per version operation
- **Option Size**: Consider cleanup after 50+ versions

---

## 🔧 Configuration Options

### Customization Points

#### 1. TinyMCE Settings
```javascript
// File: includes/Admin/views/legal-documents.php:1383
tinymce: {
  wpautop: false,                    // Disable auto-paragraphs
  height: 500,                       // Editor height
  plugins: 'lists link code fullscreen paste',
  toolbar1: 'formatselect | bold italic...',
  content_style: 'body { ... }'     // Editor styling
}
```

#### 2. Version History Limit
```php
// Add to DocumentsModule.php
private $max_versions = 50;

// In ajax_save_policy(), add:
if (count($version_history) > $this->max_versions) {
    array_shift($version_history); // Remove oldest
}
```

#### 3. PDF Page Settings
```css
/* File: includes/Admin/views/legal-documents.php:1847 */
@page {
  margin: 2cm;      /* Page margins */
  size: A4;         /* Paper size */
  orientation: portrait;
}
```

---

## 🐛 Troubleshooting

### TinyMCE Not Loading
**Problem**: Editor appears as plain textarea  
**Solution**: 
1. Check browser console for errors
2. Verify WordPress version >= 5.0
3. Clear browser cache
4. Disable conflicting plugins

### Version History Empty
**Problem**: No versions showing in timeline  
**Solution**:
1. Generate and save a policy first
2. Check `wp_options` table for `{type}_version_history`
3. Verify user has `manage_options` capability

### Diff Not Showing Changes
**Problem**: Comparison shows everything as unchanged  
**Solution**:
1. Ensure versions actually differ
2. Check if HTML tags are interfering
3. Verify `generate_diff()` method is being called

### PDF Export Fails
**Problem**: Print dialog doesn't open  
**Solution**:
1. Check popup blocker settings
2. Verify AJAX response contains content
3. Test in different browser
4. Check browser console for JavaScript errors

---

## 🚀 Future Enhancements

### Planned Features

1. **Server-Side PDF Generation**
   - Integrate TCPDF or Dompdf
   - Generate PDFs without print dialog
   - Add PDF headers/footers with branding
   - Support for custom paper sizes

2. **Advanced Diff Algorithm**
   - Word-level differences (not just line-level)
   - Inline diff highlighting
   - Character-by-character comparison
   - Syntax-aware HTML diffing

3. **Version Branching**
   - Create named versions (v1.0, v2.0, etc.)
   - Tag important milestones
   - Branch from any version
   - Merge changes from branches

4. **Collaboration Features**
   - Multi-user editing with conflict resolution
   - Comment system on policy sections
   - Approval workflow for policy changes
   - Role-based editing permissions

5. **AI-Powered Updates**
   - Suggest changes based on law updates
   - Auto-generate compliance sections
   - Natural language policy queries
   - Smart content recommendations

6. **Export Options**
   - Microsoft Word (.docx) export
   - Markdown (.md) export
   - JSON structured data export
   - Multiple language versions

7. **Compliance Dashboard**
   - Visual compliance score
   - Missing sections highlighter
   - Regulation coverage matrix
   - Audit trail reporting

---

## 📚 API Reference

### JavaScript Functions

#### `showVersionHistory(policyType)`
Opens version history modal for specified policy.

**Parameters:**
- `policyType` (string): Policy type identifier

**Example:**
```javascript
showVersionHistory('privacy_policy');
```

#### `viewVersion(policyType, version)`
Preview specific version of a policy.

**Parameters:**
- `policyType` (string): Policy type
- `version` (int): Version number

#### `compareVersions(policyType, version1, version2)`
Show diff between two versions.

**Parameters:**
- `policyType` (string): Policy type
- `version1` (int): First version number
- `version2` (int): Second version number

#### `restoreVersion(policyType, version)`
Restore an old version as current.

**Parameters:**
- `policyType` (string): Policy type
- `version` (int): Version to restore

### PHP Methods

#### `DocumentsModule::ajax_get_version_history()`
AJAX handler for retrieving version history.

**Returns:** JSON with versions array

#### `DocumentsModule::ajax_compare_versions()`
AJAX handler for version comparison.

**Returns:** JSON with HTML diff

#### `DocumentsModule::generate_diff($content1, $content2)`
Generate HTML diff between two content strings.

**Parameters:**
- `$content1` (string): First content
- `$content2` (string): Second content

**Returns:** (string) HTML formatted diff

---

## 📝 Changelog

### Version 4.8.0 (2024-01-15)
**Added:**
- TinyMCE rich text editor with Visual/HTML/Preview tabs
- Complete version history tracking system
- Visual timeline UI for version management
- Policy comparison tool with diff viewer
- PDF export via browser print dialog
- Loading animations and UI enhancements
- Comprehensive styling for all new modals

**Improved:**
- Edit modal now 900px wide for better editing
- Version metadata includes size and user info
- Diff viewer with color-coded changes
- Modal close handlers with confirmation

**Fixed:**
- TinyMCE content retrieval on save
- Version history storage format
- Diff generation for large policies

---

## 🤝 Contributing

To contribute to these features or report issues:

1. **Bug Reports**: Use WordPress debug mode and check error logs
2. **Feature Requests**: Document use case and expected behavior
3. **Code Contributions**: Follow WordPress coding standards
4. **Testing**: Test on multiple browsers and WordPress versions

---

## 📞 Support

For questions or issues with these features:

- **Documentation**: This guide
- **Code Comments**: Inline documentation in source files
- **WordPress Forums**: ComplyFlow plugin support
- **GitHub Issues**: Report bugs or request features

---

**Last Updated**: January 15, 2024  
**Version**: 4.8.0  
**Compatibility**: WordPress 5.8+, PHP 8.0+
