<?php

use Illuminate\Database\Seeder;

class PropertyValueAddedTableSeeder extends Seeder
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
            ['label' => 'Electricity','value'=>50000],
            ['label' => 'Water','value'=>50000],
            ['label' => 'Swimming Pool','value'=>250000],
            ['label' => 'Solar','value'=>50000],
            ['label' => 'Dug Well/Borehole','value'=>25000],
            ['label' => 'Paved Yard','value'=>25000],
            ['label' => 'Tiled exterior walls','value'=>35000],
            ['label' => 'Communication Masts','value'=>100000],
            ['label' => 'Shop','value'=>100000]
        ];

        foreach ($data as $dt):
            App\Models\PropertyValueAdded::create($dt);
        endforeach;
    }
}
