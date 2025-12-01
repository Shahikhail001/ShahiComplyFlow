<?php
/**
 * Documents Module
 *
 * Handles legal document generation including Privacy Policy,
 * Terms of Service, and Cookie Policy.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   1.0.0
 */

namespace ComplyFlow\Modules\Documents;

use ComplyFlow\Core\Interfaces\ModuleInterface;
use ComplyFlow\Core\SettingsRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class DocumentsModule
 */
class DocumentsModule implements ModuleInterface {
    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Questionnaire instance
     *
     * @var Questionnaire
     */
    private Questionnaire $questionnaire;

    /**
     * Constructor
     *
     * @param SettingsRepository $settings Settings repository.
     */
    public function __construct(?SettingsRepository $settings = null) {
        $this->settings = $settings ?? SettingsRepository::get_instance();
        $this->questionnaire = new Questionnaire();
    }

    /**
     * Get module information
     *
     * @return array<string, mixed>
     */
    public static function get_info(): array {
        return [
            'name' => __('Legal Documents', 'complyflow'),
            'description' => __('Generate Privacy Policy, Terms of Service, and Cookie Policy content.', 'complyflow'),
            'slug' => 'documents',
            'version' => '1.0.0',
            'author' => 'ComplyFlow Team',
            'dependencies' => [],
        ];
    }

