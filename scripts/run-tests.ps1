# PowerShell helper script to locate PHP/composer and run tests
# Compatible with PowerShell 5.1 and later

param(
    [switch]$Help,
    [switch]$PhpUnitOnly,
    [switch]$LightweightOnly
)

if ($Help) {
    Write-Host "Usage: scripts/run-tests.ps1 [options]"
    Write-Host "Options:"
    Write-Host "  -Help              Show this help message"
    Write-Host "  -PhpUnitOnly       Run only PHPUnit tests (requires composer install)"
    Write-Host "  -LightweightOnly   Run only the lightweight test runner"
    Write-Host ""
    Write-Host "By default, runs both lightweight tests and PHPUnit (if available)"
    exit 0
}

function Find-Executable($name) {
    $paths = @()
    
    # Add common Windows PHP/Composer installation paths
    $paths += "${env:ProgramFiles}\PHP\php.exe"
    $paths += "${env:ProgramFiles(x86)}\PHP\php.exe"
    $paths += "${env:LOCALAPPDATA}\ComposerSetup\bin\composer.bat"
    $paths += "${env:APPDATA}\Composer\vendor\bin\composer.bat"
    $paths += "$env:USERPROFILE\composer.phar"
    
    # Check if it's in PATH
    try {
        $pathResult = Get-Command $name -ErrorAction SilentlyContinue
        if ($pathResult) {
            return $pathResult.Source
        }
    } catch {
        # Continue searching
    }
    
    # Check common installation paths
    foreach ($path in $paths) {
        if (Test-Path $path) {
            return $path
        }
    }
    
    return $null
}

function Run-LightweightTests($phpPath) {
    Write-Host "Running lightweight test runner..." -ForegroundColor Green
    
    $testFile = "tests/run_pricing_tests.php"
    if (-not (Test-Path $testFile)) {
        Write-Host "Error: Test file $testFile not found" -ForegroundColor Red
        return $false
    }
    
    try {
        $result = & $phpPath $testFile
        $exitCode = $LASTEXITCODE
        
        if ($exitCode -eq 0) {
            Write-Host "Lightweight tests: PASSED" -ForegroundColor Green
            return $true
        } else {
            Write-Host "Lightweight tests: FAILED (exit code: $exitCode)" -ForegroundColor Red
            return $false
        }
    } catch {
        Write-Host "Error running lightweight tests: $_" -ForegroundColor Red
        return $false
    }
}

function Run-PhpUnitTests($phpPath) {
    Write-Host "Running PHPUnit tests..." -ForegroundColor Green
    
    # Check if vendor directory exists
    if (-not (Test-Path "vendor")) {
        Write-Host "vendor directory not found. Running composer install..." -ForegroundColor Yellow
        
        $composerPath = Find-Executable "composer"
        if (-not $composerPath) {
            Write-Host "Composer not found. Please install composer and run 'composer install'" -ForegroundColor Red
            return $false
        }
        
        try {
            & $composerPath install --no-interaction --prefer-dist
            if ($LASTEXITCODE -ne 0) {
                Write-Host "Composer install failed" -ForegroundColor Red
                return $false
            }
        } catch {
            Write-Host "Error running composer install: $_" -ForegroundColor Red
            return $false
        }
    }
    
    # Check if PHPUnit exists
    $phpunitPath = "vendor/bin/phpunit"
    if (Test-Path "vendor\bin\phpunit.bat") {
        $phpunitPath = "vendor\bin\phpunit.bat"
    } elseif (-not (Test-Path $phpunitPath)) {
        Write-Host "PHPUnit not found in vendor/bin/" -ForegroundColor Red
        return $false
    }
    
    try {
        if ($phpunitPath.EndsWith(".bat")) {
            & cmd /c $phpunitPath -c phpunit.xml
        } else {
            & $phpPath $phpunitPath -c phpunit.xml
        }
        
        $exitCode = $LASTEXITCODE
        if ($exitCode -eq 0) {
            Write-Host "PHPUnit tests: PASSED" -ForegroundColor Green
            return $true
        } else {
            Write-Host "PHPUnit tests: FAILED (exit code: $exitCode)" -ForegroundColor Red
            return $false
        }
    } catch {
        Write-Host "Error running PHPUnit: $_" -ForegroundColor Red
        return $false
    }
}

# Main script execution
Write-Host "WPCAM Test Runner for Windows" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan
Write-Host ""

# Find PHP
$phpPath = Find-Executable "php"
if (-not $phpPath) {
    Write-Host "PHP not found. Please install PHP and add it to your PATH, or install it in a standard location." -ForegroundColor Red
    Write-Host "Common installation paths checked:" -ForegroundColor Yellow
    Write-Host "  - ${env:ProgramFiles}\PHP\php.exe" -ForegroundColor Yellow
    Write-Host "  - ${env:ProgramFiles(x86)}\PHP\php.exe" -ForegroundColor Yellow
    exit 1
}

Write-Host "Found PHP: $phpPath" -ForegroundColor Green
Write-Host ""

$allPassed = $true

# Run tests based on parameters
if ($LightweightOnly) {
    $allPassed = Run-LightweightTests $phpPath
} elseif ($PhpUnitOnly) {
    $allPassed = Run-PhpUnitTests $phpPath
} else {
    # Run both by default
    $lightweightPassed = Run-LightweightTests $phpPath
    Write-Host ""
    $phpunitPassed = Run-PhpUnitTests $phpPath
    
    $allPassed = $lightweightPassed -and $phpunitPassed
}

Write-Host ""
if ($allPassed) {
    Write-Host "All tests completed successfully!" -ForegroundColor Green
    exit 0
} else {
    Write-Host "Some tests failed. Please check the output above." -ForegroundColor Red
    exit 1
}