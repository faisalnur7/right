<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['type', 'purchase_id', 'sale_id', 'amount', 'transaction_date'];

    const TYPE_PURCHASE = 1;
    const TYPE_SALE = 2;

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function related()
    {
        return $this->type === self::TYPE_PURCHASE 
            ? $this->purchase 
            : $this->sale;
    }

    public static function typeList(): array
    {
        return [
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_SALE => 'Sale',
        ];
    }
}
