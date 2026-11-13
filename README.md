# SOA TH5 - Dịch Vụ Báo Cáo & Thống Kê (Reporting Service)

[![Tests](https://img.shields.io/badge/PHPUnit-15%20Passed%2C%20139%20Assertions-brightgreen)](tests/ReportTest.php)
[![Architecture](https://img.shields.io/badge/Architecture-SOA%20Aggregator-blue)]()
[![Framework](https://img.shields.io/badge/Framework-Lumen%2010-red)]()
[![PHP](https://img.shields.io/badge/PHP-8.4-purple)]()

Dự án **Bài Thực Hành Số 5** - Môn Kiến Trúc Hướng Dịch Vụ (Service-Oriented Architecture - SOA).
Dịch vụ hoạt động độc lập trên cổng **8003** với cơ sở dữ liệu riêng biệt `database/reports.sqlite` gồm 2 bảng chuẩn theo Phụ lục đề bài: `orders_reports` và `product_reports`. Đóng vai trò là **Service Aggregator** tổng hợp dữ liệu từ **Product Service (Port 8001)** và **Order Service (Port 8002)**, đồng thời xác thực tập trung qua **Auth Service (Port 8000)**.

---

## 🎯 Chức Năng Chính (Theo Đề Bài)
- **Báo cáo theo sản phẩm (`product_reports`)**:
  - `GET /reports/products`: Lấy danh sách tất cả các báo cáo theo sản phẩm.
  - `GET /reports/products/{id}`: Lấy chi tiết báo cáo cho một sản phẩm.
  - `POST /reports/products`: Tạo báo cáo sản phẩm mới (tổng hợp tồn kho và hàng bán).
  - `DELETE /reports/products/{id}`: Xóa báo cáo sản phẩm.
- **Báo cáo theo đơn hàng (`orders_reports`)**:
  - `GET /reports/orders`: Lấy danh sách tất cả các báo cáo theo đơn hàng.
  - `GET /reports/orders/{id}`: Lấy chi tiết báo cáo cho một đơn hàng.
  - `POST /reports/orders`: Tạo báo cáo đơn hàng mới (tính toán `total_revenue`, `total_cost`, `total_profit`).
  - `DELETE /reports/orders/{id}`: Xóa báo cáo đơn hàng.
- **KPI Real-time Dashboard**:
  - `GET /reports/summary`: Thống kê tổng hợp số lượng hàng tồn, hàng bán được, doanh thu, chi phí, lợi nhuận thời gian thực.
  - Giao diện Web tương tác trực quan với Tailwind CSS.

---

## 🚀 Khởi Chạy Nhanh

### 1. Bật toàn bộ hệ sinh thái (4 Services)
```powershell
.\start_services.ps1
```

### 2. Truy cập hệ thống
- **Web UI Dashboard**: [http://127.0.0.1:8003](http://127.0.0.1:8003)
- **Tài khoản mẫu**: `student` / `123456`
- **Health Check**: [http://127.0.0.1:8003/health](http://127.0.0.1:8003/health)

### 3. Chạy kiểm thử tự động
```powershell
.\run_tests.ps1
# hoặc: php vendor/phpunit/phpunit/phpunit --testdox
```

---

## 📁 Tài Liệu Chi Tiết
- [Hướng dẫn chi tiết & Câu hỏi bảo vệ đồ án](HUONG_DAN.md)
- [Postman Collection](postman/Reporting-Service.postman_collection.json)
