<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActiveRequest extends Model
{
    protected $fillable = [
        'user_id','product_id','sale_log_id','present_reference_user_id','new_reference_id','status'
    ];

    const REJECTED = 0;
    const PENDING = 1;
    const APPROVED = 2;

    const USER_ACTIVE_STATUS = [
        self::REJECTED => 'Rejected',
        self::PENDING => 'Pending',
        self::APPROVED => 'Approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function presentReference()
    {
        return $this->belongsTo(User::class, 'present_reference_user_id');
    }

    public function newReference()
    {
        return $this->belongsTo(User::class, 'new_reference_id');
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
