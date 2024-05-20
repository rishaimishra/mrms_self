<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Folklore\Image\Facades\Image;

class UnfinishedProperty extends Model
{
    use LogsActivity;

    const UNFINISHED_PROPERTY_IMAGE = 'property/unfinished/image';

    protected $fillable = [
        'reason', 
        'unfinished_property_image',
        'unfinished_property_lat',
        'unfinished_property_long',
        'enumerator'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'unfinished-property-details';

    protected $hidden = [
        'pivot'
    ];

    protected $appends = [
        'unfinished_property_image_path',
    ];

    //unfinished images
    public function getUnfinishedImagePathAttribute()
    {
        return $this->getUnfinishedImagePath(800, 800);
    }
    public function getUnfinishdImage()
    {
        return storage_path('app/' . $this->unfinished_property_image);
    }
    public function hasUnfinishedImage()
    {
        return (bool) $this->unfinished_property_image && file_exists($this->getUnfinishdImage());
    }
    public function getUnfinishedImagePath($width = 800, $height = 800)
    {
        return $this->hasUnfinishedImage() ? url(Image::url($this->unfinished_property_image, $width, $height, [])) : null;   
    }
}
