<?php
/**
 * Data Protection Policy Generator
 *
 * Generates comprehensive Data Protection Policy based on
 * enabled compliance modes and questionnaire answers.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   4.7.0
 */

namespace ComplyFlow\Modules\Documents;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class DataProtectionPolicyGenerator
 */
class DataProtectionPolicyGenerator {
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
     * Generate data protection policy
     *
     * @return string Generated policy HTML.
     */
    public function generate(): string {
        $tokens = $this->build_tokens();
        $template = $this->load_template('data-protection-policy-template.php');

        // Replace tokens
        return $this->replace_tokens($template, $tokens);
    }

    /**
     * Build replacement tokens
     *
     * @return array Tokens and their replacements.
     */
    private function build_tokens(): array {
        $enabled_modes = $this->get_enabled_compliance_modes();
        
        $tokens = [
            '{{COMPANY_NAME}}' => $this->get_company_name(),
            '{{CONTACT_EMAIL}}' => $this->get_contact_email(),
            '{{PHYSICAL_ADDRESS}}' => $this->get_physical_address(),
            '{{PHONE_NUMBER}}' => $this->get_phone_number(),
            '{{EFFECTIVE_DATE}}' => current_time('F j, Y'),
            '{{LAST_UPDATED}}' => current_time('F j, Y'),
            '{{WEBSITE_URL}}' => get_site_url(),
            '{{WEBSITE_NAME}}' => get_bloginfo('name'),

            // Compliance badges
            '{{GDPR_BADGE}}' => $enabled_modes['GDPR'] ? '<span class="compliance-badge">GDPR (EU)</span>' : '',
            '{{UK_GDPR_BADGE}}' => $enabled_modes['UK_GDPR'] ? '<span class="compliance-badge">UK GDPR</span>' : '',
            '{{CCPA_BADGE}}' => $enabled_modes['CCPA'] ? '<span class="compliance-badge">CCPA (California)</span>' : '',
            '{{LGPD_BADGE}}' => $enabled_modes['LGPD'] ? '<span class="compliance-badge">LGPD (Brazil)</span>' : '',
            '{{PIPEDA_BADGE}}' => $enabled_modes['PIPEDA'] ? '<span class="compliance-badge">PIPEDA (Canada)</span>' : '',
            '{{PDPA_SG_BADGE}}' => $enabled_modes['PDPA_SG'] ? '<span class="compliance-badge">PDPA (Singapore)</span>' : '',
            '{{PDPA_TH_BADGE}}' => $enabled_modes['PDPA_TH'] ? '<span class="compliance-badge">PDPA (Thailand)</span>' : '',
            '{{APPI_BADGE}}' => $enabled_modes['APPI'] ? '<span class="compliance-badge">APPI (Japan)</span>' : '',
            '{{POPIA_BADGE}}' => $enabled_modes['POPIA'] ? '<span class="compliance-badge">POPIA (South Africa)</span>' : '',
            '{{KVKK_BADGE}}' => $enabled_modes['KVKK'] ? '<span class="compliance-badge">KVKK (Turkey)</span>' : '',
            '{{PDPL_BADGE}}' => $enabled_modes['PDPL'] ? '<span class="compliance-badge">PDPL (Saudi Arabia)</span>' : '',
            '{{AUSTRALIA_BADGE}}' => $enabled_modes['AUSTRALIA'] ? '<span class="compliance-badge">Australia Privacy Act</span>' : '',

            // Dynamic compliance sections based on enabled modes
            '{{GDPR_SECTION}}' => $enabled_modes['GDPR'] ? $this->load_snippet('gdpr-compliance') : '',
            '{{UK_GDPR_SECTION}}' => $enabled_modes['UK_GDPR'] ? $this->load_snippet('uk-gdpr-compliance') : '',
            '{{CCPA_SECTION}}' => $enabled_modes['CCPA'] ? $this->load_snippet('ccpa-compliance') : '',
            '{{LGPD_SECTION}}' => $enabled_modes['LGPD'] ? $this->load_snippet('lgpd-compliance') : '',
            '{{PIPEDA_SECTION}}' => $enabled_modes['PIPEDA'] ? $this->load_snippet('pipeda-compliance') : '',
            '{{PDPA_SG_SECTION}}' => $enabled_modes['PDPA_SG'] ? $this->load_snippet('pdpa-singapore-compliance') : '',
            '{{PDPA_TH_SECTION}}' => $enabled_modes['PDPA_TH'] ? $this->load_snippet('pdpa-thailand-compliance') : '',
            '{{APPI_SECTION}}' => $enabled_modes['APPI'] ? $this->load_snippet('appi-japan-compliance') : '',
            '{{POPIA_SECTION}}' => $enabled_modes['POPIA'] ? $this->load_snippet('popia-southafrica-compliance') : '',
            '{{KVKK_SECTION}}' => $enabled_modes['KVKK'] ? $this->load_snippet('kvkk-turkey-compliance') : '',
            '{{PDPL_SECTION}}' => $enabled_modes['PDPL'] ? $this->load_snippet('pdpl-saudi-compliance') : '',
            '{{AUSTRALIA_SECTION}}' => $enabled_modes['AUSTRALIA'] ? $this->load_snippet('australia-privacy') : '',

            // Additional sections
            '{{DPO_SECTION}}' => $this->render_dpo_section(),
            '{{DATA_PROCESSING_SECTION}}' => $this->render_data_processing(),
            '{{LEGAL_BASIS_SECTION}}' => $this->render_legal_basis(),
            '{{DATA_SECURITY_SECTION}}' => $this->render_data_security(),
            '{{BREACH_PROCEDURES_SECTION}}' => $this->render_breach_procedures(),
            '{{DATA_TRANSFERS_SECTION}}' => $this->render_data_transfers(),
            '{{RETENTION_POLICY_SECTION}}' => $this->render_retention_policy(),
            '{{RIGHTS_SUMMARY_SECTION}}' => $this->render_rights_summary(),
            '{{AUTOMATED_DECISIONS_SECTION}}' => $this->render_automated_decisions(),
            '{{AUDIT_RECORDS_SECTION}}' => $this->render_audit_records(),
            '{{CONTACT_SECTION}}' => $this->render_contact(),
        ];

        return $tokens;
    }

