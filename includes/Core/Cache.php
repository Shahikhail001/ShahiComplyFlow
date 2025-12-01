<?php
/**
 * Cache Management Class
 *
 * Provides caching functionality using WordPress Transients API
 * with support for object caching backends (Redis, Memcached).
 *
 * @package ComplyFlow\Core
 * @since 1.0.0
 */

namespace ComplyFlow\Core;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cache Class
 *
 * @since 1.0.0
 */
final class Cache {
    /**
     * Cache instance
     *
     * @var Cache|null
     */
    private static ?Cache $instance = null;

    /**
     * Cache prefix
     *
     * @var string
     */
    private string $prefix = 'complyflow_';

    /**
     * Default cache TTL (in seconds)
     *
     * @var int
     */
    private int $default_ttl = 3600; // 1 hour

    /**
     * Cache groups with their TTL
     *
     * @var array<string, int>
     */
    private array $groups = [
        'settings' => 3600,      // 1 hour
        'scans' => 86400,        // 1 day
        'consent' => 21600,      // 6 hours
        'dsr' => 21600,          // 6 hours
        'stats' => 900,          // 15 minutes
        'default' => 3600,       // 1 hour
    ];

    /**
     * Cache statistics
     *
     * @var array<string, array<string, int>>
     */
    private array $stats = [];

    /**
     * Private constructor
     */
    private function __construct() {
        $this->init_stats();
    }

