# HƯỚNG DẪN CHI TIẾT & BẢO VỆ BÀI THỰC HÀNH 5 (SOA)
## DỊCH VỤ BÁO CÁO & THỐNG KÊ (REPORTING SERVICE) TRONG HỆ SINH THÁI KIẾN TRÚC SOA

---

## 1. TỔNG QUAN ĐỀ BÀI VÀ MỤC TIÊU

### 1.1. Yêu cầu đề bài:
> - **Xây dựng dịch vụ báo cáo** với các chức năng thống kê:
>   - Số lượng hàng tồn kho.
>   - Số lượng hàng bán được.
>   - Doanh thu (Revenue).
>   - Chi phí (Cost).
>   - Lợi nhuận (Profit) & Biên lợi nhuận (Profit Margin).
> - **Báo cáo phân loại**:
>   - Báo cáo theo từng **sản phẩm** (Product breakdown: tồn kho, đã bán, doanh thu, lợi nhuận).
>   - Báo cáo theo **đơn hàng** (Order breakdown: trạng thái đơn, giá trị trung bình đơn hàng AOV, tỷ lệ thành công).
> - **Tuân thủ nguyên tắc kiến trúc SOA**:
>   - Dịch vụ hoạt động **độc lập trên một cổng riêng** (Port `8003`).
>   - Dịch vụ sở hữu **cơ sở dữ liệu riêng biệt** (`reports.sqlite`), không dùng chung bảng hay truy vấn trực tiếp CSDL của các dịch vụ khác.
>   - Giao tiếp liên dịch vụ hoàn toàn thông qua giao thức **HTTP RESTful API** và chuyển tiếp mã xác thực **Bearer Token**.

---

## 2. KIẾN TRÚC TỔNG THỂ HỆ SINH THÁI SOA (4 SERVICES)

Hệ thống hoàn chỉnh bao gồm 4 Microservices độc lập tương tác với nhau:

```
                  +-----------------------------------+
                  |         NGƯỜI DÙNG / POSTMAN      |
                  +-----------------+-----------------+
                                    |
          1. Đăng nhập              | 2. Gửi request kèm Token
     (POST /login userName/pass)    |
                                    v
     +--------------------------------------------------------------+
     |                SERVICE 4: REPORTING SERVICE                  |
     |           Port: 8003 | CSDL: reports.sqlite                  |
     |      (Đóng vai trò Service Aggregator - Tổng hợp dữ liệu)     |
     +---------+--------------------+---------------------+---------+
               |                    |                     |
     Xác thực  |                    |                     |
     Token     |                    | Lấy tồn kho,        | Lấy danh sách đơn,
  (GET /auth)  |                    | giá bán sản phẩm    | số lượng đã bán
               |                    | (GET /products)     | (GET /orders)
               v                    v                     v
     +-----------------+   +------------------+   +-----------------+
     |   SERVICE 1     |   |    SERVICE 2     |   |    SERVICE 3    |
     |  AUTH SERVICE   |   | PRODUCT SERVICE  |   |  ORDER SERVICE  |
     |   (Bài TH 2)    |   |    (Bài TH 3)    |   |   (Bài TH 4)    |
     |   Port: 8000    |   |    Port: 8001    |   |   Port: 8002    |
     | CSDL: database/ |   | CSDL: database/  |   | CSDL: database/ |
     | database.sqlite |   | products.sqlite  |   |  orders.sqlite  |
     +-----------------+   +------------------+   +-----------------+
```

### Bảng phân định trách nhiệm và ranh giới dữ liệu (Data Boundaries):

| Thành phần | Dịch vụ | Cổng (Port) | Đường dẫn CSDL riêng | Bảng dữ liệu quản lý | Giao thức kết nối |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Service 1** | Auth Service | `8000` | `th2/database/database.sqlite` | `users` | REST API / JWT |
| **Service 2** | Product Service | `8001` | `TH3/database/products.sqlite` | `products` | REST API / JSON |
| **Service 3** | Order Service | `8002` | `TH4/database/orders.sqlite` | `orders` | REST API / JSON |
| **Service 4** | **Reporting Service** | **`8003`** | **`TH5/database/reports.sqlite`** | **`report_snapshots`** | **REST API / JSON / Blade Web UI** |

