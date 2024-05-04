<?php

use Illuminate\Database\Seeder;

class PropertyUseTableSeeder extends Seeder
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
            ['label' => 'Residential','value'=>1],
            ['label' => 'Commercial','value'=>2],
            ['label' => 'Industrial','value'=>3],
        ];

        foreach ($data as $dt):
            App\Models\PropertyUse::create($dt);
        endforeach;
    }
}
