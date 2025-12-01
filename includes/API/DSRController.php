<?php
/**
 * DSR REST Controller
 *
 * Handles REST API endpoints for Data Subject Rights requests.
 *
 * @package ComplyFlow\API
 * @since 1.0.0
 */

namespace ComplyFlow\API;

use ComplyFlow\Database\DSRRepository;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * DSR REST Controller Class
 *
 * @since 1.0.0
 */
class DSRController extends RestController {
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
        parent::__construct();
        $this->rest_base = 'dsr';
        $this->repository = new DSRRepository();
    }

    /**
     * Register routes
     *
     * @return void
     */
    public function register_routes(): void {
        // Public: Submit DSR request
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods' => 'POST',
                'callback' => [$this, 'create_request'],
                'permission_callback' => '__return_true',
                'args' => $this->get_create_request_args(),
            ],
        ]);

        // Public: Verify DSR request
        register_rest_route($this->namespace, '/' . $this->rest_base . '/verify', [
            [
                'methods' => 'POST',
                'callback' => [$this, 'verify_request'],
                'permission_callback' => '__return_true',
                'args' => [
                    'token' => [
                        'required' => true,
                        'type' => 'string',
                    ],
                ],
            ],
        ]);

        // Admin: Get all DSR requests
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_requests'],
                'permission_callback' => [$this, 'authorize_manage'],
                'args' => $this->get_collection_args(),
            ],
        ]);

        // Admin: Get single DSR request
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_request'],
                'permission_callback' => [$this, 'authorize_manage'],
            ],
        ]);

        // Admin: Update DSR request status
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/status', [
            [
                'methods' => 'PUT',
                'callback' => [$this, 'update_status'],
                'permission_callback' => [$this, 'authorize_manage'],
                'args' => [
                    'status' => [
                        'required' => true,
                        'type' => 'string',
                        'enum' => ['pending', 'verified', 'processing', 'completed', 'rejected'],
                    ],
                ],
            ],
        ]);

        // Admin: Get DSR statistics
        register_rest_route($this->namespace, '/' . $this->rest_base . '/stats', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_statistics'],
                'permission_callback' => [$this, 'authorize_manage'],
            ],
        ]);
    }

    /**
     * Create DSR request
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function create_request(WP_REST_Request $request) {
        // Rate limiting
        $email = sanitize_email($request->get_param('email'));
        $rate_limit = $this->check_rate_limit('dsr_' . $email, 5, 3600);
        if (is_wp_error($rate_limit)) {
            return $rate_limit;
        }

        // Validate email
        if (!is_email($email)) {
            return $this->error_response(__('Invalid email address.', 'complyflow'), 400);
        }

        $data = [
            'request_type' => sanitize_text_field($request->get_param('request_type')),
            'email' => $email,
            'message' => sanitize_textarea_field($request->get_param('message')),
            'user_id' => get_current_user_id(),
        ];

        $request_id = $this->repository->create_request($data);

        if ($request_id === false) {
            return $this->error_response(
                __('Failed to create request.', 'complyflow'),
                500
            );
        }

        // Send verification email
        $this->send_verification_email($request_id);

        $this->log_request($request, 'create_dsr_request');

        return $this->success_response(
            ['id' => $request_id],
            __('Request submitted. Please check your email to verify.', 'complyflow'),
            201
        );
    }

    /**
     * Verify DSR request
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function verify_request(WP_REST_Request $request) {
        $token = sanitize_text_field($request->get_param('token'));
        
        $dsr = $this->repository->find_by_token($token);

        if (!$dsr) {
            return $this->error_response(__('Invalid verification token.', 'complyflow'), 404);
        }

        if ($dsr->status !== 'pending') {
            return $this->error_response(__('Request already verified.', 'complyflow'), 400);
        }

        $verified = $this->repository->verify_request($token);

        if (!$verified) {
            return $this->error_response(__('Verification failed.', 'complyflow'), 500);
        }

        $this->log_request($request, 'verify_dsr_request');

        return $this->success_response(
            null,
            __('Request verified successfully. We will process it within 30 days.', 'complyflow')
        );
    }

    /**
     * Get DSR requests
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response Response.
     */
    public function get_requests(WP_REST_Request $request): WP_REST_Response {
        $pagination = $this->get_pagination_params($request);
        $sort = $this->get_sort_params($request, ['id', 'created_at', 'status'], 'created_at');

        $args = [
            'limit' => $pagination['limit'],
            'offset' => $pagination['offset'],
            'orderby' => $sort['orderby'],
            'order' => $sort['order'],
        ];

        // Filter by status
        if ($request->get_param('status')) {
            $args['where']['status'] = sanitize_text_field($request->get_param('status'));
        }

        // Filter by type
        if ($request->get_param('type')) {
            $args['where']['request_type'] = sanitize_text_field($request->get_param('type'));
        }

        $requests = $this->repository->find_all($args);
        $total = $this->repository->count($args);

        return $this->paginated_response($requests, $total, $pagination['page'], $pagination['limit']);
    }

    /**
     * Get single DSR request
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function get_request(WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $dsr = $this->repository->find($id);

        if (!$dsr) {
            return $this->error_response(__('Request not found.', 'complyflow'), 404);
        }

        return $this->success_response($dsr);
    }

    /**
     * Update DSR request status
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response or error.
     */
    public function update_status(WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $status = sanitize_text_field($request->get_param('status'));

        if (!$this->repository->exists($id)) {
            return $this->error_response(__('Request not found.', 'complyflow'), 404);
        }

        $updated = $this->repository->update_status($id, $status);

        if (!$updated) {
            return $this->error_response(__('Failed to update status.', 'complyflow'), 500);
        }

        $this->log_request($request, 'update_dsr_status');

        return $this->success_response(
            null,
            __('Status updated successfully.', 'complyflow')
        );
    }

    /**
     * Get DSR statistics
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response Response.
     */
    public function get_statistics(WP_REST_Request $request): WP_REST_Response {
        $stats = $this->repository->get_statistics();
        return $this->success_response($stats);
    }

    /**
     * Get create request arguments
     *
     * @return array
     */
    private function get_create_request_args(): array {
        return [
            'request_type' => [
                'required' => true,
                'type' => 'string',
                'enum' => ['access', 'erasure', 'rectify', 'portability'],
                'description' => __('Type of data subject request', 'complyflow'),
            ],
            'email' => [
                'required' => true,
                'type' => 'string',
                'format' => 'email',
                'description' => __('Email address', 'complyflow'),
            ],
            'message' => [
                'type' => 'string',
                'description' => __('Additional message or details', 'complyflow'),
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
                'enum' => ['id', 'created_at', 'status'],
            ],
            'order' => [
                'type' => 'string',
                'default' => 'DESC',
                'enum' => ['ASC', 'DESC'],
            ],
            'status' => [
                'type' => 'string',
                'enum' => ['pending', 'verified', 'processing', 'completed', 'rejected'],
            ],
            'type' => [
                'type' => 'string',
                'enum' => ['access', 'erasure', 'rectify', 'portability'],
            ],
        ];
    }

    /**
     * Send verification email
     *
     * @param int $request_id Request ID.
     * @return void
     */
    private function send_verification_email(int $request_id): void {
        $dsr = $this->repository->find($request_id);
        if (!$dsr) {
            return;
        }

        $verify_url = add_query_arg(
            ['token' => $dsr->verification_token],
            home_url('/complyflow/verify')
        );

        $subject = __('Verify Your Data Subject Request', 'complyflow');
        $message = sprintf(
            __('Please verify your data subject request by clicking this link: %s', 'complyflow'),
            $verify_url
        );

        wp_mail($dsr->email, $subject, $message);
    }
}
