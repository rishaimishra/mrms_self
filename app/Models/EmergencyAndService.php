<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyAndService extends Model
{
    //
    public function ComplainImages()
    {
        return $this->hasMany(EmergencyAndServiceImages::class,'form_and_resourses_id');
    }
    public function get_user(){
        return $this->belongsTo('App\Models\AdminUser','user_id');
    }
}
