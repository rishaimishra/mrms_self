<?php

use App\Models\LandlordDetail;
use App\Models\MetaValue;
use App\Models\OccupancyDetail;
use Illuminate\Database\Seeder;

class CreateMetaData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result['first_name'] =  LandlordDetail::select('first_name as label', 'first_name as value')->whereNotNull('first_name')->union(OccupancyDetail::select('tenant_first_name as label', 'tenant_first_name as value')->whereNotNull('tenant_first_name'))->union(MetaValue::select('value as label', 'value')->where('name', 'first_name'))->distinct()->get()->each->setVisible(['label', 'value']);
        $result['surname'] = LandlordDetail::select('surname as label', 'surname as value')->whereNotNull('surname')->union(OccupancyDetail::select('surname as label', 'surname as value')->whereNotNull('surname'))->union(MetaValue::select('value as label', 'value')->where('name', 'surname'))->distinct()->get()->each->setVisible(['label', 'value']);;
        $result['street_name'] = LandlordDetail::select('street_name as label', 'street_name as value')->whereNotNull('street_name')->union(MetaValue::select('value as label', 'value')->whereNotNull('value')->where('name', 'street_name'))->distinct()->get()->each->setVisible(['label', 'value']);

        foreach ($result as $key => $value) {
           foreach ($value as $val) {

               $data = ['name'=> $key,'value'=> $val->value];

               MetaValue::firstOrCreate($data);
           }
        }

    }
}
