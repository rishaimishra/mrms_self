<?php

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypesTableSeeder extends Seeder
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
            ['label' => 'Cellar','value'=>0.5],
            ['label' => 'Zinc House','value'=>0.5],
            ['label' => 'Flat','value'=>1],
            ['label' => '2 Storey','value'=>1.75],
            ['label' => '3 Storey','value'=>2.75],
            ['label' => '4 Storey','value'=>3.75],
            ['label' => '5 Storey','value'=>4.75],
            ['label' => '6 Storey','value'=>5.75],
            ['label' => '7 Storey','value'=>6.75],
            ['label' => '8 Storey','value'=>7.75],
            ['label' => '9 Storey','value'=>8.75],
            ['label' => '10 Storey','value'=>9.75],
            ['label' => '11 Storey','value'=>10.75],
            ['label' => '12 Storey','value'=>11.75],
            ['label' => '13 Storey','value'=>12.75],
            ['label' => '14 Storey','value'=>13.75],
            ['label' => '15 Storey','value'=>14.75],
            ['label' => '16 Storey','value'=>15.75],
            ['label' => '17 Storey','value'=>16.75],
            ['label' => '18 Storey','value'=>17.75]
        ];

        foreach ($data as $dt):
            App\Models\PropertyType::create($dt);
        endforeach;
    }
}
