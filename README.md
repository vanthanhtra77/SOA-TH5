# SOA TH5 - Dịch Vụ Báo Cáo & Thống Kê (Reporting Service)

[![Tests](https://img.shields.io/badge/PHPUnit-12%20Passed%2C%20159%20Assertions-brightgreen)](tests/ReportTest.php)
[![Architecture](https://img.shields.io/badge/Architecture-SOA%20Aggregator-blue)]()
[![Framework](https://img.shields.io/badge/Framework-Lumen%2010-red)]()
[![PHP](https://img.shields.io/badge/PHP-8.4-purple)]()

Dự án **Bài Thực Hành Số 5** - Môn Kiến Trúc Hướng Dịch Vụ (Service-Oriented Architecture - SOA).
Dịch vụ hoạt động độc lập trên cổng **8003** với cơ sở dữ liệu riêng biệt `database/reports.sqlite`, thực hiện tổng hợp dữ liệu (Aggregator Pattern) từ **Product Service (Port 8001)** và **Order Service (Port 8002)** và xác thực tập trung qua **Auth Service (Port 8000)**.

---

## 🎯 Chức Năng Chính
- **Thống kê tổng quan**: Số lượng hàng tồn kho, hàng bán được, doanh thu, chi phí, lợi nhuận và biên lợi nhuận.
- **Báo cáo theo sản phẩm**: Chi tiết tồn kho, số lượng đã bán, doanh thu và đóng góp lợi nhuận của từng mặt hàng.
- **Báo cáo theo đơn hàng**: Phân tích trạng thái đơn (pending, confirmed, shipping, completed, cancelled), giá trị đơn hàng trung bình (AOV), tỷ lệ hoàn tất.
- **Quản lý Bản Lưu Báo Cáo (Snapshots)**: Lưu trữ các mốc thời gian báo cáo vào CSDL riêng `reports.sqlite` (CRUD).
- **Giao diện Web Dashboard**: Trực quan hóa số liệu thời gian thực bằng Tailwind CSS responsive.

---

## 🏗️ Hệ Sinh Thái 4 Dịch Vụ SOA

| Dịch vụ | Cổng | Cơ sở dữ liệu | Vai trò |
| :--- | :---: | :--- | :--- |
| **Auth Service (TH2)** | `8000` | `database.sqlite` | Quản lý người dùng, cấp phát và xác thực JWT |
| **Product Service (TH3)** | `8001` | `products.sqlite` | Quản lý danh mục & số lượng tồn kho sản phẩm |
| **Order Service (TH4)** | `8002` | `orders.sqlite` | Quản lý đơn hàng, khấu trừ & hoàn kho tự động |
| **Reporting Service (TH5)** | `8003` | `reports.sqlite` | **Tổng hợp báo cáo kinh doanh liên dịch vụ (Aggregator)** |

---

## 🚀 Khởi Chạy Nhanh

### 1. Bật toàn bộ hệ thống (4 Microservices)
Chạy script PowerShell đi kèm:
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
# hoặc: php vendor/phpunit/phpunit/phpunit
```

---

## 📁 Tài Liệu Chi Tiết
- [Hướng dẫn chi tiết & Câu hỏi bảo vệ đồ án](HUONG_DAN.md)
- [Postman Collection](postman/Reporting-Service.postman_collection.json)
