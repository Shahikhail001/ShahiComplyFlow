<?php
/**
 * Base Repository Class
 *
 * Abstract base class for all database repositories.
 * Provides common CRUD operations and query building.
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
 * Abstract Repository Class
 *
 * @since 1.0.0
 */
abstract class Repository {
    /**
     * WordPress database instance
     *
     * @var \wpdb
     */
    protected \wpdb $db;

    /**
     * Table name (without prefix)
     *
     * @var string
     */
    protected string $table;

    /**
     * Primary key column
     *
     * @var string
     */
    protected string $primary_key = 'id';

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Get full table name with prefix
     *
     * @return string
     */
    protected function get_table_name(): string {
        return $this->db->prefix . $this->table;
    }

    /**
     * Find record by ID
     *
     * @param int $id Record ID.
     * @return object|null Record object or null if not found.
     */
    public function find(int $id): ?object {
        $table = $this->get_table_name();
        
        $query = $this->db->prepare(
            "SELECT * FROM {$table} WHERE {$this->primary_key} = %d LIMIT 1",
            $id
        );

        $result = $this->db->get_row($query);

        return $result ?: null;
    }

    /**
     * Find all records
     *
     * @param array<string, mixed> $args Query arguments.
     * @return array<object> Array of record objects.
     */
    public function find_all(array $args = []): array {
        $defaults = [
            'orderby' => $this->primary_key,
            'order' => 'DESC',
            'limit' => 100,
            'offset' => 0,
        ];

        $args = wp_parse_args($args, $defaults);
        $table = $this->get_table_name();

        $query = "SELECT * FROM {$table}";
        $query .= $this->build_where_clause($args);
        $query .= sprintf(' ORDER BY %s %s', esc_sql($args['orderby']), esc_sql($args['order']));
        $query .= sprintf(' LIMIT %d OFFSET %d', (int) $args['limit'], (int) $args['offset']);

        return $this->db->get_results($query);
    }

    /**
     * Find records by field value
     *
     * @param string $field Field name.
     * @param mixed  $value Field value.
     * @param array  $args  Additional query arguments.
     * @return array<object> Array of record objects.
     */
    public function find_by(string $field, $value, array $args = []): array {
        $args['where'] = [$field => $value];
        return $this->find_all($args);
    }

    /**
     * Count total records
     *
     * @param array<string, mixed> $args Query arguments.
     * @return int Total count.
     */
    public function count(array $args = []): int {
        $table = $this->get_table_name();
        
        $query = "SELECT COUNT(*) FROM {$table}";
        $query .= $this->build_where_clause($args);

        return (int) $this->db->get_var($query);
    }

    /**
     * Insert a new record
     *
     * @param array<string, mixed> $data Record data.
     * @return int|false Insert ID on success, false on failure.
     */
    public function insert(array $data) {
        $table = $this->get_table_name();
        
        $result = $this->db->insert($table, $data);

        if ($result === false) {
            error_log('ComplyFlow: Database insert failed for table ' . $table . ': ' . $this->db->last_error);
            return false;
        }

        $insert_id = $this->db->insert_id;
        error_log('ComplyFlow: Database insert succeeded for table ' . $table . ', ID: ' . $insert_id);
        
        return $insert_id;
    }

    /**
     * Update a record by ID
     *
     * @param int                  $id   Record ID.
     * @param array<string, mixed> $data Data to update.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, array $data): bool {
        $table = $this->get_table_name();
        
        $result = $this->db->update(
            $table,
            $data,
            [$this->primary_key => $id]
        );

        return $result !== false;
    }

    /**
     * Delete a record by ID
     *
     * @param int $id Record ID.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool {
        $table = $this->get_table_name();
        
        $result = $this->db->delete(
            $table,
            [$this->primary_key => $id]
        );

        return $result !== false;
    }

    /**
     * Delete records by field value
     *
     * @param string $field Field name.
     * @param mixed  $value Field value.
     * @return int Number of rows deleted.
     */
    public function delete_by(string $field, $value): int {
        $table = $this->get_table_name();
        
        $result = $this->db->delete($table, [$field => $value]);

        return $result !== false ? $result : 0;
    }

    /**
     * Check if record exists
     *
     * @param int $id Record ID.
     * @return bool True if exists, false otherwise.
     */
    public function exists(int $id): bool {
        $table = $this->get_table_name();
        
        $query = $this->db->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE {$this->primary_key} = %d",
            $id
        );

        return (int) $this->db->get_var($query) > 0;
    }

    /**
     * Bulk insert records
     *
     * @param array<array<string, mixed>> $records Array of record data.
     * @return int Number of records inserted.
     */
    public function bulk_insert(array $records): int {
        if (empty($records)) {
            return 0;
        }

        $inserted = 0;
        foreach ($records as $record) {
            if ($this->insert($record) !== false) {
                $inserted++;
            }
        }

        return $inserted;
    }

    /**
     * Truncate table (delete all records)
     *
     * @return bool True on success, false on failure.
     */
    public function truncate(): bool {
        $table = $this->get_table_name();
        
        $result = $this->db->query("TRUNCATE TABLE {$table}");

        return $result !== false;
    }

    /**
     * Build WHERE clause from arguments
     *
     * @param array<string, mixed> $args Query arguments.
     * @return string WHERE clause SQL.
     */
    protected function build_where_clause(array $args): string {
        if (empty($args['where'])) {
            return '';
        }

        $conditions = [];
        foreach ($args['where'] as $field => $value) {
            if (is_array($value)) {
                // IN clause
                $placeholders = implode(',', array_fill(0, count($value), '%s'));
                $conditions[] = $this->db->prepare(
                    "{$field} IN ({$placeholders})",
                    ...$value
                );
            } elseif ($value === null) {
                $conditions[] = "{$field} IS NULL";
            } else {
                $conditions[] = $this->db->prepare("{$field} = %s", $value);
            }
        }

        return ' WHERE ' . implode(' AND ', $conditions);
    }

    /**
     * Execute raw SQL query
     *
     * @param string $query SQL query.
     * @return mixed Query result.
     */
    protected function query(string $query) {
        return $this->db->query($query);
    }

    /**
     * Get last database error
     *
     * @return string Last error message.
     */
    public function get_last_error(): string {
        return $this->db->last_error;
    }
}
