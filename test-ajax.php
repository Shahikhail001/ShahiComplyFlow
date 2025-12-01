<?php
/**
 * Test AJAX endpoint
 * Access: http://localhost/shahitest/wp-admin/admin-ajax.php?action=complyflow_test_ajax
 */

// This file tests if AJAX actions are working

// Add a simple test action
add_action('wp_ajax_complyflow_test_ajax', function() {
    error_log('ComplyFlow: Test AJAX called successfully!');
    wp_send_json_success(['message' => 'Test AJAX is working!']);
});

// Test with nonce
add_action('wp_ajax_complyflow_test_nonce', function() {
    error_log('ComplyFlow: Test nonce AJAX called');
    $nonce = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '';
    error_log('ComplyFlow: Received nonce: ' . $nonce);
    
    if (!wp_verify_nonce($nonce, 'complyflow_test_nonce')) {
        error_log('ComplyFlow: Nonce verification FAILED');
        wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        return;
    }
    
    error_log('ComplyFlow: Nonce verification SUCCESS');
    wp_send_json_success(['message' => 'Nonce test passed!']);
});
