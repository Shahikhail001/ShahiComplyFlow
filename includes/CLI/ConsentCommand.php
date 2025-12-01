<?php
/**
 * Consent WP-CLI Command
 *
 * WP-CLI commands for consent log management.
 *
 * @package ComplyFlow\CLI
 * @since 1.0.0
 */

namespace ComplyFlow\CLI;

use ComplyFlow\Database\ConsentRepository;
use WP_CLI;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage consent logs.
 *
 * ## EXAMPLES
 *
 *     # List all consent logs
 *     $ wp complyflow consent list
 *
 *     # Get consent statistics
 *     $ wp complyflow consent stats
 *
 *     # Export consent logs for a user
 *     $ wp complyflow consent export 123
 *
 * @since 1.0.0
 */
class ConsentCommand extends BaseCommand {
    /**
     * Consent repository
     *
     * @var ConsentRepository
     */
    private ConsentRepository $repository;

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new ConsentRepository();
    }

    /**
     * List consent logs.
     *
     * ## OPTIONS
     *
     * [--user-id=<id>]
     * : Filter by user ID.
     *
     * [--consent-given=<boolean>]
     * : Filter by consent status (true/false).
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
     *     wp complyflow consent list
     *     wp complyflow consent list --user-id=123
     *     wp complyflow consent list --consent-given=true --format=json
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

        if (isset($assoc_args['user-id'])) {
            $query_args['where']['user_id'] = (int) $assoc_args['user-id'];
        }

        if (isset($assoc_args['consent-given'])) {
            $query_args['where']['consent_given'] = filter_var($assoc_args['consent-given'], FILTER_VALIDATE_BOOLEAN);
        }

        $consents = $this->repository->find_all($query_args);

        if (empty($consents)) {
            $this->warning('No consent logs found.');
            return;
        }

        $items = array_map(function ($consent) {
            return [
                'ID' => $consent->id,
                'User ID' => $consent->user_id ?: 'Guest',
                'Consent' => $consent->consent_given ? 'Accepted' : 'Rejected',
                'IP' => $consent->ip_address,
                'Location' => $consent->geo_location ?: 'N/A',
                'Date' => $consent->created_at,
            ];
        }, $consents);

        $this->format_items(
            $items,
            ['ID', 'User ID', 'Consent', 'IP', 'Location', 'Date'],
            $assoc_args['format'] ?? 'table'
        );
    }

    /**
     * Get consent statistics.
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
     *     wp complyflow consent stats
     *     wp complyflow consent stats --format=json
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
            $this->log("\nConsent Statistics:");
            $this->log("Total Consents: " . $stats['total']);
            $this->log("Accepted: " . $stats['accepted']);
            $this->log("Rejected: " . $stats['rejected']);
            $this->log("Acceptance Rate: " . $stats['acceptance_rate'] . "%");
            
            if (!empty($stats['by_geolocation'])) {
                $this->log("\nTop Locations:");
                foreach (array_slice($stats['by_geolocation'], 0, 5) as $geo) {
                    $location = $geo->geo_location ?: 'Unknown';
                    $this->log("  {$location}: {$geo->count}");
                }
            }
        }
    }

    /**
     * Export consent logs for a user.
     *
     * ## OPTIONS
     *
     * <user-id>
     * : User ID to export data for.
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: json
     * options:
     *   - json
     *   - csv
     * ---
     *
     * ## EXAMPLES
     *
     *     wp complyflow consent export 123
     *     wp complyflow consent export 123 --format=csv
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function export(array $args, array $assoc_args): void {
        list($user_id) = $args;
        $user_id = (int) $user_id;

        if (!get_userdata($user_id)) {
            $this->error("User ID $user_id not found.");
        }

        $consents = $this->repository->export_user_data($user_id);

        if (empty($consents)) {
            $this->warning("No consent logs found for user $user_id.");
            return;
        }

        WP_CLI::print_value($consents, ['format' => $assoc_args['format'] ?? 'json']);
    }

    /**
     * Anonymize consent logs for a user (GDPR).
     *
     * ## OPTIONS
     *
     * <user-id>
     * : User ID to anonymize data for.
     *
     * [--yes]
     * : Skip confirmation.
     *
     * ## EXAMPLES
     *
     *     wp complyflow consent anonymize 123
     *     wp complyflow consent anonymize 123 --yes
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function anonymize(array $args, array $assoc_args): void {
        list($user_id) = $args;
        $user_id = (int) $user_id;

        if (!get_userdata($user_id)) {
            $this->error("User ID $user_id not found.");
        }

        if (!isset($assoc_args['yes'])) {
            $this->confirm("Anonymize all consent logs for user $user_id? This cannot be undone.");
        }

        $anonymized = $this->repository->anonymize_user_data($user_id);

        $this->success("Anonymized $anonymized consent log(s) for user $user_id.");
    }

    /**
     * Delete old consent logs.
     *
     * ## OPTIONS
     *
     * [--days=<number>]
     * : Delete logs older than this many days.
     * ---
     * default: 365
     * ---
     *
     * [--yes]
     * : Skip confirmation.
     *
     * ## EXAMPLES
     *
     *     wp complyflow consent cleanup --days=365
     *     wp complyflow consent cleanup --days=730 --yes
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Named arguments.
     * @return void
     */
    public function cleanup(array $args, array $assoc_args): void {
        $days = (int) ($assoc_args['days'] ?? 365);

        if (!isset($assoc_args['yes'])) {
            $this->confirm("Delete consent logs older than $days days?");
        }

        $deleted = $this->repository->delete_old_records($days);

        $this->success("Deleted $deleted consent log(s).");
    }
}
