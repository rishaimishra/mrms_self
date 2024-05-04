<?php

use Illuminate\Database\Seeder;

class PropertyDimensionTableSeeder extends Seeder
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
            ['label' => 'Below 40 Square meters','value'=>0.75],
            ['label' => '40 - 80','value'=>1],
            ['label' => '80 - 160','value'=>1.75],
            ['label' => '160 - 320','value'=>2.75],
            ['label' => '320 - 640','value'=>3.75],
            ['label' => '640 - 1280','value'=>4.75],
            ['label' => '1280 - 2560','value'=>5.75],
            ['label' => '2560 - 3840','value'=>6.75],
            ['label' => '3840 - 5120','value'=>7.75],
            ['label' => '5120 - 7680','value'=>8.75],
            ['label' => '7680 - 8,960','value'=>9.75],
            ['label' => '8,960 - 10,240','value'=>10.75],
            ['label' => '10240 - 11,520','value'=>11.75],
            ['label' => '11520 - 12,800','value'=>12.75],
            ['label' => '12,800 - 14,080','value'=>13.75],
            ['label' => '14,080 -15,360','value'=>14.75],
            ['label' => '15,360 - 16,640','value'=>15.75],
            ['label' => '16,640 - 17,920','value'=>16.75],
            ['label' => '17,920 - 19,200','value'=>17.75],
            ['label' => '19,200 - 20,480','value'=>18.75]
        ];

        foreach ($data as $dt):
            App\Models\PropertyDimension::create($dt);
        endforeach;
    }
}
