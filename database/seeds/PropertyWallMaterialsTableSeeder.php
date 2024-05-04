<?php

use Illuminate\Database\Seeder;

class PropertyWallMaterialsTableSeeder extends Seeder
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
            ['label' => 'Glass','value'=>250000],
            ['label' => 'Cement Blocks','value'=>75000],
            ['label' => 'Stone','value'=>60000],
            ['label' => 'Clay Bricks','value'=>50000],
            ['label' => 'Sandcrete','value'=>40000],
            ['label' => 'Wood','value'=>35000],
            ['label' => 'Zinc','value'=>30000]
        ];

        foreach ($data as $dt):
            App\Models\PropertyWallMaterials::create($dt);
        endforeach;
    }
}
