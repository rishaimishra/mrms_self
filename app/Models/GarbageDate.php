<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarbageDate extends Model
{
    public function get_user(){
        return $this->belongsTo('App\Models\AdminUser','user_id');
    }
  
        
    public function get_slot(){
        return $this->hasMany('App\Models\GarbageDateSlot', 'garbage_date_id');
    }
        
    
}
