<?php
/**
 * Policy Generator
 *
 * Generates legal documents based on questionnaire answers.
 *
 * @package ComplyFlow\Modules\Legal
 * @since   2.0.1
 */

namespace ComplyFlow\Modules\Legal;

use ComplyFlow\Core\Repositories\SettingsRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class PolicyGenerator
 */
class PolicyGenerator {
    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Template manager
     *
     * @var TemplateManager
     */
    private TemplateManager $templates;

    /**
     * Questionnaire answers
     *
     * @var array
     */
    private array $answers;

    /**
     * Constructor
     *
     * @param SettingsRepository $settings Settings repository.
     * @param TemplateManager    $templates Template manager.
     */
    public function __construct(SettingsRepository $settings, TemplateManager $templates) {
        $this->settings = $settings;
        $this->templates = $templates;
        $this->answers = $this->settings->get('legal_questionnaire_answers', []);
    }

    /**
     * Generate privacy policy
     *
     * @return string Generated policy HTML.
     */
    public function generate_privacy_policy(): string {
        $template = $this->templates->get_template('privacy-policy');

        $tokens = [
            '{{BUSINESS_NAME}}' => $this->get_answer('business_name', get_bloginfo('name')),
            '{{CONTACT_EMAIL}}' => $this->get_answer('contact_email', get_bloginfo('admin_email')),
            '{{CONTACT_PHONE}}' => $this->get_answer('contact_phone', ''),
            '{{BUSINESS_ADDRESS}}' => $this->get_answer('business_address', ''),
            '{{EFFECTIVE_DATE}}' => date_i18n(get_option('date_format')),
            '{{DATA_COLLECTION_SECTION}}' => $this->render_data_collection_section(),
            '{{COOKIES_SECTION}}' => $this->render_cookies_section(),
            '{{USER_RIGHTS_SECTION}}' => $this->render_user_rights_section(),
            '{{DATA_RETENTION_SECTION}}' => $this->render_data_retention_section(),
            '{{INTERNATIONAL_TRANSFERS_SECTION}}' => $this->render_international_transfers_section(),
            '{{CHILDREN_SECTION}}' => $this->render_children_section(),
        ];

        return $this->replace_tokens($template, $tokens);
    }

    /**
     * Generate terms of service
     *
     * @return string Generated terms HTML.
     */
    public function generate_terms_of_service(): string {
        $template = $this->templates->get_template('terms-of-service');

        $tokens = [
            '{{BUSINESS_NAME}}' => $this->get_answer('business_name', get_bloginfo('name')),
            '{{WEBSITE_URL}}' => home_url(),
            '{{CONTACT_EMAIL}}' => $this->get_answer('contact_email', get_bloginfo('admin_email')),
            '{{EFFECTIVE_DATE}}' => date_i18n(get_option('date_format')),
            '{{ECOMMERCE_SECTION}}' => $this->render_ecommerce_section(),
            '{{USER_CONDUCT_SECTION}}' => $this->render_user_conduct_section(),
            '{{LIMITATION_LIABILITY_SECTION}}' => $this->render_limitation_liability_section(),
        ];

        return $this->replace_tokens($template, $tokens);
    }

    /**
     * Generate cookie policy
     *
     * @return string Generated policy HTML.
     */
    public function generate_cookie_policy(): string {
        $template = $this->templates->get_template('cookie-policy');

        // Get managed cookies from consent module
        $consent_module = \ComplyFlow\Core\ModuleManager::get_instance()->get_module('consent');
        $managed_cookies = [];
        
        if ($consent_module) {
            $scanner = $consent_module->get_scanner();
            $managed_cookies = $scanner->get_managed_cookies();
        }

        $tokens = [
            '{{BUSINESS_NAME}}' => $this->get_answer('business_name', get_bloginfo('name')),
            '{{EFFECTIVE_DATE}}' => date_i18n(get_option('date_format')),
            '{{COOKIE_TABLE}}' => $this->render_cookie_table($managed_cookies),
            '{{COOKIE_MANAGEMENT_SECTION}}' => $this->render_cookie_management_section(),
        ];

        return $this->replace_tokens($template, $tokens);
    }

