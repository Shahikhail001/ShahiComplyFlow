<?php
/**
 * Third-Party Services Disclosure Generator
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
 * Generates comprehensive third-party services disclosure
 *
 * @since 4.9.0
 */
class ThirdPartyServicesDisclosureGenerator {
    
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
     * Generate the disclosure
     *
     * @return string
     */
    public function generate() {
        // Load template
        $template_path = COMPLYFLOW_PLUGIN_DIR . 'templates/policies/third-party-services-disclosure-template.php';
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
        $template = str_replace('{{ANALYTICS_SERVICES_SECTION}}', $this->get_analytics_services_section(), $template);
        $template = str_replace('{{MARKETING_SERVICES_SECTION}}', $this->get_marketing_services_section(), $template);
        $template = str_replace('{{SOCIAL_MEDIA_SECTION}}', $this->get_social_media_section(), $template);
        $template = str_replace('{{DATA_SHARING_SECTION}}', $this->get_data_sharing_section(), $template);
        $template = str_replace('{{PROCESSING_LOCATIONS_SECTION}}', $this->get_processing_locations_section(), $template);
        $template = str_replace('{{YOUR_CONTROL_SECTION}}', $this->get_your_control_section(), $template);
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
    <p>This document discloses all third-party services that may collect, process, or access your data when you use {$company}'s website and services. We believe in complete transparency about who we share your data with and why.</p>
    
    <div class="info-box">
        <h3>What is a Third-Party Service?</h3>
        <p>A third-party service is any external company or platform we use to operate our website, analyze usage, deliver features, or serve advertisements. These services may:</p>
        <ul>
            <li><strong>Collect data directly</strong> through cookies, pixels, or scripts embedded on our site</li>
            <li><strong>Receive data from us</strong> when we share information to provide services</li>
            <li><strong>Process data on our behalf</strong> as data processors under contract</li>
            <li><strong>Use data for their own purposes</strong> as independent data controllers</li>
        </ul>
    </div>

    <h3>Your Rights</h3>
    <p>You have the right to:</p>
    <ul>
        <li><strong>Know</strong> which third parties receive your data</li>
        <li><strong>Control</strong> which services can collect your data through <a href="/cookie-preferences/">Cookie Preferences</a></li>
        <li><strong>Object</strong> to data sharing with specific services (see <a href="/user-rights-notice/">Your Privacy Rights</a>)</li>
        <li><strong>Opt-out</strong> of targeted advertising through your consent preferences</li>
        <li><strong>Request deletion</strong> of your data from both our systems and third-party services</li>
    </ul>

    <h3>How We Detect Third-Party Services</h3>
    <p>We use automated cookie scanning to detect third-party services on our website. This disclosure includes:</p>
    <ul>
        <li><strong>Services we intentionally use</strong> (Analytics, Marketing, Hosting)</li>
        <li><strong>Services embedded in content</strong> (YouTube videos, Social media embeds)</li>
        <li><strong>Services used by plugins/themes</strong> (Third-party WordPress plugins)</li>
        <li><strong>Advertising networks</strong> (if applicable)</li>
    </ul>
    
    <p><em>Last scanned: <?php echo date('F j, Y'); ?></em></p>
</div>
HTML;
    }

