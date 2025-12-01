<?php
/**
 * Scan WP-CLI Command
 *
 * WP-CLI commands for accessibility scanning operations.
 *
 * @package ComplyFlow\CLI
 * @since 1.0.0
 */

namespace ComplyFlow\CLI;

use ComplyFlow\Database\ScanRepository;
use ComplyFlow\Modules\Accessibility\Scanner;
use ComplyFlow\Modules\Accessibility\ScheduledScanManager;
use ComplyFlow\Core\Repositories\SettingsRepository;
use WP_CLI;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage accessibility scans.
 *
 * ## EXAMPLES
 *
 *     # Run a scan on a specific URL
 *     $ wp complyflow scan run https://example.com/page
 *
 *     # List all scan results
 *     $ wp complyflow scan list
 *
 *     # Delete old scan results
 *     $ wp complyflow scan cleanup --days=30
 *
 * @since 1.0.0
 */
class ScanCommand extends BaseCommand {
    /**
     * Scan repository
     *
     * @var ScanRepository
     */
    private ScanRepository $repository;

    /**
     * Scanner instance
     *
     * @var Scanner
     */
    private Scanner $scanner;

    /**
     * Scheduled scan manager
     *
     * @var ScheduledScanManager
     */
    private ScheduledScanManager $scheduled_manager;

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new ScanRepository();
        $this->scanner = new Scanner();
        
