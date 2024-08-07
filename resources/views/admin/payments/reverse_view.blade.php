@extends('admin.layout.main')
@push('stylesheets')
    {{--    <link href="{{ url('admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet"/>--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>
@endpush

@section('content')
    <style type="text/css">
        .zoom:hover {
  transform: scale(6.5); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
}

.zoom {
  padding: 10px;
  transition: transform .2s; /* Animation */
  width: 300px;
  height: 200px;
  margin: 0 auto;
}

.zoom_receipt  {
    padding: 10px;
  transition: transform .2s; /* Animation */
  margin: 0 auto;
}


.zoom_receipt:hover {
  transform: scale(6.5); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
}


        </style>

    @include('admin.layout.partial.alert')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-green">
                    <h2>
                        Search Property of reverse payment
                    </h2>
                </div>
                <div class="body">
                    {!! Form::open(['novalidate' => 'novalidate', 'id' => 'search_payment', 'method' => 'get']) !!}
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>Property ID</label>
                                    {!! Form::text('property_id' , request('property_id'), ['class' => 'form-control', 'autocomplete' => 'off']) !!}
                                </div>
                            </div>
                        </div>
                        {{--  <div class="col-sm-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>Old Digital Address</label>
                                    <div id="old_digital_address" class="form-control"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label>New Digital Address</label>
                                    <div id="digital_address" class="form-control"></div>
                                </div>
                            </div>
                        </div>  --}}
                        <div class="col-sm-1">
                            <button type="submit" class="btn btn-primary m-t-15 waves-effect">Search</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>










    @if(!empty($property))

    <!-- && ($property->assessment->pensioner_discount == 0) -->
    @if((!empty($property->payments[$property->payments->count() -1]->disability_discount_image_path) || !empty($property->payments[$property->payments->count() -1]->pensioner_discount_image_path) )  )
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header bg-orange">
                                <h2>
                                    Pensioner Discount | Disability Discount
                                </h2>
                            </div>
                            <div class="body"  style=" overflow-x: scroll; ">
                                <table class="table">
                                    <thead>
                                    <th>Disability Image Proof</th>
                                    <th>Pensioner Image Proof</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                            @if(!empty($property->payments[$property->payments->count() -1]->disability_discount_image_path) && !($property->assessment->is_rejected_disability)  )
                                                <img class="zoom" src="{{ !empty($property->payments[$property->payments->count() -1]->disability_discount_image_path) ? $property->payments[$property->payments->count() -1]->disability_discount_image_path : '---' }}" alt="" width="200" height="100">
                                            @endif
                                            </td>


                                            <td>
                                            @if(!empty($property->payments[$property->payments->count() -1]->pensioner_discount_image_path) && !($property->assessment->is_rejected_pensioner))
                                                <img style="z-index:100;" class="zoom" src="{{ !empty($property->payments[$property->payments->count() -1]->pensioner_discount_image_path) ? $property->payments[$property->payments->count() -1]->pensioner_discount_image_path : '---' }}" alt="" width="200" height="100">
                                            @endif
                                            </td>

                                            <tr>


                                                <td>
                                                @if(!empty($property->payments[$property->payments->count() -1]->disability_discount_image_path) && !($property->assessment->disability_discount) && !($property->assessment->is_rejected_disability) )
                                                    <a href="{{ route('admin.disability.approve', $property->id) }}" class="btn btn-large btn-success">Approve</a>
                                                    <a href="{{ route('admin.disability.reject', $property->id) }}" class="btn btn-large btn-danger">Reject</a>
                                                @endif
                                                </td>



                                                <td>
                                                @if(!empty($property->payments[$property->payments->count() -1]->pensioner_discount_image_path) && !($property->assessment->pensioner_discount) && !($property->assessment->is_rejected_pensioner) )
                                                    <a href="{{ route('admin.pensioner.approve', $property->id) }}" class="btn btn-large btn-success">Approve</a>
                                                    <a href="{{ route('admin.pensioner.reject', $property->id) }}" class="btn btn-large btn-danger">Reject</a>
                                                @endif
                                                </td>


                                            </tr>


                                        </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

            </div>
            @endif




        @if(!empty($property->getAttributes()))
           


                @if($property->payments()->count())

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header bg-red">
            <h2>
               Reversed Payments
            </h2>
        </div>
        <div class="body"  style=" overflow-x: scroll; ">
            <table class="table">
                <thead>
                <th>Property ID</th>
                <th>Transaction ID</th>
                <th>Payment Made</th>
                <th>Cashier Name</th>
                <th>Amount Due</th>
                {{-- <th>Paying Amount</th>
                <th>Penalty</th> --}}
                <th>Amount Paid</th>
                <th>Remaining Balance</th>
                <th>Payment Type</th>
                <!-- <th>Cheque Number</th> -->
                <th>Payee Name</th>
                <th>Transaction Date</th>
                <th>Receipt</th>
                @hasanyrole('Super Admin|Admin')
                <th>View</th>
                <!-- <th>Reverse Payment</th> -->
                @endhasanyrole
                </thead>
                <tbody>
                @foreach($property->payments()->where('reverse', '1')->latest()->get() as $payment)
                    <tr>
                        <td>{{ $payment->property_id }}</td>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->payment_made_year == null ? '---' :  $payment->payment_made_year }}</td>
                        <td>{{ $payment->admin->getName() }}</td>
                        <td>{{ number_format($payment->assessment) }}</td>
                        {{-- <td>{{ number_format($payment->amount) }}</td>
                        <td>{{ number_format($payment->penalty) }}</td> --}}
                        <td>{{ number_format($payment->total) }}</td>
                        <td>{{ number_format($payment->balance < 0 ? 0 : $payment->balance) }}</td>
                        <td>{{ ucwords($payment->payment_type) }}</td>
                        <!-- <td>{{ $payment->cheque_number }}</td> -->
                        <td>{{ $payment->payee_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                        <td>
                            @if(!empty($payment->physical_receipt_image_path))
                           <img class="zoom_receipt" src="{{ $payment->physical_receipt_image_path }}" alt="" width="80" height="60">
                           @else
                           <p>No Receipt Uploaded</p>
                           @endif
                        </td>
                        @hasanyrole('Super Admin|Admin')
                        <td>
                        <!-- <a class=""
                               href="{{ route('admin.payment.verify', $payment->id) }}"><i
                                    style="font-size: 25px;" class="material-icons">fact_check</i>
                            </a> -->

                            <a class=""
                               href="{{ route('admin.payment.edit', $payment->id) }}"><i
                                    style="font-size: 25px;" class="material-icons">visibility</i>
                            </a>
                               <!-- <a href="{{ route('admin.payment.delete', $payment->id) }}"
                                              id="delete-payment">
                                              <i style="font-size: 25px;"
                                                                     class="material-icons">delete</i></a> -->

                           </td>

                           @endhasanyrole
                        {{--<th>
                            <a class="btn btn-primary btn-xs"
                               href="{{ route('admin.payment.edit', $payment->id) }}"><i
                                    style="font-size: 14px;" class="material-icons">colorize</i>Edit
                            </a> &nbsp;&nbsp;
                            <a class="btn btn-danger btn-xs"
                               href="{{ route('admin.payment.delete', $payment->id) }}"
                               id="delete-payment"><i style="font-size: 14px;"
                                                      class="material-icons">delete</i>Delete</a>
                            <a style="margin-left: 10px;" class="btn btn-success btn-xs"
                               href="{{ route('admin.payment.pos.receipt', ['id' => $property->id, 'payment_id' => $payment->id]) }}"><i
                                    style="font-size: 14px;"
                                    class="material-icons">print</i>Print</a>
                        </th>--}}
                            <!-- <td>
                                <a href="{{route('admin.reverse', $payment->id)}}" class="btn btn-primary m-t-15 waves-effect btn-lg">Reverse Payment</a>
                            </td> -->
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif

                @if($property->assessmentHistory->count())

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header bg-orange">
                                <h2>
                                    Assessment History
                                </h2>
                            </div>
                            <div class="body"  style=" overflow-x: scroll; ">
                                <table class="table">
                                    <thead>
                                    <th>Assessment Year</th>
                                    <th>Assessment amount</th>
                                    <th>Arrears</th>
                                    <th>Penalty</th>
                                    <th>Amount Paid</th>
                                    <th>Due</th>
                                    </thead>
                                    <tbody>
                                    @foreach($property->assessmentHistory as $assessmentHistory)
                                        <tr>
                                            <td>{{ $assessmentHistory->created_at->format('Y') }}</td>
                                            <td>{{ number_format($assessmentHistory->getCurrentYearAssessmentAmount()) }}</td>
                                            <td>{{ number_format($assessmentHistory->getPastPayableDue()) }}</td>
                                            <td>{{ number_format($assessmentHistory->getPenalty()) }}</td>
                                            <td>{{ number_format($assessmentHistory->getCurrentYearTotalPayment()) }}</td>
                                            <td>{{ number_format($assessmentHistory->getCurrentYearTotalDue()) }}</td>
                                        </tr>
                                    @endforeach
                                    {{-- <tr>
                                        <td>{{ \Carbon\Carbon::parse($property->assessment->created_at)->format('Y') }}</td>
                                        <td>{{ number_format($property->assessment->getCurrentYearAssessmentAmount()) }}</td>
                                        <td>{{ number_format($property->assessment->getPastPayableDue()) }}</td>
                                        <td>{{ number_format($property->assessment->getPenalty()) }}</td>
                                        <td>{{ number_format($property->assessment->getCurrentYearTotalPayment()) }}</td>
                                        <td>{{ number_format($property->assessment->getCurrentYearTotalDue()) }}</td>
                                    </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                @endif




            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header bg-cyan">
                            <h2>
                                Landlord Details
                            </h2>

                        </div>
                        <div class="body">
                            <div class="row">
                                @if($property->is_organization==0)
                                    <div class="col-sm-3">
                                        <h6>First Name</h6>
                                        <p>{{$property->landlord->first_name}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Middle Name</h6>
                                        <p>{{$property->landlord->middle_name}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Surname</h6>
                                        <p>{{$property->landlord->surname}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Gender</h6>
                                        <p>{{$property->landlord->sex}}</p>
                                    </div>
                                @elseif($property->is_organization==1)
                                    <div class="col-sm-3">
                                        <h6>Organization Name</h6>
                                        <p>{{$property->organization_name}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Organization Type</h6>
                                        <p>{{$property->organization_type}}</p>
                                    </div>
                                    {{-- <div class="col-sm-3">
                                        <h6>Organization Tin Number</h6>
                                        <p>{{$property->organization_tin}}</p>
                                    </div> --}}
                                    <div class="col-sm-3">
                                        <h6>Organization Address</h6>
                                        <p>{{$property->organization_addresss}}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Street Number</h6>
                                    <p>{{$property->landlord->street_number}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Street Name</h6>
                                    <p>{{$property->landlord->street_name}}</p>
                                </div>
                                @if($property->is_organization==0)
                                    {{-- <div class="col-sm-3">
                                        <h6>Tin Number</h6>
                                        <p>{{$property->landlord->tin}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Id Type</h6>
                                        <p>{{$property->landlord->id_type}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Id Number</h6>
                                        <p>{{$property->landlord->id_number}}</p>
                                    </div> --}}
                                    {{--  <div class="col-sm-3">
                                        <div id="aniimated-thumbnials"
                                             class="list-unstyled row clearfix aniimated-thumbnials">
                                            <h6>Image</h6>
                                            <a href="{{$property->landlord->getImageUrl(800,800)}}" data-sub-html="">
                                                <img class="img-responsive thumbnail"
                                                     src="{{$property->landlord->getImageUrl(100,100)}}">
                                            </a>
                                        </div>
                                    </div>  --}}
                                @endif
                                <div class="col-sm-3">
                                    <h6>Ward</h6>
                                    <p>{{$property->landlord->ward}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Constituency</h6>
                                    <p>{{$property->landlord->constituency}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Section</h6>
                                    <p>{{$property->landlord->section}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Chiefdom</h6>
                                    <p>{{$property->landlord->chiefdom}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>District</h6>
                                    <p>{{$property->landlord->district}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Province</h6>
                                    <p>{{$property->landlord->province}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Postcode</h6>
                                    <p>{{$property->landlord->postcode}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Mobile Number 1</h6>
                                    <p>{{$property->landlord->mobile_1}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Mobile Number 2</h6>
                                    <p>{{$property->landlord->mobile_2}}</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header bg-cyan">
                            <h2>
                                Property Details
                            </h2>

                        </div>
                        <div class="body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Street Number</h6>
                                    <p>{{$property->street_number}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Street Name</h6>
                                    <p>{{$property->street_name}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Ward</h6>
                                    <p>{{$property->ward}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Constituency</h6>
                                    <p>{{$property->constituency}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Section</h6>
                                    <p>{{$property->section}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Chiefdom</h6>
                                    <p>{{$property->chiefdom}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>District</h6>
                                    <p>{{$property->district}}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Province</h6>
                                    <p>{{$property->province}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6>Postcode</h6>
                                    <p>{{$property->postcode}}</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($property->occupancy)
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header bg-cyan">
                                <h2>
                                    Occupancy Details
                                </h2>

                            </div>
                            <div class="body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6>Occupancy Type</h6>
                                        <p>{{$property->occupancies->pluck('occupancy_type')->implode(', ')}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Tenant First Name</h6>
                                        <p>{{$property->occupancy->tenant_first_name}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6>Middle Name</h6>
                                        <p>{{$property->occupancy->middle_name}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Surname</h6>
                                        <p>{{$property->occupancy->surname}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Mobile Number 1</h6>
                                        <p>{{$property->occupancy->mobile_1}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Mobile Number 2</h6>
                                        <p>{{$property->occupancy->mobile_2}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if($property->assessment)
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header bg-cyan">
                                <h2>
                                    Assessment Details
                                </h2>

                            </div>
                            <div class="body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6>property Category</h6>
                                        {{ $property->assessment->categories->pluck('label')->implode(', ') }}
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Property Types</h6>
                                        <p>
                                            @foreach($property->assessment->types as $type)
                                                {{$type->label}}
                                                @if(!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Wall Materials</h6>
                                        <p>{{ optional(App\Models\PropertyWallMaterials::find($property->assessment->property_wall_materials))->label}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Roofs Materials</h6>
                                        <p>{{ optional(App\Models\PropertyRoofsMaterials::find($property->assessment->roofs_materials))->label}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6>Property Dimension</h6>
                                        <p>{{ optional(App\Models\PropertyDimension::find($property->assessment->property_dimension))->label}}
                                            Sq Ft.</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Value Added</h6>
                                        <p>
                                            @foreach($property->assessment->valuesAdded as $vd)
                                                {{$vd->label}}
                                                @if(!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Property Use</h6>
                                        <p>{{optional(App\Models\PropertyUse::find($property->assessment->property_use))->label}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Property Zone </h6>
                                        <p>{{ optional(App\Models\PropertyZones::find($property->assessment->zone))->label}} </p>
                                    </div>
                                </div>
                                <div class="row">
                                    @if($property->assessment->no_of_shop!=null)
                                        <div class="col-sm-3">
                                            <h6> Number Of Shops</h6>
                                            <p>{{ $property->assessment->no_of_shop}} </p>
                                        </div>
                                    @endif
                                    @if($property->assessment->no_of_mast!=null)
                                        <div class="col-sm-3">
                                            <h6> Number Of Mast</h6>
                                            <p>{{ $property->assessment->no_of_mast}} </p>
                                        </div>
                                    @endif
                                    @if($property->assessment->no_of_compound_house!=null)
                                        <div class="col-sm-3">
                                            <h6> Number Of Compound House</h6>
                                            <p>{{ $property->assessment->no_of_compound_house}} </p>
                                        </div>
                                    @endif
                                    @if($property->assessment->compound_name!=null)
                                        <div class="col-sm-3">
                                            <h6> Compound Name</h6>
                                            <p>{{ $property->assessment->compound_name}} </p>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6>Calculated Property Rate </h6>
                                        <p>
                                            Le {{number_format($property->assessment->property_rate_without_gst,0,'',',')}}</p>
                                    </div>
                                    {{--                                <div class="col-sm-3">--}}
                                    {{--                                    <h6>GST Calculation</h6>--}}
                                    {{--                                    <p>Le {{number_format($property->assessment->property_gst,0,'',',')}}</p>--}}
                                    {{--                                </div>--}}
                                    {{--                                <div class="col-sm-3">--}}
                                    {{--                                    <h6>Property Calculation With GST</h6>--}}
                                    {{--                                    <p>Le {{number_format($property->assessment->property_rate_with_gst,0,'',',')}}</p>--}}
                                    {{--                                </div>--}}

                                </div>
                                <h6>Assessment Images</h6>
                                <div id="aniimated-thumbnials" class="list-unstyled row clearfix aniimated-thumbnials">
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{$property->assessment->getAdminImageOneUrl(800,800)}}"
                                           data-sub-html="">
                                            <img class="img-responsive thumbnail"
                                                 src="{{$property->assessment->getImageOneUrl(100,100)}}">
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{$property->assessment->getAdminImageTwoUrl(800,800)}}"
                                           data-sub-html="">
                                            <img class="img-responsive thumbnail"
                                                 src="{{$property->assessment->getImageTwoUrl(100,100)}}">
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if($property->geoRegistry)
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header bg-cyan">
                                <h2>
                                    Geo-Registry Details
                                </h2>

                            </div>
                            <div class="body geo-registry-view">
                                <div class="row">
                                    @if($property->registryMeters->count())
                                        @foreach($property->registryMeters as $key=>$value)
                                            <div class="col-sm-3">
                                                <h6>Meter Number {{$key+1}}</h6>
                                                <p>{{$value->number}}</p>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-sm-3">
                                            <h6>Meter Number</h6>
                                            <p></p>
                                        </div>
                                    @endif

                                    <div class="col-sm-3">
                                        <h6>Point 1</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point1)}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Point 2</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point2)}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Point 3</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point3)}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6>Point 4</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point4)}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Point 5</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point5)}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Point 6</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point6)}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Point 7</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point7)}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6>Point 8</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point8)}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Digital Address</h6>
                                        <p>{{$property->geoRegistry->digital_address}}</p>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6>Open Location Code</h6>
                                        <p>{{ $property->postcode }} {{$property->geoRegistry->open_location_code}}</p>
                                    </div>
                                </div>
                                <div id="aniimated-thumbnials" class="list-unstyled row clearfix aniimated-thumbnials">

                                    @if($property->registryMeters->count())
                                        @foreach($property->registryMeters as $key=>$image)
                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                <h6>Meter Image {{$key+1}}</h6>

                                                <a href="{{$image->getImageUrl(800,800)}}" data-sub-html="">
                                                    <img class="img-responsive thumbnail"
                                                         src="{{$image->getImageUrl(100,100)}}">
                                                </a>
                                            </div>

                                        @endforeach
                                    @else
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                            <h6>Meter Image</h6>
                                            <a href="{{asset('/images/No_Image_Available.jpg')}}" data-sub-html="">
                                                <img style="width: 100px;height: 100px" class="img-responsive thumbnail"
                                                     src="{{asset('/images/No_Image_Available.jpg')}}">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header bg-cyan">
                                <h2>
                                    Map
                                </h2>

                            </div>
                            <div class="body">
                                <div class="row">
                                    <div id="map" style="height: 500px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <p>There is no result for this digital address</p>
        @endif
    @else
       
    
        {{--  <p>There is no result for this digital address</p>  --}}
        <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-red">
                    <h2>
                       Reversed Payments
                    </h2>
                </div>
                <div class="body"  style=" overflow-x: scroll; ">
                    <table class="table">
                        <thead>
                        <th>Property ID</th>
                        <th>Transaction ID</th>
                        <th>Payment Made</th>
                        <th>Cashier Name</th>
                        <th>Amount Due</th>
                        {{-- <th>Paying Amount</th>
                        <th>Penalty</th> --}}
                        <th>Amount Paid</th>
                        <th>Remaining Balance</th>
                        <th>Payment Type</th>
                        <!-- <th>Cheque Number</th> -->
                        <th>Payee Name</th>
                        <th>Transaction Date</th>
                        <th>Receipt</th>
                        @hasanyrole('Super Admin|Admin')
                        <th>View</th>
                        <!-- <th>Reverse Payment</th> -->
                        @endhasanyrole
                        </thead>
                        <tbody>
                        @foreach($reverse_payments as $payment)
                            <tr>
                                <td>{{ $payment->property_id }}</td>
                                <td>{{ $payment->transaction_id }}</td>
                                <td>{{ $payment->payment_made_year == null ? '---' :  $payment->payment_made_year }}</td>
                                <td>{{ $payment->admin->getName() }}</td>
                                <td>{{ number_format($payment->assessment) }}</td>
                                {{-- <td>{{ number_format($payment->amount) }}</td>
                                <td>{{ number_format($payment->penalty) }}</td> --}}
                                <td>{{ number_format($payment->total) }}</td>
                                <td>{{ number_format($payment->balance < 0 ? 0 : $payment->balance) }}</td>
                                <td>{{ ucwords($payment->payment_type) }}</td>
                                <!-- <td>{{ $payment->cheque_number }}</td> -->
                                <td>{{ $payment->payee_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                                <td>
                                    @if(!empty($payment->physical_receipt_image_path))
                                   <img class="zoom_receipt" src="{{ $payment->physical_receipt_image_path }}" alt="" width="80" height="60">
                                   @else
                                   <p>No Receipt Uploaded</p>
                                   @endif
                                </td>
                                @hasanyrole('Super Admin|Admin')
                                <td>
                                <!-- <a class=""
                                       href="{{ route('admin.payment.verify', $payment->id) }}"><i
                                            style="font-size: 25px;" class="material-icons">fact_check</i>
                                    </a> -->
        
                                    <a class=""
                                       href="{{ route('admin.payment_reverse.edit', $payment->transaction_id) }}"><i
                                            style="font-size: 25px;" class="material-icons">visibility</i>
                                    </a>
                                       <!-- <a href="{{ route('admin.payment.delete', $payment->id) }}"
                                                      id="delete-payment">
                                                      <i style="font-size: 25px;"
                                                                             class="material-icons">delete</i></a> -->
        
                                   </td>
        
                                   @endhasanyrole
                                {{--<th>
                                    <a class="btn btn-primary btn-xs"
                                       href="{{ route('admin.payment.edit', $payment->id) }}"><i
                                            style="font-size: 14px;" class="material-icons">colorize</i>Edit
                                    </a> &nbsp;&nbsp;
                                    <a class="btn btn-danger btn-xs"
                                       href="{{ route('admin.payment.delete', $payment->id) }}"
                                       id="delete-payment"><i style="font-size: 14px;"
                                                              class="material-icons">delete</i>Delete</a>
                                    <a style="margin-left: 10px;" class="btn btn-success btn-xs"
                                       href="{{ route('admin.payment.pos.receipt', ['id' => $property->id, 'payment_id' => $payment->id]) }}"><i
                                            style="font-size: 14px;"
                                            class="material-icons">print</i>Print</a>
                                </th>--}}
                                    <!-- <td>
                                        <a href="{{route('admin.reverse', $payment->id)}}" class="btn btn-primary m-t-15 waves-effect btn-lg">Reverse Payment</a>
                                    </td> -->
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
      
    @endif
@stop

@push('scripts')
    {{--    <script src="{{ url('admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}"></script>--}}

    <script>
        jQuery("a#delete-payment").on('click', function () {

            url = jQuery(this).attr('href');
            swal({
                    title: "Are you sure?",
                    text: "Do you want to delete this payment!",
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
    </script>

    <script>
        function Comma(Num) { //function to add commas to textboxes
            Num += '';
            Num = Num.replace(',', '');
            Num = Num.replace(',', '');
            Num = Num.replace(',', '');
            Num = Num.replace(',', '');
            Num = Num.replace(',', '');
            Num = Num.replace(',', '');
            x = Num.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1))
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            return x1 + x2;
        }
    </script>

    <script>
        jQuery("#amount, #penalty").on('keyup', function () {
            var amount = jQuery("#amount").val();
            var penalty = jQuery("#penalty").val();

            amount = amount.replace(/,/g, '');
            penalty = penalty.replace(/,/g, '');

            if (amount == '') {
                amount = 0;
            }
            if (penalty == '') {
                penalty = 0;
            }
           // var total = parseInt(amount) + parseInt(penalty);

            var total = parseInt(amount);

            jQuery("#total").val(Comma(total));

        });
    </script>
    @if(!empty($property))
        @if(!empty($property->getAttributes()))
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuA1HA0cE6VXwO48-VNstt7x00yz5H6tE"></script>
            <script>
                var locations = {!! $property->geoRegistry->getPoints() !!};

                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 16,
                    center: new google.maps.LatLng({!! $property->geoRegistry->getCenterPoint() !!}),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                var infowindow = new google.maps.InfoWindow();

                var marker, i;

                for (i = 0; i < locations.length; i++) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map
                    });

                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            infowindow.setContent(locations[i][0]);
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }
            </script>

        @endif
    @endif
    <script type="text/javascript">
        var digital_address, town, old_digital_address

        function doOnLoad() {

            digital_address = new dhtmlXCombo({
                parent: "digital_address",

                filter: "{{route('admin.digital')}}",
                filter_cache: true,
                name: "digital_address"
            });

            old_digital_address = new dhtmlXCombo({
                parent: "old_digital_address",
                filter: "{{route('admin.olddigital')}}",
                filter_cache: true,
                name: "old_digital_address"
            });


        }

        $(document).ready(function () {
            doOnLoad();

        });
    </script>
    <script src="{{ url('admin/js/dhtmlxcombo.js') }}"></script>

@endpush