    /**
     * Analytics services section
     */
    private function get_analytics_services_section() {
        $home_url = home_url();
        $detected_cookies = [];
        
        try {
            $detected_cookies = $this->cookie_scanner->scan_cookies($home_url);
        } catch (\Exception $e) {
            error_log('Cookie scanning failed: ' . $e->getMessage());
        }

        // Analytics services definitions
        $analytics_services = [
            'Google Analytics' => [
                'cookies' => ['_ga', '_gid', '_gat', '_gat_gtag_', '__utma', '__utmb', '__utmc', '__utmz'],
                'provider' => 'Google LLC',
                'privacy_policy' => 'https://policies.google.com/privacy',
                'data_collected' => 'IP address (anonymized), pages visited, time on site, referral source, device type, browser, geographic location (country/region)',
                'purpose' => 'Understand how visitors use our website, improve content and user experience, identify popular pages',
                'retention' => 'Analytics cookies: 2 years; Server-side data: 14-26 months (configurable)',
                'processing_location' => 'United States (Google servers)',
                'opt_out' => 'Toggle off "Analytics Cookies" in Cookie Preferences Center, or use Google Analytics Opt-out Browser Add-on',
            ],
            'Google Tag Manager' => [
                'cookies' => ['_dc_gtm_', '_gcl_au'],
                'provider' => 'Google LLC',
                'privacy_policy' => 'https://policies.google.com/privacy',
                'data_collected' => 'Tags triggered, conversion events, custom variables, dataLayer information',
                'purpose' => 'Manage analytics and marketing tags without editing website code',
                'retention' => 'Cookie data: 2 years; Event data: processed immediately',
                'processing_location' => 'United States (Google servers)',
                'opt_out' => 'Toggle off cookies in relevant categories (Analytics/Marketing)',
            ],
            'Hotjar' => [
                'cookies' => ['_hjSession', '_hjSessionUser', '_hjIncludedInPageviewSample', '_hjAbsoluteSessionInProgress'],
                'provider' => 'Hotjar Ltd.',
                'privacy_policy' => 'https://www.hotjar.com/legal/policies/privacy/',
                'data_collected' => 'Mouse movements, clicks, taps, scrolling, form interactions (no passwords), session recordings, heatmaps',
                'purpose' => 'Understand user behavior through heatmaps and session recordings to improve user experience',
                'retention' => 'Session data: 365 days; Recordings: 365 days',
                'processing_location' => 'European Union (Ireland, Germany)',
                'opt_out' => 'Toggle off "Analytics Cookies" or visit Hotjar opt-out page',
            ],
            'Matomo' => [
                'cookies' => ['_pk_id', '_pk_ses', '_pk_ref', 'mtm_consent'],
                'provider' => 'Self-hosted or Matomo Cloud',
                'privacy_policy' => 'https://matomo.org/privacy-policy/',
                'data_collected' => 'Pages visited, time on site, referral source, device information, search terms, downloads',
                'purpose' => 'Privacy-focused web analytics with full data ownership',
                'retention' => 'Cookies: 13 months; Server data: 180 days (configurable)',
                'processing_location' => 'Self-hosted on our servers or EU (if using Matomo Cloud)',
                'opt_out' => 'Toggle off "Analytics Cookies" in Cookie Preferences',
            ],
            'Adobe Analytics' => [
                'cookies' => ['s_cc', 's_sq', 's_vi', 's_fid'],
                'provider' => 'Adobe Inc.',
                'privacy_policy' => 'https://www.adobe.com/privacy/policy.html',
                'data_collected' => 'Visitor behavior, page views, custom events, product interactions, conversion data',
                'purpose' => 'Enterprise web analytics and customer journey tracking',
                'retention' => 'Cookies: 2 years; Server data: 25 months',
                'processing_location' => 'United States (Adobe servers)',
                'opt_out' => 'Toggle off "Analytics Cookies" in Cookie Preferences',
            ],
        ];

        // Detect which analytics services are present
        $detected_analytics = [];
        foreach ($analytics_services as $service_name => $service_data) {
            foreach ($service_data['cookies'] as $cookie_pattern) {
                foreach ($detected_cookies as $detected_cookie) {
                    if (isset($detected_cookie['name']) && strpos($detected_cookie['name'], $cookie_pattern) !== false) {
                        $detected_analytics[$service_name] = $service_data;
                        break 2;
                    }
                }
            }
        }

        if (empty($detected_analytics)) {
            return <<<HTML
<div class="policy-section">
    <h2>2. Analytics Services</h2>
    <div class="no-services-box">
        <p><strong>✓ No third-party analytics services detected</strong></p>
        <p>We are not currently using external analytics services to track website visitors.</p>
    </div>
</div>
HTML;
        }

        $html = <<<HTML
<div class="policy-section">
    <h2>2. Analytics Services</h2>
    <p>We use the following analytics services to understand how visitors use our website. These services collect data about your browsing behavior:</p>

HTML;

        foreach ($detected_analytics as $service_name => $service_data) {
            $html .= <<<HTML
    <div class="service-box analytics">
        <h3>{$service_name}</h3>
        <table class="service-details-table">
            <tr>
                <th>Provider:</th>
                <td>{$service_data['provider']}</td>
            </tr>
            <tr>
                <th>Cookies Set:</th>
                <td><code>
HTML;
            $html .= implode('</code>, <code>', $service_data['cookies']);
            $html .= <<<HTML
</code></td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>{$service_data['data_collected']}</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>{$service_data['purpose']}</td>
            </tr>
            <tr>
                <th>Data Retention:</th>
                <td>{$service_data['retention']}</td>
            </tr>
            <tr>
                <th>Processing Location:</th>
                <td>{$service_data['processing_location']}</td>
            </tr>
            <tr>
                <th>Privacy Policy:</th>
                <td><a href="{$service_data['privacy_policy']}" target="_blank">{$service_data['privacy_policy']}</a></td>
            </tr>
            <tr>
                <th>How to Opt-Out:</th>
                <td>{$service_data['opt_out']}</td>
            </tr>
        </table>
    </div>

HTML;
        }

        $html .= "</div>";
        return $html;
    }

