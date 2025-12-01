<?php
// Table creation for consent logs
register_activation_hook(__FILE__, function() {
    global $wpdb;
    $table = $wpdb->prefix . 'complyflow_consent';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        form_id varchar(64) NOT NULL,
        user_id varchar(64) DEFAULT '',
        consent_value varchar(16) NOT NULL,
        timestamp datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});
