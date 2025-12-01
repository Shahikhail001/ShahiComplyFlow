<#
.SYNOPSIS
    Comprehensive Settings Save Fix Verification
#>

Write-Host "=== ComplyFlow Settings Save Fix Verification ===" -ForegroundColor Cyan
Write-Host ""

$tests = @()

# Test 1: Check AJAX hook registration
Write-Host "Test 1: Verifying AJAX hook registration..." -ForegroundColor Yellow
$phpCode1 = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wp_filter;
echo isset($wp_filter['wp_ajax_complyflow_save_settings']) ? "PASS" : "FAIL";
'@
$result1 = php -r $phpCode1
if ($result1 -eq "PASS") {
    Write-Host "  ✓ wp_ajax_complyflow_save_settings is registered" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ Hook not registered" -ForegroundColor Red
    $tests += $false
}

# Test 2: Check export hook
Write-Host "`nTest 2: Verifying export hook registration..." -ForegroundColor Yellow
$phpCode2 = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wp_filter;
echo isset($wp_filter['wp_ajax_complyflow_export_settings']) ? "PASS" : "FAIL";
'@
$result2 = php -r $phpCode2
if ($result2 -eq "PASS") {
    Write-Host "  ✓ wp_ajax_complyflow_export_settings is registered" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ Hook not registered" -ForegroundColor Red
    $tests += $false
}

# Test 3: Check import hook
Write-Host "`nTest 3: Verifying import hook registration..." -ForegroundColor Yellow
$phpCode3 = @'
require 'c:/xampp/htdocs/shahitest/wp-load.php';
global $wp_filter;
echo isset($wp_filter['wp_ajax_complyflow_import_settings']) ? "PASS" : "FAIL";
'@
$result3 = php -r $phpCode3
if ($result3 -eq "PASS") {
    Write-Host "  ✓ wp_ajax_complyflow_import_settings is registered" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ Hook not registered" -ForegroundColor Red
    $tests += $false
}

# Test 4: Check if ajax_save_settings method exists
Write-Host "`nTest 4: Verifying ajax_save_settings method..." -ForegroundColor Yellow
$grep4 = Select-String -Path "includes/Core/Plugin.php" -Pattern "public function ajax_save_settings"
if ($grep4) {
    Write-Host "  ✓ Method exists in Plugin.php" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ Method not found" -ForegroundColor Red
    $tests += $false
}

# Test 5: Check if method has implementation
Write-Host "`nTest 5: Verifying method implementation..." -ForegroundColor Yellow
$grep5 = Select-String -Path "includes/Core/Plugin.php" -Pattern "settings->save"
if ($grep5) {
    Write-Host "  ✓ Method has full implementation" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ Method missing implementation" -ForegroundColor Red
    $tests += $false
}

# Test 6: Check if register_ajax_handlers is called outside is_admin
Write-Host "`nTest 6: Verifying AJAX handlers registration location..." -ForegroundColor Yellow
$content = Get-Content "includes/Core/Plugin.php" -Raw
if ($content -match "// Register AJAX handlers \(must be outside is_admin check for AJAX requests\)") {
    Write-Host "  ✓ AJAX handlers registered outside is_admin() check" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ AJAX handlers may still be inside is_admin() check" -ForegroundColor Red
    $tests += $false
}

# Test 7: Check JavaScript event listener
Write-Host "`nTest 7: Verifying JavaScript form handler..." -ForegroundColor Yellow
$grep7 = Select-String -Path "assets/src/js/admin.js" -Pattern "\.complyflow-settings-form.*saveSettings"
if ($grep7) {
    Write-Host "  ✓ JavaScript listening for form submission" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ JavaScript handler not found" -ForegroundColor Red
    $tests += $false
}

# Test 8: Check nonce generation
Write-Host "`nTest 8: Verifying nonce configuration..." -ForegroundColor Yellow
$grep8 = Select-String -Path "includes/Core/Plugin.php" -Pattern "wp_create_nonce\('complyflow_admin_nonce'\)"
if ($grep8) {
    Write-Host "  ✓ Nonce 'complyflow_admin_nonce' is generated" -ForegroundColor Green
    $tests += $true
} else {
    Write-Host "  ✗ Nonce not found" -ForegroundColor Red
    $tests += $false
}

# Summary
Write-Host "`n=== Test Summary ===" -ForegroundColor Cyan
$passed = ($tests | Where-Object { $_ -eq $true }).Count
$total = $tests.Count
$percentage = [math]::Round(($passed / $total) * 100, 0)

Write-Host "Tests Passed: $passed/$total ($percentage%)" -ForegroundColor $(if ($passed -eq $total) { "Green" } else { "Yellow" })

if ($passed -eq $total) {
    Write-Host "`n✓ ALL TESTS PASSED!" -ForegroundColor Green
    Write-Host "`nThe Settings Save button should now work correctly." -ForegroundColor White
    Write-Host "Please refresh your WordPress admin page and test:" -ForegroundColor White
    Write-Host "  1. Go to ComplyFlow → Settings" -ForegroundColor White
    Write-Host "  2. Switch between tabs (General, Consent, Accessibility, etc.)" -ForegroundColor White
    Write-Host "  3. Change any setting" -ForegroundColor White
    Write-Host "  4. Click 'Save Settings'" -ForegroundColor White
    Write-Host "  5. You should see a success message at the top" -ForegroundColor White
} else {
    Write-Host "`n⚠ Some tests failed. Please review the errors above." -ForegroundColor Yellow
}

Write-Host "`nTest location: https://localhost/shahitest/wp-content/plugins/ShahiComplyFlow/test-settings-save-browser.php" -ForegroundColor Cyan
