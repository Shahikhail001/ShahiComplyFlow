<?php
/**
 * Test Settings Save - Check browser console and nonce
 */
require_once '../../../wp-load.php';

echo "=== Settings Save Debug ===\n\n";

// Check if nonce is being generated
echo "Admin Nonce: " . wp_create_nonce('complyflow_admin_nonce') . "\n";
echo "Settings Save Nonce: " . wp_create_nonce('complyflow_settings_save') . "\n\n";

// Check JavaScript localization
echo "JavaScript should have:\n";
echo "  complyflowAdmin.ajaxUrl = " . admin_url('admin-ajax.php') . "\n";
echo "  complyflowAdmin.nonce = [admin_nonce]\n\n";

// Check if AJAX handler is registered
echo "AJAX Handlers:\n";
global $wp_filter;
if (isset($wp_filter['wp_ajax_complyflow_save_settings'])) {
    echo "  ✓ wp_ajax_complyflow_save_settings is registered\n";
} else {
    echo "  ✗ wp_ajax_complyflow_save_settings NOT registered\n";
}

// Check current settings
$settings = get_option('complyflow_settings', []);
echo "\nCurrent Settings Count: " . count($settings) . "\n";
echo "Sample settings:\n";
echo "  compliance_history_schedule: " . ($settings['compliance_history_schedule'] ?? 'not set') . "\n";
echo "  accessibility_auto_scan: " . ($settings['accessibility_auto_scan'] ?? 'not set') . "\n";
