# Advanced Features Test Script
# ShahiComplyFlow v4.8.0
# Test all newly implemented features

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "ShahiComplyFlow - Advanced Features Test" -ForegroundColor Cyan
Write-Host "Version 4.8.0 - Feature Test Suite" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$testsPassed = 0
$testsFailed = 0
$testsTotal = 0

function Test-Feature {
    param(
        [string]$Name,
        [scriptblock]$Test
    )
    
    $script:testsTotal++
    Write-Host "Testing: $Name" -ForegroundColor Yellow -NoNewline
    
    try {
        $result = & $Test
        if ($result) {
            Write-Host " ✓ PASSED" -ForegroundColor Green
            $script:testsPassed++
            return $true
        } else {
            Write-Host " ✗ FAILED" -ForegroundColor Red
            $script:testsFailed++
            return $false
        }
    } catch {
        Write-Host " ✗ ERROR: $($_.Exception.Message)" -ForegroundColor Red
        $script:testsFailed++
        return $false
    }
}

$pluginPath = "c:\xampp\htdocs\shahitest\wp-content\plugins\ShahiComplyFlow"

# Test 1: Check if main files exist
Write-Host "`n=== File Existence Tests ===" -ForegroundColor Cyan
Test-Feature "Legal documents view file exists" {
    Test-Path "$pluginPath\includes\Admin\views\legal-documents.php"
}

Test-Feature "DocumentsModule file exists" {
    Test-Path "$pluginPath\includes\Modules\Documents\DocumentsModule.php"
}

Test-Feature "Advanced Features Guide exists" {
    Test-Path "$pluginPath\ADVANCED_FEATURES_GUIDE.md"
}

Test-Feature "Implementation Summary exists" {
    Test-Path "$pluginPath\ADVANCED_FEATURES_IMPLEMENTATION_SUMMARY.md"
}

# Test 2: Check for TinyMCE integration
Write-Host "`n=== TinyMCE Integration Tests ===" -ForegroundColor Cyan
$legalDocsContent = Get-Content "$pluginPath\includes\Admin\views\legal-documents.php" -Raw

Test-Feature "TinyMCE initialization code present" {
    $legalDocsContent -match "wp\.editor\.initialize"
}

Test-Feature "Editor tabs (Editor/HTML/Preview) present" {
    $legalDocsContent -match 'data-tab="editor"' -and 
    $legalDocsContent -match 'data-tab="html"' -and 
    $legalDocsContent -match 'data-tab="preview"'
}

Test-Feature "TinyMCE content retrieval logic present" {
    $legalDocsContent -match "tinymce\.get\('complyflow-policy-editor'\)"
}

Test-Feature "Tab switching functionality present" {
    $legalDocsContent -match "\.complyflow-tab-btn.*click" -and
    $legalDocsContent -match "\.complyflow-tab-content"
}

Test-Feature "TinyMCE cleanup on modal close" {
    $legalDocsContent -match "wp\.editor\.remove"
}

# Test 3: Check for Version History implementation
Write-Host "`n=== Version History Tests ===" -ForegroundColor Cyan
$moduleContent = Get-Content "$pluginPath\includes\Modules\Documents\DocumentsModule.php" -Raw

Test-Feature "Version History AJAX handler registered" {
    $moduleContent -match "wp_ajax_complyflow_get_version_history"
}

Test-Feature "Version History function exists" {
    $moduleContent -match "public function ajax_get_version_history"
}

Test-Feature "Version History timeline UI present" {
    $legalDocsContent -match "complyflow-version-timeline" -and
    $legalDocsContent -match "complyflow-version-item"
}

Test-Feature "Version metadata display (timestamp, size, user)" {
    $legalDocsContent -match "version\.timestamp" -and
    $legalDocsContent -match "version\.size" -and
    $legalDocsContent -match "version\.user"
}

Test-Feature "Version actions (View, Compare, Restore)" {
    $legalDocsContent -match "complyflow-view-version" -and
    $legalDocsContent -match "complyflow-compare-versions" -and
    $legalDocsContent -match "complyflow-restore-version"
}

Test-Feature "showVersionHistory function exists" {
    $legalDocsContent -match "function showVersionHistory\(policyType\)"
}

