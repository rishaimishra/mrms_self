<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emergency extends Model
{
    //
    public function EmergencyImages()
    {
        return $this->hasMany(EmergencyImages::class,'emergency_id');
    }
    public function get_user(){
        return $this->belongsTo('App\Models\AdminUser','user_id');
    }
}
