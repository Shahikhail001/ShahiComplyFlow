<?php
/**
 * Base REST Controller Class
 *
 * Abstract base class for all REST API controllers.
 * Provides common functionality, authentication, and response formatting.
 *
 * @package ComplyFlow\API
 * @since 1.0.0
 */

namespace ComplyFlow\API;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstract REST Controller Class
 *
 * @since 1.0.0
 */
abstract class RestController extends WP_REST_Controller {
    /**
     * Constructor
     */
    public function __construct() {
        $this->namespace = 'complyflow/v1';
    }

    /**
     * Check if user is authenticated
     *
     * @param WP_REST_Request $request Request object.
     * @return bool|WP_Error True if authenticated, WP_Error otherwise.
     */
    public function authenticate(WP_REST_Request $request) {
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                __('You must be logged in to access this endpoint.', 'complyflow'),
                ['status' => 401]
            );
        }

        return true;
    }

    /**
     * Check if user has admin permissions
     *
     * @param WP_REST_Request $request Request object.
     * @return bool|WP_Error True if authorized, WP_Error otherwise.
     */
    public function authorize_admin(WP_REST_Request $request) {
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access this endpoint.', 'complyflow'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Check if user can manage ComplyFlow
     *
     * @param WP_REST_Request $request Request object.
     * @return bool|WP_Error True if authorized, WP_Error otherwise.
     */
    public function authorize_manage(WP_REST_Request $request) {
        if (!current_user_can('manage_complyflow')) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to manage ComplyFlow.', 'complyflow'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Format success response
     *
     * @param mixed  $data    Response data.
     * @param string $message Success message.
     * @param int    $status  HTTP status code.
     * @return WP_REST_Response
     */
    protected function success_response($data = null, string $message = '', int $status = 200): WP_REST_Response {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return new WP_REST_Response($response, $status);
    }

    /**
     * Format error response
     *
     * @param string $message Error message.
     * @param int    $status  HTTP status code.
     * @param mixed  $data    Additional error data.
     * @return WP_Error
     */
    protected function error_response(string $message, int $status = 400, $data = null): WP_Error {
        return new WP_Error(
            'rest_error',
            $message,
            [
                'status' => $status,
                'data' => $data,
            ]
        );
    }

    /**
     * Format validation error response
     *
     * @param array<string, string> $errors Validation errors.
     * @return WP_Error
     */
    protected function validation_error_response(array $errors): WP_Error {
        return new WP_Error(
            'rest_validation_error',
            __('Validation failed.', 'complyflow'),
            [
                'status' => 422,
                'errors' => $errors,
            ]
        );
    }

    /**
     * Format paginated response
     *
     * @param array $items  Items array.
     * @param int   $total  Total items count.
     * @param int   $page   Current page.
     * @param int   $limit  Items per page.
     * @return WP_REST_Response
     */
    protected function paginated_response(array $items, int $total, int $page, int $limit): WP_REST_Response {
        $total_pages = ceil($total / $limit);

        $response = new WP_REST_Response([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'count' => count($items),
                'per_page' => $limit,
                'current_page' => $page,
                'total_pages' => $total_pages,
                'has_more' => $page < $total_pages,
            ],
        ]);

        // Add pagination headers
        $response->header('X-WP-Total', $total);
        $response->header('X-WP-TotalPages', $total_pages);

        return $response;
    }

    /**
     * Sanitize and validate pagination parameters
     *
     * @param WP_REST_Request $request Request object.
     * @return array{page: int, limit: int, offset: int}
     */
    protected function get_pagination_params(WP_REST_Request $request): array {
        $page = max(1, (int) $request->get_param('page'));
        $limit = max(1, min(100, (int) $request->get_param('per_page') ?: 20));
        $offset = ($page - 1) * $limit;

        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * Sanitize and validate sort parameters
     *
     * @param WP_REST_Request $request        Request object.
     * @param array           $allowed_fields Allowed fields for sorting.
     * @param string          $default_field  Default sort field.
     * @return array{orderby: string, order: string}
     */
    protected function get_sort_params(WP_REST_Request $request, array $allowed_fields, string $default_field = 'id'): array {
        $orderby = $request->get_param('orderby') ?: $default_field;
        $order = strtoupper($request->get_param('order') ?: 'DESC');

        // Validate orderby field
        if (!in_array($orderby, $allowed_fields, true)) {
            $orderby = $default_field;
        }

        // Validate order direction
        if (!in_array($order, ['ASC', 'DESC'], true)) {
            $order = 'DESC';
        }

        return [
            'orderby' => $orderby,
            'order' => $order,
        ];
    }

    /**
     * Validate required parameters
     *
     * @param WP_REST_Request $request  Request object.
     * @param array           $required Required parameter names.
     * @return true|WP_Error True if valid, WP_Error otherwise.
     */
    protected function validate_required_params(WP_REST_Request $request, array $required) {
        $errors = [];

        foreach ($required as $param) {
            $value = $request->get_param($param);
            if (empty($value) && $value !== 0 && $value !== '0') {
                $errors[$param] = sprintf(
                    /* translators: %s: Parameter name */
                    __('The %s parameter is required.', 'complyflow'),
                    $param
                );
            }
        }

        if (!empty($errors)) {
            return $this->validation_error_response($errors);
        }

        return true;
    }

    /**
     * Log API request
     *
     * @param WP_REST_Request $request Request object.
     * @param string          $action  Action being performed.
     * @return void
     */
    protected function log_request(WP_REST_Request $request, string $action): void {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        error_log(sprintf(
            'ComplyFlow API: %s - %s %s by user %d',
            $action,
            $request->get_method(),
            $request->get_route(),
            get_current_user_id()
        ));
    }

    /**
     * Rate limit check
     *
     * @param string $key    Rate limit key.
     * @param int    $limit  Max requests.
     * @param int    $period Time period in seconds.
     * @return bool|WP_Error True if allowed, WP_Error if rate limited.
     */
    protected function check_rate_limit(string $key, int $limit = 100, int $period = 3600) {
        $transient_key = 'complyflow_rate_limit_' . md5($key);
        $requests = (int) get_transient($transient_key);

        if ($requests >= $limit) {
            return new WP_Error(
                'rest_rate_limit_exceeded',
                __('Rate limit exceeded. Please try again later.', 'complyflow'),
                ['status' => 429]
            );
        }

        set_transient($transient_key, $requests + 1, $period);

        return true;
    }
}