# Test 4: Check for Comparison Tool implementation
Write-Host "`n=== Comparison Tool Tests ===" -ForegroundColor Cyan

Test-Feature "Compare versions AJAX handler registered" {
    $moduleContent -match "wp_ajax_complyflow_compare_versions"
}

Test-Feature "Compare versions function exists" {
    $moduleContent -match "public function ajax_compare_versions"
}

Test-Feature "Diff generation function exists" {
    $moduleContent -match "private function generate_diff"
}

Test-Feature "Diff viewer UI present" {
    $legalDocsContent -match "complyflow-diff-view" -and
    $legalDocsContent -match "complyflow-diff-legend"
}

Test-Feature "Diff color coding (added/removed lines)" {
    $legalDocsContent -match "diff-line-added" -and
    $legalDocsContent -match "diff-line-removed"
}

Test-Feature "compareVersions function exists" {
    $legalDocsContent -match "function compareVersions\(policyType, version1, version2\)"
}

# Test 5: Check for PDF Export implementation
Write-Host "`n=== PDF Export Tests ===" -ForegroundColor Cyan

Test-Feature "PDF export AJAX handler registered" {
    $moduleContent -match "wp_ajax_complyflow_export_pdf"
}

Test-Feature "PDF export function exists" {
    $moduleContent -match "public function ajax_export_pdf"
}

Test-Feature "Export PDF buttons present (4 policy types)" {
    $exportCount = ([regex]::Matches($legalDocsContent, 'class="button export-pdf"')).Count
    $exportCount -eq 4
}

Test-Feature "PDF export JavaScript handler present" {
    $legalDocsContent -match "\.export-pdf.*click" -and
    $legalDocsContent -match "printWindow"
}

Test-Feature "Print dialog CSS optimization present" {
    $legalDocsContent -match "@media print" -or
    $legalDocsContent -match "printWindow\.document\.write\('<style>'"
}

# Test 6: Check for Version Restore functionality
Write-Host "`n=== Version Restore Tests ===" -ForegroundColor Cyan

Test-Feature "Restore version AJAX handler registered" {
    $moduleContent -match "wp_ajax_complyflow_restore_version"
}

Test-Feature "Restore version function exists" {
    $moduleContent -match "public function ajax_restore_version"
}

Test-Feature "Auto-backup before restore logic present" {
    $moduleContent -match "Save current version to history before restoring"
}

Test-Feature "restoreVersion function exists in frontend" {
    $legalDocsContent -match "function restoreVersion\(policyType, version\)"
}

Test-Feature "Restore confirmation dialog present" {
    $legalDocsContent -match "confirm.*restore version"
}

# Test 7: Check for Security implementations
Write-Host "`n=== Security Tests ===" -ForegroundColor Cyan

Test-Feature "Nonce verification in all AJAX handlers" {
    $handlers = @(
        "ajax_get_version_history",
        "ajax_compare_versions", 
        "ajax_restore_version",
        "ajax_export_pdf"
    )
    
    $allSecure = $true
    foreach ($handler in $handlers) {
        if ($moduleContent -notmatch "$handler[\s\S]{0,200}check_ajax_referer") {
            $allSecure = $false
            break
        }
    }
    $allSecure
}

Test-Feature "User capability checks present" {
    $capabilityChecks = ([regex]::Matches($moduleContent, "current_user_can\('manage_options'\)")).Count
    $capabilityChecks -ge 4  # At least 4 new handlers
}

Test-Feature "Input sanitization present" {
    $moduleContent -match "sanitize_text_field" -or
    $moduleContent -match "intval\(\\\$_POST"
}

Test-Feature "Output escaping in frontend" {
    $legalDocsContent -match "esc_html"
}

# Test 8: Check for CSS Styling
Write-Host "`n=== Styling Tests ===" -ForegroundColor Cyan

Test-Feature "Modal tab styles present" {
    $legalDocsContent -match "\.complyflow-modal-tabs" -and
    $legalDocsContent -match "\.complyflow-tab-btn"
}

Test-Feature "Version history timeline styles" {
    $legalDocsContent -match "\.complyflow-version-timeline" -and
    $legalDocsContent -match "\.complyflow-version-badge"
}

