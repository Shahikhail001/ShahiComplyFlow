<?php
/**
 * DSR WP-CLI Command
 *
 * WP-CLI commands for Data Subject Request processing.
 *
 * @package ComplyFlow\CLI
 * @since 1.0.0
 */

namespace ComplyFlow\CLI;

use ComplyFlow\Database\DSRRepository;
use WP_CLI;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage Data Subject Requests.
 *
 * ## EXAMPLES
 *
 *     # List all DSR requests
 *     $ wp complyflow dsr list
 *
 *     # Process a DSR request
 *     $ wp complyflow dsr process 123
 *
 *     # Get overdue requests
 *     $ wp complyflow dsr overdue
 *
 * @since 1.0.0
 */
class DSRCommand extends BaseCommand {
    /**
     * DSR repository
     *
     * @var DSRRepository
     */
    private DSRRepository $repository;

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new DSRRepository();
    }

    /**
     * List DSR requests.
     *
     * ## OPTIONS
     *
     * [--status=<status>]
     * : Filter by status.
     * ---
     * options:
     *   - pending
     *   - verified
     *   - processing
     *   - completed
     *   - rejected
     * ---
     *
     * [--type=<type>]
     * : Filter by request type.
     * ---
     * options:
     *   - access
     *   - erasure
     *   - rectify
     *   - portability
     * ---
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
     *     wp complyflow dsr list
     *     wp complyflow dsr list --status=pending
     *     wp complyflow dsr list --type=erasure --format=json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function list(array $args, array $assoc_args): void {
        $query_args = [
            'limit' => (int) ($assoc_args['limit'] ?? 20),
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];

        if (isset($assoc_args['status'])) {
            $query_args['where']['status'] = sanitize_text_field($assoc_args['status']);
        }

        if (isset($assoc_args['type'])) {
            $query_args['where']['request_type'] = sanitize_text_field($assoc_args['type']);
        }

        $requests = $this->repository->find_all($query_args);

        if (empty($requests)) {
            $this->warning('No DSR requests found.');
            return;
        }

        $items = array_map(function ($request) {
            return [
                'ID' => $request->id,
                'Type' => ucfirst($request->request_type),
                'Email' => $request->email,
                'Status' => ucfirst($request->status),
                'Created' => $request->created_at,
            ];
        }, $requests);

        $this->format_items(
            $items,
            ['ID', 'Type', 'Email', 'Status', 'Created'],
            $assoc_args['format'] ?? 'table'
        );
    }

    /**
     * Get DSR statistics.
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
     *     wp complyflow dsr stats
     *     wp complyflow dsr stats --format=json
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function stats(array $args, array $assoc_args): void {
        $stats = $this->repository->get_statistics();
        $format = $assoc_args['format'] ?? 'table';

        if ($format === 'json' || $format === 'yaml') {
            WP_CLI::print_value($stats, ['format' => $format]);
        } else {
            $this->log("\nDSR Statistics:");
            $this->log("Total Requests: " . $stats['total']);
            $this->log("Pending: " . $stats['pending']);
            $this->log("Average Processing Time: " . $stats['avg_processing_hours'] . " hours");
            
            $this->log("\nBy Status:");
            foreach ($stats['by_status'] as $status) {
                $this->log("  " . ucfirst($status->status) . ": " . $status->count);
            }
            
            $this->log("\nBy Type:");
            foreach ($stats['by_type'] as $type) {
                $this->log("  " . ucfirst($type->request_type) . ": " . $type->count);
            }
        }
    }

    /**
     * Process a DSR request.
     *
     * ## OPTIONS
     *
     * <id>
     * : Request ID to process.
     *
     * [--status=<status>]
     * : New status.
     * ---
     * default: completed
     * options:
     *   - processing
     *   - completed
     *   - rejected
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow dsr process 123
     *     wp complyflow dsr process 123 --status=completed
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function process(array $args, array $assoc_args): void {
        list($id) = $args;
        $id = (int) $id;
        $status = $assoc_args['status'] ?? 'completed';

        $request = $this->repository->find($id);

        if (!$request) {
            $this->error("DSR request #$id not found.");
        }

        $this->log("Processing DSR request #$id ({$request->request_type})...");

        // Update status
        $updated = $this->repository->update_status($id, $status);

        if (!$updated) {
            $this->error("Failed to update request status.");
        }

        $this->success("DSR request #$id marked as $status.");
    }

    /**
     * Get overdue DSR requests.
     *
     * ## OPTIONS
     *
     * [--days=<number>]
     * : Days before considered overdue.
     * ---
     * default: 30
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
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow dsr overdue
     *     wp complyflow dsr overdue --days=15
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function overdue(array $args, array $assoc_args): void {
        $days = (int) ($assoc_args['days'] ?? 30);
        $requests = $this->repository->get_overdue_requests($days);

        if (empty($requests)) {
            $this->success("No overdue requests found.");
            return;
        }

        $this->warning(count($requests) . " overdue request(s) found:");

        $items = array_map(function ($request) use ($days) {
            $created = strtotime($request->created_at);
            $age_days = floor((time() - $created) / DAY_IN_SECONDS);
            
            return [
                'ID' => $request->id,
                'Type' => ucfirst($request->request_type),
                'Email' => $request->email,
                'Status' => ucfirst($request->status),
                'Age (days)' => $age_days,
                'Created' => $request->created_at,
            ];
        }, $requests);

        $this->format_items(
            $items,
            ['ID', 'Type', 'Email', 'Status', 'Age (days)', 'Created'],
            $assoc_args['format'] ?? 'table'
        );
    }

    /**
     * Delete old completed DSR requests.
     *
     * ## OPTIONS
     *
     * [--days=<number>]
     * : Delete requests completed more than this many days ago.
     * ---
     * default: 365
     * ---
     *
     * [--yes]
     * : Skip confirmation.
     *
     * ## EXAMPLES
     *
     *     wp complyflow dsr cleanup --days=365
     *     wp complyflow dsr cleanup --days=730 --yes
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function cleanup(array $args, array $assoc_args): void {
        $days = (int) ($assoc_args['days'] ?? 365);

        if (!isset($assoc_args['yes'])) {
            $this->confirm("Delete completed DSR requests older than $days days?");
        }

        $deleted = $this->repository->delete_old_completed($days);

        $this->success("Deleted $deleted completed DSR request(s).");
    }
}