    /**
     * Get module ID
     *
     * @return string
     */
    public function get_id(): string {
        return 'documents';
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function get_name(): string {
        return __('Legal Documents', 'complyflow');
    }

    /**
     * Initialize module
     *
     * @return void
     */
    public function init(): void {
        $this->register_hooks();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    public function register_hooks(): void {
        // Admin hooks
        add_action('admin_menu', [$this, 'add_admin_menu'], 30);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_notices', [$this, 'show_regeneration_notice']);

        // AJAX handlers
        add_action('wp_ajax_complyflow_save_questionnaire', [$this, 'ajax_save_questionnaire']);
        add_action('wp_ajax_complyflow_generate_policy', [$this, 'ajax_generate_policy']);
        add_action('wp_ajax_complyflow_get_policy', [$this, 'ajax_get_policy']);
        add_action('wp_ajax_complyflow_save_policy', [$this, 'ajax_save_policy']);
        add_action('wp_ajax_complyflow_dismiss_regen_notice', [$this, 'ajax_dismiss_regeneration_notice']);

        // Shortcodes
        add_shortcode('complyflow_policy', [$this, 'render_policy_shortcode']);

        // Version history AJAX handlers
        add_action('wp_ajax_complyflow_get_version', [$this, 'ajax_get_version']);
        add_action('wp_ajax_complyflow_diff_versions', [$this, 'ajax_diff_versions']);
        add_action('wp_ajax_complyflow_rollback_version', [$this, 'ajax_rollback_version']);
        add_action('wp_ajax_complyflow_get_version_history', [$this, 'ajax_get_version_history']);
        add_action('wp_ajax_complyflow_compare_versions', [$this, 'ajax_compare_versions']);
        add_action('wp_ajax_complyflow_restore_version', [$this, 'ajax_restore_version']);
        add_action('wp_ajax_complyflow_export_pdf', [$this, 'ajax_export_pdf']);

        // Compliance change detection hooks
        $this->register_compliance_hooks();
    }

    /**
     * Register compliance change detection hooks
     *
     * @return void
     */
    private function register_compliance_hooks(): void {
        $compliance_options = [
            'complyflow_consent_gdpr_enabled',
            'complyflow_consent_uk_gdpr_enabled',
            'complyflow_consent_ccpa_enabled',
            'complyflow_consent_lgpd_enabled',
            'complyflow_consent_pipeda_enabled',
            'complyflow_consent_pdpa_sg_enabled',
            'complyflow_consent_pdpa_th_enabled',
            'complyflow_consent_appi_enabled',
            'complyflow_consent_popia_enabled',
            'complyflow_consent_kvkk_enabled',
            'complyflow_consent_pdpl_enabled',
            'complyflow_consent_australia_enabled',
        ];

        foreach ($compliance_options as $option) {
            add_action("update_option_{$option}", [$this, 'on_compliance_mode_changed'], 10, 3);
        }
    }

    /**
     * Handle compliance mode changes
     *
     * @param mixed  $old_value Old option value.
     * @param mixed  $new_value New option value.
     * @param string $option    Option name.
     * @return void
     */
    public function on_compliance_mode_changed($old_value, $new_value, string $option): void {
        // Only trigger if value actually changed
        if ($old_value !== $new_value) {
            // Set transient flag for admin notice
            set_transient('complyflow_documents_need_regeneration', true, DAY_IN_SECONDS);

            // Extract framework name from option
            $framework = str_replace(['complyflow_consent_', '_enabled'], '', $option);

            // Log the change
            do_action('complyflow_compliance_mode_changed', $framework, $new_value, $old_value);
        }
    }

    /**
     * Show admin notice when documents need regeneration
     *
     * @return void
     */
    public function show_regeneration_notice(): void {
        // Only show on relevant admin pages
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, ['toplevel_page_complyflow', 'complyflow_page_complyflow-documents'], true)) {
            return;
        }

        // Check if regeneration is needed
        if (!get_transient('complyflow_documents_need_regeneration')) {
            return;
        }

        ?>
        <div class="notice notice-warning is-dismissible" id="complyflow-regen-notice">
            <p>
                <strong><?php esc_html_e('ComplyFlow:', 'complyflow'); ?></strong>
                <?php esc_html_e('Your compliance mode settings have changed. We recommend regenerating your legal documents to reflect these changes.', 'complyflow'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-documents')); ?>" class="button button-primary" style="margin-left: 10px;">
                    <?php esc_html_e('Regenerate Documents', 'complyflow'); ?>
                </a>
            </p>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('#complyflow-regen-notice').on('click', '.notice-dismiss', function() {
                $.post(ajaxurl, {
                    action: 'complyflow_dismiss_regen_notice',
                    nonce: '<?php echo esc_js(wp_create_nonce('complyflow_dismiss_regen')); ?>'
                });
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX handler: Dismiss regeneration notice
     *
     * @return void
     */
    public function ajax_dismiss_regeneration_notice(): void {
        check_ajax_referer('complyflow_dismiss_regen', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        delete_transient('complyflow_documents_need_regeneration');
        wp_send_json_success();
    }
    /**
     * AJAX handler: Get a specific version of a policy
     */
    public function ajax_get_version(): void {
        check_ajax_referer('complyflow_version_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }
        $type = sanitize_text_field($_POST['type'] ?? '');
        $index = intval($_POST['index'] ?? 0);
        require_once(ABSPATH . 'wp-content/plugins/complyflow/VersionManager.php');
        $version = \ComplyFlow\VersionManager::get_version($type, $index);
        if ($version) {
            wp_send_json_success(['content' => $version['content']]);
        } else {
            wp_send_json_error(['message' => __('Version not found', 'complyflow')]);
        }
    }

    /**
     * AJAX handler: Get diff between two versions
     */
    public function ajax_diff_versions(): void {
        check_ajax_referer('complyflow_version_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }
        $type = sanitize_text_field($_POST['type'] ?? '');
        $indexA = intval($_POST['indexa'] ?? 0);
        $indexB = intval($_POST['indexb'] ?? 0);
        require_once(ABSPATH . 'wp-content/plugins/complyflow/VersionManager.php');
        $diff = \ComplyFlow\VersionManager::get_diff($type, $indexA, $indexB);
        wp_send_json_success(['diff' => $diff]);
    }

    /**
     * AJAX handler: Rollback to a specific version
     */
    public function ajax_rollback_version(): void {
        check_ajax_referer('complyflow_version_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }
        $type = sanitize_text_field($_POST['type'] ?? '');
        $index = intval($_POST['index'] ?? 0);
        require_once(ABSPATH . 'wp-content/plugins/complyflow/VersionManager.php');
        $result = \ComplyFlow\VersionManager::rollback($type, $index);
        if ($result) {
            wp_send_json_success(['message' => __('Version restored successfully', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Failed to restore version', 'complyflow')]);
        }
    }

    /**
     * Add admin menu item
     *
     * @return void
     */
    public function add_admin_menu(): void {
        add_submenu_page(
            'complyflow',
            __('Legal Documents', 'complyflow'),
            __('Legal Documents', 'complyflow'),
            'manage_options',
            'complyflow-documents',
            [$this, 'render_admin_page']
        );

        // Add questionnaire submenu
        add_submenu_page(
            'complyflow-documents',
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
        register_setting('complyflow_documents', 'complyflow_questionnaire_answers');
        register_setting('complyflow_documents', 'complyflow_generated_privacy_policy');
        register_setting('complyflow_documents', 'complyflow_generated_terms_of_service');
        register_setting('complyflow_documents', 'complyflow_generated_cookie_policy');
        register_setting('complyflow_documents', 'complyflow_generated_data_protection');
        register_setting('complyflow_documents', 'complyflow_generated_consent_management');
        register_setting('complyflow_documents', 'complyflow_generated_user_rights_notice');
        register_setting('complyflow_documents', 'complyflow_generated_third_party_services');
        register_setting('complyflow_documents', 'complyflow_generated_cookie_categories');
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

        $questionnaire = $this->questionnaire;
        $completion = $questionnaire->get_completion_percentage();
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

        $questionnaire = $this->questionnaire;
        $questions = $questionnaire->get_questions();
        $answers = $questionnaire->get_saved_answers();
        $completion = $questionnaire->get_completion_percentage();
        
        include COMPLYFLOW_PATH . 'includes/Admin/views/legal-questionnaire.php';
    }

    /**
     * AJAX handler for saving questionnaire
     *
     * @return void
     */
    public function ajax_save_questionnaire(): void {
        // Check nonce
        if (!isset($_POST['questionnaire_nonce']) || !wp_verify_nonce($_POST['questionnaire_nonce'], 'complyflow_questionnaire')) {
            error_log('ComplyFlow Questionnaire: Nonce verification failed');
            wp_send_json_error(['message' => __('Security check failed', 'complyflow')]);
            return;
        }

        if (!current_user_can('manage_options')) {
            error_log('ComplyFlow Questionnaire: Permission denied');
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        // Get answers from POST data
        $answers = isset($_POST['answers']) ? $_POST['answers'] : [];
        
        // Log for debugging
        error_log('ComplyFlow Questionnaire: Received answers - ' . print_r($answers, true));

        // Validate we have some data
        if (empty($answers)) {
            error_log('ComplyFlow Questionnaire: No answers provided');
            wp_send_json_error(['message' => __('No questionnaire data received. Please fill out the form.', 'complyflow')]);
            return;
        }

        // Try to save
        $save_result = $this->questionnaire->save_answers($answers);
        
        error_log('ComplyFlow Questionnaire: Save result - ' . ($save_result ? 'success' : 'failed'));

        if ($save_result) {
            // Get the redirect URL - use correct menu slug
            $redirect_url = admin_url('admin.php?page=complyflow-documents&questionnaire_saved=1');
            
            wp_send_json_success([
                'message' => __('Questionnaire saved successfully! Redirecting to Legal Documents...', 'complyflow'),
                'completion' => $this->questionnaire->get_completion_percentage(),
                'redirect' => $redirect_url,
            ]);
        } else {
            error_log('ComplyFlow Questionnaire: update_option failed');
            wp_send_json_error(['message' => __('Failed to save questionnaire. Please check server permissions.', 'complyflow')]);
        }
    }

    /**
     * AJAX handler for generating policy
     *
     * @return void
     */
    public function ajax_generate_policy(): void {
        check_ajax_referer('complyflow_generate_policy_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $policy_type = sanitize_text_field($_POST['policy_type'] ?? '');

        if (!in_array($policy_type, ['privacy_policy', 'terms_of_service', 'cookie_policy', 'data_protection', 'consent_management', 'user_rights_notice', 'third_party_services', 'cookie_categories'])) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        // Check if questionnaire is complete
        if (!$this->questionnaire->is_complete()) {
            wp_send_json_error(['message' => __('Please complete the questionnaire first', 'complyflow')]);
            return;
        }

        // Generate policy (will implement generators next)
        $content = $this->generate_policy($policy_type);

        if ($content) {
            $option_name = 'complyflow_generated_' . $policy_type;
            
            // Get previous content for version history
            $previous_content = get_option($option_name, '');
            
            // Save generated policy
            update_option($option_name, $content);
            
            // Update timestamp - use separate timestamp option without "generated_" prefix
            $timestamp_key = 'complyflow_' . $policy_type . '_updated';
            update_option($timestamp_key, time());
            update_option($option_name . '_timestamp', current_time('mysql'));
            
            // Save version history if content changed
            if (!empty($previous_content) && $previous_content !== $content) {
                $version_history = get_option($option_name . '_version_history', []);
                $version_history[] = [
                    'content' => $previous_content,
                    'timestamp' => current_time('mysql'),
                    'user_id' => get_current_user_id(),
                    'type' => 'regeneration',
                ];
                update_option($option_name . '_version_history', $version_history);
            }

            wp_send_json_success([
                'message' => __('Policy generated successfully', 'complyflow'),
                'content' => $content,
            ]);
        } else {
            wp_send_json_error(['message' => __('Failed to generate policy', 'complyflow')]);
        }
    }

    /**
     * Generate policy content
     *
     * @param string $policy_type Policy type.
     * @return string Generated policy content.
     */
    private function generate_policy(string $policy_type): string {
        $answers = $this->questionnaire->get_saved_answers();

        if (empty($answers)) {
            return '';
        }

        try {
            switch ($policy_type) {
                case 'privacy_policy':
                    $generator = new PrivacyPolicyGenerator($answers);
                    return $generator->generate();

                case 'terms_of_service':
                    $generator = new TermsOfServiceGenerator($answers);
                    return $generator->generate();

                case 'cookie_policy':
                    $generator = new CookiePolicyGenerator($answers);
                    return $generator->generate();

                case 'data_protection':
                    $generator = new DataProtectionPolicyGenerator($answers);
                    return $generator->generate();

                case 'consent_management':
                    $generator = new ConsentManagementPolicyGenerator($answers);
                    return $generator->generate();

                case 'user_rights_notice':
                    $generator = new UserRightsNoticeGenerator($answers);
                    return $generator->generate();

                case 'third_party_services':
                    $generator = new ThirdPartyServicesDisclosureGenerator($answers);
                    return $generator->generate();

                case 'cookie_categories':
                    $generator = new CookieCategoriesReferenceGenerator($answers);
                    return $generator->generate();

                default:
                    return '';
            }
        } catch (\Exception $e) {
            error_log('ComplyFlow: Error generating policy - ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Render policy shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string Policy content.
     */
    public function render_policy_shortcode(array $atts): string {
        $atts = shortcode_atts([
            'type' => 'privacy_policy',
        ], $atts);

        $policy_type = sanitize_text_field($atts['type']);
        $content = get_option('complyflow_generated_' . $policy_type, '');

        if (empty($content)) {
            return '<p>' . __('Policy not yet generated. Please visit the Legal Documents admin page.', 'complyflow') . '</p>';
        }

        return '<div class="complyflow-policy">' . wp_kses_post($content) . '</div>';
    }

    /**
     * Get questionnaire instance
     *
     * @return Questionnaire
     */
    public function get_questionnaire(): Questionnaire {
        return $this->questionnaire;
    }

    /**
     * AJAX handler: Get policy content
     *
     * @return void
     */
    public function ajax_get_policy(): void {
        check_ajax_referer('complyflow_generate_policy_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        // Support both 'type' and 'policy_type' parameter names
        $policy_type = sanitize_text_field($_POST['policy_type'] ?? $_POST['type'] ?? '');
        
        if (empty($policy_type)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        // Standardize policy type names
        $policy_type_map = [
            'privacy' => 'privacy_policy',
            'terms' => 'terms_of_service',
        ];
        $policy_type = $policy_type_map[$policy_type] ?? $policy_type;

        // Map policy types to option names
        $option_map = [
            'privacy_policy' => 'complyflow_generated_privacy_policy',
            'terms_of_service' => 'complyflow_generated_terms_of_service',
            'cookie_policy' => 'complyflow_generated_cookie_policy',
            'data_protection' => 'complyflow_generated_data_protection',
            'consent_management' => 'complyflow_generated_consent_management',
            'user_rights_notice' => 'complyflow_generated_user_rights_notice',
            'third_party_services' => 'complyflow_generated_third_party_services',
            'cookie_categories' => 'complyflow_generated_cookie_categories',
        ];

        $option_name = $option_map[$policy_type] ?? '';
        
        if (empty($option_name)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        $content = get_option($option_name, '');

        if (empty($content)) {
            wp_send_json_error(['message' => __('Policy not found', 'complyflow')]);
            return;
        }

        wp_send_json_success(['content' => $content]);
    }

    /**
     * AJAX handler: Save policy content
     *
     * @return void
     */
    public function ajax_save_policy(): void {
        check_ajax_referer('complyflow_generate_policy_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        // Support both 'type' and 'policy_type' parameter names
        $policy_type = sanitize_text_field($_POST['policy_type'] ?? $_POST['type'] ?? '');
        $content = wp_kses_post($_POST['content'] ?? '');

        if (empty($policy_type) || empty($content)) {
            wp_send_json_error(['message' => __('Invalid data', 'complyflow')]);
            return;
        }

        // Standardize policy type names
        $policy_type_map = [
            'privacy' => 'privacy_policy',
            'terms' => 'terms_of_service',
        ];
        $policy_type = $policy_type_map[$policy_type] ?? $policy_type;

        // Map policy types to option names
        $option_map = [
            'privacy_policy' => 'complyflow_generated_privacy_policy',
            'terms_of_service' => 'complyflow_generated_terms_of_service',
            'cookie_policy' => 'complyflow_generated_cookie_policy',
            'data_protection' => 'complyflow_generated_data_protection',
            'consent_management' => 'complyflow_generated_consent_management',
            'user_rights_notice' => 'complyflow_generated_user_rights_notice',
            'third_party_services' => 'complyflow_generated_third_party_services',
            'cookie_categories' => 'complyflow_generated_cookie_categories',
        ];

        $option_name = $option_map[$policy_type] ?? '';
        
        if (empty($option_name)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        // Get previous content for version history
        $previous_content = get_option($option_name, '');
        
        // Save to main option and edited version
        $result = update_option($option_name, $content);
        update_option($option_name . '_edited', $content);
        
        // Update timestamp - use separate timestamp option without "generated_" prefix
        $timestamp_key = 'complyflow_' . $policy_type . '_updated';
        update_option($timestamp_key, time());
        update_option($option_name . '_timestamp', current_time('mysql'));
        update_option($option_name . '_edited_timestamp', current_time('mysql'));
        
        // Mark as manually edited
        update_option($option_name . '_manual_edit', true);
        
        // Save version history if content changed
        if (!empty($previous_content) && $previous_content !== $content) {
            $version_history = get_option($option_name . '_version_history', []);
            $version_history[] = [
                'content' => $previous_content,
                'timestamp' => current_time('mysql'),
                'user_id' => get_current_user_id(),
                'type' => 'manual_edit',
            ];
            update_option($option_name . '_version_history', $version_history);
        }

        if ($result !== false) {
            wp_send_json_success(['message' => __('Policy saved successfully', 'complyflow')]);
        } else {
            wp_send_json_error(['message' => __('Failed to save policy', 'complyflow')]);
        }
    }

    /**
     * Get version history
     *
     * @return void
     */
    public function ajax_get_version_history(): void {
        check_ajax_referer('complyflow_generate_policy_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $policy_type = $_POST['policy_type'] ?? '';
        
        if (empty($policy_type)) {
            wp_send_json_error(['message' => __('Policy type is required', 'complyflow')]);
            return;
        }

        $option_map = [
            'privacy_policy' => 'complyflow_generated_privacy_policy',
            'terms_of_service' => 'complyflow_generated_terms_of_service',
            'cookie_policy' => 'complyflow_generated_cookie_policy',
            'data_protection' => 'complyflow_generated_data_protection',
            'consent_management' => 'complyflow_generated_consent_management',
            'user_rights_notice' => 'complyflow_generated_user_rights_notice',
            'third_party_services' => 'complyflow_generated_third_party_services',
            'cookie_categories' => 'complyflow_generated_cookie_categories',
        ];

        $option_name = $option_map[$policy_type] ?? '';
        
        if (empty($option_name)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        // Get version history from option
        $version_history = get_option($option_name . '_version_history', []);
        
        // Format versions for display
        $versions = [];
        $current_content = get_option($option_name . '_edited', get_option($option_name, ''));
        
        // Add current version first
        $versions[] = [
            'version' => count($version_history) + 1,
            'timestamp' => get_option($option_name . '_edited_timestamp', current_time('mysql')),
            'size' => $this->format_bytes(strlen($current_content)),
            'user' => get_userdata(get_current_user_id())->display_name,
            'is_current' => true,
            'changes_summary' => '',
        ];
        
        // Add historical versions
        foreach (array_reverse($version_history) as $index => $history_item) {
            $versions[] = [
                'version' => count($version_history) - $index,
                'timestamp' => $history_item['timestamp'] ?? '',
                'size' => $this->format_bytes(strlen($history_item['content'] ?? '')),
                'user' => $history_item['user'] ?? 'Unknown',
                'is_current' => false,
                'changes_summary' => $history_item['changes_summary'] ?? '',
            ];
        }

        wp_send_json_success(['versions' => $versions]);
    }

    /**
     * Compare two versions
     *
     * @return void
     */
    public function ajax_compare_versions(): void {
        check_ajax_referer('complyflow_generate_policy_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $policy_type = $_POST['policy_type'] ?? '';
        $version1 = intval($_POST['version1'] ?? 0);
        $version2 = intval($_POST['version2'] ?? 0);
        
        if (empty($policy_type) || $version1 <= 0 || $version2 <= 0) {
            wp_send_json_error(['message' => __('Invalid parameters', 'complyflow')]);
            return;
        }

        $option_map = [
            'privacy_policy' => 'complyflow_generated_privacy_policy',
            'terms_of_service' => 'complyflow_generated_terms_of_service',
            'cookie_policy' => 'complyflow_generated_cookie_policy',
            'data_protection' => 'complyflow_generated_data_protection',
        ];

        $option_name = $option_map[$policy_type] ?? '';
        
        if (empty($option_name)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        // Get both versions
        $version_history = get_option($option_name . '_version_history', []);
        $total_versions = count($version_history) + 1;
        
        $content1 = '';
        $content2 = '';
        
        // Get first version
        if ($version1 == $total_versions) {
            $content1 = get_option($option_name . '_edited', get_option($option_name, ''));
        } else {
            $history_index = count($version_history) - $version1;
            $content1 = $version_history[$history_index]['content'] ?? '';
        }
        
        // Get second version
        if ($version2 == $total_versions) {
            $content2 = get_option($option_name . '_edited', get_option($option_name, ''));
        } else {
            $history_index = count($version_history) - $version2;
            $content2 = $version_history[$history_index]['content'] ?? '';
        }

        if (empty($content1) || empty($content2)) {
            wp_send_json_error(['message' => __('One or both versions not found', 'complyflow')]);
            return;
        }

        // Generate simple line-by-line diff
        $diff = $this->generate_diff($content1, $content2);

        wp_send_json_success(['diff' => $diff]);
    }

    /**
     * Restore a previous version
     *
     * @return void
     */
    public function ajax_restore_version(): void {
        check_ajax_referer('complyflow_generate_policy_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $policy_type = $_POST['policy_type'] ?? '';
        $version = intval($_POST['version'] ?? 0);
        
        if (empty($policy_type) || $version <= 0) {
            wp_send_json_error(['message' => __('Invalid parameters', 'complyflow')]);
            return;
        }

        $option_map = [
            'privacy_policy' => 'complyflow_generated_privacy_policy',
            'terms_of_service' => 'complyflow_generated_terms_of_service',
            'cookie_policy' => 'complyflow_generated_cookie_policy',
            'data_protection' => 'complyflow_generated_data_protection',
        ];

        $option_name = $option_map[$policy_type] ?? '';
        
        if (empty($option_name)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        $version_history = get_option($option_name . '_version_history', []);
        $history_index = count($version_history) - $version;
        
        if (!isset($version_history[$history_index])) {
            wp_send_json_error(['message' => __('Version not found', 'complyflow')]);
            return;
        }

        $restored_content = $version_history[$history_index]['content'] ?? '';
        
        if (empty($restored_content)) {
            wp_send_json_error(['message' => __('Version content is empty', 'complyflow')]);
            return;
        }

        // Save current version to history before restoring
        $current_content = get_option($option_name . '_edited', get_option($option_name, ''));
        if (!empty($current_content)) {
            $version_history[] = [
                'content' => $current_content,
                'timestamp' => current_time('mysql'),
                'user' => get_userdata(get_current_user_id())->display_name,
                'changes_summary' => 'Auto-saved before restore',
            ];
            update_option($option_name . '_version_history', $version_history);
        }

        // Restore the version
        update_option($option_name . '_edited', $restored_content);
        update_option($option_name . '_edited_timestamp', current_time('mysql'));
        update_option($option_name . '_manual_edit', true);

        wp_send_json_success(['message' => __('Version restored successfully', 'complyflow')]);
    }

    /**
     * Export policy as PDF
     *
     * @return void
     */
    public function ajax_export_pdf(): void {
        check_ajax_referer('complyflow_generate_policy_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }

        $policy_type = $_POST['policy_type'] ?? '';
        
        if (empty($policy_type)) {
            wp_send_json_error(['message' => __('Policy type is required', 'complyflow')]);
            return;
        }

        $option_map = [
            'privacy_policy' => 'complyflow_generated_privacy_policy',
            'terms_of_service' => 'complyflow_generated_terms_of_service',
            'cookie_policy' => 'complyflow_generated_cookie_policy',
            'data_protection' => 'complyflow_generated_data_protection',
        ];

        $option_name = $option_map[$policy_type] ?? '';
        
        if (empty($option_name)) {
            wp_send_json_error(['message' => __('Invalid policy type', 'complyflow')]);
            return;
        }

        $content = get_option($option_name . '_edited', get_option($option_name, ''));
        
        if (empty($content)) {
            wp_send_json_error(['message' => __('Policy not found', 'complyflow')]);
            return;
        }

        // Check if TCPDF is available (we'll use a simple implementation)
        // For now, return the HTML content that will be converted on the client side
        // In production, you'd use a library like TCPDF, Dompdf, or mPDF
        
        wp_send_json_success([
            'content' => $content,
            'filename' => $policy_type . '.pdf',
        ]);
    }

    /**
     * Format bytes into human-readable format
     *
     * @param int $bytes Number of bytes.
     * @return string Formatted string.
     */
    private function format_bytes(int $bytes): string {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Generate simple HTML diff
     *
     * @param string $content1 First content.
     * @param string $content2 Second content.
     * @return string HTML diff.
     */
    private function generate_diff(string $content1, string $content2): string {
        // Strip HTML tags for comparison
        $text1 = strip_tags($content1);
        $text2 = strip_tags($content2);
        
        // Split into lines
        $lines1 = explode("\n", $text1);
        $lines2 = explode("\n", $text2);
        
        $diff_html = '<div class="complyflow-diff-content">';
        
        // Simple line-by-line comparison
        $max_lines = max(count($lines1), count($lines2));
        
        for ($i = 0; $i < $max_lines; $i++) {
            $line1 = isset($lines1[$i]) ? trim($lines1[$i]) : '';
            $line2 = isset($lines2[$i]) ? trim($lines2[$i]) : '';
            
            if ($line1 === $line2) {
                if (!empty($line1)) {
                    $diff_html .= '<div class="diff-line-equal" style="padding: 4px 8px; margin: 2px 0;">' . 
                                  '<span style="color: #666; margin-right: 10px; width: 40px; display: inline-block;">' . ($i + 1) . '</span>' .
                                  esc_html($line1) . '</div>';
                }
            } elseif (empty($line1)) {
                $diff_html .= '<div class="diff-line-added" style="padding: 4px 8px; margin: 2px 0; background: #d4edda;">' .
                              '<span style="color: #155724; margin-right: 10px; width: 40px; display: inline-block; font-weight: bold;">+</span>' .
                              esc_html($line2) . '</div>';
            } elseif (empty($line2)) {
                $diff_html .= '<div class="diff-line-removed" style="padding: 4px 8px; margin: 2px 0; background: #f8d7da;">' .
                              '<span style="color: #721c24; margin-right: 10px; width: 40px; display: inline-block; font-weight: bold;">-</span>' .
                              esc_html($line1) . '</div>';
            } else {
                // Modified line
                $diff_html .= '<div class="diff-line-removed" style="padding: 4px 8px; margin: 2px 0; background: #f8d7da;">' .
                              '<span style="color: #721c24; margin-right: 10px; width: 40px; display: inline-block; font-weight: bold;">-</span>' .
                              esc_html($line1) . '</div>';
                $diff_html .= '<div class="diff-line-added" style="padding: 4px 8px; margin: 2px 0; background: #d4edda;">' .
                              '<span style="color: #155724; margin-right: 10px; width: 40px; display: inline-block; font-weight: bold;">+</span>' .
                              esc_html($line2) . '</div>';
            }
        }
        
        $diff_html .= '</div>';
        
        return $diff_html;
    }
}