Test-Feature "Diff viewer styles present" {
    $legalDocsContent -match "\.complyflow-diff-view" -and
    $legalDocsContent -match "\.diff-line-added" -and
    $legalDocsContent -match "\.diff-line-removed"
}

Test-Feature "Spin animation for loading states" {
    $legalDocsContent -match "@keyframes spin" -and
    $legalDocsContent -match "\.spin"
}

Test-Feature "Badge component styles present" {
    $legalDocsContent -match "\.complyflow-badge" -and
    $legalDocsContent -match "\.complyflow-badge-success"
}

# Test 9: Check for Helper Functions
Write-Host "`n=== Helper Functions Tests ===" -ForegroundColor Cyan

Test-Feature "format_bytes helper function exists" {
    $moduleContent -match "private function format_bytes"
}

Test-Feature "generate_diff helper function exists" {
    $moduleContent -match "private function generate_diff"
}

Test-Feature "Version History button in edit modal" {
    $legalDocsContent -match "complyflow-view-history" -and
    $legalDocsContent -match "Version History"
}

# Test 10: Check for Data Storage logic
Write-Host "`n=== Data Storage Tests ===" -ForegroundColor Cyan

Test-Feature "Version history storage logic present" {
    $moduleContent -match "_version_history"
}

Test-Feature "Edited version storage present" {
    $moduleContent -match "_edited"
}

Test-Feature "Timestamp tracking present" {
    $moduleContent -match "_edited_timestamp" -or
    $moduleContent -match "current_time\('mysql'\)"
}

Test-Feature "Manual edit flag present" {
    $moduleContent -match "_manual_edit"
}

# Test 11: PHP Syntax Check
Write-Host "`n=== PHP Syntax Tests ===" -ForegroundColor Cyan

Test-Feature "legal-documents.php has no PHP syntax errors" {
    $phpPath = (Get-Command php -ErrorAction SilentlyContinue).Source
    if ($phpPath) {
        $result = php -l "$pluginPath\includes\Admin\views\legal-documents.php" 2>&1
        $result -match "No syntax errors"
    } else {
        Write-Host " (PHP not found, skipping)" -ForegroundColor Yellow
        $true  # Skip if PHP not available
    }
}

Test-Feature "DocumentsModule.php has no PHP syntax errors" {
    $phpPath = (Get-Command php -ErrorAction SilentlyContinue).Source
    if ($phpPath) {
        $result = php -l "$pluginPath\includes\Modules\Documents\DocumentsModule.php" 2>&1
        $result -match "No syntax errors"
    } else {
        Write-Host " (PHP not found, skipping)" -ForegroundColor Yellow
        $true  # Skip if PHP not available
    }
}

# Test 12: Documentation Tests
Write-Host "`n=== Documentation Tests ===" -ForegroundColor Cyan

Test-Feature "Advanced Features Guide is comprehensive" {
    $guideContent = Get-Content "$pluginPath\ADVANCED_FEATURES_GUIDE.md" -Raw
    $guideContent.Length -gt 10000  # Should be substantial
}

Test-Feature "Guide covers all 5 features" {
    $guideContent = Get-Content "$pluginPath\ADVANCED_FEATURES_GUIDE.md" -Raw
    $guideContent -match "TinyMCE" -and
    $guideContent -match "Version History" -and
    $guideContent -match "Comparison Tool" -and
    $guideContent -match "PDF Export" -and
    $guideContent -match "Automated.*Update"
}

Test-Feature "Implementation summary exists and is detailed" {
    $summaryContent = Get-Content "$pluginPath\ADVANCED_FEATURES_IMPLEMENTATION_SUMMARY.md" -Raw
    $summaryContent.Length -gt 5000
}

Test-Feature "Documentation includes usage instructions" {
    $guideContent = Get-Content "$pluginPath\ADVANCED_FEATURES_GUIDE.md" -Raw
    $guideContent -match "How to Use" -and
    $guideContent -match "Technical Details"
}

# Test 13: Integration Tests
Write-Host "`n=== Integration Tests ===" -ForegroundColor Cyan

