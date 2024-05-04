<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{


    public $incrementing = false;

    protected $fillable = [
        'property_id',
        'mobile_number',
        'payee_name',
        'payment_id',
        'payment_mode',
        'amount',
        'amount_in_le',
        'is_completed',
        'physical_receipt_image'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    protected $appends = [
        'physical_receipt_image_path'
    ];

    public function getPhysicalReceiptImagePathAttribute()
    {
        return $this->getPhysicalReceiptImagePath(800, 800);
    }

    public function getPhysicalReceiptImage()
    {
        return storage_path('app/' . $this->physical_receipt_image);
    }

    public function hasPhysicalReceiptImage()
    {
        return (bool) $this->physical_receipt_image && file_exists($this->getPhysicalReceiptImage());
    }

    public function getPhysicalReceiptImagePath($width = 100, $height = 100)
    {
        return $this->hasPhysicalReceiptImage() ? url(Image::url($this->physical_receipt_image, $width, $height, [])) : null;   
    }
}
