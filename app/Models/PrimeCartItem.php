<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrimeCartItem extends Model
{
    protected $fillable = ['prime_cart_id','product_id','sale_log_id','quantity','price'];

    public function prime_cart(){
        return $this->belongsTo(PrimeCart::class, 'prime_cart_id');
    }

    public function sale_log(){
        return $this->belongsTo(SaleLog::class, 'sale_log_id');
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }


}
