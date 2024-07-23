<?php

namespace App\Models;

use Folklore\Image\Facades\Image;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    const USER_IMAGE = 'user/profile/image';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username', 'image', 'email', 'password', 'ward', 'constituency', 'section', 'chiefdom', 'district', 'province', 'street_name', 'street_number', 'gender', 'is_active', 'assign_district', 'assign_district_id', //'device_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function passwordResetRequest()
    {
        return $this->hasMany('App\Models\PasswordResetRequest');
    }


    protected $appends = ['small_preview', 'large_preview'];

    public function getSmallPreviewAttribute()
    {
        return $this->getImageUrl(100, 100);
    }

    public function getLargePreviewAttribute()
    {
        return $this->getImageUrl(800, 800);
    }

    public function hasImage()
    {
        return $this->image && file_exists($this->getImage());
    }

    public function getImage()
    {
        return storage_path('app/' . $this->image);
    }

    public function getImageUrl($width = 100, $height = 100)
    {
        return $this->hasImage() ? url(Image::url($this->image, $width, $height, ['crop'])) : null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id');
    }

    public function scopeActive($query)
    {
        $query->where('is_active', true);
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function district()
    {
        return $this->belongTo(District::class, 'name');
    }
    public function payments()
    {
        return $this->hasMany(PropertyPayment::class);
    }
}
