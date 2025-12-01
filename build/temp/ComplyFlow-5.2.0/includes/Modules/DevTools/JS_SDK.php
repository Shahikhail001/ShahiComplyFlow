<?php
namespace ComplyFlow\Modules\DevTools;

if (!defined('ABSPATH')) {
    exit;
}

class JS_SDK {
    /**
     * Output JS SDK for custom compliance integrations
     */
    public static function output_sdk() {
        ?>
        <script>
        window.ComplyFlow = {
            logConsent: function(formId, consentValue) {
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=complyflow_log_consent&form_id=' + encodeURIComponent(formId) + '&consent_value=' + encodeURIComponent(consentValue)
                });
            },
            getComplianceScore: function(callback) {
                fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=complyflow_get_score')
                    .then(res => res.json())
                    .then(data => callback(data.score));
            }
        };
        </script>
        <?php
    }
}
