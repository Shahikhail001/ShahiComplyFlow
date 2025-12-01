<?php
/**
 * Insert Sample Data for Dashboard Details
 * 
 * Access: http://localhost/shahitest/wp-content/plugins/ShahiComplyFlow/insert-sample-data.php
 */

define('WP_USE_THEMES', false);
require_once(dirname(__FILE__) . '/../../../../wp-load.php');

// Check admin capability
if (!current_user_can('manage_options')) {
    wp_die('Permission denied. You must be an administrator.');
}

global $wpdb;
$dsr_table = $wpdb->prefix . 'complyflow_dsr_requests';
$consent_table = $wpdb->prefix . 'complyflow_consent_log';

echo '<h1>Inserting Sample Data</h1>';
echo '<style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 40px; } h2 { color: #2563eb; margin-top: 30px; } p { line-height: 1.6; } .success { color: #10b981; font-weight: 600; } .error { color: #ef4444; }</style>';

// Insert sample DSR requests
echo '<h2>1. DSR Requests</h2>';
echo '<p>Inserting 5 sample requests with different types...</p>';

$dsr_data = [
    ['user1@example.com', 'access', 'pending'],
    ['user2@example.com', 'deletion', 'verified'],
    ['user3@example.com', 'access', 'in_progress'],
    ['user4@example.com', 'portability', 'completed'],
    ['user5@example.com', 'rectification', 'pending'],
];

$inserted_dsr = 0;
foreach ($dsr_data as $data) {
    $result = $wpdb->insert(
        $dsr_table,
        [
            'email' => $data[0],
            'request_type' => $data[1],
            'status' => $data[2],
            'message' => 'Sample request',
            'created_at' => current_time('mysql'),
        ],
        ['%s', '%s', '%s', '%s', '%s']
    );
    if ($result) {
        $inserted_dsr++;
        echo "✅ {$data[1]} request ({$data[2]})<br>";
    }
}

echo "<p class='success'>✅ Inserted {$inserted_dsr} DSR requests</p>";

// Insert sample consent records
echo '<h2>2. Consent Records</h2>';
echo '<p>Inserting 6 sample consent records...</p>';

$consent_data = [
    ['visitor1', 'necessary', 1],
    ['visitor2', 'analytics', 1],
    ['visitor3', 'marketing', 1],
    ['visitor4', 'functional', 1],
    ['visitor5', 'necessary', 1],
    ['visitor6', 'analytics', 0], // Rejected
];

$inserted_consent = 0;
foreach ($consent_data as $data) {
    $result = $wpdb->insert(
        $consent_table,
        [
            'user_identifier' => $data[0],
            'category' => $data[1],
            'consent_given' => $data[2],
            'created_at' => current_time('mysql'),
        ],
        ['%s', '%s', '%d', '%s']
    );
    if ($result) {
        $inserted_consent++;
        $status = $data[2] ? 'accepted' : 'rejected';
        echo "✅ {$data[1]} - {$status}<br>";
    }
}

echo "<p class='success'>✅ Inserted {$inserted_consent} consent records</p>";

// Show summary
echo '<h2>3. Summary</h2>';
echo '<ul>';
echo '<li><strong>DSR Requests:</strong> 2 Access, 1 Deletion, 1 Portability, 1 Rectification</li>';
echo '<li><strong>Consents:</strong> 2 Necessary, 2 Analytics, 1 Marketing, 1 Functional</li>';
echo '<li><strong>Acceptance Rate:</strong> 83.3% (5 accepted, 1 rejected)</li>';
echo '</ul>';

echo '<h2>✅ All Done!</h2>';
echo '<p><a href="' . admin_url('admin.php?page=complyflow') . '" style="display: inline-block; background: #2563eb; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">← View Dashboard with Details</a></p>';
