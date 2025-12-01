<?php
/**
 * Privacy Policy Generator
 *
 * Generates GDPR/CCPA/LGPD compliant privacy policies based on
 * questionnaire answers.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   1.0.0
 */

namespace ComplyFlow\Modules\Documents;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class PrivacyPolicyGenerator
 */
class PrivacyPolicyGenerator {
    /**
     * Questionnaire answers
     *
     * @var array
     */
    private array $answers;

    /**
     * Template path
     *
     * @var string
     */
    private string $template_path;

    /**
     * Constructor
     *
     * @param array $answers Questionnaire answers.
     */
    public function __construct(array $answers) {
        $this->answers = $answers;
        $this->template_path = COMPLYFLOW_PATH . 'templates/policies/';
    }

    /**
     * Generate privacy policy
     *
     * @return string Generated privacy policy HTML.
     */
    public function generate(): string {
        $tokens = $this->build_tokens();
        $template = $this->load_template('privacy-policy-template.php');

        // Replace tokens
        return $this->replace_tokens($template, $tokens);
    }

    /**
     * Build replacement tokens
     *
     * @return array Tokens and their replacements.
     */
    private function build_tokens(): array {
        $tokens = [
            '{{COMPANY_NAME}}' => $this->get_company_name(),
            '{{CONTACT_EMAIL}}' => $this->get_contact_email(),
            '{{PHYSICAL_ADDRESS}}' => $this->get_physical_address(),
            '{{PHONE_NUMBER}}' => $this->get_phone_number(),
            '{{EFFECTIVE_DATE}}' => current_time('F j, Y'),
            '{{LAST_UPDATED}}' => current_time('F j, Y'),
            '{{WEBSITE_URL}}' => get_site_url(),
            '{{WEBSITE_NAME}}' => get_bloginfo('name'),

            // Dynamic sections
            '{{INTRODUCTION_SECTION}}' => $this->render_introduction(),
            '{{DATA_COLLECTION_SECTION}}' => $this->render_data_collection(),
            '{{DATA_USAGE_SECTION}}' => $this->render_data_usage(),
            '{{COOKIES_SECTION}}' => $this->render_cookies(),
            '{{THIRD_PARTY_SECTION}}' => $this->render_third_party(),
            '{{DATA_STORAGE_SECTION}}' => $this->render_data_storage(),
            '{{USER_RIGHTS_SECTION}}' => $this->render_user_rights(),
            '{{DPO_SECTION}}' => $this->render_dpo_section(),
            '{{CHILDREN_SECTION}}' => $this->render_children(),
            '{{REGIONAL_COMPLIANCE_SECTION}}' => $this->render_regional_compliance(),
            '{{CHANGES_SECTION}}' => $this->render_changes(),
            '{{CONTACT_SECTION}}' => $this->render_contact(),
        ];

        return $tokens;
    }

    /**
     * Replace tokens in template
     *
     * @param string $template Template content.
     * @param array  $tokens   Replacement tokens.
     * @return string Processed content.
     */
    private function replace_tokens(string $template, array $tokens): string {
        $content = str_replace(
            array_keys($tokens),
            array_values($tokens),
            $template
        );
        
        // Remove any remaining unreplaced placeholders (safety cleanup)
        $content = preg_replace('/\{\{[A-Z_]+\}\}/', '', $content);
        
        return $content;
    }

    /**
     * Load template file
     *
     * @param string $filename Template filename.
     * @return string Template content.
     */
    private function load_template(string $filename): string {
        $filepath = $this->template_path . $filename;

        if (file_exists($filepath)) {
            ob_start();
            include $filepath;
            return ob_get_clean();
        }

        return '';
    }

    /**
     * Load snippet file
     *
     * @param string $filename Snippet filename.
     * @return string Snippet content.
     */
    private function load_snippet(string $filename): string {
        return $this->load_template('snippets/' . $filename . '.php');
    }

    /**
     * Get company name
     *
     * @return string Company name.
     */
    private function get_company_name(): string {
        return $this->answers['company_name'] ?? get_bloginfo('name');
    }

