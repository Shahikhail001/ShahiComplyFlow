<?php
/**
 * Script Blocker
 *
 * Blocks scripts until consent is given.
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
 * Class ScriptBlocker
 */
class ScriptBlocker {
    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Scripts to block
     *
     * @var array
     */
    private array $blocked_scripts = [
        'analytics' => [
            'google-analytics.com/ga.js',
            'google-analytics.com/analytics.js',
            'googletagmanager.com/gtag/js',
            'googletagmanager.com/gtm.js',
        ],
        'marketing' => [
            'connect.facebook.net',
            'facebook.com/tr',
            'doubleclick.net',
            'ads.google.com',
            'adservice.google.com',
        ],
        'preferences' => [
            'youtube.com/iframe_api',
            'youtube.com/embed',
        ],
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
     * Start output buffering
     *
     * @return void
     */
    public function start_output_buffering(): void {
        $auto_block = $this->settings->get('consent_auto_block', true);

        if (!$auto_block) {
            return;
        }

        // Check if user has consented
        if (!isset($_COOKIE['complyflow_consent'])) {
            ob_start([$this, 'process_output']);
        } else {
            $consent = json_decode(stripslashes($_COOKIE['complyflow_consent']), true);
            
            // If user hasn't consented to everything, still need to block
            if (!$consent['analytics'] || !$consent['marketing'] || !$consent['preferences']) {
                ob_start([$this, 'process_output']);
            }
        }
    }

    /**
     * End output buffering
     *
     * @return void
     */
    public function end_output_buffering(): void {
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    /**
     * Process buffered output
     *
     * @param string $buffer HTML buffer.
     * @return string Modified buffer.
     */
    public function process_output(string $buffer): string {
        // Get consent status
        $consent = $this->get_consent_status();

        // Block scripts based on consent
        $buffer = $this->block_scripts($buffer, $consent);

        // Add script unblocking code
        $buffer = $this->add_unblock_script($buffer);

        return $buffer;
    }

    /**
     * Get consent status from cookie
     *
     * @return array Consent status.
     */
    private function get_consent_status(): array {
        if (!isset($_COOKIE['complyflow_consent'])) {
            return [
                'necessary' => true,
                'analytics' => false,
                'marketing' => false,
                'preferences' => false,
            ];
        }

        $consent = json_decode(stripslashes($_COOKIE['complyflow_consent']), true);

        return array_merge([
            'necessary' => true,
            'analytics' => false,
            'marketing' => false,
            'preferences' => false,
        ], $consent);
    }

    /**
     * Block scripts in HTML
     *
     * @param string $html    HTML content.
     * @param array  $consent Consent status.
     * @return string Modified HTML.
     */
    private function block_scripts(string $html, array $consent): string {
        // Find all script tags
        $html = preg_replace_callback(
            '/<script([^>]*)>(.*?)<\/script>/is',
            function ($matches) use ($consent) {
                $attributes = $matches[1];
                $content = $matches[2];

                // Check if script should be blocked
                $category = $this->get_script_category($attributes . $content);

                if ($category && !$consent[$category]) {
                    // Block script by changing type
                    $attributes = preg_replace('/type=["\']text\/javascript["\']/i', '', $attributes);
                    $attributes = ' type="text/plain" data-complyflow-category="' . $category . '"' . $attributes;
                    
                    return '<script' . $attributes . '>' . $content . '</script>';
                }

                return $matches[0];
            },
            $html
        );

        // Block external scripts (iframes, img tracking pixels)
        $html = preg_replace_callback(
            '/<iframe([^>]*)>/i',
            function ($matches) use ($consent) {
                $attributes = $matches[1];

                // Check if iframe should be blocked
                $category = $this->get_script_category($attributes);

                if ($category && !$consent[$category]) {
                    // Block iframe by adding data attribute
                    $attributes .= ' data-complyflow-category="' . $category . '" data-complyflow-blocked="true"';
                    
                    // Add style to hide blocked iframe
                    $attributes .= ' style="display:none;"';
                    
                    return '<iframe' . $attributes . '>';
                }

                return $matches[0];
            },
            $html
        );

        return $html;
    }

    /**
     * Get script category based on content
     *
     * @param string $content Script content or attributes.
     * @return string|false Category or false if not blocked.
     */
    private function get_script_category(string $content) {
        foreach ($this->blocked_scripts as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (stripos($content, $pattern) !== false) {
                    return $category;
                }
            }
        }

        return false;
    }

    /**
     * Add script to unblock when consent given
     *
     * @param string $html HTML content.
     * @return string Modified HTML.
     */
    private function add_unblock_script(string $html): string {
        $script = <<<'SCRIPT'
<script>
(function() {
    'use strict';
    
    // Check for consent changes
    document.addEventListener('complyflowConsentUpdated', function(e) {
        var consent = e.detail;
        unblockScripts(consent);
    });
    
    // Unblock scripts based on consent
    function unblockScripts(consent) {
        // Unblock script tags
        var scripts = document.querySelectorAll('script[data-complyflow-category]');
        scripts.forEach(function(script) {
            var category = script.getAttribute('data-complyflow-category');
            
            if (consent[category]) {
                // Create new script element
                var newScript = document.createElement('script');
                
                // Copy attributes
                Array.from(script.attributes).forEach(function(attr) {
                    if (attr.name !== 'type' && attr.name !== 'data-complyflow-category') {
                        newScript.setAttribute(attr.name, attr.value);
                    }
                });
                
                // Set correct type
                newScript.type = 'text/javascript';
                
                // Copy content
                newScript.textContent = script.textContent;
                
                // Replace old script
                script.parentNode.replaceChild(newScript, script);
            }
        });
        
        // Unblock iframes
        var iframes = document.querySelectorAll('iframe[data-complyflow-blocked]');
        iframes.forEach(function(iframe) {
            var category = iframe.getAttribute('data-complyflow-category');
            
            if (consent[category]) {
                iframe.removeAttribute('data-complyflow-blocked');
                iframe.removeAttribute('data-complyflow-category');
                iframe.style.display = '';
                
                // Reload iframe
                var src = iframe.src;
                iframe.src = '';
                iframe.src = src;
            }
        });
    }
    
    // Check initial consent state
    var consentCookie = document.cookie.split('; ').find(row => row.startsWith('complyflow_consent='));
    if (consentCookie) {
        var consent = JSON.parse(decodeURIComponent(consentCookie.split('=')[1]));
        unblockScripts(consent);
    }
})();
</script>
SCRIPT;

        // Add script before closing body tag
        $html = str_replace('</body>', $script . '</body>', $html);

        return $html;
    }

    /**
     * Add blocked script pattern
     *
     * @param string $category Category.
     * @param string $pattern  Pattern to block.
     * @return void
     */
    public function add_blocked_script(string $category, string $pattern): void {
        if (!isset($this->blocked_scripts[$category])) {
            $this->blocked_scripts[$category] = [];
        }

        $this->blocked_scripts[$category][] = $pattern;
    }

    /**
     * Get blocked scripts
     *
     * @return array Blocked scripts by category.
     */
    public function get_blocked_scripts(): array {
        return $this->blocked_scripts;
    }
}
