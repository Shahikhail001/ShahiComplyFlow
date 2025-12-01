<?php
/**
 * Dashboard Widgets Helper
 *
 * Provides data aggregation for dashboard widgets.
 *
 * @package ComplyFlow\Modules\Dashboard
 * @since   3.5.0
 */

namespace ComplyFlow\Modules\Dashboard;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Widgets Class
 *
 * @since 3.5.0
 */
class DashboardWidgets {
    
    /**
     * Get compliance score
     *
     * Calculates overall compliance score based on multiple factors.
     *
     * @return array {
     *     @type int    $score     Overall score (0-100)
     *     @type string $grade     Letter grade (A-F)
     *     @type string $status    Status label (excellent/good/needs-improvement/critical)
     *     @type array  $breakdown Module-by-module breakdown
     * }
     */
    public function get_compliance_score(): array {
        global $wpdb;
        
        $scores = [];
        
        // Accessibility Score (30% weight)
        $accessibility_score = $this->calculate_accessibility_score();
        $scores['accessibility'] = [
            'score' => $accessibility_score,
            'weight' => 30,
            'label' => __('Accessibility', 'complyflow'),
        ];
        
        // Consent Configuration (20% weight)
        $consent_score = $this->calculate_consent_score();
        $scores['consent'] = [
            'score' => $consent_score,
            'weight' => 20,
            'label' => __('Consent Management', 'complyflow'),
        ];
        
        // DSR Portal Setup (20% weight)
        $dsr_score = $this->calculate_dsr_score();
        $scores['dsr'] = [
            'score' => $dsr_score,
            'weight' => 20,
            'label' => __('Data Subject Rights', 'complyflow'),
        ];
        
        // Cookie Inventory (15% weight)
        $cookie_score = $this->calculate_cookie_score();
        $scores['cookies'] = [
            'score' => $cookie_score,
            'weight' => 15,
            'label' => __('Cookie Inventory', 'complyflow'),
        ];
        
        // Document Generation (15% weight)
        $document_score = $this->calculate_document_score();
        $scores['documents'] = [
            'score' => $document_score,
            'weight' => 15,
            'label' => __('Legal Documents', 'complyflow'),
        ];
        
        // Calculate weighted average
        $total_score = 0;
        $total_weight = 0;
        
        foreach ($scores as $module => $data) {
            $total_score += ($data['score'] * $data['weight']);
            $total_weight += $data['weight'];
        }
        
        $overall_score = $total_weight > 0 ? round($total_score / $total_weight) : 0;
        
        return [
            'score' => $overall_score,
            'grade' => $this->get_grade($overall_score),
            'status' => $this->get_status($overall_score),
            'breakdown' => $scores,
        ];
    }

    /**
     * Calculate accessibility score
     *
     * @return int Score (0-100)
     */
    private function calculate_accessibility_score(): int {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'complyflow_accessibility_scans';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            return 50; // Default score if not scanned yet
        }
        
        // Get latest scan
        $latest_scan = $wpdb->get_row(
            "SELECT * FROM $table_name ORDER BY scanned_at DESC LIMIT 1"
        );
        
        if (!$latest_scan) {
            return 50; // Default score if no scans
        }
        
        // Calculate score based on issue severity
        $critical = (int) $latest_scan->critical_count;
        $serious = (int) $latest_scan->serious_count;
        $moderate = (int) $latest_scan->moderate_count;
        
        // Deduct points based on issues
        $score = 100;
        $score -= ($critical * 10); // -10 points per critical
        $score -= ($serious * 5);   // -5 points per serious
        $score -= ($moderate * 2);  // -2 points per moderate
        
