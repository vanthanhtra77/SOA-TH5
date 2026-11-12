<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes - BÀI THỰC HÀNH SỐ 5: Dịch vụ Báo cáo & Thống kê (SOA)
|--------------------------------------------------------------------------
*/

// Giao diện Web Dashboard tương tác trực quan trên trình duyệt
$router->get('/', function () {
    return view('demo');
});

// Endpoint kiểm tra trạng thái sức khỏe dịch vụ & kết nối liên dịch vụ SOA
$router->get('/health', function () {
    return response()->json([
        'service' => 'Reporting & Analytics Service (Bài Thực Hành Số 5)',
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

// Nhóm API Thống kê & Báo cáo (Yêu cầu xác thực qua Auth Service - Service 2)
$router->group(['middleware' => 'auth.service'], function () use ($router) {
    // 1. Thống kê KPI tổng quan (Hàng tồn, Hàng bán được, Doanh thu, Chi phí, Lợi nhuận)
    $router->get('/reports/summary', 'ReportController@summary');

    // 2. Báo cáo thống kê chi tiết theo từng sản phẩm
    $router->get('/reports/products', 'ReportController@products');

    // 3. Báo cáo thống kê chi tiết theo đơn hàng
    $router->get('/reports/orders', 'ReportController@orders');

    // 4. Báo cáo tích hợp toàn diện
    $router->get('/reports/full', 'ReportController@full');

    // 5. Quản lý lưu trữ bản chụp báo cáo (Snapshots) vào CSDL riêng
    $router->get('/reports/snapshots', 'ReportController@listSnapshots');
    $router->get('/reports/snapshots/{id}', 'ReportController@showSnapshot');
    $router->post('/reports/snapshots', 'ReportController@createSnapshot');
    $router->delete('/reports/snapshots/{id}', 'ReportController@destroySnapshot');
});



