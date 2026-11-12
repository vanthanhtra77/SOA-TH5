# ==============================================================================
# SOA TH5 - Script khởi chạy toàn bộ 4 Microservices trong Hệ Sinh Thái SOA
# Service 1 (Auth):      Port 8000 | CSDL: th2/database/database.sqlite
# Service 2 (Product):   Port 8001 | CSDL: TH3/database/products.sqlite
# Service 3 (Order):     Port 8002 | CSDL: TH4/database/orders.sqlite
# Service 4 (Reporting): Port 8003 | CSDL: TH5/database/reports.sqlite
# ==============================================================================

Write-Host "=================================================================" -ForegroundColor Cyan
Write-Host "  KHOI CHAY HE SINH THAI SOA - 4 SERVICES (BAI THUC HANH 5)" -ForegroundColor Green
Write-Host "=================================================================" -ForegroundColor Cyan

function Check-Port ($port) {
    $conn = Test-NetConnection -ComputerName 127.0.0.1 -Port $port -WarningAction SilentlyContinue
    return $conn.TcpTestSucceeded
}

# 1. Service 1 - Auth Service (Port 8000)
if (Check-Port 8000) {
    Write-Host "[OK] Port 8000 (Auth Service) da dang chay." -ForegroundColor Green
} else {
    Write-Host "[>>] Dang khoi dong Service 1 (Auth Service - Port 8000)..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd 'D:\SOA\th2\SOATh2-main\SOATh2-main'; Write-Host '=== AUTH SERVICE (PORT 8000) ===' -ForegroundColor Green; php -S 127.0.0.1:8000 -t public"
    Start-Sleep -Seconds 2
}

# 2. Service 2 - Product Service (Port 8001)
if (Check-Port 8001) {
    Write-Host "[OK] Port 8001 (Product Service) da dang chay." -ForegroundColor Green
} else {
    Write-Host "[>>] Dang khoi dong Service 2 (Product Service - Port 8001)..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd 'D:\SOA\TH3'; Write-Host '=== PRODUCT SERVICE (PORT 8001) ===' -ForegroundColor Cyan; php -S 127.0.0.1:8001 -t public"
    Start-Sleep -Seconds 2
}

# 3. Service 3 - Order Service (Port 8002)
if (Check-Port 8002) {
    Write-Host "[OK] Port 8002 (Order Service) da dang chay." -ForegroundColor Green
} else {
    Write-Host "[>>] Dang khoi dong Service 3 (Order Service - Port 8002)..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd 'D:\SOA\TH4'; Write-Host '=== ORDER SERVICE (PORT 8002) ===' -ForegroundColor Magenta; php -S 127.0.0.1:8002 -t public"
    Start-Sleep -Seconds 2
}

# 4. Service 4 - Reporting Service (Port 8003)
if (Check-Port 8003) {
    Write-Host "[OK] Port 8003 (Reporting Service) da dang chay." -ForegroundColor Green
} else {
    Write-Host "[>>] Dang khoi dong Service 4 (Reporting Service - Port 8003)..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd 'D:\SOA\TH5'; Write-Host '=== REPORTING SERVICE (PORT 8003) ===' -ForegroundColor Blue; php -S 127.0.0.1:8003 -t public"
    Start-Sleep -Seconds 2
}

Write-Host "`n--- KIEM TRA KET NOI TOAN HE THONG ---" -ForegroundColor Cyan
Start-Sleep -Seconds 2

foreach ($p in @(8000, 8001, 8002, 8003)) {
    if (Check-Port $p) {
        Write-Host "  -> Port $p: [HOAT DONG]" -ForegroundColor Green
    } else {
        Write-Host "  -> Port $p: [CHUA SAN SANG]" -ForegroundColor Red
    }
}

Write-Host "`n=== HE THONG SOA DA SAN SANG SU DUNG ===" -ForegroundColor Green
Write-Host "Web UI Dashboard:  http://127.0.0.1:8003" -ForegroundColor Yellow
Write-Host "Tai khoan mau:     student / 123456" -ForegroundColor Yellow
Write-Host "API Health:        http://127.0.0.1:8003/health" -ForegroundColor Yellow
Write-Host "Postman file:      D:\SOA\TH5\postman\Reporting-Service.postman_collection.json" -ForegroundColor Gray