    /**
     * Generate data protection policy (GDPR/CCPA)
     *
     * @return string Generated policy HTML.
     */
    public function generate_data_protection_policy(): string {
        $template = $this->templates->get_template('data-protection-policy');

        $tokens = [
            '{{BUSINESS_NAME}}' => $this->get_answer('business_name', get_bloginfo('name')),
            '{{CONTACT_EMAIL}}' => $this->get_answer('contact_email', get_bloginfo('admin_email')),
            '{{EFFECTIVE_DATE}}' => date_i18n(get_option('date_format')),
            '{{GDPR_SECTION}}' => $this->render_gdpr_section(),
            '{{UK_GDPR_SECTION}}' => $this->render_uk_gdpr_section(),
            '{{CCPA_SECTION}}' => $this->render_ccpa_section(),
            '{{LGPD_SECTION}}' => $this->render_lgpd_section(),
            '{{PIPEDA_SECTION}}' => $this->render_pipeda_section(),
            '{{PDPA_SG_SECTION}}' => $this->render_pdpa_singapore_section(),
            '{{PDPA_TH_SECTION}}' => $this->render_pdpa_thailand_section(),
            '{{APPI_SECTION}}' => $this->render_appi_section(),
            '{{POPIA_SECTION}}' => $this->render_popia_section(),
            '{{KVKK_SECTION}}' => $this->render_kvkk_section(),
            '{{PDPL_SECTION}}' => $this->render_pdpl_section(),
            '{{DATA_SUBJECT_RIGHTS}}' => $this->render_data_subject_rights(),
        ];

        return $this->replace_tokens($template, $tokens);
    }

    /**
     * Render data collection section
     *
     * @return string Section HTML.
     */
    private function render_data_collection_section(): string {
        $sections = [];

        // Basic information
        $sections[] = $this->templates->get_snippet('data-collection-basic');

        // Ecommerce
        if ($this->get_answer('has_ecommerce')) {
            $sections[] = $this->templates->get_snippet('data-collection-ecommerce');
        }

        // Analytics
        if ($this->get_answer('has_analytics')) {
            $sections[] = $this->templates->get_snippet('data-collection-analytics');
        }

        // Marketing
        if ($this->get_answer('has_marketing')) {
            $sections[] = $this->templates->get_snippet('data-collection-marketing');
        }

        // Social media
        if ($this->get_answer('has_social_media')) {
            $sections[] = $this->templates->get_snippet('data-collection-social');
        }

        return implode("\n\n", $sections);
    }

    /**
     * Render cookies section
     *
     * @return string Section HTML.
     */
    private function render_cookies_section(): string {
        return $this->templates->get_snippet('cookies-overview');
    }

    /**
     * Render user rights section
     *
     * @return string Section HTML.
     */
    private function render_user_rights_section(): string {
        $target_regions = $this->get_answer('target_regions', []);
        $sections = [];

        // General rights
        $sections[] = $this->templates->get_snippet('user-rights-general');

        // GDPR rights (EU)
        if (in_array('eu', $target_regions, true) || in_array('gdpr', $target_regions, true)) {
            $sections[] = $this->templates->get_snippet('user-rights-gdpr');
        }

        // CCPA rights (California)
        if (in_array('us', $target_regions, true) || in_array('ccpa', $target_regions, true)) {
            $sections[] = $this->templates->get_snippet('user-rights-ccpa');
        }

        // LGPD rights (Brazil)
        if (in_array('br', $target_regions, true) || in_array('lgpd', $target_regions, true)) {
            $sections[] = $this->templates->get_snippet('user-rights-lgpd');
        }

        return implode("\n\n", $sections);
    }

    /**
     * Render data retention section
     *
     * @return string Section HTML.
     */
    private function render_data_retention_section(): string {
        $retention = $this->get_answer('data_retention_period', '2 years');
        $snippet = $this->templates->get_snippet('data-retention');
        
        return str_replace('{{RETENTION_PERIOD}}', $retention, $snippet);
    }

    /**
     * Render international transfers section
     *
     * @return string Section HTML.
     */
    private function render_international_transfers_section(): string {
        if (!$this->get_answer('international_transfers')) {
            return '';
        }

        return $this->templates->get_snippet('international-transfers');
    }

    /**
     * Render children section
     *
     * @return string Section HTML.
     */
    private function render_children_section(): string {
        if (!$this->get_answer('collects_children_data')) {
            return $this->templates->get_snippet('children-no-collection');
        }

        return $this->templates->get_snippet('children-coppa');
    }

    /**
     * Render ecommerce section
     *
     * @return string Section HTML.
     */
    private function render_ecommerce_section(): string {
        if (!$this->get_answer('has_ecommerce')) {
            return '';
        }

        return $this->templates->get_snippet('terms-ecommerce');
    }

    /**
     * Render user conduct section
     *
     * @return string Section HTML.
     */
    private function render_user_conduct_section(): string {
        return $this->templates->get_snippet('terms-user-conduct');
    }

