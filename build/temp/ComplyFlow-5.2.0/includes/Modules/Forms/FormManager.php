<?php
namespace ComplyFlow\Modules\Forms;

if (!defined('ABSPATH')) {
    exit;
}

class FormManager {
    /**
     * Hook into form submissions to log consent
     */
    public static function log_consent_hooks() {
        // WPForms
        add_action('wpforms_process_complete', function($fields, $entry, $form_data) {
            $consent = $fields['complyflow_consent'] ?? '';
            $user_id = get_current_user_id();
            \ComplyFlow\Modules\Forms\ConsentLogger::log($form_data['id'], $user_id, $consent);
        }, 10, 3);

        // Contact Form 7
        add_action('wpcf7_mail_sent', function($contact_form) {
            $submission = \WPCF7_Submission::get_instance();
            $data = $submission ? $submission->get_posted_data() : [];
            $consent = $data['complyflow_consent'] ?? '';
            $user_id = get_current_user_id();
            \ComplyFlow\Modules\Forms\ConsentLogger::log($contact_form->id(), $user_id, $consent);
        });

        // Gravity Forms
        add_action('gform_after_submission', function($entry, $form) {
            $consent = rgar($entry, 'complyflow_consent');
            $user_id = get_current_user_id();
            \ComplyFlow\Modules\Forms\ConsentLogger::log($form['id'], $user_id, $consent);
        }, 10, 2);
    }
    /**
     * Inject consent checkbox into supported forms
     */
    public static function inject_consent_checkbox() {
        $consent_text = \ComplyFlow\Modules\Forms\ConsentTextSettings::get_consent_text();
        // WPForms
        add_filter('wpforms_frontend_fields', function($fields, $form_data) use ($consent_text) {
            $has_consent = false;
            foreach ($fields as $field) {
                if ($field['type'] === 'checkbox' && stripos($field['label'], 'consent') !== false) {
                    $has_consent = true;
                }
            }
            if (!$has_consent) {
                $fields[] = [
                    'id' => 'complyflow_consent',
                    'type' => 'checkbox',
                    'label' => $consent_text,
                    'required' => true,
                ];
            }
            return $fields;
        }, 10, 2);

        // Contact Form 7
        add_filter('wpcf7_form_elements', function($content) use ($consent_text) {
            if (stripos($content, 'acceptance') === false && stripos($content, 'consent') === false) {
                $consent_html = '<span class="wpcf7-form-control-wrap complyflow-consent"><input type="checkbox" name="complyflow_consent" required> ' . esc_html($consent_text) . '</span>';
                $content .= '<div class="complyflow-consent-field">' . $consent_html . '</div>';
            }
            return $content;
        });

        // Gravity Forms
        add_filter('gform_pre_render', function($form) use ($consent_text) {
            $has_consent = false;
            foreach ($form['fields'] as $field) {
                if ($field->type === 'checkbox' && stripos($field->label, 'consent') !== false) {
                    $has_consent = true;
                }
            }
            if (!$has_consent) {
                $form['fields'][] = new \GF_Field_Checkbox([
                    'label' => $consent_text,
                    'isRequired' => true,
                    'choices' => [['text' => $consent_text, 'value' => '1']],
                ]);
            }
            return $form;
        });
    }
    /**
     * Scan all supported forms for compliance issues
     * @return array
     */
    public static function scan_forms() {
        $results = [];
        // WPForms
        if (class_exists('WPForms')) {
            $forms = \WPForms\Forms\Loader::get();
            foreach ($forms as $form) {
                $results[] = self::scan_wpforms($form);
            }
        }
        // Contact Form 7
        if (class_exists('WPCF7_ContactForm')) {
            $forms = \WPCF7_ContactForm::find();
            foreach ($forms as $form) {
                $results[] = self::scan_cf7($form);
            }
        }
        // Gravity Forms
        if (class_exists('GFForms')) {
            $forms = \GFFormsModel::get_forms();
            foreach ($forms as $form) {
                $results[] = self::scan_gravity($form);
            }
        }
        // Native WP forms (search for wpforms/form tags in posts)
        // ...extend as needed...
        return $results;
    }

    private static function scan_wpforms($form) {
        $issues = [];
        $fields = $form['fields'] ?? [];
        $has_consent = false;
        foreach ($fields as $field) {
            if (isset($field['type']) && $field['type'] === 'checkbox' && stripos($field['label'], 'consent') !== false) {
                $has_consent = true;
            }
        }
        if (!$has_consent) {
            $issues[] = 'Missing consent checkbox';
        }
        // ...add more checks...
        return [
            'plugin' => 'WPForms',
            'title' => $form['name'] ?? 'Untitled',
            'id' => $form['id'] ?? '',
            'issues' => $issues,
        ];
    }

    private static function scan_cf7($form) {
        $issues = [];
        $content = $form->prop('form');
        if (stripos($content, 'acceptance') === false && stripos($content, 'consent') === false) {
            $issues[] = 'Missing consent checkbox';
        }
        // ...add more checks...
        return [
            'plugin' => 'Contact Form 7',
            'title' => $form->title(),
            'id' => $form->id(),
            'issues' => $issues,
        ];
    }

    private static function scan_gravity($form) {
        $issues = [];
        $fields = $form['fields'] ?? [];
        $has_consent = false;
        foreach ($fields as $field) {
            if (isset($field['type']) && $field['type'] === 'checkbox' && stripos($field['label'], 'consent') !== false) {
                $has_consent = true;
            }
        }
        if (!$has_consent) {
            $issues[] = 'Missing consent checkbox';
        }
        // ...add more checks...
        return [
            'plugin' => 'Gravity Forms',
            'title' => $form['title'] ?? 'Untitled',
            'id' => $form['id'] ?? '',
            'issues' => $issues,
        ];
    }
}
