<?php
/**
 * Cookie Scan Debug Test
 * Access this file directly to test cookie scan functionality
 * URL: http://localhost/shahitest/wp-content/plugins/ShahiComplyFlow/test-cookie-scan.php
 */

// Load WordPress
require_once('../../../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to access this page.');
}

echo "<h1>Cookie Scan Debug Test</h1>";

// Test 1: Check if CookieModule is registered
echo "<h2>Test 1: Module Registration</h2>";
$module_manager = \ComplyFlow\Core\ModuleManager::get_instance();
$modules = $module_manager->get_modules();
$is_registered = isset($modules['inventory']);
echo "Cookie Module Registered: " . ($is_registered ? "✅ YES" : "❌ NO") . "<br>";

if ($is_registered) {
    echo "Module Config: <pre>" . print_r($modules['inventory'], true) . "</pre>";
}

// Test 2: Check if module is loaded
echo "<h2>Test 2: Module Loading</h2>";
$is_loaded = $module_manager->is_module_loaded('inventory');
echo "Cookie Module Loaded: " . ($is_loaded ? "✅ YES" : "❌ NO") . "<br>";

if ($is_loaded) {
    $cookie_module = $module_manager->get_module('inventory');
    echo "Module Instance: " . get_class($cookie_module) . "<br>";
}

// Test 3: Check if AJAX action is registered
echo "<h2>Test 3: AJAX Action Registration</h2>";
global $wp_filter;
$has_action = isset($wp_filter['wp_ajax_complyflow_scan_cookies']);
echo "AJAX Action Registered: " . ($has_action ? "✅ YES" : "❌ NO") . "<br>";

if ($has_action) {
    echo "Registered Callbacks: <pre>" . print_r($wp_filter['wp_ajax_complyflow_scan_cookies'], true) . "</pre>";
}

// Test 4: Test nonce generation
echo "<h2>Test 4: Nonce Generation</h2>";
$nonce = wp_create_nonce('complyflow_cookie_nonce');
echo "Generated Nonce: <code>" . esc_html($nonce) . "</code><br>";
$verify = wp_verify_nonce($nonce, 'complyflow_cookie_nonce');
echo "Nonce Verification: " . ($verify ? "✅ VALID" : "❌ INVALID") . "<br>";

// Test 5: Manually trigger scan
echo "<h2>Test 5: Manual Scan Test</h2>";
try {
    $settings = \ComplyFlow\Core\SettingsRepository::get_instance();
    $scanner = new \ComplyFlow\Modules\Cookie\CookieScanner($settings);
    $scanner->init();
    
    $url = home_url();
    echo "Scanning URL: <code>" . esc_html($url) . "</code><br>";
    
    $cookies = $scanner->scan_site($url);
    
    if (is_wp_error($cookies)) {
        echo "❌ Scan Error: " . $cookies->get_error_message() . "<br>";
    } elseif (is_array($cookies)) {
        echo "✅ Scan Successful!<br>";
        echo "Found " . count($cookies) . " cookies<br>";
        echo "<pre>" . print_r(array_slice($cookies, 0, 5), true) . "</pre>";
    } else {
        echo "❌ Unexpected result type: " . gettype($cookies) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

// Test 6: Check if table exists
echo "<h2>Test 6: Database Table</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'complyflow_cookies';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
echo "Table Exists: " . ($table_exists ? "✅ YES" : "❌ NO") . "<br>";

if ($table_exists) {
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Cookies in Database: " . $count . "<br>";
}

echo "<hr>";
echo "<p><a href='" . admin_url('admin.php?page=complyflow') . "'>← Back to Dashboard</a></p>";
