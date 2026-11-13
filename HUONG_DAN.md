# HƯỚNG DẪN CHI TIẾT & BẢO VỆ BÀI THỰC HÀNH SỐ 5 (SOA)
## DỊCH VỤ BÁO CÁO & THỐNG KÊ (REPORTING SERVICE)

---

## 1. TỔNG QUAN ĐỀ BÀI VÀ MỤC TIÊU
- **Mục tiêu**:
  - Xây dựng dịch vụ báo cáo với các chức năng thống kê số lượng hàng tồn, hàng bán được, doanh thu, chi phí, lợi nhuận. Báo cáo theo sản phẩm và đơn hàng.
  - Dịch vụ hoạt động độc lập trên một cổng (`8003`) và cơ sở dữ liệu riêng biệt (`reports.sqlite`) tuân theo nguyên tắc của kiến trúc SOA.
  - Xác thực qua dịch vụ đã xây dựng ở bài thực hành số 2 (Auth Service - Port `8000`).

---

## 2. KIẾN TRÚC CƠ SỞ DỮ LIỆU RIÊNG (PHỤ LỤC ĐỀ BÀI)

Cơ sở dữ liệu độc lập `database/reports.sqlite` gồm 2 bảng chuẩn theo đúng Phụ lục:

### Bảng 1: Bảng báo cáo đơn hàng (`orders_reports`)
| Tên cột | Kiểu dữ liệu | Mô tả |
| :--- | :--- | :--- |
| `id` | INT (PRIMARY KEY) | ID của báo cáo đơn hàng |
| `order_id` | INT | ID của đơn hàng (lưu vết để liên kết với dịch vụ quản lý đơn hàng) |
| `total_revenue` | DECIMAL(10, 2) | Tổng doanh thu của đơn hàng |
| `total_cost` | DECIMAL(10, 2) | Tổng chi phí nhập sản phẩm trong đơn hàng |
| `total_profit` | DECIMAL(10, 2) | Tổng lợi nhuận (`total_revenue - total_cost`) |
| `created_at` | TIMESTAMP | Ngày tạo báo cáo |
| `updated_at` | TIMESTAMP | Ngày cập nhật báo cáo |

### Bảng 2: Bảng báo cáo sản phẩm (`product_reports`)
| Tên cột | Kiểu dữ liệu | Mô tả |
| :--- | :--- | :--- |
| `id` | INT (PRIMARY KEY) | ID của báo cáo sản phẩm |
| `order_report_id`| INT | ID đơn hàng (liên kết với bảng `orders_reports`) |
| `product_id` | INT | ID sản phẩm (lưu vết để liên kết với dịch vụ quản lý sản phẩm) |
| `total_sold` | INT | Tổng số lượng sản phẩm đã bán |
| `revenue` | DECIMAL(10, 2) | Doanh thu từ sản phẩm |
| `cost` | DECIMAL(10, 2) | Chi phí nhập sản phẩm |
| `profit` | DECIMAL(10, 2) | Lợi nhuận (`revenue - cost`) |
| `created_at` | TIMESTAMP | Ngày tạo báo cáo |
| `updated_at` | TIMESTAMP | Ngày cập nhật báo cáo |

---

## 3. DANH MỤC RESTFUL API HOÀN CHỈNH (8 ENDPOINTS CHÍNH THEO ĐỀ)

### Nhóm 1: Báo cáo theo Sản phẩm (`/reports/products`)
1. `GET /reports/products`: Lấy danh sách tất cả các báo cáo theo sản phẩm.
2. `GET /reports/products/{id}`: Lấy chi tiết báo cáo cho một sản phẩm theo ID.
3. `POST /reports/products`: Tạo báo cáo sản phẩm mới dựa trên dữ liệu từ dịch vụ quản lý sản phẩm (Port 8001) và quản lý đơn hàng (Port 8002).
4. `DELETE /reports/products/{id}`: Xóa báo cáo sản phẩm.

