<?php
/**
 * Settings API Class
 *
 * Manages plugin settings with validation, sanitization, and tabbed interface.
 *
 * @package ComplyFlow\Admin
 * @since 1.0.0
 */

namespace ComplyFlow\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings Class
 *
 * @since 1.0.0
 */
class Settings {
    /**
     * Settings option name
     *
     * @var string
     */
    private const OPTION_NAME = 'complyflow_settings';

    /**
     * Settings tabs
     *
     * @var array<string, array<string, string>>
     */
    private array $tabs = [];

    /**
     * Settings sections
     *
     * @var array<string, array<string, mixed>>
     */
    private array $sections = [];

    /**
     * Settings fields
     *
     * @var array<string, array<string, mixed>>
     */
    private array $fields = [];

    /**
     * Current settings values
     *
     * @var array<string, mixed>
     */
    private array $settings = [];

    /**
     * Initialize settings
     */
    public function __construct() {
        $this->settings = get_option(self::OPTION_NAME, []);
        $this->register_default_tabs();
        $this->register_default_sections();
        $this->register_default_fields();
        
        // Debug: Check what was registered
        error_log('Settings initialized - Tabs: ' . count($this->tabs) . ', Sections: ' . count($this->sections) . ', Fields: ' . count($this->fields));
    }

    /**
     * Register default tabs
     *
     * @return void
     */
    private function register_default_tabs(): void {
        $this->tabs = [
            'general' => [
                'title' => __('General', 'complyflow'),
                'icon' => 'dashicons-admin-generic',
            ],
            'consent' => [
                'title' => __('Consent Manager', 'complyflow'),
                'icon' => 'dashicons-shield',
            ],
            'accessibility' => [
                'title' => __('Accessibility', 'complyflow'),
                'icon' => 'dashicons-universal-access',
            ],
            'dsr' => [
                'title' => __('DSR Portal', 'complyflow'),
                'icon' => 'dashicons-privacy',
            ],
            'documents' => [
                'title' => __('Legal Documents', 'complyflow'),
                'icon' => 'dashicons-media-document',
            ],
            'advanced' => [
                'title' => __('Advanced', 'complyflow'),
                'icon' => 'dashicons-admin-tools',
            ],
        ];

        /**
         * Filter settings tabs
         *
         * @param array<string, array<string, string>> $tabs Settings tabs
         */
        $this->tabs = apply_filters('complyflow_settings_tabs', $this->tabs);
    }

