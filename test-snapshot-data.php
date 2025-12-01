<?php
require_once '../../../wp-load.php';

$widgets = new \ComplyFlow\Modules\Dashboard\DashboardWidgets();

echo "=== Testing Dashboard Methods ===\n\n";

echo "1. Compliance Score:\n";
$compliance = $widgets->get_compliance_score();
print_r($compliance);
echo "\n";

echo "2. Accessibility Summary:\n";
$accessibility = $widgets->get_accessibility_summary();
print_r($accessibility);
echo "\n";

echo "3. DSR Statistics:\n";
$dsr = $widgets->get_dsr_statistics();
print_r($dsr);
echo "\n";

echo "4. Consent Statistics:\n";
$consent = $widgets->get_consent_statistics();
print_r($consent);
echo "\n";

echo "5. Cookie Summary:\n";
$cookies = $widgets->get_cookie_summary();
print_r($cookies);
echo "\n";

echo "\n=== Testing Snapshot Data Preparation ===\n";
$snapshot = [
    'compliance_score' => $compliance['score'] ?? 0,
    'module_scores' => $compliance['breakdown'] ?? [],
    'accessibility_issues' => $accessibility['total_issues'] ?? 0,
    'dsr_pending_count' => $dsr['pending_count'] ?? 0,
    'consent_acceptance_rate' => $consent['acceptance_rate'] ?? 0,
    'cookie_count' => $cookies['total_cookies'] ?? 0,
];

echo "\nPrepared Snapshot Data:\n";
print_r($snapshot);

echo "\n=== Settings Check ===\n";
$settings = get_option('complyflow_settings', []);
echo "Settings:\n";
print_r($settings);

echo "\nCompliance history schedule: " . ($settings['compliance_history_schedule'] ?? 'NOT SET') . "\n";
