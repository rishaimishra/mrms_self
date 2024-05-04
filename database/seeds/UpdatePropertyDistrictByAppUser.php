<?php

use Illuminate\Database\Seeder;

class UpdatePropertyDistrictByAppUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $properties = \App\Models\Property::with(['user'])->whereDate('updated_at', '2020-12-25')->where('district', '!=', 'Western Area Rural')->get();

        foreach ($properties as $property) {
            $property->district = $property->user->assign_district;
            $property->save();
        }
    }
}
