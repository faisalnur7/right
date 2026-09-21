<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReferenceRequest extends Model
{
    protected $fillable = [
        'user_id','previous_reference_user_id','new_reference_user_id','status'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function previousReferenceUser(){
        return $this->belongsTo(User::class,'previous_reference_user_id');
    }

    public function newReferenceUser(){
        return $this->belongsTo(User::class,'new_reference_user_id');
    }
    
}
