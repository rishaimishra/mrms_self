<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadlineImages extends Model
{
    public function Headline_Images()
    {
        return $this->belongsTo('App\Models\Headlines');
    }
}
