<?php
/**
 * Consent Management Policy Generator
 *
 * Generates comprehensive consent management policy explaining how the consent system works.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   4.9.0
 */

namespace ComplyFlow\Modules\Documents;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ConsentManagementPolicyGenerator
 */
class ConsentManagementPolicyGenerator {
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
     * Generate consent management policy
     *
     * @return string Generated policy HTML.
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
            '{{HOW_CONSENT_WORKS_SECTION}}' => $this->render_how_consent_works(),
            '{{COOKIE_CATEGORIES_SECTION}}' => $this->render_cookie_categories(),
            '{{CONSENT_BANNER_SECTION}}' => $this->render_consent_banner(),
            '{{PREFERENCES_CENTER_SECTION}}' => $this->render_preferences_center(),
            '{{WITHDRAWING_CONSENT_SECTION}}' => $this->render_withdrawing_consent(),
            '{{CONSENT_STORAGE_SECTION}}' => $this->render_consent_storage(),
            '{{GEO_TARGETING_SECTION}}' => $this->render_geo_targeting(),
            '{{SCRIPT_BLOCKING_SECTION}}' => $this->render_script_blocking(),
            '{{CONSENT_LOGGING_SECTION}}' => $this->render_consent_logging(),
            '{{COMPLIANCE_MODES_SECTION}}' => $this->render_compliance_modes(),
            '{{UPDATES_SECTION}}' => $this->render_updates(),
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
        $template_path = COMPLYFLOW_PATH . 'templates/policies/consent-management-policy-template.php';
        
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
        
        return <<<HTML
<div class="policy-section">
    <h2>1. Overview</h2>
    <p>This Consent Management Policy explains how {$company} manages your consent for the use of cookies and similar tracking technologies on our website. Our consent management system is designed to comply with multiple privacy frameworks including GDPR, CCPA, LGPD, and other applicable regulations.</p>
    
    <h3>What This Policy Covers</h3>
    <ul>
        <li>How our consent banner works</li>
        <li>The types of cookies we use and require consent for</li>
        <li>How to manage your cookie preferences</li>
        <li>How we store and process your consent choices</li>
        <li>Your rights regarding consent and how to exercise them</li>
    </ul>
    