    /**
     * Marketing services section
     */
    private function get_marketing_services_section() {
        $home_url = home_url();
        $detected_cookies = [];
        
        try {
            $detected_cookies = $this->cookie_scanner->scan_cookies($home_url);
        } catch (\Exception $e) {
            error_log('Cookie scanning failed: ' . $e->getMessage());
        }

        $marketing_services = [
            'Facebook Pixel' => [
                'cookies' => ['_fbp', 'fr', '_fbc'],
                'provider' => 'Meta Platforms, Inc. (Facebook)',
                'privacy_policy' => 'https://www.facebook.com/privacy/policy/',
                'data_collected' => 'Page views, button clicks, purchases, form submissions, device information, IP address',
                'purpose' => 'Track conversions from Facebook ads, build custom audiences, optimize ad delivery, retargeting',
                'retention' => 'Cookies: 90 days; Server-side event data: varies',
                'processing_location' => 'United States and global Facebook servers',
                'opt_out' => 'Toggle off "Marketing Cookies" in Cookie Preferences, or adjust Facebook Ad Preferences',
            ],
            'Google Ads' => [
                'cookies' => ['_gcl_aw', '_gcl_dc', 'IDE', 'test_cookie', 'DSID'],
                'provider' => 'Google LLC',
                'privacy_policy' => 'https://policies.google.com/privacy',
                'data_collected' => 'Ad clicks, conversions, remarketing tags, device info, browsing history across Google network',
                'purpose' => 'Measure ad performance, conversion tracking, remarketing, personalized advertising',
                'retention' => 'Cookies: 90 days to 2 years; Conversion data: varies',
                'processing_location' => 'United States (Google servers)',
                'opt_out' => 'Toggle off "Marketing Cookies" or use Google Ads Settings',
            ],
            'DoubleClick' => [
                'cookies' => ['IDE', 'test_cookie', '__gads'],
                'provider' => 'Google LLC',
                'privacy_policy' => 'https://policies.google.com/privacy',
                'data_collected' => 'Ad impressions, clicks, browsing behavior across ad network, device identifiers',
                'purpose' => 'Serve targeted display advertising, frequency capping, measure ad effectiveness',
                'retention' => 'Cookies: 13-24 months',
                'processing_location' => 'United States (Google ad servers)',
                'opt_out' => 'Toggle off "Marketing Cookies" or visit Google Ad Settings',
            ],
            'LinkedIn Insight Tag' => [
                'cookies' => ['li_sugr', 'UserMatchHistory', 'bcookie', 'lidc'],
                'provider' => 'LinkedIn Corporation',
                'privacy_policy' => 'https://www.linkedin.com/legal/privacy-policy',
                'data_collected' => 'Page views, LinkedIn profile data (if logged in), conversions, company demographics',
                'purpose' => 'Conversion tracking, retargeting, audience insights for LinkedIn ads',
                'retention' => 'Cookies: 6 months to 2 years',
                'processing_location' => 'United States (LinkedIn servers)',
                'opt_out' => 'Toggle off "Marketing Cookies" or adjust LinkedIn ad preferences',
            ],
            'Twitter Pixel' => [
                'cookies' => ['personalization_id', 'guest_id', 'ct0'],
                'provider' => 'Twitter, Inc. (X Corp)',
                'privacy_policy' => 'https://twitter.com/en/privacy',
                'data_collected' => 'Page views, button clicks, conversions, device info, Twitter user ID (if logged in)',
                'purpose' => 'Conversion tracking for Twitter ads, build custom audiences, measure campaign effectiveness',
                'retention' => 'Cookies: 2 years',
                'processing_location' => 'United States (Twitter servers)',
                'opt_out' => 'Toggle off "Marketing Cookies" or adjust Twitter personalization settings',
            ],
        ];

        $detected_marketing = [];
        foreach ($marketing_services as $service_name => $service_data) {
            foreach ($service_data['cookies'] as $cookie_pattern) {
                foreach ($detected_cookies as $detected_cookie) {
                    if (isset($detected_cookie['name']) && strpos($detected_cookie['name'], $cookie_pattern) !== false) {
                        $detected_marketing[$service_name] = $service_data;
                        break 2;
                    }
                }
            }
        }

        if (empty($detected_marketing)) {
            return <<<HTML
<div class="policy-section">
    <h2>3. Marketing & Advertising Services</h2>
    <div class="no-services-box">
        <p><strong>✓ No third-party marketing services detected</strong></p>
        <p>We are not currently using external marketing or advertising services.</p>
    </div>
</div>
HTML;
        }

        $html = <<<HTML
<div class="policy-section">
    <h2>3. Marketing & Advertising Services</h2>
    <p>We use the following marketing and advertising services. These services track your interactions to deliver targeted advertising:</p>
    
    <div class="marketing-notice">
        <p><strong>Important:</strong> Marketing services may use your data for their own purposes beyond what we request. They may track you across other websites in their advertising networks. <strong>You can block all marketing cookies by toggling off "Marketing Cookies" in your <a href="/cookie-preferences/">Cookie Preferences</a>.</strong></p>
    </div>

HTML;

        foreach ($detected_marketing as $service_name => $service_data) {
            $html .= <<<HTML
    <div class="service-box marketing">
        <h3>{$service_name}</h3>
        <table class="service-details-table">
            <tr>
                <th>Provider:</th>
                <td>{$service_data['provider']}</td>
            </tr>
            <tr>
                <th>Cookies Set:</th>
                <td><code>
HTML;
            $html .= implode('</code>, <code>', $service_data['cookies']);
            $html .= <<<HTML
</code></td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>{$service_data['data_collected']}</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>{$service_data['purpose']}</td>
            </tr>
            <tr>
                <th>Data Retention:</th>
                <td>{$service_data['retention']}</td>
            </tr>
            <tr>
                <th>Processing Location:</th>
                <td>{$service_data['processing_location']}</td>
            </tr>
            <tr>
                <th>Privacy Policy:</th>
                <td><a href="{$service_data['privacy_policy']}" target="_blank">{$service_data['privacy_policy']}</a></td>
            </tr>
            <tr>
                <th>How to Opt-Out:</th>
                <td>{$service_data['opt_out']}</td>
            </tr>
        </table>
    </div>

HTML;
        }

        $html .= "</div>";
        return $html;
    }

