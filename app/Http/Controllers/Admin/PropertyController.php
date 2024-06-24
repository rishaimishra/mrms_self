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
        // return "sdf";
        // dd($request->all());
        $organizationTypes = collect(json_decode(file_get_contents(storage_path('data/organizationTypes.json')), true))->pluck('label', 'value');


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

        if ($request->input('is_organization') == 1 && $request->input('organization_type')) {
            $this->properties->where('organization_type', $request->input('organization_type'))->where('is_organization', true);
        }

        if ($request->input('is_organization') && $request->input('is_organization') == 0) {
            $this->properties->where('is_organization', false);
        }


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
        $data['postcodes'] = Property::distinct('postcode')->orderBy('postcode')->pluck('postcode', 'postcode')->sort()->prepend('Select post code', '');
        $data['organizationTypes'] = $organizationTypes;

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

           }
        }else{
         // If User uploads a Excel file

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

    public function saveAssessment(Request $request)
    {
        // return $request;
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
        $data['property_assessed_value']=$request->property_assessed_value;
        $data['net_property_assessed_value']=$request->net_property_assessed_value;
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

        $propertyIds = [
            154, 527, 940, 1109, 1110, 2900, 3050, 3876, 4507, 7903, 7922, 9286, 
            9387, 10774, 11461, 11600, 15524, 41839, 41840, 41844
        ];
    
        if (empty($propertyIds)) {
            return ['error' => 'No property IDs provided'];
        }
    
        $propertiesToDelete = Property::get();
        $propertiesToDelete = Property::whereNotIn('id', $propertyIds)->get();
    
        DB::beginTransaction();
    
        try {
            foreach ($propertiesToDelete as $property) {
                // Delete associated assessment data
                $property->assessment()->delete();
    
                // Delete associated payment data
                $property->payments()->delete();
    
                // Delete related landlords, occupancies, and registry
                $property->landlord()->delete();
                $property->occupancies()->delete();
                $property->georegistry()->delete();
    
                // Delete property itself
                $property->delete();
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
    public function read_excel(){
        $streetNames = [
            "Ngiawo",
            "Ngobeh",
            "Ngobie",
            "Ngoka",
            "Ngongou",
            "NGulama",
            "Nichole",
            "Nicholson",
            "Nicle",
            "Nicol",
            "Nicol-Wilson",
            "Niederhausein",
            "Njai",
            "Nkamra",
            "Nnadi",
            "Nnaji",
            "Nneli",
            "Noah",
            "Noldred",
            "Norfleet",
            "Nortey",
            "Nottidge",
            "NULL",
            "Nuni",
            "Nuyma",
            "Nyagua",
            "Nyallay",
            "Nyalley",
            "Nyalopo",
            "Nyamoh",
            "Nyandemoh",
            "Nyangui",
            "Nyberg",
            "Nyei",
            "Nyelenkeh",
            "Nylander",
            "Nyuma",
            "Obed-Cole",
            "O'Connor",
            "Odinga",
            "Office",
            "Ojumu",
            "Okechukwu",
            "Okeke",
            "Okeke-Macauley",
            "Okoro",
            "Okoye",
            "Olah",
            "Olatunji",
            "Olings",
            "Olujones",
            "Olu-Jones",
            "Olu-williams",
            "Omodal",
            "Omoh",
            "Oneil",
            "Oniell",
            "Onuoha",
            "Onuona",
            "Onwama",
            "Onwuma",
            "Onyenze",
            "Onyeudo",
            "Opene",
            "O'reilly",
            "Organization",
            "Orielly",
            "Oseah",
            "Osman",
            "Othello-Kargbo",
            "Othmam",
            "Ottie",
            "Oyovo",
            "Paikisan",
            "Pakinson",
            "Palamer",
            "Palmer",
            "Palner",
            "Panaki",
            "Papa",
            "Papu",
            "Paris",
            "Parkinson",
            "Parsons",
            "Partel",
            "Partt",
            "Patewa",
            "Patnelli",
            "Patso",
            "Paul",
            "Pawells",
            "Paye",
            "Paywa",
            "Pearce",
            "Pears",
            "Peetz",
            "Pegbo",
            "Pemagbi",
            "Penn-Timity",
            "Pessima",
            "Peter",
            "Peters",
            "Phatty",
            "Philip",
            "Philips",
            "Pienaar",
            "Pimbi",
            "Pitt",
            "Pokawa",
            "Police",
            "Pollect",
            "Polo",
            "Pontis",
            "Porter",
            "Posseh",
            "Poter",
            "Potho",
            "Pover",
            "Praet",
            "Pratt",
            "Pratta",
            "Preston",
            "Prett",
            "Priddy",
            "Princess",
            "Property",
            "Pujuh",
            "Pullom",
            "Pyne",
            "Quee",
            "Queen",
            "Quinn",
            "Quist",
            "Quity",
            "Quiwa",
            "Quraishi",
            "Radder",
            "Rahman",
            "Ramos",
            "Randall",
            "Rando",
            "Ranner",
            "Rashid",
            "Raymond",
            "Reader",
            "Record",
            "Reffell",
            "Refuell",
            "Reider",
            "Reken",
            "Remi",
            "Renner",
            "Residence",
            "Richard",
            "Richards",
            "Riche",
            "Riddle",
            "Rinna",
            "Risch",
            "Robbin-Coker",
            "Robert",
            "Robertin",
            "Roberts",
            "Robet",
            "Robinson",
            "Robort",
            "Robot",
            "Rogers",
            "Rojos",
            "Rollings",
            "Roques",
            "Rose",
            "Rosenior",
            "Ross",
            "Rowe",
            "Roy",
            "Rubes",
            "Rugiatu",
            "Russel",
            "Saad",
            "Saba",
            "Sabab",
            "Saboleh",
            "Saccoh",
            "Sackey",
            "Saffa",
            "Sagba",
            "Saha",
            "Sahid",
            "Sahr",
            "Saidu",
            "Saio",
            "Sajoh",
            "Sakilla",
            "Sakoh",
            "Sakpa",
            "Sal",
            "Salaam",
            "Salamie",
            "Salankole",
            "Saleyah",
            "Salia",
            "Salieu",
            "Salin",
            "Sall",
            "Sallie",
            "Sallieu",
            "Salloum",
            "Sam",
            "Sama",
            "Samah",
            "Samai",
            "Samasa",
            "Samba",
            "Sambo",
            "Sam-kabba",
            "Samkoh",
            "Sam-Kpakra",
            "Sammie",
            "Samoura",
            "Sam-Roberts",
            "Samson",
            "Samu",
            "Samuel",
            "Samuels",
            "Samura",
            "Samurah",
            "Samuru",
            "Sandi",
            "Sando",
            "Sandy",
            "Sanko",
            "Sankoh",
            "Sankon",
            "Sannase",
            "Sannoh",
            "Sanpha",
            "Sansie",
            "Santigie",
            "Santigie-Koroma",
            "Sanu",
            "Sanusie",
            "Sanusy",
            "Sapateh",
            "Saquee",
            "Sarah",
            "Sarky",
            "Sarmu",
            "Sasay",
            "Sasey",
            "Saunders",
            "Savage",
            "Savula",
            "Saw",
            "Sawanah",
            "Sawaneh",
            "Sawanneh",
            "Sawarray",
            "Sawi",
            "Sawie",
            "Sawo",
            "Sawyah",
            "Sawyer",
            "Sawyerr",
            "Schenka",
            "Scot",
            "Scott",
            "Scott-Boyle",
            "Scott-Manga",
            "Seaay",
            "Seasy",
            "Sebeh",
            "Seeay",
            "Seewald",
            "Sei",
            "Seilenga",
            "Seisay",
            "Seiwoh",
            "Sell",
            "Sellah",
            "Sellu",
            "Senasi",
            "Sendor",
            "Senesie",
            "Senessie",
            "Sengeh",
            "Sengova",
            "Sento",
            "Sepha",
            "Serry",
            "Sesar",
            "Sesay",
            "Sesay-Jalloh",
            "Sesay-Kamara",
            "Sessay",
            "Session",
            "Sessy",
            "Sewor",
            "Seworld",
            "Shaban",
            "Shahbour",
            "Sharie",
            "Sharkha",
            "Sharma",
            "Sharp",
            "Sharpe",
            "Shavay",
            "Shaw",
            "Shears",
            "Shears-Moses",
            "Shebureh",
            "Sheika",
            "Sheku",
            "Shengeh",
            "Shepard",
            "Sherief",
            "Sheriff",
            "Sheriff-Zorokong",
            "Sherriff",
            "Sherry",
            "Shillon",
            "Shonkoh",
            "Show",
            "Showers",
            "Shrief",
            "Shriff",
            "Shyllon",
            "Shylon",
            "Sidi",
            "Sidibay",
            "Sidigba",
            "Sidikie",
            "Sidique",
            "Sidu",
            "Sie",
            "Sifoi",
            "Silla",
            "Sillah",
            "Sim",
            "Simbo",
            "Simon",
            "Simth",
            "Sinah",
            "Sindeh",
            "Singh",
            "Sinnah",
            "Sirfaya",
            "Sivalie",
            "Siwckler",
            "Skaikay",
            "Slow",
            "Slowe",
            "Small",
            "Smalle",
            "Smart",
            "Smartia",
            "Smith",
            "Smoura",
            "Smythe",
            "Sogbandi",
            "Sogie-Thomas",
            "Soja",
            "Soko",
            "Soloku",
            "Solomon",
            "Sombi",
            "Sombo",
            "Sondai",
            "Songa",
            "Songbo",
            "Songo",
            "Songo-Brown",
            "Songowa",
            "Songu-M'briwa",
            "Sonnie",
            "Sonsiama",
            "Sorie",
            "Sorieba",
            "Sorie-Bah",
            "Sorrie",
            "Sourie",
            "Sovola",
            "Sow",
            "Sowa",
            "Sowah",
            "Sowe",
            "Soya",
            "Spain",
            "Spencer",
            "Spido",
            "Spilsbury-Williams",
            "Spring",
            "Square",
            "Squire",
            "Staanley",
            "Stanley",
            "Station",
            "Stephens",
            "Steven",
            "Stevens",
            "Stevin",
            "Stone",
            "Store",
            "Strasser",
            "Streeter",
            "Stronge",
            "Stvens",
            "Sulaiman",
            "Suluku",
            "Suma",
            "Sumah",
            "Sumaila",
            "Sumalai",
            "Sumana",
            "Sumelia",
            "Summah",
            "Summer",
            "Sumura",
            "Sun",
            "Sundu",
            "Sunkari",
            "Suray",
            "Sutton",
            "Swalley",
            "Swaray",
            "Swarray",
            "Swill",
            "Swyer",
            "Sylvalie",
            "Taal",
            "Tabib",
            "Tagoe",
            "Tailor",
            "Tam",
            "Tamba",
            "Tambayor",
            "Tamer",
            "Tamu",
            "Tamukay",
            "Tamukey",
            "Tangar",
            "Taqi",
            "Tarawali",
            "Tarawalia",
            "Tarawalie",
            "Tarawalli",
            "Tarawallie",
            "Tarawally",
            "Tarawaly",
            "Tarawellie",
            "Tarawollie",
            "Tarawulie",
            "Tarawullie",
            "Tarbay",
            "Tasima",
            "Tatta",
            "Tawaralie",
            "Tawarally",
            "Taylor",
            "Taylor-Morgan",
            "Techam",
            "Technologies",
            "Tee",
            "Teh",
            "Tehrawaly",
            "Tejan",
            "Tejan-Cole",
            "Tejancy",
            "Tejansie",
            "Tekuyama",
            "Temple",
            "TENGBE",
            "Tengbeh",
            "Tenneh",
            "Terry",
            "Thamas",
            "Thamb",
            "Thension",
            "Thokuwa",
            "Tholey",
            "Tholley",
            "Thollie",
            "Tholly",
            "Thomas",
            "Thomas-Comba",
            "Thomas-Coomba",
            "Thomos",
            "Thompson",
            "Thomson",
            "Thonkara",
            "Thonpson",
            "Thoranka",
            "Thorankah",
            "Thorley",
            "Thorlie",
            "Thorlu-Bangura",
            "Thoronka",
            "Thoronkah",
            "Thorpe",
            "Thukara",
            "Thula",
            "Thular",
            "Thulla",
            "Thullah",
            "Thullay",
            "Thuray",
            "Tie",
            "Timbo",
            "Timbo-Jalloh",
            "Timity",
            "Tolno",
            "Tolnoh",
            "Tomboyeke",
            "Tommes",
            "Tommy",
            "Tomson",
            "Tomus",
            "Tonkara",
            "Top",
            "Toranka",
            "Toranlla",
            "Torto",
            "Totangi",
            "Touray",
            "Toure",
            "Trawallie",
            "Try",
            "Trye",
            "Tuary",
            "Tuboku-Metzger",
            "Tucker",
            "Tuday",
            "Tuffic",
            "Tugbawa",
            "Tulliver",
            "Tuma",
            "Tumoe",
            "Tunateh",
            "Tunis",
            "Tunkara",
            "Tunkarah",
            "Tunkura",
            "Tunubu",
            "Turay",
            "Turner",
            "Tweed",
            "Ugwucke",
            "Umaru",
            "Unisa",
            "Ureh",
            "Uthman",
            "Uz",
            "Uzondu",
            "Valcarcel",
            "Vamboi",
            "Vandalieda",
            "Vandi",
            "Vandy",
            "Vanger",
            "Velzevoal",
            "Venson",
            "Vicent",
            "Vincent",
            "Vonjoe",
            "Waema",
            "Wagay",
            "Wahid",
            "Wai",
            "Walkei",
            "Walker",
            "Wallace",
            "Wallah",
            "Wallice",
            "Walter",
            "Walterneba",
            "Walter-Neba",
            "Walters",
            "Walton",
            "Wanaeh",
            "Wanna",
            "Ward",
            "Wardlow",
            "Waritay",
            "Warritay",
            "Warshaw",
            "Wast",
            "Watfa",
            "Watifa",
            "Watkins",
            "Wattey",
            "Weath",
            "Weekes",
            "Weeks",
            "Whaheed",
            "Whaleed",
            "Whilem",
            "White",
            "Wicks",
            "Wieliams",
            "Wilhelm",
            "Wilkinson",
            "Will",
            "Willams",
            "Willems",
            "William",
            "Williams",
            "Willie",
            "Willile",
            "Willoughby",
            "Willson",
            "Wilson",
            "Winiba",
            "Winiber",
            "Winner",
            "Winton-Cummings",
            "Wobeh",
            "Woobay",
            "Wood",
            "Woodward",
            "Wray",
            "Wright",
            "Wrobeh",
            "Wunda",
            "Wurie",
            "Wurrie",
            "Wurroh",
            "Wurror",
            "Wyndham",
            "Wyse",
            "Yabom",
            "Yabu",
            "Yagbe",
            "Yajah",
            "Yakoh",
            "Yalie",
            "Yama",
            "Yamba",
            "Yambasu",
            "Yanka",
            "Yankain",
            "Yankson",
            "Yannie",
            "Yansaeh",
            "Yansaneh",
            "Yarjah",
            "Yarmah",
            "Yarteh",
            "Yasaneh",
            "Yaskey",
            "Yatteh",
            "Yayah",
            "Yealie",
            "Yellow",
            "Yewoh",
            "Yibaya",
            "Yilla",
            "Yillah",
            "Yokey",
            "Yokie",
            "Yomba-Bindi",
            "Yombo",
            "Yongai",
            "Yorke",
            "Yorpoi",
            "Young",
            "Yumba",
            "Yumkela",
            "Yunkella",
            "Yus",
            "Yusuf",
            "Zainab",
            "Zaka",
            "Zakaim",
            "Zoker",
            "Zombo",
            "Zorokong",
            "Zorro",
            "Zubairu",
            "Zylbersztain",
            "Abass", "Abass-Bangura", "Abban", "Abdalah", "Abdallah", "Abdul", "Abdullah", "Abdullai", "Abdulrahman", "Aberdeen", 
            "Abess", "Abijoudi", "Aboko-Cole", "Abou", "Aboy", "Abraha", "Abraham", "Abron", "Abu", "Abubakarr", 
            "Academy", "Acheampong", "Achmus", "Adama", "Adams", "Adamsay", "Adaqua", "Adaqueh", "Adejobi", "Adekule", 
            "Adeniran", "Adikalie", "Admire", "Adraman", "Adu", "Aduadjoe", "Adusdjoe", "Afadi", "Affu", "Afful", 
            "Aforo", "Agell", "Aghali", "Agu", "Ahaba", "Ahlvor", "Ahmed", "Aitkins", "Ajami", "Ajax", 
            "Akai", "Akara", "Akibobeth", "Akibo-betts", "Akosua", "Alaba", "Alabi", "Aladdin", "Alammy", "Alao", 
            "Albat", "Aldam", "Alex", "Alhaji", "Ali", "Alie", "Aliesos", "Alieu", "Alieya", "Alima", 
            "Al-Kamara", "Allen", "Allie", "Allieu", "Almond", "Alpha", "Alusine", "Amadi", "Amadu", "Amara", 
            "Amarah", "Aminzason", "Anante", "Anderson", "Annan", "Ansari", "Ansu", "Ansumana", "Antar", "Anthony", 
            "Antony", "Antuah", "Anyaa", "Arab", "Archer", "Arkhust", "Arngel", "Arnold", "Arouni", "Arthur", 
            "Aruna", "Asgill", "Ashely", "Asirifi", "Association", "Atarrah", "Atippo", "Atkins", "Atsakpo", "Attara", 
            "Attiogbe", "Attipoe", "Auab", "Ayuba", "Aziz", "Ba", "Babin", "Babonjo", "Baby", "Badara", 
            "Badin", "Bagura", "Bah", "Bahaguna", "Bah-Kargbo", "Bahkeywar", "Bahsoon", "Bailay", "Bailey", "Bailor", 
            "Baimba", "BAIN", "Bainda", "Bainna", "Baio", "Bakar", "Bakarr", "Bakarr-Conteh", "Baker", "Balater", 
            "Balay", "Balde", "Ballah", "Ballani", "Ballay", "Bamba", "Bambay", "Bamigbaye", "Banata", "Bandagba", 
            "Bandami", "Banda-Thomas", "Bandu", "Bangalie", "Bangra", "Bangrua", "Bangs", "Bangs'Tucker", "Bangura", "Bangura/Kanu", 
            "Bangurah", "Banguray", "Bangure", "Banguta", "Banguura", "Banjo", "Bankoloh", "Bannerman", "Bannett", "Bannister", 
            "Banugra", "Banya", "Bao", "Bar", "Baraka", "Baratay", "Barbah", "Barber", "Bare", "Barie", 
            "Barley", "Barnes", "Barnet", "Barnett", "Baronn", "Barra", "Barrel", "Barrie", "Barrow", "Barry", 
            "Barton", "Baryoh", "Barzey", "Basamba", "Bash", "Basma", "Bassie", "Bassir", "Bassma", "Batema", 
            "Batter", "Battis", "Bawa", "Bawah", "Bawoh", "Bayanka", "Bayinka", "Bayo", "Bayoh", "Bayon", 
            "Bayor", "Beabum", "Beah", "Beccles", "Becker", "Beckles", "Beckley", "Beckley-Thomas", "Beckly", "Becule", 
            "Belewa", "Bell", "Bello", "Belloh", "Belmon", "Ben", "Bendu", "Benga", "Bengah", "Bengali", 
            "Bengeh", "Benjamin", "Bennett", "Benya", "Berewa", "Berewe", "Bernard", "Bertels", "Bertin", "Bets", 
            "Bettey", "Betts", "Betty", "Biango", "Bickerseth", "Bickersteth", "Bidwell", "Bimdi", "Bindi", "Binneh", 
            "Bio", "Bishop", "Black", "Blackie", "Blaize", "Blake", "Blakie", "Blango", "Blessed", "Bliden", 
            "Blyden", "Boayh", "Bob", "Bobareh", "Bobb", "Bobby", "Bob-Williams", "Bockarie", "Bockarie-Konteh", "Bockrie", 
            "Bocum", "Bodin", "Boima", "Boima-Hamid", "Boina", "Bokarie", "Bokon", "Bokrey", "Bolaroh", "Bollaroh", 
            "Bolley", "Bolow", "Bome", "Bona", "Bona-Bayoh", "Bonaparte", "Bonbil", "Bondo", "Bongo", "Bonguloh", 
            "Bonta", "Borboh", "Borbor", "Borlaror", "Born", "Bosco", "Boss", "Boston", "Bowen", "Boyah", 
            "Boyle", "Boymah", "Boyor", "Braima", "Brainaerd", "Brained", "Brainerd", "Brandon", "Brassay", "Breject", 
            "Brewa", "Brewah", "Briama", "Bricks", "Bright", "Brima", "Brima-", "Broki", "Brown", "Browne", 
            "Buck", "Buckle", "Budunka", "Buery", "Bull", "Bull/Children", "Bundor", "Bundu", "Bunduka", "Bungera", 
            "Bunter", "Bureh", "Burney-Nicol", "Butcher", "Buya", "Buyaka", "Buya-Kamara", "Cabralfilme", "Cambo", "Campbell", 
            "Candy", "Cannon", "Cantala", "Capenter", "Carell", "Carew", "Carol", "Caroll", "Carpenter", "Carr", 
            "Carrol", "Carroll", "Carsel", "Carter", "Cassah", "Cater", "Caulia", "Caulker", "Cempbell", "Chails", 
            "Chalie", "Cham", "Chambers", "Chamie", "Chandeh", "Chando", "Chang", "Chapman", "Charles", "Charley", 
            "Charlie", "Chinery-Hesse", "Choga", "Chuku", "Chukuma", "Church", "Cinteh", "Clarke", "Clarkson", "Claye", 
            "Clemens", "Clerkson", "Cleveland", "Clifford-Jarrett", "Cline", "Cline-Cole", "Cline-Smythe", "Cobba", "Cobinah", "Codah", 
            "Coke", "Coker", "Coker-Davies", "Cole", "Collier", "Collingwoode-Williams", "Collins", "Combay", "Commings", "Company", 
            "Complex", "Condeh", "Coneth", "Confort", "Conger-Thompson", "Conision", "Connell", "Contah", "Conteh", "Conteh-Kalawa", 
            "Cooker", "Coomber", "Cooper", "Cornford", "Coulson", "Courtis", "Cowan", "Cowan-Bangura", "Cracrabah", "Crowther", 
            "Cudjoe", "Cummings", "Cummings-Wray", "Curtis", "Cyprain", "Dadeh", "Daffy", "Dago", "Dain", "Dains", 
            "Dala", "Dallo", "Damon", "Dandrew", "Dan-Dewar", "Daniel", "Daniels", "Dankay", "Daramy", "Daro", 
            "Darries", "Darusman", "Dauda", "Dauda-Kamara", "David", "Davidson", "Davies", "Davis", "Daykineh", "De", 
            "Dea", "Deangama", "Debayo", "Dedah", "Dennis", "Deshield", "Deveaux", "Dewey", "Dexter", "Dickens", 
            "Dickson", "Diegbe", "Doh", "Doherty", "Dokie", "Dombaya", "Dombay-Moore", "Dombey", "Dombey-Kargbo", "Domby", 
            "Dondo", "Dongey", "Dongkau", "Donso", "Dontauski", "Dorley", "Douglas", "Dove", "Dovowa", "Dowu", 
            "Dowuona", "Dre", "Duncan", "Dunn", "Durand", "Dusu", "Dwyer", "Dzong", "Eannoh", "Eboma", 
            "Ebo-Sowa", "Ebun", "Eddie", "Eddy", "Edwards", "Edwardson", "Eesay", "Efana", "Efenali", "Egbor", 
            "Egbon", "Ejiofor", "Ekpiwhre", "Eku", "El", "Ellis", "Eltayeb", "Eman", "Emazan", "Embalo", 
            "Embalo-Lowe", "Emmett", "Enokela", "Entoh", "Entri", "Enuwa", "Ernest", "Erskine", "Esan", "Esenwah", 
            "Esho", "Essamuah", "Essau", "Essendoh", "Essig", "Essien", "Essob", "Essuman", "Etornam", "Etuk", 
            "Eva", "Evans", "Eyenshie", "Eyram", "Eysia", "Fabba", "Fadahunsi", "Fadika", "Fadike", "Fagbuara", 
            "Fahina", "Fahm", "Fahmy", "Faika", "Fakayode", "Fallah", "Fambule", "Fanama", "Fanata", "Farida", 
            "Faris", "Farmer", "Farnie", "Farr", "Fatai", "Fatmata", "Fatou", "Fatoumata", "Fawundu", "Fayia", 
            "Fayiah", "Fayon", "Faziah", "Fell", "Ferrao", "Fernandez", "Ferran", "Ferrara", "Ferriera", "Fhemo", 
            "Fibbings", "Fidel", "Filthorpe", "Finney", "Fisher", "Fitzgerald", "Fitzjohn", "Floyd", "Fofana", "Fofanah", 
            "Fo-fona", "Fokam", "Fokams", "Fomba", "Foray", "Foray-Kamara", "Forbes", "Ford", "Forde", "Formeh", 
            "Fortune", "Foster", "Foya", "Fowora", "Fox", "Foye", "Foyoh", "Foyoh-Byron", "Foyoh-Grant", "Francis", 
            "Fraser", "Freckleton", "French", "Frempong", "French", "Freston", "Friday", "Frimpong", "Ftahsia", "Ftshams", 
            "Fudia", "Fulah", "Fullah", "Funes", "Funes-Ribiero", "Furkati", "Furkoto", "Fyn", "Gabiddon", "Gado", 
            "Gandam", "Gangwar", "Garnett", "Garrick", "Garwo", "Gary", "Gassimu", "Gay", "Gayflor", "Gaygflor", 
            "Gebeh", "Gebrilla", "Gebrillah", "George", "Ghaly", "Gibrilla", "Gibrillah", "Gifty", "Gilpin", "Gindiri", 
            "Ginoria", "Gitimoma", "Gittens", "Givens", "Giza", "Gnagbe", "Goayue", "Goba", "Gobow", "Gombeh", 
            "Gomez", "Gomina", "Gonga", "Gonzalez", "Gooding", "Gordon", "Gouray", "Gourley", "Gracia", "Graham", 
            "Grant", "Grant-Gbomoh", "Grant-Gbormoh", "Grant-Gould", "Graves", "Gray", "Green", "Greene", "Gregory", "Griffin", 
            "Griffin-Kargbo", "Gudman", "Gueh", "Guevara", "Guidotti", "Gundor", "Gungor", "Gurley", "Guruza", "Gwambe", 
            "Gwamby", "Gwana", "Gwargwar", "Gweh", "Gyamfi", "Gyening", "Gysin", "Gywa", "Habib", "Haggaard", 
            "Haja", "Hajara", "Haji", "Haji-Ahmed", "Haji-Kella", "Haji-Sesay", "Haji-Williams", "Halibozek", "Hall", "Hallowell", 
            "Hallowell-Smith", "Hamad", "Hamadi", "Hamaji", "Hamilton", "Hamzat", "Hanciles", "Hansen", "Hanson", "Hapoor", 
            "Harb", "Harding", "Harris", "Harry", "Harvey", "Hasan", "Hasana", "Hashim", "Hassan", "Hassim", 
            "Hasson", "Haver", "Haynes", "Hayward", "Hazzan", "Healy", "Heath", "Heaven", "Hector", "Henderson", 
            "Henries", "Henry", "Heridt", "Hill", "Hills", "Hinga", "Hinneh", "Hirsch", "Hodge", "Hodges", 
            "Hofer", "Hoffman", "Holliday", "Holmes", "Holmquist", "Holness", "Holst", "Holt", "Hooper", "Horatio", 
            "Horgan", "Horsfall", "Horst", "Howard", "Howarth", "Howell", "Hudson", "Humphrey", "Hunt", "Hunter", 
            "Husain", "Hussein", "Huyzer", "Hyde", "Ibrahim", "Ibro", "Ibtoye", "Idaghdour", "Idris", "Idrissa", 
            "Ihejirika", "Ijomah", "Ikechi", "Ikeh", "Ikoku", "Ikonneh", "Ilaw", "Illodut", "Imoke", "Ingmar", 
            "Ingred", "Ingram", "Innis", "Ireke", "Iribhogbe", "Ironkwe", "Isaac", "Ishmael", "Ishmail", "Isis", 
            "Ismail", "Ismailla", "Issa", "Iwara", "Iyana", "Iyebo", "Iyengar", "Jabbie", "Jackson", "Jagana", 
            "Jalloh", "Jaloh", "James", "Jammeh", "Jan", "Janus", "Jappah", "Javombo", "Jawara", "Jaye", 
            "Jenkins", "Jesse", "Jimoh", "Jinga", "Jna", "John", "John's", "Johnson", "Jones", "Jordan", 
            "Joseph", "Joshua", "Jusu", "Kabanka",
            "Kabay",
            "Kabba",
            "Kabbah",
            "Kabba-Sei",
            "Kabbay",
            "Kabbba",
            "Kabbia",
            "Kabia",
            "Kabineh",
            "Kabo",
            "Kabu",
            "Kadi",
            "Kadie",
            "Kafoe",
            "Kagbo",
            "Kai",
            "Kaifala",
            "Kaikai",
            "Kailey",
            "Kailie",
            "Kaindaneh",
            "Kaindoh",
            "Kaine",
            "Kainehsie",
            "Kainwa",
            "Kainyanda",
            "Kainyande",
            "Kaisamba",
            "Kaitell",
            "Kaitibi",
            "Kajue",
            "Kakata",
            "Kakay",
            "Kalawa",
            "Kalie",
            "Kalil",
            "Kallay",
            "Kallno",
            "Kalloh",
            "Kallon",
            "Kaloga",
            "Kalohah",
            "Kalokoh",
            "Kamada",
            "Kamanda",
            "Kamara",
            "Kamarah",
            "Kamara-Kay",
            "Kamarakeh",
            "Kamara-Keh",
            "Kamara-Will",
            "Kamason",
            "Kambai",
            "Kambay",
            "Kambo",
            "Kamoh",
            "Kamtuck",
            "Kamuray",
            "Kanagbo",
            "Kanawa",
            "Kanbu",
            "Kande",
            "Kandeh",
            "Kandi",
            "Kanga",
            "Kangaju",
            "Kangbai",
            "Kangbay",
            "Kangoma",
            "Kanjah",
            "Kankay",
            "Kannah",
            "Kannan",
            "Kanneh",
            "Kanta",
            "Kanu",
            "Kanwa",
            "Kanyade",
            "Kapen",
            "Kapindi",
            "Kaprie",
            "Kapu",
            "Kapuwa",
            "Karbgo",
            "Kargbo",
            "Kargbp",
            "Kargobai",
            "Karhbo",
            "Karim",
            "Karimu",
            "Karji",
            "Karoma",
            "Karrow-Kamara",
            "Karta",
            "Karum",
            "Kasheteh",
            "Kaspa",
            "Kassegbama",
            "Kassim",
            "Kassuma",
            "Katta",
            "Kaun",
            "Kawa",
            "Kawan",
            "Kawn",
            "Kay",
            "Kayanda",
            "Kaye",
            "Kebbay",
            "Kebbe",
            "Kebbie",
            "Kefel",
            "Keifa",
            "Keifala",
            "Keikura",
            "Keili",
            "Keister",
            "Keita",
            "Kekuda",
            "Kelfala",
            "Kella",
            "Kellay",
            "Kellenberger",
            "Kellie",
            "Kellon",
            "Kelly",
            "Kembay",
            "Kemoh",
            "Kemokai",
            "Kemokia",
            "Kenagbou",
            "Kendor",
            "Kenneh",
            "Kennie",
            "Kenny",
            "Kenyenyen",
            "Keppah",
            "Keppor",
            "Kessambo",
            "Kessebeh",
            "Kesuma",
            "Khalil",
            "Khan",
            "Khanda",
            "Khanou",
            "Khanu",
            "Khaun",
            "Khouri",
            "Kiawu",
            "King",
            "Kingsley",
            "Kister",
            "Koademba",
            "Kobba",
            "Kobe",
            "Kodamie",
            "Kofie",
            "Koi",
            "Koipoi",
            "Koita",
            "Koker",
            "Kokobay",
            "Kokobaye",
            "Kokofele",
            "Kolifa",
            "Kolleh",
            "Kollie",
            "Komara",
            "Komba",
            "Kombay",
            "Kombe",
            "Komeh",
            "Komohma",
            "Komora",
            "Konda",
            "Kondeh",
            "Kondoh",
            "Kondorvoh",
            "Kondowa",
            "Koneh",
            "Kongaima",
            "Kongo",
            "Konjo",
            "Konnah",
            "Konne",
            "Konneh",
            "Kono",
            "Konoboy",
            "Konomanyi",
            "Konte",
            "Konteh",
            "Kontehmoi",
            "Konuwa",
            "Korama",
            "Korfeh",
            "Korgbo",
            "Korgbow",
            "Korji",
            "Korngor",
            "Koroma",
            "Koromah",
            "Kortequee",
            "Kortu",
            "Kortutay",
            "Koryapoe",
            "Kosia",
            "Kosia-Gande",
            "Kosseh",
            "Koteh",
            "Kouroma",
            "Kowa",
            "Kowateh",
            "Kpaera",
            "Kpagoi",
            "Kpaka",
            "Kpakima",
            "Kpakiwa",
            "Kpakoi",
            "Kpakra",
            "Kpana",
            "Kpange",
            "Kpolewa",
            "Kpolie",
            "Kposowa",
            "Kpukumu",
            "Kpullum",
            "Kpundeh",
            "Krio",
            "Kroma",
            "Kromah",
            "Kuangueh",
            "Kukubeh",
            "Kulabaly",
            "Kulanda",
            "Kumba",
            "Kumbala",
            "Kumbula",
            "Kuminah",
            "Kunateh",
            "Kuneteh",
            "Kungo",
            "Kusha",
            "Kuteh",
            "Kutubu",
            "Kutubu-koroma",
            "Kutubu-Kosia",
            "Kuyateh",
            "Kuyatey",
            "Kuyembeh",
            "Labbie",
            "Labi",
            "Labiyi",
            "Labor",
            "Lacan",
            "Laggah",
            "Laghai",
            "Lahai",
            "Lahei",
            "Lahundeh",
            "Lake",
            "Lakka",
            "Lakoh",
            "Lamboi",
            "Lamin",
            "Lamtey",
            "Landana",
            "Langley",
            "Lansana",
            "Lapia",
            "Lappia",
            "Lardner",
            "Lashite",
            "Lasite",
            "Las-Lamin",
            "Lassayo",
            "Latif",
            "Lavahun",
            "Lavaley",
            "Lavalie",
            "Lavallie",
            "Lavaly",
            "Lavay",
            "Laverly",
            "Lavey",
            "Lawal",
            "Lawalli",
            "Lawn",
            "Lawrence",
            "Lawson",
            "Lawundeh",
            "Lebbia",
            "LEBBIE",
            "Lebby",
            "Ledlum",
            "Lee",
            "Lefevre",
            "Leigh",
            "Lemoh",
            "Lemon",
            "Lengger",
            "Lengon",
            "Lengor",
            "Lenoh",
            "Lenor",
            "Lew",
            "Lewalley",
            "Lewally",
            "Lewis",
            "Lima",
            "Limited",
            "Lisk",
            "Lisk-anani",
            "Liverpool",
            "Lloyd",
            "Logan",
            "Lolliod",
            "Longstreach",
            "Louis",
            "Lua",
            "Lucas",
            "Lucky",
            "Lugbu",
            "Luke",
            "Lukeman",
            "Lukulay",
            "Lungay",
            "Lunsar",
            "Luronica",
            "Lusani",
            "Lusany",
            "Luseni",
            "Lusine",
            "Lymon",
            "Lynch",
            "Macarthy",
            "Macathy",
            "Macaulay",
            "Macauley",
            "Macauly",
            "Macavorey",
            "Macbutcsher",
            "Macco",
            "Macfoi",
            "Macfoy",
            "Mackie",
            "Maclean",
            "Macleane",
            "Macomok",
            "Mactay",
            "Macualey",
            "Macualy",
            "Maculey",
            "Macwright",
            "Maddie",
            "Madi",
            "Madret",
            "Magana",
            "Magbinty",
            "Maggi",
            "Maggo",
            "Magona",
            "Mahdi",
            "Mahmood",
            "Mahmoud",
            "Mahoi",
            "Maiyeli",
            "Maj",
            "Majid",
            "Maju",
            "Makavore",
            "Mali",
            "Malik",
            "Malo",
            "Mamah",
            "Mambu",
            "Mamie",
            "Mammah",
            "Mammie",
            "Mammy",
            "Mamsaray",
            "Mana",
            "Manages",
            "Manah",
            "Manasaray",
            "Manasary",
            "Manbu",
            "Mandela",
            "Mando",
            "Manga",
            "Mangoh",
            "Mangu",
            "Mani",
            "Manley",
            "Manley-Spain",
            "Mannah",
            "Mansaray",
            "Mansarsay",
            "Mansary",
            "Manseray",
            "Mansour",
            "Manu",
            "Manyeh",
            "Manzo",
            "Maoilh",
            "Marah",
            "Marchaty",
            "Margai",
            "Margay",
            "Mariama",
            "Mark",
            "Marna",
            "Marrah",
            "Marthia",
            "Martia",
            "Martin",
            "Martyn",
            "Masalay",
            "Masaqui",
            "Masaray",
            "Maseray",
            "Mason",
            "Massa",
            "Massally",
            "Massaqoui",
            "Massaquoi",
            "Masuba",
            "Mathews",
            "Mattews",
            "Matthews",
            "Mattia",
            "Matturi",
            "Maucualey",
            "Mawendeh",
            "Max-Macarthy",
            "Maxwell",
            "Max-williams",
            "May",
            "Mayah",
            "Mayalli",
            "Mayfield",
            "Mayler",
            "Mbaimba",
            "Mbawah",
            "Mbayo",
            "M'bayo",
            "Mbayoh",
            "Mboma",
            "M'boma",
            "Mbrewa",
            "Mcarthy",
            "McCarthy",
            "Mc-carthy",
            "McEwen",
            "Meheux",
            "Memuna",
            "Memunatu",
            "Mende",
            "Mendia",
            "Mends",
            "Mensah",
            "Meson",
            "Messon",
            "Metzger",
            "Metziger",
            "Mezegar",
            "Mgaujah",
            "Michael",
            "Miller",
            "Milton",
            "Minah",
            "Mirie",
            "Mission",
            "Mmegbuanaeze",
            "Mobis",
            "Modeh",
            "Mohai",
            "Mohamed",
            "Mohammed",
            "Moi",
            "Moiba",
            "Moibe",
            "Moiforay",
            "Moifula",
            "Moigua",
            "Moijueh",
            "Moiwah",
            "Moiwo",
            "Molaro",
            "Molimba",
            "Momodu",
            "Momoh",
            "Momoi",
            "Momorie",
            "Mondeh",
            "Mongorquee",
            "Monibah",
            "Monyamoigwoi",
            "Moody",
            "Moor",
            "Moore",
            "Morfue",
            "Morgan",
            "Morgan-Williams",
            "Moriba",
            "Morie",
            "Mormorie",
            "Mornowa",
            "Morovia",
            "Morowah",
            "Morray",
            "Morrba",
            "Morriba",
            "Morrow",
            "Mosaiay",
            "Mosee",
            "Moseray",
            "Moses",
            "Mousa",
            "Moussa",
            "Moyo",
            "Mpenah",
            "Muana",
            "Mukeh",
            "Mulai",
            "Multi-Kamara",
            "Mumu",
            "Munda",
            "Mundah",
            "Munu",
            "Murray",
            "Musa",
            "Mustada",
            "Mustapha",
            "Myers",
            "Nabay",
            "Nabie",
            "Nabue",
            "Nachs",
            "Nadav",
            "Nahim",
            "Nallah",
            "Nallo",
            "Nanoh",
            "Nao",
            "Nasralla",
            "Nasser",
            "Navo",
            "Nawo",
            "Ndanama",
            "Ndanema",
            "N'danema",
            "Ndoinje",
            "Ndoinjeh",
            "Ndoleh",
            "Ndomaina",
            "Ndulu",
            "Nelson-Okrafor",
            "Neneh",
            "Newellyn",
            "Newland",
            "Newman",
            "Ngaima",
            "Ngaiwa",
            "Ngakui",
            "Ngaliwa",
            "Ngaojia",
            "Ngaujah",
            "Ngayenga",
            "N'Gbark",
            "Ngbegba",
            "Ngebeh",
            "Ngegba",
            "Ngeh",
            "Ngevao",
            "Nghandi"
        ];
    //    echo count($streetNames);
    // echo $properties = LandlordDetail::select('id')->count();
    // die;
        $properties = LandlordDetail::select('id')->get();
    //    die;
        for($i=1;$i<2077;$i++){
            // echo count($streetNames[$i]);
            // echo $properties[$i]->id ;    
            LandlordDetail::where('id', $properties[$i]->id)->update(['surname' => $streetNames[$i]]);
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
