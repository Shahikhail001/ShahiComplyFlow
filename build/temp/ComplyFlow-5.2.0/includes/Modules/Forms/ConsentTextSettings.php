<?php
namespace ComplyFlow\Modules\Forms;

if (!defined('ABSPATH')) {
    exit;
}

class ConsentTextSettings {
    public static function get_consent_text() {
        return get_option('complyflow_consent_text', __('I consent to the collection and processing of my data in accordance with the Privacy Policy.', 'complyflow'));
    }
    public static function set_consent_text($text) {
        update_option('complyflow_consent_text', sanitize_text_field($text));
    }
}
