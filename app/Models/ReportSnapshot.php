<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSnapshot extends Model
{
    /**
     * Tên bảng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'report_snapshots';

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
        'title',
        'total_inventory',
        'total_sold',
        'total_revenue',
        'total_cost',
        'total_profit',
        'profit_margin',
        'product_summary',
        'order_summary',
        'generated_by',
        'notes',
    ];

    /**
     * Ép kiểu dữ liệu cho các trường
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'total_inventory' => 'integer',
        'total_sold' => 'integer',
        'total_revenue' => 'float',
        'total_cost' => 'float',
        'total_profit' => 'float',
        'profit_margin' => 'float',
        'product_summary' => 'array',
        'order_summary' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
