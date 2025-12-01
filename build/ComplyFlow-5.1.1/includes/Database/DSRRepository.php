<?php
/**
 * DSR Repository Class
 *
 * Manages Data Subject Request records in the database.
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
 * DSR Repository Class
 *
 * @since 1.0.0
 */
class DSRRepository extends Repository {
    /**
     * Table name
     *
     * @var string
     */
    protected string $table = 'complyflow_dsr';

    /**
     * Find DSR records by user ID
     *
     * @param int   $user_id User ID.
     * @param array $args    Additional query arguments.
     * @return array<object> Array of DSR records.
     */
    public function find_by_user(int $user_id, array $args = []): array {
        return $this->find_by('user_id', $user_id, $args);
    }

    /**
     * Find DSR records by email
     *
     * @param string $email Email address.
     * @param array  $args  Additional query arguments.
     * @return array<object> Array of DSR records.
     */
    public function find_by_email(string $email, array $args = []): array {
        return $this->find_by('email', $email, $args);
    }

    /**
     * Find DSR by verification token
     *
     * @param string $token Verification token.
     * @return object|null DSR record or null.
     */
    public function find_by_token(string $token): ?object {
        $table = $this->get_table_name();
        
        $query = $this->db->prepare(
            "SELECT * FROM {$table} WHERE verification_token = %s LIMIT 1",
            $token
        );

        $result = $this->db->get_row($query);

        return $result ?: null;
    }

    /**
     * Find DSR records by status
     *
     * @param string $status Request status.
     * @param array  $args   Additional query arguments.
     * @return array<object> Array of DSR records.
     */
    public function find_by_status(string $status, array $args = []): array {
        return $this->find_by('status', $status, $args);
    }

    /**
     * Find DSR records by type
     *
     * @param string $type Request type.
     * @param array  $args Additional query arguments.
     * @return array<object> Array of DSR records.
     */
    public function find_by_type(string $type, array $args = []): array {
        return $this->find_by('request_type', $type, $args);
    }

    /**
     * Get pending DSR requests
     *
     * @param array $args Query arguments.
     * @return array<object> Array of pending requests.
     */
    public function get_pending_requests(array $args = []): array {
        return $this->find_by_status('pending', $args);
    }

    /**
     * Get overdue requests (older than X days)
     *
     * @param int   $days Maximum days before overdue.
     * @param array $args Additional query arguments.
     * @return array<object> Array of overdue requests.
     */
    public function get_overdue_requests(int $days = 30, array $args = []): array {
        $table = $this->get_table_name();
        $date = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));

        $query = $this->db->prepare(
            "SELECT * FROM {$table} WHERE status IN ('pending', 'verified') AND created_at < %s ORDER BY created_at ASC",
            $date
        );

        return $this->db->get_results($query);
    }

    /**
     * Update request status
     *
     * @param int    $id     Request ID.
     * @param string $status New status.
     * @return bool True on success, false on failure.
     */
    public function update_status(int $id, string $status): bool {
        $data = ['status' => $status];

        // Set completed/processed timestamp
        if (in_array($status, ['completed', 'processed'], true)) {
            $data['processed_at'] = current_time('mysql', true);
        }

        return $this->update($id, $data);
    }

    /**
     * Verify a request
     *
     * @param string $token Verification token.
     * @return bool True on success, false on failure.
     */
    public function verify_request(string $token): bool {
        $table = $this->get_table_name();
        
        $result = $this->db->update(
            $table,
            [
                'status' => 'verified',
                'verified_at' => current_time('mysql', true),
            ],
            ['verification_token' => $token]
        );

        return $result !== false && $result > 0;
    }

    /**
     * Get DSR statistics
     *
     * @param array $args Query arguments.
     * @return array<string, mixed> Statistics array.
     */
    public function get_statistics(array $args = []): array {
        $table = $this->get_table_name();
        $where = $this->build_where_clause($args);

        // Total requests
        $total = (int) $this->db->get_var("SELECT COUNT(*) FROM {$table}{$where}");

        // By status
        $by_status = $this->db->get_results(
            "SELECT status, COUNT(*) as count FROM {$table}{$where} GROUP BY status"
        );

        // By type
        $by_type = $this->db->get_results(
            "SELECT request_type, COUNT(*) as count FROM {$table}{$where} GROUP BY request_type"
        );

        // Average processing time (for completed requests)
        $avg_time = $this->db->get_var(
            "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, processed_at)) FROM {$table} WHERE status = 'completed'"
        );

        // Pending count
        $pending = (int) $this->db->get_var(
            "SELECT COUNT(*) FROM {$table} WHERE status = 'pending'"
        );

        return [
            'total' => $total,
            'pending' => $pending,
            'by_status' => $by_status,
            'by_type' => $by_type,
            'avg_processing_hours' => $avg_time ? round((float) $avg_time, 2) : 0,
        ];
    }

    /**
     * Delete old completed requests
     *
     * @param int $days Number of days to keep.
     * @return int Number of deleted records.
     */
    public function delete_old_completed(int $days): int {
        $table = $this->get_table_name();
        
        $date = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $query = $this->db->prepare(
            "DELETE FROM {$table} WHERE status = 'completed' AND processed_at < %s",
            $date
        );

        $result = $this->db->query($query);

        return $result !== false ? $result : 0;
    }

    /**
     * Export DSR data for a user
     *
     * @param int $user_id User ID.
     * @return array<object> All DSR records for the user.
     */
    public function export_user_data(int $user_id): array {
        return $this->find_by_user($user_id, ['limit' => 99999]);
    }

    /**
     * Create a new DSR request
     *
     * @param array $data Request data.
     * @return int|false Insert ID on success, false on failure.
     */
    public function create_request(array $data) {
        $defaults = [
            'status' => 'pending',
            'verification_token' => wp_generate_password(32, false),
            'created_at' => current_time('mysql', true),
        ];

        $data = wp_parse_args($data, $defaults);

        return $this->insert($data);
    }
}
