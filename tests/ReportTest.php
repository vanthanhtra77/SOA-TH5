<?php

namespace Tests;

use App\Models\OrderReport;
use App\Models\ProductReport;
use App\Models\ReportSnapshot;

class ReportTest extends TestCase
{
    protected string $validToken = 'valid-test-token';
    protected string $invalidToken = 'invalid-test-token';

    protected function setUp(): void
    {
        parent::setUp();
        // Cấu hình mock cho testing độc lập
        putenv('AUTH_SERVICE_MOCK=true');
        putenv('PRODUCT_SERVICE_MOCK=true');
        putenv('ORDER_SERVICE_MOCK=true');
    }

    // =========================================================================
    // 1. KIỂM THỬ XÁC THỰC BẢO MẬT LIÊN DỊCH VỤ
    // =========================================================================

    /**
     * Test 1: Yêu cầu không có Token phải bị từ chối với HTTP 401
     */
    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->get('/reports/summary');
        $this->assertResponseStatus(401);
        $this->seeJsonStructure(['success', 'message']);
    }

    /**
     * Test 2: Yêu cầu với Token không hợp lệ phải bị từ chối với HTTP 401
     */
    public function test_invalid_token_is_rejected(): void
    {
        $this->get('/reports/summary', ['Authorization' => 'Bearer ' . $this->invalidToken]);
        $this->assertResponseStatus(401);
        $this->seeJsonStructure(['success', 'message', 'error']);
    }

    // =========================================================================
    // 2. KIỂM THỬ NHÓM BÁO CÁO THEO SẢN PHẨM (PRODUCT REPORTS - BẢNG product_reports)
    // =========================================================================

    /**
     * Test 3: Lấy danh sách tất cả các báo cáo theo sản phẩm (GET /reports/products)
     */
    public function test_get_all_product_reports(): void
    {
        $this->get('/reports/products', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'total',
            'data' => [
                '*' => ['id', 'order_report_id', 'product_id', 'total_sold', 'revenue', 'cost', 'profit']
            ]
        ]);
    }

    /**
     * Test 4: Lấy chi tiết báo cáo cho một sản phẩm theo ID (GET /reports/products/{id})
     */
    public function test_get_single_product_report_by_id(): void
    {
        $rep = ProductReport::first();
        $this->assertNotNull($rep);

        $this->get('/reports/products/' . $rep->id, ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJson([
            'success' => true,
            'id' => $rep->id,
            'product_id' => $rep->product_id,
        ]);
    }

    /**
     * Test 5: Tạo báo cáo sản phẩm mới (POST /reports/products)
     */
    public function test_create_product_report(): void
    {
        $payload = [
            'product_id' => 1,
        ];

        $this->post('/reports/products', $payload, ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(201);
        $this->seeJson([
            'success' => true,
            'product_id' => 1,
        ]);

        $this->seeInDatabase('product_reports', ['product_id' => 1]);
    }

    /**
     * Test 6: Xóa báo cáo sản phẩm (DELETE /reports/products/{id})
     */
    public function test_delete_product_report(): void
    {
        $temp = ProductReport::create([
            'product_id' => 999,
            'total_sold' => 0,
            'revenue' => 0,
            'cost' => 0,
            'profit' => 0,
        ]);

        $this->delete('/reports/products/' . $temp->id, [], ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJson([
            'success' => true,
            'deleted_id' => $temp->id,
        ]);

        $this->notSeeInDatabase('product_reports', ['id' => $temp->id]);
    }

    // =========================================================================
    // 3. KIỂM THỬ NHÓM BÁO CÁO THEO ĐƠN HÀNG (ORDER REPORTS - BẢNG orders_reports)
    // =========================================================================

    /**
     * Test 7: Lấy danh sách tất cả các báo cáo theo đơn hàng (GET /reports/orders)
     */
    public function test_get_all_order_reports(): void
    {
        $this->get('/reports/orders', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'total',
            'data' => [
                '*' => ['id', 'order_id', 'total_revenue', 'total_cost', 'total_profit']
            ]
        ]);
    }

    /**
     * Test 8: Lấy chi tiết báo cáo cho một đơn hàng (GET /reports/orders/{id})
     */
    public function test_get_single_order_report_by_id(): void
    {
        $rep = OrderReport::first();
        $this->assertNotNull($rep);

        $this->get('/reports/orders/' . $rep->id, ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJson([
            'success' => true,
            'id' => $rep->id,
            'order_id' => $rep->order_id,
        ]);
    }

    /**
     * Test 9: Tạo báo cáo đơn hàng mới (POST /reports/orders)
     */
    public function test_create_order_report(): void
    {
        $payload = [
            'order_id' => 1,
        ];

        $this->post('/reports/orders', $payload, ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(201);
        $this->seeJson([
            'success' => true,
            'order_id' => 1,
        ]);

        $this->seeInDatabase('orders_reports', ['order_id' => 1]);
    }

    /**
     * Test 10: Xóa báo cáo đơn hàng (DELETE /reports/orders/{id})
     */
    public function test_delete_order_report(): void
    {
        $temp = OrderReport::create([
            'order_id' => 999,
            'total_revenue' => 1000000,
            'total_cost' => 700000,
            'total_profit' => 300000,
        ]);

        $this->delete('/reports/orders/' . $temp->id, [], ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJson([
            'success' => true,
            'deleted_id' => $temp->id,
        ]);

        $this->notSeeInDatabase('orders_reports', ['id' => $temp->id]);
    }

    // =========================================================================
    // 4. KIỂM THỬ BÁO CÁO TỔNG QUAN, DASHBOARD & SNAPSHOTS
    // =========================================================================

    /**
     * Test 11: Lấy báo cáo KPI tổng quan thời gian thực (GET /reports/summary)
     */
    public function test_get_summary_report(): void
    {
        $this->get('/reports/summary', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'total_inventory',
                'inventory_value',
                'total_sold',
                'total_revenue',
                'total_cost',
                'total_profit',
                'profit_margin',
            ]
        ]);
    }

    /**
     * Test 12: Báo cáo tích hợp toàn diện (GET /reports/full)
     */
    public function test_get_full_report(): void
    {
        $this->get('/reports/full', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'data' => ['summary', 'by_product', 'by_order', 'generated_at']
        ]);
    }

    /**
     * Test 13: Lấy danh sách các bản lưu báo cáo (GET /reports/snapshots)
     */
    public function test_list_report_snapshots(): void
    {
        $this->get('/reports/snapshots', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'total',
            'data' => [
                '*' => ['id', 'title', 'total_inventory', 'total_sold', 'total_revenue', 'total_profit']
            ]
        ]);
    }

    /**
     * Test 14: Tạo bản lưu báo cáo mới (POST /reports/snapshots)
     */
    public function test_create_report_snapshot(): void
    {
        $payload = [
            'title' => 'Báo cáo doanh số tháng 11',
            'notes' => 'Tự động tạo từ PHPUnit Test Suite',
        ];

        $this->post('/reports/snapshots', $payload, ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(201);
        $this->seeJson([
            'success' => true,
            'title' => 'Báo cáo doanh số tháng 11',
        ]);
    }

    /**
     * Test 15: Endpoint kiểm tra trạng thái sức khỏe /health
     */
    public function test_health_check_endpoint(): void
    {
        $this->get('/health');
        $this->assertResponseStatus(200);
        $this->seeJson(['status' => 'UP', 'port' => 8003]);
    }
}
