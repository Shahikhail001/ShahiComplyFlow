<?php
/**
 * Test AJAX save settings endpoint directly
 */

// Simulate AJAX request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['action'] = 'complyflow_save_settings';
$_POST['nonce'] = ''; // Will be set below
$_POST['settings'] = 'complyflow_settings[test]=value';

// Load WordPress
require_once '../../../wp-load.php';

// Set admin context
if (!defined('WP_ADMIN')) {
    define('WP_ADMIN', true);
}
if (!defined('DOING_AJAX')) {
    define('DOING_AJAX', true);
}

// Create nonce
$_POST['nonce'] = wp_create_nonce('complyflow_admin_nonce');

echo "=== Testing AJAX Endpoint Directly ===\n\n";
echo "Action: complyflow_save_settings\n";
echo "Nonce: {$_POST['nonce']}\n";
echo "is_admin(): " . (is_admin() ? "YES" : "NO") . "\n";
echo "DOING_AJAX: " . (defined('DOING_AJAX') && DOING_AJAX ? "YES" : "NO") . "\n\n";

// Check if action exists
global $wp_filter;
if (isset($wp_filter['wp_ajax_complyflow_save_settings'])) {
    echo "✓ Hook 'wp_ajax_complyflow_save_settings' is registered\n\n";
    
    echo "Triggering action...\n";
    do_action('wp_ajax_complyflow_save_settings');
    
} else {
    echo "✗ Hook 'wp_ajax_complyflow_save_settings' is NOT registered\n\n";
    
    echo "Registered AJAX hooks:\n";
    foreach ($wp_filter as $hook_name => $hook_obj) {
        if (strpos($hook_name, 'wp_ajax_complyflow') === 0) {
            echo "  - $hook_name\n";
        }
    }
}
