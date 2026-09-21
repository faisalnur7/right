<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    protected $fillable = [
        'user_id',
        'prime_transaction_id',
        'business_day',
        'disburse_date',
        'account_type',
        'account_number',
        'leads_amount',
        'affiliate_amount',
        'subscription_amount',
        'associate_amount',
        'total_amount'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