    <p><strong>Key Principle:</strong> We believe in transparent, user-controlled consent. You have full control over which non-essential cookies we can use, and you can change your preferences at any time.</p>
</div>
HTML;
    }

    /**
     * Render how consent works section
     *
     * @return string Section content.
     */
    private function render_how_consent_works(): string {
        return <<<HTML
<div class="policy-section">
    <h2>2. How Our Consent System Works</h2>
    
    <h3>First Visit</h3>
    <p>When you first visit our website, you will see a consent banner that explains our use of cookies and similar technologies. This banner provides you with options to:</p>
    <ul>
        <li><strong>Accept All:</strong> Consent to all cookie categories</li>
        <li><strong>Reject All:</strong> Decline all non-essential cookies (only necessary cookies will be used)</li>
        <li><strong>Customize:</strong> Choose specific cookie categories based on your preferences</li>
    </ul>
    
    <h3>Consent Mechanisms</h3>
    <p>Depending on your location and applicable privacy laws, our consent system operates in different modes:</p>
    
    <div class="consent-mode-box">
        <h4>🇪🇺 GDPR Mode (EU/EEA Visitors)</h4>
        <p><strong>Type:</strong> Opt-In (Explicit Consent Required)</p>
        <p>Non-essential cookies are blocked by default until you provide explicit consent. This complies with GDPR Article 6 and the ePrivacy Directive.</p>
    </div>
    
    <div class="consent-mode-box">
        <h4>🇺🇸 CCPA Mode (California Visitors)</h4>
        <p><strong>Type:</strong> Opt-Out (Notice & Choice)</p>
        <p>Cookies may be used unless you opt-out. We provide a "Do Not Sell My Personal Information" option in compliance with CCPA requirements.</p>
    </div>
    
    <div class="consent-mode-box">
        <h4>🇧🇷 LGPD Mode (Brazil Visitors)</h4>
        <p><strong>Type:</strong> Opt-In (Explicit Consent Required)</p>
        <p>Similar to GDPR, we require your explicit consent before using non-essential cookies, in compliance with Brazil's LGPD.</p>
    </div>
    
    <h3>Automatic Geo-Detection</h3>
    <p>Our system automatically detects your geographic location to apply the appropriate consent framework. This ensures you receive the highest level of privacy protection mandated by your local laws.</p>
</div>
HTML;
    }

    /**
     * Render cookie categories section
     *
     * @return string Section content.
     */
    private function render_cookie_categories(): string {
        return <<<HTML
<div class="policy-section">
    <h2>3. Cookie Categories</h2>
    <p>We organize cookies into four categories, giving you granular control over your preferences:</p>
    
    <div class="cookie-category-detail">
        <h3>🔒 Necessary Cookies (Always Active)</h3>
        <p><strong>Consent Required:</strong> No</p>
        <p><strong>Purpose:</strong> Essential for website functionality</p>
        <p>These cookies are required for the website to function properly and cannot be disabled. They enable core features such as:</p>
        <ul>
            <li>User authentication and session management</li>
            <li>Security and fraud prevention</li>
            <li>Shopping cart functionality (if applicable)</li>
            <li>Load balancing and performance optimization</li>
            <li>Consent preference storage</li>
        </ul>
        <p><strong>Examples:</strong> wordpress_logged_in_*, wp-settings-*, woocommerce_cart_hash, complyflow_consent</p>
    </div>
    
    <div class="cookie-category-detail">
        <h3>📊 Analytics Cookies (Opt-In Required)</h3>
        <p><strong>Consent Required:</strong> Yes</p>
        <p><strong>Purpose:</strong> Help us understand visitor behavior</p>
        <p>These cookies collect information about how visitors use our website, helping us improve user experience:</p>
        <ul>
            <li>Pages visited and time spent on each page</li>
            <li>Referring websites and search terms used</li>
            <li>Browser and device information</li>
            <li>Geographic location (country/city level)</li>
            <li>Click patterns and navigation paths</li>
        </ul>
        <p><strong>Examples:</strong> _ga, _gid, _gat (Google Analytics)</p>
        <p><strong>Data Sharing:</strong> Data may be shared with analytics service providers (e.g., Google Analytics)</p>
    </div>
    
    <div class="cookie-category-detail">
        <h3>🎯 Marketing Cookies (Opt-In Required)</h3>
        <p><strong>Consent Required:</strong> Yes</p>
        <p><strong>Purpose:</strong> Deliver personalized advertisements</p>
        <p>These cookies are used to show you relevant ads based on your interests:</p>
        <ul>
            <li>Track your browsing across websites</li>
            <li>Build advertising profiles</li>
            <li>Measure ad campaign effectiveness</li>
            <li>Frequency capping (limit ad repetition)</li>
            <li>Retargeting and remarketing</li>
        </ul>
        <p><strong>Examples:</strong> _fbp (Facebook Pixel), IDE (Google DoubleClick), fr (Facebook)</p>
        <p><strong>Data Sharing:</strong> Data may be shared with advertising networks and social media platforms</p>
    </div>
    
    <div class="cookie-category-detail">
        <h3>⚙️ Preferences Cookies (Opt-In Required)</h3>
        <p><strong>Consent Required:</strong> Yes</p>
        <p><strong>Purpose:</strong> Remember your preferences and settings</p>
        <p>These cookies enhance your experience by remembering your choices:</p>
        <ul>
            <li>Language and region preferences</li>
            <li>Display settings (font size, theme)</li>
            <li>Video/audio player settings</li>
            <li>Form auto-fill information</li>
            <li>Recently viewed items</li>
        </ul>
        <p><strong>Examples:</strong> PREF (YouTube preferences), YSC (YouTube session)</p>
    </div>
</div>
HTML;
    }

    /**
     * Render consent banner section
     *
     * @return string Section content.
     */
    private function render_consent_banner(): string {
        return <<<HTML
<div class="policy-section">
    <h2>4. Consent Banner</h2>
    
    <h3>When You See the Banner</h3>
    <p>The consent banner appears automatically when:</p>
    <ul>
        <li>You visit our website for the first time</li>
        <li>You have cleared your browser cookies</li>
        <li>Your previous consent has expired (after 12 months)</li>
        <li>We have made significant changes to our cookie usage</li>
    </ul>
    
    <h3>Banner Design & Placement</h3>
    <p>Our consent banner is designed to be:</p>
    <ul>
        <li><strong>Non-intrusive:</strong> Positioned to not block essential content</li>
        <li><strong>Clear:</strong> Plain language explanation of cookie usage</li>
        <li><strong>Accessible:</strong> Keyboard navigable and screen reader compatible</li>
        <li><strong>Mobile-responsive:</strong> Optimized for all screen sizes</li>
    </ul>
    
    <h3>Your Choices</h3>
    <p>The banner provides three main actions:</p>
    
    <div class="consent-action-box">
        <h4>✅ Accept All Cookies</h4>
        <p>Clicking this button means you consent to all cookie categories (Necessary, Analytics, Marketing, and Preferences). This is the fastest way to continue browsing if you're comfortable with all cookies.</p>
    </div>
    
    <div class="consent-action-box">
        <h4>❌ Reject All Cookies</h4>
        <p>Clicking this button means you decline all non-essential cookies. Only Necessary cookies will be used. Some features may be limited.</p>
    </div>
    
    <div class="consent-action-box">
        <h4>⚙️ Customize Preferences</h4>
        <p>Opens detailed settings where you can toggle each cookie category individually. This gives you precise control over your privacy preferences.</p>
    </div>
    
    <h3>After Making Your Choice</h3>
    <p>Once you make a selection:</p>
    <ul>
        <li>Your choice is immediately saved in a consent cookie</li>
        <li>The banner disappears and won't reappear for 12 months</li>
        <li>Scripts are blocked or unblocked according to your preferences</li>
        <li>Your consent is logged for compliance purposes (anonymized)</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render preferences center section
     *
     * @return string Section content.
     */
    private function render_preferences_center(): string {
        $preferences_url = home_url('/cookie-preferences/');
        
        return <<<HTML
<div class="policy-section">
    <h2>5. Cookie Preferences Center</h2>
    
    <p>You can manage your cookie preferences at any time through our dedicated Cookie Preferences Center:</p>
    
    <div class="preferences-link-box">
        <p><strong>🔗 Access Your Preferences:</strong></p>
        <p><a href="{$preferences_url}" class="btn-primary">Manage Cookie Preferences</a></p>
    </div>
    
    <h3>Features of the Preferences Center</h3>
    
    <h4>📊 View Current Consent Status</h4>
    <p>See which cookie categories are currently enabled or disabled for your browser.</p>
    
    <h4>🔄 Toggle Cookie Categories</h4>
    <p>Use simple on/off switches to enable or disable each cookie category:</p>
    <ul>
        <li>Analytics Cookies - ON/OFF</li>
        <li>Marketing Cookies - ON/OFF</li>
        <li>Preferences Cookies - ON/OFF</li>
        <li>Necessary Cookies - Always ON (cannot be disabled)</li>
    </ul>
    
    <h4>📋 View Cookie Details</h4>
    <p>See detailed information about each cookie we use:</p>
    <ul>
        <li>Cookie name and provider</li>
        <li>Purpose and description</li>
        <li>Expiration period</li>
        <li>Category classification</li>
    </ul>
    
    <h4>💾 Save Your Preferences</h4>
    <p>Your changes take effect immediately and are saved for 12 months. You'll see a confirmation message after saving.</p>
    
    <h3>Quick Actions</h3>
    <p>The Preferences Center also provides quick action buttons:</p>
    <ul>
        <li><strong>Accept All:</strong> Enable all cookie categories</li>
        <li><strong>Reject All:</strong> Disable all non-essential cookies</li>
        <li><strong>Save Preferences:</strong> Apply your custom settings</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render withdrawing consent section
     *
     * @return string Section content.
     */
    private function render_withdrawing_consent(): string {
        $preferences_url = home_url('/cookie-preferences/');
        
        return <<<HTML
<div class="policy-section">
    <h2>6. Withdrawing or Changing Consent</h2>
    
    <p>You have the right to withdraw or modify your consent at any time. Here's how:</p>
    
    <h3>Method 1: Use Our Preferences Center</h3>
    <p>The easiest way is to visit our Cookie Preferences Center:</p>
    <ol>
        <li>Go to <a href="{$preferences_url}">{$preferences_url}</a></li>
        <li>Toggle the cookie categories you want to change</li>
        <li>Click "Save Preferences"</li>
        <li>Your changes take effect immediately</li>
    </ol>
    
    <h3>Method 2: Delete Browser Cookies</h3>
    <p>You can delete all cookies through your browser settings:</p>
    <ul>
        <li><strong>Chrome:</strong> Settings → Privacy and Security → Clear browsing data</li>
        <li><strong>Firefox:</strong> Settings → Privacy & Security → Cookies and Site Data → Clear Data</li>
        <li><strong>Safari:</strong> Preferences → Privacy → Manage Website Data → Remove All</li>
        <li><strong>Edge:</strong> Settings → Privacy, search, and services → Clear browsing data</li>
    </ul>
    <p><em>Note: This will delete all cookies, including the consent cookie, and you'll see the consent banner again on your next visit.</em></p>
    
    <h3>Method 3: Block Cookies in Browser Settings</h3>
    <p>You can configure your browser to block all or specific types of cookies:</p>
    <ul>
        <li>Block all cookies (may break website functionality)</li>
        <li>Block third-party cookies only</li>
        <li>Block cookies from specific domains</li>
    </ul>
    
    <h3>What Happens When You Withdraw Consent</h3>
    <p>When you withdraw consent for a cookie category:</p>
    <ul>
        <li>✅ Existing cookies in that category are immediately deleted</li>
        <li>✅ Associated tracking scripts are blocked from loading</li>
        <li>✅ No new cookies in that category will be set</li>
        <li>✅ Data collection through those cookies stops immediately</li>
        <li>⚠️ Some features dependent on those cookies may not work</li>
    </ul>
    
    <h3>Re-Consent Process</h3>
    <p>If we make significant changes to our cookie usage or privacy policy, we may ask you to re-consent. In this case:</p>
    <ul>
        <li>You'll see the consent banner again even if you previously consented</li>
        <li>We'll clearly explain what has changed</li>
        <li>Your previous consent will not be carried over automatically</li>
        <li>You'll need to make a new choice</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render consent storage section
     *
     * @return string Section content.
     */
    private function render_consent_storage(): string {
        return <<<HTML
<div class="policy-section">
    <h2>7. How We Store Your Consent</h2>
    
    <h3>Consent Cookie</h3>
    <p>Your consent choices are stored in a cookie named <code>complyflow_consent</code> with the following characteristics:</p>
    
    <div class="technical-details-box">
        <h4>Technical Details</h4>
        <ul>
            <li><strong>Cookie Name:</strong> complyflow_consent</li>
            <li><strong>Type:</strong> First-party cookie (set by our domain)</li>
            <li><strong>Duration:</strong> 12 months (365 days)</li>
            <li><strong>Storage Location:</strong> Your browser's cookie storage</li>
            <li><strong>Security:</strong> SameSite=Lax attribute for CSRF protection</li>
            <li><strong>Category:</strong> Necessary (exempt from consent requirement)</li>
        </ul>
    </div>
    
    <h3>What Information Is Stored</h3>
    <p>The consent cookie contains:</p>
    <pre><code>{
  "necessary": true,
  "analytics": true/false,
  "marketing": true/false,
  "preferences": true/false,
  "timestamp": "2025-11-26T10:30:00Z"
}</code></pre>
    
    <p><strong>This cookie does NOT contain:</strong></p>
    <ul>
        <li>❌ Your name or email address</li>
        <li>❌ Your IP address</li>
        <li>❌ Any personally identifiable information</li>
        <li>❌ Browsing history or behavioral data</li>
    </ul>
    
    <h3>Server-Side Consent Logs</h3>
    <p>For compliance and audit purposes, we also maintain a server-side log of consent events:</p>
    
    <div class="consent-log-box">
        <h4>What We Log</h4>
        <ul>
            <li>Timestamp of consent action</li>
            <li>Anonymized IP address (last octet removed for privacy)</li>
            <li>Consent choices (which categories were accepted/rejected)</li>
            <li>Browser user agent string</li>
            <li>Consent action type (accept all, reject all, customize)</li>
        </ul>
        
        <h4>Why We Log Consent</h4>
        <ul>
            <li>Demonstrate GDPR compliance to regulators</li>
            <li>Prove we obtained valid consent if challenged</li>
            <li>Track consent withdrawal requests</li>
            <li>Generate compliance reports and statistics</li>
        </ul>
        
        <h4>Log Retention Period</h4>
        <p>Consent logs are retained for 3 years to meet regulatory requirements, after which they are securely deleted.</p>
    </div>
    
    <h3>Data Security</h3>
    <p>Consent data is protected through:</p>
    <ul>
        <li>Encryption in transit (HTTPS)</li>
        <li>Secure database storage</li>
        <li>Access controls (admin-only access)</li>
        <li>Regular security audits</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render geo-targeting section
     *
     * @return string Section content.
     */
    private function render_geo_targeting(): string {
        return <<<HTML
<div class="policy-section">
    <h2>8. Geographic Targeting & Compliance</h2>
    
    <p>Our consent system automatically adapts based on your location to ensure compliance with local privacy laws.</p>
    
    <h3>How Geo-Detection Works</h3>
    <p>We detect your approximate location using:</p>
    <ul>
        <li>Your IP address (mapped to country/region)</li>
        <li>Browser language settings</li>
        <li>Timezone information</li>
    </ul>
    <p><em>Note: This detection is done client-side or server-side without storing your precise location.</em></p>
    
    <h3>Regional Consent Rules</h3>
    
    <div class="geo-region-box">
        <h4>🇪🇺 European Union & EEA (GDPR)</h4>
        <p><strong>Consent Type:</strong> Opt-In (Explicit Consent)</p>
        <p><strong>Default State:</strong> All non-essential cookies blocked</p>
        <p><strong>Requirements:</strong></p>
        <ul>
            <li>Explicit action required (no pre-checked boxes)</li>
            <li>Granular choices for each category</li>
            <li>Easy withdrawal mechanism</li>
            <li>Clear information about cookie purposes</li>
        </ul>
        <p><strong>Applicable Countries:</strong> All 27 EU member states + Iceland, Liechtenstein, Norway</p>
    </div>
    
    <div class="geo-region-box">
        <h4>🇬🇧 United Kingdom (UK GDPR)</h4>
        <p><strong>Consent Type:</strong> Opt-In (Explicit Consent)</p>
        <p><strong>Default State:</strong> All non-essential cookies blocked</p>
        <p>Same requirements as EU GDPR plus UK-specific ICO guidance</p>
    </div>
    
    <div class="geo-region-box">
        <h4>🇺🇸 California, USA (CCPA/CPRA)</h4>
        <p><strong>Consent Type:</strong> Opt-Out (Notice & Choice)</p>
        <p><strong>Default State:</strong> Cookies allowed unless user opts out</p>
        <p><strong>Requirements:</strong></p>
        <ul>
            <li>"Do Not Sell My Personal Information" link</li>
            <li>Right to opt-out of data sales</li>
            <li>Right to opt-in for minors under 16</li>
        </ul>
    </div>
    
    <div class="geo-region-box">
        <h4>🇧🇷 Brazil (LGPD)</h4>
        <p><strong>Consent Type:</strong> Opt-In (Explicit Consent)</p>
        <p><strong>Default State:</strong> All non-essential cookies blocked</p>
        <p>Similar to GDPR with specific LGPD compliance requirements</p>
    </div>
    
    <div class="geo-region-box">
        <h4>🇨🇦 Canada (PIPEDA)</h4>
        <p><strong>Consent Type:</strong> Opt-In (Express or Implied)</p>
        <p>Meaningful consent required with clear notice</p>
    </div>
    
    <div class="geo-region-box">
        <h4>🌏 Other Regions</h4>
        <p>Visitors from other regions see a standard consent banner with opt-in requirements to ensure maximum privacy protection.</p>
    </div>
    
    <h3>Overriding Geo-Detection</h3>
    <p>In rare cases where geo-detection is incorrect, you can:</p>
    <ul>
        <li>Use your browser's "Do Not Track" setting for enhanced privacy</li>
        <li>Contact us to report incorrect geo-detection</li>
        <li>Use our strictest consent settings (GDPR mode) regardless of location</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render script blocking section
     *
     * @return string Section content.
     */
    private function render_script_blocking(): string {
        return <<<HTML
<div class="policy-section">
    <h2>9. Automatic Script Blocking</h2>
    
    <p>Our consent system includes intelligent script blocking to ensure third-party services only load with your consent.</p>
    
    <h3>How Script Blocking Works</h3>
    <p>When you visit our website:</p>
    <ol>
        <li>All scripts are scanned for third-party tracking code</li>
        <li>Scripts are categorized (Analytics, Marketing, Preferences)</li>
        <li>Non-essential scripts are blocked by default (in opt-in regions)</li>
        <li>Scripts are only loaded after you provide consent</li>
    </ol>
    
    <h3>Blocked Services</h3>
    <p>The following types of services are blocked until consent:</p>
    
    <div class="blocked-service-category">
        <h4>📊 Analytics Services</h4>
        <ul>
            <li>Google Analytics (ga.js, gtag.js, analytics.js)</li>
            <li>Google Tag Manager</li>
            <li>Adobe Analytics</li>
            <li>Matomo / Piwik</li>
            <li>Hotjar heatmaps</li>
        </ul>
    </div>
    
    <div class="blocked-service-category">
        <h4>🎯 Marketing & Advertising</h4>
        <ul>
            <li>Facebook Pixel</li>
            <li>Google Ads / DoubleClick</li>
            <li>Twitter advertising</li>
            <li>LinkedIn Insight Tag</li>
            <li>Retargeting pixels</li>
        </ul>
    </div>
    
    <div class="blocked-service-category">
        <h4>🎥 Embedded Content</h4>
        <ul>
            <li>YouTube video embeds (until consent)</li>
            <li>Vimeo embeds</li>
            <li>Social media widgets (Facebook, Twitter)</li>
            <li>Third-party comment systems</li>
        </ul>
    </div>
    
    <h3>Placeholder Content</h3>
    <p>When content is blocked, we show user-friendly placeholders that:</p>
    <ul>
        <li>Explain why the content is blocked</li>
        <li>Provide a button to enable that specific cookie category</li>
        <li>Respect your privacy choice without breaking the page layout</li>
    </ul>
    
    <h3>Technical Implementation</h3>
    <p>Script blocking is implemented through:</p>
    <ul>
        <li><strong>Type Modification:</strong> Scripts are changed to <code>type="text/plain"</code> until consent</li>
        <li><strong>Data Attributes:</strong> <code>data-category="analytics"</code> tags identify script categories</li>
        <li><strong>Dynamic Loading:</strong> Scripts are loaded via JavaScript after consent is given</li>
        <li><strong>Output Buffering:</strong> HTML is processed before sending to browser</li>
    </ul>
    
    <h3>What's Never Blocked</h3>
    <p>Essential functionality is never blocked:</p>
    <ul>
        <li>✅ Website core functionality</li>
        <li>✅ Security features</li>
        <li>✅ Shopping cart and checkout</li>
        <li>✅ User authentication</li>
        <li>✅ Content delivery networks (CDNs) for site resources</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render consent logging section
     *
     * @return string Section content.
     */
    private function render_consent_logging(): string {
        return <<<HTML
<div class="policy-section">
    <h2>10. Consent Audit Logs</h2>
    
    <p>We maintain comprehensive logs of all consent actions for compliance and accountability purposes.</p>
    
    <h3>What We Log</h3>
    <p>Each consent event records:</p>
    
    <div class="log-detail-box">
        <h4>Event Information</h4>
        <ul>
            <li><strong>Timestamp:</strong> Exact date and time of consent action</li>
            <li><strong>Action Type:</strong> Accept All, Reject All, Custom Preferences, Withdrawal</li>
            <li><strong>Consent Choices:</strong> Which categories were enabled/disabled</li>
            <li><strong>Version:</strong> Which version of the privacy policy was consented to</li>
        </ul>
        
        <h4>Technical Data</h4>
        <ul>
            <li><strong>Anonymized IP:</strong> Last octet removed (e.g., 192.168.1.xxx)</li>
            <li><strong>User Agent:</strong> Browser and operating system information</li>
            <li><strong>Referrer:</strong> How the visitor arrived at our site</li>
            <li><strong>Language:</strong> Browser language setting</li>
        </ul>
        
        <h4>Compliance Data</h4>
        <ul>
            <li><strong>Geo-Location:</strong> Country/region (for compliance framework application)</li>
            <li><strong>Consent Scope:</strong> Which privacy frameworks applied</li>
            <li><strong>Consent ID:</strong> Unique identifier for this consent record</li>
        </ul>
    </div>
    
    <h3>Why We Keep Logs</h3>
    <p>Consent logs serve multiple important purposes:</p>
    
    <div class="log-purpose-box">
        <h4>✅ Regulatory Compliance</h4>
        <p>Demonstrate to regulators (ICO, CNIL, FTC, etc.) that we:</p>
        <ul>
            <li>Obtained valid consent before processing personal data</li>
            <li>Honored withdrawal requests promptly</li>
            <li>Applied appropriate consent frameworks per region</li>
            <li>Maintained audit trails as required by law</li>
        </ul>
    </div>
    
    <div class="log-purpose-box">
        <h4>📊 Compliance Reporting</h4>
        <ul>
            <li>Generate statistics on consent rates</li>
            <li>Identify patterns in user preferences</li>
            <li>Measure effectiveness of consent mechanisms</li>
            <li>Support Data Protection Impact Assessments (DPIAs)</li>
        </ul>
    </div>
    
    <div class="log-purpose-box">
        <h4>🛡️ Legal Defense</h4>
        <ul>
            <li>Prove consent in case of disputes</li>
            <li>Respond to data subject access requests</li>
            <li>Defend against privacy complaints</li>
            <li>Document compliance efforts</li>
        </ul>
    </div>
    
    <h3>Log Security</h3>
    <p>Consent logs are protected through:</p>
    <ul>
        <li><strong>Access Controls:</strong> Only authorized administrators can view logs</li>
        <li><strong>Encryption:</strong> Logs are encrypted at rest and in transit</li>
        <li><strong>Anonymization:</strong> IP addresses are anonymized to prevent re-identification</li>
        <li><strong>Integrity:</strong> Logs cannot be altered after creation</li>
    </ul>
    
    <h3>Log Retention</h3>
    <p>We retain consent logs for:</p>
    <ul>
        <li><strong>3 Years:</strong> Standard retention period for compliance documentation</li>
        <li><strong>Longer if Required:</strong> If involved in legal proceedings or investigations</li>
        <li><strong>Secure Deletion:</strong> After retention period, logs are securely and permanently deleted</li>
    </ul>
    
    <h3>Your Rights Regarding Logs</h3>
    <p>Under GDPR and similar laws, you can:</p>
    <ul>
        <li><strong>Access:</strong> Request a copy of your consent log entries</li>
        <li><strong>Rectification:</strong> Correct any inaccurate information</li>
        <li><strong>Deletion:</strong> Request deletion (subject to legal retention requirements)</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render compliance modes section
     *
     * @return string Section content.
     */
    private function render_compliance_modes(): string {
        // Get enabled compliance modes
        $enabled_modes = [];
        $modes = [
            'consent_gdpr_enabled' => 'GDPR (EU)',
            'consent_uk_gdpr_enabled' => 'UK GDPR',
            'consent_ccpa_enabled' => 'CCPA (California)',
            'consent_lgpd_enabled' => 'LGPD (Brazil)',
            'consent_pipeda_enabled' => 'PIPEDA (Canada)',
            'consent_pdpa_sg_enabled' => 'PDPA (Singapore)',
            'consent_pdpa_th_enabled' => 'PDPA (Thailand)',
            'consent_appi_enabled' => 'APPI (Japan)',
            'consent_popia_enabled' => 'POPIA (South Africa)',
            'consent_kvkk_enabled' => 'KVKK (Turkey)',
            'consent_pdpl_enabled' => 'PDPL (Saudi Arabia)',
        ];
        
        foreach ($modes as $option => $label) {
            if (get_option($option, false)) {
                $enabled_modes[] = $label;
            }
        }
        
        $modes_list = !empty($enabled_modes) ? implode(', ', $enabled_modes) : 'Standard compliance mode';
        
        return <<<HTML
<div class="policy-section">
    <h2>11. Multi-Framework Compliance</h2>
    
    <p>Our consent management system is designed to comply with multiple privacy frameworks simultaneously.</p>
    
    <h3>Currently Enabled Compliance Modes</h3>
    <div class="compliance-status-box">
        <p><strong>Active Frameworks:</strong> {$modes_list}</p>
    </div>
    
    <h3>Supported Privacy Frameworks</h3>
    
    <div class="framework-detail">
        <h4>🇪🇺 GDPR (General Data Protection Regulation)</h4>
        <p><strong>Applicable:</strong> EU/EEA residents</p>
        <p><strong>Key Requirements:</strong></p>
        <ul>
            <li>Explicit, freely given, specific, informed consent (Article 4(11))</li>
            <li>Granular consent for different processing purposes (Article 7)</li>
            <li>Easy withdrawal mechanism (Article 7(3))</li>
            <li>Pre-ticked boxes prohibited (WP29 Guidelines)</li>
            <li>Consent for children under 16 requires parental authorization (Article 8)</li>
        </ul>
    </div>
    
    <div class="framework-detail">
        <h4>🇬🇧 UK GDPR</h4>
        <p><strong>Applicable:</strong> UK residents (post-Brexit)</p>
        <p>Substantially similar to EU GDPR with ICO (Information Commissioner's Office) guidance</p>
    </div>
    
    <div class="framework-detail">
        <h4>🇺🇸 CCPA/CPRA (California Consumer Privacy Act)</h4>
        <p><strong>Applicable:</strong> California residents</p>
        <p><strong>Key Requirements:</strong></p>
        <ul>
            <li>Notice before data collection</li>
            <li>Right to opt-out of "sale" of personal information</li>
            <li>"Do Not Sell My Personal Information" link</li>
            <li>Opt-in required for consumers under 16</li>
            <li>Cannot discriminate for exercising privacy rights</li>
        </ul>
    </div>
    
    <div class="framework-detail">
        <h4>🇧🇷 LGPD (Lei Geral de Proteção de Dados)</h4>
        <p><strong>Applicable:</strong> Brazil residents</p>
        <p>Similar to GDPR with specific Brazilian requirements for consent and data processing</p>
    </div>
    
    <div class="framework-detail">
        <h4>🇨🇦 PIPEDA (Personal Information Protection and Electronic Documents Act)</h4>
        <p><strong>Applicable:</strong> Canadian residents</p>
        <p>Requires meaningful consent with clear notice of purposes</p>
    </div>
    
    <div class="framework-detail">
        <h4>🇸🇬 PDPA (Personal Data Protection Act - Singapore)</h4>
        <p><strong>Applicable:</strong> Singapore residents</p>
        <p>Consent required with notification of purposes and right to withdraw</p>
    </div>
    
    <h3>How We Ensure Compliance</h3>
    <ul>
        <li>✅ Automatic framework selection based on visitor location</li>
        <li>✅ Strictest standard applied when multiple frameworks overlap</li>
        <li>✅ Regular audits of consent mechanisms</li>
        <li>✅ Documentation of all consent-related processes</li>
        <li>✅ Training for staff on privacy requirements</li>
    </ul>
</div>
HTML;
    }

    /**
     * Render updates section
     *
     * @return string Section content.
     */
    private function render_updates(): string {
        return <<<HTML
<div class="policy-section">
    <h2>12. Updates to This Policy</h2>
    
    <p>We may update this Consent Management Policy from time to time to reflect:</p>
    <ul>
        <li>Changes in our cookie usage or consent mechanisms</li>
        <li>New privacy regulations or guidance from regulators</li>
        <li>Improvements to our consent management system</li>
        <li>Feedback from users or privacy advocates</li>
    </ul>
    
    <h3>How We Notify You</h3>
    <p>When we make significant changes:</p>
    <ol>
        <li>We'll update the "Effective Date" at the top of this policy</li>
        <li>You'll see a notice on our website highlighting the changes</li>
        <li>You may be asked to re-consent to reflect the new terms</li>
        <li>We'll send email notifications to registered users (if applicable)</li>
    </ol>
    
    <h3>Material Changes</h3>
    <p>If changes materially affect your rights or our processing of consent data, we will:</p>
    <ul>
        <li>Provide prominent notice at least 30 days before changes take effect</li>
        <li>Request fresh consent under the new terms</li>
        <li>Allow you to review changes before accepting</li>
        <li>Honor existing consents until you make a new choice</li>
    </ul>
    
    <h3>Version History</h3>
    <p>We maintain a history of all policy versions. You can request previous versions by contacting us.</p>
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
        
        $contact = <<<HTML
<div class="policy-section">
    <h2>13. Contact Us</h2>
    
    <p>If you have questions about this Consent Management Policy or how we handle your consent, please contact us:</p>
    
    <div class="contact-box">
        <h3>{$company}</h3>
HTML;

        if ($email) {
            $contact .= "<p><strong>Email:</strong> <a href=\"mailto:{$email}\">{$email}</a></p>";
        }
        
        if ($has_dpo && $dpo_contact) {
            $contact .= <<<HTML
        
        <h4>Data Protection Officer</h4>
        <p><strong>Contact:</strong> {$dpo_contact}</p>
        <p>Our DPO is responsible for overseeing our consent management and privacy compliance.</p>
HTML;
        }
        
        $preferences_url = home_url('/cookie-preferences/');
        
        $contact .= <<<HTML
    </div>
    
    <h3>Quick Links</h3>
    <ul>
        <li><a href="{$preferences_url}">Cookie Preferences Center</a> - Manage your consent settings</li>
        <li><a href="/privacy-policy/">Privacy Policy</a> - Full privacy information</li>
        <li><a href="/cookie-policy/">Cookie Policy</a> - Detailed cookie information</li>
    </ul>
    
    <h3>Complaints to Regulators</h3>
    <p>You have the right to lodge a complaint with your local data protection authority if you believe we have not handled your consent properly.</p>
</div>
HTML;
        
        return $contact;
    }
}