---

## 3. CÁC NGUYÊN TẮC SOA ÁP DỤNG TRONG BÀI THỰC HÀNH 5

1. **Database-per-Service (Mỗi dịch vụ một CSDL độc lập)**:
   - Reporting Service **tuyệt đối không** kết nối vào `products.sqlite` hay `orders.sqlite` để viết câu lệnh SQL JOIN.
   - Dữ liệu tồn kho và đơn hàng được truy vấn động qua HTTP REST API.
   - Reporting Service chỉ lưu trữ lịch sử báo cáo (Snapshots) vào CSDL riêng `reports.sqlite` (bảng `report_snapshots`).

2. **Aggregator Service Pattern (Mẫu thiết kế dịch vụ tổng hợp)**:
   - `ReportingService` nhận yêu cầu từ client, phân rã và gửi các truy vấn song song/tuần tự tới Product Service và Order Service, sau đó tổng hợp, tính toán các chỉ số kinh doanh (Doanh thu, Chi phí, Lợi nhuận) và trả về cho Client trong một định dạng thống nhất.

3. **Loose Coupling & Service Contracts (Kết nối lỏng lẻo & Khế ước dịch vụ)**:
   - Các dịch vụ liên lạc với nhau qua chuẩn mở HTTP/JSON.
   - Nếu Product Service thay đổi cấu trúc bảng nội bộ, chỉ cần giữ nguyên giao diện JSON đầu ra của `/products`, Reporting Service hoàn toàn không bị ảnh hưởng.

4. **Security Context Propagation (Lan truyền ngữ cảnh bảo mật)**:
   - Client gửi Bearer Token kèm theo request.
   - Reporting Service xác thực token qua Auth Service (Port 8000), sau đó chuyển tiếp token này sang Product Service và Order Service để đảm bảo quyền truy cập xuyên suốt.

5. **Graceful Degradation & Mocking**:
   - Khi chạy kiểm thử tự động (Unit Test), hệ thống hỗ trợ Mock Client độc lập giúp kiểm thử nhanh chóng mà không cần bật cả cụm 4 server.

---

## 4. CÔNG THỨC VÀ NGHIỆP VỤ TÍNH TOÁN BÁO CÁO

### 4.1. Hàng tồn kho (`total_inventory`):
$$\text{total\_inventory} = \sum_{p \in \text{products}} \text{quantity}_p$$
- Giá trị tồn kho (`inventory_value`): $\sum (p.\text{price} \times p.\text{quantity})$.

### 4.2. Hàng bán được (`total_sold`):
$$\text{total\_sold} = \sum_{o \in \text{valid\_orders}} \text{quantity}_o$$
*(Chỉ tính các đơn hàng hợp lệ: `pending`, `confirmed`, `shipping`, `completed`; loại trừ các đơn có trạng thái `cancelled`).*

### 4.3. Doanh thu (`total_revenue`):
$$\text{total\_revenue} = \sum_{o \in \text{valid\_orders}} \text{total\_price}_o$$

### 4.4. Chi phí vốn hàng bán (`total_cost` - COGS):
$$\text{total\_cost} = \text{total\_revenue} \times 70\%$$
*(Chi phí giá vốn hàng bán ước tính bằng 70% giá trị doanh thu).*

### 4.5. Lợi nhuận gộp (`total_profit`):
$$\text{total\_profit} = \text{total\_revenue} - \text{total\_cost}$$

### 4.6. Tỷ suất lợi nhuận (`profit_margin`):
$$\text{profit\_margin} = \left(\frac{\text{total\_profit}}{\text{total\_revenue}}\right) \times 100\% \quad (\approx 30\%)$$

---

## 5. DANH MỤC API CỦA REPORTING SERVICE (PORT 8003)

