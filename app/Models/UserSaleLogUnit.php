<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSaleLogUnit extends Model
{
    protected $fillable = ['user_id', 'sale_log_id', 'remaining_units'];
}
