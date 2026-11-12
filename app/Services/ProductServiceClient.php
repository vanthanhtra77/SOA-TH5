<?php

namespace App\Services;

class ProductServiceClient
{
    protected string $productServiceUrl;
    protected int $timeout;
    protected bool $mockEnabled;

    /**
     * Dữ liệu giả lập cho môi trường testing / mock độc lập
     */
    protected static array $mockProducts = [
        [
            'id' => 1,
            'name' => 'iPhone 15 Pro Max 256GB',
            'description' => 'Điện thoại cao cấp chip A17 Pro',
            'price' => 29990000.00,
            'quantity' => 15,
        ],
        [
            'id' => 2,
            'name' => 'Laptop Dell XPS 15 9530',
            'description' => 'Laptop doanh nhân Core i7',
            'price' => 45500000.00,
            'quantity' => 8,
        ],
        [
            'id' => 3,
            'name' => 'Bàn phím cơ Keychron K2 Pro',
            'description' => 'Bàn phím cơ không dây',
            'price' => 2150000.00,
            'quantity' => 30,
        ],
        [
            'id' => 4,
            'name' => 'Chuột không dây Logitech MX Master 3S',
            'description' => 'Chuột công thái học cao cấp',
            'price' => 2490000.00,
            'quantity' => 25,
        ],
        [
            'id' => 5,
            'name' => 'Tai nghe Sony WH-1000XM5',
            'description' => 'Tai nghe chống ồn chủ động cao cấp',
            'price' => 7990000.00,
            'quantity' => 20,
        ],
    ];

    public function __construct()
    {
        $this->productServiceUrl = rtrim(env('PRODUCT_SERVICE_URL', 'http://127.0.0.1:8001/products'), '/');
        $this->timeout = (int) env('PRODUCT_SERVICE_TIMEOUT', 5);
        $this->mockEnabled = (bool) env('PRODUCT_SERVICE_MOCK', false);
    }

    protected function isMock(): bool
    {
        return $this->mockEnabled || (bool) env('PRODUCT_SERVICE_MOCK', false) || app()->environment('testing');
    }

    /**
     * Lấy danh sách tất cả các sản phẩm và số lượng tồn kho (GET /products)
     */
    public function getAllProducts(?string $token = null): array
    {
        if ($this->isMock()) {
            return [
                'success' => true,
                'status_code' => 200,
                'products' => self::$mockProducts,
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
            CURLOPT_URL => $this->productServiceUrl,
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
                'products' => [],
                'error' => "Không thể kết nối đến Product Service tại {$this->productServiceUrl}. Lỗi: {$curlError}",
            ];
        }

        $data = json_decode($responseBody, true);

        if ($statusCode === 200 && isset($data['data'])) {
            return [
                'success' => true,
                'status_code' => 200,
                'products' => $data['data'],
                'error' => null,
            ];
        }

        return [
            'success' => false,
            'status_code' => $statusCode ?: 500,
            'products' => [],
            'error' => $data['message'] ?? 'Lỗi lấy dữ liệu sản phẩm từ Product Service.',
        ];
    }
}
