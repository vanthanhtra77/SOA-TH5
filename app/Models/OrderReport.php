<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReport extends Model
{
    /**
     * Tên bảng trong CSDL riêng của Reporting Service
     *
     * @var string
     */
    protected $table = 'orders_reports';

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
        'order_id',
        'total_revenue',
        'total_cost',
        'total_profit',
    ];

    /**
     * Ép kiểu dữ liệu cho các trường
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'order_id' => 'integer',
        'total_revenue' => 'float',
        'total_cost' => 'float',
        'total_profit' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ: Báo cáo đơn hàng có thể liên kết với các chi tiết báo cáo sản phẩm
     */
    public function productReports()
    {
        return $this->hasMany(ProductReport::class, 'order_report_id', 'id');
    }
}
