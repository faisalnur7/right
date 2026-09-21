<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrimeTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'source_user_id',
        'type',
        'amount_in',
        'amount_out',
        'description'
    ];

    CONST DIRECT_PRODUCT_USE_COMMISSION = 1;
    CONST INDIRECT_PRODUCT_USE_COMMISSION = 2;

    CONST DIRECT_PRODUCT_PURCHASE_COMMISSION = 3;
    CONST INDIRECT_PRODUCT_PURCHASE_COMMISSION = 4;

    CONST DIRECT_SUBSCRIPTION_COMMISSION = 5;
    CONST INDIRECT_SUBSCRIPTION_COMMISSION = 6;
    CONST ASSOCIATE_COMMISSION = 7;

    CONST DISBURSEMENT = 8;



    public static $commissionTypes = [
        self::DIRECT_PRODUCT_USE_COMMISSION => 'Leads Income',
        self::INDIRECT_PRODUCT_USE_COMMISSION => 'Leads Income',
        self::DIRECT_PRODUCT_PURCHASE_COMMISSION => 'Affiliate Commission - D',
        self::INDIRECT_PRODUCT_PURCHASE_COMMISSION => 'Affiliate Commission - I',
        self::DIRECT_SUBSCRIPTION_COMMISSION => 'Subscription Commission - D',
        self::INDIRECT_SUBSCRIPTION_COMMISSION => 'Subscription Commission - I',
        self::ASSOCIATE_COMMISSION => 'Associate Commission',
        self::DISBURSEMENT => 'Disbursement'
    ];


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function source_user(){
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function disbursements(){
        return $this->hasMany(Disbursement::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeIncome($query)
    {
        return $query->where('amount_in', '>', 0);
    }

}
