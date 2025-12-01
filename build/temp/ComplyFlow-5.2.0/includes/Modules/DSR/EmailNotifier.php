<?php
/**
 * DSR Email Notifier
 *
 * @package ComplyFlow\Modules\DSR
 * @since   3.1.3
 */

namespace ComplyFlow\Modules\DSR;

use ComplyFlow\Core\SettingsRepository;

class EmailNotifier {
    
    private SettingsRepository $settings;

    public function __construct(SettingsRepository $settings) {
        $this->settings = $settings;
    }

    public function init(): void {
        add_filter('wp_mail_content_type', [$this, 'set_html_content_type']);
    }

    public function set_html_content_type(): string {
        return 'text/html';
    }

    public function send_verification_email(int $request_id): bool {
        $request_handler = new RequestHandler($this->settings);
        $request_data = $request_handler->get_request_data($request_id);

        if (is_wp_error($request_data)) {
            return false;
        }

        $token = get_post_meta($request_id, '_dsr_verification_token', true);
        $verification_url = add_query_arg('complyflow_verify_dsr', $token, home_url('/'));

        $subject = sprintf(__('[%s] Verify Your Data Subject Rights Request', 'complyflow'), get_bloginfo('name'));

        $template_data = [
            'name' => $request_data['full_name'],
            'request_type' => ucfirst($request_data['type']),
            'verification_link' => $verification_url,
            'site_name' => get_bloginfo('name'),
            'site_url' => home_url(),
        ];

        $message = $this->get_email_template('verification', $template_data);

        return wp_mail($request_data['email'], $subject, $message);
    }

    public function send_status_change_notification(int $request_id, string $old_status, string $new_status): bool {
        $request_handler = new RequestHandler($this->settings);
        $request_data = $request_handler->get_request_data($request_id);

        if (is_wp_error($request_data)) {
            return false;
        }

        $template_name = match($new_status) {
            'dsr_in_progress' => 'request-approved',
            'dsr_completed' => 'request-completed',
            'dsr_rejected' => 'request-rejected',
            default => null,
        };

        if (!$template_name) {
            return false;
        }

        $subject = sprintf(__('[%s] Update on Your Data Request', 'complyflow'), get_bloginfo('name'));

        $template_data = [
            'name' => $request_data['full_name'],
            'request_type' => ucfirst($request_data['type']),
            'request_id' => $request_id,
            'site_name' => get_bloginfo('name'),
            'site_url' => home_url(),
            'status' => $this->get_status_label($new_status),
        ];

        if ($new_status === 'dsr_completed') {
            $export_url = get_post_meta($request_id, '_dsr_export_url', true);
            if ($export_url) {
                $template_data['download_link'] = $export_url;
            }
        }

        $message = $this->get_email_template($template_name, $template_data);

        return wp_mail($request_data['email'], $subject, $message);
    }

    public function send_admin_notification(int $request_id): bool {
        $request_handler = new RequestHandler($this->settings);
        $request_data = $request_handler->get_request_data($request_id);

        if (is_wp_error($request_data)) {
            return false;
        }

        $admin_email = get_option('admin_email');
        $admin_url = admin_url('admin.php?page=complyflow-dsr&request_id=' . $request_id);

        $subject = sprintf(__('[%s] New Verified DSR Request Requires Action', 'complyflow'), get_bloginfo('name'));

        $template_data = [
            'request_id' => $request_id,
            'request_type' => ucfirst($request_data['type']),
            'requester_name' => $request_data['full_name'],
            'requester_email' => $request_data['email'],
            'submitted_date' => $request_data['submitted_date'],
            'admin_link' => $admin_url,
            'site_name' => get_bloginfo('name'),
        ];

        $message = $this->get_email_template('admin-new-request', $template_data);

        return wp_mail($admin_email, $subject, $message);
    }

    private function get_email_template(string $template_name, array $data): string {
        $template_path = COMPLYFLOW_PATH . "templates/emails/{$template_name}.php";

        if (file_exists($template_path)) {
            ob_start();
            extract($data);
            include $template_path;
            return ob_get_clean();
        }

        return $this->get_default_template($template_name, $data);
    }

