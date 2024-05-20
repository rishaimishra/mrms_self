<?php

namespace App\Http\Controllers\APIV2\General;

use App\Http\Controllers\API\ApiController;
use App\Logic\SystemConfig;
use App\Models\District;

class DistrictController extends ApiController
{


    public function getDistrict()
    {
        $result = District::orderBy('order_council', 'asc')->get();

        $districtData = [];

        foreach ($result as $district) {
            $districtData[] = [
                'id' => $district->id,
                'name' => $district->name,
                'council_name' => $district->council_name,
                'enquiries_email' => $district->enquiries_email,
                'council_address' => $district->council_address,
                'enquiries_phone' => $district->enquiries_phone,
                'enquiries_phone2' => $district->enquiries_phone2,
                'wards' => $district->wards,
                'constituencies' => $district->constituencies,
                'district' => $district->district,
                'province' => $district->province,
                'primary_logo' => $district->getPrimaryLogoUrl()
            ];
        }

        return $this->success([
            'result' => $districtData
        ]);
    }
}
