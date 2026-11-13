<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Chạy migration: Tạo bảng orders_reports cho Dịch vụ Báo cáo (Bài TH 5 - SOA)
     * Tuân thủ cấu trúc Bảng orders_reports trong Phụ lục trang 1 đề bài:
     * - id: INT (PRIMARY KEY)
     * - order_id: INT (ID của đơn hàng lưu vết để liên kết với dịch vụ quản lý đơn hàng)
     * - total_revenue: DECIMAL(10, 2) (Tổng doanh thu của đơn hàng)
     * - total_cost: DECIMAL(10, 2) (Tổng chi phí nhập sản phẩm trong đơn hàng)
     * - total_profit: DECIMAL(10, 2) (Tổng lợi nhuận = total_revenue - total_cost)
     * - created_at & updated_at: TIMESTAMP
     */
    public function up(): void
    {
        Schema::create('orders_reports', function (Blueprint $table) {
            $table->increments('id'); // INT (PRIMARY KEY) AUTO_INCREMENT
            $table->unsignedInteger('order_id'); // ID đơn hàng lưu vết từ Order Service
            $table->decimal('total_revenue', 12, 2); // Tổng doanh thu
            $table->decimal('total_cost', 12, 2); // Tổng chi phí nhập sản phẩm trong đơn hàng
            $table->decimal('total_profit', 12, 2); // Tổng lợi nhuận = total_revenue - total_cost
            $table->timestamps(); // created_at & updated_at TIMESTAMP
        });
    }

    /**
     * Hoàn tác migration
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_reports');
    }
};
