<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'total_commission',
        'tier1_percentage',
        'tier2_percentage',
        'total_use_commission'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sale_log')
                    ->withPivot('unit', 'price')
                    ->withTimestamps()->latest();
    }
}
