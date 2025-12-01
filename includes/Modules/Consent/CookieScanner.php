<?php
/**
 * Cookie Scanner
 *
 * Scans and categorizes cookies.
 *
 * @package ComplyFlow\Modules\Consent
 * @since   1.0.0
 */

namespace ComplyFlow\Modules\Consent;

use ComplyFlow\Core\Repositories\SettingsRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CookieScanner
 */
class CookieScanner {
    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Known cookie patterns
     *
     * @var array
     */
    private array $known_cookies = [
        // WordPress
        'wordpress_' => ['category' => 'necessary', 'description' => 'WordPress session cookies'],
        'wp-settings' => ['category' => 'necessary', 'description' => 'WordPress user settings'],
        'wp_woocommerce' => ['category' => 'necessary', 'description' => 'WooCommerce cart'],
        
        // Analytics
        '_ga' => ['category' => 'analytics', 'description' => 'Google Analytics'],
        '_gid' => ['category' => 'analytics', 'description' => 'Google Analytics'],
        '_gat' => ['category' => 'analytics', 'description' => 'Google Analytics'],
        '_gcl' => ['category' => 'analytics', 'description' => 'Google Analytics'],
        
        // Marketing
        '_fbp' => ['category' => 'marketing', 'description' => 'Facebook Pixel'],
        'fr' => ['category' => 'marketing', 'description' => 'Facebook advertising'],
        'IDE' => ['category' => 'marketing', 'description' => 'Google DoubleClick'],
        'test_cookie' => ['category' => 'marketing', 'description' => 'Google DoubleClick test'],
        
        // Preferences
        'PREF' => ['category' => 'preferences', 'description' => 'YouTube preferences'],
        'YSC' => ['category' => 'preferences', 'description' => 'YouTube session'],
    ];

    /**
     * Constructor
     *
     * @param SettingsRepository $settings Settings repository.
     */
    public function __construct(SettingsRepository $settings) {
        $this->settings = $settings;
    }

    /**
     * Scan cookies from a URL
     *
     * @param string $url URL to scan.
     * @return array Detected cookies.
     */
    public function scan_cookies(string $url): array {
        $cookies = [];

        // Method 1: Get cookies from WordPress
        $wp_cookies = $this->get_wordpress_cookies();
        $cookies = array_merge($cookies, $wp_cookies);

        // Method 2: Scan HTML for cookie-setting scripts
        $html_cookies = $this->scan_html_for_cookies($url);
        $cookies = array_merge($cookies, $html_cookies);

        // Remove duplicates
        $cookies = $this->deduplicate_cookies($cookies);

        // Categorize cookies
        $cookies = array_map([$this, 'categorize_cookie'], $cookies);

        return $cookies;
    }

    /**
     * Get WordPress cookies
     *
     * @return array Cookies.
     */
    private function get_wordpress_cookies(): array {
        $cookies = [];

        // WordPress auth cookies
        $cookies[] = [
            'name' => 'wordpress_logged_in_*',
            'domain' => parse_url(home_url(), PHP_URL_HOST),
            'path' => '/',
            'expiry' => 'Session',
            'description' => 'WordPress authentication',
            'category' => 'necessary',
        ];

        // WordPress test cookie
        $cookies[] = [
            'name' => 'wordpress_test_cookie',
            'domain' => parse_url(home_url(), PHP_URL_HOST),
            'path' => '/',
            'expiry' => 'Session',
            'description' => 'WordPress test cookie',
            'category' => 'necessary',
        ];

        // Check for WooCommerce
        if (class_exists('WooCommerce')) {
            $cookies[] = [
                'name' => 'woocommerce_cart_hash',
                'domain' => parse_url(home_url(), PHP_URL_HOST),
                'path' => '/',
                'expiry' => 'Session',
                'description' => 'WooCommerce shopping cart',
                'category' => 'necessary',
            ];

            $cookies[] = [
                'name' => 'woocommerce_items_in_cart',
                'domain' => parse_url(home_url(), PHP_URL_HOST),
                'path' => '/',
                'expiry' => 'Session',
                'description' => 'WooCommerce cart items',
                'category' => 'necessary',
            ];
        }

        return $cookies;
    }

