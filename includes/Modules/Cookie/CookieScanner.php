<?php
/**
 * Cookie Scanner
 *
 * @package ComplyFlow\Modules\Cookie
 * @since   3.3.1
 */

namespace ComplyFlow\Modules\Cookie;

use ComplyFlow\Core\SettingsRepository;
use WP_Error;

class CookieScanner {
    
    private SettingsRepository $settings;
    
    private array $tracking_patterns = [
        'google-analytics' => [
            'pattern' => '/googletagmanager\.com\/gtag|google-analytics\.com\/analytics\.js|ga\.js|gtag\/js/',
            'category' => 'analytics',
            'name' => 'Google Analytics',
            'cookies' => ['_ga', '_gid', '_gat', '_gat_gtag_*', '_ga_*'],
        ],
        'google-ads' => [
            'pattern' => '/googleadservices\.com|googlesyndication\.com|doubleclick\.net/',
            'category' => 'marketing',
            'name' => 'Google Ads',
            'cookies' => ['_gcl_au', '_gcl_aw', '_gcl_dc', 'test_cookie', 'IDE', 'DSID'],
        ],
        'google-tag-manager' => [
            'pattern' => '/googletagmanager\.com\/gtm\.js/',
            'category' => 'analytics',
            'name' => 'Google Tag Manager',
            'cookies' => ['_dc_gtm_*'],
        ],
        'google-optimize' => [
            'pattern' => '/optimize\.google\.com/',
            'category' => 'analytics',
            'name' => 'Google Optimize',
            'cookies' => ['_gaexp', '_opt_awcid', '_opt_awmid', '_opt_awgid', '_opt_awkid', '_opt_utmc'],
        ],
        'facebook-pixel' => [
            'pattern' => '/connect\.facebook\.net\/.*\/fbevents\.js/',
            'category' => 'marketing',
            'name' => 'Facebook Pixel',
            'cookies' => ['_fbp', '_fbc', 'fr', 'sb', 'datr'],
        ],
        'hotjar' => [
            'pattern' => '/static\.hotjar\.com\/c\/hotjar-/',
            'category' => 'analytics',
            'name' => 'Hotjar',
            'cookies' => ['_hjid', '_hjIncludedInSample', '_hjAbsoluteSessionInProgress', '_hjFirstSeen', '_hjSessionUser_*', '_hjSession_*'],
        ],
        'tiktok' => [
            'pattern' => '/analytics\.tiktok\.com|tiktok\.com\/i18n\/pixel/',
            'category' => 'marketing',
            'name' => 'TikTok Pixel',
            'cookies' => ['_ttp', '_tta', 'tt_appInfo', 'tt_sessionId'],
        ],
        'linkedin' => [
            'pattern' => '/snap\.licdn\.com|platform\.linkedin\.com/',
            'category' => 'marketing',
            'name' => 'LinkedIn Insight Tag',
            'cookies' => ['li_sugr', 'UserMatchHistory', 'bcookie', 'lidc', 'bscookie'],
        ],
        'twitter' => [
            'pattern' => '/platform\.twitter\.com|static\.ads-twitter\.com/',
            'category' => 'marketing',
            'name' => 'Twitter Pixel',
            'cookies' => ['personalization_id', 'guest_id', 'ct0', 'auth_token'],
        ],
        'pinterest' => [
            'pattern' => '/ct\.pinterest\.com/',
            'category' => 'marketing',
            'name' => 'Pinterest Tag',
            'cookies' => ['_pinterest_ct_ua', '_pinterest_sess', '_epik', '_pin_unauth'],
        ],
        'snapchat' => [
            'pattern' => '/sc-static\.net\/scevent\.min\.js/',
            'category' => 'marketing',
            'name' => 'Snapchat Pixel',
            'cookies' => ['_scid', '_scid_r', '_gcl_au'],
        ],
        'youtube' => [
            'pattern' => '/youtube\.com\/iframe_api|youtube-nocookie\.com|youtube\.com\/embed/',
            'category' => 'functional',
            'name' => 'YouTube Embeds',
            'cookies' => ['VISITOR_INFO1_LIVE', 'YSC', 'CONSENT', 'GPS', 'PREF'],
        ],
        'vimeo' => [
            'pattern' => '/player\.vimeo\.com/',
            'category' => 'functional',
            'name' => 'Vimeo Player',
            'cookies' => ['vuid', 'player'],
        ],
        'stripe' => [
            'pattern' => '/js\.stripe\.com/',
            'category' => 'necessary',
            'name' => 'Stripe Payment',
            'cookies' => ['__stripe_mid', '__stripe_sid', 'cid', 'machine_identifier'],
        ],
        'paypal' => [
            'pattern' => '/paypal\.com\/sdk|paypalobjects\.com/',
            'category' => 'necessary',
            'name' => 'PayPal',
            'cookies' => ['ts_c', 'x-pp-s', 'l7_az', 'tsrce', 'ts', 'enforce_policy'],
        ],
        'mailchimp' => [
            'pattern' => '/chimpstatic\.com|list-manage\.com/',
            'category' => 'marketing',
            'name' => 'Mailchimp',
            'cookies' => ['_AVESTA_ENVIRONMENT', 'ak_bmsc'],
        ],
        'hubspot' => [
            'pattern' => '/js\.hs-scripts\.com|js\.hs-analytics\.net/',
            'category' => 'analytics',
            'name' => 'HubSpot',
            'cookies' => ['__hstc', '__hssc', '__hssrc', 'hubspotutk', 'messagesUtk'],
        ],
        'intercom' => [
            'pattern' => '/widget\.intercom\.io/',
            'category' => 'functional',
            'name' => 'Intercom',
            'cookies' => ['intercom-id-*', 'intercom-session-*'],
        ],
        'drift' => [
            'pattern' => '/js\.driftt\.com/',
            'category' => 'functional',
            'name' => 'Drift Chat',
            'cookies' => ['driftt_aid', 'drift_aid', 'drift_campaign_refresh'],
        ],
        'zendesk' => [
            'pattern' => '/static\.zdassets\.com|zendesk\.com\/embeddable/',
            'category' => 'functional',
            'name' => 'Zendesk Chat',
            'cookies' => ['_zendesk_cookie', '_help_center_session', '__zlcmid'],
        ],
        'cloudflare' => [
            'pattern' => '/cloudflare\.com|cf-cdn\.com/',
            'category' => 'necessary',
            'name' => 'Cloudflare',
            'cookies' => ['__cfduid', '__cf_bm', 'cf_ob_info', 'cf_use_ob'],
        ],
        'recaptcha' => [
            'pattern' => '/recaptcha\/api\.js|recaptcha\/enterprise\.js/',
            'category' => 'necessary',
            'name' => 'Google reCAPTCHA',
            'cookies' => ['_GRECAPTCHA', 'rc::a', 'rc::b', 'rc::c'],
        ],
        'matomo' => [
            'pattern' => '/matomo\.js|piwik\.js/',
            'category' => 'analytics',
            'name' => 'Matomo Analytics',
            'cookies' => ['_pk_id', '_pk_ses', '_pk_ref', '_pk_cvar', '_pk_hsr'],
        ],
        'mixpanel' => [
            'pattern' => '/cdn\.mxpnl\.com/',
            'category' => 'analytics',
            'name' => 'Mixpanel',
            'cookies' => ['mp_*_mixpanel'],
        ],
        'segment' => [
            'pattern' => '/cdn\.segment\.com/',
            'category' => 'analytics',
            'name' => 'Segment',
            'cookies' => ['ajs_anonymous_id', 'ajs_user_id', 'ajs_group_id'],
        ],
    ];