    /**
     * Get contact email
     *
     * @return string Contact email.
     */
    private function get_contact_email(): string {
        return $this->answers['contact_email'] ?? get_option('admin_email');
    }

    /**
     * Get physical address
     *
     * @return string Physical address or empty.
     */
    private function get_physical_address(): string {
        return $this->answers['physical_address'] ?? '';
    }

    /**
     * Get phone number
     *
     * @return string Phone number or empty.
     */
    private function get_phone_number(): string {
        return $this->answers['phone_number'] ?? '';
    }

    /**
     * Render introduction section
     *
     * @return string Introduction HTML.
     */
    private function render_introduction(): string {
        return $this->load_snippet('introduction');
    }

    /**
     * Render data collection section
     *
     * @return string Data collection HTML.
     */
    private function render_data_collection(): string {
        $sections = [];

        // Basic data collection
        $sections[] = $this->load_snippet('data-collection-basic');

        // Ecommerce data
        if ($this->answers['has_ecommerce'] ?? false) {
            $sections[] = $this->load_snippet('data-collection-ecommerce');

            if ($this->answers['collect_payment_info'] ?? false) {
                $sections[] = $this->load_snippet('data-collection-payment');
            }
        }

        // User accounts
        if ($this->answers['has_user_accounts'] ?? false) {
            $sections[] = $this->load_snippet('data-collection-accounts');
        }

        // Email collection
        if ($this->answers['collect_emails'] ?? false) {
            $sections[] = $this->load_snippet('data-collection-emails');
        }

        return implode("\n\n", $sections);
    }

    /**
     * Render data usage section
     *
     * @return string Data usage HTML.
     */
    private function render_data_usage(): string {
        $sections = [];

        $sections[] = $this->load_snippet('data-usage-general');

        if ($this->answers['has_ecommerce'] ?? false) {
            $sections[] = $this->load_snippet('data-usage-ecommerce');
        }

        if ($this->answers['has_email_marketing'] ?? false) {
            $sections[] = $this->load_snippet('data-usage-marketing');
        }

        return implode("\n\n", $sections);
    }

    /**
     * Render cookies section
     *
     * @return string Cookies HTML.
     */
    private function render_cookies(): string {
        $sections = [];

        $sections[] = $this->load_snippet('cookies-essential');

        if ($this->answers['has_analytics'] ?? false) {
            $sections[] = $this->load_snippet('cookies-analytics');
        }

        if ($this->answers['has_advertising'] ?? false) {
            $sections[] = $this->load_snippet('cookies-advertising');
        }

        return implode("\n\n", $sections);
    }

    /**
     * Render third party services section
     *
     * @return string Third party HTML.
     */
    private function render_third_party(): string {
        $sections = [];

        if ($this->answers['has_analytics'] ?? false) {
            $tools = $this->answers['analytics_tools'] ?? [];
            
            if (in_array('google_analytics', $tools)) {
                $sections[] = $this->load_snippet('third-party-google-analytics');
            }

            if (in_array('hotjar', $tools)) {
                $sections[] = $this->load_snippet('third-party-hotjar');
            }
        }

        if ($this->answers['has_email_marketing'] ?? false) {
            $provider = $this->answers['email_marketing_provider'] ?? '';
            
            if ($provider === 'mailchimp') {
                $sections[] = $this->load_snippet('third-party-mailchimp');
            } elseif ($provider === 'sendgrid') {
                $sections[] = $this->load_snippet('third-party-sendgrid');
            }
        }

        if ($this->answers['has_social_sharing'] ?? false) {
            $sections[] = $this->load_snippet('third-party-social-media');
        }

        if (empty($sections)) {
            $sections[] = $this->load_snippet('third-party-none');
        }

        return implode("\n\n", $sections);
    }

    /**
     * Render data storage section
     *
     * @return string Data storage HTML.
     */
    private function render_data_storage(): string {
        $sections = [];

        $sections[] = $this->load_snippet('data-storage-general');

        $retention = $this->answers['data_retention_period'] ?? 24;
        $sections[] = sprintf(
            '<p>We retain your personal data for <strong>%d months</strong> from your last interaction with our services, or as required by law.</p>',
            $retention
        );

        return implode("\n\n", $sections);
    }

