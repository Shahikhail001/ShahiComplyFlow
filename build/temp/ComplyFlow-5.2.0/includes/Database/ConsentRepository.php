<?php
/**
 * Consent Repository Class
 *
 * Manages consent log records in the database.
 *
 * @package ComplyFlow\Database
 * @since 1.0.0
 */

namespace ComplyFlow\Database;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Consent Repository Class
 *
 * @since 1.0.0
 */
class ConsentRepository extends Repository {
    /**
     * Table name
     *
     * @var string
     */
    protected string $table = 'complyflow_consent';

    /**
     * Find consent records by user ID
     *
     * @param int   $user_id User ID.
     * @param array $args    Additional query arguments.
     * @return array<object> Array of consent records.
     */
    public function find_by_user(int $user_id, array $args = []): array {
        return $this->find_by('user_id', $user_id, $args);
    }

    /**
     * Find consent records by IP address
     *
     * @param string $ip_address IP address.
     * @param array  $args       Additional query arguments.
     * @return array<object> Array of consent records.
     */
    public function find_by_ip(string $ip_address, array $args = []): array {
        return $this->find_by('ip_address', $ip_address, $args);
    }

    /**
     * Get user's latest consent
     *
     * @param int $user_id User ID.
     * @return object|null Latest consent record or null.
     */
    public function get_latest_consent(int $user_id): ?object {
        $table = $this->get_table_name();
        
        $query = $this->db->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
            $user_id
        );

        $result = $this->db->get_row($query);

        return $result ?: null;
    }

    /**
     * Get consent statistics
     *
     * @param array $args Query arguments.
     * @return array<string, mixed> Statistics array.
     */
    public function get_statistics(array $args = []): array {
        $table = $this->get_table_name();
        $where = $this->build_where_clause($args);

        // Total consents
        $total = (int) $this->db->get_var("SELECT COUNT(*) FROM {$table}{$where}");

        // Accepted vs Rejected
        $accepted = (int) $this->db->get_var(
            "SELECT COUNT(*) FROM {$table}{$where}" . 
            ($where ? ' AND' : ' WHERE') . " consent_given = 1"
        );
        $rejected = $total - $accepted;

        // By category
        $categories = $this->db->get_results(
            "SELECT categories, COUNT(*) as count FROM {$table}{$where} GROUP BY categories"
        );

        // By geo-location
        $geolocations = $this->db->get_results(
            "SELECT geo_location, COUNT(*) as count FROM {$table}{$where} GROUP BY geo_location ORDER BY count DESC LIMIT 10"
        );

        return [
            'total' => $total,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
            'by_category' => $categories,
            'by_geolocation' => $geolocations,
        ];
    }

    /**
     * Delete old consent records
     *
     * @param int $days Number of days to keep.
     * @return int Number of deleted records.
     */
    public function delete_old_records(int $days): int {
        $table = $this->get_table_name();
        
        $date = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $query = $this->db->prepare(
            "DELETE FROM {$table} WHERE created_at < %s",
            $date
        );

        $result = $this->db->query($query);

        return $result !== false ? $result : 0;
    }

    /**
     * Update consent preferences
     *
     * @param int   $consent_id Consent record ID.
     * @param array $categories New category preferences.
     * @return bool True on success, false on failure.
     */
    public function update_preferences(int $consent_id, array $categories): bool {
        return $this->update($consent_id, [
            'categories' => wp_json_encode($categories),
            'updated_at' => current_time('mysql', true),
        ]);
    }

    /**
     * Export consent logs for a user
     *
     * @param int $user_id User ID.
     * @return array<object> All consent records for the user.
     */
    public function export_user_data(int $user_id): array {
        return $this->find_by_user($user_id, ['limit' => 99999]);
    }

    /**
     * Anonymize consent logs for a user (GDPR right to erasure)
     *
     * @param int $user_id User ID.
     * @return int Number of anonymized records.
     */
    public function anonymize_user_data(int $user_id): int {
        $table = $this->get_table_name();
        
        $result = $this->db->update(
            $table,
            [
                'user_id' => 0,
                'ip_address' => '0.0.0.0',
                'user_agent' => 'ANONYMIZED',
                'geo_location' => '',
            ],
            ['user_id' => $user_id]
        );

        return $result !== false ? $result : 0;
    }
}
