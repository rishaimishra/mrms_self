<?php

namespace App\Models;

use App\Notifications\Admin\PasswordResetNotification;
use Folklore\Image\Facades\Image;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;

class AdminUser extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;
    use LogsActivity;

    const ADMIN_PATH = 'back-admin';
    const USER_IMAGE = 'adminuser/profile/image';

    protected $guard_name = 'admin';

    protected $fillable = [
        'first_name', 'last_name', 'ward', 'constituency', 'section', 'chiefdom', 'district', 'province', 'street_name', 'street_number', 'gender', 'email', 'password', 'is_active', 'username', 'media_id', 'position', 'image', 'assign_district_id', 'assign_district'
    ];

    protected static $logAttributes = ['*'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeActive()
    {
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getName()
    {
        return ucwords($this->first_name . ' ' . $this->last_name);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function getImage($width = 100, $height = 100)
    {
        if ($media = $this->media) {

            return $media->getImage($width, $height);
        }

        return null;
    }


    public function hasImage()
    {
        return $this->image && file_exists($this->getAdminImage());
    }

    public function getAdminImage()
    {
        return storage_path('app/' . $this->image);
    }

    public function getImageUrl($width = 100, $height = 100)
    {
        return $this->hasImage() ? url(Image::url($this->image, $width, $height, ['crop'])) : asset('admin/images/user.png');
    }

    public function payments()
    {
        return $this->hasMany(PropertyPayment::class);
    }
}
