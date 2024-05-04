<?php

use Illuminate\Database\Seeder;

class backupDigitalAddress extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $addresses = \App\Models\PropertyGeoRegistry::get();

        foreach ($addresses as $address)
        {
            $address->old_digital_address =  $address->digital_address;

           // $address->digital_address = $address->digital_address . '-' . $address->property_id;

            $address->save();
        }
    }
}
