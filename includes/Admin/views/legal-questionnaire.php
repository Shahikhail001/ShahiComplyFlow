<?php
/**
 * Legal Questionnaire View
 *
 * @package ComplyFlow\Admin\Views
 * @since   4.7.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Use passed answers variable if available, otherwise try to get from settings
if (!isset($answers)) {
    $answers = [];
    if (isset($this) && property_exists($this, 'settings')) {
        $answers = $this->settings->get('legal_questionnaire_answers', []);
    }
}
?>

<style>
/* Dashboard Theme Variables */
:root {
    --cf-primary: #2563eb;
    --cf-primary-dark: #1e40af;
    --cf-primary-light: #3b82f6;
    --cf-gradient-start: #1e3a8a;
    --cf-gradient-mid: #2563eb;
    --cf-gradient-end: #0ea5e9;
    --cf-success: #10b981;
    --cf-warning: #f59e0b;
    --cf-danger: #ef4444;
    --cf-info: #06b6d4;
    --cf-surface: #ffffff;
    --cf-surface-hover: #f8fafc;
    --cf-border: #e2e8f0;
    --cf-text: #1e293b;
    --cf-text-light: #64748b;
    --cf-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    --cf-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    --cf-shadow-blue: 0 4px 14px 0 rgba(37, 99, 235, 0.15);
}

/* Questionnaire Container */
.complyflow-questionnaire {
    background: linear-gradient(135deg, var(--cf-gradient-start) 0%, var(--cf-gradient-mid) 50%, var(--cf-gradient-end) 100%);
    margin: -10px -20px 0 -2px;
    padding: 2rem;
    min-height: calc(100vh - 32px);
}

/* Header Section */
.complyflow-questionnaire-header {
    background: var(--cf-surface);
    border-radius: 18px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--cf-shadow-lg);
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.complyflow-questionnaire-header h1 {
    color: var(--cf-primary);
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    background: linear-gradient(135deg, var(--cf-gradient-start), var(--cf-gradient-mid));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.complyflow-questionnaire-header .description {
    color: var(--cf-text-light);
    font-size: 1.1rem;
    line-height: 1.6;
    margin: 0;
}

/* Progress Bar */
.complyflow-progress-container {
    background: var(--cf-surface);
    border-radius: 18px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--cf-shadow-lg);
    border: 1px solid rgba(255, 255, 255, 0.8);
}

.complyflow-progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    position: relative;
}

.complyflow-progress-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--cf-border);
    z-index: 1;
}

.progress-step {
    position: relative;
    z-index: 2;
    flex: 1;
    text-align: center;
}

.progress-step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--cf-surface);
    border: 3px solid var(--cf-border);
    margin: 0 auto 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: var(--cf-text-light);
    transition: all 0.3s ease;
}

.progress-step.active .progress-step-circle {
    background: linear-gradient(135deg, var(--cf-gradient-mid), var(--cf-gradient-end));
    border-color: var(--cf-primary);
    color: white;
    transform: scale(1.1);
    box-shadow: var(--cf-shadow-blue);
}

.progress-step.completed .progress-step-circle {
    background: var(--cf-success);
    border-color: var(--cf-success);
    color: white;
}

.progress-step.completed .progress-step-circle::after {
    content: '✓';
    font-size: 1.2rem;
}

.progress-step-label {
    font-size: 0.875rem;
    color: var(--cf-text-light);
    font-weight: 500;
}

.progress-step.active .progress-step-label {
    color: var(--cf-primary);
    font-weight: 700;
}

.progress-step.completed .progress-step-label {
    color: var(--cf-success);
}

.progress-bar-fill-container {
    height: 8px;
    background: var(--cf-border);
    border-radius: 999px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--cf-gradient-mid), var(--cf-gradient-end));
    border-radius: 999px;
    transition: width 0.5s ease;
}

/* Step Container */
.questionnaire-step {
    display: none;
}

.questionnaire-step.active {
    display: block;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Card Styles */
.complyflow-card {
    background: var(--cf-surface);
    border-radius: 18px;
    padding: 2.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--cf-shadow-lg);
    border: 1px solid rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
}

.complyflow-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.2);
}

.complyflow-card-header {
    border-bottom: 2px solid var(--cf-border);
    padding-bottom: 1.5rem;
    margin-bottom: 2rem;
}

.complyflow-card-header h2 {
    color: var(--cf-primary);
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.complyflow-card-header h2 .dashicons {
    font-size: 2rem;
    width: 2rem;
    height: 2rem;
}

.complyflow-card-header .description {
    color: var(--cf-text-light);
    font-size: 1rem;
    margin: 0;
}

/* Section Groups */
.question-section {
    background: var(--cf-surface-hover);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--cf-border);
}

.question-section-title {
    color: var(--cf-text);
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.question-section-title .dashicons {
    color: var(--cf-primary);
}

/* Form Fields */
.form-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 1rem;
}

.form-table tr {
    transition: all 0.3s ease;
}

.form-table th {
    width: 30%;
    padding: 1rem 1.5rem 1rem 0;
    vertical-align: top;
    font-weight: 600;
    color: var(--cf-text);
}

.form-table td {
    padding: 1rem 0;
}

.form-table label {
    font-weight: 500;
    color: var(--cf-text);
}

.form-table .required {
    color: var(--cf-danger);
    font-weight: 700;
}

