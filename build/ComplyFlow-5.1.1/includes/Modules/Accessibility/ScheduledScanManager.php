<?php
/**
 * Scheduled Scan Manager
 *
 * Handles scheduled accessibility scans via WP-Cron
 *
 * @package    ComplyFlow
 * @subpackage Modules/Accessibility
 * @since      1.0.0
 */

namespace ComplyFlow\Modules\Accessibility;

use ComplyFlow\Core\Repositories\SettingsRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ScheduledScanManager
 */
class ScheduledScanManager {
    /**
     * Scanner instance
     *
     * @var Scanner
     */
    private Scanner $scanner;

    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Cron hook name
     *
     * @var string
     */
    private const CRON_HOOK = 'complyflow_scheduled_scan';

    /**
     * Constructor
     *
     * @param Scanner            $scanner  Scanner instance.
     * @param SettingsRepository $settings Settings repository.
     */
    public function __construct(Scanner $scanner, SettingsRepository $settings) {
        $this->scanner = $scanner;
        $this->settings = $settings;
    }

    /**
     * Initialize scheduled scans
     *
     * @return void
     */
    public function init(): void {
        // Register cron hook
        add_action(self::CRON_HOOK, [$this, 'run_scheduled_scans']);

        // Add custom cron intervals
        add_filter('cron_schedules', [$this, 'add_cron_intervals']);

        // Schedule initial cron if not already scheduled
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            $this->schedule_scans();
        }
    }

    /**
     * Add custom cron intervals
     *
     * @param array $schedules Existing schedules.
     * @return array Modified schedules.
     */
    public function add_cron_intervals(array $schedules): array {
        $schedules['complyflow_hourly'] = [
            'interval' => HOUR_IN_SECONDS,
            'display'  => __('Every Hour (ComplyFlow)', 'complyflow'),
        ];

        $schedules['complyflow_twice_daily'] = [
            'interval' => 12 * HOUR_IN_SECONDS,
            'display'  => __('Twice Daily (ComplyFlow)', 'complyflow'),
        ];

        $schedules['complyflow_weekly'] = [
            'interval' => WEEK_IN_SECONDS,
            'display'  => __('Once Weekly (ComplyFlow)', 'complyflow'),
        ];

        $schedules['complyflow_monthly'] = [
            'interval' => 30 * DAY_IN_SECONDS,
            'display'  => __('Once Monthly (ComplyFlow)', 'complyflow'),
        ];

        return $schedules;
    }

    /**
     * Schedule scans based on settings
     *
     * @return bool True on success, false on failure.
     */
    public function schedule_scans(): bool {
        $enabled = $this->settings->get('accessibility_scheduled_scans_enabled', false);

        if (!$enabled) {
            return $this->unschedule_scans();
        }

        $frequency = $this->settings->get('accessibility_scheduled_scans_frequency', 'daily');

        // Map frequency to WordPress schedule
        $schedule_map = [
            'hourly'      => 'complyflow_hourly',
            'twicedaily'  => 'complyflow_twice_daily',
            'daily'       => 'daily',
            'weekly'      => 'complyflow_weekly',
            'monthly'     => 'complyflow_monthly',
        ];

        $schedule = $schedule_map[$frequency] ?? 'daily';

        // Clear existing schedule
        $this->unschedule_scans();

        // Schedule new event
        $scheduled = wp_schedule_event(time(), $schedule, self::CRON_HOOK);

        if ($scheduled !== false) {
            do_action('complyflow_scheduled_scans_activated', $schedule);
            return true;
        }

        return false;
    }

    /**
     * Unschedule all scans
     *
     * @return bool True on success.
     */
    public function unschedule_scans(): bool {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);

        if ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }

        // Clear all instances of the hook
        wp_clear_scheduled_hook(self::CRON_HOOK);

        return true;
    }

    /**
     * Run scheduled scans
     *
     * @return void
     */
    public function run_scheduled_scans(): void {
        $urls = $this->get_scheduled_urls();

        if (empty($urls)) {
            return;
        }

        $results = [];

        foreach ($urls as $url) {
            try {
                $result = $this->scanner->scan_url($url);

                if ($result['success']) {
                    $results[] = [
                        'url'     => $url,
                        'success' => true,
                        'scan_id' => $result['scan_id'],
                        'score'   => $result['score'],
                        'issues'  => $result['summary']['by_severity'] ?? [],
                    ];

                    // Check if notification should be sent
                    $this->maybe_send_notification($url, $result);
                } else {
                    $results[] = [
                        'url'     => $url,
                        'success' => false,
                        'error'   => $result['error'] ?? __('Unknown error', 'complyflow'),
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'url'     => $url,
                    'success' => false,
                    'error'   => $e->getMessage(),
                ];
            }
        }

        // Store last run results
        update_option('complyflow_last_scheduled_scan_results', $results, false);
        update_option('complyflow_last_scheduled_scan_time', time(), false);

        // Fire action for extensibility
        do_action('complyflow_scheduled_scans_completed', $results);
    }

    /**
     * Get URLs to scan from settings
     *
     * @return array Array of URLs.
     */
    private function get_scheduled_urls(): array {
        $urls = $this->settings->get('accessibility_scheduled_scans_urls', []);

        if (empty($urls)) {
            // Default to home page
            $urls = [home_url()];
        }

        // Ensure all URLs are properly formatted
        return array_filter(array_map('esc_url_raw', $urls));
    }

    /**
     * Maybe send notification based on scan results
     *
     * @param string $url    URL scanned.
     * @param array  $result Scan result.
     * @return void
     */
    private function maybe_send_notification(string $url, array $result): void {
        $notifications_enabled = $this->settings->get('accessibility_notifications_enabled', false);

        if (!$notifications_enabled) {
            return;
        }

        $threshold = $this->settings->get('accessibility_notifications_threshold', 'critical');
        $summary = $result['summary'] ?? [];
        $by_severity = $summary['by_severity'] ?? [];

        // Check if threshold is met
        $should_notify = false;

        switch ($threshold) {
            case 'critical':
                $should_notify = !empty($by_severity['critical']);
                break;
            case 'serious':
                $should_notify = !empty($by_severity['critical']) || !empty($by_severity['serious']);
                break;
            case 'moderate':
                $should_notify = !empty($by_severity['critical']) || !empty($by_severity['serious']) || !empty($by_severity['moderate']);
                break;
            case 'any':
                $should_notify = !empty($by_severity);
                break;
        }

        if (!$should_notify) {
            return;
        }

        // Compare with previous scan if available
        $previous_scan = $this->get_previous_scan($url);
        $new_issues = [];

        if ($previous_scan) {
            $new_issues = $this->get_new_issues($previous_scan, $result['scan_id']);
        }

        $this->send_notification($url, $result, $new_issues);
    }

    /**
     * Get previous scan for URL
     *
     * @param string $url URL to check.
     * @return array|null Previous scan data or null.
     */
    private function get_previous_scan(string $url): ?array {
        global $wpdb;

        $table = $wpdb->prefix . 'complyflow_scan_results';

        $scan = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE url = %s ORDER BY created_at DESC LIMIT 1 OFFSET 1",
                $url
            ),
            ARRAY_A
        );

        return $scan ?: null;
    }

    /**
     * Get new issues by comparing scans
     *
     * @param array $previous_scan Previous scan data.
     * @param int   $current_scan_id Current scan ID.
     * @return array New issues.
     */
    private function get_new_issues(array $previous_scan, int $current_scan_id): array {
        $previous_issues = $this->scanner->export_scan((int) $previous_scan['id'])['issues'] ?? [];
        $current_issues = $this->scanner->export_scan($current_scan_id)['issues'] ?? [];

        // Create unique keys for comparison
        $previous_keys = array_map(function ($issue) {
            return $issue['selector'] . '|' . $issue['wcag_criterion'] . '|' . $issue['message'];
        }, $previous_issues);

        $new_issues = [];

        foreach ($current_issues as $issue) {
            $key = $issue['selector'] . '|' . $issue['wcag_criterion'] . '|' . $issue['message'];

            if (!in_array($key, $previous_keys, true)) {
                $new_issues[] = $issue;
            }
        }

        return $new_issues;
    }

    /**
     * Send email notification
     *
     * @param string $url        URL scanned.
     * @param array  $result     Scan result.
     * @param array  $new_issues New issues since last scan.
     * @return void
     */
    private function send_notification(string $url, array $result, array $new_issues = []): void {
        $recipients = $this->settings->get('accessibility_notifications_recipients', [get_option('admin_email')]);
        $score = $result['score'] ?? 0;
        $summary = $result['summary'] ?? [];
        $by_severity = $summary['by_severity'] ?? [];

        // Email subject
        $subject = sprintf(
            __('[ComplyFlow] Accessibility Scan Alert - %s', 'complyflow'),
            $url
        );

        // Email body
        $message = $this->get_notification_template($url, $score, $by_severity, $new_issues, $result['scan_id']);

        // Email headers
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>',
        ];

        // Send email to each recipient
        foreach ($recipients as $recipient) {
            wp_mail(trim($recipient), $subject, $message, $headers);
        }

        do_action('complyflow_notification_sent', $url, $recipients, $result);
    }

    /**
     * Get email notification template
     *
     * @param string $url         URL scanned.
     * @param float  $score       Accessibility score.
     * @param array  $by_severity Issues by severity.
     * @param array  $new_issues  New issues.
     * @param int    $scan_id     Scan ID.
     * @return string HTML email content.
     */
    private function get_notification_template(string $url, float $score, array $by_severity, array $new_issues, int $scan_id): string {
        $results_url = admin_url('admin.php?page=complyflow-accessibility-results&scan_id=' . $scan_id);

        $score_color = '#d63638'; // Red
        if ($score >= 80) {
            $score_color = '#00a32a'; // Green
        } elseif ($score >= 50) {
            $score_color = '#f0b849'; // Yellow
        }

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen-Sans, Ubuntu, Cantarell, \'Helvetica Neue\', sans-serif; line-height: 1.6; color: #333;">';
        $html .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px;">';
        
        // Header
        $html .= '<div style="background: #2271b1; color: #fff; padding: 20px; border-radius: 4px 4px 0 0;">';
        $html .= '<h1 style="margin: 0; font-size: 24px;">ComplyFlow Accessibility Alert</h1>';
        $html .= '</div>';

        // Content
        $html .= '<div style="background: #fff; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 4px 4px;">';
        
        // URL
        $html .= '<p style="font-size: 16px; margin-bottom: 20px;">';
        $html .= sprintf(__('A scheduled accessibility scan has been completed for:', 'complyflow')) . '<br>';
        $html .= '<strong><a href="' . esc_url($url) . '" style="color: #2271b1;">' . esc_html($url) . '</a></strong>';
        $html .= '</p>';

        // Score
        $html .= '<div style="text-align: center; margin: 30px 0;">';
        $html .= '<div style="display: inline-block; background: ' . $score_color . '; color: #fff; padding: 20px 40px; border-radius: 50%; font-size: 36px; font-weight: bold;">';
        $html .= round($score);
        $html .= '</div>';
        $html .= '<p style="margin-top: 10px; font-size: 14px; color: #666;">' . __('Accessibility Score', 'complyflow') . '</p>';
        $html .= '</div>';

        // Issues summary
        if (!empty($by_severity)) {
            $html .= '<h2 style="font-size: 18px; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">' . __('Issues Found', 'complyflow') . '</h2>';
            $html .= '<table style="width: 100%; border-collapse: collapse;">';
            
            $severity_labels = [
                'critical' => __('Critical', 'complyflow'),
                'serious'  => __('Serious', 'complyflow'),
                'moderate' => __('Moderate', 'complyflow'),
                'minor'    => __('Minor', 'complyflow'),
            ];

            $severity_colors = [
                'critical' => '#d63638',
                'serious'  => '#f0b849',
                'moderate' => '#f0c33c',
                'minor'    => '#72aee6',
            ];

            foreach ($by_severity as $severity => $count) {
                if ($count > 0) {
                    $html .= '<tr>';
                    $html .= '<td style="padding: 10px; border-bottom: 1px solid #eee;">';
                    $html .= '<span style="display: inline-block; padding: 4px 12px; border-radius: 12px; background: ' . $severity_colors[$severity] . '; color: #fff; font-weight: 600; font-size: 12px;">';
                    $html .= esc_html($severity_labels[$severity]);
                    $html .= '</span>';
                    $html .= '</td>';
                    $html .= '<td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right; font-weight: 600;">';
                    $html .= esc_html($count);
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }
            
            $html .= '</table>';
        }

        // New issues
        if (!empty($new_issues)) {
            $html .= '<h2 style="font-size: 18px; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">' . sprintf(__('New Issues (%d)', 'complyflow'), count($new_issues)) . '</h2>';
            $html .= '<ul style="margin: 15px 0; padding-left: 20px;">';
            
            foreach (array_slice($new_issues, 0, 5) as $issue) {
                $html .= '<li style="margin-bottom: 10px;">';
                $html .= '<strong>' . esc_html($issue['message']) . '</strong><br>';
                $html .= '<span style="color: #666; font-size: 13px;">WCAG ' . esc_html($issue['wcag_criterion']) . '</span>';
                $html .= '</li>';
            }

            if (count($new_issues) > 5) {
                $html .= '<li style="color: #666; font-style: italic;">' . sprintf(__('...and %d more', 'complyflow'), count($new_issues) - 5) . '</li>';
            }
            
            $html .= '</ul>';
        }

        // CTA Button
        $html .= '<div style="text-align: center; margin-top: 30px;">';
        $html .= '<a href="' . esc_url($results_url) . '" style="display: inline-block; background: #2271b1; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600;">';
        $html .= __('View Full Report', 'complyflow');
        $html .= '</a>';
        $html .= '</div>';

        $html .= '</div>'; // Content end

        // Footer
        $html .= '<div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">';
        $html .= '<p>' . sprintf(__('This is an automated message from %s', 'complyflow'), get_option('blogname')) . '</p>';
        $html .= '<p>' . __('To manage notification settings, visit your ComplyFlow dashboard.', 'complyflow') . '</p>';
        $html .= '</div>';

        $html .= '</div></body></html>';

        return $html;
    }

    /**
     * Get next scheduled scan time
     *
     * @return int|false Timestamp or false if not scheduled.
     */
    public function get_next_scheduled_time() {
        return wp_next_scheduled(self::CRON_HOOK);
    }

    /**
     * Get last scan results
     *
     * @return array|false Last scan results or false.
     */
    public function get_last_results() {
        return get_option('complyflow_last_scheduled_scan_results', false);
    }

    /**
     * Get last scan time
     *
     * @return int|false Timestamp or false.
     */
    public function get_last_scan_time() {
        return get_option('complyflow_last_scheduled_scan_time', false);
    }
}
