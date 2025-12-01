<?php
namespace ComplyFlow\Modules\Analytics;

use ComplyFlow\Modules\Analytics\ComplianceDashboardRenderer;
use ComplyFlow\Modules\Analytics\AuditTrailRenderer;
use ComplyFlow\Modules\Analytics\ReportExporter;

if (!defined('ABSPATH')) {
    exit;
}

class AnalyticsModule {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu'], 60);
        add_action('admin_post_complyflow_export_report', [$this, 'export_report']);
    }

    public function add_admin_menu() {
        add_submenu_page(
            'complyflow',
            __('Compliance Dashboard', 'complyflow'),
            __('Compliance Dashboard', 'complyflow'),
            'manage_options',
            'complyflow-compliance-dashboard',
            [$this, 'render_dashboard_page']
        );
        add_submenu_page(
            'complyflow',
            __('Audit Trail', 'complyflow'),
            __('Audit Trail', 'complyflow'),
            'manage_options',
            'complyflow-audit-trail',
            [$this, 'render_audit_page']
        );
        add_submenu_page(
            'complyflow',
            __('Export Compliance Report', 'complyflow'),
            __('Export Compliance Report', 'complyflow'),
            'manage_options',
            'complyflow-export-report',
            [$this, 'render_export_page']
        );
    }

    public function render_dashboard_page() {
        ComplianceDashboardRenderer::render();
    }

    public function render_audit_page() {
        AuditTrailRenderer::render();
    }

    public function render_export_page() {
        echo '<h2>' . esc_html__('Export Compliance Report', 'complyflow') . '</h2>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="complyflow_export_report">';
        echo '<button type="submit" class="button button-primary">' . esc_html__('Download CSV', 'complyflow') . '</button>';
        echo '</form>';
    }

    public function export_report() {
        ReportExporter::export_csv();
    }
}
