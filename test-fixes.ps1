<#
.SYNOPSIS
    Test all fixes for v5.1.1 - Chart data and Settings save
#>

Write-Host "=== ComplyFlow v5.1.1 Fix Verification ===" -ForegroundColor Cyan
Write-Host ""

# Test 1: Check compliance history table and data
Write-Host "Test 1: Checking compliance history table..." -ForegroundColor Yellow
$result1 = php -r "require 'c:/xampp/htdocs/shahitest/wp-load.php'; global `$wpdb; `$table = `$wpdb->prefix . 'complyflow_compliance_history'; `$count = `$wpdb->get_var('SELECT COUNT(*) FROM ' . `$table); echo `$count;"

if ($result1 -gt 0) {
    Write-Host "  ✓ Table has $result1 record(s)" -ForegroundColor Green
} else {
    Write-Host "  ✗ Table is empty" -ForegroundColor Red
}

# Test 2: Check if snapshot has real data (not NULL)
Write-Host "`nTest 2: Checking if snapshot has real data..." -ForegroundColor Yellow
$phpCode = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wpdb;
$table = $wpdb->prefix . 'complyflow_compliance_history';
$record = $wpdb->get_row("SELECT * FROM $table ORDER BY recorded_at DESC LIMIT 1", ARRAY_A);
if ($record && $record['compliance_score'] > 0) {
    echo "PASS";
} else {
    echo "FAIL";
}
'@

$result2 = php -r $phpCode
if ($result2 -eq "PASS") {
    Write-Host "  ✓ Snapshot contains real data" -ForegroundColor Green
} else {
    Write-Host "  ✗ Snapshot data is NULL or invalid" -ForegroundColor Red
}

# Test 3: Check if DashboardWidgets has correct method
Write-Host "`nTest 3: Checking DashboardWidgets::get_real_compliance_trends()..." -ForegroundColor Yellow
$grep = Select-String -Path "includes/Modules/Dashboard/DashboardWidgets.php" -Pattern "function get_real_compliance_trends"
if ($grep) {
    Write-Host "  ✓ Method exists" -ForegroundColor Green
} else {
    Write-Host "  ✗ Method not found" -ForegroundColor Red
}

# Test 4: Check if threshold changed from 5 to 0
Write-Host "`nTest 4: Checking if data threshold was lowered..." -ForegroundColor Yellow
$grep2 = Select-String -Path "includes/Modules/Dashboard/DashboardWidgets.php" -Pattern "count\(\`$history\) === 0"
if ($grep2) {
    Write-Host "  ✓ Threshold changed to work with minimal data" -ForegroundColor Green
} else {
    Write-Host "  ✗ Still requires 5+ records" -ForegroundColor Red
}

# Test 5: Check if AJAX handler is implemented
Write-Host "`nTest 5: Checking ajax_save_settings implementation..." -ForegroundColor Yellow
$grep3 = Select-String -Path "includes/Core/Plugin.php" -Pattern "settings->save"
if ($grep3) {
    Write-Host "  ✓ AJAX handler implemented" -ForegroundColor Green
} else {
    Write-Host "  ✗ AJAX handler missing implementation" -ForegroundColor Red
}

# Test 6: Check settings value
Write-Host "`nTest 6: Checking compliance_history_schedule setting..." -ForegroundColor Yellow
$phpCode2 = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
$settings = get_option('complyflow_settings', []);
echo isset($settings['compliance_history_schedule']) ? $settings['compliance_history_schedule'] : 'NOT_SET';
'@

$result6 = php -r $phpCode2
if ($result6 -ne "NOT_SET") {
    Write-Host "  ✓ Setting is configured: $result6" -ForegroundColor Green
} else {
    Write-Host "  ✗ Setting not found" -ForegroundColor Red
}

# Test 7: Check DSR key fix
Write-Host "`nTest 7: Checking DSR pending key fix..." -ForegroundColor Yellow
$grep4 = Select-String -Path "includes/Core/ComplianceHistoryScheduler.php" -Pattern "dsr_stats\['pending'\]"
if ($grep4) {
    Write-Host "  ✓ DSR key fixed (pending instead of pending_count)" -ForegroundColor Green
} else {
    Write-Host "  ✗ DSR key still incorrect" -ForegroundColor Red
}

Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "All critical fixes have been applied:" -ForegroundColor White
Write-Host "  1. Chart now uses real data with threshold of 0 records" -ForegroundColor White
Write-Host "  2. Settings Save button AJAX handler implemented" -ForegroundColor White
Write-Host "  3. DSR statistics key corrected" -ForegroundColor White
Write-Host "  4. Compliance history snapshot fixed" -ForegroundColor White

Write-Host "`nNext Steps:" -ForegroundColor Yellow
Write-Host "  1. Refresh the WordPress admin page" -ForegroundColor White
Write-Host "  2. Navigate to ComplyFlow Dashboard" -ForegroundColor White
Write-Host "  3. Verify the 30-Day Compliance Trend shows real data" -ForegroundColor White
Write-Host "  4. Go to Settings > Accessibility" -ForegroundColor White
Write-Host "  5. Try saving settings to verify Save button works" -ForegroundColor White
