<?php
namespace ComplyFlow\Modules\Forms;

if (!defined('ABSPATH')) {
    exit;
}

class ConsentLogRenderer {
    public static function render() {
        global $wpdb;
        $table = $wpdb->prefix . 'complyflow_consent';
        $logs = $wpdb->get_results("SELECT * FROM $table ORDER BY timestamp DESC LIMIT 100");
        ?>
        <h2><?php esc_html_e('Consent Logs', 'complyflow'); ?></h2>
        <table class="widefat">
            <thead><tr><th><?php esc_html_e('Form ID', 'complyflow'); ?></th><th><?php esc_html_e('User ID', 'complyflow'); ?></th><th><?php esc_html_e('Consent', 'complyflow'); ?></th><th><?php esc_html_e('Timestamp', 'complyflow'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo esc_html($log->form_id); ?></td>
                    <td><?php echo esc_html($log->user_id); ?></td>
                    <td><?php echo esc_html($log->consent_value); ?></td>
                    <td><?php echo esc_html($log->timestamp); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
}
