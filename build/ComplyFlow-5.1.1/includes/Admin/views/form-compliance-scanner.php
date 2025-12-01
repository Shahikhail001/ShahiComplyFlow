<?php
/**
 * Form Compliance Scanner Admin View
 *
 * @package ComplyFlow\Admin\Views
 * @since 3.3.1
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap complyflow-admin">
    <h1><?php esc_html_e('Form Compliance Scanner', 'complyflow'); ?></h1>
    <p class="description"><?php esc_html_e('Scan all forms for GDPR/CCPA compliance issues. Missing consent checkboxes, privacy notices, and required fields will be highlighted.', 'complyflow'); ?></p>
    <div id="complyflow-form-scan-results">
        <!-- Results will be loaded here via AJAX -->
    </div>
    <button type="button" class="button button-primary" id="complyflow-scan-forms">
        <span class="dashicons dashicons-search"></span>
        <?php esc_html_e('Scan Forms', 'complyflow'); ?>
    </button>
</div>
<script>
jQuery(document).ready(function($) {
    $('#complyflow-scan-forms').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> <?php esc_html_e('Scanning...', 'complyflow'); ?>');
        $.post(ajaxurl, {
            action: 'complyflow_scan_forms',
            nonce: '<?php echo esc_js(wp_create_nonce('complyflow_scan_forms_nonce')); ?>'
        }, function(response) {
            if (response.success) {
                $('#complyflow-form-scan-results').html(response.data.html);
            } else {
                $('#complyflow-form-scan-results').html('<div class="notice notice-error"><p>' + (response.data.message || 'Scan failed') + '</p></div>');
            }
            $btn.prop('disabled', false).html('<span class="dashicons dashicons-search"></span> <?php esc_html_e('Scan Forms', 'complyflow'); ?>');
        });
    });
});
</script>
<style>
.complyflow-form-issue { color: #d63638; font-weight: bold; }
.complyflow-form-ok { color: #00a32a; font-weight: bold; }
</style>
