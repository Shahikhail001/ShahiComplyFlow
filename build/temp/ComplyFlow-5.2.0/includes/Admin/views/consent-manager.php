<?php
/**
 * Consent Manager Admin View
 *
 * @package ComplyFlow\Admin\Views
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = get_option('complyflow_consent_settings', []);
$banner_enabled = $settings['banner_enabled'] ?? true;
$position = $settings['position'] ?? 'bottom';
$title = $settings['title'] ?? __('We use cookies', 'complyflow');
$message = $settings['message'] ?? __('We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.', 'complyflow');
$show_reject = $settings['show_reject'] ?? true;
$primary_color = $settings['primary_color'] ?? '#2271b1';
$bg_color = $settings['bg_color'] ?? '#ffffff';
$auto_block = $settings['auto_block'] ?? true;
$duration = $settings['duration'] ?? 365;
$gdpr_enabled = $settings['gdpr_enabled'] ?? true;
$ccpa_enabled = $settings['ccpa_enabled'] ?? false;
$lgpd_enabled = $settings['lgpd_enabled'] ?? false;

// Get consent statistics
$logger = new \ComplyFlow\Modules\Consent\ConsentLogger();
$stats = $logger->get_statistics();
?>

<div class="complyflow-admin-page">
    <!-- Page Header with Gradient -->
    <div class="complyflow-page-header">
        <h1><?php esc_html_e('Consent Manager', 'complyflow'); ?></h1>
        <p class="page-subtitle"><?php esc_html_e('Manage cookie consent banners and compliance settings', 'complyflow'); ?></p>
    </div>

    <div class="complyflow-page-content">
        <!-- Statistics Overview -->
        <div class="complyflow-stats-grid">
            <div class="complyflow-stat-card">
                <div class="complyflow-stat-label"><?php esc_html_e('Total Consents', 'complyflow'); ?></div>
                <div class="complyflow-stat-value"><?php echo esc_html(number_format($stats['total'] ?? 0)); ?></div>
            </div>
            <div class="complyflow-stat-card">
                <div class="complyflow-stat-label"><?php esc_html_e('Last 30 Days', 'complyflow'); ?></div>
                <div class="complyflow-stat-value"><?php echo esc_html(number_format($stats['last_30_days'] ?? 0)); ?></div>
            </div>
            <div class="complyflow-stat-card">
                <div class="complyflow-stat-label"><?php esc_html_e('Consent Rate', 'complyflow'); ?></div>
                <div class="complyflow-stat-value"><?php echo esc_html(number_format($stats['consent_rate'] ?? 0, 1)); ?>%</div>
            </div>
        </div>

        <!-- Main Settings -->
        <div class="complyflow-card">
            <form method="post" action="options.php">
                <?php settings_fields('complyflow_consent'); ?>
                
                <!-- Banner Settings -->
                <h2><?php esc_html_e('Cookie Banner Settings', 'complyflow'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <?php esc_html_e('Enable Cookie Banner', 'complyflow'); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="complyflow_consent_banner_enabled" value="1" <?php checked($banner_enabled, 1); ?>>
                                        <?php esc_html_e('Display cookie consent banner on frontend', 'complyflow'); ?>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <?php esc_html_e('Banner Position', 'complyflow'); ?>
                                </th>
                                <td>
                                    <select name="complyflow_consent_position">
                                        <option value="bottom" <?php selected($position, 'bottom'); ?>><?php esc_html_e('Bottom', 'complyflow'); ?></option>
                                        <option value="top" <?php selected($position, 'top'); ?>><?php esc_html_e('Top', 'complyflow'); ?></option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="complyflow-banner-title"><?php esc_html_e('Banner Title', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="complyflow-banner-title" name="complyflow_consent_title" value="<?php echo esc_attr($title); ?>" class="regular-text">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="complyflow-banner-message"><?php esc_html_e('Banner Message', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <textarea id="complyflow-banner-message" name="complyflow_consent_message" rows="4" class="large-text"><?php echo esc_textarea($message); ?></textarea>
                                    <p class="description"><?php esc_html_e('HTML allowed', 'complyflow'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <?php esc_html_e('Show Reject Button', 'complyflow'); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="complyflow_consent_show_reject" value="1" <?php checked($show_reject, 1); ?>>
                                        <?php esc_html_e('Display "Reject All" button', 'complyflow'); ?>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="complyflow-primary-color"><?php esc_html_e('Primary Color', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="complyflow-primary-color" name="complyflow_consent_primary_color" value="<?php echo esc_attr($primary_color); ?>">
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="complyflow-bg-color"><?php esc_html_e('Background Color', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="complyflow-bg-color" name="complyflow_consent_bg_color" value="<?php echo esc_attr($bg_color); ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Cookie Blocking -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2><?php esc_html_e('Cookie Blocking', 'complyflow'); ?></h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <?php esc_html_e('Automatic Script Blocking', 'complyflow'); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="complyflow_consent_auto_block" value="1" <?php checked($auto_block, 1); ?>>
                                        <?php esc_html_e('Block scripts until consent is given', 'complyflow'); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e('Automatically blocks analytics and marketing scripts until user consents', 'complyflow'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="complyflow-consent-duration"><?php esc_html_e('Consent Duration (days)', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="complyflow-consent-duration" name="complyflow_consent_duration" value="<?php echo esc_attr($duration); ?>" min="1" max="3650">
                                    <p class="description"><?php esc_html_e('How long to remember user consent', 'complyflow'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Compliance Settings -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2><?php esc_html_e('Compliance Settings', 'complyflow'); ?></h2>
                    </div>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <?php esc_html_e('GDPR Mode', 'complyflow'); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="complyflow_consent_gdpr_enabled" value="1" <?php checked($gdpr_enabled, 1); ?>>
                                        <?php esc_html_e('Enable GDPR compliance (EU)', 'complyflow'); ?>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <?php esc_html_e('CCPA Mode', 'complyflow'); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="complyflow_consent_ccpa_enabled" value="1" <?php checked($ccpa_enabled, 1); ?>>
                                        <?php esc_html_e('Enable CCPA compliance (California)', 'complyflow'); ?>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <?php esc_html_e('LGPD Mode', 'complyflow'); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="complyflow_consent_lgpd_enabled" value="1" <?php checked($lgpd_enabled, 1); ?>>
                                        <?php esc_html_e('Enable LGPD compliance (Brazil)', 'complyflow'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php submit_button(__('Save Settings', 'complyflow')); ?>
            </form>

            <!-- Cookie Scanner -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2><?php esc_html_e('Cookie Scanner', 'complyflow'); ?></h2>
                </div>
                <div class="inside">
                    <p><?php esc_html_e('Scan your website to detect cookies automatically.', 'complyflow'); ?></p>
                    
                    <button type="button" id="complyflow-scan-cookies" class="button button-secondary">
                        <?php esc_html_e('Scan Cookies', 'complyflow'); ?>
                    </button>

                    <div id="complyflow-scan-results" style="margin-top: 20px;"></div>
                </div>
            </div>

            <!-- Managed Cookies -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2><?php esc_html_e('Managed Cookies', 'complyflow'); ?></h2>
                </div>
                <div class="inside">
                    <p><?php esc_html_e('View and manage cookies detected on your website.', 'complyflow'); ?></p>

                    <?php
                    $managed_cookies = $scanner->get_managed_cookies();
                    $cookie_count = 0;
                    foreach ($managed_cookies as $category => $cookies) {
                        $cookie_count += count($cookies);
                    }
                    ?>

                    <?php if ($cookie_count > 0): ?>
                        <div class="complyflow-cookie-tabs">
                            <nav class="nav-tab-wrapper">
                                <a href="#cookies-necessary" class="nav-tab nav-tab-active"><?php esc_html_e('Necessary', 'complyflow'); ?> (<?php echo count($managed_cookies['necessary'] ?? []); ?>)</a>
                                <a href="#cookies-analytics" class="nav-tab"><?php esc_html_e('Analytics', 'complyflow'); ?> (<?php echo count($managed_cookies['analytics'] ?? []); ?>)</a>
                                <a href="#cookies-marketing" class="nav-tab"><?php esc_html_e('Marketing', 'complyflow'); ?> (<?php echo count($managed_cookies['marketing'] ?? []); ?>)</a>
                                <a href="#cookies-preferences" class="nav-tab"><?php esc_html_e('Preferences', 'complyflow'); ?> (<?php echo count($managed_cookies['preferences'] ?? []); ?>)</a>
                            </nav>

                            <?php foreach ($managed_cookies as $category => $cookies): ?>
                                <div id="cookies-<?php echo esc_attr($category); ?>" class="complyflow-cookie-tab-content" style="<?php echo $category !== 'necessary' ? 'display:none;' : ''; ?>">
                                    <?php if (empty($cookies)): ?>
                                        <p><?php esc_html_e('No cookies in this category.', 'complyflow'); ?></p>
                                    <?php else: ?>
                                        <table class="wp-list-table widefat fixed striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 25%;"><?php esc_html_e('Cookie Name', 'complyflow'); ?></th>
                                                    <th style="width: 15%;"><?php esc_html_e('Domain', 'complyflow'); ?></th>
                                                    <th style="width: 15%;"><?php esc_html_e('Expiry', 'complyflow'); ?></th>
                                                    <th style="width: 35%;"><?php esc_html_e('Description', 'complyflow'); ?></th>
                                                    <th style="width: 10%;"><?php esc_html_e('Actions', 'complyflow'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cookies as $index => $cookie): ?>
                                                    <tr>
                                                        <td><strong><?php echo esc_html($cookie['name']); ?></strong></td>
                                                        <td><?php echo esc_html($cookie['domain'] ?? '-'); ?></td>
                                                        <td><?php echo esc_html($cookie['expiry'] ?? 'Session'); ?></td>
                                                        <td><?php echo esc_html($cookie['description'] ?? __('No description available', 'complyflow')); ?></td>
                                                        <td>
                                                            <button type="button" class="button button-small complyflow-delete-cookie" data-category="<?php echo esc_attr($category); ?>" data-index="<?php echo esc_attr($index); ?>">
                                                                <?php esc_html_e('Delete', 'complyflow'); ?>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div style="margin-top: 20px;">
                            <button type="button" id="complyflow-add-cookie" class="button button-secondary">
                                <?php esc_html_e('+ Add Cookie Manually', 'complyflow'); ?>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="notice notice-info inline">
                            <p><?php esc_html_e('No cookies detected yet. Click "Scan Cookies" to detect cookies automatically.', 'complyflow'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Add Cookie Modal -->
            <div id="complyflow-add-cookie-modal" style="display: none;">
                <div class="complyflow-modal-content">
                    <h3><?php esc_html_e('Add Cookie', 'complyflow'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="cookie-name"><?php esc_html_e('Cookie Name', 'complyflow'); ?></label></th>
                            <td><input type="text" id="cookie-name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="cookie-domain"><?php esc_html_e('Domain', 'complyflow'); ?></label></th>
                            <td><input type="text" id="cookie-domain" class="regular-text" placeholder=".example.com"></td>
                        </tr>
                        <tr>
                            <th><label for="cookie-expiry"><?php esc_html_e('Expiry', 'complyflow'); ?></label></th>
                            <td><input type="text" id="cookie-expiry" class="regular-text" placeholder="1 year"></td>
                        </tr>
                        <tr>
                            <th><label for="cookie-description"><?php esc_html_e('Description', 'complyflow'); ?></label></th>
                            <td><textarea id="cookie-description" class="large-text" rows="3"></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="cookie-category"><?php esc_html_e('Category', 'complyflow'); ?></label></th>
                            <td>
                                <select id="cookie-category">
                                    <option value="necessary"><?php esc_html_e('Necessary', 'complyflow'); ?></option>
                                    <option value="analytics"><?php esc_html_e('Analytics', 'complyflow'); ?></option>
                                    <option value="marketing"><?php esc_html_e('Marketing', 'complyflow'); ?></option>
                                    <option value="preferences"><?php esc_html_e('Preferences', 'complyflow'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <button type="button" id="save-cookie" class="button button-primary"><?php esc_html_e('Add Cookie', 'complyflow'); ?></button>
                        <button type="button" class="button complyflow-close-modal"><?php esc_html_e('Cancel', 'complyflow'); ?></button>
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="complyflow-admin-sidebar">
            <!-- Consent Statistics -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2><?php esc_html_e('Consent Statistics', 'complyflow'); ?></h2>
                </div>
                <div class="inside">
                    <div class="complyflow-stat">
                        <div class="complyflow-stat-value"><?php echo esc_html(number_format($stats['total_consents'])); ?></div>
                        <div class="complyflow-stat-label"><?php esc_html_e('Total Consents', 'complyflow'); ?></div>
                    </div>

                    <div class="complyflow-stat">
                        <div class="complyflow-stat-value"><?php echo esc_html(number_format($stats['last_30_days'])); ?></div>
                        <div class="complyflow-stat-label"><?php esc_html_e('Last 30 Days', 'complyflow'); ?></div>
                    </div>

                    <div class="complyflow-stat">
                        <div class="complyflow-stat-value"><?php echo esc_html($stats['acceptance_rate']); ?>%</div>
                        <div class="complyflow-stat-label"><?php esc_html_e('Acceptance Rate', 'complyflow'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2><?php esc_html_e('Quick Links', 'complyflow'); ?></h2>
                </div>
                <div class="inside">
                    <ul>
                        <li><a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-consent-logs')); ?>"><?php esc_html_e('View Consent Logs', 'complyflow'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/?complyflow-preview=consent')); ?>" target="_blank"><?php esc_html_e('Preview Banner', 'complyflow'); ?></a></li>
                        <li><a href="https://gdpr.eu/" target="_blank"><?php esc_html_e('GDPR Guidelines', 'complyflow'); ?></a></li>
                        <li><a href="https://oag.ca.gov/privacy/ccpa" target="_blank"><?php esc_html_e('CCPA Guidelines', 'complyflow'); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Cookie Scanner
    $('#complyflow-scan-cookies').on('click', function() {
        var $button = $(this);
        var $results = $('#complyflow-scan-results');

        $button.prop('disabled', true).text('<?php esc_html_e('Scanning...', 'complyflow'); ?>');
        $results.html('<p><?php esc_html_e('Scanning website for cookies...', 'complyflow'); ?></p>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_scan_cookies',
                nonce: '<?php echo esc_js(wp_create_nonce('complyflow_scan_cookies')); ?>',
                url: '<?php echo esc_js(home_url()); ?>'
            },
            success: function(response) {
                if (response.success && response.data.cookies) {
                    var html = '<div class="notice notice-success inline"><p>';
                    html += '<?php esc_html_e('Scan complete! Found', 'complyflow'); ?> ' + response.data.cookies.length + ' <?php esc_html_e('cookies.', 'complyflow'); ?>';
                    html += '</p></div>';
                    html += '<table class="wp-list-table widefat fixed striped">';
                    html += '<thead><tr>';
                    html += '<th><?php esc_html_e('Cookie Name', 'complyflow'); ?></th>';
                    html += '<th><?php esc_html_e('Category', 'complyflow'); ?></th>';
                    html += '<th><?php esc_html_e('Expiry', 'complyflow'); ?></th>';
                    html += '<th><?php esc_html_e('Description', 'complyflow'); ?></th>';
                    html += '</tr></thead><tbody>';

                    response.data.cookies.forEach(function(cookie) {
                        html += '<tr>';
                        html += '<td><strong>' + cookie.name + '</strong></td>';
                        html += '<td><span class="complyflow-badge complyflow-badge-' + cookie.category + '">' + cookie.category + '</span></td>';
                        html += '<td>' + (cookie.expiry || 'Session') + '</td>';
                        html += '<td>' + (cookie.description || '-') + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table>';
                    html += '<p style="margin-top: 15px;"><button class="button button-primary" id="save-scanned-cookies"><?php esc_html_e('Save These Cookies', 'complyflow'); ?></button></p>';
                    $results.html(html);
                } else {
                    $results.html('<div class="notice notice-error inline"><p><?php esc_html_e('Failed to scan cookies.', 'complyflow'); ?></p></div>');
                }
            },
            error: function() {
                $results.html('<div class="notice notice-error inline"><p><?php esc_html_e('An error occurred during scanning.', 'complyflow'); ?></p></div>');
            },
            complete: function() {
                $button.prop('disabled', false).text('<?php esc_html_e('Scan Cookies', 'complyflow'); ?>');
            }
        });
    });

    // Save scanned cookies
    $(document).on('click', '#save-scanned-cookies', function() {
        location.reload();
    });

    // Cookie Tabs
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.complyflow-cookie-tab-content').hide();
        $(target).show();
    });

    // Add Cookie Modal
    $('#complyflow-add-cookie').on('click', function() {
        $('#complyflow-add-cookie-modal').fadeIn();
    });

    $('.complyflow-close-modal').on('click', function() {
        $('#complyflow-add-cookie-modal').fadeOut();
    });

    // Save New Cookie
    $('#save-cookie').on('click', function() {
        var cookieData = {
            name: $('#cookie-name').val(),
            domain: $('#cookie-domain').val(),
            expiry: $('#cookie-expiry').val(),
            description: $('#cookie-description').val(),
            category: $('#cookie-category').val()
        };

        if (!cookieData.name) {
            alert('<?php esc_html_e('Cookie name is required', 'complyflow'); ?>');
            return;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_add_cookie',
                nonce: '<?php echo esc_js(wp_create_nonce('complyflow_manage_cookies')); ?>',
                cookie: cookieData
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || '<?php esc_html_e('Failed to add cookie', 'complyflow'); ?>');
                }
            }
        });
    });

    // Delete Cookie
    $('.complyflow-delete-cookie').on('click', function() {
        if (!confirm('<?php esc_html_e('Are you sure you want to delete this cookie?', 'complyflow'); ?>')) {
            return;
        }

        var $button = $(this);
        var category = $button.data('category');
        var index = $button.data('index');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'complyflow_delete_cookie',
                nonce: '<?php echo esc_js(wp_create_nonce('complyflow_manage_cookies')); ?>',
                category: category,
                index: index
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || '<?php esc_html_e('Failed to delete cookie', 'complyflow'); ?>');
                }
            }
        });
    });
});
</script>

<style>
.complyflow-admin-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    margin-top: 20px;
}

.complyflow-stat {
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid #dcdcde;
}

.complyflow-stat:last-child {
    border-bottom: none;
}

.complyflow-stat-value {
    font-size: 32px;
    font-weight: 600;
    color: #2271b1;
}

.complyflow-stat-label {
    font-size: 14px;
    color: #646970;
    margin-top: 5px;
}

.complyflow-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}

.complyflow-badge-necessary { background: #d1e7dd; color: #0a3622; }
.complyflow-badge-analytics { background: #cfe2ff; color: #052c65; }
.complyflow-badge-marketing { background: #f8d7da; color: #58151c; }
.complyflow-badge-preferences { background: #fff3cd; color: #664d03; }

.complyflow-cookie-tabs {
    margin-top: 20px;
}

.complyflow-cookie-tab-content {
    padding: 20px 0;
}

#complyflow-add-cookie-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.complyflow-modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.complyflow-modal-content h3 {
    margin-top: 0;
}

@media (max-width: 782px) {
    .complyflow-admin-grid {
        grid-template-columns: 1fr;
    }
}
</style>
