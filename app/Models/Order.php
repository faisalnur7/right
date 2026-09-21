<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    const PENDING = 1;
    const CONFIRMED = 2;
    const REJECTED = 3;
    const PROCESSING = 4;
    const SHIPPED = 5;
    const COMPLETED = 6;

    const ORDER_STATUS = [
        self::PENDING => "Pending",
        self::CONFIRMED => "Confirmed",
        // self::REJECTED => "Rejected",
        self::PROCESSING => "Packed",
        self::SHIPPED => "Shipped",
        self::COMPLETED => "Delivered",
    ];

    const ORDER_STATUS_FILTER = [
        self::PENDING => "Pending",
        self::CONFIRMED => "Confirmed",
        self::REJECTED => "Rejected",
        self::PROCESSING => "Packed",
        self::SHIPPED => "Shipped",
        self::COMPLETED => "Delivered",
    ];

    const ORDER_STATUS_PRIME = [
        self::PENDING => [
            'label' => 'Pending',
            'color' => '#FFA500', // Orange
        ],
        self::CONFIRMED => [
            'label' => 'Confirmed',
            'color' => '#007BFF', // Blue
        ],
        self::REJECTED => [
            'label' => 'Rejected',
            'color' => '#DC3545', // Red
        ],
        self::PROCESSING => [
            'label' => 'Packed',
            'color' => '#17A2B8', // Teal
        ],
        self::SHIPPED => [
            'label' => 'Shipped',
            'color' => '#6F42C1', // Purple
        ],
        self::COMPLETED => [
            'label' => 'Delivered',
            'color' => '#28A745', // Green
        ],
    ];

    protected $fillable = [
        'user_id',
        'billing_address',
        'shipping_address',
        'payment_option_id',
        'payment_account_number',
        'transaction_id',
        'sender_phone_number',
        'status',
        'subtotal',
        'shipping_charge',
        'total',
        'order_tracking_number',
        'start_processing_at',
        'packaged_at',
        'shipped_at',
        'completed_at',
        'is_cod'
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'start_processing_at' => 'datetime',
        'packaged_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}