.form-table input[type="text"],
.form-table input[type="email"],
.form-table input[type="tel"],
.form-table input[type="number"],
.form-table textarea,
.form-table select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--cf-border);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--cf-surface);
}

.form-table input[type="text"]:focus,
.form-table input[type="email"]:focus,
.form-table input[type="tel"]:focus,
.form-table input[type="number"]:focus,
.form-table textarea:focus,
.form-table select:focus {
    outline: none;
    border-color: var(--cf-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-table .description {
    margin: 0.5rem 0 0 0;
    color: var(--cf-text-light);
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Checkbox/Radio Styles */
.complyflow-checkbox-group {
    display: grid;
    gap: 1rem;
}

.complyflow-checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--cf-surface);
    border: 2px solid var(--cf-border);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.complyflow-checkbox-label:hover {
    border-color: var(--cf-primary-light);
    background: var(--cf-surface-hover);
    transform: translateX(4px);
}

.complyflow-checkbox-label input[type="checkbox"] {
    margin-top: 0.25rem;
    width: 1.25rem;
    height: 1.25rem;
    cursor: pointer;
}

.complyflow-checkbox-label strong {
    display: block;
    color: var(--cf-text);
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.complyflow-checkbox-label .description {
    color: var(--cf-text-light);
    font-size: 0.875rem;
}

/* Multi-select Checkboxes */
.multiselect-options {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

/* Navigation Buttons */
.questionnaire-nav {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    margin-top: 2rem;
}

.questionnaire-nav .button {
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.questionnaire-nav .button-primary {
    background: linear-gradient(135deg, var(--cf-gradient-mid), var(--cf-gradient-end));
    color: white;
    box-shadow: var(--cf-shadow-blue);
}

.questionnaire-nav .button-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
}

.questionnaire-nav .button-secondary {
    background: var(--cf-surface);
    color: var(--cf-primary);
    border: 2px solid var(--cf-primary);
}

.questionnaire-nav .button-secondary:hover {
    background: var(--cf-primary);
    color: white;
}

.questionnaire-nav .prev-step {
    margin-right: auto;
}

.questionnaire-nav .next-step,
.questionnaire-nav #submit-questionnaire {
    margin-left: auto;
}

/* Notice Styles */
.complyflow-notice {
    padding: 1.25rem;
    border-radius: 8px;
    margin: 1.5rem 0;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.complyflow-notice.info {
    background: rgba(6, 182, 212, 0.1);
    border-left: 4px solid var(--cf-info);
}

.complyflow-notice.warning {
    background: rgba(245, 158, 11, 0.1);
    border-left: 4px solid var(--cf-warning);
}

.complyflow-notice p {
    margin: 0;
    color: var(--cf-text);
}

/* Conditional Fields */
.conditional-field {
    display: none;
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(37, 99, 235, 0.05);
    border-left: 3px solid var(--cf-primary);
    border-radius: 8px;
}

.conditional-field.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 500px;
    }
}

/* Review Summary */
#questionnaire-summary {
    background: var(--cf-surface-hover);
    border-radius: 12px;
    padding: 2rem;
    margin: 2rem 0;
}

#questionnaire-summary h3 {
    color: var(--cf-primary);
    font-size: 1.25rem;
    margin-top: 0;
}

#questionnaire-summary .summary-section {
    margin-bottom: 1.5rem;
}

#questionnaire-summary .summary-section strong {
    color: var(--cf-text);
    display: block;
    margin-bottom: 0.5rem;
}

/* Loading State */
.questionnaire-loading {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999999;
    align-items: center;
    justify-content: center;
}

.questionnaire-loading.active {
    display: flex;
}

.questionnaire-loading-spinner {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
}

.spinner {
    border: 4px solid var(--cf-border);
    border-top: 4px solid var(--cf-primary);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 782px) {
    .complyflow-questionnaire {
        padding: 1rem;
        margin: -10px -10px 0 -10px;
    }

    .complyflow-questionnaire-header h1 {
        font-size: 1.75rem;
    }

    .complyflow-card {
        padding: 1.5rem;
    }

    .form-table th,
    .form-table td {
        display: block;
        width: 100%;
        padding: 0.5rem 0;
    }

    .complyflow-progress-steps {
        flex-wrap: wrap;
    }

    .progress-step {
        flex: 0 0 25%;
        margin-bottom: 1rem;
    }

    .progress-step-label {
        font-size: 0.75rem;
    }

    .multiselect-options {
        grid-template-columns: 1fr;
    }

    .questionnaire-nav {
        flex-direction: column;
    }

    .questionnaire-nav .prev-step,
    .questionnaire-nav .next-step,
    .questionnaire-nav #submit-questionnaire {
        margin: 0;
        width: 100%;
    }
}
</style>

