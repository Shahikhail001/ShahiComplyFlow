<?php
/**
 * DSR Portal Template
 *
 * Frontend template for Data Subject Request portal.
 * Use shortcode: [complyflow_dsr_portal]
 *
 * @package ComplyFlow
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="complyflow-dsr-portal">
    <div class="complyflow-dsr-form">
        <h2><?php esc_html_e('Data Subject Request', 'complyflow'); ?></h2>
        <p><?php esc_html_e('Submit a request to access, rectify, or delete your personal data.', 'complyflow'); ?></p>

        <form id="complyflow-dsr-form" method="post">
            <?php wp_nonce_field('complyflow_dsr_submit', 'complyflow_dsr_nonce'); ?>

            <div class="complyflow-dsr-form-group">
                <label for="dsr-email" class="complyflow-dsr-label">
                    <?php esc_html_e('Your Email Address', 'complyflow'); ?> <span style="color: red;">*</span>
                </label>
                <input 
                    type="email" 
                    id="dsr-email" 
                    name="dsr_email" 
                    class="complyflow-dsr-input" 
                    required 
                    placeholder="<?php esc_attr_e('email@example.com', 'complyflow'); ?>"
                >
            </div>

            <div class="complyflow-dsr-form-group">
                <label for="dsr-type" class="complyflow-dsr-label">
                    <?php esc_html_e('Request Type', 'complyflow'); ?> <span style="color: red;">*</span>
                </label>
                <select id="dsr-type" name="dsr_type" class="complyflow-dsr-input" required>
                    <option value=""><?php esc_html_e('Select a request type', 'complyflow'); ?></option>
                    <option value="access"><?php esc_html_e('Access My Data', 'complyflow'); ?></option>
                    <option value="erasure"><?php esc_html_e('Delete My Data', 'complyflow'); ?></option>
                    <option value="rectify"><?php esc_html_e('Correct My Data', 'complyflow'); ?></option>
                    <option value="portability"><?php esc_html_e('Export My Data', 'complyflow'); ?></option>
                </select>
            </div>

            <div class="complyflow-dsr-form-group">
                <label for="dsr-message" class="complyflow-dsr-label">
                    <?php esc_html_e('Additional Details (Optional)', 'complyflow'); ?>
                </label>
                <textarea 
                    id="dsr-message" 
                    name="dsr_message" 
                    class="complyflow-dsr-textarea" 
                    rows="4"
                    placeholder="<?php esc_attr_e('Provide any additional information about your request...', 'complyflow'); ?>"
                ></textarea>
            </div>

            <div class="complyflow-dsr-form-group">
                <button type="submit" class="complyflow-dsr-button">
                    <?php esc_html_e('Submit Request', 'complyflow'); ?>
                </button>
            </div>

            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 1rem;">
                <?php esc_html_e('You will receive a verification email to confirm your identity before we process your request.', 'complyflow'); ?>
            </p>
        </form>
    </div>
</div>
