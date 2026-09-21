<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'admin_user_id',
        'reference_no',
        'purchase_date',
        'sub_total',
        'discount_value',
        'delivery_charge',
        'total_amount',
        'attachment',
        'status',
    ];

    /**
     * Get the supplier associated with the purchase.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    /**
     * Get the admin user who created the purchase.
     */
    public function adminUser()
    {
        return $this->belongsTo(Admin::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
