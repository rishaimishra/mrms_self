<?php

use Illuminate\Database\Seeder;

class MigrateDorLatLong extends Seeder
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
           $digitalAddress = explode(' ', $address->digital_address);

           if(count($digitalAddress) == 3)
           {
               $address->dor_lat_long =   $digitalAddress[1] . ', ' . $digitalAddress[2];

               $address->save();
           }
       }

    }
}
