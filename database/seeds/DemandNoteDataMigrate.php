<?php

use Illuminate\Database\Seeder;

class DemandNoteDataMigrate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\Property::where('is_draft_delivered', true)->get();

        foreach ($properties as $property) {

            /* @var $property \App\Models\Property */
            $assessment = $property->assessments()->orderBy('id', 'asc')->first();

            if ($assessment) {

                $assessment->fill([
                    'demand_note_delivered_at' => $property->updated_at,
                    'demand_note_recipient_name' => $property->delivered_name,
                    'demand_note_recipient_mobile' => $property->delivered_number,
                    'demand_note_recipient_photo' => $property->delivered_image
                ]);

                $assessment->save();
            }
        }
    }
}
