
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
                        Search Property
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
                        <!-- <div class="col-sm-4">
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
                        </div> -->
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
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header bg-orange">
                            <div class="row">
                                <div class="col-md-8">
                                    <h2>
                                        Payment

                                    </h2>
                                </div>
                                <div class="col-md-4">
                                    <h2>Property ID : {{$property->id}}</h2>
                                </div>
                            </div>

                        </div>
                        <div class="body">
                            {!! Form::open(['id' => 'forgot_password', 'route' => ['admin.payment.store', $property->id]]) !!}

                            <div class="row">
                                <div class="col-sm-3">
                                    <label
                                        for="email_address">Discounted Rate payable {{ \Carbon\Carbon::parse($property->assessment->created_at)->format('Y') }}</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="assessment"
                                                   value="{{ old('assessment', number_format($property->assessment->getPensionerDisabilityDiscountActual(),0,'',',')) }}"
                                                   disabled class="form-control" placeholder="Assessment"
                                                   style="background-color: #eee;padding-left: 5px;">
                                        </div>
                                        {!! $errors->first('assessment', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label
                                        for="arrear_due">Arrear Due</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="assessment"
                                                   value="{{ old('arrear_due', number_format($property->assessment->getPastPayableDue(),0,'',',')) }}"
                                                   disabled class="form-control" placeholder="Arrear Due"
                                                   style="background-color: #eee;padding-left: 5px;">
                                        </div>
                                        {!! $errors->first('arrear_due', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label
                                        for="penalty">Penalty</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="penalty"
                                                   value="{{ old('penalty', number_format($property->assessment->getPenalty(),0,'',',')) }}"
                                                   disabled class="form-control" placeholder="Penalty"
                                                   style="background-color: #eee;padding-left: 5px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label for="amount_paid">Amount
                                        Paid ({{ $property->assessment->created_at->format('Y') }})</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="amount_paid" name="amount_paid"
                                                   value="{{ old('amount_paid', number_format($property->assessment->getCurrentYearTotalPayment(),0,'',',')) }}"
                                                   class="form-control"
                                                   style="background-color: #eee;padding-left: 5px;"
                                                   placeholder="Enter Amount Paid" disabled>
                                        </div>
                                        {!! $errors->first('amount_paid', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="balance">Balance</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                        <input type="text" id="balance"
                                                value="{{ old('balance', number_format((float) max($property->assessment->getCurrentYearTotalDue(), 0), 2, '.', ',')) }}"
                                                name="balance"
                                                class="form-control"
                                                style="background-color: #eee; padding-left: 5px;"
                                                placeholder="Enter Balance" disabled>
                                        </div>
                                        {!! $errors->first('balance', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="amount">Paying Amount</label>
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <input type="text" id="amount"
                                                           value="{{ old('amount') }}"
                                                           name="amount" class="form-control"
                                                           placeholder="Enter Amount Paying"
                                                           onkeyup="javascript:this.value=Comma(this.value);"
                                                    >
                                                </div>
                                                <span
                                                    class="small">Pre-Calculated : {{number_format($property->assessment->getCurrentInstallmentDueAmount(),0,'',',')}}</span>
                                                {!! $errors->first('amount', '<span class="error">:message</span>') !!}
                                            </div>

                                        </div>

{{--                                        <div class="col-md-4">--}}
{{--                                            <label for="amount">Penalty</label>--}}
{{--                                            <div class="form-group">--}}
{{--                                                <div class="form-line">--}}
{{--                                                    <input type="text" id="penalty"--}}
{{--                                                           value="{{ old('amount') }}"--}}
{{--                                                           name="penalty" class="form-control"--}}
{{--                                                           placeholder="Enter Amount Paying"--}}
{{--                                                           onkeyup="javascript:this.value=Comma(this.value);"--}}
{{--                                                    >--}}
{{--                                                </div>--}}
{{--                                                <span--}}
{{--                                                    class="small">Pre-Calculated : {{number_format($property->getPenalty(),0,'',',')}}</span>--}}
{{--                                                {!! $errors->first('penalty', '<span class="error">:message</span>') !!}--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

                                        <div class="col-md-4">
                                            <label for="amount">Total Amount</label>
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <input type="text" id="total"
                                                           value="{{ old('amount') }}" disabled
                                                           class="form-control"
                                                           style="background-color: #eee;padding-left: 5px;"
                                                           placeholder=""
                                                           onchange="javascript:this.value=Comma(this.value);">
                                                </div>
                                                <span
                                                    class="small">Pre-Calculated : {{number_format($property->assessment->getCurrentInstallmentDueAmount(),0,'',',')}}</span>
                                                {!! $errors->first('amount', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="payment_type">Payment Type</label>
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {!! Form::select('payment_type', ['' => 'Select', 'cash' => 'Cash', 'cheque'=> 'Cheque'], old('payment_type'), ['class' => 'form-control']) !!}
                                                </div>
                                                {!! $errors->first('payment_type', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="email_address">Cheque No</label>
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <input type="text" id="cheque_number"
                                                           value="{{ old('cheque_number') }}"
                                                           name="cheque_number" class="form-control"
                                                           placeholder="Cheque Number">
                                                </div>
                                                {!! $errors->first('cheque_number', '<span class="error">:message</span>') !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-sm-3">
                                    <label for="email_address">Payee Name</label>
                                    <div class="form-group">
                                        <div class="form-line" id="payee_name">
                                            <input type="text" id="payee_name" value="{{ old('payee_name') }}"
                                                   name="payee_name" class="form-control"
                                                   placeholder="Payee Name">
                                        </div>
                                        {!! $errors->first('payee_name', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label for="email_address">Payment Fulfilment</label>
                                    <div class="form-group">
                                        <div class="form-line" id="payemnt_fulfilment">
                                            <input type="text" id="payment_fulfilment"  value=""
                                                   name="payment_fulfilment_type" class="form-control"
                                                   placeholder="Payment Fulfilment">
                                        </div>
                                        {!! $errors->first('payment_fulfilment', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="row">

                                        @for($i = 0; $i <= 1; $i++)
                                            <div class="col-md-6">
                                                <div class="text-center"
                                                     style="min-height: 105px; border: 1px solid #eee; padding: 10px; background-color: {{ $property->assessment->getQuarter()  == ($i + 1) ? '#eee' : ''}}">
                                                    <h5>{!!  \App\Models\PropertyPayment::numberToWord($i + 1) !!}
                                                        Installment Date</h5>
                                                        @if($i == 0)
                                                    <h4>30-06-2024</h4>
                                                    @elseif($i == 1)
                                                    <h4>31-12-2024</h4>
                                                    @else
                                                    @endelse
                                                    @endif
                                                    {{--@if($i==3)
                                                        <h6>{!! number_format($property->getAssessment()) !!}</h6>
                                                        @else--}}
                                                    <h6>{!! isset($paymentInQuarter[$i+1]) ?  number_format($paymentInQuarter[$i+1]) : '-' !!}</h6>
                                                    {{-- @endif--}}
                                                </div>

                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="col-sm-6 text-left">
                                    <button type="submit" class="btn btn-primary m-t-15 waves-effect btn-lg">Save
                                    </button>
                                </div>
                            </div>


                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>


                @if($property->payments()->count())
                  
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header bg-orange">
                                <h2>
                                    Transactions
                                </h2>
                            </div>
                            <div class="body"  style=" overflow-x: scroll; ">
                                <table class="table">
                                    <thead>
                                    <th>Property ID</th>
                                    <th>Transaction ID</th>
                                    <th>Payment Fulfillment</th>
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
                                    @foreach($property->payments()->where('assessment','!=',0.0000)->where('amount','!=',0.0000)->latest()->get() as $payment)
                                        <tr>
                                            <td>{{ $payment->property_id }}</td>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->payment_fulfilment }}</td>
                                            <td>{{ $payment->admin->getName() }}</td>
                                            <td>{{ number_format($payment->assessment, 2, '.', '') }}</td>
                                            {{-- <td>{{ number_format($payment->amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($payment->penalty) }}</td> --}}
                                            <td>{{ number_format($payment->total , 2, '.', ',') }}</td>
                                            <td>{{ number_format($payment->balance < 0 ? 0 : $payment->balance , 2, '.', '') }}</td>
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
                                    <th>Council Adjustments</th>
                                    <th>Net Assessed Value</th>
                                    <th>Rate Payable</th>
                                    <th>Council Discounts</th>
                                    <th>Discounted rate payable</th>
                                    <th>Arrears</th>
                                    <th>Penalty</th>
                                    <th>Amount Paid</th>
                                    <th>Due</th>
                                    </thead>
                                    <tbody>
                                    @foreach($property->assessmentHistory as $assessmentHistory)
                                        <tr>
                                            <td>{{ $assessmentHistory->created_at->format('Y') }}</td>
                                            <td>{{ number_format($assessmentHistory->getCurrentYearAssessmentAmount(), 2, '.', ',') }}</td>
                                            <td>{{ number_format(floatval($assessmentHistory->getCouncilAdjustments()), 2, '.', ',') }}</td>
                                            <td>{{ number_format(floatval($assessmentHistory->getNetPropertyAssessedValue()), 2, '.', ',') }}</td>
                                            <td>{{ number_format(floatval($assessmentHistory->getPropertyTaxPayable()), 2, '.', ',') }}</td>
                                            @if ($assessmentHistory->pensioner_discount && $assessmentHistory->disability_discount)
                                            <td>{{ number_format(floatval($assessmentHistory->getPensionerDiscount()), 2, '.', ',') + number_format(floatval($assessmentHistory->getDisabilityDiscount()), 2, '.', ',') }}</td>
                                        @else
                                            <td>{!! $assessmentHistory->pensioner_discount ? number_format(floatval($assessmentHistory->getPensionerDiscount()), 2, '.', ',') : 0 !!}</td>
                                        @endif
                                        <td>{!! $assessmentHistory->getPensionerDisabilityDiscountActual() ? number_format(floatval($assessmentHistory->getPensionerDisabilityDiscountActual()),2,'.',',') : 0 !!}</td>
                                            <td>{{ number_format($assessmentHistory->getPastPayableDue(), 2, '.', ',') }}</td>
                                            <td>{{ number_format($assessmentHistory->getPenalty(), 2, '.', ',') }}</td>
                                            <td>{{ number_format($assessmentHistory->getCurrentYearTotalPayment(), 2, '.', ',') }}</td>
                                            <td>{{ number_format((float)$assessmentHistory->getCurrentYearTotalDue(), 2, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                    {{-- <tr>
                                        <td>{{ \Carbon\Carbon::parse($property->assessment->created_at)->format('Y') }}</td>
                                        <td>{{ number_format($property->assessment->getCurrentYearAssessmentAmount()) }}</td>
                                        <td>{{ number_format($property->assessment->getPastPayableDue()) }}</td>
                                        <td>{{ number_format($property->assessment->getPenalty()) }}</td>
                                        <td>{{ number_format($property->assessment->getCurrentYearTotalPayment()) }}</td>
                                        <td>{{ number_format((float)$property->assessment->getCurrentYearTotalDue(), 2, '.', ',') }}</td>
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
                                    {{-- <div class="col-sm-3">
                                        <div id="aniimated-thumbnials"
                                             class="list-unstyled row clearfix aniimated-thumbnials">
                                            <h6>Image</h6>
                                            <a href="{{$property->landlord->getImageUrl(800,800)}}" data-sub-html="">
                                                <img class="img-responsive thumbnail"
                                                     src="{{$property->landlord->getImageUrl(100,100)}}">
                                            </a>
                                        </div>
                                    </div> --}}
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
                                        <h6>Assessed Value </h6>
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
                                    {{-- <div class="col-sm-3">
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
                                    <div class="col-sm-3">
                                        <h6>Point 8</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point8)}}</p>
                                    </div> --}}
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3">
                                        <h6>Dor Lat Long</h6>
                                        <p>{{\App\Models\Property::getLatLong($property->geoRegistry->dor_lat_long)}}</p>
                                    </div>
        
                                    <div class="col-sm-3">
                                        <h6>Digital Address</h6>
                                        <p>{{$property->geoRegistry->digital_address}}</p>
                                    </div>
        
                                    <div class="col-sm-3">
                                        <h6>Open Location Code</h6>
                                        {{-- <p>{{ $property->postcode }} </p> --}}
                                            
                                            <p>{{$property->geoRegistry->open_location_code}}</p>
                                    </div>
                                </div>
        
                                <div id="aniimated-thumbnials" class="list-unstyled row clearfix aniimated-thumbnials">
        
                                    @if($property->registryMeters->count())
                                        @foreach($property->registryMeters as $key=>$image)
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                                <h6>Meter Image {{$key+1}}</h6>
        
                                                <a href="{{$image->getImageUrl(800,800)}}" data-sub-html="">
                                                    <img class="img-responsive thumbnail"
                                                         src="{{$image->getImageUrl(224,155)}}">
                                                </a>
                                                <p class="text-center"><strong>Meter Number: </strong> {{$image->number}}</p>
                                            </div>
        
                                        @endforeach
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
        <p>There is no result for this digital address</p>
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
@php
if($property){
    if($property->assessment){
      //  $preCalculatedAmount = $property->assessment->getCurrentInstallmentDueAmount()??0;
        $preCalculatedAmount = $property->assessment->getCurrentYearTotalDue()??0;
    }
}
@endphp
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
            
            let preCalculatedAmount = '{{$preCalculatedAmount??0}}';
            console.log(preCalculatedAmount)
            console.log(amount)
            if (parseInt(amount) >= parseInt(preCalculatedAmount)) {
               // alert("if")
                jQuery("#payment_fulfilment").val('Complete Amount');
        } else {
           // alert("else")
            jQuery("#payment_fulfilment").val('Partial');
            
        }
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
