# ComplyFlow Quick Start Guide

## 🚀 Getting Started in 5 Minutes

This guide will help you get ComplyFlow up and running quickly.

---

## Prerequisites Checklist

Before you begin, ensure you have:

- ✅ WordPress 6.4 or higher
- ✅ PHP 8.0 or higher
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ `mod_rewrite` enabled (for REST API)
- ✅ WordPress memory limit: 128MB+ recommended

---

## Installation

### Method 1: Standard WordPress Installation

1. **Download the plugin**:
   - Download `complyflow.zip` from your purchase
   - Or clone from GitHub: `git clone https://github.com/yourusername/complyflow.git`

2. **Upload to WordPress**:
   ```
   WP Admin → Plugins → Add New → Upload Plugin
   ```

3. **Activate the plugin**:
   ```
   WP Admin → Plugins → Activate ComplyFlow
   ```

4. **Verify activation**:
   - You should see "ComplyFlow" in the admin menu
   - Check for success notice: "ComplyFlow is now active!"

### Method 2: WP-CLI Installation

```bash
# Navigate to WordPress directory
cd /path/to/wordpress

# Install plugin
wp plugin install /path/to/complyflow.zip --activate

# Verify installation
wp plugin list | grep complyflow
```

---

## Initial Configuration

### Step 1: Access Settings

Navigate to: **WP Admin → ComplyFlow → Settings**

### Step 2: Configure General Settings

**General Tab**:
- Plugin Status: Enabled
- Default Language: Select your language
- Timezone: Select your timezone

### Step 3: Enable Modules

The plugin uses a modular system. Enable the features you need:

```php
// Via code (in functions.php or custom plugin)
add_action('complyflow_init', function() {
    $modules = ComplyFlow\Core\Plugin::instance()->get_module_manager();
    
    // Enable modules
    $modules->enable_module('accessibility');  // WCAG scanner
    $modules->enable_module('consent');        // Cookie consent
    $modules->enable_module('dsr');            // Data subject requests
    $modules->enable_module('legal');          // Legal documents
    $modules->enable_module('tracker');        // Cookie inventory
});
```

Or via WP-CLI:
```bash
# Enable specific module
wp eval "ComplyFlow\Core\Plugin::instance()->get_module_manager()->enable_module('accessibility');"
```

---

## Verify Installation

### 1. Check Database Tables

```bash
wp db query "SHOW TABLES LIKE 'wp_complyflow%';"
```

You should see 4 tables:
- `wp_complyflow_consent`
- `wp_complyflow_dsr`
- `wp_complyflow_scan_results`
- `wp_complyflow_tracker_inventory`

### 2. Test REST API

```bash
# Test consent endpoint
curl -X GET "https://yoursite.com/wp-json/complyflow/v1/consent/stats"

# Test scan endpoint
curl -X GET "https://yoursite.com/wp-json/complyflow/v1/scan"
```

### 3. Test WP-CLI Commands

```bash
# View plugin info
wp complyflow

# List settings
wp complyflow settings list

# View cache stats
wp complyflow cache stats
```

---

## Common Setup Tasks

### Task 1: Export Settings (Backup)

```bash
# Via WP-CLI
wp complyflow settings export --file=/path/to/backup.json

# Via REST API
curl -X GET "https://yoursite.com/wp-admin/admin-ajax.php?action=complyflow_export_settings&nonce=YOUR_NONCE"
```

### Task 2: Configure Cache

```php
// In wp-config.php or functions.php
define('COMPLYFLOW_CACHE_TTL', 3600); // 1 hour default

// Warm cache on init
add_action('complyflow_init', function() {
    $cache = ComplyFlow\Core\Cache::get_instance();
    $cache->warm_stats();
});
```

### Task 3: Set Up Cron Jobs (Future)

```bash
# Add to your server crontab
0 2 * * * wp complyflow scan cleanup --days=90 --yes
0 3 * * * wp complyflow consent cleanup --days=365 --yes
```

---

## Development Setup

If you're developing or customizing ComplyFlow:

### 1. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 2. Build Assets

```bash
# Development mode (with watch)
npm run dev

# Production build
npm run build
```

### 3. Code Quality Tools

```bash
# Run PHPCS (WordPress coding standards)
composer phpcs

# Run PHPStan (static analysis)
composer phpstan

# Run all quality checks
composer quality
```

### 4. Development Constants

Add to `wp-config.php`:

```php
// Enable debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// ComplyFlow dev mode
define('COMPLYFLOW_DEV_MODE', true);

// Script debug (use unminified assets)
define('SCRIPT_DEBUG', true);
```

---

## Testing Your Setup

### 1. Test Settings API

```php
// In functions.php or custom plugin
add_action('init', function() {
    $settings = ComplyFlow\Core\Plugin::instance()->get_settings();
    
    // Set a value
    $settings->set('test_setting', 'Hello World');
    
    // Get the value
    $value = $settings->get('test_setting');
    
    // Output
    error_log('ComplyFlow Test: ' . $value);
});
```

### 2. Test REST API

