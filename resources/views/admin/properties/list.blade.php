@extends('admin.layout.main')


@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>
    <!-- progress bar (not required, but cool) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css"/>
    <!-- bootstrap (required) -->
    <!-- date picker (required if you need date picker & date range filters) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <!-- grid's css (required) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>

@endpush

@section('content')
    <style type="text/css">
        div.laravel-grid {
            margin-top: 10px !important;
        }
    </style>
    <div class="card">
        <div class="header">
            <h2>
                Filter By
                <small>Properties additional filters</small>
            </h2>
        </div>
        <div class="body">
            {!! Form::open(['method' => 'get', 'id' => 'filter-form']) !!}
            <div class="row row-flex">

                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="form-line">
                            <label>Property ID</label>
                            {!! Form::text('property_id' , $request->property_id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

<!--                 <div class="col-sm-3">
                    <div class="form-group">
                        <label>Old Digital Address</label>

                        <div id="old_digital_address" style="width: 100%; overflow: hidden;"></div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>New Digital Address</label>

                        <div id="digital_address" style="width: 100%; overflow: hidden;"></div>
                    </div>
                </div> -->
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Digital Address</label>

                        <div id="open_location_code" style="width: 100%; overflow: hidden;"></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Owner Fname</label>

                                
                                {!! Form::select('owner_first_name', $first_name, $request->owner_first_name, ['class' => 'form-control','data-live-search'=>'true']) !!}
                            </div>
                        </div>
                       <div class="col-sm-4">
                            <div class="form-group">
                                <label>Owner Mname</label>

                              
                                {!! Form::select('owner_middle_name', $middle_name, $request->owner_middle_name, ['class' => 'form-control','data-live-search'=>'true']) !!}
                            </div>
                        </div> 
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Owner Lname</label>

                               
                                {!! Form::select('owner_last_name', $surname, $request->owner_last_name, ['class' => 'form-control','data-live-search'=>'true']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tenant Fname</label>

                                <div id="tenant_first_name" style="width: 100%; overflow: hidden;"></div>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tenant Mname</label>

                                <div id="tenant_middle_name" style="width: 100%; overflow: hidden;"></div>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Tenant Lname</label>

                                <div id="tenant_last_name" style="width: 100%; overflow: hidden;"></div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Street No.</label>
                        
                            {!! Form::select('street_number', $street_numbers, $request->street_number, ['class' => 'form-control','data-live-search'=>'true']) !!}
                       
                    </div>
                </div>
               

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Street Name</label>
                       
                            {!! Form::select('street_name', $street_names, $request->street_name, ['class' => 'form-control','data-live-search'=>'true']) !!}
                       
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Additional Address</label>
                       
                            {!! Form::select('addition_address', $additional_address, $request->additional_address_id, ['class' => 'form-control','data-live-search'=>'true']) !!}
                       
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Area</label>
                       
                            {!! Form::select('area', $propertyArea, $request->propertyArea, ['class' => 'form-control','data-live-search'=>'true']) !!}
                       
                    </div>
                </div>


                
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Ward</label>
                        {!! Form::select('ward', $ward , $request->ward, ['class' => 'form-control','data-live-search'=>'true']) !!}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Section</label>
                        <div id="town" style="width: 230px;"></div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Constituency</label>
                        {!! Form::select('constituency', $constituency ,$request->constituency, ['class' => 'form-control','data-live-search'=>'true']) !!}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Chiefdom</label>
                        {!! Form::select('chiefdom', $chiefdom ,$request->chiefdom, ['class' => 'form-control','data-live-search'=>'true']) !!}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>District</label>
                        <div class="form-line">
                            {!! Form::select('district', $district,$request->district, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Province</label>
                        <div class="form-line">
                            {!! Form::select('province', $province ,$request->province, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Postcode</label>
                        
                            {!! Form::select('postcode', $postcodes, $request->postcode, ['class' => 'form-control']) !!}
                        
                    </div>
                </div>


                

              
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Occupancy Type</label>
                        <div class="form-line">
                            {!! Form::select('occupancy_type', ['' => 'Select', 'Owned Tenancy' => 'Owned Tenancy', 'Rented House' => 'Rented House','Unoccupied House'=>'Unoccupied House'] , $request->occupancy_type, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                


                
                
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Property Type</label>
                        <div class="form-line">
                            {!! Form::select('type', $types ,$request->type, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Habitable Floors</label>
                        <div class="form-line">
                            {!! Form::select('categories', $categories ,$request->categories, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Wall Material</label>
                        <div class="form-line">
                            {!! Form::select('wall_material', $wallMaterial ,$request->wall_material, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Window Type</label>
                        <div class="form-line">
                            {!! Form::select('window_type', $windowtype ,$request->windowtype, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Roof Type</label>
                        <div class="form-line">
                            {!! Form::select('roof_material', $roofMaterial ,$request->roof_material, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>
                <!-- <div class="col-sm-3">
                    <div class="form-group">
                        <label>Property Dimensions</label>
                        <div class="form-line">
                            {!! Form::select('property_dimension', $propertyDimension ,$request->property_dimension, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div> -->
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Value Added Assessment Parameters</label>
                        <div class="form-line">
                            {!! Form::select('value_added', $valueAdded ,$request->value_added, ['class' => 'form-control','data-live-search'=>'true']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Enumerator</label>
                        <div id="name" style="width: 100%; overflow: hidden;"></div>
                    </div>
                </div>


                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="form-line">
                            <label>Telephone Number</label>
                            {!! Form::text('telephone_number', $request->telephone_number, ['class' => 'form-control ']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Complete - All Data Captured</label>
                        <div class="form-line">
                            {!! Form::select('is_completed', ['' => 'All', 'yes' => 'Yes', 'no' => 'No'] , $request->is_completed, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-3" style="display: none">
                    <div class="form-group">
                        <div class="form-line">
                            <label>Compound Name</label>
                            <div id="compound_name" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>

               

                

                

                {{--  <div class="col-md-3">
                    <label for="">Created </label>
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>Start Date</label>
                                    {!! Form::text('start_date', $request->start_date, ['class' => 'form-control datepicker']) !!}
                                </div>
                                {!! $errors->first('start_date', '<div class="error">:message</div>') !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>End Date</label>
                                    {!! Form::text('end_date', $request->end_date, ['class' => 'form-control datepicker']) !!}
                                </div>
                                {!! $errors->first('end_date', '<div class="error">:message</div>') !!}
                            </div>
                        </div>
                    </div>
                </div>  --}}

                

                {{--  <div class="col-md-3">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>Unpaid Start Date</label>
                                    {!! Form::text('unpaid_start_date', $request->unpaid_start_date, ['class' => 'form-control datepicker']) !!}
                                </div>
                                {!! $errors->first('unpaid_start_date', '<div class="error">:message</div>') !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>Unpaid End Date</label>
                                    {!! Form::text('unpaid_end_date', $request->unpaid_end_date, ['class' => 'form-control datepicker']) !!}
                                </div>
                                {!! $errors->first('unpaid_end_date', '<div class="error">:message</div>') !!}
                            </div>
                        </div>
                    </div>
                </div>  --}}

                {{--  <div class="col-md-3">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>Paid Start Date</label>
                                    {!! Form::text('paid_start_date', $request->paid_start_date, ['class' => 'form-control datepicker']) !!}
                                </div>
                                {!! $errors->first('paid_start_date', '<div class="error">:message</div>') !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>Paid End Date</label>
                                    {!! Form::text('paid_end_date', $request->paid_end_date, ['class' => 'form-control datepicker']) !!}
                                </div>
                                {!! $errors->first('paid_end_date', '<div class="error">:message</div>') !!}
                            </div>
                        </div>
                    </div>
                </div>  --}}

               

                

               

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Demand Note Year</label>
                        <div class="form-line">
                            <select name="demand_draft_year" class="form-control">
                                @for($i = date('Y'); $i >= 2019; $i--)
                                    <option
                                        {{ $i == request('demand_draft_year') ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Printed Demand Notes</label>
                                    <div class="demo-radio-button">
                                        <input name="is_printed"
                                            {{ $request->is_printed ? 'checked' : ''  }}
                                            type="radio" id="is_printed_1" value="1"/>
                                        <label for="is_printed_1" style="min-width: auto">Yes</label>
                                        <input name="is_printed"
                                            {{ ($request->is_printed === "0") ? 'checked' : ''  }}
                                            type="radio" id="is_printed_2" value="0"/>
                                        <label for="is_printed_2" style="min-width: auto">No</label>
                                    </div>
                                </div>
                            </div>
                {{--                <div class="col-sm-3">--}}
                {{--                    <div class="form-group">--}}
                {{--                        <label>Open Location Code</label>--}}
                {{--                        <div id="open_location_code" style="width: 100%;"></div>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Gated Community</label>
                                    <div class="demo-radio-button">
                                        <input name="is_gated_community"
                                            {{ $request->is_gated_community ? 'checked' : ''  }}
                                            type="radio" id="is_gated_community_1" value="1"/>
                                        <label for="is_gated_community_1" style="min-width: auto">Yes</label>
                                        <input name="is_gated_community"
                                            {{ ($request->is_gated_community === "0") ? 'checked' : ''  }}
                                            type="radio" id="is_gated_community_2" value="0"/>
                                        <label for="is_gated_community_2" style="min-width: auto">No</label>
                                    </div>

                                    <div class="gcr {{$request->is_gated_community == 1 ?:"hidden"}}" >
                                        <div class="form-group">
                                            {{-- <label>Gated Community</label> --}}
                                            <div class="form-line">
                                                <select name="gated_community" class="form-control">
                                                    @php
                                                        $minyer = 2018;
                                                        $j=1;
                                                    @endphp
                                                    @for($i = date('Y'); $i >= 2019; $i--)
                                                    @php
                                                        $j = $i - $minyer;
                                                    @endphp
                                                        <option
                                                            {{ $j == request('gated_community') ? 'selected' : '' }} value="{{ $j }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                           
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Delivered Demand Notes</label>
                                    <div class="demo-radio-button">
                                        <input name="is_draft_delivered"
                                            {{ $request->is_draft_delivered ? 'checked' : ''  }}
                                            type="radio" id="is_draft_delivered_1" value="1"/>
                                        <label for="is_draft_delivered_1" style="min-width: auto">Yes</label>
                                        <input name="is_draft_delivered"
                                            {{ ($request->is_draft_delivered === "0") ? 'checked' : ''  }}
                                            type="radio" id="is_draft_delivered_2" value="0"/>
                                        <label for="is_draft_delivered_2" style="min-width: auto">No</label>
                                    </div>
                                </div>

                                <div class="dd {{$request->is_draft_delivered == 1 ?:"hidden"}}">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label>Start Date</label>
                                                    {!! Form::text('dd_start_date', $request->dd_start_date, ['class' => 'form-control datepicker']) !!}
                                                </div>
                                                {!! $errors->first('dd_start_date', '<div class="error">:message</div>') !!}
                                            </div>
                                        </div>
                
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label>End Date</label>
                                                    {!! Form::text('dd_end_date', $request->dd_end_date, ['class' => 'form-control datepicker']) !!}
                                                </div>
                                                {!! $errors->first('dd_end_date', '<div class="error">:message</div>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Date created</label>
                                    <div class="demo-radio-button">
                                        <input name="is_date_created"
                                            {{ $request->is_date_created ? 'checked' : ''  }}
                                            type="radio" id="is_date_created" value="1"/>
                                        <label for="is_date_created" style="min-width: auto">Yes</label>
                                        <input name="is_date_created"
                                            {{ ($request->is_date_created === "0") ? 'checked' : ''  }}
                                            type="radio" id="is_date_created_2" value="0"/>
                                        <label for="is_date_created_2" style="min-width: auto">No</label>
                                    </div>
                                </div>

                                <div class="dc {{$request->is_date_created == 1 ?:"hidden"}}">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label>Start Date</label>
                                                    {!! Form::text('dc_start_date', $request->dc_start_date, ['class' => 'form-control datepicker']) !!}
                                                </div>
                                                {!! $errors->first('dc_start_date', '<div class="error">:message</div>') !!}
                                            </div>
                                        </div>
                
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label>End Date</label>
                                                    {!! Form::text('dc_end_date', $request->dc_end_date, ['class' => 'form-control datepicker']) !!}
                                                </div>
                                                {!! $errors->first('dc_end_date', '<div class="error">:message</div>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Payments</label>
                                    <div class="demo-radio-button">
                                        <input name="paid"
                                               {{ $request->paid == 'paid' ? 'checked' : ''  }}
                                               type="radio" id="is_paid_property_1" value="paid"/>
                                        <label for="is_paid_property_1" style="min-width: auto">Yes</label>
                                        <input name="paid"
                                               {{ ($request->paid === "unpaid") ? 'checked' : ''  }}
                                               type="radio" id="is_paid_property_2" value="unpaid"/>
                                        <label for="is_paid_property_2" style="min-width: auto">No</label>
                                    </div>
                                </div>
                                 <div class="pp {{$request->paid == 'paid' ?:"hidden"}}">
                                     <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label>Start Date</label>
                                                    {!! Form::text('paid_start_date', $request->paid_start_date, ['class' => 'form-control datepicker']) !!}
                                                </div>
                                                {!! $errors->first('paid_start_date', '<div class="error">:message</div>') !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label>End Date</label>
                                                    {!! Form::text('paid_end_date', $request->paid_end_date, ['class' => 'form-control datepicker']) !!}
                                                </div>
                                                {!! $errors->first('paid_end_date', '<div class="error">:message</div>') !!}
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                    <div class="up {{$request->paid == 'unpaid' ?:"hidden"}}">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label>Start Date</label>
                                                        {!! Form::text('unpaid_start_date', $request->unpaid_start_date, ['class' => 'form-control datepicker']) !!}
                                                    </div>
                                                    {!! $errors->first('unpaid_start_date', '<div class="error">:message</div>') !!}
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label>End Date</label>
                                                        {!! Form::text('unpaid_end_date', $request->unpaid_end_date, ['class' => 'form-control datepicker']) !!}
                                                    </div>
                                                    {!! $errors->first('unpaid_end_date', '<div class="error">:message</div>') !!}
                                                </div>
                                            </div>
                                        </div>
                                 </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Inaccessible Properties</label>
                                    <br>
                                    <div class="demo-radio-button" style="display: inline-block;">
                                        <input name="is_accessible"
                                               {{ $request->is_accessible ? 'checked' : ''  }} class="input-inaccessible"
                                               type="radio" id="radio_1" value="1"/>
                                        <label for="radio_1" style="min-width: auto">Yes</label>
                                        <input name="is_accessible"
                                               {{ ($request->is_accessible === "0") ? 'checked' : ''  }}  class="input-inaccessible"
                                               type="radio" id="radio_2" value="0"/>
                                        <label for="radio_2" style="min-width: auto">No</label>
                                    </div>
            
                                    {!! Form::select('property_inaccessible', $property_inaccessibles , $request->property_inaccessible, ['class' => 'form-control dropdown-inaccessible','data-live-search'=>'true']) !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Organization Properties</label>
                                    <br>
                                    <div class="demo-radio-button" style="display: inline-block">
                                        <input name="is_organization"
                                               {{ $request->is_organization == 1 ? 'checked' : ''  }} class="input-is-organization"
                                               type="radio" id="is_organization_yes" value="1"/>
                                        <label for="is_organization_yes" style="min-width: auto">Yes</label>
                                        <input name="is_organization"
                                               {{ ($request->is_organization == "0") ? 'checked' : ''  }}  class="input-is-organization"
                                               type="radio" id="is_organization_no" value="0"/>
                                        <label for="is_organization_no" style="min-width: auto">No</label>
                                    </div>
                                    <div id="sub-input" style="display: {{ $request->is_organization == 1 ? 'block' : 'none' }}">
                                        {!! Form::select('organization_type', $organizationTypes ,$request->organization_type, ['class' => 'form-control', 'data-live-search'=>'true', 'placeholder' => 'Organization Type']) !!}
                                    </div>
                                    <div id="sub-school" style="display: {{ $request->is_organization == 1 ? 'block' : 'none' }}">
                                        {!! Form::select('school_type', $schoolTypes ,$request->school_type, ['class' => 'form-control', 'data-live-search'=>'true', 'placeholder' => 'Select school Type']) !!}
                                    </div>
                                </div>
            
            
                            </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::checkbox('bulk_demand',2, old('bulk_demand'), ['class' => 'filled-in chk-col-blue', 'id' => 'view_map']) !!}
                                <label for="view_map">View on map</label>
                            </div>
                        </div>

<!--                         <div class="col-md-6 dd {{$request->is_draft_delivered == 1 ?:"hidden"}}">

                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label>Delivery Start Date</label>
                                            {!! Form::text('dd_start_date', $request->dd_start_date, ['class' => 'form-control datepicker']) !!}
                                        </div>
                                        {!! $errors->first('dd_start_date', '<div class="error">:message</div>') !!}
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label>Delivery End Date</label>
                                            {!! Form::text('dd_end_date', $request->dd_end_date, ['class' => 'form-control datepicker']) !!}
                                        </div>
                                        {!! $errors->first('dd_end_date', '<div class="error">:message</div>') !!}
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-3 ">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="submit" value="Filter" id="filter-button" class="btn btn-success"
                                           style="width: 100%;">
                                </div>
                                <div class="col-sm-6">
                                    <a href="{{route('admin.properties')}}" class="btn btn-danger" style="width: 100%;">Clear
                                        Filters</a></div>

                            </div>
                        </div>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-3">
                                    <input type="button" value="Download Demand Note" id="download-pdf" style="width: 100%;"
                                           class="btn btn-info"/>
                                </div>
                                <div class="col-sm-3">
                                    <input type="button" value="Download Excel" id="download-excel" style="width: 100%;"
                                           class="btn btn-default"/>
                                </div>
                                <div class="col-sm-3">
                                    <input type="button" value="Download Stickers" id="download-stickers"
                                           style="width: 100%;"
                                           class="btn btn-primary"/>
                                </div>
                                <div class="col-sm-3">
                                    <input type="button" value="Download Notice" id="download-notice"
                                           style="width: 100%; background: #00cc66 !important;"
                                           class="btn btn-primary"/>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="download_pdf_in_bulk" id="download-pdf-in-bulk">
                        <input type="hidden" name="download_excel_in_bulk" id="download-excel-in-bulk">
                        <input type="hidden" name="download_stickers" id="download-sticker-in-bulk">
                        <input type="hidden" name="download_notice" id="download-notice-in-bulk">

                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <form id="download-form" onsubmit="setSelected()" action="{{ route('admin.download.pdf') }}">

        <input type="hidden" id="download-property" name="properties" />

        <div class="row" id="properties">
            <div class="col-md-3">
                <div class="demo-checkbox1">
                    <input type="checkbox" class="filled-in" id="select-all"/>
                    <label for="select-all">Select All</label>
                    <button type="button" class="btn btn-success" id="send-sms" style="margin-left: 8px">Send SMS</button>
                    <input type="hidden" name="send_sms" id="send_sms" value="0">                    
                    <button type="button" class="btn btn-danger pull-right" id="delete-property">Delete</button>
                    <input type="hidden" name="delete_property" id="delete_property" value="0">
                    <!-- <a class="btn-outline-danger">Delete</a> -->
                </div>
                 {!! $errors->first('properties', '<p class="error">:message</p>') !!}
            </div>
            <!-- <div class="col-md-1">
                <button type="submit" name="delete_property" value="1"  class="btn btn-danger pull-right" id="delete-property">Delete</button>
            </div> -->
            <div class="col-md-9">
<!--                 <div class="col-md-1">
                    <button type="button" class="btn btn-success pull-right" id="send-sms">Send SMS</button>
                    <input type="hidden" name="send_sms" id="send_sms" value="0">
                </div> -->
                <div class="col-md-1">
                    <label style="font-size: 12px;"> Year</label>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::select('demand_draft_year', assessmentYearArray(), request('demand_draft_year'), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="download_stickers" value="1"  class="btn btn-primary pull-left" id="stickers-downloads">Download stickers</button>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="download_excel" value="1" class="btn btn-info waves-effect" id="filter-excel">Download Excel</button>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="download_envelope" value="1" class="btn btn-info waves-effect" id="filter-envelope">Download Envelope</button>
                </div>                
                <div class="col-md-3">
                    <button type="submit" name="download_pdf" value="1"  class="btn btn-primary waves-effect pull-right" id="filter-downloads">Download Demand Note (PDF) </button>
                </div>

            </div>
             <div class="col-md-12">
                <p><label style="font-size: 12px;">Total Assessment :</label> Le <span id="total_amount"></span></p>
             </div>
        </div>

    </form>


    <div>
        {!! $grid !!}
    </div>


    <script type="text/javascript">

        

        $(window).on("load", function () {
            var urlHash = window.location.href.split("#")[1];
            if (urlHash != undefined) {
                jQuery('html, body').animate({
                    scrollTop: jQuery('.' + urlHash).offset().top
                }, 4000);
            }

        });

        jQuery("#is_draft_delivered_1").on('click', function () {

            if (jQuery(this).is(':checked')) {
                jQuery(".dd").removeClass('hidden');

            } else {
                jQuery(".dd").addClass('hidden');

            }
        });
        jQuery("#is_date_created").on('click', function () {

            if (jQuery(this).is(':checked')) {
                jQuery(".dc").removeClass('hidden');

            } else {
                jQuery(".dc").addClass('hidden');

            }
        });
        jQuery("#is_date_created_2").on('click', function () {

             jQuery(".dc").addClass('hidden');
        });
        jQuery("#is_draft_delivered_2").on('click', function () {

             jQuery(".dd").addClass('hidden');
        });
      jQuery("#is_paid_property_1").on('click', function () {
    if (jQuery(this).is(':checked')) {
        jQuery(".pp").removeClass('hidden');
        jQuery(".up").addClass('hidden');
    } else {
        jQuery(".pp").addClass('hidden');
    }
});

jQuery("#is_paid_property_2").on('click', function () {
    if (jQuery(this).is(':checked')) {
        jQuery(".up").removeClass('hidden');
        jQuery(".pp").addClass('hidden');
    } else {
        jQuery(".up").addClass('hidden');
    }
});

        jQuery("#is_gated_community_1").on('click', function () {

            if (jQuery(this).is(':checked')) {
                jQuery(".gcr").removeClass('hidden');

            } else {
                jQuery(".gcr").addClass('hidden');

            }
        });
        jQuery("#is_gated_community_2").on('click', function () {

             jQuery(".gcr").addClass('hidden');
        });

        jQuery("#delete-property").on('click', function () {
          // if (confirm("Are you sure want to delete propertie(s)?")) {
          //   $('#download-form').submit();
          // }
          //   url = jQuery(this).attr('href');
            swal({
                    title: "Are you sure?",
                    text: "Do you want to delete selected properties!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete!",
                    closeOnConfirm: false,
                    confirmButtonColor: '#fb483a',
                },
                function () {
                    $("#delete_property").val(1);
                    $('#download-form').submit();
                });
            return false;

        });

        function setSelected() {

            var selections = [];

            jQuery(".filtered-property").each(function (key, value) {
                if (jQuery(this).prop('checked') == true) {
                    selections.push($(this).val());
                }
            });

            jQuery("#download-property").val(selections);
        }

        jQuery(document).ready(function () {

            jQuery("#demand_draft_year_id").on('change', function () {
                var val = jQuery(this).val();
                jQuery("#demand_draft_year").val(val);
            });
            @hasanyrole('supervisor')
            jQuery(".btn-outline-danger").hide();
            @endhasanyrole
        });


        var digital_address, town, owner_first_name, compound_name, old_digital_address, open_location_code, tenant_first_name, tenant_last_name, tenant_middle_name;

        function doOnLoad() {

            // digital_address = new dhtmlXCombo({
            //     parent: "digital_address",
            //     width: 600,
            //     filter: "{{route('admin.digital')}}",
            //     filter_cache: true,
            //     name: "digital_address"
            // });

            // old_digital_address = new dhtmlXCombo({
            //     parent: "old_digital_address",
            //     width: 600,
            //     filter: "{{route('admin.olddigital')}}",
            //     filter_cache: true,
            //     name: "old_digital_address"
            // });

            open_location_code = new dhtmlXCombo({
                parent: "open_location_code",
                width: 600,
                filter: "{{route('admin.openlocationcode')}}",
                filter_cache: true,
                name: "open_location_code"
            });

            compound_name = new dhtmlXCombo({
                parent: "compound_name",
                width: 230,
                filter: "{{route('admin.compound.name')}}",
                filter_cache: true,
                name: "compound_name"
            });
            town = new dhtmlXCombo({
                parent: "town",
                width: 230,
                filter: "{{route('admin.town')}}",
                filter_cache: true,
                name: "town"
            });

            name = new dhtmlXCombo({
                parent: "name",
                width: 230,
                filter: "{{route('admin.name')}}",
                filter_cache: true,
                name: "name"
            });

         

           

           

            tenant_first_name = new dhtmlXCombo({
                parent: "tenant_first_name",
                width: 230,
                filter: "{{route('admin.tenant-first-name')}}",
                filter_cache: true,
                name: "tenant_first_name"
            });

            tenant_last_name = new dhtmlXCombo({
                parent: "tenant_last_name",
                width: 230,
                filter: "{{route('admin.tenant-last-name')}}",
                filter_cache: true,
                name: "tenant_last_name"
            });

            tenant_middle_name = new dhtmlXCombo({
                parent: "tenant_middle_name",
                width: 230,
                filter: "{{route('admin.tenant-middle-name')}}",
                filter_cache: true,
                name: "tenant_middle_name"
            });
        }

        $(document).ready(function () {
            doOnLoad();

            // $("#digital_address").find(".dhxcombo_input").val("{{$request->digital_address}}");
            // $("#old_digital_address").find(".dhxcombo_input").val("{{$request->old_digital_address}}");
            $("#open_location_code").find(".dhxcombo_input").val("{{$request->open_location_code}}");
            $("#compound_name").find(".dhxcombo_input").val("{{$request->compound_name}}");
            $("#town").find(".dhxcombo_input").val("{{$request->town}}");
            $("#owner_first_name").find(".dhxcombo_input").val("{{$request->owner_first_name}}");
            $("#owner_last_name").find(".dhxcombo_input").val("{{$request->owner_last_name}}");
            //$("#owner_middle_name").find(".dhxcombo_input").val("{{$request->owner_middle_name}}");
            $("#tenant_first_name").find(".dhxcombo_input").val("{{$request->tenant_first_name}}");
            $("#tenant_last_name").find(".dhxcombo_input").val("{{$request->tenant_last_name}}");
            $("#tenant_middle_name").find(".dhxcombo_input").val("{{$request->tenant_middle_name}}");
            $(".filter-header").next('tr').hide();

            jQuery("#select-all").on('click', function () {
                if (jQuery(this).prop("checked") == true) {
                    jQuery(".filtered-property").prop('checked', true);
                } else {
                    jQuery(".filtered-property").prop('checked', false);
                }
            });

            jQuery(".filtered-property").click(function () {
                jQuery(".filtered-property").each(function (key, value) {
                    if (jQuery(this).prop('checked') == false) {
                        jQuery("#select-all").prop('checked', false);
                    }
                });
            });

        });
    </script>

    <script>

        jQuery("#download-pdf").on('click', function () {
            jQuery("#download-pdf-in-bulk").val(1);
            jQuery("#download-excel-in-bulk").val('');
            jQuery("#download-sticker-in-bulk").val('');
            jQuery("#download-notice-in-bulk").val('');
            jQuery("#filter-form").submit();
        });

        jQuery("#download-stickers").on('click', function () {
            jQuery("#download-sticker-in-bulk").val(1);
            jQuery("#download-excel-in-bulk").val('');
            jQuery("#download-pdf-in-bulk").val('');
            jQuery("#download-notice-in-bulk").val('');
            jQuery("#filter-form").submit();
        });

        jQuery("#download-excel").on('click', function () {
            jQuery("#download-excel-in-bulk").val(1);
            jQuery("#download-pdf-in-bulk").val('');
            jQuery("#download-sticker-in-bulk").val('');
            jQuery("#download-notice-in-bulk").val('');
            jQuery("#filter-form").submit();
        });

        jQuery("#filter-button").on('click', function () {
            jQuery("#download-pdf-in-bulk").val('');
            jQuery("#download-excel-in-bulk").val('');
            jQuery("#download-sticker-in-bulk").val('');
            jQuery("#download-notice-in-bulk").val('');
        });

        jQuery("#download-notice").on('click', function () {
            jQuery("#download-notice-in-bulk").val(1);
            jQuery("#download-sticker-in-bulk").val('');
            jQuery("#download-excel-in-bulk").val('');
            jQuery("#download-pdf-in-bulk").val('');
            jQuery("#filter-form").submit();
        });

        jQuery("a.btn-outline-danger").on('click', function () {

            url = jQuery(this).attr('href');
            swal({
                    title: "Are you sure?",
                    text: "Do you want to delete this property!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function () {
                    window.location.href = url;
                });
            return false;
        });

        jQuery("#send-sms").on('click', function () {
          // if (confirm("Are you sure want to delete propertie(s)?")) {
          //   $('#download-form').submit();
          // }
          //   url = jQuery(this).attr('href');
            swal({
                    title: "Are you sure?",
                    text: "Do you want to send SMS to this selected landlords!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, send!",
                    closeOnConfirm: false,
                    confirmButtonColor: '#fb483a',
                },
                function () {
                    $("#send_sms").val(1);
                    $('#download-form').submit();
                });
            return false;

        });
    </script>





@stop

@section('scripts')

    <script>
        jQuery(document).ready(function () {
            var isChecked = jQuery("input[name='is_accessible']:checked").val();

            if (isChecked == 1) {
                jQuery(".dropdown-inaccessible").show();
            } else {
                jQuery(".dropdown-inaccessible").hide();
            }

        });
    </script>



    <script>
        // send csrf token (see https://laravel.com/docs/5.6/csrf#csrf-x-csrf-token) - this is required
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // for the progress bar (required for progress bar functionality)
        $(document).on('pjax:start', function () {
            NProgress.start();
        });
        $(document).on('pjax:end', function () {
            NProgress.done();
        });
    </script>
    <script src="{{ url('admin/js/pages/forms/advanced-form-elements.js') }}"></script>
    <script src="{{ url('admin/js/dhtmlxcombo.js') }}"></script>

    <script>
        jQuery(".input-inaccessible").on('change', function () {
            val = jQuery(this).val();
            if (val == 0) {
                jQuery(".dropdown-inaccessible").hide();
            } else {
                jQuery(".dropdown-inaccessible").show();
            }
        });

        jQuery("body .input-is-organization").on('change', function () {
            
            if ($(this).val() == 1) {
                $(this).closest('.form-group').find('#sub-input').show();
            } else {
                $(this).closest('.form-group').find('#sub-input').hide();
                $(this).closest('.form-group').find('#sub-school').hide();
            }
           
        });
        jQuery("#sub-input").on('change', function() {
           // alert("sdfsd");
          //  console.log('text: ', $('#sub-input .selected a .text').text());
            if ($('#sub-input .selected a .text').text() === 'School') {
                jQuery(this).closest('.form-group').find('#sub-school').show();
            } else {
                jQuery(this).closest('.form-group').find('#sub-school').hide();
            }
        });
        totalAssessment();

        function totalAssessment()
        {
            var str=0;
            var table = $('table tbody tr');

            for(var row=0; row<table.length; row++)
            {
                str=str+Number(table[row].cells[8].innerText.replace("Le", "").split(",").join(""));
                            // if(document.all)
                            //     str=str+Number(table.rows[row].cells[8].innerText);

                            // else
                            //     str=str+Number(table.rows[row].cells[8].textContent);

            }

            $("#total_amount").text((str.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")));
            return false;
        }
    </script>

    <style>
        .pagination {
            margin: 0px;
        }

        .pagination ul {
            margin-bottom: 0px;
        }

    </style>

@stop
