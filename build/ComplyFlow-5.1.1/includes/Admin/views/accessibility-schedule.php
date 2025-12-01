<?php
/**
 * Accessibility Scheduled Scans Settings View
 *
 * @package ComplyFlow
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$enabled = $this->settings->get('accessibility_scheduled_scans_enabled', false);
$frequency = $this->settings->get('accessibility_scheduled_scans_frequency', 'daily');
$urls = $this->settings->get('accessibility_scheduled_scans_urls', [home_url()]);
$notifications_enabled = $this->settings->get('accessibility_notifications_enabled', false);
$notifications_threshold = $this->settings->get('accessibility_notifications_threshold', 'critical');
$notifications_recipients = $this->settings->get('accessibility_notifications_recipients', [get_option('admin_email')]);

// Get schedule status
$next_scan = $scheduled_manager->get_next_scheduled_time();
$last_results = $scheduled_manager->get_last_results();
$last_scan_time = $scheduled_manager->get_last_scan_time();
?>

<div class="wrap">
    <h1><?php esc_html_e('Scheduled Accessibility Scans', 'complyflow'); ?></h1>
    <p class="description">
        <?php esc_html_e('Automatically scan your website for accessibility issues on a regular schedule.', 'complyflow'); ?>
    </p>

    <?php if ($next_scan): ?>
        <div class="notice notice-info" style="margin-top: 20px;">
            <p>
                <strong><?php esc_html_e('Next Scheduled Scan:', 'complyflow'); ?></strong>
                <?php echo esc_html(wp_date(get_option('date_format') . ' ' . get_option('time_format'), $next_scan)); ?>
                <span style="color: #666;">(<?php echo esc_html(human_time_diff($next_scan, time())); ?>)</span>
            </p>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">
        <!-- Settings Form -->
        <div class="postbox" style="padding: 20px;">
            <h2 style="margin-top: 0;"><?php esc_html_e('Schedule Settings', 'complyflow'); ?></h2>

            <form id="cf-schedule-settings-form">
                <?php wp_nonce_field('complyflow_admin_nonce', 'nonce'); ?>

                <!-- Enable/Disable -->
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cf-schedule-enabled"><?php esc_html_e('Enable Scheduled Scans', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="cf-schedule-enabled" name="enabled" value="1" <?php checked($enabled, true); ?>>
                                <?php esc_html_e('Automatically run accessibility scans', 'complyflow'); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e('When enabled, scans will run automatically based on the frequency you set below.', 'complyflow'); ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Frequency -->
                    <tr>
                        <th scope="row">
                            <label for="cf-schedule-frequency"><?php esc_html_e('Scan Frequency', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <select id="cf-schedule-frequency" name="frequency" class="regular-text">
                                <option value="hourly" <?php selected($frequency, 'hourly'); ?>><?php esc_html_e('Every Hour', 'complyflow'); ?></option>
                                <option value="twicedaily" <?php selected($frequency, 'twicedaily'); ?>><?php esc_html_e('Twice Daily', 'complyflow'); ?></option>
                                <option value="daily" <?php selected($frequency, 'daily'); ?>><?php esc_html_e('Once Daily', 'complyflow'); ?></option>
                                <option value="weekly" <?php selected($frequency, 'weekly'); ?>><?php esc_html_e('Once Weekly', 'complyflow'); ?></option>
                                <option value="monthly" <?php selected($frequency, 'monthly'); ?>><?php esc_html_e('Once Monthly', 'complyflow'); ?></option>
                            </select>
                            <p class="description">
                                <?php esc_html_e('How often should scans run?', 'complyflow'); ?>
                            </p>
                        </td>
                    </tr>

                    <!-- URLs to Scan -->
                    <tr>
                        <th scope="row">
                            <label for="cf-schedule-urls"><?php esc_html_e('URLs to Scan', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <div id="cf-url-list">
                                <?php foreach ($urls as $index => $url): ?>
                                    <div class="cf-url-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                        <input type="url" name="urls[]" value="<?php echo esc_url($url); ?>" class="regular-text" placeholder="https://example.com" required>
                                        <button type="button" class="button cf-remove-url" <?php echo count($urls) === 1 ? 'disabled' : ''; ?>>
                                            <?php esc_html_e('Remove', 'complyflow'); ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="cf-add-url" class="button" style="margin-top: 10px;">
                                <?php esc_html_e('+ Add URL', 'complyflow'); ?>
                            </button>
                            <p class="description">
                                <?php esc_html_e('Enter the URLs you want to scan regularly. You can add multiple URLs.', 'complyflow'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <hr>

                <h3><?php esc_html_e('Email Notifications', 'complyflow'); ?></h3>

                <table class="form-table">
                    <!-- Enable Notifications -->
                    <tr>
                        <th scope="row">
                            <label for="cf-notifications-enabled"><?php esc_html_e('Enable Notifications', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="cf-notifications-enabled" name="notifications_enabled" value="1" <?php checked($notifications_enabled, true); ?>>
                                <?php esc_html_e('Send email notifications when issues are found', 'complyflow'); ?>
                            </label>
                        </td>
                    </tr>

                    <!-- Severity Threshold -->
                    <tr>
                        <th scope="row">
                            <label for="cf-notifications-threshold"><?php esc_html_e('Notify When', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <select id="cf-notifications-threshold" name="notifications_threshold" class="regular-text">
                                <option value="critical" <?php selected($notifications_threshold, 'critical'); ?>><?php esc_html_e('Critical Issues Only', 'complyflow'); ?></option>
                                <option value="serious" <?php selected($notifications_threshold, 'serious'); ?>><?php esc_html_e('Critical or Serious Issues', 'complyflow'); ?></option>
                                <option value="moderate" <?php selected($notifications_threshold, 'moderate'); ?>><?php esc_html_e('Critical, Serious, or Moderate Issues', 'complyflow'); ?></option>
                                <option value="any" <?php selected($notifications_threshold, 'any'); ?>><?php esc_html_e('Any Issues Found', 'complyflow'); ?></option>
                            </select>
                            <p class="description">
                                <?php esc_html_e('Choose the minimum severity level that triggers email notifications.', 'complyflow'); ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Email Recipients -->
                    <tr>
                        <th scope="row">
                            <label for="cf-notifications-recipients"><?php esc_html_e('Recipients', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <div id="cf-recipient-list">
                                <?php foreach ($notifications_recipients as $index => $email): ?>
                                    <div class="cf-recipient-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                        <input type="email" name="notifications_recipients[]" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="email@example.com" required>
                                        <button type="button" class="button cf-remove-recipient" <?php echo count($notifications_recipients) === 1 ? 'disabled' : ''; ?>>
                                            <?php esc_html_e('Remove', 'complyflow'); ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="cf-add-recipient" class="button" style="margin-top: 10px;">
                                <?php esc_html_e('+ Add Recipient', 'complyflow'); ?>
                            </button>
                            <p class="description">
                                <?php esc_html_e('Email addresses that will receive scan notifications.', 'complyflow'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary" id="cf-save-schedule">
                        <?php esc_html_e('Save Settings', 'complyflow'); ?>
                    </button>
                    <span class="spinner" style="float: none; margin: 0 10px;"></span>
                </p>
            </form>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status Box -->
            <div class="postbox" style="padding: 20px;">
                <h3 style="margin-top: 0;"><?php esc_html_e('Schedule Status', 'complyflow'); ?></h3>

                <?php if ($enabled && $next_scan): ?>
                    <p>
                        <span class="dashicons dashicons-clock" style="color: #00a32a;"></span>
                        <strong style="color: #00a32a;"><?php esc_html_e('Active', 'complyflow'); ?></strong>
                    </p>
                    <p>
                        <strong><?php esc_html_e('Next Scan:', 'complyflow'); ?></strong><br>
                        <?php echo esc_html(wp_date(get_option('date_format') . ' ' . get_option('time_format'), $next_scan)); ?>
                    </p>
                <?php else: ?>
                    <p>
                        <span class="dashicons dashicons-dismiss" style="color: #d63638;"></span>
                        <strong style="color: #d63638;"><?php esc_html_e('Inactive', 'complyflow'); ?></strong>
                    </p>
                    <p class="description">
                        <?php esc_html_e('Scheduled scans are currently disabled.', 'complyflow'); ?>
                    </p>
                <?php endif; ?>

                <?php if ($last_scan_time): ?>
                    <hr style="margin: 15px 0;">
                    <p>
                        <strong><?php esc_html_e('Last Scan:', 'complyflow'); ?></strong><br>
                        <?php echo esc_html(human_time_diff($last_scan_time, time())); ?> <?php esc_html_e('ago', 'complyflow'); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Last Scan Results -->
            <?php if ($last_results): ?>
                <div class="postbox" style="padding: 20px; margin-top: 20px;">
                    <h3 style="margin-top: 0;"><?php esc_html_e('Last Scan Results', 'complyflow'); ?></h3>

                    <?php foreach ($last_results as $result): ?>
                        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                            <p style="margin: 0 0 5px 0;">
                                <strong><?php echo esc_html($result['url']); ?></strong>
                            </p>
                            <?php if ($result['success']): ?>
                                <p style="margin: 0; color: #666; font-size: 13px;">
                                    <?php
                                    printf(
                                        __('Score: %d | Issues: %s', 'complyflow'),
                                        round($result['score']),
                                        implode(', ', array_map(function($severity, $count) {
                                            return sprintf('%s: %d', ucfirst($severity), $count);
                                        }, array_keys($result['issues']), $result['issues']))
                                    );
                                    ?>
                                </p>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-accessibility-results&scan_id=' . $result['scan_id'])); ?>" class="button button-small" style="margin-top: 5px;">
                                    <?php esc_html_e('View Details', 'complyflow'); ?>
                                </a>
                            <?php else: ?>
                                <p style="margin: 0; color: #d63638; font-size: 13px;">
                                    <?php echo esc_html($result['error']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Help Box -->
            <div class="postbox" style="padding: 20px; margin-top: 20px;">
                <h3 style="margin-top: 0;"><?php esc_html_e('WP-CLI Commands', 'complyflow'); ?></h3>
                <p class="description">
                    <?php esc_html_e('Manage scheduled scans via command line:', 'complyflow'); ?>
                </p>
                <code style="display: block; padding: 10px; background: #f5f5f5; margin: 10px 0; font-size: 12px;">
                    wp complyflow scan schedule
                </code>
                <code style="display: block; padding: 10px; background: #f5f5f5; margin: 10px 0; font-size: 12px;">
                    wp complyflow scan unschedule
                </code>
                <code style="display: block; padding: 10px; background: #f5f5f5; margin: 10px 0; font-size: 12px;">
                    wp complyflow scan run-scheduled
                </code>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add URL
    $('#cf-add-url').on('click', function() {
        const urlRow = `
            <div class="cf-url-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="url" name="urls[]" value="" class="regular-text" placeholder="https://example.com" required>
                <button type="button" class="button cf-remove-url"><?php esc_html_e('Remove', 'complyflow'); ?></button>
            </div>
        `;
        $('#cf-url-list').append(urlRow);
        updateRemoveButtons();
    });

    // Remove URL
    $(document).on('click', '.cf-remove-url', function() {
        $(this).closest('.cf-url-row').remove();
        updateRemoveButtons();
    });

    // Add Recipient
    $('#cf-add-recipient').on('click', function() {
        const recipientRow = `
            <div class="cf-recipient-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="email" name="notifications_recipients[]" value="" class="regular-text" placeholder="email@example.com" required>
                <button type="button" class="button cf-remove-recipient"><?php esc_html_e('Remove', 'complyflow'); ?></button>
            </div>
        `;
        $('#cf-recipient-list').append(recipientRow);
        updateRemoveButtons();
    });

    // Remove Recipient
    $(document).on('click', '.cf-remove-recipient', function() {
        $(this).closest('.cf-recipient-row').remove();
        updateRemoveButtons();
    });

    // Update remove button states
    function updateRemoveButtons() {
        $('.cf-remove-url').prop('disabled', $('.cf-url-row').length === 1);
        $('.cf-remove-recipient').prop('disabled', $('.cf-recipient-row').length === 1);
    }

    // Save settings
    $('#cf-schedule-settings-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $('#cf-save-schedule');
        const $spinner = $form.find('.spinner');

        $button.prop('disabled', true);
        $spinner.addClass('is-active');

        $.ajax({
            url: complyflowAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_update_scheduled_scans',
                nonce: $form.find('input[name="nonce"]').val(),
                enabled: $('#cf-schedule-enabled').is(':checked') ? '1' : '0',
                frequency: $('#cf-schedule-frequency').val(),
                urls: $('input[name="urls[]"]').map(function() { return $(this).val(); }).get(),
                notifications_enabled: $('#cf-notifications-enabled').is(':checked') ? '1' : '0',
                notifications_threshold: $('#cf-notifications-threshold').val(),
                notifications_recipients: $('input[name="notifications_recipients[]"]').map(function() { return $(this).val(); }).get()
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || '<?php esc_html_e('An error occurred.', 'complyflow'); ?>');
                }
            },
            error: function() {
                alert('<?php esc_html_e('An error occurred while saving settings.', 'complyflow'); ?>');
            },
            complete: function() {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });
});
</script>
