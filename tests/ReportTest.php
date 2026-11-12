<?php

namespace Tests;

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

    /**
     * Test 3: Lấy báo cáo KPI tổng quan (Hàng tồn, Hàng bán, Doanh thu, Chi phí, Lợi nhuận) (HTTP 200)
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
                'total_orders',
                'active_orders',
            ]
        ]);

        $data = json_decode($this->response->getContent(), true)['data'];
        $this->assertGreaterThan(0, $data['total_inventory']);
        $this->assertGreaterThan(0, $data['total_sold']);
        $this->assertGreaterThan(0, $data['total_revenue']);
        $this->assertEquals(round($data['total_revenue'] - $data['total_cost'], 2), $data['total_profit']);
    }

    /**
     * Test 4: Báo cáo chi tiết theo sản phẩm (HTTP 200)
     */
    public function test_get_product_report(): void
    {
        $this->get('/reports/products', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'total_products',
            'data' => [
                '*' => [
                    'product_id',
                    'product_name',
                    'unit_price',
                    'current_stock',
                    'sold_quantity',
                    'revenue',
                    'cost',
                    'profit',
                    'stock_status',
                ]
            ]
        ]);
    }

    /**
     * Test 5: Báo cáo chi tiết theo đơn hàng (HTTP 200)
     */
    public function test_get_order_report(): void
    {
        $this->get('/reports/orders', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'total_orders',
                'status_breakdown',
                'average_order_value',
                'success_rate',
                'orders' => [
                    '*' => ['id', 'user_name', 'product_name', 'quantity', 'total_price', 'status']
                ]
            ]
        ]);
    }

    /**
     * Test 6: Báo cáo tích hợp toàn diện (Full Dashboard) (HTTP 200)
     */
    public function test_get_full_report(): void
    {
        $this->get('/reports/full', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'summary',
                'by_product',
                'by_order',
                'generated_at',
            ]
        ]);
    }

    /**
     * Test 7: Lấy danh sách các bản lưu báo cáo (Snapshots) từ CSDL riêng (HTTP 200)
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
     * Test 8: Tạo và lưu mới một bản chụp báo cáo vào database (HTTP 201)
     */
    public function test_create_report_snapshot(): void
    {
        $payload = [
            'title' => 'Báo cáo doanh thu tháng 11',
            'notes' => 'Tự động tạo từ PHPUnit Test Suite',
        ];

        $this->post('/reports/snapshots', $payload, ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(201);
        $this->seeJson([
            'success' => true,
            'title' => 'Báo cáo doanh thu tháng 11',
        ]);

        $this->seeInDatabase('report_snapshots', ['title' => 'Báo cáo doanh thu tháng 11']);
    }

    /**
     * Test 9: Xem chi tiết một bản chụp báo cáo (HTTP 200)
     */
    public function test_show_report_snapshot(): void
    {
        $snapshot = ReportSnapshot::first();
        $this->assertNotNull($snapshot);

        $this->get('/reports/snapshots/' . $snapshot->id, ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJson([
            'success' => true,
            'id' => $snapshot->id,
            'title' => $snapshot->title,
        ]);
    }

    /**
     * Test 10: Xem bản chụp báo cáo không tồn tại trả về HTTP 404
     */
    public function test_show_nonexistent_snapshot_returns_404(): void
    {
        $this->get('/reports/snapshots/999999', ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(404);
        $this->seeJson(['success' => false]);
    }

    /**
     * Test 11: Xóa một bản chụp báo cáo (HTTP 200)
     */
    public function test_delete_report_snapshot(): void
    {
        $temp = ReportSnapshot::create([
            'title' => 'Bản lưu thử nghiệm xóa',
            'total_inventory' => 50,
            'total_sold' => 10,
            'total_revenue' => 1000000,
            'total_cost' => 700000,
            'total_profit' => 300000,
            'profit_margin' => 30.0,
        ]);

        $this->delete('/reports/snapshots/' . $temp->id, [], ['Authorization' => 'Bearer ' . $this->validToken]);
        $this->assertResponseStatus(200);
        $this->seeJson([
            'success' => true,
            'deleted_id' => $temp->id,
        ]);

        $this->notSeeInDatabase('report_snapshots', ['id' => $temp->id]);
    }

    /**
     * Test 12: Endpoint kiểm tra trạng thái sức khỏe /health
     */
    public function test_health_check_endpoint(): void
    {
        $this->get('/health');
        $this->assertResponseStatus(200);
        $this->seeJson(['status' => 'UP', 'port' => 8003]);
    }
}
