<?php

use Illuminate\Database\Seeder;

class MigrateData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $properties = \App\Models\Property::with('assessment', 'occupancy')->get();

       foreach ($properties as $property)
       {
            if($category = $property->assessment->property_categories)
            {
                $property->categories()->sync([$category]);
            }


            if($occupancy = $property->occupancy->type)
            {
                $property->occupancies()->firstOrcreate(['occupancy_type' => $occupancy]);
            }

       }
    }
}
