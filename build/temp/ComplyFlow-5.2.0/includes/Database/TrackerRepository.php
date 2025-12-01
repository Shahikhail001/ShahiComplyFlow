<?php
/**
 * Tracker Repository Class
 *
 * Manages cookie and tracker inventory records in the database.
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
 * Tracker Repository Class
 *
 * @since 1.0.0
 */
class TrackerRepository extends Repository {
    /**
     * Table name
     *
     * @var string
     */
    protected string $table = 'complyflow_tracker_inventory';

    /**
     * Find trackers by name
     *
     * @param string $name Tracker name.
     * @param array  $args Additional query arguments.
     * @return array<object> Array of tracker records.
     */
    public function find_by_name(string $name, array $args = []): array {
        return $this->find_by('name', $name, $args);
    }

    /**
     * Find trackers by type
     *
     * @param string $type Tracker type (cookie, script, pixel, etc.).
     * @param array  $args Additional query arguments.
     * @return array<object> Array of tracker records.
     */
    public function find_by_type(string $type, array $args = []): array {
        return $this->find_by('type', $type, $args);
    }

    /**
     * Find trackers by category
     *
     * @param string $category Tracker category (necessary, functional, analytics, marketing).
     * @param array  $args     Additional query arguments.
     * @return array<object> Array of tracker records.
     */
    public function find_by_category(string $category, array $args = []): array {
        return $this->find_by('category', $category, $args);
    }

    /**
     * Find trackers by provider
     *
     * @param string $provider Provider name.
     * @param array  $args     Additional query arguments.
     * @return array<object> Array of tracker records.
     */
    public function find_by_provider(string $provider, array $args = []): array {
        return $this->find_by('provider', $provider, $args);
    }

    /**
     * Get tracker statistics
     *
     * @param array $args Query arguments.
     * @return array<string, mixed> Statistics array.
     */
    public function get_statistics(array $args = []): array {
        $table = $this->get_table_name();
        $where = $this->build_where_clause($args);

        // Total trackers
        $total = (int) $this->db->get_var("SELECT COUNT(*) FROM {$table}{$where}");

        // By type
        $by_type = $this->db->get_results(
            "SELECT type, COUNT(*) as count FROM {$table}{$where} GROUP BY type"
        );

        // By category
        $by_category = $this->db->get_results(
            "SELECT category, COUNT(*) as count FROM {$table}{$where} GROUP BY category"
        );

        // By provider (top 10)
        $by_provider = $this->db->get_results(
            "SELECT provider, COUNT(*) as count FROM {$table}{$where} GROUP BY provider ORDER BY count DESC LIMIT 10"
        );

        // Active vs. inactive
        $active = (int) $this->db->get_var(
            "SELECT COUNT(*) FROM {$table}{$where}" .
            ($where ? ' AND' : ' WHERE') . " is_active = 1"
        );

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'by_type' => $by_type,
            'by_category' => $by_category,
            'by_provider' => $by_provider,
        ];
    }

    /**
     * Get all active trackers
     *
     * @param array $args Query arguments.
     * @return array<object> Array of active trackers.
     */
    public function get_active_trackers(array $args = []): array {
        $args['where'] = ['is_active' => 1];
        return $this->find_all($args);
    }

    /**
     * Get trackers by domain
     *
     * @param string $domain Domain name.
     * @param array  $args   Additional query arguments.
     * @return array<object> Array of tracker records.
     */
    public function find_by_domain(string $domain, array $args = []): array {
        $table = $this->get_table_name();
        
        $query = $this->db->prepare(
            "SELECT * FROM {$table} WHERE domain LIKE %s ORDER BY name ASC",
            '%' . $this->db->esc_like($domain) . '%'
        );

        return $this->db->get_results($query);
    }

    /**
     * Update tracker status
     *
     * @param int  $id        Tracker ID.
     * @param bool $is_active Active status.
     * @return bool True on success, false on failure.
     */
    public function update_status(int $id, bool $is_active): bool {
        return $this->update($id, [
            'is_active' => $is_active ? 1 : 0,
            'updated_at' => current_time('mysql', true),
        ]);
    }

    /**
     * Get trackers requiring consent
     *
     * @param array $args Query arguments.
     * @return array<object> Array of trackers requiring consent.
     */
    public function get_consent_required_trackers(array $args = []): array {
        $table = $this->get_table_name();
        $where = $this->build_where_clause($args);

        // Trackers in non-necessary categories require consent
        $consent_condition = "category IN ('functional', 'analytics', 'marketing', 'advertising')";
        
        if ($where) {
            $where .= ' AND ' . $consent_condition;
        } else {
            $where = ' WHERE ' . $consent_condition;
        }

        $query = "SELECT * FROM {$table}{$where} ORDER BY category, name";

        return $this->db->get_results($query);
    }

    /**
     * Bulk update tracker categories
     *
     * @param array $updates Array of [id => category] pairs.
     * @return int Number of updated records.
     */
    public function bulk_update_categories(array $updates): int {
        if (empty($updates)) {
            return 0;
        }

        $updated = 0;
        foreach ($updates as $id => $category) {
            if ($this->update($id, ['category' => $category])) {
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Create or update a tracker
     *
     * @param string $name Tracker name (unique identifier).
     * @param array  $data Tracker data.
     * @return int|false Tracker ID on success, false on failure.
     */
    public function upsert(string $name, array $data) {
        $table = $this->get_table_name();

        // Check if tracker exists
        $existing = $this->db->get_var(
            $this->db->prepare(
                "SELECT id FROM {$table} WHERE name = %s LIMIT 1",
                $name
            )
        );

        if ($existing) {
            // Update existing
            $data['updated_at'] = current_time('mysql', true);
            $success = $this->update((int) $existing, $data);
            return $success ? (int) $existing : false;
        }

        // Insert new
        $data['name'] = $name;
        $data['created_at'] = current_time('mysql', true);
        return $this->insert($data);
    }

    /**
     * Export tracker inventory as array
     *
     * @return array<object> All trackers with full details.
     */
    public function export_inventory(): array {
        return $this->find_all(['limit' => 99999, 'orderby' => 'category, name']);
    }

    /**
     * Delete inactive trackers older than X days
     *
     * @param int $days Number of days to keep.
     * @return int Number of deleted records.
     */
    public function delete_old_inactive(int $days): int {
        $table = $this->get_table_name();
        
        $date = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $query = $this->db->prepare(
            "DELETE FROM {$table} WHERE is_active = 0 AND updated_at < %s",
            $date
        );

        $result = $this->db->query($query);

        return $result !== false ? $result : 0;
    }

    /**
     * Search trackers by keyword
     *
     * @param string $keyword Search keyword.
     * @param array  $args    Additional query arguments.
     * @return array<object> Matching tracker records.
     */
    public function search(string $keyword, array $args = []): array {
        $table = $this->get_table_name();
        
        $search_term = '%' . $this->db->esc_like($keyword) . '%';
        
        $query = $this->db->prepare(
            "SELECT * FROM {$table} 
             WHERE name LIKE %s 
             OR description LIKE %s 
             OR provider LIKE %s 
             ORDER BY name ASC 
             LIMIT 100",
            $search_term,
            $search_term,
            $search_term
        );

        return $this->db->get_results($query);
    }
}