    /**
     * Get enabled compliance modes from consent settings
     *
     * @return array Enabled compliance modes.
     */
    private function get_enabled_compliance_modes(): array {
        return [
            'GDPR' => get_option('consent_gdpr_enabled', false),
            'UK_GDPR' => get_option('consent_uk_gdpr_enabled', false),
            'CCPA' => get_option('consent_ccpa_enabled', false),
            'LGPD' => get_option('consent_lgpd_enabled', false),
            'PIPEDA' => get_option('consent_pipeda_enabled', false),
            'PDPA_SG' => get_option('consent_pdpa_sg_enabled', false),
            'PDPA_TH' => get_option('consent_pdpa_th_enabled', false),
            'APPI' => get_option('consent_appi_enabled', false),
            'POPIA' => get_option('consent_popia_enabled', false),
            'KVKK' => get_option('consent_kvkk_enabled', false),
            'PDPL' => get_option('consent_pdpl_enabled', false),
            'AUSTRALIA' => get_option('consent_australia_enabled', false),
        ];
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

        $content = '<h2>Data Protection Officer</h2>';
        
        if (!empty($dpo_name) && !empty($dpo_email)) {
            $content .= '<p>We have appointed a Data Protection Officer (DPO) who is responsible for overseeing our data protection strategy and ensuring compliance with applicable laws.</p>';
            $content .= '<p><strong>DPO Contact Information:</strong></p>';
            $content .= '<ul>';
            $content .= '<li><strong>Name:</strong> ' . esc_html($dpo_name) . '</li>';
            $content .= '<li><strong>Email:</strong> <a href="mailto:' . esc_attr($dpo_email) . '">' . esc_html($dpo_email) . '</a></li>';
            $content .= '</ul>';
            $content .= '<p>You can contact our DPO directly for any questions about how we handle your personal data, including requests to exercise your data protection rights.</p>';
        } else {
            $content .= '<p>For questions about data protection, please contact us at the email address provided in the Contact section of this policy.</p>';
        }

        return $content;
    }

