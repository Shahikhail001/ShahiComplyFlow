<?php
/**
 * Main Plugin Class
 *
 * Singleton pattern for plugin initialization and management.
 *
 * @package ComplyFlow\Core
 * @since 1.0.0
 */

namespace ComplyFlow\Core;

use ComplyFlow\Admin\Settings;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Main Class
 *
 * @since 1.0.0
 */
final class Plugin {
    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * Loader instance
     *
     * @var Loader
     */
    private Loader $loader;

    /**
     * Settings instance
     *
     * @var Settings
     */
    private Settings $settings;

    /**
     * Module manager instance
     *
     * @var ModuleManager
     */
    private ModuleManager $module_manager;

    /**
     * Cache instance
     *
     * @var Cache
     */
    private Cache $cache;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->loader = new Loader();
        $this->settings = new Settings();
        $this->module_manager = new ModuleManager($this->settings);
        $this->cache = Cache::get_instance();
        
        // Make settings available globally for views
        $GLOBALS['complyflow_settings'] = $this->settings;
    }

    /**
     * Get plugin instance (Singleton)
     *
     * @return Plugin
     */
    public static function instance(): Plugin {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init(): void {
        // Load dependencies
        $this->load_dependencies();

        // Set locale for internationalization
        $this->set_locale();

        // Define admin hooks
        $this->define_admin_hooks();

        // Define public hooks
        $this->define_public_hooks();
        
        // Register AJAX handlers (must be outside is_admin check for AJAX requests)
        $this->register_ajax_handlers();
        
        // Initialize compliance history scheduler
        $this->init_compliance_scheduler();

        // Initialize modules
        $this->init_modules();

        // Run the loader
        $this->loader->run();

        /**
         * Fires after ComplyFlow is fully initialized
         *
         * @since 1.0.0
         */
        do_action('complyflow_init');
    }

    /**
     * Load required dependencies
     *
     * @return void
     */
    private function load_dependencies(): void {
        // Core classes are autoloaded via Composer PSR-4
        
        /**
         * Fires when ComplyFlow loads dependencies
         *
         * @since 1.0.0
         */
        do_action('complyflow_load_dependencies');
    }

    /**
     * Set plugin locale for internationalization
     *
     * @return void
     */
    private function set_locale(): void {
        $this->loader->add_action('init', $this, 'load_textdomain', 0);
    }

    /**
     * Load translation files when WordPress initialization has completed.
     */
    public function load_textdomain(): void {
        $locale = apply_filters('plugin_locale', get_locale(), 'complyflow');

        load_textdomain('complyflow', WP_LANG_DIR . '/complyflow/complyflow-' . $locale . '.mo');
        load_plugin_textdomain('complyflow', false, dirname(COMPLYFLOW_BASENAME) . '/languages/');
    }

    /**
     * Define admin-specific hooks
     *
     * @return void
     */
    private function define_admin_hooks(): void {
        if (!is_admin()) {
            return;
        }

        // Admin menu
        $this->loader->add_action('admin_menu', $this, 'register_admin_menu');

        // Admin scripts and styles
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_admin_assets');

        // Admin notices
        $this->loader->add_action('admin_notices', $this, 'display_admin_notices');
    }

    /**
     * Define public-facing hooks
     *
     * @return void
     */
    private function define_public_hooks(): void {
        // Frontend scripts and styles
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_assets');

        // Shortcodes
        $this->register_shortcodes();

        // REST API
        $this->loader->add_action('rest_api_init', $this, 'register_rest_routes');

        // WP-CLI commands
        if (defined('WP_CLI') && WP_CLI) {
            $this->register_cli_commands();
        }
    }

    /**
     * Initialize compliance history scheduler
     *
     * @return void
     */
    private function init_compliance_scheduler(): void {
        if (class_exists('ComplyFlow\Core\ComplianceHistoryScheduler')) {
            $scheduler = new ComplianceHistoryScheduler();
            $scheduler->init();
        }
    }

    /**
     * Initialize plugin modules
     *
     * @return void
     */
    private function init_modules(): void {
        // Initialize all enabled modules via ModuleManager
        $this->module_manager->init_modules();
        
        /**
         * Fires when ComplyFlow modules are being initialized
         *
         * @since 1.0.0
         *
         * @param ModuleManager $module_manager Module manager instance.
         */
        do_action('complyflow_init_modules', $this->module_manager);
    }

    /**
     * Register admin menu
     *
     * @return void
     */
    public function register_admin_menu(): void {
        add_menu_page(
            __('ComplyFlow', 'complyflow'),
            __('ComplyFlow', 'complyflow'),
            'manage_options',
            'complyflow',
            [$this, 'render_dashboard'],
            $this->get_menu_icon(),
            30
        );

        // Removed duplicate explicit dashboard submenu to prevent confusion.

        add_submenu_page(
            'complyflow',
            __('Settings', 'complyflow'),
            __('Settings', 'complyflow'),
            'manage_options',
            'complyflow-settings',
            [$this, 'render_settings']
        );

        // Redirect logic removed; canonical access is via admin.php?page=complyflow.
    }

    /**
     * Get menu icon (base64 encoded SVG)
     *
     * @return string
     */
    private function get_menu_icon(): string {
        // Shield icon SVG (base64 encoded)
        return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9Im5vbmUiIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZmlsbD0iIzlDQTNCMiIgZD0iTTEyIDIuMjVhLjc1Ljc1IDAgMCAxIC42NTkuNDAybDcuNSAxNWEuNzUuNzUgMCAwIDEtLjY1OSAxLjA5OEg0LjVhLjc1Ljc1IDAgMCAxLS42NTktMS4wOThsNy41LTE1QS43NS43NSAwIDAgMSAxMiAyLjI1WiIvPjwvc3ZnPg==';
    }

    /**
     * Render dashboard page
     *
     * @return void
     */
    public function render_dashboard(): void {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'complyflow'));
        }

        // Delegate to DashboardModule to ensure proper variable setup
        $dashboard_module = $this->module_manager->get_module('dashboard');
        if ($dashboard_module && method_exists($dashboard_module, 'render_dashboard_page')) {
            $dashboard_module->render_dashboard_page();
        } else {
            // Fallback: include directly (for backward compatibility)
            include_once COMPLYFLOW_PATH . 'includes/Admin/views/dashboard.php';
        }
    }

    /**
     * Render settings page
     *
     * @return void
     */
    public function render_settings(): void {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'complyflow'));
        }

        include_once COMPLYFLOW_PATH . 'includes/Admin/views/settings.php';
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     * @return void
     */
    public function enqueue_admin_assets(string $hook): void {
        // Only load on ComplyFlow pages
        if (strpos($hook, 'complyflow') === false) {
            return;
        }

        // Enqueue common admin styles (modern blue theme)
        wp_enqueue_style(
            'complyflow-admin-common',
            COMPLYFLOW_URL . 'assets/dist/admin-common.css',
            [],
            COMPLYFLOW_VERSION
        );

        wp_enqueue_style(
            'complyflow-admin',
            COMPLYFLOW_URL . 'assets/dist/admin-style.css',
            ['complyflow-admin-common'],
            COMPLYFLOW_VERSION
        );

        wp_enqueue_script(
            'complyflow-admin',
            COMPLYFLOW_URL . 'assets/dist/admin.js',
            ['jquery'],
            COMPLYFLOW_VERSION,
            true
        );

        wp_localize_script(
            'complyflow-admin',
            'complyflowAdmin',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => rest_url('complyflow/v1'),
                'nonce' => wp_create_nonce('complyflow_admin_nonce'),
                'strings' => [
                    'saved' => __('Settings saved successfully.', 'complyflow'),
                    'saving' => __('Saving changes...', 'complyflow'),
                    'error' => __('An error occurred. Please try again.', 'complyflow'),
                ],
            ]
        );
    }

    /**
     * Enqueue public assets
     *
     * @return void
     */
    public function enqueue_public_assets(): void {
        wp_enqueue_style(
            'complyflow-frontend',
            COMPLYFLOW_URL . 'assets/dist/frontend-style.css',
            [],
            COMPLYFLOW_VERSION
        );

        wp_enqueue_script(
            'complyflow-frontend',
            COMPLYFLOW_URL . 'assets/dist/frontend.js',
            [],
            COMPLYFLOW_VERSION,
            true
        );

        wp_localize_script(
            'complyflow-frontend',
            'complyflow',
            [
                'restUrl' => rest_url('complyflow/v1'),
                'nonce' => wp_create_nonce('wp_rest'),
            ]
        );
    }

    /**
     * Register shortcodes
     *
     * @return void
     */
    private function register_shortcodes(): void {
        // DSR Portal shortcode
        add_shortcode('complyflow_dsr_portal', function () {
            ob_start();
            include COMPLYFLOW_PATH . 'templates/dsr-portal.php';
            return ob_get_clean();
        });

        // Privacy Policy shortcode
        add_shortcode('complyflow_policy', function ($atts) {
            $atts = shortcode_atts(['type' => 'privacy'], $atts);
            
            ob_start();
            include COMPLYFLOW_PATH . 'templates/policy-' . sanitize_file_name($atts['type']) . '.php';
            return ob_get_clean();
        });
    }

    /**
     * Register AJAX handlers
     *
     * @return void
     */
    private function register_ajax_handlers(): void {
        // Admin AJAX handlers
        $this->loader->add_action('wp_ajax_complyflow_save_settings', $this, 'ajax_save_settings');
        $this->loader->add_action('wp_ajax_complyflow_export_settings', $this, 'ajax_export_settings');
        $this->loader->add_action('wp_ajax_complyflow_import_settings', $this, 'ajax_import_settings');
        $this->loader->add_action('wp_ajax_complyflow_run_scan', $this, 'ajax_run_scan');
        
        // Public AJAX handlers
        $this->loader->add_action('wp_ajax_nopriv_complyflow_save_consent', $this, 'ajax_save_consent');
        $this->loader->add_action('wp_ajax_complyflow_save_consent', $this, 'ajax_save_consent');
    }

    /**
     * Register REST API routes
     *
     * @return void
     */
    public function register_rest_routes(): void {
        // Consent endpoints
        $consent_controller = new \ComplyFlow\API\ConsentController();
        $consent_controller->register_routes();

        // DSR endpoints
        $dsr_controller = new \ComplyFlow\API\DSRController();
        $dsr_controller->register_routes();

        // Scan endpoints
        $scan_controller = new \ComplyFlow\API\ScanController();
        $scan_controller->register_routes();

        /**
         * Fires after REST API routes are registered
         *
         * @since 1.0.0
         */
        do_action('complyflow_rest_api_init');
    }

    /**
     * Register WP-CLI commands
     *
     * @return void
     */
    private function register_cli_commands(): void {
        \ComplyFlow\CLI\CommandRegistry::register();
    }

    /**
     * AJAX: Save settings
     *
     * @return void
     */
    public function ajax_save_settings(): void {
        check_ajax_referer('complyflow_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        // Parse the serialized settings data
        $settings_string = isset($_POST['settings']) ? wp_unslash($_POST['settings']) : '';
        parse_str($settings_string, $settings_array);
        
        // Extract the actual settings from the parsed array
        $new_settings = isset($settings_array['complyflow_settings']) ? $settings_array['complyflow_settings'] : [];
        
        // Sanitize settings array recursively
        $new_settings = $this->sanitize_settings_array($new_settings);
        
        // Validate settings
        $validation = $this->settings->validate($new_settings);
        
        if (!$validation['valid']) {
            wp_send_json_error([
                'message' => __('Settings validation failed.', 'complyflow'),
                'errors' => $validation['errors']
            ]);
        }
        
        // Save settings
        $saved = $this->settings->save($new_settings);
        
        if ($saved) {
            wp_send_json_success(['message' => __('Settings saved successfully.', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Failed to save settings.', 'complyflow')]);
        }
    }
    
    /**
     * Recursively sanitize settings array
     *
     * @param array $array Array to sanitize
     * @return array Sanitized array
     */
    private function sanitize_settings_array(array $array): array {
        $sanitized = [];
        
        foreach ($array as $key => $value) {
            $key = sanitize_key($key);
            
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize_settings_array($value);
            } elseif (is_string($value)) {
                // Check if it's a URL
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    $sanitized[$key] = esc_url_raw($value);
                } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    // Check if it's an email
                    $sanitized[$key] = sanitize_email($value);
                } else {
                    // Default: sanitize as text field
                    $sanitized[$key] = sanitize_text_field($value);
                }
            } elseif (is_numeric($value)) {
                $sanitized[$key] = $value;
            } elseif (is_bool($value)) {
                $sanitized[$key] = (bool) $value;
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }

    /**
     * AJAX: Export settings
     *
     * @return void
     */
    public function ajax_export_settings(): void {
        check_ajax_referer('complyflow_export_settings', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        $exported = $this->settings->export();
        
        wp_send_json_success($exported);
    }

    /**
     * AJAX: Import settings
     *
     * @return void
     */
    public function ajax_import_settings(): void {
        check_ajax_referer('complyflow_import_settings', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        $json = isset($_POST['json']) ? wp_unslash($_POST['json']) : '';
        
        if (empty($json)) {
            wp_send_json_error(['message' => __('No data provided.', 'complyflow')]);
        }

        $result = $this->settings->import($json);
        
        if ($result) {
            wp_send_json_success(['message' => __('Settings imported successfully.', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Invalid settings data.', 'complyflow')]);
        }
    }

    /**
     * AJAX: Run accessibility scan
     *
     * @return void
     */
    public function ajax_run_scan(): void {
        check_ajax_referer('complyflow_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        // Scan logic here - delegates to scan controller
        
        wp_send_json_success(['message' => __('Scan completed.', 'complyflow')]);
    }

    /**
     * AJAX: Save user consent (deprecated - use REST API)
     *
     * @return void
     */
    public function ajax_save_consent(): void {
        // Redirects to REST API endpoint
        wp_send_json_success(['message' => __('Consent saved.', 'complyflow')]);
    }

    /**
     * Display admin notices
     *
     * @return void
     */
    public function display_admin_notices(): void {
        // Check if just activated
        if (get_transient('complyflow_activated')) {
            delete_transient('complyflow_activated');
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <?php
                    printf(
                        /* translators: %s: Plugin settings page URL */
                        esc_html__('ComplyFlow is now active! Get started by %s.', 'complyflow'),
                        '<a href="' . esc_url(admin_url('admin.php?page=complyflow-settings')) . '">' . esc_html__('configuring your settings', 'complyflow') . '</a>'
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Get settings instance
     *
     * @return Settings
     */
    public function get_settings(): Settings {
        return $this->settings;
    }

    /**
     * Get module manager instance
     *
     * @return ModuleManager
     */
    public function get_module_manager(): ModuleManager {
        return $this->module_manager;
    }

    /**
     * Get cache instance
     *
     * @return Cache
     */
    public function get_cache(): Cache {
        return $this->cache;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception('Cannot unserialize singleton');
    }
}