    /**
     * Social media services section
     */
    private function get_social_media_section() {
        return <<<HTML
<div class="policy-section">
    <h2>4. Social Media Services</h2>
    <p>We may embed social media content on our website. When you interact with these embeds, the social media platforms may collect data about you.</p>

    <div class="service-box social">
        <h3>YouTube (Google)</h3>
        <table class="service-details-table">
            <tr>
                <th>Provider:</th>
                <td>Google LLC</td>
            </tr>
            <tr>
                <th>Cookies Set:</th>
                <td><code>VISITOR_INFO1_LIVE</code>, <code>YSC</code>, <code>PREF</code>, <code>CONSENT</code></td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Video views, watch time, preferences, device info, YouTube account data (if logged in)</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Deliver video content, track views, personalize recommendations, serve targeted ads</td>
            </tr>
            <tr>
                <th>Privacy-Enhanced Mode:</th>
                <td>We use YouTube's privacy-enhanced mode (youtube-nocookie.com) which limits cookie placement until you play the video</td>
            </tr>
            <tr>
                <th>Privacy Policy:</th>
                <td><a href="https://policies.google.com/privacy" target="_blank">https://policies.google.com/privacy</a></td>
            </tr>
        </table>
    </div>

    <div class="service-box social">
        <h3>Facebook Social Plugins</h3>
        <table class="service-details-table">
            <tr>
                <th>Provider:</th>
                <td>Meta Platforms, Inc.</td>
            </tr>
            <tr>
                <th>Features Used:</th>
                <td>Like buttons, Share buttons, Embedded posts, Comments plugin</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Page URL, IP address, browser info, Facebook user ID (if logged in), interactions with buttons</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Enable social sharing, display Facebook content, track social interactions</td>
            </tr>
            <tr>
                <th>Privacy Policy:</th>
                <td><a href="https://www.facebook.com/privacy/policy/" target="_blank">https://www.facebook.com/privacy/policy/</a></td>
            </tr>
        </table>
    </div>

    <div class="service-box social">
        <h3>Twitter/X Embeds</h3>
        <table class="service-details-table">
            <tr>
                <th>Provider:</th>
                <td>Twitter, Inc. (X Corp)</td>
            </tr>
            <tr>
                <th>Features Used:</th>
                <td>Embedded tweets, Follow buttons, Tweet buttons</td>
            </tr>
            <tr>
                <th>Data Collected:</th>
                <td>Page URL, IP address, browser info, Twitter user ID (if logged in), interactions with buttons</td>
            </tr>
            <tr>
                <th>Purpose:</th>
                <td>Display Twitter content, enable social sharing, personalize Twitter experience</td>
            </tr>
            <tr>
                <th>Privacy Policy:</th>
                <td><a href="https://twitter.com/en/privacy" target="_blank">https://twitter.com/en/privacy</a></td>
            </tr>
        </table>
    </div>

    <h3>How Social Media Embeds Work</h3>
    <ul>
        <li><strong>Automatic Loading:</strong> When you visit a page with social media embeds, your browser automatically contacts those platforms</li>
        <li><strong>Tracking Across Sites:</strong> Social platforms can track your browsing across multiple websites that use their embeds</li>
        <li><strong>Logged-In Tracking:</strong> If you're logged into a social platform, they can connect your browsing to your account</li>
        <li><strong>Advertising Use:</strong> Data from embeds may be used to serve you targeted ads on social platforms</li>
    </ul>

    <h3>Controlling Social Media Tracking</h3>
    <ul>
        <li>Log out of social media accounts when browsing</li>
        <li>Use browser privacy modes or incognito windows</li>
        <li>Install browser extensions that block social media trackers</li>
        <li>Adjust privacy settings within each social media platform</li>
        <li>Toggle off relevant cookie categories in <a href="/cookie-preferences/">Cookie Preferences</a></li>
    </ul>
</div>
HTML;
    }

