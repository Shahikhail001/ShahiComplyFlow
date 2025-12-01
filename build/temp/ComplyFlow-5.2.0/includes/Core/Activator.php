<?php
/**
 * Plugin Activator
 *
 * Fired during plugin activation.
 *
 * @package ComplyFlow\Core
 * @since 1.0.0
 */

namespace ComplyFlow\Core;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Activator Class
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 1.0.0
 */
class Activator {
    /**
     * Activate the plugin
     *
     * Creates database tables, sets default options, and performs initial setup.
     *
     * @return void
     */
    public static function activate(): void {
        // Check minimum requirements
        self::check_requirements();

        // Create database tables
        self::create_tables();

        // Set default options
        self::set_default_options();

        // Create necessary directories
        self::create_directories();

        // Add custom capabilities
        self::add_capabilities();

        // Schedule cron jobs
        self::schedule_cron_jobs();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set activation flag for welcome notice
        set_transient('complyflow_activated', true, 60);

        /**
         * Fires after ComplyFlow activation
         *
         * @since 1.0.0
         */
        do_action('complyflow_activated');
    }

    /**
     * Check system requirements
     *
     * @return void
     */
    private static function check_requirements(): void {
        global $wp_version;

        $errors = [];

        // PHP version check
        if (version_compare(PHP_VERSION, COMPLYFLOW_MIN_PHP_VERSION, '<')) {
            $errors[] = sprintf(
                'PHP version %s or higher is required. You are running version %s.',
                COMPLYFLOW_MIN_PHP_VERSION,
                PHP_VERSION
            );
        }

        // WordPress version check
        if (version_compare($wp_version, COMPLYFLOW_MIN_WP_VERSION, '<')) {
            $errors[] = sprintf(
                'WordPress version %s or higher is required. You are running version %s.',
                COMPLYFLOW_MIN_WP_VERSION,
                $wp_version
            );
        }

        // Check required PHP extensions
        $required_extensions = ['json', 'mbstring', 'curl'];
        foreach ($required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $errors[] = sprintf('Required PHP extension "%s" is not loaded.', $extension);
            }
        }

