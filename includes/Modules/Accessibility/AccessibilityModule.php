<?php
/**
 * Accessibility Module
 *
 * WCAG 2.2 AA compliance scanning and reporting.
 *
 * @package ComplyFlow\Modules\Accessibility
 * @since 1.0.0
 */

namespace ComplyFlow\Modules\Accessibility;

use ComplyFlow\Core\Repositories\SettingsRepository;
use ComplyFlow\Admin\AccessibilityDashboardWidget;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Accessibility Module Class
 *
 * @since 1.0.0
 */
class AccessibilityModule {
    /**
     * Scanner instance
     *
     * @var Scanner
     */
    private Scanner $scanner;

    /**
     * Scheduled scan manager
     *
     * @var ScheduledScanManager
     */
    private ScheduledScanManager $scheduled_manager;

    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Dashboard widget
     *
     * @var AccessibilityDashboardWidget
     */
    private AccessibilityDashboardWidget $dashboard_widget;

    /**
     * Constructor
     */
    public function __construct() {
        $this->scanner = new Scanner();
        $this->settings = new SettingsRepository();
        $this->scheduled_manager = new ScheduledScanManager($this->scanner, $this->settings);
        $this->dashboard_widget = new AccessibilityDashboardWidget($this->scheduled_manager);
    }

    /**
     * Initialize the module
     *
     * @return void
     */
    public function init(): void {
        // Register hooks immediately instead of waiting for complyflow_init
        $this->register_hooks();

        // Initialize scheduled scans
        $this->scheduled_manager->init();

        // Initialize dashboard widget
        $this->dashboard_widget->init();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    public function register_hooks(): void {
        // Add admin menu
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);
        
        // Register settings
        add_action('admin_init', [$this, 'register_settings']);
        
        // Register AJAX handlers
        add_action('wp_ajax_complyflow_run_accessibility_scan', [$this, 'ajax_run_scan']);
        add_action('wp_ajax_complyflow_get_scan_results', [$this, 'ajax_get_scan_results']);
        add_action('wp_ajax_complyflow_delete_scan', [$this, 'ajax_delete_scan']);
        add_action('wp_ajax_complyflow_export_scan_csv', [$this, 'ajax_export_scan_csv']);
        add_action('wp_ajax_complyflow_update_scheduled_scans', [$this, 'ajax_update_scheduled_scans']);
    }

    /**
     * Add admin menu item
     *
     * @return void
     */
    public function add_admin_menu(): void {
        add_submenu_page(
            'complyflow',
            __('Accessibility Scanner', 'complyflow'),
            __('Accessibility', 'complyflow'),
            'manage_options',
            'complyflow-accessibility',
            [$this, 'render_page']
        );

        // Hidden submenu for scan results
        add_submenu_page(
            null, // Hide from menu
            __('Scan Results', 'complyflow'),
            __('Scan Results', 'complyflow'),
            'manage_options',
            'complyflow-accessibility-results',
            [$this, 'render_results_page']
        );

        // Hidden submenu for scheduled scans settings
        add_submenu_page(
            null, // Hide from menu
            __('Scheduled Scans', 'complyflow'),
            __('Scheduled Scans', 'complyflow'),
            'manage_options',
            'complyflow-accessibility-schedule',
            [$this, 'render_schedule_page']
        );
    }

    /**
     * Register settings
     *
     * @return void
     */
    public function register_settings(): void {
        register_setting('complyflow_accessibility_schedule', 'accessibility_scheduled_scans_enabled');
        register_setting('complyflow_accessibility_schedule', 'accessibility_scheduled_scans_frequency');
        register_setting('complyflow_accessibility_schedule', 'accessibility_scheduled_scans_urls');
        register_setting('complyflow_accessibility_schedule', 'accessibility_notifications_enabled');
        register_setting('complyflow_accessibility_schedule', 'accessibility_notifications_threshold');
        register_setting('complyflow_accessibility_schedule', 'accessibility_notifications_recipients');
    }

