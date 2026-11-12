<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Chạy migration: Tạo bảng report_snapshots cho Dịch vụ Báo cáo & Thống kê (Bài TH 5 - SOA)
     */
    public function up(): void
    {
        Schema::create('report_snapshots', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->string('title', 255); // Tiêu đề kỳ báo cáo
            $table->integer('total_inventory')->default(0); // Tổng số lượng hàng tồn kho
            $table->integer('total_sold')->default(0); // Tổng số lượng hàng đã bán
            $table->decimal('total_revenue', 15, 2)->default(0.00); // Doanh thu
            $table->decimal('total_cost', 15, 2)->default(0.00); // Chi phí
            $table->decimal('total_profit', 15, 2)->default(0.00); // Lợi nhuận
            $table->decimal('profit_margin', 5, 2)->default(0.00); // Tỷ suất lợi nhuận (%)
            $table->json('product_summary')->nullable(); // Chi tiết báo cáo theo sản phẩm
            $table->json('order_summary')->nullable(); // Chi tiết báo cáo theo đơn hàng
            $table->string('generated_by', 255)->default('system'); // Người xuất báo cáo
            $table->text('notes')->nullable(); // Ghi chú báo cáo
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Hoàn tác migration
     */
    public function down(): void
    {
        Schema::dropIfExists('report_snapshots');
    }
};
