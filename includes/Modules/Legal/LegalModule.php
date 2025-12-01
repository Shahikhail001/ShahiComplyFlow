<?php
/**
 * Legal Module
 *
 * Handles legal document generation (Privacy Policy, Terms, Cookie Policy).
 *
 * @package ComplyFlow\Modules\Legal
 * @since   2.0.1
 */

namespace ComplyFlow\Modules\Legal;

use ComplyFlow\Core\Interfaces\ModuleInterface;
use ComplyFlow\Core\Repositories\SettingsRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class LegalModule
 */
class LegalModule implements ModuleInterface {
    /**
     * Module slug
     *
     * @var string
     */
    private const SLUG = 'legal';

    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Policy generator
     *
     * @var PolicyGenerator
     */
    private PolicyGenerator $generator;

    /**
     * Template manager
     *
     * @var TemplateManager
     */
    private TemplateManager $templates;

    /**
     * Constructor
     *
     * @param SettingsRepository $settings Settings repository.
     */
    public function __construct(SettingsRepository $settings) {
        $this->settings = $settings;
        $this->templates = new TemplateManager();
        $this->generator = new PolicyGenerator($this->settings, $this->templates);
    }

    /**
     * Get module info
     *
     * @return array Module information.
     */
    public static function get_info(): array {
        return [
            'name' => __('Legal Documents', 'complyflow'),
            'description' => __('Generate and manage legal documents like Privacy Policy, Terms of Service, and Cookie Policy.', 'complyflow'),
            'slug' => self::SLUG,
            'version' => '1.0.0',
            'author' => 'ComplyFlow Team',
            'dependencies' => [],
        ];
    }

    /**
     * Initialize module
     *
     * @return void
     */
    public function init(): void {
        $this->register_hooks();
        $this->register_shortcodes();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    public function register_hooks(): void {
        // Admin hooks
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

        // AJAX handlers
        add_action('wp_ajax_complyflow_save_questionnaire', [$this, 'ajax_save_questionnaire']);
        add_action('wp_ajax_complyflow_generate_policy', [$this, 'ajax_generate_policy']);
        add_action('wp_ajax_complyflow_save_policy', [$this, 'ajax_save_policy']);
        add_action('wp_ajax_complyflow_export_policy', [$this, 'ajax_export_policy']);
    }

    /**
     * Register shortcodes
     *
     * @return void
     */
    private function register_shortcodes(): void {
        add_shortcode('complyflow_policy', [$this, 'render_policy_shortcode']);
        add_shortcode('complyflow_privacy_policy', [$this, 'render_privacy_policy_shortcode']);
        add_shortcode('complyflow_terms', [$this, 'render_terms_shortcode']);
        add_shortcode('complyflow_cookie_policy', [$this, 'render_cookie_policy_shortcode']);
    }

    /**
     * Add admin menu
     *
     * @return void
     */
    public function add_admin_menu(): void {
        add_submenu_page(
            'complyflow',
            __('Legal Documents', 'complyflow'),
            __('Legal Documents', 'complyflow'),
            'manage_options',
            'complyflow-legal',
            [$this, 'render_admin_page']
        );

        // Questionnaire submenu
        add_submenu_page(
            'complyflow-legal',
            __('Policy Questionnaire', 'complyflow'),
            __('Questionnaire', 'complyflow'),
            'manage_options',
            'complyflow-questionnaire',
            [$this, 'render_questionnaire_page']
        );
    }

    /**
     * Register settings
     *
     * @return void
     */
    public function register_settings(): void {
        // Questionnaire settings
        register_setting('complyflow_legal', 'legal_questionnaire_completed');
        register_setting('complyflow_legal', 'legal_questionnaire_answers');
        
        // Business information
        register_setting('complyflow_legal', 'legal_business_name');
        register_setting('complyflow_legal', 'legal_business_type');
        register_setting('complyflow_legal', 'legal_business_address');
        register_setting('complyflow_legal', 'legal_contact_email');
        register_setting('complyflow_legal', 'legal_contact_phone');
        
        // Data practices
        register_setting('complyflow_legal', 'legal_has_ecommerce');
        register_setting('complyflow_legal', 'legal_has_analytics');
        register_setting('complyflow_legal', 'legal_has_marketing');
        register_setting('complyflow_legal', 'legal_has_social_media');
        register_setting('complyflow_legal', 'legal_collects_children_data');
        register_setting('complyflow_legal', 'legal_international_transfers');
        
        // Generated policies
        register_setting('complyflow_legal', 'legal_privacy_policy');
        register_setting('complyflow_legal', 'legal_terms_of_service');
        register_setting('complyflow_legal', 'legal_cookie_policy');
        register_setting('complyflow_legal', 'legal_data_protection_policy');
        
        // Version tracking
        register_setting('complyflow_legal', 'legal_policy_versions');
        register_setting('complyflow_legal', 'legal_last_updated');
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     * @return void
     */
    public function enqueue_admin_assets(string $hook): void {
        if (!str_contains($hook, 'complyflow-legal') && !str_contains($hook, 'complyflow-questionnaire')) {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'complyflow-legal-admin',
            COMPLYFLOW_URL . 'assets/dist/css/legal-admin.css',
            [],
            COMPLYFLOW_VERSION
        );

        // Admin JS
        wp_enqueue_script(
            'complyflow-legal-admin',
            COMPLYFLOW_URL . 'assets/dist/js/legal-admin.js',
            ['jquery'],
            COMPLYFLOW_VERSION,
            true
        );

        // Get policies for JavaScript
        $policies = [
            'privacy' => $this->settings->get('legal_privacy_policy', ''),
            'terms' => $this->settings->get('legal_terms', ''),
            'cookie' => $this->settings->get('legal_cookie_policy', ''),
            'data_protection' => $this->settings->get('legal_data_protection', ''),
        ];

        wp_localize_script('complyflow-legal-admin', 'complyflowLegal', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('complyflow_legal_nonce'),
            'policies' => $policies,
            'i18n' => [
                'generating' => __('Generating policy...', 'complyflow'),
                'generatingAll' => __('Generating all policies...', 'complyflow'),
                'saving' => __('Saving...', 'complyflow'),
                'success' => __('Saved successfully!', 'complyflow'),
                'error' => __('An error occurred', 'complyflow'),
                'confirmRegenerate' => __('Are you sure you want to regenerate this policy? Any manual changes will be lost.', 'complyflow'),
                'confirmGenerateAll' => __('Generate all policies? This may take a moment.', 'complyflow'),
                'step' => __('Step', 'complyflow'),
                'of' => __('of', 'complyflow'),
                'requiredFields' => __('Please fill in all required fields.', 'complyflow'),
                'yourAnswers' => __('Your Answers', 'complyflow'),
                'businessInfo' => __('Business Information', 'complyflow'),
                'name' => __('Name', 'complyflow'),
                'type' => __('Type', 'complyflow'),
                'email' => __('Email', 'complyflow'),
                'dataCollection' => __('Data Collection', 'complyflow'),
                'ecommerce' => __('Ecommerce', 'complyflow'),
                'analytics' => __('Analytics', 'complyflow'),
                'marketing' => __('Marketing', 'complyflow'),
                'socialMedia' => __('Social Media', 'complyflow'),
                'targetRegions' => __('Target Regions', 'complyflow'),
                'noneSelected' => __('None selected', 'complyflow'),
            ],
        ]);
    }

    /**
     * Render admin page
     *
     * @return void
     */
    public function render_admin_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $generator = $this->generator;
        $completed = $this->settings->get('legal_questionnaire_completed', false);

        include COMPLYFLOW_PATH . 'includes/Admin/views/legal-documents.php';
    }