    /**
     * Render admin page
     *
     * @return void
     */
    public function render_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        include COMPLYFLOW_PATH . 'includes/Admin/views/accessibility-scanner.php';
    }

    /**
     * Render scan results page
     *
     * @return void
     */
    public function render_results_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        include COMPLYFLOW_PATH . 'includes/Admin/views/accessibility-results.php';
    }

    /**
     * Render schedule settings page
     *
     * @return void
     */
    public function render_schedule_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $scheduled_manager = $this->scheduled_manager;
        include COMPLYFLOW_PATH . 'includes/Admin/views/accessibility-schedule.php';
    }

    /**
     * AJAX handler for scan
     *
     * @return void
     */
    public function ajax_run_scan(): void {
        error_log('ComplyFlow: ajax_run_scan called');
        error_log('ComplyFlow: POST data: ' . print_r($_POST, true));
        
        try {
            check_ajax_referer('complyflow_admin_nonce', 'nonce');
        } catch (\Exception $e) {
            error_log('ComplyFlow: Nonce verification failed: ' . $e->getMessage());
            wp_send_json_error(['message' => __('Security check failed.', 'complyflow')]);
        }

        if (!current_user_can('manage_options')) {
            error_log('ComplyFlow: Permission check failed');
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
        error_log('ComplyFlow: URL to scan: ' . $url);

        if (empty($url)) {
            error_log('ComplyFlow: URL is empty');
            wp_send_json_error(['message' => __('URL is required.', 'complyflow')]);
        }

        // Run the scan
        error_log('ComplyFlow: Starting scanner->scan_url()');
        try {
            $result = $this->scanner->scan_url($url);
            error_log('ComplyFlow: Scanner result: ' . print_r($result, true));
        } catch (\Exception $e) {
            error_log('ComplyFlow: Scanner exception: ' . $e->getMessage());
            wp_send_json_error(['message' => __('Scan error: ', 'complyflow') . $e->getMessage()]);
        }

        if ($result['success']) {
            // Flatten summary for modal compatibility
            $summary = $result['summary'];
            $flattened_summary = [
                'total_issues' => $summary['total_issues'] ?? 0,
                'critical' => $summary['by_severity']['critical'] ?? 0,
                'serious' => $summary['by_severity']['serious'] ?? 0,
                'moderate' => $summary['by_severity']['moderate'] ?? 0,
                'minor' => $summary['by_severity']['minor'] ?? 0,
                'by_severity' => $summary['by_severity'] ?? [],
                'by_wcag' => $summary['by_wcag'] ?? [],
                'by_category' => $summary['by_category'] ?? [],
            ];
            
            wp_send_json_success([
                'message' => __('Scan completed successfully.', 'complyflow'),
                'scan_id' => $result['scan_id'],
                'score' => $result['score'],
                'summary' => $flattened_summary,
            ]);
        } else {
            wp_send_json_error([
                'message' => $result['error'] ?? __('Scan failed.', 'complyflow'),
            ]);
        }
    }

    /**
     * AJAX handler for getting scan results
     *
     * @return void
     */
    public function ajax_get_scan_results(): void {
        check_ajax_referer('complyflow_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        $scan_id = isset($_POST['scan_id']) ? absint($_POST['scan_id']) : 0;

        if (!$scan_id) {
            wp_send_json_error(['message' => __('Scan ID is required.', 'complyflow')]);
        }

        $scan_data = $this->scanner->export_scan($scan_id);

        if ($scan_data) {
            wp_send_json_success($scan_data);
        } else {
            wp_send_json_error(['message' => __('Scan not found.', 'complyflow')]);
        }
    }

    /**
     * AJAX handler for deleting scan
     *
     * @return void
     */
    public function ajax_delete_scan(): void {
        check_ajax_referer('complyflow_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        $scan_id = isset($_POST['scan_id']) ? absint($_POST['scan_id']) : 0;

        if (!$scan_id) {
            wp_send_json_error(['message' => __('Scan ID is required.', 'complyflow')]);
        }

        $result = $this->scanner->delete_scan($scan_id);

        if ($result) {
            wp_send_json_success(['message' => __('Scan deleted successfully.', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Failed to delete scan.', 'complyflow')]);
        }
    }

    /**
     * AJAX handler for exporting scan results as CSV
     *
     * @return void
     */
    public function ajax_export_scan_csv(): void {
        check_ajax_referer('complyflow_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'complyflow'));
        }

        $scan_id = isset($_GET['scan_id']) ? absint($_GET['scan_id']) : 0;

        if (!$scan_id) {
            wp_die(__('Invalid scan ID.', 'complyflow'));
        }

        try {
            $scan_data = $this->scanner->export_scan($scan_id);

            if (!$scan_data) {
                wp_die(__('Scan not found.', 'complyflow'));
            }

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="accessibility-scan-' . $scan_id . '-' . date('Y-m-d') . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Open output stream
            $output = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 support
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write headers
            fputcsv($output, [
                __('Category', 'complyflow'),
                __('Severity', 'complyflow'),
                __('WCAG Criterion', 'complyflow'),
                __('Message', 'complyflow'),
                __('Element', 'complyflow'),
                __('Selector', 'complyflow'),
                __('How to Fix', 'complyflow'),
                __('Learn More', 'complyflow')
            ]);

            // Write scan info
            fputcsv($output, [
                __('Scan Information', 'complyflow'),
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ]);
            fputcsv($output, [
                __('URL', 'complyflow'),
                $scan_data['url'],
                '',
                '',
                '',
                '',
                '',
                ''
            ]);
            fputcsv($output, [
                __('Score', 'complyflow'),
                $scan_data['score'] . '/100',
                '',
                '',
                '',
                '',
                '',
                ''
            ]);
            fputcsv($output, [
                __('Scanned', 'complyflow'),
                $scan_data['scanned_at'],
                '',
                '',
                '',
                '',
                '',
                ''
            ]);
            fputcsv($output, ['', '', '', '', '', '', '', '']); // Empty row

            // Write issues
            if (!empty($scan_data['issues'])) {
                foreach ($scan_data['issues'] as $issue) {
                    $category_labels = [
                        'images' => __('Images', 'complyflow'),
                        'structure' => __('Structure', 'complyflow'),
                        'forms' => __('Forms', 'complyflow'),
                        'links' => __('Links', 'complyflow'),
                        'aria' => __('ARIA', 'complyflow'),
                        'keyboard' => __('Keyboard', 'complyflow'),
                        'multimedia' => __('Multimedia', 'complyflow'),
                        'tables' => __('Tables', 'complyflow'),
                    ];

                    fputcsv($output, [
                        $category_labels[$issue['category']] ?? $issue['category'],
                        ucfirst($issue['severity']),
                        $issue['wcag_criterion'],
                        $issue['message'],
                        strip_tags($issue['element']),
                        $issue['selector'],
                        $issue['fix'],
                        'https://www.w3.org/WAI/WCAG22/Understanding/' . strtolower(str_replace('.', '', $issue['wcag_criterion']))
                    ]);
                }
            }

            fclose($output);
            exit;
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * AJAX handler for updating scheduled scans
     *
     * @return void
     */
    public function ajax_update_scheduled_scans(): void {
        check_ajax_referer('complyflow_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        // Get posted data
        $enabled = isset($_POST['enabled']) && sanitize_text_field($_POST['enabled']) === '1';
        $frequency = sanitize_text_field($_POST['frequency'] ?? 'daily');
        $urls = isset($_POST['urls']) ? array_map('esc_url_raw', $_POST['urls']) : [];
        $notifications_enabled = isset($_POST['notifications_enabled']) && sanitize_text_field($_POST['notifications_enabled']) === '1';
        $notifications_threshold = sanitize_text_field($_POST['notifications_threshold'] ?? 'critical');
        $notifications_recipients = isset($_POST['notifications_recipients']) ? array_map('sanitize_email', $_POST['notifications_recipients']) : [];

        // Save settings
        $this->settings->set('accessibility_scheduled_scans_enabled', $enabled);
        $this->settings->set('accessibility_scheduled_scans_frequency', $frequency);
        $this->settings->set('accessibility_scheduled_scans_urls', $urls);
        $this->settings->set('accessibility_notifications_enabled', $notifications_enabled);
        $this->settings->set('accessibility_notifications_threshold', $notifications_threshold);
        $this->settings->set('accessibility_notifications_recipients', $notifications_recipients);

        // Update cron schedule
        $scheduled = $this->scheduled_manager->schedule_scans();

        if ($scheduled || !$enabled) {
            wp_send_json_success([
                'message' => __('Scheduled scan settings updated successfully.', 'complyflow'),
                'next_run' => $this->scheduled_manager->get_next_scheduled_time(),
            ]);
        } else {
            wp_send_json_error(['message' => __('Failed to schedule scans.', 'complyflow')]);
        }
    }

    /**
     * Get scanner instance
     *
     * @return Scanner
     */
    public function get_scanner(): Scanner {
        return $this->scanner;
    }

    /**
     * Get scheduled scan manager instance
     *
     * @return ScheduledScanManager
     */
    public function get_scheduled_manager(): ScheduledScanManager {
        return $this->scheduled_manager;
    }
}
