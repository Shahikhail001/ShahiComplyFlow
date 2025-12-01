<?php
namespace ComplyFlow\Modules\Forms;

if (!defined('ABSPATH')) {
    exit;
}

class RetentionManager {
    /**
     * Set retention period for a form (in days)
     */
    public static function set_retention($form_id, $days) {
        $retentions = get_option('complyflow_form_retention', []);
        $retentions[$form_id] = intval($days);
        update_option('complyflow_form_retention', $retentions);
    }

    /**
     * Get retention period for a form
     */
    public static function get_retention($form_id) {
        $retentions = get_option('complyflow_form_retention', []);
        return $retentions[$form_id] ?? 0;
    }

    /**
     * Delete or anonymize entries past retention
     */
    public static function enforce_retention() {
        // Example for WPForms
        if (class_exists('WPForms')) {
            $forms = \WPForms\Forms\Loader::get();
            foreach ($forms as $form) {
                $form_id = $form['id'];
                $days = self::get_retention($form_id);
                if ($days > 0) {
                    $entries = \WPForms_Entry_Model::get_entries(['form_id' => $form_id]);
                    foreach ($entries as $entry) {
                        $date = strtotime($entry->date);
                        if ($date < strtotime('-' . $days . ' days')) {
                            // Delete or anonymize
                            \WPForms_Entry_Model::delete($entry->entry_id);
                        }
                    }
                }
            }
        }
        // Extend for other plugins as needed
    }
}
