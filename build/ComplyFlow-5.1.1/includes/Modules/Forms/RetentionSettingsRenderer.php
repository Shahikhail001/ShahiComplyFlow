<?php
namespace ComplyFlow\Modules\Forms;

use ComplyFlow\Modules\Forms\RetentionManager;

if (!defined('ABSPATH')) {
    exit;
}

class RetentionSettingsRenderer {
    public static function render() {
        // Example: WPForms only for now
        $forms = class_exists('WPForms') ? \WPForms\Forms\Loader::get() : [];
        ?>
        <h2><?php esc_html_e('Form Data Retention Settings', 'complyflow'); ?></h2>
        <form method="post" action="">
            <?php wp_nonce_field('complyflow_retention_settings_nonce', 'complyflow_retention_settings_nonce'); ?>
            <table class="widefat">
                <thead><tr><th><?php esc_html_e('Form', 'complyflow'); ?></th><th><?php esc_html_e('Retention Period (days)', 'complyflow'); ?></th></tr></thead>
                <tbody>
                <?php foreach ($forms as $form):
                    $form_id = $form['id'];
                    $title = $form['name'] ?? 'Untitled';
                    $retention = RetentionManager::get_retention($form_id);
                ?>
                <tr>
                    <td><?php echo esc_html($title); ?></td>
                    <td><input type="number" name="retention[<?php echo esc_attr($form_id); ?>]" value="<?php echo esc_attr($retention); ?>" min="0" style="width:80px;"></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <button type="submit" class="button button-primary"><?php esc_html_e('Save Retention Settings', 'complyflow'); ?></button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complyflow_retention_settings_nonce']) && wp_verify_nonce($_POST['complyflow_retention_settings_nonce'], 'complyflow_retention_settings_nonce')) {
            $retentions = $_POST['retention'] ?? [];
            foreach ($retentions as $form_id => $days) {
                RetentionManager::set_retention($form_id, $days);
            }
            echo '<div class="notice notice-success"><p>' . esc_html__('Retention settings updated.', 'complyflow') . '</p></div>';
        }
    }
}
