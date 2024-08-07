<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyImages extends Model
{
    //
    public function EmergencyImages()
    {
        return $this->belongsTo('App\Models\Emergencies');
    }
}
