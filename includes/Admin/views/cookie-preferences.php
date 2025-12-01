<?php
/**
 * Cookie Preferences View
 *
 * User-facing preferences center for managing cookie consent.
 *
 * @package ComplyFlow\Admin\Views
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current consent from cookie
$current_consent = null;
if (isset($_COOKIE['complyflow_consent'])) {
    $current_consent = json_decode(stripslashes($_COOKIE['complyflow_consent']), true);
}

// Get managed cookies
$managed_cookies = $scanner->get_managed_cookies();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_e('Cookie Preferences', 'complyflow'); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f0f0f1;
            padding: 40px 20px;
        }

        .complyflow-preferences {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .preferences-header {
            padding: 30px;
            background: #2271b1;
            color: #fff;
        }

        .preferences-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }

        .preferences-header p {
            margin: 0;
            opacity: 0.9;
        }

        .preferences-content {
            padding: 30px;
        }

        .preference-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #dcdcde;
        }

        .preference-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .preference-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .preference-header h2 {
            margin: 0;
            font-size: 18px;
            color: #1d2327;
        }

        .preference-description {
            color: #646970;
            margin-bottom: 15px;
        }

        .cookie-list {
            background: #f6f7f7;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }

        .cookie-list h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #1d2327;
        }

        .cookie-list ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .cookie-list li {
            padding: 8px 0;
            border-bottom: 1px solid #dcdcde;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cookie-list li:last-child {
            border-bottom: none;
        }

        .cookie-name {
            font-weight: 500;
            color: #1d2327;
        }

        .cookie-expiry {
            font-size: 12px;
            color: #646970;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #dcdcde;
            transition: .4s;
            border-radius: 26px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .toggle-switch input:checked + .toggle-slider {
            background-color: #2271b1;
        }

        .toggle-switch input:disabled + .toggle-slider {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        .preferences-actions {
            padding: 20px 30px;
            background: #f6f7f7;
            border-top: 1px solid #dcdcde;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background: #2271b1;
            color: #fff;
        }

        .btn-primary:hover {
            background: #135e96;
        }

        .btn-secondary {
            background: #fff;
            color: #2271b1;
            border: 1px solid #2271b1;
        }

        .btn-secondary:hover {
            background: #f6f7f7;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .consent-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .consent-status.active {
            background: #d1e7dd;
            color: #0a3622;
        }

        .consent-status.inactive {
            background: #f8d7da;
            color: #58151c;
        }

        .notice {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .notice-success {
            background: #d1e7dd;
            color: #0a3622;
            border-left: 4px solid #0a3622;
        }

        .notice-info {
            background: #cfe2ff;
            color: #052c65;
            border-left: 4px solid #052c65;
        }

        @media (max-width: 600px) {
            .preferences-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="complyflow-preferences">
        <div class="preferences-header">
            <h1><?php esc_html_e('Cookie Preferences', 'complyflow'); ?></h1>
            <p><?php esc_html_e('Manage your cookie consent preferences and see what cookies we use.', 'complyflow'); ?></p>
        </div>

        <div class="preferences-content">
            <div id="preference-notice"></div>

            <!-- Necessary Cookies -->
            <div class="preference-section">
                <div class="preference-header">
                    <h2><?php esc_html_e('Necessary Cookies', 'complyflow'); ?></h2>
                    <span class="consent-status active"><?php esc_html_e('Always Active', 'complyflow'); ?></span>
                </div>
                <p class="preference-description">
                    <?php esc_html_e('These cookies are essential for the website to function properly. They enable core functionality such as security, network management, and accessibility. You cannot disable these cookies.', 'complyflow'); ?>
                </p>
                <?php if (!empty($managed_cookies['necessary'])): ?>
                <div class="cookie-list">
                    <h3><?php esc_html_e('Cookies Used:', 'complyflow'); ?></h3>
                    <ul>
                        <?php foreach ($managed_cookies['necessary'] as $cookie): ?>
                        <li>
                            <span class="cookie-name"><?php echo esc_html($cookie['name']); ?></span>
                            <span class="cookie-expiry"><?php echo esc_html($cookie['expiry'] ?? 'Session'); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Analytics Cookies -->
            <div class="preference-section">
                <div class="preference-header">
                    <h2><?php esc_html_e('Analytics Cookies', 'complyflow'); ?></h2>
                    <label class="toggle-switch">
                        <input type="checkbox" id="pref-analytics" <?php checked($current_consent['analytics'] ?? false, true); ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <p class="preference-description">
                    <?php esc_html_e('These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously. This helps us improve the website experience.', 'complyflow'); ?>
                </p>
                <?php if (!empty($managed_cookies['analytics'])): ?>
                <div class="cookie-list">
                    <h3><?php esc_html_e('Cookies Used:', 'complyflow'); ?></h3>
                    <ul>
                        <?php foreach ($managed_cookies['analytics'] as $cookie): ?>
                        <li>
                            <span class="cookie-name"><?php echo esc_html($cookie['name']); ?></span>
                            <span class="cookie-expiry"><?php echo esc_html($cookie['expiry'] ?? 'Session'); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Marketing Cookies -->
            <div class="preference-section">
                <div class="preference-header">
                    <h2><?php esc_html_e('Marketing Cookies', 'complyflow'); ?></h2>
                    <label class="toggle-switch">
                        <input type="checkbox" id="pref-marketing" <?php checked($current_consent['marketing'] ?? false, true); ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <p class="preference-description">
                    <?php esc_html_e('These cookies are used to track visitors across websites. They are used to display ads that are relevant and engaging for the individual user.', 'complyflow'); ?>
                </p>
                <?php if (!empty($managed_cookies['marketing'])): ?>
                <div class="cookie-list">
                    <h3><?php esc_html_e('Cookies Used:', 'complyflow'); ?></h3>
                    <ul>
                        <?php foreach ($managed_cookies['marketing'] as $cookie): ?>
                        <li>
                            <span class="cookie-name"><?php echo esc_html($cookie['name']); ?></span>
                            <span class="cookie-expiry"><?php echo esc_html($cookie['expiry'] ?? 'Session'); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Preferences Cookies -->
            <div class="preference-section">
                <div class="preference-header">
                    <h2><?php esc_html_e('Preference Cookies', 'complyflow'); ?></h2>
                    <label class="toggle-switch">
                        <input type="checkbox" id="pref-preferences" <?php checked($current_consent['preferences'] ?? false, true); ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <p class="preference-description">
                    <?php esc_html_e('These cookies enable the website to remember choices you make (such as language or region) and provide enhanced, more personal features.', 'complyflow'); ?>
                </p>
                <?php if (!empty($managed_cookies['preferences'])): ?>
                <div class="cookie-list">
                    <h3><?php esc_html_e('Cookies Used:', 'complyflow'); ?></h3>
                    <ul>
                        <?php foreach ($managed_cookies['preferences'] as $cookie): ?>
                        <li>
                            <span class="cookie-name"><?php echo esc_html($cookie['name']); ?></span>
                            <span class="cookie-expiry"><?php echo esc_html($cookie['expiry'] ?? 'Session'); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="preferences-actions">
            <button type="button" id="save-preferences" class="btn btn-primary">
                <?php esc_html_e('Save Preferences', 'complyflow'); ?>
            </button>
            <button type="button" id="accept-all" class="btn btn-secondary">
                <?php esc_html_e('Accept All', 'complyflow'); ?>
            </button>
            <button type="button" id="reject-all" class="btn btn-secondary">
                <?php esc_html_e('Reject All', 'complyflow'); ?>
            </button>
        </div>
    </div>

    <script>
    (function() {
        'use strict';

        var saveBtn = document.getElementById('save-preferences');
        var acceptBtn = document.getElementById('accept-all');
        var rejectBtn = document.getElementById('reject-all');

        function saveConsent(consent) {
            // Set cookie
            var duration = 365;
            var date = new Date();
            date.setTime(date.getTime() + (duration * 24 * 60 * 60 * 1000));
            var expires = 'expires=' + date.toUTCString();
            document.cookie = 'complyflow_consent=' + JSON.stringify(consent) + ';' + expires + ';path=/;SameSite=Lax';

            // Show success notice
            var notice = document.getElementById('preference-notice');
            notice.innerHTML = '<div class="notice notice-success"><?php esc_html_e('Your preferences have been saved successfully.', 'complyflow'); ?></div>';

            // Reload after delay
            setTimeout(function() {
                window.location.href = '<?php echo esc_url(home_url()); ?>';
            }, 1500);
        }

        saveBtn.addEventListener('click', function() {
            var consent = {
                necessary: true,
                analytics: document.getElementById('pref-analytics').checked,
                marketing: document.getElementById('pref-marketing').checked,
                preferences: document.getElementById('pref-preferences').checked
            };
            saveConsent(consent);
        });

        acceptBtn.addEventListener('click', function() {
            var consent = {
                necessary: true,
                analytics: true,
                marketing: true,
                preferences: true
            };
            
            // Update toggles
            document.getElementById('pref-analytics').checked = true;
            document.getElementById('pref-marketing').checked = true;
            document.getElementById('pref-preferences').checked = true;
            
            saveConsent(consent);
        });

        rejectBtn.addEventListener('click', function() {
            var consent = {
                necessary: true,
                analytics: false,
                marketing: false,
                preferences: false
            };
            
            // Update toggles
            document.getElementById('pref-analytics').checked = false;
            document.getElementById('pref-marketing').checked = false;
            document.getElementById('pref-preferences').checked = false;
            
            saveConsent(consent);
        });
    })();
    </script>

    <?php wp_footer(); ?>
</body>
</html>
