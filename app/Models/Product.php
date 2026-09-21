<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'unit',
        'purchase_price',
        'price',
        'affiliate_price',
        'stock',
        'description',
        'short_description',
        'image',
        'gallery_images',
        'status',
        'featured',
        'weight',
        'meta',
        'total_commission',
        'tier1_percentage',
        'tier2_percentage',
        'total_use_commission',
        'associate_commission'
    ];
    

    protected $casts = [
        'gallery_images' => 'array',
        'meta' => 'array',
        'featured' => 'boolean',
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subCategory() {
        return $this->belongsTo(SubCategory::class);
    }

    public function attributes() {
        return $this->belongsToMany(Attribute::class)->withPivot('value');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    
    public function saleLogs()
    {
        return $this->belongsToMany(SaleLog::class, 'product_sale_log')
                    ->withPivot('unit', 'price')
                    ->withTimestamps();
    }   
    
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
