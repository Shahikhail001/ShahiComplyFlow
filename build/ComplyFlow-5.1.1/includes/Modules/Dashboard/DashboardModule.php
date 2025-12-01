<?php
/**
 * Dashboard Module
 *
 * Overview dashboard with compliance metrics and quick actions.
 *
 * @package ComplyFlow\Modules\Dashboard
 * @since   3.5.0
 */

namespace ComplyFlow\Modules\Dashboard;

use ComplyFlow\Core\Repositories\SettingsRepository;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Module Class
 *
 * @since 3.5.0
 */
class DashboardModule {
    
    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Dashboard widgets helper
     *
     * @var DashboardWidgets
     */
    private DashboardWidgets $widgets;

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = new SettingsRepository();
        $this->widgets = new DashboardWidgets();
    }

    /**
     * Initialize the module
     *
     * @return void
     */
    public function init(): void {
        error_log('ComplyFlow: DashboardModule init() called');
        // Register hooks immediately instead of waiting for complyflow_init
        $this->register_hooks();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    public function register_hooks(): void {
        error_log('ComplyFlow: DashboardModule register_hooks() called');
        add_action('admin_menu', [$this, 'add_admin_menu'], 5); // Priority 5 to be first
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        // Quick action AJAX endpoints
        add_action('wp_ajax_complyflow_dashboard_export_dsr', [$this, 'ajax_export_dsr']);
        add_action('wp_ajax_complyflow_dashboard_refresh_stats', [$this, 'ajax_refresh_stats']);
        error_log('ComplyFlow: DashboardModule registered wp_ajax_complyflow_dashboard_export_dsr and refresh_stats actions');
    }

    /**
     * Add admin menu item
     *
     * @return void
     */
    public function add_admin_menu(): void {
        // Dashboard submenu removed (single top-level page already renders dashboard).
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     * @return void
     */
    public function enqueue_admin_assets(string $hook): void {
        // Adjust to top-level page hook (menu slug 'complyflow').
        if ($hook !== 'toplevel_page_complyflow') {
            return;
        }

        // Enqueue styles
        // Correct path: CSS emitted at assets/dist/dashboard-admin.css by Vite
        wp_enqueue_style(
            'complyflow-dashboard-admin',
            COMPLYFLOW_URL . 'assets/dist/dashboard-admin.css',
            [],
            COMPLYFLOW_VERSION
        );

        // Enqueue chart/dark-mode JS (after adding entry to build config)
        wp_enqueue_script(
            'complyflow-dashboard-admin-js',
            COMPLYFLOW_URL . 'assets/dist/dashboard-admin-js.js',
            ['jquery','complyflow-admin'],
            COMPLYFLOW_VERSION . '.' . time(), // Force cache bust
            true
        );

        // Fetch stats for charts
        $widgets = $this->widgets;
        $compliance = $widgets->get_compliance_score();
        $dsr = $widgets->get_dsr_statistics();
        $consent = $widgets->get_consent_statistics();
        $accessibility = $widgets->get_accessibility_summary();
        $cookies = $widgets->get_cookie_summary();
        $trends = $widgets->get_compliance_trends();
        $activities = $widgets->get_recent_activities();
        $risk = $widgets->get_risk_assessment();
        $processing = $widgets->get_data_processing_summary();
        $module_health = $widgets->get_module_health();

        // Localize script with chart data & i18n
        wp_localize_script('complyflow-dashboard-admin-js', 'complyflowDashboard', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('complyflow_dashboard_nonce'),
            'adminNonce' => wp_create_nonce('complyflow_admin_nonce'),
            'cookieNonce' => wp_create_nonce('complyflow_cookie_nonce'),
            'siteUrl' => home_url(),
            'stats' => [
                'compliance' => $compliance,
                'dsr' => $dsr,
                'consent' => $consent,
                'accessibility' => $accessibility,
                'cookies' => $cookies,
                'trends' => $trends,
                'activities' => $activities,
                'risk' => $risk,
                'processing' => $processing,
                'module_health' => $module_health,
            ],
            'i18n' => [
                'scanning' => __('Running full scan...', 'complyflow'),
                'scanComplete' => __('Scan complete!', 'complyflow'),
                'fullScanStarted' => __('Full scan started...', 'complyflow'),
                'fullScanDone' => __('Full scan completed.', 'complyflow'),
                'error' => __('An error occurred. Please try again.', 'complyflow'),
                'refreshing' => __('Refreshing data...', 'complyflow'),
                'exportingDSR' => __('Exporting DSR data...', 'complyflow'),
                'updatingPolicies' => __('Updating policies...', 'complyflow'),
                'darkMode' => __('Dark Mode', 'complyflow'),
                'lightMode' => __('Light Mode', 'complyflow'),
                'accessibilityScan' => __('Accessibility scan started...', 'complyflow'),
                'accessibilityScanDone' => __('Accessibility scan completed.', 'complyflow'),
                'cookieScan' => __('Cookie scan started...', 'complyflow'),
                'cookieScanDone' => __('Cookie scan completed.', 'complyflow'),
                'dsrExportDone' => __('DSR export generated.', 'complyflow'),
            ],
        ]);
    }

    /**
     * AJAX: Refresh dashboard stats
     */
    public function ajax_refresh_stats(): void {
        check_ajax_referer('complyflow_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        $widgets = $this->widgets;
        $compliance = $widgets->get_compliance_score();
        $dsr = $widgets->get_dsr_statistics();
        $consent = $widgets->get_consent_statistics();
        $accessibility = $widgets->get_accessibility_summary();
        $cookies = $widgets->get_cookie_summary();
        $trends = $widgets->get_compliance_trends();
        $activities = $widgets->get_recent_activities();
        $risk = $widgets->get_risk_assessment();
        $processing = $widgets->get_data_processing_summary();
        $module_health = $widgets->get_module_health();

        wp_send_json_success([
            'stats' => [
                'compliance' => $compliance,
                'dsr' => $dsr,
                'consent' => $consent,
                'accessibility' => $accessibility,
                'cookies' => $cookies,
                'trends' => $trends,
                'activities' => $activities,
                'risk' => $risk,
                'processing' => $processing,
                'module_health' => $module_health,
            ]
        ]);
    }

    /**
     * AJAX: Export DSR data (basic CSV)
     */
    public function ajax_export_dsr(): void {
        check_ajax_referer('complyflow_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }
        global $wpdb;
        $table = $wpdb->prefix . 'complyflow_dsr_requests';
        $rows = $wpdb->get_results("SELECT id,request_type,email,status,created_at FROM {$table} ORDER BY created_at DESC LIMIT 50", ARRAY_A);
        if (!is_array($rows)) {
            wp_send_json_error(['message' => __('Failed to retrieve DSR data.', 'complyflow')]);
        }
        // Build CSV
        $csv = "id,request_type,email,status,created_at\n";
        foreach ($rows as $r) {
            $csv .= sprintf("%d,%s,%s,%s,%s\n", (int)$r['id'], $r['request_type'], $r['email'], $r['status'], $r['created_at']);
        }
        wp_send_json_success([
            'count' => count($rows),
            'csv' => $csv,
            'filename' => 'dsr-export-' . gmdate('Ymd-His') . '.csv'
        ]);
    }

    /**
     * Render dashboard page
     *
     * @return void
     */
    public function render_dashboard_page(): void {
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Sorry, you are not allowed to access this page.', 'complyflow'));
        }

        // Get widget data
        $compliance_score = $this->widgets->get_compliance_score();
        $dsr_stats = $this->widgets->get_dsr_statistics();
        $consent_stats = $this->widgets->get_consent_statistics();
        $accessibility_summary = $this->widgets->get_accessibility_summary();
        $cookie_summary = $this->widgets->get_cookie_summary();
        $trends = $this->widgets->get_compliance_trends();
        $activities = $this->widgets->get_recent_activities();
        $risk_assessment = $this->widgets->get_risk_assessment();
        $data_processing = $this->widgets->get_data_processing_summary();
        $module_health = $this->widgets->get_module_health();

        // Include view
        include COMPLYFLOW_PATH . 'includes/Admin/views/dashboard.php';
    }
}