    /**
     * Register default sections
     *
     * @return void
     */
    private function register_default_sections(): void {
        $this->sections = [
            'general_basic' => [
                'tab' => 'general',
                'title' => __('Basic Settings', 'complyflow'),
                'description' => __('Core plugin configuration including site identity, contact information, and data retention policies that affect all compliance modules.', 'complyflow'),
            ],
            'general_modules' => [
                'tab' => 'general',
                'title' => __('Module Management', 'complyflow'),
                'description' => __('Enable or disable individual compliance features. Disabling unused modules improves performance and reduces administrative overhead.', 'complyflow'),
            ],
            'consent_banner' => [
                'tab' => 'consent',
                'title' => __('Banner Settings', 'complyflow'),
                'description' => __('Customize the appearance, position, and behavior of your consent banner. These settings ensure compliance while maintaining a good user experience.', 'complyflow'),
            ],
            'consent_geo' => [
                'tab' => 'consent',
                'title' => __('Geo-Targeting', 'complyflow'),
                'description' => __('Automatically adjust consent requirements based on visitor location to comply with regional privacy laws (GDPR, CCPA, LGPD, etc.) while minimizing impact on non-regulated visitors.', 'complyflow'),
            ],
            'accessibility_scanner' => [
                'tab' => 'accessibility',
                'title' => __('Scanner Settings', 'complyflow'),
                'description' => __('Configure WCAG 2.2 compliance scanning to meet ADA, Section 508, and international accessibility standards. Choose your target compliance level and automation preferences.', 'complyflow'),
            ],
            'dsr_portal' => [
                'tab' => 'dsr',
                'title' => __('Portal Settings', 'complyflow'),
                'description' => __('Configure the Data Subject Request portal for handling access, erasure, portability, and objection requests. Settings control identity verification methods and response timelines per GDPR Article 12-23.', 'complyflow'),
            ],
            'documents_general' => [
                'tab' => 'documents',
                'title' => __('Document Settings', 'complyflow'),
                'description' => __('Configure automated legal document generation for Privacy Policies, Cookie Policies, and Terms of Service. These templates are kept up-to-date with current privacy law requirements.', 'complyflow'),
            ],
            'documents_privacy' => [
                'tab' => 'documents',
                'title' => __('Privacy Policy', 'complyflow'),
                'description' => __('Customize your Privacy Policy content including data collection practices, third-party services, and user rights under GDPR, CCPA, and other privacy laws.', 'complyflow'),
            ],
            'advanced_performance' => [
                'tab' => 'advanced',
                'title' => __('Performance', 'complyflow'),
                'description' => __('Optimize plugin performance through caching of scan results, settings, and frequently-accessed data. Adjust cache duration to balance performance with data freshness requirements.', 'complyflow'),
            ],
            'advanced_security' => [
                'tab' => 'advanced',
                'title' => __('Security', 'complyflow'),
                'description' => __('Enhanced privacy and security features including IP anonymization for GDPR compliance and comprehensive audit logging for accountability and regulatory requirements.', 'complyflow'),
            ],
        ];

        /**
         * Filter settings sections
         *
         * @param array<string, array<string, mixed>> $sections Settings sections
         */
        $this->sections = apply_filters('complyflow_settings_sections', $this->sections);
    }