    /**
     * Data sharing details section
     */
    private function get_data_sharing_section() {
        return <<<HTML
<div class="policy-section">
    <h2>5. Data Sharing Details</h2>
    
    <h3>What Data Do We Share?</h3>
    <div class="data-sharing-box">
        <h4>Automatically Shared (via cookies/pixels):</h4>
        <ul>
            <li><strong>Technical Data:</strong> IP address, browser type, device type, operating system, screen resolution</li>
            <li><strong>Browsing Data:</strong> Pages visited, time on site, referral source, exit pages, clicks</li>
            <li><strong>Unique Identifiers:</strong> Cookie IDs, device IDs, advertising IDs</li>
        </ul>

        <h4>Shared by Us (server-to-server):</h4>
        <ul>
            <li><strong>Transaction Data:</strong> Purchase amount, product IDs, order IDs (for conversion tracking)</li>
            <li><strong>User Data:</strong> Email addresses (hashed), customer IDs (for customer matching)</li>
            <li><strong>Event Data:</strong> Form submissions, button clicks, custom events (for analytics)</li>
        </ul>
    </div>

    <h3>Legal Basis for Sharing</h3>
    <p>We share your data with third parties based on:</p>
    <ul>
        <li><strong>Your Consent:</strong> You accept cookies via our consent banner (GDPR Article 6(1)(a))</li>
        <li><strong>Legitimate Interest:</strong> To operate and improve our website (GDPR Article 6(1)(f))</li>
        <li><strong>Contract Necessity:</strong> To provide services you've requested (GDPR Article 6(1)(b))</li>
        <li><strong>Legal Obligation:</strong> To comply with laws and regulations (GDPR Article 6(1)(c))</li>
    </ul>

    <h3>Third Parties as Data Controllers</h3>
    <p>The following services act as <strong>independent data controllers</strong>, meaning they can use your data for their own purposes:</p>
    <ul>
        <li><strong>Google (Analytics, Ads, DoubleClick):</strong> May use data for improving their services, fraud prevention, and serving ads across their network</li>
        <li><strong>Facebook/Meta:</strong> May use data to improve ad targeting across Facebook, Instagram, and partner sites</li>
        <li><strong>Social Media Platforms:</strong> May use embed data to build profiles and personalize your social media experience</li>
    </ul>

    <div class="important-notice">
        <h4>⚠️ Important: Beyond Our Control</h4>
        <p>Once data is shared with third-party data controllers, their own privacy policies govern how they use it. While we require these services to comply with privacy laws, we cannot control their independent use of your data. <strong>Always review the privacy policies of third-party services.</strong></p>
    </div>

    <h3>Third Parties as Data Processors</h3>
    <p>The following services act as <strong>data processors</strong>, meaning they only process data on our instructions:</p>
    <ul>
        <li><strong>Web Hosting Provider:</strong> Stores website files and databases</li>
        <li><strong>Email Service Provider:</strong> Sends emails on our behalf</li>
        <li><strong>Payment Processor:</strong> Processes transactions securely</li>
        <li><strong>Self-Hosted Analytics (if using Matomo):</strong> Only processes data we control</li>
    </ul>
    <p><em>We have Data Processing Agreements (DPAs) with all data processors to ensure GDPR compliance.</em></p>

    <h3>Cross-Border Data Transfers</h3>
    <p>Some third-party services are located outside your country. Data may be transferred to:</p>
    <ul>
        <li><strong>United States:</strong> Google, Facebook/Meta, LinkedIn, Twitter (EU-US Data Privacy Framework)</li>
        <li><strong>European Union:</strong> Hotjar (Ireland, Germany), some hosting providers</li>
        <li><strong>Other Countries:</strong> Various CDN and infrastructure providers globally</li>
    </ul>
    <p><strong>Safeguards in Place:</strong> We only use services that provide adequate data protection through Standard Contractual Clauses (SCCs), EU-US Data Privacy Framework certification, or equivalent safeguards.</p>
</div>
HTML;
    }

