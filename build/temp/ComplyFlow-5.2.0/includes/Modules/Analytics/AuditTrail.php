<?php
namespace ComplyFlow\Modules\Analytics;

if (!defined('ABSPATH')) {
    exit;
}

class AuditTrail {
    /**
     * Log an audit event
     */
    public static function log($action, $user_id = null, $details = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'complyflow_audit';
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $wpdb->insert($table, [
            'action' => $action,
            'user_id' => $user_id,
            'details' => $details,
            'timestamp' => current_time('mysql'),
        ]);
    }

    /**
     * Get recent audit events
     */
    public static function get_recent($limit = 100) {
        global $wpdb;
        $table = $wpdb->prefix . 'complyflow_audit';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY timestamp DESC LIMIT %d", $limit));
    }
}
