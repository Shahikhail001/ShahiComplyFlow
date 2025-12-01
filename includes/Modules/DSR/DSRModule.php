<?php
/**
 * Data Subject Rights (DSR) Module
 *
 * @package ComplyFlow\Modules\DSR
 * @since   3.1.3
 */

namespace ComplyFlow\Modules\DSR;

use ComplyFlow\Core\Interfaces\ModuleInterface;
use ComplyFlow\Core\SettingsRepository;

class DSRModule implements ModuleInterface {
    
    private const SLUG = 'dsr';
    private const POST_TYPE = 'complyflow_dsr';
    
    private SettingsRepository $settings;
    private RequestHandler $request_handler;
    private DataExporter $data_exporter;
    private EmailNotifier $email_notifier;

    public function __construct(?SettingsRepository $settings = null) {
        $this->settings = $settings ?? SettingsRepository::get_instance();
        $this->request_handler = new RequestHandler($this->settings);
        $this->data_exporter = new DataExporter($this->settings);
        $this->email_notifier = new EmailNotifier($this->settings);
    }

    public static function get_info(): array {
        return [
            'name' => __('Data Subject Rights', 'complyflow'),
            'description' => __('Handle GDPR/CCPA/LGPD data subject access requests.', 'complyflow'),
            'slug' => self::SLUG,
            'version' => '1.0.0',
            'author' => 'ComplyFlow Team',
            'dependencies' => [],
        ];
    }

    public function init(): void {
        $this->register_hooks();
        $this->request_handler->init();
        $this->data_exporter->init();
        $this->email_notifier->init();
    }

    private function register_hooks(): void {
        add_action('init', [$this, 'register_post_type']);
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_shortcode('complyflow_dsr_form', [$this, 'render_dsr_form_shortcode']);
        add_action('wp_ajax_complyflow_submit_dsr', [$this, 'ajax_submit_dsr']);
        add_action('wp_ajax_nopriv_complyflow_submit_dsr', [$this, 'ajax_submit_dsr']);
        add_action('wp_ajax_complyflow_process_dsr', [$this, 'ajax_process_dsr']);
        add_action('wp_ajax_complyflow_export_dsr_data', [$this, 'ajax_export_dsr_data']);
        add_action('template_redirect', [$this, 'handle_verification_link']);
        add_action('transition_post_status', [$this, 'on_status_change'], 10, 3);
    }

    public function register_post_type(): void {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => __('DSR Requests', 'complyflow'),
                'singular_name' => __('DSR Request', 'complyflow'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => ['title'],
            'capabilities' => ['create_posts' => false],
            'map_meta_cap' => true,
        ]);

        register_post_status('dsr_pending', [
            'label' => __('Pending Verification', 'complyflow'),
            'public' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
        ]);

        register_post_status('dsr_verified', [
            'label' => __('Verified', 'complyflow'),
            'public' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
        ]);

        register_post_status('dsr_in_progress', [
            'label' => __('In Progress', 'complyflow'),
            'public' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
        ]);

        register_post_status('dsr_completed', [
            'label' => __('Completed', 'complyflow'),
            'public' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
        ]);