    /**
     * Render data transfers section
     *
     * @return string Data transfers HTML.
     */
    private function render_data_transfers(): string {
        $content = '<h2>International Data Transfers</h2>';
        $content .= '<p>We may transfer your personal data to countries outside your jurisdiction. When we do, we ensure appropriate safeguards are in place:</p>';
        $content .= '<ul>';
        $content .= '<li><strong>Standard Contractual Clauses (SCCs):</strong> EU Commission-approved contracts that provide adequate safeguards for data transfers</li>';
        $content .= '<li><strong>Adequacy Decisions:</strong> Transfers to countries deemed to provide adequate protection by relevant authorities</li>';
        $content .= '<li><strong>Binding Corporate Rules (BCRs):</strong> Internal policies for intra-organizational transfers</li>';
        $content .= '<li><strong>Consent:</strong> Where you have given explicit consent to the transfer</li>';
        $content .= '</ul>';
        
        $enabled_modes = $this->get_enabled_compliance_modes();
        
        if ($enabled_modes['GDPR']) {
            $content .= '<p><strong>GDPR Note:</strong> We comply with GDPR Chapter V requirements for international transfers. You can request information about the specific safeguards in place for transfers of your data.</p>';
        }

        return $content;
    }

    /**
     * Render rights summary section
     *
     * @return string Rights summary HTML.
     */
    private function render_rights_summary(): string {
        $enabled_modes = $this->get_enabled_compliance_modes();
        
        $content = '<h2>Data Subject Rights Summary</h2>';
        $content .= '<p>Depending on your location and applicable laws, you may have the following rights:</p>';
        
        $content .= '<table class="rights-summary-table" style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
        $content .= '<thead>';
        $content .= '<tr style="background: #2c3e50; color: white;">';
        $content .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Right</th>';
        $content .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Description</th>';
        $content .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Applicable Laws</th>';
        $content .= '</tr>';
        $content .= '</thead>';
        $content .= '<tbody>';

        // Right to Access
        $content .= '<tr>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Right to Access</strong></td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">Request a copy of your personal data</td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">GDPR, CCPA, LGPD, PIPEDA, PDPA, APPI, POPIA</td>';
        $content .= '</tr>';

        // Right to Rectification
        $content .= '<tr>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Right to Rectification</strong></td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">Correct inaccurate or incomplete data</td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">GDPR, LGPD, PIPEDA, PDPA, APPI</td>';
        $content .= '</tr>';

        // Right to Erasure
        $content .= '<tr>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Right to Erasure</strong></td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">Request deletion of your personal data</td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">GDPR, CCPA, LGPD, PDPA (TH), APPI</td>';
        $content .= '</tr>';

        // Right to Data Portability
        $content .= '<tr>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Right to Data Portability</strong></td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">Receive your data in a machine-readable format</td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">GDPR, LGPD, PDPA (TH)</td>';
        $content .= '</tr>';

        // Right to Object
        $content .= '<tr>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Right to Object</strong></td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">Object to processing of your data</td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">GDPR, LGPD, PDPA (TH), POPIA</td>';
        $content .= '</tr>';

        // Right to Opt-Out
        $content .= '<tr>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Right to Opt-Out of Sale</strong></td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">Opt-out of the sale of personal information</td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">CCPA</td>';
        $content .= '</tr>';

        // Right to Withdraw Consent
        $content .= '<tr>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;"><strong>Right to Withdraw Consent</strong></td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">Withdraw consent at any time</td>';
        $content .= '<td style="padding: 12px; border: 1px solid #ddd;">GDPR, LGPD, PIPEDA, PDPA, POPIA, KVKK</td>';
        $content .= '</tr>';

        $content .= '</tbody>';
        $content .= '</table>';

        $content .= '<p>To exercise any of these rights, please contact us using the information provided in the Contact section.</p>';

        return $content;
    }

