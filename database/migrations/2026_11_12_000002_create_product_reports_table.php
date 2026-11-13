<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Chạy migration: Tạo bảng product_reports cho Dịch vụ Báo cáo (Bài TH 5 - SOA)
     * Tuân thủ cấu trúc Bảng product_reports trong Phụ lục trang 2 đề bài:
     * - id: INT (PRIMARY KEY)
     * - order_report_id: INT (ID đơn hàng liên kết với bảng orders_reports)
     * - product_id: INT (ID sản phẩm lưu vết để liên kết với dịch vụ quản lý sản phẩm)
     * - total_sold: INT (Tổng số lượng sản phẩm đã bán)
     * - revenue: DECIMAL(10, 2) (Doanh thu từ sản phẩm)
     * - cost: DECIMAL(10, 2) (Chi phí nhập sản phẩm)
     * - profit: DECIMAL(10, 2) (Lợi nhuận = revenue - cost)
     * - created_at & updated_at: TIMESTAMP
     */
    public function up(): void
    {
        Schema::create('product_reports', function (Blueprint $table) {
            $table->increments('id'); // INT (PRIMARY KEY) AUTO_INCREMENT
            $table->unsignedInteger('order_report_id')->nullable(); // Liên kết với bảng orders_reports
            $table->unsignedInteger('product_id'); // ID sản phẩm lưu vết từ Product Service
            $table->integer('total_sold')->default(0); // Tổng số lượng sản phẩm đã bán
            $table->decimal('revenue', 12, 2)->default(0); // Doanh thu từ sản phẩm
            $table->decimal('cost', 12, 2)->default(0); // Chi phí nhập sản phẩm
            $table->decimal('profit', 12, 2)->default(0); // Lợi nhuận = revenue - cost
            $table->timestamps(); // created_at & updated_at TIMESTAMP

            $table->foreign('order_report_id')->references('id')->on('orders_reports')->onDelete('cascade');
        });
    }

    /**
     * Hoàn tác migration
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reports');
    }
};