    /**
     * Processing locations section
     */
    private function get_processing_locations_section() {
        return <<<HTML
<div class="policy-section">
    <h2>6. Data Processing Locations</h2>
    <p>Your data may be processed in the following locations:</p>

    <table class="locations-table">
        <thead>
            <tr>
                <th>Service Category</th>
                <th>Primary Location</th>
                <th>Additional Locations</th>
                <th>Safeguards</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Google Services</strong><br>(Analytics, Ads, YouTube)</td>
                <td>United States</td>
                <td>Global data centers (EU, Asia-Pacific, Americas)</td>
                <td>EU-US Data Privacy Framework, Standard Contractual Clauses</td>
            </tr>
            <tr>
                <td><strong>Facebook/Meta</strong><br>(Pixel, Social Plugins)</td>
                <td>United States</td>
                <td>EU (Ireland), Global data centers</td>
                <td>EU-US Data Privacy Framework, Standard Contractual Clauses</td>
            </tr>
            <tr>
                <td><strong>LinkedIn</strong></td>
                <td>United States</td>
                <td>EU (Ireland), Global offices</td>
                <td>EU-US Data Privacy Framework, Standard Contractual Clauses</td>
            </tr>
            <tr>
                <td><strong>Twitter/X</strong></td>
                <td>United States</td>
                <td>Global data centers</td>
                <td>Standard Contractual Clauses</td>
            </tr>
            <tr>
                <td><strong>Hotjar</strong></td>
                <td>European Union (Ireland, Germany)</td>
                <td>N/A - EU-only processing</td>
                <td>GDPR-compliant (EU-based)</td>
            </tr>
            <tr>
                <td><strong>Matomo Cloud</strong><br>(if applicable)</td>
                <td>European Union</td>
                <td>N/A - EU-only processing</td>
                <td>GDPR-compliant (EU-based)</td>
            </tr>
            <tr>
                <td><strong>Web Hosting</strong></td>
                <td>Varies by provider</td>
                <td>May use CDN (global)</td>
                <td>Data Processing Agreement</td>
            </tr>
        </tbody>
    </table>

    <h3>EU-US Data Privacy Framework</h3>
    <p>Many US-based services (Google, Facebook, LinkedIn) are certified under the <strong>EU-US Data Privacy Framework</strong>, which replaced Privacy Shield. This framework provides:</p>
    <ul>
        <li>Adequate data protection for EU-US transfers</li>
        <li>Enforceable privacy commitments</li>
        <li>Oversight by US Department of Commerce and FTC</li>
        <li>Right to access, correction, and deletion</li>
        <li>Independent dispute resolution</li>
    </ul>
    <p><em>You can verify a company's certification at <a href="https://www.dataprivacyframework.gov/list" target="_blank">dataprivacyframework.gov</a>.</em></p>

    <h3>Standard Contractual Clauses (SCCs)</h3>
    <p>For services not covered by adequacy decisions, we rely on <strong>Standard Contractual Clauses (SCCs)</strong> approved by the European Commission. SCCs are:</p>
    <ul>
        <li>Legally binding contracts between data exporters and importers</li>
        <li>Require equivalent data protection as in the EU</li>
        <li>Give you enforceable rights as a third-party beneficiary</li>
        <li>Allow you to lodge complaints with EU supervisory authorities</li>
    </ul>
</div>
HTML;
    }

