<?php
require_once '../../../wp-load.php';
global $wpdb;

$table = $wpdb->prefix . 'complyflow_compliance_history';
$record = $wpdb->get_row("SELECT * FROM $table ORDER BY created_at DESC LIMIT 1", ARRAY_A);

echo "=== Latest Compliance History Record ===\n";
print_r($record);
