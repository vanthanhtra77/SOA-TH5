<?php

namespace App\Http\Controllers;

use App\Models\ReportSnapshot;
use App\Services\ReportingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportingService $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * GET /reports/summary
     * Báo cáo KPI tổng quan: Hàng tồn, Hàng bán được, Doanh thu, Chi phí, Lợi nhuận
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
     * GET /reports/products
     * Báo cáo chi tiết theo từng sản phẩm
     */
    public function products(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $productsReport = $this->reportingService->getProductReport($token);

        return response()->json([
            'success' => true,
            'message' => 'Lấy báo cáo theo sản phẩm thành công.',
            'total_products' => count($productsReport),
            'data' => $productsReport,
        ], 200);
    }

    /**
     * GET /reports/orders
     * Báo cáo chi tiết theo đơn hàng
     */
    public function orders(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $ordersReport = $this->reportingService->getOrderReport($token);

        return response()->json([
            'success' => true,
            'message' => 'Lấy báo cáo theo đơn hàng thành công.',
            'data' => $ordersReport,
        ], 200);
    }

    /**
     * GET /reports/full
     * Báo cáo tích hợp toàn diện
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
     * Danh sách các bản chụp báo cáo đã lưu trong CSDL riêng của Reporting Service
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
     * GET /reports/snapshots/{id}
     * Chi tiết một bản chụp báo cáo đã lưu
     */
    public function showSnapshot($id): JsonResponse
    {
        $snapshot = ReportSnapshot::find($id);

        if (!$snapshot) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy bản lưu báo cáo có ID = {$id}.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết bản lưu báo cáo thành công.',
            'data' => $snapshot,
        ], 200);
    }

    /**
     * POST /reports/snapshots
     * Tạo và lưu lại một bản chụp báo cáo (Snapshot) vào CSDL riêng
     */
    public function createSnapshot(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'title.required' => 'Tiêu đề bản lưu báo cáo là bắt buộc.',
        ]);

        $token = $request->bearerToken();
        $authUser = $request->attributes->get('auth_user') ?? [];

        // Lấy dữ liệu báo cáo thời gian thực từ các Service
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
            'message' => 'Lưu bản chụp báo cáo vào cơ sở dữ liệu thành công.',
            'data' => $snapshot,
        ], 201);
    }

    /**
     * DELETE /reports/snapshots/{id}
     * Xóa một bản chụp báo cáo
     */
    public function destroySnapshot($id): JsonResponse
    {
        $snapshot = ReportSnapshot::find($id);

        if (!$snapshot) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy bản lưu báo cáo có ID = {$id} để xóa.",
            ], 404);
        }

        $deletedId = $snapshot->id;
        $snapshot->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa bản lưu báo cáo #{$deletedId} thành công.",
            'deleted_id' => $deletedId,
        ], 200);
    }
}
