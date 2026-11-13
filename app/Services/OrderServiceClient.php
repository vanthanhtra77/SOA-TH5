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
            'customer_name' => 'Nguyễn Văn Sinh Viên',
            'customer_email' => 'student@example.com',
            'total_amount' => 29990000.00,
            'total_price' => 29990000.00,
            'status' => 'completed',
            'product_id' => 1,
            'quantity' => 1,
            'created_at' => '2026-11-12 10:00:00',
            'items' => [
                [
                    'id' => 1,
                    'order_id' => 1,
                    'product_id' => 1,
                    'product_name' => 'iPhone 15 Pro Max 256GB',
                    'quantity' => 1,
                    'unit_price' => 29990000.00,
                    'total_price' => 29990000.00,
                ]
            ]
        ],
        [
            'id' => 2,
            'customer_name' => 'Trần Thị Thu Hà',
            'customer_email' => 'thuha@example.com',
            'total_amount' => 4300000.00,
            'total_price' => 4300000.00,
            'status' => 'confirmed',
            'product_id' => 3,
            'quantity' => 2,
            'created_at' => '2026-11-12 11:30:00',
            'items' => [
                [
                    'id' => 2,
                    'order_id' => 2,
                    'product_id' => 3,
                    'product_name' => 'Bàn phím cơ Keychron K2 Pro',
                    'quantity' => 2,
                    'unit_price' => 2150000.00,
                    'total_price' => 4300000.00,
                ]
            ]
        ],
        [
            'id' => 3,
            'customer_name' => 'Lê Hoàng Nam',
            'customer_email' => 'namle@example.com',
            'total_amount' => 2490000.00,
            'total_price' => 2490000.00,
            'status' => 'pending',
            'product_id' => 4,
            'quantity' => 1,
            'created_at' => '2026-11-12 14:00:00',
            'items' => [
                [
                    'id' => 3,
                    'order_id' => 3,
                    'product_id' => 4,
                    'product_name' => 'Chuột không dây Logitech MX Master 3S',
                    'quantity' => 1,
                    'unit_price' => 2490000.00,
                    'total_price' => 2490000.00,
                ]
            ]
        ],
        [
            'id' => 4,
            'customer_name' => 'Phạm Văn Hùng',
            'customer_email' => 'hung@example.com',
            'total_amount' => 7990000.00,
            'total_price' => 7990000.00,
            'status' => 'cancelled',
            'product_id' => 6,
            'quantity' => 1,
            'created_at' => '2026-11-12 15:15:00',
            'items' => [
                [
                    'id' => 4,
                    'order_id' => 4,
                    'product_id' => 6,
                    'product_name' => 'Tai nghe Sony WH-1000XM5',
                    'quantity' => 1,
                    'unit_price' => 7990000.00,
                    'total_price' => 7990000.00,
                ]
            ]
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

    /**
     * Lấy chi tiết một đơn hàng theo ID từ Order Service (GET /orders/{id})
     */
    public function getOrderById(int $id, ?string $token = null): array
    {
        if ($this->isMock()) {
            foreach (self::$mockOrders as $o) {
                if ($o['id'] === $id) {
                    return ['success' => true, 'order' => $o, 'status_code' => 200];
                }
            }
            return ['success' => false, 'order' => null, 'status_code' => 404, 'message' => 'Không tìm thấy đơn hàng.'];
        }

        $url = "{$this->orderServiceUrl}/{$id}";
        $headers = ['Accept: application/json', 'User-Agent: ReportingService/1.0'];
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $responseBody = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($responseBody, true);
        if ($statusCode === 200 && isset($data['data'])) {
            return ['success' => true, 'order' => $data['data'], 'status_code' => 200];
        }

        return ['success' => false, 'order' => null, 'status_code' => $statusCode ?: 404, 'message' => $data['message'] ?? 'Lỗi lấy thông tin đơn hàng'];
    }

    /**
     * Lấy danh sách tất cả các chi tiết mặt hàng từ Order Service (GET /order_items)
     */
    public function getAllOrderItems(?string $token = null): array
    {
        if ($this->isMock()) {
            $allItems = [];
            foreach (self::$mockOrders as $o) {
                if (isset($o['items'])) {
                    foreach ($o['items'] as $it) {
                        $allItems[] = $it;
                    }
                }
            }
            return ['success' => true, 'items' => $allItems, 'status_code' => 200];
        }

        $baseUrl = dirname($this->orderServiceUrl);
        $url = "{$baseUrl}/order_items";
        $headers = ['Accept: application/json', 'User-Agent: ReportingService/1.0'];
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $responseBody = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($responseBody, true);
        if ($statusCode === 200 && isset($data['data'])) {
            return ['success' => true, 'items' => $data['data'], 'status_code' => 200];
        }

        return ['success' => false, 'items' => [], 'status_code' => $statusCode ?: 500];
    }
}