| STT | Phương thức | Endpoint | Yêu cầu Token | Mô tả chức năng |
| :---: | :---: | :--- | :---: | :--- |
| 1 | `GET` | `/` | Không | Giao diện Web Dashboard trực quan (Tailwind CSS) |
| 2 | `GET` | `/health` | Không | Kiểm tra trạng thái hoạt động của Reporting Service & Dependencies |
| 3 | `GET` | `/reports/summary` | **Có** | Thống kê KPI tổng quan (Tồn kho, Đã bán, Doanh thu, Chi phí, Lợi nhuận) |
| 4 | `GET` | `/reports/products` | **Có** | Thống kê chi tiết theo từng sản phẩm (Tồn, Bán, Doanh thu, Lợi nhuận) |
| 5 | `GET` | `/reports/orders` | **Có** | Thống kê theo đơn hàng (Phân loại trạng thái, AOV, Tỷ lệ thành công) |
| 6 | `GET` | `/reports/full` | **Có** | Báo cáo tích hợp toàn diện (Summary + Products + Orders) |
| 7 | `GET` | `/reports/snapshots` | **Có** | Lấy danh sách các bản lưu báo cáo trong CSDL riêng |
| 8 | `GET` | `/reports/snapshots/{id}` | **Có** | Lấy chi tiết một bản lưu báo cáo theo ID |
| 9 | `POST` | `/reports/snapshots` | **Có** | Chụp nhanh số liệu thời gian thực và lưu vào CSDL riêng |
| 10 | `DELETE`| `/reports/snapshots/{id}` | **Có** | Xóa một bản lưu báo cáo khỏi CSDL riêng |

---

## 6. HƯỚNG DẪN CÀI ĐẶT VÀ KHỞI CHẠY HỆ THỐNG

### Cách 1: Khởi động tự động toàn bộ 4 Services bằng 1 click
Mở PowerShell tại thư mục `D:\SOA\TH5` và chạy:
```powershell
.\start_services.ps1
```
Script sẽ tự động kiểm tra và bật cả 4 Microservices:
- Port 8000: Auth Service
- Port 8001: Product Service
- Port 8002: Order Service
- Port 8003: Reporting Service

### Cách 2: Khởi động thủ công từng dịch vụ
Mở 4 cửa sổ Terminal riêng biệt:
- **Terminal 1 (Auth Service)**:
  ```powershell
  cd "D:\SOA\th2\SOATh2-main\SOATh2-main"
  php -S 127.0.0.1:8000 -t public
  ```
- **Terminal 2 (Product Service)**:
  ```powershell
  cd "D:\SOA\TH3"
  php -S 127.0.0.1:8001 -t public
  ```
- **Terminal 3 (Order Service)**:
  ```powershell
  cd "D:\SOA\TH4"
  php -S 127.0.0.1:8002 -t public
  ```
- **Terminal 4 (Reporting Service)**:
  ```powershell
  cd "D:\SOA\TH5"
  php -S 127.0.0.1:8003 -t public
  ```

---

## 7. HƯỚNG DẪN KIỂM THỬ

### 7.1. Trải nghiệm giao diện Web Dashboard trực quan
1. Mở trình duyệt truy cập: `http://127.0.0.1:8003/`
2. Nhấn nút **"Đăng nhập & Lấy Token mẫu"** (hệ thống tự động kết nối Auth Service port 8000 với tài khoản `student` / `123456`).
3. Nhấn **"Tải Báo Cáo Thời Gian Thực"**: Xem ngay 6 thẻ KPI, biểu đồ trạng thái đơn hàng, bảng phân tích lợi nhuận từng sản phẩm.
4. Nhập tiêu đề và nhấn **"Lưu Bản Chụp (Snapshot) vào CSDL"**: Xác nhận dữ liệu được lưu vĩnh viễn vào `reports.sqlite`.

### 7.2. Chạy kiểm thử tự động (Unit Test Suite)
Tại thư mục `D:\SOA\TH5`, chạy lệnh:
```powershell
.\run_tests.ps1
# hoặc: php vendor/phpunit/phpunit/phpunit
```
**Kết quả chuẩn:** Đạt `OK (12 tests, 159 assertions)` 100% Passed.

