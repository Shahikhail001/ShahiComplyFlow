<?php
/**
 * Accessibility Scan Results Detail Page
 *
 * @package ComplyFlow
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$scan_id = isset($_GET['scan_id']) ? absint($_GET['scan_id']) : 0;

if (!$scan_id) {
    wp_die(esc_html__('Invalid scan ID.', 'complyflow'));
}

$scanner = new \ComplyFlow\Modules\Accessibility\Scanner();
$scan_data = $scanner->export_scan($scan_id);

if (!$scan_data) {
    wp_die(esc_html__('Scan not found.', 'complyflow'));
}

$issues = $scan_data['issues'];
$summary = $scan_data['summary'];
$score = $scan_data['score'];

// Group issues by category
$issues_by_category = [];
foreach ($issues as $issue) {
    $category = $issue['category'] ?? 'other';
    if (!isset($issues_by_category[$category])) {
        $issues_by_category[$category] = [];
    }
    $issues_by_category[$category][] = $issue;
}

// Category labels
$category_labels = [
    'images' => __('Images', 'complyflow'),
    'structure' => __('Structure', 'complyflow'),
    'forms' => __('Forms', 'complyflow'),
    'links' => __('Links', 'complyflow'),
    'aria' => __('ARIA', 'complyflow'),
    'keyboard' => __('Keyboard', 'complyflow'),
    'multimedia' => __('Multimedia', 'complyflow'),
    'tables' => __('Tables', 'complyflow'),
    'other' => __('Other', 'complyflow'),
];

// Severity labels and colors
$severity_config = [
    'critical' => ['label' => __('Critical', 'complyflow'), 'color' => '#d63638'],
    'serious' => ['label' => __('Serious', 'complyflow'), 'color' => '#f0b849'],
    'moderate' => ['label' => __('Moderate', 'complyflow'), 'color' => '#f0c33c'],
    'minor' => ['label' => __('Minor', 'complyflow'), 'color' => '#72aee6'],
];
?>

<div class="wrap complyflow-scan-results">
    <h1 class="wp-heading-inline"><?php esc_html_e('Scan Results', 'complyflow'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-accessibility')); ?>" class="page-title-action">
        <?php esc_html_e('← Back to Scans', 'complyflow'); ?>
    </a>
    <a href="<?php echo esc_url(add_query_arg([
        'action' => 'complyflow_export_scan_csv',
        'scan_id' => $scan_id,
        'nonce' => wp_create_nonce('complyflow_admin_nonce')
    ], admin_url('admin-ajax.php'))); ?>" class="page-title-action">
        <?php esc_html_e('Export CSV', 'complyflow'); ?>
    </a>
    <a href="#" class="page-title-action cf-export-pdf">
        <?php esc_html_e('Export PDF', 'complyflow'); ?>
    </a>
    <hr class="wp-header-end">

    <!-- Scan Header -->
    <div class="cf-scan-header" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; margin: 20px 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <h2 style="margin: 0 0 10px 0;">
                    <?php echo esc_html($scan_data['url']); ?>
                </h2>
                <p style="margin: 0; color: #666;">
                    <?php
                    printf(
                        esc_html__('Scanned on %s', 'complyflow'),
                        esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $scan_data['scanned_at']))
                    );
                    ?>
                </p>
            </div>
            <div style="text-align: center;">
                <div class="cf-score-badge" style="
                    display: inline-block;
                    padding: 20px 30px;
                    border-radius: 50%;
                    font-size: 36px;
                    font-weight: 700;
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
                </div>
                <p style="margin: 10px 0 0 0; font-weight: 600;">
                    <?php esc_html_e('Accessibility Score', 'complyflow'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('Total Issues', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600;">
                <?php echo esc_html($summary['total_issues']); ?>
            </p>
        </div>
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('Critical Issues', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600; color: #d63638;">
                <?php echo esc_html($summary['by_severity']['critical']); ?>
            </p>
        </div>
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('Serious Issues', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600; color: #f0b849;">
                <?php echo esc_html($summary['by_severity']['serious']); ?>
            </p>
        </div>
        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                <?php esc_html_e('WCAG Criteria', 'complyflow'); ?>
            </h3>
            <p style="margin: 0; font-size: 32px; font-weight: 600;">
                <?php echo esc_html(count($summary['by_wcag'])); ?>
            </p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="cf-filter-tabs" style="background: #fff; border: 1px solid #ddd; border-bottom: none; border-radius: 4px 4px 0 0; margin: 20px 0 0 0;">
        <div style="display: flex; border-bottom: 1px solid #ddd;">
            <button class="cf-tab-btn active" data-filter="all" style="padding: 15px 25px; border: none; background: none; cursor: pointer; border-bottom: 2px solid #2271b1; font-weight: 600;">
                <?php esc_html_e('All Issues', 'complyflow'); ?> (<?php echo esc_html($summary['total_issues']); ?>)
            </button>
            <button class="cf-tab-btn" data-filter="critical" style="padding: 15px 25px; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent;">
                <?php esc_html_e('Critical', 'complyflow'); ?> (<?php echo esc_html($summary['by_severity']['critical']); ?>)
            </button>
            <button class="cf-tab-btn" data-filter="serious" style="padding: 15px 25px; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent;">
                <?php esc_html_e('Serious', 'complyflow'); ?> (<?php echo esc_html($summary['by_severity']['serious']); ?>)
            </button>
            <button class="cf-tab-btn" data-filter="moderate" style="padding: 15px 25px; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent;">
                <?php esc_html_e('Moderate', 'complyflow'); ?> (<?php echo esc_html($summary['by_severity']['moderate']); ?>)
            </button>
            <button class="cf-tab-btn" data-filter="minor" style="padding: 15px 25px; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent;">
                <?php esc_html_e('Minor', 'complyflow'); ?> (<?php echo esc_html($summary['by_severity']['minor']); ?>)
            </button>
        </div>
    </div>

    <!-- Issues List -->
    <div class="cf-issues-list" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 4px 4px;">
        <?php if (empty($issues)) : ?>
            <div class="notice notice-success" style="margin: 20px 0;">
                <p><strong><?php esc_html_e('Congratulations!', 'complyflow'); ?></strong></p>
                <p><?php esc_html_e('No accessibility issues were found on this page.', 'complyflow'); ?></p>
            </div>
        <?php else : ?>
            <?php foreach ($issues_by_category as $category => $category_issues) : ?>
                <div class="cf-category-group" style="margin-bottom: 30px;">
                    <h3 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ddd;">
                        <?php echo esc_html($category_labels[$category] ?? ucfirst($category)); ?>
                        <span style="color: #666; font-weight: normal; font-size: 14px;">
                            (<?php echo esc_html(count($category_issues)); ?> <?php esc_html_e('issues', 'complyflow'); ?>)
                        </span>
                    </h3>

                    <?php foreach ($category_issues as $index => $issue) : ?>
                        <?php
                        $severity = $issue['severity'] ?? 'moderate';
                        $severity_info = $severity_config[$severity] ?? $severity_config['moderate'];
                        ?>
                        <div class="cf-issue-card" data-severity="<?php echo esc_attr($severity); ?>" style="
                            border-left: 4px solid <?php echo esc_attr($severity_info['color']); ?>;
                            background: #f9f9f9;
                            padding: 20px;
                            margin-bottom: 15px;
                            border-radius: 0 4px 4px 0;
                        ">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                <div>
                                    <span style="
                                        display: inline-block;
                                        padding: 4px 10px;
                                        border-radius: 3px;
                                        font-size: 12px;
                                        font-weight: 600;
                                        color: #fff;
                                        background: <?php echo esc_attr($severity_info['color']); ?>;
                                    ">
                                        <?php echo esc_html($severity_info['label']); ?>
                                    </span>
                                    <span style="
                                        display: inline-block;
                                        padding: 4px 10px;
                                        border-radius: 3px;
                                        font-size: 12px;
                                        background: #e0e0e0;
                                        margin-left: 5px;
                                    ">
                                        WCAG <?php echo esc_html($issue['wcag']); ?>
                                    </span>
                                </div>
                            </div>

                            <h4 style="margin: 10px 0; font-size: 16px;">
                                <?php echo esc_html($issue['message']); ?>
                            </h4>

                            <?php if (!empty($issue['element'])) : ?>
                                <div style="margin: 15px 0;">
                                    <strong><?php esc_html_e('Element:', 'complyflow'); ?></strong>
                                    <pre style="
                                        background: #fff;
                                        padding: 10px;
                                        border: 1px solid #ddd;
                                        border-radius: 3px;
                                        overflow-x: auto;
                                        font-size: 12px;
                                        margin: 5px 0;
                                    "><?php echo esc_html($issue['element']); ?></pre>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($issue['selector'])) : ?>
                                <p style="margin: 10px 0;">
                                    <strong><?php esc_html_e('Selector:', 'complyflow'); ?></strong>
                                    <code style="background: #fff; padding: 2px 6px; border: 1px solid #ddd; border-radius: 3px;">
                                        <?php echo esc_html($issue['selector']); ?>
                                    </code>
                                </p>
                            <?php endif; ?>

                            <div style="background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 3px; margin: 15px 0;">
                                <h5 style="margin: 0 0 10px 0; color: #2271b1;">
                                    <?php esc_html_e('How to Fix:', 'complyflow'); ?>
                                </h5>
                                <p style="margin: 0;">
                                    <?php echo esc_html($issue['fix']); ?>
                                </p>
                            </div>

                            <?php if (!empty($issue['learn_more'])) : ?>
                                <p style="margin: 10px 0 0 0;">
                                    <a href="<?php echo esc_url($issue['learn_more']); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php esc_html_e('Learn more about this issue →', 'complyflow'); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.cf-tab-btn.active {
    border-bottom-color: #2271b1 !important;
    font-weight: 600;
}

.cf-tab-btn:hover {
    background: #f0f0f1;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab filtering
    $('.cf-tab-btn').on('click', function() {
        $('.cf-tab-btn').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        
        if (filter === 'all') {
            $('.cf-issue-card').show();
        } else {
            $('.cf-issue-card').hide();
            $('.cf-issue-card[data-severity="' + filter + '"]').show();
        }
    });

    // Export PDF
    $('.cf-export-pdf').on('click', function(e) {
        e.preventDefault();
        alert('<?php esc_html_e('PDF export functionality will be implemented in a future update.', 'complyflow'); ?>');
    });
});
});
</script>
