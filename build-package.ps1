# ComplyFlow CodeCanyon Package Builder
# Builds a clean, production-ready ZIP package for CodeCanyon submission

param(
    [string]$Version = "5.2.0",
    [string]$OutputDir = ".\build",
    [switch]$IncludeVendor = $true
)

Write-Host "==================================" -ForegroundColor Cyan
Write-Host " ComplyFlow Package Builder v1.0" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

$PluginDir = Get-Location
$PackageName = "ComplyFlow-$Version"
$TempDir = Join-Path $OutputDir "temp\$PackageName"
$OutputZip = Join-Path $OutputDir "$PackageName.zip"

# Create output directory if it doesn't exist
if (!(Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir | Out-Null
}

# Clean up any existing temp directory
if (Test-Path $TempDir) {
    Write-Host "Cleaning up existing temp directory..." -ForegroundColor Yellow
    Remove-Item -Path $TempDir -Recurse -Force
}

# Create temp directory
New-Item -ItemType Directory -Path $TempDir -Force | Out-Null

Write-Host "Building package: $PackageName" -ForegroundColor Green
Write-Host ""

# Define files/folders to EXCLUDE (from .distignore)
$ExcludePatterns = @(
    'node_modules',
    '.git',
    '.gitignore',
    '.eslintrc.json',
    '.prettierrc.json',
    '*_COMPLETE.md',
    '*_IMPLEMENTATION*.md',
    '*_FIX.md',
    '*_ENHANCEMENT*.md',
    '*_TESTING.md',
    '*_PLAN.md',
    '*_AUDIT*.md',
    'DEVELOPMENT_PLAN.md',
    'STRATEGIC_POLICY_ENHANCEMENT_PLAN.md',
    'GLOBAL_COMPLIANCE_AUDIT.md',
    'TESTING_COMPLETE.md',
    'CODE_TESTING_VALIDATION_REPORT.md',
    'check-db.php',
    'debug-questionnaire.php',
    'insert-sample-data.php',
    'migrate-cookie-table.php',
    'security-audit.php',
    'test-*.php',
    'test-*.ps1',
    'phpcs.xml.dist',
    'phpstan.neon',
    'composer.lock',
    'package-lock.json',
    'var',
    'tools',
    'plan',
    'assets\src',
    'documentation\SCREENSHOT-GUIDE.md',
    'documentation\CODECANYON-LISTING.md',
    '.distignore',
    'build-package.ps1',
    'CODECANYON_SUBMISSION_PLAN.md'
)

Write-Host "Step 1: Copying core files..." -ForegroundColor Cyan

# Copy files with exclusions
Get-ChildItem -Path $PluginDir -Recurse | ForEach-Object {
    $RelativePath = $_.FullName.Substring($PluginDir.Path.Length + 1)
    
    # Check if file/folder should be excluded
    $ShouldExclude = $false
    foreach ($Pattern in $ExcludePatterns) {
        if ($RelativePath -like $Pattern) {
            $ShouldExclude = $true
            break
        }
    }
    
    # Additional exclusions for specific folders
    if ($RelativePath -match '^build\\' -or 
        $RelativePath -match '^\.vscode\\' -or
        $RelativePath -match '^node_modules\\') {
        $ShouldExclude = $true
    }
    
    if (!$ShouldExclude) {
        $TargetPath = Join-Path $TempDir $RelativePath
        
        if ($_.PSIsContainer) {
            if (!(Test-Path $TargetPath)) {
                New-Item -ItemType Directory -Path $TargetPath -Force | Out-Null
            }
        } else {
            $TargetDir = Split-Path $TargetPath -Parent
            if (!(Test-Path $TargetDir)) {
                New-Item -ItemType Directory -Path $TargetDir -Force | Out-Null
            }
            Copy-Item -Path $_.FullName -Destination $TargetPath -Force
        }
    }
}

Write-Host "  ✓ Core files copied" -ForegroundColor Green

# Step 2: Handle vendor dependencies
if ($IncludeVendor) {
    Write-Host ""
    Write-Host "Step 2: Rebuilding vendor dependencies (production only)..." -ForegroundColor Cyan
    
    $VendorTempDir = Join-Path $TempDir "vendor"
    if (Test-Path $VendorTempDir) {
        Remove-Item -Path $VendorTempDir -Recurse -Force
    }
    
    Push-Location $TempDir
    try {
        # Run composer install with production flag
        if (Get-Command composer -ErrorAction SilentlyContinue) {
            composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | Out-Null
            Write-Host "  ✓ Vendor dependencies rebuilt (production mode)" -ForegroundColor Green
        } else {
            Write-Host "  ⚠ Composer not found - copying existing vendor folder" -ForegroundColor Yellow
            Copy-Item -Path (Join-Path $PluginDir "vendor") -Destination $VendorTempDir -Recurse -Force
        }
    } finally {
        Pop-Location
    }
} else {
    Write-Host ""
    Write-Host "Step 2: Skipping vendor dependencies" -ForegroundColor Yellow
}

# Step 3: Remove any remaining unwanted files
Write-Host ""
Write-Host "Step 3: Cleaning up unwanted files..." -ForegroundColor Cyan

$FilesToRemove = @(
    '.DS_Store',
    'Thumbs.db',
    '*.log',
    '*.tmp',
    '.gitkeep'
)

foreach ($Pattern in $FilesToRemove) {
    Get-ChildItem -Path $TempDir -Filter $Pattern -Recurse -Force | Remove-Item -Force
}

Write-Host "  ✓ Cleanup complete" -ForegroundColor Green

# Step 4: Create ZIP file
Write-Host ""
Write-Host "Step 4: Creating ZIP archive..." -ForegroundColor Cyan

if (Test-Path $OutputZip) {
    Remove-Item -Path $OutputZip -Force
}

# Use .NET compression (faster than Compress-Archive for large files)
Add-Type -Assembly System.IO.Compression.FileSystem
$CompressionLevel = [System.IO.Compression.CompressionLevel]::Optimal
[System.IO.Compression.ZipFile]::CreateFromDirectory($TempDir, $OutputZip, $CompressionLevel, $false)

Write-Host "  ✓ ZIP archive created" -ForegroundColor Green

# Step 5: Calculate statistics
Write-Host ""
Write-Host "Step 5: Package statistics..." -ForegroundColor Cyan

$FileCount = (Get-ChildItem -Path $TempDir -Recurse -File).Count
$ZipSize = (Get-Item $OutputZip).Length
$ZipSizeMB = [math]::Round($ZipSize / 1MB, 2)

Write-Host "  Files included: $FileCount" -ForegroundColor White
Write-Host "  Package size: $ZipSizeMB MB" -ForegroundColor White
Write-Host "  Output location: $OutputZip" -ForegroundColor White

# Cleanup temp directory
Write-Host ""
Write-Host "Cleaning up temporary files..." -ForegroundColor Yellow
Remove-Item -Path (Join-Path $OutputDir "temp") -Recurse -Force

# Final summary
Write-Host ""
Write-Host "==================================" -ForegroundColor Green
Write-Host " ✓ Package Build Complete!" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""
Write-Host "Package: $OutputZip" -ForegroundColor Cyan
Write-Host "Size: $ZipSizeMB MB" -ForegroundColor Cyan
Write-Host "Files: $FileCount" -ForegroundColor Cyan
Write-Host ""

if ($ZipSizeMB -gt 20) {
    Write-Host "⚠ WARNING: Package size exceeds 20MB. Consider optimizing assets." -ForegroundColor Yellow
} else {
    Write-Host "✓ Package size is within CodeCanyon limits." -ForegroundColor Green
}

Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Extract and test the package on a fresh WordPress install" -ForegroundColor White
Write-Host "2. Verify all features work correctly" -ForegroundColor White
Write-Host "3. Check for any errors with WP_DEBUG enabled" -ForegroundColor White
Write-Host "4. Submit to CodeCanyon when ready" -ForegroundColor White
Write-Host ""
