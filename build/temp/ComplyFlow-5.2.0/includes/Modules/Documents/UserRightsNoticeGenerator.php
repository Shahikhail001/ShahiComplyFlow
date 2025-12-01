<?php
/**
 * User Rights Notice Generator
 *
 * Generates comprehensive notice explaining data subject rights under GDPR/CCPA/LGPD.
 * Integrates with DSR (Data Subject Request) module.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   4.9.0
 */

namespace ComplyFlow\Modules\Documents;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class UserRightsNoticeGenerator
 */
class UserRightsNoticeGenerator {
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
     * Generate user rights notice
     *
     * @return string Generated notice HTML.
     */
    public function generate(): string {
        $company_name = $this->answers['company_name'] ?? 'Our Company';
        $effective_date = $this->answers['effective_date'] ?? date('F j, Y');
        $contact_email = $this->answers['contact_email'] ?? '';

        $content = $this->load_template();

        // Replace tokens
        $replacements = [
            '{{COMPANY_NAME}}' => esc_html($company_name),
            '{{EFFECTIVE_DATE}}' => esc_html($effective_date),
            '{{CONTACT_EMAIL}}' => esc_html($contact_email),
            '{{OVERVIEW_SECTION}}' => $this->render_overview(),
            '{{RIGHT_TO_ACCESS_SECTION}}' => $this->render_right_to_access(),
            '{{RIGHT_TO_RECTIFICATION_SECTION}}' => $this->render_right_to_rectification(),
            '{{RIGHT_TO_ERASURE_SECTION}}' => $this->render_right_to_erasure(),
            '{{RIGHT_TO_PORTABILITY_SECTION}}' => $this->render_right_to_portability(),
            '{{RIGHT_TO_OBJECT_SECTION}}' => $this->render_right_to_object(),
            '{{RIGHT_TO_RESTRICT_SECTION}}' => $this->render_right_to_restrict(),
            '{{RIGHT_TO_WITHDRAW_SECTION}}' => $this->render_right_to_withdraw(),
            '{{AUTOMATED_DECISIONS_SECTION}}' => $this->render_automated_decisions(),
            '{{CALIFORNIA_RIGHTS_SECTION}}' => $this->render_california_rights(),
            '{{HOW_TO_EXERCISE_SECTION}}' => $this->render_how_to_exercise(),
            '{{DSR_PORTAL_SECTION}}' => $this->render_dsr_portal(),
            '{{RESPONSE_TIMELINE_SECTION}}' => $this->render_response_timeline(),
            '{{VERIFICATION_SECTION}}' => $this->render_verification(),
            '{{COMPLAINTS_SECTION}}' => $this->render_complaints(),
            '{{CONTACT_SECTION}}' => $this->render_contact(),
        ];

        foreach ($replacements as $token => $value) {
            $content = str_replace($token, $value, $content);
        }

        // Clean up any remaining unreplaced tokens
        $content = preg_replace('/\{\{[A-Z_]+\}\}/', '', $content);

        return $content;
    }

    /**
     * Load template
     *
     * @return string Template content.
     */
    private function load_template(): string {
        $template_path = COMPLYFLOW_PATH . 'templates/policies/user-rights-notice-template.php';
        
        if (file_exists($template_path)) {
            ob_start();
            include $template_path;
            return ob_get_clean();
        }
        
        return '';
    }

    /**
     * Render overview section
     *
     * @return string Section content.
     */
    private function render_overview(): string {
        $company = $this->answers['company_name'] ?? 'Our Company';
        $target_countries = $this->answers['target_countries'] ?? [];
        
        $has_gdpr = is_array($target_countries) && in_array('EU', $target_countries);
        $has_ccpa = is_array($target_countries) && in_array('US', $target_countries);
        $has_lgpd = is_array($target_countries) && in_array('BR', $target_countries);
        
        $frameworks = [];
        if ($has_gdpr) $frameworks[] = 'GDPR (EU)';
        if ($has_ccpa) $frameworks[] = 'CCPA (California)';
        if ($has_lgpd) $frameworks[] = 'LGPD (Brazil)';
        
        $frameworks_text = !empty($frameworks) ? implode(', ', $frameworks) : 'applicable privacy laws';
        
        return <<<HTML
<div class="policy-section">
    <h2>1. Your Privacy Rights</h2>
    <p>{$company} respects your privacy and is committed to protecting your personal data. Under {$frameworks_text} and other privacy regulations, you have important rights regarding your personal information.</p>
    
    <div class="rights-overview-box">
        <h3>Key Rights Summary</h3>
        <ul>
            <li><strong>Right to Access:</strong> Request a copy of your personal data</li>
            <li><strong>Right to Rectification:</strong> Correct inaccurate or incomplete data</li>
            <li><strong>Right to Erasure:</strong> Request deletion of your data ("right to be forgotten")</li>
            <li><strong>Right to Data Portability:</strong> Receive your data in a machine-readable format</li>
            <li><strong>Right to Object:</strong> Object to certain types of processing</li>
            <li><strong>Right to Restrict Processing:</strong> Limit how we use your data</li>
            <li><strong>Right to Withdraw Consent:</strong> Revoke previously given consent</li>
            <li><strong>Right to Non-Discrimination:</strong> Not be discriminated against for exercising rights</li>
        </ul>
    </div>
    
    <p>This notice explains each right in detail and how you can exercise them.</p>
</div>
HTML;
    }

