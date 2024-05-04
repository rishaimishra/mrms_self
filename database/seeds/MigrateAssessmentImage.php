<?php

use Illuminate\Database\Seeder;

class MigrateAssessmentImage extends Seeder
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
        $properties = \App\Models\Property::whereRaw('id BETWEEN ' . 1 . ' AND ' . 10080 . '')->get()->toArray();

        foreach ($properties as $newProperty) {

            $this->property = $newProperty;

            $this->updateAssessmentImage();
          //  $this->updateMeterImage();
        }
    }

    protected function updateAssessmentImage()
    {
        $assessmentss = DB::table('property_assessment_details')
            ->where('property_id', $this->property['id'])->get()->toArray();

        if (count($assessmentss)) {
            foreach ($assessmentss as $key => $oldAssessment) {
                if($oldAssessment->assessment_images_1) {
                    if(!file_exists(storage_path('app/' . $oldAssessment->assessment_images_1))) {
                        $newProperty = \App\Models\PropertyAssessmentDetail::where('property_id', $this->property['id'])->whereYear('created_at', 2020)->first();
                        if($newProperty) {
                            $newProperty->assessment_images_1 = $oldAssessment->assessment_images_1;
                            $newProperty->save();
                        }
                    }
                }

                if($oldAssessment->assessment_images_2) {
                    if(!file_exists(storage_path('app/' . $oldAssessment->assessment_images_2))) {
                        $newProperty = \App\Models\PropertyAssessmentDetail::where('property_id', $this->property['id'])->whereYear('created_at', 2020)->first();
                        if($newProperty) {
                            $newProperty->assessment_images_2 = $oldAssessment->assessment_images_2;
                            $newProperty->save();
                        }
                    }
                }
            }
        }
    }

    protected function updateMeterImage()
    {
        $oldRegistryMeters = DB::connection('mysql2')->table('registry_meters')
            ->where('property_id', $this->oldProperty['id'])->get()->toArray();

        if (count($oldRegistryMeters)) {
            foreach ($oldRegistryMeters as $oldRegisterMeter) {

                if($oldRegisterMeter->image) {
                    if(file_exists(storage_path('app/' . $oldRegisterMeter->image))) {
                        $newProperty = \App\Models\RegistryMeter::where('property_id', $this->oldProperty['id'])->where('id', $oldRegisterMeter->id)->first();

                        if(!$newProperty) {
                            $newProperty = \App\Models\RegistryMeter::where('property_id', $this->oldProperty['id'])->first();
                            if($newProperty) {
                                $newProperty->image = $oldRegisterMeter->image;
                                $newProperty->save();
                            }
                        }

                    }
                }
            }
        }
    }
}
