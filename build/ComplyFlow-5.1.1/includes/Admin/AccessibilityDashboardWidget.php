<?php
/**
 * Dashboard Widget for Accessibility Scans
 *
 * @package ComplyFlow\Admin
 * @since   1.0.0
 */

namespace ComplyFlow\Admin;

use ComplyFlow\Modules\Accessibility\ScheduledScanManager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AccessibilityDashboardWidget
 */
class AccessibilityDashboardWidget {
    /**
     * Scheduled scan manager
     *
     * @var ScheduledScanManager
     */
    private ScheduledScanManager $scheduled_manager;

    /**
     * Constructor
     *
     * @param ScheduledScanManager $scheduled_manager Scheduled scan manager instance.
     */
    public function __construct(ScheduledScanManager $scheduled_manager) {
        $this->scheduled_manager = $scheduled_manager;
    }

    /**
     * Initialize widget
     *
     * @return void
     */
    public function init(): void {
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);
    }

    /**
     * Add dashboard widget
     *
     * @return void
     */
    public function add_dashboard_widget(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        wp_add_dashboard_widget(
            'complyflow_accessibility_widget',
            __('ComplyFlow - Accessibility Scans', 'complyflow'),
            [$this, 'render_widget']
        );
    }

    /**
     * Render dashboard widget
     *
     * @return void
     */
    public function render_widget(): void {
        $next_scan = $this->scheduled_manager->get_next_scheduled_time();
        $last_results = $this->scheduled_manager->get_last_results();
        $last_scan_time = $this->scheduled_manager->get_last_scan_time();
        ?>
        <div class="complyflow-dashboard-widget">
            <style>
                .complyflow-dashboard-widget .cf-stat-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 12px 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                .complyflow-dashboard-widget .cf-stat-row:last-child {
                    border-bottom: none;
                }
                .complyflow-dashboard-widget .cf-stat-label {
                    font-weight: 600;
                    color: #666;
                }
                .complyflow-dashboard-widget .cf-stat-value {
                    font-size: 16px;
                }
                .complyflow-dashboard-widget .cf-status-badge {
                    display: inline-block;
                    padding: 4px 10px;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 600;
                }
                .complyflow-dashboard-widget .cf-status-active {
                    background: #d5f5e3;
                    color: #00a32a;
                }
                .complyflow-dashboard-widget .cf-status-inactive {
                    background: #f5f5f5;
                    color: #666;
                }
                .complyflow-dashboard-widget .cf-scan-result {
                    padding: 10px;
                    margin: 10px 0;
                    background: #f9f9f9;
                    border-left: 3px solid #2271b1;
                    border-radius: 2px;
                }
                .complyflow-dashboard-widget .cf-scan-url {
                    font-weight: 600;
                    margin-bottom: 5px;
                }
                .complyflow-dashboard-widget .cf-scan-meta {
                    font-size: 12px;
                    color: #666;
                }
                .complyflow-dashboard-widget .cf-score-badge {
                    display: inline-block;
                    padding: 2px 8px;
                    border-radius: 10px;
                    font-weight: 600;
                    font-size: 12px;
                    margin-right: 5px;
                }
                .complyflow-dashboard-widget .cf-actions {
                    margin-top: 15px;
                    padding-top: 15px;
                    border-top: 1px solid #f0f0f0;
                }
                .complyflow-dashboard-widget .cf-action-btn {
                    display: inline-block;
                    margin-right: 10px;
                }
            </style>

            <!-- Schedule Status -->
            <div class="cf-stat-row">
                <span class="cf-stat-label">
                    <span class="dashicons dashicons-clock"></span>
                    <?php esc_html_e('Schedule Status', 'complyflow'); ?>
                </span>
                <span class="cf-stat-value">
                    <?php if ($next_scan): ?>
                        <span class="cf-status-badge cf-status-active">
                            <?php esc_html_e('Active', 'complyflow'); ?>
                        </span>
                    <?php else: ?>
                        <span class="cf-status-badge cf-status-inactive">
                            <?php esc_html_e('Inactive', 'complyflow'); ?>
                        </span>
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($next_scan): ?>
                <div class="cf-stat-row">
                    <span class="cf-stat-label">
                        <?php esc_html_e('Next Scan', 'complyflow'); ?>
                    </span>
                    <span class="cf-stat-value">
                        <?php echo esc_html(human_time_diff($next_scan, time())); ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($last_scan_time): ?>
                <div class="cf-stat-row">
                    <span class="cf-stat-label">
                        <?php esc_html_e('Last Scan', 'complyflow'); ?>
                    </span>
                    <span class="cf-stat-value">
                        <?php echo esc_html(human_time_diff($last_scan_time, time())); ?> <?php esc_html_e('ago', 'complyflow'); ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- Recent Results -->
            <?php if ($last_results && !empty($last_results)): ?>
                <div style="margin-top: 15px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 13px;">
                        <?php esc_html_e('Recent Scan Results', 'complyflow'); ?>
                    </h4>

                    <?php foreach (array_slice($last_results, 0, 3) as $result): ?>
                        <div class="cf-scan-result" style="
                            border-left-color: <?php echo $result['success'] ? '#00a32a' : '#d63638'; ?>;
                        ">
                            <div class="cf-scan-url">
                                <?php 
                                $url_parts = parse_url($result['url']);
                                echo esc_html($url_parts['host'] . ($url_parts['path'] ?? ''));
                                ?>
                            </div>
                            <div class="cf-scan-meta">
                                <?php if ($result['success']): ?>
                                    <span class="cf-score-badge" style="
                                        <?php
                                        if ($result['score'] >= 80) {
                                            echo 'background: #d5f5e3; color: #00a32a;';
                                        } elseif ($result['score'] >= 50) {
                                            echo 'background: #fff9e6; color: #f0b849;';
                                        } else {
                                            echo 'background: #ffe5e5; color: #d63638;';
                                        }
                                        ?>
                                    ">
                                        <?php echo esc_html(round($result['score'])); ?>
                                    </span>
                                    <?php
                                    $total_issues = array_sum($result['issues']);
                                    printf(
                                        esc_html(_n('%d issue found', '%d issues found', $total_issues, 'complyflow')),
                                        $total_issues
                                    );
                                    ?>
                                    <?php if (!empty($result['issues']['critical'])): ?>
                                        <span style="color: #d63638; font-weight: 600;">
                                            (<?php echo esc_html($result['issues']['critical']); ?> <?php esc_html_e('critical', 'complyflow'); ?>)
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #d63638;">
                                        <?php echo esc_html($result['error']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="cf-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-accessibility')); ?>" class="button cf-action-btn">
                    <?php esc_html_e('View All Scans', 'complyflow'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-accessibility-schedule')); ?>" class="button cf-action-btn">
                    <span class="dashicons dashicons-admin-settings" style="margin-top: 3px;"></span>
                    <?php esc_html_e('Settings', 'complyflow'); ?>
                </a>
            </div>
        </div>
        <?php
    }
}
