<?php

use Folklore\Image\Facades\Image;
use Illuminate\Database\Seeder;

class PropertyImageMigration extends Seeder
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
        $properties = \App\Models\Dpm\Property::whereRaw('id BETWEEN ' . 10081 . ' AND ' . 10488 . '')->get()->toArray();

        foreach ($properties as $oldProperty) {

            $this->oldProperty = $oldProperty;

            $this->getAssessmentImage();
            $this->getMeterImage();
        }
    }

    protected function getAssessmentImage()
    {

        $assessmentss = DB::connection('mysql2')->table('property_assessment_details')
            ->where('property_id', $this->oldProperty['id'])->get()->toArray();

        if (count($assessmentss)) {
            foreach ($assessmentss as $oldAssessment) {
                if($oldAssessment->assessment_images_1) {

                if(!file_exists(storage_path('app/' . $oldAssessment->assessment_images_1))) {
                    $img1Path = 'https://dpm.sigmaventuressl.com' . (Image::url($oldAssessment->assessment_images_1));

                    if($imageCode = @file_get_contents($img1Path)) {
                        Storage::disk('local')->put($oldAssessment->assessment_images_1, $imageCode);
                    }
                }
            }

            if($oldAssessment->assessment_images_2) {
                if(!file_exists(storage_path('app/' . $oldAssessment->assessment_images_2))) {
                    $img1Path2 = 'https://dpm.sigmaventuressl.com' . (Image::url($oldAssessment->assessment_images_2));

                    if($imageCode2 = @file_get_contents($img1Path2)) {
                        Storage::disk('local')->put($oldAssessment->assessment_images_2, $imageCode2);
                    }
                }
            }
        }
    }
    }

    protected function getMeterImage()
    {
        $oldRegistryMeters = DB::connection('mysql2')->table('registry_meters')
            ->where('property_id', $this->oldProperty['id'])->get()->toArray();

        if (count($oldRegistryMeters)) {
            foreach ($oldRegistryMeters as $oldRegisterMeter) {

                if($oldRegisterMeter->image) {
                    if(!file_exists(storage_path('app/' . $oldRegisterMeter->image))) {
                        $img1Path1 = 'https://dpm.sigmaventuressl.com' . (Image::url($oldRegisterMeter->image));

                        if($imageCode2 = @file_get_contents($img1Path1)) {
                            Storage::disk('local')->put($oldRegisterMeter->image, $imageCode2);
                        }
                    }
                }

            }
        }

    }
}
