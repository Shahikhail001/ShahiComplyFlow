<?php
namespace ComplyFlow\Modules\Analytics;

if (!defined('ABSPATH')) {
    exit;
}

class ReportExporter {
    /**
     * Export compliance report as CSV
     */
    public static function export_csv() {
        $score = \ComplyFlow\Modules\Analytics\ComplianceScore::calculate();
        $logs = \ComplyFlow\Modules\Analytics\AuditTrail::get_recent();
        $output = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=complyflow_report.csv');
        fputcsv($output, ['Compliance Score', $score]);
        fputcsv($output, []);
        fputcsv($output, ['Action', 'User ID', 'Details', 'Timestamp']);
        foreach ($logs as $log) {
            fputcsv($output, [$log->action, $log->user_id, $log->details, $log->timestamp]);
        }
        fclose($output);
        exit;
    }
}
