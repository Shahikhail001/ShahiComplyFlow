<?php
/**
 * Cookie Inventory Module
 *
 * @package ComplyFlow\Modules\Cookie
 * @since   3.3.1
 */

namespace ComplyFlow\Modules\Cookie;

use ComplyFlow\Core\SettingsRepository;

class CookieModule {
    
    private const SLUG = 'cookie';
    
    private SettingsRepository $settings;
    private CookieScanner $scanner;
    private CookieInventory $inventory;
    
    public function __construct() {
        $this->settings = SettingsRepository::get_instance();
        $this->scanner = new CookieScanner($this->settings);
        $this->inventory = new CookieInventory($this->settings);
    }
    
    /**
     * Initialize the module
     *
     * @return void
     */
    public function init(): void {
        error_log('ComplyFlow: CookieModule init() called');
        // Register hooks immediately instead of waiting for complyflow_init
        $this->register_hooks();
        $this->scanner->init();
        $this->inventory->init();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    public function register_hooks(): void {
        error_log('ComplyFlow: CookieModule register_hooks() called');
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        
        // Test action - no nonce check at all
        add_action('wp_ajax_complyflow_test_simple', function() {
            error_log('ComplyFlow: TEST ACTION CALLED!!!');
            wp_send_json_success(['message' => 'Test works!']);
        });
        
        // Try wrapping the method in a closure
        add_action('wp_ajax_complyflow_scan_cookies', function() {
            error_log('ComplyFlow: CLOSURE WRAPPER CALLED - about to call ajax_scan_cookies');
            $this->ajax_scan_cookies();
        }, 1);
        error_log('ComplyFlow: CookieModule registered wp_ajax_complyflow_scan_cookies action');
        error_log('ComplyFlow: ajax_scan_cookies method exists: ' . (method_exists($this, 'ajax_scan_cookies') ? 'YES' : 'NO'));
        error_log('ComplyFlow: Callback is callable: ' . (is_callable([$this, 'ajax_scan_cookies']) ? 'YES' : 'NO'));
        add_action('wp_ajax_complyflow_update_cookie_category', [$this, 'ajax_update_category']);
        add_action('wp_ajax_complyflow_bulk_update_cookies', [$this, 'ajax_bulk_update']);
        add_action('wp_ajax_complyflow_export_cookies_csv', [$this, 'ajax_export_csv']);
        add_action('wp_ajax_complyflow_delete_cookie', [$this, 'ajax_delete_cookie']);
        add_action('wp_ajax_complyflow_edit_cookie', [$this, 'ajax_edit_cookie']);
        add_action('wp_ajax_complyflow_get_cookie', [$this, 'ajax_get_cookie']);
        add_action('wp_ajax_complyflow_add_manual_cookie', [$this, 'ajax_add_manual_cookie']);
        add_action('wp_ajax_complyflow_import_cookies_csv', [$this, 'ajax_import_cookies']);
    }

    public function add_admin_menu(): void {
        add_submenu_page(
            'complyflow',
            __('Cookie Inventory', 'complyflow'),
            __('Cookie Inventory', 'complyflow'),
            'manage_options',
            'complyflow-cookies',
            [$this, 'render_admin_page']
        );
    }

    public function enqueue_admin_assets(string $hook): void {
        if (!str_contains($hook, 'complyflow-cookies')) {
            return;
        }

        // Enqueue common admin styles
        wp_enqueue_style('complyflow-admin-common', COMPLYFLOW_URL . 'assets/dist/admin-common.css', [], COMPLYFLOW_VERSION);
        wp_enqueue_style('complyflow-admin-style', COMPLYFLOW_URL . 'assets/dist/admin-style.css', ['complyflow-admin-common'], COMPLYFLOW_VERSION);
        
        // jQuery for AJAX functionality (inline script in view)
        wp_enqueue_script('jquery');
    }

    public function render_admin_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }
        include COMPLYFLOW_PATH . 'includes/Admin/views/cookie-inventory.php';
    }

    public function ajax_scan_cookies(): void {
        error_log('ComplyFlow: ajax_scan_cookies called');
        error_log('ComplyFlow: POST data: ' . print_r($_POST, true));
        
        // Check user capability first
        if (!current_user_can('manage_options')) {
            error_log('ComplyFlow: Cookie permission check failed');
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')], 403);
            return;
        }
        
        // Check nonce using wp_verify_nonce directly
        $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : (isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '');
        error_log('ComplyFlow: Received nonce: ' . $nonce);
        
        if (!wp_verify_nonce($nonce, 'complyflow_cookie_nonce')) {
            error_log('ComplyFlow: Cookie nonce verification failed');
            error_log('ComplyFlow: Expected nonce action: complyflow_cookie_nonce');
            wp_send_json_error(['message' => __('Security check failed. Please refresh the page and try again.', 'complyflow')], 403);
            return;
        }
        
        error_log('ComplyFlow: Nonce verification passed');

        $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : home_url();
        error_log('ComplyFlow: Cookie URL to scan: ' . $url);
        
        try {
            error_log('ComplyFlow: Starting cookie scanner->scan_site()');
            $cookies = $this->scanner->scan_site($url);
            error_log('ComplyFlow: Cookie scanner result type: ' . gettype($cookies));
            if (is_array($cookies)) {
                error_log('ComplyFlow: Found ' . count($cookies) . ' cookies');
            }
        } catch (\Exception $e) {
            error_log('ComplyFlow: Cookie scanner exception: ' . $e->getMessage());
            wp_send_json_error(['message' => __('Cookie scan error: ', 'complyflow') . $e->getMessage()]);
            return;
        }

        if (is_wp_error($cookies)) {
            error_log('ComplyFlow: Cookie scanner returned WP_Error: ' . $cookies->get_error_message());
            wp_send_json_error(['message' => $cookies->get_error_message()]);
            return;
        }

        // Save to inventory
        try {
            error_log('ComplyFlow: Saving cookies to inventory');
            foreach ($cookies as $cookie) {
                $this->inventory->add_or_update($cookie);
            }
        } catch (\Exception $e) {
            error_log('ComplyFlow: Error saving cookies: ' . $e->getMessage());
        }

        error_log('ComplyFlow: Cookie scan successful');
        wp_send_json_success([
            'message' => sprintf(__('Found %d cookies/trackers', 'complyflow'), count($cookies)),
            'cookies' => $cookies,
            'count' => count($cookies),
        ]);
    }

    public function ajax_update_category(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $cookie_id = intval($_POST['cookie_id'] ?? 0);
        $category = sanitize_text_field($_POST['category'] ?? '');

        if (!$cookie_id || !$category) {
            wp_send_json_error(['message' => __('Invalid data', 'complyflow')]);
        }

        $result = $this->inventory->update_category($cookie_id, $category);

        if ($result) {
            wp_send_json_success(['message' => __('Category updated', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Update failed', 'complyflow')]);
        }
    }

    public function ajax_bulk_update(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $cookie_ids = isset($_POST['cookie_ids']) ? array_map('intval', $_POST['cookie_ids']) : [];
        $category = sanitize_text_field($_POST['category'] ?? '');

        if (empty($cookie_ids) || !$category) {
            wp_send_json_error(['message' => __('Invalid data', 'complyflow')]);
        }

        $updated = 0;
        foreach ($cookie_ids as $cookie_id) {
            if ($this->inventory->update_category($cookie_id, $category)) {
                $updated++;
            }
        }

        wp_send_json_success([
            'message' => sprintf(__('Updated %d cookies', 'complyflow'), $updated),
            'count' => $updated,
        ]);
    }

    public function ajax_export_csv(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $cookies = $this->inventory->get_all();
        $csv = $this->inventory->export_to_csv($cookies);

        if (is_wp_error($csv)) {
            wp_send_json_error(['message' => $csv->get_error_message()]);
        }

        // Save CSV to uploads directory
        $upload_dir = wp_upload_dir();
        $filename = 'complyflow-cookies-' . date('Y-m-d-His') . '.csv';
        $filepath = $upload_dir['path'] . '/' . $filename;
        $fileurl = $upload_dir['url'] . '/' . $filename;

        // Write CSV file
        $result = file_put_contents($filepath, $csv);

        if ($result === false) {
            wp_send_json_error(['message' => __('Failed to create export file', 'complyflow')]);
        }

        wp_send_json_success([
            'url' => $fileurl,
            'filename' => $filename,
        ]);
    }

    public function ajax_delete_cookie(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $cookie_id = intval($_POST['cookie_id'] ?? 0);

        if (!$cookie_id) {
            wp_send_json_error(['message' => __('Invalid cookie ID', 'complyflow')]);
        }

        $result = $this->inventory->delete($cookie_id);

        if ($result) {
            wp_send_json_success(['message' => __('Cookie deleted', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Delete failed', 'complyflow')]);
        }
    }

    public function ajax_get_cookie(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $cookie_id = intval($_POST['cookie_id'] ?? 0);

        if (!$cookie_id) {
            wp_send_json_error(['message' => __('Invalid cookie ID', 'complyflow')]);
        }

        $cookie = $this->inventory->get_by_id($cookie_id);

        if ($cookie) {
            wp_send_json_success(['cookie' => $cookie]);
        } else {
            wp_send_json_error(['message' => __('Cookie not found', 'complyflow')]);
        }
    }

    public function ajax_edit_cookie(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $cookie_id = intval($_POST['cookie_id'] ?? 0);
        $details = [
            'purpose' => sanitize_textarea_field($_POST['purpose'] ?? ''),
            'expiry' => sanitize_text_field($_POST['expiry'] ?? ''),
            'provider' => sanitize_text_field($_POST['provider'] ?? ''),
            'type' => sanitize_text_field($_POST['type'] ?? ''),
        ];

        if (!$cookie_id) {
            wp_send_json_error(['message' => __('Invalid cookie ID', 'complyflow')]);
        }

        $result = $this->inventory->update_details($cookie_id, $details);

        if ($result) {
            wp_send_json_success(['message' => __('Cookie updated successfully', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Update failed', 'complyflow')]);
        }
    }

    public function ajax_add_manual_cookie(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $cookie = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'provider' => sanitize_text_field($_POST['provider'] ?? ''),
            'category' => sanitize_text_field($_POST['category'] ?? 'functional'),
            'type' => sanitize_text_field($_POST['type'] ?? 'http'),
            'purpose' => sanitize_textarea_field($_POST['purpose'] ?? ''),
            'expiry' => sanitize_text_field($_POST['expiry'] ?? ''),
            'is_manual' => 1,
            'source' => 'manual',
        ];

        if (empty($cookie['name'])) {
            wp_send_json_error(['message' => __('Cookie name is required', 'complyflow')]);
        }

        $result = $this->inventory->add_or_update($cookie);

        if ($result !== false) {
            wp_send_json_success([
                'message' => __('External cookie added successfully', 'complyflow'),
                'cookie_id' => $result,
            ]);
        } else {
            wp_send_json_error(['message' => __('Failed to add cookie', 'complyflow')]);
        }
    }

    public function ajax_import_cookies(): void {
        check_ajax_referer('complyflow_cookie_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        if (empty($_FILES['csv_file'])) {
            wp_send_json_error(['message' => __('No file uploaded', 'complyflow')]);
        }

        $file = $_FILES['csv_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => __('File upload error', 'complyflow')]);
        }

        $csv_data = file_get_contents($file['tmp_name']);

        if ($csv_data === false) {
            wp_send_json_error(['message' => __('Failed to read file', 'complyflow')]);
        }

        $result = $this->inventory->import_from_csv_data($csv_data);

        wp_send_json_success([
            'message' => sprintf(
                __('Imported %d cookies', 'complyflow'),
                $result['imported']
            ),
            'imported' => $result['imported'],
            'errors' => $result['errors'],
        ]);
    }
}
