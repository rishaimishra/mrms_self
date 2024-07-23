<?php

namespace App\Http\Controllers\Admin;
use App\Grids\LandLordVerifyGrid;
use App\Exports\PropertyExport;
use App\Imports\ExcelImport;
use App\Imports\AddressImport;
use App\Grids\PropertiesGrid;
use App\Grids\AssignPropertiesGrid;
use App\Http\Controllers\Controller;
use App\Jobs\PropertyInBulk;
use App\Jobs\PropertyEnvpBulk;
use App\Jobs\PropertyNotice;
use App\Jobs\PropertyStickers;
use App\Logic\SystemConfig;
use App\Models\BoundaryDelimitation;
use App\Models\Property;
use App\Models\PropertyAssessmentDetail;
use App\Models\PropertyCategory;
use App\Models\PropertyDimension;
use App\Models\PropertyGeoRegistry;
use App\Models\PropertyInaccessible;
use App\Models\PropertyRoofsMaterials;
use App\Models\AssiegnedProperties;
use App\Models\PropertyType;
use App\Models\PropertyUse;
use App\Models\PropertyValueAdded;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyZones;
use App\Models\RegistryMeter;
use App\Models\PropertyWindowType;
use App\Models\LandlordDetail;
use App\Models\UserTitleTypes;
use App\Models\PropertySanitationType;
use App\Models\AdjustmentValue;
use App\Models\Adjustment;
use App\Models\Swimming;
use App\Models\User;
use App\Models\District;
use App\Models\InaccessibleProperty;
use App\Models\UnfinishedProperty;
use App\Models\AdditionalAddress;
use App\Models\MetaValue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio;
use App\Notifications\PaymentRequestSMS;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class PropertyController extends Controller
{
    private $properties;

    public function list(PropertiesGrid $usersGrid, Request $request)
    {
        // return "sas";
        // return $request;
        // dd($request->all());
       
        $organizationTypes = collect(json_decode(file_get_contents(storage_path('data/organizationTypes.json')), true))->pluck('label', 'value');
        $schoolTypes = collect(json_decode(file_get_contents(storage_path('data/schoolTypes.json')), true))->pluck('label', 'value');


        // $data['excel_data_properties'] = Property::with(['images', 'occupancy', 'assessment', 'geoRegistry', 'registryMeters', 'payments', 'landlord', 'assessment.typesTotal:id,label,value', 'assessment.types:id,label,value', 'assessment.valuesAdded:id,label,value', 'occupancies:id,occupancy_type,property_id', 'assessment.categories:id,label,value', 'propertyInaccessible:id,label'])
        // ->orderBy('id', 'desc')
        // ->get();

        $this->properties = Property::where('street_number','!=',null)->where('street_name','!=',null)->where('ward','!=',null)->where('constituency','!=',null)->with([
            'user',
            'landlord',
            'assessment' => function ($query) use ($request) {
                if ($request->filled('demand_draft_year')) {
                    $query->whereYear('created_at', $request->demand_draft_year);
                }
            },
            'geoRegistry',
            'user',
            'occupancies',
            'propertyInaccessible',
            'payments',
            'districts',
            'assessmentHistory'
        ])
            ->whereHas('assessment', function ($query) use ($request) {

                if ($request->filled('demand_draft_year')) {
                    $query->whereYear('created_at', $request->demand_draft_year);
                }

                if ($request->filled('is_printed')) {

                    if ($request->input('is_printed') === '1') {
                        $query->whereNotNull('last_printed_at');
                    }

                    if ($request->input('is_printed') === '0') {
                        $query->whereNull('last_printed_at');
                    }
                }

                if ($request->is_gated_community) {
                    $query->where('gated_community', $request->gated_community);
                }

            });
            // ->whereHas('districts', function($query) {
            //     $query->where('id',13);
            // });
        if (request()->user()->hasRole('Super Admin')) {
        } else {
            $this->properties->where('district', request()->user()->assign_district);
        }
        if ($request->start_date && $request->end_date) {
            $this->properties->whereBetween('properties.created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            !$request->start_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::parse($request->start_date), Carbon::now()]);
            !$request->end_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::now()->subYear(5), Carbon::parse($request->end_date)->endOfDay()]);
        }
        if ($request->dc_start_date && $request->dc_end_date) {
            $this->properties->whereBetween('properties.created_at', [
                Carbon::parse($request->dc_start_date)->startOfDay(),
                Carbon::parse($request->dc_end_date)->endOfDay()
            ]);
        } else {
            !$request->dc_start_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::parse($request->dc_start_date), Carbon::now()]);
            !$request->dc_end_date ?: $this->properties->whereBetween('properties.created_at', [Carbon::now()->subYear(5), Carbon::parse($request->dc_end_date)->endOfDay()]);
        }

        if ($request->unpaid_start_date && $request->unpaid_end_date) {

            $year = date('Y', strtotime($request->unpaid_start_date));
            //$this->properties->whereYear('properties.created_at', $year);

            $this->properties->doesntHave('payments');
        }

        if ($payment_status = $request->input('paid')) {
            if ($payment_status == 'paid') {
                $this->properties->whereHas('payments');
            } else {
                $this->properties->doesntHave('payments');
            }
        }

        if ($request->paid_start_date && $request->paid_end_date) {
            $year = date('Y', strtotime($request->paid_start_date));
            //$this->properties->whereYear('property_payments.created_at', $year);

            $this->properties->whereHas('payments', function ($query) use ($request) {
                return $query->whereBetween('property_payments.created_at', [Carbon::parse($request->paid_start_date)->startOfDay(), Carbon::parse($request->paid_end_date)->endOfDay()]);
            });
        }

        !$request->occupancy_type ?: $this->properties->whereHas('occupancy', function ($query) use ($request) {
            return $query->where('type', $request->occupancy_type);
        });

        $request->filled('property_id') && $this->properties->where('properties.id', $request->input('property_id'));

        !$request->town ?: $this->properties->where('properties.section', $request->town);
        !$request->street_name ?: $this->properties->where('properties.street_name', 'like', "%{$request->street_name}%");
        !$request->street_number ?: $this->properties->where('properties.street_number', $request->street_number);
        !$request->postcode ?: $this->properties->where('properties.postcode', $request->postcode);
        !$request->ward ?: $this->properties->where('properties.ward', $request->ward);
        !$request->district ?: $this->properties->where('properties.district', $request->district);
        !$request->province ?: $this->properties->where('properties.province', $request->province);
        !$request->chiefdom ?: $this->properties->where('properties.chiefdom', $request->chiefdom);
        !$request->constituency ?: $this->properties->where('properties.constituency', $request->constituency);

        $request->is_accessible == "0" ? $this->properties->where('is_property_inaccessible', 0) : null;
        $request->is_accessible == "1" ? $this->properties->where('is_property_inaccessible', 1) : null;

        $request->is_draft_delivered == "0" ? $this->properties->whereHas('assessment', function ($query) use ($request) {
            $query->whereYear('created_at', now()->format('Y'))->whereNull('demand_note_delivered_at');
        }) : null;
        $request->is_draft_delivered == "1" ? $this->properties->whereHas('assessment', function ($query) use ($request) {
            if ($request->dd_start_date && $request->dd_end_date) {
                $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::parse($request->dd_start_date), Carbon::parse($request->dd_end_date)]);
            } else {
                if ($request->dd_start_date) {
                    $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::parse($request->dd_start_date), Carbon::now()]);
                } else if ($request->dd_end_date) {
                    $query->whereYear('created_at', now()->format('Y'))->whereBetween('demand_note_delivered_at', [Carbon::now()->subYear(5),  Carbon::parse($request->dd_end_date)]);
                } else {
                    $query->whereYear('created_at', now()->format('Y'))->whereNotNull('demand_note_delivered_at');
                }
            }
        }) : null;

        //!$request->open_location_code ?: $this->properties->where('properties.id', $request->open_location_code);

        //!$request->open_location_code ?: $this->properties->where('properties.id', $request->open_location_code);

        !$request->digital_address ?: $this->properties->where('properties.id', $request->digital_address);

        !$request->old_digital_address ?: $this->properties->where('properties.id', $request->old_digital_address);

        !$request->is_completed ?: $this->properties->where('properties.is_completed', ($request->is_completed == 'yes' ? true : false));

        !$request->type ?: $this->properties->whereHas('types', function ($query) use ($request) {
            return $query->where('id', $request->type);
        });


        !$request->categories ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_categories', $request->categories);
        });


        !$request->wall_material ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_wall_materials', $request->wall_material);
        });


        !$request->window_type ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_window_type', $request->window_type);
        });

        !$request->compound_name ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('compound_name', 'like', "%$request->compound_name%");
        });

        !$request->roof_material ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('roofs_materials', $request->roof_material);
        });

        !$request->property_dimension ?: $this->properties->whereHas('assessment', function ($query) use ($request) {
            return $query->where('property_dimension', $request->property_dimension);
        });

        !$request->value_added ?: $this->properties->whereHas('valueAdded', function ($query) use ($request) {
            return $query->where('id', $request->value_added);
        });

        !$request->property_inaccessible ?: $this->properties->whereHas('propertyInaccessible', function ($query) use ($request) {
            return $query->where('id', $request->property_inaccessible);
        });

        $this->properties->whereHas('landlord', function ($query) use ($request) {
            //
            if ($request->owner_first_name)
                $query = $query->where('first_name', 'like', "%{$request->owner_first_name}%");

            if ($request->owner_last_name)
                $query = $query->where('surname', 'like', "%{$request->owner_last_name}%");

            if ($request->input('mobile')) {
                $query->where('mobile_1', $request->input('mobile'));
            }

            return $query;
        });

        $this->properties->whereHas('occupancy', function ($query) use ($request) {
            //
            if ($request->tenant_first_name)
                $query = $query->where('tenant_first_name', 'like', "%{$request->tenant_first_name}%");

            if ($request->tenant_middle_name)
                $query = $query->where('middle_name', 'like', "%{$request->tenant_middle_name}%");

            if ($request->tenant_last_name)
                $query = $query->where('surname', 'like', "%{$request->tenant_last_name}%");


            return $query;
        });
        //!$request->landloard_name || $this->properties->orWhere('organization_name', 'like', '%' . $request->landloard_name . '%');

        !$request->telephone_number || $this->properties->whereHas('landlord', function ($query) use ($request) {
            return $query->where('mobile_1', 'like', '%' . $request->telephone_number . '%');
        });

        !$request->open_location_code || $this->properties->whereHas('geoRegistry', function ($query) use ($request) {
          return $query->where('open_location_code', $request->open_location_code);
        });

        !$request->name ?: $this->properties->whereHas('user', function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->name . '%');
        });

// echo $request->input('is_organization');
// echo $request->input('organization_type');
// die;
        if ($request->input('is_organization') == 1 && $request->input('organization_type') || $request->input('school_type')) {
            // return "if first";
            if($request->input('school_type')){
// return "1"; 
                $this->properties->where('organization_school_type', $request->input('school_type'));
            }else{
             $this->properties->where('organization_type', $request->input('organization_type'))->where('is_organization', true);
            }
            

        }
        if ($request->input('is_organization') && $request->input('is_organization') == 0) {
            return "if second";
            $this->properties->where('is_organization', false);
        }
        // if ($request->input('is_organization') == 1 && $request->input('organization_type') && $request->input('school_type')) {
        //     return "if 3rd";
        //     $this->properties->where('properties.organization_school_type', $request->input('school_type'));
        // }
