@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>
    <style type="text/css">
        #highcharts-data-table-0{ margin-left: 20px; }
        #highcharts-data-table-1{ margin-left: 20px; }
        div.dhxcombo_material {border-bottom: 2px solid #dddddd;}

        .form-group .form-line {border-bottom: 0px solid #ddd;}        
        .bootstrap-select {border-bottom: 2px solid #ddd !important;}
        .form-group .form-line::after {display: none;}
        .input-group .form-line {border-bottom: 2px solid #ddd;}
    </style>
@endpush
@section('content')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="">
                        <h2>
                            Reports
                        </h2>
                    </div>
                </div>

                <div class="body">
                    {!! Form::open(['method' => 'get']) !!}
                    <div class="row row-flex">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>App User Name</label>

{{--                                <div id="name" style="width: 230px;"></div>--}}
                                {{-- {!! Form::text('name', $request->name, ['class' => 'form-control']) !!}--}}
                                {!! Form::select('name', $assessmentUser , $request->name, ['class' => 'form-control','data-live-search'=>'true']) !!}

                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Digital Address</label>

                                <div id="digital_address" style="width: 230px;"></div>
                                {{--{!! Form::text('address', $request->address, ['class' => 'form-control']) !!}--}}
                                {{--{!! Form::select('address', $digital_address , $request->address, ['class' => 'form-control','data-live-search'=>'true']) !!}--}}

                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Is Organization</label>
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
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Postcode</label>

                                <div id="postcode" style="width: 230px;"></div>
                                {{-- {!! Form::text('postcode', $request->postcode, ['class' => 'form-control']) !!}--}}
                                {{--{!! Form::select('postcode', $postcode , $request->postcode, ['class' => 'form-control','data-live-search'=>'true']) !!}--}}

                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Ward</label>
                                <div class="form-line">
                                    {!! Form::select('ward', $ward , $request->ward, ['class' => 'form-control','data-live-search'=>'true']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Section</label>

                                <div id="town" style="width: 230px;">

                                </div>
                                {{--{!! Form::select('town', $town->prepend('Select Town') , $request->town, ['class' => 'form-control','data-live-search'=>'true']) !!}--}}

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
                                <label>Constituency</label>

                                {!! Form::select('constituency', $constituency ,$request->constituency, ['class' => 'form-control','data-live-search'=>'true']) !!}


                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>District</label>
                                <div class="form-line">
                                    {!! Form::select('district', $district ,$request->district, ['class' => 'form-control','data-live-search'=>'true']) !!}
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
                                <label>Meter Number</label>
                                <div class="form-line">
                                    {{--{!! Form::text('meter_number', $request->meter_number, ['class' => 'form-control','data-live-search'=>'true']) !!}--}}
                                    {!! Form::select('meter_number', $meter_number , $request->meter_number, ['class' => 'form-control','data-live-search'=>'true']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Property Types</label>
                                <div class="form-line">
                                    {!! Form::select('type', $types ,$request->type, ['class' => 'form-control','data-live-search'=>'true']) !!}
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
                                <label>Roof Material</label>
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
                                <label>Value Added</label>
                                <div class="form-line">
                                    {!! Form::select('value_added', $valueAdded ,$request->value_added, ['class' => 'form-control','data-live-search'=>'true']) !!}
                                </div>
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
                        {{--<div class="col-sm-3">
                            <div class="form-group">
                                <label>Rented</label>
                                <div class="form-line">
                                    {!! Form::select('rented', ['' => 'Select', 'yes' => 'Yes', 'no' => 'No'] , $request->rented, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Unoccupied</label>
                                <div class="form-line">
                                    {!! Form::select('unoccupied', ['' => 'Select', 'yes' => 'Yes', 'no' => 'No'] , $request->unoccupied, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>--}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Is Completed</label>
                                <div class="form-line">
                                    {!! Form::select('is_completed', ['' => 'All', 'yes' => 'Yes', 'no' => 'No'] , $request->is_completed, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Filter By</label>
                                <div class="form-line">
                                    {!! Form::select('filter_by', \App\Models\Property::filters(), $request->filter_by,['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h2 class="card-inside-title">Start Date And End date</h2>
                            <div class="input-daterange input-group" id="bs_datepicker_range_container">
                                <div class="form-line">
                                    <input type="text" name="from" value="{{$request->from}}"
                                           class="form-control datepicker" placeholder="Date start...">
                                </div>
                                <span class="input-group-addon " style="padding: 0px 15px">to</span>
                                <div class="form-line ">
                                    <input type="text" value="{{$request->to}}" name="to"
                                           class="form-control datepicker" placeholder="Date end...">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Property Inaccessible</label>
                                <div class="form-line">
                                    {!! Form::select('property_inaccessible', $property_inaccessibles ,$request->property_inaccessible, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <input type="submit" value="Submit" class="btn btn-primary" style="width: 100%;">
                        </div>
                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <a href="{{route('admin.report')}}" class="btn btn-primary" style="width: 100%;">Clear
                                Filters</a></div>

                    </div>
                    {{ Form::close() }}
                </div>
            </div>

            @if($assessmentData)

                @php
                    $assessmentTitle = \App\Models\Property::filters($request->filter_by ? $request->filter_by : 'daily') ;
                @endphp

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div id="assessiment_container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div id="monitoring_container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>

            @else

                <div class="text-center">
                    <i class="material-icons" style="font-size: 60px">hourglass_empty</i>
                    <h3>No Records found</h3>
                </div>

            @endif

            @if($assessmentMonthlyData->count())
                <div class="card">
                    <div class="header">
                        <h2>
                            Assessment <small>From {{ \Carbon\Carbon::parse($request->from)->format('Y M, d') }}
                                To {{ \Carbon\Carbon::parse($request->to)->format('Y M, d') }}</small>
                        </h2>
                    </div>
                    <div class="row">
                        <div class="body table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>User Name</th>
                                    <th>Section</th>
                                    <th>Total Assessment</th>
                                    <th>Total Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $total_amount = 0;  @endphp
                                @foreach($assessmentMonthlyData as $amd)
                                    @php $total_amount = $total_amount+$amd->total;  @endphp
                                    <tr>
                                        <td>{{ $amd->year }}</td>
                                        <td>{{$months[$amd->month]}}</td>
                                        <td>{{$amd->user->name}}</td>
                                        <td>{{ $amd->section }}</td>
                                        <td>{{ $amd->count }}</td>
                                        <td>Le {{ number_format($amd->total) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" style="text-align: right;"><b>Grand Total Amount:</b></td>
                                    <td colspan="1"><b>Le {{number_format($total_amount)}}</b></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            @endif

        </div>
    </div>



@endsection

@push('scripts')

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="{{ url('admin/js/dhtmlxcombo.js') }}"></script>

    <script>
        Highcharts.chart('assessiment_container', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Assessment Report'
            },
            subtitle: {
                text: "From {{ \Carbon\Carbon::parse($request->from ? $request->from : $property->created_at)->format('Y M, d') }}\n" +
                    "                                            To {{ \Carbon\Carbon::parse($request->to)->format('Y M, d') }}"
            },
            xAxis: {
                categories: {!! !empty($assessmentData) ? json_encode(array_keys($assessmentData), JSON_NUMERIC_CHECK) : "[]" !!},
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Assessment'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y} </b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Assessment',
                data: {!! !empty($assessmentData) ? json_encode(array_values($assessmentData), JSON_NUMERIC_CHECK) : '[]' !!}

            }]
        });
    </script>


    <script>
        Highcharts.chart('monitoring_container', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monitoring Report'
            },
            subtitle: {
                text: "From {{ \Carbon\Carbon::parse($request->from)->format('Y M, d') }} To {{ \Carbon\Carbon::parse($request->to)->format('Y M, d') }}"
            },
            xAxis: {
                categories: {!! !empty($monitoringData) ? json_encode(array_keys($monitoringData)) : '[]' !!},
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Values'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Values',
                data: {!! !empty($monitoringData) ? json_encode(array_values($monitoringData)) : '[]' !!}

            }]
        });
    </script>


    <script>
        var digital_address, town, postcode, name

        function doOnLoad() {

            digital_address = new dhtmlXCombo({
                parent: "digital_address",
                width: 230,
                filter: "{{route('admin.digitalrep')}}",
                filter_cache: true,
                name: "address"
            });
            town = new dhtmlXCombo({
                parent: "town",
                width: 230,
                filter: "{{route('admin.town')}}",
                filter_cache: true,
                name: "town"
            });

            postcode = new dhtmlXCombo({
                parent: "postcode",
                width: 230,
                filter: "{{route('admin.postcode')}}",
                filter_cache: true,
                name: "postcode"
            });

            // name = new dhtmlXCombo({
            //     parent: "name",
            //     width: 230,
            //     filter: "{{route('admin.name')}}",
            //     filter_cache: true,
            //     name: "name"
            // });

        }

        $(document).ready(function () {
            


            jQuery("body .input-is-organization").on('change', function() {

                if ($(this).val() == 1) {
                    $(this).closest('.form-group').find('#sub-input').show();
                } else {
                    $(this).closest('.form-group').find('#sub-input').hide();
                }
            });

            doOnLoad();

            $("#digital_address").find(".dhxcombo_input").val("{{$request->address}}");
            $("#town").find(".dhxcombo_input").val("{{$request->town}}");
            $("#postcode").find(".dhxcombo_input").val("{{$request->postcode}}");
            //$("#name").find(".dhxcombo_input").val("{{$request->name}}");
            


        });

    </script>


@endpush
