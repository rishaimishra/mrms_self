<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AssiegnedProperties extends Model
{
    use LogsActivity;
    protected $fillable = [
        'property_id', 'user_id','latlong'
    ];

    protected $table = 'assiegned_properties';

   

    public function get_user()
    {
        return $this->belongsTo('App\Models\User', 'officer_id');
    }

    public function get_properties()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
