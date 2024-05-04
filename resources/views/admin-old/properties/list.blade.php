@extends('admin.layout.master')

@section('title')
    {{$title}}
@stop

@section('page_title') {{$title}} @stop

@section('content')

{{--    <pre> {{print_r($properties)}}</pre>--}}
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    STRIPED ROWS
                    <small>Use <code>.table-striped</code> to add zebra-striping to any table row within the <code>&lt;tbody&gt;</code></small>
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:void(0);">Action</a></li>
                            <li><a href="javascript:void(0);">Another action</a></li>
                            <li><a href="javascript:void(0);">Something else here</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Landlord Name</th>
                        <th>Section</th>
                        <th>Chiefdom</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>Postcode</th>
                        <th>Meter Number</th>
                        <th>Digital Address</th>
                        <th>Property Type</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($properties as $property)
                    <tr>
                        <th scope="row"><a href="{{url('/property/details')}}"> {{$property->id}}</a></th>
                        <td><a href="{{url('/property/details')}}">{{$property->landlord->first_name}} {{$property->landlord->middle_name}} {{$property->landlord->surname}}</a></td>
                        <td>{{$property->section}}</td>
                        <td>{{$property->chiefdom}}</td>
                        <td>{{$property->district}}</td>
                        <td>{{$property->province}}</td>
                        <td>{{$property->postcode}}</td>
                        <td>{{$property->geoRegistry->meter_number}}</td>
                        <td>{{$property->geoRegistry->digital_address}}</td>
                        <td>{{\App\Models\PropertyAssessmentDetail::find($property->assessment->property_types_id)->propertyType()->select('label')->get()[0]['label']}}</td>
                        <td>{{$property->created_at->format('d/m/Y')}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$properties->links()}}
            </div>
        </div>
    </div>
</div>





@stop