    /**
     * Get cache instance (Singleton)
     *
     * @return Cache
     */
    public static function get_instance(): Cache {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get cached value
     *
     * @param string $key   Cache key.
     * @param string $group Cache group.
     * @return mixed|false Value on success, false on failure.
     */
    public function get(string $key, string $group = 'default') {
        $cache_key = $this->get_cache_key($key, $group);
        $value = get_transient($cache_key);

        // Update stats
        if (false !== $value) {
            $this->increment_stat($group, 'hits');
        } else {
            $this->increment_stat($group, 'misses');
        }

        return $value;
    }

    /**
     * Set cached value
     *
     * @param string   $key        Cache key.
     * @param mixed    $value      Value to cache.
     * @param string   $group      Cache group.
     * @param int|null $expiration Expiration time in seconds. Null uses group default.
     * @return bool True on success, false on failure.
     */
    public function set(string $key, $value, string $group = 'default', ?int $expiration = null): bool {
        $cache_key = $this->get_cache_key($key, $group);
        $ttl = $expiration ?? $this->get_group_ttl($group);

        $result = set_transient($cache_key, $value, $ttl);

        if ($result) {
            $this->increment_stat($group, 'keys');
        }

        return $result;
    }

    /**
     * Delete cached value
     *
     * @param string $key   Cache key.
     * @param string $group Cache group.
     * @return bool True on success, false on failure.
     */
    public function delete(string $key, string $group = 'default'): bool {
        $cache_key = $this->get_cache_key($key, $group);
        return delete_transient($cache_key);
    }

    /**
     * Flush all cache
     *
     * @return bool True on success, false on failure.
     */
    public function flush(): bool {
        global $wpdb;

        // Delete all ComplyFlow transients
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} 
                WHERE option_name LIKE %s 
                OR option_name LIKE %s",
                $wpdb->esc_like('_transient_' . $this->prefix) . '%',
                $wpdb->esc_like('_transient_timeout_' . $this->prefix) . '%'
            )
        );

        // Reset stats
        $this->init_stats();

        return $result !== false;
    }

    /**
     * Flush specific cache group
     *
     * @param string $group Cache group to flush.
     * @return bool True on success, false on failure.
     */
    public function flush_group(string $group): bool {
        global $wpdb;

        $pattern = $this->prefix . $group . '_';

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} 
                WHERE option_name LIKE %s 
                OR option_name LIKE %s",
                $wpdb->esc_like('_transient_' . $pattern) . '%',
                $wpdb->esc_like('_transient_timeout_' . $pattern) . '%'
            )
        );

        // Reset group stats
        if (isset($this->stats[$group])) {
            $this->stats[$group] = [
                'keys' => 0,
                'hits' => 0,
                'misses' => 0,
                'size' => 0,
            ];
        }

        return $result !== false;
    }

    /**
     * Get cache statistics
     *
     * @return array<string, array<string, mixed>>
     */
    public function get_stats(): array {
        $stats = [];

        foreach ($this->stats as $group => $data) {
            $total = $data['hits'] + $data['misses'];
            $hit_rate = $total > 0 ? ($data['hits'] / $total) * 100 : 0;

            $stats[$group] = [
                'keys' => $data['keys'],
                'hits' => $data['hits'],
                'misses' => $data['misses'],
                'size' => $data['size'],
                'hit_rate' => $hit_rate,
            ];
        }

        return $stats;
    }

    /**
     * Warm settings cache
     *
     * @return bool True on success.
     */
    public function warm_settings(): bool {
        $settings = get_option('complyflow_settings', []);
        return $this->set('all_settings', $settings, 'settings');
    }

    /**
     * Warm scans cache
     *
     * @return bool True on success.
     */
    public function warm_scans(): bool {
        $repository = new \ComplyFlow\Database\ScanRepository();
        
        // Cache latest scans
        $latest_scans = $repository->get_latest(10);
        $this->set('latest_scans', $latest_scans, 'scans');

        // Cache scan statistics
        $stats = $repository->get_statistics();
        $this->set('scan_stats', $stats, 'stats');

        return true;
    }

    /**
     * Warm consent cache
     *
     * @return bool True on success.
     */
    public function warm_consent(): bool {
        $repository = new \ComplyFlow\Database\ConsentRepository();
        
        // Cache consent statistics
        $stats = $repository->get_statistics();
        $this->set('consent_stats', $stats, 'stats');

        // Cache acceptance rate
        $rate = $repository->get_acceptance_rate();
        $this->set('acceptance_rate', $rate, 'stats');

        return true;
    }

    /**
     * Warm DSR cache
     *
     * @return bool True on success.
     */
    public function warm_dsr(): bool {
        $repository = new \ComplyFlow\Database\DSRRepository();
        
        // Cache DSR statistics
        $stats = $repository->get_statistics();
        $this->set('dsr_stats', $stats, 'stats');

        // Cache pending requests count
        $pending = $repository->count(['status' => 'pending']);
        $this->set('pending_dsr_count', $pending, 'dsr');

        return true;
    }

    /**
     * Warm statistics cache
     *
     * @return bool True on success.
     */
    public function warm_stats(): bool {
        $this->warm_scans();
        $this->warm_consent();
        $this->warm_dsr();

        return true;
    }

    /**
     * Remember cached query result
     *
     * @param string   $key      Cache key.
     * @param callable $callback Callback to execute if cache misses.
     * @param string   $group    Cache group.
     * @param int|null $ttl      Cache TTL.
     * @return mixed Cached or fresh value.
     */
    public function remember(string $key, callable $callback, string $group = 'default', ?int $ttl = null) {
        $value = $this->get($key, $group);

        if (false !== $value) {
            return $value;
        }

        $value = $callback();

        if ($value !== false) {
            $this->set($key, $value, $group, $ttl);
        }

        return $value;
    }

    /**
     * Get cache key with prefix and group
     *
     * @param string $key   Cache key.
     * @param string $group Cache group.
     * @return string Full cache key.
     */
    private function get_cache_key(string $key, string $group): string {
        return $this->prefix . $group . '_' . $key;
    }

    /**
     * Get TTL for cache group
     *
     * @param string $group Cache group.
     * @return int TTL in seconds.
     */
    private function get_group_ttl(string $group): int {
        return $this->groups[$group] ?? $this->default_ttl;
    }

    /**
     * Initialize cache statistics
     *
     * @return void
     */
    private function init_stats(): void {
        foreach (array_keys($this->groups) as $group) {
            $this->stats[$group] = [
                'keys' => 0,
                'hits' => 0,
                'misses' => 0,
                'size' => 0,
            ];
        }
    }

    /**
     * Increment cache statistic
     *
     * @param string $group Group name.
     * @param string $stat  Statistic name.
     * @return void
     */
    private function increment_stat(string $group, string $stat): void {
        if (!isset($this->stats[$group])) {
            $this->stats[$group] = [
                'keys' => 0,
                'hits' => 0,
                'misses' => 0,
                'size' => 0,
            ];
        }

        $this->stats[$group][$stat]++;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception('Cannot unserialize singleton');
    }
}
