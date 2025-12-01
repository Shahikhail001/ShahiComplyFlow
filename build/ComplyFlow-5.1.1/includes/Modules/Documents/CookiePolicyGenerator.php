<?php
/**
 * Cookie Policy Generator
 *
 * Generates customized Cookie Policy based on questionnaire answers
 * and detected cookies from CookieScanner.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   3.0.0
 */

namespace ComplyFlow\Modules\Documents;

use ComplyFlow\Modules\Consent\CookieScanner;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CookiePolicyGenerator
 */
class CookiePolicyGenerator {
    /**
     * Questionnaire answers
     *
     * @var array
     */
    private array $answers;

    /**
     * Cookie scanner instance
     *
     * @var CookieScanner
     */
    private CookieScanner $cookie_scanner;

    /**
     * Detected cookies
     *
     * @var array
     */
    private array $detected_cookies = [];

    /**
     * Constructor
     *
     * @param array $answers Questionnaire answers.
     */
    public function __construct(array $answers) {
        $this->answers = $answers;
        
        // Initialize CookieScanner with SettingsRepository
        require_once COMPLYFLOW_PATH . 'includes/Core/Repositories/SettingsRepository.php';
        $settings_repository = new \ComplyFlow\Core\Repositories\SettingsRepository();
        $this->cookie_scanner = new CookieScanner($settings_repository);
        
        $this->load_detected_cookies();
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
        ];
    }

    /**
     * Generate Cookie Policy
     *
     * @return string Generated Cookie Policy HTML.
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
            'WHAT_ARE_COOKIES_SECTION' => $this->render_what_are_cookies(),
            'COOKIES_WE_USE_SECTION' => $this->render_cookies_we_use(),
            'COOKIE_CATEGORIES_SECTION' => $this->render_cookie_categories(),
            'THIRD_PARTY_COOKIES_SECTION' => $this->render_third_party_cookies(),
            'MANAGING_COOKIES_SECTION' => $this->render_managing_cookies(),
            'CONSENT_SECTION' => $this->render_consent(),
            'UPDATES_SECTION' => $this->render_updates(),
            'CONTACT_SECTION' => $this->render_contact(),
        ];
    }

    /**
     * Load detected cookies from CookieScanner
     *
     * @return void
     */
    private function load_detected_cookies(): void {
        // Get cookies from settings (saved by CookieScanner)
        $saved_cookies = get_option('complyflow_detected_cookies', []);
        
        if (empty($saved_cookies)) {
            // Perform a scan if no cookies are saved
            $home_url = home_url();
            $this->detected_cookies = $this->cookie_scanner->scan_cookies($home_url);
        } else {
            $this->detected_cookies = $saved_cookies;
        }
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
     * Render what are cookies section
     *
     * @return string Section content.
     */
    private function render_what_are_cookies(): string {
        return $this->load_snippet('what-are-cookies');
    }

    /**
     * Render cookies we use section
     *
     * @return string Section content.
     */
    private function render_cookies_we_use(): string {
        if (empty($this->detected_cookies)) {
            return '<p>We currently do not use any cookies on our Site. If this changes in the future, we will update this Cookie Policy accordingly.</p>';
        }

        $content = '<h2>Cookies We Use</h2>';
        $content .= '<p>The following table lists all cookies used on our Site:</p>';
        
        // Group cookies by category
        $cookies_by_category = $this->group_cookies_by_category();

        foreach ($cookies_by_category as $category => $cookies) {
            $content .= '<h3>' . esc_html(ucfirst($category)) . ' Cookies</h3>';
            $content .= '<table class="cookie-table">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th>Cookie Name</th>';
            $content .= '<th>Domain</th>';
            $content .= '<th>Purpose</th>';
            $content .= '<th>Expiry</th>';
            $content .= '</tr>';
            $content .= '</thead>';
            $content .= '<tbody>';

            foreach ($cookies as $cookie) {
                $content .= '<tr>';
                $content .= '<td><code>' . esc_html($cookie['name']) . '</code></td>';
                $content .= '<td>' . esc_html($cookie['domain'] ?? 'This site') . '</td>';
                $content .= '<td>' . esc_html($cookie['description'] ?? 'No description available') . '</td>';
                $content .= '<td>' . esc_html($cookie['expiry'] ?? 'Session') . '</td>';
                $content .= '</tr>';
            }

            $content .= '</tbody>';
            $content .= '</table>';
        }

        return $content;
    }

    /**
     * Render cookie categories section
     *
     * @return string Section content.
     */
    private function render_cookie_categories(): string {
        $content = $this->load_snippet('cookie-categories');

        // Add information about which categories we use
        $categories_used = $this->get_categories_used();
        
        if (!empty($categories_used)) {
            $content .= '<h3>Cookie Categories We Use</h3>';
            $content .= '<p>Based on our scan, we use the following types of cookies:</p>';
            $content .= '<ul>';
            foreach ($categories_used as $category) {
                $content .= '<li><strong>' . esc_html(ucfirst($category)) . ' Cookies</strong></li>';
            }
            $content .= '</ul>';
        }

        return $content;
    }

    /**
     * Render third-party cookies section
     *
     * @return string Section content.
     */
    private function render_third_party_cookies(): string {
        $third_party_cookies = $this->get_third_party_cookies();

        if (empty($third_party_cookies)) {
            return '<h2>Third-Party Cookies</h2><p>We do not currently use any third-party cookies on our Site.</p>';
        }

        $content = $this->load_snippet('third-party-cookies-intro');

        // Add detected third-party services
        $services = $this->detect_third_party_services();
        
        if (!empty($services)) {
            $content .= '<h3>Third-Party Services We Use</h3>';
            $content .= '<p>We use the following third-party services that may set cookies:</p>';
            $content .= '<ul>';
            
            foreach ($services as $service) {
                $content .= '<li><strong>' . esc_html($service['name']) . ':</strong> ' . esc_html($service['purpose']) . '</li>';
            }
            
            $content .= '</ul>';
        }

        return $content;
    }

    /**
     * Render managing cookies section
     *
     * @return string Section content.
     */
    private function render_managing_cookies(): string {
        $content = $this->load_snippet('managing-cookies');

        // Add link to consent preferences if consent module is enabled
        if (class_exists('ComplyFlow\Modules\Consent\ConsentModule')) {
            $preferences_url = home_url('/cookie-preferences/');
            $content .= '<div class="cookie-highlight">';
            $content .= '<h3>Manage Your Cookie Preferences</h3>';
            $content .= '<p>You can manage your cookie preferences at any time by visiting our <a href="' . esc_url($preferences_url) . '">Cookie Preferences Center</a>.</p>';
            $content .= '</div>';
        }

        return $content;
    }

    /**
     * Render consent section
     *
     * @return string Section content.
     */
    private function render_consent(): string {
        $content = $this->load_snippet('consent');
        
        // Add compliance-specific consent language
        $enabled_modes = $this->get_enabled_compliance_modes();
        
        if ($enabled_modes['GDPR'] || $enabled_modes['UK_GDPR']) {
            $content .= '<div class="cookie-highlight">';
            $content .= '<h3>GDPR Cookie Consent</h3>';
            $content .= '<p>Under GDPR, we require your explicit consent before placing non-essential cookies. You can withdraw your consent at any time through our Cookie Preferences Center.</p>';
            $content .= '</div>';
        }
        
        if ($enabled_modes['CCPA']) {
            $content .= '<div class="cookie-highlight">';
            $content .= '<h3>California Consumer Privacy Act (CCPA)</h3>';
            $content .= '<p>California residents have the right to opt-out of the "sale" of personal information. We provide a "Do Not Sell My Personal Information" link in our consent banner.</p>';
            $content .= '</div>';
        }
        
        return $content;
    }

    /**
     * Render updates section
     *
     * @return string Section content.
     */
    private function render_updates(): string {
        return $this->load_snippet('updates');
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
     * Group cookies by category
     *
     * @return array Cookies grouped by category.
     */
    private function group_cookies_by_category(): array {
        $grouped = [];

        foreach ($this->detected_cookies as $cookie) {
            $category = $cookie['category'] ?? 'necessary';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $cookie;
        }

        return $grouped;
    }

    /**
     * Get categories used
     *
     * @return array List of cookie categories in use.
     */
    private function get_categories_used(): array {
        $categories = [];

        foreach ($this->detected_cookies as $cookie) {
            $category = $cookie['category'] ?? 'necessary';
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * Get third-party cookies
     *
     * @return array Third-party cookies.
     */
    private function get_third_party_cookies(): array {
        $site_domain = wp_parse_url(get_site_url(), PHP_URL_HOST);
        $third_party = [];

        foreach ($this->detected_cookies as $cookie) {
            $cookie_domain = $cookie['domain'] ?? '';
            if (!empty($cookie_domain) && $cookie_domain !== $site_domain && !str_contains($cookie_domain, $site_domain)) {
                $third_party[] = $cookie;
            }
        }

        return $third_party;
    }

    /**
     * Detect third-party services from cookies
     *
     * @return array Detected services.
     */
    private function detect_third_party_services(): array {
        $services = [];
        $detected = [];

        foreach ($this->detected_cookies as $cookie) {
            $name = $cookie['name'] ?? '';
            $domain = $cookie['domain'] ?? '';

            // Google Analytics
            if ((str_starts_with($name, '_ga') || str_starts_with($name, '_gid')) && !in_array('google_analytics', $detected)) {
                $services[] = [
                    'name' => 'Google Analytics',
                    'purpose' => 'Analyzes website traffic and user behavior to help us improve our Site',
                ];
                $detected[] = 'google_analytics';
            }

            // Facebook
            if ((str_starts_with($name, '_fb') || str_starts_with($name, 'fr')) && !in_array('facebook', $detected)) {
                $services[] = [
                    'name' => 'Facebook Pixel',
                    'purpose' => 'Tracks conversions, optimizes ads, and builds targeted audiences',
                ];
                $detected[] = 'facebook';
            }

            // YouTube
            if ((str_contains($domain, 'youtube.com') || str_contains($name, 'VISITOR_INFO')) && !in_array('youtube', $detected)) {
                $services[] = [
                    'name' => 'YouTube',
                    'purpose' => 'Embeds video content and tracks video engagement',
                ];
                $detected[] = 'youtube';
            }

            // Hotjar
            if (str_starts_with($name, '_hj') && !in_array('hotjar', $detected)) {
                $services[] = [
                    'name' => 'Hotjar',
                    'purpose' => 'Provides heatmaps and session recordings to understand user behavior',
                ];
                $detected[] = 'hotjar';
            }
        }

        return $services;
    }

    /**
     * Load template file
     *
     * @return string Template content.
     */
    private function load_template(): string {
        $template_path = COMPLYFLOW_PATH . 'templates/policies/cookie-policy-template.php';

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
     * @return string Snippet content.
     */
    private function load_snippet(string $snippet): string {
        $snippet_path = COMPLYFLOW_PATH . 'templates/policies/snippets/cookie-' . $snippet . '.php';

        if (!file_exists($snippet_path)) {
            return '';
        }

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
