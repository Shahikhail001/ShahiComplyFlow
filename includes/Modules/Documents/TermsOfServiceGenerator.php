<?php
/**
 * Terms of Service Generator
 *
 * Generates customized Terms of Service based on questionnaire answers.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   2.0.1
 */

namespace ComplyFlow\Modules\Documents;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class TermsOfServiceGenerator
 */
class TermsOfServiceGenerator {
    /**
     * Questionnaire answers
     *
     * @var array
     */
    private array $answers;

    /**
     * Constructor
     *
     * @param array $answers Questionnaire answers.
     */
    public function __construct(array $answers) {
        $this->answers = $answers;
    }

    /**
     * Generate Terms of Service
     *
     * @return string Generated Terms of Service HTML.
     */
    public function generate(): string {
        $tokens = $this->build_tokens();
        $template = $this->load_template();

        // Replace tokens in template
        foreach ($tokens as $token => $value) {
            $template = str_replace('{{' . $token . '}}', $value, $template);
        }

        return $template;
    }

    /**
     * Build token replacement map
     *
     * @return array Token map.
     */
    private function build_tokens(): array {
        return [
            // Metadata tokens
            'COMPANY_NAME' => $this->get_company_name(),
            'CONTACT_EMAIL' => $this->get_contact_email(),
            'PHYSICAL_ADDRESS' => $this->get_physical_address(),
            'PHONE_NUMBER' => $this->get_phone_number(),
            'WEBSITE_URL' => get_site_url(),
            'WEBSITE_NAME' => get_bloginfo('name'),
            'EFFECTIVE_DATE' => date_i18n('F j, Y'),
            'LAST_UPDATED' => date_i18n('F j, Y'),

            // Section tokens
            'INTRODUCTION_SECTION' => $this->render_introduction(),
            'ACCEPTANCE_SECTION' => $this->render_acceptance(),
            'ELIGIBILITY_SECTION' => $this->render_eligibility(),
            'ACCOUNT_SECTION' => $this->render_account_terms(),
            'ECOMMERCE_SECTION' => $this->render_ecommerce_terms(),
            'INTELLECTUAL_PROPERTY_SECTION' => $this->render_intellectual_property(),
            'USER_CONTENT_SECTION' => $this->render_user_content(),
            'PROHIBITED_CONDUCT_SECTION' => $this->render_prohibited_conduct(),
            'DISCLAIMERS_SECTION' => $this->render_disclaimers(),
            'LIABILITY_SECTION' => $this->render_liability_limitations(),
            'INDEMNIFICATION_SECTION' => $this->render_indemnification(),
            'TERMINATION_SECTION' => $this->render_termination(),
            'GOVERNING_LAW_SECTION' => $this->render_governing_law(),
            'DISPUTE_RESOLUTION_SECTION' => $this->render_dispute_resolution(),
            'CHANGES_SECTION' => $this->render_changes(),
            'CONTACT_SECTION' => $this->render_contact(),
        ];
    }

    /**
     * Get enabled compliance modes from Consent Manager
     *
     * @return array Enabled compliance modes.
     */
    private function get_enabled_compliance_modes(): array {
        return [
            'EU' => get_option('complyflow_consent_gdpr_enabled', false),
            'UK' => get_option('complyflow_consent_uk_gdpr_enabled', false),
            'US' => get_option('complyflow_consent_ccpa_enabled', false),
            'BR' => get_option('complyflow_consent_lgpd_enabled', false),
            'CA' => get_option('complyflow_consent_pipeda_enabled', false),
            'SG' => get_option('complyflow_consent_pdpa_sg_enabled', false),
            'TH' => get_option('complyflow_consent_pdpa_th_enabled', false),
            'JP' => get_option('complyflow_consent_appi_enabled', false),
            'ZA' => get_option('complyflow_consent_popia_enabled', false),
            'TR' => get_option('complyflow_consent_kvkk_enabled', false),
            'SA' => get_option('complyflow_consent_pdpl_enabled', false),
            'AU' => get_option('complyflow_consent_australia_enabled', false),
        ];
    }

    /**
     * Render introduction section
     *
     * @return string Section content.
     */
    private function render_introduction(): string {
        return $this->load_snippet('introduction');
    }

    /**
     * Render acceptance section
     *
     * @return string Section content.
     */
    private function render_acceptance(): string {
        return $this->load_snippet('acceptance');
    }

    /**
     * Render eligibility section
     *
     * @return string Section content.
     */
    private function render_eligibility(): string {
        $minimum_age = $this->answers['minimum_age'] ?? 18;
        return $this->load_snippet('eligibility', ['minimum_age' => $minimum_age]);
    }

    /**
     * Render account terms section
     *
     * @return string Section content.
     */
    private function render_account_terms(): string {
        $has_accounts = $this->answers['has_user_accounts'] ?? false;

        if (!$has_accounts) {
            return '';
        }

        return $this->load_snippet('account-terms');
    }

    /**
     * Render ecommerce terms section
     *
     * @return string Section content.
     */
    private function render_ecommerce_terms(): string {
        $has_ecommerce = $this->answers['has_ecommerce'] ?? false;

        if (!$has_ecommerce) {
            return '';
        }

        $content = '';
        $content .= $this->load_snippet('ecommerce-general');

        // Add payment-specific terms if applicable
        if ($this->answers['collect_payment_info'] ?? false) {
            $content .= $this->load_snippet('ecommerce-payment');
        }

        // Add subscription terms if applicable
        if ($this->answers['has_subscriptions'] ?? false) {
            $content .= $this->load_snippet('ecommerce-subscriptions');
        }

        // Add shipping terms if applicable
        $content .= $this->load_snippet('ecommerce-shipping');
        $content .= $this->load_snippet('ecommerce-returns');

        return $content;
    }