    /**
     * Render data processing section
     *
     * @return string Data processing HTML.
     */
    private function render_data_processing(): string {
        $html = '<h2>1. Data Processing Activities</h2>';
        $html .= '<p>We process personal data for the following purposes:</p>';
        $html .= '<ul>';
        
        // Basic purposes
        $html .= '<li><strong>Service Delivery:</strong> To provide and maintain our services</li>';
        $html .= '<li><strong>Communication:</strong> To respond to inquiries and provide customer support</li>';
        $html .= '<li><strong>Security:</strong> To protect against fraud, abuse, and security threats</li>';
        $html .= '<li><strong>Legal Compliance:</strong> To comply with legal obligations</li>';
        
        // E-commerce specific
        if ($this->answers['has_ecommerce'] ?? false) {
            $html .= '<li><strong>Order Processing:</strong> To process transactions and fulfill orders</li>';
            $html .= '<li><strong>Payment Processing:</strong> To handle payments securely through our processors</li>';
        }
        
        // Marketing
        if ($this->answers['has_email_marketing'] ?? false) {
            $html .= '<li><strong>Marketing:</strong> To send promotional communications (with your consent)</li>';
        }
        
        // Analytics
        if ($this->answers['has_analytics'] ?? false) {
            $html .= '<li><strong>Analytics:</strong> To analyze usage and improve our services</li>';
        }
        
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Render legal basis section
     *
     * @return string Legal basis HTML.
     */
    private function render_legal_basis(): string {
        $html = '<h2>2. Legal Basis for Processing</h2>';
        $html .= '<p>We process personal data based on the following legal grounds:</p>';
        $html .= '<ul>';
        $html .= '<li><strong>Consent:</strong> Where you have given explicit consent for specific processing activities</li>';
        $html .= '<li><strong>Contract:</strong> Where processing is necessary to fulfill our contractual obligations</li>';
        $html .= '<li><strong>Legal Obligation:</strong> Where we must process data to comply with the law</li>';
        $html .= '<li><strong>Legitimate Interests:</strong> Where we have a legitimate business interest that does not override your rights</li>';
        $html .= '<li><strong>Vital Interests:</strong> Where processing is necessary to protect someone\'s life</li>';
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Render data security section
     *
     * @return string Data security HTML.
     */
    private function render_data_security(): string {
        $html = '<h2>3. Data Security Measures</h2>';
        $html .= '<p>We implement comprehensive technical and organizational measures to protect personal data:</p>';
        
        $html .= '<h3>Technical Measures</h3>';
        $html .= '<ul>';
        $html .= '<li><strong>Encryption:</strong> Data encrypted in transit (TLS/SSL) and at rest</li>';
        $html .= '<li><strong>Access Controls:</strong> Role-based access with multi-factor authentication</li>';
        $html .= '<li><strong>Firewalls:</strong> Network security with intrusion detection systems</li>';
        $html .= '<li><strong>Regular Updates:</strong> Security patches and system updates</li>';
        $html .= '<li><strong>Secure Backups:</strong> Encrypted backups with tested recovery procedures</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Organizational Measures</h3>';
        $html .= '<ul>';
        $html .= '<li><strong>Staff Training:</strong> Regular data protection training for all personnel</li>';
        $html .= '<li><strong>Confidentiality:</strong> Confidentiality agreements with all staff and processors</li>';
        $html .= '<li><strong>Access Policies:</strong> Strict policies limiting data access to authorized personnel only</li>';
        $html .= '<li><strong>Incident Response:</strong> Documented procedures for security incidents</li>';
        $html .= '<li><strong>Vendor Management:</strong> Due diligence on all third-party processors</li>';
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Render breach procedures section
     *
     * @return string Breach procedures HTML.
     */
    private function render_breach_procedures(): string {
        $html = '<h2>4. Data Breach Notification Procedures</h2>';
        $html .= '<p>In the event of a personal data breach, we follow these procedures:</p>';
        
        $html .= '<h3>Detection and Assessment</h3>';
        $html .= '<ul>';
        $html .= '<li>Immediate detection and logging of security incidents</li>';
        $html .= '<li>Assessment of breach severity and potential impact</li>';
        $html .= '<li>Documentation of the breach details and timeline</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Notification Timeline</h3>';
        $html .= '<ul>';
        $html .= '<li><strong>Supervisory Authority:</strong> Within 72 hours of becoming aware (GDPR)</li>';
        $html .= '<li><strong>Affected Individuals:</strong> Without undue delay if high risk to rights and freedoms</li>';
        $html .= '<li><strong>Data Protection Officer:</strong> Immediate notification to DPO (if applicable)</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Information Provided</h3>';
        $html .= '<ul>';
        $html .= '<li>Nature of the breach and categories of data affected</li>';
        $html .= '<li>Number of affected individuals and data records</li>';
        $html .= '<li>Likely consequences of the breach</li>';
        $html .= '<li>Measures taken or proposed to address the breach</li>';
        $html .= '<li>Contact point for further information</li>';
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Render retention policy section
     *
     * @return string Retention policy HTML.
     */
    private function render_retention_policy(): string {
        $retention_period = $this->answers['data_retention_period'] ?? 24;
        
        $html = '<h2>5. Data Retention Policy</h2>';
        $html .= '<p>We retain personal data only for as long as necessary to fulfill the purposes for which it was collected:</p>';
        
        $html .= '<h3>General Retention Period</h3>';
        $html .= '<p>Personal data is retained for <strong>' . $retention_period . ' months</strong> unless specific legal requirements mandate longer retention.</p>';
        
        $html .= '<h3>Category-Specific Retention</h3>';
        $html .= '<ul>';
        $html .= '<li><strong>Account Data:</strong> Retained while account is active plus ' . $retention_period . ' months after closure</li>';
        $html .= '<li><strong>Transaction Records:</strong> Retained for 7 years for tax and accounting purposes</li>';
        $html .= '<li><strong>Marketing Data:</strong> Retained until consent is withdrawn</li>';
        $html .= '<li><strong>Log Files:</strong> Retained for 12 months for security purposes</li>';
        $html .= '<li><strong>Legal Claims:</strong> Retained for applicable statute of limitations period</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Secure Deletion</h3>';
        $html .= '<p>Upon expiration of the retention period, personal data is securely deleted using:</p>';
        $html .= '<ul>';
        $html .= '<li>Secure deletion methods preventing recovery</li>';
        $html .= '<li>Physical destruction of offline storage media</li>';
        $html .= '<li>Anonymization where data must be retained for statistical purposes</li>';
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Render automated decisions section
     *
     * @return string Automated decisions HTML.
     */
    private function render_automated_decisions(): string {
        $html = '<h2>6. Automated Decision-Making and Profiling</h2>';
        $html .= '<p>We inform you about any automated decision-making, including profiling:</p>';
        
        $html .= '<h3>Automated Processing</h3>';
        $html .= '<ul>';
        $html .= '<li><strong>Fraud Detection:</strong> Automated analysis of transactions for fraud prevention</li>';
        $html .= '<li><strong>Service Optimization:</strong> Automated personalization of user experience</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Your Rights</h3>';
        $html .= '<p>You have the right to:</p>';
        $html .= '<ul>';
        $html .= '<li>Not be subject to decisions based solely on automated processing that produces legal or similarly significant effects</li>';
        $html .= '<li>Request human intervention in automated decisions</li>';
        $html .= '<li>Express your point of view</li>';
        $html .= '<li>Contest automated decisions</li>';
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Render audit records section
     *
     * @return string Audit records HTML.
     */
    private function render_audit_records(): string {
        $html = '<h2>7. Records of Processing Activities</h2>';
        $html .= '<p>We maintain comprehensive records of all data processing activities as required by data protection laws:</p>';
        
        $html .= '<h3>Documentation Maintained</h3>';
        $html .= '<ul>';
        $html .= '<li><strong>Data Inventory:</strong> Categories of personal data processed</li>';
        $html .= '<li><strong>Processing Purposes:</strong> Specific purposes for each processing activity</li>';
        $html .= '<li><strong>Data Recipients:</strong> Categories of recipients who receive personal data</li>';
        $html .= '<li><strong>International Transfers:</strong> Documentation of data transfers outside the jurisdiction</li>';
        $html .= '<li><strong>Security Measures:</strong> Technical and organizational security measures implemented</li>';
        $html .= '<li><strong>Retention Schedules:</strong> Time limits for erasure of different data categories</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Accountability Measures</h3>';
        $html .= '<ul>';
        $html .= '<li>Regular data protection impact assessments (DPIAs) for high-risk processing</li>';
        $html .= '<li>Documentation of consent mechanisms and opt-out procedures</li>';
        $html .= '<li>Regular audits of data processing activities</li>';
        $html .= '<li>Records of data breach incidents and responses</li>';
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Render contact section
     *
     * @return string Contact section HTML.
     */
    private function render_contact(): string {
        $content = '<h2>Contact Us</h2>';
        $content .= '<p>For data protection inquiries, please contact us:</p>';
        $content .= '<ul>';
        $content .= sprintf('<li><strong>Email:</strong> <a href="mailto:%s">%s</a></li>', 
            esc_attr($this->get_contact_email()),
            esc_html($this->get_contact_email())
        );

        if ($phone = $this->get_phone_number()) {
            $content .= sprintf('<li><strong>Phone:</strong> %s</li>', esc_html($phone));
        }

        if ($address = $this->get_physical_address()) {
            $content .= sprintf('<li><strong>Address:</strong> %s</li>', nl2br(esc_html($address)));
        }

        $content .= '</ul>';

        return $content;
    }

    /**
     * Replace tokens in template
     *
     * @param string $template Template content.
     * @param array  $tokens   Replacement tokens.
     * @return string Processed content.
     */
    private function replace_tokens(string $template, array $tokens): string {
        return str_replace(
            array_keys($tokens),
            array_values($tokens),
            $template
        );
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

        return $this->get_default_template();
    }

    /**
     * Get default template if file doesn't exist
     *
     * @return string Default template content.
     */
    private function get_default_template(): string {
        return '<html><head><title>Data Protection Policy - {{COMPANY_NAME}}</title></head><body>
            <h1>Data Protection Policy</h1>
            <p><strong>{{COMPANY_NAME}}</strong></p>
            <p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>
            
            {{GDPR_SECTION}}
            {{UK_GDPR_SECTION}}
            {{CCPA_SECTION}}
            {{LGPD_SECTION}}
            {{PIPEDA_SECTION}}
            {{PDPA_SG_SECTION}}
            {{PDPA_TH_SECTION}}
            {{APPI_SECTION}}
            {{POPIA_SECTION}}
            {{KVKK_SECTION}}
            {{PDPL_SECTION}}
            
            {{DPO_SECTION}}
            {{DATA_TRANSFERS_SECTION}}
            {{RIGHTS_SUMMARY_SECTION}}
            {{CONTACT_SECTION}}
        </body></html>';
    }

    /**
     * Load snippet file
     *
     * @param string $filename Snippet filename.
     * @return string Snippet content.
     */
    private function load_snippet(string $filename): string {
        $filepath = $this->template_path . 'snippets/' . $filename . '.php';

        if (!file_exists($filepath)) {
            return '';
        }

        ob_start();
        include $filepath;
        return ob_get_clean();
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
}
