<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomineeInfo extends Model
{
    protected $fillable = ['user_id','nominee_name','relation','doc_type','nominee_nid','nominee_document'];
}
