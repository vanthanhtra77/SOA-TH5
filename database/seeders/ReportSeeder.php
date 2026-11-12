<?php

namespace Database\Seeders;

use App\Models\ReportSnapshot;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReportSnapshot::create([
            'title' => 'Báo cáo tổng kết kinh doanh ban đầu (Seed)',
            'total_inventory' => 110,
            'total_sold' => 4,
            'total_revenue' => 38590000.00,
            'total_cost' => 27013000.00,
            'total_profit' => 11577000.00,
            'profit_margin' => 30.00,
            'product_summary' => [
                ['id' => 1, 'name' => 'iPhone 15 Pro Max 256GB', 'stock' => 15, 'sold' => 1, 'revenue' => 29990000.00, 'profit' => 8997000.00],
                ['id' => 2, 'name' => 'Laptop Dell XPS 15 9530', 'stock' => 8, 'sold' => 0, 'revenue' => 0, 'profit' => 0],
                ['id' => 3, 'name' => 'Bàn phím cơ Keychron K2 Pro', 'stock' => 30, 'sold' => 2, 'revenue' => 4300000.00, 'profit' => 1290000.00],
                ['id' => 4, 'name' => 'Chuột không dây Logitech MX Master 3S', 'stock' => 25, 'sold' => 1, 'revenue' => 2490000.00, 'profit' => 747000.00],
            ],
            'order_summary' => [
                'total_orders' => 3,
                'completed' => 1,
                'confirmed' => 1,
                'pending' => 1,
                'cancelled' => 0,
            ],
            'generated_by' => 'student',
            'notes' => 'Bản lưu mẫu phục vụ kiểm thử SOA',
        ]);
    }
}
