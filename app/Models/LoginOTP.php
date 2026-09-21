<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginOTP extends Model
{
    protected $fillable = ['user_id','otp','expires_at'];
}