    /**
     * Your control section
     */
    private function get_your_control_section() {
        return <<<HTML
<div class="policy-section">
    <h2>7. Your Control Over Third-Party Data Collection</h2>
    
    <div class="control-promo">
        <h3>🎛️ Cookie Preferences Center</h3>
        <p>The easiest way to control third-party services is through our Cookie Preferences Center:</p>
        <p style="text-align: center; margin: 20px 0;">
            <a href="/cookie-preferences/" class="btn-primary">Manage Cookie Preferences</a>
        </p>
        <p>You can toggle entire categories on/off:</p>
        <ul>
            <li><strong>Necessary Cookies:</strong> Always active (required for website functionality)</li>
            <li><strong>Analytics Cookies:</strong> Toggle to block Google Analytics, Hotjar, etc.</li>
            <li><strong>Marketing Cookies:</strong> Toggle to block Facebook Pixel, Google Ads, etc.</li>
            <li><strong>Preferences Cookies:</strong> Toggle to block YouTube, language preferences, etc.</li>
        </ul>
    </div>

    <h3>Browser-Level Controls</h3>
    
    <h4>Block All Third-Party Cookies</h4>
    <p>Most browsers let you block all third-party cookies:</p>
    <ul>
        <li><strong>Chrome:</strong> Settings → Privacy and security → Cookies → "Block third-party cookies"</li>
        <li><strong>Firefox:</strong> Settings → Privacy & Security → Enhanced Tracking Protection → "Strict"</li>
        <li><strong>Safari:</strong> Settings → Privacy → "Prevent cross-site tracking" (enabled by default)</li>
        <li><strong>Edge:</strong> Settings → Privacy → "Strict" tracking prevention</li>
    </ul>
    <p><em>Note: Blocking all third-party cookies may break some website features.</em></p>

    <h4>Do Not Track (DNT)</h4>
    <p>Enable "Do Not Track" in your browser settings to signal that you don't want to be tracked:</p>
    <ul>
        <li><strong>Chrome:</strong> Settings → Privacy and security → Send a "Do Not Track" request</li>
        <li><strong>Firefox:</strong> Settings → Privacy & Security → "Tell websites not to sell or share my data"</li>
        <li><strong>Safari:</strong> Settings → Privacy → "Ask websites not to track me"</li>
    </ul>
    <p><em>Note: Not all websites respect DNT signals. Our website honors DNT by defaulting to reject non-essential cookies.</em></p>

    <h3>Service-Specific Opt-Outs</h3>
    
    <h4>Google Services</h4>
    <ul>
        <li><strong>Google Analytics Opt-out:</strong> Install the <a href="https://tools.google.com/dlpage/gaoptout" target="_blank">Google Analytics Opt-out Browser Add-on</a></li>
        <li><strong>Google Ad Personalization:</strong> Visit <a href="https://adssettings.google.com/" target="_blank">Google Ad Settings</a> to turn off ad personalization</li>
        <li><strong>YouTube:</strong> Adjust <a href="https://www.youtube.com/account_privacy" target="_blank">YouTube Privacy Settings</a></li>
    </ul>

    <h4>Facebook/Meta</h4>
    <ul>
        <li><strong>Facebook Ad Preferences:</strong> Visit <a href="https://www.facebook.com/ads/preferences/" target="_blank">Facebook Ad Preferences</a></li>
        <li><strong>Off-Facebook Activity:</strong> View and clear your <a href="https://www.facebook.com/off_facebook_activity/" target="_blank">Off-Facebook Activity</a></li>
        <li><strong>Opt-out of Ads:</strong> Adjust settings at <a href="https://www.facebook.com/settings?tab=ads" target="_blank">Facebook Ad Settings</a></li>
    </ul>

    <h4>Other Services</h4>
    <ul>
        <li><strong>LinkedIn:</strong> <a href="https://www.linkedin.com/psettings/advertising" target="_blank">LinkedIn Ad Preferences</a></li>
        <li><strong>Twitter/X:</strong> <a href="https://twitter.com/settings/personalization" target="_blank">Twitter Personalization Settings</a></li>
        <li><strong>Hotjar:</strong> <a href="https://www.hotjar.com/policies/do-not-track/" target="_blank">Hotjar Opt-Out Page</a></li>
    </ul>

    <h3>Industry Opt-Out Tools</h3>
    <ul>
        <li><strong>Digital Advertising Alliance:</strong> <a href="https://optout.aboutads.info/" target="_blank">aboutads.info opt-out</a></li>
        <li><strong>Network Advertising Initiative:</strong> <a href="https://optout.networkadvertising.org/" target="_blank">NAI opt-out</a></li>
        <li><strong>European Interactive Digital Advertising Alliance:</strong> <a href="https://www.youronlinechoices.com/" target="_blank">Your Online Choices</a></li>
    </ul>

    <h3>Privacy-Focused Browser Extensions</h3>
    <p>Consider installing browser extensions that block trackers:</p>
    <ul>
        <li><strong>uBlock Origin:</strong> Blocks ads and trackers</li>
        <li><strong>Privacy Badger:</strong> Automatically learns and blocks trackers</li>
        <li><strong>Ghostery:</strong> Blocks ads, trackers, and speeds up page loads</li>
        <li><strong>DuckDuckGo Privacy Essentials:</strong> Blocks third-party trackers</li>
    </ul>

    <h3>Request Data Deletion from Third Parties</h3>
    <p>You have the right to request deletion of your data directly from third-party services. Links to privacy/data request pages:</p>
    <ul>
        <li><strong>Google:</strong> <a href="https://myaccount.google.com/delete-services-or-account" target="_blank">Delete your Google Account or services</a></li>
        <li><strong>Facebook:</strong> <a href="https://www.facebook.com/help/contact/1638046109617856" target="_blank">Facebook Data Deletion Request</a></li>
        <li><strong>LinkedIn:</strong> <a href="https://www.linkedin.com/help/linkedin/ask/TS-RDR" target="_blank">LinkedIn Data Request</a></li>
        <li><strong>Twitter:</strong> <a href="https://help.twitter.com/en/managing-your-account/how-to-download-your-twitter-archive" target="_blank">Twitter Data Download</a></li>
    </ul>
    <p><em>We can also submit data deletion requests on your behalf when you submit a Data Subject Rights Request through our <a href="/dsr-portal/">DSR Portal</a>.</em></p>
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
    <h2>8. Contact Us About Third-Party Services</h2>
    <p>If you have questions about the third-party services we use, how they collect data, or how to opt-out, please contact us:</p>

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

    <h3>Quick Links</h3>
    <ul>
        <li><a href="/privacy-policy/">Privacy Policy</a> - Our complete privacy practices</li>
        <li><a href="/cookie-policy/">Cookie Policy</a> - Detailed cookie information</li>
        <li><a href="/cookie-categories-reference/">Cookie Categories Reference</a> - Explanation of cookie categories</li>
        <li><a href="/consent-management-policy/">Consent Management Policy</a> - How our consent system works</li>
        <li><a href="/user-rights-notice/">Your Privacy Rights</a> - How to exercise your rights</li>
    </ul>

    <p><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
</div>
HTML;
    }
}
