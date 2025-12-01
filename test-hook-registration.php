<?php
/**
 * Test if hooks are actually registered
 */
require_once '../../../wp-load.php';

echo "=== Hook Registration Test ===\n\n";

// Simulate admin context
if (!defined('WP_ADMIN')) {
    define('WP_ADMIN', true);
}

// Force the plugin to initialize
do_action('plugins_loaded');

echo "Checking hooks after plugins_loaded:\n\n";

global $wp_filter;

$hooks_to_check = [
    'wp_ajax_complyflow_save_settings',
    'wp_ajax_complyflow_export_settings',
    'wp_ajax_complyflow_import_settings',
    'wp_ajax_complyflow_run_scan',
];

foreach ($hooks_to_check as $hook) {
    if (isset($wp_filter[$hook]) && !empty($wp_filter[$hook]->callbacks)) {
        echo "✓ $hook - REGISTERED\n";
        // Show what's registered
        foreach ($wp_filter[$hook]->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                if (is_array($callback['function'])) {
                    $class = is_object($callback['function'][0]) ? get_class($callback['function'][0]) : $callback['function'][0];
                    echo "    Priority $priority: $class::{$callback['function'][1]}\n";
                }
            }
        }
    } else {
        echo "✗ $hook - NOT REGISTERED\n";
    }
}

echo "\n=== Checking is_admin() ===\n";
echo "is_admin(): " . (is_admin() ? "TRUE" : "FALSE") . "\n";
echo "WP_ADMIN defined: " . (defined('WP_ADMIN') ? "TRUE" : "FALSE") . "\n";
