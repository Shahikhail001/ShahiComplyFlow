<?php
/**
 * Plugin Deactivator
 *
 * Fired during plugin deactivation.
 *
 * @package ComplyFlow\Core
 * @since 1.0.0
 */

namespace ComplyFlow\Core;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Deactivator Class
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since 1.0.0
 */
class Deactivator {
    /**
     * Deactivate the plugin
     *
     * Clears scheduled events and flushes rewrite rules.
     * Note: Does NOT delete data - that happens in uninstall.php
     *
     * @return void
     */
    public static function deactivate(): void {
        // Clear scheduled cron jobs
        self::clear_scheduled_events();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Clear any transients
        self::clear_transients();

        /**
         * Fires after ComplyFlow deactivation
         *
         * @since 1.0.0
         */
        do_action('complyflow_deactivated');
    }

    /**
     * Clear all scheduled events
     *
     * @return void
     */
    private static function clear_scheduled_events(): void {
        $cron_hooks = [
            'complyflow_scan_scheduled',
            'complyflow_dsr_reminder',
            'complyflow_consent_cleanup',
            'complyflow_legal_update_check',
        ];

        foreach ($cron_hooks as $hook) {
            $timestamp = wp_next_scheduled($hook);
            if ($timestamp) {
                wp_unschedule_event($timestamp, $hook);
            }
            
            // Clear all instances of this hook
            wp_clear_scheduled_hook($hook);
        }
    }

    /**
     * Clear plugin transients
     *
     * @return void
     */
    private static function clear_transients(): void {
        global $wpdb;

        // Delete all ComplyFlow transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_complyflow_%' 
            OR option_name LIKE '_transient_timeout_complyflow_%'"
        );

        // Clear object cache if available
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
}
