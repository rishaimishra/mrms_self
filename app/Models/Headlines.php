<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Headlines extends Model
{
    public function HeadingImages()
    {
        return $this->hasMany(HeadlineImages::class,'headline_id');
    }
}
