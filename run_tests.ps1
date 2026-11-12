# ==============================================================================
# SOA TH5 - Script chạy kiểm thử tự động toàn diện (PHPUnit Test Suite)
# ==============================================================================

Write-Host "=================================================================" -ForegroundColor Cyan
Write-Host "  CHAY KIEM THU TU DONG - REPORTING SERVICE (BAI THUC HANH 5)   " -ForegroundColor Green
Write-Host "=================================================================" -ForegroundColor Cyan

Set-Location $PSScriptRoot

Write-Host "`n[1/2] Kiem tra moi truong va Database SQLite..." -ForegroundColor Yellow
if (-not (Test-Path "database\reports.sqlite")) {
    Write-Host "Database chua ton tai, dang tao moi..." -ForegroundColor Yellow
    New-Item -ItemType File -Path "database\reports.sqlite" -Force | Out-Null
    php artisan migrate:fresh --seed --force
}

Write-Host "`n[2/2] Khoi chay PHPUnit (12 Test Cases, 150+ Assertions)..." -ForegroundColor Yellow
php vendor/phpunit/phpunit/phpunit --colors=always

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n>>> TAT CA TEST CASES DEU VUOT QUA THANH CONG (100% PASSED)! <<<" -ForegroundColor Green
} else {
    Write-Host "`n>>> CO LOI XAY RA TRONG QUA TRINH TEST! <<<" -ForegroundColor Red
}
