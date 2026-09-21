<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UseProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'user_id',
        'product_id',
        'sale_log_id',
        'quantity',
        'price',
        'total',
        'status',
        'payment_status',
        'transaction_id',
        'transaction_mobile_number',
        'used_at',
        'remarks',
        'validity',
        'unit_per_day'
    ];

    const USED_PRODUCT = 0;
    const IN_USE_PRODUCT = 1;

    // Relationships
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleLog()
    {
        return $this->belongsTo(SaleLog::class);
    }
}
