#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test Compliance History Tracking Implementation
.DESCRIPTION
    Tests the new compliance history tracking feature including database,
    repository, scheduler, and dashboard integration.
#>

Write-Host "`n╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║    Compliance History Tracking - Implementation Test     ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

$pluginPath = $PSScriptRoot
$testsPassed = 0
$testsFailed = 0

function Test-FileExists {
    param([string]$Path, [string]$Description)
    
    if (Test-Path $Path) {
        Write-Host "✓ $Description" -ForegroundColor Green
        return $true
    } else {
        Write-Host "✗ $Description" -ForegroundColor Red
        Write-Host "  Path: $Path" -ForegroundColor DarkGray
        return $false
    }
}

function Test-PHPSyntax {
    param([string]$Path, [string]$Description)
    
    try {
        $result = php -l $Path 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✓ $Description" -ForegroundColor Green
            return $true
        } else {
            Write-Host "✗ $Description" -ForegroundColor Red
            Write-Host "  $result" -ForegroundColor DarkGray
            return $false
        }
    } catch {
        Write-Host "✗ $Description" -ForegroundColor Red
        Write-Host "  Error: $_" -ForegroundColor DarkGray
        return $false
    }
}

function Test-ClassExists {
    param([string]$FilePath, [string]$ClassName, [string]$Description)
    
    if (Test-Path $FilePath) {
        $content = Get-Content $FilePath -Raw
        if ($content -match "class\s+$ClassName") {
            Write-Host "✓ $Description" -ForegroundColor Green
            return $true
        } else {
            Write-Host "✗ $Description" -ForegroundColor Red
            return $false
        }
    } else {
        Write-Host "✗ $Description - File not found" -ForegroundColor Red
        return $false
    }
}

function Test-MethodExists {
    param([string]$FilePath, [string]$MethodName, [string]$Description)
    
    if (Test-Path $FilePath) {
        $content = Get-Content $FilePath -Raw
        if ($content -match "function\s+$MethodName") {
            Write-Host "✓ $Description" -ForegroundColor Green
            return $true
        } else {
            Write-Host "✗ $Description" -ForegroundColor Red
            return $false
        }
    } else {
        Write-Host "✗ $Description - File not found" -ForegroundColor Red
        return $false
    }
}

Write-Host "Testing File Structure..." -ForegroundColor Yellow
Write-Host ""