    public function __construct(SettingsRepository $settings) {
        $this->settings = $settings;
    }

    public function init(): void {
        // Initialization if needed
    }

    public function scan_site(string $url = ''): array|WP_Error {
        if (empty($url)) {
            $url = home_url();
        }

        // Fetch page HTML
        $sslverify = apply_filters('complyflow_cookie_scanner_sslverify', true, $url);
        if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
            $sslverify = false;
        }
        $response = wp_remote_get($url, [
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
            'sslverify' => $sslverify,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $html = wp_remote_retrieve_body($response);
        $detected = [];

        // Scan for tracking scripts
        foreach ($this->tracking_patterns as $key => $tracker) {
            if (preg_match($tracker['pattern'], $html)) {
                foreach ($tracker['cookies'] as $cookie_name) {
                    $detected[] = [
                        'name' => $cookie_name,
                        'provider' => $tracker['name'],
                        'category' => $tracker['category'],
                        'type' => 'tracking',
                        'purpose' => $this->get_cookie_purpose($cookie_name, $tracker['name']),
                        'expiry' => $this->get_typical_expiry($cookie_name),
                        'detected_at' => current_time('mysql'),
                    ];
                }
            }
        }

        // Scan for WordPress core cookies
        $detected = array_merge($detected, $this->get_wordpress_cookies());

        // Scan for WooCommerce cookies if active
        if (class_exists('WooCommerce')) {
            $detected = array_merge($detected, $this->get_woocommerce_cookies());
        }

        // Remove duplicates based on cookie name
        $detected = array_values(array_reduce($detected, function($carry, $item) {
            $carry[$item['name']] = $item;
            return $carry;
        }, []));

        return apply_filters('complyflow_scanned_cookies', $detected, $url);
    }

    private function get_wordpress_cookies(): array {
        return [
            [
                'name' => 'wordpress_test_cookie',
                'provider' => 'WordPress Core',
                'category' => 'necessary',
                'type' => 'functional',
                'purpose' => 'Used to check if cookies are enabled in the browser',
                'expiry' => 'Session',
                'detected_at' => current_time('mysql'),
            ],
            [
                'name' => 'wordpress_logged_in_*',
                'provider' => 'WordPress Core',
                'category' => 'necessary',
                'type' => 'authentication',
                'purpose' => 'Indicates when you are logged in and who you are',
                'expiry' => '14 days',
                'detected_at' => current_time('mysql'),
            ],
            [
                'name' => 'wp-settings-*',
                'provider' => 'WordPress Core',
                'category' => 'functional',
                'type' => 'preferences',
                'purpose' => 'Stores user interface customization settings',
                'expiry' => '1 year',
                'detected_at' => current_time('mysql'),
            ],
        ];
    }

    private function get_woocommerce_cookies(): array {
        return [
            [
                'name' => 'woocommerce_cart_hash',
                'provider' => 'WooCommerce',
                'category' => 'necessary',
                'type' => 'ecommerce',
                'purpose' => 'Helps WooCommerce determine when cart contents change',
                'expiry' => 'Session',
                'detected_at' => current_time('mysql'),
            ],
            [
                'name' => 'woocommerce_items_in_cart',
                'provider' => 'WooCommerce',
                'category' => 'necessary',
                'type' => 'ecommerce',
                'purpose' => 'Helps WooCommerce determine if cart has data',
                'expiry' => 'Session',
                'detected_at' => current_time('mysql'),
            ],
            [
                'name' => 'wp_woocommerce_session_*',
                'provider' => 'WooCommerce',
                'category' => 'necessary',
                'type' => 'ecommerce',
                'purpose' => 'Contains a unique code for each customer for cart and checkout',
                'expiry' => '2 days',
                'detected_at' => current_time('mysql'),
            ],
        ];
    }

    private function get_cookie_purpose(string $cookie_name, string $provider): string {
        $purposes = [
            '_ga' => 'Used to distinguish users and sessions',
            '_gid' => 'Used to distinguish users',
            '_gat' => 'Used to throttle request rate',
            '_gcl_au' => 'Used by Google AdSense for advertising efficiency',
            '_fbp' => 'Used to deliver advertising when on Facebook or digital platforms',
            'fr' => 'Contains browser and user unique ID for targeted advertising',
            '_hjid' => 'Hotjar cookie for persistent user session ID',
            '_hjIncludedInSample' => 'Determines if user is included in data sampling',
            '_ttp' => 'Used for tracking and targeting for advertising',
            'li_sugr' => 'Used for tracking and analytics',
            'personalization_id' => 'Twitter advertising tracking',
            'VISITOR_INFO1_LIVE' => 'YouTube cookie for tracking video views',
            'YSC' => 'YouTube cookie for tracking unique user sessions',
            '__stripe_mid' => 'Fraud prevention and detection',
        ];

        return $purposes[$cookie_name] ?? "Used by {$provider} for functionality";
    }

    private function get_typical_expiry(string $cookie_name): string {
        $expiries = [
            '_ga' => '2 years',
            '_gid' => '24 hours',
            '_gat' => '1 minute',
            '_gcl_au' => '90 days',
            '_fbp' => '90 days',
            'fr' => '90 days',
            '_hjid' => '1 year',
            '_hjIncludedInSample' => 'Session',
            '_ttp' => '13 months',
            'li_sugr' => '90 days',
            'personalization_id' => '2 years',
            'VISITOR_INFO1_LIVE' => '6 months',
            'YSC' => 'Session',
            '__stripe_mid' => '1 year',
        ];

        return $expiries[$cookie_name] ?? 'Varies';
    }

    public function scan_javascript_cookies(string $html): array {
        $detected = [];
        
        // Extract all script tags
        preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $html, $scripts);
        
        foreach ($scripts[1] as $script) {
            // Look for document.cookie assignments
            if (preg_match_all('/document\.cookie\s*=\s*["\']([^=]+)=/i', $script, $matches)) {
                foreach ($matches[1] as $cookie_name) {
                    $detected[] = [
                        'name' => trim($cookie_name),
                        'provider' => 'JavaScript',
                        'category' => 'functional',
                        'type' => 'script',
                        'purpose' => 'Set via JavaScript',
                        'expiry' => 'Unknown',
                        'detected_at' => current_time('mysql'),
                    ];
                }
            }
        }
        
        return $detected;
    }
}
