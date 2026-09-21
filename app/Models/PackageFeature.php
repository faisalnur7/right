<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    protected $fillable = ['name', 'order'];

    public function subscriptionPackages()
    {
        return $this->belongsToMany(SubscriptionPackage::class, 'subscription_package_feature');
    }

}
