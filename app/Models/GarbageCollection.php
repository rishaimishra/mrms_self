<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarbageCollection extends Model
{
    public function get_user(){
        return $this->belongsTo('App\Models\AdminUser','user_id');
    }
}
