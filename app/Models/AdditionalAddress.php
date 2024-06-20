<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Spatie\Activitylog\Traits\LogsActivity;

class AdditionalAddress extends Model
{
    //use LogsActivity;
    protected $fillable = [
        'title'
    ];
    public $guarded = [];
    protected $table = 'additional_address';

    // protected static $logAttributes = ['*'];
    // protected static $logOnlyDirty = true;
    // protected static $logName = 'swimmings';
}
