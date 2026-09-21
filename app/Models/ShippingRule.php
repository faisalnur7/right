<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    protected $fillable = ['min_subtotal','max_subtotal','shipping_cost','status'];
}
