<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'district_id',
        'police_station_id',
        'post_office_id',
        'address',
        'city',
        'zip',
        'user_id',
        'type'
    ];

    const BILLING_ADDRESS_TYPE = 1;
    const SHIPPING_ADDRESS_TYPE = 2;

    // Define relationship back to User (optional but recommended)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function district(){
        return $this->belongsTo(District::class,'district_id');
    }

    public function police_station(){
        return $this->belongsTo(PoliceStation::class,'police_station_id');
    }

    public function post_office(){
        return $this->belongsTo(PostOffice::class,'post_office_id');
    }
}