    private function get_default_template(string $template_name, array $data): string {
        $header = $this->get_email_header();
        $footer = $this->get_email_footer();
        $body = '';

        switch ($template_name) {
            case 'verification':
                $body = sprintf(
                    '<h2>%s</h2><p>%s,</p><p>%s</p><p style="text-align: center; margin: 30px 0;"><a href="%s" style="background-color: #0073aa; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block;">%s</a></p><p><strong>%s:</strong> %s<br><strong>%s:</strong> %s</p><p style="color: #666; font-size: 14px;">%s</p>',
                    __('Verify Your Data Request', 'complyflow'),
                    esc_html($data['name']),
                    __('Thank you for submitting a data subject rights request. Please verify your email address by clicking the button below:', 'complyflow'),
                    esc_url($data['verification_link']),
                    __('Verify Email Address', 'complyflow'),
                    __('Request Type', 'complyflow'),
                    esc_html($data['request_type']),
                    __('Email', 'complyflow'),
                    esc_html($data['name']),
                    __('This verification link will expire in 24 hours. If you did not make this request, please ignore this email.', 'complyflow')
                );
                break;

            case 'request-approved':
                $body = sprintf(
                    '<h2>%s</h2><p>%s,</p><p>%s</p><p><strong>%s:</strong> %s<br><strong>%s:</strong> #%d</p><p>%s</p>',
                    __('Your Request Has Been Approved', 'complyflow'),
                    esc_html($data['name']),
                    __('We are writing to inform you that your data subject rights request has been approved and is now being processed.', 'complyflow'),
                    __('Request Type', 'complyflow'),
                    esc_html($data['request_type']),
                    __('Request ID', 'complyflow'),
                    $data['request_id'],
                    __('We will notify you once your request has been completed.', 'complyflow')
                );
                break;

            case 'request-completed':
                $download_section = '';
                if (isset($data['download_link'])) {
                    $download_section = sprintf(
                        '<p style="text-align: center; margin: 30px 0;"><a href="%s" style="background-color: #00a32a; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block;">%s</a></p>',
                        esc_url($data['download_link']),
                        __('Download Your Data', 'complyflow')
                    );
                }

                $body = sprintf(
                    '<h2>%s</h2><p>%s,</p><p>%s</p><p><strong>%s:</strong> %s<br><strong>%s:</strong> #%d</p>%s<p>%s</p>',
                    __('Your Request Has Been Completed', 'complyflow'),
                    esc_html($data['name']),
                    __('We are pleased to inform you that your data subject rights request has been completed.', 'complyflow'),
                    __('Request Type', 'complyflow'),
                    esc_html($data['request_type']),
                    __('Request ID', 'complyflow'),
                    $data['request_id'],
                    $download_section,
                    __('Thank you for your patience. If you have any questions, please contact us.', 'complyflow')
                );
                break;

            case 'request-rejected':
                $body = sprintf(
                    '<h2>%s</h2><p>%s,</p><p>%s</p><p><strong>%s:</strong> %s<br><strong>%s:</strong> #%d</p><p>%s</p>',
                    __('Your Request Status Update', 'complyflow'),
                    esc_html($data['name']),
                    __('We regret to inform you that we are unable to process your data subject rights request at this time.', 'complyflow'),
                    __('Request Type', 'complyflow'),
                    esc_html($data['request_type']),
                    __('Request ID', 'complyflow'),
                    $data['request_id'],
                    __('If you believe this is an error or would like more information, please contact us.', 'complyflow')
                );
                break;

            case 'admin-new-request':
                $body = sprintf(
                    '<h2>%s</h2><p>%s</p><p><strong>%s:</strong> #%d<br><strong>%s:</strong> %s<br><strong>%s:</strong> %s<br><strong>%s:</strong> %s<br><strong>%s:</strong> %s</p><p style="text-align: center; margin: 30px 0;"><a href="%s" style="background-color: #0073aa; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block;">%s</a></p>',
                    __('New DSR Request Requires Action', 'complyflow'),
                    __('A new data subject rights request has been verified and is awaiting your review.', 'complyflow'),
                    __('Request ID', 'complyflow'),
                    $data['request_id'],
                    __('Type', 'complyflow'),
                    esc_html($data['request_type']),
                    __('Requester', 'complyflow'),
                    esc_html($data['requester_name']),
                    __('Email', 'complyflow'),
                    esc_html($data['requester_email']),
                    __('Submitted', 'complyflow'),
                    esc_html($data['submitted_date']),
                    esc_url($data['admin_link']),
                    __('View Request', 'complyflow')
                );
                break;
        }

        return $header . $body . $footer;
    }

    private function get_email_header(): string {
        return sprintf(
            '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>%s</title></head><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;"><div style="background-color: #f4f4f4; padding: 20px; border-radius: 8px;"><div style="background-color: white; padding: 30px; border-radius: 4px;">',
            get_bloginfo('name')
        );
    }

    private function get_email_footer(): string {
        return sprintf(
            '</div><div style="text-align: center; padding: 20px; font-size: 12px; color: #666;"><p>%s<br><a href="%s" style="color: #0073aa;">%s</a></p><p>%s</p></div></div></body></html>',
            get_bloginfo('name'),
            home_url(),
            home_url(),
            __('This is an automated message. Please do not reply to this email.', 'complyflow')
        );
    }

    private function get_status_label(string $status): string {
        return match($status) {
            'dsr_pending' => __('Pending Verification', 'complyflow'),
            'dsr_verified' => __('Verified', 'complyflow'),
            'dsr_in_progress' => __('In Progress', 'complyflow'),
            'dsr_completed' => __('Completed', 'complyflow'),
            'dsr_rejected' => __('Rejected', 'complyflow'),
            default => $status,
        };
    }
}
