<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadlineImages extends Model
{
    protected $fillable = [
        'images'];
    protected $appends = [
        'heading_image_path'
    ];
    public function Headline_Images()
    {
        return $this->belongsTo('App\Models\Headlines');
    }
  

    public function getHeadingImagePathAttribute()
    {
        return asset('storage/'.$this->images);
    }
}
