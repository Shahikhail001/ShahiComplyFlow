<?php
/**
 * Dashboard Admin View
 *
 * Overview dashboard with compliance metrics and widgets.
 *
 * @package ComplyFlow
 * @since   3.5.0
 *
 * @var array $compliance_score    Compliance score data
 * @var array $dsr_stats          DSR statistics
 * @var array $consent_stats      Consent statistics
 * @var array $accessibility_summary Accessibility summary
 * @var array $cookie_summary     Cookie summary
 * @var array $trends             Compliance trends over time
 * @var array $activities         Recent activity timeline
 * @var array $risk_assessment    Risk assessment data
 * @var array $data_processing    Data processing summary
 * @var array $module_health      Module health indicators
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap complyflow-dashboard">
    <div class="dashboard-header">
        <h1><?php esc_html_e('ComplyFlow Dashboard', 'complyflow'); ?></h1>
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <button type="button" id="run-full-scan" class="page-title-action" aria-label="<?php esc_attr_e('Run full compliance scan', 'complyflow'); ?>">
                <?php esc_html_e('Run Full Scan', 'complyflow'); ?>
            </button>
            <button type="button" id="cf-dark-toggle" class="page-title-action" aria-label="<?php esc_attr_e('Toggle dark mode', 'complyflow'); ?>">
                <span id="cf-dark-toggle-label"><?php esc_html_e('Dark Mode', 'complyflow'); ?></span>
            </button>
            <?php if (!defined('COMPLYFLOW_DEBUG_DASH') || COMPLYFLOW_DEBUG_DASH): ?>
                <span style="color:#fff;font-size:12px;opacity:.85;">
                    <?php echo esc_html(__('Dashboard view loaded', 'complyflow')); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="dashboard-welcome">
        <div class="welcome-info">
            <p class="welcome-version">
                <?php
                printf(
                    /* translators: %s: Plugin version */
                    esc_html__('Version %s', 'complyflow'),
                    esc_html(COMPLYFLOW_VERSION)
                );
                ?>
            </p>
            <?php if (!empty($accessibility_summary['last_scan'])): ?>
                <p class="welcome-last-scan">
                    <?php
                    printf(
                        /* translators: %s: Last scan date */
                        esc_html__('Last scan: %s', 'complyflow'),
                        esc_html(wp_date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($accessibility_summary['last_scan'])))
                    );
                    ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="welcome-links">
            <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-settings')); ?>" class="welcome-link">
                <?php esc_html_e('Settings', 'complyflow'); ?>
            </a>
            <a href="https://complyflow.com/docs" target="_blank" class="welcome-link">
                <?php esc_html_e('Documentation', 'complyflow'); ?>
            </a>
            <a href="https://complyflow.com/support" target="_blank" class="welcome-link">
                <?php esc_html_e('Support', 'complyflow'); ?>
            </a>
        </div>
    </div>

    <!-- Compliance Score Card -->
    <div class="compliance-score-card" aria-labelledby="cf-compliance-heading">
        <h2 id="cf-compliance-heading"><?php esc_html_e('Compliance Score', 'complyflow'); ?></h2>

        <div class="score-display" role="group" aria-label="<?php esc_attr_e('Overall compliance performance', 'complyflow'); ?>">
            <div class="score-circle" aria-hidden="true" data-score="<?php echo esc_attr($compliance_score['score']); ?>">
                <svg width="200" height="200" viewBox="0 0 200 200" role="img" aria-labelledby="cf-score-label">
                    <title id="cf-score-label"><?php esc_html_e('Overall compliance score', 'complyflow'); ?></title>
                    <circle class="score-bg" cx="100" cy="100" r="90" fill="none" stroke="#e0e0e0" stroke-width="12" />
                    <circle class="score-progress cf-score-color" cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="12"
                        stroke-dasharray="<?php echo esc_attr(565.48); ?>"
                        stroke-dashoffset="<?php echo esc_attr(565.48 * (1 - $compliance_score['score'] / 100)); ?>"
                        transform="rotate(-90 100 100)" 
                        stroke-linecap="round" />
                </svg>
                <div class="score-overlay">
                    <span class="score-number cf-compliance-score" aria-live="polite"><?php echo esc_html($compliance_score['score']); ?></span>
                    <span class="grade-badge grade-<?php echo esc_attr($compliance_score['grade']); ?>">
                        <?php echo esc_html($compliance_score['grade']); ?>
                    </span>
                </div>
            </div>
            <div class="score-status">
                <p class="status-label status-<?php echo esc_attr($compliance_score['status']); ?>">
                    <?php
                    $status_labels = [
                        'excellent' => __('Excellent Compliance', 'complyflow'),
                        'good' => __('Good Compliance', 'complyflow'),
                        'needs-improvement' => __('Needs Improvement', 'complyflow'),
                        'critical' => __('Critical Issues', 'complyflow'),
                    ];
                    echo esc_html($status_labels[$compliance_score['status']] ?? '');
                    ?>
                </p>
            </div>
        </div>

        <div class="score-breakdown" role="group" aria-label="<?php esc_attr_e('Module score breakdown', 'complyflow'); ?>">
            <h3><?php esc_html_e('Module Breakdown', 'complyflow'); ?></h3>
            <div style="height:260px;">
                <canvas id="cf-module-breakdown-chart" aria-label="<?php esc_attr_e('Module score bar chart', 'complyflow'); ?>" role="img"></canvas>
            </div>
            <div class="chart-help-box" style="margin-top:16px;padding:14px;background:#f0f9ff;border-left:4px solid #3b82f6;border-radius:6px;">
                <div style="font-weight:600;font-size:13px;color:#1e3a8a;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                    <span class="dashicons dashicons-info" style="font-size:16px;"></span>
                    <?php esc_html_e('How to Interpret', 'complyflow'); ?>
                </div>
                <p style="margin:0 0 10px;font-size:12px;color:#1e40af;line-height:1.6;">
                    <?php esc_html_e('Each bar shows individual module compliance (0-100%). Blue (80-100%) = Excellent, Cyan (60-79%) = Good, Purple (40-59%) = Needs Improvement, Orange (20-39%) = Requires Attention, Red (<20%) = Critical.', 'complyflow'); ?>
                </p>
                <div style="font-weight:600;font-size:12px;color:#1e3a8a;margin-bottom:6px;">
                    💡 <?php esc_html_e('Tips to Improve:', 'complyflow'); ?>
                </div>
                <ul style="margin:0;padding-left:20px;font-size:12px;color:#1e40af;line-height:1.6;">
                    <li><?php esc_html_e('Focus on modules scoring below 60% first', 'complyflow'); ?></li>
                    <li><?php esc_html_e('Run scans regularly to detect new issues early', 'complyflow'); ?></li>
                    <li><?php esc_html_e('Fix critical issues before addressing moderate ones', 'complyflow'); ?></li>
                    <li><?php esc_html_e('Review and update legal documents quarterly', 'complyflow'); ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Widgets Grid -->
    <div class="dashboard-widgets">
        
        <!-- DSR Requests Widget -->
        <div class="dashboard-widget widget-dsr" role="region" aria-labelledby="cf-dsr-heading">
            <div class="widget-header">
                <h3 id="cf-dsr-heading"><?php esc_html_e('DSR Requests', 'complyflow'); ?></h3>
                <span class="dashicons dashicons-admin-users"></span>
            </div>
            <div class="widget-body">
                <div class="pending-count">
                    <?php echo esc_html($dsr_stats['pending']); ?>
                </div>
                <p class="pending-label"><?php esc_html_e('Pending Requests', 'complyflow'); ?></p>
                
                <ul class="status-list">
                    <li>
                        <span class="status-label"><?php esc_html_e('Verified', 'complyflow'); ?></span>
                        <span class="status-count"><?php echo esc_html($dsr_stats['verified']); ?></span>
                    </li>
                    <li>
                        <span class="status-label"><?php esc_html_e('In Progress', 'complyflow'); ?></span>
                        <span class="status-count"><?php echo esc_html($dsr_stats['in_progress']); ?></span>
                    </li>
                    <li>
                        <span class="status-label"><?php esc_html_e('Completed', 'complyflow'); ?></span>
                        <span class="status-count"><?php echo esc_html($dsr_stats['completed']); ?></span>
                    </li>
                </ul>
                
                <?php if (!empty($dsr_stats['by_type'])): ?>
                <div class="dsr-type-breakdown" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <h4 style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                        <?php esc_html_e('Request Types', 'complyflow'); ?>
                    </h4>
                    <ul class="type-list" style="display: flex; flex-direction: column; gap: 8px;">
                        <?php 
                        $type_labels = [
                            'access' => __('Data Access', 'complyflow'),
                            'deletion' => __('Data Deletion', 'complyflow'),
                            'rectification' => __('Data Correction', 'complyflow'),
                            'portability' => __('Data Portability', 'complyflow'),
                            'restriction' => __('Processing Restriction', 'complyflow'),
                            'objection' => __('Processing Objection', 'complyflow'),
                        ];
                        foreach ($dsr_stats['by_type'] as $type => $count):
                            if ($count > 0):
                        ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                            <span style="color: #374151;">
                                <span class="dashicons dashicons-arrow-right-alt2" style="font-size: 14px; width: 14px; height: 14px; color: #9ca3af;"></span>
                                <?php echo esc_html($type_labels[$type] ?? ucfirst($type)); ?>
                            </span>
                            <span style="font-weight: 600; color: #1f2937; background: #f3f4f6; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                <?php echo esc_html($count); ?>
                            </span>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="widget-help-note" style="margin-top:16px;padding:12px;background:#fef3c7;border-radius:6px;border-left:3px solid #f59e0b;">
                    <div style="font-size:11px;font-weight:600;color:#92400e;margin-bottom:6px;">
                        ⚡ <?php esc_html_e('GDPR Compliance:', 'complyflow'); ?>
                    </div>
                    <p style="margin:0;font-size:11px;color:#92400e;line-height:1.5;">
                        <?php esc_html_e('Respond to DSR requests within 30 days (GDPR Article 12). High pending counts increase legal risk. Verify requests quickly to maintain compliance.', 'complyflow'); ?>
                    </p>
                </div>
                
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-dsr')); ?>" class="widget-link">
                    <?php esc_html_e('View All Requests →', 'complyflow'); ?>
                </a>
            </div>
        </div>

        <!-- Consent Statistics Widget -->
        <div class="dashboard-widget widget-consent" role="region" aria-labelledby="cf-consent-heading">
            <div class="widget-header">
                <h3 id="cf-consent-heading"><?php esc_html_e('Consent Statistics', 'complyflow'); ?></h3>
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="widget-body">
                <div class="acceptance-rate">
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e0e0e0" stroke-width="12"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#00a32a" stroke-width="12"
                                stroke-dasharray="<?php echo esc_attr(339.29); ?>"
                                stroke-dashoffset="<?php echo esc_attr(339.29 * (1 - $consent_stats['acceptance_rate'] / 100)); ?>"
                                transform="rotate(-90 60 60)"/>
                    </svg>
                    <div class="rate-overlay">
                        <span class="rate-number"><?php echo esc_html($consent_stats['acceptance_rate']); ?>%</span>
                    </div>
                </div>
                <p class="rate-label"><?php esc_html_e('Acceptance Rate', 'complyflow'); ?></p>
                
                <div class="consent-counts">
                    <div class="count-item count-accepted">
                        <span class="count-number"><?php echo esc_html($consent_stats['accepted_count']); ?></span>
                        <span class="count-label"><?php esc_html_e('Accepted', 'complyflow'); ?></span>
                    </div>
                    <div class="count-item count-rejected">
                        <span class="count-number"><?php echo esc_html($consent_stats['rejected_count']); ?></span>
                        <span class="count-label"><?php esc_html_e('Rejected', 'complyflow'); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($consent_stats['by_category'])): ?>
                <div class="consent-category-breakdown" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <h4 style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                        <?php esc_html_e('Consent by Category', 'complyflow'); ?>
                    </h4>
                    <ul class="category-list" style="display: flex; flex-direction: column; gap: 8px;">
                        <?php 
                        $category_labels = [
                            'necessary' => __('Necessary', 'complyflow'),
                            'functional' => __('Functional', 'complyflow'),
                            'analytics' => __('Analytics', 'complyflow'),
                            'marketing' => __('Marketing', 'complyflow'),
                            'preferences' => __('Preferences', 'complyflow'),
                        ];
                        $category_colors = [
                            'necessary' => '#10b981',
                            'functional' => '#3b82f6',
                            'analytics' => '#8b5cf6',
                            'marketing' => '#f59e0b',
                            'preferences' => '#ec4899',
                        ];
                        foreach ($consent_stats['by_category'] as $category => $count):
                            if ($count > 0):
                                $color = $category_colors[$category] ?? '#6b7280';
                        ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                            <span style="display: flex; align-items: center; gap: 8px; color: #374151;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo esc_attr($color); ?>;"></span>
                                <?php echo esc_html($category_labels[$category] ?? ucfirst($category)); ?>
                            </span>
                            <span style="font-weight: 600; color: #1f2937; background: #f3f4f6; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                <?php echo esc_html($count); ?>
                            </span>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="widget-help-note" style="margin-top:16px;padding:12px;background:#d1fae5;border-radius:6px;border-left:3px solid #10b981;">
                    <div style="font-size:11px;font-weight:600;color:#065f46;margin-bottom:6px;">
                        📊 <?php esc_html_e('What This Means:', 'complyflow'); ?>
                    </div>
                    <p style="margin:0 0 8px;font-size:11px;color:#065f46;line-height:1.5;">
                        <?php esc_html_e('Acceptance rate shows how many visitors agree to your cookie policy. Industry average: 40-70%. Lower rates may indicate unclear messaging or excessive tracking.', 'complyflow'); ?>
                    </p>
                    <div style="font-size:11px;font-weight:600;color:#065f46;margin-bottom:4px;">💡 <?php esc_html_e('Improve Rates:', 'complyflow'); ?></div>
                    <ul style="margin:0;padding-left:16px;font-size:11px;color:#065f46;">
                        <li><?php esc_html_e('Use clear, simple language', 'complyflow'); ?></li>
                        <li><?php esc_html_e('Highlight benefits of consent', 'complyflow'); ?></li>
                        <li><?php esc_html_e('Minimize unnecessary cookies', 'complyflow'); ?></li>
                    </ul>
                </div>
                
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-consent')); ?>" class="widget-link">
                    <?php esc_html_e('View Details →', 'complyflow'); ?>
                </a>
            </div>
        </div>

        <!-- Accessibility Issues Widget -->
        <div class="dashboard-widget widget-accessibility" role="region" aria-labelledby="cf-accessibility-heading">
            <div class="widget-header">
                <h3 id="cf-accessibility-heading"><?php esc_html_e('Accessibility Issues', 'complyflow'); ?></h3>
                <span class="dashicons dashicons-universal-access"></span>
            </div>
            <div class="widget-body">
                <div style="height:220px;margin-bottom:12px;">
                    <canvas id="cf-accessibility-severity-chart" aria-label="<?php esc_attr_e('Accessibility issues severity chart', 'complyflow'); ?>" role="img"></canvas>
                </div>
                <p class="chart-help-text">
                    <?php esc_html_e('Polar area chart showing issue distribution by severity. Red=Critical (fix first), Orange=Serious, Yellow=Moderate. Larger areas indicate more issues.', 'complyflow'); ?>
                </p>
                <p class="total-issues" aria-live="polite">
                    <?php
                    printf(
                        esc_html(_n('%d total issue found', '%d total issues found', $accessibility_summary['total_issues'], 'complyflow')),
                        esc_html($accessibility_summary['total_issues'])
                    );
                    ?>
                </p>
                
                <div class="widget-help-note" style="margin-top:12px;padding:12px;background:#fef2f2;border-radius:6px;border-left:3px solid #dc2626;">
                    <div style="font-size:11px;font-weight:600;color:#991b1b;margin-bottom:6px;">
                        ⚠️ <?php esc_html_e('Priority Levels:', 'complyflow'); ?>
                    </div>
                    <ul style="margin:0;padding-left:16px;font-size:11px;color:#991b1b;line-height:1.5;">
                        <li><strong><?php esc_html_e('Critical:', 'complyflow'); ?></strong> <?php esc_html_e('Fix immediately - blocks accessibility for disabled users', 'complyflow'); ?></li>
                        <li><strong><?php esc_html_e('Serious:', 'complyflow'); ?></strong> <?php esc_html_e('Fix within 1 week - major barriers', 'complyflow'); ?></li>
                        <li><strong><?php esc_html_e('Moderate:', 'complyflow'); ?></strong> <?php esc_html_e('Fix within 1 month - usability issues', 'complyflow'); ?></li>
                    </ul>
                    <p style="margin:8px 0 0;font-size:11px;color:#991b1b;font-style:italic;">
                        <?php esc_html_e('ADA/WCAG compliance requires addressing all critical and serious issues.', 'complyflow'); ?>
                    </p>
                </div>
                
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-accessibility')); ?>" class="widget-link">
                    <?php esc_html_e('View Report →', 'complyflow'); ?>
                </a>
            </div>
        </div>

        <!-- Cookie Inventory Widget -->
        <div class="dashboard-widget widget-cookies" role="region" aria-labelledby="cf-cookie-heading">
            <div class="widget-header">
                <h3 id="cf-cookie-heading"><?php esc_html_e('Cookie Inventory', 'complyflow'); ?></h3>
                <span class="dashicons dashicons-list-view"></span>
            </div>
            <div class="widget-body">
                <div class="cookie-total">
                    <span class="total-number"><?php echo esc_html($cookie_summary['total_cookies']); ?></span>
                    <span class="total-label"><?php esc_html_e('Total Cookies', 'complyflow'); ?></span>
                </div>
                
                <!-- Scanned vs Manual Breakdown -->
                <div style="display: flex; gap: 12px; margin-bottom: 16px; padding: 12px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 8px;">
                    <div style="flex: 1; text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: #0ea5e9;"><?php echo esc_html($cookie_summary['scanned']); ?></div>
                        <div style="font-size: 12px; color: #0369a1; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e('Auto-Scanned', 'complyflow'); ?></div>
                    </div>
                    <div style="width: 1px; background: linear-gradient(to bottom, transparent, #94a3b8, transparent);"></div>
                    <div style="flex: 1; text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: #f59e0b;"><?php echo esc_html($cookie_summary['manual']); ?></div>
                        <div style="font-size: 12px; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e('Manual/Import', 'complyflow'); ?></div>
                    </div>
                </div>
                
                <div style="height:200px;margin-bottom:14px;">
                    <canvas id="cf-cookie-category-chart" aria-label="<?php esc_attr_e('Cookie category distribution chart', 'complyflow'); ?>" role="img"></canvas>
                </div>
                <p class="chart-help-text">
                    <?php esc_html_e('Doughnut chart showing cookies by category (Necessary, Functional, Analytics, Marketing). Helps ensure proper consent categories are assigned.', 'complyflow'); ?>
                </p>
                
                <div class="widget-help-note" style="margin-top:12px;padding:12px;background:#dbeafe;border-radius:6px;border-left:3px solid #3b82f6;">
                    <div style="font-size:11px;font-weight:600;color:#1e3a8a;margin-bottom:6px;">
                        🍪 <?php esc_html_e('Cookie Categories:', 'complyflow'); ?>
                    </div>
                    <ul style="margin:0;padding-left:16px;font-size:11px;color:#1e40af;line-height:1.5;">
                        <li><strong><?php esc_html_e('Necessary:', 'complyflow'); ?></strong> <?php esc_html_e('No consent needed - essential site functions', 'complyflow'); ?></li>
                        <li><strong><?php esc_html_e('Functional:', 'complyflow'); ?></strong> <?php esc_html_e('Enhances UX - preferences, language', 'complyflow'); ?></li>
                        <li><strong><?php esc_html_e('Analytics:', 'complyflow'); ?></strong> <?php esc_html_e('Tracks usage - Google Analytics, etc.', 'complyflow'); ?></li>
                        <li><strong><?php esc_html_e('Marketing:', 'complyflow'); ?></strong> <?php esc_html_e('Requires explicit consent - ads, tracking', 'complyflow'); ?></li>
                    </ul>
                    <p style="margin:8px 0 0;font-size:11px;color:#1e40af;font-style:italic;">
                        <?php esc_html_e('Tip: Scan regularly to detect new cookies from plugins/themes.', 'complyflow'); ?>
                    </p>
                </div>
                
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-cookies')); ?>" class="widget-link">
                    <?php esc_html_e('Manage Cookies →', 'complyflow'); ?>
                </a>
            </div>
        </div>

    </div>

    <!-- Enhanced Analytics Section -->
    <div class="dashboard-analytics-enhanced">
        <h2><?php esc_html_e('Advanced Analytics', 'complyflow'); ?></h2>

        <!-- Analytics Grid -->
        <div class="analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
            
            <!-- Compliance Trend Widget -->
            <div class="dashboard-widget widget-trend" role="region" aria-labelledby="cf-trend-heading">
                <div class="widget-header">
                    <h3 id="cf-trend-heading"><?php esc_html_e('30-Day Compliance Trend', 'complyflow'); ?></h3>
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="widget-body">
                    <div style="height:200px;margin-bottom:12px;">
                        <canvas id="cf-compliance-trend-chart" aria-label="<?php esc_attr_e('Compliance trend line chart', 'complyflow'); ?>" role="img"></canvas>
                    </div>
                    <?php if (!empty($trends['trend'])): ?>
                    <div class="trend-indicator" style="text-align:center;padding:12px;background:<?php echo $trends['trend'] > 0 ? '#d1fae5' : '#fee2e2'; ?>;border-radius:6px;">
                        <span class="dashicons dashicons-arrow-<?php echo $trends['trend'] > 0 ? 'up' : 'down'; ?>-alt" style="color:<?php echo $trends['trend'] > 0 ? '#059669' : '#dc2626'; ?>;font-size:20px;"></span>
                        <span style="font-weight:600;color:<?php echo $trends['trend'] > 0 ? '#065f46' : '#991b1b'; ?>;font-size:14px;">
                            <?php 
                            echo $trends['trend'] > 0 
                                ? sprintf(esc_html__('Improving (+%d%%)', 'complyflow'), abs($trends['trend'] * 100))
                                : sprintf(esc_html__('Declining (%d%%)', 'complyflow'), abs($trends['trend'] * 100));
                            ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="widget-help-note" style="margin-top:16px;padding:12px;background:#f0fdf4;border-radius:6px;border-left:3px solid #10b981;">
                        <div style="font-size:11px;font-weight:600;color:#065f46;margin-bottom:6px;">
                            📈 <?php esc_html_e('Reading the Trend:', 'complyflow'); ?>
                        </div>
                        <ul style="margin:0;padding-left:16px;font-size:11px;color:#065f46;line-height:1.5;">
                            <li><strong><?php esc_html_e('Rising trend:', 'complyflow'); ?></strong> <?php esc_html_e('Your compliance efforts are working', 'complyflow'); ?></li>
                            <li><strong><?php esc_html_e('Flat trend:', 'complyflow'); ?></strong> <?php esc_html_e('Stable but may need attention', 'complyflow'); ?></li>
                            <li><strong><?php esc_html_e('Declining trend:', 'complyflow'); ?></strong> <?php esc_html_e('Issues accumulating - take action', 'complyflow'); ?></li>
                        </ul>
                        <p style="margin:8px 0 0;font-size:11px;color:#065f46;font-style:italic;">
                            <?php esc_html_e('Monitor weekly to catch problems early. Sudden drops indicate new issues.', 'complyflow'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Risk Assessment Widget -->
            <div class="dashboard-widget widget-risk" role="region" aria-labelledby="cf-risk-heading">
                <div class="widget-header">
                    <h3 id="cf-risk-heading"><?php esc_html_e('Risk Assessment', 'complyflow'); ?></h3>
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <div class="widget-body">
                    <?php
                    $risk_colors = [
                        'low' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => __('Low Risk', 'complyflow')],
                        'medium' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => __('Medium Risk', 'complyflow')],
                        'high' => ['bg' => '#fed7aa', 'text' => '#9a3412', 'label' => __('High Risk', 'complyflow')],
                        'critical' => ['bg' => '#fecaca', 'text' => '#991b1b', 'label' => __('Critical Risk', 'complyflow')],
                    ];
                    $risk_info = $risk_colors[$risk_assessment['level']] ?? $risk_colors['low'];
                    ?>
                    <div class="risk-level" style="text-align:center;padding:20px;background:<?php echo esc_attr($risk_info['bg']); ?>;border-radius:8px;margin-bottom:16px;">
                        <div style="font-size:36px;font-weight:700;color:<?php echo esc_attr($risk_info['text']); ?>;">
                            <?php echo esc_html($risk_assessment['score']); ?>
                        </div>
                        <div style="font-size:14px;font-weight:600;color:<?php echo esc_attr($risk_info['text']); ?>;text-transform:uppercase;letter-spacing:0.5px;">
                            <?php echo esc_html($risk_info['label']); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($risk_assessment['risk_factors'])): ?>
                    <div class="risk-factors">
                        <h4 style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">
                            <?php esc_html_e('Risk Factors', 'complyflow'); ?>
                        </h4>
                        <ul class="factor-list" style="display:flex;flex-direction:column;gap:10px;">
                            <?php foreach (array_slice($risk_assessment['risk_factors'], 0, 3) as $factor): 
                                $severity_colors = [
                                    'high' => '#dc2626',
                                    'medium' => '#f59e0b',
                                    'low' => '#3b82f6',
                                ];
                                $color = $severity_colors[$factor['severity']] ?? '#6b7280';
                            ?>
                            <li style="padding:10px;background:#f9fafb;border-left:3px solid <?php echo esc_attr($color); ?>;border-radius:4px;">
                                <div style="font-weight:600;font-size:13px;color:#1f2937;margin-bottom:4px;">
                                    <?php echo esc_html($factor['factor']); ?>
                                </div>
                                <div style="font-size:12px;color:#6b7280;">
                                    <?php echo esc_html($factor['description']); ?>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div class="widget-help-note" style="margin-top:16px;padding:12px;background:<?php echo $risk_assessment['level'] === 'critical' || $risk_assessment['level'] === 'high' ? '#fef2f2' : '#f0fdf4'; ?>;border-radius:6px;border-left:3px solid:<?php echo $risk_assessment['level'] === 'critical' ? '#dc2626' : ($risk_assessment['level'] === 'high' ? '#f59e0b' : '#10b981'); ?>;">
                        <div style="font-size:11px;font-weight:600;color:<?php echo $risk_assessment['level'] === 'critical' || $risk_assessment['level'] === 'high' ? '#991b1b' : '#065f46'; ?>;margin-bottom:6px;">
                            🎯 <?php esc_html_e('Action Plan:', 'complyflow'); ?>
                        </div>
                        <ul style="margin:0;padding-left:16px;font-size:11px;color:<?php echo $risk_assessment['level'] === 'critical' || $risk_assessment['level'] === 'high' ? '#991b1b' : '#065f46'; ?>;line-height:1.5;">
                            <li><?php esc_html_e('Address risk factors in order shown (highest priority first)', 'complyflow'); ?></li>
                            <li><?php esc_html_e('Critical risks can result in legal penalties', 'complyflow'); ?></li>
                            <li><?php esc_html_e('Aim to keep risk level at Medium or below', 'complyflow'); ?></li>
                            <li><?php esc_html_e('Re-scan after fixing issues to update risk score', 'complyflow'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Data Processing Widget -->
            <div class="dashboard-widget widget-processing" role="region" aria-labelledby="cf-processing-heading">
                <div class="widget-header">
                    <h3 id="cf-processing-heading"><?php esc_html_e('Data Processing', 'complyflow'); ?></h3>
                    <span class="dashicons dashicons-database"></span>
                </div>
                <div class="widget-body">
                    <div class="processing-stats" style="display:flex;flex-direction:column;gap:16px;">
                        <div class="stat-row" style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:#f9fafb;border-radius:6px;">
                            <span style="font-size:13px;color:#6b7280;">
                                <span class="dashicons dashicons-admin-users" style="font-size:16px;width:16px;height:16px;color:#3b82f6;"></span>
                                <?php esc_html_e('DSR Records', 'complyflow'); ?>
                            </span>
                            <span style="font-weight:700;font-size:18px;color:#1f2937;">
                                <?php echo esc_html($data_processing['total_records']); ?>
                            </span>
                        </div>
                        
                        <div class="stat-row" style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:#f9fafb;border-radius:6px;">
                            <span style="font-size:13px;color:#6b7280;">
                                <span class="dashicons dashicons-clock" style="font-size:16px;width:16px;height:16px;color:#8b5cf6;"></span>
                                <?php esc_html_e('Avg. Fulfillment', 'complyflow'); ?>
                            </span>
                            <span style="font-weight:700;font-size:18px;color:#1f2937;">
                                <?php echo esc_html($data_processing['dsr_fulfillment']); ?>h
                            </span>
                        </div>
                        
                        <div class="stat-row" style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:#f9fafb;border-radius:6px;">
                            <span style="font-size:13px;color:#6b7280;">
                                <span class="dashicons dashicons-yes-alt" style="font-size:16px;width:16px;height:16px;color:#10b981;"></span>
                                <?php esc_html_e('Consent Updates', 'complyflow'); ?>
                            </span>
                            <span style="font-weight:700;font-size:18px;color:#1f2937;">
                                <?php echo esc_html($data_processing['consent_updates']); ?>
                            </span>
                        </div>
                        
                        <div class="stat-row" style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:#f9fafb;border-radius:6px;">
                            <span style="font-size:13px;color:#6b7280;">
                                <span class="dashicons dashicons-download" style="font-size:16px;width:16px;height:16px;color:#f59e0b;"></span>
                                <?php esc_html_e('Data Exports', 'complyflow'); ?>
                            </span>
                            <span style="font-weight:700;font-size:18px;color:#1f2937;">
                                <?php echo esc_html($data_processing['data_exports']); ?>
                            </span>
                        </div>
                    </div>
                    <p style="margin-top:16px;font-size:12px;color:#6b7280;text-align:center;">
                        <?php esc_html_e('Statistics for current month', 'complyflow'); ?>
                    </p>
                    
                    <div class="widget-help-note" style="margin-top:12px;padding:12px;background:#fef3c7;border-radius:6px;border-left:3px solid #f59e0b;">
                        <div style="font-size:11px;font-weight:600;color:#92400e;margin-bottom:6px;">
                            ⏱️ <?php esc_html_e('Performance Benchmarks:', 'complyflow'); ?>
                        </div>
                        <ul style="margin:0;padding-left:16px;font-size:11px;color:#92400e;line-height:1.5;">
                            <li><strong><?php esc_html_e('DSR Fulfillment:', 'complyflow'); ?></strong> <?php esc_html_e('Must be under 720h (30 days) per GDPR', 'complyflow'); ?></li>
                            <li><strong><?php esc_html_e('Consent Updates:', 'complyflow'); ?></strong> <?php esc_html_e('Higher numbers = active user base', 'complyflow'); ?></li>
                            <li><strong><?php esc_html_e('Data Exports:', 'complyflow'); ?></strong> <?php esc_html_e('Track for audit trail compliance', 'complyflow'); ?></li>
                        </ul>
                        <p style="margin:8px 0 0;font-size:11px;color:#92400e;font-style:italic;">
                            <?php esc_html_e('Slow fulfillment times? Automate responses or increase staff capacity.', 'complyflow'); ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Activity Timeline & Module Health -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:30px;" class="dashboard-double-widgets">
            
            <!-- Recent Activity Timeline -->
            <div class="dashboard-widget widget-activity" role="region" aria-labelledby="cf-activity-heading">
                <div class="widget-header">
                    <h3 id="cf-activity-heading"><?php esc_html_e('Recent Activity', 'complyflow'); ?></h3>
                    <span class="dashicons dashicons-backup"></span>
                </div>
                <div class="widget-body">
                    <?php if (!empty($activities)): ?>
                    <div class="activity-timeline" style="max-height:340px;overflow-y:auto;">
                        <?php foreach (array_slice($activities, 0, 8) as $activity): 
                            $icon_colors = [
                                'dsr' => '#3b82f6',
                                'scan' => '#8b5cf6',
                                'consent' => '#10b981',
                            ];
                            $color = $icon_colors[$activity['type']] ?? '#6b7280';
                        ?>
                        <div class="activity-item" style="display:flex;gap:12px;padding:12px;border-bottom:1px solid #e5e7eb;">
                            <div class="activity-icon" style="flex-shrink:0;width:32px;height:32px;border-radius:50%;background:<?php echo esc_attr($color); ?>15;display:flex;align-items:center;justify-content:center;">
                                <span class="dashicons dashicons-<?php echo esc_attr($activity['icon']); ?>" style="color:<?php echo esc_attr($color); ?>;font-size:16px;width:16px;height:16px;"></span>
                            </div>
                            <div class="activity-content" style="flex:1;">
                                <div style="font-size:13px;color:#1f2937;font-weight:500;margin-bottom:2px;">
                                    <?php echo esc_html($activity['message']); ?>
                                </div>
                                <div style="font-size:11px;color:#9ca3af;">
                                    <?php echo esc_html($activity['time_ago']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p style="text-align:center;color:#9ca3af;padding:40px 20px;">
                        <?php esc_html_e('No recent activity', 'complyflow'); ?>
                    </p>
                    <?php endif; ?>
                    
                    <div class="widget-help-note" style="margin-top:12px;padding:12px;background:#f3f4f6;border-radius:6px;border-left:3px solid #6b7280;">
                        <div style="font-size:11px;font-weight:600;color:#374151;margin-bottom:6px;">
                            🔍 <?php esc_html_e('Why This Matters:', 'complyflow'); ?>
                        </div>
                        <p style="margin:0;font-size:11px;color:#4b5563;line-height:1.5;">
                            <?php esc_html_e('Activity timeline provides an audit trail for compliance officers. Use this to demonstrate active privacy management to regulators. Sudden spikes in DSR requests may indicate a data breach or PR issue.', 'complyflow'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Module Health Status -->
            <div class="dashboard-widget widget-health" role="region" aria-labelledby="cf-health-heading">
                <div class="widget-header">
                    <h3 id="cf-health-heading"><?php esc_html_e('Module Health', 'complyflow'); ?></h3>
                    <span class="dashicons dashicons-heart"></span>
                </div>
                <div class="widget-body">
                    <?php if (!empty($module_health)): ?>
                    <div class="health-list" style="display:flex;flex-direction:column;gap:12px;">
                        <?php foreach ($module_health as $module): 
                            $status_colors = [
                                'excellent' => ['bg' => '#d1fae5', 'bar' => '#10b981', 'text' => '#065f46'],
                                'good' => ['bg' => '#dbeafe', 'bar' => '#3b82f6', 'text' => '#1e3a8a'],
                                'warning' => ['bg' => '#fef3c7', 'bar' => '#f59e0b', 'text' => '#92400e'],
                                'critical' => ['bg' => '#fee2e2', 'bar' => '#dc2626', 'text' => '#991b1b'],
                            ];
                            $colors = $status_colors[$module['status']] ?? $status_colors['good'];
                        ?>
                        <div class="health-item" style="padding:12px;background:<?php echo esc_attr($colors['bg']); ?>;border-radius:6px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <span style="font-size:13px;font-weight:600;color:<?php echo esc_attr($colors['text']); ?>;">
                                    <?php echo esc_html($module['module']); ?>
                                </span>
                                <span style="font-size:14px;font-weight:700;color:<?php echo esc_attr($colors['text']); ?>;">
                                    <?php echo esc_html($module['percentage']); ?>
                                </span>
                            </div>
                            <div class="health-bar" style="height:6px;background:rgba(0,0,0,0.1);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:<?php echo esc_attr($module['percentage']); ?>;background:<?php echo esc_attr($colors['bar']); ?>;transition:width 0.3s ease;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="widget-help-note" style="margin-top:12px;padding:12px;background:#eff6ff;border-radius:6px;border-left:3px solid #3b82f6;">
                        <div style="font-size:11px;font-weight:600;color:#1e3a8a;margin-bottom:6px;">
                            💊 <?php esc_html_e('Health Check Guide:', 'complyflow'); ?>
                        </div>
                        <ul style="margin:0;padding-left:16px;font-size:11px;color:#1e40af;line-height:1.5;">
                            <li><strong><?php esc_html_e('Green (80%+):', 'complyflow'); ?></strong> <?php esc_html_e('Excellent - maintain current practices', 'complyflow'); ?></li>
                            <li><strong><?php esc_html_e('Blue (60-79%):', 'complyflow'); ?></strong> <?php esc_html_e('Good - minor improvements needed', 'complyflow'); ?></li>
                            <li><strong><?php esc_html_e('Yellow (40-59%):', 'complyflow'); ?></strong> <?php esc_html_e('Warning - needs attention this week', 'complyflow'); ?></li>
                            <li><strong><?php esc_html_e('Red (<40%):', 'complyflow'); ?></strong> <?php esc_html_e('Critical - immediate action required', 'complyflow'); ?></li>
                        </ul>
                        <p style="margin:8px 0 0;font-size:11px;color:#1e40af;font-style:italic;">
                            <?php esc_html_e('Click module name to jump to details. Target: all modules above 70%.', 'complyflow'); ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Quick Actions -->
    <div class="dashboard-quick-actions">
        <h2><?php esc_html_e('Quick Actions', 'complyflow'); ?></h2>
        <div class="actions-grid">
            <button type="button" class="action-button" id="run-full-scan">
                <span class="dashicons dashicons-admin-site"></span>
                <?php esc_html_e('Run Full Scan', 'complyflow'); ?>
            </button>
            <button type="button" class="action-button" id="run-accessibility-scan">
                <span class="dashicons dashicons-search"></span>
                <?php esc_html_e('Run Accessibility Scan', 'complyflow'); ?>
            </button>
            <button type="button" class="action-button" id="scan-cookies">
                <span class="dashicons dashicons-update"></span>
                <?php esc_html_e('Scan Cookies', 'complyflow'); ?>
            </button>
            <button type="button" class="action-button" id="export-dsr-data">
                <span class="dashicons dashicons-download"></span>
                <?php esc_html_e('Export DSR Data', 'complyflow'); ?>
            </button>
        </div>
    </div>

</div>

<!-- Scan Results Modal -->
<div id="cf-scan-results-modal" class="cf-modal" style="display:none;">
    <div class="cf-modal-backdrop"></div>
    <div class="cf-modal-content">
        <div class="cf-modal-header">
            <h2 id="cf-modal-title"><?php esc_html_e('Scan Results', 'complyflow'); ?></h2>
            <button class="cf-modal-close" aria-label="<?php esc_attr_e('Close', 'complyflow'); ?>">&times;</button>
        </div>
        <div class="cf-modal-body">
            <div id="cf-scan-results-content">
                <!-- Results will be populated here -->
            </div>
        </div>
        <div class="cf-modal-footer">
            <button class="cf-btn cf-btn-secondary cf-modal-close"><?php esc_html_e('Close', 'complyflow'); ?></button>
            <button id="cf-view-details" class="cf-btn cf-btn-primary" style="display:none;"><?php esc_html_e('View Full Details', 'complyflow'); ?></button>
        </div>
    </div>
</div>
