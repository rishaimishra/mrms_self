<?php

use App\Models\PropertyAssessmentDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // property category
        $categories = DB::table('property_property_category')->get();

        foreach ($categories as $category) {

            $assessment = PropertyAssessmentDetail::where('property_id', $category->property_id)->first();

            if (! $assessment) {
                dump($category->property_id);
                continue;
            }

            DB::table('property_property_category')->where('property_id', $category->property_id)->update([
                'assessment_id' => $assessment->id
            ]);
        }

        // value added
        $valuesAdded = DB::table('property_property_value_added')->get();

        foreach ($valuesAdded as $valueAdded) {

            $assessment = PropertyAssessmentDetail::where('property_id', $valueAdded->property_id)->first();

            if (! $assessment) {
                dump($valueAdded->property_id);
                continue;
            }

            DB::table('property_property_value_added')->where('property_id', $assessment->property_id)->update([
                'assessment_id' => $assessment->id
            ]);
        }

        // property type
        $propertyTypes = DB::table('property_property_type')->get();

        foreach ($propertyTypes as $propertyType) {

            $assessment = PropertyAssessmentDetail::where('property_id', $propertyType->property_id)->first();

            if (! $assessment) {
                dump($propertyType->property_id);
                continue;
            }

            DB::table('property_property_type')->where('property_id', $assessment->property_id)->update([
                'assessment_id' => $assessment->id
            ]);
        }
    }
}
