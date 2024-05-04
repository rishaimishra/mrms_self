<?php

use Illuminate\Database\Seeder;

class UpdatePostcodeByWard extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $properties = \App\Models\Property::with('boundryDelimetation')->whereNotNull('ward')->get();

        // foreach ($properties as $property) {
        //    if($property->boundryDelimetation) {
        //        $property->district = $property->boundryDelimetation->district;
        //        $property->save();
        //    }
        // }

        $properties = \App\Models\LandlordDetail::with('boundryDelimetation')->whereNotNull('ward')->get();

        foreach ($properties as $property) {
           if($property->boundryDelimetation) {
               $property->district = $property->boundryDelimetation->district;
               $property->save();
           }
        }
    }
}
