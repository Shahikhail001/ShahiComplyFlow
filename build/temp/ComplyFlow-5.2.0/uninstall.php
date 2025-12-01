<?php
/**
 * Uninstall Script
 *
 * Fired when the plugin is uninstalled.
 * Removes all plugin data, options, and database tables.
 *
 * @package ComplyFlow
 * @since 1.0.0
 */

// Exit if accessed directly or not in uninstall context
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove all plugin options
 */
function complyflow_remove_options(): void {
    global $wpdb;

    // Delete all options
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'complyflow_%'");
    
    // Delete all transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_complyflow_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_complyflow_%'");
}

/**
 * Remove all plugin database tables
 */
function complyflow_remove_tables(): void {
    global $wpdb;

    $tables = [
        $wpdb->prefix . 'complyflow_consent',
        $wpdb->prefix . 'complyflow_dsr',
        $wpdb->prefix . 'complyflow_scan_results',
        $wpdb->prefix . 'complyflow_tracker_inventory',
    ];

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
}

/**
 * Remove all plugin user meta
 */
function complyflow_remove_user_meta(): void {
    global $wpdb;

    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'complyflow_%'");
}

/**
 * Remove scheduled cron jobs
 */
function complyflow_remove_cron_jobs(): void {
    $cron_jobs = [
        'complyflow_scan_scheduled',
        'complyflow_dsr_reminder',
        'complyflow_consent_cleanup',
        'complyflow_legal_update_check',
    ];

    foreach ($cron_jobs as $hook) {
        wp_clear_scheduled_hook($hook);
    }
}

/**
 * Remove custom capabilities
 */
function complyflow_remove_capabilities(): void {
    $roles = ['administrator', 'editor'];
    $capabilities = [
        'manage_complyflow',
        'view_dsr_requests',
        'process_dsr_requests',
    ];

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            foreach ($capabilities as $cap) {
                $role->remove_cap($cap);
            }
        }
    }
}

/**
 * Remove uploaded files
 */
function complyflow_remove_uploads(): void {
    $upload_dir = wp_upload_dir();
    $complyflow_dir = $upload_dir['basedir'] . '/complyflow';

    if (is_dir($complyflow_dir)) {
        complyflow_recursive_rmdir($complyflow_dir);
    }
}

/**
 * Recursively remove directory
 *
 * @param string $dir Directory path.
 * @return bool
 */
function complyflow_recursive_rmdir(string $dir): bool {
    if (!is_dir($dir)) {
        return false;
    }

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $item;
        
        if (is_dir($path)) {
            complyflow_recursive_rmdir($path);
        } else {
            unlink($path);
        }
    }

    return rmdir($dir);
}

/**
 * Main uninstall routine
 * Only proceed if the user explicitly opted in to delete all data
 */
if (defined('WP_UNINSTALL_PLUGIN')) {
    // Check if user wants to delete data on uninstall
    // First check the settings class option, then fallback to direct option
    $settings = get_option('complyflow_settings', []);
    $delete_data = isset($settings['delete_data_on_uninstall']) ? 
        $settings['delete_data_on_uninstall'] : 
        get_option('complyflow_delete_data_on_uninstall', false);
    
    if (!$delete_data) {
        // User wants to preserve data - exit without deleting anything
        // This ensures no data loss if plugin is accidentally uninstalled
        return;
    }
    
    // User explicitly opted in to data deletion - proceed with cleanup
    
    // Remove options
    complyflow_remove_options();

    // Remove database tables
    complyflow_remove_tables();

    // Remove user meta
    complyflow_remove_user_meta();

    // Remove cron jobs
    complyflow_remove_cron_jobs();

    // Remove capabilities
    complyflow_remove_capabilities();

    // Remove uploaded files
    complyflow_remove_uploads();

    // Clear any cached data
    wp_cache_flush();
}
