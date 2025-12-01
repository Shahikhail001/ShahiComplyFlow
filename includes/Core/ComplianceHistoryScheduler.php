<?php
/**
 * Compliance History Scheduler
 *
 * Handles automated compliance snapshot scheduling via WP-Cron.
 *
 * @package ComplyFlow\Core
 * @since 4.8.0
 */

namespace ComplyFlow\Core;

use ComplyFlow\Database\ComplianceHistoryRepository;
use ComplyFlow\Modules\Dashboard\DashboardWidgets;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Compliance History Scheduler Class
 *
 * @since 4.8.0
 */
class ComplianceHistoryScheduler {
    
    /**
     * Cron hook name
     */
    const CRON_HOOK = 'complyflow_compliance_snapshot';

    /**
     * Repository instance
     *
     * @var ComplianceHistoryRepository
     */
    private ComplianceHistoryRepository $repository;

    /**
     * Dashboard widgets instance
     *
     * @var DashboardWidgets
     */
    private DashboardWidgets $widgets;

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new ComplianceHistoryRepository();
        $this->widgets = new DashboardWidgets();
    }

    /**
     * Initialize scheduler hooks
     *
     * @return void
     */
    public function init(): void {
        // Register cron action
        add_action(self::CRON_HOOK, [$this, 'take_snapshot']);
        
        // Add custom cron schedules
        add_filter('cron_schedules', [$this, 'add_custom_schedules']);
        
        // Handle schedule changes
        add_action('update_option_complyflow_settings', [$this, 'handle_schedule_change'], 10, 2);
        
        // Ensure schedule is active
        $this->ensure_schedule_active();
    }

    /**
     * Add custom cron schedules
     *
     * @param array $schedules Existing schedules
     * @return array Modified schedules
     */
    public function add_custom_schedules(array $schedules): array {
        // Add fortnightly schedule (14 days)
        if (!isset($schedules['fortnightly'])) {
            $schedules['fortnightly'] = [
                'interval' => 14 * DAY_IN_SECONDS,
                'display' => __('Every 2 Weeks', 'complyflow'),
            ];
        }

        return $schedules;
    }

    /**
     * Take a compliance snapshot (cron callback)
     *
     * @return bool True on success, false on failure
     */
    public function take_snapshot(): bool {
        // Check if table exists
        if (!$this->repository->table_exists()) {
            error_log('ComplyFlow: Compliance history table does not exist. Skipping snapshot.');
            return false;
        }

        // Check if snapshot already exists for today (prevent duplicates)
        if ($this->repository->has_snapshot_today()) {
            error_log('ComplyFlow: Snapshot already exists for today. Skipping.');
            return false;
        }

        // Get current compliance data
        $compliance_score = $this->widgets->get_compliance_score();
        $accessibility_summary = $this->widgets->get_accessibility_summary();
        $dsr_stats = $this->widgets->get_dsr_statistics();
        $consent_stats = $this->widgets->get_consent_statistics();
        $cookie_summary = $this->widgets->get_cookie_summary();

        // Prepare snapshot data
        $snapshot = [
            'compliance_score' => $compliance_score['score'] ?? 0,
            'module_scores' => $compliance_score['breakdown'] ?? [],
            'accessibility_issues' => $accessibility_summary['total_issues'] ?? 0,
            'dsr_pending_count' => $dsr_stats['pending'] ?? 0,
            'consent_acceptance_rate' => $consent_stats['acceptance_rate'] ?? 0,
            'cookie_count' => $cookie_summary['total_cookies'] ?? 0,
        ];

        // Save snapshot
        $result = $this->repository->save_snapshot($snapshot);

        if ($result) {
            error_log('ComplyFlow: Compliance snapshot saved successfully (ID: ' . $result . ')');
            
            // Perform cleanup based on retention period
            $retention_days = get_option('complyflow_data_retention', 365);
            $this->repository->cleanup_old_records($retention_days);
            
            return true;
        }

        error_log('ComplyFlow: Failed to save compliance snapshot');
        return false;
    }

    /**
     * Ensure cron schedule is active
     *
     * @return void
     */
    public function ensure_schedule_active(): void {
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            $this->schedule_next_snapshot();
        }
    }

    /**
     * Schedule next snapshot based on user settings
     *
     * @return void
     */
    private function schedule_next_snapshot(): void {
        $settings = get_option('complyflow_settings', []);
        $schedule = $settings['compliance_history_schedule'] ?? 'daily';

        // Map schedule to WP-Cron recurrence
        $recurrence_map = [
            'daily' => 'daily',
            'weekly' => 'weekly',
            'fortnightly' => 'fortnightly',
            'monthly' => 'monthly',
        ];

        $recurrence = $recurrence_map[$schedule] ?? 'daily';

        // Schedule the event
        wp_schedule_event(time(), $recurrence, self::CRON_HOOK);
    }

    /**
     * Handle schedule change when settings are updated
     *
     * @param array $old_value Old settings values
     * @param array $new_value New settings values
     * @return void
     */
    public function handle_schedule_change($old_value, $new_value): void {
        $old_schedule = $old_value['compliance_history_schedule'] ?? null;
        $new_schedule = $new_value['compliance_history_schedule'] ?? null;

        // If schedule changed, reschedule
        if ($old_schedule !== $new_schedule && $new_schedule !== null) {
            $this->reschedule($new_schedule);
        }
    }

    /**
     * Reschedule cron job
     *
     * @param string $new_schedule New schedule (daily, weekly, fortnightly, monthly)
     * @return void
     */
    public function reschedule(string $new_schedule): void {
        // Clear existing schedule
        $this->clear_schedule();
        
        // Wait a moment to ensure clean state
        usleep(100000); // 0.1 seconds
        
        // Schedule with new recurrence
        $this->schedule_next_snapshot();
        
        error_log("ComplyFlow: Compliance snapshot rescheduled to '{$new_schedule}'");
    }

    /**
     * Clear scheduled events
     *
     * @return void
     */
    public function clear_schedule(): void {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }
    }

    /**
     * Force take snapshot (manual trigger)
     *
     * @return bool True on success
     */
    public function force_snapshot(): bool {
        return $this->take_snapshot();
    }

    /**
     * Get next scheduled time
     *
     * @return int|false Timestamp of next scheduled event or false
     */
    public function get_next_scheduled() {
        return wp_next_scheduled(self::CRON_HOOK);
    }

    /**
     * Get current schedule setting
     *
     * @return string Current schedule (daily, weekly, fortnightly, monthly)
     */
    public function get_current_schedule(): string {
        $settings = get_option('complyflow_settings', []);
        return $settings['compliance_history_schedule'] ?? 'daily';
    }
}
