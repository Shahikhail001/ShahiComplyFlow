<?php
namespace ComplyFlow\Modules\DevTools;

if (!defined('ABSPATH')) {
    exit;
}

class Hooks {
    /**
     * Register custom action and filter hooks for developers
     */
    public static function register_hooks() {
        // Action: After compliance score calculation
        do_action('complyflow_after_score', \ComplyFlow\Modules\Analytics\ComplianceScore::calculate());
        // Filter: Modify consent categories
        add_filter('complyflow_consent_categories', function($cats) {
            $cats['custom'] = 'Custom Tool';
            return $cats;
        });
        // Action: Collect custom DSR data
        add_action('complyflow_dsr_collect', function($email, $type) {
            // Example: Export custom data
        }, 10, 2);
    }
}
