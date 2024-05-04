<?php

use Illuminate\Database\Seeder;

class MigrateAssessmentDeliveryNote extends Seeder
{
    protected $oldProperty;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\Dpm\Property::whereRaw('id BETWEEN ' . 10081 . ' AND ' . 10488 . '')->get();

        foreach ($properties as $oldProperty) {

            $this->oldProperty = $oldProperty;

            $this->migrateDeliveredImage();
        }
    }

    public function migrateDeliveredImage() {

        $assessments = DB::connection('mysql2')->table('property_assessment_details')
            ->where('property_id', $this->oldProperty->id)->whereYear('created_at', 2020)->get()->toArray();

        foreach ($assessments as $oldAssessment) {
            $newAssessment = \App\Models\PropertyAssessmentDetail::where('property_id', $this->oldProperty->id)->whereYear('created_at', 2020)->first();

            if($newAssessment) {
//                $newAssessment->demand_note_delivered_at = $oldAssessment->demand_note_delivered_at;
//                $newAssessment->demand_note_recipient_name = $oldAssessment->demand_note_recipient_name;
//                $newAssessment->demand_note_recipient_mobile = $oldAssessment->demand_note_recipient_mobile;
//                $newAssessment->demand_note_recipient_photo = $oldAssessment->demand_note_recipient_photo;
//                $newAssessment->save();

                if(!file_exists(storage_path('app/' . $oldAssessment->demand_note_recipient_photo))) {
                    $img1Path1 = 'https://dpm.sigmaventuressl.com' . (Image::url($oldAssessment->demand_note_recipient_photo));

                    if ($imageCode2 = @file_get_contents($img1Path1)) {
                        Storage::disk('local')->put($oldAssessment->demand_note_recipient_photo, $imageCode2);
                    }
                }
            }
        }
    }
}