    /**
     * Scan HTML for cookie-setting scripts
     *
     * @param string $url URL to scan.
     * @return array Detected cookies.
     */
    private function scan_html_for_cookies(string $url): array {
        $cookies = [];

        // Fetch HTML
        $response = wp_remote_get($url, [
            'timeout' => 15,
            'sslverify' => false,
        ]);

        if (is_wp_error($response)) {
            return $cookies;
        }

        $html = wp_remote_retrieve_body($response);

        // Detect Google Analytics
        if (preg_match('/gtag|ga\.js|analytics\.js|googletagmanager\.com/i', $html)) {
            $cookies[] = [
                'name' => '_ga',
                'domain' => '.google.com',
                'path' => '/',
                'expiry' => '2 years',
                'description' => 'Google Analytics - Used to distinguish users',
                'category' => 'analytics',
            ];

            $cookies[] = [
                'name' => '_gid',
                'domain' => '.google.com',
                'path' => '/',
                'expiry' => '24 hours',
                'description' => 'Google Analytics - Used to distinguish users',
                'category' => 'analytics',
            ];
        }

        // Detect Facebook Pixel
        if (preg_match('/facebook\.com\/tr|fbevents\.js|connect\.facebook\.net/i', $html)) {
            $cookies[] = [
                'name' => '_fbp',
                'domain' => '.facebook.com',
                'path' => '/',
                'expiry' => '3 months',
                'description' => 'Facebook Pixel - Used for advertising and analytics',
                'category' => 'marketing',
            ];

            $cookies[] = [
                'name' => 'fr',
                'domain' => '.facebook.com',
                'path' => '/',
                'expiry' => '3 months',
                'description' => 'Facebook - Used for advertising',
                'category' => 'marketing',
            ];
        }

        // Detect YouTube
        if (preg_match('/youtube\.com|youtu\.be/i', $html)) {
            $cookies[] = [
                'name' => 'VISITOR_INFO1_LIVE',
                'domain' => '.youtube.com',
                'path' => '/',
                'expiry' => '6 months',
                'description' => 'YouTube - Used to track visitor behavior',
                'category' => 'preferences',
            ];

            $cookies[] = [
                'name' => 'YSC',
                'domain' => '.youtube.com',
                'path' => '/',
                'expiry' => 'Session',
                'description' => 'YouTube - Used to track video views',
                'category' => 'preferences',
            ];
        }

        return $cookies;
    }

    /**
     * Categorize cookie
     *
     * @param array $cookie Cookie data.
     * @return array Categorized cookie.
     */
    private function categorize_cookie(array $cookie): array {
        if (isset($cookie['category'])) {
            return $cookie;
        }

        // Try to match against known patterns
        $cookie_name = $cookie['name'];

        foreach ($this->known_cookies as $pattern => $info) {
            if (strpos($cookie_name, $pattern) !== false) {
                $cookie['category'] = $info['category'];
                if (empty($cookie['description'])) {
                    $cookie['description'] = $info['description'];
                }
                return $cookie;
            }
        }

        // Default to necessary if unknown
        $cookie['category'] = $cookie['category'] ?? 'necessary';
        $cookie['description'] = $cookie['description'] ?? __('Unknown cookie', 'complyflow');

        return $cookie;
    }

    /**
     * Deduplicate cookies
     *
     * @param array $cookies Cookies array.
     * @return array Deduplicated cookies.
     */
    private function deduplicate_cookies(array $cookies): array {
        $unique = [];
        $seen = [];

        foreach ($cookies as $cookie) {
            $key = $cookie['name'] . '|' . $cookie['domain'];
            
            if (!in_array($key, $seen, true)) {
                $unique[] = $cookie;
                $seen[] = $key;
            }
        }

        return $unique;
    }

    /**
     * Get managed cookies
     *
     * @return array Managed cookies from settings.
     */
    public function get_managed_cookies(): array {
        return $this->settings->get('consent_cookie_categories', [
            'necessary' => [],
            'analytics' => [],
            'marketing' => [],
            'preferences' => [],
        ]);
    }

    /**
     * Save managed cookies
     *
     * @param array $cookies Cookies to save.
     * @return bool Success status.
     */
    public function save_managed_cookies(array $cookies): bool {
        return $this->settings->set('consent_cookie_categories', $cookies);
    }
}
