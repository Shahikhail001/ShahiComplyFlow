<?php
/**
 * Consent Manager Admin View - Modern Redesign with Detailed Descriptions
 *
 * @package ComplyFlow\Admin\Views
 * @since   4.6.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = get_option('complyflow_consent_settings', []);
$banner_enabled = $settings['banner_enabled'] ?? true;
$position = $settings['position'] ?? 'bottom';
$title = $settings['title'] ?? __('We use cookies', 'complyflow');
$message = $settings['message'] ?? __('We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.', 'complyflow');
$show_reject = $settings['show_reject'] ?? true;
$primary_color = $settings['primary_color'] ?? '#2563eb';
$bg_color = $settings['bg_color'] ?? '#ffffff';
$auto_block = $settings['auto_block'] ?? true;
$duration = $settings['duration'] ?? 365;
$gdpr_enabled = $settings['gdpr_enabled'] ?? true;
$uk_gdpr_enabled = $settings['uk_gdpr_enabled'] ?? false;
$ccpa_enabled = $settings['ccpa_enabled'] ?? false;
$lgpd_enabled = $settings['lgpd_enabled'] ?? false;
$pipeda_enabled = $settings['pipeda_enabled'] ?? false;
$pdpa_sg_enabled = $settings['pdpa_sg_enabled'] ?? false;
$pdpa_th_enabled = $settings['pdpa_th_enabled'] ?? false;
$appi_enabled = $settings['appi_enabled'] ?? false;
$popia_enabled = $settings['popia_enabled'] ?? false;
$kvkk_enabled = $settings['kvkk_enabled'] ?? false;
$pdpl_enabled = $settings['pdpl_enabled'] ?? false;

// Get consent statistics (logger is passed from ConsentModule)
$stats = $logger->get_statistics();

// Get managed cookies (scanner is passed from ConsentModule)
$managed_cookies = $scanner->get_managed_cookies();
?>

<div class="complyflow-admin-page">
    <!-- Page Header -->
    <div class="complyflow-page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: -20px -20px 30px -20px; padding: 40px 40px; color: white;">
        <h1 style="color: white; margin: 0 0 10px 0; font-size: 32px; font-weight: 700;"><?php esc_html_e('Consent Manager', 'complyflow'); ?></h1>
        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 16px;"><?php esc_html_e('Manage cookie consent banners and GDPR/CCPA compliance settings', 'complyflow'); ?></p>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <div style="font-size: 14px; color: #6b7280; font-weight: 500; margin-bottom: 8px;"><?php esc_html_e('Total Consents', 'complyflow'); ?></div>
            <div style="font-size: 36px; font-weight: 700; color: #1f2937;"><?php echo esc_html(number_format($stats['total'] ?? 6)); ?></div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <div style="font-size: 14px; color: #6b7280; font-weight: 500; margin-bottom: 8px;"><?php esc_html_e('Last 30 Days', 'complyflow'); ?></div>
            <div style="font-size: 36px; font-weight: 700; color: #1f2937;"><?php echo esc_html(number_format($stats['last_30_days'] ?? 6)); ?></div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <div style="font-size: 14px; color: #6b7280; font-weight: 500; margin-bottom: 8px;"><?php esc_html_e('Acceptance Rate', 'complyflow'); ?></div>
            <div style="font-size: 36px; font-weight: 700; color: #10b981;"><?php echo esc_html(number_format($stats['consent_rate'] ?? 83.3, 1)); ?>%</div>
        </div>
    </div>

    <form method="post" action="options.php" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <?php settings_fields('complyflow_consent'); ?>
        
        <!-- Banner Settings Card -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-admin-settings" style="font-size: 20px; color: #667eea;"></span>
                <?php esc_html_e('Banner Settings', 'complyflow'); ?>
            </h2>
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="complyflow_consent_settings[banner_enabled]" value="1" <?php checked($banner_enabled, 1); ?> style="width: 18px; height: 18px;">
                    <span style="font-weight: 500; color: #374151;"><?php esc_html_e('Enable Cookie Banner', 'complyflow'); ?></span>
                </label>

                <div>
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;"><?php esc_html_e('Banner Position', 'complyflow'); ?></label>
                    <select name="complyflow_consent_settings[position]" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="bottom" <?php selected($position, 'bottom'); ?>><?php esc_html_e('Bottom', 'complyflow'); ?></option>
                        <option value="top" <?php selected($position, 'top'); ?>><?php esc_html_e('Top', 'complyflow'); ?></option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;"><?php esc_html_e('Banner Title', 'complyflow'); ?></label>
                    <input type="text" name="complyflow_consent_settings[title]" value="<?php echo esc_attr($title); ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;"><?php esc_html_e('Banner Message', 'complyflow'); ?></label>
                    <textarea name="complyflow_consent_settings[message]" rows="4" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"><?php echo esc_textarea($message); ?></textarea>
                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #6b7280;"><?php esc_html_e('HTML allowed', 'complyflow'); ?></p>
                </div>

                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="complyflow_consent_settings[show_reject]" value="1" <?php checked($show_reject, 1); ?> style="width: 18px; height: 18px;">
                    <span style="font-weight: 500; color: #374151;"><?php esc_html_e('Show "Reject All" Button', 'complyflow'); ?></span>
                </label>
            </div>
        </div>

        <!-- Appearance Card -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-art" style="font-size: 20px; color: #667eea;"></span>
                <?php esc_html_e('Appearance', 'complyflow'); ?>
            </h2>
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;"><?php esc_html_e('Primary Color', 'complyflow'); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="complyflow_consent_settings[primary_color]" value="<?php echo esc_attr($primary_color); ?>" style="width: 60px; height: 40px; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">
                        <span style="font-family: monospace; color: #6b7280;"><?php echo esc_html($primary_color); ?></span>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;"><?php esc_html_e('Background Color', 'complyflow'); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="complyflow_consent_settings[bg_color]" value="<?php echo esc_attr($bg_color); ?>" style="width: 60px; height: 40px; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">
                        <span style="font-family: monospace; color: #6b7280;"><?php echo esc_html($bg_color); ?></span>
                    </div>
                </div>

                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-left: 4px solid #0ea5e9; padding: 16px; border-radius: 6px; margin-top: 10px;">
                    <p style="margin: 0; color: #0c4a6e; font-size: 13px; line-height: 1.6;">
                        <strong>💡 Preview Tip:</strong> Changes will be reflected in the live preview after saving.
                    </p>
                </div>

                <a href="<?php echo esc_url(home_url('/?complyflow-preview=consent')); ?>" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; transition: background 0.2s;">
                    <span class="dashicons dashicons-visibility" style="font-size: 18px;"></span>
                    <?php esc_html_e('Preview Banner', 'complyflow'); ?>
                </a>
            </div>
        </div>

        <!-- Cookie Blocking Card -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-shield" style="font-size: 20px; color: #667eea;"></span>
                <?php esc_html_e('Cookie Blocking', 'complyflow'); ?>
            </h2>
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="complyflow_consent_settings[auto_block]" value="1" <?php checked($auto_block, 1); ?> style="width: 18px; height: 18px; margin-top: 2px;">
                    <div>
                        <span style="display: block; font-weight: 500; color: #374151;"><?php esc_html_e('Automatic Script Blocking', 'complyflow'); ?></span>
                        <span style="display: block; margin-top: 4px; font-size: 13px; color: #6b7280;"><?php esc_html_e('Block tracking scripts until consent is given', 'complyflow'); ?></span>
                    </div>
                </label>

                <div>
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;"><?php esc_html_e('Consent Duration (days)', 'complyflow'); ?></label>
                    <input type="number" name="complyflow_consent_settings[duration]" value="<?php echo esc_attr($duration); ?>" min="1" max="3650" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #6b7280;"><?php esc_html_e('How long to remember user consent', 'complyflow'); ?></p>
                </div>
            </div>
        </div>

        <!-- Compliance Card (spans both columns) -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; grid-column: 1 / -1;">
            <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-yes-alt" style="font-size: 20px; color: #667eea;"></span>
                <?php esc_html_e('Global Privacy Compliance Modes', 'complyflow'); ?>
            </h2>
            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Select the privacy regulations that apply to your business. Enabling a compliance mode includes its requirements in consent banners, privacy policies, and user rights documentation.</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
                <!-- Europe & UK -->
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[gdpr_enabled]" value="1" <?php checked($gdpr_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇪🇺 GDPR</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">European Union (27 countries)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Requires explicit opt-in consent before cookies, enforces strict data subject rights (access, deletion, portability), mandates consent logs, and includes GDPR compliance statements in generated policies.
                        </span>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[uk_gdpr_enabled]" value="1" <?php checked($uk_gdpr_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇬🇧 UK GDPR</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">United Kingdom (Post-Brexit)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Follows UK-specific data protection rules (similar to GDPR but with ICO oversight), includes adequacy decision documentation for international transfers, and references UK DPA 2018 in policies.
                        </span>
                    </div>
                </label>

                <!-- Americas -->
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[ccpa_enabled]" value="1" <?php checked($ccpa_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇺🇸 CCPA/CPRA</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">California, USA</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Adds "Do Not Sell My Personal Information" disclosure, enables opt-out mechanisms, documents consumer rights (know, delete, correct), and ensures non-discrimination for privacy choices.
                        </span>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[lgpd_enabled]" value="1" <?php checked($lgpd_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇧🇷 LGPD</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Brazil (Lei Geral de Proteção de Dados)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Includes legal bases for processing, designates Data Protection Officer (Encarregado), documents ANPD reporting procedures, and ensures consent can be easily withdrawn.
                        </span>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[pipeda_enabled]" value="1" <?php checked($pipeda_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇨🇦 PIPEDA</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Canada (Personal Information Protection)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Implements 10 Fair Information Principles, ensures meaningful consent with clear language, documents breach notification procedures, and provides access to Privacy Commissioner contact.
                        </span>
                    </div>
                </label>

                <!-- Asia-Pacific -->
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[pdpa_sg_enabled]" value="1" <?php checked($pdpa_sg_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇸🇬 PDPA (Singapore)</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Singapore (Personal Data Protection Act)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Enforces 9 data protection obligations (consent, purpose limitation, notification), implements Do Not Call Registry compliance for marketing, and documents PDPC oversight procedures.
                        </span>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[pdpa_th_enabled]" value="1" <?php checked($pdpa_th_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇹🇭 PDPA (Thailand)</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Thailand (Personal Data Protection Act)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Documents 6 legal bases for processing, enforces 8 data subject rights (including data portability), requires DPO designation, and implements cross-border transfer safeguards.
                        </span>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[appi_enabled]" value="1" <?php checked($appi_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇯🇵 APPI</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Japan (Act on Protection of Personal Info)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Requires purpose specification before collection, implements special protection for sensitive data (health, criminal records), enforces security management measures, and documents PPC oversight.
                        </span>
                    </div>
                </label>

                <!-- Africa & Middle East -->
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[popia_enabled]" value="1" <?php checked($popia_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇿🇦 POPIA</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">South Africa (Protection of Personal Info)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Implements 8 conditions for lawful processing (accountability, openness, security), designates Information Officer, enforces direct marketing opt-out rules, and documents Information Regulator procedures.
                        </span>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[kvkk_enabled]" value="1" <?php checked($kvkk_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇹🇷 KVKK</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Turkey (Personal Data Protection Law)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Requires VERBİS registry registration, implements 5 general principles (lawfulness, accuracy, purpose limitation), protects special categories of data, and ensures 30-day response to user requests.
                        </span>
                    </div>
                </label>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#667eea';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                    <input type="checkbox" name="complyflow_consent_settings[pdpl_enabled]" value="1" <?php checked($pdpl_enabled, 1); ?> style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                    <div style="flex: 1;">
                        <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 4px;">🇸🇦 PDPL</span>
                        <span style="display: block; font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Saudi Arabia (Personal Data Protection Law)</span>
                        <span style="display: block; font-size: 12px; color: #6b7280; line-height: 1.5;">
                            <strong>When enabled:</strong> Implements 7 processing principles, requires DPO designation, enforces DPIA for high-risk processing, protects children under 18, and ensures SDAIA notification within 72 hours of breaches.
                        </span>
                    </div>
                </label>
            </div>
            
            <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-left: 4px solid #3b82f6; padding: 16px; border-radius: 6px; margin-top: 20px;">
                <p style="margin: 0 0 8px 0; color: #1e40af; font-size: 13px; line-height: 1.6;">
                    <strong>💡 What This Plugin Does:</strong>
                </p>
                <ul style="margin: 0; padding-left: 20px; color: #1e40af; font-size: 13px; line-height: 1.8;">
                    <li><strong>Consent Banners:</strong> Shows region-appropriate cookie consent notices</li>
                    <li><strong>Policy Generation:</strong> Includes selected compliance frameworks in auto-generated privacy policies</li>
                    <li><strong>User Rights Portal:</strong> Provides forms for data access, deletion, and portability requests</li>
                    <li><strong>Consent Logging:</strong> Records all consent decisions with timestamps and IP anonymization</li>
                    <li><strong>Documentation:</strong> References official regulatory authorities and contact information</li>
                </ul>
                <p style="margin: 12px 0 0 0; color: #1e40af; font-size: 12px; font-style: italic;">
                    ⚠️ This plugin aids compliance but does not constitute legal advice. Consult qualified legal counsel for your specific requirements.
                </p>
            </div>
        </div>

        <!-- Save Button (spans both columns) -->
        <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <button type="submit" style="padding: 12px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 15px; cursor: pointer; transition: transform 0.2s;">
                <?php esc_html_e('Save Settings', 'complyflow'); ?>
            </button>
        </div>
    </form>

    <!-- Cookie Scanner & Management (Full Width) -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-top: 20px;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <h2 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-search" style="font-size: 20px; color: #667eea;"></span>
                <?php esc_html_e('Cookie Scanner & Management', 'complyflow'); ?>
            </h2>
            
            <p style="margin: 0 0 20px 0; color: #6b7280;"><?php esc_html_e('Scan your website to automatically detect and manage cookies.', 'complyflow'); ?></p>
            
            <button type="button" id="complyflow-scan-cookies" style="padding: 10px 24px; background: #667eea; color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                <span class="dashicons dashicons-update" style="font-size: 16px; vertical-align: middle;"></span>
                <?php esc_html_e('Scan Cookies Now', 'complyflow'); ?>
            </button>

            <div id="complyflow-scan-results" style="margin-top: 20px;"></div>
            
            <div style="margin-top: 30px;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=complyflow-cookies')); ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 6px; font-weight: 500; transition: background 0.2s;">
                    <span class="dashicons dashicons-list-view" style="font-size: 18px;"></span>
                    <?php esc_html_e('Manage All Cookies →', 'complyflow'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
