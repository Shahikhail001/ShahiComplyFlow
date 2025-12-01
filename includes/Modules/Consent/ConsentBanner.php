<?php
/**
 * Consent Banner
 *
 * Renders and manages the cookie consent banner.
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
 * Class ConsentBanner
 */
class ConsentBanner {
    /**
     * Settings repository
     *
     * @var SettingsRepository
     */
    private SettingsRepository $settings;

    /**
     * Consent logger
     *
     * @var ConsentLogger
     */
    private ConsentLogger $logger;

    /**
     * Constructor
     *
     * @param SettingsRepository $settings Settings repository.
     * @param ConsentLogger      $logger   Consent logger.
     */
    public function __construct(SettingsRepository $settings, ConsentLogger $logger) {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    /**
     * Render consent banner
     *
     * @return void
     */
    public function render_banner(): void {
        // Read from complyflow_consent_settings option
        $consent_settings = get_option('complyflow_consent_settings', []);
        $enabled = $consent_settings['banner_enabled'] ?? false;

        if (!$enabled) {
            return;
        }

        // Check if user already consented
        if (isset($_COOKIE['complyflow_consent'])) {
            return;
        }

        // Get banner settings
        $position = $consent_settings['position'] ?? 'bottom';
        $title = $consent_settings['title'] ?? __('We use cookies', 'complyflow');
        $message = $consent_settings['message'] ?? __('We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic.', 'complyflow');
        $show_reject = $consent_settings['show_reject'] ?? true;
        $primary_color = $consent_settings['primary_color'] ?? '#2271b1';
        $bg_color = $consent_settings['bg_color'] ?? '#ffffff';

        ?>
        <div id="complyflow-consent-banner" class="cf-consent-banner cf-position-<?php echo esc_attr($position); ?>" style="display: none;" data-nosnippet>
            <div class="cf-consent-overlay"></div>
            <div class="cf-consent-container">
                <div class="cf-consent-content">
                    <?php if ($title): ?>
                        <h3 class="cf-consent-title"><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>
                    
                    <p class="cf-consent-message"><?php echo wp_kses_post($message); ?></p>

                    <div class="cf-consent-categories">
                        <!-- Necessary cookies (always on) -->
                        <div class="cf-consent-category">
                            <label class="cf-consent-toggle">
                                <input type="checkbox" 
                                       name="consent_necessary" 
                                       value="1" 
                                       checked 
                                       disabled>
                                <span class="cf-consent-toggle-slider"></span>
                            </label>
                            <div class="cf-consent-category-info">
                                <strong><?php esc_html_e('Necessary', 'complyflow'); ?></strong>
                                <span class="cf-consent-category-desc">
                                    <?php esc_html_e('Required for the website to function properly.', 'complyflow'); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Analytics cookies -->
                        <div class="cf-consent-category">
                            <label class="cf-consent-toggle">
                                <input type="checkbox" 
                                       id="cf-toggle-analytics"
                                       name="consent_analytics" 
                                       value="1"
                                       class="cf-consent-checkbox">
                                <span class="cf-consent-toggle-slider"></span>
                            </label>
                            <div class="cf-consent-category-info">
                                <strong><?php esc_html_e('Analytics', 'complyflow'); ?></strong>
                                <span class="cf-consent-category-desc">
                                    <?php esc_html_e('Help us understand how visitors interact with our website.', 'complyflow'); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Marketing cookies -->
                        <div class="cf-consent-category">
                            <label class="cf-consent-toggle">
                                <input type="checkbox" 
                                       id="cf-toggle-marketing"
                                       name="consent_marketing" 
                                       value="1"
                                       class="cf-consent-checkbox">
                                <span class="cf-consent-toggle-slider"></span>
                            </label>
                            <div class="cf-consent-category-info">
                                <strong><?php esc_html_e('Marketing', 'complyflow'); ?></strong>
                                <span class="cf-consent-category-desc">
                                    <?php esc_html_e('Used to deliver personalized advertisements.', 'complyflow'); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Preferences cookies -->
                        <div class="cf-consent-category">
                            <label class="cf-consent-toggle">
                                <input type="checkbox" 
                                       id="cf-toggle-preferences"
                                       name="consent_preferences" 
                                       value="1"
                                       class="cf-consent-checkbox">
                                <span class="cf-consent-toggle-slider"></span>
                            </label>
                            <div class="cf-consent-category-info">
                                <strong><?php esc_html_e('Preferences', 'complyflow'); ?></strong>
                                <span class="cf-consent-category-desc">
                                    <?php esc_html_e('Remember your preferences and settings.', 'complyflow'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="cf-consent-actions">
                        <button type="button" 
                                class="cf-consent-btn cf-consent-btn-primary" 
                                id="cf-consent-accept-all"
                                style="background-color: <?php echo esc_attr($primary_color); ?>;">
                            <?php esc_html_e('Accept All', 'complyflow'); ?>
                        </button>
                        
                        <?php if ($show_reject): ?>
                            <button type="button" 
                                    class="cf-consent-btn cf-consent-btn-secondary" 
                                    id="cf-consent-reject-all">
                                <?php esc_html_e('Reject All', 'complyflow'); ?>
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" 
                                class="cf-consent-btn cf-consent-btn-secondary" 
                                id="cf-consent-save-preferences">
                            <?php esc_html_e('Save Preferences', 'complyflow'); ?>
                        </button>
                    </div>

                    <div class="cf-consent-links">
                        <?php
                        $privacy_page = $this->settings->get('privacy_policy_page_id');
                        if ($privacy_page):
                        ?>
                            <a href="<?php echo esc_url(get_permalink($privacy_page)); ?>" target="_blank">
                                <?php esc_html_e('Privacy Policy', 'complyflow'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <style>
                .cf-consent-banner {
                    position: fixed;
                    left: 0;
                    right: 0;
                    z-index: 999999;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
                }

                .cf-consent-banner.cf-position-bottom {
                    bottom: 0;
                }

                .cf-consent-banner.cf-position-top {
                    top: 0;
                }

                .cf-consent-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.3);
                    z-index: 999998;
                    backdrop-filter: blur(2px);
                    -webkit-backdrop-filter: blur(2px);
                }

                .cf-consent-container {
                    position: relative;
                    background: <?php echo esc_attr($bg_color); ?>;
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
                    border-top: 1px solid rgba(255, 255, 255, 0.5);
                    z-index: 999999;
                    max-height: 90vh;
                    overflow-y: auto;
                }

                .cf-consent-content {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 30px;
                }

                .cf-consent-title {
                    margin: 0 0 15px 0;
                    font-size: 24px;
                    font-weight: 600;
                    color: #333;
                }

                .cf-consent-message {
                    margin: 0 0 25px 0;
                    font-size: 16px;
                    line-height: 1.6;
                    color: #666;
                }

                .cf-consent-categories {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 20px;
                    margin-bottom: 25px;
                }

                .cf-consent-category {
                    display: flex;
                    gap: 15px;
                    align-items: flex-start;
                }

                .cf-consent-category-info strong {
                    display: block;
                    margin-bottom: 5px;
                    font-size: 14px;
                    color: #333;
                }

                .cf-consent-category-desc {
                    display: block;
                    font-size: 13px;
                    color: #666;
                    line-height: 1.4;
                }

                /* Toggle Switch */
                .cf-consent-toggle {
                    position: relative;
                    display: inline-block;
                    width: 50px;
                    height: 26px;
                    flex-shrink: 0;
                }

                .cf-consent-toggle input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .cf-consent-toggle-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: .4s;
                    border-radius: 26px;
                }

                .cf-consent-toggle-slider:before {
                    position: absolute;
                    content: "";
                    height: 20px;
                    width: 20px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }

                .cf-consent-toggle input:checked + .cf-consent-toggle-slider {
                    background-color: <?php echo esc_attr($primary_color); ?>;
                }

                .cf-consent-toggle input:checked + .cf-consent-toggle-slider:before {
                    transform: translateX(24px);
                }

                .cf-consent-toggle input:disabled + .cf-consent-toggle-slider {
                    opacity: 0.6;
                    cursor: not-allowed;
                }

                /* Buttons */
                .cf-consent-actions {
                    display: flex;
                    gap: 15px;
                    flex-wrap: wrap;
                    margin-bottom: 20px;
                }

                .cf-consent-btn {
                    padding: 12px 24px;
                    border: none;
                    border-radius: 4px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                }

                .cf-consent-btn-primary {
                    background: <?php echo esc_attr($primary_color); ?>;
                    color: #fff;
                }

                .cf-consent-btn-primary:hover {
                    opacity: 0.9;
                }

                .cf-consent-btn-secondary {
                    background: #f5f5f5;
                    color: #333;
                }

                .cf-consent-btn-secondary:hover {
                    background: #e5e5e5;
                }

                /* Links */
                .cf-consent-links {
                    display: flex;
                    gap: 20px;
                    flex-wrap: wrap;
                }

                .cf-consent-links a {
                    color: <?php echo esc_attr($primary_color); ?>;
                    text-decoration: none;
                    font-size: 14px;
                }

                .cf-consent-links a:hover {
                    text-decoration: underline;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    .cf-consent-content {
                        padding: 20px;
                    }

                    .cf-consent-title {
                        font-size: 20px;
                    }

                    .cf-consent-message {
                        font-size: 14px;
                    }

                    .cf-consent-categories {
                        grid-template-columns: 1fr;
                    }

                    .cf-consent-actions {
                        flex-direction: column;
                    }

                    .cf-consent-btn {
                        width: 100%;
                    }
                }
            </style>
        </div>
        <?php
    }
}
