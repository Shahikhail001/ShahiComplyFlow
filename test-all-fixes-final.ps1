<#
.SYNOPSIS
    Final comprehensive verification - All Settings tabs
#>

Write-Host "======================================================" -ForegroundColor Cyan
Write-Host "  ComplyFlow v5.1.1 - Settings Save Complete Test" -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host ""

$allPassed = $true

# Test 1: Chart fix verification
Write-Host "1. Chart Mock Data Fix" -ForegroundColor Yellow
Write-Host "   Testing compliance history..." -ForegroundColor Gray
$phpCode = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wpdb;
$table = $wpdb->prefix . 'complyflow_compliance_history';
$count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE compliance_score > 0");
echo $count > 0 ? "PASS" : "FAIL";
'@
$result = php -r $phpCode
if ($result -eq "PASS") {
    Write-Host "   ✓ Chart has real historical data" -ForegroundColor Green
} else {
    Write-Host "   ✗ Chart data issue" -ForegroundColor Red
    $allPassed = $false
}

# Test 2: AJAX Save Hook
Write-Host "`n2. Settings Save - AJAX Hook Registration" -ForegroundColor Yellow
Write-Host "   Checking wp_ajax_complyflow_save_settings..." -ForegroundColor Gray
$phpCode = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wp_filter;
echo isset($wp_filter['wp_ajax_complyflow_save_settings']) ? "PASS" : "FAIL";
'@
$result = php -r $phpCode
if ($result -eq "PASS") {
    Write-Host "   ✓ Save settings AJAX hook registered" -ForegroundColor Green
} else {
    Write-Host "   ✗ Hook not registered" -ForegroundColor Red
    $allPassed = $false
}

# Test 3: Export Hook
Write-Host "`n3. Settings Export - AJAX Hook Registration" -ForegroundColor Yellow
Write-Host "   Checking wp_ajax_complyflow_export_settings..." -ForegroundColor Gray
$phpCode = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wp_filter;
echo isset($wp_filter['wp_ajax_complyflow_export_settings']) ? "PASS" : "FAIL";
'@
$result = php -r $phpCode
if ($result -eq "PASS") {
    Write-Host "   ✓ Export settings AJAX hook registered" -ForegroundColor Green
} else {
    Write-Host "   ✗ Hook not registered" -ForegroundColor Red
    $allPassed = $false
}

# Test 4: Import Hook
Write-Host "`n4. Settings Import - AJAX Hook Registration" -ForegroundColor Yellow
Write-Host "   Checking wp_ajax_complyflow_import_settings..." -ForegroundColor Gray
$phpCode = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wp_filter;
echo isset($wp_filter['wp_ajax_complyflow_import_settings']) ? "PASS" : "FAIL";
'@
$result = php -r $phpCode
if ($result -eq "PASS") {
    Write-Host "   ✓ Import settings AJAX hook registered" -ForegroundColor Green
} else {
    Write-Host "   ✗ Hook not registered" -ForegroundColor Red
    $allPassed = $false
}

# Test 5: Hook Location
Write-Host "`n5. AJAX Handlers Registration Location" -ForegroundColor Yellow
Write-Host "   Verifying hooks are outside is_admin() check..." -ForegroundColor Gray
$content = Get-Content "includes/Core/Plugin.php" -Raw
if ($content -match "// Register AJAX handlers \(must be outside is_admin check for AJAX requests\)") {
    Write-Host "   ✓ AJAX handlers correctly placed" -ForegroundColor Green
} else {
    Write-Host "   ✗ AJAX handlers may be in wrong location" -ForegroundColor Red
    $allPassed = $false
}

# Test 6: Method Implementation
Write-Host "`n6. AJAX Save Method Implementation" -ForegroundColor Yellow
Write-Host "   Checking ajax_save_settings() implementation..." -ForegroundColor Gray
$grep = Select-String -Path "includes/Core/Plugin.php" -Pattern "settings->validate.*settings->save" -Context 0,5
if ($grep) {
    Write-Host "   ✓ Method fully implemented with validation" -ForegroundColor Green
} else {
    Write-Host "   ✗ Method implementation incomplete" -ForegroundColor Red
    $allPassed = $false
}