    /**
     * Render limitation of liability section
     *
     * @return string Section HTML.
     */
    private function render_limitation_liability_section(): string {
        return $this->templates->get_snippet('terms-liability');
    }

    /**
     * Render cookie table
     *
     * @param array $managed_cookies Managed cookies by category.
     * @return string Cookie table HTML.
     */
    private function render_cookie_table(array $managed_cookies): string {
        if (empty($managed_cookies)) {
            return '<p>' . __('No cookies have been detected yet.', 'complyflow') . '</p>';
        }

        $html = '<div class="cookie-tables">';

        foreach ($managed_cookies as $category => $cookies) {
            if (empty($cookies)) {
                continue;
            }

            $html .= '<h3>' . ucfirst($category) . ' ' . __('Cookies', 'complyflow') . '</h3>';
            $html .= '<table class="cookie-table">';
            $html .= '<thead><tr>';
            $html .= '<th>' . __('Cookie Name', 'complyflow') . '</th>';
            $html .= '<th>' . __('Purpose', 'complyflow') . '</th>';
            $html .= '<th>' . __('Expiry', 'complyflow') . '</th>';
            $html .= '</tr></thead><tbody>';

            foreach ($cookies as $cookie) {
                $html .= '<tr>';
                $html .= '<td>' . esc_html($cookie['name']) . '</td>';
                $html .= '<td>' . esc_html($cookie['description'] ?? __('No description available', 'complyflow')) . '</td>';
                $html .= '<td>' . esc_html($cookie['expiry'] ?? 'Session') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render cookie management section
     *
     * @return string Section HTML.
     */
    private function render_cookie_management_section(): string {
        return $this->templates->get_snippet('cookie-management');
    }

    /**
     * Render GDPR section
     *
     * @return string Section HTML.
     */
    private function render_gdpr_section(): string {
        return $this->templates->get_snippet('gdpr-compliance');
    }

    /**
     * Render UK GDPR section
     *
     * @return string Section HTML.
     */
    private function render_uk_gdpr_section(): string {
        return $this->templates->get_snippet('uk-gdpr-compliance');
    }

    /**
     * Render CCPA section
     *
     * @return string Section HTML.
     */
    private function render_ccpa_section(): string {
        return $this->templates->get_snippet('ccpa-compliance');
    }

    /**
     * Render LGPD section
     *
     * @return string Section HTML.
     */
    private function render_lgpd_section(): string {
        return $this->templates->get_snippet('lgpd-compliance');
    }

    /**
     * Render PIPEDA section
     *
     * @return string Section HTML.
     */
    private function render_pipeda_section(): string {
        return $this->templates->get_snippet('pipeda-compliance');
    }

    /**
     * Render PDPA Singapore section
     *
     * @return string Section HTML.
     */
    private function render_pdpa_singapore_section(): string {
        return $this->templates->get_snippet('pdpa-singapore-compliance');
    }

    /**
     * Render PDPA Thailand section
     *
     * @return string Section HTML.
     */
    private function render_pdpa_thailand_section(): string {
        return $this->templates->get_snippet('pdpa-thailand-compliance');
    }

    /**
     * Render APPI Japan section
     *
     * @return string Section HTML.
     */
    private function render_appi_section(): string {
        return $this->templates->get_snippet('appi-japan-compliance');
    }

    /**
     * Render POPIA South Africa section
     *
     * @return string Section HTML.
     */
    private function render_popia_section(): string {
        return $this->templates->get_snippet('popia-southafrica-compliance');
    }

    /**
     * Render KVKK Turkey section
     *
     * @return string Section HTML.
     */
    private function render_kvkk_section(): string {
        return $this->templates->get_snippet('kvkk-turkey-compliance');
    }

    /**
     * Render PDPL Saudi Arabia section
     *
     * @return string Section HTML.
     */
    private function render_pdpl_section(): string {
        return $this->templates->get_snippet('pdpl-saudi-compliance');
    }

    /**
     * Render data subject rights
     *
     * @return string Section HTML.
     */
    private function render_data_subject_rights(): string {
        return $this->templates->get_snippet('data-subject-rights');
    }

    /**
     * Replace tokens in template
     *
     * @param string $template Template content.
     * @param array  $tokens   Token replacements.
     * @return string Processed content.
     */
    private function replace_tokens(string $template, array $tokens): string {
        return str_replace(array_keys($tokens), array_values($tokens), $template);
    }

    /**
     * Get questionnaire answer
     *
     * @param string $key     Answer key.
     * @param mixed  $default Default value.
     * @return mixed Answer value.
     */
    private function get_answer(string $key, $default = '') {
        return $this->answers[$key] ?? $default;
    }
}
