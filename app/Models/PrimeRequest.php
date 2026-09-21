<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrimeRequest extends Model
{
    protected $fillable = ['requester_id', 'prime_id', 'status'];

    const PENDING = 1;
    const ACCEPTED = 2;
    const REJECTED = 3;
    
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function prime()
    {
        return $this->belongsTo(User::class, 'prime_id');
    }

    public function packageUser(){
        return $this->belongsTo(PackageUser::class,'requester_id');
    }
}