    /**
     * Register default fields
     *
     * @return void
     */
    private function register_default_fields(): void {
        $this->fields = [
            // General - Basic
            'site_name' => [
                'section' => 'general_basic',
                'type' => 'text',
                'label' => __('Site Name', 'complyflow'),
                'description' => __('The official name of your website or organization that will appear in all generated legal documents, privacy policies, and compliance notices. This should match your registered business name.', 'complyflow'),
                'default' => get_bloginfo('name'),
            ],
            'contact_email' => [
                'section' => 'general_basic',
                'type' => 'email',
                'label' => __('Contact Email', 'complyflow'),
                'description' => __('Primary email address for receiving data subject requests, privacy inquiries, and compliance-related communications. This email will be displayed in your privacy policy and used for DSR notifications.', 'complyflow'),
                'default' => get_option('admin_email'),
            ],
            'data_retention_days' => [
                'section' => 'general_basic',
                'type' => 'number',
                'label' => __('Data Retention Period', 'complyflow'),
                'description' => __('Number of days to retain consent logs, DSR records, and audit trails before automatic deletion. Must comply with GDPR Article 5(1)(e) storage limitation principle. Recommended: 365 days minimum for legal defense, 3 years maximum for most businesses.', 'complyflow'),
                'default' => 365,
                'min' => 30,
                'max' => 3650,
                'suffix' => __('days', 'complyflow'),
            ],

            // General - Modules
            'module_consent' => [
                'section' => 'general_modules',
                'type' => 'toggle',
                'label' => __('Consent Manager', 'complyflow'),
                'description' => __('Enables intelligent consent banners with geo-targeting for GDPR (EU), CCPA (California), LGPD (Brazil), and other regional privacy laws. Includes cookie categorization, granular consent options, and proof-of-consent logging.', 'complyflow'),
                'default' => true,
            ],
            'module_accessibility' => [
                'section' => 'general_modules',
                'type' => 'toggle',
                'label' => __('Accessibility Scanner', 'complyflow'),
                'description' => __('Automated WCAG 2.2 compliance scanner that detects accessibility issues including missing alt text, color contrast problems, keyboard navigation issues, ARIA violations, and heading structure problems. Helps meet ADA Section 508 requirements.', 'complyflow'),
                'default' => true,
            ],
            'module_dsr' => [
                'section' => 'general_modules',
                'type' => 'toggle',
                'label' => __('DSR Portal', 'complyflow'),
                'description' => __('Self-service portal for Data Subject Requests including right to access, rectification, erasure (right to be forgotten), data portability, and objection. Includes identity verification, automated workflows, and deadline tracking per GDPR Article 12-23.', 'complyflow'),
                'default' => true,
            ],
            'module_documents' => [
                'section' => 'general_modules',
                'type' => 'toggle',
                'label' => __('Legal Documents', 'complyflow'),
                'description' => __('Automated legal document generator for Privacy Policies, Cookie Policies, Terms of Service, and Data Processing Agreements. Templates are regularly updated to reflect current GDPR, CCPA, and international privacy law requirements.', 'complyflow'),
                'default' => true,
            ],
            'module_inventory' => [
                'section' => 'general_modules',
                'type' => 'toggle',
                'label' => __('Cookie Inventory', 'complyflow'),
                'description' => __('Automatic detection and categorization of cookies and tracking technologies (first-party, third-party, session, persistent). Identifies vendors, purposes, and durations for cookie policy documentation and consent management compliance.', 'complyflow'),
                'default' => true,
            ],

            // Consent - Banner
            'consent_position' => [
                'section' => 'consent_banner',
                'type' => 'select',
                'label' => __('Banner Position', 'complyflow'),
                'description' => __('Choose where the consent banner appears on your site. Bottom bar is least intrusive, top bar is more noticeable, center modal ensures visibility and compliance with "clear and conspicuous" notice requirements.', 'complyflow'),
                'default' => 'bottom',
                'options' => [
                    'top' => __('Top', 'complyflow'),
                    'bottom' => __('Bottom', 'complyflow'),
                    'center' => __('Center (Modal)', 'complyflow'),
                ],
            ],
            'consent_cookie_lifetime' => [
                'section' => 'consent_banner',
                'type' => 'number',
                'label' => __('Cookie Lifetime', 'complyflow'),
                'description' => __('Duration the user\'s consent choice is remembered before re-prompting. GDPR allows up to 12 months (365 days), CCPA allows up to 24 months. Must re-confirm if privacy policy changes materially.', 'complyflow'),
                'default' => 365,
                'min' => 30,
                'max' => 730,
                'suffix' => __('days', 'complyflow'),
            ],
            'consent_primary_color' => [
                'section' => 'consent_banner',
                'type' => 'color',
                'label' => __('Primary Color', 'complyflow'),
                'description' => __('Main color for consent banner buttons and interactive elements. Should match your brand colors while maintaining sufficient contrast for accessibility (WCAG AA requires 4.5:1 ratio for normal text).', 'complyflow'),
                'default' => '#4361ee',
            ],

            // Consent - Geo
            'consent_geo_enabled' => [
                'section' => 'consent_geo',
                'type' => 'toggle',
                'label' => __('Enable Geo-Targeting', 'complyflow'),
                'description' => __('Automatically detect visitor location and display region-appropriate consent requirements. EU visitors see strict GDPR opt-in, California visitors see CCPA opt-out, others see notice-only. Reduces compliance overhead and improves user experience.', 'complyflow'),
                'default' => true,
            ],
            'consent_eu_mode' => [
                'section' => 'consent_geo',
                'type' => 'select',
                'label' => __('EU Mode', 'complyflow'),
                'description' => __('Opt-In (recommended): Blocks non-essential cookies until user consents (GDPR Article 7 compliant). Opt-Out: Loads all cookies but allows rejection. Notice Only: Informational banner without blocking (not GDPR compliant for tracking).', 'complyflow'),
                'default' => 'opt_in',
                'options' => [
                    'opt_in' => __('Opt-In (GDPR Compliant)', 'complyflow'),
                    'opt_out' => __('Opt-Out', 'complyflow'),
                    'notice' => __('Notice Only', 'complyflow'),
                ],
            ],

            // Accessibility
            'accessibility_wcag_level' => [
                'section' => 'accessibility_scanner',
                'type' => 'select',
                'label' => __('WCAG Level', 'complyflow'),
                'description' => __('Level A: Minimum accessibility (basic). Level AA: Recommended for most sites, required by ADA and Section 508 (mid-range). Level AAA: Highest standard, enhanced accessibility for specialized audiences (advanced). Each level includes all requirements from lower levels.', 'complyflow'),
                'default' => 'AA',
                'options' => [
                    'A' => __('Level A', 'complyflow'),
                    'AA' => __('Level AA', 'complyflow'),
                    'AAA' => __('Level AAA', 'complyflow'),
                ],
            ],
            'accessibility_auto_scan' => [
                'section' => 'accessibility_scanner',
                'type' => 'toggle',
                'label' => __('Auto Scan', 'complyflow'),
                'description' => __('Automatically scan newly published or updated posts and pages for accessibility issues in the background. Helps maintain ongoing compliance and alerts content creators to issues before they reach visitors. May impact server performance on high-traffic sites.', 'complyflow'),
                'default' => false,
            ],
            'compliance_history_schedule' => [
                'section' => 'accessibility_scanner',
                'type' => 'select',
                'label' => __('Compliance History Tracking', 'complyflow'),
                'description' => __('Automatically calculate and store compliance scores for historical trending in the Dashboard. More frequent tracking provides better insights into compliance changes over time. Stored data is used for the "30-Day Compliance Trend" chart.', 'complyflow'),
                'default' => 'daily',
                'options' => [
                    'daily' => __('Daily (Recommended)', 'complyflow'),
                    'weekly' => __('Weekly', 'complyflow'),
                    'fortnightly' => __('Every 2 Weeks', 'complyflow'),
                    'monthly' => __('Monthly', 'complyflow'),
                ],
            ],

            // DSR
            'dsr_verification_method' => [
                'section' => 'dsr_portal',
                'type' => 'select',
                'label' => __('Verification Method', 'complyflow'),
                'description' => __('Email Code: Send 6-digit verification code (higher security, requires user action). Email Link: Send magic link for one-click verification (lower friction). Both methods comply with GDPR Article 12 requirement to verify identity before processing DSR requests.', 'complyflow'),
                'default' => 'email',
                'options' => [
                    'email' => __('Email Code (6-digit)', 'complyflow'),
                    'link' => __('Email Link', 'complyflow'),
                ],
            ],
            'dsr_response_days' => [
                'section' => 'dsr_portal',
                'type' => 'number',
                'label' => __('Response Deadline', 'complyflow'),
                'description' => __('Maximum days to respond to Data Subject Requests. GDPR Article 12 requires 30 days (extendable to 60 for complex requests). CCPA requires 45 days. Setting internal deadline shorter than legal requirement provides buffer time for review.', 'complyflow'),
                'default' => 30,
                'min' => 7,
                'max' => 90,
                'suffix' => __('days', 'complyflow'),
            ],

            // Documents - General
            'documents_auto_update' => [
                'section' => 'documents_general',
                'type' => 'toggle',
                'label' => __('Auto-Update Documents', 'complyflow'),
                'description' => __('Automatically update legal documents when plugin settings change (e.g., contact email, data retention period). Recommended to ensure documents stay synchronized with your actual practices. Manual review recommended after updates.', 'complyflow'),
                'default' => true,
            ],
            'documents_last_updated' => [
                'section' => 'documents_general',
                'type' => 'toggle',
                'label' => __('Show Last Updated Date', 'complyflow'),
                'description' => __('Display "Last Updated" date on generated legal documents. Transparency about policy changes is required by GDPR Article 13 and helps build user trust. Users should be notified when policies change materially.', 'complyflow'),
                'default' => true,
            ],
            'documents_language' => [
                'section' => 'documents_general',
                'type' => 'select',
                'label' => __('Document Language', 'complyflow'),
                'description' => __('Primary language for generated legal documents. Multi-language sites should provide documents in all languages offered to users per GDPR Article 12(1) requirement for "clear and plain language".', 'complyflow'),
                'default' => 'en',
                'options' => [
                    'en' => __('English', 'complyflow'),
                    'es' => __('Spanish', 'complyflow'),
                    'fr' => __('French', 'complyflow'),
                    'de' => __('German', 'complyflow'),
                    'it' => __('Italian', 'complyflow'),
                    'pt' => __('Portuguese', 'complyflow'),
                ],
            ],

            // Documents - Privacy Policy
            'privacy_policy_include_contact' => [
                'section' => 'documents_privacy',
                'type' => 'toggle',
                'label' => __('Include Contact Information', 'complyflow'),
                'description' => __('Add contact details (email, phone, address) to Privacy Policy. GDPR Article 13(1)(a) requires identity and contact details of data controller. Essential for users to exercise their rights.', 'complyflow'),
                'default' => true,
            ],
            'privacy_policy_include_dpo' => [
                'section' => 'documents_privacy',
                'type' => 'toggle',
                'label' => __('Include DPO Information', 'complyflow'),
                'description' => __('Add Data Protection Officer (DPO) contact details if appointed. GDPR Article 37 requires DPOs for public authorities and organizations processing sensitive data at scale. If you have a DPO, their contact info must be published per Article 37(7).', 'complyflow'),
                'default' => false,
            ],
            'privacy_policy_dpo_email' => [
                'section' => 'documents_privacy',
                'type' => 'email',
                'label' => __('DPO Email', 'complyflow'),
                'description' => __('Email address of your Data Protection Officer. Leave empty if no DPO appointed. This will be displayed in your Privacy Policy for users to contact regarding data protection matters.', 'complyflow'),
                'default' => '',
            ],
            'privacy_policy_retention_details' => [
                'section' => 'documents_privacy',
                'type' => 'textarea',
                'label' => __('Data Retention Details', 'complyflow'),
                'description' => __('Describe specific retention periods for different data types (e.g., "Account data: until deletion requested", "Order history: 7 years for tax purposes"). GDPR Article 13(2)(a) requires informing users about storage periods. Be specific about different categories.', 'complyflow'),
                'default' => '',
            ],
            'privacy_policy_third_parties' => [
                'section' => 'documents_privacy',
                'type' => 'textarea',
                'label' => __('Third-Party Services', 'complyflow'),
                'description' => __('List third-party services that process user data (e.g., "Google Analytics for website statistics", "Stripe for payment processing", "Mailchimp for newsletters"). GDPR Article 13(1)(e) requires disclosure of data recipients. Include purpose and link to their privacy policies.', 'complyflow'),
                'default' => '',
            ],

            // Advanced - Performance
            'cache_enabled' => [
                'section' => 'advanced_performance',
                'type' => 'toggle',
                'label' => __('Enable Caching', 'complyflow'),
                'description' => __('Cache accessibility scan results, cookie inventory data, and frequently-accessed settings to improve performance and reduce database queries. Recommended for production sites. Disable for development/testing to see real-time changes.', 'complyflow'),
                'default' => true,
            ],
            'cache_duration' => [
                'section' => 'advanced_performance',
                'type' => 'number',
                'label' => __('Cache Duration', 'complyflow'),
                'description' => __('Time in seconds to store cached data before refreshing. 3600 (1 hour) balances performance and data freshness. Shorter durations (300-900s) for frequently-changing sites, longer (7200-86400s) for static content. Cache is automatically cleared when settings change.', 'complyflow'),
                'default' => 3600,
                'min' => 300,
                'max' => 86400,
                'suffix' => __('seconds', 'complyflow'),
            ],

            // Advanced - Security
            'ip_anonymization' => [
                'section' => 'advanced_security',
                'type' => 'toggle',
                'label' => __('IP Anonymization', 'complyflow'),
                'description' => __('Mask last octet of IP addresses (192.168.1.xxx becomes 192.168.1.0) before storage to comply with GDPR Article 6(1)(f) legitimate interest and data minimization requirements. Recommended for EU audiences. Reduces ability to identify individual users while maintaining geographic/network data.', 'complyflow'),
                'default' => true,
            ],
            'enable_audit_log' => [
                'section' => 'advanced_security',
                'type' => 'toggle',
                'label' => __('Audit Log', 'complyflow'),
                'description' => __('Maintain detailed records of all compliance actions including DSR processing, consent changes, settings modifications, and document updates. Provides accountability trail and evidence of GDPR Article 5(2) compliance ("demonstrate compliance"). Essential for regulatory audits and legal defense.', 'complyflow'),
                'default' => true,
            ],
            'delete_data_on_uninstall' => [
                'section' => 'advanced_security',
                'type' => 'toggle',
                'label' => __('Delete All Data on Uninstall', 'complyflow'),
                'description' => __('⚠️ WARNING: When enabled, uninstalling the plugin will permanently delete ALL data including consent logs, DSR records, scan results, and settings. This action cannot be undone. Keep disabled (recommended) to preserve data if plugin is accidentally uninstalled. Enable only if you want complete data removal when uninstalling.', 'complyflow'),
                'default' => false,
            ],
        ];

        /**
         * Filter settings fields
         *
         * @param array<string, array<string, mixed>> $fields Settings fields
         */
        $this->fields = apply_filters('complyflow_settings_fields', $this->fields);
    }

