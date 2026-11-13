<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes - BÀI THỰC HÀNH SỐ 5: Dịch vụ Báo cáo & Thống kê (SOA)
|--------------------------------------------------------------------------
*/

// Trang Web Demo kiểm thử trực quan trên trình duyệt
$router->get('/', function () {
    return view('demo');
});

// Endpoint kiểm tra trạng thái sức khỏe dịch vụ & kết nối SOA
$router->get('/health', function () {
    return response()->json([
        'service' => 'Reporting Service (Bài Thực Hành Số 5)',
        'status' => 'UP',
        'port' => 8003,
        'database' => config('database.default'),
        'connected_services' => [
            'auth_service' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:8000/auth'),
            'product_service' => env('PRODUCT_SERVICE_URL', 'http://127.0.0.1:8001/products'),
            'order_service' => env('ORDER_SERVICE_URL', 'http://127.0.0.1:8002/orders'),
        ],
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
});

// Nhóm API Dịch vụ Báo cáo (Yêu cầu xác thực qua Auth Service - Service 2)
$router->group(['middleware' => 'auth.service'], function () use ($router) {

    // -------------------------------------------------------------
    // 1. BÁO CÁO THEO SẢN PHẨM (PRODUCT REPORTS - BẢNG product_reports)
    // -------------------------------------------------------------
    // GET /reports/products: Lấy danh sách tất cả các báo cáo theo sản phẩm.
    $router->get('/reports/products', 'ReportController@indexProductReports');

    // GET /reports/products/id: Lấy chi tiết báo cáo cho một sản phẩm.
    $router->get('/reports/products/{id}', 'ReportController@showProductReport');

    // POST /reports/products: Tạo báo cáo sản phẩm mới dựa trên dữ liệu từ dịch vụ quản lý sản phẩm và quản lý đơn hàng.
    $router->post('/reports/products', 'ReportController@storeProductReport');

    // DELETE /reports/products/id: Xóa báo cáo sản phẩm.
    $router->delete('/reports/products/{id}', 'ReportController@destroyProductReport');

    // -------------------------------------------------------------
    // 2. BÁO CÁO THEO ĐƠN HÀNG (ORDER REPORTS - BẢNG orders_reports)
    // -------------------------------------------------------------
    // GET /reports/orders: Lấy danh sách tất cả các báo cáo theo đơn hàng.
    $router->get('/reports/orders', 'ReportController@indexOrderReports');

    // GET /reports/orders/id: Lấy chi tiết báo cáo cho một đơn hàng.
    $router->get('/reports/orders/{id}', 'ReportController@showOrderReport');

    // POST /reports/orders: Tạo báo cáo đơn hàng mới dựa trên dữ liệu từ dịch vụ quản lý đơn hàng.
    $router->post('/reports/orders', 'ReportController@storeOrderReport');

    // DELETE /reports/orders/id: Xóa báo cáo đơn hàng.
    $router->delete('/reports/orders/{id}', 'ReportController@destroyOrderReport');

    // -------------------------------------------------------------
    // 3. API BÁO CÁO TỔNG QUAN THỜI GIAN THỰC & BẢN LƯU (DASHBOARD)
    // -------------------------------------------------------------
    // GET /reports/summary: Thống kê KPI tổng quan (tồn kho, hàng bán được, doanh thu, chi phí, lợi nhuận)
    $router->get('/reports/summary', 'ReportController@summary');
    $router->get('/reports/full', 'ReportController@full');
    $router->get('/reports/snapshots', 'ReportController@listSnapshots');
    $router->post('/reports/snapshots', 'ReportController@createSnapshot');
});
