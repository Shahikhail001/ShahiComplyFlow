<?php
/**
 * Scan REST Controller
 *
 * Handles REST API endpoints for accessibility scans.
 *
 * @package ComplyFlow\API
 * @since 1.0.0
 */

namespace ComplyFlow\API;

use ComplyFlow\Database\ScanRepository;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Scan REST Controller Class
 *
 * @since 1.0.0
 */
class ScanController extends RestController {
    /**
     * Scan repository
     *
     * @var ScanRepository
     */
    private ScanRepository $repository;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->rest_base = 'scan';
        $this->repository = new ScanRepository();
    }

    /**
     * Register routes
     *
     * @return void
     */
    public function register_routes(): void {
        // Admin: Run scan
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods' => 'POST',
                'callback' => [$this, 'run_scan'],
                'permission_callback' => [$this, 'authorize_manage'],
                'args' => [
                    'url' => [
                        'required' => true,
                        'type' => 'string',
                        'format' => 'uri',
                    ],
                ],
            ],
        ]);

        // Admin: Get scan results
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_scans'],
                'permission_callback' => [$this, 'authorize_manage'],
                'args' => $this->get_collection_args(),
            ],
        ]);

        // Admin: Get single scan
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_scan'],
                'permission_callback' => [$this, 'authorize_manage'],
            ],
        ]);

        // Admin: Get scan statistics
        register_rest_route($this->namespace, '/' . $this->rest_base . '/stats', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_statistics'],
                'permission_callback' => [$this, 'authorize_manage'],
            ],
        ]);

        // Admin: Delete scan
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
            [
                'methods' => 'DELETE',
                'callback' => [$this, 'delete_scan'],
                'permission_callback' => [$this, 'authorize_admin'],
            ],
        ]);
    }

    /**
     * Run accessibility scan
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function run_scan(WP_REST_Request $request) {
        $url = esc_url_raw($request->get_param('url'));

        // Validate URL belongs to this site
        if (strpos($url, home_url()) !== 0) {
            return $this->error_response(
                __('Can only scan pages from this website.', 'complyflow'),
                400
            );
        }

        // Check if scan already running
        $running = get_transient('complyflow_scan_running_' . md5($url));
        if ($running) {
            return $this->error_response(
                __('A scan is already running for this URL.', 'complyflow'),
                409
            );
        }

        // Set running flag
        set_transient('complyflow_scan_running_' . md5($url), true, 300);

        // Run scan (this would call the actual scanner)
        $results = $this->perform_scan($url);

        // Clear running flag
        delete_transient('complyflow_scan_running_' . md5($url));

        if (is_wp_error($results)) {
            return $results;
        }

        // Save scan results
        $scan_id = $this->repository->create_scan([
            'page_url' => $url,
            'score' => $results['score'],
            'issue_count' => $results['issue_count'],
            'results' => $results,
        ]);

        if ($scan_id === false) {
            return $this->error_response(
                __('Failed to save scan results.', 'complyflow'),
                500
            );
        }

        $this->log_request($request, 'run_scan');

        return $this->success_response(
            array_merge(['id' => $scan_id], $results),
            __('Scan completed successfully.', 'complyflow'),
            201
        );
    }

    /**
     * Get scan results
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response Response.
     */
    public function get_scans(WP_REST_Request $request): WP_REST_Response {
        $pagination = $this->get_pagination_params($request);
        $sort = $this->get_sort_params($request, ['id', 'created_at', 'score'], 'created_at');

        $args = [
            'limit' => $pagination['limit'],
            'offset' => $pagination['offset'],
            'orderby' => $sort['orderby'],
            'order' => $sort['order'],
        ];

        // Filter by URL
        if ($request->get_param('url')) {
            $args['where']['page_url'] = esc_url_raw($request->get_param('url'));
        }

        $scans = $this->repository->find_all($args);
        $total = $this->repository->count($args);

        return $this->paginated_response($scans, $total, $pagination['page'], $pagination['limit']);
    }

    /**
     * Get single scan
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function get_scan(WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $scan = $this->repository->find($id);

        if (!$scan) {
            return $this->error_response(__('Scan not found.', 'complyflow'), 404);
        }

        // Decode results JSON
        $scan->results = json_decode($scan->results, true);

        return $this->success_response($scan);
    }

    /**
     * Get scan statistics
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response Response.
     */
    public function get_statistics(WP_REST_Request $request): WP_REST_Response {
        $stats = $this->repository->get_statistics();
        return $this->success_response($stats);
    }

    /**
     * Delete scan
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function delete_scan(WP_REST_Request $request) {
        $id = (int) $request->get_param('id');

        if (!$this->repository->exists($id)) {
            return $this->error_response(__('Scan not found.', 'complyflow'), 404);
        }

        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->error_response(__('Failed to delete scan.', 'complyflow'), 500);
        }

        $this->log_request($request, 'delete_scan');

        return $this->success_response(null, __('Scan deleted successfully.', 'complyflow'));
    }

    /**
     * Get collection arguments
     *
     * @return array
     */
    private function get_collection_args(): array {
        return [
            'page' => [
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1,
            ],
            'per_page' => [
                'type' => 'integer',
                'default' => 20,
                'minimum' => 1,
                'maximum' => 100,
            ],
            'orderby' => [
                'type' => 'string',
                'default' => 'created_at',
                'enum' => ['id', 'created_at', 'score'],
            ],
            'order' => [
                'type' => 'string',
                'default' => 'DESC',
                'enum' => ['ASC', 'DESC'],
            ],
            'url' => [
                'type' => 'string',
                'format' => 'uri',
            ],
        ];
    }

    /**
     * Perform accessibility scan (placeholder)
     *
     * @param string $url URL to scan.
     * @return array|WP_Error Scan results or error.
     */
    private function perform_scan(string $url) {
        // This would integrate with the actual accessibility scanner
        // For now, return mock data
        
        return [
            'score' => 85,
            'issue_count' => 12,
            'summary' => [
                'critical' => 2,
                'serious' => 4,
                'moderate' => 3,
                'minor' => 3,
            ],
            'issues' => [
                [
                    'type' => 'missing_alt',
                    'severity' => 'serious',
                    'message' => 'Image missing alt text',
                    'selector' => 'img.header-logo',
                    'wcag' => '1.1.1',
                ],
            ],
        ];
    }
}
