<?php

namespace App\Services;

class OrderServiceClient
{
    protected string $orderServiceUrl;
    protected int $timeout;
    protected bool $mockEnabled;

    /**
     * Dữ liệu giả lập cho môi trường testing / mock độc lập
     */
    protected static array $mockOrders = [
        [
            'id' => 1,
            'user_id' => 1,
            'user_name' => 'student',
            'product_id' => 1,
            'product_name' => 'iPhone 15 Pro Max 256GB',
            'quantity' => 1,
            'unit_price' => 29990000.00,
            'total_price' => 29990000.00,
            'status' => 'completed',
            'created_at' => '2026-11-12 10:00:00',
        ],
        [
            'id' => 2,
            'user_id' => 1,
            'user_name' => 'student',
            'product_id' => 3,
            'product_name' => 'Bàn phím cơ Keychron K2 Pro',
            'quantity' => 2,
            'unit_price' => 2150000.00,
            'total_price' => 4300000.00,
            'status' => 'confirmed',
            'created_at' => '2026-11-12 11:30:00',
        ],
        [
            'id' => 3,
            'user_id' => 1,
            'user_name' => 'student',
            'product_id' => 4,
            'product_name' => 'Chuột không dây Logitech MX Master 3S',
            'quantity' => 1,
            'unit_price' => 2490000.00,
            'total_price' => 2490000.00,
            'status' => 'pending',
            'created_at' => '2026-11-12 14:00:00',
        ],
        [
            'id' => 4,
            'user_id' => 1,
            'user_name' => 'student',
            'product_id' => 5,
            'product_name' => 'Tai nghe Sony WH-1000XM5',
            'quantity' => 1,
            'unit_price' => 7990000.00,
            'total_price' => 7990000.00,
            'status' => 'cancelled', // Đã hủy
            'created_at' => '2026-11-12 15:15:00',
        ],
    ];

    public function __construct()
    {
        $this->orderServiceUrl = rtrim(env('ORDER_SERVICE_URL', 'http://127.0.0.1:8002/orders'), '/');
        $this->timeout = (int) env('ORDER_SERVICE_TIMEOUT', 5);
        $this->mockEnabled = (bool) env('ORDER_SERVICE_MOCK', false);
    }

    protected function isMock(): bool
    {
        return $this->mockEnabled || (bool) env('ORDER_SERVICE_MOCK', false) || app()->environment('testing');
    }

    /**
     * Lấy danh sách tất cả các đơn hàng từ Order Service (GET /orders)
     */
    public function getAllOrders(?string $token = null): array
    {
        if ($this->isMock()) {
            return [
                'success' => true,
                'status_code' => 200,
                'orders' => self::$mockOrders,
                'error' => null,
            ];
        }

        $headers = [
            'Accept: application/json',
            'User-Agent: ReportingService/1.0',
        ];
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->orderServiceUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $responseBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false || !empty($curlError)) {
            return [
                'success' => false,
                'status_code' => 503,
                'orders' => [],
                'error' => "Không thể kết nối đến Order Service tại {$this->orderServiceUrl}. Lỗi: {$curlError}",
            ];
        }

        $data = json_decode($responseBody, true);

        if ($statusCode === 200 && isset($data['data'])) {
            return [
                'success' => true,
                'status_code' => 200,
                'orders' => $data['data'],
                'error' => null,
            ];
        }

        return [
            'success' => false,
            'status_code' => $statusCode ?: 500,
            'orders' => [],
            'error' => $data['message'] ?? 'Lỗi lấy dữ liệu đơn hàng từ Order Service.',
        ];
    }
}
