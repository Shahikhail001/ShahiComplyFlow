
# Consent Management

## What is Consent Management?
Consent Management in ComplyFlow is a comprehensive system for handling user consent for cookies and trackers, ensuring your site meets GDPR, CCPA, LGPD, and other privacy regulations. It provides a customizable consent banner, automatic cookie detection, and robust logging for compliance audits.

## Key Features

### 1. Consent Banner
- **Customizable Design**: Change banner position (top, bottom, modal), colors, text, and button styles to match your branding.
- **Multi-Language Support**: Display banners in the user’s language automatically.
- **Granular Consent**: Users can accept all, reject all, or opt in/out of specific cookie categories (Necessary, Analytics, Marketing, etc.).
- **Geo-Targeting**: Show different banners or consent requirements based on user location (EU, California, Brazil, Canada, custom regions).
- **Preview Mode**: Instantly preview banner changes in the admin settings.

### 2. Cookie Management
- **Automatic Detection**: Scans your site for cookies and trackers, categorizing them by provider and purpose.
- **Category Management**: Create, edit, or remove cookie categories as needed.
- **Manual Overrides**: Add or edit cookies manually for custom scripts or integrations.
- **Policy Integration**: Automatically updates your cookie policy with detected cookies and categories.
- **Script Blocking**: Blocks tracking scripts (GA, GTM, FB Pixel, Ads, YouTube, etc.) until consent is given.

### 3. Logging and Reporting
- **Consent Logs**: Every user consent action is logged with timestamp, IP (anonymized), and selected categories.
- **Audit Trails**: Full history of consent changes for compliance audits.
- **CSV Export**: Download consent logs for external review or regulatory requests.
- **Admin Log Viewer**: Search, filter, and review consent logs in the WordPress admin.

### 4. Integrations & Automation
- **AJAX Endpoints**: Save and retrieve consent without page reloads.
- **REST API**: Access consent data programmatically for advanced integrations.
- **SettingsRepository Integration**: All settings are stored securely and can be imported/exported.

## How to Use
1. **Configure the Banner**: Go to ComplyFlow → Settings → Consent Manager. Customize appearance, text, and categories.
2. **Enable Geo-Targeting**: Set up region-specific rules if needed.
3. **Review Detected Cookies**: Check the Cookie Inventory for automatically detected cookies and adjust categories as needed.
4. **Monitor Logs**: Use the Consent Logs viewer to track user actions and export data for compliance.

## Troubleshooting & Tips
- If the banner does not appear, ensure it is enabled in settings and not hidden by caching plugins.
- For missing cookies, run a manual scan or add them manually in the Cookie Inventory.
- Use the preview mode to test banner changes before publishing.
- Regularly export consent logs for backup and compliance.

## Advanced Usage
- **Custom Scripts**: Use the Script Blocker to add custom patterns for blocking or allowing scripts.
- **REST API**: Integrate with external systems using `/complyflow/v1/consent` endpoints.
- **Developer Hooks**: Extend consent logic with WordPress hooks and filters.

## Related Documentation
- See Cookie Inventory for more on managing cookies.
- See Legal Document Generator for updating your cookie policy.