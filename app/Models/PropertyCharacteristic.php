<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class PropertyCharacteristic extends Model
{
    //use LogsActivity;
    protected $fillable = [
        'name'
    ];

    protected $table = 'property_characteristics';

    // protected static $logAttributes = ['*'];
    // protected static $logOnlyDirty = true;
    // protected static $logName = 'swimmings';
}
