<?php
/**
 * Accessibility Scanner Admin Page
 *
 * @package ComplyFlow
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$scanner = new \ComplyFlow\Modules\Accessibility\Scanner();
$scans = $scanner->get_scans(['limit' => 20, 'offset' => 0]);
$statistics = $scanner->get_statistics();
?>

<div class="wrap complyflow-accessibility">
    <h1 class="wp-heading-inline"><?php esc_html_e('Accessibility Scanner', 'complyflow'); ?></h1>
    <a href="#" class="page-title-action" id="cf-new-scan-btn">
        <?php esc_html_e('New Scan', 'complyflow'); ?>
    </a>
    <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-accessibility-schedule')); ?>" class="page-title-action">
        <span class="dashicons dashicons-clock" style="margin-top: 3px;"></span>
        <?php esc_html_e('Scheduled Scans', 'complyflow'); ?>
    </a>
    <hr class="wp-header-end">

    <?php if (!empty($_GET['scan_deleted'])) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Scan deleted successfully.', 'complyflow'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Statistics Dashboard -->
    <div class="cf-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="cf-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('Total Scans', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600; color: #2271b1;">
                <?php echo esc_html($statistics['total_scans'] ?? 0); ?>
            </p>
        </div>

        <div class="cf-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('Total Issues', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600; color: #d63638;">
                <?php echo esc_html($statistics['total_issues'] ?? 0); ?>
            </p>
        </div>

        <div class="cf-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('Average Score', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600; color: #00a32a;">
                <?php echo esc_html($statistics['average_score'] ?? 0); ?>
            </p>
        </div>

        <div class="cf-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('Pages Scanned', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600; color: #2271b1;">
                <?php echo esc_html($statistics['pages_scanned'] ?? 0); ?>
            </p>
        </div>
    </div>

    <!-- Issues by Severity -->
    <?php if (!empty($statistics['by_severity'])) : ?>
        <div class="cf-severity-stats" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin: 20px 0;">
            <h2 style="margin: 0 0 15px 0; font-size: 16px;">
                <?php esc_html_e('Issues by Severity', 'complyflow'); ?>
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #d63638; border-radius: 50%; margin-right: 8px;"></span>
                    <strong><?php esc_html_e('Critical:', 'complyflow'); ?></strong>
                    <?php echo esc_html($statistics['by_severity']['critical'] ?? 0); ?>
                </div>
                <div>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #f0b849; border-radius: 50%; margin-right: 8px;"></span>
                    <strong><?php esc_html_e('Serious:', 'complyflow'); ?></strong>
                    <?php echo esc_html($statistics['by_severity']['serious'] ?? 0); ?>
                </div>
                <div>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #f0c33c; border-radius: 50%; margin-right: 8px;"></span>
                    <strong><?php esc_html_e('Moderate:', 'complyflow'); ?></strong>
                    <?php echo esc_html($statistics['by_severity']['moderate'] ?? 0); ?>
                </div>
                <div>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #72aee6; border-radius: 50%; margin-right: 8px;"></span>
                    <strong><?php esc_html_e('Minor:', 'complyflow'); ?></strong>
                    <?php echo esc_html($statistics['by_severity']['minor'] ?? 0); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Scans List -->
    <div class="cf-scans-list" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin: 20px 0;">
        <h2 style="margin: 0 0 15px 0; font-size: 16px;">
            <?php esc_html_e('Recent Scans', 'complyflow'); ?>
        </h2>

        <?php if (empty($scans)) : ?>
            <p><?php esc_html_e('No scans found. Click "New Scan" to get started.', 'complyflow'); ?></p>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Page URL', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Score', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Issues', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Date', 'complyflow'); ?></th>
                        <th><?php esc_html_e('Actions', 'complyflow'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scans as $scan) : ?>
                        <?php
                        // Extract score from JSON results
                        $results = json_decode($scan->results, true);
                        $score = $results['score'] ?? 0;
                        
                        $score_class = 'score-high';
                        if ($score < 50) {
                            $score_class = 'score-low';
                        } elseif ($score < 80) {
                            $score_class = 'score-medium';
                        }
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url(add_query_arg(['scan_id' => $scan->id], admin_url('admin.php?page=complyflow-accessibility-results'))); ?>">
                                        <?php echo esc_html($scan->url); ?>
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <span class="cf-score <?php echo esc_attr($score_class); ?>" style="
                                    display: inline-block;
                                    padding: 4px 12px;
                                    border-radius: 12px;
                                    font-weight: 600;
                                    <?php
                                    if ($score >= 80) {
                                        echo 'background: #d5f5e3; color: #00a32a;';
                                    } elseif ($score >= 50) {
                                        echo 'background: #fff9e6; color: #f0b849;';
                                    } else {
                                        echo 'background: #ffe5e5; color: #d63638;';
                                    }
                                    ?>
                                ">
                                    <?php echo esc_html(round($score)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($scan->total_issues); ?></td>
                            <td><?php echo esc_html(mysql2date(get_option('date_format'), $scan->created_at)); ?></td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg(['scan_id' => $scan->id], admin_url('admin.php?page=complyflow-accessibility-results'))); ?>" class="button button-small">
                                    <?php esc_html_e('View Details', 'complyflow'); ?>
                                </a>
                                <a href="#" class="button button-small cf-delete-scan" data-scan-id="<?php echo esc_attr($scan->id); ?>">
                                    <?php esc_html_e('Delete', 'complyflow'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- New Scan Modal -->
<div id="cf-new-scan-modal" style="display: none;">
    <div class="cf-modal-content">
        <h2><?php esc_html_e('Run Accessibility Scan', 'complyflow'); ?></h2>
        <form id="cf-new-scan-form">
            <p>
                <label for="cf-scan-url">
                    <strong><?php esc_html_e('Page URL:', 'complyflow'); ?></strong>
                </label>
                <input type="url" id="cf-scan-url" name="url" class="regular-text" placeholder="https://example.com/page" required />
            </p>
            <p class="description">
                <?php esc_html_e('Enter the full URL of the page you want to scan for accessibility issues.', 'complyflow'); ?>
            </p>
            <div id="cf-scan-progress" style="display: none; margin: 20px 0;">
                <p><?php esc_html_e('Scanning in progress...', 'complyflow'); ?></p>
                <progress style="width: 100%; height: 30px;"></progress>
            </div>
            <div id="cf-scan-result" style="display: none; margin: 20px 0;"></div>
            <p class="submit">
                <button type="submit" class="button button-primary" id="cf-scan-submit">
                    <?php esc_html_e('Start Scan', 'complyflow'); ?>
                </button>
                <button type="button" class="button" id="cf-scan-cancel">
                    <?php esc_html_e('Cancel', 'complyflow'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<style>
#cf-new-scan-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cf-modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 4px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}
</style>

<script>
jQuery(document).ready(function($) {
    // New scan modal
    $('#cf-new-scan-btn').on('click', function(e) {
        e.preventDefault();
        $('#cf-new-scan-modal').show();
    });

    $('#cf-scan-cancel').on('click', function() {
        $('#cf-new-scan-modal').hide();
        $('#cf-new-scan-form')[0].reset();
        $('#cf-scan-progress').hide();
        $('#cf-scan-result').hide();
    });

    // Submit scan
    $('#cf-new-scan-form').on('submit', function(e) {
        e.preventDefault();
        
        const url = $('#cf-scan-url').val();
        $('#cf-scan-submit').prop('disabled', true);
        $('#cf-scan-progress').show();
        $('#cf-scan-result').hide();

        $.ajax({
            url: complyflowAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_run_accessibility_scan',
                nonce: complyflowAdmin.nonce,
                url: url
            },
            success: function(response) {
                $('#cf-scan-progress').hide();
                
                if (response.success) {
                    $('#cf-scan-result')
                        .html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>')
                        .show();
                    
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    $('#cf-scan-result')
                        .html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>')
                        .show();
                    $('#cf-scan-submit').prop('disabled', false);
                }
            },
            error: function() {
                $('#cf-scan-progress').hide();
                $('#cf-scan-result')
                    .html('<div class="notice notice-error"><p><?php esc_html_e('An error occurred. Please try again.', 'complyflow'); ?></p></div>')
                    .show();
                $('#cf-scan-submit').prop('disabled', false);
            }
        });
    });

    // Delete scan
    $('.cf-delete-scan').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('<?php esc_html_e('Are you sure you want to delete this scan?', 'complyflow'); ?>')) {
            return;
        }

        const scanId = $(this).data('scan-id');
        const $row = $(this).closest('tr');

        $.ajax({
            url: complyflowAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'complyflow_delete_scan',
                nonce: complyflowAdmin.nonce,
                scan_id: scanId
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
</script>
