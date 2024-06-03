{!! Form::open(['id'=>'assessment-form','route'=>'admin.properties.assessment.save','files' => true]) !!}
{!! Form::hidden('assessment_id',$assessment->id) !!}
{!! Form::hidden('property_id',$property->id) !!}


<h2 class="card-inside-title">Assessment - {{ $assessment->created_at->format('Y') }}

    @php
    $typestotal = array();
    $c_adjusments = array();
    $adjustments = [];
    $council_adjusment_labels = array();
    $council_adjusment_ids = array();

                if($assessment->water_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 1,
                    'label' => 'Water Supply',
                    'percentage' => '3'
                    ]);

                    array_push($council_adjusment_labels,'Water Supply');
                    array_push($council_adjusment_ids,1);
                }
                if($assessment->electricity_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 2,
                    'label' => 'Electricity',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Electricity');
                    array_push($council_adjusment_ids,2);
                }
                if($assessment->waste_management_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 3,
                    'label' => 'Waste Management Services/Points/Locations',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Waste Management Services/Points/Locations');
                    array_push($council_adjusment_ids,3);
                }
                if($assessment->market_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 4,
                    'label' => 'Market',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Market');
                    array_push($council_adjusment_ids,4);
                }
                if($assessment->hazardous_precentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 5,
                    'label' => 'Hazardous Location/Environment',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Hazardous Location/Environment');
                    array_push($council_adjusment_ids,5);
                }
                if($assessment->informal_settlement_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 6,
                    'label' => 'Informal settlement',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Informal settlement');
                    array_push($council_adjusment_ids,6);
                }
                if($assessment->easy_street_access_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 7,
                    'label' => 'Easy Street Access',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Easy Street Access');
                    array_push($council_adjusment_ids,7);
                }
                if($assessment->paved_tarred_street_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 8,
                    'label' => 'Paved/Tarred Road/Street',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Paved/Tarred Road/Street');
                    array_push($council_adjusment_ids,8);
                }
                if($assessment->drainage_percentage != 0 )
                {
                    array_push($adjustments,
                    [ 'id' => 9,
                    'label' => 'Drainage',
                    'percentage' => '3'
                    ]);
                    array_push($council_adjusment_labels,'Drainage');
                    array_push($council_adjusment_ids,9);
                }





        $typestotal = clone $types;
        foreach($typestotal as $key=>$value)
        {
            $typestotal[$key] = '.'.$typestotal[$key] ;
            //echo $typestotal[$key];
            //echo ("key: ". $key ." value: ".$value ."\n");

        }

        $c_adjusments = clone $adjustment_values;
        foreach($c_adjusments as $key=>$value)
        {
            if($c_adjusments[$key] === 'Electricity'){
                $c_adjusments[$key] = $c_adjusments[$key].' '.'Supply' ;
            }else {
                $c_adjusments[$key] = $c_adjusments[$key];
            }

            //echo $typestotal[$key];
            //echo ("key: ". $key ." value: ".$value ."\n");

        }

       // echo $assessment->valuesAdded->pluck('label')->implode(', ');
       // echo $value_added;

    @endphp
    @hasanyrole('Super Admin|Admin|manager')
    {{-- <p>{{ $assessment->sanitation }}</p> --}}
    <div class="pull-right">
        <button type="button" id="assessment-button" class="btn btn-primary">
            Edit
        </button>
        @if($assessment->getBalanceAttribute() < 1)
        <a class="btn btn-default sticker-btn" id="sticker-btn"
            data-content="{{ route('admin.stickers', [$property->id,$assessment->created_at->format('Y')]) }}"
            href="javascript: return false;">Sticker
        </a>
        @endif
        <button style="display: none" type="submit" id="assessment-save" class="btn btn-primary"> Save</button>
        <button style="display: none" type="button" id="assessment-cancel" class="btn btn-primary"> Cancel</button>
    </div>
    @endhasanyrole
</h2>
<p class="text-muted">
    Last Printed: {{ $assessment->isPrinted() ? $assessment->last_printed_at->toDayDateTimeString() : 'Never' }}<br/>

    @php $lastPayment = $property->recentPayment()->whereYear('created_at', $assessment->created_at->format('Y'))->first() @endphp

    Last Payment: {{ $lastPayment ? $lastPayment->created_at->toDayDateTimeString() : 'Never' }} <br/>
</p>