        return max(0, min(100, $score));
    }

    /**
     * Calculate consent score
     *
     * @return int Score (0-100)
     */
    private function calculate_consent_score(): int {
        $settings = get_option('complyflow_consent_settings', []);
        
        $score = 0;
        
        // Banner enabled (+40 points)
        if (!empty($settings['banner_enabled'])) {
            $score += 40;
        }
        
        // Categories configured (+30 points)
        if (!empty($settings['cookie_categories'])) {
            $score += 30;
        }
        
        // Geo-targeting configured (+15 points)
        if (!empty($settings['geo_targeting_enabled'])) {
            $score += 15;
        }
        
        // Consent logging enabled (+15 points)
        if (!empty($settings['logging_enabled'])) {
            $score += 15;
        }
        
        return $score;
    }

    /**
     * Calculate DSR score
     *
     * @return int Score (0-100)
     */
    private function calculate_dsr_score(): int {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'complyflow_dsr_requests';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            return 50; // Default score
        }
        
        $score = 50; // Base score for having DSR enabled
        
        // Count total requests
        $total_requests = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($total_requests > 0) {
            $score += 20; // Bonus for having processed requests
            
            // Check completion rate
            $completed = $wpdb->get_var(
                "SELECT COUNT(*) FROM $table_name WHERE status = 'completed'"
            );
            
            $completion_rate = ($completed / $total_requests) * 100;
            
            if ($completion_rate > 80) {
                $score += 30; // High completion rate
            } elseif ($completion_rate > 50) {
                $score += 15; // Medium completion rate
            }
        }
        
        return min(100, $score);
    }

    /**
     * Calculate cookie score
     *
     * @return int Score (0-100)
     */
    private function calculate_cookie_score(): int {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'complyflow_cookies';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            return 30; // Low score if inventory not initialized
        }
        
        $total_cookies = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($total_cookies === null || $total_cookies == 0) {
            return 30; // Low score if no cookies detected
        }
        
        $score = 50; // Base score for having cookies
        
        // Check categorization completeness
        $uncategorized = $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name WHERE category IS NULL OR category = ''"
        );
        
        $categorization_rate = (($total_cookies - $uncategorized) / $total_cookies) * 100;
        
        if ($categorization_rate >= 90) {
            $score += 50; // Fully categorized
        } elseif ($categorization_rate >= 70) {
            $score += 30; // Mostly categorized
        } elseif ($categorization_rate >= 50) {
            $score += 15; // Partially categorized
        }
        
        return min(100, $score);
    }

    /**
     * Calculate document score
     *
     * @return int Score (0-100)
     */
    private function calculate_document_score(): int {
        $score = 0;
        
        // Check if privacy policy page exists
        $privacy_page = get_option('wp_page_for_privacy_policy');
        if (!empty($privacy_page)) {
            $score += 30;
        }
        
        // Check ComplyFlow document settings
        $settings = get_option('complyflow_settings', []);
        
        // Privacy policy configured (+25 points)
        if (!empty($settings['privacy_policy_page']) || !empty($privacy_page)) {
            $score += 25;
        }
        
        // Terms of service page configured (+25 points)
        if (!empty($settings['terms_page'])) {
            $score += 25;
        }
        
        // Cookie policy page configured (+20 points)
        if (!empty($settings['cookie_policy_page'])) {
            $score += 20;
        }
        
        return min(100, $score);
    }

    /**
     * Get letter grade from score
     *
     * @param int $score Score (0-100)
     * @return string Grade (A-F)
     */
    private function get_grade(int $score): string {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Get status from score
     *
     * @param int $score Score (0-100)
     * @return string Status label
     */
    private function get_status(int $score): string {
        if ($score >= 90) return 'excellent';
        if ($score >= 70) return 'good';
        if ($score >= 50) return 'needs-improvement';
        return 'critical';
    }

    /**
     * Get DSR statistics
     *
     * @return array {
     *     @type int $pending      Pending requests
     *     @type int $verified     Verified requests
     *     @type int $in_progress  In progress requests
     *     @type int $completed    Completed requests
     *     @type int $rejected     Rejected requests
     *     @type int $total        Total requests
     * }
     */
    public function get_dsr_statistics(): array {
        // DSR requests are stored as custom post type 'complyflow_dsr', not in a table
        $stats = [
            'pending' => 0,
            'verified' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'rejected' => 0,
            'total' => 0,
            'by_type' => [
                'access' => 0,
                'delete' => 0,
                'rectification' => 0,
                'portability' => 0,
                'restriction' => 0,
                'objection' => 0,
            ],
        ];
        
        // Count by status using custom post statuses
        $statuses = ['dsr_pending', 'dsr_verified', 'dsr_in_progress', 'dsr_completed', 'dsr_rejected'];
        
        foreach ($statuses as $status) {
            $count = wp_count_posts('complyflow_dsr')->$status ?? 0;
            $key = str_replace('dsr_', '', $status);
            $stats[$key] = (int) $count;
        }
        
        $stats['total'] = array_sum(array_filter($stats, 'is_int'));
        
        // Get breakdown by request type from post meta
        $all_requests = get_posts([
            'post_type' => 'complyflow_dsr',
            'post_status' => $statuses,
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        
        foreach ($all_requests as $post_id) {
            $type = get_post_meta($post_id, '_dsr_type', true);
            if ($type && isset($stats['by_type'][$type])) {
                $stats['by_type'][$type]++;
            }
        }
        
        return $stats;
    }

    /**
     * Get consent statistics
     *
     * @return array {
     *     @type int   $total_consents   Total consent records
     *     @type int   $accepted_count   Accepted consents
     *     @type int   $rejected_count   Rejected consents
     *     @type float $acceptance_rate  Acceptance percentage
     *     @type array $by_category      Consent breakdown by category
     * }
     */
    public function get_consent_statistics(): array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'complyflow_consent_logs';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            return [
                'total_consents' => 0,
                'accepted_count' => 0,
                'rejected_count' => 0,
                'acceptance_rate' => 0.0,
                'by_category' => [],
            ];
        }
        
        $total_consents = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($total_consents === 0) {
            return [
                'total_consents' => 0,
                'accepted_count' => 0,
                'rejected_count' => 0,
                'acceptance_rate' => 0.0,
                'by_category' => [],
            ];
        }
        
        $accepted_count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name WHERE consent_given = 1"
        );
        
        $rejected_count = $total_consents - $accepted_count;
        $acceptance_rate = ($accepted_count / $total_consents) * 100;
        
        // Get breakdown by category (parsing JSON consent_categories field)
        $by_category = [
            'necessary' => 0,
            'functional' => 0,
            'analytics' => 0,
            'marketing' => 0,
            'preferences' => 0,
        ];
        
        $consent_records = $wpdb->get_results(
            "SELECT consent_categories, consent_given FROM $table_name WHERE consent_given = 1",
            ARRAY_A
        );
        
        if ($consent_records) {
            foreach ($consent_records as $record) {
                $categories = json_decode($record['consent_categories'], true);
                if (is_array($categories)) {
                    foreach ($categories as $category => $value) {
                        if ($value && isset($by_category[$category])) {
                            $by_category[$category]++;
                        }
                    }
                }
            }
        }
        
        return [
            'total_consents' => $total_consents,
            'accepted_count' => $accepted_count,
            'rejected_count' => $rejected_count,
            'acceptance_rate' => round($acceptance_rate, 1),
            'by_category' => $by_category,
        ];
    }

    /**
     * Get accessibility summary
     *
     * @return array {
     *     @type int    $total_issues   Total issues found
     *     @type int    $critical_count Critical issues
     *     @type int    $serious_count  Serious issues
     *     @type int    $moderate_count Moderate issues
     *     @type string $last_scan      Last scan date
     * }
     */
    public function get_accessibility_summary(): array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'complyflow_scan_results';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            error_log('ComplyFlow: Table does not exist: ' . $table_name);
            return [
                'total_issues' => 0,
                'critical_count' => 0,
                'serious_count' => 0,
                'moderate_count' => 0,
                'last_scan' => null,
            ];
        }
        
        // Get latest accessibility scan
        $latest_scan = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE scan_type = %s ORDER BY created_at DESC LIMIT 1",
                'accessibility'
            )
        );
        
        error_log('ComplyFlow: get_accessibility_summary query result: ' . print_r($latest_scan, true));
        
        if (!$latest_scan) {
            error_log('ComplyFlow: No accessibility scan found in database');
            return [
                'total_issues' => 0,
                'critical_count' => 0,
                'serious_count' => 0,
                'moderate_count' => 0,
                'last_scan' => null,
            ];
        }
        
        $critical = (int) $latest_scan->critical_issues;
        $serious = (int) $latest_scan->warning_issues;
        $moderate = (int) $latest_scan->notice_issues;
        
        error_log('ComplyFlow: Accessibility summary - Critical: ' . $critical . ', Serious: ' . $serious . ', Moderate: ' . $moderate);
        
        return [
            'total_issues' => $critical + $serious + $moderate,
            'critical_count' => $critical,
            'serious_count' => $serious,
            'moderate_count' => $moderate,
            'last_scan' => $latest_scan->created_at,
        ];
    }

    /**
     * Get cookie summary
     *
     * @return array {
     *     @type int   $total_cookies Total cookies
     *     @type array $by_category   Breakdown by category
     *     @type int   $scanned       Auto-detected cookies
     *     @type int   $manual        Manually added/imported cookies
     * }
     */
    public function get_cookie_summary(): array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'complyflow_cookies';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            return [
                'total_cookies' => 0,
                'by_category' => [
                    'necessary' => 0,
                    'functional' => 0,
                    'analytics' => 0,
                    'marketing' => 0,
                ],
                'scanned' => 0,
                'manual' => 0,
            ];
        }
        
        $total_cookies = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        $by_category = [
            'necessary' => (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM $table_name WHERE category = 'necessary'"
            ),
            'functional' => (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM $table_name WHERE category = 'functional'"
            ),
            'analytics' => (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM $table_name WHERE category = 'analytics'"
            ),
            'marketing' => (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM $table_name WHERE category = 'marketing'"
            ),
        ];
        
        // Get scanned vs manual breakdown
        $scanned = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name WHERE is_manual = 0 OR is_manual IS NULL"
        );
        $manual = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name WHERE is_manual = 1"
        );
        
        return [
            'total_cookies' => $total_cookies,
            'by_category' => $by_category,
            'scanned' => $scanned,
            'manual' => $manual,
        ];
    }

    /**
     * Get compliance trends over time (last 30 days)
     *
     * @return array {
     *     @type array $dates          Array of dates
     *     @type array $scores         Compliance scores by date
     *     @type array $dsr_counts     DSR request counts by date
     *     @type array $consent_counts Consent counts by date
     *     @type float $trend          Overall trend direction (-1 to 1)
     * }
     */
    public function get_compliance_trends(): array {
        // Try to load historical data first
        $use_real_data = class_exists('ComplyFlow\Database\ComplianceHistoryRepository');
        
        if ($use_real_data) {
            $repository = new \ComplyFlow\Database\ComplianceHistoryRepository();
            
            // Check if table exists and has data
            if ($repository->table_exists() && $repository->get_count() > 0) {
                return $this->get_real_compliance_trends($repository);
            }
        }
        
        // Fallback to simulated data if no history exists yet
        return $this->get_simulated_compliance_trends();
    }

    /**
     * Get real compliance trends from historical data
     *
     * @param \ComplyFlow\Database\ComplianceHistoryRepository $repository Repository instance
     * @return array Trend data
     */
    private function get_real_compliance_trends($repository): array {
        $history = $repository->get_history(30);
        
        // If we don't have any data at all, use simulated
        if (count($history) === 0) {
            return $this->get_simulated_compliance_trends();
        }
        
        $dates = [];
        $scores = [];
        
        // Group by date (in case multiple snapshots per day)
        $grouped = [];
        foreach ($history as $record) {
            $date = date('Y-m-d', strtotime($record['recorded_at']));
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = (int) $record['compliance_score'];
        }
        
        // Average scores per day
        $daily_scores = [];
        foreach ($grouped as $date => $day_scores) {
            $daily_scores[$date] = round(array_sum($day_scores) / count($day_scores));
        }
        
        // Fill in missing days with interpolation or use the most recent score
        $start_date = date('Y-m-d', strtotime('-29 days'));
        $end_date = date('Y-m-d');
        $most_recent_score = end($daily_scores); // Get last score as fallback
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = date('M j', strtotime($date));
            
            if (isset($daily_scores[$date])) {
                $scores[] = $daily_scores[$date];
            } else {
                // Use interpolation if we have multiple data points, otherwise use most recent
                $interpolated = $this->interpolate_score($date, $daily_scores);
                $scores[] = $interpolated !== 0 ? $interpolated : $most_recent_score;
            }
        }
        
        // Calculate trend
        $trend = 0;
        if (count($scores) >= 2) {
            $first_week = array_slice($scores, 0, min(7, count($scores)));
            $last_week = array_slice($scores, -min(7, count($scores)));
            $first_avg = array_sum($first_week) / count($first_week);
            $last_avg = array_sum($last_week) / count($last_week);
            $trend = ($last_avg - $first_avg) / 100; // Normalized -1 to 1
        }
        
        return [
            'dates' => $dates,
            'scores' => $scores,
            'trend' => round($trend, 2),
        ];
    }

    /**
     * Get simulated compliance trends (fallback)
     *
     * @return array Simulated trend data
     */
    private function get_simulated_compliance_trends(): array {
        $dates = [];
        $scores = [];
        
        // Generate last 30 days with simulated data
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = date('M j', strtotime($date));
            
            // Use current score with slight variation
            $current = $this->get_compliance_score();
            $variation = rand(-5, 5);
            $scores[] = max(0, min(100, $current['score'] + $variation));
        }
        
        // Calculate trend
        $trend = 0;
        if (count($scores) >= 2) {
            $first_week = array_slice($scores, 0, 7);
            $last_week = array_slice($scores, -7);
            $first_avg = array_sum($first_week) / count($first_week);
            $last_avg = array_sum($last_week) / count($last_week);
            $trend = ($last_avg - $first_avg) / 100; // Normalized -1 to 1
        }
        
        return [
            'dates' => $dates,
            'scores' => $scores,
            'trend' => round($trend, 2),
        ];
    }

    /**
     * Interpolate score for missing date
     *
     * @param string $target_date Target date to interpolate
     * @param array  $known_scores Known scores by date
     * @return int Interpolated score
     */
    private function interpolate_score(string $target_date, array $known_scores): int {
        if (empty($known_scores)) {
            $current = $this->get_compliance_score();
            return $current['score'];
        }
        
        // Find nearest known scores before and after
        $target_timestamp = strtotime($target_date);
        $before = null;
        $after = null;
        
        foreach ($known_scores as $date => $score) {
            $timestamp = strtotime($date);
            if ($timestamp < $target_timestamp) {
                $before = ['date' => $date, 'score' => $score, 'timestamp' => $timestamp];
            } elseif ($timestamp > $target_timestamp && $after === null) {
                $after = ['date' => $date, 'score' => $score, 'timestamp' => $timestamp];
                break;
            }
        }
        
        // If we have both before and after, interpolate
        if ($before && $after) {
            $total_diff = $after['timestamp'] - $before['timestamp'];
            $target_diff = $target_timestamp - $before['timestamp'];
            $ratio = $target_diff / $total_diff;
            $score_diff = $after['score'] - $before['score'];
            return round($before['score'] + ($score_diff * $ratio));
        }
        
        // Otherwise use nearest known score
        if ($before) {
            return $before['score'];
        }
        if ($after) {
            return $after['score'];
        }
        
        // Fallback to current score
        $current = $this->get_compliance_score();
        return $current['score'];
    }

    /**
     * Get recent activity timeline
     *
     * @return array Array of activity items with type, message, timestamp
     */
    public function get_recent_activities(): array {
        global $wpdb;
        
        $activities = [];
        
        // Get recent DSR requests
        $recent_dsr = get_posts([
            'post_type' => 'complyflow_dsr',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
        
        foreach ($recent_dsr as $dsr) {
            $type = get_post_meta($dsr->ID, '_dsr_type', true);
            $activities[] = [
                'type' => 'dsr',
                'icon' => 'admin-users',
                'message' => sprintf(__('New %s request received', 'complyflow'), ucfirst($type)),
                'timestamp' => get_the_time('U', $dsr),
                'time_ago' => human_time_diff(get_the_time('U', $dsr), current_time('timestamp')) . ' ago',
            ];
        }
        
        // Get recent accessibility scans
        $scan_table = $wpdb->prefix . 'complyflow_scan_results';
        if ($wpdb->get_var("SHOW TABLES LIKE '$scan_table'") === $scan_table) {
            $recent_scans = $wpdb->get_results(
                "SELECT * FROM $scan_table WHERE scan_type = 'accessibility' ORDER BY created_at DESC LIMIT 3",
                ARRAY_A
            );
            
            foreach ($recent_scans as $scan) {
                $activities[] = [
                    'type' => 'scan',
                    'icon' => 'search',
                    'message' => sprintf(__('Accessibility scan completed (%d issues)', 'complyflow'), 
                        $scan['critical_issues'] + $scan['warning_issues'] + $scan['notice_issues']),
                    'timestamp' => strtotime($scan['created_at']),
                    'time_ago' => human_time_diff(strtotime($scan['created_at']), current_time('timestamp')) . ' ago',
                ];
            }
        }
        
        // Get recent consent records
        $consent_table = $wpdb->prefix . 'complyflow_consent_logs';
        if ($wpdb->get_var("SHOW TABLES LIKE '$consent_table'") === $consent_table) {
            $recent_consents = $wpdb->get_results(
                "SELECT * FROM $consent_table ORDER BY created_at DESC LIMIT 3",
                ARRAY_A
            );
            
            foreach ($recent_consents as $consent) {
                $status = $consent['consent_given'] ? 'accepted' : 'rejected';
                $activities[] = [
                    'type' => 'consent',
                    'icon' => 'yes-alt',
                    'message' => sprintf(__('User consent %s', 'complyflow'), $status),
                    'timestamp' => strtotime($consent['created_at']),
                    'time_ago' => human_time_diff(strtotime($consent['created_at']), current_time('timestamp')) . ' ago',
                ];
            }
        }
        
        // Sort by timestamp descending
        usort($activities, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return array_slice($activities, 0, 10);
    }

    /**
     * Get compliance risk assessment
     *
     * @return array {
     *     @type string $level        Risk level (low/medium/high/critical)
     *     @type int    $score        Risk score (0-100)
     *     @type array  $risk_factors Individual risk factors
     * }
     */
    public function get_risk_assessment(): array {
        $risk_score = 0;
        $risk_factors = [];
        
        // Check accessibility issues
        $accessibility = $this->get_accessibility_summary();
        if ($accessibility['critical_count'] > 0) {
            $risk_score += 30;
            $risk_factors[] = [
                'factor' => __('Critical Accessibility Issues', 'complyflow'),
                'severity' => 'high',
                'description' => sprintf(__('%d critical issues found', 'complyflow'), $accessibility['critical_count']),
            ];
        }
        
        // Check pending DSR requests
        $dsr = $this->get_dsr_statistics();
        if ($dsr['pending'] > 5) {
            $risk_score += 20;
            $risk_factors[] = [
                'factor' => __('High Pending DSR Volume', 'complyflow'),
                'severity' => 'medium',
                'description' => sprintf(__('%d requests awaiting response', 'complyflow'), $dsr['pending']),
            ];
        }
        
        // Check uncategorized cookies
        global $wpdb;
        $cookie_table = $wpdb->prefix . 'complyflow_cookies';
        if ($wpdb->get_var("SHOW TABLES LIKE '$cookie_table'") === $cookie_table) {
            $uncategorized = $wpdb->get_var(
                "SELECT COUNT(*) FROM $cookie_table WHERE category IS NULL OR category = ''"
            );
            if ($uncategorized > 0) {
                $risk_score += 15;
                $risk_factors[] = [
                    'factor' => __('Uncategorized Cookies', 'complyflow'),
                    'severity' => 'medium',
                    'description' => sprintf(__('%d cookies need categorization', 'complyflow'), $uncategorized),
                ];
            }
        }
        
        // Check if consent banner is disabled
        $consent_settings = get_option('complyflow_consent_settings', []);
        if (empty($consent_settings['banner_enabled'])) {
            $risk_score += 25;
            $risk_factors[] = [
                'factor' => __('Consent Banner Disabled', 'complyflow'),
                'severity' => 'high',
                'description' => __('Cookie consent collection is not active', 'complyflow'),
            ];
        }
        
        // Check if privacy policy is missing
        $privacy_page = get_option('wp_page_for_privacy_policy');
        if (empty($privacy_page)) {
            $risk_score += 10;
            $risk_factors[] = [
                'factor' => __('Missing Privacy Policy', 'complyflow'),
                'severity' => 'medium',
                'description' => __('No privacy policy page configured', 'complyflow'),
            ];
        }
        
        // Determine risk level
        $level = 'low';
        if ($risk_score >= 70) {
            $level = 'critical';
        } elseif ($risk_score >= 40) {
            $level = 'high';
        } elseif ($risk_score >= 20) {
            $level = 'medium';
        }
        
        return [
            'level' => $level,
            'score' => min(100, $risk_score),
            'risk_factors' => $risk_factors,
        ];
    }

    /**
     * Get data processing summary
     *
     * @return array {
     *     @type int $total_records      Total data records processed
     *     @type int $dsr_fulfillment    Average DSR fulfillment time (hours)
     *     @type int $consent_updates    Consent updates this month
     *     @type int $data_exports       Number of data exports
     * }
     */
    public function get_data_processing_summary(): array {
        global $wpdb;
        
        $dsr_table = $wpdb->prefix . 'complyflow_dsr_requests';
        $consent_table = $wpdb->prefix . 'complyflow_consent_logs';
        
        // Count DSR requests this month
        $this_month_start = date('Y-m-01 00:00:00');
        $dsr_count = 0;
        
        // Count completed DSR and calculate avg fulfillment time
        $completed_requests = get_posts([
            'post_type' => 'complyflow_dsr',
            'post_status' => 'dsr_completed',
            'posts_per_page' => -1,
            'date_query' => [
                [
                    'after' => date('Y-m-01'),
                    'inclusive' => true,
                ]
            ],
        ]);
        
        $total_fulfillment_time = 0;
        $fulfillment_count = 0;
        
        foreach ($completed_requests as $request) {
            $created = get_the_time('U', $request);
            $completed = get_post_modified_time('U', false, $request);
            $hours = ($completed - $created) / 3600;
            $total_fulfillment_time += $hours;
            $fulfillment_count++;
        }
        
        $avg_fulfillment = $fulfillment_count > 0 ? round($total_fulfillment_time / $fulfillment_count) : 0;
        
        // Count consent updates this month
        $consent_updates = 0;
        if ($wpdb->get_var("SHOW TABLES LIKE '$consent_table'") === $consent_table) {
            $consent_updates = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $consent_table WHERE created_at >= %s",
                    $this_month_start
                )
            );
        }
        
        // Count data exports (from completed DSR of type 'access' or 'portability')
        $export_count = 0;
        foreach ($completed_requests as $request) {
            $type = get_post_meta($request->ID, '_dsr_type', true);
            if (in_array($type, ['access', 'portability'])) {
                $export_count++;
            }
        }
        
        return [
            'total_records' => count($completed_requests),
            'dsr_fulfillment' => $avg_fulfillment,
            'consent_updates' => (int) $consent_updates,
            'data_exports' => $export_count,
        ];
    }

    /**
     * Get module health indicators
     *
     * @return array Array of module health status
     */
    public function get_module_health(): array {
        $compliance = $this->get_compliance_score();
        $health = [];
        
        foreach ($compliance['breakdown'] as $key => $module) {
            $status = 'excellent';
            if ($module['score'] < 80) $status = 'good';
            if ($module['score'] < 60) $status = 'warning';
            if ($module['score'] < 40) $status = 'critical';
            
            $health[] = [
                'module' => $module['label'],
                'score' => $module['score'],
                'status' => $status,
                'percentage' => $module['score'] . '%',
            ];
        }
        
        return $health;
    }
}
