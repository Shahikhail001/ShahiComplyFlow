<?php
/**
 * DSR Form Frontend View
 *
 * @package ComplyFlow\Modules\DSR
 * @since   3.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

$form_title = $atts['title'] ?? __('Submit a Data Subject Rights Request', 'complyflow');
$show_types = isset($atts['show_types']) ? explode(',', $atts['show_types']) : ['access', 'delete', 'portability', 'rectification', 'restriction'];
$unique_id = uniqid('dsr_');
?>

<div class="complyflow-dsr-wrapper">
    <div class="complyflow-dsr-form-container">
        <h2 class="complyflow-form-title"><?php echo esc_html($form_title); ?></h2>
        
        <div class="complyflow-notice complyflow-notice-info">
            <p><?php esc_html_e('Please fill out this form to submit a request regarding your personal data. You will receive a verification email to confirm your request.', 'complyflow'); ?></p>
        </div>

        <form id="complyflow-dsr-form-<?php echo esc_attr($unique_id); ?>" class="complyflow-dsr-form" method="post">
            
            <div class="complyflow-form-field">
                <label for="dsr_type_<?php echo esc_attr($unique_id); ?>">
                    <?php esc_html_e('Request Type', 'complyflow'); ?> <span class="required">*</span>
                </label>
                <select 
                    id="dsr_type_<?php echo esc_attr($unique_id); ?>" 
                    name="request_type" 
                    class="complyflow-input" 
                    required
                >
                    <option value=""><?php esc_html_e('Select a request type...', 'complyflow'); ?></option>
                    <?php if (in_array('access', $show_types)): ?>
                        <option value="access"><?php esc_html_e('Access My Data - View what data you have about me', 'complyflow'); ?></option>
                    <?php endif; ?>
                    <?php if (in_array('delete', $show_types)): ?>
                        <option value="delete"><?php esc_html_e('Delete My Data - Remove my personal data', 'complyflow'); ?></option>
                    <?php endif; ?>
                    <?php if (in_array('portability', $show_types)): ?>
                        <option value="portability"><?php esc_html_e('Export My Data (Portability) - Download a copy of my data', 'complyflow'); ?></option>
                    <?php endif; ?>
                    <?php if (in_array('rectification', $show_types)): ?>
                        <option value="rectification"><?php esc_html_e('Correct My Data (Rectification) - Update inaccurate information', 'complyflow'); ?></option>
                    <?php endif; ?>
                    <?php if (in_array('restriction', $show_types)): ?>
                        <option value="restriction"><?php esc_html_e('Restrict Processing - Limit how you use my data', 'complyflow'); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="complyflow-form-field">
                <label for="dsr_full_name_<?php echo esc_attr($unique_id); ?>">
                    <?php esc_html_e('Full Name', 'complyflow'); ?> <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="dsr_full_name_<?php echo esc_attr($unique_id); ?>" 
                    name="full_name" 
                    class="complyflow-input" 
                    required
                    placeholder="<?php esc_attr_e('Enter your full name', 'complyflow'); ?>"
                />
            </div>

            <div class="complyflow-form-field">
                <label for="dsr_email_<?php echo esc_attr($unique_id); ?>">
                    <?php esc_html_e('Email Address', 'complyflow'); ?> <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="dsr_email_<?php echo esc_attr($unique_id); ?>" 
                    name="email" 
                    class="complyflow-input" 
                    required
                    placeholder="<?php esc_attr_e('your.email@example.com', 'complyflow'); ?>"
                />
            </div>

            <div class="complyflow-form-field">
                <label for="dsr_additional_info_<?php echo esc_attr($unique_id); ?>">
                    <?php esc_html_e('Additional Information', 'complyflow'); ?> <span class="optional"><?php esc_html_e('(Optional)', 'complyflow'); ?></span>
                </label>
                <textarea 
                    id="dsr_additional_info_<?php echo esc_attr($unique_id); ?>" 
                    name="additional_info" 
                    class="complyflow-input complyflow-textarea" 
                    rows="4"
                    placeholder="<?php esc_attr_e('Please provide any additional details that will help us process your request...', 'complyflow'); ?>"
                ></textarea>
            </div>

            <div class="complyflow-form-field complyflow-privacy-notice">
                <p>
                    <strong><?php esc_html_e('Privacy Notice:', 'complyflow'); ?></strong>
                    <?php esc_html_e('By submitting this form, you consent to the processing of your data for the purpose of fulfilling your data subject rights request. Your information will be used solely for this purpose and will be handled in accordance with applicable data protection laws.', 'complyflow'); ?>
                </p>
            </div>

            <!-- CAPTCHA placeholder for future integration -->
            <div class="complyflow-captcha-placeholder" id="dsr_captcha_<?php echo esc_attr($unique_id); ?>"></div>

            <div class="complyflow-form-actions">
                <button 
                    type="submit" 
                    class="complyflow-button complyflow-button-primary"
                    data-request-type="dsr-submit"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('complyflow_dsr_public_nonce')); ?>"
                >
                    <span class="button-text"><?php esc_html_e('Submit Request', 'complyflow'); ?></span>
                    <span class="button-loader" style="display: none;">
                        <span class="spinner"></span>
                        <?php esc_html_e('Submitting...', 'complyflow'); ?>
                    </span>
                </button>
            </div>

            <?php wp_nonce_field('complyflow_submit_dsr', 'complyflow_dsr_nonce'); ?>
            <input type="hidden" name="action" value="complyflow_submit_dsr" />
        </form>

        <div class="complyflow-form-message" style="display: none;"></div>

        <div class="complyflow-form-instructions">
            <h3><?php esc_html_e('What happens next?', 'complyflow'); ?></h3>
            <ol>
                <li><?php esc_html_e('You will receive a verification email at the address you provided.', 'complyflow'); ?></li>
                <li><?php esc_html_e('Click the verification link in the email to confirm your request (link expires in 24 hours).', 'complyflow'); ?></li>
                <li><?php esc_html_e('Once verified, our team will review and process your request.', 'complyflow'); ?></li>
                <li><?php esc_html_e('You will be notified by email when your request is completed.', 'complyflow'); ?></li>
            </ol>
        </div>
    </div>
</div>