    /**
     * Render user rights section
     *
     * @return string User rights HTML.
     */
    private function render_user_rights(): string {
        $sections = [];

        $sections[] = '<h2>Your Rights</h2>';
        $sections[] = '<p>Depending on your location and applicable laws, you may have the following rights:</p>';
        $sections[] = '<ul>';

        // Access right
        $sections[] = '<li><strong>Right to Access:</strong> You can request a copy of your personal data.</li>';

        // Export right
        if ($this->answers['allow_data_export'] ?? true) {
            $sections[] = '<li><strong>Right to Data Portability:</strong> You can request your data in a structured, machine-readable format.</li>';
        }

        // Deletion right
        if ($this->answers['allow_data_deletion'] ?? true) {
            $sections[] = '<li><strong>Right to Erasure:</strong> You can request deletion of your personal data.</li>';
        }

        // Correction right
        $sections[] = '<li><strong>Right to Rectification:</strong> You can request correction of inaccurate data.</li>';

        // Objection right
        $sections[] = '<li><strong>Right to Object:</strong> You can object to processing of your data for certain purposes.</li>';

        $sections[] = '</ul>';

        $sections[] = sprintf(
            '<p>To exercise these rights, please contact us at <a href="mailto:%s">%s</a>.</p>',
            esc_attr($this->get_contact_email()),
            esc_html($this->get_contact_email())
        );

        return implode("\n", $sections);
    }

    /**
     * Render children section
     *
     * @return string Children HTML.
     */
    private function render_children(): string {
        if (!($this->answers['allows_children'] ?? false)) {
            $min_age = $this->answers['minimum_age'] ?? 13;
            
            return sprintf(
                '<h2>Children\'s Privacy</h2>
                <p>Our service is not intended for children under the age of %d. We do not knowingly collect personal information from children under %d. If you are a parent or guardian and believe your child has provided us with personal information, please contact us immediately.</p>',
                $min_age,
                $min_age
            );
        }

        return $this->load_snippet('children-coppa');
    }

    /**
     * Render DPO section
     *
     * @return string DPO section HTML.
     */
    private function render_dpo_section(): string {
        $has_dpo = $this->answers['has_dpo'] ?? false;
        
        if (!$has_dpo) {
            return '';
        }

        $dpo_name = $this->answers['dpo_name'] ?? '';
        $dpo_email = $this->answers['dpo_email'] ?? '';
        
        $enabled_modes = $this->get_enabled_compliance_modes();
        $requires_dpo = $enabled_modes['EU'] || $enabled_modes['UK'] || $enabled_modes['BR'] || $enabled_modes['TR'];
        
        if (!$requires_dpo && empty($dpo_name) && empty($dpo_email)) {
            return '';
        }

        $content = '<h2>Data Protection Officer</h2>';
        
        if (!empty($dpo_name) && !empty($dpo_email)) {
            $content .= '<p>We have appointed a Data Protection Officer (DPO) who is responsible for overseeing our data protection strategy and ensuring compliance with applicable laws.</p>';
            $content .= '<p><strong>DPO Contact Information:</strong></p>';
            $content .= '<ul>';
            $content .= '<li><strong>Name:</strong> ' . esc_html($dpo_name) . '</li>';
            $content .= '<li><strong>Email:</strong> <a href="mailto:' . esc_attr($dpo_email) . '">' . esc_html($dpo_email) . '</a></li>';
            $content .= '</ul>';
            $content .= '<p>You can contact our DPO directly for any questions about how we handle your personal data, including requests to exercise your data protection rights.</p>';
        } elseif ($requires_dpo) {
            $content .= '<p>For questions about data protection, please contact us at the email address provided in the Contact section of this policy.</p>';
        }

        return $content;
    }

