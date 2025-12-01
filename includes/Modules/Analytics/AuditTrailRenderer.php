<?php
namespace ComplyFlow\Modules\Analytics;

use ComplyFlow\Modules\Analytics\AuditTrail;

if (!defined('ABSPATH')) {
    exit;
}

class AuditTrailRenderer {
    public static function render() {
        $logs = AuditTrail::get_recent();
        ?>
        <h2><?php esc_html_e('Audit Trail', 'complyflow'); ?></h2>
        <table class="widefat">
            <thead><tr><th><?php esc_html_e('Action', 'complyflow'); ?></th><th><?php esc_html_e('User ID', 'complyflow'); ?></th><th><?php esc_html_e('Details', 'complyflow'); ?></th><th><?php esc_html_e('Timestamp', 'complyflow'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo esc_html($log->action); ?></td>
                    <td><?php echo esc_html($log->user_id); ?></td>
                    <td><?php echo esc_html($log->details); ?></td>
                    <td><?php echo esc_html($log->timestamp); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
}
