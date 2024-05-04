@extends('admin.layout.master')

@section('title')
    {{$title}}
@stop

@section('page_title') {{$title}} @stop

@section('content')
    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Landlord Details
                </h2>

            </div>
            <div class="body">
                <div class="row">
                    <div class="col-sm-3">
                        <h6>First Name</h6>
                        <p>{{$properties[0]->landlord->first_name}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Middle Name</h6>
                        <p>{{$properties[0]->landlord->middle_name}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Surname</h6>
                        <p>{{$properties[0]->landlord->surname}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Gender</h6>
                        <p>{{$properties[0]->landlord->sex}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <h6>Street Number</h6>
                        <p>{{$properties[0]->landlord->street_number}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Street Name</h6>
                        <p>{{$properties[0]->landlord->street_name}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Ward</h6>
                        <p>{{$properties[0]->landlord->ward_id}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Constituency</h6>
                        <p>{{$properties[0]->landlord->constituency}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <h6>Section</h6>
                        <p>{{$properties[0]->landlord->section}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Chiefdom</h6>
                        <p>{{$properties[0]->landlord->chiefdom}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>District</h6>
                        <p>{{$properties[0]->landlord->district}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Province</h6>
                        <p>{{$properties[0]->landlord->province}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <h6>Postcode</h6>
                        <p>{{$properties[0]->landlord->postcode}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Mobile Number 1</h6>
                        <p>{{$properties[0]->landlord->mobile_1}}</p>
                    </div>
                    <div class="col-sm-3">
                        <h6>Mobile Number 2</h6>
                        <p>{{$properties[0]->landlord->mobile_2}}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>

    @stop
