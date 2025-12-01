<?php
/**
 * Cookie Inventory Admin View
 *
 * @package ComplyFlow\Modules\Cookie
 * @since   3.3.1
 */

if (!defined('ABSPATH')) {
    exit;
}

use ComplyFlow\Modules\Cookie\CookieInventory;

$settings = ComplyFlow\Core\SettingsRepository::get_instance();
$inventory = new CookieInventory($settings);

$cookies = $inventory->get_all();
$stats = $inventory->get_stats();
?>

<div class="complyflow-admin-page">
    <!-- Page Header with Gradient -->
    <div class="complyflow-page-header">
        <h1><?php esc_html_e('Cookie Inventory', 'complyflow'); ?></h1>
        <p class="page-subtitle"><?php esc_html_e('Detect, categorize, and manage cookies and tracking technologies on your website', 'complyflow'); ?></p>
    </div>

    <div class="complyflow-page-content">
        <!-- Information Banner -->
        <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 4px solid #2563eb; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
            <div style="display: flex; align-items: start; gap: 16px;">
                <span class="dashicons dashicons-info" style="color: #1e40af; font-size: 24px; flex-shrink: 0; margin-top: 2px;"></span>
                <div>
                    <h3 style="margin: 0 0 12px 0; color: #1e3a8a; font-size: 16px; font-weight: 600;">
                        <?php esc_html_e('Cookie Inventory Management', 'complyflow'); ?>
                    </h3>
                    <p style="margin: 0 0 12px 0; color: #1e40af; line-height: 1.6;">
                        <?php esc_html_e('This inventory tracks all cookies and tracking technologies on your website. It supports 25+ major services including Google Analytics, Facebook Pixel, TikTok, LinkedIn, and more.', 'complyflow'); ?>
                    </p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; margin-top: 16px;">
                        <div style="background: rgba(255, 255, 255, 0.7); padding: 12px; border-radius: 6px;">
                            <strong style="color: #1e3a8a; display: block; margin-bottom: 4px;">
                                <span class="dashicons dashicons-search" style="font-size: 16px; vertical-align: middle;"></span>
                                <?php esc_html_e('Scan for Cookies', 'complyflow'); ?>
                            </strong>
                            <span style="color: #1e40af; font-size: 13px;"><?php esc_html_e('Automatically detect cookies and trackers on your site. Scans homepage and analyzes JavaScript/iframes.', 'complyflow'); ?></span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.7); padding: 12px; border-radius: 6px;">
                            <strong style="color: #1e3a8a; display: block; margin-bottom: 4px;">
                                <span class="dashicons dashicons-edit" style="font-size: 16px; vertical-align: middle;"></span>
                                <?php esc_html_e('Edit Cookies', 'complyflow'); ?>
                            </strong>
                            <span style="color: #1e40af; font-size: 13px;"><?php esc_html_e('Refine auto-detected cookie descriptions, update purposes, expiry times, and provider information.', 'complyflow'); ?></span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.7); padding: 12px; border-radius: 6px;">
                            <strong style="color: #1e3a8a; display: block; margin-bottom: 4px;">
                                <span class="dashicons dashicons-plus" style="font-size: 16px; vertical-align: middle;"></span>
                                <?php esc_html_e('Add External Cookies', 'complyflow'); ?>
                            </strong>
                            <span style="color: #1e40af; font-size: 13px;"><?php esc_html_e('Manually document cookies from iframes, embedded widgets, or third-party scripts the scanner cannot detect.', 'complyflow'); ?></span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.7); padding: 12px; border-radius: 6px;">
                            <strong style="color: #1e3a8a; display: block; margin-bottom: 4px;">
                                <span class="dashicons dashicons-upload" style="font-size: 16px; vertical-align: middle;"></span>
                                <?php esc_html_e('Import CSV', 'complyflow'); ?>
                            </strong>
                            <span style="color: #1e40af; font-size: 13px;"><?php esc_html_e('Bulk import cookies from other tools or previous audits. Supports validation and error reporting.', 'complyflow'); ?></span>
                        </div>
                    </div>
                    <p style="margin: 16px 0 0 0; color: #1e40af; font-size: 13px; font-style: italic;">
                        <strong><?php esc_html_e('Note:', 'complyflow'); ?></strong> <?php esc_html_e('Cookies marked with an orange MANUAL badge were either added manually or imported from CSV, not automatically scanned.', 'complyflow'); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="cf-flex cf-flex-between" style="margin-bottom: 24px;">
            <div class="cf-flex" style="gap: 12px;">
                <button type="button" id="scan-cookies" class="complyflow-button-primary">
                    <span class="dashicons dashicons-search" style="margin-right: 6px;"></span>
                    <?php esc_html_e('Scan for Cookies', 'complyflow'); ?>
                </button>
                <button type="button" id="add-external-cookie" class="complyflow-button-secondary">
                    <span class="dashicons dashicons-plus" style="margin-right: 6px;"></span>
                    <?php esc_html_e('Add External Cookie', 'complyflow'); ?>
                </button>
            </div>
            <div class="cf-flex" style="gap: 12px;">
                <button type="button" id="import-cookies-csv" class="complyflow-button-secondary">
                    <span class="dashicons dashicons-upload" style="margin-right: 6px;"></span>
                    <?php esc_html_e('Import CSV', 'complyflow'); ?>
                </button>
                <button type="button" id="export-cookies-csv" class="complyflow-button-secondary">
                    <span class="dashicons dashicons-download" style="margin-right: 6px;"></span>
                    <?php esc_html_e('Export CSV', 'complyflow'); ?>
                </button>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="complyflow-stats-grid">
            <div class="complyflow-stat-card">
                <div class="stat-icon" style="background: var(--cf-primary); color: white;">
                    <span class="dashicons dashicons-admin-generic"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($stats['total']); ?></div>
                    <div class="stat-label"><?php esc_html_e('Total Cookies', 'complyflow'); ?></div>
                </div>
            </div>
            <div class="complyflow-stat-card">
                <div class="stat-icon" style="background: #10b981; color: white;">
                    <span class="dashicons dashicons-shield"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($stats['by_category']['necessary']); ?></div>
                    <div class="stat-label"><?php esc_html_e('Necessary', 'complyflow'); ?></div>
                </div>
            </div>
            <div class="complyflow-stat-card">
                <div class="stat-icon" style="background: #3b82f6; color: white;">
                    <span class="dashicons dashicons-admin-tools"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($stats['by_category']['functional']); ?></div>
                    <div class="stat-label"><?php esc_html_e('Functional', 'complyflow'); ?></div>
                </div>
            </div>
            <div class="complyflow-stat-card">
                <div class="stat-icon" style="background: #8b5cf6; color: white;">
                    <span class="dashicons dashicons-chart-bar"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($stats['by_category']['analytics']); ?></div>
                    <div class="stat-label"><?php esc_html_e('Analytics', 'complyflow'); ?></div>
                </div>
            </div>
            <div class="complyflow-stat-card">
                <div class="stat-icon" style="background: #ec4899; color: white;">
                    <span class="dashicons dashicons-megaphone"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($stats['by_category']['marketing']); ?></div>
                    <div class="stat-label"><?php esc_html_e('Marketing', 'complyflow'); ?></div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="complyflow-card" style="margin-top: 32px;">
            <div class="cf-flex cf-flex-between" style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--cf-border);">
                <div class="cf-flex" style="gap: 12px; align-items: center;">
                    <select name="bulk_action" id="bulk-action-selector-top" class="complyflow-select" style="min-width: 180px;">
                        <option value=""><?php esc_html_e('Bulk Actions', 'complyflow'); ?></option>
                        <option value="necessary"><?php esc_html_e('Set as Necessary', 'complyflow'); ?></option>
                        <option value="functional"><?php esc_html_e('Set as Functional', 'complyflow'); ?></option>
                        <option value="analytics"><?php esc_html_e('Set as Analytics', 'complyflow'); ?></option>
                        <option value="marketing"><?php esc_html_e('Set as Marketing', 'complyflow'); ?></option>
                    </select>
                    <button type="button" id="do-bulk-action" class="complyflow-button-secondary"><?php esc_html_e('Apply', 'complyflow'); ?></button>
                </div>
            </div>

            <!-- Cookies Table -->
            <table class="complyflow-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all-cookies">
                        </th>
                        <th><?php esc_html_e('Cookie Name', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Provider', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Category', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Type', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Purpose', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Expiry', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Actions', 'complyflow'); ?></th>
                    </tr>
                </thead>
                <tbody id="cookie-table-body">
                    <?php if (empty($cookies)): ?>
                    <tr class="no-items">
                        <td colspan="8">
                            <div class="complyflow-empty-state">
                                <div class="empty-icon">
                                    <span class="dashicons dashicons-admin-generic" style="font-size: 48px; color: var(--cf-muted);"></span>
                                </div>
                                <h3><?php esc_html_e('No Cookies Found', 'complyflow'); ?></h3>
                                <p><?php esc_html_e('Click "Scan for Cookies" to automatically detect cookies and tracking technologies on your website.', 'complyflow'); ?></p>
                                <button type="button" class="complyflow-button-primary" onclick="document.getElementById('scan-cookies').click()">
                                    <span class="dashicons dashicons-search" style="margin-right: 6px;"></span>
                                    <?php esc_html_e('Start Scan', 'complyflow'); ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($cookies as $cookie): ?>
                        <tr data-cookie-id="<?php echo esc_attr($cookie->id); ?>">
                            <td>
                                <input type="checkbox" class="cookie-checkbox" value="<?php echo esc_attr($cookie->id); ?>">
                            </td>
                            <td>
                                <strong><?php echo esc_html($cookie->name); ?></strong>
                                <?php if (!empty($cookie->is_manual)): ?>
                                    <span class="complyflow-badge" style="background-color: #f59e0b; color: white; font-size: 11px; margin-left: 8px;" title="<?php esc_attr_e('Manually Documented (Not Scanned)', 'complyflow'); ?>">
                                        <?php esc_html_e('MANUAL', 'complyflow'); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo esc_html($cookie->provider ?: '-'); ?>
                            </td>
                            <td>
                                <select class="category-select" data-cookie-id="<?php echo esc_attr($cookie->id); ?>" style="padding: 4px 8px; border: 1px solid var(--cf-border); border-radius: 4px;">
                                    <option value="necessary" <?php selected($cookie->category, 'necessary'); ?>><?php esc_html_e('Necessary', 'complyflow'); ?></option>
                                    <option value="functional" <?php selected($cookie->category, 'functional'); ?>><?php esc_html_e('Functional', 'complyflow'); ?></option>
                                    <option value="analytics" <?php selected($cookie->category, 'analytics'); ?>><?php esc_html_e('Analytics', 'complyflow'); ?></option>
                                    <option value="marketing" <?php selected($cookie->category, 'marketing'); ?>><?php esc_html_e('Marketing', 'complyflow'); ?></option>
                                </select>
                            </td>
                            <td>
                                <?php
                                $type_colors = [
                                    'http' => '#3b82f6',
                                    'session' => '#8b5cf6',
                                    'persistent' => '#10b981',
                                    'tracking' => '#ec4899',
                                ];
                                $color = $type_colors[$cookie->type] ?? '#6b7280';
                                ?>
                                <span class="complyflow-badge" style="background-color: <?php echo esc_attr($color); ?>; color: white;">
                                    <?php echo esc_html(ucfirst($cookie->type)); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo esc_html(wp_trim_words($cookie->purpose, 10, '...')); ?>
                            </td>
                            <td>
                                <?php echo esc_html($cookie->expiry); ?>
                            </td>
                            <td>
                                <div class="cf-flex" style="gap: 4px;">
                                    <button type="button" class="complyflow-button-secondary complyflow-button-sm edit-cookie-btn" data-cookie-id="<?php echo esc_attr($cookie->id); ?>" title="<?php esc_attr_e('Edit Cookie', 'complyflow'); ?>">
                                        <span class="dashicons dashicons-edit" style="font-size: 14px;"></span>
                                    </button>
                                    <button type="button" class="complyflow-button-secondary complyflow-button-sm delete-cookie-btn" data-cookie-id="<?php echo esc_attr($cookie->id); ?>" title="<?php esc_attr_e('Delete Cookie', 'complyflow'); ?>">
                                        <span class="dashicons dashicons-trash" style="font-size: 14px;"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scan Progress Modal -->
