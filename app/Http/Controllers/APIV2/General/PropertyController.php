<?php

namespace App\Http\Controllers\APIV2\General;

use Folklore\Image\Facades\Image;
use App\Http\Controllers\API\ApiController;
use App\Logic\SystemConfig;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\RegistryMeter;
use App\Models\Swimming;
use App\Models\PropertyWindowType;
use App\Models\District;
use App\Models\BoundaryDelimitation;
use App\Models\User;
use App\Models\InaccessibleProperty;
use App\Models\UnfinishedProperty;
use App\Models\PropertyInaccessible;
use App\Notifications\DraftDeliveredSMSNotification;
use App\Notifications\PaymentSMSNotification;
use App\Models\UserTitleTypes;
use App\Types\ApiStatusCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Property;
use App\Models\AdjustmentValue;
use App\Models\Adjustment;
use App\Models\MillRate;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class PropertyController extends ApiController
{
	private $propertyId;

    public function save(Request $request)
    {
        $assessment_images = new PropertyAssessmentDetail();
        \Illuminate\Support\Facades\Log::debug($request->all());
        $this->propertyId = $request->input('property_id');

        if ($this->propertyId && $property = Property::find($this->propertyId)) {
            $assessment_images = $property->assessment()->first();
        }elseif($property = Property::where('random_id', $request->random_id)->where('random_id', '<>', '')->first()){
        	$this->propertyId = $property->id;
        	$assessment_images = $property->assessment()->first();
        }

        /* @var User */
        $user = $request->user();

       /* $validator = $this->validator($request, $assessment_images);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        } */

        \DB::beginTransaction();

        $rate = $this->calculateNewRate($request);

        /* @var $property Property */
        $property = $user->properties()->firstOrNew(['id' => $this->propertyId]);

        // $groupName = $request->group_name;
        // $totalAdjustmentPercent = array_sum($request->adjustment_percentage);
        // $millrs = MillRate::where('group_name', $groupName)->first();
        // $millRate = 0;
        // if($millrs){
        //     $millRate = $millrs->rate;
        // }

        // echo '$groupName->'.$groupName.'<br/>';
        // echo 'totalAdjustmentPercent->'.$totalAdjustmentPercent.'<br/>';
        // echo 'millRate->'.$millRate.'<br/>';

        // exit;



        $property->fill([
            'assessment_area' => $request->assessment_area,
            'street_number' => $request->property_street_number,
            'street_numbernew' => $request->property_street_numbernew,
            'street_name' => $request->property_street_name,
            'ward' => $request->property_ward,
            'constituency' => $request->property_constituency,
            'section' => $request->property_section,
            'chiefdom' => $request->property_chiefdom,
            'district' => $request->property_district ? $request->property_district : $user->assign_district,
            'province' => $request->property_province,
            'postcode' => $request->property_postcode,
            'organization_addresss' => $request->organization_address ? $request->organization_address : null,
            'organization_tin' => $request->organization_tin ? $request->organization_tin : null,
            'organization_type' => $request->organization_type ? $request->organization_type : null,
            'organization_name' => $request->organization_name ? $request->organization_name : null,
            'isDilapidatedProperty' => $request->isDilapidatedProperty ? $request->isDilapidatedProperty : null,
            'propertyArea' => $request->propertyArea ? $request->propertyArea : null,
            'landlordPropertyArea' => $request->landlordPropertyArea ? $request->landlordPropertyArea : null,
            'ninNumber' => $request->ninNumber ? $request->ninNumber : null,
            'coocrdinates' => $request->coocrdinates ? $request->coocrdinates : null,
            'is_organization' => $request->input('is_organization', false),
            'is_completed' => $request->input('is_completed', false),
            'is_property_inaccessible' => $request->input('is_property_inaccessible', false),
            'is_draft_delivered' => $request->input('is_draft_delivered', false),
            'delivered_name' => $request->input('delivered_name'),
            'delivered_number' => $request->input('delivered_number'),

            // 'window_type_value' =>($request->window_type_condition)? $request->window_type_condition['value'] : null,
            'random_id' => $request->input('random_id'),

        ]);


        $recipient_photo = null;

        if ($request->hasFile('delivered_image')) {
            $recipient_photo = $request->delivered_image->store(Property::DELIVERED_IMAGE);
            $property->delivered_image = $recipient_photo;
        }

        $property->save();

        $property->propertyInaccessible()->sync($request->property_inaccessible);

        $landlord = $property->landlord()->firstOrNew([]);

        /* landlord image */
        $landlord_image = $landlord->image;

        if ($request->hasFile('landlord_image')) {
            if ($landlord->hasImage()) {
                unlink($landlord->getImage());
            }
            $landlord_image = $request->landlord_image->store(Property::ASSESSMENT_IMAGE);
        }


        $landlord_title_label = UserTitleTypes::where('id',$request->landlord_ownerTitle_id)->value('label');
        /* Save/Update landlord details*/
        $landlord->fill([
            'ownerTitle' => $request->landlord_ownerTitle_id,
            'first_name' => $request->landlord_first_name,
            'middle_name' => $request->landlord_middle_name,
            'surname' => $request->landlord_surname,
            'sex' => $request->landlord_sex,
            'street_number' => $request->landlord_street_number,
            'street_numbernew' => $request->landlord_street_numbernew,
            'street_name' => $request->landlord_street_name,
            'email' => $request->landlord_email,
            'image' => $landlord_image,
            'id_number' => $request->landlord_id_number,
            'id_type' => $request->landlord_id_type,
            'tin' => $request->landlord_tin,
            'ward' => $request->landlord_ward,
            'constituency' => $request->landlord_constituency,
            'section' => $request->landlord_section,
            'chiefdom' => $request->landlord_chiefdom,
            'district' => $request->landlord_district,
            'province' => $request->landlord_province,
            'postcode' => $request->landlord_postcode,
            'mobile_1' => $request->landlord_mobile_1,
            'mobile_1' => $request->landlord_mobile_1,
            'mobile_2' => $request->landlord_mobile_2,
        ]);

        $landlord->save();

        /* Save/Update occupancy details*/

        $occupancy = $property->occupancy()->firstOrNew([]);

        $tenant_title_label = UserTitleTypes::where('id',$request->tenant_ownerTitle_id)->value('label');
        $occupancy->fill([
            'type' => $request->occupancy_type,
            'ownerTenantTitle' => $request->ownerTenantTitle,
            'tenant_first_name' => $request->occupancy_tenant_first_name,
            'middle_name' => $request->occupancy_middle_name,
            'surname' => $request->occupancy_surname,
            'mobile_1' => $request->occupancy_mobile_1,
            'mobile_2' => $request->occupancy_mobile_2
        ]);

        $occupancy->save();

        if ($request->occupancy_type && count(array_filter($request->occupancy_type))) {
            foreach (array_filter($request->occupancy_type) as $types) {
                $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
            }
            $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
        }

        /* @var $assessment PropertyAssessmentDetail */

        /* Save/Update assessment details*/
        if ($property->assessment()->exists()) {

            $assessment = $property->generateAssessments();
        } else {
            $assessment = $property->assessment()->firstOrNew([]);
        }

        $water_percentage = 0;
        $electrical_percentage = 0;
        $waster_precentage = 0;
        $market_percentage = 0;
        $hazardous_percentage = 0;
        $drainage_percentage = 0;
        $informal_settlement_percentage = 0;
        $easy_street_access_percentage = 0;
        $paved_tarred_street_percentage = 0;

        $groupName = $request->group_name ? $request->group_name : 'A' ;
        $adjustmentPercentage = [];
        if(is_array($request->adjustment_ids)){
            $adjustmentsArray = $request->adjustment_ids;
            foreach($adjustmentsArray as $id)
            {
                $name_perc = Adjustment::where('id',$id)->pluck('name');
                if($id == 1){
                    $water_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 2){
                    $electrical_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 3){
                    $waster_precentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 4){
                    $market_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 5){
                    $hazardous_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 6){
                    $informal_settlement_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 7){
                    $easy_street_access_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }elseif($id == 8){
                    $paved_tarred_street_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }else{
                    $drainage_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                }
            }
        }
        if(is_array($request->adjustment_ids)){
            $adjustmentPercentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', $request->adjustment_ids)->pluck('percentage')->toArray();
        }


        $totalAdjustmentPercent = array_sum($adjustmentPercentage);

        //$totalAdjustmentPercent = array_sum($request->adjustment_percentage);

        $district = District::where('name', $request->property_district)->first() ;
        $mill_rate_group_name = $district->group_name;
        $millrs = MillRate::where('group_name', $mill_rate_group_name)->first();
        $millRate = 0;
        if($millrs){
            $millRate = $millrs->rate;
        }

        // echo '$groupName->'.$groupName.'<br/>';
        // echo 'totalAdjustmentPercent->'.$totalAdjustmentPercent.'<br/>';
        // echo 'millRate->'.$millRate.'<br/>';

        // exit;


        $assessment_data = [
            'property_wall_materials' => ($request->wall_material)? $request->wall_material[0]['id'] : null,
            'roofs_materials' => ($request->roof_material)? $request->roof_material[0]['id'] : null,
            'property_window_type' => ($request->window_type)? $request->window_type[0]['id'] : null,
            'property_dimension' => $request->assessment_dimension_id,
            'length' => $request->assessment_length,
            'breadth' => $request->assessment_breadth,
            'square_meter' => $request->assessment_square_meter,
            'property_rate_without_gst' => $request->assessmentRateWithoutGST > 0 ? $request->assessmentRateWithoutGST: $assessment->property_rate_without_gst,
            'property_gst' => $request->assessmentRateWithGST > 0 ? $request->assessmentRateWithGST : $assessment->property_gst,
            'property_rate_with_gst' => $rate['rateWithGST'],
            'property_use' => $request->assessment_use_id,
            'zone' => $request->assessment_zone_id,
            'no_of_mast' => $request->total_mast,
            'no_of_shop' => $request->total_shops,
            'no_of_compound_house' => $request->total_compound_house,
            'compound_name' => $request->compound_name,
            'gated_community' => $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : null,
            'total_adjustment_percent' => $totalAdjustmentPercent,
            'group_name' => $mill_rate_group_name,
            'mill_rate' => $millRate,

            'wall_material_percentage' =>($request->wall_material)? $request->wall_material[0]['percentage'] : 0,
            'wall_material_type' =>($request->wall_material)? $request->wall_material[0]['type'] : 'A',

            'roof_material_percentage' =>($request->roof_material)? $request->roof_material[0]['percentage'] : 0,
            'roof_material_type' =>($request->roof_material)? $request->roof_material[0]['type'] : 'A',

            'value_added_percentage' =>($request->value_added)? $request->value_added[0]['percentage'] : 0,
            'value_added_type' =>($request->value_added)? $request->value_added[0]['type'] : 'A',

            'window_type_percentage' =>($request->window_type)? $request->window_type[0]['percentage'] : 0,
            'window_type_type' =>($request->window_type)? $request->window_type[0]['type'] : 'A',

            'water_percentage' => $water_percentage,
            'electricity_percentage' => $electrical_percentage,
            'waste_management_percentage'=> $waster_precentage,
            'market_percentage'=> $market_percentage,
            'hazardous_precentage'=> $hazardous_percentage,
            'drainage_percentage'=> $drainage_percentage,
            'informal_settlement_percentage'=> $informal_settlement_percentage,
            'easy_street_access_percentage'=> $easy_street_access_percentage,
            'paved_tarred_street_percentage'=> $paved_tarred_street_percentage,
            'sanitation' => ($request->sanitation)? $request->sanitation[0]['type'] : 'A'
        ];
        // return $request->roof_material;
        // return $assessment_data;

        if ($request->hasFile('assessment_images_1')) {
            if ($assessment_images->hasImageOne()) {
                unlink($assessment_images->getImageOne());
            }
            $assessment_data['assessment_images_1'] = $request->assessment_images_1->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->hasFile('assessment_images_2')) {
            if ($assessment_images->hasImageTwo()) {
                unlink($assessment_images->getImageTwo());
            }
            $assessment_data['assessment_images_2'] = $request->assessment_images_2->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->input('is_draft_delivered')) {

            if (!$assessment->demand_note_delivered_at) {

                if ($mobile_number = $property->landlord->mobile_1) {
                    //$property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
                    $name = $request->input('delivered_name');
                    $year = now()->format('Y');
                    if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                        $property->landlord->notify(new DraftDeliveredSMSNotification($property, $mobile_number, $name, $year));
                    }
                }
            }
            $assessment_data['demand_note_delivered_at'] = now();
            $assessment_data['demand_note_recipient_name'] = $request->input('delivered_name');
            $assessment_data['demand_note_recipient_mobile'] = $request->input('delivered_number');
            $assessment_data['demand_note_recipient_photo'] = $recipient_photo;
        }

        $assessment->fill($assessment_data);

        if ($request->input('swimming_pool')) {
            $assessment->swimming()->associate($request->input('swimming_pool'));
        }

        $assessment->save();

        $categories = getSyncArray($request->input('assessment_categories_id'), ['property_id' => $property->id]);
        $assessment->categories()->sync($categories);

        /* property type (Habitat) multiple value */
        $types = getSyncArray($request->input('assessment_types'), ['property_id' => $property->id]);
        $assessment->types()->sync($types);

        /* Property type (typesTotal) multiple value */
        if ($request->input('assessment_types_total')) {
            $typesTotal = getSyncArray($request->input('assessment_types_total'), ['property_id' => $property->id]);
            $assessment->typesTotal()->sync($typesTotal);
        }


        /* property value added multiple value */
        $valuesAdded = getSyncArray($request->input('assessment_value_added_id'), ['property_id' => $property->id]);
        $assessment->valuesAdded()->sync($valuesAdded);

        /* Geo Registry Data  */

        $geoData = [
            'point1' => $request->registry_point1,
            'point2' => $request->registry_point2,
            'point3' => $request->registry_point3,
            'point4' => $request->registry_point4,
            'point5' => $request->registry_point5,
            'point6' => $request->registry_point6,
            'point7' => $request->registry_point7,
            'point8' => $request->registry_point8,
            'digital_address' => $request->registry_digital_address,
            'dor_lat_long' => str_replace(',', ', ', $request->dor_lat_long),
        ];

        if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
            list($lat, $lng) = explode(',', $request->dor_lat_long);
            $geoData['open_location_code'] = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
        }

       // !$geoData['digital_address'] || $geoData = $this->addIdToDigitalAddress($geoData, $property);

        $geoRegistry = $property->geoRegistry()->firstOrNew([]);

        $geoRegistry->fill($geoData);
        $geoRegistry->save();

        /* save and update Registry Image */
        $registryImageId = [];
        $allregistryImage = $property->registryMeters()->pluck('id')->toArray();
        if ($request->registry && count($request->registry) and is_array($request->registry)) {
            foreach (array_filter($request->registry) as $key => $registry) {
                $image = null;
                $registryImageId[] = isset($registry['id']) ? (int) $registry['id'] : '';
                if ($request->hasFile('registry.' . $key . '.meter_image')) {
                    $registryMeters = $property->registryMeters()->where('id', isset($registry['id']) ? (int) $registry['id'] : '')->first();
                    if ($registryMeters && $registryMeters->image != null) {
                        if ($registryMeters->hasImage())
                            unlink($registryMeters->getImage());
                        // $registryMeters->delete();
                    }
                    $image = $registry['meter_image']->store(Property::METER_IMAGE);
                    $property->registryMeters()
                        ->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number'], 'image' => $image]);
                } else {
                    $property->registryMeters()->updateOrCreate(['id' => $registry['id']], ['number' => $registry['meter_number']]);
                }
            }
        }

        /* delete registry image which not updated*/

        $removeImageId = array_diff($allregistryImage, $registryImageId);
        if (count($removeImageId)) {
            foreach ($removeImageId as $diffId) {
                $registryMetersDelete = $property->registryMeters()->where('id', $diffId)->first();
                if ($registryMetersDelete && $registryMetersDelete->image != null) {
                    if ($registryMetersDelete->hasImage()) {
                        unlink($registryMetersDelete->getImage());
                    }

                    //$registryMetersDelete->delete();
                }
                $registryMetersDelete->delete();
            }
        }

        \DB::commit();

        $getProperty = $property->with('landlord', 'occupancy', 'assessment', 'geoRegistry', 'registryMeters', 'occupancies', 'categories', 'propertyInaccessible')->where('id', $property->id)->get();

                    $adjustments = [

                [ 'id' => '1',
                 'name' => 'Water Supply',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [ 'id' => '2',
                 'name' => 'Electricity',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [
                    'id'=> '3',
                    'name'=> 'Waste Management Services/Points/Locations',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '5',
                    'name'=> 'Hazardous Location/Environment',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '7',
                    'name'=> 'Easy Street Access',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ]

             ];

        return $this->success([
            'property_id' => $property->id,
            'sink' => 1,
            'is_completed' => $property->is_completed,
            'property' => $getProperty,
            'values_adjustment' => $adjustments
        ]);
    }

    protected function addIdToDigitalAddress($geoData, $property)
    {
        $digitalAddress = $property->geoRegistry()->first();

        //if (!$digitalAddress) {
         //   $geoData['digital_address'] = $geoData['digital_address'];

            return $geoData;
       // }
        //
        //        $addresses = explode('-', $digitalAddress->digital_address);
        //
        //        $last = count($addresses) > 1 ? intval(array_last($addresses)) : array_last($addresses);
        //
        //        if($last != $property->id)
        //        {
        //            $geoData['digital_address'] = $geoData['digital_address'] . '-' . $property->id;
        //
        //            return $geoData;
        //        }
        //
        //        $geoData['digital_address'] = $geoData['digital_address'] . '-' . $property->id;

        return $geoData;
    }

    public function validator($request, $assessment_images)
    {
        $registryimage = new RegistryMeter();

        if ($this->propertyId && $property = Property::find($this->propertyId)) {
            $registryimage = $property->registryMeters()->first();
        }

        $validationRequriedIf = $registryimage && $registryimage->image != null ? '' : 'required_if:is_completed,1';

        if (isset($request->is_completed) && $request->is_completed == 1) {
            $organizationYes = 'required_if:is_organization,1';
            $organizationNo = 'required_if:is_organization,0';
            $registryField = 'required';
        } else {
            $organizationYes = '';
            $organizationNo = '';
            $registryField = 'nullable';
        }

        $validator = Validator::make($request->all(), [
            'landlord_ownerTitle_id' =>'integer',
            'tenant_ownerTitle_id' => 'integer',
            'is_completed' => 'nullable|boolean',
            'is_organization' => 'required|boolean',
            'organization_name' => '' . $organizationYes . '|string|max:255',
            'organization_type' => '' . $organizationYes . '|string|max:255',
            'organization_tin' => 'nullable|string|max:255',
            'organization_address' => '' . $organizationYes . '|string|max:255',
            'assessment_area' => 'string',
            'property_street_number' => 'required_if:is_completed,1|string',
            'property_street_numbernew' => 'required_if:is_completed,1|string',
            'property_street_name' => 'required_if:is_completed,1|string|max:255|nullable',
            'property_ward' => 'required_if:is_completed,1|integer',
            'property_constituency' => 'required_if:is_completed,1|integer',
            'property_section' => 'required_if:is_completed,1|string|max:255',
            'property_chiefdom' => 'required_if:is_completed,1|string|max:255',
            'property_district' => 'required_if:is_completed,1|string|max:255',
            'property_province' => 'required_if:is_completed,1|string|max:255',
            'property_postcode' => 'required_if:is_completed,1|string|max:255',
            'landlord_first_name' => '' . $organizationNo . '|string|max:255',
            'landlord_middle_name' => 'nullable|string|max:255',
            'landlord_surname' => '' . $organizationNo . '|string|max:255',
            'landlord_sex' => '' . $organizationNo . '|string|max:255',
            'landlord_street_number' => 'string',
            'landlord_street_numbernew' => 'string',
            'landlord_street_name' => 'string|max:255',
            'landlord_email' => "nullable|email",
            'landlord_tin' => 'nullable|string|max:255',
            'landlord_id_type' => 'nullable|string|max:255',
            'landlord_id_number' => 'nullable|string|max:255',
            'landlord_image' => 'nullable|max:10240‬',
            'landlord_ward' => 'required_if:is_completed,1|integer',
            'landlord_constituency' => 'required_if:is_completed,1|integer',
            'landlord_section' => 'required_if:is_completed,1|string|max:255',
            'landlord_chiefdom' => 'required_if:is_completed,1|string|max:255',
            'landlord_district' => 'required_if:is_completed,1|string|max:255',
            'landlord_province' => 'required_if:is_completed,1|string|max:255',
            'landlord_postcode' => 'required_if:is_completed,1|string|max:255',
            'landlord_mobile_1' => 'required_if:is_completed,1|string|max:15',
            'landlord_mobile_2' => 'nullable|string|max:15',
            'occupancy_type' => 'nullable|required_if:is_completed,1|array',
            'occupancy_type.*' => 'nullable|required_if:is_completed,1|in:Owned Tenancy,Rented House,Unoccupied House',
            'occupancy_tenant_first_name' => 'nullable|string|max:255',
            'occupancy_middle_name' => 'nullable|string|max:255',
            'occupancy_surname' => 'nullable|string',
            'occupancy_mobile_1' => 'nullable|string|max:15',
            'occupancy_mobile_2' => 'nullable|string|max:15',
            'assessment_categories_id' => 'nullable|required_if:is_completed,1|array',
            'assessment_categories_id.*' => 'nullable|required_if:is_completed,1|exists:property_categories,id',
            'assessment_images_1' => '' . ($assessment_images->assessment_images_1 == null ? 'required_if:is_completed,1|' : ''),
            'assessment_images_2' => '' . ($assessment_images->assessment_images_2 == null ? 'required_if:is_completed,1|' : ''),
            'assessment_types' => 'required_if:is_completed,1|array|max:2',
            'assessment_types.*' => 'required_if:is_completed,1|exists:property_types,id',
            "assessment_types_total" => 'nullable|array|max:2',
            "assessment_types_total.*" => 'nullable|exists:property_types,id',
            'assessment_wall_materials_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_length' => 'nullable',
            'assessment_breadth' => 'nullable',
            'assessment_square_meter' => 'nullable',
            'assessment_roofs_materials_id' => 'required_if:is_completed,1|string|max:255',
            //'assessment_dimension_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_value_added_id' => 'required_if:is_completed,1|array',
            'assessment_value_added_id.*' => 'required_if:is_completed,1|exists:property_value_added,id',
            'assessment_use_id' => 'required_if:is_completed,1|string|max:255',
            'assessment_zone_id' => 'required_if:is_completed,1|string|max:255',
            'compound_name' => 'nullable|string|max:255',
            'total_compound_house' => 'nullable|string|max:255',
            'total_shops' => 'nullable|string|max:255',
            'total_mast' => 'nullable|string|max:255',
            'registry' => 'array',
            'registry.*.meter_image' => [
                'max:10240‬'
            ],
            'registry.*.meter_number' => 'nullable|string|max:255',
            'registry_point1' => 'required_if:is_completed,1|string|max:255',
            'registry_point2' => 'required_if:is_completed,1|string|max:255',
            'registry_point3' => 'required_if:is_completed,1|string|max:255',
            'registry_point4' => 'nullable|string|max:255',
            'registry_point5' => 'nullable|string|max:255',
            'registry_point6' => 'nullable|string|max:255',
            'registry_point7' => 'nullable|string|max:255',
            'registry_point8' => 'nullable|string|max:255',
            'registry_digital_address' => [
                'required_if:is_completed,1',
                'string',
                'max:159'
            ],
            'dor_lat_long' => 'nullable|required_if:is_completed,1|max:190',
            'gated_community' => 'nullable|required_if:is_completed,1|boolean',
            'swimming_pool' => 'nullable|exists:swimmings,id',
            'is_property_inaccessible' => 'required|boolean',
            'property_inaccessible' => 'nullable|required_if:is_property_inaccessible,1|array',
            'property_inaccessible.*' => 'nullable|required_if:is_property_inaccessible,1|exists:property_inaccessibles,id',
            'is_draft_delivered' => 'nullable|boolean',
            'delivered_name' => 'nullable|max:70',
            'delivered_number' => 'nullable|string|max:55',
            'delivered_image' => 'nullable|max:10240‬'

        ]);

        return $validator->after(function ($validator) use ($request) {

            $openLocationCode = '';
            if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
                list($lat, $lng) = explode(',', $request->dor_lat_long);
                $openLocationCode = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
            }

            if($this->propertyId){
                $propertyExist = Property::where('id', '<>', $this->propertyId)
                ->whereHas('geoRegistry', function($q) use ($openLocationCode){
                    $q->where('open_location_code', $openLocationCode)
                    ->where('open_location_code','<>','');
                })->first();
            }else{
                $propertyExist = Property::whereHas('geoRegistry', function($q) use ($openLocationCode){
                    $q->where('open_location_code', $openLocationCode)
                    ->where('open_location_code','<>','');;
                })->first();
            }

            if ($propertyExist) {
                //$validator->errors()->add('open_location_code', 'This digital address is already exist');
            }

        });

    }

    public function getIncompleteProperty(Request $request)
    {
        $property = $request->user()->properties()
            ->with('images', 'occupancy', 'assessment', 'geoRegistry', 'registryMeters', 'payments', 'landlord', 'assessment.typesTotal:id,label,value', 'assessment.types:id,label,value', 'assessment.valuesAdded:id,label,value', 'occupancies:id,occupancy_type,property_id', 'assessment.categories:id,label,value', 'propertyInaccessible:id,label')
            ->orderBy('id', 'desc')
            ->get();

                        $adjustments = [

                [ 'id' => '1',
                 'name' => 'Water Supply',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [ 'id' => '2',
                 'name' => 'Electricity',
                 'percentage' => '3',
                 'group_name' => '"A"'
                ],
                [
                    'id'=> '3',
                    'name'=> 'Waste Management Services/Points/Locations',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '5',
                    'name'=> 'Hazardous Location/Environment',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ],
                [
                    'id'=> '7',
                    'name'=> 'Easy Street Access',
                    'percentage'=> '5',
                    'group_name'=> '"A"'
                ]

             ];

             $parr = [];
             foreach($property as $p)
             {
                $adjustments = [];


                if($p->assessment->water_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '1',
                    'name' => 'Water Supply',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->electricity_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '2',
                    'name' => 'Electricity',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->waste_management_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '3',
                    'name' => 'Waste Management Services/Points/Locations',
                    'percentage' => '5',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->market_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '4',
                    'name' => 'Market',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->hazardous_precentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '5',
                    'name' => 'Hazardous Location/Environment',
                    'percentage' => '15',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->informal_settlement_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '6',
                    'name' => 'Informal settlement',
                    'percentage' => '21',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->easy_street_access_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '7',
                    'name' => 'Easy Street Access',
                    'percentage' => '7',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->paved_tarred_street_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '8',
                    'name' => 'Paved/Tarred Road/Street',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }
                if($p->assessment->drainage_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => '9',
                    'name' => 'Drainage',
                    'percentage' => '3',
                    'group_name' => '"A"'
                    ]);
                }

                array_add($p, 'values_adjustment', $adjustments);
             }

        return $this->success([
            'property' => $property,
            'values_adjustment' => $adjustments
        ]);
    }

    public function getMyDistrict(Request $request)
    {
        // $result = BoundaryDelimitation::where('district', $request->user()->assign_district)->get();

        $result = \DB::table('boundary_delimitations')
            ->leftJoin('districts', 'boundary_delimitations.district', '=', 'districts.name')
            ->select('boundary_delimitations.id', 'boundary_delimitations.ward', 'boundary_delimitations.constituency', 'boundary_delimitations.section', 'boundary_delimitations.chiefdom', 'boundary_delimitations.district', 'boundary_delimitations.province', 'boundary_delimitations.council', 'boundary_delimitations.prefix', 'districts.group_name')
            ->where('boundary_delimitations.district', $request->user()->assign_district)
            ->get();


        return $this->success([
            'result' => $result,
        ]);
    }

    public function calculateRate($request)
    {
        $property_category = 0;
        $wall_material = 0;
        $roof_material = 0;
        $value_added_val = 0;
        $property_type_val = 0;
        $property_dimension = 0;
        $property_use = 0;
        $zones = 0;
        $no_of_shops = $request->total_shops ? $request->total_shops : 0;
        $no_of_mast = $request->total_mast ? $request->total_mast : 0;
        $shopValue = 0;
        $mastValue = 0;
        $valueAdded = [8, 9];
        $property_categories = [];

        if (isset($request->assessment_value_added_id) && is_array($request->assessment_value_added_id)) {
            foreach ($valueAdded as $value) {
                if (in_array($value, $request->assessment_value_added_id)) {
                    $amount = PropertyValueAdded::select('value')->where('id', $value)->first();
                    if ($value == 9) {
                        $shopValue = $amount->value;
                    }
                    if ($value == 8) {
                        $mastValue = $amount->value;
                    }
                }
            }
            $valueAdded = array_diff($request->assessment_value_added_id, $valueAdded);
        }

        if (isset($request->assessment_categories_id) and $request->assessment_categories_id != null)
            $property_categories = PropertyCategory::whereIn('id', $request->assessment_categories_id)->get();

        if (isset($request->assessment_wall_materials_id) and $request->assessment_wall_materials_id != null)
            $wall_material = PropertyWallMaterials::select('value')->find($request->assessment_wall_materials_id);

        if (isset($request->assessment_roofs_materials_id) and $request->assessment_roofs_materials_id != null)
            $roof_material = PropertyRoofsMaterials::select('value')->find($request->assessment_roofs_materials_id);

        if (is_array($request->assessment_value_added_id) and count($request->assessment_value_added_id) > 0)
            $value_added_val = PropertyValueAdded::whereIn('id', $valueAdded)->sum('value');

        if (is_array($request->assessment_types) and count($request->assessment_types) > 0)
            $property_type_val = PropertyType::whereIn('id', $request->assessment_types)->sum('value');

        if (isset($request->assessment_dimension_id) and $request->assessment_dimension_id != null)
            $property_dimension = PropertyDimension::select('value')->find($request->assessment_dimension_id);

        if (isset($request->assessment_use_id) and $request->assessment_use_id != null)
            $property_use = PropertyUse::select('value')->find($request->assessment_use_id);

        if (isset($request->assessment_zone_id) and $request->assessment_zone_id != null)
            $zones = PropertyZones::select('value')->find($request->assessment_zone_id);

        /*number of Shop available*/

        if ($shopValue > 0)
            $value_added_val = $value_added_val + ($shopValue * $no_of_shops);

        /*number of mast available*/
        if ($mastValue > 0)
            $value_added_val = $value_added_val + ($mastValue * $no_of_mast);

        $step1 = $wall_material['value'] + $roof_material['value'] + $value_added_val;
        $step2 = $property_type_val;
        $step3 = $property_dimension['value'];
        $step4 = $property_use['value'];
        $step5 = $zones['value'];
        $step6 = 0;
        $swimming_pool = optional(Swimming::find($request->swimming_pool))->value;

        $gated_community = $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : 1;

        if (count($property_categories) && $property_categories->count()) {
            $step6 = 1;

            foreach ($property_categories as $prop_category) {
                $step6 *= $prop_category->value;
            }
        }

        $result['rateWithoutGST'] = @(((($step1 * $step2 * $step3 * $step4) * $gated_community) + ($swimming_pool ? $swimming_pool : 0)) / ($step6 > 0 ? $step6 : 1));

        $result['GST'] = $result['rateWithoutGST'] * .15;

        $result['rateWithGST'] = round($result['rateWithoutGST'] + $result['GST'], 4);

        return $result;
    }

    public function calculateNewRate($request)
    {
        $cost_of_one_town = 250000;
        $property_category = 0;
        // $rate_square_meter = 2750.00;
        $rate_square_meter = 3750;
        $wall_material = 0;
        $roof_material = 0;
        $value_added_val = 0;
        $property_type_val = 0;
        $property_dimension = 0;
        $property_use = 0;
        $zones = 0;
        $no_of_shops = $request->total_shops ? $request->total_shops : 0;
        $no_of_mast = $request->total_mast ? $request->total_mast : 0;
        $shopValue = 0;
        $mastValue = 0;
        $valueAdded = [8, 9];
        $property_categories = [];
        $floor_area=1722;

        $result['value_per_square_one_town'] = round($cost_of_one_town / $rate_square_meter,1);
        $result['floor_area_value'] = $floor_area *  $result['value_per_square_one_town'];

        if (isset($request->assessment_value_added_id) && is_array($request->assessment_value_added_id)) {
            foreach ($valueAdded as $value) {
                if (in_array($value, $request->assessment_value_added_id)) {
                    $amount = PropertyValueAdded::select('value')->where('id', $value)->first();
                    if ($value == 9) {
                        $shopValue = $amount->value;
                    }
                    if ($value == 8) {
                        $mastValue = $amount->value;
                    }
                }
            }
            $valueAdded = array_diff($request->assessment_value_added_id, $valueAdded);
        }

        if(isset($request->assessment_window_type_id) and $request->assessment_window_type_id != null){
            $window_val = PropertyWindowType::select('value')->find($request->assessment_window_type_id);
        }
        if (isset($request->assessment_categories_id) and $request->assessment_categories_id != null)
            $property_categories = PropertyCategory::whereIn('id', $request->assessment_categories_id)->get();

        if (isset($request->assessment_wall_materials_id) and $request->assessment_wall_materials_id != null)
            $wall_material = PropertyWallMaterials::select('value')->find($request->assessment_wall_materials_id);

        if (isset($request->assessment_roofs_materials_id) and $request->assessment_roofs_materials_id != null)
            $roof_material = PropertyRoofsMaterials::select('value')->find($request->assessment_roofs_materials_id);

        if (is_array($request->assessment_value_added_id) and count($request->assessment_value_added_id) > 0)
            $value_added_val = PropertyValueAdded::whereIn('id', $valueAdded)->sum('value');

        if (is_array($request->assessment_types) and count($request->assessment_types) > 0)
            $property_type_val = PropertyType::whereIn('id', $request->assessment_types)->sum('value');

        // if (isset($request->assessment_dimension_id) and $request->assessment_dimension_id != null)
        //     $property_dimension = PropertyDimension::select('value')->find($request->assessment_dimension_id);
        if (isset($request->assessment_length) and $request->assessment_length != null and (isset($request->assessment_breadth) and $request->assessment_breadth != null) ) {



            if ($request->has('property_district')) {
                $district = District::where('name', $request->property_district)->first();
                if ($district->sq_meter_value) {
                    $rate_square_meter = $district->sq_meter_value;
                }
            }

            $property_dimension = ($request->assessment_length * $request->assessment_breadth) * $rate_square_meter;
            //$property_dimension = ($request->assessment_area) * $rate_square_meter;
            //$property_dimension = $request->property_dimension * getSystemConfig(SystemConfig::CURRENT_RATE);
            //$property_dimension = PropertyDimension::select('value')->find($request->property_dimension);
        }

        if (isset($request->assessment_area) and $request->assessment_area != null) {



            if ($request->has('property_district')) {
                $district = District::where('name', $request->property_district)->first();
                if ($district->sq_meter_value) {
                    $rate_square_meter = $district->sq_meter_value;
                }
            }

            //$property_dimension = ($request->assessment_length * $request->assessment_breadth) * $rate_square_meter;
            $property_dimension = ($request->assessment_area) * $rate_square_meter;
            //$property_dimension = $request->property_dimension * getSystemConfig(SystemConfig::CURRENT_RATE);
            //$property_dimension = PropertyDimension::select('value')->find($request->property_dimension);
        }


        if (isset($request->assessment_use_id) and $request->assessment_use_id != null)
            $property_use = PropertyUse::select('value')->find($request->assessment_use_id);

        if (isset($request->assessment_zone_id) and $request->assessment_zone_id != null)
            $zones = PropertyZones::select('value')->find($request->assessment_zone_id);

        /*number of Shop available*/

        if ($shopValue > 0)
            $value_added_val = $value_added_val + ($shopValue * $no_of_shops);

        /*number of mast available*/
        if ($mastValue > 0)
            $value_added_val = $value_added_val + ($mastValue * $no_of_mast);

        // $step1 = $wall_material['value'] + $roof_material['value'] + $value_added_val;
        // $step2 = $property_type_val;
        // $step3 = $property_dimension['value'];
        // $step4 = $property_use['value'];
        // $step5 = $zones['value'];
        // $step6 = 0;
        $swimming_pool = optional(Swimming::find($request->swimming_pool))->value;
        $step1 = optional($wall_material)->value + optional($roof_material)->value + $value_added_val + optional($window_val)->value + ($swimming_pool ? $swimming_pool : 0);
        $step2 = optional($property_use)->value;
        $step3 = optional($zones)->value;
        $step4 = $property_type_val;
        //$step3 = $property_dimension['value'];
        $step0 = $property_dimension;
        $step6 = 0;


        $gated_community = $request->gated_community ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : 1;

        if (count($property_categories) && $property_categories->count()) {
            $step6 = 1;

            foreach ($property_categories as $prop_category) {
                $step6 *= $prop_category->value;
            }
        }

        //$result['rateWithoutGST'] = @(((($step1 * $step2 * $step3 * $step4) * $gated_community) + ($swimming_pool ? $swimming_pool : 0)) / ($step6 > 0 ? $step6 : 1));
        $result['rateWithoutGST'] = @((($step0 + ($step1 *  $step2 * $step3 * $step4)) * $gated_community)  + ($swimming_pool ? $swimming_pool : 0)) * ($step6 > 0 ? $step6 : 1);
        $wallMaterialPercentage = ($request->wallPer)? $request->wallPer : 0;
        $roofMaterialPercentage = ($request->roofPer)? $request->roofPer : 0;
        $valueAddedPercentage = ($request->valuePer)? $request->valuePer : 0;
        $windowTypePercentage = ($request->windowPer)? $request->windowPer : 0;

        //Total percentage of property characteristic
        $totalPercentage = array_sum([$wallMaterialPercentage, $roofMaterialPercentage, $valueAddedPercentage, $windowTypePercentage]);
        //If property characteristic exist
        if($totalPercentage){
            $result['rateWithoutGST'] = $result['rateWithoutGST'] + ($result['rateWithoutGST'] * ($totalPercentage/100));
        }

         //dd($result['rateWithoutGST']);
        //If value added exist
        $groupName = $request->group_name ? $request->group_name : 'A';
        if(is_array($request->adjustment_ids) && count($request->adjustment_ids)){
            $adjustmentPercentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', $request->adjustment_ids)->pluck('percentage')->toArray();

            $result['rateWithoutGST'] = $result['rateWithoutGST'] * ((100-array_sum($adjustmentPercentage))/100);
        }


        $result['GST'] = $result['rateWithoutGST'] * .15;

        $result['rateWithGST'] = round($result['rateWithoutGST'] + $result['GST'], 4);

        return $result;
    }

    public function saveImage(Request $request)
    {

        if ($request->hasFile('assessment_images_1')) {

            $assessment_data = $request->assessment_images_1->store(Property::ASSESSMENT_IMAGE);
        }

        return url(Image::url($assessment_data, 200, 200, ['crop']));
    }