// print_r($this->properties->get());
//         die;
        

        $data['types'] = PropertyCategory::pluck('label', 'id')->prepend('Select', '');

        $data['categories'] = PropertyType::pluck('label', 'id')->prepend('Select', '');
        $data['windowtype'] = PropertyWindowType::pluck('label', 'id')->prepend('Select Window Type', '');
        $data['wallMaterial'] = PropertyWallMaterials::pluck('label', 'id')->prepend('Wall Material', '')->prepend('Select wall material', '');
        $data['roofMaterial'] = PropertyRoofsMaterials::pluck('label', 'id')->prepend('Roof Material', '')->prepend('Select roof material', '');
        $data['propertyDimension'] = PropertyDimension::pluck('label', 'id')->prepend('Dimensions', '');
        $data['valueAdded'] = PropertyValueAdded::where('is_active', true)->pluck('label', 'id')->prepend('Value Added', '');
        $data['town'] = BoundaryDelimitation::distinct()->orderBy('section')->pluck('section', 'section')->prepend('Select Town', '');;

        if (request()->user()->hasRole('Super Admin')) {
            $data['district'] = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort()->prepend('Select District', '');
            $data['province'] = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort()->prepend('Select Province', '');;
            $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select Ward', '');
            $data['chiefdom'] = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort()->prepend('Select Chiefdom', '');
            $data['constituency'] = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort()->prepend('Select Constituency', '');
        } else {
            $data['district'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('district')->pluck('district', 'district')->sort()->prepend('Select District', '');
            $data['province'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('province')->pluck('province', 'province')->sort()->prepend('Select Province', '');;
            $data['ward'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('ward')->pluck('ward', 'ward')->sort()->prepend('Select Ward', '');
            $data['chiefdom'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort()->prepend('Select Chiefdom', '');
            $data['constituency'] = BoundaryDelimitation::where('district', request()->user()->assign_district)->distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort()->prepend('Select Constituency', '');
        }
        $data['digital_address'] = PropertyGeoRegistry::distinct()->orderBy('property_id')->pluck('digital_address', 'digital_address')->sort()->prepend('Select Digital Address', '');

        $data['request'] = $request;

        $data['property_inaccessibles'] = PropertyInaccessible::where('is_active', 1)->pluck('label', 'id')->prepend('Select Property Inaccessible');

        $data['street_names'] = Property::distinct('street_name')->orderBy('street_name')->pluck('street_name', 'street_name')->sort()->prepend('Select street name', '');
        $data['street_numbers'] = Property::distinct('street_number')->orderBy('street_number')->pluck('street_number', 'street_number')->sort()->prepend('Select street number', '');
        $data['additional_address'] = Property::distinct('additional_address_id')->orderBy('additional_address_id')->pluck('additional_address_id', 'additional_address_id')->sort()->prepend('Select additional address', '');
        $data['propertyArea'] = Property::distinct('propertyArea')->orderBy('propertyArea')->pluck('propertyArea', 'propertyArea')->sort()->prepend('Select area', '');
        $data['postcodes'] = Property::distinct('postcode')->orderBy('postcode')->pluck('postcode', 'postcode')->sort()->prepend('Select post code', '');
        $data['organizationTypes'] = $organizationTypes;
        $data['schoolTypes'] = $schoolTypes;
        $data['first_name'] = LandlordDetail::distinct('first_name')->orderBy('first_name')->pluck('first_name', 'first_name')->sort()->prepend('Select owner name', '');
        $data['middle_name'] = LandlordDetail::distinct('middle_name')->orderBy('middle_name')->pluck('middle_name', 'middle_name')->sort()->prepend('Select owner middle name', '');
        $data['surname'] = LandlordDetail::distinct('surname')->orderBy('surname')->pluck('surname', 'surname')->sort()->prepend('Select owner last name', '');
        //return view('admin.payments.bulk-receipt')->with(['properties' => $this->properties->latest()->get()]);

        if ($request->download_pdf_in_bulk && $request->download_pdf_in_bulk == 1) {
            $bulkDemand = new PropertyInBulk();
            return $bulkDemand->handle($this->properties, $request->demand_draft_year);
        }

        if ($request->download_stickers && $request->download_stickers == 1) {

            $stickers = new PropertyStickers();

            $nProperty = $this->properties->withAssessmentCalculation($request->input('demand_draft_year'))
                ->having('current_year_payment', '>', 0)
                ->having('total_payable_due', 0)
                ->orderBy('total_payable_due')
                ->get();
            return $stickers->handle($nProperty, $request);
        }

        if ($request->download_notice && $request->download_notice == 1) {

            //dd($this->properties->get());

            $notices = new PropertyNotice();

            return $notices->handle($this->properties->latest()->get());
        }

        if ($request->download_excel_in_bulk && $request->download_excel_in_bulk == 1) {
            return \Excel::download(new PropertyExport($this->properties), date('Y-m-d-H-i-s') . '-properties.xlsx');
        }

        if ($request->bulk_demand && $request->bulk_demand == 2 && $this->properties->count() > 0) {
            
            $coordinates = $this->getMapCoordinates();
            $points = $coordinates[0];
            $center = $coordinates[1];

            return view('admin.properties.poly-map', compact('points', 'center'));

            $polygons = $this->properties->latest()->get();
        }

        if (!isset($request->sort_by)) {
            $this->properties = $this->properties->latest('properties.updated_at');
        }
        if ($request->sort_by == "is_completed") {
            $this->properties = $this->properties->orderBy('is_completed', $request->sort_dir)->orderBy('is_draft_delivered', $request->sort_dir);
        }
        // $data['list_user'] = User::pluck('name', 'name')->toArray();
        // dd($this->properties->get());
        return $usersGrid
            ->create(['query' => $this->properties, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.properties.list', $data);
        //return view('admin.properties.list',$data);

    }

    public function listInaccessibleProperties()
    {
        $property = InaccessibleProperty::where('id','>',0)->get();
        return view('admin.properties.inaccessiblelist',compact('property'));
    }

    public function listUnfinishedProperties()
    {
        $property = UnfinishedProperty::where('id','>',0)->get();
        return view('admin.properties.unfinishedlist',compact('property'));
    }

    public function deleteMeter($id)
    {
        $registryMeter = RegistryMeter::findOrFail($id);

        if ($registryMeter->hasImage()) {
            unlink($registryMeter->getImage());
        }

        $registryMeter->delete();

        return response()->json(['success' => true]);
    }

    public function getMapCoordinates()
    {
    //   return  $properties = $this->properties->latest()->get();
    $properties = $this->properties = Property::with([
        'landlord',
        'geoRegistry',
        'user',
        'districts'
    ])->get();
        $points = [];
        $center = null;

        if ($properties->count()) {
            foreach ($properties as $key => $property) {
                // return $property->id;
                // echo($property->street_number == null);
                if (optional($property->geoRegistry)->dor_lat_long) {
                    $point = explode(', ', $property->geoRegistry->dor_lat_long);
                } else {
                    continue;
                }
               
                if ($property->assessment->getTotalPaid() == 0 ){
                    if($property->street_number == null && $property->assessment->getCurrentYearAssessmentAmount() == 0 && $property->ward == null && $property->constituency == null && $property->street_name == null){
                        // return "black";
                        $icon = "https://i.ibb.co/ysShF3B/location-pin.png";
                    }else{
                        $icon = "https://maps.google.com/mapfiles/ms/icons/orange-dot.png";
                    }
                } else if ($property->assessment->getCurrentYearTotalDue() > 0) {
                    $icon = "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
                } else if ($property->assessment->getCurrentYearTotalDue() < 0) {
                    $icon = "https://maps.google.com/mapfiles/ms/icons/green-dot.png";
                } else if ($property->assessment->getCurrentYearTotalDue() == 0) {
                    $icon = "https://maps.google.com/mapfiles/ms/icons/orange-dot.png";
                } else if(isset($property->user->assign_district_id)){
                    $icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                }else if($property->street_number == null ){
                    // return "black";
                    $icon = "https://i.ibb.co/ysShF3B/location-pin.png";
                }
                else{
                    $icon = "http://maps.google.com/mapfiles/ms/icons/pink-dot.png";
                }
                // if ($property->is_admin_created == 1) {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                // } else if ($property->assessment->getCurrentYearTotalDue() - $property->assessment->getCurrentYearTotalPayment() == 0) {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
                // } else {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
                // }

                // if ($property->is_admin_created == 1) {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                // } else if ($property->assessment->getCurrentYearTotalPayment() != 0) {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
                // } else {
                //     $icon = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
                // }
                if (count($point) == 2) {
                    $points[] = [$property->getAddress(), $point[0], $point[1], $key++, $icon];
                }

                if ($property->geoRegistry->dor_lat_long) {
                    $center = $property->geoRegistry->dor_lat_long;
                }
            }
        }

        return [json_encode($points), $center];
    }

    public function downloadPdf(Request $request)
    {
        
        $this->validate($request, [
            'properties' => 'required',

        ], [
            'properties.required' => 'Select at least one property'
        ]);

        $this->properties = Property::with([
            'landlord',
            'geoRegistry',
            'user',
            'districts'
        ]);

        if($request->delete_property){
            
            $this->properties->whereIn('properties.id', explode(',', $request->properties))->delete();
            return \Redirect::back()->with('success', 'Record deleted Successfully');
        }

        if($request->send_sms){
            $this->sendPaymentRequestSMS(explode(',', $request->properties));
            return \Redirect::back()->with('success', 'SMS sent Successfully');
        }

        $this->properties = $this->properties->whereIn('properties.id', explode(',', $request->properties));

        if ($request->download_excel) {

            $this->properties->with([
                'assessment' => function ($query) use ($request) {
                    $query->whereYear('created_at', $request->input('demand_draft_year'))
                        ->with('categories', 'types', 'valuesAdded', 'dimension', 'wallMaterial', 'roofMaterial', 'zone', 'swimming');
                },
            ]);

            $this->properties->whereHas('assessment', function ($query) use ($request) {
                $query->whereYear('created_at', $request->input('demand_draft_year'));
            });

            return \Excel::download(new PropertyExport($this->properties), date('Y-m-d-H-i-s') . '-properties.xlsx');
        }

        if ($request->download_stickers && $request->download_stickers == 1) {

            $stickers = new PropertyStickers();

            $nProperty = $this->properties->withAssessmentCalculation($request->input('demand_draft_year'))
                ->having('current_year_payment', '>', 0)
                ->having('total_payable_due', 0)
                ->orderBy('total_payable_due')
                ->get();

            return $stickers->handle($nProperty, $request);
        }

        if ($request->download_envelope) {
            $bulkDemand = new PropertyEnvpBulk();

            return $bulkDemand->handle($this->properties, $request->demand_draft_year);
        }

        $bulkDemand = new PropertyInBulk();

        return $bulkDemand->handle($this->properties, $request->demand_draft_year);
    }

    public function show(Request $request)
    {
        /* @var $property Property */
        $property = Property::findOrFail($request->property);

        // Generate current year assessment if missing
         $property->generateAssessments();

        // load sub modals
        $property->load([
            'images',
            'occupancy',
            'assessments' => function ($query) {
                $query->with('types', 'valuesAdded', 'categories')->latest();
            },
            'geoRegistry',
            'payments',
            'landlord',
            'propertyInaccessible'
        ]);

        // return $property;
        if (request()->user()->hasRole('Super Admin')) {
            $data['town'] = BoundaryDelimitation::distinct()->orderBy('section')->pluck('section', 'section');
            $data['chiefdom'] = BoundaryDelimitation::distinct()->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort();
            $data['district'] = BoundaryDelimitation::distinct()->orderBy('district')->pluck('district', 'district')->sort();
            $data['province'] = BoundaryDelimitation::distinct()->orderBy('province')->pluck('province', 'province')->sort();
            $data['ward'] = BoundaryDelimitation::distinct()->orderBy('ward')->pluck('ward', 'ward')->sort();
            $data['constituency'] = BoundaryDelimitation::distinct()->orderBy('constituency')->pluck('constituency', 'constituency')->sort();
        } else {
            $data['town'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('section')->pluck('section', 'section');
            $data['chiefdom'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('chiefdom')->pluck('chiefdom', 'chiefdom')->sort();
            $data['district'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('district')->pluck('district', 'district')->sort();
            $data['province'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('province')->pluck('province', 'province')->sort();
            $data['ward'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('ward')->pluck('ward', 'ward')->sort();
            $data['constituency'] = BoundaryDelimitation::distinct()->where('district', request()->user()->assign_district)->orderBy('constituency')->pluck('constituency', 'constituency')->sort();
        }
        //  $get_cat = PropertyCategory::where('is_active', 1)->get();
        // $options = [];
        // foreach ($get_cat as $key => $value) {
        //     $options[$value->value]=$value->label;
        // }
        // return $options;
         $data['categories'] = PropertyCategory::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['types'] = PropertyType::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['window_types'] = PropertyWindowType::distinct()->where('is_active', 1)->pluck('label', 'id');
        //$data['window_types_values'] = PropertyWindowType::distinct()->where('is_active', 1)->pluck('value', 'id');
        $data['wall_materials'] = PropertyWallMaterials::distinct()->where('is_active', 1)->pluck('label','id');
        $data['sanitation'] = PropertySanitationType::pluck('label','id');
        $data['adjustment_values'] = Adjustment::pluck('name','id');
        //$data['wall_material_values'] = PropertyWallMaterials::distinct()->where('is_active', 1)->pluck('value', 'id');
        $data['roofs_materials'] = PropertyRoofsMaterials::distinct()->where('is_active', 1)->pluck('label', 'id');
        //$data['roofs_material_values'] = PropertyRoofsMaterials::distinct()->where('is_active', 1)->pluck('value', 'id');
        $data['property_dimension'] = PropertyDimension::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['value_added'] = PropertyValueAdded::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['property_use'] = PropertyUse::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['zone'] = PropertyZones::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['occupancy_type'] = ['Owned Tenancy' => 'Owned Tenancy', 'Rented House' => 'Rented House', 'Unoccupied House' => 'Unoccupied House'];
        $data['id_type'] = ['National ID' => 'National ID', 'Passport' => 'Passport', 'Driver’s License' => 'Driver’s License', 'Voter ID' => 'Voter ID', 'other' => 'Other'];
        $data['org_type'] = ['Government' => 'Government', 'NGO' => 'NGO', 'Business' => 'Business', 'School' => 'School', 'Religious' => 'Religious', 'Diplomatic Mission' => 'Diplomatic Mission', 'Hospital' => 'Hospital', 'Other' => 'Other'];
        $data['gender'] = ['m' => 'Male', 'f' => 'Female'];
        $data['title'] = 'Details';
        $data['property'] = $property;
        $data['usertitles'] = UserTitleTypes::distinct()->where('is_active', 1)->pluck('label', 'id');
        $data['selected_occupancies'] = $property->occupancies->pluck('occupancy_type')->toArray();

        $data['property_inaccessable'] = PropertyInaccessible::where('is_active', 1)->pluck('label', 'id')->toArray();
        $data['selected_property_inaccessable'] = $property->propertyInaccessible()->pluck('id')->toArray();
        $data['swimmings'] = Swimming::where('is_active', 1)->pluck('label', 'id')->prepend('Select', '')->toArray();

        return view('admin.properties.view', $data);
    }

    public function downloadEnvelope($id, $year = null)
    {
        $year = !$year ? date('Y') : $year;

       return $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        $paymentInQuarter = $property->getPaymentsInQuarter($year);
        $district = District::where('name', $property->district)->first();
        $pdf = \PDF::loadView('admin.envelope.single-envelope', compact('property', 'paymentInQuarter', 'assessment', 'district', 'year'));

        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '-envelope.pdf');

        //return view('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));
    }

    public function create()
    {
    }

    public function assignProperty(AssignPropertiesGrid $assignusersGrid, Request $request)
    {
        $this->properties = Property::where('street_number','=',null)->where('street_name','=',null)->where('ward','=',null)->where('constituency','=',null)->with([
            'user',
            'landlord',
            'assessment',
            'geoRegistry',
            'user',
            'occupancies',
            'propertyInaccessible',
            'payments',
            'districts',
            'assessmentHistory'
        ]);
        $data['title'] = 'Details';
        $data['request'] = $request;
        $data['assessmentOfficer'] = $assessmentUser = User::where('ward','!=', 'NA')->pluck('name', 'id')->prepend('Select Officer', '');

        return $assignusersGrid
            ->create(['query' => $this->properties, 'request' => $request])
            ->renderOn('admin.properties.assign', $data);
        // return view('admin.properties.assign', $data);
    }


    public function verifyLandlord(Request $request)
    {


        if($request->has('search'))
        {
            $search = $request->search;
            $landlords = LandlordDetail::where('verified',0)->where('property_id',$search)->simplePaginate(10);
        }else{
            $landlords = LandlordDetail::where('verified',0)->simplePaginate(10);
        }

        $state = 0;
        return view('admin.properties.verifylandlords', compact('landlords','state'));

    }


    public function verifyProperty(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $properties = Property::where('verified',0)->where('id',$search)->simplePaginate(10);
        }else{
            $properties = Property::where('verified',0)->simplePaginate(10);
        }

        $state = 0;
        return view('admin.properties.verifyproperties', compact('properties','state'));

    }

    public function rejectedLandlord(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $landlords = LandlordDetail::where('verified',-2)->where('property_id',$search)->simplePaginate(10);
        }else{
            $landlords = LandlordDetail::where('verified',-2)->simplePaginate(10);
        }
        $state = -2;
        return view('admin.properties.verifylandlords', compact('landlords','state'));

    }

    public function rejectedProperty(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $properties = Property::where('verified',-2)->where('id',$search)->simplePaginate(10);
        }else{
            $properties = Property::where('verified',-2)->simplePaginate(10);
        }
        $state = -2;
        return view('admin.properties.verifyproperties', compact('properties','state'));

    }

    public function approvedLandlord(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $landlords = LandlordDetail::where('verified',1)->where('property_id',$search)->simplePaginate(10);
        }else{
            $landlords = LandlordDetail::where('verified',1)->simplePaginate(10);
        }
        $state = 1;
        return view('admin.properties.verifylandlords', compact('landlords','state'));

    }

    public function approvedProperty(Request $request)
    {
        if($request->has('search'))
        {
            $search = $request->search;
            $properties = Property::where('verified',1)->where('id',$search)->simplePaginate(10);
        }else{
            $properties = Property::where('verified',1)->simplePaginate(10);
        }
        $state = 1;
        return view('admin.properties.verifyproperties', compact('properties','state'));

    }


    public function approveLandlord($id, Request $request)
    {

        $landlords = LandlordDetail::where('id',$id)->first();
        $landlords->verified = 1;
        $landlords->first_name = $landlords->temp_first_name;
        $landlords->middle_name = $landlords->temp_middle_name;
        $landlords->surname = $landlords->temp_surname;
        $landlords->street_number = $landlords->temp_street_number;
        $landlords->street_numbernew = $landlords->temp_street_numbernew;
        $landlords->street_name = $landlords->temp_street_name;
        $landlords->email = $landlords->temp_email;
        $landlords->mobile_1 = $landlords->temp_mobile_1;
        $landlords->save();
        return redirect()->route('admin.verify.landlord');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }

    public function approveProperty($id, Request $request)
    {

        $property = Property::where('id',$id)->first();
        $property->verified = 1;
        //$property->street_number = $property->temp_street_number;
        $property->street_numbernew = $property->temp_street_numbernew;
        $property->street_name = $property->temp_street_name;
        $property->save();
        return redirect()->route('admin.verify.property');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }


    public function rejectLandlord($id, Request $request)
    {

        $landlords = LandlordDetail::where('id',$id)->first();
        $landlords->verified = -2;
        $landlords->save();
        return redirect()->route('admin.verify.landlord');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }

    public function rejectProperty($id, Request $request)
    {

        $property = Property::where('id',$id)->first();
        $property->verified = -2;
        $property->save();
        return redirect()->route('admin.verify.property');
       //return view('admin.properties.verifylandlords', compact('landlords'));

    }


    public function saveAssignProperty(Request $request)
    {
        // return $request;
        // return request()->user()->id;
        $this->validate($request, [
            'assessment_officer' => 'required|exists:users,id',
          //  'dor_lat_long' => 'required'
        ]);

        // If User uploads a Excel file
        if($request->bulk_lat_long_file) {
            $users = \Excel::toArray(new ExcelImport, $request->file('bulk_lat_long_file'));

            $phones = array_map(function($iter){
                $numbers = array();
                foreach($iter as $key => $item){
                        $numbers[] = $item[0];
                }
                return $numbers;
            }, $users);
            array_walk_recursive($phones, function ($value, $key) use (&$numbers){
                $numbers[] = $value;
            }, $numbers);

            for($i=0;$i<count($numbers);$i++){
                $assessmentOfficer = User::findOrFail($request->assessment_officer);
                $property = $assessmentOfficer->properties()->firstOrNew(['id' => null]);
                $property->is_admin_created = 1;
                $property->save();
                $property->landlord()->firstOrCreate(["property_id" => $property->id]);
                $property->occupancy()->firstOrCreate(["property_id" => $property->id]);
                if ($property->assessment()->exists()) {

                    $assessment = $property->generateAssessments();
                } else {
                    $assessment = $property->assessment()->firstOrCreate(["property_id" => $property->id]);
                }

                $geoRegistry = $property->geoRegistry()->firstOrCreate(["property_id" => $property->id]);
                //dd($numbers[$i]);


                        $geoRegistry->fill(['dor_lat_long' => $numbers[$i]]);
                        $geoRegistry->save();
                        $assigned_prop = new AssiegnedProperties();
       
                        $assigned_prop->officer_id = $assessmentOfficer->id;
                        $assigned_prop->latlong = $request->dor_lat_long;
                        $assigned_prop->user_id = request()->user()->id;
                        $assigned_prop->property_id = $property->id ? $property->id : null;
                         $assigned_prop->save();
           }
        }else{
         // If User uploads a Excel file
            // return "else";
        $assessmentOfficer = User::findOrFail($request->assessment_officer);
        $property = $assessmentOfficer->properties()->firstOrNew(['id' => null]);
        $property->is_admin_created = 1;
        $property->save();
        $property->landlord()->firstOrCreate(["property_id" => $property->id]);
        $property->occupancy()->firstOrCreate(["property_id" => $property->id]);
        if ($property->assessment()->exists()) {

            $assessment = $property->generateAssessments();
        } else {
            $assessment = $property->assessment()->firstOrCreate(["property_id" => $property->id]);
        }

        $geoRegistry = $property->geoRegistry()->firstOrCreate(["property_id" => $property->id]);

            $geoRegistry->fill(['dor_lat_long' => $request->dor_lat_long]);
            $geoRegistry->save();
        $assigned_prop = new AssiegnedProperties();
       
        $assigned_prop->officer_id = $assessmentOfficer->id;
        $assigned_prop->latlong = $request->dor_lat_long;
        $assigned_prop->user_id = request()->user()->id;
        $assigned_prop->property_id = $property->id ? $property->id : null;
         $assigned_prop->save();
        }

        return redirect()->back()->with('success', 'New Property Assigned Successfully!');
    }

    public function destroy(Request $request)
    {
        /* @var $property Property */
        $property = Property::findOrFail($request->property);

        $property->landlord()->delete();
        $property->occupancy()->delete();
        //$property->assessments()->delete();
        $property->geoRegistry()->delete();
        $property->categories()->detach();
        $property->occupancies()->delete();
        $property->payments()->delete();
        $property->registryMeters()->delete();
        $property->propertyInaccessible()->detach();

        $property->delete();

        return redirect()->back()->with($this->setMessage('Property successfully deleted', true));
    }


    public function saveLandlord(Request $request)
    {
        //dd($request->all());
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'landlord_id' => "required|integer",
            'is_organization' => 'required|boolean',
            'organization_name' => 'nullable|string|max:255',
            'organization_type' => 'nullable|string|max:255',
            'organization_tin' => 'nullable|string|max:255',
            'organization_addresss' => 'nullable|string|max:255',
            'first_name' => 'required_if:is_organization,0|nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required_if:is_organization,0|nullable|string|max:255',
            'sex' => 'required_if:is_organization,0|nullable|string|max:255',
            'street_number' => 'required|string',
            'email' => "nullable|email",
            'street_name' => 'required|string|max:255|nullable',
            'tin' => 'nullable|string|max:255',
            'id_type' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'ward' => 'required|string',
            'constituency' => 'required|string',
            'section' => 'required|string|max:255',
            'chiefdom' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput()->with('id', 'landloard');
        }
        $data = $request->all();
        unset($data['landlord_id']);
        unset($data['property_id']);
        unset($data['organization_name']);
        unset($data['organization_tin']);
        unset($data['organization_type']);
        unset($data['organization_addresss']);


        $property = Property::findorFail($request->property_id);

        $property->organization_name = $request->organization_name;
        $property->organization_type = $request->organization_type;
        $property->organization_tin = $request->organization_tin;
        $property->organization_addresss = $request->organization_addresss;
        $property->is_organization = $request->is_organization;
        $property->save();

        $landlord = $property->landlord()->first();

        if ($request->hasFile('image')) {
            if ($landlord->hasImage()) {
                unlink($landlord->getImage());
            }
            $data['image'] = $request->image->store(Property::ASSESSMENT_IMAGE);
        }

        $landlord->fill($data);

        $landlord->save();


        return redirect()->back()->with('success', 'Landlord details Updated Successfully !');
    }

    public function sensSmsLandlord(Request $request){
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'landlord_id' => "required|integer",
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput()->with('id', 'landloard');
        }
        $this->sendPaymentRequestSMS([$request->property_id]);
        return \Redirect::back()->with('success', 'SMS sent Successfully');

    }



    public function saveProperty(Request $request)
    {
        $v = Validator::make($request->all(), [
            "property_id" => "required|integer",
            'street_number' => 'required|string',
            'street_name' => 'required|string|max:255|nullable',
            'ward' => 'required|string',
            'constituency' => 'required|string',
            'section' => 'required|string|max:255',
            'chiefdom' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->with('id', 'property');
        }

        $data = $request->all();

        $property = Property::findorFail($request->property_id);
        $property->fill($data);
        $property->is_property_inaccessible = ($request->property_inaccessable && count($request->property_inaccessable)) ? true : false;
        $property->is_draft_delivered = $request->is_draft_delivered ? $request->is_draft_delivered : 0;
        $property->delivered_name = $request->delivered_name;
        $property->delivered_number = $request->delivered_number;

        if ($request->hasFile('delivered_image')) {
            $property->delivered_image = $request->delivered_image->store(Property::DELIVERED_IMAGE);
        }

        $property->save();

        $property->propertyInaccessible()->sync($request->property_inaccessable);

        return redirect()->back()->with('success', 'Property details Updated Successfully !');
    }

    public function saveOccupancy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "occupancy_id" => "required|integer",
            "property_id" => "required|integer",
            'occupancy_type' => 'nullable|array',
            'occupancy_type.*' => 'nullable|in:Owned Tenancy,Rented House,Unoccupied House',
            "tenant_first_name" => "nullable|string|max:50",
            "middle_name" => "nullable|string|max:40",
            "surname" => "nullable|string|max:30",
            "mobile_1" => "nullable|string|max:15",
            "mobile_2" => "nullable|string|max:15"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->with('id', 'occupancy');
        }

        $data = $request->all();

        $property = Property::findorFail($request->input('property_id'));

        $occupancy = $property->occupancy()->first();
        $occupancy->fill($data);
        $occupancy->save();

        if (count(array_filter($request->occupancy_type))) {
            foreach (array_filter($request->occupancy_type) as $types) {
                $property->occupancies()->firstOrcreate(['occupancy_type' => $types]);
            }
            $property->occupancies()->whereNotIn('occupancy_type', array_filter($request->occupancy_type))->delete();
        }

        return redirect()->back()->with('success', 'Occupancy details Updated Successfully !');
    }
    public function calculateAssessment($request){
        // return $request;
        $district_name = $request->district_name;
        $district_value = District::where('name',$district_name)->first();
        $onetownlotincat = $district_value->sq_meter_value;
        // $onetownlotincat = 250000;
        $onetownlot = 3750;
        $persquare = $onetownlotincat / $onetownlot;
        $floorarea = $request->length * $request->breadth;
        $floorareavalue = $persquare * $floorarea;
        $category = PropertyCategory::find($request->property_categories[0]??0);
       if ($category) {
        $cat_value = $category->value;
       }
       $wall_value = $request->property_wall_materials_percentage;
       $roof_value = $request->property_roof_materials_percentage;
       $window_type_value = $request->property_window_materials_percentage;
        $sanitation = PropertySanitationType::find($request->property_sanitation);
       if ($sanitation) {
         $san_value = $sanitation->value;
       }
       $habitable_floor = PropertyType::find($request->property_types[0]??0);
       if ($habitable_floor) {
        $hab_value = $habitable_floor->value;
       }
       $property_use = PropertyUse::find($request->property_use);
       if ($property_use) {
         $prop_use_value = $property_use->value;
       }
       $zone= 1;
       $valuesArray = json_decode($request->property_value_added_percentage, true);
        // Initialize the sum variable
        $sum = 0;

        // Iterate through the array and sum the numeric parts
        foreach ($valuesArray as $value) {
            $parts = explode(',', $value);
            if (isset($parts[2])) {
                $sum += (int)$parts[2]; // Convert to integer and add to sum
            }
        }

    // Save the sum in a variable
     $totalValueAdded = $sum;

    $additions = $wall_value + $roof_value + $window_type_value +  $totalValueAdded ;
    $multipliers = $prop_use_value * $san_value * $cat_value * $zone * $hab_value; 

     $calculatedassessedvalue = ($floorareavalue + $additions) * $multipliers ;
      $counciladjustment = AdjustmentValue::whereIn('adjustment_id',$request->property_council_adjustments)->where('id','<',10)->sum('percentage');
       $actualadjustment = $counciladjustment / 100 ;

       $calculatedNetAssessedvalue = $calculatedassessedvalue - ($actualadjustment * $calculatedassessedvalue);
      return ['newassessmentvalue'=>$calculatedassessedvalue, 'newnetassessedvalue'=> $calculatedNetAssessedvalue];
    }
    public function saveAssessment(Request $request)
    {
        // return $request;
          $assessmentValue = $this->calculateAssessment($request);
           $assessmentValue['newassessmentvalue'];
           $assessmentValue['newnetassessedvalue'];
        $request->validate([
            "assessment_id" => "required|integer",
            "property_id" => "required|integer",
            'property_categories' => 'nullable|array',
            'property_categories.*' => 'nullable|exists:property_categories,id',
            "property_types" => "required|array|max:2",
            "property_types.*" => 'required|exists:property_types,id',
            "property_types_total" => "nullable|array|max:2",
            "property_types_total.*" => 'nullable|exists:property_types,id',
            "property_wall_materials" => "required|integer",
            "roofs_materials" => "required|integer",
            "property_dimension" => "nullable|integer",
            "property_sanitation" => "nullable|integer",
            "property_value_added.*" => "required|exists:property_value_added,id",
            "property_use" => "required|integer",
            "zone" => "required|integer",
            'assessment_images_1' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'assessment_images_2' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            
        ]);

        $data = $request->except(['property_types', 'property_types_total', 'property_value_added', 'assessment_images_1', 'assessment_images_2']);
        //$data = $request->except(['property_types', 'property_value_added', 'assessment_images_1', 'assessment_images_2']);
        /* @var $property Property */

        $property = Property::findorFail($request->input('property_id'));

        /* @var $assessment PropertyAssessmentDetail */
        $assessment = $property->assessment()->findOrFail($request->input('assessment_id'));

        if ($request->hasFile('assessment_images_1')) {
            if ($assessment->hasImageOne()) {
                unlink($assessment->getImageOne());
            }
            $data['assessment_images_1'] = $request->file('assessment_images_1')->store(Property::ASSESSMENT_IMAGE);
        }

        if ($request->hasFile('assessment_images_2')) {
            if ($assessment->hasImageTwo()) {
                unlink($assessment->getImageTwo());
            }
            $data['assessment_images_2'] = $request->file('assessment_images_2')->store(Property::ASSESSMENT_IMAGE);
        }

        /* @var $assessment PropertyAssessmentDetail */
        $data['gated_community'] = $data['gated_community'] ? getSystemConfig(SystemConfig::OPTION_GATED_COMMUNITY) : 2;
        $data['sanitation'] = $request->property_sanitation;



        $water_percentage = 0;
        $electrical_percentage = 0;
        $waster_precentage = 0;
        $market_percentage = 0;
        $hazardous_percentage = 0;
        $drainage_percentage = 0;
        $informal_settlement_percentage = 0;
        $easy_street_access_percentage = 0;
        $paved_tarred_street_percentage = 0;
        if(is_array($request->property_council_adjustments)){
            $adjustmentsArray = $request->property_council_adjustments;
            foreach($adjustmentsArray as $id)
            {
                $name_perc = Adjustment::where('id',$id)->pluck('name');
                if($id == 1){
                    $water_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 2){
                    $electrical_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 3){
                    $waster_precentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 4){
                    $market_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 5){
                    $hazardous_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 6){
                    $informal_settlement_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 7){
                    $easy_street_access_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }elseif($id == 8){
                    $paved_tarred_street_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }else{
                    $drainage_percentage = AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')->count() > 0 ? AdjustmentValue::where('group_name', $request->council_group_name)->whereIn('adjustment_id', [$id])->pluck('percentage')[0] : 0;
                }
            }
        }

        $data['water_percentage'] = $water_percentage;
        $data['electrical_percentage'] = $electrical_percentage;
        $data['waste_management_percentage'] = $waster_precentage;
        $data['market_percentage'] = $market_percentage;
        $data['hazardous_precentage'] = $hazardous_percentage;
        $data['informal_settlement_percentage'] = $informal_settlement_percentage;
        $data['easy_street_access_percentage'] = $easy_street_access_percentage;
        $data['paved_tarred_street_percentage'] = $paved_tarred_street_percentage;
        $data['drainage_percentage'] = $drainage_percentage;
        $data['property_wall_materials'] = $request->property_wall_materials;
        $data['roofs_materials'] =$request->roofs_materials;
        $data['property_window_type']=$request->property_window_type;
        $data['wall_material_type']=$request->property_wall_materials_type;
        $data['roof_material_type']=$request->property_roof_materials_type;
        $data['window_type_type']=$request->property_window_materials_type;
        $data['wall_material_percentage']=$request->property_wall_materials_percentage;
        $data['roof_material_percentage']=$request->property_roof_materials_percentage;
        $data['window_type_percentage']=$request->property_window_materials_percentage;
        $data['property_assessed_value']= $assessmentValue['newassessmentvalue'];
        $data['property_rate_without_gst']= $assessmentValue['newassessmentvalue'];
        $data['net_property_assessed_value']=$assessmentValue['newnetassessedvalue'];
        $data['taxable_property_value']=$request->taxbale_property_value;
        $data['property_tax_payable_2024']=$request->property_tax_payable_2024;
        $data['discounted_rate_payable']=$request->discounted_rate_payable;
        $data['council_adjustments']=$request->council_adjustments;
        $assessment->fill($data);
        $assessment->swimming()->associate($request->input('swimming_pool'));
        $assessment->save();

        $categories = getSyncArray($request->input('property_categories'), ['property_id' => $property->id]);

        $assessment->categories()->sync($categories);

        /* Property type (Habitat) multiple value */
        $types = getSyncArray($request->input('property_types'), ['property_id' => $property->id]);
        $assessment->types()->sync($types);

        /* Property type (typesTotal) multiple value */
        $typesTotal = getSyncArray($request->input('property_types_total'), ['property_id' => $property->id]);
        $assessment->typesTotal()->sync($typesTotal);

        /* Property value added multiple value */
        $valuesAdded = getSyncArray($request->input('property_value_added'), ['property_id' => $property->id]);
        $assessment->valuesAdded()->sync($valuesAdded);

        return redirect()->back()->with('success', 'Assessment details Updated Successfully!');
    }
    public function updatePropertyAssessmentDetail(Request $request)
    {
        $detail = PropertyAssessmentDetail::with('property','types','typesTotal','valuesAdded','categories')->where('id', '=', $request->assessment_id)->firstOrFail();
       $district_name = $detail->property->district;
        $district_value = District::where('name',$district_name)->first();
         $onetownlotincat = $district_value->sq_meter_value;
        // $onetownlotincat = 250000;
        $onetownlot = 3750;
        $persquare = $onetownlotincat / $onetownlot;
        // return $request;
        $floorareavalue = $request->area * $persquare;
        $wall_value = $detail->wall_material_percentage;
        $roof_value = $detail->roof_material_percentage;
        $window_type_value = $detail->window_type_percentage;
         $sanitation = PropertySanitationType::find($detail->sanitation);
        if ($sanitation) {
          $san_value = $sanitation->value;
        }
        $zone = 1;
        $property_use = PropertyUse::find($detail->property_use);
        if ($property_use) {
          $prop_use_value = $property_use->value;
        }
        $property_type = $detail['types'][0]->value;
        $va = 0; 
        foreach ($detail['valuesAdded'] as $key => $value) {
            $va += $value->value;
         }
          $va;
          $property_cat = $detail['categories'][0]->value;
          $additions = $wall_value + $roof_value + $window_type_value +  $va ;
          $multipliers = $prop_use_value * $san_value * $property_cat * $zone * $property_type; 
      
           $calculatedassessedvalue = ($floorareavalue + $additions) * $multipliers ;
          $actualadjustment = $detail->council_adjustments / 100 ;

            $calculatedNetAssessedvalue = $calculatedassessedvalue - ($actualadjustment * $calculatedassessedvalue);
        $property_id = $request->property_id;
        $length = $request->length;
        $breadth = $request->breadth;
        $area = $request->area;
        $is_map_set = $request->is_map_set;

        $detail->square_meter = round($area,2);
        $detail->length = round($length,2);
        $detail->breadth = round($breadth,2);
        $detail->is_map_set = $is_map_set;
        $detail->property_assessed_value= $calculatedassessedvalue;
        $detail->property_rate_without_gst= $calculatedassessedvalue;
        $detail->net_property_assessed_value=$calculatedNetAssessedvalue;
        $detail->save();
         return response()->json("Assessment Updated successfully");
    }

    public function saveGeoRegistry(Request $request)
    {
        /* @var $geoRegistry PropertyGeoRegistry */
        $geoRegistry = PropertyGeoRegistry::findOrFail($request->input('property_geo_registry_id'));

       $validator = Validator::make($request->all(), [
            'digital_address' => 'required|unique:property_geo_registry,digital_address,' . $geoRegistry->id,
            'dor_lat_long' => 'required'
        ]);


       $validator =  $validator->after(function ($validator) use ($request,$geoRegistry) {

            if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
                list($lat, $lng) = explode(',', $request->dor_lat_long);
                $openLocationCode = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
            }

            $geoExist = PropertyGeoRegistry::where('id', '<>', $geoRegistry->id)
            ->where('open_location_code', $openLocationCode)->first();

            if ($geoExist) {
                $validator->errors()->add('dor_lat_long', 'This dor lat lng is already exist');
            }

        });


        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
            // return $this->error(ApiStatusCode::VALIDATION_ERROR, [
            //     'errors' => $validator->errors()
            // ]);
        }

        $geoRegistry->fill($request->all());

        // if($request->digital_address!=''){
        //     $pos = strpos($request->digital_address, ' ');
        //     $locationCode = substr($request->digital_address, $pos+1);
        //     $latlngArr =  explode(' ', $locationCode);
        //     // echo $locationCode;
        //     // print_r( $latlngArr);
        //     // echo \OpenLocationCode\OpenLocationCode::encode($latlngArr[0], $latlngArr[1]);
        //     // exit;
        //     $geoRegistry->open_location_code = \OpenLocationCode\OpenLocationCode::encode($latlngArr[0], $latlngArr[1]);
        // }

        // Edited by KB 23-04-2019
        if ($request->dor_lat_long && count(explode(',', $request->dor_lat_long)) === 2) {
            list($lat, $lng) = explode(',', $request->dor_lat_long);
            $geoRegistry->open_location_code = \OpenLocationCode\OpenLocationCode::encode($lat, $lng);
        }


        $geoRegistry->save();

        /*$v = Validator::make($request->all(), [
            "georegistry_id" => "required|integer",
            "property_id" => "required|integer",
            'registry' => 'required|array',
            'registry.*.meter_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png','max:5120'] ,
            'registry.*.meter_number' => 'nullable|string|max:255',
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors())->with('id','geo-registry');
        }*/

        /* @var $property Property */
        $property = Property::findOrFail($request->property_id);

        if (count($request->registry) and is_array($request->registry)) {

            foreach (array_filter($request->registry) as $key => $registry) {

                if (isset($registry['id']) && $registry['id'] != null) {
                    $registryImageId[] = $registry['id'];
                    $regdata['number'] = $registry['meter_number'];

                    $registryMeters = $property->registryMeters()->where('id', $registry['id'])->first();
                    $regdata['image'] = $registryMeters->image;
                    if ($request->hasFile('registry.' . $key . '.meter_image')) {
                        if ($registryMeters->hasImage())
                            unlink($registryMeters->getImage());
                        $regdata['image'] = $registry['meter_image']->store(Property::METER_IMAGE);
                    }

                    $property->registryMeters()->where('id', $registry['id'])->update($regdata);
                } else {
                    if ($registry['meter_number'] != null) {

                        $Cregdata['number'] = $registry['meter_number'];
                        if ($request->hasFile('registry.' . $key . '.meter_image')) {

                            $Cregdata['image'] = $registry['meter_image']->store(Property::METER_IMAGE);
                        }
                        $property->registryMeters()->create($Cregdata);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Assessment details Updated Successfully!');
    }

    public function sendPaymentRequestSMS($property_ids){
        $properties = Property::whereIn('id', $property_ids)->get();

        foreach ($properties as $property) {
            $year = \Carbon\Carbon::parse($property->assessment->created_at)->format('Y');
            $dueamount = number_format($property->assessment->getCurrentYearTotalDue());
            $council_short_name = ($property->districts)? $property->districts->council_short_name: '';
            // $mobileNo = $property->landlord->mobile_1;

            if ($mobile_number = $property->landlord->mobile_1) {
                if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                    $property->landlord->notify(new PaymentRequestSMS($dueamount, $year, $council_short_name));
                }
            }


            // Twilio::message(
            //     $mobileNo,
            //     [
            //         "body" => "Dear Property Owner, you have arrears of Le {$dueamount} for your {$year} {$council_short_name} PropertyRate. Kindly make payments soon. Ignore if already paid or 76864861 for query.",
            //         "from" => config('services.twilio.alphanumeric_sender')
            //     ]
            // );
        }


    }


    public function updatePropertyAssessmentPensionDiscount($id,Request $request)
    {

        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->pensioner_discount = 1;
        $detail->save();
        return redirect()->back();

    }

    public function rejectPropertyAssessmentPensionDiscount($id,Request $request)
    {

        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->pensioner_discount = 0;
        $detail->is_rejected_pensioner = 1;
        $detail->save();
        return redirect()->back();

    }

    public function updatePropertyAssessmentDisabilityDiscount($id,Request $request)
    {

        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->disability_discount = 1;
        $detail->save();
        return redirect()->back();

    }

    public function rejectPropertyAssessmentDisabilityDiscount($id,Request $request)
    {

        $detail = PropertyAssessmentDetail::where('property_id', '=', $id)->firstOrFail();
        $detail->disability_discount = 0;
        $detail->is_rejected_disability = 1;
        $detail->save();
        return redirect()->back();

    }


    public function loadGMap()
    {
        return view('admin.properties.loadmap');
    }

    public function update_entries(){
        // return "called";
       return $properties = Property::whereHas('assessments', function ($query) {
            $query->whereYear('created_at' , '>=' , '2023')->groupBy('property_id')

                  ->havingRaw('COUNT(DISTINCT property_rate_without_gst) > 1');
        })
        ->with(['assessments' => function ($query) {
           $query->whereYear('created_at', '>=', '2023')->orderBy('created_at', 'asc');
       }])
        ->get();
    }

    public function delete_selected_prop()
    {

        // $propertyIds = [
        //     154, 527, 940, 1109, 1110, 2900, 3050, 3876, 4507, 7903, 7922, 9286, 
        //     9387, 10774, 11461, 11600, 15524, 41839, 41840, 41844
        // ];
    
        // if (empty($propertyIds)) {
        //     return ['error' => 'No property IDs provided'];
        // }
    
        $propertiesToDelete = Property::get();
        // $propertiesToDelete = Property::whereNotIn('id', $propertyIds)->get();
    
        DB::beginTransaction();
    
        try {
            foreach ($propertiesToDelete as $property) {
                // Delete associated assessment data
                $property->assessment()->forceDelete();
    
                // Delete associated payment data
                $property->payments()->forceDelete();
    
                // forceDelete related landlords, occupancies, and registry
                $property->landlord()->forceDelete();
                $property->occupancies()->forceDelete();
                $property->georegistry()->forceDelete();
    
                // forceDelete property itself
                $property->forceDelete();
            }
    
            DB::commit();
            return ['success' => 'Properties and associated data successfully deleted'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['error' => 'Failed to delete properties and associated data', 'message' => $e->getMessage()];
        }
    }
    
    public function get_material(Request $request){
        // return $request;
        $wall_material = PropertyWallMaterials::where('id',$request->value)->first();

       return view('admin.properties.wall_material_dropdown',compact('wall_material'));
    }
    public function get_material_roof(Request $request){
        // return $request;
        $roof_material = PropertyRoofsMaterials::where('id',$request->value)->first();

       return view('admin.properties.roof_material_dropdown',compact('roof_material'));
    }
    public function get_material_window(Request $request){
        // return $request;
        $window_material = PropertyWindowType::where('id',$request->value)->first();

       return view('admin.properties.window_material_dropdown',compact('window_material'));
    }
    public function get_value_added(Request $request){
        // return $request;
        $value_added = PropertyValueAdded::where('id',$request->value)->first();
        
        return view('admin.properties.value_added_dropdown',compact('value_added'));
    }
    public function read_excel(){
        return "sdaf";
        $streetNames = [
            "6 Road Drive",
            "A Line Drive",
            "A.B.J. Drive",
            "Aayan Drive",
            "Abayome Drive",
            "Abayomi Cole Avenue",
            "Abayomi Cole Drive",
            "Abb Lane",
            "Abbie Drive",
            "Abdul Drive",
            "Abdul Kanu Drive",
            "Abdul Sandy Street",
            "Abdulai Lane",
            "Abdulai Street",
            "Abdulai Street ",
            "Abduli Drive",
            "Aberdeen Ferry Road",
            "Aberdeen Road",
            "Aberdeen Turn Table",
            "Abidjan Street",
            "Abie Drive",
            "Abies Drive",
            "Abijan Street",
            "Abioseh Lane",
            "Abioseh Lane ",
            "Abioseh Lane Kamara Street",
            "Abioseh Street",
            "Aboa Close .",
            "Aboko Drive",
            "Aboko-Cole Avenue",
            "Aboko-Cole Drive",
            "Abraham Drive",
            "Abu & Issa Kamara Drive",
            "Abu Bakar Drive",
            "Abu Bakarr Street",
            "Abu Bhonapha Drive ",
            "Abu Drive ",
            "Abu Fullah Drive",
            "Abu Kamara Drive",
            "Abu Lane",
            "Abu Lane ",
            "Access Road",
            "Achmus Drive",
            "Action Aid Drive",
            "Action Aid Road",
            "Action Street",
            "Adajiks Drive",
            "Adakalie Kombor Street",
            "Adams Drive",
            "Adam's Drive",
            "Adams Street",
            "Adejobi Drive",
            "Adelaide Street",
            "Adesanyah Street",
            "Adidas Drive",
            "Adikali Street",
            "Adikalie Kombo Street",
            "Adikalie Street",
            "Adimagboleh Drive",
            "Adioka Road",
            "Adionkia road",
            "Admirality Road",
            "Adolphus Street",
            "Adonkia road",
            "Adrian Drive",
            "Adu Lane",
            "Advic Drive",
            "Aecon Drive",
            "Agbata Lane",
            "Agness Street",
            "Agriculture Road",
            "Ahamadiyya Drive",
            "Ahmadiyya Junction",
            "Ahmed Drive",
            "Ahmed Drive ",
            "Air Field Road",
            "Air Port Ferry Road",
            "Airport Junction",
            "Airport Road ",
            "Aitkin Street",
            "Ajami Drive",
            "Akamabah Road",
            "Akram Drive",
            "Aku Town",
            "Alaffia Drive",
            "Alafia Street",
            "Alana Drive",
            "Albangs Drive ",
            "Albert Drive ",
            "Albert Road",
            "Albert Street",
            "Albib Drive",
            "Alcock Street",
            "Alex Allen Drive ",
            "Alfana Conteh Street",
            "Alfred Lane",
            "Algha Wurie",
            "Algha Wurie Drive",
            "Alhaji Dauda Dumbuya Street",
            "Alhaji Lane",
            "Alhaji Shek Koroma Street",
            "Alhassan Koroma Drive",
            "Ali Musa Drive",
            "Aliamie Drive ",
            "Alice Lane ",
            "Alice Lim Drive  ",
            "Alicianat Drive",
            "Alie Amie Drive ",
            "Alie Drive",
            "Alie Sesay Drive",
            "Aliforay Street",
            "Alim Drive",
            "Alimamy Amara Road",
            "Alimamy Conteh Road",
            "Alimamy Conteh Street",
            "Alimamy Foday Road",
            "Alimamy Kalaymodu Road ",
            "Alimamy Kamara Drive",
            "Alimamy Seramadu Road",
            "Aljayzz Drive",
            "Allen Drive",
            "Allen Street",
            "Allen Town Road",
            "Allen Town Street",
            "Allies Street",
            "Alpha Daballa  Road",
            "Alpha Drive",
            "Alpha Drive ",
            "Alpha Street",
            "Alpha Wurie Drive",
            "Alphajor Drive",
            "Alshak Drive",
            "Al-Sheik Crescent",
            "Al-Sheik Drive",
            "Alshek Drive",
            "Alu Drive",
            "Alusine Drive",
            "Alusine Road ",
            "Alusine Street",
            "Alys Lane",
            "Amara Conteh Street  ",
            "Amara Lane",
            "Amara Street",
            "Amara Street ",
            "Ambrose Street",
            "American Embassy Road",
            "Amie Silver Street ",
            "Amie Sylvia Street",
            "Amina Drive",
            "Amina Road ",
            "Amina Street",
            "Amina Tulun Road",
            "Aminata Lane",
            "Aminata Street",
            "Amos Drive",
            "Amsays Compound Drive",
            "Anante Drive",
            "Andrew Kanu Close",
            "Andrew Street",
            "Andrews Street",
            "Andrian Close",
            "Andy Kargbo Drive",
            "Andy's Drive",
            "Anita Drive",
            "Annie Walsh Street",
            "Ansu Lane ",
            "Ansu Street",
            "Ansumana Street",
            "Anthony Williams Drive",
            "Antony Drive",
            "Any's Drive",
            "Argyle Street",
            "Arian Drive",
            "Arlene's Gardens",
            "Arthur Drive ",
            "Aruba Drive",
            "Aruna Drive",
            "Aruna Street",
            "Ascawah Drive",
            "Ascension Town Road",
            "Asgil Drive",
            "Asgill Drive",
            "Ash Wood Drive",
            "Ashmata Drive",
            "Ashmatta Drive",
            "Asibe Drive ",
            "Asigill Drive",
            "Asive Lane",
            "Askawa Drive",
            "Asrill Drive",
            "Atkin Street",
            "Atlantic Street",
            "Avery Street ",
            "Azark Renner Street",
            "Azick Drive",
            "B. B. Kamara Street ",
            "B. Jet Drive",
            "B.K.K. Drive",
            "B.K.K. Drive.",
            "B.Z. Lane",
            "Babadorie Road",
            "Babarah Lane",
            "Babarah Street",
            "Baboon College Drive",
            "Babymus Drive",
            "Back Off Baptist Church",
            "Back Street",
            "Back Street  ",
            "Baech Road",
            "Bagan Street",
            "Bah Drive",
            "Bahome Street",
            "Bai Bureh Hospital Road",
            "Bai Burreh Road",
            "Bai Shake Drive",
            "Bai Sheka Drive",
            "Baila Leigh Drive",
            "Bailey Street",
            "Baimaya Compound",
            "Baimbu Drive",
            "Baio Lane  ",
            "Bakarr Drive",
            "Bakissa Street",
            "Balay Drive ",
            "Balcony Close ",
            "Balcony Lane",
            "Balcony Road ",
            "Ball Street",
            "Ballay Lane",
            "Bamawo Area",
            "Bambalia Drive",
            "Bambaya Lane",
            "Bambeh Road",
            "Banana Water Drive",
            "Banga Farm ",
            "Bango farm",
            "Bangs Drive",
            "Bangura Avenue",
            "Bangura Drive",
            "Bangura Drive ",
            "Bangura Lane",
            "Bangura Square",
            "Bangura Street",
            "Banjui Street",
            "Bankole Drive",
            "Banya Drive ",
            "Banya Road",
            "Baoma Road",
            "Baoma Village.",
            "Baome Street",
            "Barbaradorie Drive",
            "Barham Road",
            "Barhome Street",
            "Barhomie Street",
            "Barlatt Drive",
            "Barlatt Street",
            "Barlay Drive",
            "Barracks Road",
            "Barrie Alafia Drive",
            "Barrie Close ",
            "Barrie Drive",
            "Barrie Lane",
            "Barrie Street",
            "Barrier Lane",
            "Barry Drive",
            "Barwoh Street",
            "Baryoh Drive",
            "Baryoh Street",
            "Bashram St",
            "Bass Street (Main)",
            "Bass Street 1",
            "Bass Street 2",
            "Bassie  Drive",
            "Batema Drive ",
            "Bath Street",
            "Bathurst Junction  Drive",
            "Bathurst Road",
            "Bathurst Street",
            "Batkanu Drive",
            "Battery Road",
            "Battery Street",
            "Baw Baw Village",
            "Bawome Street",
            "Bayan Drive",
            "Bayoh Drive",
            "Bayor Street",
            "Beach Road",
            "Beccle Lane",
            "Beccles Street",
            "Beckely Street",
            "Beckley Drive",
            "Beckley Lane",
            "Beckley Lane 1",
            "Beckly Street",
            "Belewa Drive",
            "Belia Road",
            "Bell Drive Sussex",
            "Ben Drive",
            "Ben Lane",
            "Bendu Kamara Street",
            "Bendus Drive",
            "Benga Street",
            "Bengal Street",
            "Benghazi Road",
            "Benjamin Drive",
            "Benjamin Drive ",
            "Benjamin Lane",
            "Benka Lane",
            "Benshina Close",
            "Bent Street",
            "Benton Williams Street",
            "Beoku Lane",
            "Bernice Street",
            "Bernuear Street",
            "Berry Street",
            "Berwick Street",
            "Bestman Drive",
            "Bet Farm Road",
            "Beteh Street",
            "Betham Lane",
            "Bethel Drive",
            "Bethel Street",
            "Bettanis Road",
            "Beyah Street",
            "Biango Street",
            "Big House",
            "Big Kumba Lane",
            "Bill Lane",
            "Billo Lane",
            "Bindy Street",
            "Bintu Turay Drive",
            "Bintumani Drive",
            "Bio Street",
            "Bishop Drive",
            "Bismacjohnson Lane",
            "Bismark Johnson St",
            "Biya Street",
            "Biyah Lane ",
            "B-Jet Drive",
            "Black Field Street",
            "Black Hall Road",
            "Black Johnson Beach",
            "Black Tank",
            "Blaud Ford Drive .",
            "Blessing Drive",
            "Bob Conteh Street .",
            "Bob J Drive",
            "Bob Koker Drive",
            "Bobson Sesay Drive",
            "Bockariegbay Street",
            "Bolling Street",
            "Bolo Junction",
            "Bomba Road",
            "Bombay Lane",
            "Bombay Terrace",
            "Bombeh Road ",
            "Bombeh Swamp",
            "Bomposseh Street",
            "Bondeba",
            "Bondo Water",
            "Bonga Farm",
            "Bonga Tong",
            "Bonkay Lane",
            "Borbin Avenue",
            "Borderline",
            "Borgorny Street",
            "Borteh Drive.",
            "Bottom  Mango",
            "Boundary One",
            "Boyle Lane",
            "Boyle Street",
            "Boys Society",
            "Braima Drive ",
            "Brandon Drive",
            "Brazil Street",
            "Brian Lane",
            "Bright Lane",
            "Bright Street",
            "Brima Drive",
            "Brima Jalloh Drive",
            "Brima Kargbo Street",
            "Brima Kondeh Street",
            "Brima Lane",
            "Brinsley Johnson Drive",
            "British Drive",
            "Bronce Lane",
            "Brown Lane",
            "Brush Street",
            "Bryne Lane",
            "Bubu Water",
            "Bumbuna Road 1",
            "Bumposseh Stree",
            "Bundu Cham Street",
            "Bundu Drive",
            "Bundu Street",
            "Bunting Willams Drive",
            "Bunting Willams Street",
            "Buntycranie Street",
            "Burah Lane",
            "Bureh Drive",
            "Bureh Lane",
            "Bus Station Road",
            "Bush Street",
            "Bush Water",
            "Busura Lane",
            "Byne Lane",
            "Byrne Lane",
            "C.R.S. Drive",
            "Calamari Drive",
            "Calcuta Street",
            "Calcutta Street",
            "Calmont Road",
            "Cambel Drive",
            "Cambell Street",
            "Camillo Crescent",
            "Campbel Drive",
            "Campbell Lane",
            "Campbell Street",
            "Candy Lane",
            "Cane stick Junction",
            "Canneshire Close",
            "Cannie Close",
            "Cannieshire Close",
            "Cannishire Drive",
            "Cannister Drive",
            "Cannon Street",
            "Cape Palmas Street",
            "Cape Road",
            "Cape Sierra Drive",
            "Capelight Drive",
            "Cardew Street",
            "Carew Drive",
            "Caritas Road",
            "Carlcutta Street",
            "Carlos Drive ",
            "Carlton Carew Road",
            "Carol Drive",
            "Carr Street",
            "Carrol Drive ",
            "Carrol Lane",
            "Carrow Street ",
            "Cassava Farm",
            "Casslie Street ",
            "Caulker Drive",
            "Cemetery Road",
            "Chambers Drive",
            "Chapel Street",
            "Charles Avenue",
            "Charles Street",
            "Charles Street ",
            "Charles Williams Drive",
            "Charley Avenue",
            "Charlie Avenue",
            "Charlie's Avenue",
            "Charlotte Drive",
            "Charlotte Street",
            "Charlton Carew Road",
            "Charple Street",
            "Cheril Drive",
            "Cherry Coco Drive ",
            "Chico Drive",
            "Chief Kondeh Drive",
            "Chief Massaquoi Street",
            "Chinese Compound",
            "Chinese Compound Road",
            "Chinese Drive",
            "Chris Charlie Drive",
            "Christiana Lane",
            "Christiana Terrace",
            "Christo Dumbuya Drive",
            "Church Of Christ Lane",
            "Church Road",
            "Church Street",
            "Circular Road",
            "Cisco Road",
            "City Road",
            "Clarence Street",
            "Clark Road",
            "Clark Street",
            "Clarke Street",
            "Clarkson Drive",
            "Class One Road  ",
            "Claud Lane",
            "Claude Lane",
            "Clearance Street",
            "Cline Street",
            "Coachie Drive",
            "Coachies Drive",
            "Cocker Drive",
            "Cockerill North",
            "Cockle Bay Drive ",
            "Cockle Bay Road",
            "Cogno Town Road",
            "Coker Drive",
            "Cole Drive",
            "Cole Drive  ",
            "Cole Lane",
            "Cole Street",
            "Coleridge-Taylor  Drive",
            "College Road",
            "Collegiate Road",
            "Colleh Lane",
            "Collier Drive",
            "Collier Street",
            "Collingwoode Drive",
            "Colonel Street",
            "Columbia Davies Drive",
            "Combema Road",
            "Combema Street",
            "Combination Drive",
            "Combon Drive",
            "Combum Drive",
            "Company  Road",
            "Compound Street",
            "Conakry Dee Main Road",
            "Conakry Dee Road",
            "Concern Drive",
            "Congo Bridge Lane",
            "Congo Cross",
            "Congo Town Road",
            "Congo Town Slip Road",
            "Consider Lane",
            "Conteh Drive",
            "Conteh Lane",
            "Conteh Street",
            "Convenient Drive",
            "Cookson Drive",
            "Coomber Lane",
            "Cooper Lane",
            "Corbala Raod ",
            "Corner Stone Miniry Drive",
            "Cotton Club Road",
            "Cotton Tree Drive",
            "Council Drive",
            "Council Lane",
            "Council Road",
            "Courtrite",
            "Cox-George Road",
            "Crawder Lane",
            "Crescent Drive",
            "Crew Street",
            "Cromwell Street",
            "Crooked Lane",
            "Crooks Street",
            "Cross Road",
            "Cross Road ",
            "Crowther Drive ",
            "Crystal Clear Drivee",
            "Crystal Palace Drive",
            "Cumboh Avenue ",
            "Cumboh Drive",
            "Cyril Jengo Drive",
            "Cyril Jengo Street",
            "Daballa Street",
            "Daboh Drive ",
            "Dabor Drive",
            "Dabor Lane",
            "Daddely Street",
            "Dadley Street.",
            "Dadlry Street",
            "Daisy Street",
            "Dakama Lane",
            "Dakiyai Street",
            "Dalal street",
            "Daley Street",
            "Dalmadu Street",
            "Damaya Drive",
            "Damba Road",
            "Dambie Drive ",
            "Damkama Street",
            "Dan And Marie Lane",
            "Dan And Marie Lane ",
            "Dance Street",
            "Dankama Lane",
            "Dankama Road",
            "Dankama Street ",
            "Darboh Drive",
            "Darmy Street",
            "Dauda Sandy Street",
            "Dauzy Drive ",
            "Davcilia Lane ",
            "David Drive",
            "David Kargbo Street ",
            "Davida Drive",
            "Davies Drive",
            "Davies St",
            "Davies Street",
            "Davies Williams Drive",
            "Davis Drive",
            "Davowa Close",
            "Debbie Macaulay Street",
            "Decca Drive",
            "Decca Lane",
            "Decker Drive",
            "Decker Drive ",
            "Decker Lane",
            "Deen Drive",
            "Deen Drive ",
            "Deen Rogers Drive",
            "Deen Sankoh Drive",
            "Deen Sesay Drive",
            "Deens Avenue",
            "Deep Cassava Farm",
            "Deka Drive",
            "Delken Drive",
            "Delmodu Street",
            "Delon Street",
            "Deluxe Crescent Drive",
            "Dema Road",
            "Demby Street",
            "Demi Turner Drive",
            "Denking Close",
            "Dennis Williams Drive",
            "Derick Drive",
            "Derick's Drive",
            "Desmond Lane ",
            "Destiny Drive",
            "Destiny Junction",
            "Devere Street",
            "Devil Hole",
            "Dfid Drive",
            "Diame Drive",
            "Diamei Drive",
            "Diamei Road",
            "Dick Street",
            "Dillet Drive",
            "Dillet Street",
            "Dillet Street ",
            "Diogbuma Road",
            "Dirty Road",
            "Dixon Street",
            "Dizen Town",
            "Dizzen Drive",
            "D-Line Road",
            "Doherty Street",
            "Donald Smith Drive",
            "Dora Way",
            "Dorie Town",
            "Dougan Street",
            "Douglas Drive",
            "Dove Drive",
            "Dr.  Samba Drive",
            "Dr. Demby Street",
            "Dr. Jah Drive ",
            "Dr. Jah Street",
            "Dr. Kamara Drive",
            "Dr. Lamin Drive",
            "Dr. Samba Drive ",
            "Drake Drive ",
            "Drive 2 ",
            "Duke Street",
            "Dultas Drive ",
            "Dumbuya Drive ",
            "Dumbuya Lane",
            "Dumbuya Road ",
            "Dumbuya Street ",
            "Dundas Street",
            "Dunkley Street",
            "Durasal Drive ",
            "During Drive ",
            "During Street",
            "Dylan Street ",
            "Eacon Drive",
            "Eacon Road",
            "Eaks Street",
            "Earl Street",
            "Easmon Drive",
            "Easmon Ngakui Drive ",
            "East Box Street",
            "East Box Street ",
            "East Brook Street",
            "Easton Street",
            "Easy Corner ",
            "Ecowas Street",
            "Eddie Macaulay Street",
            "Eddle Street",
            "Edmas Drive",
            "Edmond Lane",
            "Edmond Street",
            "Edna Drive",
            "Ednat Drive",
            "Edu Street",
            "Edward J. Bannister Drive",
            "Edward Lane",
            "Edward Street",
            "Edwin Drive",
            "Edwin Street",
            "Edwina Drive",
            "Eku Lane",
            "Elaine Drive",
            "Elba Street",
            "Eleady Street",
            "Elim",
            "Elizabeth  Street",
            "Elizabeth Drive",
            "Elizabeth Lane",
            "Elizabeth Steet",
            "Elizabeth Street",
            "Elk Street",
            "Ellaline Drive",
            "Elliah Lane Drive",
            "Ellie Drive",
            "Ellie Street",
            "Elliott Drive ",
            "Elliott Street",
            "Emerson Bockarie Drive",
            "Emile Wilson Steeet",
            "Emile Wilson Street",
            "Emily Lane",
            "Emmanuel Kamara Avenue ",
            "Emmanuel Kamara Drive",
            "Emmanuel Turay Lane",
            "Emond Boie Drive",
            "Emos Drive",
            "Ensa Street",
            "Eoncon Drive",
            "Ernest Cole Drive",
            "Essequee Drive",
            "Essex  Street",
            "Eva's Street",
            "Evert Street ",
            "Exodus Lane ",
            "F.C. Drive",
            "Fabai Street",
            "Fadia Drive",
            "Fadia Lengor Drive",
            "Fadika Drive",
            "Fadika Drive ",
            "Fadika Lane",
            "Fadugu ",
            "Fadugu Drive",
            "Faith Lane ",
            "Fakai Drive",
            "Fakai Road",
            "Falla Lane ",
            "Falla Street ",
            "Falls Lane",
            "Falls Lane ",
            "Falls Street ",
            "Falmouth Street",
            "False Cape Garden",
            "Family Drive ",
            "Fanny Bakei Lane ",
            "Fanta Street",
            "Farm Drive",
            "Farm Lane",
            "Farrah Lane",
            "Fasei Street",
            "Fasuluku Drive",
            "Fatima Drive",
            "Fatimeh Street",
            "Fatmata Conteh Drive",
            "Fatmata Street",
            "Fatorma Drive ",
            "Fatu Elizabeth Drive ",
            "Faulkner Street",
            "Favour Drive",
            "Felix Street",
            "Fellie Street",
            "Femi Turner Drive",
            "Femi Turner Road",
            "Fergusson Lane",
            "Fergusson Street",
            "Festus Drive",
            "Fetter Lane",
            "Field  Road ",
            "Field Street",
            "Fifth Street",
            "Fillie Drive ",
            "Finakra1 Street",
            "Finda Lane",
            "Findley Lane",
            "Findley Street",
            "First Drive",
            "First Lane",
            "First Pump Drive",
            "First Street",
            "Fisher Drive",
            "Fisher Street",
            "Fitz James Street",
            "Fitz John Street",
            "Fitz-James Street",
            "Fitzjohn Street",
            "Fleck Street",
            "Florester Drive",
            "Floresther Terrace",
            "Floresther Terrace Drive",
            "Foday Alimamy  Conteh Road",
            "Foday Alimamy Road",
            "Foday Drive",
            "Foday Kanu Drive",
            "Foday Lane",
            "Foday Musa Drive",
            "Foday Sankoh Drive ",
            "Foday Street",
            "Fofana Drive",
            "Fofana Drive ",
            "Fofana Street",
            "Fofanah Drive",
            "Fofonah Street",
            "Fomba Drive",
            "Fomba Street",
            "Fon Drive",
            "Fonima Off Lion Street",
            "Fonima Road",
            "Fonima Road ",
            "Forest  Lane  ",
            "Forest Hill",
            "Forgiveness Drive",
            "Fornah-Sesay Drive",
            "Fornima Road ",
            "Fort Street",
            "Forty Lane",
            "Fothaneh  Road  ",
            "Fouad Lane",
            "Foulah Street",
            "Fourah Bay Road",
            "Fourth Street",
            "Foyah Street",
            "Fraizer Cole Drive",
            "Frake Drive",
            "Francis Brive",
            "Francis Drive",
            "Francis Koroma Street",
            "Francis Street ",
            "Frank Drive",
            "Franklin Drive",
            "Fraser Drive",
            "Fraser Street",
            "Frazer Davies Drive",
            "Frazer Drive",
            "Frazer Street",
            "Frazier Drive",
            "Freak Drive",
            "Frederick Street",
            "Free Gospel Lane",
            "Free Gospel Street",
            "Free Street",
            "Freedom West Drive",
            "Freeman Drive",
            "Freeman Street",
            "Freetown Road",
            "Frenacss Street ",
            "Frey Street",
            "Friday Drive",
            "Friendship Drive",
            "Frontair Road",
            "Fuad Kanu Drive",
            "Fuad Lane",
            "Fudia Drive",
            "Fudia Terrace",
            "Fudia Torrece",
            "Fula Street",
            "Fulla Drive",
            "Fullah Lane",
            "Fullah Street",
            "Fullah Town",
            "Funna Drive",
            "Funna Drive ",
            "Gabanah Road",
            "Gabriel Street",
            "Gadrage Road Kingtom",
            "Gamanga Drive",
            "Garage Road",
            "Garber Lane",
            "Garrison Street",
            "Gassama  Street ",
            "Gassama Drive",
            "Gassimu Drive",
            "Gaurd Room Drive",
            "Gbagbayila ",
            "Gbainty Road",
            "Gbamanja Drive ",
            "Gbaneh Road ",
            "Gbaneh-lol Road",
            "Gbanie Drive",
            "Gbawuru Mansary Drive",
            "Gbendembu Road",
            "Gbintiy Road",
            "Gbla Drive",
            "Gbonie Street",
            "Gbonkoh Road",
            "Gbontho Lane",
            "Genet Lane",
            "Gentle View",
            "George Brook Road",
            "George Drive",
            "George Street",
            "George-Broooke Street",
            "Ger Drive",
            "Gerber Lane",
            "Gibbon Lane",
            "Gilbert Street",
            "Gileskil Drive",
            "Gilpin Street",
            "Glasgow Street",
            "Glendon Drive ",
            "Glory Drive",
            "Gloucester Drive",
            "Gloucester Road",
            "Gloucester Street",
            "Gluester Road",
            "Goderich Road",
            "Goderich Street",
            "Golden Drive",
            "Golden Valley Drive",
            "Golfer Street",
            "Gooding Drive",
            "Gooding Street",
            "Gordon Close",
            "Gordon Drive",
            "Gordon Street",
            "Goree Street",
            "Goshen Drive",
            "Graceland",
            "Graffin Lane",
            "Grafton Road",
            "Grafton-Regent Road",
            "Grand Cess Street",
            "Grankoko Drive",
            "Grant Street",
            "Grass Field Road",
            "Grassfarm",
            "Grassfield",
            "Grassield ",
            "Gray Avanu ",
            "Green Avenue",
            "Green Drive",
            "Green Lane",
            "Green Street",
            "Greenville Lane",
            "Griffin Lane",
            "Grosven Drive",
            "Guard Room Road",
            "Guard Street",
            "Guilford Street",
            "Gulford Street",
            "Gullah Street",
            "Gulliver Street",
            "Guma Drive",
            "Guy Street",
            "Gwinner Avenue",
            "Gwinner Avenue ",
            "H. M. Bangura Avenue",
            "Haddington Street",
            "Hadirudeen Street",
            "Haed Street",
            "Haedumu Street",
            "Hagan Street",
            "Haja Emma Drive",
            "Haja Emma Lane",
            "Haja Fatmata Drive",
            "Haja Isata Nabi Drive",
            "Haja Isatu Drive",
            "Haja Kadie Drive",
            "Haja Memunatu Drive",
            "Haja Seray Drive",
            "Haja Sonny Drive",
            "Haja Yallie Drive",
            "Haja-Soni Drive",
            "Hall Lane",
            "Hall Street",
            "Hamburg ",
            "Hamburg Street",
            "Hamdoz",
            "Hamilton Junction",
            "Hamilton Lane",
            "Hamilton Street",
            "Hance Lane",
            "Handel Street",
            "Hangar Road",
            "Hannah Benka Coker Street",
            "Hans Kawa Drive",
            "Hapite Road",
            "Harding Drive",
            "Hardwick Street",
            "Hardy Street",
            "Harmony Crescent",
            "Harmony Drive",
            "Harry Sawyer Drive",
            "Hartshorne Street",
            "Harvey Street",
            "Hassan Drive",
            "Hassan Samura Drive",
            "Hassan Street",
            "Hassib Drive",
            "Havelock Street",
            "Hawa Kalokoh Drive",
            "Hawa Shekie Street",
            "HB Minah Cove",
            "Headdle Lane",
            "Headmudu Street",
            "Heddle Street",
            "Hellen Drive",
            "Henneson Street",
            "Hennessy Street",
            "Henry Drive",
            "Henry Street",
            "Herbert Street",
            "Herima Avenue",
            "Hermira Avenue",
            "Hermonn Drive",
            "Hermony West Drive",
            "Hervry Street",
            "Higging Drive",
            "Higgins Drive",
            "High Broad Street",
            "High Hill Drive",
            "High Land Street",
            "High Lane",
            "High Road",
            "High street",
            "Hihg Lane Street",
            "Hill Cot Road",
            "Hill Side Drive",
            "Hill Street",
            "Hill Valley Drive",
            "Hill View Drive",
            "Hillside Bypass Road",
            "Hillside Drive",
            "Hinga Drive",
            "HM Bangura Avenue",
            "Holland Drive ",
            "Holland Street",
            "Hope Stree ",
            "Hope Street",
            "Horton Street",
            "Hospital  Lane  ",
            "Hospital Road",
            "Hotagua Street",
            "Hotel Barmoi Drive",
            "Hotel Road",
            "Hotta's Drive",
            "Howe Street",
            "Huggins Lane",
            "Huggins Street",
            "Hughes Drive ",
            "Hughes Street",
            "Hulket Street",
            "Hulper Elizabeth Lane",
            "Humburg street",
            "Humburg Street ",
            "Humonya Avenue",
            "Humper Drive",
            "Humper Street",
            "Hydro Road",
            "I K Mohamed Street",
            "IB Drive ",
            "Ib Kowo Drive",
            "Ibrahim Drive",
            "Ibrahim Sorie Drive",
            "Icc Drive",
            "Iddris Drive",
            "Idris Drive",
            "Idrissa Street",
            "Imam Rhida Drive",
            "Independence Avenue",
            "Index Drive",
            "Iris Drive ",
            "Isa Drive",
            "Isaac Drive ",
            "Isaac Renner Street",
            "Isatu Drive",
            "Isatu Street",
            "Ishbock  Street",
            "Ishbock Street",
            "Ishmael Drive",
            "Islam Drive",
            "Islamic Lane",
            "Issa Drive",
            "Issac Renner Drive",
            "Issac Renner Street",
            "Iyi Johnson Way",
            "J. Bunting Williams Street",
            "J.J. Drive",
            "Jabba Street",
            "Jabbie Lane",
            "Jabbie Street",
            "Jabbiela Drive",
            "Jabie Lane ",
            "Jackson Drive",
            "Jah Drive",
            "Jah Drive ",
            "Jah Lane",
            "Jakas Rbsmosing St",
            "Jakitay Terrace",
            "Jalloh Drive",
            "Jalloh Jamboria Drive",
            "Jalloh Lane",
            "Jalloh Road",
            "Jalloh Street",
            "Jamaica Lane",
            "Jamaican Street",
            "Jambai Street",
            "Jamboria Street",
            "Jamboria Street ",
            "Jamburia Steet",
            "James Abu Street ",
            "James Alie Drive",
            "James Conteh Street",
            "James Crescent Drive",
            "James Dauda Street",
            "James Drive",
            "James Mccarthy Drive",
            "James Street",
            "Jamiacan Street",
            "Jamican Lane",
            "Jan Lane",
            "Jango Drive",
            "Janie Lane",
            "Jannah Lane",
            "Janneh Lane",
            "Jaret Drive",
            "Jarett Street",
            "Jarfoi Drive",
            "Jarr Street",
            "Jarret Street",
            "Jarrett Street",
            "Jarry Street",
            "Jawoya Drive",
            "Jay Drive",
            "Jejeh Drive",
            "Jen Jen Drive",
            "Jeneba Drive",
            "Jengo Drive",
            "Jenkins Street",
            "Jenna Drive",
            "Jenneh Lane",
            "Jenneh Street",
            "Jennifer Drive",
            "Jeredyne Drive",
            "Jeremahi Street",
            "Jeremiah Drive",
            "Jeremiah Street",
            "Jerusalem Avenue",
            "Jescar Drive",
            "Jesse Drive",
            "Jessica Mac Drive",
            "Jesus Rain Avenue ",
            "Jimissa Drive.",
            "Jimmy B Drive",
            "Jimmy Drive",
            "Jobson Momoh Drive",
            "Joe Freeman Street",
            "John Benjamin Avenue",
            "John Benjamin Drive",
            "John Benjamin Drive ",
            "John Brima  Drive",
            "John Coker Drive ",
            "John Lahai Street",
            "John Lane",
            "John Samuel Doe Avenue",
            "John Street",
            "John Thorpe Road",
            "John Toko Street",
            "Johnny Lane",
            "Johnson Drive",
            "Johnson Drive ",
            "Johnson Lane",
            "Johnson Momoh Drive",
            "Johnson Street",
            "Johnson-Caulker Drive ",
            "Jojo Drive",
            "Jombo Stone",
            "Jomo Kenyatta Road",
            "Jones Avenue",
            "Jones Lane",
            "Jones Steet",
            "Jones Street",
            "Jordan Drive",
            "Josephine Drive",
            "Joshua Isreal Drive",
            "Joshua Lane",
            "Josia Drive",
            "Josiah Drive",
            "Joy Lane",
            "Joyce Drive",
            "Joycy Street",
            "Juba Barracks",
            "Juba Estate Drive",
            "Juba Hill Road",
            "Jubilee St",
            "Jui Road",
            "Jui Station Road",
            "Julakunda Drive",
            "Juldeh Drive",
            "Jumper Drive",
            "Juna Drive",
            "Justice Drive",
            "Juxin Avenue",
            "Juxon Avenue",
            "K Drive",
            "K Road",
            "K Turvy Lain",
            "K.L Kamara Street",
            "K.L. Kamara Street.",
            "Kabba Drive",
            "Kabba Lane",
            "Kabba Street",
            "Kabbia Street",
            "Kabia Drive",
            "Kadie Lane-",
            "Kadie Satu Drive-",
            "Kadie Vandi Lane-",
            "Kagbantama Road-",
            "Kahkah Drive",
            "Kahunla Street",
            "Kaikai Avenue",
            "Kai-Kamara ",
            "Kailie Drive",
            "Kailie Drive.",
            "Kaimachaimde Drive",
            "Kaimachiande Street",
            "Kaisamba Terrace",
            "Kajue Drive",
            "Kakays Drive",
            "Kalaba Road",
            "Kalaba Town",
            "Kalama Road",
            "Kalamari Drive ",
            "Kalayma Road",
            "Kalaymodu Road",
            "Kalefa Drive ",
            "Kalie Drive",
            "Kalil Lane ",
            "Kalima Road",
            "Kalimar Road",
            "Kallay Drive ",
            "Kallay Street",
            "Kallon Drive",
            "Kallos Drive ",
            "Kally Drive",
            "Kalokoh Drive",
            "Kalokon Street",
            "Kalos Drive",
            "Kamala Drive",
            "Kamalo Drive",
            "Kamanda Compound",
            "Kamanda Drive ",
            "Kamanda Lane",
            "Kamanda Lane ",
            "Kamanda Street",
            "Kamanda-Fonah Drive",
            "Kamanda-Fornah Drive",
            "Kamara Bah",
            "Kamara Court ",
            "Kamara Drive",
            "Kamara Lane",
            "Kamara Road",
            "Kamara Street",
            "Kambai Drive",
            "Kambaia Road",
            "Kambalie Street",
            "Kambia Road",
            "Kamblie Street",
            "Kamen Road",
            "Kamllo Drive",
            "Kammal Street",
            "Kamtuck Street",
            "Kamuray Drive",
            "Kanata Street",
            "Kandeh Drive",
            "Kandia Road",
            "Kandil Drive",
            "Kangama Drive",
            "Kangbai Drive",
            "Kaningo Drive",
            "Kanna Drive",
            "Kanneh Drive",
            "Kanneh Street",
            "Kannex Drive",
            "Kanu Drive",
            "Kapu Drive ",
            "Kapuwa Matoe Street",
            "Karaneh Street",
            "Karefe Drive",
            "Kargbo  Street  ",
            "Kargbo Drive",
            "Kargbo Lane",
            "Karim Drive",
            "Karisatu Drive",
            "Karlie Lane ",
            "Karl's Drive",
            "Karrow Street ",
            "Kasongha Road",
            "Kasseh Lane",
            "Kaun Street",
            "Kawan Street",
            "Kayama Street",
            "Kayanda Drive",
            "Kebbi Drive",
            "Kebe Drive",
            "Kebie Drive",
            "Kefala Street ",
            "Kefel Drive",
            "Keily Drive",
            "Keima Juma Street",
            "Kei-Sumah Street",
            "Kelefah Street ",
            "Keleh Compound",
            "Kelfala Street",
            "Kelleh Drive",
            "Kelly Drive",
            "Kelsuma Street",
            "Kendall Street",
            "Kenema Road",
            "Kenema Street",
            "Kennedy Street",
            "Kennex Drive",
            "Kenson Drive",
            "Kent Drive",
            "Kent Street",
            "Kermoneh Road",
            "Kestea Street",
            "Ketura's Close",
            "KevCol Terrance ",
            "Keytel Compound Drive",
            "Khalil Street ",
            "Kibinma Drive",
            "Kilogrie Drive",
            "Kincardin Street",
            "King  Street  Road ",
            "King David Drive",
            "King Dellamadu Road",
            "King Harman Road",
            "King Street",
            "King William Street",
            "Kingdemodu Road",
            "Kingdom Corner Street",
            "Kingdon Hall Drive",
            "Kingorie Drive ",
            "Kings Drive",
            "Kingson Drive",
            "Kingsway Corner",
            "Kingsway Hospital Road.",
            "Kingsway Street",
            "Kinsella Street",
            "Kirkland Street",
            "Kissy Road",
            "Kissy Street",
            "K-Man's Road",
            "Kngbai Drive",
            "Koffi Drive",
            "Koffie Garage Drive",
            "Kofi Drive",
            "Kofie Drive",
            "Kogbo 1",
            "Kogbo 2",
            "Kogbo 3",
            "Kokobeh Drive",
            "Kolia Street",
            "Kolleh Lane",
            "Kolleh Town Main Motorway",
            "Kolo Lane",
            "Koloneh Drive",
            "Komba Drive",
            "Komkanda Drive ",
            "Komkanda Street",
            "Komrabai Lawyer Road",
            "Komrabai Street",
            "Konakridy Road",
            "Konary Dee Road",
            "Koncoya Street",
            "Kondolo Road",
            "Konel Lane",
            "Konicon Lane",
            "Koniemodu Street",
            "Kono Compound Drive",
            "Konoyama Drive",
            "Korjie Street",
            "Korma St",
            "Koroma Crescent ",
            "Koroma Drive",
            "Koroma Lane",
            "Koroma Lane ",
            "Koroma Street",
            "Koroma Street ",
            "Koromaya Street",
            "Koronkoya Road",
            "Kortu Drive",
            "Kowa Drive",
            "Kowa Street",
            "Kowar Drive",
            "Koya Drive",
            "Koyar Drive",
            "Koyo Street",
            "Kpakiwa Drive",
            "Kpambawa Street",
            "Kpassamoi Street",
            "Kpoli Street",
            "Kpulun Street",
            "Kpumuku Drive",
            "Kriodar Drive",
            "Kroo Town Road",
            "K'S Drive",
            "Kukuna Drive",
            "Kuku's Drive",
            "Kumaya Street",
            "Kumaya Street ",
            "Kumba lane",
            "Kuyateh Avenue",
            "Kuyateh Drive",
            "Kuyateh Street",
            "Kuyembeh Drive",
            "Kuyumodu Street",
            "Kwonkwo Lane",
            "KwonkwoLane Main Motor Road ",
            "L B J Drive",
            "Lab Lane",
            "Labaka Lane",
            "Ladies Lane",
            "Lady Smile Street",
            "Laggah Street",
            "Lagoun Drive ",
            "Lahai Drive",
            "Lahai Lane",
            "Lahai Street",
            "Lake Street",
            "Lakka Beach road",
            "Lakka hospital road",
            "Lakka Road",
            "Laloya Drive",
            "Lamar's Lane",
            "Lambay Drive",
            "Lamin Drive",
            "Lamin Lane",
            "Lamin Street",
            "Lamina Sankoh Street",
            "Lamina Yard",
            "Land Site Road",
            "Langley Street",
            "Lanla Drive ",
            "Lansana Drive",
            "Lansana Nyalley Drive",
            "Lansana Nylalley Drive",
            "Lansana Sesay Drive",
            "Lansite Street",
            "Lashes Lane",
            "Lashiteh Drive ",
            "Lasiteh Drive ",
            "Las-Palmas",
            "Last Banking",
            "Latco Drive",
            "Lavalie Drive",
            "Lawrence Drive",
            "Lawrence Street",
            "Lawson Lane",
            "Lawson Street",
            "LBJ Drive",
            "Leah Street",
            "Lear Shaw Drive",
            "Leceister Peak ",
            "Leicester Peak Road",
            "Leicester Road",
            "Leigh Drive",
            "Leigh Lane",
            "Leigh Road",
            "Leigh Street",
            "Leigh's Lane",
            "Lemon Lane",
            "Leopard Drive",
            "Leopard Hill Drive",
            "Leopard Hill Road",
            "Leopard Street",
            "Leo's Drive",
            "Lera  Shaw Drive",
            "Levay Drive",
            "Lewis Drive",
            "Lewis Street",
            "Liberty Drive",
            "Liddle Street",
            "Lightfoot Boston Street",
            "Lightfoot-Boston Road",
            "Liion Street ",
            "Lima Street",
            "Lion Drive",
            "Lion Street",
            "Little Kroo Street",
            "Little Street",
            "Liverpool Street",
            "Lizzy Decker Drive",
            "Locust Street",
            "Loko Lane",
            "Loko Town",
            "Loko Town Road",
            "London Street",
            "Looking Town Road",
            "Lord Mo Drive",
            "Lord Street",
            "Lott Farm Road",
            "Lotto Farm  Road",
            "Lotto Farm Road ",
            "Love Lane ",
            "Lovel Lane",
            "Low Kingham",
            "Lower Bombay Street",
            "Lower Cassava Farm",
            "Lower Gbendemubu",
            "Lower Imatt",
            "Lower Kandeh Drive",
            "Lower Komba Lane",
            "Lower Pipe Lane",
            "Lower Pipe Line ",
            "Lower Sequeen Drive",
            "Lower SS Camp",
            "Lower Waterloo St.",
            "Loxley Street",
            "Lucan Drive",
            "Lucas Street",
            "Luck J Street",
            "Lucky Road",
            "Lugbu Drive",
            "Luke Lane",
            "Lukeley Drive",
            "Lumely Road",
            "Lumley Beach Road",
            "Lumley Road",
            "Lumley Roundabout",
            "Lumley Street",
            "Lungi Road  ",
            "Lunsar Road",
            "Lunsar-Makeni Highway",
            "M.C.A Drive",
            "M.C.A. Lane",
            "M.O.P Drive",
            "Maana Kpukumu Drive",
            "Mabarta Road",
            "Mabessenh Road",
            "Mabint Ray Drive",
            "Mabinty K. Drive",
            "Macaluey Street",
            "Macarthy Street",
            "Macaulay Close Drive",
            "Macaulay Drive",
            "Macaulay Street",
            "Macauley Street",
            "Macdonald Street",
            "Macfoy Lane",
            "Mackay Street",
            "Macloud Drive",
            "Macuauley Drive",
            "Macuauley Street",
            "Maculay Street",
            "Madam Yoko Street",
            "Madana Street",
            "Madara Street",
            "Maddett Avenue",
            "Maddett Avenue ",
            "Madette Drive",
            "Madina  Road",
            "Madongo Town",
            "Madusu Drive",
            "Magalia Drive",
            "Magazine Cut",
            "Magazine Street",
            "Magbosy Drive",
            "Magburaka Road",
            "Magburaka Roundabout",
            "Maggy's Avenue",
            "Maggy's Avenue ",
            "Magnus Street ",
            "Mahdi Drive",
            "Mahera Road",
            "Mahmoud Drive",
            "Mahmoud Lane",
            "Mahoi Drive ",
            "Main Mo",
            "Main Motor Road",
            "Main Mottor Road .",
            "Main Road Kissi Town",
            "Main Street",
            "Maize Terrace",
            "Maju Bah Street",
            "Makama Street",
            "Makani Street ",
            "Makeni-Kono Highway",
            "Makieu Lane",
            "Malama Thomas Street",
            "Malata Lane",
            "Maligi Drive",
            "Mal's Drive",
            "Malta Lane",
            "Malta Street",
            "Mama Lane",
            "Mama Messie Drive ",
            "Mama Rogers Drive",
            "Mamah Lane",
            "Mamankie Road",
            "Mambo Lane",
            "Mambo village",
            "Mambolo Drive",
            "Mambu Drive",
            "Mambu Lane",
            "Mambu Street",
            "Mamie Koroma Street",
            "Mammah Lane",
            "Mammah Street",
            "Mammy Street",
            "Mammy Streety",
            "Mammy Yoko Street",
            "Mamnah Lane",
            "Mamodia Lane",
            "Mamoud Lane",
            "Mamoud Lane ",
            "Mamud Town",
            "Mamudu Lane",
            "Mamudu Town",
            "Mamy Yoko Street",
            "Mandette Drive",
            "Manette Street",
            "Manfred Sesay Drive",
            "Manga Drive",
            "Mangalia Drive",
            "Manna Kpukumu Drive",
            "Mannah Street",
            "Mans Lane ",
            "Mansaray Drive",
            "Mansaray Lane",
            "Mansaray Street",
            "Mansary Drive",
            "Mansurah Lane ",
            "Marah Lane",
            "Marah Street",
            "Mariam Drive",
            "Mariama Street",
            "Mariara Street",
            "Mariatu Street",
            "Marilyn Drive",
            "Marilyn's Drive",
            "Marimbo",
            "Marine Crescent",
            "Marjay Town Road",
            "Market Road",
            "Market Street",
            "Marlyn Drive",
            "Marrah Drive",
            "Martin Farmer Street",
            "Martins Street",
            "Marute Road",
            "Mary Drive",
            "Mary J Drive",
            "Mary Lane",
            "Mary Street",
            "Masaray Street",
            "Masata Street",
            "Masaya Street",
            "Maseray Drive",
            "Masimbo Drive",
            "Masimera Road",
            "Massaquio Drive",
            "Massaquoi Street",
            "Matekeh Road",
            "Mathensha Road ",
            "Mathew Demby Street",
            "Mathomeh",
            "Mathore Drive",
            "Mathore Road",
            "Matkay Lane",
            "Matkay Street",
            "Matthew Street",
            "Mattu Close",
            "Maxwell Khobe Street",
            "May Drive",
            "May Street",
            "Mayemie Drive",
            "Maylie Lane",
            "Maylie Street",
            "Mayor Close",
            "M'boma Drive",
            "MB's Drive",
            "Mc Confort",
            "Mccormac Street",
            "M'cormick Street",
            "Med Drive",
            "Med K. Drive",
            "Med Kolleh Drive",
            "Medta Drive",
            "Mellon Street",
            "Melvetta Drive",
            "Memordia Lane",
            "Memorial Lane",
            "Mende Corner",
            "Mende Street",
            "Mende Town Road",
            "Mendenkia Drive",
            "Mendez Street",
            "Mends Street",
            "Mensurah Lane",
            "Mercer Street",
            "Mess Road",
            "Messoh Drive",
            "Metchem Avenue",
            "Metchem Drive",
            "Metchem Junction",
            "Metchem Road",
            "Metzeger Lane",
            "Metzger Street",
            "Mexeux Street",
            "Michael Street",
            "Middle Hill Station",
            "Mik Drive",
            "Mile Thirteen Old Road",
            "Milia Street",
            "Military Hospital Road",
            "Millicent Drive",
            "Mills Drive",
            "Milton Margai College Road",
            "Milton Street",
            "Mina Drive",
            "Minkalu Street",
            "Minor Road",
            "Mirror Road",
            "Misery Street",
            "Miss Drive",
            "Mission Road",
            "Misson Road",
            "Modu Street",
            "Mof Road",
            "Mogboroka Road",
            "Mohamed Ali Drive",
            "Mohamed Davidson Sesay Drive",
            "Mohamed Drive",
            "Mohmoud Lane",
            "Moisia Street",
            "Moiwuleh Street",
            "Mojama Street",
            "Mojo Drive ",
            "Mo-Kamara Drive",
            "Mokeh Drive",
            "Moko Town",
            "Mola Drive",
            "Molia Street",
            "Momanga Drive ",
            "Momenga Drive",
            "Momiya Road",
            "Momoh Gottor Street",
            "Momoh Phujhan Drive",
            "Moneyah Road",
            "Monfred Momoh Drive",
            "Monfred Sesay Drive",
            "Mongegba Main Highway",
            "Mongolay Road",
            "Moniya Road",
            "Monsieur Saffa Drive",
            "Montague Street",
            "MOP Drive",
            "Morgan Drive ",
            "Morgan Lane",
            "Morgan Lane ",
            "Morgan Street",
            "Morie Momoh Drive",
            "Moril Drive0",
            "Morris Drive",
            "Mortal Lane",
            "Morthiam High Way",
            "Moseray Drive",
            "Moseray Lane ",
            "Moses Close",
            "Mosidia Drive",
            "Mosimbo Drive1",
            "Mosimbo Drive2",
            "Mosque road",
            "Motormeh Community",
            "Mount Aureol Terrace",
            "Mount Blue Close",
            "Mount Zion",
            "Mountain Cut",
            "Muctarr Drive",
            "Mudge Farm",
            "Mukpateh Terrace",
            "Mumenga Drive",
            "Mummy Grace Street",
            "Mummy Sheik Drive",
            "Mummy Shiek Drive.",
            "Mummy Street",
            "Munu Drive",
            "Murray Town Road",
            "Mus Drive",
            "Musa Drive",
            "Musa Lane",
            "Musa Sesay Street",
            "Musa Street",
            "Mustapha Street",
            "Musu Drive",
            "Musu Jonny Drive",
            "N.M. Turay Drive",
            "Nabieu Drive",
            "Nagbana  Street ",
            "Nahim Drive",
            "Naiahcom Drive ",
            "Naiahcom Road ",
            "Naimbana Street",
            "Nana Kroo Street",
            "Nanoh Drive",
            "Nanor Drive",
            "Naomi Lane",
            "Naoson Street",
            "Nas Carew Drive",
            "Nataamue Street",
            "Natamus Street",
            "Navo Drive",
            "Nazarene Drive",
            "Nbanbaya Lane",
            "Ndoeka Drive",
            "Nelson Lane",
            "Nemweh Street",
            "Nenekoro Road",
            "Netwon Drive",
            "Neville Drive",
            "Neville Terrace",
            "New  London  Road  ",
            "New  Site  Road",
            "New Jersey",
            "New Jerusalem Lane",
            "New Kabba Drive",
            "New Kambees Road",
            "New Lane",
            "New Line",
            "New London",
            "New London Street",
            "New Site",
            "New Site  Road",
            "New Street",
            "New York Drive",
            "New York Garage Road",
            "New York Junction",
            "Newah Drive",
            "Newland Drive",
            "Newstead Lane",
            "Newton Drive",
            "Newtown Drive",
            "Ngaujah Drive",
            "Ngawo Drive",
            "Ngevao Drive",
            "Ngobeh Drive",
            "Ngobulango Street",
            "Nicholson Drive.",
            "Nicol Drive.",
            "Nicol Street",
            "Nicole Street",
            "Nicolson Drive .",
            "Niematta Drive",
            "Nnaji Tunkara Drive ",
            "Noah Drive",
            "Nonneh Umu Drive",
            "Nonsolt Drive",
            "Norman Street",
            "Noroh Lane",
            "Not Passable In Vehicle",
            "NP Drive",
            "Number 2 River Beach Road",
            "Number Two River ",
            "Numukel Drive",
            "Nuni Drive ",
            "Nunius Drive",
            "Nyama Drive",
            "Nyandeyama Road",
            "Nyberg Drive",
            "Nylander Street",
            "O. Jalloh Drive",
            "Oasis Drive",
            "Oau Drive",
            "Oau Quater Drive",
            "Obama Junction",
            "Obed Cole Street",
            "Obed Cole Street ",
            "Obed Street",
            "Ocean Street",
            "Ocean View",
            "Odelia Drive",
            "Ogendeh Drive",
            "Ogoo Lane",
            "Ogoo Lane 3",
            "Ojumends Farms",
            "Okd Adonkia Road",
            "Okutown",
            "Old Adonkia Road",
            "Old Cotton Club",
            "Old Freetown Road",
            "Old Gloucester Road",
            "Old Kambia Field",
            "Old Kambis Road",
            "Old Peninsular Road",
            "Old Portloko Road",
            "Old Railway Line",
            "Old Road",
            "Old Road ",
            "Old S.L.B.C Drive",
            "Old School Drive",
            "Old Signal Hill Road",
            "Old Town Road",
            "Old Waterloo Road",
            "Old York Road",
            "Oldfield Street",
            "Oliver Street",
            "Olombo Drive",
            "Olu Williams Street",
            "Olu-Jones Drive",
            "Olympus Jerusalem Avenue",
            "Omolay Bush Drive",
            "Omolay Bush Step",
            "One House",
            "One Mile Road",
            "One Pole Drive",
            "One Pole , Old Road",
            "O`neil Street",
            "One-Word St",
            "Oniel Street",
            "Ophelia Drive",
            "Ophelia Foyoh Drive",
            "Opper Kargbo Drive",
            "Opposite Conteh Street",
            "Oranto Close",
            "Osis Drive ",
            "Osman Banko Drive",
            "Osman Conteh Street",
            "Osman Drive",
            "Osman Drive ",
            "Osman Kamara Drive",
            "Osman Lane",
            "Osman Thomas",
            "Owen Lane",
            "Owen Street",
            "Ozark Renner Street",
            "Pa Jo Juana Drive",
            "Pa Komrabai Compound",
            "Pa Osman Sheriff Drive",
            "Pa Will Street",
            "Pa-Adikalie Lane",
            "Pabai Street",
            "Pademba Road",
            "Pain Street",
            "Pakai Drive",
            "Palampo Wata",
            "Paleys Drive",
            "Pallie Drive",
            "Palmar Drive",
            "Palmer Lane",
            "Palmer Street",
            "Paloko Road",
            "Panji Street",
            "Pantap Water",
            "Papa Pee Drive ",
            "Papa T Drive",
            "Paris Road",
            "Paris Street",
            "Park Royal Drive",
            "Parker Drive",
            "Parker Lane",
            "Parry street",
            "Parry Street ",
            "Pasonage Street",
            "Pastor Chambers Drive",
            "Path",
            "Pat-Sowe Drive",
            "Patt Street",
            "Patton Street",
            "Paul Kamara Drive",
            "Paul Saffa Avenue ",
            "Paul Saffa Drive ",
            "Paul Street",
            "Paulsilla Street ",
            "Peace Drive",
            "Peak Hills",
            "Pearson Street",
            "Peemoone Drive",
            "Peninsular Road",
            "Percival Lane",
            "Percival Street",
            "Perry Street",
            "Pesima Drive",
            "Pessima Drive",
            "Peter Conteh Street",
            "Peter Kenah Drive",
            "Peter Lane",
            "Peters Drive",
            "Peters Lane",
            "Peters Street",
            "Peterson Street",
            "Petifu Konmadu Junction",
            "Philip Street",
            "Pike Street",
            "Pikes Street",
            "Pipe Line",
            "Pipe Line Drive",
            "Pipe Street",
            "Pivot Street",
            "Plums Heath",
            "Plums Heath Street",
            "Point Street",
            "Police Roundabout",
            "Ponka Road",
            "Porpor Street",
            "Port Loko Road",
            "Portloko Highway Road",
            "Possible Lane",
            "Potter Hill",
            "Pottor Road",
            "Poultry Drive",
            "Poultry Road",
            "Pownal Street",
            "Pratt Lane",
            "Pratt Street",
            "Precilla Lane",
            "Preston's Drive",
            "Prince Alfred Street",
            "Prince Alfred Street, Old Road",
            "Prince Alieu Drive",
            "Prince Drive",
            "Prince Street",
            "Princess Allieu Drive",
            "Priscilla Street",
            "Probyn Street",
            "Providence Drive",
            "Puke Street",
            "Pultney Street",
            "Pump Line",
            "Pump Line Drive",
            "Purcell Street",
            "Pyke Street",
            "Pyne Street",
            "Q Line",
            "Qiuality Drive",
            "Quaray  Ferry  Road ",
            "Quarry  Tardi  Road",
            "Quarry Road",
            "Queen Drive",
            "Queen Street",
            "Qurray  Road  ",
            "R Street",
            "R. B. Kowa Drive",
            "R.B. Kowa Drive",
            "Rabboni Playing Ground Avenue",
            "Rabboni Street",
            "Ragent Road",
            "Rahman's Drive",
            "Ramond Caulker Drive",
            "Ramsy Close",
            "Ramzy Close",
            "Ranger Street",
            "Rasmusson Street",
            "Rasta Garden",
            "Rawdon Street",
            "Raymond Caullker Drive",
            "Raymond Koker Drive",
            "Raymond Smith Drive",
            "Reader Street",
            "Reconciliation Drive",
            "Regent Grafton Highway",
            "Regent Road",
            "Regent Street",
            "Reggies Drive",
            "Regina Street",
            "Rehab Lane",
            "Reider Drive",
            "Renka  Drive",
            "Renka Street",
            "Renner Drive",
            "Repentance Drive",
            "Reservation Road",
            "Residential Road",
            "Resmuss Johnson Street",
            "Reu De La Paux",
            "Rev. Millicent Bock  Drive",
            "Rev. Samuels Lane",
            "Rica Drive",
            "Richard Caulker Drive",
            "Richard Street",
            "Richie Drive",
            "Richmond Street",
            "Riddle Avenue ",
            "Riddle Drive",
            "Ridley Street",
            "Right Path Lane",
            "Right Path Street",
            "Rita Drive",
            "Rita Street .",
            "Riverside Drive",
            "Robala Street",
            "Robert Drive",
            "Robert Street",
            "Roberts Drive ",
            "Robins Road",
            "Robis Road",
            "Robis Royeama Road",
            "Robola  Street",
            "Rock Lane",
            "Rock Street",
            "Rock Street ",
            "Rocklyn Street",
            "Rofe Road Godericeh",
            "Rogbane Road",
            "Rokel Street",
            "Rokel Street ",
            "Rokierahman Drive",
            "Rokupr Road",
            "Roll Drive",
            "Roman Street",
            "Ronifer Drive",
            "Ronsab Drive",
            "Rosamond Drive",
            "Rosamond Street",
            "Rosemond Drive",
            "Rotifunk Market",
            "Route Principale",
            "Rowe Street",
            "Royal Lane",
            "Royay Becklyn",
            "Royeama Drive",
            "Royeama Field Road",
            "Roy-Macaluley Drive",
            "Ruedelapaix",
            "Rui de Paix",
            "Runiffer Drive",
            "Ryan Drive",
            "S S Drive",
            "S.L.B.C Dirve",
            "S.S Drive",
            "Sa Road",
            "Saadatu Drive",
            "Saba Drive",
            "Sabanor Street",
            "Sabu Drive",
            "Sabu Lane",
            "Saccoh Drive",
            "Sackville Lane",
            "Sackville Street",
            "Saffa Sama",
            "Saffa Street",
            "Sagill Drive",
            "Sahara Street",
            "Sahid Sesay Drive",
            "Sahr Fomba Drive",
            "Sahr Fomba Street",
            "Sahr Johnny Drive",
            "Sahrson Drive",
            "Saia Lane",
            "Saiba Street",
            "Saidu Lane",
            "Saidu Street",
            "Saila Lane",
            "Saint Augustine Compound",
            "Saint Francis Street",
            "Saint Mary Street",
            "Sal Drive",
            "Salam Drive ",
            "Salia Lane",
            "Salia-Konneh Drive",
            "Salieu Kamara Street",
            "Salifu Road",
            "Saliya Street",
            "Sallia Drive",
            "Sallia Konneh Drive",
            "Sallme Road",
            "Salter Street",
            "Sam Ansumana Drive",
            "Sam Lane",
            "Sama Drive",
            "Sama Lane",
            "Samai Compound",
            "Samai Street",
            "Samasa Drive ",
            "Samba Drive",
            "Samco Farm",
            "Samira Drive",
            "Samko City",
            "Samll Road",
            "Sammy J Drive",
            "Sample Lane",
            "Sample Street",
            "Samson Drive",
            "Samu Drive",
            "Samuel Drive",
            "Samuel Lane",
            "Samuel Street",
            "Samuels Drive",
            "Samuel's Drive",
            "Samura Drive",
            "Samura Lane",
            "Samura Street",
            "Samurah Drive",
            "Samurai Drive",
            "Sand Lane",
            "Sand Sand Ground",
            "Sanda Lane",
            "Sanda Road",
            "Sanda Street",
            "Sanders Street",
            "Sandi Drive ",
            "Sandi Lane",
            "Sandi Street",
            "Sandra Street",
            "Sandy Drive",
            "Sandy Lane",
            "Sandy Street",
            "Sandyya Drive ",
            "Sangarie Close ",
            "Saniya Road",
            "Sankey Street",
            "Sankoh Lane",
            "Sannah Balaya Street",
            "Sannoh Drive",
            "Sansie Drive",
            "San-Sumana Drive",
            "Santigie  Lane ",
            "Santos Lane ",
            "Sapo Drive",
            "Saquee Drive",
            "Sara Lane",
            "Sara Lane ",
            "Sarah Lane",
            "Sasin Drive",
            "Satlan Drive ",
            "Savage Square",
            "Savage Street",
            "Sawaneh Drive",
            "Sawanei Drive",
            "Sawani Drive",
            "Sawanie Drive",
            "Sawanneh Drive",
            "Sawarray-Deen Street",
            "Sawi Drive",
            "Sawie Drive",
            "Sawneh Drive",
            "Sawnnah Drive",
            "Sawnneh Drive",
            "Scan Drive",
            "Scan Drive 2",
            "Schelenka Drive",
            "Scott Street",
            "Scott-Manga Drive",
            "Sea View Estate",
            "Sea View Road",
            "Seaga Drive ",
            "Seasey Drive",
            "Seaside Drive",
            "Sebeh Drive",
            "Second Bango Farm",
            "Second Street",
            "Sedia Drive",
            "Sedomoya Lane",
            "Seems Drive",
            "Seiyia Street",
            "Selina Drive",
            "Semabu Lane",
            "Senbeh Drive",
            "Senesie Street",
            "Senessie Drive",
            "Sengbeh Pieh Drive",
            "Senphine Drive",
            "Sent Marie Street",
            "Sent Mary Street",
            "Septimus Vandy Street",
            "Seri Drive",
            "Serry Drive",
            "Sesay  Drive",
            "Sesay Bright Lane ",
            "Sesay Compound",
            "Sesay Drive",
            "Sesay Lane",
            "Sesay Street",
            "Sesay's Drive",
            "Settra Kroo Street",
            "Seuphine Drive",
            "Seventh Battalion Drive",
            "Shaka Lane",
            "Shalom Drive",
            "Shaloon Drive",
            "Shared Drive",
            "Sharon Street",
            "Sheku Bangura Drive ",
            "Sheku Drive ",
            "Sheku Kargbo Drive",
            "Sheku Lane",
            "Sheldon Street",
            "Shelenka Drive",
            "Shellon Street",
            "Shephpedeh Drive",
            "Sherbro Town",
            "Sheriff Drive",
            "Sherry Lane",
            "Shiek Kallon Drive",
            "Shillon Drive",
            "Shinku Lane ",
            "Shorenkeh Street",
            "Short Street",
            "Shot Street",
            "Show Avenue",
            "Show Field Road",
            "Shyllon Complex",
            "Shyllon Street",
            "Sia Drive",
            "Sia Warker Drive",
            "Siabah Drive",
            "Siabaj Drive",
            "Siaka Steven Street",
            "Siaka Stevens Street",
            "Siba Street",
            "Sibthorpe Street",
            "Sidibe Lane",
            "Sierra Drive",
            "Signal Hill Road",
            "Silcam Drive",
            "Silcom Drive",
            "Sillah Drive",
            "Silvanos Street",
            "Silver Street",
            "Sim Street",
            "Simbya Drive",
            "Simiyatu Drive",
            "Sindomoya Road",
            "Sinkanunia Road",
            "Sir Samuel Lewis Road",
            "Sixth Street",
            "Skelton Street",
            "Sky Junction",
            "Slaughter Street",
            "Slbc Drive",
            "SLBS  Drive",
            "Slingo Street",
            "SLMB Playing Field",
            "Small Road",
            "Smart Farm Road",
            "Smart Lane",
            "Smith Drive",
            "Smith Lane",
            "Smith Street",
            "Smythe Street",
            "Sogie-Thomas Close",
            "Sogie-Thomas Street",
            "Soko Kaisamba Lane",
            "Soldier Street",
            "Solo B Drive",
            "Solo B. Drive",
            "Solo Drive",
            "Somalia Town Road",
            "Sombo Street",
            "Somerset Street",
            "Sommer Street ",
            "Songa Drive",
            "Songha Drive",
            "Songo Street",
            "Songo Town",
            "Songs Drive",
            "Sonia Drive",
            "Sonkay Drive",
            "Sonny Street",
            "Sorie Jannah Lane",
            "Sorie Town",
            "Sos Drive",
            "Sosie Town",
            "Soso Compound",
            "Soso Town Street",
            "South Ridge",
            "Sowe Lane",
            "Spencer Lodge",
            "Spilsbury Drive ",
            "Spo Drive",
            "Spur Loop",
            "Spur Road",
            "Spur View Estate Drive",
            "Square Street",
            "SS Drive.",
            "St Augustine Compound",
            "St Charles Drive",
            "St Joseph Road",
            "St Josephs Avenue",
            "St Mary's Drive",
            "St Michael's Beach Road",
            "St Paul Drive",
            "St Pauls Drive",
            "St Paul's Drive",
            "St. Charles Road",
            "St. Michael Junction",
            "St. Michael Road",
            "St.Charles Road",
            "St.John Turn-Table",
            "Stafford Drive",
            "Stagarbom  Road  ",
            "State Street",
            "Station Road",
            "Steven Drive",
            "Steward Street",
            "Straser King Drive",
            "Strasser King Ville",
            "Street Two Plums Heath",
            "Sugar Land Drive",
            "Sugar Loaf Drive",
            "Sugar Loaf Road",
            "Suluku Drive",
            "Suma Street",
            "Sumah Street",
            "Sumaila Street",
            "Summer Street",
            "Sumner Street",
            "Sundu Drive",
            "Sunny Street",
            "Sunshine Close",
            "Sunshine Drive",
            "Sunshine Valley",
            "Sunshine Valley Drive",
            "Suppui Street",
            "Sure Drive",
            "Susu Compound",
            "Susudi Road",
            "Susuya Road",
            "Swaford Drive",
            "Swarary Street",
            "Sward Street",
            "Swarray-Deen Street",
            "Sweds Free Avenue Drive",
            "Sweet Street",
            "Syke Lane",
            "Syke Street",
            "Syke Street2",
            "T Line",
            "T.K Drive",
            "Tabara Sillah Drive",
            "Tailor Luwis Drive",
            "Tamba Lebbie Drive",
            "Tamba's Drive ",
            "Tambi Drive",
            "Tamukay Drive",
            "Tamukey Drive",
            "Tankyard Road",
            "Taqi Drive",
            "Tarakunda Drive",
            "Tarawalie Drive",
            "Tarawally Drive",
            "Tardi  Road",
            "Tarleton Lane",
            "Taylor Coleridge Drive",
            "Taylor Drive",
            "Taylor Kamara Street",
            "Taylor Street",
            "Taylor Villa",
            "Technical Institute Drive",
            "Teddy Drive",
            "Tee Cee Drive",
            "Teekay Drive ",
            "Tejan Kabba Street",
            "Tejan Lane",
            "Tejan Street",
            "Teko Road",
            "Temne Compound",
            "Ten House Drive",
            "Tenefoe Drive ",
            "Tengbeh Terrace",
            "Tengbeh Town",
            "Teresa Lane",
            "Terrace Drive",
            "Terry Street",
            "The Christian Palace",
            "The Lord's Avenue",
            "The Maze",
            "Theresa Lane",
            "Thiamu Bangura Drive",
            "Third Drive",
            "Third Street",
            "Tholley Drive",
            "Thomas Drive",
            "Thomas Street",
            "Thomas-Humper Drive",
            "Thomas-Humper Street",
            "Thombi Lol Road",
            "Thombo Lol Road",
            "Thompson Lane",
            "Thomson Bay Road",
            "Thomson Drive",
            "Thonkia Road",
            "Thonkoya Main Road",
            "Thonkoya Road",
            "Thornton Street",
            "Thoronka Drive",
            "Thoronkah Drive",
            "Thorpe Drive",
            "Thorton Street",
            "Thronkah Drive",
            "Thullah Street",
            "Thundar Hill",
            "Tia Street",
            "Ticker Drive",
            "Tiger Land",
            "Tigi Drive",
            "Tigie Drive ",
            "Tihaland",
            "Timbo Avenue",
            "Timbo Drive",
            "Timbo Lane",
            "Timbo Street",
            "Timothy Drive",
            "Titty Drive ",
            "Tity Lane",
            "Tiwani Drive",
            "Tiyo Street",
            "Today Alimamy ",
            "Today Alimamy Conteh Road ",
            "Today Conteh ",
            "Today Lane",
            "Today Musa Drive",
            "Tom Avenue Eco Center",
            "Tommy Drive",
            "Tommy Street",
            "Tonika Drive",
            "Tonkaya Road",
            "Tonkolili Street",
            "Tonkoya Road",
            "Too Good Street",
            "Top Estate Drive",
            "Toronka Drive",
            "Torunka Drive",
            "Toure Street",
            "Town Square Lane",
            "Tranquility Close",
            "Tree Planting",
            "Trelawney Street",
            "Trera Avenue",
            "Trera Drive",
            "Trojan Lane",
            "Truscott Street",
            "Tucker Avenue",
            "Tucker Drive",
            "Tucker Street",
            "Tumba Street",
            "Tumoe Drive",
            "Tumoi Drive Marjay Town",
            "Turanka Drive",
            "Turay Drive",
            "Turay Street",
            "Tutu Drive",
            "Two Plums Heath",
            "Ubay Drive ",
            "UK Drive ",
            "UN Drive",
            "Unamsil  Drive",
            "Unity Drive",
            "Unity Street",
            "Unoayou Drive ",
            "Updam Mambo",
            "Upper Angola Town",
            "Upper Banana Street",
            "Upper Baoma",
            "Upper Bathole",
            "Upper Betham Lane",
            "Upper Brook Street",
            "Upper Casava Farm",
            "Upper Clearence Street",
            "Upper Dadley Street",
            "Upper Darm Mambo",
            "Upper East Street",
            "Upper Easton Street",
            "Upper Femi Turner Drive ",
            "Upper Gbendenmbu",
            "Upper George-Brook",
            "Upper Gooding Drive",
            "Upper Jeneba Drive",
            "Upper Johannes Street",
            "Upper John Street",
            "Upper Kabba Drive",
            "Upper Kapu Drive",
            "Upper Kargbo Drive",
            "Upper Kennex Drive",
            "Upper Komba Lane",
            "Upper Lion Street ",
            "Upper Mambo Dam",
            "Upper Marjay Town",
            "Upper Metchem",
            "Upper Modimbo",
            "Upper Mosimbo 1",
            "Upper Mosimbo 2",
            "Upper Mosimbo Drive",
            "Upper Mosque Road",
            "Upper Mountain Cut",
            "Upper Nelson Lane",
            "Upper Patton Lane",
            "Upper Patton Street",
            "Upper Quarry",
            "Upper Regent Street",
            "Upper Richmond Street",
            "Upper Savage Square",
            "Upper Sequeen Drive",
            "Upper Signal Hill Road",
            "Upper Sugar Loaf",
            "Upper Turay Drive",
            "Upper Venns Drive",
            "Upper Victoria Street",
            "Upper Waterloo Street",
            "Upper West Brook Street",
            "Upper Winchester Street",
            "Vandi Drive",
            "Vandi Drive ",
            "Vandy Lane",
            "Vandy Street",
            "Vecent Street",
            "Vic Amara Drive",
            "Viccana Avenue",
            "Victoria Drive",
            "Victoria Street",
            "Vidma Drive",
            "Vidma Street",
            "Villa Blanc",
            "Villa Road",
            "Vincent Drive",
            "Vincent Street",
            "Vinell Drive",
            "Vinton Street",
            "W Lane",
            "Wadi Lane",
            "Wafa Road",
            "Waffe Road",
            "Wahid Drive",
            "Wahtad Drive",
            "Walker Lane",
            "Wallace Johnson Street",
            "Wallie Drive",
            "Walpole Street",
            "Wan-Ose Portor Road",
            "Wardlow Drive",
            "Wassaya Road",
            "Water Oxe Road",
            "Water Road ",
            "Water Rocks Road",
            "Water Street",
            "Waterloo Division High Way",
            "Waterloo Main Highway",
            "Waterloo Street",
            "Waterside Road",
            "Waterworks Road",
            "Watkins Drive ",
            "Watson Street",
            "Well Body Street",
            "Wellington Street",
            "Wesley Street",
            "Wesleyan Street",
            "West Brook Street",
            "West Street",
            "Whahdee Street",
            "Wharf Road",
            "White House ",
            "White Pole",
            "White Street",
            "Who Can Clos Drive",
            "Whyeed Drive",
            "Wilberforce Road",
            "Wilberforce Road .",
            "Wilberforce Street",
            "Wilkinson Bypass Lane",
            "Wilkinson Road",
            "Will Street",
            "Willberforce Road",
            "Willberforce Spur Loop",
            "William Drive",
            "William Shaka Road",
            "William Street ",
            "Williams Drive",
            "Williams Street",
            "Williams Street ",
            "Willison Street ",
            "Willoughby Lane",
            "Willoughby Lane 1",
            "Willoughby Lane 2",
            "Willoughby Lane 3",
            "Willoughby Lane 4",
            "Wilson Drive",
            "Wilson Street",
            "Wilson Street ",
            "Wincester Street",
            "Winchester street",
            "Winner Avenue ",
            "Wisdom Drive",
            "Wise Lane",
            "Wizzy Lane",
            "Woobay Lane",
            "Woreh  Road",
            "Wright Drive",
            "Wujawah Street",
            "Wurie  Drive",
            "Wurie Street",
            "Wurisco Drive",
            "Wurrie Compound",
            "Wurrie Lane",
            "Wyse Lane",
            "Y M Jones Drive",
            "Y2K Drive ",
            "Ya Alimamy Street ",
            "Ya Mamadi Street",
            "Ya Marie Sesay  Drive",
            "Ya Marie Sesay Drive",
            "Yaidi Street",
            "Yaikain Street",
            "Yaiyai Street",
            "Yalifoday Street",
            "Yankai Beach Road",
            "Yankson Drive",
            "Yankuba Street",
            "Yansaneh Drive",
            "Yarteh Drive",
            "Yateh Drive",
            "Yateya Field ",
            "Yatta Musa Drive",
            "Yatta Musu Drive",
            "Yatta Street",
            "Yayah And Musu Closet",
            "Yayah Seray Drive",
            "Yeiliforah Street",
            "Yetaya  Road",
            "Yetta Musa Drive",
            "Yibaya Drive",
            "Yoke Road ",
            "Yokies Street",
            "Yongoro Road",
            "Yopoi Street",
            "York Road",
            "Youth Farm ",
            "Youth Land Drive ",
            "Yumkella Drive",
            "Yumkella Road",
            "Zainab Drive",
            "Zarrah Lane",
            "Zier Lane ",
            "Zion Drive",
            "Zizer Avenue",
            "Z-Line",
            "Zoe Avenue",
            "Zone Drive",
            "Zone One Drive",
            "Zubaka Drive",
            "Zubaya Drive",
            "Zula Drive"
        ];
    //    echo count($streetNames);
    // echo $properties = MetaValue::select('id')->where('name','street_name')->count();
    // die;
        $properties = MetaValue::select('id')->where('name','street_name')->get();
    //    die;
        for($i=0;$i<2729;$i++){
            // echo count($streetNames[$i]);
            // echo $properties[$i]->id ;    
            MetaValue::where('id', $properties[$i]->id)->where('name','street_name')->update(['value' => $streetNames[$i]]);
        }
    //    return $users = \Excel::toArray(new ExcelImport, asset('abc_imports/additionaladdeess.xlsx'));
    // $url = asset('abc_imports/additionaladdress.csv');
    // $fp = fopen($url, 'r');
    // $csv = [];
    // $additional_address = new AdditionalAddress();
    // while ($row = fgetcsv($fp)) {
    //     $csv[] = $row;

    //     $check = \App\Models\AdditionalAddress::where('title',$row[0])->first();
    //     if(!$check){
    //         $additional_address->id = null;
    //         $additional_address->title = $row[0];
    //         $additional_address->save(); 
    //     }
    // }

    // // echo $additional_address;
    // fclose($fp);
    // return $csv;
    
    }
    
}
