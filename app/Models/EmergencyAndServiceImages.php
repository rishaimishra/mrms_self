<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyAndServiceImages extends Model
{
    //
    public function ComplainImages()
    {
        return $this->belongsTo('App\Models\EmergencyAndService');
    }
}
