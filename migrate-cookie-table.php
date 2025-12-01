<?php
/**
 * Database Migration: Add is_manual column to cookies table
 * 
 * Run this file once to update the database schema
 * Access: http://localhost/shahitest/wp-content/plugins/ShahiComplyFlow/migrate-cookie-table.php
 */

// Load WordPress
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/../../../../wp-load.php');

// Check admin capability
if (!current_user_can('manage_options')) {
    wp_die('Permission denied. You must be an administrator to run database migrations.');
}

global $wpdb;
$table_name = $wpdb->prefix . 'complyflow_cookies';

echo '<h1>ComplyFlow Database Migration</h1>';
echo '<p><strong>Table:</strong> ' . $table_name . '</p>';

// Check if table exists
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

if (!$table_exists) {
    echo '<p style="color: red;">❌ Table does not exist. Please activate the plugin first.</p>';
    exit;
}

echo '<p style="color: green;">✅ Table exists</p>';

// Check current schema
echo '<h2>Current Table Schema:</h2>';
$columns = $wpdb->get_results("DESCRIBE $table_name");
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
foreach ($columns as $column) {
    echo '<tr>';
    echo '<td>' . $column->Field . '</td>';
    echo '<td>' . $column->Type . '</td>';
    echo '<td>' . $column->Null . '</td>';
    echo '<td>' . $column->Key . '</td>';
    echo '<td>' . $column->Default . '</td>';
    echo '</tr>';
}
echo '</table>';

// Check if is_manual column exists
$has_is_manual = false;
foreach ($columns as $column) {
    if ($column->Field === 'is_manual') {
        $has_is_manual = true;
        break;
    }
}

if ($has_is_manual) {
    echo '<p style="color: green;">✅ Column <code>is_manual</code> already exists. No migration needed.</p>';
} else {
    echo '<h2>Adding Missing Column:</h2>';
    echo '<p>Adding <code>is_manual</code> column...</p>';
    
    $sql = "ALTER TABLE $table_name ADD COLUMN is_manual tinyint(1) DEFAULT 0 AFTER expiry";
    $result = $wpdb->query($sql);
    
    if ($result === false) {
        echo '<p style="color: red;">❌ Failed to add column: ' . $wpdb->last_error . '</p>';
    } else {
        echo '<p style="color: green;">✅ Column <code>is_manual</code> added successfully!</p>';
    }
}

// Check if source column exists (also part of v4.6.0)
$has_source = false;
foreach ($columns as $column) {
    if ($column->Field === 'source') {
        $has_source = true;
        break;
    }
}

if (!$has_source) {
    echo '<p>Adding <code>source</code> column...</p>';
    $sql = "ALTER TABLE $table_name ADD COLUMN source varchar(50) DEFAULT 'scanner' AFTER is_manual";
    $result = $wpdb->query($sql);
    
    if ($result === false) {
        echo '<p style="color: red;">❌ Failed to add column: ' . $wpdb->last_error . '</p>';
    } else {
        echo '<p style="color: green;">✅ Column <code>source</code> added successfully!</p>';
    }
}

// Show updated schema
echo '<h2>Updated Table Schema:</h2>';
$columns = $wpdb->get_results("DESCRIBE $table_name");
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
foreach ($columns as $column) {
    echo '<tr>';
    echo '<td>' . $column->Field . '</td>';
    echo '<td>' . $column->Type . '</td>';
    echo '<td>' . $column->Null . '</td>';
    echo '<td>' . $column->Key . '</td>';
    echo '<td>' . $column->Default . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<h2>✅ Migration Complete!</h2>';
echo '<p>You can now close this page and return to the dashboard.</p>';
echo '<p><a href="' . admin_url('admin.php?page=complyflow') . '">← Back to ComplyFlow Dashboard</a></p>';
