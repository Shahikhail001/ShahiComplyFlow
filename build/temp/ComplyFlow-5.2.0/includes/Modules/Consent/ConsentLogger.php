<?php
/**
 * Consent Logger
 *
 * Logs and manages user consent records.
 *
 * @package ComplyFlow\Modules\Consent
 * @since   1.0.0
 */

namespace ComplyFlow\Modules\Consent;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ConsentLogger
 */
class ConsentLogger {
    /**
     * Log consent decision
     *
     * @param array $consent_data Consent data.
     * @return int|false Log ID or false on failure.
     */
    public function log_consent(array $consent_data) {
        global $wpdb;

        $table = $wpdb->prefix . 'complyflow_consent_logs';

        // consent_given is true if user accepted at least one non-necessary category
        $consent_given = false;
        foreach ($consent_data as $category => $accepted) {
            if ($accepted && $category !== 'necessary') {
                $consent_given = true;
                break;
            }
        }

        $data = [
            'user_id' => get_current_user_id() ?: null,
            'session_id' => $this->get_or_create_session_id(),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])), 0, 255) : '',
            'consent_data' => wp_json_encode($consent_data),
            'consent_categories' => wp_json_encode($consent_data), // Store as JSON to match dashboard expectations
            'consent_given' => $consent_given ? 1 : 0,
            'geo_country' => '', // Can be enhanced with geo-location later
            'created_at' => current_time('mysql'),
        ];

        $result = $wpdb->insert($table, $data);

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Get or create session ID
     *
     * @return string Session ID.
     */
    private function get_or_create_session_id(): string {
        if (isset($_COOKIE['complyflow_session'])) {
            return sanitize_text_field($_COOKIE['complyflow_session']);
        }

        // Generate new session ID
        $session_id = wp_generate_password(32, false);
        
        // Set cookie for 24 hours
        setcookie(
            'complyflow_session',
            $session_id,
            time() + DAY_IN_SECONDS,
            COOKIEPATH,
            COOKIE_DOMAIN,
            is_ssl(),
            true
        );

        return $session_id;
    }

    /**
     * Get consent logs
     *
     * @param array $args Query arguments.
     * @return array Consent logs.
     */
    public function get_logs(array $args = []): array {
        global $wpdb;

        $table = $wpdb->prefix . 'complyflow_consent_logs';

        $defaults = [
            'limit' => 50,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];

        $args = wp_parse_args($args, $defaults);

        $query = $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d",
            $args['limit'],
            $args['offset']
        );

        $results = $wpdb->get_results($query);

        return array_map(function ($log) {
            $log->consent_data = json_decode($log->consent_data, true);
            return $log;
        }, $results);
    }

    /**
     * Get consent statistics
     *
     * @return array Statistics.
     */
    public function get_statistics(): array {
        global $wpdb;

        $table = $wpdb->prefix . 'complyflow_consent_logs';

        // Total consents
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

        // Consents in last 30 days
        $last_30_days = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s",
                date('Y-m-d H:i:s', strtotime('-30 days'))
            )
        );

        // Acceptance rate (users who accepted all)
        $total_with_data = $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE consent_data IS NOT NULL");
        
        if ($total_with_data > 0) {
            $accepted_all = $wpdb->get_var(
                "SELECT COUNT(*) FROM {$table} WHERE consent_data LIKE '%\"analytics\":true%' AND consent_data LIKE '%\"marketing\":true%'"
            );
            $acceptance_rate = ($accepted_all / $total_with_data) * 100;
        } else {
            $acceptance_rate = 0;
        }

        return [
            'total_consents' => (int) $total,
            'last_30_days' => (int) $last_30_days,
            'acceptance_rate' => round($acceptance_rate, 2),
        ];
    }

    /**
     * Delete old consent logs
     *
     * @param int $days Days to keep.
     * @return int Number of deleted logs.
     */
    public function cleanup_old_logs(int $days = 365): int {
        global $wpdb;

        $table = $wpdb->prefix . 'complyflow_consent_logs';

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$table} WHERE created_at < %s",
                date('Y-m-d H:i:s', strtotime("-{$days} days"))
            )
        );

        return $result ?: 0;
    }

    /**
     * Get client IP address
     *
     * @return string IP address.
     */
    private function get_client_ip(): string {
        $ip = '';

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }

        // Anonymize IP for GDPR compliance (remove last octet)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = preg_replace('/\.\d+$/', '.0', $ip);
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip = preg_replace('/:[^:]+$/', ':0', $ip);
        }

        return $ip;
    }
}
