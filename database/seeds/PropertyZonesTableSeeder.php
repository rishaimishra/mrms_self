<?php

use Illuminate\Database\Seeder;

class PropertyZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            ['label' => 'Zone 1','value'=>1],
            ['label' => 'Zone 2','value'=>1.25],
            ['label' => 'Zone 3','value'=>1.40]
        ];

        foreach ($data as $dt):
            App\Models\PropertyZones::create($dt);
        endforeach;
    }
}
