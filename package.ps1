# Simple CodeCanyon Package Builder
# Creates a clean ZIP file for CodeCanyon submission

$pluginName = "ComplyFlow"
$version = "5.1.1"
$pluginDir = Get-Location
$buildDir = Join-Path $pluginDir "build"
$tempDir = Join-Path $buildDir "temp"
$packageDir = Join-Path $tempDir "$pluginName-$version"
$zipFile = Join-Path $buildDir "$pluginName-$version.zip"

Write-Host "`n=== ComplyFlow Package Builder ===" -ForegroundColor Cyan
Write-Host "Version: $version`n"

# Clean build directory
if (Test-Path $buildDir) {
    Write-Host "Cleaning build directory..."
    Remove-Item $buildDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir -Force | Out-Null

# Exclusion patterns
$exclude = @(
    'node_modules', 'build', 'tools', 'var', 'documentation',
    '*.log', '*.md', '.git*', '.dist*', 'test-*.php', 'test-*.ps1',
    'package*.json', 'composer.lock', 'vite.config.js', 'tailwind.config.js',
    'postcss.config.js', 'phpcs.xml.dist', 'phpstan.neon', '.DS_Store',
    'VersionManager.php', 'check-db.php', 'debug-*.php', 'migrate-*.php',
    'insert-sample-data.php', 'security-audit.php', 'test-*.php',
    'assets/src', 'docs', 'plan'
)

# Copy files
Write-Host "Copying plugin files..."
$copied = 0
Get-ChildItem $pluginDir -Recurse -File | ForEach-Object {
    $relPath = $_.FullName.Substring($pluginDir.Path.Length + 1)
    
    # Check exclusions
    $skip = $false
    foreach ($pattern in $exclude) {
        if ($relPath -like "*$pattern*") {
            $skip = $true
            break
        }
    }
    
    if (-not $skip) {
        $targetPath = Join-Path $packageDir $relPath
        $targetDir = Split-Path $targetPath -Parent
        
        if (-not (Test-Path $targetDir)) {
            New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
        }
        
        Copy-Item $_.FullName $targetPath -Force
        $copied++
        
        if ($copied % 100 -eq 0) {
            Write-Host "  Copied $copied files..." -NoNewline -ForegroundColor Gray
            Write-Host "`r" -NoNewline
        }
    }
}
Write-Host "  Copied $copied files" -ForegroundColor Green

# Create ZIP
Write-Host "Creating ZIP archive..."
if (Test-Path $zipFile) {
    Remove-Item $zipFile -Force
}

Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($packageDir, $zipFile, 'Optimal', $false)

# Show results
$size = [math]::Round((Get-Item $zipFile).Length / 1MB, 2)
Write-Host "`n=== Package Complete ===" -ForegroundColor Green
Write-Host "File: $zipFile"
Write-Host "Size: $size MB"

if ($size -gt 20) {
    Write-Host "`nWARNING: Package exceeds CodeCanyon 20MB limit!" -ForegroundColor Red
    Write-Host "Consider removing additional files or assets.`n"
} else {
    Write-Host "`n✓ Package is within CodeCanyon size limits`n" -ForegroundColor Green
}