    /**
     * Get enabled compliance modes from consent settings
     *
     * @return array Enabled compliance modes.
     */
    private function get_enabled_compliance_modes(): array {
        return [
            'EU' => get_option('consent_gdpr_enabled', false),
            'UK' => get_option('consent_uk_gdpr_enabled', false),
            'CA' => get_option('consent_ccpa_enabled', false),
            'BR' => get_option('consent_lgpd_enabled', false),
            'CN' => get_option('consent_pipeda_enabled', false),
            'SG' => get_option('consent_pdpa_sg_enabled', false),
            'TH' => get_option('consent_pdpa_th_enabled', false),
            'JP' => get_option('consent_appi_enabled', false),
            'ZA' => get_option('consent_popia_enabled', false),
            'TR' => get_option('consent_kvkk_enabled', false),
            'SA' => get_option('consent_pdpl_enabled', false),
        ];
    }

    /**
     * Render regional compliance section
     *
     * @return string Regional compliance HTML.
     */
    private function render_regional_compliance(): string {
        $sections = [];
        
        // First check enabled compliance modes from consent settings
        $enabled_modes = $this->get_enabled_compliance_modes();
        $has_enabled_modes = false;
        
        foreach ($enabled_modes as $region => $enabled) {
            if ($enabled) {
                $has_enabled_modes = true;
                break;
            }
        }
        
        // If compliance modes are enabled, use them; otherwise fallback to questionnaire
        if ($has_enabled_modes) {
            if ($enabled_modes['EU']) {
                $sections[] = $this->load_snippet('gdpr-rights');
            }
            if ($enabled_modes['UK']) {
                $sections[] = $this->load_snippet('uk-gdpr-compliance');
            }
            if ($enabled_modes['CA']) {
                $sections[] = $this->load_snippet('ccpa-rights');
            }
            if ($enabled_modes['BR']) {
                $sections[] = $this->load_snippet('lgpd-rights');
            }
            if ($enabled_modes['CN']) {
                $sections[] = $this->load_snippet('pipeda-compliance');
            }
            if ($enabled_modes['SG']) {
                $sections[] = $this->load_snippet('pdpa-singapore-compliance');
            }
            if ($enabled_modes['TH']) {
                $sections[] = $this->load_snippet('pdpa-thailand-compliance');
            }
            if ($enabled_modes['JP']) {
                $sections[] = $this->load_snippet('appi-japan-compliance');
            }
            if ($enabled_modes['ZA']) {
                $sections[] = $this->load_snippet('popia-southafrica-compliance');
            }
            if ($enabled_modes['TR']) {
                $sections[] = $this->load_snippet('kvkk-turkey-compliance');
            }
            if ($enabled_modes['SA']) {
                $sections[] = $this->load_snippet('pdpl-saudi-compliance');
            }
        } else {
            // Fallback to questionnaire answers
            $countries = $this->answers['target_countries'] ?? [];

            if (in_array('EU', $countries) || in_array('UK', $countries)) {
                $sections[] = $this->load_snippet('gdpr-rights');
            }

            if (in_array('CA', $countries)) {
                $sections[] = $this->load_snippet('ccpa-rights');
            }

            if (in_array('BR', $countries)) {
                $sections[] = $this->load_snippet('lgpd-rights');
            }

            if (in_array('AU', $countries)) {
                $sections[] = $this->load_snippet('australia-privacy');
            }
        }

        return implode("\n\n", $sections);
    }

    /**
     * Render changes section
     *
     * @return string Changes HTML.
     */
    private function render_changes(): string {
        return $this->load_snippet('policy-changes');
    }

    /**
     * Render contact section
     *
     * @return string Contact HTML.
     */
    private function render_contact(): string {
        $contact = '<h2>Contact Us</h2>';
        $contact .= '<p>If you have any questions about this Privacy Policy, please contact us:</p>';
        $contact .= '<ul>';
        $contact .= sprintf('<li><strong>Email:</strong> <a href="mailto:%s">%s</a></li>', 
            esc_attr($this->get_contact_email()),
            esc_html($this->get_contact_email())
        );

        if ($phone = $this->get_phone_number()) {
            $contact .= sprintf('<li><strong>Phone:</strong> %s</li>', esc_html($phone));
        }

        if ($address = $this->get_physical_address()) {
            $contact .= sprintf('<li><strong>Address:</strong> %s</li>', nl2br(esc_html($address)));
        }

        $contact .= '</ul>';

        return $contact;
    }
}
