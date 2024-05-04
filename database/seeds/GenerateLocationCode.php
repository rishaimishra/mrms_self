<?php

use Illuminate\Database\Seeder;

class GenerateLocationCode extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $geoRegistries = \App\Models\PropertyGeoRegistry::where('open_location_code', '')->where('dor_lat_long', '!=', '')->get();

        foreach ($geoRegistries as $geoRegistry) {

            list ($lat, $lng) = explode(',', $geoRegistry->dor_lat_long);

            $geoRegistry->open_location_code = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
            $geoRegistry->save();
        }
    }
}