```javascript
// In browser console or custom script
async function testComplyFlowAPI() {
    const response = await fetch('/wp-json/complyflow/v1/consent/stats');
    const data = await response.json();
    console.log('ComplyFlow API Response:', data);
}

testComplyFlowAPI();
```

### 3. Test WP-CLI

```bash
# List all CLI commands
wp complyflow --help

# Get a setting
wp complyflow settings get consent_banner_enabled

# View cache statistics
wp complyflow cache stats --format=json
```

---

## Troubleshooting

### Issue: Plugin Won't Activate

**Solution**:
1. Check PHP version: `php -v` (must be 8.0+)
2. Check WordPress version: `wp core version` (must be 6.4+)
3. Check error logs: `wp-content/debug.log`
4. Enable debug mode in `wp-config.php`

### Issue: REST API 404 Errors

**Solution**:
1. Flush rewrite rules:
   ```bash
   wp rewrite flush
   ```
2. Check permalink structure: **Settings → Permalinks** (must not be "Plain")
3. Verify mod_rewrite is enabled

### Issue: Database Tables Not Created

**Solution**:
1. Check database user permissions (CREATE TABLE required)
2. Manually run activation:
   ```bash
   wp eval "ComplyFlow\Core\Activator::activate();"
   ```
3. Check database prefix in `wp-config.php`

### Issue: Assets Not Loading

**Solution**:
1. Build assets:
   ```bash
   npm run build
   ```
2. Check file permissions (755 for directories, 644 for files)
3. Verify `COMPLYFLOW_URL` constant is correct
4. Check browser console for 404 errors

### Issue: WP-CLI Commands Not Found

**Solution**:
1. Verify WP-CLI is installed:
   ```bash
   wp --version
   ```
2. Check if commands are registered:
   ```bash
   wp cli has-command complyflow
   ```
3. Ensure plugin is activated:
   ```bash
   wp plugin is-active complyflow
   ```

---

## Performance Optimization

### 1. Enable Object Cache

Install Redis or Memcached for better performance:

```bash
# Redis
apt-get install redis-server php-redis

# Memcached
apt-get install memcached php-memcached
```

Then install a WordPress object cache plugin.

### 2. Configure Cache TTLs

```php
// In functions.php
add_filter('complyflow_cache_ttl', function($ttl, $group) {
    switch ($group) {
        case 'settings':
            return 3600; // 1 hour
        case 'stats':
            return 900;  // 15 minutes
        case 'scans':
            return 86400; // 1 day
        default:
            return $ttl;
    }
}, 10, 2);
```

### 3. Warm Cache on Deploy

```bash
# Add to deployment script
wp complyflow cache warm
```

---

## Security Checklist

- ✅ Use HTTPS for all pages (especially DSR portal)
- ✅ Keep WordPress, PHP, and ComplyFlow updated
- ✅ Use strong passwords for admin accounts
- ✅ Enable WordPress security headers
- ✅ Restrict `manage_options` capability to trusted users
- ✅ Regular database backups
- ✅ Monitor REST API access logs
- ✅ Use nonces on all forms

---

## Next Steps

Now that ComplyFlow is installed and configured:

1. **Phase 2 - Accessibility Scanner** (In Development):
   - Run your first WCAG scan
   - Review accessibility issues
   - Generate compliance report

2. **Phase 3 - Consent Manager** (Coming Soon):
   - Configure cookie consent banner
   - Set up geo-detection
   - Implement script blocking

3. **Phase 4 - Legal Documents** (Coming Soon):
   - Generate privacy policy
   - Create terms of service
   - Customize templates

4. **Phase 5 - DSR Portal** (Coming Soon):
   - Enable data subject request form
   - Configure email notifications
   - Set up automated workflows

---

## Getting Help

### Documentation
- **Full Documentation**: [docs.complyflow.com](https://docs.complyflow.com)
- **API Reference**: [docs.complyflow.com/api](https://docs.complyflow.com/api)
- **Developer Guide**: [docs.complyflow.com/developers](https://docs.complyflow.com/developers)

### Support Channels
- **GitHub Issues**: [github.com/yourusername/complyflow/issues](https://github.com/yourusername/complyflow/issues)
- **WordPress Forum**: [wordpress.org/support/plugin/complyflow](https://wordpress.org/support/plugin/complyflow)
- **Email Support**: support@complyflow.com

### Community
- **Slack Channel**: [complyflow.slack.com](https://complyflow.slack.com)
- **Twitter**: [@ComplyFlowWP](https://twitter.com/ComplyFlowWP)
- **YouTube**: [ComplyFlow Tutorials](https://youtube.com/complyflow)

---

## Useful Resources

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [GDPR Compliance Guide](https://gdpr.eu/)
- [CCPA Overview](https://oag.ca.gov/privacy/ccpa)
- [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/)
- [WP-CLI Commands](https://developer.wordpress.org/cli/commands/)

---

**Ready to start?** Run your first command:

```bash
wp complyflow
```

🎉 **Welcome to ComplyFlow!**
