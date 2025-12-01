<?php
namespace ComplyFlow\Modules\Analytics;

if (!defined('ABSPATH')) {
    exit;
}

class ComplianceScore {
    /**
     * Calculate overall compliance score (0-100%)
     * @return int
     */
    public static function calculate() {
        $score = 100;
        // Example: deduct points for missing consent, retention, vendor DPA, etc.
        // Check forms
        $forms = class_exists('WPForms') ? \WPForms\Forms\Loader::get() : [];
        foreach ($forms as $form) {
            $form_id = $form['id'];
            // Consent checkbox
            $has_consent = false;
            foreach ($form['fields'] ?? [] as $field) {
                if ($field['type'] === 'checkbox' && stripos($field['label'], 'consent') !== false) {
                    $has_consent = true;
                }
            }
            if (!$has_consent) {
                $score -= 10;
            }
            // Retention
            $retention = get_option('complyflow_form_retention', []);
            if (empty($retention[$form_id])) {
                $score -= 5;
            }
        }
        // Check vendors
        $vendors = get_option('complyflow_vendor_inventory', []);
        foreach ($vendors as $vendor) {
            $dpa = get_option('complyflow_vendor_dpa', []);
            if (empty($dpa[$vendor['name']])) {
                $score -= 5;
            }
        }
        // Minimum score
        return max(0, $score);
    }
}