### 7.3. Kiểm thử với Postman
1. Mở Postman -> Chọn **Import** -> Chọn file:
   `D:\SOA\TH5\postman\Reporting-Service.postman_collection.json`
2. Chạy request đầu tiên: **0. Đăng nhập lấy Token (POST /login)** (Token sẽ tự động lưu vào biến môi trường Postman).
3. Chạy các request trong mục **2. Báo cáo & Thống kê** và **3. Quản lý Bản Lưu Báo Cáo**.

---

## 8. BỘ CÂU HỎI BẢO VỆ BÀI THỰC HÀNH VỚI GIẢNG VIÊN

**Câu hỏi 1: Tại sao dịch vụ báo cáo không kết nối trực tiếp vào CSDL của dịch vụ sản phẩm và đơn hàng để viết câu lệnh SQL JOIN cho nhanh?**
> **Trả lời:** Theo nguyên tắc cốt lõi của Kiến trúc Hướng Dịch vụ (SOA) và Microservices, mỗi dịch vụ phải sở hữu một CSDL riêng biệt (**Database-per-Service**) để đảm bảo tính **Loose Coupling** (kết nối lỏng lẻo) và **High Autonomy** (tính tự chủ cao). Nếu Reporting Service kết nối trực tiếp vào CSDL của Product Service hay Order Service:
> 1. Vi phạm tính đóng gói (encapsulation) của dữ liệu.
> 2. Gây ra hiện tượng thắt cổ chai phụ thuộc cấu trúc (Schema tight coupling). Nếu Product Service đổi tên cột bảng, Reporting Service sẽ lập tức bị gãy.
> 3. Không thể mở rộng (scale) độc lập hoặc triển khai CSDL khác loại (ví dụ Order dùng MongoDB còn Product dùng PostgreSQL). Do đó, việc tổng hợp qua HTTP REST API là chuẩn mực SOA.

**Câu hỏi 2: Dịch vụ báo cáo xử lý xác thực người dùng như thế nào?**
> **Trả lời:** Dịch vụ sử dụng Middleware `AuthenticateViaAuthService`. Khi nhận request có Bearer Token, middleware sẽ gọi sang Auth Service (Port 8000: `GET /auth`) để xác minh tính hợp lệ của token và lấy thông tin người dùng (`student`). Sau khi xác thực thành công, Reporting Service chuyển tiếp chính token này trong header sang Product Service (8001) và Order Service (8002) để lấy dữ liệu.

**Câu hỏi 3: Nếu một trong hai dịch vụ (Product hoặc Order) bị sập thì Reporting Service xử lý ra sao?**
> **Trả lời:** Reporting Service được thiết kế theo nguyên tắc chịu lỗi (Fault Tolerance). Trong client `ProductServiceClient` và `OrderServiceClient`, các request đều có thiết lập `timeout` (3 giây) và bắt lỗi kết nối `curl_error`. Nếu một dịch vụ không phản hồi, hệ thống ghi nhận trạng thái nguồn dữ liệu là `UNAVAILABLE` và vẫn trả về thông tin sẵn có thay vì để crash toàn bộ ứng dụng. Đồng thời, các bản lưu báo cáo (Snapshots) trong quá khứ vẫn được truy xuất bình thường từ CSDL `reports.sqlite` của chính nó.

**Câu hỏi 4: Cơ sở dữ liệu riêng của Reporting Service lưu trữ những gì?**
> **Trả lời:** CSDL `database/reports.sqlite` chứa bảng `report_snapshots`. Bảng này lưu lại các "bản chụp" số liệu tại một thời điểm kinh doanh (chốt ca, chốt ngày, chốt tháng) bao gồm: tổng tồn kho, tổng hàng bán, doanh thu, chi phí, lợi nhuận, chi tiết JSON theo sản phẩm, phân tích đơn hàng, người tạo và ghi chú.