### Nhóm 2: Báo cáo theo Đơn hàng (`/reports/orders`)
5. `GET /reports/orders`: Lấy danh sách tất cả các báo cáo theo đơn hàng.
6. `GET /reports/orders/{id}`: Lấy chi tiết báo cáo cho một đơn hàng theo ID.
7. `POST /reports/orders`: Tạo báo cáo đơn hàng mới dựa trên dữ liệu từ dịch vụ quản lý đơn hàng (Port 8002).
8. `DELETE /reports/orders/{id}`: Xóa báo cáo đơn hàng.

---

## 4. QUY TRÌNH GIAO TIẾP & TÍNH TOÁN LIÊN DỊCH VỤ (SOA DATA FLOW)

```
[Client / Postman]
       |
       | 1. Gửi request: POST /reports/products (product_id = 1) kèm Token
       v
[Reporting Service - Port 8003]
       |
       | 2. Giao tiếp Service 1 (Auth Service - Port 8000)
       |    GET /auth -> Xác minh tính hợp lệ của JWT Token
       v
[Reporting Service - Port 8003]
       |
       | 3. Giao tiếp Service 2 (Product Service - Port 8001)
       |    GET /products/1 -> Lấy thông tin giá bán và tên sản phẩm
       v
[Reporting Service - Port 8003]
       |
       | 4. Giao tiếp Service 3 (Order Service - Port 8002)
       |    GET /orders -> Tổng hợp số lượng đã bán (total_sold) và doanh thu (revenue)
       v
[Reporting Service - Port 8003]
       |
       | 5. Tính toán theo nghiệp vụ:
       |    cost = revenue * 70%
       |    profit = revenue - cost
       v
[Reporting Service CSDL Riêng: database/reports.sqlite]
       | 6. Lưu vào bảng product_reports (hoặc orders_reports)
       v
[Client / Postman]: Nhận phản hồi HTTP 201 Created
```

---

## 5. HƯỚNG DẪN KIỂM THỬ

### 5.1. Khởi chạy hệ thống 4 Microservices
Chạy script PowerShell tại thư mục `D:\SOA\TH5`:
```powershell
.\start_services.ps1
```

### 5.2. Chạy bộ kiểm thử tự động (PHPUnit Test Suite)
```powershell
.\run_tests.ps1
# hoặc: php vendor/phpunit/phpunit/phpunit --testdox
```
**Kết quả đạt được:** `OK (15 tests, 139 assertions)` — **100% Passed**.

### 5.3. Trải nghiệm giao diện Web trực quan
Mở trình duyệt truy cập: **`http://localhost:8003`**
- Tab 1: Báo Cáo Sản Phẩm (`product_reports`)
- Tab 2: Báo Cáo Đơn Hàng (`orders_reports`)
- Tab 3: KPI Tổng Quan Thời Gian Thực
- Form tạo báo cáo và Console Log thời gian thực.

---

## 6. CÂU HỎI VẤN ĐÁP BẢO VỆ VỚI GIẢNG VIÊN

**Câu hỏi 1: Tại sao Reporting Service phải có CSDL riêng mà không dùng chung với Order hoặc Product?**
> **Trả lời:** Theo nguyên tắc cốt lõi của Kiến trúc Hướng Dịch vụ (SOA) - **Database per Service**, mỗi dịch vụ phải có ranh giới dữ liệu độc lập. Dịch vụ báo cáo cần lưu trữ lịch sử báo cáo định kỳ (snapshots / reporting logs) mà không làm ảnh hưởng đến hiệu năng truy vấn giao dịch (OLTP) của Product Service và Order Service.

**Câu hỏi 2: Dịch vụ báo cáo lấy dữ liệu như thế nào để tính toán doanh thu và lợi nhuận?**
> **Trả lời:** Dịch vụ đóng vai trò là một **Service Aggregator**. Khi cần tạo báo cáo, nó gọi API qua giao thức HTTP RESTful sang Product Service (Port 8001) để lấy giá và danh mục, gọi Order Service (Port 8002) để lấy dữ liệu bán hàng. Sau đó áp dụng công thức: `total_cost = total_revenue * 70%`, `total_profit = total_revenue - total_cost` và lưu vào bảng `orders_reports` hoặc `product_reports`.