<div id="scan-progress-modal" class="complyflow-modal" style="display: none;">
    <div class="complyflow-modal-overlay"></div>
    <div class="complyflow-modal-content">
        <div class="complyflow-modal-header">
            <h2><?php esc_html_e('Scanning for Cookies...', 'complyflow'); ?></h2>
        </div>
        <div class="complyflow-modal-body" style="text-align: center; padding: 40px;">
            <div class="complyflow-loading-spinner" style="margin: 0 auto 20px;"></div>
            <p id="scan-status" style="color: var(--cf-muted);"><?php esc_html_e('Analyzing your website...', 'complyflow'); ?></p>
        </div>
    </div>
</div>

<!-- Edit Cookie Modal -->
<div id="edit-cookie-modal" class="complyflow-modal" style="display: none;">
    <div class="complyflow-modal-overlay"></div>
    <div class="complyflow-modal-content" style="max-width: 600px;">
        <div class="complyflow-modal-header">
            <h2><?php esc_html_e('Edit Cookie Details', 'complyflow'); ?></h2>
            <button class="complyflow-modal-close" onclick="jQuery('#edit-cookie-modal').fadeOut();">&times;</button>
        </div>
        <div class="complyflow-modal-body">
            <form id="edit-cookie-form">
                <input type="hidden" id="edit-cookie-id" name="cookie_id">
                
                <div class="complyflow-form-group">
                    <label for="edit-cookie-name" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Cookie Name', 'complyflow'); ?>
                    </label>
                    <input type="text" id="edit-cookie-name" readonly 
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; background: #f9fafb; cursor: not-allowed;">
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="edit-cookie-provider" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Provider', 'complyflow'); ?>
                    </label>
                    <input type="text" id="edit-cookie-provider" name="provider" required
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="edit-cookie-type" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Type', 'complyflow'); ?>
                    </label>
                    <select id="edit-cookie-type" name="type" required
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="http"><?php esc_html_e('HTTP Cookie', 'complyflow'); ?></option>
                        <option value="session"><?php esc_html_e('Session', 'complyflow'); ?></option>
                        <option value="persistent"><?php esc_html_e('Persistent', 'complyflow'); ?></option>
                        <option value="tracking"><?php esc_html_e('Tracking', 'complyflow'); ?></option>
                    </select>
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="edit-cookie-purpose" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Purpose', 'complyflow'); ?>
                    </label>
                    <textarea id="edit-cookie-purpose" name="purpose" rows="3" required
                              style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="edit-cookie-expiry" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Expiry', 'complyflow'); ?>
                    </label>
                    <input type="text" id="edit-cookie-expiry" name="expiry" placeholder="e.g., Session, 1 year, 30 days"
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                    <small style="color: var(--cf-muted); display: block; margin-top: 4px;">
                        <?php esc_html_e('Human-readable expiry time (e.g., "Session", "1 year", "30 days")', 'complyflow'); ?>
                    </small>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end;">
                    <button type="button" class="complyflow-btn complyflow-btn-secondary" onclick="jQuery('#edit-cookie-modal').fadeOut();">
                        <?php esc_html_e('Cancel', 'complyflow'); ?>
                    </button>
                    <button type="submit" class="complyflow-btn complyflow-btn-primary">
                        <?php esc_html_e('Save Changes', 'complyflow'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Manual Cookie Modal -->
<div id="add-manual-cookie-modal" class="complyflow-modal" style="display: none;">
    <div class="complyflow-modal-overlay"></div>
    <div class="complyflow-modal-content" style="max-width: 600px;">
        <div class="complyflow-modal-header">
            <h2><?php esc_html_e('Add External Cookie', 'complyflow'); ?></h2>
            <button class="complyflow-modal-close" onclick="jQuery('#add-manual-cookie-modal').fadeOut();">&times;</button>
        </div>
        <div class="complyflow-modal-body">
            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0; color: #78350f; font-size: 14px;">
                    <strong><?php esc_html_e('Note:', 'complyflow'); ?></strong>
                    <?php esc_html_e('Use this for cookies set by external services (iframes, third-party scripts) that the scanner cannot detect automatically.', 'complyflow'); ?>
                </p>
            </div>

            <form id="add-manual-cookie-form">
                <div class="complyflow-form-group">
                    <label for="manual-cookie-name" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Cookie Name', 'complyflow'); ?> <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="manual-cookie-name" name="name" required
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="manual-cookie-provider" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Provider', 'complyflow'); ?> <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="manual-cookie-provider" name="provider" required
                           placeholder="e.g., Google, Facebook, Custom Service"
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="manual-cookie-category" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Category', 'complyflow'); ?> <span style="color: #dc2626;">*</span>
                    </label>
                    <select id="manual-cookie-category" name="category" required
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="necessary"><?php esc_html_e('Necessary', 'complyflow'); ?></option>
                        <option value="functional"><?php esc_html_e('Functional', 'complyflow'); ?></option>
                        <option value="analytics"><?php esc_html_e('Analytics', 'complyflow'); ?></option>
                        <option value="marketing"><?php esc_html_e('Marketing', 'complyflow'); ?></option>
                    </select>
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="manual-cookie-type" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Type', 'complyflow'); ?> <span style="color: #dc2626;">*</span>
                    </label>
                    <select id="manual-cookie-type" name="type" required
                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="http"><?php esc_html_e('HTTP Cookie', 'complyflow'); ?></option>
                        <option value="session"><?php esc_html_e('Session', 'complyflow'); ?></option>
                        <option value="persistent"><?php esc_html_e('Persistent', 'complyflow'); ?></option>
                        <option value="tracking"><?php esc_html_e('Tracking', 'complyflow'); ?></option>
                    </select>
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="manual-cookie-purpose" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Purpose', 'complyflow'); ?> <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea id="manual-cookie-purpose" name="purpose" rows="3" required
                              placeholder="Describe what this cookie is used for..."
                              style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>

                <div class="complyflow-form-group" style="margin-top: 16px;">
                    <label for="manual-cookie-expiry" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Expiry', 'complyflow'); ?>
                    </label>
                    <input type="text" id="manual-cookie-expiry" name="expiry" placeholder="e.g., Session, 1 year, 30 days"
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>

                <div style="display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end;">
                    <button type="button" class="complyflow-btn complyflow-btn-secondary" onclick="jQuery('#add-manual-cookie-modal').fadeOut();">
                        <?php esc_html_e('Cancel', 'complyflow'); ?>
                    </button>
                    <button type="submit" class="complyflow-btn complyflow-btn-primary">
                        <?php esc_html_e('Add Cookie', 'complyflow'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import CSV Modal -->
<div id="import-csv-modal" class="complyflow-modal" style="display: none;">
    <div class="complyflow-modal-overlay"></div>
    <div class="complyflow-modal-content" style="max-width: 600px;">
        <div class="complyflow-modal-header">
            <h2><?php esc_html_e('Import Cookies from CSV', 'complyflow'); ?></h2>
            <button class="complyflow-modal-close" onclick="jQuery('#import-csv-modal').fadeOut();">&times;</button>
        </div>
        <div class="complyflow-modal-body">
            <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 4px solid #2563eb; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0 0 10px 0; color: #1e3a8a; font-size: 14px; font-weight: 600;">
                    <?php esc_html_e('CSV Format:', 'complyflow'); ?>
                </p>
                <code style="display: block; background: white; padding: 10px; border-radius: 4px; font-size: 12px; color: #1f2937;">
                    Cookie Name,Provider,Category,Type,Purpose,Expiry
                </code>
                <p style="margin: 10px 0 0 0; color: #1e40af; font-size: 13px;">
                    <?php esc_html_e('Categories: necessary, functional, analytics, marketing', 'complyflow'); ?><br>
                    <?php esc_html_e('Types: http, session, persistent, tracking', 'complyflow'); ?>
                </p>
            </div>

            <form id="import-csv-form" enctype="multipart/form-data">
                <div class="complyflow-form-group">
                    <label for="csv-file-input" style="font-weight: 600; display: block; margin-bottom: 8px;">
                        <?php esc_html_e('Select CSV File', 'complyflow'); ?> <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="file" id="csv-file-input" name="csv_file" accept=".csv" required
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; background: white;">
                </div>

                <div id="import-results" style="margin-top: 16px; display: none;"></div>

                <div style="display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end;">
                    <button type="button" class="complyflow-btn complyflow-btn-secondary" onclick="jQuery('#import-csv-modal').fadeOut();">
                        <?php esc_html_e('Cancel', 'complyflow'); ?>
                    </button>
                    <button type="submit" class="complyflow-btn complyflow-btn-primary">
                        <span class="dashicons dashicons-upload" style="margin-right: 6px;"></span>
                        <?php esc_html_e('Import', 'complyflow'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const nonce = '<?php echo wp_create_nonce('complyflow_cookie_nonce'); ?>';
    const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

    // Select all cookies
    $('#select-all-cookies').on('change', function() {
        $('.cookie-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Scan for cookies
    $('#scan-cookies').on('click', function() {
        $('#scan-progress-modal').fadeIn();
        $('#scan-status').text('<?php echo esc_js(__('Scanning your website...', 'complyflow')); ?>');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_scan_cookies',
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#scan-status').text('<?php echo esc_js(__('Scan complete! Reloading...', 'complyflow')); ?>');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert(response.data || '<?php echo esc_js(__('Scan failed', 'complyflow')); ?>');
                    $('#scan-progress-modal').fadeOut();
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred', 'complyflow')); ?>');
                $('#scan-progress-modal').fadeOut();
            }
        });
    });

    // Update category
    $('.category-select').on('change', function() {
        const cookieId = $(this).data('cookie-id');
        const category = $(this).val();
        const $select = $(this);

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_update_cookie_category',
                nonce: nonce,
                cookie_id: cookieId,
                category: category
            },
            success: function(response) {
                if (!response.success) {
                    alert(response.data || '<?php echo esc_js(__('Update failed', 'complyflow')); ?>');
                    location.reload();
                }
            }
        });
    });

    // Bulk update
    $('#do-bulk-action').on('click', function() {
        const action = $('#bulk-action-selector-top').val();
        const cookies = $('.cookie-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (!action) {
            alert('<?php echo esc_js(__('Please select an action', 'complyflow')); ?>');
            return;
        }

        if (cookies.length === 0) {
            alert('<?php echo esc_js(__('Please select cookies', 'complyflow')); ?>');
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_bulk_update_cookies',
                nonce: nonce,
                cookies: cookies,
                category: action
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data || '<?php echo esc_js(__('Update failed', 'complyflow')); ?>');
                }
            }
        });
    });

    // Delete cookie
    $('.delete-cookie-btn').on('click', function() {
        if (!confirm('<?php echo esc_js(__('Delete this cookie?', 'complyflow')); ?>')) {
            return;
        }

        const cookieId = $(this).data('cookie-id');
        const $row = $(this).closest('tr');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_delete_cookie',
                nonce: nonce,
                cookie_id: cookieId
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(function() {
                        $(this).remove();
                        // Reload if no cookies left
                        if ($('#cookie-table-body tr').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    alert(response.data || '<?php echo esc_js(__('Delete failed', 'complyflow')); ?>');
                }
            }
        });
    });

    // Export to CSV
    $('#export-cookies-csv').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update-alt" style="animation: spin 1s linear infinite; margin-right: 6px;"></span><?php echo esc_js(__('Exporting...', 'complyflow')); ?>');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_export_cookies_csv',
                nonce: nonce
            },
            success: function(response) {
                if (response.success && response.data.url) {
                    window.location.href = response.data.url;
                } else {
                    alert(response.data || '<?php echo esc_js(__('Export failed', 'complyflow')); ?>');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred', 'complyflow')); ?>');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Edit Cookie - Open Modal
    $(document).on('click', '.edit-cookie-btn', function() {
        const cookieId = $(this).data('cookie-id');
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_get_cookie',
                nonce: nonce,
                cookie_id: cookieId
            },
            success: function(response) {
                if (response.success && response.data) {
                    const cookie = response.data;
                    $('#edit-cookie-id').val(cookie.id);
                    $('#edit-cookie-name').val(cookie.name);
                    $('#edit-cookie-provider').val(cookie.provider || '');
                    $('#edit-cookie-type').val(cookie.type || 'http');
                    $('#edit-cookie-purpose').val(cookie.purpose || '');
                    $('#edit-cookie-expiry').val(cookie.expiry || '');
                    $('#edit-cookie-modal').fadeIn();
                } else {
                    alert(response.data || '<?php echo esc_js(__('Failed to load cookie', 'complyflow')); ?>');
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred', 'complyflow')); ?>');
            }
        });
    });

    // Edit Cookie - Submit Form
    $('#edit-cookie-form').on('submit', function(e) {
        e.preventDefault();
        
        const cookieId = $('#edit-cookie-id').val();
        const formData = {
            action: 'complyflow_edit_cookie',
            nonce: nonce,
            cookie_id: cookieId,
            provider: $('#edit-cookie-provider').val(),
            type: $('#edit-cookie-type').val(),
            purpose: $('#edit-cookie-purpose').val(),
            expiry: $('#edit-cookie-expiry').val()
        };

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#edit-cookie-modal').fadeOut();
                    location.reload();
                } else {
                    alert(response.data || '<?php echo esc_js(__('Update failed', 'complyflow')); ?>');
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred', 'complyflow')); ?>');
            }
        });
    });

    // Add Manual Cookie - Open Modal
    $('#add-external-cookie').on('click', function() {
        $('#add-manual-cookie-form')[0].reset();
        $('#add-manual-cookie-modal').fadeIn();
    });

    // Add Manual Cookie - Submit Form
    $('#add-manual-cookie-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'complyflow_add_manual_cookie',
            nonce: nonce,
            name: $('#manual-cookie-name').val().trim(),
            provider: $('#manual-cookie-provider').val().trim(),
            category: $('#manual-cookie-category').val(),
            type: $('#manual-cookie-type').val(),
            purpose: $('#manual-cookie-purpose').val().trim(),
            expiry: $('#manual-cookie-expiry').val().trim()
        };

        if (!formData.name || !formData.provider || !formData.purpose) {
            alert('<?php echo esc_js(__('Please fill in all required fields', 'complyflow')); ?>');
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#add-manual-cookie-modal').fadeOut();
                    location.reload();
                } else {
                    alert(response.data || '<?php echo esc_js(__('Failed to add cookie', 'complyflow')); ?>');
                }
            },
            error: function() {
                alert('<?php echo esc_js(__('An error occurred', 'complyflow')); ?>');
            }
        });
    });

    // Import CSV - Open Modal
    $('#import-cookies-csv').on('click', function() {
        $('#import-csv-form')[0].reset();
        $('#import-results').hide();
        $('#import-csv-modal').fadeIn();
    });

    // Import CSV - Submit Form
    $('#import-csv-form').on('submit', function(e) {
        e.preventDefault();
        
        const fileInput = $('#csv-file-input')[0];
        if (!fileInput.files.length) {
            alert('<?php echo esc_js(__('Please select a CSV file', 'complyflow')); ?>');
            return;
        }

        const file = fileInput.files[0];
        if (!file.name.endsWith('.csv')) {
            alert('<?php echo esc_js(__('Please select a valid CSV file', 'complyflow')); ?>');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'complyflow_import_cookies_csv');
        formData.append('nonce', nonce);
        formData.append('csv_file', file);

        const $submitBtn = $('#import-csv-form button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span class="dashicons dashicons-update-alt" style="animation: spin 1s linear infinite; margin-right: 6px;"></span><?php echo esc_js(__('Importing...', 'complyflow')); ?>');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                if (response.success) {
                    const data = response.data;
                    let resultHtml = '<div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-left: 4px solid #10b981; padding: 16px; border-radius: 8px;">';
                    resultHtml += '<p style="margin: 0; color: #065f46; font-weight: 600;"><span class="dashicons dashicons-yes-alt" style="color: #10b981;"></span> ';
                    resultHtml += '<?php echo esc_js(__('Imported', 'complyflow')); ?>: ' + data.imported + ' <?php echo esc_js(__('cookies', 'complyflow')); ?></p>';
                    
                    if (data.errors && data.errors.length > 0) {
                        resultHtml += '<div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #6ee7b7;">';
                        resultHtml += '<p style="margin: 0 0 8px 0; color: #dc2626; font-weight: 600;"><?php echo esc_js(__('Errors:', 'complyflow')); ?></p>';
                        resultHtml += '<ul style="margin: 0; padding-left: 20px; color: #991b1b; font-size: 13px;">';
                        data.errors.forEach(function(error) {
                            resultHtml += '<li>' + error + '</li>';
                        });
                        resultHtml += '</ul></div>';
                    }
                    
                    resultHtml += '</div>';
                    resultHtml += '<p style="text-align: center; margin-top: 16px;"><button class="complyflow-btn complyflow-btn-primary" onclick="location.reload();"><?php echo esc_js(__('Reload Page', 'complyflow')); ?></button></p>';
                    
                    $('#import-results').html(resultHtml).show();
                } else {
                    alert(response.data || '<?php echo esc_js(__('Import failed', 'complyflow')); ?>');
                }
            },
            error: function() {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                alert('<?php echo esc_js(__('An error occurred', 'complyflow')); ?>');
            }
        });
    });

    // Close modal on overlay click
    $(document).on('click', '.complyflow-modal-overlay', function() {
        $(this).closest('.complyflow-modal').fadeOut();
    });
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
