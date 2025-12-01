#!/usr/bin/env pwsh
# ShahiComplyFlow - Legal Policy Generation System Test Script
# Run this script to verify all features are working correctly

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  ShahiComplyFlow - System Test Suite" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

$testResults = @()
$passCount = 0
$failCount = 0

function Test-Feature {
    param(
        [string]$Name,
        [string]$Description,
        [scriptblock]$Test
    )
    
    Write-Host "Testing: $Name" -ForegroundColor Yellow
    Write-Host "  $Description" -ForegroundColor Gray
    
    try {
        $result = & $Test
        if ($result) {
            Write-Host "  ✓ PASS" -ForegroundColor Green
            $script:passCount++
            return @{ Name = $Name; Status = "PASS"; Message = "" }
        } else {
            Write-Host "  ✗ FAIL" -ForegroundColor Red
            $script:failCount++
            return @{ Name = $Name; Status = "FAIL"; Message = "Test returned false" }
        }
    } catch {
        Write-Host "  ✗ FAIL: $($_.Exception.Message)" -ForegroundColor Red
        $script:failCount++
        return @{ Name = $Name; Status = "FAIL"; Message = $_.Exception.Message }
    }
    Write-Host ""
}

# Define base path
$pluginPath = "c:\xampp\htdocs\shahitest\wp-content\plugins\ShahiComplyFlow"

Write-Host "Plugin Path: $pluginPath`n" -ForegroundColor Cyan

# Test 1: Check if main plugin file exists
$testResults += Test-Feature `
    -Name "Main Plugin File" `
    -Description "Verify complyflow.php exists" `
    -Test {
        Test-Path "$pluginPath\complyflow.php"
    }

# Test 2: Check DocumentsModule exists
$testResults += Test-Feature `
    -Name "Documents Module" `
    -Description "Verify DocumentsModule.php exists" `
    -Test {
        Test-Path "$pluginPath\includes\Modules\Documents\DocumentsModule.php"
    }

# Test 3: Check all generator classes exist
$testResults += Test-Feature `
    -Name "Generator Classes" `
    -Description "Verify all 4 generator classes exist" `
    -Test {
        $generators = @(
            "PrivacyPolicyGenerator.php",
            "TermsOfServiceGenerator.php",
            "CookiePolicyGenerator.php",
            "DataProtectionPolicyGenerator.php"
        )
        
        $allExist = $true
        foreach ($gen in $generators) {
            if (-not (Test-Path "$pluginPath\includes\Modules\Documents\$gen")) {
                $allExist = $false
                break
            }
        }
        $allExist
    }

# Test 4: Check all enhanced templates exist
$testResults += Test-Feature `
    -Name "Enhanced Templates" `
    -Description "Verify all 4 policy templates exist" `
    -Test {
        $templates = @(
            "privacy-policy-template.php",
            "terms-of-service-template.php",
            "cookie-policy-template.php",
            "data-protection-policy-template.php"
        )
        
        $allExist = $true
        foreach ($template in $templates) {
            if (-not (Test-Path "$pluginPath\templates\policies\$template")) {
                $allExist = $false
                break
            }
        }
        $allExist
    }

# Test 5: Check if snippets directory exists
$testResults += Test-Feature `
    -Name "Snippets Directory" `
    -Description "Verify snippets folder exists with files" `
    -Test {
        $snippetsPath = "$pluginPath\templates\policies\snippets"
        if (Test-Path $snippetsPath) {
            $snippetCount = (Get-ChildItem $snippetsPath -Filter "*.php").Count
            Write-Host "  Found $snippetCount snippet files" -ForegroundColor Gray
            $snippetCount -gt 50
        } else {
            $false
        }
    }

# Test 6: Check legal-documents.php for modal handlers
$testResults += Test-Feature `
    -Name "Modal Handlers" `
    -Description "Verify View/Edit/Copy handlers in legal-documents.php" `
    -Test {
        $legalDocsPath = "$pluginPath\includes\Admin\views\legal-documents.php"
        if (Test-Path $legalDocsPath) {
            $content = Get-Content $legalDocsPath -Raw
            $hasView = $content -match "\.on\('click', '\.view-policy'"
            $hasEdit = $content -match "\.on\('click', '\.edit-policy'"
            $hasCopy = $content -match "\.on\('click', '\.copy-policy'"
            
            $hasView -and $hasEdit -and $hasCopy
        } else {
            $false
        }
    }

