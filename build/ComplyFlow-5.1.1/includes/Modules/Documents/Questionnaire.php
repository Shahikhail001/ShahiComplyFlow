<?php
/**
 * Questionnaire Engine
 *
 * Intelligent questionnaire system for gathering site information
 * to generate compliant legal documents.
 *
 * @package ComplyFlow\Modules\Documents
 * @since   1.0.0
 */

namespace ComplyFlow\Modules\Documents;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Questionnaire
 */
class Questionnaire {
    /**
     * Get all questionnaire questions
     *
     * @return array Questions with conditional logic.
     */
    public function get_questions(): array {
        $questions = [
            // Basic Information
            [
                'id' => 'company_name',
                'section' => 'basic',
                'text' => __('Legal Company/Business Name', 'complyflow'),
                'description' => __('The official name of your business or organization', 'complyflow'),
                'type' => 'text',
                'required' => true,
                'default' => get_bloginfo('name'),
                'affects' => ['all_policies'],
            ],
            [
                'id' => 'contact_email',
                'section' => 'basic',
                'text' => __('Contact Email Address', 'complyflow'),
                'description' => __('Email address for privacy and legal inquiries', 'complyflow'),
                'type' => 'email',
                'required' => true,
                'default' => get_option('admin_email'),
                'affects' => ['all_policies'],
            ],
            [
                'id' => 'physical_address',
                'section' => 'basic',
                'text' => __('Physical Business Address', 'complyflow'),
                'description' => __('Required for GDPR compliance (EU businesses)', 'complyflow'),
                'type' => 'textarea',
                'required' => false,
                'affects' => ['privacy_policy', 'terms_of_service'],
            ],
            [
                'id' => 'phone_number',
                'section' => 'basic',
                'text' => __('Contact Phone Number', 'complyflow'),
                'description' => __('Optional but recommended for user support', 'complyflow'),
                'type' => 'text',
                'required' => false,
                'affects' => ['privacy_policy', 'terms_of_service'],
            ],

            // Target Regions
            [
                'id' => 'target_countries',
                'section' => 'compliance',
                'text' => __('Which regions do you target?', 'complyflow'),
                'description' => __('Select all regions where your users are located', 'complyflow'),
                'type' => 'multiselect',
                'required' => true,
                'options' => [
                    'EU' => __('European Union (GDPR)', 'complyflow'),
                    'UK' => __('United Kingdom (UK GDPR)', 'complyflow'),
                    'US' => __('United States', 'complyflow'),
                    'CA' => __('California (CCPA)', 'complyflow'),
                    'BR' => __('Brazil (LGPD)', 'complyflow'),
                    'CN' => __('Canada (PIPEDA)', 'complyflow'),
                    'SG' => __('Singapore (PDPA)', 'complyflow'),
                    'TH' => __('Thailand (PDPA)', 'complyflow'),
                    'JP' => __('Japan (APPI)', 'complyflow'),
                    'ZA' => __('South Africa (POPIA)', 'complyflow'),
                    'TR' => __('Turkey (KVKK)', 'complyflow'),
                    'SA' => __('Saudi Arabia (PDPL)', 'complyflow'),
                    'AU' => __('Australia (Privacy Act)', 'complyflow'),
                    'OTHER' => __('Other Regions', 'complyflow'),
                ],
                'affects' => ['privacy_policy', 'cookie_policy'],
            ],

            // Data Collection
            [
                'id' => 'has_ecommerce',
                'section' => 'data_collection',
                'text' => __('Do you sell products or services?', 'complyflow'),
                'description' => __('Ecommerce sites have additional compliance requirements', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'default' => is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active('easy-digital-downloads/easy-digital-downloads.php'),
                'affects' => ['privacy_policy', 'terms_of_service'],
            ],
            [
                'id' => 'collect_emails',
                'section' => 'data_collection',
                'text' => __('Do you collect email addresses?', 'complyflow'),
                'description' => __('For newsletters, user accounts, or contact forms', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'default' => true,
                'affects' => ['privacy_policy'],
            ],
            [
                'id' => 'has_user_accounts',
                'section' => 'data_collection',
                'text' => __('Do users create accounts on your site?', 'complyflow'),
                'description' => __('Membership sites, forums, or user registrations', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'default' => get_option('users_can_register'),
                'affects' => ['privacy_policy', 'terms_of_service'],
            ],
            [
                'id' => 'collect_payment_info',
                'section' => 'data_collection',
                'text' => __('Do you collect payment information?', 'complyflow'),
                'description' => __('Credit cards, bank details, PayPal, etc.', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'show_if' => ['has_ecommerce' => true],
                'affects' => ['privacy_policy', 'terms_of_service'],
            ],
            [
                'id' => 'has_subscriptions',
                'section' => 'data_collection',
                'text' => __('Do you offer subscription services?', 'complyflow'),
                'description' => __('Recurring billing for memberships or products', 'complyflow'),
                'type' => 'boolean',
                'required' => false,
                'show_if' => ['has_ecommerce' => true],
                'default' => is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php'),
                'affects' => ['terms_of_service'],
            ],

            // Third-Party Services
            [
                'id' => 'has_analytics',
                'section' => 'third_party',
                'text' => __('Do you use analytics tools?', 'complyflow'),
                'description' => __('Google Analytics, Matomo, etc.', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'affects' => ['privacy_policy', 'cookie_policy'],
            ],
            [
                'id' => 'analytics_tools',
                'section' => 'third_party',
                'text' => __('Which analytics tools do you use?', 'complyflow'),
                'type' => 'multiselect',
                'required' => false,
                'show_if' => ['has_analytics' => true],
                'options' => [
                    'google_analytics' => __('Google Analytics', 'complyflow'),
                    'google_tag_manager' => __('Google Tag Manager', 'complyflow'),
                    'matomo' => __('Matomo', 'complyflow'),
                    'hotjar' => __('Hotjar', 'complyflow'),
                    'other' => __('Other', 'complyflow'),
                ],
                'affects' => ['privacy_policy', 'cookie_policy'],
            ],
            [
                'id' => 'has_advertising',
                'section' => 'third_party',
                'text' => __('Do you use advertising networks?', 'complyflow'),
                'description' => __('Google AdSense, Facebook Ads, etc.', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'affects' => ['privacy_policy', 'cookie_policy'],
            ],
            [
                'id' => 'has_social_sharing',
                'section' => 'third_party',
                'text' => __('Do you have social media sharing buttons?', 'complyflow'),
                'description' => __('Facebook, Twitter, LinkedIn share buttons', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'affects' => ['privacy_policy', 'cookie_policy'],
            ],
            [
                'id' => 'has_email_marketing',
                'section' => 'third_party',
                'text' => __('Do you use email marketing services?', 'complyflow'),
                'description' => __('Mailchimp, Constant Contact, SendGrid, etc.', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'show_if' => ['collect_emails' => true],
                'affects' => ['privacy_policy'],
            ],
            [
                'id' => 'email_marketing_provider',
                'section' => 'third_party',
                'text' => __('Which email marketing provider?', 'complyflow'),
                'type' => 'select',
                'required' => false,
                'show_if' => ['has_email_marketing' => true],
                'options' => [
                    'mailchimp' => __('Mailchimp', 'complyflow'),
                    'constant_contact' => __('Constant Contact', 'complyflow'),
                    'sendgrid' => __('SendGrid', 'complyflow'),
                    'mailerlite' => __('MailerLite', 'complyflow'),
                    'other' => __('Other', 'complyflow'),
                ],
                'affects' => ['privacy_policy'],
            ],

            // User Rights
            [
                'id' => 'allow_data_export',
                'section' => 'user_rights',
                'text' => __('Allow users to export their data?', 'complyflow'),
                'description' => __('Required for GDPR compliance', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'default' => true,
                'affects' => ['privacy_policy'],
            ],
            [
                'id' => 'allow_data_deletion',
                'section' => 'user_rights',
                'text' => __('Allow users to request data deletion?', 'complyflow'),
                'description' => __('Required for GDPR compliance', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'default' => true,
                'affects' => ['privacy_policy'],
            ],
            [
                'id' => 'data_retention_period',
                'section' => 'user_rights',
                'text' => __('How long do you retain user data?', 'complyflow'),
                'description' => __('In months (e.g., 12 for 1 year, 24 for 2 years)', 'complyflow'),
                'type' => 'number',
                'required' => true,
                'default' => 24,
                'affects' => ['privacy_policy'],
            ],

            // DPO (Data Protection Officer)
            [
                'id' => 'has_dpo',
                'section' => 'compliance',
                'text' => __('Do you have a Data Protection Officer (DPO)?', 'complyflow'),
                'description' => __('Required for GDPR if processing large-scale sensitive data or as a core activity', 'complyflow'),
                'type' => 'boolean',
                'required' => false,
                'default' => false,
                'affects' => ['privacy_policy', 'data_protection'],
            ],
            [
                'id' => 'dpo_name',
                'section' => 'compliance',
                'text' => __('Data Protection Officer Name', 'complyflow'),
                'description' => __('Full name of your DPO', 'complyflow'),
                'type' => 'text',
                'required' => false,
                'show_if' => ['has_dpo' => true],
                'affects' => ['privacy_policy', 'data_protection'],
            ],
            [
                'id' => 'dpo_email',
                'section' => 'compliance',
                'text' => __('Data Protection Officer Email', 'complyflow'),
                'description' => __('Contact email for data protection inquiries', 'complyflow'),
                'type' => 'email',
                'required' => false,
                'show_if' => ['has_dpo' => true],
                'affects' => ['privacy_policy', 'data_protection'],
            ],

            // Children & COPPA
            [
                'id' => 'allows_children',
                'section' => 'special',
                'text' => __('Do you knowingly collect data from children under 13?', 'complyflow'),
                'description' => __('COPPA compliance required if yes', 'complyflow'),
                'type' => 'boolean',
                'required' => true,
                'default' => false,
                'affects' => ['privacy_policy'],
            ],
            [
                'id' => 'minimum_age',
                'section' => 'special',
                'text' => __('Minimum age requirement for your service', 'complyflow'),
                'type' => 'number',
                'required' => false,
                'default' => 13,
                'affects' => ['privacy_policy', 'terms_of_service'],
            ],
        ];

        return $this->apply_conditional_logic($questions);
    }

    /**
     * Apply conditional logic to questions
     *
     * @param array $questions Questions array.
     * @return array Filtered questions.
     */
    private function apply_conditional_logic(array $questions): array {
        $answers = $this->get_saved_answers();
        $filtered = [];

        foreach ($questions as $question) {
            // Check if question should be shown
            if (isset($question['show_if'])) {
                $show = true;
                foreach ($question['show_if'] as $dependency_id => $required_value) {
                    if (!isset($answers[$dependency_id]) || $answers[$dependency_id] !== $required_value) {
                        $show = false;
                        break;
                    }
                }
                if (!$show) {
                    continue;
                }
            }

            $filtered[] = $question;
        }

        return $filtered;
    }

    /**
     * Get saved questionnaire answers
     *
     * @return array Saved answers.
     */
    public function get_saved_answers(): array {
        return get_option('complyflow_questionnaire_answers', []);
    }

    /**
     * Save questionnaire answers
     *
     * @param array $answers Answers to save.
     * @return bool Success status.
     */
    public function save_answers(array $answers): bool {
        // Sanitize answers
        $sanitized = [];
        
        foreach ($answers as $key => $value) {
            if (is_array($value)) {
                // Handle multi-select checkboxes - save even if empty
                $sanitized[$key] = empty($value) ? [] : array_map('sanitize_text_field', $value);
            } elseif (is_bool($value) || $value === '1' || $value === '0') {
                // Handle checkboxes
                $sanitized[$key] = $value === true || $value === '1' || $value === 1;
            } elseif (is_numeric($value)) {
                $sanitized[$key] = $value;
            } else {
                // Handle text fields - save even if empty (for proper validation)
                $sanitized[$key] = sanitize_textarea_field($value);
            }
        }

        // update_option returns false if the value is the same, so we need to handle that
        $old_value = get_option('complyflow_questionnaire_answers', []);
        $result = update_option('complyflow_questionnaire_answers', $sanitized, true);
        
        // If update_option returns false, check if it's because the value is the same
        if ($result === false) {
            // Compare old and new values
            $is_same = ($old_value === $sanitized);
            
            if ($is_same) {
                // Value is the same, which is actually a success case
                error_log('ComplyFlow: Questionnaire answers unchanged, but save is successful');
                return true;
            } else {
                // Actual failure
                error_log('ComplyFlow: Failed to save questionnaire answers. Data: ' . print_r($sanitized, true));
                return false;
            }
        }
        
        error_log('ComplyFlow: Successfully saved questionnaire answers. Count: ' . count($sanitized));
        return true;
    }

    /**
     * Get questions grouped by section
     *
     * @return array Questions grouped by section.
     */
    public function get_questions_by_section(): array {
        $questions = $this->get_questions();
        $grouped = [];

        foreach ($questions as $question) {
            $section = $question['section'] ?? 'general';
            if (!isset($grouped[$section])) {
                $grouped[$section] = [
                    'title' => $this->get_section_title($section),
                    'description' => $this->get_section_description($section),
                    'questions' => [],
                ];
            }
            $grouped[$section]['questions'][] = $question;
        }

        return $grouped;
    }

    /**
     * Get section title
     *
     * @param string $section Section ID.
     * @return string Section title.
     */
    private function get_section_title(string $section): string {
        $titles = [
            'basic' => __('Basic Information', 'complyflow'),
            'compliance' => __('Compliance & Regions', 'complyflow'),
            'data_collection' => __('Data Collection', 'complyflow'),
            'third_party' => __('Third-Party Services', 'complyflow'),
            'user_rights' => __('User Rights & Data Retention', 'complyflow'),
            'special' => __('Special Considerations', 'complyflow'),
        ];

        return $titles[$section] ?? ucfirst($section);
    }

    /**
     * Get section description
     *
     * @param string $section Section ID.
     * @return string Section description.
     */
    private function get_section_description(string $section): string {
        $descriptions = [
            'basic' => __('Provide your business contact information', 'complyflow'),
            'compliance' => __('Select the regions and laws that apply to your business', 'complyflow'),
            'data_collection' => __('Tell us what types of data you collect from users', 'complyflow'),
            'third_party' => __('List the third-party services you use on your website', 'complyflow'),
            'user_rights' => __('Define how you handle user data rights and retention', 'complyflow'),
            'special' => __('Additional compliance considerations', 'complyflow'),
        ];

        return $descriptions[$section] ?? '';
    }

    /**
     * Check if questionnaire is complete
     *
     * @return bool True if all required questions answered.
     */
    public function is_complete(): bool {
        $questions = $this->get_questions();
        $answers = $this->get_saved_answers();

        // Debug logging
        error_log('ComplyFlow is_complete check:');
        error_log('- Total questions: ' . count($questions));
        error_log('- Total answers: ' . count($answers));

        foreach ($questions as $question) {
            // Skip non-required fields
            if (!$question['required']) {
                continue;
            }

            // Check if field is conditionally shown
            if (isset($question['show_if'])) {
                $show_field = true;
                foreach ($question['show_if'] as $condition_key => $condition_value) {
                    // Check if the condition is met
                    if (!isset($answers[$condition_key]) || $answers[$condition_key] !== $condition_value) {
                        $show_field = false;
                        error_log(sprintf(
                            '- Required field "%s" SKIPPED (condition not met: %s must be %s, but is %s)',
                            $question['id'],
                            $condition_key,
                            json_encode($condition_value),
                            isset($answers[$condition_key]) ? json_encode($answers[$condition_key]) : 'not set'
                        ));
                        break;
                    }
                }
                
                // If condition not met, skip this field
                if (!$show_field) {
                    continue;
                }
            }

            // Check if required field has a value
            $has_answer = isset($answers[$question['id']]);
            
            // For boolean fields, just check if isset (false/0 is a valid answer)
            // For other fields, check if not empty
            $is_valid = false;
            if ($question['type'] === 'boolean') {
                // Boolean: just needs to be set (true/false/0/1 are all valid)
                $is_valid = $has_answer;
            } else {
                // Other types: must not be empty
                $is_valid = $has_answer && !empty($answers[$question['id']]);
            }
            
            error_log(sprintf(
                '- Required field "%s" (type=%s): has_answer=%s, is_valid=%s, value=%s',
                $question['id'],
                $question['type'],
                $has_answer ? 'yes' : 'NO',
                $is_valid ? 'yes' : 'NO',
                $has_answer ? json_encode($answers[$question['id']]) : 'N/A'
            ));
            
            if (!$is_valid) {
                error_log('ComplyFlow: is_complete() = FALSE (missing required field: ' . $question['id'] . ')');
                return false;
            }
        }

        error_log('ComplyFlow: is_complete() = TRUE (all required fields present)');
        return true;
    }

    /**
     * Get completion percentage
     *
     * @return int Completion percentage (0-100).
     */
    public function get_completion_percentage(): int {
        $questions = $this->get_questions();
        $answers = $this->get_saved_answers();
        
        $total = count($questions);
        $answered = 0;

        foreach ($questions as $question) {
            if (!empty($answers[$question['id']])) {
                $answered++;
            }
        }

        return $total > 0 ? (int) (($answered / $total) * 100) : 0;
    }
}