        register_post_status('dsr_rejected', [
            'label' => __('Rejected', 'complyflow'),
            'public' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
        ]);
    }

    public function add_admin_menu(): void {
        error_log('ComplyFlow: DSRModule add_admin_menu() called');
        $result = add_submenu_page(
            'complyflow',
            __('DSR Requests', 'complyflow'),
            __('DSR Requests', 'complyflow'),
            'manage_options',
            'complyflow-dsr',
            [$this, 'render_admin_page']
        );
        error_log('ComplyFlow: DSRModule menu added, result: ' . var_export($result, true));
    }

    public function enqueue_admin_assets(string $hook): void {
        if (!str_contains($hook, 'complyflow-dsr')) {
            return;
        }

        // TODO: Create DSR-specific assets or use common admin styles
        // wp_enqueue_style('complyflow-dsr-admin', COMPLYFLOW_URL . 'assets/dist/css/dsr-admin.css', [], COMPLYFLOW_VERSION);
        // wp_enqueue_script('complyflow-dsr-admin', COMPLYFLOW_URL . 'assets/dist/js/dsr-admin.js', ['jquery'], COMPLYFLOW_VERSION, true);
        
        // Use common admin styles for now
        wp_enqueue_style('complyflow-admin-common', COMPLYFLOW_URL . 'assets/dist/admin-common.css', [], COMPLYFLOW_VERSION);

        wp_localize_script('complyflow-dsr-admin', 'complyflowDSR', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('complyflow_dsr_nonce'),
            'i18n' => [
                'processing' => __('Processing...', 'complyflow'),
                'error' => __('An error occurred', 'complyflow'),
                'confirmApprove' => __('Approve this request?', 'complyflow'),
                'confirmReject' => __('Reject this request?', 'complyflow'),
            ],
        ]);
    }

    public function enqueue_frontend_assets(): void {
        if (!is_singular() && !is_page()) {
            return;
        }

        global $post;
        if (!$post || !has_shortcode($post->post_content, 'complyflow_dsr_form')) {
            return;
        }

        // Use common frontend styles
        wp_enqueue_style('complyflow-frontend', COMPLYFLOW_URL . 'assets/dist/frontend-style.css', [], COMPLYFLOW_VERSION);
        wp_enqueue_script('jquery');

        // Add inline JavaScript for DSR form handling
        wp_add_inline_script('jquery', "
            jQuery(document).ready(function($) {
                $('.complyflow-dsr-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    var \$form = $(this);
                    var \$button = \$form.find('button[type=\"submit\"]');
                    var \$message = \$form.closest('.complyflow-dsr-wrapper').find('.complyflow-form-message');
                    
                    // Disable button and show loading
                    \$button.prop('disabled', true);
                    \$button.find('.button-text').hide();
                    \$button.find('.button-loader').show();
                    \$message.hide();
                    
                    var formData = {
                        action: 'complyflow_submit_dsr',
                        nonce: \$button.data('nonce'),
                        request_type: \$form.find('[name=\"request_type\"]').val(),
                        full_name: \$form.find('[name=\"full_name\"]').val(),
                        email: \$form.find('[name=\"email\"]').val(),
                        additional_info: \$form.find('[name=\"additional_info\"]').val()
                    };
                    
                    console.log('Submitting DSR request:', formData);
                    
                    $.ajax({
                        url: '" . admin_url('admin-ajax.php') . "',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log('DSR response:', response);
                            
                            \$button.prop('disabled', false);
                            \$button.find('.button-text').show();
                            \$button.find('.button-loader').hide();
                            
                            if (response.success) {
                                \$message.removeClass('complyflow-notice-error')
                                       .addClass('complyflow-notice complyflow-notice-success')
                                       .html('<p>' + (response.data.message || '" . esc_js(__('Request submitted! Please check your email to verify.', 'complyflow')) . "') + '</p>')
                                       .fadeIn();
                                \$form[0].reset();
                            } else {
                                \$message.removeClass('complyflow-notice-success')
                                       .addClass('complyflow-notice complyflow-notice-error')
                                       .html('<p>' + (response.data.message || '" . esc_js(__('An error occurred. Please try again.', 'complyflow')) . "') + '</p>')
                                       .fadeIn();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('DSR AJAX error:', error);
                            
                            \$button.prop('disabled', false);
                            \$button.find('.button-text').show();
                            \$button.find('.button-loader').hide();
                            
                            \$message.removeClass('complyflow-notice-success')
                                   .addClass('complyflow-notice complyflow-notice-error')
                                   .html('<p>" . esc_js(__('Network error. Please try again.', 'complyflow')) . "</p>')
                                   .fadeIn();
                        }
                    });
                });
            });
        ");
    }

    public function render_admin_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }
        include COMPLYFLOW_PATH . 'includes/Admin/views/dsr-requests.php';
    }

    public function render_dsr_form_shortcode(array $atts = []): string {
        $atts = shortcode_atts([
            'title' => __('Data Subject Rights Request', 'complyflow'),
            'show_types' => 'access,deletion,portability,rectification,restriction',
        ], $atts);

        ob_start();
        include COMPLYFLOW_PATH . 'includes/Frontend/views/dsr-form.php';
        return ob_get_clean();
    }

    public function ajax_submit_dsr(): void {
        error_log('ComplyFlow: DSR ajax_submit_dsr called');
        error_log('ComplyFlow: POST data: ' . print_r($_POST, true));
        
        check_ajax_referer('complyflow_dsr_public_nonce', 'nonce');

        $request_type = sanitize_text_field($_POST['request_type'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $full_name = sanitize_text_field($_POST['full_name'] ?? '');
        $additional_info = sanitize_textarea_field($_POST['additional_info'] ?? '');

        error_log("ComplyFlow: DSR data - Type: $request_type, Email: $email, Name: $full_name");

        if (empty($request_type) || empty($email) || empty($full_name)) {
            error_log('ComplyFlow: DSR missing required fields');
            wp_send_json_error(['message' => __('Missing required fields', 'complyflow')]);
        }

        $result = $this->request_handler->create_request([
            'type' => $request_type,
            'email' => $email,
            'full_name' => $full_name,
            'additional_info' => $additional_info,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);

        if (is_wp_error($result)) {
            error_log('ComplyFlow: DSR error - ' . $result->get_error_message());
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        error_log("ComplyFlow: DSR request created successfully, ID: $result");
        wp_send_json_success([
            'message' => __('Request submitted successfully. Please check your email to verify your request.', 'complyflow'),
            'request_id' => $result,
        ]);
    }

    public function handle_verification_link(): void {
        if (!isset($_GET['complyflow_verify_dsr'])) {
            return;
        }

        $token = sanitize_text_field($_GET['complyflow_verify_dsr']);
        $result = $this->request_handler->verify_request($token);

        if (is_wp_error($result)) {
            wp_die($result->get_error_message(), __('Verification Failed', 'complyflow'));
        }

        wp_die(__('Your request has been verified successfully.', 'complyflow'), __('Request Verified', 'complyflow'));
    }

    public function ajax_process_dsr(): void {
        check_ajax_referer('complyflow_dsr_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $request_id = intval($_POST['request_id'] ?? 0);
        $action = sanitize_text_field($_POST['dsr_action'] ?? '');
        $note = sanitize_textarea_field($_POST['note'] ?? '');

        $result = $this->request_handler->process_request($request_id, $action, $note);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success(['message' => __('Request processed successfully', 'complyflow')]);
    }

    public function ajax_export_dsr_data(): void {
        check_ajax_referer('complyflow_dsr_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
        }

        $request_id = intval($_POST['request_id'] ?? 0);
        $format = sanitize_text_field($_POST['format'] ?? 'json');

        $result = $this->data_exporter->export_user_data($request_id, $format);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    public function on_status_change(string $new_status, string $old_status, \WP_Post $post): void {
        if ($post->post_type !== self::POST_TYPE || $new_status === $old_status) {
            return;
        }

        $this->email_notifier->send_status_change_notification($post->ID, $old_status, $new_status);
        $this->request_handler->add_note($post->ID, sprintf(
            __('Status changed from %s to %s', 'complyflow'),
            $old_status,
            $new_status
        ));
    }
}