    /**
     * Render right to access section
     *
     * @return string Section content.
     */
    private function render_right_to_access(): string {
        return <<<HTML
<div class="policy-section">
    <h2>2. Right to Access Your Personal Data</h2>
    
    <h3>What This Means</h3>
    <p>You have the right to request confirmation about whether we process your personal data and, if we do, to access that data along with specific information about how we use it.</p>
    
    <h3>What Information You Can Request</h3>
    <ul>
        <li><strong>Confirmation:</strong> Whether we hold personal data about you</li>
        <li><strong>Categories:</strong> What types of personal data we collect</li>
        <li><strong>Purposes:</strong> Why we collect and process your data</li>
        <li><strong>Recipients:</strong> Who we share your data with</li>
        <li><strong>Retention Period:</strong> How long we keep your data</li>
        <li><strong>Source:</strong> Where we obtained your data (if not from you directly)</li>
        <li><strong>Automated Decisions:</strong> Whether we use automated decision-making or profiling</li>
        <li><strong>Safeguards:</strong> Security measures we use to protect your data</li>
    </ul>
    
    <h3>What You'll Receive</h3>
    <p>When you make an access request, we'll provide:</p>
    <div class="info-box">
        <h4>📄 Data Export Package</h4>
        <ul>
            <li>Copy of all personal data we hold about you</li>
            <li>Data provided in a commonly used format (PDF, CSV, or JSON)</li>
            <li>Explanation of data categories and processing purposes</li>
            <li>List of third parties who have received your data</li>
            <li>Information about data retention periods</li>
        </ul>
    </div>
    
    <h3>Limitations</h3>
    <p>We may not be able to provide access to data if:</p>
    <ul>
        <li>It would adversely affect the rights of others</li>
        <li>It contains legal privilege or confidential information</li>
        <li>Disclosure would compromise security or prevent fraud detection</li>
        <li>The request is manifestly unfounded or excessive</li>
    </ul>
    
    <h3>Cost</h3>
    <p><strong>First Request:</strong> Free of charge<br>
    <strong>Additional Requests:</strong> May incur a reasonable fee to cover administrative costs if requests are excessive or repetitive</p>
</div>
HTML;
    }