    /**
     * Get setting value
     *
     * @param string $key     Setting key.
     * @param mixed  $default Default value if setting doesn't exist.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed {
        // Check cache first
        $cache = \ComplyFlow\Core\Cache::get_instance();
        $cache_key = 'setting_' . $key;
        
        $cached = $cache->get($cache_key, 'settings');
        if (false !== $cached) {
            return $cached;
        }

        if (isset($this->settings[$key])) {
            $value = $this->settings[$key];
        } elseif (isset($this->fields[$key]['default'])) {
            $value = $this->fields[$key]['default'];
        } else {
            $value = $default;
        }

        // Cache the value
        $cache->set($cache_key, $value, 'settings');

        return $value;
    }

    /**
     * Set setting value
     *
     * @param string $key   Setting key.
     * @param mixed  $value Setting value.
     * @return bool
     */
    public function set(string $key, mixed $value): bool {
        $this->settings[$key] = $this->sanitize_value($key, $value);
        $result = update_option(self::OPTION_NAME, $this->settings);

        // Invalidate cache on successful update
        if ($result) {
            $cache = \ComplyFlow\Core\Cache::get_instance();
            $cache->flush_group('settings');
        }

        return $result;
    }

    /**
     * Get all settings
     *
     * @return array<string, mixed>
     */
    public function get_all(): array {
        // Check cache first
        $cache = \ComplyFlow\Core\Cache::get_instance();
        $cached = $cache->get('all_settings', 'settings');
        
        if (false !== $cached) {
            return $cached;
        }

        // Cache all settings
        $cache->set('all_settings', $this->settings, 'settings');

        return $this->settings;
    }