public function createInAccessibleProperties(Request $request)
    {
        $inaccessibleProperty = new InaccessibleProperty;
        $inaccessible_property_img = null;
        try {
            if ($request->hasFile('inaccessible_property_image')) {
                $inaccessible_property_img = $request->inaccessible_property_image->store(InaccessibleProperty::INACCESSBILE_PROPERTY_IMAGE);
            }
        }catch(Exception $e){
            echo $e->getMessage();

        }
        $reason_id = $request->reason;
        $lat = $request->lat;
        $long = $request->long;
        $enumerator = $request->enumerator;
        $reason_label = PropertyInaccessible::where('id',$reason_id)->value('label');
        $inaccessibleProperty->reason = $reason_label;
        $inaccessibleProperty->inaccessbile_property_image =  $inaccessible_property_img;
        $inaccessibleProperty->inaccessbile_property_lat = $lat;
        $inaccessibleProperty->inaccessbile_property_long = $long;
        $inaccessibleProperty->enumerator = $enumerator;
        $inaccessibleProperty->save();
        return $this->success([
            "inaccessible_property" => "Saved"
            // "path" => $destinationPath
        ]);

    }


    public function createUnfinishedProperties(Request $request)
    {
        $inaccessibleProperty = new UnfinishedProperty;
        $inaccessible_property_img = null;
        try {
            if ($request->hasFile('unfinished_property_image')) {
                $inaccessible_property_img = $request->unfinished_property_image->store(UnfinishedProperty::UNFINISHED_PROPERTY_IMAGE);
            }
        }catch(Exception $e){
            echo $e->getMessage();

        }
        // $reason_id = $request->reason;
        $lat = $request->lat;
        $long = $request->long;
        $enumerator = $request->enumerator;
        // $reason_label = PropertyUnfinished::where('id',$reason_id)->value('label');
        $inaccessibleProperty->reason = 'unfinished property';
        $inaccessibleProperty->unfinished_property_image =  $inaccessible_property_img;
        $inaccessibleProperty->unfinished_property_lat = $lat;
        $inaccessibleProperty->unfinished_property_long = $long;
        $inaccessibleProperty->enumerator = $enumerator ?? 'na';
        $inaccessibleProperty->save();
        return $this->success([
            "unfinished_property" => "Saved"
            // "path" => $destinationPath
        ]);

    }


    public function updatePropertyAssessmentDetail(Request $request)
    {


        $property_id = $request->property_id;
        $length = $request->length;
        $breadth = $request->breadth;
        $area = $request->area;
        $is_map_set = $request->is_map_set;
        $detail = PropertyAssessmentDetail::where('id', '=', $request->assessment_id)->firstOrFail();
        $detail->square_meter = round($area,2);
        $detail->length = round($length,2);
        $detail->breadth = round($breadth,2);
        $detail->is_map_set = $is_map_set;
        $detail->save();
        $data = [
            'property_id' => $property_id,
            'length' => $length,
            'area' => $area
        ];

        return $this->success([
            "data" => $data,
            "detail" => $detail
            // "path" => $destinationPath
        ]);

    }


    public function updatePropertyAssessmentPensionDiscount(Request $request)
    {
        $property_id = $request->property_id;
        $is_pension_set = $request->is_pension_set;
        $detail = PropertyAssessmentDetail::where('id', '=', $request->assessment_id)->firstOrFail();
        $detail->pensioner_discount = $is_pension_set;
        $detail->save();


        return $this->success([
            "detail" => $detail
            // "path" => $destinationPath
        ]);

    }

    public function updatePropertyAssessmentDisabilityDiscount(Request $request)
    {
        $property_id = $request->property_id;
        $is_disability_set = $request->is_disability_set;
        $detail = PropertyAssessmentDetail::where('id', '=', $request->assessment_id)->firstOrFail();
        $detail->disability_discount = $is_disability_set;
        $detail->save();


        return $this->success([
            "detail" => $detail
            // "path" => $destinationPath
        ]);

    }


    public function pldcCouncilAdjustment(Request $request)
    {
        $ward = $request->ward;
        $section = $request->section;

        $property = Property::where('ward', $ward)->where('section', $section)->get();

        foreach($property as $p)
        {
            $assessment = $p->assessment()->first();

            $water_percentage = 0;
            $electrical_percentage = 0;
            $waster_precentage = 0;
            $market_percentage = 0;
            $hazardous_percentage = 0;
            $drainage_percentage = 0;
            $informal_settlement_percentage = 0;
            $easy_street_access_percentage = 0;
            $paved_tarred_street_percentage = 0;
            $group_name = '';
            $groupName = $request->group_name ? $request->group_name : 'A';

            if(is_array($request->adjustment_ids)){
                $adjustmentsArray = $request->adjustment_ids;
                foreach($adjustmentsArray as $id)
                {
                    if($id == 1){
                        $water_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 2){
                        $electrical_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 3){
                        $waster_precentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 4){
                        $market_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 5){
                        $hazardous_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 6){
                        $informal_settlement_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 7){
                        $easy_street_access_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }elseif($id == 8){
                        $paved_tarred_street_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }else{
                        $drainage_percentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', [$id])->pluck('percentage')[0];
                    }
                }
            }

            if(is_array($request->adjustment_ids)){
                $adjustmentPercentage = AdjustmentValue::where('group_name', $groupName)->whereIn('adjustment_id', $request->adjustment_ids)->pluck('percentage')->toArray();
            }


            $totalAdjustmentPercent = array_sum($adjustmentPercentage);

            $assessment_data = [
                'water_percentage' => $water_percentage,
                'electricity_percentage' => $electrical_percentage,
                'waste_management_percentage'=> $waster_precentage,
                'market_percentage'=> $market_percentage,
                'hazardous_precentage'=> $hazardous_percentage,
                'drainage_percentage'=> $drainage_percentage,
                'informal_settlement_percentage'=> $informal_settlement_percentage,
                'easy_street_access_percentage'=> $easy_street_access_percentage,
                'paved_tarred_street_percentage'=> $paved_tarred_street_percentage,
                'total_adjustment_percent' => $totalAdjustmentPercent
            ];

            $assessment->fill($assessment_data);
            $assessment->save();
        }



        return $this->success([
            "stat" => $property[0]->assessment()->first()
        ]);

    }


    public function setPropSanitation(Request $request)
    {
        $ward = $request->ward;
        $section = $request->section;

        $property = Property::where('ward', $ward)->get();


        foreach($property as $p)
        {
            $sanitation = 2;
            $assessment = $p->assessment()->first();
            $wall_id = $assessment['property_wall_materials'];

            if($wall_id == "5" || $wall_id == "6" || $wall_id == "7" || $wall_id == "8")
            {
                $sanitation = 2;
            }else if($wall_id == "2")
            {
                $sanitation = 1;
            }

            $assessment_data = [
                'sanitation' => $sanitation,
            ];

            $assessment->fill($assessment_data);
            $assessment->save();

        }



        return $this->success([
            "stat" => $property[0]->assessment()->first()
        ]);


    }

    public function updateEnumerator(Request $request)
    {
        $from_ward = $request->from_ward;
        $to_ward = $request->to_ward;
        $from_enumerator = $request->from_enumerator;
        $to_enumerator = $request->to_enumerator;


        $property = Property::whereBetween('ward', [$from_ward,$to_ward])->where('user_id',$from_enumerator)->get();


        foreach($property as $p)
        {
            $p->user_id = $to_enumerator;
            $p->save();
        }



        return $this->success([
            "stat" => $property[0]
        ]);

    }



    public function deleteProperty(Request $request)
    {
        // $property = Property::where('district','Port Loko District Council')->take(7)->get();
        $count = 0;

        // foreach($property as $pr) {
        //     $pr->landlord()->delete();
        //     $pr->occupancy()->delete();
        //     $pr->assessments()->delete();
        //     $pr->geoRegistry()->delete();
        //     $pr->categories()->detach();
        //     $pr->occupancies()->delete();
        //     $pr->payments()->delete();
        //     $pr->registryMeters()->delete();
        //     $pr->propertyInaccessible()->detach();

        //     $pr->delete();
        //     $count = $count + 1;
        // }


        // $property->landlord()->delete();
        // $propertyoperty->occupancy()->delete();
        // //$property->assessments()->delete();
        // $property->geoRegistry()->delete();
        // $property->categories()->detach();
        // $property->occupancies()->delete();
        // $property->payments()->delete();
        // $property->registryMeters()->delete();
        // $property->propertyInaccessible()->detach();

        // $property->delete();



        $raw = \DB::select('SELECT id FROM properties where district <> "Western Area Rural District"');
        $ids = array();
        foreach($raw as $pr){
            $ids[] = $pr->id;
        }

        // foreach($ids as $id) {
        //     $property = Property::findOrFail($id);
        //     $property->landlord()->delete();
        //     $property->occupancy()->delete();
        //     $property->assessments()->delete();
        //     $property->geoRegistry()->delete();
        //     $property->categories()->detach();
        //     $property->occupancies()->delete();
        //     $property->payments()->delete();
        //     $property->registryMeters()->delete();
        //     $property->propertyInaccessible()->detach();

        //     $property->delete();
        //     $count = $count + 1;
        // }
        $property = Property::where('id',$ids[0]);
        return $this->success([
            "stat" => count($ids),
            "prop" => $property
        ]);
    }

    public function getCount(Request $request) {

        $raw = \DB::select('SELECT id FROM properties where district <> "Western Area Rural District"');
        $property = Property::where('district','<>','Western Area Rural District')->count();
        $ids = array();
        foreach($raw as $pr){
            $ids[] = $pr->id;
        }

        return $this->success([
            "count" => count($ids),
            "stat" => $ids
        ]);
    }
}
