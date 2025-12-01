<?php
/**
 * Cookie Categories Reference Generator
 *
 * @package ComplyFlow
 * @subpackage Documents
 * @since 4.9.0
 */

namespace ComplyFlow\Modules\Documents;

use ComplyFlow\Core\Repositories\SettingsRepository;
use ComplyFlow\Modules\Cookie\CookieScanner;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generates detailed reference guide for cookie categories
 *
 * @since 4.9.0
 */
class CookieCategoriesReferenceGenerator {
    
    /**
     * User's questionnaire answers
     *
     * @var array
     */
    private $answers;

    /**
     * Cookie scanner instance
     *
     * @var CookieScanner
     */
    private $cookie_scanner;

    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private $settings;

    /**
     * Constructor
     *
     * @param array $answers User questionnaire answers
     */
    public function __construct($answers = []) {
        $this->answers = $answers;
        $this->settings = new SettingsRepository();
        $this->cookie_scanner = new CookieScanner($this->settings);
    }

    /**
     * Generate the reference guide
     *
     * @return string
     */
    public function generate() {
        // Load template
        $template_path = COMPLYFLOW_PLUGIN_DIR . 'templates/policies/cookie-categories-reference-template.php';
        if (!file_exists($template_path)) {
            return 'Error: Template file not found';
        }

        ob_start();
        include $template_path;
        $template = ob_get_clean();

        // Perform token replacements
        $template = str_replace('{{COMPANY_NAME}}', $this->get_company_name(), $template);
        $template = str_replace('{{EFFECTIVE_DATE}}', $this->get_effective_date(), $template);
        $template = str_replace('{{OVERVIEW_SECTION}}', $this->get_overview_section(), $template);
        $template = str_replace('{{NECESSARY_COOKIES_SECTION}}', $this->get_necessary_cookies_section(), $template);
        $template = str_replace('{{ANALYTICS_COOKIES_SECTION}}', $this->get_analytics_cookies_section(), $template);
        $template = str_replace('{{MARKETING_COOKIES_SECTION}}', $this->get_marketing_cookies_section(), $template);
        $template = str_replace('{{PREFERENCES_COOKIES_SECTION}}', $this->get_preferences_cookies_section(), $template);
        $template = str_replace('{{HOW_TO_MANAGE_SECTION}}', $this->get_how_to_manage_section(), $template);
        $template = str_replace('{{CONTACT_SECTION}}', $this->get_contact_section(), $template);

        return $template;
    }

    /**
     * Get company name
     */
    private function get_company_name() {
        return !empty($this->answers['company_name']) ? esc_html($this->answers['company_name']) : get_bloginfo('name');
    }

    /**
     * Get effective date
     */
    private function get_effective_date() {
        return !empty($this->answers['effective_date']) ? esc_html($this->answers['effective_date']) : date('F j, Y');
    }

    /**
     * Overview section
     */
    private function get_overview_section() {
        $company = $this->get_company_name();
        
        return <<<HTML
<div class="policy-section">
    <h2>1. Overview</h2>
    <p>This reference guide explains the four cookie categories used on {$company}'s website. Understanding these categories helps you make informed decisions about which cookies to accept through our <a href="/cookie-preferences/">Cookie Preferences Center</a>.</p>
    
    <div class="info-box">
        <h3>What are Cookie Categories?</h3>
        <p>We organize cookies into four categories based on their purpose:</p>
        <ol>
            <li><strong>Necessary Cookies</strong> - Essential for website functionality (always active)</li>
            <li><strong>Analytics Cookies</strong> - Help us understand how visitors use our website (opt-in required)</li>
            <li><strong>Marketing Cookies</strong> - Track you across websites to deliver targeted advertising (opt-in required)</li>
            <li><strong>Preferences Cookies</strong> - Remember your choices and settings (opt-in required)</li>
        </ol>
    </div>

    <h3>Why Categorize Cookies?</h3>
    <ul>
        <li><strong>Transparency:</strong> Clearly communicate the purpose of each cookie</li>
        <li><strong>User Control:</strong> Allow you to accept/reject cookies by category</li>
        <li><strong>Legal Compliance:</strong> Meet GDPR requirements for granular consent</li>
        <li><strong>Privacy Protection:</strong> Give you control over what data is collected</li>
    </ul>

    <h3>How Cookie Blocking Works</h3>
    <p>When you reject a cookie category through our <a href="/cookie-preferences/">Cookie Preferences Center</a>:</p>
    <ol>
        <li><strong>Cookies are blocked:</strong> Scripts that set those cookies won't load</li>
        <li><strong>Existing cookies are deleted:</strong> Previously set cookies are removed from your browser</li>
        <li><strong>Your choice is saved:</strong> We remember your preference for 12 months</li>
        <li><strong>You can change anytime:</strong> Update preferences whenever you want</li>
    </ol>

    <p><em>Learn more about how our consent system works in our <a href="/consent-management-policy/">Consent Management Policy</a>.</em></p>
</div>
HTML;
    }

