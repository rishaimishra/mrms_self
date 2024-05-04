<?php

use Illuminate\Database\Seeder;

class UpdateLandloardPostcodeByWard extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\LandlordDetail::with('boundryDelimetation')->whereNotNull('ward')->get();

        foreach ($properties as $property) {
            if($property->boundryDelimetation) {
                $prefix = $property->boundryDelimetation->prefix . $property->boundryDelimetation->ward;
                $property->postcode = $prefix;
                $property->save();
            }
        }

    }
}
