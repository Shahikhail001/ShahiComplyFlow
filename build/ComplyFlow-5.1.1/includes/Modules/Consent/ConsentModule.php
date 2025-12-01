<?php
/**
 * Consent Module
 *
 * GDPR, CCPA, and LGPD compliant consent management.
 *
 * @package ComplyFlow\Modules\Consent
 * @since 1.0.0
 */

namespace ComplyFlow\Modules\Consent;

use ComplyFlow\Core\Repositories\SettingsRepository;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Consent Module Class
 *
 * @since 1.0.0
 */
class ConsentModule {
    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Consent banner
     *
     * @var ConsentBanner
     */
    private ConsentBanner $banner;

    /**
     * Cookie scanner
     *
     * @var CookieScanner
     */
    private CookieScanner $scanner;

    /**
     * Script blocker
     *
     * @var ScriptBlocker
     */
    private ScriptBlocker $blocker;

    /**
     * Consent logger
     *
     * @var ConsentLogger
     */
    private ConsentLogger $logger;

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = new SettingsRepository();
        $this->logger = new ConsentLogger();
        $this->scanner = new CookieScanner($this->settings);
        $this->blocker = new ScriptBlocker($this->settings);
        $this->banner = new ConsentBanner($this->settings, $this->logger);
    }

    /**
     * Initialize the module
     *
     * @return void
     */
    public function init(): void {
        $this->register_hooks();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    public function register_hooks(): void {
        // Frontend hooks
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('wp_footer', [$this->banner, 'render_banner'], 999);
        
        // Script blocking
        add_action('wp_head', [$this->blocker, 'start_output_buffering'], 1);
        add_action('wp_footer', [$this->blocker, 'end_output_buffering'], 999);
        
        // Admin hooks
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);
        add_action('admin_init', [$this, 'register_settings']);
        
        // AJAX handlers
        add_action('wp_ajax_complyflow_save_consent', [$this, 'ajax_save_consent']);
        add_action('wp_ajax_nopriv_complyflow_save_consent', [$this, 'ajax_save_consent']);
        add_action('wp_ajax_complyflow_get_consent', [$this, 'ajax_get_consent']);
        add_action('wp_ajax_nopriv_complyflow_get_consent', [$this, 'ajax_get_consent']);
        add_action('wp_ajax_complyflow_scan_cookies', [$this, 'ajax_scan_cookies']);
        add_action('wp_ajax_complyflow_add_cookie', [$this, 'ajax_add_cookie']);
        add_action('wp_ajax_complyflow_delete_cookie', [$this, 'ajax_delete_cookie']);
    }

    /**
     * Enqueue frontend assets
     *
     * @return void
     */
    public function enqueue_frontend_assets(): void {
        // Read from complyflow_consent_settings option
        $consent_settings = get_option('complyflow_consent_settings', []);
        $enabled = $consent_settings['banner_enabled'] ?? false;

        if (!$enabled) {
            return;
        }

        // Note: Consent banner styles are inline in ConsentBanner.php for critical CSS performance
        // Only enqueue the JavaScript for banner interactions

        // Enqueue consent banner script
        wp_enqueue_script(
            'complyflow-consent',
            COMPLYFLOW_URL . 'assets/dist/consent-banner.js',
            ['jquery'],
            COMPLYFLOW_VERSION,
            true
        );

        // Localize script
        $consent_settings = get_option('complyflow_consent_settings', []);
        wp_localize_script('complyflow-consent', 'complyflowConsent', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('complyflow_consent_nonce'),
            'settings' => [
                'position' => $consent_settings['position'] ?? 'bottom',
                'showRejectButton' => $consent_settings['show_reject'] ?? true,
                'autoBlock' => $consent_settings['auto_block'] ?? true,
                'consentDuration' => $consent_settings['duration'] ?? 365,
            ],
            'i18n' => [
                'acceptAll' => __('Accept All', 'complyflow'),
                'rejectAll' => __('Reject All', 'complyflow'),
                'customize' => __('Customize', 'complyflow'),
                'savePreferences' => __('Save Preferences', 'complyflow'),
                'necessary' => __('Necessary', 'complyflow'),
                'analytics' => __('Analytics', 'complyflow'),
                'marketing' => __('Marketing', 'complyflow'),
                'preferences' => __('Preferences', 'complyflow'),
            ],
        ]);
    }

    /**
     * Add admin menu item
     *
     * @return void
     */
    public function add_admin_menu(): void {
        add_submenu_page(
            'complyflow',
            __('Consent Manager', 'complyflow'),
            __('Consent Manager', 'complyflow'),
            'manage_options',
            'complyflow-consent',
            [$this, 'render_admin_page']
        );

        // Hidden submenu for preferences center preview
        add_submenu_page(
            null,
            __('Cookie Preferences', 'complyflow'),
            __('Cookie Preferences', 'complyflow'),
            'read',
            'complyflow-cookie-preferences',
            [$this, 'render_preferences_page']
        );
    }

    /**
     * Register settings
     *
     * @return void
     */
    public function register_settings(): void {
        // Register single option that stores all consent settings as an array
        register_setting(
            'complyflow_consent',
            'complyflow_consent_settings',
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize_consent_settings'],
                'default' => [],
            ]
        );
    }

    /**
     * Sanitize consent settings
     *
     * @param array $input Raw input data from form.
     * @return array Sanitized settings.
     */
    public function sanitize_consent_settings($input): array {
        $sanitized = [];

        // Banner settings
        $sanitized['banner_enabled'] = isset($input['banner_enabled']) ? (bool) $input['banner_enabled'] : false;
        $sanitized['position'] = isset($input['position']) ? sanitize_text_field($input['position']) : 'bottom';
        $sanitized['title'] = isset($input['title']) ? sanitize_text_field($input['title']) : __('We use cookies', 'complyflow');
        $sanitized['message'] = isset($input['message']) ? wp_kses_post($input['message']) : '';
        $sanitized['show_reject'] = isset($input['show_reject']) ? (bool) $input['show_reject'] : false;
        $sanitized['primary_color'] = isset($input['primary_color']) ? sanitize_hex_color($input['primary_color']) : '#2563eb';
        $sanitized['bg_color'] = isset($input['bg_color']) ? sanitize_hex_color($input['bg_color']) : '#ffffff';

        // Cookie settings
        $sanitized['auto_block'] = isset($input['auto_block']) ? (bool) $input['auto_block'] : false;
        $sanitized['duration'] = isset($input['duration']) ? absint($input['duration']) : 365;

        // Compliance settings - Major Global Privacy Laws
        $sanitized['gdpr_enabled'] = isset($input['gdpr_enabled']) ? (bool) $input['gdpr_enabled'] : false;
        $sanitized['uk_gdpr_enabled'] = isset($input['uk_gdpr_enabled']) ? (bool) $input['uk_gdpr_enabled'] : false;
        $sanitized['ccpa_enabled'] = isset($input['ccpa_enabled']) ? (bool) $input['ccpa_enabled'] : false;
        $sanitized['lgpd_enabled'] = isset($input['lgpd_enabled']) ? (bool) $input['lgpd_enabled'] : false;
        $sanitized['pipeda_enabled'] = isset($input['pipeda_enabled']) ? (bool) $input['pipeda_enabled'] : false;
        $sanitized['pdpa_sg_enabled'] = isset($input['pdpa_sg_enabled']) ? (bool) $input['pdpa_sg_enabled'] : false;
        $sanitized['pdpa_th_enabled'] = isset($input['pdpa_th_enabled']) ? (bool) $input['pdpa_th_enabled'] : false;
        $sanitized['appi_enabled'] = isset($input['appi_enabled']) ? (bool) $input['appi_enabled'] : false;
        $sanitized['popia_enabled'] = isset($input['popia_enabled']) ? (bool) $input['popia_enabled'] : false;
        $sanitized['kvkk_enabled'] = isset($input['kvkk_enabled']) ? (bool) $input['kvkk_enabled'] : false;
        $sanitized['pdpl_enabled'] = isset($input['pdpl_enabled']) ? (bool) $input['pdpl_enabled'] : false;

        return $sanitized;
    }

    /**
     * Render admin page
     *
     * @return void
     */
    public function render_admin_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $scanner = $this->scanner;
        $logger = $this->logger;
        include COMPLYFLOW_PATH . 'includes/Admin/views/consent-manager-new.php';
    }

    /**
     * Render preferences page
     *
     * @return void
     */
    public function render_preferences_page(): void {
        $scanner = $this->scanner;
        include COMPLYFLOW_PATH . 'includes/Admin/views/cookie-preferences.php';
    }

    /**
     * AJAX handler for saving consent
     *
     * @return void
     */
    public function ajax_save_consent(): void {
        check_ajax_referer('complyflow_consent_nonce', 'nonce');

        $consent_data = [
            'necessary' => true, // Always true
            'analytics' => isset($_POST['analytics']) && sanitize_text_field($_POST['analytics']) === 'true',
            'marketing' => isset($_POST['marketing']) && sanitize_text_field($_POST['marketing']) === 'true',
            'preferences' => isset($_POST['preferences']) && sanitize_text_field($_POST['preferences']) === 'true',
        ];

        // Try to log consent (non-critical, continue if it fails)
        try {
            $this->logger->log_consent($consent_data);
        } catch (\Exception $e) {
            error_log('ComplyFlow: Failed to log consent: ' . $e->getMessage());
        }

        // Set cookie (this is the primary consent storage)
        $consent_settings = get_option('complyflow_consent_settings', []);
        $duration = $consent_settings['duration'] ?? 365;
        
        $cookie_set = setcookie(
            'complyflow_consent',
            json_encode($consent_data),
            time() + ($duration * DAY_IN_SECONDS),
            COOKIEPATH,
            COOKIE_DOMAIN,
            is_ssl(),
            true
        );

        // Always return success if we get here (cookie will be set on response)
        wp_send_json_success([
            'message' => __('Consent preferences saved.', 'complyflow'),
            'consent' => $consent_data,
        ]);
    }

    /**
     * AJAX handler for getting consent
     *
     * @return void
     */
    public function ajax_get_consent(): void {
        check_ajax_referer('complyflow_consent_nonce', 'nonce');

        if (isset($_COOKIE['complyflow_consent'])) {
            $consent = json_decode(stripslashes($_COOKIE['complyflow_consent']), true);
            wp_send_json_success(['consent' => $consent]);
        } else {
            wp_send_json_success(['consent' => null]);
        }
    }

    /**
     * AJAX handler for scanning cookies
     *
     * @return void
     */
    public function ajax_scan_cookies(): void {
        check_ajax_referer('complyflow_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions.', 'complyflow')]);
        }

        $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : home_url();

        try {
            $cookies = $this->scanner->scan_cookies($url);
            wp_send_json_success([
                'cookies' => $cookies,
                'message' => sprintf(__('Found %d cookies.', 'complyflow'), count($cookies)),
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Get consent banner instance
     *
     * @return ConsentBanner
     */
    public function get_banner(): ConsentBanner {
        return $this->banner;
    }

    /**
     * Get cookie scanner instance
     *
     * @return CookieScanner
     */
    public function get_scanner(): CookieScanner {
        return $this->scanner;
    }

    /**
     * Get script blocker instance
     *
     * @return ScriptBlocker
     */
    public function get_blocker(): ScriptBlocker {
        return $this->blocker;
    }

    /**
     * Get consent logger instance
     *
     * @return ConsentLogger
     */
    public function get_logger(): ConsentLogger {
        return $this->logger;
    }

    /**
     * AJAX handler for adding a cookie
     *
     * @return void
     */
    public function ajax_add_cookie(): void {
        check_ajax_referer('complyflow_manage_cookies', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $cookie = isset($_POST['cookie']) ? array_map('sanitize_text_field', wp_unslash($_POST['cookie'])) : [];

        if (empty($cookie['name'])) {
            wp_send_json_error(['message' => __('Cookie name is required', 'complyflow')]);
            return;
        }

        // Get current cookies
        $managed_cookies = $this->scanner->get_managed_cookies();
        $category = $cookie['category'] ?? 'necessary';

        // Add new cookie
        $managed_cookies[$category][] = [
            'name' => sanitize_text_field($cookie['name']),
            'domain' => sanitize_text_field($cookie['domain'] ?? ''),
            'path' => '/',
            'expiry' => sanitize_text_field($cookie['expiry'] ?? 'Session'),
            'description' => sanitize_textarea_field($cookie['description'] ?? ''),
            'category' => $category,
        ];

        // Save cookies
        $this->scanner->save_managed_cookies($managed_cookies);

        wp_send_json_success(['message' => __('Cookie added successfully', 'complyflow')]);
    }

    /**
     * AJAX handler for deleting a cookie
     *
     * @return void
     */
    public function ajax_delete_cookie(): void {
        check_ajax_referer('complyflow_manage_cookies', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $category = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : '';
        $index = isset($_POST['index']) ? intval($_POST['index']) : -1;

        if (empty($category) || $index < 0) {
            wp_send_json_error(['message' => __('Invalid parameters', 'complyflow')]);
            return;
        }

        // Get current cookies
        $managed_cookies = $this->scanner->get_managed_cookies();

        // Remove cookie
        if (isset($managed_cookies[$category][$index])) {
            unset($managed_cookies[$category][$index]);
            $managed_cookies[$category] = array_values($managed_cookies[$category]); // Re-index array

            // Save cookies
            $this->scanner->save_managed_cookies($managed_cookies);

            wp_send_json_success(['message' => __('Cookie deleted successfully', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Cookie not found', 'complyflow')]);
        }
    }

}