        if (!empty($errors)) {
            deactivate_plugins(COMPLYFLOW_BASENAME);
            wp_die(
                '<h1>Plugin Activation Error</h1><p>' . implode('</p><p>', $errors) . '</p>',
                'Plugin Activation Error',
                ['back_link' => true]
            );
        }
    }

    /**
     * Create custom database tables
     *
     * @return void
     */
    private static function create_tables(): void {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $prefix = $wpdb->prefix;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Consent logs table
        $consent_table = "CREATE TABLE IF NOT EXISTS {$prefix}complyflow_consent_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            session_id VARCHAR(64) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            consent_data TEXT,
            consent_categories TEXT NOT NULL,
            consent_given BOOLEAN DEFAULT TRUE,
            user_agent TEXT,
            geo_country VARCHAR(2),
            created_at DATETIME NOT NULL,
            INDEX idx_session (session_id),
            INDEX idx_user (user_id),
            INDEX idx_created (created_at)
        ) $charset_collate;";

        // DSR requests table
        $dsr_table = "CREATE TABLE IF NOT EXISTS {$prefix}complyflow_dsr (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            request_type ENUM('access', 'erasure', 'rectify', 'portability') NOT NULL,
            email VARCHAR(255) NOT NULL,
            verification_code VARCHAR(6),
            verified_at DATETIME NULL,
            status ENUM('pending', 'verified', 'processing', 'completed', 'rejected') DEFAULT 'pending',
            message TEXT,
            response TEXT NULL,
            created_at DATETIME NOT NULL,
            completed_at DATETIME NULL,
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) $charset_collate;";

        // Scan results table
        $scan_table = "CREATE TABLE IF NOT EXISTS {$prefix}complyflow_scan_results (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            post_id BIGINT UNSIGNED NULL,
            url VARCHAR(500) NOT NULL,
            scan_type VARCHAR(50) NOT NULL,
            total_issues INT UNSIGNED DEFAULT 0,
            critical_issues INT UNSIGNED DEFAULT 0,
            warning_issues INT UNSIGNED DEFAULT 0,
            notice_issues INT UNSIGNED DEFAULT 0,
            results LONGTEXT,
            created_at DATETIME NOT NULL,
            INDEX idx_post (post_id),
            INDEX idx_created (created_at)
        ) $charset_collate;";

        // Tracker inventory table
        $tracker_table = "CREATE TABLE IF NOT EXISTS {$prefix}complyflow_tracker_inventory (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            script_url VARCHAR(500) NOT NULL,
            script_name VARCHAR(255),
            category VARCHAR(50) NOT NULL,
            consent_required BOOLEAN DEFAULT TRUE,
            status ENUM('allowed', 'blocked') DEFAULT 'blocked',
            detected_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY idx_url (script_url),
            INDEX idx_category (category)
        ) $charset_collate;";

        // Compliance history table
        $history_table = "CREATE TABLE IF NOT EXISTS {$prefix}complyflow_compliance_history (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            compliance_score INT UNSIGNED NOT NULL,
            module_scores TEXT NOT NULL,
            accessibility_issues INT UNSIGNED DEFAULT 0,
            dsr_pending_count INT UNSIGNED DEFAULT 0,
            consent_acceptance_rate DECIMAL(5,2) DEFAULT 0.00,
            cookie_count INT UNSIGNED DEFAULT 0,
            recorded_at DATETIME NOT NULL,
            INDEX idx_recorded (recorded_at)
        ) $charset_collate;";

        // Execute table creation
        dbDelta($consent_table);
        dbDelta($dsr_table);
        dbDelta($scan_table);
        dbDelta($tracker_table);
        dbDelta($history_table);

        // Store database version
        update_option('complyflow_db_version', '1.1.0');
    }

    /**
     * Set default plugin options
     *
     * @return void
     */
    private static function set_default_options(): void {
        $default_options = [
            'complyflow_version' => COMPLYFLOW_VERSION,
            'complyflow_installed_at' => current_time('mysql'),
            
            // General settings
            'complyflow_enabled' => true,
            
            // Consent manager settings
            'complyflow_consent_enabled' => true,
            'complyflow_consent_position' => 'bottom',
            'complyflow_consent_cookie_lifetime' => 365,
            
            // Accessibility settings
            'complyflow_accessibility_enabled' => true,
            'complyflow_wcag_level' => 'AA',
            
            // DSR settings
            'complyflow_dsr_enabled' => true,
            'complyflow_dsr_response_time' => 30, // days
            
            // Privacy settings
            'complyflow_ip_anonymization' => true,
            'complyflow_data_retention' => 365, // days
            
            // Cloud sync settings
            'complyflow_cloud_sync' => false,
        ];

        foreach ($default_options as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
        
        // Set default compliance history schedule in settings array
        $settings = get_option('complyflow_settings', []);
        if (!isset($settings['compliance_history_schedule'])) {
            $settings['compliance_history_schedule'] = 'daily';
            update_option('complyflow_settings', $settings);
        }
        
        // Take initial compliance snapshot
        self::take_initial_snapshot();
    }

    /**
     * Create necessary directories
     *
     * @return void
     */
    private static function create_directories(): void {
        $upload_dir = wp_upload_dir();
        $complyflow_dir = $upload_dir['basedir'] . '/complyflow';

        $directories = [
            $complyflow_dir,
            $complyflow_dir . '/exports',
            $complyflow_dir . '/reports',
            $complyflow_dir . '/logs',
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
                
                // Create .htaccess to protect directory
                $htaccess_content = "Order Deny,Allow\nDeny from all\n";
                file_put_contents($dir . '/.htaccess', $htaccess_content);
                
                // Create index.php to prevent directory listing
                file_put_contents($dir . '/index.php', '<?php // Silence is golden');
            }
        }
    }

    /**
     * Add custom capabilities to roles
     *
     * @return void
     */
    private static function add_capabilities(): void {
        $capabilities = [
            'manage_complyflow',
            'view_dsr_requests',
            'process_dsr_requests',
        ];

        // Add to administrator role
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($capabilities as $cap) {
                $admin_role->add_cap($cap);
            }
        }

        // Optionally add limited capabilities to editor
        $editor_role = get_role('editor');
        if ($editor_role) {
            $editor_role->add_cap('view_dsr_requests');
        }
    }

    /**
     * Schedule cron jobs
     *
     * @return void
     */
    private static function schedule_cron_jobs(): void {
        // Daily scan schedule (if enabled)
        if (!wp_next_scheduled('complyflow_scan_scheduled')) {
            wp_schedule_event(time(), 'daily', 'complyflow_scan_scheduled');
        }

        // DSR reminder check (daily)
        if (!wp_next_scheduled('complyflow_dsr_reminder')) {
            wp_schedule_event(time(), 'daily', 'complyflow_dsr_reminder');
        }

        // Consent log cleanup (weekly)
        if (!wp_next_scheduled('complyflow_consent_cleanup')) {
            wp_schedule_event(time(), 'weekly', 'complyflow_consent_cleanup');
        }

        // Legal update check (monthly, if cloud sync enabled)
        if (!wp_next_scheduled('complyflow_legal_update_check')) {
            wp_schedule_event(time(), 'monthly', 'complyflow_legal_update_check');
        }

        // Compliance history snapshot (daily by default, user can change in settings)
        if (!wp_next_scheduled('complyflow_compliance_snapshot')) {
            wp_schedule_event(time(), 'daily', 'complyflow_compliance_snapshot');
        }
    }

    /**
     * Take initial compliance snapshot on activation
     *
     * @return void
     */
    private static function take_initial_snapshot(): void {
        // Only take snapshot if ComplianceHistoryScheduler class exists
        if (!class_exists('ComplyFlow\Core\ComplianceHistoryScheduler')) {
            return;
        }

        try {
            $scheduler = new \ComplyFlow\Core\ComplianceHistoryScheduler();
            $scheduler->force_snapshot();
        } catch (\Exception $e) {
            error_log('ComplyFlow: Failed to take initial compliance snapshot: ' . $e->getMessage());
        }
    }
}