    /**
     * Render questionnaire page
     *
     * @return void
     */
    public function render_questionnaire_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $answers = $this->settings->get('legal_questionnaire_answers', []);

        include COMPLYFLOW_PATH . 'includes/Admin/views/legal-questionnaire.php';
    }

    /**
     * AJAX: Save questionnaire answers
     *
     * @return void
     */
    public function ajax_save_questionnaire(): void {
        check_ajax_referer('complyflow_legal_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $answers = $_POST['answers'] ?? [];

        // Sanitize answers
        $sanitized = [
            'business_name' => sanitize_text_field($answers['business_name'] ?? ''),
            'business_type' => sanitize_text_field($answers['business_type'] ?? ''),
            'business_address' => sanitize_textarea_field($answers['business_address'] ?? ''),
            'contact_email' => sanitize_email($answers['contact_email'] ?? ''),
            'contact_phone' => sanitize_text_field($answers['contact_phone'] ?? ''),
            'has_ecommerce' => !empty($answers['has_ecommerce']),
            'has_analytics' => !empty($answers['has_analytics']),
            'has_marketing' => !empty($answers['has_marketing']),
            'has_social_media' => !empty($answers['has_social_media']),
            'collects_children_data' => !empty($answers['collects_children_data']),
            'international_transfers' => !empty($answers['international_transfers']),
            'data_retention_period' => sanitize_text_field($answers['data_retention_period'] ?? '2 years'),
            'target_regions' => array_map('sanitize_text_field', $answers['target_regions'] ?? []),
        ];

        // Save to settings repository
        foreach ($sanitized as $key => $value) {
            $this->settings->set('legal_' . $key, $value);
        }

        $this->settings->set('legal_questionnaire_answers', $sanitized);
        $this->settings->set('legal_questionnaire_completed', true);

        wp_send_json_success([
            'message' => __('Questionnaire saved successfully!', 'complyflow'),
            'redirect' => admin_url('admin.php?page=complyflow-legal'),
        ]);
    }

    /**
     * AJAX: Generate policy
     *
     * @return void
     */
    public function ajax_generate_policy(): void {
        check_ajax_referer('complyflow_legal_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $type = sanitize_text_field($_POST['type'] ?? '');

        if (!in_array($type, ['privacy', 'terms', 'cookie', 'data_protection'], true)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        try {
            $content = match ($type) {
                'privacy' => $this->generator->generate_privacy_policy(),
                'terms' => $this->generator->generate_terms_of_service(),
                'cookie' => $this->generator->generate_cookie_policy(),
                'data_protection' => $this->generator->generate_data_protection_policy(),
            };

            wp_send_json_success([
                'content' => $content,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Save generated policy
     *
     * @return void
     */
    public function ajax_save_policy(): void {
        check_ajax_referer('complyflow_legal_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $type = sanitize_text_field($_POST['type'] ?? '');
        $content = wp_kses_post($_POST['content'] ?? '');

        if (empty($type) || empty($content)) {
            wp_send_json_error(['message' => __('Invalid parameters', 'complyflow')]);
            return;
        }

        // Save policy
        $setting_key = 'legal_' . $type;
        $this->settings->set($setting_key, $content);

        // Update version history
        $versions = $this->settings->get('legal_policy_versions', []);
        $versions[$type][] = [
            'date' => current_time('mysql'),
            'user' => get_current_user_id(),
            'content_hash' => md5($content),
        ];
        $this->settings->set('legal_policy_versions', $versions);

        // Update last updated timestamp
        $last_updated = $this->settings->get('legal_last_updated', []);
        $last_updated[$type] = current_time('mysql');
        $this->settings->set('legal_last_updated', $last_updated);

        wp_send_json_success([
            'message' => __('Policy saved successfully!', 'complyflow'),
        ]);
    }

    /**
     * AJAX: Export policy
     *
     * @return void
     */
    public function ajax_export_policy(): void {
        check_ajax_referer('complyflow_legal_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $type = sanitize_text_field($_POST['type'] ?? '');
        $format = sanitize_text_field($_POST['format'] ?? 'html');

        $content = $this->settings->get('legal_' . $type, '');

        if (empty($content)) {
            wp_send_json_error(['message' => __('Policy not found', 'complyflow')]);
            return;
        }

        if ($format === 'pdf') {
            // Generate PDF (requires library)
            wp_send_json_error(['message' => __('PDF export not yet implemented', 'complyflow')]);
            return;
        }

        // Return HTML
        wp_send_json_success([
            'content' => $content,
            'filename' => $type . '-policy-' . date('Y-m-d') . '.html',
        ]);
    }

    /**
     * Render policy shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string Rendered content.
     */
    public function render_policy_shortcode(array $atts): string {
        $atts = shortcode_atts([
            'type' => 'privacy',
        ], $atts);

        $content = $this->settings->get('legal_' . $atts['type'], '');

        if (empty($content)) {
            return '<p>' . esc_html__('Policy not yet generated.', 'complyflow') . '</p>';
        }

        $last_updated = $this->settings->get('legal_last_updated', []);
        $updated_date = $last_updated[$atts['type']] ?? '';

        $output = '<div class="complyflow-policy">';
        if ($updated_date) {
            $output .= '<p class="policy-updated"><em>' . 
                       sprintf(__('Last updated: %s', 'complyflow'), date_i18n(get_option('date_format'), strtotime($updated_date))) . 
                       '</em></p>';
        }
        $output .= wp_kses_post($content);
        $output .= '</div>';

        return $output;
    }

    /**
     * Render privacy policy shortcode
     *
     * @return string Rendered content.
     */
    public function render_privacy_policy_shortcode(): string {
        return $this->render_policy_shortcode(['type' => 'privacy_policy']);
    }

    /**
     * Render terms shortcode
     *
     * @return string Rendered content.
     */
    public function render_terms_shortcode(): string {
        return $this->render_policy_shortcode(['type' => 'terms_of_service']);
    }

    /**
     * Render cookie policy shortcode
     *
     * @return string Rendered content.
     */
    public function render_cookie_policy_shortcode(): string {
        return $this->render_policy_shortcode(['type' => 'cookie_policy']);
    }

    /**
     * Get policy generator
     *
     * @return PolicyGenerator
     */
    public function get_generator(): PolicyGenerator {
        return $this->generator;
    }

    /**
     * Get template manager
     *
     * @return TemplateManager
     */
    public function get_templates(): TemplateManager {
        return $this->templates;
    }
}
