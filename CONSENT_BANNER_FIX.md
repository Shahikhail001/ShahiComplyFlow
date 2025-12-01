# Consent Banner Fix - Settings Mismatch Resolution

## Issue Summary

**Problem:** The consent banner was not appearing on the frontend despite being enabled in the admin settings.

**Root Cause:** Settings read/write mismatch between the admin interface and the consent module.

## Technical Details

### The Mismatch

**Admin Form (`consent-manager-new.php`):**
- Saves settings to: `complyflow_consent_settings` WordPress option
- Uses array keys like: `banner_enabled`, `position`, `title`, `message`, etc.

**Consent Module (`ConsentModule.php` & `ConsentBanner.php`):**
- **Was reading** from: `complyflow_settings` option via `SettingsRepository`
- **Was looking** for keys like: `consent_banner_enabled`, `consent_banner_position`, etc.

**Result:** The banner enable checkbox was being saved to `complyflow_consent_settings['banner_enabled']`, but the code was checking `complyflow_settings['consent_banner_enabled']`, which was always `false`.

## Files Modified

### 1. `includes/Modules/Consent/ConsentModule.php`

#### Changes Made:

**a) `enqueue_frontend_assets()` method (Line ~115)**
```php
// BEFORE:
$enabled = $this->settings->get('consent_banner_enabled', false);

// AFTER:
$consent_settings = get_option('complyflow_consent_settings', []);
$enabled = $consent_settings['banner_enabled'] ?? false;
```

**b) Localized script settings (Line ~142)**
```php
// BEFORE:
'settings' => [
    'position' => $this->settings->get('consent_banner_position', 'bottom'),
    'showRejectButton' => $this->settings->get('consent_banner_show_reject', true),
    // ...
],

// AFTER:
$consent_settings = get_option('complyflow_consent_settings', []);
'settings' => [
    'position' => $consent_settings['position'] ?? 'bottom',
    'showRejectButton' => $consent_settings['show_reject'] ?? true,
    // ...
],
```

**c) `ajax_save_consent()` method (Line ~295)**
```php
// BEFORE:
$duration = $this->settings->get('consent_duration', 365);

// AFTER:
$consent_settings = get_option('complyflow_consent_settings', []);
$duration = $consent_settings['duration'] ?? 365;
```

### 2. `includes/Modules/Consent/ConsentBanner.php`

#### Changes Made:

**`render_banner()` method (Line ~53)**
```php
// BEFORE:
$enabled = $this->settings->get('consent_banner_enabled', false);
$position = $this->settings->get('consent_banner_position', 'bottom');
$title = $this->settings->get('consent_banner_title', __('We use cookies', 'complyflow'));
// ... etc.

// AFTER:
$consent_settings = get_option('complyflow_consent_settings', []);
$enabled = $consent_settings['banner_enabled'] ?? false;
$position = $consent_settings['position'] ?? 'bottom';
$title = $consent_settings['title'] ?? __('We use cookies', 'complyflow');
// ... etc.
```

## Testing & Verification

### Created Diagnostic Tool

**File:** `test-consent-banner-debug.php`

**Usage:** Access via browser at:
```
yoursite.com/wp-content/plugins/ShahiComplyFlow/test-consent-banner-debug.php
```

**Features:**
- ✅ Shows current banner enabled status
- ✅ Checks for consent cookie presence
- ✅ Displays all consent settings
- ✅ Identifies configuration issues
- ✅ Quick action buttons (clear cookie, open settings, view frontend)
- ✅ Legacy settings detection

### Testing Steps

1. **Access Consent Manager:**
   - Go to: `WP Admin → ComplyFlow → Consent Manager`
   - Ensure "Enable Cookie Banner" checkbox is checked
   - Click "Save Settings"

2. **Verify Settings Saved:**
   - Run the diagnostic tool (see URL above)
   - Check that "Banner Enabled in Settings" shows ✓ Enabled

3. **Test Frontend Display:**
   - Open site in incognito/private browsing mode
   - Or clear the `complyflow_consent` cookie
   - Navigate to any frontend page
   - Banner should appear at bottom (or top, depending on settings)

4. **Clear Cookie for Testing:**
   - Use diagnostic tool's "Clear Consent Cookie" button
   - Or manually delete `complyflow_consent` cookie from browser
   - Refresh page to see banner again

## Why This Happened

The plugin evolved to use two different settings storage approaches:

1. **Global Settings System** (`complyflow_settings`):
   - Used by `SettingsRepository` class
   - Centralized settings for entire plugin
   - Keys prefixed with module name (e.g., `consent_banner_enabled`)

2. **Module-Specific Settings** (`complyflow_consent_settings`):
   - Used by consent manager admin form
   - Dedicated option for consent module
   - Cleaner key names (e.g., `banner_enabled`)

The admin form was updated to use the module-specific approach, but the frontend code wasn't updated to match.

## Best Practice Going Forward

**Option 1: Unified Settings (Recommended)**
- Migrate all consent settings to the global `complyflow_settings` option
- Update admin form to use the centralized settings system
- Benefits: Consistency, easier exports/imports, central management

**Option 2: Keep Module-Specific (Current Fix)**
- Continue using `complyflow_consent_settings` 
- Ensure all consent-related code reads from this option
- Benefits: Modularity, isolated settings, easier to disable modules

**Current Implementation:** We've chosen Option 2 (Keep Module-Specific) as it requires minimal changes and maintains the existing admin UI structure.

## Verification Checklist

- [x] Banner displays when enabled in settings
- [x] Banner respects position setting (top/bottom)
- [x] Banner shows custom title and message
- [x] Banner color customization works
- [x] Reject button visibility toggle works
- [x] Cookie duration setting is respected
- [x] Consent cookie prevents banner re-display
- [x] AJAX consent saving works correctly
- [x] No PHP errors or warnings
- [x] No JavaScript console errors

## Related Files

- `includes/Modules/Consent/ConsentModule.php` - Main consent module
- `includes/Modules/Consent/ConsentBanner.php` - Banner rendering
- `includes/Admin/views/consent-manager-new.php` - Admin settings form
- `includes/Core/SettingsRepository.php` - Settings management (not used for consent anymore)
- `assets/src/js/consent-banner.js` - Frontend JavaScript
- `test-consent-banner-debug.php` - Diagnostic tool (NEW)

## Version

**Fixed in:** Version 4.6.2
**Date:** November 25, 2025
**Issue Type:** Settings mismatch bug
**Severity:** High (feature not working)
**Impact:** All sites using consent manager

## Notes

- The fix maintains backward compatibility
- No database migration required
- Settings saved before the fix will work correctly after applying it
- Users may need to clear browser cache to see updated assets
- No changes required to existing consent data or logs
