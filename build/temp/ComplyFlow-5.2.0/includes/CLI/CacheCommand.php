<?php
/**
 * Cache Management CLI Command
 *
 * WP-CLI commands for cache operations.
 *
 * @package ComplyFlow\CLI
 * @since 1.0.0
 */

namespace ComplyFlow\CLI;

use ComplyFlow\Core\Cache;
use WP_CLI;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage ComplyFlow cache.
 *
 * @since 1.0.0
 */
class CacheCommand extends BaseCommand {
    /**
     * Cache instance
     *
     * @var Cache
     */
    private Cache $cache;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cache = Cache::get_instance();
    }

    /**
     * Flush all cache.
     *
     * ## OPTIONS
     *
     * [--group=<group>]
     * : Flush specific cache group only.
     * ---
     * options:
     *   - settings
     *   - scans
     *   - consent
     *   - dsr
     *   - stats
     * ---
     *
     * [--yes]
     * : Skip confirmation prompt.
     *
     * ## EXAMPLES
     *
     *     # Flush all cache
     *     wp complyflow cache flush --yes
     *
     *     # Flush specific group
     *     wp complyflow cache flush --group=settings
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function flush(array $args, array $assoc_args): void {
        $group = $assoc_args['group'] ?? null;

        // Confirm action
        if (!isset($assoc_args['yes'])) {
            $message = $group
                ? "Are you sure you want to flush cache group '{$group}'?"
                : 'Are you sure you want to flush all cache?';
            
            if (!$this->confirm($message)) {
                $this->warning('Operation cancelled.');
                return;
            }
        }

        if ($group) {
            $result = $this->cache->flush_group($group);
            
            if ($result) {
                $this->success("Cache group '{$group}' flushed successfully.");
            } else {
                $this->error("Failed to flush cache group '{$group}'.");
            }
        } else {
            $result = $this->cache->flush();
            
            if ($result) {
                $this->success('All cache flushed successfully.');
            } else {
                $this->error('Failed to flush cache.');
            }
        }
    }

    /**
     * Display cache statistics.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - csv
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow cache stats
     *     wp complyflow cache stats --format=json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function stats(array $args, array $assoc_args): void {
        $format = $assoc_args['format'] ?? 'table';
        
        $stats = $this->cache->get_stats();
        
        if (empty($stats)) {
            $this->warning('No cache statistics available.');
            return;
        }

        $items = [];
        foreach ($stats as $group => $data) {
            $items[] = [
                'group' => $group,
                'keys' => $data['keys'],
                'size' => size_format($data['size'], 2),
                'hits' => $data['hits'],
                'misses' => $data['misses'],
                'hit_rate' => sprintf('%.2f%%', $data['hit_rate']),
            ];
        }

        $this->format_items($format, $items, [
            'group',
            'keys',
            'size',
            'hits',
            'misses',
            'hit_rate',
        ]);
    }

    /**
     * Warm up cache by preloading frequently accessed data.
     *
     * ## OPTIONS
     *
     * [--group=<group>]
     * : Warm specific cache group only.
     * ---
     * options:
     *   - settings
     *   - scans
     *   - consent
     *   - dsr
     *   - stats
     * ---
     *
     * ## EXAMPLES
     *
     *     # Warm all cache
     *     wp complyflow cache warm
     *
     *     # Warm specific group
     *     wp complyflow cache warm --group=stats
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function warm(array $args, array $assoc_args): void {
        $group = $assoc_args['group'] ?? null;

        $this->log('Starting cache warm-up...');

        if ($group) {
            $result = $this->warm_group($group);
            
            if ($result) {
                $this->success("Cache group '{$group}' warmed successfully.");
            } else {
                $this->error("Failed to warm cache group '{$group}'.");
            }
        } else {
            $groups = ['settings', 'scans', 'consent', 'dsr', 'stats'];
            $progress = $this->progress('Warming cache', count($groups));

            foreach ($groups as $group_name) {
                $this->warm_group($group_name);
                $progress->tick();
            }

            $progress->finish();
            $this->success('All cache warmed successfully.');
        }
    }

    /**
     * Get cache value for a specific key.
     *
     * ## OPTIONS
     *
     * <key>
     * : Cache key to retrieve.
     *
     * [--group=<group>]
     * : Cache group. Default: default
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: json
     * options:
     *   - json
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow cache get scan_stats --group=stats
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function get(array $args, array $assoc_args): void {
        $key = $args[0];
        $group = $assoc_args['group'] ?? 'default';
        $format = $assoc_args['format'] ?? 'json';

        $value = $this->cache->get($key, $group);

        if (false === $value) {
            $this->warning("Cache key '{$key}' not found in group '{$group}'.");
            return;
        }

        if ($format === 'json') {
            WP_CLI::log(wp_json_encode($value, JSON_PRETTY_PRINT));
        } elseif ($format === 'yaml') {
            WP_CLI::log(\WP_CLI\Utils\mustache_render('template.mustache', $value));
        }
    }

    /**
     * Delete cache value for a specific key.
     *
     * ## OPTIONS
     *
     * <key>
     * : Cache key to delete.
     *
     * [--group=<group>]
     * : Cache group. Default: default
     *
     * ## EXAMPLES
     *
     *     wp complyflow cache delete scan_stats --group=stats
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function delete(array $args, array $assoc_args): void {
        $key = $args[0];
        $group = $assoc_args['group'] ?? 'default';

        $result = $this->cache->delete($key, $group);

        if ($result) {
            $this->success("Cache key '{$key}' deleted from group '{$group}'.");
        } else {
            $this->error("Failed to delete cache key '{$key}' from group '{$group}'.");
        }
    }

    /**
     * Warm cache for specific group.
     *
     * @param string $group Cache group.
     * @return bool
     */
    private function warm_group(string $group): bool {
        switch ($group) {
            case 'settings':
                return $this->cache->warm_settings();

            case 'scans':
                return $this->cache->warm_scans();

            case 'consent':
                return $this->cache->warm_consent();

            case 'dsr':
                return $this->cache->warm_dsr();

            case 'stats':
                return $this->cache->warm_stats();

            default:
                return false;
        }
    }
}
