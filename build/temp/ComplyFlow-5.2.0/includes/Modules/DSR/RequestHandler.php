<?php
/**
 * DSR Request Handler
 *
 * @package ComplyFlow\Modules\DSR
 * @since   3.1.3
 */

namespace ComplyFlow\Modules\DSR;

use ComplyFlow\Core\SettingsRepository;
use WP_Error;

class RequestHandler {
    
    private SettingsRepository $settings;

    public function __construct(SettingsRepository $settings) {
        $this->settings = $settings;
    }

    public function init(): void {
        // Initialization logic if needed
    }

    public function create_request(array $data): int|WP_Error {
        $verification_token = wp_generate_password(32, false);
        $token_expiry = time() + (24 * HOUR_IN_SECONDS);

        $title = sprintf(
            '%s - %s (%s)',
            ucfirst($data['type']),
            $data['full_name'],
            $data['email']
        );

        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_type' => 'complyflow_dsr',
            'post_status' => 'dsr_pending',
            'post_author' => 0,
        ]);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        update_post_meta($post_id, '_dsr_type', sanitize_text_field($data['type']));
        update_post_meta($post_id, '_dsr_email', sanitize_email($data['email']));
        update_post_meta($post_id, '_dsr_full_name', sanitize_text_field($data['full_name']));
        update_post_meta($post_id, '_dsr_additional_info', wp_kses_post($data['additional_info'] ?? ''));
        update_post_meta($post_id, '_dsr_ip_address', sanitize_text_field($data['ip_address'] ?? ''));
        update_post_meta($post_id, '_dsr_user_agent', sanitize_text_field($data['user_agent'] ?? ''));
        update_post_meta($post_id, '_dsr_verification_token', $verification_token);
        update_post_meta($post_id, '_dsr_token_expiry', $token_expiry);
        update_post_meta($post_id, '_dsr_submitted_date', current_time('mysql'));

        // Send verification email
        $verification_url = add_query_arg('complyflow_verify_dsr', $verification_token, home_url('/'));
        
        $subject = sprintf(__('[%s] Verify Your Data Request', 'complyflow'), get_bloginfo('name'));
        $message = sprintf(
            __('Hello %s,\n\nPlease verify your data subject rights request by clicking the link below:\n\n%s\n\nThis link will expire in 24 hours.\n\nRequest Type: %s\nEmail: %s\n\nIf you did not make this request, please ignore this email.', 'complyflow'),
            $data['full_name'],
            $verification_url,
            ucfirst($data['type']),
            $data['email']
        );

        error_log("ComplyFlow: Sending verification email to {$data['email']}");
        error_log("ComplyFlow: Email subject: $subject");
        error_log("ComplyFlow: Verification URL: $verification_url");
        
        // Hook to capture email details for debugging/testing on localhost
        add_action('wp_mail_failed', function($error) {
            error_log("ComplyFlow: wp_mail failed with error: " . $error->get_error_message());
        });
        
        $mail_sent = wp_mail($data['email'], $subject, $message);
        
        if ($mail_sent) {
            error_log("ComplyFlow: Verification email sent successfully");
        } else {
            error_log("ComplyFlow: wp_mail returned false - mail server not configured");
            error_log("ComplyFlow: FOR TESTING: Copy this verification URL to verify the request:");
            error_log("ComplyFlow: $verification_url");
            
            // Store verification URL in post meta for admin to access
            update_post_meta($post_id, '_dsr_verification_url_for_testing', $verification_url);
        }

        $this->add_note($post_id, __('Request submitted. Verification email ' . ($mail_sent ? 'sent' : 'could not be sent (check mail server configuration)') . '.', 'complyflow'));

        return $post_id;
    }

    public function verify_request(string $token): true|WP_Error {
        $posts = get_posts([
            'post_type' => 'complyflow_dsr',
            'post_status' => 'dsr_pending',
            'meta_query' => [
                [
                    'key' => '_dsr_verification_token',
                    'value' => $token,
                ],
            ],
            'posts_per_page' => 1,
        ]);

        if (empty($posts)) {
            return new WP_Error('invalid_token', __('Invalid or expired verification token.', 'complyflow'));
        }

        $post = $posts[0];
        $token_expiry = get_post_meta($post->ID, '_dsr_token_expiry', true);

        if (time() > $token_expiry) {
            return new WP_Error('token_expired', __('Verification token has expired.', 'complyflow'));
        }

        wp_update_post([
            'ID' => $post->ID,
            'post_status' => 'dsr_verified',
        ]);

        update_post_meta($post->ID, '_dsr_verified_date', current_time('mysql'));
        delete_post_meta($post->ID, '_dsr_verification_token');
        delete_post_meta($post->ID, '_dsr_token_expiry');

        $this->add_note($post->ID, __('Request verified successfully.', 'complyflow'));

        // Notify admin
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('[%s] New Verified DSR Request', 'complyflow'), get_bloginfo('name'));
        $admin_url = admin_url('admin.php?page=complyflow-dsr&request_id=' . $post->ID);
        $message = sprintf(
            __('A new data subject rights request has been verified and requires your attention.\n\nRequest ID: %d\nType: %s\nRequester: %s\n\nView request: %s', 'complyflow'),
            $post->ID,
            get_post_meta($post->ID, '_dsr_type', true),
            get_post_meta($post->ID, '_dsr_full_name', true),
            $admin_url
        );

        wp_mail($admin_email, $subject, $message);

        return true;
    }

    public function process_request(int $request_id, string $action, string $note = ''): true|WP_Error {
        $post = get_post($request_id);

        if (!$post || $post->post_type !== 'complyflow_dsr') {
            return new WP_Error('invalid_request', __('Invalid request ID.', 'complyflow'));
        }

        $new_status = match($action) {
            'verify' => 'dsr_verified',
            'approve' => 'dsr_in_progress',
            'complete' => 'dsr_completed',
            'reject' => 'dsr_rejected',
            default => null,
        };

        if (!$new_status) {
            return new WP_Error('invalid_action', __('Invalid action.', 'complyflow'));
        }

        wp_update_post([
            'ID' => $request_id,
            'post_status' => $new_status,
        ]);

        if ($action === 'verify') {
            update_post_meta($request_id, '_dsr_verified_date', current_time('mysql'));
        }

        if ($action === 'complete') {
            update_post_meta($request_id, '_dsr_completed_date', current_time('mysql'));
        }

        if (!empty($note)) {
            $this->add_note($request_id, $note);
        }

        $action_text = match($action) {
            'verify' => __('manually verified by admin', 'complyflow'),
            'approve' => __('approved', 'complyflow'),
            'complete' => __('completed', 'complyflow'),
            'reject' => __('rejected', 'complyflow'),
        };

        $this->add_note($request_id, sprintf(__('Request %s.', 'complyflow'), $action_text));

        return true;
    }

    public function add_note(int $request_id, string $note): void {
        $notes = get_post_meta($request_id, '_dsr_notes', true) ?: [];
        
        $notes[] = [
            'note' => sanitize_text_field($note),
            'date' => current_time('mysql'),
            'user' => wp_get_current_user()->display_name ?: __('System', 'complyflow'),
        ];

        update_post_meta($request_id, '_dsr_notes', $notes);
    }

    public function get_request_data(int $request_id): array|WP_Error {
        $post = get_post($request_id);

        if (!$post || $post->post_type !== 'complyflow_dsr') {
            return new WP_Error('invalid_request', __('Invalid request ID.', 'complyflow'));
        }

        return [
            'id' => $post->ID,
            'type' => get_post_meta($post->ID, '_dsr_type', true),
            'email' => get_post_meta($post->ID, '_dsr_email', true),
            'full_name' => get_post_meta($post->ID, '_dsr_full_name', true),
            'additional_info' => get_post_meta($post->ID, '_dsr_additional_info', true),
            'ip_address' => get_post_meta($post->ID, '_dsr_ip_address', true),
            'user_agent' => get_post_meta($post->ID, '_dsr_user_agent', true),
            'status' => $post->post_status,
            'submitted_date' => get_post_meta($post->ID, '_dsr_submitted_date', true),
            'verified_date' => get_post_meta($post->ID, '_dsr_verified_date', true),
            'completed_date' => get_post_meta($post->ID, '_dsr_completed_date', true),
            'notes' => get_post_meta($post->ID, '_dsr_notes', true) ?: [],
        ];
    }
}
