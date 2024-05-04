<?php

use Illuminate\Database\Seeder;


class PropertyRoofsMaterialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //


        //
        $data = [
            ['label' => 'Concrete','value'=>100000],
            ['label' => 'Asbestos','value'=>75000],
            ['label' => 'Zinc','value'=>30000],
            ['label' => 'Thatch','value'=>25000],
        ];

        foreach ($data as $dt):
            App\Models\PropertyRoofsMaterials::create($dt);
        endforeach;

    }
}
