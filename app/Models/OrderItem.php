<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'sale_log_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
    
    public function sale_log()
    {
        return $this->belongsTo(SaleLog::class,'sale_log_id');
    }

}
