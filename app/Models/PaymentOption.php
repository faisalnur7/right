<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOption extends Model
{
    protected $fillable = ['name', 'account_number', 'order', 'logo', 'zoomable'];

    protected function casts(): array
    {
        return [
            'zoomable' => 'boolean',
        ];
    }
}