    /**
     * Save all settings
     *
     * @param array<string, mixed> $new_settings New settings array.
     * @return bool
     */
    public function save(array $new_settings): bool {
        $sanitized = [];

        foreach ($new_settings as $key => $value) {
            $sanitized[$key] = $this->sanitize_value($key, $value);
        }

        $this->settings = array_merge($this->settings, $sanitized);
        $result = update_option(self::OPTION_NAME, $this->settings);

        // Invalidate cache on successful update
        if ($result) {
            $cache = \ComplyFlow\Core\Cache::get_instance();
            $cache->flush_group('settings');
        }

        return $result;
    }

    /**
     * Sanitize setting value
     *
     * @param string $key   Setting key.
     * @param mixed  $value Setting value.
     * @return mixed
     */
    private function sanitize_value(string $key, mixed $value): mixed {
        if (!isset($this->fields[$key])) {
            return sanitize_text_field($value);
        }

        $field = $this->fields[$key];
        $type = $field['type'] ?? 'text';

        return match ($type) {
            'text' => sanitize_text_field($value),
            'email' => sanitize_email($value),
            'url' => esc_url_raw($value),
            'number' => absint($value),
            'textarea' => sanitize_textarea_field($value),
            'toggle' => (bool) $value,
            'select' => sanitize_key($value),
            'color' => sanitize_hex_color($value),
            default => sanitize_text_field($value),
        };
    }