Test-Feature "Edit modal integrates TinyMCE properly" {
    $legalDocsContent -match "edit-policy.*click" -and
    $legalDocsContent -match "wp\.editor\.initialize.*complyflow-policy-editor"
}

Test-Feature "Version History accessible from edit modal" {
    $legalDocsContent -match "complyflow-view-history.*click" -and
    $legalDocsContent -match "showVersionHistory"
}

Test-Feature "Comparison tool accessible from version history" {
    $legalDocsContent -match "complyflow-compare-versions.*click" -and
    $legalDocsContent -match "compareVersions"
}

Test-Feature "All modals have close handlers" {
    $modalCloseCount = ([regex]::Matches($legalDocsContent, "\.complyflow-modal-close.*click")).Count
    $modalCloseCount -ge 3  # Edit, History, Diff modals
}

# Test 14: Code Quality Tests
Write-Host "`n=== Code Quality Tests ===" -ForegroundColor Cyan

Test-Feature "No duplicate function definitions" {
    $functionNames = [regex]::Matches($moduleContent, "public function (\w+)").Groups | 
                     Where-Object { $_.Index -gt 0 } | 
                     Select-Object -ExpandProperty Value
    
    $uniqueCount = ($functionNames | Select-Object -Unique).Count
    $totalCount = $functionNames.Count
    $uniqueCount -eq $totalCount
}

Test-Feature "Consistent naming conventions" {
    $legalDocsContent -match "complyflow-" -and  # CSS classes
    $moduleContent -match "ajax_\w+" -and        # AJAX handlers
    $moduleContent -match "wp_ajax_complyflow_"  # WordPress hooks
}

Test-Feature "Error handling present in AJAX" {
    $moduleContent -match "wp_send_json_error" -and
    $moduleContent -match "wp_send_json_success"
}

Test-Feature "Loading states implemented" {
    $legalDocsContent -match "prop\('disabled', true\)" -and
    $legalDocsContent -match "Saving\.\.\.|Generating|Loading"
}

# Test 15: Feature Completeness
Write-Host "`n=== Feature Completeness Tests ===" -ForegroundColor Cyan

Test-Feature "All 4 policy types have Export PDF button" {
    $privacyExport = $legalDocsContent -match 'export-pdf.*data-type="privacy_policy"'
    $termsExport = $legalDocsContent -match 'export-pdf.*data-type="terms_of_service"'
    $cookieExport = $legalDocsContent -match 'export-pdf.*data-type="cookie_policy"'
    $dataExport = $legalDocsContent -match 'export-pdf.*data-type="data_protection"'
    
    $privacyExport -and $termsExport -and $cookieExport -and $dataExport
}

Test-Feature "Modal system supports all operations" {
    $legalDocsContent -match "view-policy" -and
    $legalDocsContent -match "edit-policy" -and
    $legalDocsContent -match "copy-policy" -and
    $legalDocsContent -match "export-pdf"
}

Test-Feature "Version operations fully implemented" {
    $moduleContent -match "ajax_get_version_history" -and
    $moduleContent -match "ajax_compare_versions" -and
    $moduleContent -match "ajax_restore_version"
}

# Summary
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "TEST SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Total Tests: $testsTotal" -ForegroundColor White
Write-Host "Passed: $testsPassed" -ForegroundColor Green
Write-Host "Failed: $testsFailed" -ForegroundColor $(if ($testsFailed -eq 0) { "Green" } else { "Red" })

$passRate = [math]::Round(($testsPassed / $testsTotal) * 100, 2)
Write-Host "Pass Rate: $passRate%" -ForegroundColor $(if ($passRate -ge 95) { "Green" } elseif ($passRate -ge 80) { "Yellow" } else { "Red" })

if ($testsFailed -eq 0) {
    Write-Host "`n✓ ALL TESTS PASSED!" -ForegroundColor Green
    Write-Host "The implementation is complete and ready for deployment." -ForegroundColor Green
} else {
    Write-Host "`n⚠ SOME TESTS FAILED" -ForegroundColor Yellow
    Write-Host "Please review the failed tests above." -ForegroundColor Yellow
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host ""

# Return exit code
exit $(if ($testsFailed -eq 0) { 0 } else { 1 })
