<?php
/**
 * Cookie Inventory Manager
 *
 * @package ComplyFlow\Modules\Cookie
 * @since   3.3.1
 */

namespace ComplyFlow\Modules\Cookie;

use ComplyFlow\Core\SettingsRepository;
use WP_Error;

class CookieInventory {
    
    private SettingsRepository $settings;
    private string $table_name;

    public function __construct(?SettingsRepository $settings = null) {
        global $wpdb;
        $this->settings = $settings ?? SettingsRepository::get_instance();
        $this->table_name = $wpdb->prefix . 'complyflow_cookies';
    }

    public function init(): void {
        $this->maybe_create_table();
    }

    private function maybe_create_table(): void {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            provider varchar(255) DEFAULT NULL,
            category varchar(50) DEFAULT 'functional',
            type varchar(50) DEFAULT 'tracking',
            purpose text DEFAULT NULL,
            expiry varchar(100) DEFAULT NULL,
            is_manual tinyint(1) DEFAULT 0,
            source varchar(50) DEFAULT 'scanner',
            detected_at datetime DEFAULT NULL,
            updated_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY name (name)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function add_or_update(array $cookie): int|false {
        global $wpdb;

        $existing = $this->get_by_name($cookie['name']);

        $data = [
            'name' => sanitize_text_field($cookie['name']),
            'provider' => sanitize_text_field($cookie['provider'] ?? ''),
            'category' => sanitize_text_field($cookie['category'] ?? 'functional'),
            'type' => sanitize_text_field($cookie['type'] ?? 'tracking'),
            'purpose' => sanitize_textarea_field($cookie['purpose'] ?? ''),
            'expiry' => sanitize_text_field($cookie['expiry'] ?? ''),
            'is_manual' => isset($cookie['is_manual']) ? (int) $cookie['is_manual'] : 0,
            'source' => sanitize_text_field($cookie['source'] ?? 'scanner'),
            'updated_at' => current_time('mysql'),
        ];

        if ($existing) {
            // Update existing
            $result = $wpdb->update(
                $this->table_name,
                $data,
                ['id' => $existing->id],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s'],
                ['%d']
            );

            return $result !== false ? $existing->id : false;
        } else {
            // Insert new
            $data['detected_at'] = current_time('mysql');
            
            $result = $wpdb->insert(
                $this->table_name,
                $data,
                ['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s']
            );

            return $result !== false ? $wpdb->insert_id : false;
        }
    }

    public function get_by_name(string $name): ?object {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE name = %s",
            $name
        ));
    }

    public function get_all(array $filters = []): array {
        global $wpdb;

        $where = ['1=1'];
        $values = [];

        if (!empty($filters['category'])) {
            $where[] = 'category = %s';
            $values[] = $filters['category'];
        }

        if (!empty($filters['type'])) {
            $where[] = 'type = %s';
            $values[] = $filters['type'];
        }

        if (!empty($filters['provider'])) {
            $where[] = 'provider LIKE %s';
            $values[] = '%' . $wpdb->esc_like($filters['provider']) . '%';
        }

        $where_clause = implode(' AND ', $where);
        $sql = "SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY provider, name";

        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }

        return $wpdb->get_results($sql) ?: [];
    }

    public function update_category(int $cookie_id, string $category): bool {
        global $wpdb;

        $valid_categories = ['necessary', 'functional', 'analytics', 'marketing'];
        
        if (!in_array($category, $valid_categories)) {
            return false;
        }

        $result = $wpdb->update(
            $this->table_name,
            [
                'category' => $category,
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $cookie_id],
            ['%s', '%s'],
            ['%d']
        );

        return $result !== false;
    }

    public function update_details(int $cookie_id, array $details): bool {
        global $wpdb;

        $data = [];
        $formats = [];

        if (isset($details['purpose'])) {
            $data['purpose'] = sanitize_textarea_field($details['purpose']);
            $formats[] = '%s';
        }

        if (isset($details['expiry'])) {
            $data['expiry'] = sanitize_text_field($details['expiry']);
            $formats[] = '%s';
        }

        if (isset($details['provider'])) {
            $data['provider'] = sanitize_text_field($details['provider']);
            $formats[] = '%s';
        }

        if (isset($details['type'])) {
            $data['type'] = sanitize_text_field($details['type']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return false;
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $result = $wpdb->update(
            $this->table_name,
            $data,
            ['id' => $cookie_id],
            $formats,
            ['%d']
        );

        return $result !== false;
    }

    public function get_by_id(int $cookie_id): ?object {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $cookie_id
        ));
    }

    public function delete(int $cookie_id): bool {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table_name,
            ['id' => $cookie_id],
            ['%d']
        );

        return $result !== false;
    }

    public function get_stats(): array {
        global $wpdb;

        $stats = [
            'total' => 0,
            'by_category' => [
                'necessary' => 0,
                'functional' => 0,
                'analytics' => 0,
                'marketing' => 0,
            ],
            'by_provider' => [],
        ];

        // Total count
        $stats['total'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");

        // Count by category
        $categories = $wpdb->get_results("
            SELECT category, COUNT(*) as count 
            FROM {$this->table_name} 
            GROUP BY category
        ");

        foreach ($categories as $cat) {
            if (isset($stats['by_category'][$cat->category])) {
                $stats['by_category'][$cat->category] = (int) $cat->count;
            }
        }

        // Count by provider
        $providers = $wpdb->get_results("
            SELECT provider, COUNT(*) as count 
            FROM {$this->table_name} 
            GROUP BY provider 
            ORDER BY count DESC 
            LIMIT 10
        ");

        foreach ($providers as $provider) {
            $stats['by_provider'][$provider->provider] = (int) $provider->count;
        }

        return $stats;
    }

    public function export_to_csv(array $cookies): string|WP_Error {
        if (empty($cookies)) {
            return new WP_Error('empty_data', __('No cookies to export', 'complyflow'));
        }

        $output = fopen('php://temp', 'r+');

        // Write header
        fputcsv($output, [
            'Cookie Name',
            'Provider',
            'Category',
            'Type',
            'Purpose',
            'Expiry',
            'Detected At',
        ]);

        // Write data rows
        foreach ($cookies as $cookie) {
            fputcsv($output, [
                $cookie->name ?? '',
                $cookie->provider ?? '',
                ucfirst($cookie->category ?? ''),
                $cookie->type ?? '',
                $cookie->purpose ?? '',
                $cookie->expiry ?? '',
                $cookie->detected_at ?? '',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function clear_all(): bool {
        global $wpdb;

        $result = $wpdb->query("TRUNCATE TABLE {$this->table_name}");

        return $result !== false;
    }

    public function import_from_csv_data(string $csv_data): array {
        $imported = 0;
        $errors = [];
        $lines = explode("\n", $csv_data);
        
        // Skip header row
        array_shift($lines);
        
        foreach ($lines as $line_num => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            
            $data = str_getcsv($line);
            
            if (count($data) < 3) { // At least name, provider, category
                $errors[] = sprintf(__('Line %d: Insufficient data', 'complyflow'), $line_num + 2);
                continue;
            }
            
            $cookie = [
                'name' => $data[0] ?? '',
                'provider' => $data[1] ?? '',
                'category' => strtolower($data[2] ?? 'functional'),
                'type' => $data[3] ?? 'http',
                'purpose' => $data[4] ?? '',
                'expiry' => $data[5] ?? '',
                'is_manual' => 1,
                'source' => 'import',
            ];
            
            if (empty($cookie['name'])) {
                $errors[] = sprintf(__('Line %d: Cookie name is required', 'complyflow'), $line_num + 2);
                continue;
            }
            
            $result = $this->add_or_update($cookie);
            
            if ($result !== false) {
                $imported++;
            } else {
                $errors[] = sprintf(__('Line %d: Failed to import %s', 'complyflow'), $line_num + 2, $cookie['name']);
            }
        }
        
        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }
}