    /**
     * Necessary cookies section
     */
    private function get_necessary_cookies_section() {
        $company = $this->get_company_name();
        
        return <<<HTML
<div class="policy-section">
    <h2>2. Necessary Cookies</h2>
    
    <div class="category-status-box necessary">
        <h3>🔒 Always Active</h3>
        <p>Necessary cookies cannot be disabled as they are essential for the website to function properly. Without these cookies, core features like user authentication, shopping cart, and security would not work.</p>
    </div>

    <h3>What are Necessary Cookies?</h3>
    <p>Necessary cookies (also called "strictly necessary" or "essential" cookies) enable core functionality that the website cannot function without. These cookies:</p>
    <ul>
        <li><strong>Do not require consent</strong> under GDPR (Recital 30)</li>
        <li><strong>Do not track users</strong> for marketing or analytics purposes</li>
        <li><strong>Are deleted when you close your browser</strong> (session cookies) or after a short period</li>
        <li><strong>Cannot be disabled</strong> through Cookie Preferences Center</li>
    </ul>

    <h3>Examples of Necessary Cookies Used on This Website</h3>

    <div class="cookie-detail-box">
        <h4>WordPress Session Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>wordpress_[hash]</code>, <code>wordpress_logged_in_[hash]</code>, <code>wp-settings-{user_id}</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Authenticate logged-in users, maintain login session, store user preferences</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>Session (closes with browser) or 14 days if "Remember Me" checked</td>
            </tr>
            <tr>
                <th>Why Necessary:</th>
                <td>Without these cookies, users cannot log in or stay logged in to their accounts</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>PHPSESSID</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>PHPSESSID</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Maintain user session state across page requests (PHP session identifier)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>Session (deleted when browser closes)</td>
            </tr>
            <tr>
                <th>Why Necessary:</th>
                <td>Required for server-side session management (e.g., forms, shopping cart state)</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>WooCommerce Cart Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>woocommerce_cart_hash</code>, <code>woocommerce_items_in_cart</code>, <code>wp_woocommerce_session_[hash]</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Store items in shopping cart, remember cart state across pages</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>Session or 48 hours</td>
            </tr>
            <tr>
                <th>Why Necessary:</th>
                <td>E-commerce functionality requires cart persistence between page views</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>complyflow_consent</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>complyflow_consent</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Store your cookie consent preferences (which categories you've accepted/rejected)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>12 months</td>
            </tr>
            <tr>
                <th>Why Necessary:</th>
                <td>Required to remember your consent choices and not show the banner every time you visit</td>
            </tr>
            <tr>
                <th>Data Stored:</th>
                <td>JSON object: <code>{"necessary":true,"analytics":false,"marketing":false,"preferences":false,"timestamp":1234567890}</code></td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>Security Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>__cf_bm</code>, <code>__cfruid</code> (Cloudflare), <code>wordpress_sec_[hash]</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Detect bots, prevent brute-force attacks, manage security challenges (CAPTCHA)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>30 minutes to 1 year depending on cookie</td>
            </tr>
            <tr>
                <th>Why Necessary:</th>
                <td>Protect website and users from malicious activity, spam, and security threats</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>Load Balancing Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>AWSALB</code>, <code>AWSALBCORS</code> (AWS), similar cookies from hosting providers</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Route traffic to correct server, maintain session on same server</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>7 days</td>
            </tr>
            <tr>
                <th>Why Necessary:</th>
                <td>Ensure consistent experience across multiple servers in load-balanced infrastructure</td>
            </tr>
        </table>
    </div>

    <h3>Why Can't I Disable Necessary Cookies?</h3>
    <p>Under privacy laws like GDPR, <strong>necessary cookies are exempt from the consent requirement</strong> because:</p>
    <ul>
        <li><strong>GDPR Recital 30:</strong> "Natural persons may be associated with online identifiers... This may leave traces which, in particular when combined with unique identifiers... may be used to create profiles... However, the use of a cookie... where this is strictly necessary in order to provide an information society service explicitly requested by the subscriber... should not require consent."</li>
        <li><strong>ICO Guidance (UK):</strong> "Some cookies are essential for a site to work. For example, shopping basket cookies let people make purchases. You don't need consent for these cookies."</li>
        <li><strong>CNIL Guidance (France):</strong> "Tracers that are strictly necessary for the provision of an online communication service at the express request of the user... are exempt from the obligation to obtain the user's consent."</li>
    </ul>

    <h3>How to Block Necessary Cookies (Not Recommended)</h3>
    <p>While we don't allow disabling necessary cookies through our Cookie Preferences Center, you can technically block them at the browser level. <strong>However, this will break core website functionality:</strong></p>
    <ul>
        <li>❌ You won't be able to log in</li>
        <li>❌ Shopping cart won't work</li>
        <li>❌ Forms may not submit correctly</li>
        <li>❌ Security features will be disabled</li>
        <li>❌ Website may become completely unusable</li>
    </ul>
    <p><strong>Browser Settings:</strong> Chrome/Firefox/Edge → Settings → Privacy → Block all cookies (not recommended)</p>
</div>
HTML;
    }

    /**
     * Analytics cookies section
     */
    private function get_analytics_cookies_section() {
        $home_url = home_url();
        $detected_cookies = [];
        
        try {
            $detected_cookies = $this->cookie_scanner->scan_cookies($home_url);
        } catch (\Exception $e) {
            error_log('Cookie scanning failed: ' . $e->getMessage());
        }

        // Check for analytics cookies
        $has_google_analytics = false;
        foreach ($detected_cookies as $cookie) {
            if (isset($cookie['name']) && (strpos($cookie['name'], '_ga') !== false || strpos($cookie['name'], '__utm') !== false)) {
                $has_google_analytics = true;
                break;
            }
        }

        $status_html = $has_google_analytics 
            ? '<div class="category-status-box analytics-detected"><p><strong>✓ Analytics cookies detected on this website</strong></p><p>Toggle "Analytics Cookies" in <a href="/cookie-preferences/">Cookie Preferences</a> to control these cookies.</p></div>'
            : '<div class="category-status-box analytics-none"><p><strong>✓ No analytics cookies currently detected</strong></p></div>';

        return <<<HTML
<div class="policy-section">
    <h2>3. Analytics Cookies</h2>
    
    {$status_html}

    <h3>What are Analytics Cookies?</h3>
    <p>Analytics cookies collect information about how visitors use our website. These cookies help us:</p>
    <ul>
        <li><strong>Understand visitor behavior:</strong> Which pages are popular, how long people stay, where they come from</li>
        <li><strong>Identify issues:</strong> Find broken pages, confusing navigation, performance problems</li>
        <li><strong>Improve content:</strong> Create more of what people like, remove what doesn't work</li>
        <li><strong>Optimize user experience:</strong> Make the website easier and more enjoyable to use</li>
    </ul>

    <div class="consent-notice">
        <p><strong>Your Consent Required:</strong> Analytics cookies require opt-in consent under GDPR. We will not load analytics services unless you explicitly accept "Analytics Cookies" in our consent banner or Cookie Preferences Center.</p>
    </div>

    <h3>Google Analytics Cookies</h3>
    <p>Google Analytics is the most common analytics service. It sets several cookies to track visitor behavior:</p>

    <div class="cookie-detail-box">
        <h4>_ga</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>_ga</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Distinguish unique users by assigning a randomly generated number</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>2 years</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Unique visitor ID (random string like <code>GA1.2.1234567890.1609459200</code>)</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Can track you across different visits to this website</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>_gid</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>_gid</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Distinguish unique users (short-term identifier)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>24 hours</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Unique visitor ID for today's session</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Only tracks you for one day</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>_gat</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>_gat</code>, <code>_gat_gtag_UA_XXXXX</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Throttle request rate to prevent overload (rate limiting)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>1 minute</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>No personal data; just controls request frequency</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Minimal; very short duration</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>Legacy Google Analytics Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>__utma</code>, <code>__utmb</code>, <code>__utmc</code>, <code>__utmz</code>, <code>__utmt</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Older version of Google Analytics (Universal Analytics, being phased out)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>Session to 2 years depending on cookie</td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>Legacy cookies; Google Analytics 4 uses <code>_ga</code> cookies instead</td>
            </tr>
        </table>
    </div>

    <h3>Other Analytics Cookies</h3>

    <div class="cookie-detail-box">
        <h4>Hotjar Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>_hjSession_*</code>, <code>_hjSessionUser_*</code>, <code>_hjIncludedInPageviewSample</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Session recordings, heatmaps, user feedback surveys</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>30 minutes to 1 year</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Mouse movements, clicks, scrolling, form interactions (no sensitive data like passwords)</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Records your behavior on the website; can be privacy-invasive</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>Matomo Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>_pk_id.*</code>, <code>_pk_ses.*</code>, <code>_pk_ref.*</code>, <code>mtm_consent</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Privacy-focused analytics (open-source Google Analytics alternative)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>13 months</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Similar to Google Analytics but with IP anonymization by default</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>More privacy-friendly; data can be self-hosted (not shared with third parties)</td>
            </tr>
        </table>
    </div>

    <h3>What Data Do Analytics Cookies Collect?</h3>
    <p>Analytics services typically collect:</p>
    <ul>
        <li><strong>Pages visited:</strong> URL of each page you view</li>
        <li><strong>Time on site:</strong> How long you stay on each page</li>
        <li><strong>Referral source:</strong> Where you came from (Google search, social media, direct visit)</li>
        <li><strong>Device information:</strong> Browser type, operating system, screen resolution, device type (mobile/desktop)</li>
        <li><strong>Geographic location:</strong> Country, region, city (based on anonymized IP address)</li>
        <li><strong>Interactions:</strong> Button clicks, downloads, video plays, form submissions</li>
        <li><strong>Session information:</strong> Number of page views, session duration, bounce rate</li>
    </ul>

    <div class="privacy-notice">
        <h4>Privacy Protections We Use</h4>
        <ul>
            <li><strong>IP Anonymization:</strong> We anonymize IP addresses (last octet removed: <code>192.168.1.XXX</code>)</li>
            <li><strong>No User ID Tracking:</strong> We don't link analytics data to logged-in user accounts</li>
            <li><strong>Data Retention Limits:</strong> Analytics data automatically deleted after 14-26 months</li>
            <li><strong>Opt-Out Honored:</strong> When you reject analytics cookies, no tracking scripts load</li>
        </ul>
    </div>

    <h3>How to Block Analytics Cookies</h3>
    <ul>
        <li><strong>Method 1:</strong> Toggle off "Analytics Cookies" in <a href="/cookie-preferences/">Cookie Preferences Center</a></li>
        <li><strong>Method 2:</strong> Install <a href="https://tools.google.com/dlpage/gaoptout" target="_blank">Google Analytics Opt-out Browser Add-on</a></li>
        <li><strong>Method 3:</strong> Enable "Do Not Track" in browser settings (we honor DNT signals)</li>
        <li><strong>Method 4:</strong> Use privacy-focused browsers like Brave or Firefox with Enhanced Tracking Protection</li>
    </ul>
</div>
HTML;
    }

    /**
     * Marketing cookies section
     */
    private function get_marketing_cookies_section() {
        return <<<HTML
<div class="policy-section">
    <h2>4. Marketing Cookies</h2>
    
    <div class="category-status-box marketing">
        <p><strong>⚠️ Most Privacy-Invasive Category</strong></p>
        <p>Marketing cookies track you across multiple websites to build detailed profiles for targeted advertising. <strong>We strongly recommend rejecting this category unless you want personalized ads.</strong></p>
    </div>

    <h3>What are Marketing Cookies?</h3>
    <p>Marketing cookies (also called "advertising cookies" or "targeting cookies") are used to:</p>
    <ul>
        <li><strong>Track your browsing across websites</strong> in advertising networks</li>
        <li><strong>Build profiles of your interests</strong> based on the sites you visit</li>
        <li><strong>Deliver targeted advertisements</strong> related to your interests</li>
        <li><strong>Measure ad effectiveness</strong> (clicks, conversions, ROI)</li>
        <li><strong>Retarget you with ads</strong> after you visit our website (remarketing)</li>
    </ul>

    <div class="consent-notice">
        <p><strong>Your Consent Required:</strong> Marketing cookies require explicit opt-in consent under GDPR. We will NEVER load marketing cookies unless you explicitly accept "Marketing Cookies." <strong>Default is REJECT for all marketing cookies.</strong></p>
    </div>

    <h3>Facebook Pixel</h3>
    <p>Facebook Pixel tracks conversions from Facebook ads and builds custom audiences for retargeting.</p>

    <div class="cookie-detail-box">
        <h4>_fbp</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>_fbp</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Deliver and measure Facebook ads, build custom audiences, track conversions</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>90 days</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Page views, button clicks, purchases, form submissions, device info, IP address</td>
            </tr>
            <tr>
                <th>Cross-Site Tracking:</th>
                <td><strong>YES</strong> - Tracks you across all websites using Facebook Pixel</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Very high; Facebook can track your browsing across thousands of websites and combine with your Facebook profile</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>fr (Facebook)</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>fr</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Deliver targeted Facebook ads based on your behavior</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>90 days</td>
            </tr>
            <tr>
                <th>Set By:</th>
                <td>facebook.com domain (third-party cookie)</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Very high; directly links to your Facebook account if logged in</td>
            </tr>
        </table>
    </div>

    <h3>Google Ads / DoubleClick</h3>

    <div class="cookie-detail-box">
        <h4>IDE (DoubleClick)</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>IDE</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Serve targeted display ads across Google's advertising network</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>13 months</td>
            </tr>
            <tr>
                <th>Set By:</th>
                <td>doubleclick.net domain (third-party cookie)</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Browsing history across all sites in Google's ad network, ad impressions, clicks</td>
            </tr>
            <tr>
                <th>Cross-Site Tracking:</th>
                <td><strong>YES</strong> - Tracks across millions of websites in Google Display Network</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Very high; builds comprehensive profile of your interests across the web</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>_gcl_aw</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>_gcl_aw</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Google Ads conversion tracking (measure which ads lead to actions)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>90 days</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Ad click ID, timestamp, conversion actions (purchase, signup, etc.)</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>High; links your website actions to specific ad clicks</td>
            </tr>
        </table>
    </div>

    <h3>LinkedIn Insight Tag</h3>

    <div class="cookie-detail-box">
        <h4>li_sugr</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>li_sugr</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>LinkedIn conversion tracking and retargeting</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>90 days</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Page views, conversions, company demographics (if available from LinkedIn profile)</td>
            </tr>
            <tr>
                <th>Cross-Site Tracking:</th>
                <td><strong>YES</strong> - Can link to your LinkedIn profile if logged in</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>High; LinkedIn may combine data with your professional profile</td>
            </tr>
        </table>
    </div>

    <h3>Twitter Pixel</h3>

    <div class="cookie-detail-box">
        <h4>personalization_id</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>personalization_id</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Twitter conversion tracking, build custom audiences, personalize ads</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>2 years</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Website visits, button clicks, conversions, Twitter user ID (if logged in)</td>
            </tr>
            <tr>
                <th>Cross-Site Tracking:</th>
                <td><strong>YES</strong> - Tracks across all sites using Twitter Pixel</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>High; can link to your Twitter account and browsing history</td>
            </tr>
        </table>
    </div>

    <h3>How Marketing Cookies Invade Privacy</h3>
    
    <div class="privacy-warning">
        <h4>🔍 What Marketing Networks Know About You</h4>
        <p>When you accept marketing cookies, advertising networks can build detailed profiles including:</p>
        <ul>
            <li><strong>Browsing history:</strong> Every website you visit that uses their tracking (thousands of sites)</li>
            <li><strong>Purchase history:</strong> Products you've bought, prices paid, shopping habits</li>
            <li><strong>Interests:</strong> Topics, hobbies, brands you're interested in based on sites visited</li>
            <li><strong>Demographics:</strong> Age, gender, income level (inferred from browsing patterns)</li>
            <li><strong>Location history:</strong> Where you browse from (home, work, travel)</li>
            <li><strong>Device fingerprint:</strong> Unique identifier to track you even if you clear cookies</li>
            <li><strong>Social connections:</strong> If linked to social media profiles (Facebook, LinkedIn)</li>
        </ul>
    </div>

    <h3>Why Companies Want Marketing Cookies</h3>
    <ul>
        <li><strong>Retargeting:</strong> Show ads to people who visited our website but didn't buy (e.g., abandoned cart ads)</li>
        <li><strong>Lookalike Audiences:</strong> Find new customers similar to existing customers</li>
        <li><strong>Conversion Attribution:</strong> Measure which ads lead to sales (ROI tracking)</li>
        <li><strong>Personalized Advertising:</strong> Show ads for products you're likely interested in</li>
        <li><strong>Competitive Advantage:</strong> Advertise to people who visited competitors' websites</li>
    </ul>

    <h3>How to Block Marketing Cookies</h3>
    
    <div class="blocking-options">
        <h4>🛡️ Recommended: Reject in Cookie Preferences</h4>
        <p style="text-align: center; margin: 20px 0;">
            <a href="/cookie-preferences/" class="btn-primary">Manage Cookie Preferences</a>
        </p>
        <p>Toggle off "Marketing Cookies" to block all marketing trackers. This is the easiest and most effective method.</p>

        <h4>Additional Protection Measures:</h4>
        <ul>
            <li><strong>Browser Setting:</strong> Enable "Strict" tracking protection (Firefox) or "Enhanced Protection" (Chrome)</li>
            <li><strong>Ad Blockers:</strong> Install uBlock Origin, Privacy Badger, or Ghostery</li>
            <li><strong>Privacy Browsers:</strong> Use Brave, Firefox Focus, or DuckDuckGo Privacy Browser</li>
            <li><strong>Opt-Out Links:</strong>
                <ul>
                    <li><a href="https://adssettings.google.com/" target="_blank">Google Ad Personalization</a></li>
                    <li><a href="https://www.facebook.com/ads/preferences/" target="_blank">Facebook Ad Preferences</a></li>
                    <li><a href="https://optout.aboutads.info/" target="_blank">Digital Advertising Alliance Opt-Out</a></li>
                    <li><a href="https://optout.networkadvertising.org/" target="_blank">Network Advertising Initiative Opt-Out</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <h3>Impact of Blocking Marketing Cookies</h3>
    <p><strong>✅ What Still Works:</strong></p>
    <ul>
        <li>All website functionality (shopping, forms, login)</li>
        <li>Content access (blogs, videos, downloads)</li>
        <li>Security features</li>
    </ul>

    <p><strong>⚠️ What Changes:</strong></p>
    <ul>
        <li>You'll see generic ads instead of personalized ads (ads still exist, just not targeted)</li>
        <li>We can't track if our marketing campaigns are effective</li>
        <li>You won't see retargeting ads reminding you of products you viewed</li>
    </ul>

    <p><strong>🎉 Privacy Benefits:</strong></p>
    <ul>
        <li>Advertising networks can't track your browsing across websites</li>
        <li>Your interests and shopping habits remain private</li>
        <li>No detailed profile building for ad targeting</li>
        <li>Less creepy "following you around the internet" ads</li>
    </ul>
</div>
HTML;
    }

    /**
     * Preferences cookies section
     */
    private function get_preferences_cookies_section() {
        return <<<HTML
<div class="policy-section">
    <h2>5. Preferences Cookies</h2>
    
    <div class="category-status-box preferences">
        <p><strong>Low Privacy Impact</strong></p>
        <p>Preferences cookies remember your choices to improve your experience. They don't track you for advertising and have minimal privacy impact.</p>
    </div>

    <h3>What are Preferences Cookies?</h3>
    <p>Preferences cookies (also called "functionality cookies") remember your choices and settings to personalize your experience. These cookies:</p>
    <ul>
        <li><strong>Remember your preferences:</strong> Language, region, layout choices</li>
        <li><strong>Store UI settings:</strong> Dark mode, text size, collapsed/expanded sections</li>
        <li><strong>Save form data:</strong> Remember what you typed in forms (not sensitive data)</li>
        <li><strong>Enable enhanced features:</strong> Live chat, video playback preferences, social sharing</li>
    </ul>

    <div class="consent-notice">
        <p><strong>Your Consent Required:</strong> While less privacy-invasive than marketing cookies, preferences cookies still require opt-in consent under GDPR because they're not strictly necessary for the website to function.</p>
    </div>

    <h3>YouTube Cookies</h3>
    <p>When you watch embedded YouTube videos, YouTube sets preference cookies:</p>

    <div class="cookie-detail-box">
        <h4>PREF</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>PREF</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Remember YouTube preferences (volume, quality, subtitles, playback speed)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>8 months</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Your YouTube viewing preferences</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Low; only stores preferences, not used for ad targeting</td>
            </tr>
        </table>
    </div>

    <div class="cookie-detail-box">
        <h4>VISITOR_INFO1_LIVE</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>VISITOR_INFO1_LIVE</code></td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Track YouTube video views, estimate bandwidth</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>6 months</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Videos watched, bandwidth estimates</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Medium; can track viewing patterns but we use privacy-enhanced mode (youtube-nocookie.com) to minimize tracking</td>
            </tr>
        </table>
    </div>

    <h3>Language & Region Preferences</h3>

    <div class="cookie-detail-box">
        <h4>Language Cookie</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>pll_language</code>, <code>googtrans</code>, or similar</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Remember your selected language for multilingual websites</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>1 year</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Language code (e.g., "en", "es", "fr")</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Very low; only stores a language preference</td>
            </tr>
        </table>
    </div>

    <h3>UI Preferences</h3>

    <div class="cookie-detail-box">
        <h4>Dark Mode / Theme Preferences</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Name:</th>
                <td><code>theme_preference</code>, <code>dark_mode</code>, or similar</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Remember if you prefer dark mode, light mode, or other visual themes</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>1 year</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Theme choice (e.g., "dark", "light", "auto")</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Very low; purely cosmetic preference</td>
            </tr>
        </table>
    </div>

    <h3>Chat & Support Cookies</h3>

    <div class="cookie-detail-box">
        <h4>Live Chat Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>__livechat</code>, <code>intercom-*</code>, <code>drift_*</code> (varies by provider)</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Remember your chat history, whether chat widget is minimized/expanded, support ticket ID</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>Session to 1 year depending on provider</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Chat session ID, previous messages, user ID (if provided)</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Medium; may store conversation history but necessary for support functionality</td>
            </tr>
        </table>
    </div>

    <h3>Social Sharing Preferences</h3>

    <div class="cookie-detail-box">
        <h4>Social Sharing Cookies</h4>
        <table class="cookie-table">
            <tr>
                <th>Cookie Names:</th>
                <td><code>AddThis_*</code>, <code>ShareThis_*</code>, or similar</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Remember your preferred social sharing method (Twitter, Facebook, Email, etc.)</td>
            </tr>
            <tr>
                <th>Duration:</th>
                <td>6 months to 1 year</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Preferred sharing method</td>
            </tr>
            <tr>
                <th>Privacy Impact:</th>
                <td>Low to medium; some social sharing widgets may also track for advertising</td>
            </tr>
        </table>
    </div>

    <h3>Comparison: Preferences vs. Necessary Cookies</h3>
    
    <table class="comparison-table">
        <thead>
            <tr>
                <th>Aspect</th>
                <th>Necessary Cookies</th>
                <th>Preferences Cookies</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Consent Required?</strong></td>
                <td>❌ No (GDPR exempt)</td>
                <td>✅ Yes (opt-in required)</td>
            </tr>
            <tr>
                <td><strong>Can Disable?</strong></td>
                <td>❌ No (always active)</td>
                <td>✅ Yes (toggle in preferences)</td>
            </tr>
            <tr>
                <td><strong>Website Breaks Without?</strong></td>
                <td>✅ Yes (core features fail)</td>
                <td>❌ No (minor inconvenience only)</td>
            </tr>
            <tr>
                <td><strong>Privacy Impact</strong></td>
                <td>Very Low</td>
                <td>Low</td>
            </tr>
            <tr>
                <td><strong>Example</strong></td>
                <td>Login session, shopping cart</td>
                <td>Language, dark mode, video quality</td>
            </tr>
        </tbody>
    </table>

    <h3>How to Block Preferences Cookies</h3>
    <ul>
        <li><strong>Method 1:</strong> Toggle off "Preferences Cookies" in <a href="/cookie-preferences/">Cookie Preferences Center</a></li>
        <li><strong>Method 2:</strong> Block specific third-party cookies (YouTube, chat widgets) in browser settings</li>
    </ul>

    <h3>Impact of Blocking Preferences Cookies</h3>
    <p><strong>✅ What Still Works:</strong></p>
    <ul>
        <li>All core website features (login, shopping, forms)</li>
        <li>Browsing and reading content</li>
        <li>Security and essential functionality</li>
    </ul>

    <p><strong>⚠️ What Doesn't Work:</strong></p>
    <ul>
        <li>Website won't remember your language choice (reverts to default each visit)</li>
        <li>Dark mode setting won't persist (resets each time)</li>
        <li>YouTube preferences reset (volume, quality, subtitles)</li>
        <li>Live chat won't remember previous conversations</li>
        <li>You'll need to re-enter preferences on each visit</li>
    </ul>

    <p><strong>Recommendation:</strong> If you want a better user experience but still care about privacy, consider <strong>accepting Preferences Cookies but rejecting Analytics and Marketing Cookies.</strong> This gives you personalized features without invasive tracking.</p>
</div>
HTML;
    }

    /**
     * How to manage section
     */
    private function get_how_to_manage_section() {
        $company = $this->get_company_name();
        
        return <<<HTML
<div class="policy-section">
    <h2>6. How to Manage Cookie Categories</h2>
    
    <div class="management-promo">
        <h3>🎛️ Cookie Preferences Center (Recommended)</h3>
        <p>The easiest way to control cookie categories is through our Cookie Preferences Center where you can toggle each category on or off:</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="/cookie-preferences/" class="btn-primary" style="font-size: 18px; padding: 15px 30px;">Manage Cookie Preferences</a>
        </p>
    </div>

    <h3>What You Can Control</h3>
    <table class="control-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Can Toggle?</th>
                <th>Default</th>
                <th>Recommendation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Necessary Cookies</strong></td>
                <td>❌ No (always active)</td>
                <td>Always On</td>
                <td>Cannot disable; required for website to function</td>
            </tr>
            <tr>
                <td><strong>Analytics Cookies</strong></td>
                <td>✅ Yes</td>
                <td>Off (opt-in)</td>
                <td>Enable if you want to help us improve the website; disable for more privacy</td>
            </tr>
            <tr>
                <td><strong>Marketing Cookies</strong></td>
                <td>✅ Yes</td>
                <td>Off (opt-in)</td>
                <td><strong>Keep disabled</strong> unless you want personalized ads</td>
            </tr>
            <tr>
                <td><strong>Preferences Cookies</strong></td>
                <td>✅ Yes</td>
                <td>Off (opt-in)</td>
                <td>Enable for better user experience (language, dark mode, etc.)</td>
            </tr>
        </tbody>
    </table>

    <h3>How Cookie Preferences Center Works</h3>
    <ol>
        <li><strong>Visit the Preferences Center:</strong> <a href="/cookie-preferences/">Cookie Preferences</a></li>
        <li><strong>See Current Status:</strong> View which categories are currently enabled/disabled</li>
        <li><strong>Toggle Categories:</strong> Click the toggle switch to enable/disable each category</li>
        <li><strong>View Cookie Details:</strong> Expand each category to see specific cookies with their names, purposes, and durations</li>
        <li><strong>Save Preferences:</strong> Click "Save Preferences" to apply your choices</li>
        <li><strong>Immediate Effect:</strong> Changes take effect immediately; page reloads to apply new settings</li>
    </ol>

    <h3>What Happens When You Change Preferences</h3>
    <ul>
        <li><strong>Enabling a Category:</strong>
            <ul>
                <li>Allowed scripts load on the page</li>
                <li>Cookies are set by those scripts</li>
                <li>Tracking/analytics/functionality becomes active</li>
            </ul>
        </li>
        <li><strong>Disabling a Category:</strong>
            <ul>
                <li>All existing cookies in that category are deleted</li>
                <li>Scripts for that category are blocked from loading</li>
                <li>No new cookies in that category can be set</li>
                <li>Previous tracking data is not retroactively deleted (use <a href="/dsr-portal/">DSR Portal</a> to request deletion)</li>
            </ul>
        </li>
    </ul>

    <h3>How Long Are Preferences Saved?</h3>
    <p>Your cookie preferences are saved in the <code>complyflow_consent</code> cookie for <strong>12 months</strong>. After 12 months, you'll see the consent banner again asking for fresh consent.</p>

    <h3>Consent Banner vs. Preferences Center</h3>
    <table class="comparison-table">
        <thead>
            <tr>
                <th>Feature</th>
                <th>Consent Banner</th>
                <th>Cookie Preferences Center</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>When Shown</strong></td>
                <td>First visit, or every 12 months</td>
                <td>Anytime you visit the page</td>
            </tr>
            <tr>
                <td><strong>Quick Actions</strong></td>
                <td>Accept All, Reject All, Customize</td>
                <td>Individual category toggles</td>
            </tr>
            <tr>
                <td><strong>Detail Level</strong></td>
                <td>High-level category descriptions</td>
                <td>Individual cookie details (name, purpose, duration)</td>
            </tr>
            <tr>
                <td><strong>Best For</strong></td>
                <td>Quick consent decisions</td>
                <td>Detailed control and information</td>
            </tr>
        </tbody>
    </table>

    <h3>Browser-Level Cookie Management</h3>
    <p>You can also manage cookies directly in your browser settings:</p>

    <h4>Google Chrome</h4>
    <ol>
        <li>Settings → Privacy and security → Cookies and other site data</li>
        <li>See all site data and permissions → Search for "{$company}"</li>
        <li>View and delete individual cookies</li>
        <li>Set "Block third-party cookies" to prevent tracking across sites</li>
    </ol>

    <h4>Mozilla Firefox</h4>
    <ol>
        <li>Settings → Privacy & Security</li>
        <li>Enhanced Tracking Protection → "Strict" (blocks most trackers)</li>
        <li>Cookies and Site Data → Manage Data → Search for site</li>
        <li>Remove individual cookies or "Clear Data" for all</li>
    </ol>

    <h4>Safari</h4>
    <ol>
        <li>Preferences → Privacy</li>
        <li>Enable "Prevent cross-site tracking" (blocks third-party cookies)</li>
        <li>Manage Website Data → Search for site</li>
        <li>Remove individual or all cookies</li>
    </ol>

    <h4>Microsoft Edge</h4>
    <ol>
        <li>Settings → Privacy, search, and services</li>
        <li>Tracking prevention → "Strict"</li>
        <li>Cookies and site permissions → See all cookies</li>
        <li>Search for site and delete cookies</li>
    </ol>

    <h3>Privacy-Focused Recommendations</h3>
    
    <div class="recommendations-box">
        <h4>Maximum Privacy (Recommended for most users)</h4>
        <ul>
            <li>✅ <strong>Necessary Cookies:</strong> Always On (required)</li>
            <li>❌ <strong>Analytics Cookies:</strong> Off</li>
            <li>❌ <strong>Marketing Cookies:</strong> Off</li>
            <li>⚠️ <strong>Preferences Cookies:</strong> Your choice (low privacy impact)</li>
        </ul>
        <p><strong>Result:</strong> Maximum privacy; you won't be tracked for analytics or advertising. Website still fully functional.</p>
    </div>

    <div class="recommendations-box">
        <h4>Balanced Privacy & Functionality</h4>
        <ul>
            <li>✅ <strong>Necessary Cookies:</strong> Always On (required)</li>
            <li>✅ <strong>Analytics Cookies:</strong> On (help us improve)</li>
            <li>❌ <strong>Marketing Cookies:</strong> Off (no ad tracking)</li>
            <li>✅ <strong>Preferences Cookies:</strong> On (better experience)</li>
        </ul>
        <p><strong>Result:</strong> We can improve the website based on analytics, but you won't be tracked for advertising. Good balance.</p>
    </div>

    <div class="recommendations-box">
        <h4>Full Functionality (Least Private)</h4>
        <ul>
            <li>✅ <strong>Necessary Cookies:</strong> Always On (required)</li>
            <li>✅ <strong>Analytics Cookies:</strong> On</li>
            <li>✅ <strong>Marketing Cookies:</strong> On</li>
            <li>✅ <strong>Preferences Cookies:</strong> On</li>
        </ul>
        <p><strong>Result:</strong> All features work, you'll see personalized ads, but advertising networks can track you across the web. <strong>Not recommended unless you specifically want personalized advertising.</strong></p>
    </div>

    <h3>Related Resources</h3>
    <ul>
        <li><a href="/cookie-preferences/">Cookie Preferences Center</a> - Manage your cookie settings</li>
        <li><a href="/consent-management-policy/">Consent Management Policy</a> - How our consent system works</li>
        <li><a href="/cookie-policy/">Cookie Policy</a> - Complete cookie information</li>
        <li><a href="/third-party-services-disclosure/">Third-Party Services</a> - List of all third-party services we use</li>
        <li><a href="/user-rights-notice/">Your Privacy Rights</a> - How to exercise your data rights</li>
        <li><a href="/dsr-portal/">DSR Portal</a> - Request data access or deletion</li>
    </ul>
</div>
HTML;
    }

    /**
     * Contact section
     */
    private function get_contact_section() {
        $company = $this->get_company_name();
        $email = !empty($this->answers['contact_email']) ? esc_html($this->answers['contact_email']) : get_option('admin_email');
        $dpo_section = '';
        
        if (!empty($this->answers['has_dpo']) && $this->answers['has_dpo'] === 'yes' && !empty($this->answers['dpo_contact'])) {
            $dpo_contact = esc_html($this->answers['dpo_contact']);
            $dpo_section = <<<HTML
        <tr>
            <th>Data Protection Officer:</th>
            <td>{$dpo_contact}</td>
        </tr>
HTML;
        }

        return <<<HTML
<div class="policy-section">
    <h2>7. Contact Us About Cookies</h2>
    <p>If you have questions about our cookie categories, how to manage your preferences, or any privacy concerns, please contact us:</p>

    <div class="contact-box">
        <table class="contact-table">
            <tr>
                <th>Company:</th>
                <td>{$company}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><a href="mailto:{$email}">{$email}</a></td>
            </tr>
{$dpo_section}
            <tr>
                <th>Cookie Preferences:</th>
                <td><a href="/cookie-preferences/">Manage Cookie Preferences</a></td>
            </tr>
            <tr>
                <th>Data Rights Portal:</th>
                <td><a href="/dsr-portal/">Submit Data Subject Rights Request</a></td>
            </tr>
        </table>
    </div>

    <p><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
</div>
HTML;
    }
}