# Test new files
if (Test-FileExists "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "ComplianceHistoryRepository.php created") { $testsPassed++ } else { $testsFailed++ }
if (Test-FileExists "$pluginPath\includes\Core\ComplianceHistoryScheduler.php" "ComplianceHistoryScheduler.php created") { $testsPassed++ } else { $testsFailed++ }

Write-Host ""
Write-Host "Testing PHP Syntax..." -ForegroundColor Yellow
Write-Host ""

# Test PHP syntax
if (Test-PHPSyntax "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "ComplianceHistoryRepository.php syntax valid") { $testsPassed++ } else { $testsFailed++ }
if (Test-PHPSyntax "$pluginPath\includes\Core\ComplianceHistoryScheduler.php" "ComplianceHistoryScheduler.php syntax valid") { $testsPassed++ } else { $testsFailed++ }
if (Test-PHPSyntax "$pluginPath\includes\Admin\Settings.php" "Settings.php syntax valid (modified)") { $testsPassed++ } else { $testsFailed++ }
if (Test-PHPSyntax "$pluginPath\includes\Modules\Dashboard\DashboardWidgets.php" "DashboardWidgets.php syntax valid (modified)") { $testsPassed++ } else { $testsFailed++ }
if (Test-PHPSyntax "$pluginPath\includes\Core\Plugin.php" "Plugin.php syntax valid (modified)") { $testsPassed++ } else { $testsFailed++ }
if (Test-PHPSyntax "$pluginPath\includes\Core\Activator.php" "Activator.php syntax valid (modified)") { $testsPassed++ } else { $testsFailed++ }

Write-Host ""
Write-Host "Testing Class Definitions..." -ForegroundColor Yellow
Write-Host ""

# Test class existence
if (Test-ClassExists "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "ComplianceHistoryRepository" "ComplianceHistoryRepository class defined") { $testsPassed++ } else { $testsFailed++ }
if (Test-ClassExists "$pluginPath\includes\Core\ComplianceHistoryScheduler.php" "ComplianceHistoryScheduler" "ComplianceHistoryScheduler class defined") { $testsPassed++ } else { $testsFailed++ }

Write-Host ""
Write-Host "Testing Repository Methods..." -ForegroundColor Yellow
Write-Host ""

# Test repository methods
if (Test-MethodExists "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "save_snapshot" "save_snapshot() method exists") { $testsPassed++ } else { $testsFailed++ }
if (Test-MethodExists "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "get_history" "get_history() method exists") { $testsPassed++ } else { $testsFailed++ }
if (Test-MethodExists "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "get_latest" "get_latest() method exists") { $testsPassed++ } else { $testsFailed++ }
if (Test-MethodExists "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "cleanup_old_records" "cleanup_old_records() method exists") { $testsPassed++ } else { $testsFailed++ }
if (Test-MethodExists "$pluginPath\includes\Database\ComplianceHistoryRepository.php" "table_exists" "table_exists() method exists") { $testsPassed++ } else { $testsFailed++ }

Write-Host ""
Write-Host "Testing Scheduler Methods..." -ForegroundColor Yellow
Write-Host ""

# Test scheduler methods
if (Test-MethodExists "$pluginPath\includes\Core\ComplianceHistoryScheduler.php" "take_snapshot" "take_snapshot() method exists") { $testsPassed++ } else { $testsFailed++ }
if (Test-MethodExists "$pluginPath\includes\Core\ComplianceHistoryScheduler.php" "add_custom_schedules" "add_custom_schedules() method exists") { $testsPassed++ } else { $testsFailed++ }
if (Test-MethodExists "$pluginPath\includes\Core\ComplianceHistoryScheduler.php" "handle_schedule_change" "handle_schedule_change() method exists") { $testsPassed++ } else { $testsFailed++ }
if (Test-MethodExists "$pluginPath\includes\Core\ComplianceHistoryScheduler.php" "force_snapshot" "force_snapshot() method exists") { $testsPassed++ } else { $testsFailed++ }

Write-Host ""
Write-Host "Testing Integration Points..." -ForegroundColor Yellow
Write-Host ""

# Test Settings integration
$settingsContent = Get-Content "$pluginPath\includes\Admin\Settings.php" -Raw
if ($settingsContent -match "compliance_history_schedule") {
    Write-Host "✓ compliance_history_schedule setting added" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ compliance_history_schedule setting not found" -ForegroundColor Red
    $testsFailed++
}

# Test DashboardWidgets integration
$widgetsContent = Get-Content "$pluginPath\includes\Modules\Dashboard\DashboardWidgets.php" -Raw
if ($widgetsContent -match "get_real_compliance_trends") {
    Write-Host "✓ get_real_compliance_trends() method added" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ get_real_compliance_trends() method not found" -ForegroundColor Red
    $testsFailed++
}

if ($widgetsContent -match "ComplianceHistoryRepository") {
    Write-Host "✓ DashboardWidgets uses ComplianceHistoryRepository" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ ComplianceHistoryRepository not referenced in DashboardWidgets" -ForegroundColor Red
    $testsFailed++
}

# Test Plugin.php integration
$pluginContent = Get-Content "$pluginPath\includes\Core\Plugin.php" -Raw
if ($pluginContent -match "init_compliance_scheduler") {
    Write-Host "✓ init_compliance_scheduler() method added to Plugin" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ init_compliance_scheduler() not found in Plugin" -ForegroundColor Red
    $testsFailed++
}

# Test Activator integration
$activatorContent = Get-Content "$pluginPath\includes\Core\Activator.php" -Raw
if ($activatorContent -match "complyflow_compliance_history") {
    Write-Host "✓ compliance_history table added to Activator" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ compliance_history table not found in Activator" -ForegroundColor Red
    $testsFailed++
}

if ($activatorContent -match "complyflow_compliance_snapshot") {
    Write-Host "✓ compliance_snapshot cron event registered" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ compliance_snapshot cron event not registered" -ForegroundColor Red
    $testsFailed++
}

if ($activatorContent -match "take_initial_snapshot") {
    Write-Host "✓ take_initial_snapshot() method added" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ take_initial_snapshot() method not found" -ForegroundColor Red
    $testsFailed++
}

Write-Host ""
Write-Host "Testing Database Schema..." -ForegroundColor Yellow
Write-Host ""

# Check for proper SQL syntax in table definition
if ($activatorContent -match "compliance_score INT UNSIGNED NOT NULL") {
    Write-Host "✓ compliance_score column defined correctly" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ compliance_score column definition issue" -ForegroundColor Red
    $testsFailed++
}

if ($activatorContent -match "module_scores TEXT NOT NULL") {
    Write-Host "✓ module_scores column defined correctly" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ module_scores column definition issue" -ForegroundColor Red
    $testsFailed++
}

if ($activatorContent -match "recorded_at DATETIME NOT NULL") {
    Write-Host "✓ recorded_at column defined correctly" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ recorded_at column definition issue" -ForegroundColor Red
    $testsFailed++
}

if ($activatorContent -match "INDEX idx_recorded") {
    Write-Host "✓ recorded_at index defined" -ForegroundColor Green
    $testsPassed++
} else {
    Write-Host "✗ recorded_at index not found" -ForegroundColor Red
    $testsFailed++
}

# Summary
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                      Test Summary                         ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

$totalTests = $testsPassed + $testsFailed
$passRate = [math]::Round(($testsPassed / $totalTests) * 100, 1)

Write-Host "Total Tests:  " -NoNewline
Write-Host "$totalTests" -ForegroundColor White
Write-Host "Passed:       " -NoNewline
Write-Host "$testsPassed" -ForegroundColor Green
Write-Host "Failed:       " -NoNewline
Write-Host "$testsFailed" -ForegroundColor $(if ($testsFailed -eq 0) { "Green" } else { "Red" })
Write-Host "Pass Rate:    " -NoNewline
Write-Host "$passRate%" -ForegroundColor $(if ($passRate -ge 95) { "Green" } elseif ($passRate -ge 80) { "Yellow" } else { "Red" })

Write-Host ""

if ($testsFailed -eq 0) {
    Write-Host "🎉 All tests passed! Implementation is complete." -ForegroundColor Green
    Write-Host ""
    Write-Host "Next Steps:" -ForegroundColor Yellow
    Write-Host "  1. Reactivate the plugin to create the new database table" -ForegroundColor White
    Write-Host "  2. Go to Settings > Accessibility to configure tracking schedule" -ForegroundColor White
    Write-Host "  3. View Dashboard to see compliance trend (will accumulate over time)" -ForegroundColor White
    Write-Host "  4. Wait for automated snapshots or manually trigger via WP-Cron" -ForegroundColor White
    exit 0
} else {
    Write-Host "⚠️  Some tests failed. Please review the errors above." -ForegroundColor Red
    exit 1
}
