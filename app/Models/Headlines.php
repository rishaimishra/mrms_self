<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Headlines extends Model
{
    protected $appends = [
        'headline_image_path'
    ];
    public function HeadingImages()
    {
        return $this->hasMany(HeadlineImages::class,'headline_id');
    }
    public function getHeadlineImagePathAttribute()
    {
        return asset('storage/'.$this->headline_img);
    }
}