<div class="wrap complyflow-questionnaire">
    <!-- Header -->
    <div class="complyflow-questionnaire-header">
        <h1><?php esc_html_e('Policy Questionnaire', 'complyflow'); ?></h1>
        <p class="description">
            <?php esc_html_e('Answer these questions to generate customized legal documents for your website. All information will be used to create policies specific to your business practices and compliance requirements.', 'complyflow'); ?>
        </p>
    </div>

    <!-- Progress Tracker -->
    <div class="complyflow-progress-container">
        <div class="complyflow-progress-steps">
            <div class="progress-step active" data-step="1">
                <div class="progress-step-circle">1</div>
                <div class="progress-step-label"><?php esc_html_e('Basic Info', 'complyflow'); ?></div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="progress-step-circle">2</div>
                <div class="progress-step-label"><?php esc_html_e('Compliance', 'complyflow'); ?></div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="progress-step-circle">3</div>
                <div class="progress-step-label"><?php esc_html_e('Data Collection', 'complyflow'); ?></div>
            </div>
            <div class="progress-step" data-step="4">
                <div class="progress-step-circle">4</div>
                <div class="progress-step-label"><?php esc_html_e('Third-Party', 'complyflow'); ?></div>
            </div>
            <div class="progress-step" data-step="5">
                <div class="progress-step-circle">5</div>
                <div class="progress-step-label"><?php esc_html_e('User Rights', 'complyflow'); ?></div>
            </div>
            <div class="progress-step" data-step="6">
                <div class="progress-step-circle">6</div>
                <div class="progress-step-label"><?php esc_html_e('Review', 'complyflow'); ?></div>
            </div>
        </div>
        <div class="progress-bar-fill-container">
            <div class="progress-bar-fill" style="width: 16.67%"></div>
        </div>
    </div>

    <form id="complyflow-questionnaire-form">
        <?php wp_nonce_field('complyflow_questionnaire', 'questionnaire_nonce'); ?>

        <!-- Step 1: Business Information -->
        <div class="questionnaire-step active" data-step="1">
            <div class="complyflow-card">
                <div class="complyflow-card-header">
                    <h2>
                        <span class="dashicons dashicons-admin-home"></span>
                        <?php esc_html_e('Business Information', 'complyflow'); ?>
                    </h2>
                    <p class="description"><?php esc_html_e('Tell us about your business and contact details', 'complyflow'); ?></p>
                </div>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="company_name"><?php esc_html_e('Company Name', 'complyflow'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" id="company_name" name="answers[company_name]" class="regular-text required" value="<?php echo esc_attr($answers['company_name'] ?? get_bloginfo('name')); ?>">
                            <p class="description"><?php esc_html_e('Your legal business name or website name', 'complyflow'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="contact_email"><?php esc_html_e('Contact Email', 'complyflow'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="email" id="contact_email" name="answers[contact_email]" class="regular-text required" value="<?php echo esc_attr($answers['contact_email'] ?? get_bloginfo('admin_email')); ?>">
                            <p class="description"><?php esc_html_e('Email for legal and privacy inquiries', 'complyflow'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="physical_address"><?php esc_html_e('Physical Address', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <textarea id="physical_address" name="answers[physical_address]" rows="3" class="large-text"><?php echo esc_textarea($answers['physical_address'] ?? ''); ?></textarea>
                            <p class="description"><?php esc_html_e('Your business physical address (recommended for GDPR compliance)', 'complyflow'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="phone_number"><?php esc_html_e('Phone Number', 'complyflow'); ?></label>
                        </th>
                        <td>
                            <input type="tel" id="phone_number" name="answers[phone_number]" class="regular-text" value="<?php echo esc_attr($answers['phone_number'] ?? ''); ?>">
                            <p class="description"><?php esc_html_e('Contact phone number (optional)', 'complyflow'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="questionnaire-nav">
                <button type="button" class="button button-primary next-step"><?php esc_html_e('Next Step →', 'complyflow'); ?></button>
            </div>
        </div>

        <!-- Step 2: Compliance & Regions -->
        <div class="questionnaire-step" data-step="2">
            <div class="complyflow-card">
                <div class="complyflow-card-header">
                    <h2>
                        <span class="dashicons dashicons-admin-site-alt3"></span>
                        <?php esc_html_e('Compliance & Regions', 'complyflow'); ?>
                    </h2>
                    <p class="description"><?php esc_html_e('Select the regions and laws that apply to your business', 'complyflow'); ?></p>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-location"></span>
                        <?php esc_html_e('Target Countries & Regions', 'complyflow'); ?>
                    </h3>
                    <p class="description"><?php esc_html_e('Select all regions where your users are located or your business operates:', 'complyflow'); ?></p>
                    
                    <div class="complyflow-checkbox-group">
                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="EU" <?php checked(in_array('EU', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇪🇺 European Union (GDPR)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('EU member states - Requires strict consent and comprehensive user rights', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="UK" <?php checked(in_array('UK', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇬🇧 United Kingdom (UK GDPR)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Similar to EU GDPR with UK-specific requirements', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="US" <?php checked(in_array('US', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇺🇸 United States (CCPA/CPRA)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('California and other US privacy laws', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="CA" <?php checked(in_array('CA', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇨🇦 Canada (PIPEDA)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Personal Information Protection and Electronic Documents Act', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="BR" <?php checked(in_array('BR', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇧🇷 Brazil (LGPD)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Lei Geral de Proteção de Dados', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="SG" <?php checked(in_array('SG', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇸🇬 Singapore (PDPA)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Personal Data Protection Act', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="TH" <?php checked(in_array('TH', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇹🇭 Thailand (PDPA)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Personal Data Protection Act B.E. 2562', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="JP" <?php checked(in_array('JP', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇯🇵 Japan (APPI)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Act on the Protection of Personal Information', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="ZA" <?php checked(in_array('ZA', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇿🇦 South Africa (POPIA)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Protection of Personal Information Act', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="TR" <?php checked(in_array('TR', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇹🇷 Turkey (KVKK)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Kişisel Verilerin Korunması Kanunu', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="SA" <?php checked(in_array('SA', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇸🇦 Saudi Arabia (PDPL)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Personal Data Protection Law', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="AU" <?php checked(in_array('AU', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇦🇺 Australia (Privacy Act)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Australian Privacy Principles', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="CN" <?php checked(in_array('CN', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🇨🇳 China (PIPL)', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('Personal Information Protection Law', 'complyflow'); ?></span>
                            </div>
                        </label>

                        <label class="complyflow-checkbox-label">
                            <input type="checkbox" name="answers[target_countries][]" value="OTHER" <?php checked(in_array('OTHER', $answers['target_countries'] ?? [], true)); ?>>
                            <div>
                                <strong><?php esc_html_e('🌍 Other Regions', 'complyflow'); ?></strong>
                                <span class="description"><?php esc_html_e('General privacy best practices', 'complyflow'); ?></span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-admin-users"></span>
                        <?php esc_html_e('Data Protection Officer', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Do you have a DPO?', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="has_dpo" name="answers[has_dpo]" value="1" <?php checked(!empty($answers['has_dpo'])); ?>>
                                    <?php esc_html_e('We have a designated Data Protection Officer', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Required for GDPR if processing large amounts of personal data', 'complyflow'); ?></p>
                            </td>
                        </tr>
                    </table>

                    <div id="dpo-details" class="conditional-field <?php echo !empty($answers['has_dpo']) ? 'show' : ''; ?>">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="dpo_name"><?php esc_html_e('DPO Name', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="dpo_name" name="answers[dpo_name]" class="regular-text" value="<?php echo esc_attr($answers['dpo_name'] ?? ''); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="dpo_email"><?php esc_html_e('DPO Email', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="dpo_email" name="answers[dpo_email]" class="regular-text" value="<?php echo esc_attr($answers['dpo_email'] ?? ''); ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="questionnaire-nav">
                <button type="button" class="button button-secondary prev-step">← <?php esc_html_e('Previous', 'complyflow'); ?></button>
                <button type="button" class="button button-primary next-step"><?php esc_html_e('Next Step →', 'complyflow'); ?></button>
            </div>
        </div>
        <!-- Step 3: Data Collection -->
        <div class="questionnaire-step" data-step="3">
            <div class="complyflow-card">
                <div class="complyflow-card-header">
                    <h2>
                        <span class="dashicons dashicons-database"></span>
                        <?php esc_html_e('Data Collection', 'complyflow'); ?>
                    </h2>
                    <p class="description"><?php esc_html_e('Tell us what types of data you collect from users', 'complyflow'); ?></p>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-cart"></span>
                        <?php esc_html_e('E-commerce & Transactions', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('E-commerce', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="has_ecommerce" name="answers[has_ecommerce]" value="1" <?php checked(!empty($answers['has_ecommerce'])); ?>>
                                    <?php esc_html_e('We sell products or services online', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Check if you have an online store (WooCommerce, Shopify, etc.)', 'complyflow'); ?></p>
                            </td>
                        </tr>

                        <tr id="collect_payment_info_row" class="conditional-field <?php echo !empty($answers['has_ecommerce']) ? 'show' : ''; ?>">
                            <th scope="row"><?php esc_html_e('Payment Information', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[collect_payment_info]" value="1" <?php checked(!empty($answers['collect_payment_info'])); ?>>
                                    <?php esc_html_e('We collect payment information (credit cards, billing details)', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Check if you store payment info directly (not through payment processors)', 'complyflow'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Subscriptions', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[has_subscriptions]" value="1" <?php checked(!empty($answers['has_subscriptions'])); ?>>
                                    <?php esc_html_e('We offer subscription services or memberships', 'complyflow'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-email"></span>
                        <?php esc_html_e('User Accounts & Contact', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Email Collection', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[collect_emails]" value="1" <?php checked(!empty($answers['collect_emails'])); ?>>
                                    <?php esc_html_e('We collect email addresses from visitors', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('For newsletters, contact forms, etc.', 'complyflow'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('User Accounts', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[has_user_accounts]" value="1" <?php checked(!empty($answers['has_user_accounts'])); ?>>
                                    <?php esc_html_e('Users can create accounts on our website', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('For forums, memberships, or custom functionality', 'complyflow'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="questionnaire-nav">
                <button type="button" class="button button-secondary prev-step">← <?php esc_html_e('Previous', 'complyflow'); ?></button>
                <button type="button" class="button button-primary next-step"><?php esc_html_e('Next Step →', 'complyflow'); ?></button>
            </div>
        </div>

        <!-- Step 4: Third-Party Services -->
        <div class="questionnaire-step" data-step="4">
            <div class="complyflow-card">
                <div class="complyflow-card-header">
                    <h2>
                        <span class="dashicons dashicons-share"></span>
                        <?php esc_html_e('Third-Party Services', 'complyflow'); ?>
                    </h2>
                    <p class="description"><?php esc_html_e('List the third-party services you use on your website', 'complyflow'); ?></p>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-chart-bar"></span>
                        <?php esc_html_e('Analytics & Tracking', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Analytics Tools', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="has_analytics" name="answers[has_analytics]" value="1" <?php checked(!empty($answers['has_analytics'])); ?>>
                                    <?php esc_html_e('We use analytics services', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Select which analytics tools you use:', 'complyflow'); ?></p>
                            </td>
                        </tr>
                    </table>

                    <div id="analytics-tools" class="conditional-field <?php echo !empty($answers['has_analytics']) ? 'show' : ''; ?>">
                        <div class="multiselect-options">
                            <label class="complyflow-checkbox-label">
                                <input type="checkbox" name="answers[analytics_tools][]" value="google_analytics" <?php checked(in_array('google_analytics', $answers['analytics_tools'] ?? [], true)); ?>>
                                <div><strong><?php esc_html_e('Google Analytics', 'complyflow'); ?></strong></div>
                            </label>

                            <label class="complyflow-checkbox-label">
                                <input type="checkbox" name="answers[analytics_tools][]" value="google_tag_manager" <?php checked(in_array('google_tag_manager', $answers['analytics_tools'] ?? [], true)); ?>>
                                <div><strong><?php esc_html_e('Google Tag Manager', 'complyflow'); ?></strong></div>
                            </label>

                            <label class="complyflow-checkbox-label">
                                <input type="checkbox" name="answers[analytics_tools][]" value="facebook_pixel" <?php checked(in_array('facebook_pixel', $answers['analytics_tools'] ?? [], true)); ?>>
                                <div><strong><?php esc_html_e('Facebook Pixel', 'complyflow'); ?></strong></div>
                            </label>

                            <label class="complyflow-checkbox-label">
                                <input type="checkbox" name="answers[analytics_tools][]" value="hotjar" <?php checked(in_array('hotjar', $answers['analytics_tools'] ?? [], true)); ?>>
                                <div><strong><?php esc_html_e('Hotjar', 'complyflow'); ?></strong></div>
                            </label>

                            <label class="complyflow-checkbox-label">
                                <input type="checkbox" name="answers[analytics_tools][]" value="other" <?php checked(in_array('other', $answers['analytics_tools'] ?? [], true)); ?>>
                                <div><strong><?php esc_html_e('Other Analytics Tools', 'complyflow'); ?></strong></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-megaphone"></span>
                        <?php esc_html_e('Marketing & Advertising', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Advertising', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[has_advertising]" value="1" <?php checked(!empty($answers['has_advertising'])); ?>>
                                    <?php esc_html_e('We display ads or use remarketing', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Google Ads, Facebook Ads, display networks, etc.', 'complyflow'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Email Marketing', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="has_email_marketing" name="answers[has_email_marketing]" value="1" <?php checked(!empty($answers['has_email_marketing'])); ?>>
                                    <?php esc_html_e('We use email marketing services', 'complyflow'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <div id="email-marketing-provider" class="conditional-field <?php echo !empty($answers['has_email_marketing']) ? 'show' : ''; ?>">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="email_marketing_provider"><?php esc_html_e('Email Marketing Provider', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <select id="email_marketing_provider" name="answers[email_marketing_provider]" class="regular-text">
                                        <option value=""><?php esc_html_e('Select...', 'complyflow'); ?></option>
                                        <option value="mailchimp" <?php selected($answers['email_marketing_provider'] ?? '', 'mailchimp'); ?>><?php esc_html_e('Mailchimp', 'complyflow'); ?></option>
                                        <option value="constant_contact" <?php selected($answers['email_marketing_provider'] ?? '', 'constant_contact'); ?>><?php esc_html_e('Constant Contact', 'complyflow'); ?></option>
                                        <option value="sendinblue" <?php selected($answers['email_marketing_provider'] ?? '', 'sendinblue'); ?>><?php esc_html_e('SendinBlue', 'complyflow'); ?></option>
                                        <option value="activecampaign" <?php selected($answers['email_marketing_provider'] ?? '', 'activecampaign'); ?>><?php esc_html_e('ActiveCampaign', 'complyflow'); ?></option>
                                        <option value="other" <?php selected($answers['email_marketing_provider'] ?? '', 'other'); ?>><?php esc_html_e('Other', 'complyflow'); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-share-alt"></span>
                        <?php esc_html_e('Social Media', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Social Sharing', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[has_social_sharing]" value="1" <?php checked(!empty($answers['has_social_sharing'])); ?>>
                                    <?php esc_html_e('We have social media share buttons or embeds', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Facebook, Twitter, LinkedIn sharing buttons or embedded feeds', 'complyflow'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="questionnaire-nav">
                <button type="button" class="button button-secondary prev-step">← <?php esc_html_e('Previous', 'complyflow'); ?></button>
                <button type="button" class="button button-primary next-step"><?php esc_html_e('Next Step →', 'complyflow'); ?></button>
            </div>
        </div>

        <!-- Step 5: User Rights & Special Cases -->
        <div class="questionnaire-step" data-step="5">
            <div class="complyflow-card">
                <div class="complyflow-card-header">
                    <h2>
                        <span class="dashicons dashicons-shield"></span>
                        <?php esc_html_e('User Rights & Data Retention', 'complyflow'); ?>
                    </h2>
                    <p class="description"><?php esc_html_e('Define how you handle user data rights and retention policies', 'complyflow'); ?></p>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-download"></span>
                        <?php esc_html_e('User Rights', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Data Export', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[allow_data_export]" value="1" <?php checked(!empty($answers['allow_data_export'])); ?>>
                                    <?php esc_html_e('Users can request and export their data', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Required by GDPR and other privacy laws', 'complyflow'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Data Deletion', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="answers[allow_data_deletion]" value="1" <?php checked(!empty($answers['allow_data_deletion'])); ?>>
                                    <?php esc_html_e('Users can request deletion of their data', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Right to be forgotten - required by GDPR', 'complyflow'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-clock"></span>
                        <?php esc_html_e('Data Retention', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="data_retention_period"><?php esc_html_e('Retention Period', 'complyflow'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="number" id="data_retention_period" name="answers[data_retention_period]" class="regular-text required" value="<?php echo esc_attr($answers['data_retention_period'] ?? '13'); ?>" min="1" max="120">
                                <p class="description"><?php esc_html_e('How long (in months) do you keep user data? Default is 13 months for GDPR compliance.', 'complyflow'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="question-section">
                    <h3 class="question-section-title">
                        <span class="dashicons dashicons-groups"></span>
                        <?php esc_html_e('Special Considerations', 'complyflow'); ?>
                    </h3>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Children\'s Data', 'complyflow'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="allows_children" name="answers[allows_children]" value="1" <?php checked(!empty($answers['allows_children'])); ?>>
                                    <?php esc_html_e('Our website is directed at or allows children under 16', 'complyflow'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Check if your site targets children (requires special COPPA/GDPR compliance)', 'complyflow'); ?></p>
                            </td>
                        </tr>
                    </table>

                    <div id="minimum-age-field" class="conditional-field <?php echo !empty($answers['allows_children']) ? 'show' : ''; ?>">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="minimum_age"><?php esc_html_e('Minimum Age', 'complyflow'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="minimum_age" name="answers[minimum_age]" class="regular-text" value="<?php echo esc_attr($answers['minimum_age'] ?? '13'); ?>" min="0" max="18">
                                    <p class="description"><?php esc_html_e('Minimum age requirement for users (default: 13)', 'complyflow'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="questionnaire-nav">
                <button type="button" class="button button-secondary prev-step">← <?php esc_html_e('Previous', 'complyflow'); ?></button>
                <button type="button" class="button button-primary next-step"><?php esc_html_e('Next Step →', 'complyflow'); ?></button>
            </div>
        </div>

        <!-- Step 6: Review & Generate -->
        <div class="questionnaire-step" data-step="6">
            <div class="complyflow-card">
                <div class="complyflow-card-header">
                    <h2>
                        <span class="dashicons dashicons-yes-alt"></span>
                        <?php esc_html_e('Review & Generate', 'complyflow'); ?>
                    </h2>
                    <p class="description"><?php esc_html_e('Review your answers and generate your legal documents', 'complyflow'); ?></p>
                </div>

                <div id="questionnaire-summary">
                    <h3><?php esc_html_e('Your Answers Summary', 'complyflow'); ?></h3>
                    <p><?php esc_html_e('Please review your answers before generating policies. You can go back to previous steps to make changes.', 'complyflow'); ?></p>
                </div>

                <div class="complyflow-notice info">
                    <span class="dashicons dashicons-info"></span>
                    <p>
                        <strong><?php esc_html_e('Important Note:', 'complyflow'); ?></strong><br>
                        <?php esc_html_e('The generated policies are comprehensive templates based on your answers and selected compliance frameworks. While they provide a solid foundation, we strongly recommend having them reviewed by a legal professional to ensure they meet your specific business needs and jurisdictional requirements.', 'complyflow'); ?>
                    </p>
                </div>

                <div class="complyflow-notice warning">
                    <span class="dashicons dashicons-warning"></span>
                    <p>
                        <strong><?php esc_html_e('Disclaimer:', 'complyflow'); ?></strong><br>
                        <?php esc_html_e('ComplyFlow provides tools to help you create privacy-compliant documents, but these do not constitute legal advice. Consult with a qualified attorney for legal guidance specific to your situation.', 'complyflow'); ?>
                    </p>
                </div>
            </div>

            <div class="questionnaire-nav">
                <button type="button" class="button button-secondary prev-step">← <?php esc_html_e('Previous', 'complyflow'); ?></button>
                <button type="submit" class="button button-primary button-hero" id="submit-questionnaire">
                    <span class="dashicons dashicons-saved"></span>
                    <?php esc_html_e('Save & Generate Policies', 'complyflow'); ?>
                </button>
            </div>
        </div>
    </form>

    <!-- Loading Overlay -->
    <div class="questionnaire-loading">
        <div class="questionnaire-loading-spinner">
            <div class="spinner"></div>
            <p><?php esc_html_e('Generating your policies...', 'complyflow'); ?></p>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const totalSteps = 6;
    let currentStep = 1;

    // Step Navigation
    function goToStep(stepNumber) {
        if (stepNumber < 1 || stepNumber > totalSteps) return;

        // Hide all steps
        $('.questionnaire-step').removeClass('active');
        
        // Show target step
        $(`.questionnaire-step[data-step="${stepNumber}"]`).addClass('active');
        
        // Update progress
        updateProgress(stepNumber);
        
        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 300);
        
        currentStep = stepNumber;
    }

    function updateProgress(stepNumber) {
        // Update step circles
        $('.progress-step').each(function() {
            const step = $(this).data('step');
            $(this).removeClass('active completed');
            
            if (step < stepNumber) {
                $(this).addClass('completed');
            } else if (step === stepNumber) {
                $(this).addClass('active');
            }
        });

        // Update progress bar
        const percentage = (stepNumber / totalSteps) * 100;
        $('.progress-bar-fill').css('width', percentage + '%');
    }

    // Next button
    $('.next-step').on('click', function() {
        if (validateCurrentStep()) {
            goToStep(currentStep + 1);
            if (currentStep === 6) {
                setTimeout(generateSummary, 300);
            }
        }
    });

    // Previous button
    $('.prev-step').on('click', function() {
        goToStep(currentStep - 1);
    });

    // Click on progress step
    $('.progress-step').on('click', function() {
        const step = $(this).data('step');
        goToStep(step);
    });

    // Validation
    function validateCurrentStep() {
        const currentStepEl = $(`.questionnaire-step[data-step="${currentStep}"]`);
        const requiredFields = currentStepEl.find('input.required, textarea.required, select.required');
        let isValid = true;

        requiredFields.each(function() {
            const $field = $(this);
            const fieldValue = $field.val();
            
            // Skip checkbox/radio validation
            if ($field.is(':checkbox') || $field.is(':radio')) {
                return true;
            }
            
            // Check if field is empty
            if (!fieldValue || (typeof fieldValue === 'string' && fieldValue.trim() === '')) {
                isValid = false;
                $field.css('border-color', 'var(--cf-danger)');
                
                // Add error message if not exists
                if (!$field.next('.error-message').length) {
                    $field.after('<p class="error-message" style="color: var(--cf-danger); margin-top: 0.5rem; font-size: 0.875rem;"><?php esc_html_e('This field is required', 'complyflow'); ?></p>');
                }
            } else {
                // Field has value, clear error
                $field.css('border-color', '');
                $field.next('.error-message').remove();
            }
        });

        if (!isValid) {
            const firstError = currentStepEl.find('input.required[style*="border-color: var(--cf-danger)"], textarea.required[style*="border-color: var(--cf-danger)"], select.required[style*="border-color: var(--cf-danger)"]').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 300);
            }
        }

        return isValid;
    }

    // Clear validation errors on input
    $('input.required, textarea.required, select.required').on('input change', function() {
        const $field = $(this);
        const fieldValue = $field.val();
        
        if (fieldValue && (typeof fieldValue !== 'string' || fieldValue.trim() !== '')) {
            $field.css('border-color', '');
            $field.next('.error-message').remove();
        }
    });

    // Conditional Fields Logic
    $('#has_dpo').on('change', function() {
        $('#dpo-details').toggleClass('show', $(this).is(':checked'));
    });

    $('#has_ecommerce').on('change', function() {
        $('#collect_payment_info_row').toggleClass('show', $(this).is(':checked'));
    });

    $('#has_analytics').on('change', function() {
        $('#analytics-tools').toggleClass('show', $(this).is(':checked'));
    });

    $('#has_email_marketing').on('change', function() {
        $('#email-marketing-provider').toggleClass('show', $(this).is(':checked'));
    });

    $('#allows_children').on('change', function() {
        $('#minimum-age-field').toggleClass('show', $(this).is(':checked'));
    });

    // Form Submission
    $('#complyflow-questionnaire-form').on('submit', function(e) {
        e.preventDefault();

        if (!validateCurrentStep()) {
            return false;
        }

        $('.questionnaire-loading').addClass('active');

        const formData = new FormData(this);
        formData.append('action', 'complyflow_save_questionnaire');

        // CRITICAL FIX: Ensure all checkboxes (including unchecked) have values
        // Unchecked checkboxes don't submit, so we need to add them with value "0"
        const checkboxFields = [
            'has_ecommerce',
            'collect_emails',
            'has_user_accounts',
            'collect_payment_info',
            'has_analytics',
            'has_advertising',
            'has_social_sharing',
            'has_email_marketing',
            'allow_data_export',
            'allow_data_deletion',
            'has_dpo',
            'allows_children'
        ];
        
        checkboxFields.forEach(function(fieldName) {
            const fieldKey = 'answers[' + fieldName + ']';
            // Check if this checkbox field already exists in formData
            let hasField = false;
            for (let pair of formData.entries()) {
                if (pair[0] === fieldKey) {
                    hasField = true;
                    break;
                }
            }
            // If not in formData, it means checkbox is unchecked - add with value "0"
            if (!hasField) {
                formData.append(fieldKey, '0');
            }
        });

        // Debug: Log what we're sending
        console.log('ComplyFlow: Submitting questionnaire data...');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('.questionnaire-loading').removeClass('active');
                
                console.log('ComplyFlow: Server response:', response);
                
                if (response.success) {
                    // Show success message
                    $('<div class="notice notice-success is-dismissible" style="position: fixed; top: 32px; right: 20px; z-index: 999999; min-width: 300px;"><p>' + response.data.message + '</p></div>')
                        .insertAfter('.complyflow-questionnaire-header')
                        .delay(3000)
                        .fadeOut();

                    // Redirect after short delay
                    setTimeout(function() {
                        window.location.href = response.data.redirect || '<?php echo esc_url(admin_url('admin.php?page=complyflow-documents')); ?>';
                    }, 1500);
                } else {
                    // Show error message from server
                    var errorMsg = response.data && response.data.message ? response.data.message : '<?php esc_html_e('Error saving questionnaire. Please try again.', 'complyflow'); ?>';
                    console.error('ComplyFlow: Save failed:', errorMsg);
                    $('<div class="notice notice-error is-dismissible" style="position: fixed; top: 32px; right: 20px; z-index: 999999; min-width: 300px;"><p>' + errorMsg + '</p></div>')
                        .insertAfter('.complyflow-questionnaire-header')
                        .delay(5000)
                        .fadeOut();
                }
            },
            error: function(xhr, status, error) {
                $('.questionnaire-loading').removeClass('active');
                console.error('ComplyFlow AJAX Error:', status, error);
                console.error('ComplyFlow Response:', xhr.responseText);
                
                var errorMsg = '<?php esc_html_e('Network error. Please try again.', 'complyflow'); ?>';
                if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.data && response.data.message) {
                            errorMsg = response.data.message;
                        }
                    } catch(e) {
                        console.error('Could not parse error response:', e);
                    }
                }
                
                $('<div class="notice notice-error is-dismissible" style="position: fixed; top: 32px; right: 20px; z-index: 999999; min-width: 300px;"><p>' + errorMsg + '</p></div>')
                    .insertAfter('.complyflow-questionnaire-header')
                    .delay(5000)
                    .fadeOut();
            }
        });

        return false;
    });

    // Generate Summary
    function generateSummary() {
        const summary = $('#questionnaire-summary');
        let html = '<div class="summary-sections">';

        html += '<div class="summary-section">';
        html += '<h4><span class="dashicons dashicons-admin-home"></span> <?php esc_html_e('Business Information', 'complyflow'); ?></h4>';
        html += '<ul>';
        html += '<li><strong><?php esc_html_e('Company:', 'complyflow'); ?></strong> ' + ($('#company_name').val() || '<?php esc_html_e('Not provided', 'complyflow'); ?>') + '</li>';
        html += '<li><strong><?php esc_html_e('Email:', 'complyflow'); ?></strong> ' + ($('#contact_email').val() || '<?php esc_html_e('Not provided', 'complyflow'); ?>') + '</li>';
        html += '</ul></div>';

        const countries = [];
        $('input[name="answers[target_countries][]"]:checked').each(function() {
            countries.push($(this).parent().find('strong').text());
        });
        html += '<div class="summary-section">';
        html += '<h4><span class="dashicons dashicons-admin-site-alt3"></span> <?php esc_html_e('Compliance Regions', 'complyflow'); ?></h4>';
        html += '<ul><li>' + (countries.length > 0 ? countries.join(', ') : '<?php esc_html_e('None selected', 'complyflow'); ?>') + '</li></ul></div>';

        const dataCollection = [];
        if ($('#has_ecommerce').is(':checked')) dataCollection.push('<?php esc_html_e('E-commerce', 'complyflow'); ?>');
        if ($('input[name="answers[collect_emails]"]').is(':checked')) dataCollection.push('<?php esc_html_e('Email Collection', 'complyflow'); ?>');
        if ($('input[name="answers[has_user_accounts]"]').is(':checked')) dataCollection.push('<?php esc_html_e('User Accounts', 'complyflow'); ?>');
        
        html += '<div class="summary-section">';
        html += '<h4><span class="dashicons dashicons-database"></span> <?php esc_html_e('Data Collection', 'complyflow'); ?></h4>';
        html += '<ul><li>' + (dataCollection.length > 0 ? dataCollection.join(', ') : '<?php esc_html_e('None selected', 'complyflow'); ?>') + '</li></ul></div>';

        const thirdParty = [];
        if ($('#has_analytics').is(':checked')) thirdParty.push('<?php esc_html_e('Analytics', 'complyflow'); ?>');
        if ($('input[name="answers[has_advertising]"]').is(':checked')) thirdParty.push('<?php esc_html_e('Advertising', 'complyflow'); ?>');
        if ($('#has_email_marketing').is(':checked')) thirdParty.push('<?php esc_html_e('Email Marketing', 'complyflow'); ?>');
        if ($('input[name="answers[has_social_sharing]"]').is(':checked')) thirdParty.push('<?php esc_html_e('Social Media', 'complyflow'); ?>');
        
        html += '<div class="summary-section">';
        html += '<h4><span class="dashicons dashicons-share"></span> <?php esc_html_e('Third-Party Services', 'complyflow'); ?></h4>';
        html += '<ul><li>' + (thirdParty.length > 0 ? thirdParty.join(', ') : '<?php esc_html_e('None selected', 'complyflow'); ?>') + '</li></ul></div>';

        const userRights = [];
        if ($('input[name="answers[allow_data_export]"]').is(':checked')) userRights.push('<?php esc_html_e('Data Export', 'complyflow'); ?>');
        if ($('input[name="answers[allow_data_deletion]"]').is(':checked')) userRights.push('<?php esc_html_e('Data Deletion', 'complyflow'); ?>');
        
        html += '<div class="summary-section">';
        html += '<h4><span class="dashicons dashicons-shield"></span> <?php esc_html_e('User Rights', 'complyflow'); ?></h4>';
        html += '<ul><li>' + (userRights.length > 0 ? userRights.join(', ') : '<?php esc_html_e('None selected', 'complyflow'); ?>') + '</li>';
        html += '<li><strong><?php esc_html_e('Data Retention:', 'complyflow'); ?></strong> ' + ($('#data_retention_period').val() || '13') + ' <?php esc_html_e('months', 'complyflow'); ?></li>';
        html += '</ul></div>';

        html += '</div>';
        summary.html(html);
    }

    // Initialize
    updateProgress(1);
});
</script>

<style>
.summary-sections {
    display: grid;
    gap: 1.5rem;
}

.summary-section h4 {
    color: var(--cf-primary);
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.75rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-section h4 .dashicons {
    font-size: 1.25rem;
    width: 1.25rem;
    height: 1.25rem;
}

.summary-section ul {
    margin: 0;
    padding-left: 1.5rem;
    list-style: disc;
}

.summary-section ul li {
    margin-bottom: 0.5rem;
    color: var(--cf-text);
}

.error-message {
    animation: shake 0.3s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>