    /**
     * Render right to rectification section
     *
     * @return string Section content.
     */
    private function render_right_to_rectification(): string {
        return <<<HTML
<div class="policy-section">
    <h2>3. Right to Rectification</h2>
    
    <h3>What This Means</h3>
    <p>You have the right to have inaccurate personal data corrected and incomplete personal data completed.</p>
    
    <h3>When to Use This Right</h3>
    <ul>
        <li>We have incorrect information about you (e.g., wrong email address, outdated phone number)</li>
        <li>Your personal circumstances have changed (e.g., new address, name change)</li>
        <li>We have incomplete data that needs supplementing</li>
        <li>Data quality issues affect services you receive</li>
    </ul>
    
    <h3>How Rectification Works</h3>
    <ol>
        <li><strong>Submit Request:</strong> Tell us what data is inaccurate and provide correct information</li>
        <li><strong>Verification:</strong> We may ask for proof of the correct information</li>
        <li><strong>Correction:</strong> We update your data in our systems</li>
        <li><strong>Notification:</strong> We inform any third parties to whom we've disclosed the data</li>
        <li><strong>Confirmation:</strong> You receive confirmation that corrections were made</li>
    </ol>
    
    <h3>Timeline</h3>
    <p>We will correct inaccurate data <strong>without undue delay</strong> and typically within:</p>
    <ul>
        <li><strong>Simple corrections:</strong> 1-5 business days</li>
        <li><strong>Complex corrections:</strong> Up to 30 days</li>
    </ul>
    
    <h3>Self-Service Options</h3>
    <p>For some data, you can make corrections yourself:</p>
    <ul>
        <li><strong>Account Settings:</strong> Update your profile information</li>
        <li><strong>User Dashboard:</strong> Modify preferences and contact details</li>
        <li><strong>Email Preferences:</strong> Change subscription settings</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render right to erasure section
     *
     * @return string Section content.
     */
    private function render_right_to_erasure(): string {
        return <<<HTML
<div class="policy-section">
    <h2>4. Right to Erasure ("Right to be Forgotten")</h2>
    
    <h3>What This Means</h3>
    <p>You have the right to request deletion of your personal data in certain circumstances.</p>
    
    <h3>When You Can Request Erasure</h3>
    <p>You can request deletion when:</p>
    <ul>
        <li>The data is no longer necessary for the purpose it was collected</li>
        <li>You withdraw consent and there's no other legal basis for processing</li>
        <li>You object to processing and there are no overriding legitimate grounds</li>
        <li>The data has been unlawfully processed</li>
        <li>We must delete the data to comply with a legal obligation</li>
        <li>The data was collected from a child without proper consent</li>
    </ul>
    
    <h3>When We Cannot Delete Your Data</h3>
    <p>We may refuse erasure if we need to keep your data for:</p>
    <ul>
        <li><strong>Legal Compliance:</strong> Tax records, financial reporting, audit requirements</li>
        <li><strong>Legal Claims:</strong> Establishing, exercising, or defending legal claims</li>
        <li><strong>Public Interest:</strong> Tasks in the public interest or official authority</li>
        <li><strong>Contractual Obligations:</strong> Fulfilling our contract with you</li>
        <li><strong>Vital Interests:</strong> Protecting your or others' vital interests</li>
    </ul>
    
    <h3>What Gets Deleted</h3>
    <p>When we honor an erasure request, we will:</p>
    <div class="deletion-process-box">
        <h4>🗑️ Deletion Process</h4>
        <ol>
            <li><strong>Identify Data:</strong> Locate all personal data about you across our systems</li>
            <li><strong>Anonymize:</strong> Remove personally identifiable information</li>
            <li><strong>Delete:</strong> Permanently erase data from active databases</li>
            <li><strong>Notify Third Parties:</strong> Inform data processors and recipients to delete</li>
            <li><strong>Backup Deletion:</strong> Remove from backups during next scheduled purge</li>
            <li><strong>Confirm:</strong> Provide written confirmation of deletion</li>
        </ol>
    </div>
    
    <h3>Backup Retention</h3>
    <p><strong>Important:</strong> Data in backup systems may persist for up to 90 days until the next backup cycle, after which it will be permanently deleted.</p>
    
    <h3>Account Closure</h3>
    <p>If you request deletion of your account:</p>
    <ul>
        <li>Your account will be immediately deactivated</li>
        <li>Your profile and personal information will be deleted</li>
        <li>Some data may be retained in anonymized form for analytics</li>
        <li>Transaction history may be kept for legal/financial compliance</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render right to portability section
     *
     * @return string Section content.
     */
    private function render_right_to_portability(): string {
        return <<<HTML
<div class="policy-section">
    <h2>5. Right to Data Portability</h2>
    
    <h3>What This Means</h3>
    <p>You have the right to receive your personal data in a structured, commonly used, and machine-readable format and to transmit that data to another service provider.</p>
    
    <h3>When This Right Applies</h3>
    <p>Data portability applies when:</p>
    <ul>
        <li>Processing is based on your consent or a contract</li>
        <li>Processing is carried out by automated means</li>
        <li>You want to switch to a competitor or alternative service</li>
    </ul>
    
    <h3>What Data Can Be Ported</h3>
    <p>You can request portable data including:</p>
    <div class="portable-data-box">
        <h4>📦 Exportable Data</h4>
        <ul>
            <li><strong>Profile Information:</strong> Name, email, phone, address</li>
            <li><strong>Account Data:</strong> Settings, preferences, history</li>
            <li><strong>User-Generated Content:</strong> Posts, comments, uploads</li>
            <li><strong>Transaction History:</strong> Orders, payments, invoices</li>
            <li><strong>Interaction Data:</strong> Browsing history, search queries</li>
            <li><strong>Consent Records:</strong> Cookie preferences, marketing opt-ins</li>
        </ul>
    </div>
    
    <h3>Export Formats</h3>
    <p>We provide data in the following formats:</p>
    <ul>
        <li><strong>JSON:</strong> Machine-readable, developer-friendly</li>
        <li><strong>CSV:</strong> Spreadsheet-compatible, easy to import</li>
        <li><strong>PDF:</strong> Human-readable, printable format</li>
        <li><strong>XML:</strong> Structured data for technical integrations</li>
    </ul>
    
    <h3>Direct Transfer to Another Provider</h3>
    <p>Where technically feasible, we can transmit your data directly to another service provider you specify. This requires:</p>
    <ul>
        <li>The recipient service must have compatible data import capabilities</li>
        <li>You must authorize the transfer to the specific provider</li>
        <li>We are not responsible for the recipient's handling of your data</li>
    </ul>
    
    <h3>Limitations</h3>
    <p>Data portability does not apply to:</p>
    <ul>
        <li>Inferred or derived data (e.g., analytics, insights)</li>
        <li>Data about others (e.g., communications with third parties)</li>
        <li>Proprietary algorithms or business logic</li>
        <li>Data processed purely for public interest or official authority</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render right to object section
     *
     * @return string Section content.
     */
    private function render_right_to_object(): string {
        return <<<HTML
<div class="policy-section">
    <h2>6. Right to Object to Processing</h2>
    
    <h3>What This Means</h3>
    <p>You have the right to object to certain types of processing of your personal data, particularly for direct marketing and processing based on legitimate interests.</p>
    
    <h3>Types of Objections</h3>
    
    <div class="objection-type-box">
        <h4>🚫 Objection to Direct Marketing</h4>
        <p><strong>Absolute Right</strong> - We must stop immediately</p>
        <p>You can object to:</p>
        <ul>
            <li>Email marketing and newsletters</li>
            <li>SMS/text message campaigns</li>
            <li>Targeted advertising based on your data</li>
            <li>Profiling for marketing purposes</li>
            <li>Third-party marketing communications</li>
        </ul>
        <p><strong>How to Object:</strong> Click "Unsubscribe" in emails, adjust cookie preferences, or submit an objection request</p>
    </div>
    
    <div class="objection-type-box">
        <h4>⚖️ Objection to Legitimate Interests Processing</h4>
        <p><strong>Qualified Right</strong> - We must stop unless we have compelling grounds</p>
        <p>You can object to processing based on our legitimate interests, such as:</p>
        <ul>
            <li>Analytics and website optimization</li>
            <li>Fraud prevention and security measures</li>
            <li>Network and information security</li>
            <li>Business intelligence and reporting</li>
        </ul>
        <p><strong>Our Response:</strong> We'll assess your objection and stop processing unless we can demonstrate compelling legitimate grounds that override your interests</p>
    </div>
    
    <div class="objection-type-box">
        <h4>🔬 Objection to Scientific/Historical Research</h4>
        <p><strong>Qualified Right</strong> - Unless research is in the public interest</p>
        <p>You can object to use of your data for statistical or research purposes, unless the research serves a public interest purpose</p>
    </div>
    
    <h3>What Happens After You Object</h3>
    <ol>
        <li><strong>Immediate Action:</strong> For direct marketing, we stop immediately</li>
        <li><strong>Assessment:</strong> For other objections, we evaluate within 30 days</li>
        <li><strong>Decision:</strong> We either stop processing or explain why we must continue</li>
        <li><strong>Notification:</strong> You receive confirmation of our decision</li>
    </ol>
    
    <h3>Objection vs. Withdrawal of Consent</h3>
    <p><strong>Important Distinction:</strong></p>
    <ul>
        <li><strong>Objection:</strong> Used when processing is based on legitimate interests or public interest</li>
        <li><strong>Withdrawal:</strong> Used when processing is based on your consent (see Section 7)</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render right to restrict section
     *
     * @return string Section content.
     */
    private function render_right_to_restrict(): string {
        return <<<HTML
<div class="policy-section">
    <h2>7. Right to Restrict Processing</h2>
    
    <h3>What This Means</h3>
    <p>You have the right to ask us to limit how we use your personal data without requesting full deletion.</p>
    
    <h3>When You Can Request Restriction</h3>
    <p>You can request restricted processing when:</p>
    <ul>
        <li><strong>Accuracy Challenge:</strong> You contest the accuracy of data (restriction lasts while we verify)</li>
        <li><strong>Unlawful Processing:</strong> Processing is unlawful but you prefer restriction over deletion</li>
        <li><strong>No Longer Needed:</strong> We no longer need the data but you need it for legal claims</li>
        <li><strong>Pending Objection:</strong> You've objected to processing and we're assessing legitimate grounds</li>
    </ul>
    
    <h3>What Restriction Means</h3>
    <p>When processing is restricted, we can:</p>
    <div class="restriction-box">
        <h4>✅ Allowed During Restriction</h4>
        <ul>
            <li>Store the data securely</li>
            <li>Process with your consent for specific purposes</li>
            <li>Process for legal claims establishment, exercise, or defense</li>
            <li>Process to protect rights of another person or entity</li>
            <li>Process for important public interest reasons</li>
        </ul>
    </div>
    
    <div class="restriction-box">
        <h4>❌ Not Allowed During Restriction</h4>
        <ul>
            <li>Regular business processing</li>
            <li>Sharing with third parties (except with consent)</li>
            <li>Using for marketing or analytics</li>
            <li>Including in automated decision-making</li>
        </ul>
    </div>
    
    <h3>How Long Restrictions Last</h3>
    <ul>
        <li><strong>Accuracy Disputes:</strong> Until data accuracy is verified</li>
        <li><strong>Objection Pending:</strong> Until we complete the legitimate grounds assessment</li>
        <li><strong>User-Requested:</strong> Until you authorize lifting the restriction</li>
    </ul>
    
    <h3>Notification</h3>
    <p>Before we lift a restriction, we will:</p>
    <ul>
        <li>Inform you in advance</li>
        <li>Explain the reason for lifting the restriction</li>
        <li>Give you an opportunity to object or request deletion instead</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render right to withdraw section
     *
     * @return string Section content.
     */
    private function render_right_to_withdraw(): string {
        $preferences_url = home_url('/cookie-preferences/');
        
        return <<<HTML
<div class="policy-section">
    <h2>8. Right to Withdraw Consent</h2>
    
    <h3>What This Means</h3>
    <p>Where we process your data based on your consent, you have the right to withdraw that consent at any time.</p>
    
    <h3>When This Right Applies</h3>
    <p>You can withdraw consent for:</p>
    <ul>
        <li><strong>Cookie Consent:</strong> Analytics, marketing, and preference cookies</li>
        <li><strong>Marketing Communications:</strong> Email newsletters, promotional messages</li>
        <li><strong>Optional Features:</strong> Personalization, recommendations</li>
        <li><strong>Data Sharing:</strong> Sharing with third-party partners</li>
        <li><strong>Profiling:</strong> Behavioral analysis and targeting</li>
    </ul>
    
    <h3>Easy Withdrawal Methods</h3>
    
    <div class="withdrawal-method-box">
        <h4>🍪 Cookie Consent Withdrawal</h4>
        <p>Manage or withdraw cookie consent through our <a href="{$preferences_url}">Cookie Preferences Center</a></p>
        <ul>
            <li>Toggle off any cookie categories</li>
            <li>Changes take effect immediately</li>
            <li>Blocked cookies are deleted from your browser</li>
        </ul>
    </div>
    
    <div class="withdrawal-method-box">
        <h4>📧 Email Marketing Withdrawal</h4>
        <p>Unsubscribe from marketing emails:</p>
        <ul>
            <li>Click "Unsubscribe" link in any marketing email</li>
            <li>Update preferences in your account settings</li>
            <li>Contact us directly to opt-out</li>
        </ul>
    </div>
    
    <div class="withdrawal-method-box">
        <h4>⚙️ General Consent Withdrawal</h4>
        <p>For other consents:</p>
        <ul>
            <li>Submit a withdrawal request through our DSR portal</li>
            <li>Email us with "Withdraw Consent" in the subject</li>
            <li>Specify which consent you're withdrawing</li>
        </ul>
    </div>
    
    <h3>Effect of Withdrawal</h3>
    <p>When you withdraw consent:</p>
    <ul>
        <li>✅ Processing based on that consent stops immediately</li>
        <li>✅ Your withdrawal does not affect past processing (it was lawful at the time)</li>
        <li>⚠️ Some services may no longer be available if they rely on that consent</li>
        <li>⚠️ We may still process your data if we have another legal basis (e.g., contract, legal obligation)</li>
    </ul>
    
    <h3>No Penalties</h3>
    <p>Withdrawing consent is:</p>
    <ul>
        <li>As easy as giving consent</li>
        <li>Free of charge</li>
        <li>Without negative consequences or penalties</li>
        <li>Your right under privacy law</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render automated decisions section
     *
     * @return string Section content.
     */
    private function render_automated_decisions(): string {
        return <<<HTML
<div class="policy-section">
    <h2>9. Rights Regarding Automated Decision-Making</h2>
    
    <h3>What This Means</h3>
    <p>You have the right not to be subject to decisions based solely on automated processing, including profiling, which produce legal effects or similarly significantly affect you.</p>
    
    <h3>Types of Automated Decisions</h3>
    
    <div class="automated-decision-box">
        <h4>🤖 Fully Automated Decisions (Rare)</h4>
        <p>Decisions made entirely by algorithms without human involvement, such as:</p>
        <ul>
            <li>Credit scoring or loan approval</li>
            <li>Online job application screening</li>
            <li>Automated insurance underwriting</li>
        </ul>
        <p><strong>Your Right:</strong> You can request human review and contest the decision</p>
    </div>
    
    <div class="automated-decision-box">
        <h4>📊 Profiling</h4>
        <p>Automated analysis to evaluate personal aspects, such as:</p>
        <ul>
            <li>Behavioral targeting for advertising</li>
            <li>Personalized content recommendations</li>
            <li>Fraud or risk assessment scoring</li>
        </ul>
        <p><strong>Your Right:</strong> You can object to profiling or request explanation of the logic involved</p>
    </div>
    
    <h3>Exceptions</h3>
    <p>Automated decision-making may be used if:</p>
    <ul>
        <li><strong>Necessary for Contract:</strong> Required to enter into or perform a contract with you</li>
        <li><strong>Legal Authorization:</strong> Authorized by law with appropriate safeguards</li>
        <li><strong>Your Explicit Consent:</strong> You've given explicit consent with safeguards in place</li>
    </ul>
    
    <h3>Safeguards We Implement</h3>
    <p>When we use automated decision-making, we ensure:</p>
    <ul>
        <li>Right to obtain human intervention</li>
        <li>Right to express your point of view</li>
        <li>Right to contest the decision</li>
        <li>Right to obtain an explanation of the decision</li>
        <li>Regular checks to prevent discriminatory outcomes</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render California rights section
     *
     * @return string Section content.
     */
    private function render_california_rights(): string {
        $has_ccpa = is_array($this->answers['target_countries'] ?? []) && in_array('US', $this->answers['target_countries'] ?? []);
        
        if (!$has_ccpa) {
            return '';
        }
        
        return <<<HTML
<div class="policy-section">
    <h2>10. Additional Rights for California Residents</h2>
    
    <p>If you are a California resident, you have additional rights under the California Consumer Privacy Act (CCPA) and California Privacy Rights Act (CPRA).</p>
    
    <h3>CCPA/CPRA Specific Rights</h3>
    
    <div class="ccpa-right-box">
        <h4>🔍 Right to Know</h4>
        <p>You have the right to request disclosure of:</p>
        <ul>
            <li>Categories of personal information we collected</li>
            <li>Categories of sources from which we collected it</li>
            <li>Business or commercial purpose for collection</li>
            <li>Categories of third parties with whom we share it</li>
            <li>Specific pieces of personal information we collected about you</li>
        </ul>
    </div>
    
    <div class="ccpa-right-box">
        <h4>🛑 Right to Opt-Out of Sale</h4>
        <p>You have the right to opt-out of the "sale" of your personal information. Under CCPA, "sale" includes sharing data for monetary or other valuable consideration.</p>
        <p><strong>How to Opt-Out:</strong> Click "Do Not Sell My Personal Information" in our consent banner or cookie preferences</p>
    </div>
    
    <div class="ccpa-right-box">
        <h4>🗑️ Right to Delete</h4>
        <p>You have the right to request deletion of personal information we collected from you, subject to certain exceptions.</p>
    </div>
    
    <div class="ccpa-right-box">
        <h4>🚫 Right to Non-Discrimination</h4>
        <p>We will not discriminate against you for exercising your CCPA rights, including:</p>
        <ul>
            <li>Denying goods or services</li>
            <li>Charging different prices or rates</li>
            <li>Providing different quality of goods or services</li>
            <li>Suggesting you'll receive different pricing or quality</li>
        </ul>
        <p><em>Note: We may offer financial incentives for data collection if you opt-in, but these are voluntary.</em></p>
    </div>
    
    <div class="ccpa-right-box">
        <h4>✅ Right to Correct</h4>
        <p>Under CPRA (effective 2023), you can request correction of inaccurate personal information.</p>
    </div>
    
    <div class="ccpa-right-box">
        <h4>🔒 Right to Limit Use of Sensitive Personal Information</h4>
        <p>You can limit our use of sensitive personal information (e.g., precise geolocation, health data) to only what's necessary to provide services.</p>
    </div>
    
    <h3>CCPA Request Frequency</h3>
    <p>You can make up to <strong>2 requests within a 12-month period</strong> for disclosure of information ("right to know" requests).</p>
    
    <h3>Authorized Agent</h3>
    <p>You can designate an authorized agent to submit requests on your behalf. The agent must:</p>
    <ul>
        <li>Provide written authorization from you</li>
        <li>Verify their identity and authority</li>
        <li>We may still require you to verify your identity directly</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render how to exercise section
     *
     * @return string Section content.
     */
    private function render_how_to_exercise(): string {
        $dsr_portal_url = home_url('/dsr-portal/');
        $contact_email = $this->answers['contact_email'] ?? '';
        
        return <<<HTML
<div class="policy-section">
    <h2>11. How to Exercise Your Rights</h2>
    
    <p>We've made it easy to exercise your data privacy rights through multiple channels.</p>
    
    <h3>Method 1: Data Subject Request (DSR) Portal (Recommended)</h3>
    <div class="dsr-portal-promo">
        <h4>🔐 Secure Online Portal</h4>
        <p>Our DSR portal provides the fastest and most secure way to submit requests:</p>
        <p><a href="{$dsr_portal_url}" class="btn-primary">Access DSR Portal</a></p>
        
        <p><strong>Benefits:</strong></p>
        <ul>
            <li>✅ Submit requests 24/7</li>
            <li>✅ Track request status in real-time</li>
            <li>✅ Secure identity verification</li>
            <li>✅ Encrypted document delivery</li>
            <li>✅ Request history and audit trail</li>
        </ul>
    </div>
    
    <h3>Method 2: Email</h3>
    <p>Send your request to: <a href="mailto:{$contact_email}">{$contact_email}</a></p>
    <p><strong>Include in your email:</strong></p>
    <ul>
        <li>Subject line: "Data Privacy Rights Request"</li>
        <li>Your full name and email address</li>
        <li>Type of request (access, deletion, rectification, etc.)</li>
        <li>Specific details about your request</li>
        <li>Proof of identity (see verification section)</li>
    </ul>
    
    <h3>Method 3: Postal Mail</h3>
    <p>Send written requests to our registered office address (see Contact section).</p>
    
    <h3>Method 4: In-Person</h3>
    <p>Visit our office during business hours with valid ID to submit a request in person.</p>
    
    <h3>What to Include in Your Request</h3>
    <div class="request-checklist">
        <h4>📋 Request Checklist</h4>
        <ul>
            <li>✅ Your full name</li>
            <li>✅ Email address or account username</li>
            <li>✅ Type of request (be specific)</li>
            <li>✅ Description of data (for access/deletion requests)</li>
            <li>✅ Preferred format for data delivery (if applicable)</li>
            <li>✅ Proof of identity (see verification requirements)</li>
            <li>✅ Any account numbers or reference numbers</li>
        </ul>
    </div>
</div>
HTML;
    }

    /**
     * Render DSR portal section
     *
     * @return string Section content.
     */
    private function render_dsr_portal(): string {
        $dsr_portal_url = home_url('/dsr-portal/');
        
        return <<<HTML
<div class="policy-section">
    <h2>12. Using the DSR Portal</h2>
    
    <p>Our Data Subject Request (DSR) Portal is a secure, user-friendly platform for managing your privacy rights.</p>
    
    <h3>Portal Features</h3>
    
    <div class="portal-feature-box">
        <h4>🎯 Request Submission</h4>
        <p>Submit any type of privacy request:</p>
        <ul>
            <li>Access your data</li>
            <li>Delete your data</li>
            <li>Correct inaccurate data</li>
            <li>Export your data (portability)</li>
            <li>Object to processing</li>
            <li>Restrict processing</li>
            <li>Withdraw consent</li>
        </ul>
    </div>
    
    <div class="portal-feature-box">
        <h4>📊 Request Tracking</h4>
        <ul>
            <li>Real-time status updates</li>
            <li>Estimated completion date</li>
            <li>Notification when request is fulfilled</li>
            <li>View request history</li>
        </ul>
    </div>
    
    <div class="portal-feature-box">
        <h4>🔒 Secure Delivery</h4>
        <ul>
            <li>Encrypted document storage</li>
            <li>Password-protected downloads</li>
            <li>Automatic expiration after 30 days</li>
            <li>Download confirmation</li>
        </ul>
    </div>
    
    <h3>Step-by-Step Guide</h3>
    <ol>
        <li><strong>Visit Portal:</strong> Go to <a href="{$dsr_portal_url}">{$dsr_portal_url}</a></li>
        <li><strong>Choose Request Type:</strong> Select from the request options</li>
        <li><strong>Fill Form:</strong> Provide required information and details</li>
        <li><strong>Verify Identity:</strong> Complete identity verification</li>
        <li><strong>Submit:</strong> Review and submit your request</li>
        <li><strong>Track:</strong> Use your unique request ID to check status</li>
        <li><strong>Receive:</strong> Get notified when request is complete</li>
        <li><strong>Download:</strong> Access your data securely through the portal</li>
    </ol>
    
    <h3>Portal Security</h3>
    <p>The DSR portal uses enterprise-grade security:</p>
    <ul>
        <li>🔐 SSL/TLS encryption for all transmissions</li>
        <li>🔑 Multi-factor authentication options</li>
        <li>🛡️ DDoS protection and rate limiting</li>
        <li>📝 Complete audit trail of all actions</li>
        <li>🗄️ SOC 2 compliant data storage</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render response timeline section
     *
     * @return string Section content.
     */
    private function render_response_timeline(): string {
        return <<<HTML
<div class="policy-section">
    <h2>13. Response Timeline</h2>
    
    <p>We are committed to responding to your requests promptly and within legal timeframes.</p>
    
    <h3>Standard Response Times</h3>
    
    <div class="timeline-box">
        <h4>📅 GDPR/LGPD Requests</h4>
        <ul>
            <li><strong>Standard Response:</strong> Within 1 month (30 days)</li>
            <li><strong>Complex Requests:</strong> May extend by 2 additional months (total 3 months)</li>
            <li><strong>Extension Notice:</strong> You'll be informed within the first month if extension is needed</li>
        </ul>
    </div>
    
    <div class="timeline-box">
        <h4>📅 CCPA Requests (California)</h4>
        <ul>
            <li><strong>Acknowledgment:</strong> Within 10 business days</li>
            <li><strong>Standard Response:</strong> Within 45 calendar days</li>
            <li><strong>Extension:</strong> May extend by additional 45 days (total 90 days)</li>
            <li><strong>Reason for Extension:</strong> Must explain why extension is necessary</li>
        </ul>
    </div>
    
    <div class="timeline-box">
        <h4>⚡ Priority Handling</h4>
        <p>Certain requests receive expedited processing:</p>
        <ul>
            <li><strong>Data Breach Victims:</strong> 72 hours</li>
            <li><strong>Security Concerns:</strong> 1-3 business days</li>
            <li><strong>Minors' Data:</strong> Within 15 days</li>
            <li><strong>Simple Corrections:</strong> 1-5 business days</li>
        </ul>
    </div>
    
    <h3>Our Process</h3>
    <ol>
        <li><strong>Day 1:</strong> Request received and logged</li>
        <li><strong>Days 1-3:</strong> Identity verification</li>
        <li><strong>Days 4-10:</strong> Request assessment and data gathering</li>
        <li><strong>Days 11-25:</strong> Data preparation and review</li>
        <li><strong>Days 26-30:</strong> Quality check and delivery</li>
    </ol>
    
    <h3>Status Notifications</h3>
    <p>You'll receive updates at key milestones:</p>
    <ul>
        <li>✉️ Acknowledgment within 2 business days</li>
        <li>✉️ Verification complete notification</li>
        <li>✉️ Midpoint status update (for complex requests)</li>
        <li>✉️ Completion and delivery notification</li>
    </ul>
    
    <h3>If We Need More Time</h3>
    <p>If we need an extension, we'll:</p>
    <ul>
        <li>Notify you before the initial deadline</li>
        <li>Explain why the extension is necessary</li>
        <li>Provide a new estimated completion date</li>
        <li>Keep you updated on progress</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render verification section
     *
     * @return string Section content.
     */
    private function render_verification(): string {
        return <<<HTML
<div class="policy-section">
    <h2>14. Identity Verification</h2>
    
    <p>To protect your privacy, we must verify your identity before fulfilling data rights requests.</p>
    
    <h3>Why Verification Is Necessary</h3>
    <p>Identity verification protects you from:</p>
    <ul>
        <li>Fraudulent access to your personal data</li>
        <li>Identity theft and impersonation</li>
        <li>Unauthorized data disclosure</li>
        <li>Malicious deletion requests</li>
    </ul>
    
    <h3>Verification Methods</h3>
    
    <div class="verification-method-box">
        <h4>📧 Email Verification (For Account Holders)</h4>
        <p><strong>Suitable for:</strong> Access requests, rectification, portability</p>
        <p><strong>Process:</strong></p>
        <ol>
            <li>You submit request using registered email</li>
            <li>We send verification code to that email</li>
            <li>You enter code to confirm identity</li>
            <li>Request is processed</li>
        </ol>
    </div>
    
    <div class="verification-method-box">
        <h4>🔐 Multi-Factor Authentication (For Sensitive Requests)</h4>
        <p><strong>Suitable for:</strong> Deletion requests, data portability</p>
        <p><strong>Process:</strong></p>
        <ol>
            <li>Login to your account</li>
            <li>Complete 2FA via SMS or authenticator app</li>
            <li>Submit request from authenticated session</li>
        </ol>
    </div>
    
    <div class="verification-method-box">
        <h4>📄 Document Verification (For Non-Account Holders)</h4>
        <p><strong>Suitable for:</strong> Any request from individuals without accounts</p>
        <p><strong>Required Documents (1 from each category):</strong></p>
        <p><strong>Category A - Photo ID:</strong></p>
        <ul>
            <li>Government-issued ID (driver's license, passport)</li>
            <li>National ID card</li>
        </ul>
        <p><strong>Category B - Proof of Association:</strong></p>
        <ul>
            <li>Recent transaction confirmation</li>
            <li>Account statement or invoice</li>
            <li>Previous correspondence with us</li>
        </ul>
        <p><strong>How to Submit:</strong> Upload securely through DSR portal or send encrypted email</p>
    </div>
    
    <div class="verification-method-box">
        <h4>📞 Phone Verification</h4>
        <p><strong>Suitable for:</strong> Simple requests, supplementary verification</p>
        <p><strong>Process:</strong></p>
        <ol>
            <li>Call from phone number on file</li>
            <li>Answer security questions</li>
            <li>Confirm request details</li>
        </ol>
    </div>
    
    <h3>Verification Data Retention</h3>
    <p>Information used for verification is:</p>
    <ul>
        <li>Used only for identity verification purposes</li>
        <li>Retained for 90 days to prevent repeat fraud attempts</li>
        <li>Stored separately from your main account data</li>
        <li>Encrypted at rest and in transit</li>
        <li>Securely deleted after retention period</li>
    </ul>
    
    <h3>If Verification Fails</h3>
    <p>If we cannot verify your identity:</p>
    <ul>
        <li>We'll explain what additional information is needed</li>
        <li>You have 30 days to provide additional verification</li>
        <li>We may suggest alternative verification methods</li>
        <li>Request will be denied if verification cannot be completed</li>
        <li>You can appeal the denial (see Complaints section)</li>
    </ul>
    
    <h3>Authorized Agent Verification</h3>
    <p>If someone submits a request on your behalf, they must provide:</p>
    <ul>
        <li>Written authorization signed by you</li>
        <li>Their own identification</li>
        <li>Proof of their relationship to you (if applicable)</li>
        <li>You may still need to verify your identity directly</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render complaints section
     *
     * @return string Section content.
     */
    private function render_complaints(): string {
        return <<<HTML
<div class="policy-section">
    <h2>15. Filing Complaints & Appeals</h2>
    
    <p>If you're not satisfied with how we've handled your data rights request, you have options for recourse.</p>
    
    <h3>Internal Appeals</h3>
    <p>If you disagree with our decision on your request:</p>
    <ol>
        <li><strong>Contact Us:</strong> Email us within 30 days explaining your concerns</li>
        <li><strong>Review:</strong> A senior privacy officer will review the decision</li>
        <li><strong>Response:</strong> You'll receive a response within 30 days</li>
        <li><strong>Escalation:</strong> If still not satisfied, you can escalate to our Data Protection Officer</li>
    </ol>
    
    <h3>Supervisory Authority Complaints</h3>
    <p>You have the right to lodge a complaint with your local data protection authority:</p>
    
    <div class="authority-box">
        <h4>🇪🇺 European Union</h4>
        <p>Contact your national Data Protection Authority:</p>
        <ul>
            <li><strong>Find Your DPA:</strong> <a href="https://edpb.europa.eu/about-edpb/board/members_en" target="_blank">EDPB Member List</a></li>
            <li><strong>Lead Authority:</strong> Authority in your country of residence</li>
        </ul>
    </div>
    
    <div class="authority-box">
        <h4>🇬🇧 United Kingdom</h4>
        <p><strong>Information Commissioner's Office (ICO)</strong></p>
        <p>Website: <a href="https://ico.org.uk/make-a-complaint/" target="_blank">ico.org.uk</a></p>
        <p>Phone: 0303 123 1113</p>
    </div>
    
    <div class="authority-box">
        <h4>🇺🇸 California (CCPA)</h4>
        <p><strong>California Attorney General's Office</strong></p>
        <p>Website: <a href="https://oag.ca.gov/privacy/ccpa" target="_blank">oag.ca.gov/privacy/ccpa</a></p>
        <p>Consumer Hotline: (916) 210-6276</p>
    </div>
    
    <div class="authority-box">
        <h4>🇧🇷 Brazil (LGPD)</h4>
        <p><strong>Autoridade Nacional de Proteção de Dados (ANPD)</strong></p>
        <p>Website: <a href="https://www.gov.br/anpd/" target="_blank">gov.br/anpd</a></p>
    </div>
    
    <div class="authority-box">
        <h4>🇨🇦 Canada (PIPEDA)</h4>
        <p><strong>Office of the Privacy Commissioner of Canada</strong></p>
        <p>Website: <a href="https://www.priv.gc.ca/en/report-a-concern/" target="_blank">priv.gc.ca</a></p>
        <p>Phone: 1-800-282-1376</p>
    </div>
    
    <h3>What Authorities Can Do</h3>
    <p>Data protection authorities have powers to:</p>
    <ul>
        <li>Investigate your complaint</li>
        <li>Order us to take specific actions</li>
        <li>Issue warnings or reprimands</li>
        <li>Impose fines for non-compliance</li>
        <li>Ban processing activities</li>
    </ul>
    
    <h3>Legal Action</h3>
    <p>You also have the right to seek judicial remedy if you believe your rights have been infringed.</p>
</div>
HTML;
    }

    /**
     * Render contact section
     *
     * @return string Section content.
     */
    private function render_contact(): string {
        $company = $this->answers['company_name'] ?? 'Our Company';
        $email = $this->answers['contact_email'] ?? '';
        $has_dpo = !empty($this->answers['has_dpo']) && $this->answers['has_dpo'] === true;
        $dpo_contact = $this->answers['dpo_contact'] ?? '';
        $dsr_portal_url = home_url('/dsr-portal/');
        
        $contact = <<<HTML
<div class="policy-section">
    <h2>16. Contact Information</h2>
    
    <p>For questions about your data rights or to submit a request, please contact us:</p>
    
    <div class="contact-box">
        <h3>Privacy Rights Requests</h3>
        <p><strong>Preferred Method:</strong> <a href="{$dsr_portal_url}">DSR Portal</a> (fastest and most secure)</p>
HTML;

        if ($email) {
            $contact .= "<p><strong>Email:</strong> <a href=\"mailto:{$email}\">{$email}</a></p>";
        }
        
        if ($has_dpo && $dpo_contact) {
            $contact .= <<<HTML
        
        <h3>Data Protection Officer</h3>
        <p>Our Data Protection Officer oversees privacy compliance and data subject rights:</p>
        <p><strong>Contact:</strong> {$dpo_contact}</p>
HTML;
        }
        
        $contact .= <<<HTML
    </div>
    
    <h3>Quick Reference</h3>
    <table style="width:100%; border-collapse: collapse; margin-top: 15px;">
        <tr style="background: #f8fafc;">
            <th style="padding: 10px; text-align: left; border: 1px solid #e2e8f0;">Request Type</th>
            <th style="padding: 10px; text-align: left; border: 1px solid #e2e8f0;">Best Method</th>
            <th style="padding: 10px; text-align: left; border: 1px solid #e2e8f0;">Response Time</th>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Access Your Data</td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;"><a href="{$dsr_portal_url}">DSR Portal</a></td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">30 days</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Delete Your Data</td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;"><a href="{$dsr_portal_url}">DSR Portal</a></td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">30 days</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Correct Inaccurate Data</td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Account Settings or <a href="{$dsr_portal_url}">DSR Portal</a></td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">1-5 days</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Withdraw Cookie Consent</td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;"><a href="/cookie-preferences/">Cookie Preferences</a></td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Immediate</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Unsubscribe from Marketing</td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">Email unsubscribe link</td>
            <td style="padding: 10px; border: 1px solid #e2e8f0;">1-2 days</td>
        </tr>
    </table>
    
    <h3>Related Policies</h3>
    <ul>
        <li><a href="/privacy-policy/">Privacy Policy</a> - How we collect and use your data</li>
        <li><a href="/cookie-policy/">Cookie Policy</a> - Information about cookies we use</li>
        <li><a href="/data-protection-policy/">Data Protection Policy</a> - Our data security measures</li>
        <li><a href="/consent-management-policy/">Consent Management Policy</a> - How we manage your consent</li>
    </ul>
</div>
HTML;
        
        return $contact;
    }
}
