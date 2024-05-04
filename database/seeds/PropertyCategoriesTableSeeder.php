<?php

use Illuminate\Database\Seeder;

class PropertyCategoriesTableSeeder extends Seeder
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
            ['label' => 'Separate House','value'=>1],
            ['label' => 'Semi-Detached House','value'=>1],
            ['label' => 'Flats/Apartment','value'=>1],
            ['label' => 'Compound House','value'=>1],
            ['label' => 'Unfinished House','value'=>1],
            ['label' => 'Dilapidated','value'=>2],
            ['label' => 'Mobile Network Operator Mast','value'=>1]
        ];

        foreach ($data as $dt):
        App\Models\PropertyCategory::create($dt);
        endforeach;
    }
}
