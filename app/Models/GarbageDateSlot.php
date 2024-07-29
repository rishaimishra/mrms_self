<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarbageDateSlot extends Model
{
    public function get_user(){
        return $this->belongsTo('App\Models\AdminUser','user_id');
    }
    public function get_slots()
    {
        return $this->belongsTo('App\Models\GarbageDate');
    }
}
