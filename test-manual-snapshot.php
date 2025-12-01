<?php
require_once '../../../wp-load.php';

echo "=== Manual Snapshot Test ===\n\n";

$scheduler = new \ComplyFlow\Core\ComplianceHistoryScheduler();

echo "Taking snapshot...\n";
$result = $scheduler->force_snapshot();

echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "\n\n";

// Check the database
global $wpdb;
$table = $wpdb->prefix . 'complyflow_compliance_history';
$count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
echo "Total records in table: $count\n\n";

if ($count > 0) {
    echo "Latest record:\n";
    $record = $wpdb->get_row("SELECT * FROM $table ORDER BY recorded_at DESC LIMIT 1", ARRAY_A);
    print_r($record);
}
