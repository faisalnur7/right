<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageUser extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_package_id',
        'payment_option_id',
        'transaction_number',
        'transaction_mobile_number',
        'amount',
        'assigned_at',
        'expires_at',
        'status',
        'is_verified',
    ];
    

    public function subscription_package(){
        return $this->belongsTo(SubscriptionPackage::class,'subscription_package_id');
    }

    public function payment_options()
    {
        return $this->belongsTo(PaymentOption::class, 'payment_option_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    
}
