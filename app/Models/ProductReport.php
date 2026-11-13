<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReport extends Model
{
    /**
     * Tên bảng trong CSDL riêng của Reporting Service
     *
     * @var string
     */
    protected $table = 'product_reports';

    /**
     * Khóa chính của bảng
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Các trường cho phép gán hàng loạt (Mass Assignable)
     *
     * @var array
     */
    protected $fillable = [
        'order_report_id',
        'product_id',
        'total_sold',
        'revenue',
        'cost',
        'profit',
    ];

    /**
     * Ép kiểu dữ liệu cho các trường
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'order_report_id' => 'integer',
        'product_id' => 'integer',
        'total_sold' => 'integer',
        'revenue' => 'float',
        'cost' => 'float',
        'profit' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ: Thuộc về một báo cáo đơn hàng (nếu được tạo từ đơn hàng)
     */
    public function orderReport()
    {
        return $this->belongsTo(OrderReport::class, 'order_report_id', 'id');
    }
}