    /**
     * Render intellectual property section
     *
     * @return string Section content.
     */
    private function render_intellectual_property(): string {
        return $this->load_snippet('intellectual-property');
    }

    /**
     * Render user content section
     *
     * @return string Section content.
     */
    private function render_user_content(): string {
        $has_accounts = $this->answers['has_user_accounts'] ?? false;

        if (!$has_accounts) {
            return '';
        }

        return $this->load_snippet('user-content');
    }

    /**
     * Render prohibited conduct section
     *
     * @return string Section content.
     */
    private function render_prohibited_conduct(): string {
        return $this->load_snippet('prohibited-conduct');
    }

    /**
     * Render disclaimers section
     *
     * @return string Section content.
     */
    private function render_disclaimers(): string {
        $content = $this->load_snippet('disclaimers-general');

        $has_ecommerce = $this->answers['has_ecommerce'] ?? false;
        if ($has_ecommerce) {
            $content .= $this->load_snippet('disclaimers-ecommerce');
        }

        return $content;
    }

    /**
     * Render liability limitations section
     *
     * @return string Section content.
     */
    private function render_liability_limitations(): string {
        return $this->load_snippet('liability-limitations');
    }

    /**
     * Render indemnification section
     *
     * @return string Section content.
     */
    private function render_indemnification(): string {
        return $this->load_snippet('indemnification');
    }

    /**
     * Render termination section
     *
     * @return string Section content.
     */
    private function render_termination(): string {
        return $this->load_snippet('termination');
    }

    /**
     * Render governing law section
     *
     * @return string Section content.
     */
    private function render_governing_law(): string {
        $target_countries = $this->answers['target_countries'] ?? [];
        $enabled_modes = $this->get_enabled_compliance_modes();

        $content = $this->load_snippet('governing-law-general');

        // Auto-detect from compliance modes or use questionnaire answers
        $show_eu = in_array('EU', $target_countries) || $enabled_modes['EU'] || $enabled_modes['UK'];
        $show_us = in_array('US', $target_countries) || $enabled_modes['US'];
        $show_au = in_array('AU', $target_countries) || $enabled_modes['AU'];

        // Add region-specific governing law clauses
        if ($show_eu) {
            $content .= $this->load_snippet('governing-law-eu');
        }

        if ($show_us) {
            $content .= $this->load_snippet('governing-law-us');
        }

        if ($show_au) {
            $content .= $this->load_snippet('governing-law-au');
        }

        return $content;
    }

    /**
     * Render dispute resolution section
     *
     * @return string Section content.
     */
    private function render_dispute_resolution(): string {
        $target_countries = $this->answers['target_countries'] ?? [];
        $enabled_modes = $this->get_enabled_compliance_modes();

        $content = $this->load_snippet('dispute-resolution-general');

        // Auto-detect from compliance modes or use questionnaire answers
        $show_us = in_array('US', $target_countries) || $enabled_modes['US'];
        $show_eu = in_array('EU', $target_countries) || $enabled_modes['EU'] || $enabled_modes['UK'];

        // Add arbitration clause for US
        if ($show_us) {
            $content .= $this->load_snippet('dispute-resolution-arbitration');
        }

        // Add EU-specific dispute resolution (ODR platform)
        if ($show_eu) {
            $content .= $this->load_snippet('dispute-resolution-eu');
        }

        return $content;
    }

    /**
     * Render changes section
     *
     * @return string Section content.
     */
    private function render_changes(): string {
        return $this->load_snippet('changes');
    }

    /**
     * Render contact section
     *
     * @return string Section content.
     */
    private function render_contact(): string {
        return $this->load_snippet('contact');
    }

    /**
     * Load template file
     *
     * @return string Template content.
     */
    private function load_template(): string {
        $template_path = COMPLYFLOW_PATH . 'templates/policies/terms-of-service-template.php';

        if (!file_exists($template_path)) {
            return '';
        }

        ob_start();
        include $template_path;
        return ob_get_clean();
    }

    /**
     * Load snippet file
     *
     * @param string $snippet Snippet name.
     * @param array  $vars    Variables to pass to snippet.
     * @return string Snippet content.
     */
    private function load_snippet(string $snippet, array $vars = []): string {
        $snippet_path = COMPLYFLOW_PATH . 'templates/policies/snippets/terms-' . $snippet . '.php';

        if (!file_exists($snippet_path)) {
            return '';
        }

        // Extract variables for use in snippet
        extract($vars);

        ob_start();
        include $snippet_path;
        return ob_get_clean();
    }

    /**
     * Get company name from answers
     *
     * @return string Company name.
     */
    private function get_company_name(): string {
        return $this->answers['company_name'] ?? get_bloginfo('name');
    }

    /**
     * Get contact email from answers
     *
     * @return string Contact email.
     */
    private function get_contact_email(): string {
        return $this->answers['contact_email'] ?? get_option('admin_email');
    }

    /**
     * Get physical address from answers
     *
     * @return string Physical address.
     */
    private function get_physical_address(): string {
        return $this->answers['physical_address'] ?? '';
    }

    /**
     * Get phone number from answers
     *
     * @return string Phone number.
     */
    private function get_phone_number(): string {
        return $this->answers['phone_number'] ?? '';
    }
}