    /**
     * Validate settings
     *
     * @param array<string, mixed> $settings Settings to validate.
     * @return array{valid: bool, errors: array<string, string>}
     */
    public function validate(array $settings): array {
        $errors = [];

        foreach ($settings as $key => $value) {
            if (!isset($this->fields[$key])) {
                continue;
            }

            $field = $this->fields[$key];

            // Check required fields
            if (!empty($field['required']) && empty($value)) {
                $errors[$key] = sprintf(
                    /* translators: %s: Field label */
                    __('%s is required.', 'complyflow'),
                    $field['label']
                );
                continue;
            }

            // Type-specific validation
            switch ($field['type']) {
                case 'email':
                    if (!is_email($value)) {
                        $errors[$key] = __('Invalid email address.', 'complyflow');
                    }
                    break;

                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $errors[$key] = __('Invalid URL.', 'complyflow');
                    }
                    break;

                case 'number':
                    $min = $field['min'] ?? null;
                    $max = $field['max'] ?? null;

                    if ($min !== null && $value < $min) {
                        $errors[$key] = sprintf(
                            /* translators: %d: Minimum value */
                            __('Value must be at least %d.', 'complyflow'),
                            $min
                        );
                    }

                    if ($max !== null && $value > $max) {
                        $errors[$key] = sprintf(
                            /* translators: %d: Maximum value */
                            __('Value must be at most %d.', 'complyflow'),
                            $max
                        );
                    }
                    break;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Export settings
     *
     * @return string JSON encoded settings
     */
    public function export(): string {
        $export_data = [
            'version' => COMPLYFLOW_VERSION,
            'exported_at' => current_time('mysql'),
            'site_url' => site_url(),
            'settings' => $this->settings,
        ];

        return wp_json_encode($export_data, JSON_PRETTY_PRINT);
    }

    /**
     * Import settings
     *
     * @param string $json JSON encoded settings.
     * @return array{success: bool, message: string}
     */
    public function import(string $json): array {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => __('Invalid JSON format.', 'complyflow'),
            ];
        }

        if (!isset($data['settings']) || !is_array($data['settings'])) {
            return [
                'success' => false,
                'message' => __('Invalid settings format.', 'complyflow'),
            ];
        }

        // Validate before importing
        $validation = $this->validate($data['settings']);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => __('Settings validation failed.', 'complyflow'),
            ];
        }

        // Import settings
        $this->save($data['settings']);

        return [
            'success' => true,
            'message' => __('Settings imported successfully.', 'complyflow'),
        ];
    }

    /**
     * Reset settings to defaults
     *
     * @return bool
     */
    public function reset(): bool {
        $defaults = [];

        foreach ($this->fields as $key => $field) {
            if (isset($field['default'])) {
                $defaults[$key] = $field['default'];
            }
        }

        $this->settings = $defaults;
        return update_option(self::OPTION_NAME, $this->settings);
    }

    /**
     * Get tabs
     *
     * @return array<string, array<string, string>>
     */
    public function get_tabs(): array {
        return $this->tabs;
    }

    /**
     * Get sections for a tab
     *
     * @param string $tab Tab key.
     * @return array<string, array<string, mixed>>
     */
    public function get_sections(string $tab): array {
        return array_filter($this->sections, function ($section) use ($tab) {
            return $section['tab'] === $tab;
        });
    }

    /**
     * Get fields for a section
     *
     * @param string $section Section key.
     * @return array<string, array<string, mixed>>
     */
    public function get_fields(string $section): array {
        return array_filter($this->fields, function ($field) use ($section) {
            return $field['section'] === $section;
        });
    }
}
