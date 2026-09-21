<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $table = 'subscription_packages'; // Define the table name

    protected $fillable = [
        'name',
        'sub_title',
        'duration',
        'amount',
        'discount',
        'total_commission',
        'tier1_percentage',
        'tier2_percentage',
    ];

    public function features()
    {
        return $this->belongsToMany(PackageFeature::class, 'subscription_package_feature');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'package_users',
            'subscription_package_id',
            'user_id'
        )->withPivot([
            'id',
            'payment_option_id',
            'transaction_number',
            'transaction_mobile_number',
            'amount',
            'assigned_at',
            'expires_at',
            'status',
            'is_verified',
        ]);
    }
    
    

}
