<?php

namespace App\Services;

class ReportingService
{
    protected ProductServiceClient $productClient;
    protected OrderServiceClient $orderClient;

    /**
     * Tỷ lệ giá vốn hàng bán mặc định (COGS = 70% doanh thu)
     */
    const COST_RATIO = 0.70;

    public function __construct(ProductServiceClient $productClient, OrderServiceClient $orderClient)
    {
        $this->productClient = $productClient;
        $this->orderClient = $orderClient;
    }

    /**
     * Tổng hợp báo cáo tổng quan (Summary KPI Report)
     */
    public function getSummaryReport(?string $token = null): array
    {
        $productsRes = $this->productClient->getAllProducts($token);
        $ordersRes = $this->orderClient->getAllOrders($token);

        $products = $productsRes['products'] ?? [];
        $orders = $ordersRes['orders'] ?? [];

        // 1. Thống kê hàng tồn kho từ Product Service
        $totalInventory = 0;
        $inventoryValue = 0.0;
        foreach ($products as $p) {
            $qty = (int) ($p['quantity'] ?? 0);
            $price = (float) ($p['price'] ?? 0);
            $totalInventory += $qty;
            $inventoryValue += ($qty * $price);
        }

        // 2. Thống kê hàng bán được, doanh thu, chi phí, lợi nhuận từ Order Service
        $totalSold = 0;
        $totalRevenue = 0.0;
        $cancelledCount = 0;
        $pendingCount = 0;
        $successfulOrdersCount = 0;

        foreach ($orders as $o) {
            $status = strtolower($o['status'] ?? 'pending');
            $qty = (int) ($o['quantity'] ?? 0);
            $orderTotal = (float) ($o['total_price'] ?? 0);

            if ($status === 'cancelled') {
                $cancelledCount++;
                continue;
            }

            if ($status === 'pending') {
                $pendingCount++;
            }

            // Các đơn hàng đã xác nhận, đang giao hoặc hoàn tất được tính vào doanh thu bán hàng
            if (in_array($status, ['completed', 'shipping', 'confirmed', 'pending'], true)) {
                $totalSold += $qty;
                $totalRevenue += $orderTotal;
                $successfulOrdersCount++;
            }
        }

        // 3. Tính toán chi phí và lợi nhuận
        $totalCost = round($totalRevenue * self::COST_RATIO, 2);
        $totalProfit = round($totalRevenue - $totalCost, 2);
        $profitMargin = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 2) : 0.0;

        return [
            'total_inventory' => $totalInventory,
            'inventory_value' => round($inventoryValue, 2),
            'total_sold' => $totalSold,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin,
            'total_orders' => count($orders),
            'active_orders' => $successfulOrdersCount,
            'pending_orders' => $pendingCount,
            'cancelled_orders' => $cancelledCount,
            'data_sources' => [
                'product_service' => $productsRes['success'] ? 'CONNECTED' : 'MOCK/OFFLINE',
                'order_service' => $ordersRes['success'] ? 'CONNECTED' : 'MOCK/OFFLINE',
            ],
        ];
    }

    /**
     * Báo cáo thống kê chi tiết theo Sản phẩm (Report by Product)
     */
    public function getProductReport(?string $token = null): array
    {
        $productsRes = $this->productClient->getAllProducts($token);
        $ordersRes = $this->orderClient->getAllOrders($token);

        $products = $productsRes['products'] ?? [];
        $orders = $ordersRes['orders'] ?? [];

        // Gom nhóm số lượng bán và doanh thu theo product_id
        $salesByProduct = [];
        foreach ($orders as $o) {
            $status = strtolower($o['status'] ?? 'pending');
            if ($status === 'cancelled') continue;

            $pId = (int) ($o['product_id'] ?? 0);
            if (!isset($salesByProduct[$pId])) {
                $salesByProduct[$pId] = [
                    'sold_quantity' => 0,
                    'revenue' => 0.0,
                    'orders_count' => 0,
                ];
            }
            $salesByProduct[$pId]['sold_quantity'] += (int) ($o['quantity'] ?? 0);
            $salesByProduct[$pId]['revenue'] += (float) ($o['total_price'] ?? 0);
            $salesByProduct[$pId]['orders_count']++;
        }

        $report = [];
        foreach ($products as $p) {
            $pId = (int) ($p['id'] ?? 0);
            $currentStock = (int) ($p['quantity'] ?? 0);
            $price = (float) ($p['price'] ?? 0);

            $soldQty = $salesByProduct[$pId]['sold_quantity'] ?? 0;
            $revenue = $salesByProduct[$pId]['revenue'] ?? 0.0;
            $cost = round($revenue * self::COST_RATIO, 2);
            $profit = round($revenue - $cost, 2);

            $stockStatus = 'Còn hàng';
            if ($currentStock === 0) {
                $stockStatus = 'Hết hàng';
            } elseif ($currentStock <= 5) {
                $stockStatus = 'Sắp hết';
            }

            $report[] = [
                'product_id' => $pId,
                'product_name' => $p['name'] ?? 'Không tên',
                'unit_price' => $price,
                'current_stock' => $currentStock,
                'sold_quantity' => $soldQty,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'stock_status' => $stockStatus,
            ];
        }

        // Sắp xếp sản phẩm theo doanh thu giảm dần
        usort($report, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        return $report;
    }

    /**
     * Báo cáo thống kê chi tiết theo Đơn hàng (Report by Order)
     */
    public function getOrderReport(?string $token = null): array
    {
        $ordersRes = $this->orderClient->getAllOrders($token);
        $orders = $ordersRes['orders'] ?? [];

        $statusCount = [
            'pending' => 0,
            'confirmed' => 0,
            'shipping' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        $totalRevenue = 0.0;
        $orderList = [];

        foreach ($orders as $o) {
            $status = strtolower($o['status'] ?? 'pending');
            if (isset($statusCount[$status])) {
                $statusCount[$status]++;
            } else {
                $statusCount[$status] = 1;
            }

            $totalPrice = (float) ($o['total_price'] ?? 0);
            $cost = round($totalPrice * self::COST_RATIO, 2);
            $profit = round($totalPrice - $cost, 2);

            if ($status !== 'cancelled') {
                $totalRevenue += $totalPrice;
            }

            $orderList[] = [
                'id' => $o['id'] ?? 0,
                'user_name' => $o['user_name'] ?? 'student',
                'product_name' => $o['product_name'] ?? 'Sản phẩm',
                'quantity' => (int) ($o['quantity'] ?? 0),
                'total_price' => $totalPrice,
                'estimated_cost' => $cost,
                'estimated_profit' => $status !== 'cancelled' ? $profit : 0.0,
                'status' => $status,
                'created_at' => $o['created_at'] ?? null,
            ];
        }

        $totalOrders = count($orders);
        $successfulOrders = $totalOrders - ($statusCount['cancelled'] ?? 0);
        $avgOrderValue = $successfulOrders > 0 ? round($totalRevenue / $successfulOrders, 2) : 0.0;
        $successRate = $totalOrders > 0 ? round(($successfulOrders / $totalOrders) * 100, 2) : 0.0;

        return [
            'total_orders' => $totalOrders,
            'status_breakdown' => $statusCount,
            'average_order_value' => $avgOrderValue,
            'success_rate' => $successRate,
            'orders' => $orderList,
        ];
    }

    /**
     * Báo cáo toàn diện kết hợp cả 3 phần
     */
    public function getFullReport(?string $token = null): array
    {
        return [
            'summary' => $this->getSummaryReport($token),
            'by_product' => $this->getProductReport($token),
            'by_order' => $this->getOrderReport($token),
            'generated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