# Test 7: Check for TOC in templates
$testResults += Test-Feature `
    -Name "Table of Contents" `
    -Description "Verify TOC exists in all templates" `
    -Test {
        $templates = @(
            "privacy-policy-template.php",
            "terms-of-service-template.php",
            "cookie-policy-template.php",
            "data-protection-policy-template.php"
        )
        
        $allHaveTOC = $true
        foreach ($template in $templates) {
            $content = Get-Content "$pluginPath\templates\policies\$template" -Raw
            if (-not ($content -match "toc-container")) {
                $allHaveTOC = $false
                break
            }
        }
        $allHaveTOC
    }

# Test 8: Check for gradient headers in templates
$testResults += Test-Feature `
    -Name "Gradient Headers" `
    -Description "Verify gradient styling in all templates" `
    -Test {
        $templates = @(
            "privacy-policy-template.php",
            "terms-of-service-template.php",
            "cookie-policy-template.php",
            "data-protection-policy-template.php"
        )
        
        $allHaveGradient = $true
        foreach ($template in $templates) {
            $content = Get-Content "$pluginPath\templates\policies\$template" -Raw
            if (-not ($content -match "linear-gradient")) {
                $allHaveGradient = $false
                break
            }
        }
        $allHaveGradient
    }

# Test 9: Check for responsive design in templates
$testResults += Test-Feature `
    -Name "Responsive Design" `
    -Description "Verify media queries in all templates" `
    -Test {
        $templates = @(
            "privacy-policy-template.php",
            "terms-of-service-template.php",
            "cookie-policy-template.php",
            "data-protection-policy-template.php"
        )
        
        $allResponsive = $true
        foreach ($template in $templates) {
            $content = Get-Content "$pluginPath\templates\policies\$template" -Raw
            if (-not ($content -match "@media")) {
                $allResponsive = $false
                break
            }
        }
        $allResponsive
    }

# Test 10: Check AJAX nonce consistency
$testResults += Test-Feature `
    -Name "AJAX Nonce Consistency" `
    -Description "Verify all handlers use correct nonce" `
    -Test {
        $legalDocsPath = "$pluginPath\includes\Admin\views\legal-documents.php"
        if (Test-Path $legalDocsPath) {
            $content = Get-Content $legalDocsPath -Raw
            
            # Check all nonce uses are 'complyflow_generate_policy_nonce'
            $wrongNonce = $content -match "complyflow_policy_nonce(?!_)" 
            
            -not $wrongNonce
        } else {
            $false
        }
    }

# Print Summary
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "           Test Summary" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Total Tests: $($passCount + $failCount)" -ForegroundColor White
Write-Host "Passed: $passCount" -ForegroundColor Green
Write-Host "Failed: $failCount" -ForegroundColor $(if ($failCount -gt 0) { "Red" } else { "Green" })

$percentage = [math]::Round(($passCount / ($passCount + $failCount)) * 100, 2)
Write-Host "Success Rate: $percentage%`n" -ForegroundColor $(if ($percentage -eq 100) { "Green" } else { "Yellow" })

if ($failCount -gt 0) {
    Write-Host "Failed Tests:" -ForegroundColor Red
    foreach ($result in $testResults | Where-Object { $_.Status -eq "FAIL" }) {
        Write-Host "  - $($result.Name)" -ForegroundColor Red
        if ($result.Message) {
            Write-Host "    $($result.Message)" -ForegroundColor Gray
        }
    }
    Write-Host ""
}

# Final Status
if ($failCount -eq 0) {
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  ✓ ALL TESTS PASSED!" -ForegroundColor Green
    Write-Host "  System is ready for production use." -ForegroundColor Green
    Write-Host "========================================`n" -ForegroundColor Green
    exit 0
} else {
    Write-Host "========================================" -ForegroundColor Red
    Write-Host "  ✗ SOME TESTS FAILED" -ForegroundColor Red
    Write-Host "  Please review and fix issues above." -ForegroundColor Red
    Write-Host "========================================`n" -ForegroundColor Red
    exit 1
}