        $settings = new SettingsRepository();
        $this->scheduled_manager = new ScheduledScanManager($this->scanner, $settings);
    }

    /**
     * Run accessibility scan on a URL.
     *
     * ## OPTIONS
     *
     * <url>
     * : The URL to scan.
     *
     * [--format=<format>]
     * : Output format (table, json, csv, yaml).
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
     *     wp complyflow scan run https://example.com/about
     *     wp complyflow scan run https://example.com --format=json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function run(array $args, array $assoc_args): void {
        list($url) = $args;
        $format = $assoc_args['format'] ?? 'table';

        $url = esc_url_raw($url);

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->error('Invalid URL provided.');
        }

        $this->log("Starting accessibility scan for: $url");

        // Perform scan (this would integrate with actual scanner)
        $results = $this->perform_scan($url);

        if (is_wp_error($results)) {
            $this->error($results->get_error_message());
        }

        // Save results
        $scan_id = $this->repository->create_scan([
            'page_url' => $url,
            'score' => $results['score'],
            'issue_count' => $results['issue_count'],
            'results' => $results,
        ]);

        if ($scan_id === false) {
            $this->error('Failed to save scan results.');
        }

        $this->success("Scan completed. ID: $scan_id");

        // Output results
        if ($format === 'json') {
            WP_CLI::print_value($results, ['format' => 'json']);
        } else {
            $this->log("\nScan Results:");
            $this->log("Score: {$results['score']}/100");
            $this->log("Issues: {$results['issue_count']}");
            $this->log("\nIssue Breakdown:");
            $this->log("  Critical: {$results['summary']['critical']}");
            $this->log("  Serious: {$results['summary']['serious']}");
            $this->log("  Moderate: {$results['summary']['moderate']}");
            $this->log("  Minor: {$results['summary']['minor']}");
        }
    }

    /**
     * List all scan results.
     *
     * ## OPTIONS
     *
     * [--url=<url>]
     * : Filter by URL.
     *
     * [--limit=<number>]
     * : Number of results to show.
     * ---
     * default: 20
     * ---
     *
     * [--format=<format>]
     * : Output format.
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
     *     wp complyflow scan list
     *     wp complyflow scan list --url=https://example.com
     *     wp complyflow scan list --format=json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function list(array $args, array $assoc_args): void {
        $args_query = [
            'limit' => (int) ($assoc_args['limit'] ?? 20),
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];

        if (isset($assoc_args['url'])) {
            $args_query['where']['page_url'] = esc_url_raw($assoc_args['url']);
        }

        $scans = $this->repository->find_all($args_query);

        if (empty($scans)) {
            $this->warning('No scan results found.');
            return;
        }

        $items = array_map(function ($scan) {
            return [
                'ID' => $scan->id,
                'URL' => $scan->page_url,
                'Score' => $scan->score,
                'Issues' => $scan->issue_count,
                'Date' => $scan->created_at,
            ];
        }, $scans);

        $this->format_items(
            $items,
            ['ID', 'URL', 'Score', 'Issues', 'Date'],
            $assoc_args['format'] ?? 'table'
        );
    }

    /**
     * Get scan statistics.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: table
     * options:
     *   - table
     *   - json
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow scan stats
     *     wp complyflow scan stats --format=json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function stats(array $args, array $assoc_args): void {
        $stats = $this->repository->get_statistics();

        if ($assoc_args['format'] === 'json' || $assoc_args['format'] === 'yaml') {
            WP_CLI::print_value($stats, ['format' => $assoc_args['format']]);
        } else {
            $this->log("\nAccessibility Scan Statistics:");
            $this->log("Total Scans: " . $stats['total_scans']);
            $this->log("Total Issues: " . $stats['total_issues']);
            $this->log("Average Score: " . $stats['average_score']);
            $this->log("Pages Scanned: " . $stats['pages_scanned']);
            $this->log("\nIssues by Severity:");
            $this->log("  Critical: " . $stats['by_severity']['critical']);
            $this->log("  Serious: " . $stats['by_severity']['serious']);
            $this->log("  Moderate: " . $stats['by_severity']['moderate']);
            $this->log("  Minor: " . $stats['by_severity']['minor']);
        }
    }

    /**
     * Delete old scan results.
     *
     * ## OPTIONS
     *
     * [--days=<number>]
     * : Delete scans older than this many days.
     * ---
     * default: 90
     * ---
     *
     * [--yes]
     * : Skip confirmation.
     *
     * ## EXAMPLES
     *
     *     wp complyflow scan cleanup --days=30
     *     wp complyflow scan cleanup --days=90 --yes
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function cleanup(array $args, array $assoc_args): void {
        $days = (int) ($assoc_args['days'] ?? 90);

        if (!isset($assoc_args['yes'])) {
            $this->confirm("Delete scan results older than $days days?");
        }

        $deleted = $this->repository->delete_old_scans($days);

        $this->success("Deleted $deleted scan result(s).");
    }

    /**
     * Enable scheduled accessibility scans.
     *
     * ## OPTIONS
     *
     * [--frequency=<frequency>]
     * : Scan frequency.
     * ---
     * default: daily
     * options:
     *   - hourly
     *   - twicedaily
     *   - daily
     *   - weekly
     *   - monthly
     * ---
     *
     * [--url=<url>]
     * : URL to scan. Can be specified multiple times.
     *
     * ## EXAMPLES
     *
     *     wp complyflow scan schedule --frequency=daily
     *     wp complyflow scan schedule --frequency=weekly --url=https://example.com
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function schedule(array $args, array $assoc_args): void {
        $frequency = $assoc_args['frequency'] ?? 'daily';
        $urls = isset($assoc_args['url']) ? (array) $assoc_args['url'] : [home_url()];

        // Validate frequency
        $valid_frequencies = ['hourly', 'twicedaily', 'daily', 'weekly', 'monthly'];
        if (!in_array($frequency, $valid_frequencies, true)) {
            $this->error('Invalid frequency. Must be one of: ' . implode(', ', $valid_frequencies));
        }

        // Update settings
        $settings = new SettingsRepository();
        $settings->set('accessibility_scheduled_scans_enabled', true);
        $settings->set('accessibility_scheduled_scans_frequency', $frequency);
        $settings->set('accessibility_scheduled_scans_urls', array_map('esc_url_raw', $urls));

        // Schedule scans
        $result = $this->scheduled_manager->schedule_scans();

        if ($result) {
            $next_scan = $this->scheduled_manager->get_next_scheduled_time();
            $next_time = $next_scan ? wp_date(get_option('date_format') . ' ' . get_option('time_format'), $next_scan) : 'unknown';
            
            $this->success("Scheduled scans enabled with $frequency frequency.");
            $this->log("Next scan: $next_time");
            $this->log("URLs to scan: " . implode(', ', $urls));
        } else {
            $this->error('Failed to schedule scans.');
        }
    }

    /**
     * Disable scheduled accessibility scans.
     *
     * ## EXAMPLES
     *
     *     wp complyflow scan unschedule
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function unschedule(array $args, array $assoc_args): void {
        $settings = new SettingsRepository();
        $settings->set('accessibility_scheduled_scans_enabled', false);

        $result = $this->scheduled_manager->unschedule_scans();

        if ($result) {
            $this->success('Scheduled scans disabled.');
        } else {
            $this->warning('No scheduled scans to disable.');
        }
    }

    /**
     * Run scheduled scans manually.
     *
     * ## EXAMPLES
     *
     *     wp complyflow scan run-scheduled
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function run_scheduled(array $args, array $assoc_args): void {
        $this->log('Running scheduled accessibility scans...');

        $this->scheduled_manager->run_scheduled_scans();

        $results = $this->scheduled_manager->get_last_results();

        if ($results) {
            $this->success('Scheduled scans completed.');
            
            foreach ($results as $result) {
                $this->log("\nURL: {$result['url']}");
                
                if ($result['success']) {
                    $this->log("✓ Score: {$result['score']}/100");
                    $this->log("  Issues: " . implode(', ', array_map(function($severity, $count) {
                        return ucfirst($severity) . ': ' . $count;
                    }, array_keys($result['issues']), $result['issues'])));
                } else {
                    $this->log("✗ Error: {$result['error']}");
                }
            }
        } else {
            $this->warning('No URLs configured for scheduled scans.');
        }
    }

    /**
     * Show scheduled scan status.
     *
     * ## EXAMPLES
     *
     *     wp complyflow scan status
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function status(array $args, array $assoc_args): void {
        $settings = new SettingsRepository();
        $enabled = $settings->get('accessibility_scheduled_scans_enabled', false);

        if (!$enabled) {
            $this->log('Status: Disabled');
            return;
        }

        $frequency = $settings->get('accessibility_scheduled_scans_frequency', 'daily');
        $urls = $settings->get('accessibility_scheduled_scans_urls', []);
        $next_scan = $this->scheduled_manager->get_next_scheduled_time();
        $last_scan = $this->scheduled_manager->get_last_scan_time();

        $this->log('Status: Enabled');
        $this->log("Frequency: $frequency");
        $this->log("URLs: " . (count($urls) > 0 ? implode(', ', $urls) : 'None configured'));
        
        if ($next_scan) {
            $next_time = wp_date(get_option('date_format') . ' ' . get_option('time_format'), $next_scan);
            $this->log("Next scan: $next_time");
        }

        if ($last_scan) {
            $last_time = human_time_diff($last_scan, time()) . ' ago';
            $this->log("Last scan: $last_time");
        }
    }

    /**
     * Perform scan (placeholder)
     *
     * @param string $url URL to scan.
     * @return array|\WP_Error
     */
    private function perform_scan(string $url) {
        // Mock scan results
        return [
            'score' => rand(60, 100),
            'issue_count' => rand(0, 20),
            'summary' => [
                'critical' => rand(0, 3),
                'serious' => rand(0, 5),
                'moderate' => rand(0, 7),
                'minor' => rand(0, 10),
            ],
        ];
    }
}
