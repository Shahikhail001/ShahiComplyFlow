<?php
namespace ComplyFlow\Modules\Forms;

if (!defined('ABSPATH')) {
    exit;
}

class ConsentLogger {
    /**
     * Log consent for a form submission
     */
    public static function log($form_id, $user_id, $consent_value, $timestamp = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'complyflow_consent';
        if (!$timestamp) {
            $timestamp = current_time('mysql');
        }
        $wpdb->insert($table, [
            'form_id' => $form_id,
            'user_id' => $user_id,
            'consent_value' => $consent_value,
            'timestamp' => $timestamp,
        ]);
    }
}