<h4 class="card-inside-title">Demand Note Delivery</h4>
<p>{{ $assessment->isDelivered() ? 'Delivered' : 'Not Delivered' }}</p>

@if($assessment->isDelivered())
    <div class="row">
        <div class="col-sm-3">
            <h6>Recipient Name</h6>
            <p>{{ $assessment->demand_note_recipient_name }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Recipient Contact Number</h6>
            <p>{{ $assessment->demand_note_recipient_mobile }}</p>
        </div>
    </div>

    <a href="{{ $assessment->getRecipientPhoto(600,600) }}" data-sub-html="">
        <img style="max-width: 100px" class="img-responsive thumbnail" src="{{ $assessment->getRecipientPhoto(600,600) }}">
    </a>
@endif

<h4 class="card-inside-title">Assessment Details</h4>

<div class="assessment-view" id="assessment_container_{{ $assessment->created_at->format('Y') }}">
    <div class="row">
        <div class="col-sm-3">
            <h6>Property Category</h6>
            <p>{{ $assessment->categories->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Council District Group</h6>
            <p>{{ $assessment->group_name }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Total No. of Floors</h6>
            <p>{{ $assessment->typesTotal->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Habitable Floors</h6>
            <p>{{ $assessment->types->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Wall Materials</h6>
            <p>{{ optional(App\Models\PropertyWallMaterials::find($assessment->property_wall_materials))->label}}</p>
        </div>
        <div class="col-sm-3">
            <h6>Roofs Materials</h6>
            <p>{{ optional(App\Models\PropertyRoofsMaterials::find($assessment->roofs_materials))->label}}</p>
        </div>
        <div class="col-sm-3">
            <h6>Window Type Materials</h6>
            <p>{{ optional(App\Models\PropertyWindowType::find($assessment->property_window_type))->label}}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Sanitation</h6>
            <p>{{ optional(App\Models\PropertySanitationType::find($assessment->sanitation))->label }}</p>
        </div>
    </div>
    <div class="row">
        {{-- <div class="col-sm-3">
            <h6>Property Dimension</h6>
            <p>{{ optional(App\Models\PropertyDimension::find($assessment->property_dimension))->label}}
                Sq. Meters</p>
        </div> --}}
        <div class="col-sm-3">
            <h6>Property length(Meters)</h6>
             <p id="property_length">{{ $assessment->is_map_set ? "Auto Map Value" : number_format((float)$assessment->length, 2,'.','') ." Meters" }} </p>

        </div>
        <div class="col-sm-3">
            <h6>Property breadth(Meters)</h6>
            <p id="property_breadth">{{ $assessment->is_map_set ? "Auto Map Value" :number_format((float)$assessment->breadth, 2,'.','')  ." Meters" }} </p>
        </div>

        <div class="col-sm-3">
            <h6>Property Dimension(Sq. Meters)</h6>
            <p id="property_dimensions">{{ $assessment->square_meter ? $assessment->square_meter ." Sq. Meters":"" }} </p>

        </div>
        <div class="col-sm-3">
            <h6>Property Total Distance(Meters) (Auto)</h6>
             <p id="property_length_map">{{ $assessment->length ? number_format($assessment->length, 2,'.','')." Meters":"" }} </p>

        </div>
        <div class="col-sm-3">
            <h6>Property Total Area(Meters)(Auto)</h6>
            <p id="property_area_map">{{ $assessment->square_meter ? $assessment->square_meter ."Sq. Meters":"" }} </p>
        </div>
        <div class="col-sm-3">
            <h6>Value added </h6>
            <p>{{ $assessment->valuesAdded->pluck('label')->implode(', ') }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Use</h6>
            <p>{{optional(App\Models\PropertyUse::find($assessment->property_use))->label }}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Zone </h6>
            <p>{{ optional(App\Models\PropertyZones::find($assessment->zone))->label }} </p>
        </div>
    </div>
    <div class="row">
        @if($assessment->no_of_shop!=null)
            <div class="col-sm-3">
                <h6> Number Of Shops</h6>
                <p>{{ $assessment->no_of_shop }} </p>
            </div>
        @endif
        <!-- @if($assessment->no_of_mast!=null)
            <div class="col-sm-3">
                <h6> Number Of Mast</h6>
                <p>{{ $assessment->no_of_mast }} </p>
            </div>
        @endif -->
        @if($assessment->no_of_compound_house!=null)
            <div class="col-sm-3">
                <h6> Number Of Compound House</h6>
                <p>{{ $assessment->no_of_compound_house }} </p>
            </div>
        @endif
        @if($assessment->compound_name!=null)
            <div class="col-sm-3">
                <h6> Compound Name</h6>
                <p>{{ $assessment->compound_name }} </p>
            </div>
        @endif
    </div>
    <div class="row">

        <div class="col-sm-3">
            <h6>Swimming Pool</h6>
            <p> {!! optional(optional($assessment)->swimming)->label !!}</p>
        </div>

        <div class="col-sm-3">
            <h6>Gated Community</h6>
            <p> {{ optional($assessment)->gated_community ? 'Yes' : 'No' }}</p>
        </div>


        <div class="col-sm-3">
            <h6>Property Assessed Value</h6>
            <p>Le {{number_format($assessment->property_rate_without_gst,0,'',',')}}</p>
        </div>
        {{--                        <div class="col-sm-3">--}}
        {{--                            <h6>GST Calculation</h6>--}}
        {{--                            <p>Le {{number_format($assessment->property_gst,0,'',',')}}</p>--}}
        {{--                        </div>--}}
        {{--                        <div class="col-sm-3">--}}
        {{--                            <h6>Property Calculation With GST</h6>--}}
        {{--                            <p>Le {{number_format($assessment->property_rate_with_gst,0,'',',')}}</p>--}}
        {{--                        </div>--}}

        <div class="col-sm-3">
            <h6>Council Adjustments</h6>
            <p>{{implode(', ',$council_adjusment_labels)}}</p>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-3">
            <h6>Net Property Assessed Value</h6>
            <p>Le {{number_format($assessment->getNetPropertyAssessedValue(),0,'',',')}}</p>
        </div>
        <div class="col-sm-3">

            <h6>Taxable Property Value</h6>
            <p>Le {{ number_format($assessment->geTaxablePropertyValue(),0,'',',')}}</p>
        </div>
        <div class="col-sm-3">
            <h6>Mill Rate</h6>
            <p>{!! $assessment->mill_rate>0? $assessment->mill_rate: 0 !!}</p>
        </div>
        <div class="col-sm-3">
            <h6>Property Tax Payable {{ $assessment->created_at->format('Y') }}</h6>
            <p>Le {!! number_format($assessment->getPropertyTaxPayable(),0,'',',') !!}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <h6>Pensioner Discount</h6>
           <p>
              <input type="checkbox" class="pensioner_disc_check" id="pensioner_disc_check_{{$assessment->created_at->format('Y')}}" data-year="{{$assessment->created_at->format('Y')}}" style="position:relative;left: 0px;opacity: 1;" @if($assessment->pensioner_discount == 1) checked @endif />

              <!-- Pensioner -->
            </p>
            <p id="pensioner__discount_{{$assessment->created_at->format('Y')}}" class="discountedamount__{{$assessment->created_at->format('Y')}}">Le {!! $assessment->pensioner_discount ? number_format($assessment->getPensionerDiscount(),0,'',',') : 0 !!}</p>
        </div>
        <div class="col-sm-3">
            <h6>Disability Discount</h6>
            <p><input type="checkbox" class="disability_disc_check" id="disability_disc_check_{{$assessment->created_at->format('Y')}}" data-year="{{$assessment->created_at->format('Y')}}" style="position:relative;left: 0px;opacity: 1;" @if($assessment->disability_discount == 1) checked @endif /></p>
            <p id="disability__discount_{{$assessment->created_at->format('Y')}}" class="discountedamount__{{$assessment->created_at->format('Y')}}">Le {!! $assessment->disability_discount ? number_format($assessment->getDisabilityDiscount(),0,'',',') : 0 !!}</p>
        </div>

        <div class="col-sm-3">
            <h6>Discounted Rate Payable</h6>
            <p id="disability__discount_{{$assessment->created_at->format('Y')}}" class="discountedamount__{{$assessment->created_at->format('Y')}}">Le {!! $assessment->getPensionerDisabilityDiscountActual() ? number_format($assessment->getPensionerDisabilityDiscountActual(),0,'',',') : 0 !!}</p>
        </div>
        <!-- <div class="col-sm-3 discountContainer_{{$assessment->created_at->format('Y')}}" style="display: block;">
            <h6>New Property Tax Payable After Pension and Disability Discount</h6>
            <p id="pensioner_discount_{{$assessment->created_at->format('Y')}}" class="discountedamount_{{$assessment->created_at->format('Y')}}" style="display: block;">Le {!! number_format($assessment->getPensionerDiscount(),0,'',',') !!}</p>
            <p id="disability_discount_{{$assessment->created_at->format('Y')}}" class="discountedamount_{{$assessment->created_at->format('Y')}}" style="display: block;">Le {!! number_format($assessment->getDisabilityDiscount(),0,'',',') !!}</p> -->
            <!-- <p id="pensioner_disability_discount_{{$assessment->created_at->format('Y')}}" class="discountedamount_{{$assessment->created_at->format('Y')}}" style="display: block;">Le {!! number_format($assessment->getPensionerNDisabilityDiscount(),0,'',',') !!}</p>
        </div>                         -->
    </div>

    <h6>Assessment Images</h6>
    <div id="aniimated-thumbnials" class="list-unstyled row clearfix aniimated-thumbnials">
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <a href="{{$assessment->getAdminImageOneUrl(800,800)}}" data-sub-html="">
                <img class="img-responsive thumbnail"
                     src="{{$assessment->getImageOneUrl(100,100)}}">
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <a href="{{$assessment->getAdminImageTwoUrl(800,800)}}" data-sub-html="">
                <img class="img-responsive thumbnail"
                     src="{{$assessment->getImageTwoUrl(100,100)}}">
            </a>
        </div>
    </div>
</div>

<div style="display: none" class="body assessment-edit">
    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-sm-3">
            <h6>Property Category</h6>
            <p>
                {!! Form::select('property_categories[]', $categories, old('property_categories', $assessment->categories()->pluck('id')), ['class' => 'form-control', 'data-live-search' => 'true', 'id' => 'property_categories', 'multiple' => 'multiple']) !!}

            </p>
            @if ($errors->has('property_categories'))
                <label class="error">{{ $errors->first('property_categories') }}</label>
            @endif
        </div>
            <div class="col-sm-3">
                <h6>Total No. of Floors</h6>
                <p>
                    {!! Form::select('property_types_total[]', $typestotal , $assessment->typesTotal()->pluck('id'), ['class' => 'form-control','data-live-search'=>'true','id'=>'property_types_total','multiple']) !!}
                </p>
                @if ($errors->has('property_types_total'))
                    <label class="error">{{ $errors->first('property_types_total') }}</label>
                @endif
            </div>
        <div class="col-sm-3">
            <h6>Habitable Floors</h6>
            <p>
                {!! Form::select('property_types[]', $types , $assessment->types()->pluck('id'), ['class' => 'form-control','data-live-search'=>'true','id'=>'property_types','multiple']) !!}
            </p>
            @if ($errors->has('property_types'))
                <label class="error">{{ $errors->first('property_types') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Wall Materials</h6>
            <p>{!! Form::select('property_wall_materials', $wall_materials , $assessment->property_wall_materials, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_wall_materials'.'_'. $assessment->created_at->format('Y')]) !!}</p>
            @if ($errors->has('property_wall_materials'))
                <label class="error">{{ $errors->first('property_wall_materials') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Window Type</h6>
            <p>{!! Form::select('property_window_type', $window_types , $assessment->property_window_type, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_window_type'.'_'. $assessment->created_at->format('Y')]) !!}</p>
            @if ($errors->has('property_window_type'))
                <label class="error">{{ $errors->first('property_window_type') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Roofs Materials</h6>
            <p>{!! Form::select('roofs_materials', $roofs_materials , $assessment->roofs_materials, ['class' => 'form-control','data-live-search'=>'true','id'=>'roofs_materials'.'_'. $assessment->created_at->format('Y')]) !!}</p>
            @if ($errors->has('roofs_materials'))
                <label class="error">{{ $errors->first('roofs_materials') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Council Adjustments</h6>
            <p>{!! Form::select('property_council_adjustments[]', $c_adjusments , $council_adjusment_ids, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_council_adjustments','multiple']) !!}</p>
            @if ($errors->has('adjustment_values'))
                <label class="error">{{ $errors->first('adjustment_values') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Property Council District Group</h6>
            <p>{!! Form::text('council_group_name', $assessment->group_name,['class' => 'form-control','data-live-search'=>'true','id'=>'property_council_group_name'.'_'. $assessment->created_at->format('Y'),'readonly'=>true]) !!}</p>
            @if ($errors->has('square_meter'))
                <label class="error">{{ $errors->first('council_group_name') }}</label>
            @endif

        </div>
    </div>
    <div class="row">
        {{-- <div class="col-sm-3">
            <h6>Property Dimension(Sq. Meters)</h6>
            <p>{!! Form::select('property_dimension', $property_dimension , number_format($assessment->property_dimension, 2,'.','') , ['class' => 'form-control','data-live-search'=>'true','id'=>'property_dimension']) !!}</p>
            @if ($errors->has('property_dimension'))
                <label class="error">{{ $errors->first('property_dimension') }}</label>
            @endif
        </div> --}}
        <div class="col-sm-3">
            <h6>Property length(Meters)</h6>
            <p>{!! Form::text('length',number_format((float)$assessment->length, 2,'.',''),['class' => 'form-control','data-live-search'=>'true','id'=>'property_length'.'_'. $assessment->created_at->format('Y')]) !!}</p>
            @if ($errors->has('length'))
                <label class="error">{{ $errors->first('length') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Property breadth(Meters)</h6>
            <p>{!! Form::text('breadth',number_format((float)$assessment->breadth, 2,'.',''),['class' => 'form-control','data-live-search'=>'true','id'=>'property_breadth'.'_'. $assessment->created_at->format('Y')]) !!}</p>
            @if ($errors->has('breadth'))
                <label class="error">{{ $errors->first('breadth') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Property Dimension(Sq. Meters)</h6>
            <p>{!! Form::text('square_meter',number_format($assessment->square_meter, 2,'.',''),['class' => 'form-control','data-live-search'=>'true','id'=>'property_square_meter'.'_'. $assessment->created_at->format('Y'),'readonly'=>true]) !!}</p>
            @if ($errors->has('square_meter'))
                <label class="error">{{ $errors->first('square_meter') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>value added</h6>
            <p>
                {{ Form::select('property_value_added[]', $value_added , $assessment->valuesAdded->pluck('id'), ['class' => 'form-control','data-live-search'=>'true','id'=>'property_value_added','multiple']) }}</p>
            @if ($errors->has('property_value_added'))
                <label class="error">{{ $errors->first('property_value_added') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Property Sanitation</h6>
            <p>{!! Form::select('property_sanitation', $sanitation , $assessment->sanitation, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_sanitation'.'_'. $assessment->created_at->format('Y')]) !!}</p>
            @if ($errors->has('property_sanitation'))
                <label class="error">{{ $errors->first('property_sanitation') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Swimming Pool</h6>
            <p>
                {!! Form::select('swimming_pool', $swimmings , $assessment->swimming_id, ['class' => 'form-control','data-live-search'=>'true','id'=>'swimming_pool']) !!}</p>
            @if ($errors->has('swimming_pool'))
                <label class="error">{{ $errors->first('swimming_pool') }}</label>
            @endif
        </div>

        <div class="col-sm-3">
            <h6>Gated Community</h6>
            <p>
                {!! Form::select('gated_community', [1 => 'Yes', 0 => 'No'] , $assessment->gated_community ? 1 : 0, ['class' => 'form-control','data-live-search'=>'true','id'=>'gated_community']) !!}</p>
            @if ($errors->has('gated_community'))
                <label class="error">{{ $errors->first('gated_community') }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <h6>Property Use</h6>
            <p>{!! Form::select('property_use', $property_use , $assessment->property_use, ['class' => 'form-control','data-live-search'=>'true','id'=>'property_use']) !!}</p>
            @if ($errors->has('property_use'))
                <label class="error">{{ $errors->first('property_use') }}</label>
            @endif
        </div>
        <div class="col-sm-3">
            <h6>Property Zone </h6>
            <p>{!! Form::select('zone', $zone , $assessment->zone, ['class' => 'form-control','data-live-search'=>'true','id'=>'zone']) !!} </p>
            @if ($errors->has('zone'))
                <label class="error">{{ $errors->first('zone') }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 {{$assessment->no_of_shop==null?'hidden':''}}"
             id="div_no_of_shop">
            <h6> Number Of Shops</h6>
            <p>{!! Form::text('no_of_shop',$assessment->no_of_shop,['class'=>'form-control','id'=>'no_of_shop']) !!} </p>
            @if ($errors->has('no_of_shop'))
                <label class="error">{{ $errors->first('no_of_shop') }}</label>
            @endif
        </div>

        <!-- <div class="col-sm-3 {{$assessment->no_of_mast==null?'hidden':''}}"
             id="div_no_of_mast">
            <h6> Number Of Mast</h6>
            <p>{!! Form::text('no_of_mast',$assessment->no_of_mast,['class'=>'form-control','id'=>'no_of_mast']) !!} </p>
            @if ($errors->has('no_of_mast'))
                <label class="error">{{ $errors->first('no_of_mast') }}</label>
            @endif
        </div> -->
        <div class="col-sm-3 {{$assessment->no_of_compound_house==null?'hidden':''}}"
             id="div_no_of_compound_house">
            <h6> Number Of Compound House</h6>
            <p>{!! Form::text('no_of_compound_house',$assessment->no_of_compound_house,['class'=>'form-control','id'=>'no_of_compound_house']) !!}</p>
            @if ($errors->has('no_of_compound_house'))
                <label class="error">{{ $errors->first('no_of_compound_house') }}</label>
            @endif
        </div>
        <div class="col-sm-3 {{$assessment->compound_name==null?'hidden':''}}"
             id="div_compound_name">
            <h6> Compound Name</h6>
            <p>{!! Form::text('compound_name',$assessment->compound_name,['class'=>'form-control','id'=>'compound_name']) !!}</p>
            @if ($errors->has('compound_name'))
                <label class="error">{{ $errors->first('compound_name') }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <h6>Property Assessed Value</h6>
            <p class="property_rate_without_gst">Le {{number_format($assessment->property_rate_without_gst,0,'',',')}}</p>
        </div>
        <div class="col-sm-3">
            <h6>Net Property Assessed Value</h6>
            <p class="property_rate_without_gst_council">
                Le {{number_format($assessment->getNetPropertyAssessedValue(),0,'',',')}}</p>

            {!! Form::hidden('property_rate_without_gst',$assessment->property_rate_without_gst) !!}
            {!! Form::hidden('property_rate_with_gst',$assessment->property_rate_with_gst) !!}
            {!! Form::hidden('property_gst',$assessment->property_gst) !!}
            @if ($errors->has('property_rate_without_gst'))
                <label class="error">{{ $errors->first('property_rate_without_gst') }}</label>
            @endif
        </div>
        {{--                            <div class="col-sm-3">--}}
        {{--                                <h6>GST Calculation</h6>--}}
        {{--                                <p class="property_gst">--}}
        {{--                                    Le {{number_format($assessment->property_gst,0,'',',')}}</p>--}}
        {{--                                @if ($errors->has('property_gst'))--}}
        {{--                                    <label class="error">{{ $errors->first('property_gst') }}</label>--}}
        {{--                                @endif--}}
        {{--                            </div>--}}
        {{--                            <div class="col-sm-3">--}}
        {{--                                <h6>Property Calculation With GST</h6>--}}
        {{--                                <p class="property_rate_with_gst">--}}
        {{--                                    Le {{number_format($assessment->property_rate_with_gst,0,'',',')}}</p>--}}
        {{--                                @if ($errors->has('property_rate_with_gst'))--}}
        {{--                                    <label class="error">{{ $errors->first('property_rate_with_gst') }}</label>--}}
        {{--                                @endif--}}
        {{--                            </div>--}}

    </div>
    <div class="row">
        <h6>Assessment Images</h6>
        <div class="col-sm-6">
            @if($assessment->getAdminImageOneUrl(100,100))
            <img src="{{$assessment->getAdminImageOneUrl(100,100)}}"/>
            @endif
            {!! Form::file('assessment_images_1',['class'=>'form-control']) !!}
            @if ($errors->has('assessment_images_1'))
                <label class="error">{{ $errors->first('assessment_images_1') }}</label>
            @endif
            <p>*JPG,JPEG and PNG File Allow Only</p>
        </div>
        <div class="col-sm-6">
            @if($assessment->getAdminImageTwoUrl(100,100))
            <img src="{{$assessment->getAdminImageTwoUrl(100,100)}}"/>
            @endif
            {!! Form::file('assessment_images_2',['class'=>'form-control']) !!}
            @if ($errors->has('assessment_images_2'))
                <label class="error">{{ $errors->first('assessment_images_2') }}</label>
            @endif
            <p>*JPG,JPEG and PNG File Allow Only</p>
        </div>
    </div>
</div>
{!! Form::close() !!}
