<?php
/**
 * Check compliance history table status
 */

require_once '../../../wp-load.php';
global $wpdb;

$table_name = $wpdb->prefix . 'complyflow_compliance_history';

echo "=== Compliance History Table Check ===\n\n";

// Check if table exists
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
echo "Table exists: " . ($table_exists ? "YES" : "NO") . "\n";

if ($table_exists) {
    // Get record count
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Total records: $count\n\n";
    
    if ($count > 0) {
        // Get first 5 records
        echo "First 5 records:\n";
        $records = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 5", ARRAY_A);
        foreach ($records as $record) {
            echo "  ID: {$record['id']}, Date: {$record['created_at']}, GDPR: {$record['gdpr_score']}, WCAG: {$record['wcag_score']}, CCPA: {$record['ccpa_score']}\n";
        }
    } else {
        echo "No records found in table.\n";
    }
} else {
    echo "Table does not exist. Plugin may need reactivation.\n";
}

echo "\n=== Settings Check ===\n";
$schedule = get_option('complyflow_compliance_history_schedule', 'not set');
echo "Compliance History Schedule: $schedule\n";

echo "\n=== Cron Check ===\n";
$cron_jobs = _get_cron_array();
$found_job = false;
foreach ($cron_jobs as $timestamp => $jobs) {
    if (isset($jobs['complyflow_compliance_snapshot'])) {
        $found_job = true;
        echo "Cron job scheduled: YES\n";
        echo "Next run: " . date('Y-m-d H:i:s', $timestamp) . "\n";
        break;
    }
}
if (!$found_job) {
    echo "Cron job scheduled: NO\n";
}
