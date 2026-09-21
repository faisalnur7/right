<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kyc extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'referer_id',
        'affiliate_id',
        'doc_type',
        'document_file',
        'father',
        'mother',
        'dob',
        'permanent_division_id',
        'permanent_district_id',
        'permanent_police_station_id',
        'permanent_post_office_id',
        'permanent_post_code',
        'present_division_id',
        'present_district_id',
        'present_police_station_id',
        'present_post_office_id',
        'present_post_code',
        'account_type',
        'account_number',
        'emergency_contact',
        'present_address',
        'permanent_address',
        'is_same_address',
        'postal_code',
        'transaction_number',
        'transaction_mobile_number',
        'mobile_number',
        'payment_type',
        'nominee_name',
        'nominee_nid',
        'relation',
        'package',
        'photo',
        'document_number'
    ];

    const NID       = 1;
    const BC        = 2;
    const PASSPORT  = 3;

    const BKASH     = 1;
    const NAGAD     = 2;
    const ROCKET    = 3;

    const SIXMONTH = 1;
    const ONEYEAR = 2;

    const PAYMENT_METHODS = [
        self::BKASH => 'Bkash',
        self::NAGAD => 'Nagad',
        self::ROCKET => 'Rocket',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referer()
    {
        return $this->belongsTo(User::class, 'referer_id');
    }

    public function permanentDivision()
    {
        return $this->belongsTo(Division::class, 'permanent_division_id');
    }

    public function presentDivision()
    {
        return $this->belongsTo(Division::class, 'present_division_id');
    }

    public function permanentDistrict()
    {
        return $this->belongsTo(District::class, 'permanent_district_id');
    }

    public function presentDistrict()
    {
        return $this->belongsTo(District::class, 'present_district_id');
    }

    public function permanentPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'permanent_police_station_id');
    }

    public function presentPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'present_police_station_id');
    }

    public function permanentPostOffice()
    {
        return $this->belongsTo(PostOffice::class, 'permanent_post_office_id');
    }

    public function presentPostOffice()
    {
        return $this->belongsTo(PostOffice::class, 'present_post_office_id');
    }
    
}
