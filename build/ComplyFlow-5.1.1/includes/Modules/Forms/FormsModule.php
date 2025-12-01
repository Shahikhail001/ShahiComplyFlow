<?php
namespace ComplyFlow\Modules\Forms;

use ComplyFlow\Modules\Forms\FormManager;

if (!defined('ABSPATH')) {
    exit;
}

class FormsModule {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu'], 40);
        add_action('wp_ajax_complyflow_scan_forms', [$this, 'ajax_scan_forms']);
    }

    public function add_admin_menu() {
        add_submenu_page(
            'complyflow',
            __('Form Compliance Scanner', 'complyflow'),
            __('Form Compliance Scanner', 'complyflow'),
            'manage_options',
            'complyflow-form-scanner',
            [$this, 'render_admin_page']
        );
        add_submenu_page(
            'complyflow',
            __('Consent Checkbox Text', 'complyflow'),
            __('Consent Checkbox Text', 'complyflow'),
            'manage_options',
            'complyflow-consent-text',
            [$this, 'render_consent_text_page']
        );
        add_submenu_page(
            'complyflow',
            __('Form Data Retention', 'complyflow'),
            __('Form Data Retention', 'complyflow'),
            'manage_options',
            'complyflow-form-retention',
            [$this, 'render_retention_settings_page']
        );
        add_submenu_page(
            'complyflow',
            __('Consent Logs', 'complyflow'),
            __('Consent Logs', 'complyflow'),
            'manage_options',
            'complyflow-consent-logs',
            [$this, 'render_consent_logs_page']
        );
    }

    public function render_consent_logs_page() {
        \ComplyFlow\Modules\Forms\ConsentLogRenderer::render();
    }

    public function render_retention_settings_page() {
        \ComplyFlow\Modules\Forms\RetentionSettingsRenderer::render();
    }

    public function render_consent_text_page() {
        \ComplyFlow\Modules\Forms\ConsentSettingsRenderer::render();
    }

    public function render_admin_page() {
        include COMPLYFLOW_PATH . 'includes/Admin/views/form-compliance-scanner.php';
    }

    public function ajax_scan_forms() {
        check_ajax_referer('complyflow_scan_forms_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'complyflow')]);
            return;
        }
        $results = FormManager::scan_forms();
        $html = '';
        if ($results) {
            $html .= '<table class="widefat"><thead><tr><th>Plugin</th><th>Form</th><th>Issues</th></tr></thead><tbody>';
            foreach ($results as $form) {
                $html .= '<tr>';
                $html .= '<td>' . esc_html($form['plugin']) . '</td>';
                $html .= '<td>' . esc_html($form['title']) . '</td>';
                if (empty($form['issues'])) {
                    $html .= '<td class="complyflow-form-ok">No issues found</td>';
                } else {
                    $html .= '<td class="complyflow-form-issue">' . implode('<br>', array_map('esc_html', $form['issues'])) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html = '<div class="notice notice-info"><p>No forms detected.</p></div>';
        }
        wp_send_json_success(['html' => $html]);
    }
}
