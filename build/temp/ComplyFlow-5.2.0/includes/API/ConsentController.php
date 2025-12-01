<?php
/**
 * Consent REST Controller
 *
 * Handles REST API endpoints for consent management.
 *
 * @package ComplyFlow\API
 * @since 1.0.0
 */

namespace ComplyFlow\API;

use ComplyFlow\Database\ConsentRepository;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Consent REST Controller Class
 *
 * @since 1.0.0
 */
class ConsentController extends RestController {
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
        parent::__construct();
        $this->rest_base = 'consent';
        $this->repository = new ConsentRepository();
    }

    /**
     * Register routes
     *
     * @return void
     */
    public function register_routes(): void {
        // Public: Save consent
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods' => 'POST',
                'callback' => [$this, 'save_consent'],
                'permission_callback' => '__return_true', // Public endpoint
                'args' => $this->get_save_consent_args(),
            ],
        ]);

        // Admin: Get consent logs
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_consents'],
                'permission_callback' => [$this, 'authorize_manage'],
                'args' => $this->get_collection_args(),
            ],
        ]);

        // Admin: Get single consent
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_consent'],
                'permission_callback' => [$this, 'authorize_manage'],
                'args' => [
                    'id' => [
                        'required' => true,
                        'type' => 'integer',
                    ],
                ],
            ],
        ]);

        // Admin: Get consent statistics
        register_rest_route($this->namespace, '/' . $this->rest_base . '/stats', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_statistics'],
                'permission_callback' => [$this, 'authorize_manage'],
            ],
        ]);

        // Admin: Delete consent log
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
            [
                'methods' => 'DELETE',
                'callback' => [$this, 'delete_consent'],
                'permission_callback' => [$this, 'authorize_admin'],
                'args' => [
                    'id' => [
                        'required' => true,
                        'type' => 'integer',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Save user consent
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function save_consent(WP_REST_Request $request) {
        // Rate limiting
        $ip = $request->get_header('X-Forwarded-For') ?: $_SERVER['REMOTE_ADDR'];
        $rate_limit = $this->check_rate_limit('consent_' . $ip, 10, 60);
        if (is_wp_error($rate_limit)) {
            return $rate_limit;
        }

        $categories = $request->get_param('categories');
        $consent_given = $request->get_param('consent_given');

        // Validate categories
        if (!is_array($categories)) {
            return $this->error_response(__('Invalid categories format.', 'complyflow'), 400);
        }

        // Prepare consent data
        $data = [
            'user_id' => get_current_user_id(),
            'session_id' => $this->get_session_id(),
            'ip_address' => $this->anonymize_ip($ip),
            'categories' => wp_json_encode($categories),
            'consent_given' => (bool) $consent_given,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'geo_location' => $this->get_geo_location($ip),
            'created_at' => current_time('mysql', true),
        ];

        $consent_id = $this->repository->insert($data);

        if ($consent_id === false) {
            return $this->error_response(
                __('Failed to save consent.', 'complyflow'),
                500,
                $this->repository->get_last_error()
            );
        }

        $this->log_request($request, 'save_consent');

        return $this->success_response(
            ['id' => $consent_id],
            __('Consent saved successfully.', 'complyflow'),
            201
        );
    }

    /**
     * Get consent logs
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function get_consents(WP_REST_Request $request) {
        $pagination = $this->get_pagination_params($request);
        $sort = $this->get_sort_params($request, ['id', 'created_at', 'user_id'], 'created_at');

        $args = [
            'limit' => $pagination['limit'],
            'offset' => $pagination['offset'],
            'orderby' => $sort['orderby'],
            'order' => $sort['order'],
        ];

        // Filter by user ID
        if ($request->get_param('user_id')) {
            $args['where']['user_id'] = (int) $request->get_param('user_id');
        }

        // Filter by consent status
        if ($request->get_param('consent_given') !== null) {
            $args['where']['consent_given'] = (bool) $request->get_param('consent_given');
        }

        $consents = $this->repository->find_all($args);
        $total = $this->repository->count($args);

        return $this->paginated_response($consents, $total, $pagination['page'], $pagination['limit']);
    }

    /**
     * Get single consent log
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function get_consent(WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $consent = $this->repository->find($id);

        if (!$consent) {
            return $this->error_response(__('Consent log not found.', 'complyflow'), 404);
        }

        return $this->success_response($consent);
    }

    /**
     * Get consent statistics
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response Response.
     */
    public function get_statistics(WP_REST_Request $request): WP_REST_Response {
        $stats = $this->repository->get_statistics();
        return $this->success_response($stats);
    }

    /**
     * Delete consent log
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function delete_consent(WP_REST_Request $request) {
        $id = (int) $request->get_param('id');

        if (!$this->repository->exists($id)) {
            return $this->error_response(__('Consent log not found.', 'complyflow'), 404);
        }

        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return $this->error_response(__('Failed to delete consent log.', 'complyflow'), 500);
        }

        $this->log_request($request, 'delete_consent');

        return $this->success_response(null, __('Consent log deleted successfully.', 'complyflow'));
    }

    /**
     * Get save consent arguments
     *
     * @return array
     */
    private function get_save_consent_args(): array {
        return [
            'categories' => [
                'required' => true,
                'type' => 'array',
                'description' => __('Consent categories (necessary, functional, analytics, marketing)', 'complyflow'),
            ],
            'consent_given' => [
                'required' => true,
                'type' => 'boolean',
                'description' => __('Whether consent was given or rejected', 'complyflow'),
            ],
        ];
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
                'enum' => ['id', 'created_at', 'user_id'],
            ],
            'order' => [
                'type' => 'string',
                'default' => 'DESC',
                'enum' => ['ASC', 'DESC'],
            ],
            'user_id' => [
                'type' => 'integer',
            ],
            'consent_given' => [
                'type' => 'boolean',
            ],
        ];
    }

    /**
     * Get or create session ID
     *
     * @return string
     */
    private function get_session_id(): string {
        if (isset($_COOKIE['complyflow_session'])) {
            return sanitize_text_field($_COOKIE['complyflow_session']);
        }

        $session_id = wp_generate_password(32, false);
        setcookie('complyflow_session', $session_id, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);

        return $session_id;
    }

    /**
     * Anonymize IP address (GDPR compliant)
     *
     * @param string $ip IP address.
     * @return string Anonymized IP.
     */
    private function anonymize_ip(string $ip): string {
        return wp_privacy_anonymize_ip($ip);
    }

    /**
     * Get geo-location from IP (placeholder)
     *
     * @param string $ip IP address.
     * @return string Country code.
     */
    private function get_geo_location(string $ip): string {
        // This would integrate with a GeoIP service
        // For now, return empty string
        return '';
    }
}
