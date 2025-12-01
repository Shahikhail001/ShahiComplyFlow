<?php
namespace ComplyFlow\Modules\Forms;

use ComplyFlow\Modules\Forms\ConsentTextSettings;

if (!defined('ABSPATH')) {
    exit;
}

class ConsentSettingsRenderer {
    public static function render() {
        $consent_text = ConsentTextSettings::get_consent_text();
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('complyflow_consent_text_nonce', 'complyflow_consent_text_nonce'); ?>
            <h2><?php esc_html_e('Consent Checkbox Text', 'complyflow'); ?></h2>
            <textarea name="complyflow_consent_text" rows="3" class="large-text" required><?php echo esc_textarea($consent_text); ?></textarea>
            <br>
            <button type="submit" class="button button-primary">
                <?php esc_html_e('Save Consent Text', 'complyflow'); ?>
            </button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complyflow_consent_text_nonce']) && wp_verify_nonce($_POST['complyflow_consent_text_nonce'], 'complyflow_consent_text_nonce')) {
            $new_text = $_POST['complyflow_consent_text'] ?? '';
            ConsentTextSettings::set_consent_text($new_text);
            echo '<div class="notice notice-success"><p>' . esc_html__('Consent text updated.', 'complyflow') . '</p></div>';
        }
    }
}
