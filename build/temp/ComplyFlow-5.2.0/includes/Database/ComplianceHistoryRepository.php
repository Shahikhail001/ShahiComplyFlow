<?php
/**
 * Compliance History Repository
 *
 * Manages storage and retrieval of historical compliance data.
 *
 * @package ComplyFlow\Database
 * @since 4.8.0
 */

namespace ComplyFlow\Database;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Compliance History Repository Class
 *
 * @since 4.8.0
 */
class ComplianceHistoryRepository {
    
    /**
     * Table name
     *
     * @var string
     */
    private string $table_name;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'complyflow_compliance_history';
    }

    /**
     * Save a compliance snapshot
     *
     * @param array $data {
     *     @type int    $compliance_score           Overall compliance score (0-100)
     *     @type array  $module_scores              Module breakdown scores
     *     @type int    $accessibility_issues       Total accessibility issues
     *     @type int    $dsr_pending_count         Pending DSR requests
     *     @type float  $consent_acceptance_rate   Consent acceptance rate
     *     @type int    $cookie_count              Total cookies detected
     * }
     * @return int|false Insert ID on success, false on failure
     */
    public function save_snapshot(array $data) {
        global $wpdb;

        // Validate required fields
        if (!isset($data['compliance_score'])) {
            return false;
        }

        $insert_data = [
            'compliance_score' => absint($data['compliance_score']),
            'module_scores' => wp_json_encode($data['module_scores'] ?? []),
            'accessibility_issues' => absint($data['accessibility_issues'] ?? 0),
            'dsr_pending_count' => absint($data['dsr_pending_count'] ?? 0),
            'consent_acceptance_rate' => floatval($data['consent_acceptance_rate'] ?? 0),
            'cookie_count' => absint($data['cookie_count'] ?? 0),
            'recorded_at' => current_time('mysql'),
        ];

        $result = $wpdb->insert(
            $this->table_name,
            $insert_data,
            ['%d', '%s', '%d', '%d', '%f', '%d', '%s']
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Get compliance history for the last N days
     *
     * @param int $days Number of days to retrieve (default: 30)
     * @return array Array of history records
     */
    public function get_history(int $days = 30): array {
        global $wpdb;

        $since_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} 
                WHERE recorded_at >= %s 
                ORDER BY recorded_at ASC",
                $since_date
            ),
            ARRAY_A
        );

        if (!$results) {
            return [];
        }

        // Decode JSON fields
        foreach ($results as &$record) {
            $record['module_scores'] = json_decode($record['module_scores'], true) ?? [];
        }

        return $results;
    }

    /**
     * Get the most recent snapshot
     *
     * @return array|null Most recent record or null if none exists
     */
    public function get_latest(): ?array {
        global $wpdb;

        $result = $wpdb->get_row(
            "SELECT * FROM {$this->table_name} 
            ORDER BY recorded_at DESC 
            LIMIT 1",
            ARRAY_A
        );

        if (!$result) {
            return null;
        }

        $result['module_scores'] = json_decode($result['module_scores'], true) ?? [];
        return $result;
    }

    /**
     * Get history for a specific date range
     *
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date   End date (Y-m-d format)
     * @return array Array of history records
     */
    public function get_date_range(string $start_date, string $end_date): array {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} 
                WHERE DATE(recorded_at) BETWEEN %s AND %s 
                ORDER BY recorded_at ASC",
                $start_date,
                $end_date
            ),
            ARRAY_A
        );

        if (!$results) {
            return [];
        }

        // Decode JSON fields
        foreach ($results as &$record) {
            $record['module_scores'] = json_decode($record['module_scores'], true) ?? [];
        }

        return $results;
    }

    /**
     * Clean up old records beyond retention period
     *
     * @param int $days Number of days to retain (default: 365)
     * @return int|false Number of rows deleted or false on failure
     */
    public function cleanup_old_records(int $days = 365) {
        global $wpdb;

        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->table_name} WHERE recorded_at < %s",
                $cutoff_date
            )
        );
    }

    /**
     * Check if table exists
     *
     * @return bool True if table exists
     */
    public function table_exists(): bool {
        global $wpdb;
        $table = $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'");
        return $table === $this->table_name;
    }

    /**
     * Get total record count
     *
     * @return int Total number of records
     */
    public function get_count(): int {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
    }

    /**
     * Check if snapshot already exists for today
     *
     * @return bool True if snapshot exists for today
     */
    public function has_snapshot_today(): bool {
        global $wpdb;
        
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');
        
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} 
                WHERE recorded_at BETWEEN %s AND %s",
                $today_start,
                $today_end
            )
        );
        
        return $count > 0;
    }
}