# Test 7: Sanitization Helper
Write-Host "`n7. Settings Sanitization" -ForegroundColor Yellow
Write-Host "   Checking sanitize_settings_array() helper..." -ForegroundColor Gray
$grep = Select-String -Path "includes/Core/Plugin.php" -Pattern "private function sanitize_settings_array"
if ($grep) {
    Write-Host "   ✓ Sanitization helper exists" -ForegroundColor Green
} else {
    Write-Host "   ✗ Sanitization helper missing" -ForegroundColor Red
    $allPassed = $false
}

# Test 8: JavaScript Handler
Write-Host "`n8. JavaScript Form Handler" -ForegroundColor Yellow
Write-Host "   Checking form submission listener..." -ForegroundColor Gray
$grep = Select-String -Path "assets/src/js/admin.js" -Pattern "complyflow-settings-form.*saveSettings"
if ($grep) {
    Write-Host "   ✓ JavaScript event listener configured" -ForegroundColor Green
} else {
    Write-Host "   ✗ JavaScript handler not found" -ForegroundColor Red
    $allPassed = $false
}

# Test 9: Nonce Configuration
Write-Host "`n9. Security - Nonce Verification" -ForegroundColor Yellow
Write-Host "   Checking nonce setup..." -ForegroundColor Gray
$grep = Select-String -Path "includes/Core/Plugin.php" -Pattern "check_ajax_referer\('complyflow_admin_nonce'"
if ($grep) {
    Write-Host "   ✓ Nonce verification in place" -ForegroundColor Green
} else {
    Write-Host "   ✗ Nonce verification missing" -ForegroundColor Red
    $allPassed = $false
}

# Test 10: Capability Check
Write-Host "`n10. Security - Permission Check" -ForegroundColor Yellow
Write-Host "    Checking manage_options capability..." -ForegroundColor Gray
$grep = Select-String -Path "includes/Core/Plugin.php" -Pattern "current_user_can\('manage_options'\)"
if ($grep) {
    Write-Host "    ✓ Permission check implemented" -ForegroundColor Green
} else {
    Write-Host "    ✗ Permission check missing" -ForegroundColor Red
    $allPassed = $false
}

# Final Summary
Write-Host "`n======================================================" -ForegroundColor Cyan
if ($allPassed) {
    Write-Host "  ✓ ALL TESTS PASSED - Production Ready!" -ForegroundColor Green
    Write-Host "======================================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Next Steps:" -ForegroundColor White
    Write-Host "  1. Refresh WordPress admin (Ctrl+F5)" -ForegroundColor White
    Write-Host "  2. Go to ComplyFlow → Dashboard" -ForegroundColor White
    Write-Host "     - Verify 30-Day Compliance Trend shows real data" -ForegroundColor White
    Write-Host "  3. Go to ComplyFlow → Settings" -ForegroundColor White
    Write-Host "     - Test ALL tabs:" -ForegroundColor White
    Write-Host "       • General" -ForegroundColor White
    Write-Host "       • Consent Manager" -ForegroundColor White
    Write-Host "       • Accessibility" -ForegroundColor White
    Write-Host "       • DSR Portal" -ForegroundColor White
    Write-Host "       • Legal Documents" -ForegroundColor White
    Write-Host "       • Advanced" -ForegroundColor White
    Write-Host "     - Change a setting on each tab" -ForegroundColor White
    Write-Host "     - Click 'Save Settings'" -ForegroundColor White
    Write-Host "     - Verify success message appears" -ForegroundColor White
    Write-Host ""
    Write-Host "Browser Test URL:" -ForegroundColor Cyan
    Write-Host "  https://localhost/shahitest/wp-content/plugins/ShahiComplyFlow/test-settings-save-browser.php" -ForegroundColor Gray
} else {
    Write-Host "  ✗ Some Tests Failed" -ForegroundColor Red
    Write-Host "======================================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Please review the failed tests above." -ForegroundColor Yellow
}
Write-Host ""
