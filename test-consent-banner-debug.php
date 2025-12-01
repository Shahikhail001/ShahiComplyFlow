<?php
/**
 * Consent Banner Debug Script
 * 
 * This script helps diagnose consent banner issues by showing current settings
 * and testing if the banner would be displayed.
 * 
 * Usage: Access via: yoursite.com/wp-content/plugins/ShahiComplyFlow/test-consent-banner-debug.php
 * 
 * @package ComplyFlow
 * @since 4.6.2
 */

// Load WordPress
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to access this page.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>ComplyFlow Consent Banner Debug</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .section {
            background: white;
            padding: 24px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 { color: #1e293b; margin-top: 0; }
        h2 { color: #334155; font-size: 18px; margin-top: 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .status-ok { color: #10b981; font-weight: 600; }
        .status-error { color: #ef4444; font-weight: 600; }
        .status-warning { color: #f59e0b; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 3px; font-size: 14px; }
        .info { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px; margin: 16px 0; }
        .success { background: #f0fdf4; border-left: 4px solid #10b981; padding: 12px; margin: 16px 0; }
        .error { background: #fef2f2; border-left: 4px solid #ef4444; padding: 12px; margin: 16px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; margin-top: 10px; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <h1>🔍 ComplyFlow Consent Banner Diagnostic</h1>

    <?php
    // Get current settings
    $consent_settings = get_option('complyflow_consent_settings', []);
    $legacy_settings = get_option('complyflow_settings', []);
    
    // Check banner enabled status
    $banner_enabled = $consent_settings['banner_enabled'] ?? false;
    $has_cookie = isset($_COOKIE['complyflow_consent']);
    
    // Module status
    $module_manager = \ComplyFlow\Core\ModuleManager::class;
    $module_active = class_exists($module_manager);
    ?>

    <!-- Status Overview -->
    <div class="section">
        <h2>📊 Banner Status Overview</h2>
        <table>
            <tr>
                <th>Check</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Banner Enabled in Settings</td>
                <td><?php echo $banner_enabled ? '<span class="status-ok">✓ Enabled</span>' : '<span class="status-error">✗ Disabled</span>'; ?></td>
                <td><?php echo $banner_enabled ? 'Banner should display' : 'Enable banner in Consent Manager settings'; ?></td>
            </tr>
            <tr>
                <td>User Consent Cookie</td>
                <td><?php echo $has_cookie ? '<span class="status-warning">⚠ Present</span>' : '<span class="status-ok">✓ Not Set</span>'; ?></td>
                <td><?php echo $has_cookie ? 'Banner hidden (user already consented)' : 'Banner can display'; ?></td>
            </tr>
            <tr>
                <td>Consent Module</td>
                <td><?php echo $module_active ? '<span class="status-ok">✓ Active</span>' : '<span class="status-error">✗ Inactive</span>'; ?></td>
                <td><?php echo $module_active ? 'Module is loaded' : 'Check plugin activation'; ?></td>
            </tr>
            <tr>
                <td>Settings Option</td>
                <td><?php echo !empty($consent_settings) ? '<span class="status-ok">✓ Found</span>' : '<span class="status-error">✗ Empty</span>'; ?></td>
                <td><?php echo !empty($consent_settings) ? 'Settings are configured' : 'No settings saved yet'; ?></td>
            </tr>
        </table>

        <?php if ($banner_enabled && !$has_cookie): ?>
            <div class="success">
                <strong>✓ Banner should be displaying!</strong><br>
                If you don't see it on the frontend, check:
                <ul>
                    <li>Clear browser cache and cookies</li>
                    <li>Check browser console for JavaScript errors</li>
                    <li>Verify CSS/JS assets are loading</li>
                </ul>
            </div>
        <?php elseif ($has_cookie): ?>
            <div class="info">
                <strong>ℹ Banner hidden</strong><br>
                The banner is hidden because you've already consented. To test:
                <ul>
                    <li>Clear the <code>complyflow_consent</code> cookie</li>
                    <li>Use incognito/private browsing mode</li>
                    <li><a href="?clear_cookie=1" class="btn">Clear Consent Cookie</a></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="error">
                <strong>✗ Banner will not display</strong><br>
                The banner is disabled in settings. Go to <strong>ComplyFlow → Consent Manager</strong> and enable it.
            </div>
        <?php endif; ?>
    </div>

    <!-- Current Settings -->
    <div class="section">
        <h2>⚙️ Current Consent Settings (complyflow_consent_settings)</h2>
        <?php if (!empty($consent_settings)): ?>
            <table>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                </tr>
                <?php foreach ($consent_settings as $key => $value): ?>
                    <tr>
                        <td><code><?php echo esc_html($key); ?></code></td>
                        <td><?php echo is_bool($value) ? ($value ? 'true' : 'false') : esc_html(is_array($value) ? json_encode($value) : $value); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="error">No settings found. Save settings from the Consent Manager page.</div>
        <?php endif; ?>
    </div>

    <!-- Legacy Settings Check -->
    <?php if (!empty($legacy_settings)): ?>
    <div class="section">
        <h2>🔧 Legacy Settings (complyflow_settings)</h2>
        <div class="info">
            <strong>Note:</strong> Found legacy settings in <code>complyflow_settings</code> option. 
            These are NOT used by the consent banner anymore (fixed in 4.6.2).
        </div>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <?php 
            foreach ($legacy_settings as $key => $value): 
                if (strpos($key, 'consent') !== false):
            ?>
                <tr>
                    <td><code><?php echo esc_html($key); ?></code></td>
                    <td><?php echo is_bool($value) ? ($value ? 'true' : 'false') : esc_html(is_array($value) ? json_encode($value) : $value); ?></td>
                </tr>
            <?php 
                endif;
            endforeach; 
            ?>
        </table>
    </div>
    <?php endif; ?>

    <!-- Cookie Information -->
    <div class="section">
        <h2>🍪 Cookie Information</h2>
        <?php if ($has_cookie): ?>
            <table>
                <tr>
                    <th>Cookie Name</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td><code>complyflow_consent</code></td>
                    <td><code><?php echo esc_html($_COOKIE['complyflow_consent']); ?></code></td>
                </tr>
            </table>
        <?php else: ?>
            <div class="info">No consent cookie found. Banner will display on frontend.</div>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="section">
        <h2>🔧 Quick Actions</h2>
        <a href="<?php echo admin_url('admin.php?page=complyflow-consent'); ?>" class="btn">Open Consent Manager</a>
        <a href="<?php echo home_url(); ?>" class="btn" target="_blank">View Frontend</a>
        <?php if ($has_cookie): ?>
            <a href="?clear_cookie=1" class="btn">Clear Consent Cookie</a>
        <?php endif; ?>
    </div>

    <!-- Clear Cookie Handler -->
    <?php
    if (isset($_GET['clear_cookie'])) {
        setcookie('complyflow_consent', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        echo '<script>alert("Consent cookie cleared! The page will reload."); window.location.href = window.location.pathname;</script>';
    }
    ?>

</body>
</html>
