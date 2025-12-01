<?php
/**
 * Scan Repository Class
 *
 * Manages accessibility scan result records in the database.
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
 * Scan Repository Class
 *
 * @since 1.0.0
 */
class ScanRepository extends Repository {
    /**
     * Table name
     *
     * @var string
     */
    protected string $table = 'complyflow_scan_results';

    /**
     * Find scans by page URL
     *
     * @param string $url  Page URL.
     * @param array  $args Additional query arguments.
     * @return array<object> Array of scan records.
     */
    public function find_by_url(string $url, array $args = []): array {
        return $this->find_by('url', $url, $args);
    }

    /**
     * Find scans by severity level
     *
     * @param string $severity Severity level (critical, serious, moderate, minor).
     * @param array  $args     Additional query arguments.
     * @return array<object> Array of scan records.
     */
    public function find_by_severity(string $severity, array $args = []): array {
        $table = $this->get_table_name();
        $where = $this->build_where_clause($args);

        // Add severity condition
        $severity_condition = $this->db->prepare('JSON_EXTRACT(results, "$.severity") = %s', $severity);
        
        if ($where) {
            $where .= ' AND ' . $severity_condition;
        } else {
            $where = ' WHERE ' . $severity_condition;
        }

        $query = "SELECT * FROM {$table}{$where} ORDER BY created_at DESC LIMIT 100";

        return $this->db->get_results($query);
    }

    /**
     * Get latest scan for a page
     *
     * @param string $url Page URL.
     * @return object|null Latest scan record or null.
     */
    public function get_latest_scan(string $url): ?object {
        $table = $this->get_table_name();
        
        $query = $this->db->prepare(
            "SELECT * FROM {$table} WHERE url = %s ORDER BY created_at DESC LIMIT 1",
            $url
        );

        $result = $this->db->get_row($query);

        return $result ?: null;
    }

    /**
     * Get scan statistics
     *
     * @param array $args Query arguments.
     * @return array<string, mixed> Statistics array.
     */
    public function get_statistics(array $args = []): array {
        // Check cache first
        $cache = \ComplyFlow\Core\Cache::get_instance();
        $cache_key = 'scan_statistics_' . md5(wp_json_encode($args));
        
        $cached = $cache->get($cache_key, 'stats');
        if (false !== $cached) {
            return $cached;
        }

        $table = $this->get_table_name();
        $where = $this->build_where_clause($args);

        // Total scans
        $total = (int) $this->db->get_var("SELECT COUNT(*) FROM {$table}{$where}");

        // Total issues
        $total_issues = (int) $this->db->get_var(
            "SELECT SUM(total_issues) FROM {$table}{$where}"
        );

        // Average score - extract from JSON results column
        $scans_with_results = $this->db->get_results("SELECT results FROM {$table}{$where}");
        $total_score = 0;
        $score_count = 0;
        foreach ($scans_with_results as $scan) {
            $results = json_decode($scan->results, true);
            if (isset($results['score'])) {
                $total_score += (float) $results['score'];
                $score_count++;
            }
        }
        $avg_score = $score_count > 0 ? $total_score / $score_count : 0;

        // Issues by severity (summing from all scans)
        $by_severity = [
            'critical' => 0,
            'serious' => 0,
            'moderate' => 0,
            'minor' => 0,
        ];

        $scans = $this->db->get_results("SELECT results FROM {$table}{$where}");
        foreach ($scans as $scan) {
            $results = json_decode($scan->results, true);
            if (isset($results['summary'])) {
                foreach ($by_severity as $level => $count) {
                    $by_severity[$level] += $results['summary'][$level] ?? 0;
                }
            }
        }

        // Pages scanned
        $pages_scanned = (int) $this->db->get_var(
            "SELECT COUNT(DISTINCT url) FROM {$table}{$where}"
        );

        $statistics = [
            'total_scans' => $total,
            'total_issues' => $total_issues,
            'average_score' => $avg_score ? round((float) $avg_score, 2) : 0,
            'by_severity' => $by_severity,
            'pages_scanned' => $pages_scanned,
        ];

        // Cache for 15 minutes
        $cache->set($cache_key, $statistics, 'stats', 900);

        return $statistics;
    }

    /**
     * Get all unique scanned URLs
     *
     * @param array $args Query arguments.
     * @return array<string> Array of unique URLs.
     */
    public function get_scanned_urls(array $args = []): array {
        $table = $this->get_table_name();
        
        $defaults = [
            'limit' => 1000,
            'offset' => 0,
        ];

        $args = wp_parse_args($args, $defaults);

        $query = sprintf(
            "SELECT DISTINCT url FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            (int) $args['limit'],
            (int) $args['offset']
        );

        $results = $this->db->get_col($query);

        return $results ?: [];
    }

    /**
     * Delete old scan results
     *
     * @param int $days Number of days to keep.
     * @return int Number of deleted records.
     */
    public function delete_old_scans(int $days): int {
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
     * Get pages with most issues
     *
     * @param int $limit Number of pages to return.
     * @return array<object> Pages with issue counts.
     */
    public function get_pages_with_most_issues(int $limit = 10): array {
        $table = $this->get_table_name();

        $query = $this->db->prepare(
            "SELECT url, MAX(total_issues) as max_issues, MAX(created_at) as last_scan
             FROM {$table}
             GROUP BY url
             ORDER BY max_issues DESC
             LIMIT %d",
            $limit
        );

        return $this->db->get_results($query);
    }

    /**
     * Get trend data for a specific page
     *
     * @param string $url   Page URL.
     * @param int    $limit Number of scans to include.
     * @return array<object> Historical scan data.
     */
    public function get_page_trend(string $url, int $limit = 30): array {
        $table = $this->get_table_name();

        $query = $this->db->prepare(
            "SELECT id, total_issues, created_at, results
             FROM {$table}
             WHERE url = %s
             ORDER BY created_at DESC
             LIMIT %d",
            $url,
            $limit
        );

        return $this->db->get_results($query);
    }

    /**
     * Create a new scan result
     *
     * @param array $data Scan data.
     * @return int|false Insert ID on success, false on failure.
     */
    public function create_scan(array $data) {
        $defaults = [
            'created_at' => current_time('mysql', true),
        ];

        $data = wp_parse_args($data, $defaults);

        // Encode results as JSON if it's an array
        if (isset($data['results']) && is_array($data['results'])) {
            $data['results'] = wp_json_encode($data['results']);
        }

        $result = $this->insert($data);

        // Invalidate cache on successful insert
        if ($result) {
            $cache = \ComplyFlow\Core\Cache::get_instance();
            $cache->flush_group('scans');
            $cache->flush_group('stats');
        }

        return $result;
    }
}
