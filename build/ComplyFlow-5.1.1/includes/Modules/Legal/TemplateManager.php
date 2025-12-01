<?php
/**
 * Template Manager
 *
 * Manages policy templates and snippets.
 *
 * @package ComplyFlow\Modules\Legal
 * @since   2.0.1
 */

namespace ComplyFlow\Modules\Legal;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class TemplateManager
 */
class TemplateManager {
    /**
     * Templates directory
     *
     * @var string
     */
    private string $templates_dir;

    /**
     * Snippets directory
     *
     * @var string
     */
    private string $snippets_dir;

    /**
     * Constructor
     */
    public function __construct() {
        $this->templates_dir = COMPLYFLOW_PATH . 'templates/policies/';
        $this->snippets_dir = COMPLYFLOW_PATH . 'templates/policies/snippets/';
    }

    /**
     * Get template content
     *
     * @param string $template_name Template name.
     * @return string Template content.
     */
    public function get_template(string $template_name): string {
        $file = $this->templates_dir . $template_name . '.php';

        if (!file_exists($file)) {
            return $this->get_default_template($template_name);
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Get snippet content
     *
     * @param string $snippet_name Snippet name.
     * @return string Snippet content.
     */
    public function get_snippet(string $snippet_name): string {
        $file = $this->snippets_dir . $snippet_name . '.php';

        if (!file_exists($file)) {
            return '';
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Get default template
     *
     * @param string $template_name Template name.
     * @return string Default template content.
     */
    private function get_default_template(string $template_name): string {
        return match ($template_name) {
            'privacy-policy' => $this->get_default_privacy_policy(),
            'terms-of-service' => $this->get_default_terms(),
            'cookie-policy' => $this->get_default_cookie_policy(),
            'data-protection-policy' => $this->get_default_data_protection(),
            default => '',
        };
    }

    /**
     * Get default privacy policy template
     *
     * @return string Template content.
     */
    private function get_default_privacy_policy(): string {
        return <<<'HTML'
<h1>Privacy Policy</h1>

<p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>

<h2>1. Introduction</h2>
<p>{{BUSINESS_NAME}} ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>

<h2>2. Information We Collect</h2>
{{DATA_COLLECTION_SECTION}}

<h2>3. Cookies and Tracking Technologies</h2>
{{COOKIES_SECTION}}

<h2>4. How We Use Your Information</h2>
<p>We use the information we collect to:</p>
<ul>
    <li>Provide, operate, and maintain our website</li>
    <li>Improve, personalize, and expand our website</li>
    <li>Understand and analyze how you use our website</li>
    <li>Develop new products, services, features, and functionality</li>
    <li>Communicate with you for customer service and updates</li>
    <li>Send you marketing and promotional communications</li>
    <li>Process your transactions</li>
    <li>Find and prevent fraud</li>
</ul>

<h2>5. Data Retention</h2>
{{DATA_RETENTION_SECTION}}

<h2>6. International Data Transfers</h2>
{{INTERNATIONAL_TRANSFERS_SECTION}}

<h2>7. Your Rights</h2>
{{USER_RIGHTS_SECTION}}

<h2>8. Children's Privacy</h2>
{{CHILDREN_SECTION}}

<h2>9. Changes to This Policy</h2>
<p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Effective Date" above.</p>

<h2>10. Contact Us</h2>
<p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
<ul>
    <li><strong>Email:</strong> {{CONTACT_EMAIL}}</li>
    <li><strong>Phone:</strong> {{CONTACT_PHONE}}</li>
    <li><strong>Address:</strong> {{BUSINESS_ADDRESS}}</li>
</ul>
HTML;
    }

    /**
     * Get default terms of service template
     *
     * @return string Template content.
     */
    private function get_default_terms(): string {
        return <<<'HTML'
<h1>Terms of Service</h1>

<p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>

<h2>1. Acceptance of Terms</h2>
<p>By accessing and using {{BUSINESS_NAME}} ("the Website"), you accept and agree to be bound by the terms and provisions of this agreement.</p>

<h2>2. Use License</h2>
<p>Permission is granted to temporarily access the materials on {{BUSINESS_NAME}}'s website for personal, non-commercial transitory viewing only.</p>

<h2>3. User Conduct</h2>
{{USER_CONDUCT_SECTION}}

{{ECOMMERCE_SECTION}}

<h2>4. Intellectual Property</h2>
<p>The content, organization, graphics, design, compilation, and other matters related to {{BUSINESS_NAME}} are protected under applicable copyrights, trademarks, and other proprietary rights.</p>

<h2>5. Limitation of Liability</h2>
{{LIMITATION_LIABILITY_SECTION}}

<h2>6. Governing Law</h2>
<p>These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which {{BUSINESS_NAME}} operates.</p>

<h2>7. Changes to Terms</h2>
<p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting to the website.</p>

<h2>8. Contact Information</h2>
<p>For questions about these Terms of Service, please contact us at {{CONTACT_EMAIL}}.</p>
HTML;
    }

    /**
     * Get default cookie policy template
     *
     * @return string Template content.
     */
    private function get_default_cookie_policy(): string {
        return <<<'HTML'
<h1>Cookie Policy</h1>

<p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>

<h2>1. What Are Cookies?</h2>
<p>Cookies are small text files that are placed on your computer or mobile device when you visit a website. They are widely used to make websites work more efficiently and provide information to website owners.</p>

<h2>2. How We Use Cookies</h2>
<p>{{BUSINESS_NAME}} uses cookies to:</p>
<ul>
    <li>Remember your preferences and settings</li>
    <li>Understand how you use our website</li>
    <li>Improve your user experience</li>
    <li>Deliver relevant advertising</li>
    <li>Analyze website traffic and performance</li>
</ul>

<h2>3. Types of Cookies We Use</h2>
{{COOKIE_TABLE}}

<h2>4. Managing Cookies</h2>
{{COOKIE_MANAGEMENT_SECTION}}

<h2>5. Third-Party Cookies</h2>
<p>Some cookies on our website are set by third-party services that appear on our pages. We do not control these cookies, and you should check the third-party websites for more information about these cookies.</p>

<h2>6. Contact Us</h2>
<p>If you have questions about our use of cookies, please contact us at {{CONTACT_EMAIL}}.</p>
HTML;
    }

    /**
     * Get default data protection policy template
     *
     * @return string Template content.
     */
    private function get_default_data_protection(): string {
        return <<<'HTML'
<h1>Data Protection Policy</h1>

<p><strong>Effective Date:</strong> {{EFFECTIVE_DATE}}</p>

<h2>1. Introduction</h2>
<p>{{BUSINESS_NAME}} is committed to protecting your personal data in accordance with applicable data protection laws and regulations.</p>

{{GDPR_SECTION}}

{{CCPA_SECTION}}

{{LGPD_SECTION}}

<h2>2. Your Rights</h2>
{{DATA_SUBJECT_RIGHTS}}

<h2>3. Data Security</h2>
<p>We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>4. Data Breaches</h2>
<p>In the event of a data breach that may affect your personal data, we will notify you and relevant authorities as required by applicable law.</p>

<h2>5. Contact Information</h2>
<p>For any data protection inquiries, please contact us at:</p>
<ul>
    <li><strong>Email:</strong> {{CONTACT_EMAIL}}</li>
    <li><strong>Phone:</strong> {{CONTACT_PHONE}}</li>
</ul>
HTML;
    }
}
