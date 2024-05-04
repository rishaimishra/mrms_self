<?php

use Illuminate\Database\Seeder;

class MigrateDeliveredImage extends Seeder
{
    protected $oldProperty;
    protected $property;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\Dpm\Property::whereRaw('id BETWEEN ' . 1 . ' AND ' . 10080 . '')->get();

        foreach ($properties as $oldProperty) {
            if($oldProperty->delivered_image){
                if(!file_exists(storage_path('app/' . $oldProperty->delivered_image))) {
                    $img1Path = 'https://dpm.sigmaventuressl.com' . (Image::url($oldProperty->delivered_image));

                    if($imageCode2 = @file_get_contents($img1Path)) {
                        Storage::disk('local')->put($oldProperty->delivered_image, $imageCode2);

                        $newProperty = \App\Models\Property::find($oldProperty->id);

                        if($newProperty) {
                            $newProperty->delivered_image = $oldProperty->delivered_image;
                            $newProperty->save();
                        }
                    }
                }
            }
        }
    }
}
