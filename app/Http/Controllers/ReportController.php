<?php

namespace App\Http\Controllers;

use App\Models\OrderReport;
use App\Models\ProductReport;
use App\Models\ReportSnapshot;
use App\Services\OrderServiceClient;
use App\Services\ProductServiceClient;
use App\Services\ReportingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    protected ReportingService $reportingService;
    protected ProductServiceClient $productClient;
    protected OrderServiceClient $orderClient;

    public function __construct(
        ReportingService $reportingService,
        ProductServiceClient $productClient,
        OrderServiceClient $orderClient
    ) {
        $this->reportingService = $reportingService;
        $this->productClient = $productClient;
        $this->orderClient = $orderClient;
    }

    // =========================================================================
    // 1. NHÓM API BÁO CÁO THEO SẢN PHẨM (PRODUCT REPORTS)
    // =========================================================================

    /**
     * GET /reports/products
     * Lấy danh sách tất cả các báo cáo theo sản phẩm (từ CSDL riêng: bảng product_reports)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function indexProductReports(Request $request): JsonResponse
    {
        $query = ProductReport::query();

        if ($productId = $request->query('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($orderReportId = $request->query('order_report_id')) {
            $query->where('order_report_id', $orderReportId);
        }

        $reports = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách báo cáo theo sản phẩm thành công.',
            'total' => $reports->count(),
            'data' => $reports,
        ], 200);
    }

    /**
     * GET /reports/products/{id}
     * Lấy chi tiết báo cáo cho một sản phẩm theo ID báo cáo
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function showProductReport($id): JsonResponse
    {
        $report = ProductReport::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy báo cáo sản phẩm có ID = {$id}.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết báo cáo sản phẩm thành công.',
            'data' => $report,
        ], 200);
    }

    /**
     * POST /reports/products
     * Tạo báo cáo sản phẩm mới dựa trên dữ liệu từ dịch vụ quản lý sản phẩm và quản lý đơn hàng
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeProductReport(Request $request): JsonResponse
    {
        $this->validate($request, [
            'product_id' => 'required|integer|min:1',
            'order_report_id' => 'nullable|integer',
        ], [
            'product_id.required' => 'Mã sản phẩm (product_id) là bắt buộc.',
        ]);

        $productId = (int) $request->input('product_id');
        $token = $request->bearerToken();

        // 1. Lấy thông tin sản phẩm từ Product Service (Port 8001)
        $productRes = $this->productClient->getProductById($productId, $token);
        if (!$productRes['success'] && empty($productRes['product'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin sản phẩm từ Product Service: ' . ($productRes['message'] ?? 'Sản phẩm không tồn tại'),
            ], 404);
        }
        $product = $productRes['product'];

        // 2. Lấy dữ liệu bán hàng từ Order Service (Port 8002)
        $ordersRes = $this->orderClient->getAllOrders($token);
        $orders = $ordersRes['orders'] ?? [];

        // 3. Nghiệp vụ tính toán số liệu báo cáo
        $totalSold = 0;
        $revenue = 0.0;

        foreach ($orders as $order) {
            $status = strtolower($order['status'] ?? 'pending');
            if ($status === 'cancelled') continue;

            // Nếu đơn hàng có danh sách items
            if (isset($order['items']) && is_array($order['items'])) {
                foreach ($order['items'] as $item) {
                    if ((int) ($item['product_id'] ?? 0) === $productId) {
                        $totalSold += (int) ($item['quantity'] ?? 0);
                        $revenue += (float) ($item['total_price'] ?? 0);
                    }
                }
            } elseif ((int) ($order['product_id'] ?? 0) === $productId) {
                $totalSold += (int) ($order['quantity'] ?? 0);
                $revenue += (float) ($order['total_price'] ?? $order['total_amount'] ?? 0);
            }
        }

        // Chi phí nhập hàng ước tính (70% doanh thu)
        $cost = round($revenue * 0.70, 2);
        // Lợi nhuận = revenue - cost
        $profit = round($revenue - $cost, 2);

        // 4. Lưu vào CSDL riêng của Reporting Service (bảng product_reports)
        $report = ProductReport::create([
            'order_report_id' => $request->input('order_report_id'),
            'product_id' => $productId,
            'total_sold' => $totalSold,
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Tạo báo cáo cho sản phẩm '{$product['name']}' (ID = {$productId}) thành công.",
            'data' => $report,
        ], 201);
    }

    /**
     * DELETE /reports/products/{id}
     * Xóa báo cáo sản phẩm
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroyProductReport($id): JsonResponse
    {
        $report = ProductReport::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy báo cáo sản phẩm có ID = {$id} để xóa.",
            ], 404);
        }

        $deletedId = $report->id;
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa báo cáo sản phẩm #{$deletedId} thành công.",
            'deleted_id' => $deletedId,
        ], 200);
    }

    // =========================================================================
    // 2. NHÓM API BÁO CÁO THEO ĐƠN HÀNG (ORDER REPORTS)
    // =========================================================================

    /**
     * GET /reports/orders
     * Lấy danh sách tất cả các báo cáo theo đơn hàng (từ CSDL riêng: bảng orders_reports)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function indexOrderReports(Request $request): JsonResponse
    {
        $query = OrderReport::with('productReports');

        if ($orderId = $request->query('order_id')) {
            $query->where('order_id', $orderId);
        }

        $reports = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách báo cáo theo đơn hàng thành công.',
            'total' => $reports->count(),
            'data' => $reports,
        ], 200);
    }

    /**
     * GET /reports/orders/{id}
     * Lấy chi tiết báo cáo cho một đơn hàng
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function showOrderReport($id): JsonResponse
    {
        $report = OrderReport::with('productReports')->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy báo cáo đơn hàng có ID = {$id}.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết báo cáo đơn hàng thành công.',
            'data' => $report,
        ], 200);
    }

    /**
     * POST /reports/orders
     * Tạo báo cáo đơn hàng mới dựa trên dữ liệu từ dịch vụ quản lý đơn hàng
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeOrderReport(Request $request): JsonResponse
    {
        $this->validate($request, [
            'order_id' => 'required|integer|min:1',
        ], [
            'order_id.required' => 'Mã đơn hàng (order_id) là bắt buộc.',
        ]);

        $orderId = (int) $request->input('order_id');
        $token = $request->bearerToken();

        // 1. Lấy thông tin đơn hàng từ Order Service (Port 8002)
        $orderRes = $this->orderClient->getOrderById($orderId, $token);
        if (!$orderRes['success'] && empty($orderRes['order'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin từ Order Service: ' . ($orderRes['message'] ?? 'Đơn hàng không tồn tại'),
            ], 404);
        }
        $order = $orderRes['order'];

        // 2. Nghiệp vụ tính toán: Doanh thu, Chi phí, Lợi nhuận đơn hàng
        $totalRevenue = (float) ($order['total_amount'] ?? $order['total_price'] ?? 0);
        $totalCost = round($totalRevenue * 0.70, 2); // Chi phí nhập sản phẩm ước tính 70%
        $totalProfit = round($totalRevenue - $totalCost, 2); // Lợi nhuận = total_revenue - total_cost

        // 3. Lưu vào CSDL riêng của Reporting Service (bảng orders_reports)
        $orderReport = OrderReport::create([
            'order_id' => $orderId,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
        ]);

        // 4. Nếu đơn hàng có chi tiết các mặt hàng items, tạo các bản ghi product_reports liên kết
        if (isset($order['items']) && is_array($order['items'])) {
            foreach ($order['items'] as $it) {
                $pRevenue = (float) ($it['total_price'] ?? 0);
                $pCost = round($pRevenue * 0.70, 2);
                $pProfit = round($pRevenue - $pCost, 2);

                ProductReport::create([
                    'order_report_id' => $orderReport->id,
                    'product_id' => (int) ($it['product_id'] ?? 1),
                    'total_sold' => (int) ($it['quantity'] ?? 1),
                    'revenue' => $pRevenue,
                    'cost' => $pCost,
                    'profit' => $pProfit,
                ]);
            }
        }

        $orderReport->load('productReports');

        return response()->json([
            'success' => true,
            'message' => "Tạo báo cáo cho đơn hàng #{$orderId} thành công.",
            'data' => $orderReport,
        ], 201);
    }

    /**
     * DELETE /reports/orders/{id}
     * Xóa báo cáo đơn hàng
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroyOrderReport($id): JsonResponse
    {
        $report = OrderReport::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy báo cáo đơn hàng có ID = {$id} để xóa.",
            ], 404);
        }

        $deletedId = $report->id;
        $report->productReports()->delete();
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa báo cáo đơn hàng #{$deletedId} thành công.",
            'deleted_id' => $deletedId,
        ], 200);
    }

    // =========================================================================
    // 3. API BÁO CÁO TỔNG HỢP & SNAPSHOTS CHO WEB DASHBOARD
    // =========================================================================

    /**
     * GET /reports/summary
     * Báo cáo KPI tổng quan thời gian thực (tồn kho, bán được, doanh thu, chi phí, lợi nhuận)
     */
    public function summary(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $summary = $this->reportingService->getSummaryReport($token);

        return response()->json([
            'success' => true,
            'message' => 'Lấy báo cáo tổng quan thành công.',
            'data' => $summary,
        ], 200);
    }

    /**
     * GET /reports/full
     * Báo cáo tích hợp toàn diện cho Dashboard
     */
    public function full(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $full = $this->reportingService->getFullReport($token);

        return response()->json([
            'success' => true,
            'message' => 'Lấy báo cáo tổng thể toàn diện thành công.',
            'data' => $full,
        ], 200);
    }

    /**
     * GET /reports/snapshots
     * Danh sách các bản chụp báo cáo đã lưu trong CSDL riêng
     */
    public function listSnapshots(): JsonResponse
    {
        $snapshots = ReportSnapshot::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách bản lưu báo cáo thành công.',
            'total' => $snapshots->count(),
            'data' => $snapshots,
        ], 200);
    }

    /**
     * POST /reports/snapshots
     * Lưu bản chụp báo cáo tổng hợp
     */
    public function createSnapshot(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $token = $request->bearerToken();
        $authUser = $request->attributes->get('auth_user') ?? [];

        $summary = $this->reportingService->getSummaryReport($token);
        $products = $this->reportingService->getProductReport($token);
        $orders = $this->reportingService->getOrderReport($token);

        $snapshot = ReportSnapshot::create([
            'title' => $request->input('title'),
            'total_inventory' => $summary['total_inventory'],
            'total_sold' => $summary['total_sold'],
            'total_revenue' => $summary['total_revenue'],
            'total_cost' => $summary['total_cost'],
            'total_profit' => $summary['total_profit'],
            'profit_margin' => $summary['profit_margin'],
            'product_summary' => $products,
            'order_summary' => [
                'total_orders' => $orders['total_orders'],
                'status_breakdown' => $orders['status_breakdown'],
                'average_order_value' => $orders['average_order_value'],
                'success_rate' => $orders['success_rate'],
            ],
            'generated_by' => $authUser['UserName'] ?? 'student',
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lưu bản chụp báo cáo thành công.',
            'data' => $snapshot,
        ], 201);
    }
}
