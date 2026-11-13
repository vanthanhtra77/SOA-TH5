<?php

namespace Database\Seeders;

use App\Models\OrderReport;
use App\Models\ProductReport;
use App\Models\ReportSnapshot;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Bảng orders_reports và product_reports theo chuẩn Phụ lục
        $orderReportsData = [
            [
                'order_report' => [
                    'order_id' => 1,
                    'total_revenue' => 29990000.00,
                    'total_cost' => 20993000.00,
                    'total_profit' => 8997000.00,
                ],
                'products' => [
                    [
                        'product_id' => 1,
                        'total_sold' => 1,
                        'revenue' => 29990000.00,
                        'cost' => 20993000.00,
                        'profit' => 8997000.00,
                    ]
                ]
            ],
            [
                'order_report' => [
                    'order_id' => 2,
                    'total_revenue' => 6790000.00,
                    'total_cost' => 4753000.00,
                    'total_profit' => 2037000.00,
                ],
                'products' => [
                    [
                        'product_id' => 3,
                        'total_sold' => 2,
                        'revenue' => 4300000.00,
                        'cost' => 3010000.00,
                        'profit' => 1290000.00,
                    ],
                    [
                        'product_id' => 4,
                        'total_sold' => 1,
                        'revenue' => 2490000.00,
                        'cost' => 1743000.00,
                        'profit' => 747000.00,
                    ],
                ]
            ],
            [
                'order_report' => [
                    'order_id' => 3,
                    'total_revenue' => 2490000.00,
                    'total_cost' => 1743000.00,
                    'total_profit' => 747000.00,
                ],
                'products' => [
                    [
                        'product_id' => 4,
                        'total_sold' => 1,
                        'revenue' => 2490000.00,
                        'cost' => 1743000.00,
                        'profit' => 747000.00,
                    ]
                ]
            ]
        ];

        foreach ($orderReportsData as $row) {
            $or = OrderReport::create($row['order_report']);
            foreach ($row['products'] as $p) {
                $p['order_report_id'] = $or->id;
                ProductReport::create($p);
            }
        }

        // 2. Báo cáo độc lập cho từng sản phẩm
        ProductReport::create([
            'order_report_id' => null,
            'product_id' => 2, // Laptop Dell
            'total_sold' => 0,
            'revenue' => 0.00,
            'cost' => 0.00,
            'profit' => 0.00,
        ]);
        ProductReport::create([
            'order_report_id' => null,
            'product_id' => 5, // Màn hình Dell
            'total_sold' => 0,
            'revenue' => 0.00,
            'cost' => 0.00,
            'profit' => 0.00,
        ]);

        // 3. Snapshot lưu trữ
        ReportSnapshot::create([
            'title' => 'Báo cáo chốt số liệu tháng 11',
            'total_inventory' => 165,
            'total_sold' => 4,
            'total_revenue' => 36780000.00,
            'total_cost' => 25746000.00,
            'total_profit' => 11034000.00,
            'profit_margin' => 30.00,
            'generated_by' => 'student',
            'notes' => 'Chốt số liệu kinh doanh ban đầu theo chuẩn SOA',
        ]);
    }
}
