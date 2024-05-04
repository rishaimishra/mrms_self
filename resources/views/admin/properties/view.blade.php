@extends('admin.layout.main')

@section('title')
    {{$title}}
@stop

@section('page_title') {{$title}} @stop

@section('content')




        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header bg-orange">
                        <h2>
                            Assessment History
                        </h2>
                    </div>
                    <div class="body" style=" overflow-x: scroll; ">
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

                                @if($property->assessmentHistory->count())
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
                                @endif
                                <!-- <tr>
                                    <td>{{ \Carbon\Carbon::parse($property->assessment->created_at)->format('Y') }}</td>
                                    <td>{{ number_format($property->assessment->getCurrentYearAssessmentAmount()) }}</td>
                                    <td>{{ number_format($property->assessment->getPastPayableDue()   ) }}</td>
                                    <td>{{ number_format(($property->assessment->getPastPayableDue() + ($property->assessment->getPenalty()/2)) *.25) < 0 ? 0 : number_format(($property->assessment->getPastPayableDue() + ($property->assessment->getPenalty()/2)) *.25) }}</td>
                                    <td>{{ number_format($property->assessment->getCurrentYearTotalPayment()) }}</td>
                                    <td>{{ number_format($property->assessment->getCurrentYearAssessmentAmount()+ (($property->assessment->getPastPayableDue() + ($property->assessment->getPenalty()/2)) *.25) + $property->assessment->getPastPayableDue() + ($property->assessment->getPenalty()/2)) }}</td>
                                </tr> -->
                            </tbody>
                        </table>
                        <div class="info">
                        <button type="button" id="info_button" class="btn btn-info rounded-sm"><b>i</b></button>

                                <span class="extra-info" id="info_content" style="display:none;">
                                    <br>
                                    Arrear = Past Year Due Amount <br>
                                    CurrentYearDue = Current Year Assessment Value + Arrear + (Arrear*0.25)
                                </span>
                                <a type="button" href="/back-admin/properties/" class="btn btn-info rounded-sm"><b>update</b></a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        @if($property->payments()->count())
            <div id="payments" class="row">
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

                                <th>Cashier Name</th>
                                <th>Amount Due</th>
                                {{-- <th>Amount</th> --}}
                                {{-- <th>Penalty</th> --}}
                                <th>Amount Paid</th>
                                <th>Remaining Balance</th>
                                <th>Payment Type</th>
                                <th>Cheque Number</th>
                                <th>Payee Name</th>
                                <th>Transaction Date</th>
                               {{-- <th>Action</th> --}}
                                </thead>
                                <tbody>
                                @foreach($property->payments()->latest()->get() as $payment)
                                    <tr>
                                        <td>{{ $payment->property_id }}</td>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ $payment->admin->getName() }}</td>
                                        <td>{{ number_format($payment->assessment) }}</td>
                                        {{-- <td>{{ number_format($payment->amount) }}</td>
                                        <td>{{ number_format($payment->penalty) }}</td> --}}
                                        <td>{{ number_format($payment->total) }}</td>
                                        <td>{{ number_format($payment->balance < 0 ? 0 : $payment->balance) }}</td>
                                        <td>{{ ucwords($payment->payment_type) }}</td>
                                        <td>{{ $payment->cheque_number }}</td>
                                        <td>{{ $payment->payee_name }}</td>
                                        <th>{{ \Carbon\Carbon::parse($payment->created_at)->format('Y M, d H:i A') }}</th>
                                        {{-- <th>
                                         @hasanyrole('Super Admin')
                                         <a href="{{ route('admin.payment.edit', $payment->id) }}"><i
                                                    style="font-size: 14px;" class="material-icons">colorize</i></a> |
                                            <a href="{{ route('admin.payment.delete', $payment->id) }}"
                                                           id="delete-payment">
                                                           <i style="font-size: 14px;"
                                                                                  class="material-icons">delete</i></a>
                                            @endhasanyrole
                                        </th> --}}
                                       {{-- <th><a href="{{ route('admin.payment.edit', $payment->id) }}"><i
                                                    style="font-size: 14px;" class="material-icons">colorize</i>Edit </a>
                                            &nbsp;&nbsp;<a href="{{ route('admin.payment.delete', $payment->id) }}"
                                                           id="delete-payment"><i style="font-size: 14px;"
                                                                                  class="material-icons">delete</i>Delete</a>
                                        </th>--}}

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    <div id="landloard" class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            {!! Form::open(['id'=>'landlord-sendsms','route'=>'admin.properties.landlord.sendsms']) !!}
            {!! Form::hidden('landlord_id',$property->landlord->id) !!}
            {!! Form::hidden('property_id',$property->id) !!}
            {!! Form::close() !!}

            {!! Form::open(['id'=>'landlord-form','route'=>'admin.properties.landlord.save','files' => true]) !!}
            {!! Form::hidden('landlord_id',$property->landlord->id) !!}
            {!! Form::hidden('property_id',$property->id) !!}
            <div class="card">
                <div class="header bg-orange">
                    <div class="row">
                        <div class="col-md-3">
                            <h2>
                                Landlord Details
                            </h2>
                        </div>
                        <div class="col-md-9 text-right">
                            @hasanyrole('Super Admin|Admin|manager')
                            <button type="button" class="btn btn-large btn-success" id="send-sms"> Send SMS</button>
                            <button type="button" id="landloard-button" class="btn btn-large btn-primary"> Edit</button>
                            <button style="display: none" type="submit" id="landloard-save"
                                    class="btn btn-large btn-primary">Save
                            </button>
                            <button style="display: none" type="button" id="landloard-cancel"
                                    class="btn btn-large btn-primary">Cancel
                            </button>
                            @endhasanyrole
                            @if($property->landlord->email)
                                <a class="btn btn-default" id="email-demand-btn"
                                   data-content="{{ route('admin.email.payment.receipt', $property->id) }}"
                                   href="javascript: return false;">Email Demand Note
                                </a>
                            @endif

                                {{-- <a class="btn btn-default" id="sticker-btn"
                                   data-content="{{ route('admin.stickers', $property->id) }}"
                                   href="javascript: return false;">Sticker
                                </a> --}}
                            <a class="btn btn-default" id="print-envelope-btn"
                               data-content="{{ route('admin.download.envelope', $property->id) }}"
                               href="javascript: return false;">Download Envelope
                            </a>
                            <a class="btn btn-default" id="print-demand-btn"
                               data-content="{{ route('admin.payment.receipt', $property->id) }}"
                               href="javascript: return false;">Download Demand Note
                            </a>
                            <select style="width: 50px;" id="demand-draft-year">

                                @foreach($property->assessments()->orderBy('created_at', 'desc')->pluck('created_at') as $date)
                                    @php $i = \Carbon\Carbon::parse($date)->format('Y') @endphp
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="body landlord-view">
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Property ID</h6>
                            <p>{{$property->id}}</p>
                        </div>
                        @if($property->is_organization==0)
                            <div class="col-sm-3">
                                <h6>Title</h6>
                                <p>{{ optional(App\Models\UserTitleTypes::find($property->landlord->ownerTitle))->label}}</p>
                            </div>
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
                            <h6>Email Address</h6>
                            <p>{{$property->landlord->email}}</p>
                        </div>
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
                            <div class="col-sm-3">
                                <div id="aniimated-thumbnials" class="list-unstyled row clearfix aniimated-thumbnials">
                                    {{-- <h6>Image</h6>
                                    <a href="{{$property->landlord->getImageUrl(800,800)}}" data-sub-html="">
                                        <img class="img-responsive thumbnail"
                                             src="{{$property->landlord->getImageUrl(100,100)}}">
                                    </a> --}}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
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
                <div style="display: none;" class="body landlord-edit">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6>Is Organization</h6>
                            <div class="switch">
                                <input type="hidden" value="0" name="is_organization" checked="">
                                <label>No<input id="is_organization" type="checkbox" value="1"
                                                name="is_organization" {{old('is_organization',$property->is_organization)==1?'checked=""':''}}><span
                                        class="lever"></span>Yes</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="personal {{$property->is_organization==0?'':'hidden'}}">
                            <div class="col-sm-3">
                                <h6>Title</h6>
                                <p>{!! Form::select('ownerTitle', $usertitles , optional(App\Models\UserTitleTypes::find($property->landlord->ownerTitle))->label, ['class' => 'form-control','data-live-search'=>'true','id'=>'ownerTitle']) !!}</p>
                                @if ($errors->has('ownerTitle'))
                                    <label class="error">{{ $errors->first('ownerTitle') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>First Name</h6>
                                <p>{!! Form::text('first_name',$property->landlord->first_name,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('first_name'))
                                    <label class="error">{{ $errors->first('first_name') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Middle Name</h6>
                                <p>{!! Form::text('middle_name',$property->landlord->middle_name,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('middle_name'))
                                    <label class="error">{{ $errors->first('middle_name') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Surname</h6>
                                <p>{!! Form::text('surname',$property->landlord->surname,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('surname'))
                                    <label class="error">{{ $errors->first('surname') }}</label>
                                @endif
                            </div>

                            <div class="col-sm-3">
                                <h6>Gender</h6>
                                <p>{!! Form::select('sex', $gender ,$property->landlord->sex, ['class' => 'form-control','data-live-search'=>'true','id'=>'sex']) !!}</p>
                                @if ($errors->has('sex'))
                                    <label class="error">{{ $errors->first('sex') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="organization {{$property->is_organization==0?'hidden':''}}">
                            <div class="col-sm-3">
                                <h6>Organization Name</h6>
                                <p>{!! Form::text('organization_name',$property->organization_name,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('organization_name'))
                                    <label class="error">{{ $errors->first('organization_name') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Organization Type</h6>
                                <p>{!! Form::select('organization_type', $org_type , (in_array(strtolower($property->organization_type),array('government', 'ngo', 'business', 'school', 'religious', 'diplomatic mission','hospital'))?$property->organization_type:'Other'), ['class' => 'form-control','data-live-search'=>'true','id'=>'organization_type']) !!}</p>
                                {!! Form::text('organization_type',(in_array(strtolower($property->organization_type),array('government', 'ngo', 'business', 'school', 'religious', 'diplomatic mission','hospital'))?'':$property->organization_type),['class'=>'form-control '.(in_array(strtolower($property->organization_type),array('government', 'ngo', 'business', 'school', 'religious', 'diplomatic mission','hospital'))?'hidden':'').'',''.(in_array(strtolower($property->organization_type),array('government', 'ngo', 'business', 'school', 'religious', 'diplomatic mission','hospital'))?'disabled':'').'']) !!}
                                @if ($errors->has('organization_type'))
                                    <label class="error">{{ $errors->first('organization_type') }}</label>
                                @endif

                            </div>
                            {{-- <div class="col-sm-3">
                                <h6>Organization Tin Number</h6>
                                <p>{!! Form::text('organization_tin',$property->organization_tin,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('organization_tin'))
                                    <label class="error">{{ $errors->first('organization_tin') }}</label>
                                @endif
                            </div> --}}
                            <div class="col-sm-3">
                                <h6>Organization Address</h6>

                                <p>{!! Form::text('organization_addresss',$property->organization_addresss,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('organization_addresss'))
                                    <label class="error">{{ $errors->first('organization_addresss') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Email Address</h6>
                            <p>{!! Form::text('email',$property->landlord->email,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('email'))
                                <label class="error">{{ $errors->first('email') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Street Number</h6>
                            <p>{!! Form::text('street_number',$property->landlord->street_number,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('street_number'))
                                <label class="error">{{ $errors->first('street_number') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Street Name</h6>
                            <p>{!! Form::text('street_name',$property->landlord->street_name,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('street_name'))
                                <label class="error">{{ $errors->first('street_name') }}</label>
                            @endif
                        </div>
                        <div class="personal {{$property->is_organization==0?'':'hidden'}}">
                            {{-- <div class="col-sm-3">
                                <h6>Tin Number</h6>
                                <p>{!! Form::text('tin',$property->landlord->tin,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('tin'))
                                    <label class="error">{{ $errors->first('tin') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Id Type</h6>
                                <p>{!! Form::select('id_type', $id_type , (in_array(strtolower($property->landlord->id_type),array('national id', 'passport', 'driver’s license', 'voter id'))?$property->landlord->id_type:'other'), ['class' => 'form-control','data-live-search'=>'true','id'=>'id_type']) !!}
                                    {!! Form::text('id_type',(in_array(strtolower($property->landlord->id_type),array('national id', 'passport', 'driver’s license', 'voter id'))?'':$property->landlord->id_type),['class'=>'form-control '.(in_array(strtolower($property->landlord->id_type),array('national id', 'passport', 'driver’s license', 'voter id'))?'hidden':'').'',''.(in_array(strtolower($property->landlord->id_type),array('national id', 'passport', 'driver’s license', 'voter id'))?'disabled':'').'']) !!}
                                </p>
                                @if ($errors->has('id_type'))
                                    <label class="error">{{ $errors->first('id_type') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Id Number</h6>
                                <p>{!! Form::text('id_number',$property->landlord->id_number,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('id_number'))
                                    <label class="error">{{ $errors->first('id_number') }}</label>
                                @endif
                            </div> --}}
                            {{-- <div class="col-sm-3">
                                <h6>Image</h6>
                                <p><img src="{{$property->landlord->getImageUrl(100,100)}}"></p>
                                {!! Form::file('image',['class'=>'form-control']) !!}
                                <p>*JPG,JPEG and PNG File Allow Only</p>
                                @if ($errors->has('image'))
                                    <label class="error">{{ $errors->first('image') }}</label>
                                @endif
                            </div> --}}
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Ward</h6>
                            <p>{!! Form::select('ward', $ward->prepend('Select Ward','') , $property->landlord->ward, ['class' => 'form-control','data-live-search'=>'true','id'=>'ward']) !!}</p>
                            @if ($errors->has('ward'))
                                <label class="error">{{ $errors->first('ward') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Constituency</h6>
                            <p>{!! Form::select('constituency', $constituency ,$property->landlord->constituency, ['class' => 'form-control','data-live-search'=>'true','id'=>'constituency']) !!}</p>
                            @if ($errors->has('constituency'))
                                <label class="error">{{ $errors->first('constituency') }}</label>
                            @endif
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Section</h6>
                            <p>{!! Form::select('section', $town->prepend('Select Section','') , $property->landlord->section, ['class' => 'form-control','data-live-search'=>'true','id'=>'section']) !!}</p>
                            @if ($errors->has('section'))
                                <label class="error">{{ $errors->first('section') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Chiefdom</h6>
                            <p>{!! Form::select('chiefdom', $chiefdom ,$property->landlord->chiefdom, ['class' => 'form-control','data-live-search'=>'true','id'=>'chiefdom']) !!}</p>
                            @if ($errors->has('chiefdom'))
                                <label class="error">{{ $errors->first('chiefdom') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>District</h6>
                            <p>{!! Form::select('district', $district->prepend('Select District','') ,$property->landlord->district, ['class' => 'form-control','data-live-search'=>'true','id'=>'district']) !!}</p>
                            @if ($errors->has('district'))
                                <label class="error">{{ $errors->first('district') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Province</h6>
                            <p>{!! Form::select('province', $province->prepend('Select Province','') ,$property->landlord->province, ['class' => 'form-control','data-live-search'=>'true','id'=>'province']) !!}</p>
                            @if ($errors->has('province'))
                                <label class="error">{{ $errors->first('province') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Postcode</h6>
                            <p>{!! Form::text('postcode',$property->landlord->postcode,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('postcode'))
                                <label class="error">{{ $errors->first('postcode') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Mobile Number 1</h6>
                            <p>{!! Form::text('mobile_1',$property->landlord->mobile_1,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('mobile_1'))
                                <label class="error">{{ $errors->first('mobile_1') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Mobile Number 2</h6>
                            <p>{!! Form::text('mobile_2',$property->landlord->mobile_2,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('mobile_2'))
                                <label class="error">{{ $errors->first('mobile_2') }}</label>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div id="property" class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            {!! Form::open(['id'=>'property-form','route'=>'admin.properties.property.save', 'files' => true]) !!}
            {!! Form::hidden('property_id',$property->id) !!}
            <div class="card">
                <div class="header bg-cyan">
                    <div class="row">
                        <div class="col-md-8">
                            <h2>
                                Property Details
                            </h2>
                        </div>
                        <div class="col-md-4 text-right">
                            @hasanyrole('Super Admin|Admin|manager')
                            <button type="button" id="property-button" class="btn btn-large btn-primary">Edit</button>
                            <button style="display: none" type="submit" id="property-save"
                                    class="btn btn-large btn-primary">Save
                            </button>
                            <button style="display: none" type="button" id="property-cancel"
                                    class="btn btn-large btn-primary">Cancel
                            </button>
                            @endhasanyrole
                        </div>
                    </div>

                </div>
                <div class="body property-view">
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Old Street Number</h6>
                            <p>{{$property->street_number}}</p>
                        </div>
                        <div class="col-sm-3">
                            <h6>New Street Number</h6>
                            <p>{{$property->street_numbernew}}</p>
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


                        <div class="col-sm-3">
                            <h6>Is Property Inaccessible</h6>
                            <p>{{ $property->is_property_inaccessible ? 'Yes' : 'No' }}</p>
                        </div>

                        <div class="col-sm-3">
                            <h6>Property Inaccessible</h6>
                            <p>{{ $property->propertyInaccessible->pluck('label')->implode(', ') }}</p>
                        </div>


                        <div class="col-sm-3">
                            <h6>Demand Note Delivered</h6>
                            <p>{{ $property->is_draft_delivered ? 'Yes' : 'No' }}</p>
                        </div>


                        @if($property->is_draft_delivered)
                            <div class="col-sm-3">
                                <h6>Recipient Name</h6>
                                <p>{{ $property->delivered_name ?: 'Un-specified' }}</p>
                            </div>


                            <div class="col-sm-3">
                                <h6>Recipient Number</h6>
                                <p>{{ $property->delivered_number ?: 'Un-specified' }}</p>
                            </div>

                            @if($property->delivered_image)

                                <div class="col-sm-3">
                                    <div id="aniimated-thumbnials"
                                         class="list-unstyled row clearfix aniimated-thumbnials">
                                        <h6>Recipient Image</h6>

                                        <a href="{{$property->getDeliveredImagePath(800,800)}}" data-sub-html="">
                                            <img class="img-responsive thumbnail"
                                                 src="{{$property->getDeliveredImagePath(50,50)}}">
                                        </a>
                                    </div>
                                </div>

                            @endif
                        @endif

                    </div>
                </div>
                <div style="display: none" class="body property-edit">
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Street Number</h6>
                            <p>{!! Form::text('street_number',$property->street_number,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('street_number'))
                                <label class="error">{{ $errors->first('street_number') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Street Name</h6>
                            <p>{!! Form::text('street_name',$property->street_name,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('street_name'))
                                <label class="error">{{ $errors->first('street_name') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Ward</h6>
                            <p>{!! Form::select('ward', $ward , $property->ward, ['class' => 'form-control','data-live-search'=>'true','id'=>'ward_1']) !!}</p>
                            @if ($errors->has('ward'))
                                <label class="error">{{ $errors->first('ward') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Constituency</h6>
                            <p>{!! Form::select('constituency', $constituency ,$property->constituency, ['class' => 'form-control','data-live-search'=>'true','id'=>'constituency_1']) !!}</p>
                            @if ($errors->has('constituency'))
                                <label class="error">{{ $errors->first('constituency') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Section</h6>
                            <p>{!! Form::select('section', $town->prepend('Select Section','') , $property->section, ['class' => 'form-control','data-live-search'=>'true','id'=>'section_1']) !!}</p>
                            @if ($errors->has('section'))
                                <label class="error">{{ $errors->first('section') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Chiefdom</h6>
                            <p>{!! Form::select('chiefdom', $chiefdom ,$property->chiefdom, ['class' => 'form-control','data-live-search'=>'true','id'=>'chiefdom_1']) !!}</p>
                            @if ($errors->has('chiefdom'))
                                <label class="error">{{ $errors->first('chiefdom') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>District</h6>
                            <p>{!! Form::select('district', $district->prepend('Select District','') ,$property->district, ['class' => 'form-control','data-live-search'=>'true','id'=>'district_1']) !!}</p>
                            @if ($errors->has('district'))
                                <label class="error">{{ $errors->first('district') }}</label>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <h6>Province</h6>
                            <p>{!! Form::select('province', $province->prepend('Select Province','') ,$property->province, ['class' => 'form-control','data-live-search'=>'true','id'=>'province_1']) !!}</p>
                        </div>
                        @if ($errors->has('province'))
                            <label class="error">{{ $errors->first('province') }}</label>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6>Postcode</h6>
                            <p>{!! Form::text('postcode',$property->postcode,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('postcode'))
                                <label class="error">{{ $errors->first('postcode') }}</label>
                            @endif
                        </div>


                        <div class="col-sm-3">
                            <h6>Property Inaccessible</h6>
                            <p>
                                <select name="property_inaccessable[]" class="form-control" data-live-search="true"
                                        id="property_inaccessable" multiple="multiple">
                                    <option value="">Select Type</option>
                                    @foreach($property_inaccessable as $key => $type)
                                        <option
                                            {{ in_array($key, array_values($selected_property_inaccessable)) ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </p>
                            @if ($errors->has('occupancy_type'))
                                <label class="error">{{ $errors->first('occupancy_type') }}</label>
                            @endif
                        </div>

                        <div class="col-sm-3">
                            <h6>Demand Note Delivered</h6>
                            <p>
                                <select name="is_draft_delivered" class="form-control" data-live-search="true"
                                        id="is_draft_delivered">
                                    <option value="">Select</option>
                                    <option value="1" {{ $property->is_draft_delivered == 1 ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0" {{ $property->is_draft_delivered == 0 ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </p>
                            @if ($errors->has('occupancy_type'))
                                <label class="error">{{ $errors->first('occupancy_type') }}</label>
                            @endif
                        </div>

                        <div class="col-sm-3">
                            <h6>Recipient Name</h6>
                            <p>{!! Form::text('delivered_name',$property->delivered_name,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('delivered_name'))
                                <label class="error">{{ $errors->first('delivered_name') }}</label>
                            @endif
                        </div>


                        <div class="col-sm-3">
                            <h6>Recipient Number</h6>
                            <p>{!! Form::text('delivered_number',$property->delivered_number,['class'=>'form-control']) !!}</p>
                            @if ($errors->has('delivered_number'))
                                <label class="error">{{ $errors->first('delivered_number') }}</label>
                            @endif
                        </div>

                        <div class="col-sm-3">
                            <h6>Recipient Image</h6>
                            <p>{!! Form::file('delivered_image', ['class'=>'form-control']) !!}</p>
                            @if ($errors->has('delivered_image'))
                                <label class="error">{{ $errors->first('delivered_image') }}</label>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    @if($property->occupancy)
        <div id="occupancy" class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(['id'=>'occupancy-form','route'=>'admin.properties.occupancy.save']) !!}
                {!! Form::hidden('occupancy_id',$property->occupancy->id) !!}
                {!! Form::hidden('property_id',$property->id) !!}
                <div class="card">
                    <div class="header bg-cyan">
                        <div class="row">
                            <div class="col-md-8">
                                <h2>
                                    Occupancy Details
                                </h2>
                            </div>
                            <div class="col-md-4 text-right">
                                @hasanyrole('Super Admin|Admin|manager')
                                <button type="button" id="occupancy-button" class="btn btn-large btn-primary">Edit
                                </button>
                                <button style="display: none" type="submit" id="occupancy-save"
                                        class="btn btn-large btn-primary">Save
                                </button>
                                <button style="display: none" type="button" id="occupancy-cancel"
                                        class="btn btn-large btn-primary">Cancel
                                </button>
                                @endhasanyrole
                            </div>
                        </div>

                    </div>
                    <div class="body occupancy-view">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6>Occupancy Type</h6>
                                <p>{{$property->occupancies->pluck('occupancy_type')->implode(', ') }}</p>
                            </div>
                            <div class="col-sm-3">
                                <h6>Tenant Title</h6>
                                <p>{{ optional(App\Models\UserTitleTypes::find($property->occupancy->ownertenantTitle))->label}}</p>
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
                    <div style="display: none;" class="body occupancy-edit">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6>Occupancy Type</h6>
                                <p>
                                    <select name="occupancy_type[]" class="form-control" data-live-search="true"
                                            id="occupancy_type" multiple="multiple">
                                        <option value="">Select Type</option>
                                        @foreach($occupancy_type as $key => $type)
                                            <option
                                                {{ in_array($key, array_values($selected_occupancies)) ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </p>
                                @if ($errors->has('occupancy_type'))
                                    <label class="error">{{ $errors->first('occupancy_type') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Tenant Title</h6>
                                <p>{!! Form::select('ownertenantTitle', $usertitles , optional(App\Models\UserTitleTypes::find($property->occupancy->ownertenantTitle))->label, ['class' => 'form-control','data-live-search'=>'true','id'=>'ownertenantTitle']) !!}</p>
                                @if ($errors->has('ownertenantTitle'))
                                    <label class="error">{{ $errors->first('ownertenantTitle') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Tenant First Name</h6>
                                <p>{!! Form::text('tenant_first_name',$property->occupancy->tenant_first_name,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('tenant_first_name'))
                                    <label class="error">{{ $errors->first('tenant_first_name') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Middle Name</h6>
                                <p>{!! Form::text('middle_name',$property->occupancy->middle_name,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('middle_name'))
                                    <label class="error">{{ $errors->first('middle_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6>Surname</h6>
                                <p>{!! Form::text('surname',$property->occupancy->surname,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('surname'))
                                    <label class="error">{{ $errors->first('surname') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Mobile Number 1</h6>
                                <p>{!! Form::text('mobile_1',$property->occupancy->mobile_1,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('mobile_1'))
                                    <label class="error">{{ $errors->first('mobile_1') }}</label>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <h6>Mobile Number 2</h6>
                                <p>{!! Form::text('mobile_2',$property->occupancy->mobile_2,['class'=>'form-control']) !!}</p>
                                @if ($errors->has('mobile_2'))
                                    <label class="error">{{ $errors->first('mobile_2') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    @endif

    @if($property->assessments()->count())
        @include('admin.properties.assessments')
    @endif

    @if($property->geoRegistry)

        <div id="geo-registry" class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(['id'=>'assessment-form','route'=>'admin.properties.geo-registry.save','files' => true]) !!}
                {!! Form::hidden('georegistry_id',$property->assessment->id) !!}
                {!! Form::hidden('property_id',$property->id) !!}
                {!! Form::hidden('property_geo_registry_id',$property->geoRegistry->id) !!}
                <div class="card">
                    <div class="header bg-cyan">
                        <div class="row">
                            <div class="col-md-8">
                                <h2>
                                    Geo-Registry Details
                                </h2>
                            </div>
                            <div class="col-md-4 text-right">
                                @hasanyrole('Super Admin|Admin|manager')
                                <button type="button" id="geo-registry-button" class="btn btn-large btn-primary">Edit
                                </button>
                                <button style="display: none" type="submit" id="geo-registry-save"
                                        class="btn btn-large btn-primary">Save
                                </button>
                                <button style="display: none" type="button" id="geo-registry-cancel"
                                        class="btn btn-large btn-primary">Cancel
                                </button>
                                @endhasanyrole
                            </div>
                        </div>

                    </div>
                    <div class="body geo-registry-view">
                        <div class="row">
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
                            <div class="col-sm-3">
                                <h6>Point 4</h6>
                                <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point4)}}</p>
                            </div>
                        </div>
                        <div class="row">
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
                            <div class="col-sm-3">
                                <h6>Point 8</h6>
                                <p>{{\App\Models\Property::getLatLong($property->geoRegistry->point8)}}</p>
                            </div>
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
                                <p>{{ $property->postcode }} {{$property->geoRegistry->open_location_code}}</p>
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
                    <div style="display: none;" class="body geo-registry-edit">

                        <div class="row">
                            <div class="col-sm-3">
                                <h6>Point 1</h6>
                                <input type="test" name="point1"
                                       value="{{ old('point1', $property->geoRegistry->point1) }}"
                                       class="form-control input">
                                {!! $errors->first('point1', '<span class="error">:message</span>'); !!}
                            </div>
                            <div class="col-sm-3">
                                <h6>Point 2</h6>
                                <input type="test" name="point2"
                                       value="{{ old('point2', $property->geoRegistry->point2) }}"
                                       class="form-control input">
                                {!! $errors->first('point2', '<span class="error">:message</span>'); !!}
                            </div>
                            <div class="col-sm-3">
                                <h6>Point 3</h6>
                                <input type="test" name="point3"
                                       value="{{ old('point3', $property->geoRegistry->point3) }}"
                                       class="form-control input">
                                {!! $errors->first('point3', '<span class="error">:message</span>'); !!}
                            </div>
                            <div class="col-sm-3">
                                <h6>Point 4</h6>
                                <input type="test" name="point4"
                                       value="{{ old('point4', $property->geoRegistry->point4) }}"
                                       class="form-control input">
                                {!! $errors->first('point4', '<span class="error">:message</span>'); !!}
                            </div>
                            <div class="col-sm-3">
                                <h6>Point 5</h6>
                                <input type="test" name="point5"
                                       value="{{ old('point5', $property->geoRegistry->point5) }}"
                                       class="form-control input">
                                {!! $errors->first('point5', '<span class="error">:message</span>'); !!}
                            </div>
                            <div class="col-sm-3">
                                <h6>Point 6</h6>
                                <input type="test" name="point6"
                                       value="{{ old('point6', $property->geoRegistry->point6) }}"
                                       class="form-control input">
                                {!! $errors->first('point6', '<span class="error">:message</span>'); !!}
                            </div>
                            <div class="col-sm-3">
                                <h6>Point 7</h6>
                                <input type="test" name="point7"
                                       value="{{ old('point7', $property->geoRegistry->point7) }}"
                                       class="form-control input">
                                {!! $errors->first('point7', '<span class="error">:message</span>'); !!}
                            </div>

                            <div class="col-sm-3">
                                <h6>Point 8</h6>
                                <input type="test" name="point8"
                                       value="{{ old('point8', $property->geoRegistry->point8) }}"
                                       class="form-control input">
                                {!! $errors->first('point8', '<span class="error">:message</span>'); !!}
                            </div>

                            <div class="col-sm-3">
                                <h6>Dor Lat Long</h6>
                                <input type="test" name="dor_lat_long"
                                       value="{{ old('dor_lat_long', $property->geoRegistry->dor_lat_long) }}"
                                       class="form-control input">
                                {!! $errors->first('dor_lat_long', '<span class="error">:message</span>'); !!}
                            </div>

                            <div class="col-sm-3">
                                <h6>Digital Address</h6>
                                <input type="test" name="digital_address"
                                       value="{{ old('digital_address', $property->geoRegistry->digital_address) }}"
                                       class="form-control input">
                                {!! $errors->first('digital_address', '<span class="error">:message</span>'); !!}
                            </div>
                        </div>
                        <hr>
                        @if($property->registryMeters->count())
                            @foreach($property->registryMeters as $key=>$value)
                                <div class="row" id="delete-row">

                                    <div class="col-sm-3">
                                        <h6>Meter Number {{$key+1}}</h6>
                                        <p>{!! Form::text('registry['.($key+1).'][meter_number]',$value->number,['class'=>'form-control meter-number']) !!} </p>
                                    </div>
                                    <div class="col-sm-2">
                                        <h6>Meter Image {{$key+1}}</h6>
                                        <div>
                                            <img class="img-thumbnail" src="{{$value->getImageUrl(100,100)}}"/>
                                            <a href="{{ route('admin.properties.meter.delete', $value->id) }}"
                                               id="deleteMeter" style="font-size: 12px; margin-left: 10px;">&times;
                                                Remove</a>
                                        </div>
                                        {!! Form::file('registry['.($key+1).'][meter_image]',['class'=>'form-control meter-number']) !!}
                                        <p>*JPG,JPEG and PNG File Allow Only</p>
                                        {!! Form::hidden('registry['.($key+1).'][id]',$value->id) !!}
                                    </div>

                                </div>
                            @endforeach
                        @endif





                        @for($i=0;$i<=20-$property->registryMeters->count();$i++)
                            <div class="row need-meter">
                                <div class="col-sm-3">
                                    <h6>Meter Number</h6>
                                    <p>{!! Form::text('registry['.($i+1+$property->registryMeters->count()).'][meter_number]','',['class'=>'form-control meter-number']) !!}</p>
                                </div>
                                <div class="col-sm-3">
                                    <h6>Meter Image</h6>
                                    <p>{!! Form::file('registry['.($i+1+$property->registryMeters->count()).'][meter_image]',['class'=>'form-control meter-number']) !!}</p>
                                    <p>*JPG,JPEG and PNG File Allow Only</p>
                                </div>
                            </div>
                        @endfor

                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header bg-cyan">
                        <h2>
                            Map View
                        </h2>

                    </div>
                    <div class="body">
                        <div class="row">
                            <div id="map_canvas" style="height: 500px"></div>
                        </div>
                    </div>
                    <button type="button"  style="display:none;" id="open" onClick="addruler()">
                                Open Ruler
                    </button>
                    <div class="col text-center">
                    <button type="button" class="btn btn-primary" id="open" onClick="SetValues()">
                            Set Values
                    </button>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="mapvalues" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Enter Property Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Enter Total Length</label>
                            <input type="text" class="form-control" id="total_map_length" placeholder="Length in meters">
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Enter Total Area</label>
                            <input type="text" class="form-control" id="total_map_area" placeholder="Length in Sq Meters">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onClick="saveArea({{$property->id}} , {{$property->assessment->id}})">Confirm changes</button>
                </div>
                </div>
            </div>
        </div>


        <!-- <div class="modal fade" id="govpolicymodal" tabindex="-1" role="dialog" aria-labelledby="govpolicymodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="govpolicymodalLabel">Government Policy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 60px;">
                    
                        <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value=""
                            id="gov_good"
                            checked
                        />
                        <label class="form-check-label" for="gov_good">
                            Good
                        </label>
                        </div>

                        
                        <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value=""
                            id="gov_average"
                        />
                        <label class="form-check-label" for="gov_average">
                            Average
                        </label>
                        </div>

                        <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value=""
                            id="gov_bad"
                        />
                        <label class="form-check-label" for="gov_bad">
                           Bad
                        </label>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onClick="locationReload()" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div>
        </div> -->
    @endif



@stop

@push('scripts')

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuA1HA0cE6VXwO48-VNstt7x00yz5H6tE"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuA1HA0cE6VXwO48-VNstt7x00yz5H6tE&v=3&sensor=false&libraries=geometry"></script>
    <script src="https://unpkg.com/measuretool-googlemaps-v3"></script>
    <script>
        var locations = {!! $property->geoRegistry->getPoints() !!};

        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: 100,
            center: new google.maps.LatLng({!! $property->geoRegistry->getCenterPoint() !!}),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });


       var measuretool = new MeasureTool(map, {
            showSegmentLength: true,
            tooltip: true,
            unit: MeasureTool.UnitTypeId.METRIC
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;
        console.log('locations');
        console.log(locations);
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map
            });

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                    console.log(event.latLng.lat());
                    console.log(event.latLng.lng());
                }
            })(marker, i));
            
        }



        $("#property_window_type_2022").change(function(){
            //var selected = $('#Id option:selected').val();
            //alert("Values changed"); 
            $('#govpolicymodal').modal('show');          
        });

        $("#property_wall_materials_2022").change(function(){
            //var selected = $('#Id option:selected').val();
            //alert("Values changed"); 
            $('#govpolicymodal').modal('show');          
        });

        $("#roofs_materials_2022").change(function(){
            //var selected = $('#Id option:selected').val();
            //alert("Values changed"); 
            $('#govpolicymodal').modal('show');          
        });

        function locationReload()
        {
            location.reload();
        }

        function SetValues() {
            $($("#assessment-button")[0]).click();
           // $('#assessment-button').click();
            var area = measuretool.area;
            var length = measuretool.length;
            console.log(length.toFixed(2));
            console.log(area.toFixed(2));
            $('#total_map_area').val(area.toFixed(2));
            $('#total_map_length').val(length.toFixed(2));
            $('#mapvalues').modal('show');
            $("#property_length_2022").val(length.toFixed(2) ).change();
            $("#property_breadth_2022").val((area / length).toFixed(2)).change();
            $("#property_square_meter_2022").val(area.toFixed(2));
            
        }

        function saveArea(property_id, assessment_id) {
            console.log(assessment_id);
            console.log(property_id);
            //return;
            var area = measuretool.area;
            var length = measuretool.length;
            console.log(area);
            
            var breadth = area / length;
            console.log(length.toFixed(2));
            console.log(area.toFixed(2));
            $('#property_area_map').html(area.toFixed(2) + " Meters");
            $('#property_length_map').html(length.toFixed(2) + " Meters");
            var data = {
                area: area,
                length: length,
                breadth: breadth,
                property_id: property_id,
                is_map_set: 1,
                assessment_id: assessment_id,
            }
            
            // var url = '{{route('admin.calculation-new')}}';


            //     var posting = $.get(url, {
            //         property_id:property_id,
            //         property_length: length,
            //         property_breadth: breadth,
            //     });
            $('#assessment-save').click();
            const url_dev = "/mrms_git/mrms/public/apiv2/update/assessments";
            const url_local = "/apiv2/update/assessments";
            $.ajax({
                url: url_local,
                type: "get", //send it through get method
                data: { 
                    area: area.toFixed(2),
                    length: length.toFixed(2),
                    breadth: breadth.toFixed(2),
                    property_id: property_id,
                    assessment_id: assessment_id,
                    is_map_set: 1
                },
                success: function(response) {
                   // location.reload();
                    console.log(response);
                    //Do Something
                },
                error: function(xhr) {
                    //Do Something to handle error
                }
            });

            $('#mapvalues').modal('hide');            
        }

        function addruler() {

var ruler1 = new google.maps.Marker({
    position: map.getCenter() ,
    map: map,
    draggable: true
});

var ruler2 = new google.maps.Marker({
    position: map.getCenter() ,
    map: map,
    draggable: true
});

var ruler1label = new Label({ map: map });
var ruler2label = new Label({ map: map });
ruler1label.bindTo('position', ruler1, 'position');
ruler2label.bindTo('position', ruler2, 'position');

var rulerpoly = new google.maps.Polyline({
    path: [ruler1.position, ruler2.position] ,
    strokeColor: "#FFFF00",
    strokeOpacity: .7,
    strokeWeight: 7
});

rulerpoly.setMap(map);

ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));


google.maps.event.addListener(ruler1, 'drag', function() {
    rulerpoly.setPath([ruler1.getPosition(), ruler2.getPosition()]);
    ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
    ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
});

google.maps.event.addListener(ruler2, 'drag', function() {
    rulerpoly.setPath([ruler1.getPosition(), ruler2.getPosition()]);
    ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
    ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
});

google.maps.event.addListener(ruler1, 'dblclick', function() {
    ruler1.setMap(null);
    ruler2.setMap(null);
    ruler1label.setMap(null);
    ruler2label.setMap(null);
    rulerpoly.setMap(null);
});

google.maps.event.addListener(ruler2, 'dblclick', function() {
    ruler1.setMap(null);
    ruler2.setMap(null);
    ruler1label.setMap(null);
    ruler2label.setMap(null);
    rulerpoly.setMap(null);
});


}


function distance(lat1,lon1,lat2,lon2) {
var um = "km"; // km | ft (change the constant)
var R = 6371;
if (um=="ft") { R = 20924640; /* ft constant */ }
var dLat = (lat2-lat1) * Math.PI / 180;
var dLon = (lon2-lon1) * Math.PI / 180; 
var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1 * Math.PI / 180 ) * Math.cos(lat2 * Math.PI / 180 ) * 
    Math.sin(dLon/2) * Math.sin(dLon/2); 
var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
var d = R * c;
if(um=="km") {
    if (d>1) return Math.round(d)+"km";
    else if (d<=1) return Math.round(d*1000)+"m";
}
if(um=="ft"){
    if ((d/5280)>=1) return Math.round((d/5280))+"mi";
    else if ((d/5280)<1) return Math.round(d)+"ft";
}
return d;
}





    </script>



    <script type="text/javascript">



        $("#info_button").hover(function(){
        $("#info_content").css("display", "block");
        }, function(){
        $("#info_content").css("display", "none");
        });
        


        

        var value_added = $("#property_value_added").find(":selected").index();
        console.log("Value Added");
        console.log(value_added);


        $("#property_value_added").ready(function() {
                    console.log("selected");
                    console.log($('option:selected', this).text() );
                    var selected = $('option:selected', this).text();
                    console.log(selected.includes("Water"));
         });



        
                $("#property_value_added").click(function(){
                    alert("button");
                }); 
        

            function showSelectedValue()
            {
                alert("button");
            }
      
                $(".value_added").click(function(){
                    alert("button");
                }); 
        

        

        $(function() {
                $("#property_value_added").change(function(e) {
                    console.log("selected");
                    console.log($('option:selected', this).text() );
                    var selected = $('option:selected', this).text();
                    //$('span:contains("Electricity")').css('background-color', 'red');

                    if(selected.includes("Water"))
                    {
                        e.preventDefault();
                        $('span:contains("Water Supply")').filter(function(){return $(this).text() === "Water Supply"}).parent().parent().addClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("Electricity"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("Electricity Supply")').filter(function(){return $(this).text() === "Electricity Supply"}).parent().parent().addClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                    }

                    if(!selected.includes("Water"))
                    {
                        e.preventDefault();
                        $('span:contains("Water Supply")').filter(function(){return $(this).text() === "Water Supply"}).parent().parent().removeClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(!selected.includes("Electricity"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("Electricity Supply")').filter(function(){return $(this).text() === "Electricity Supply"}).parent().parent().removeClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                    }
                });
        });



        $(function() {
                $("#property_value_added").ready(function(e) {
                    console.log("selected");
                    console.log($('option:selected', this).text() );
                    var selected = $('option:selected', this).text();
                    //$('span:contains("Electricity")').css('background-color', 'red');

                    if(selected.includes("Water"))
                    {
                        
                        $('span:contains("Water Supply")').filter(function(){return $(this).text() === "Water Supply"}).parent().parent().addClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("Electricity"))
                    {
                        
                        
                        $('span:contains("Electricity Supply")').filter(function(){return $(this).text() === "Electricity Supply"}).parent().parent().addClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                    }

                    if(!selected.includes("Water"))
                    {
                        
                        $('span:contains("Water Supply")').filter(function(){return $(this).text() === "Water Supply"}).parent().parent().removeClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(!selected.includes("Electricity"))
                    {
                        
                        
                        $('span:contains("Electricity Supply")').filter(function(){return $(this).text() === "Electricity Supply"}).parent().parent().removeClass('disabled');
                        //$('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                    }
                });
        });

        var habitable_floors = $('#property_types').find(":selected").index();
            console.log("habitable floors");
            console.log(habitable_floors);



            $(function() {
                $("#property_types").ready(function(e) {
                    console.log("selected");
                    console.log($('option:selected', this).text() );
                    var selected = $('option:selected', this).text();
                    //$('span:contains("Cellar")').css('background-color', 'red');
                    
                    if(selected.includes("Cellar"))
                    {
                        
                        $('span:contains("Zinc House")').filter(function(){return $(this).text() === "Zinc House"}).parent().parent().addClass('disabled');
                        $('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("Zinc House"))
                    {
                        
                        
                        $('span:contains("Cellar")').filter(function(){return $(this).text() === "Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                    }
                    if(selected.includes("Flat"))
                    {
                        
                        
                        $('span:contains("Cellar")').filter(function(){return $(this).text() === "Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains("Zinc House")').filter(function(){return $(this).text() === "Zinc House"}).parent().parent().addClass('disabled');
                    }
                    if(!selected.length)
                    {
                        $('span:contains("Cellar")').filter(function(){return $(this).text() === "Cellar"}).parent().parent().removeClass('disabled');
                        $('span:contains("Zinc House")').filter(function(){return $(this).text() === "Zinc House"}).parent().parent().removeClass('disabled');
                        $('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().removeClass('disabled');
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().removeClass('disabled');
                    }
                    if(selected.includes("2 Storey"))
                    {
                        
                        
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("3 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("4 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("5 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("6 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("7 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("8 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("9 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("10 Storey"))
                    {
                        
                        
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }

                });
            });

        
            $(function() {
                $("#property_types").change(function(e) {
                    console.log("selected");
                    console.log($('option:selected', this).text() );
                    var selected = $('option:selected', this).text();
                    //$('span:contains("Cellar")').css('background-color', 'red');
                    
                    if(selected.includes("Cellar"))
                    {
                        e.preventDefault();
                        $('span:contains("Zinc House")').filter(function(){return $(this).text() === "Zinc House"}).parent().parent().addClass('disabled');
                        $('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("Zinc House"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("Cellar")').filter(function(){return $(this).text() === "Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().addClass('disabled');
                    }
                    if(selected.includes("Flat"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("Cellar")').filter(function(){return $(this).text() === "Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains("Zinc House")').filter(function(){return $(this).text() === "Zinc House"}).parent().parent().addClass('disabled');
                    }
                    if(!selected.length)
                    {
                        $('span:contains("Cellar")').filter(function(){return $(this).text() === "Cellar"}).parent().parent().removeClass('disabled');
                        $('span:contains("Zinc House")').filter(function(){return $(this).text() === "Zinc House"}).parent().parent().removeClass('disabled');
                        $('span:contains("Flat")').filter(function(){return $(this).text() === "Flat"}).parent().parent().removeClass('disabled');
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().removeClass('disabled');
                    }
                    if(selected.includes("2 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("3 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("4 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("5 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("6 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("7 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("8 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("9 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("10 Storey")').filter(function(){return $(this).text() === "10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes("10 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains("2 Storey")').filter(function(){return $(this).text() === "2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("3 Storey")').filter(function(){return $(this).text() === "3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("4 Storey")').filter(function(){return $(this).text() === "4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("5 Storey")').filter(function(){return $(this).text() === "5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("6 Storey")').filter(function(){return $(this).text() === "6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("7 Storey")').filter(function(){return $(this).text() === "7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("8 Storey")').filter(function(){return $(this).text() === "8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("9 Storey")').filter(function(){return $(this).text() === "9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains("11 Storey")').filter(function(){return $(this).text() === "11 Storey"}).parent().parent().addClass('disabled');
                        
                    }

                });
            });





            var total_no_floors = $('#property_types_total').find(":selected").index();
            console.log("Total no of floors");
            console.log(total_no_floors);


            $(function() {
                $("#property_types_total").ready(function(e) {
                    console.log("selected");
                    console.log($('option:selected', this).text() );
                    var selected = $('option:selected', this).text();
                    //$('span:contains("Cellar")').css('background-color', 'red');
                    
                    if(selected.includes(".Cellar"))
                    {
                        
                        $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().addClass('disabled');
                        $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".Zinc House"))
                    {
                        
                        
                        $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().addClass('disabled');
                    }
                    if(selected.includes(".Flat"))
                    {
                        
                        
                        $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().addClass('disabled');
                    }
                    if(!selected.length)
                    {
                        $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().removeClass('disabled');
                        $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().removeClass('disabled');
                        $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().removeClass('disabled');
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().removeClass('disabled');
                    }
                    if(selected.length > 1)
                    {
                        if(selected.includes(".Cellar"))
                        {
                            $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().removeClass('disabled');
                            
                           
                            
                        }
                        if(selected.includes(".Zinc House"))
                        {
                            
                            $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().removeClass('disabled');
                            
                            
                        }
                        if(selected.includes(".Flat"))
                        {
                            
                            $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().removeClass('disabled');
                            
                        }
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().removeClass('disabled');
                    }
                    if(selected.includes(".2 Storey"))
                    {
                        
                        
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".3 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".4 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".5 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".6 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".7 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".8 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".9 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".10 Storey"))
                    {
                        
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }

                });
            });
        
            $(function() {
                $("#property_types_total").change(function(e) {
                    console.log("selected");
                    console.log($('option:selected', this).text() );
                    var selected = $('option:selected', this).text();
                    //$('span:contains("Cellar")').css('background-color', 'red');
                    
                    if(selected.includes(".Cellar"))
                    {
                        e.preventDefault();
                        $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().addClass('disabled');
                        $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".Zinc House"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().addClass('disabled');
                    }
                    if(selected.includes(".Flat"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().addClass('disabled');
                        $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().addClass('disabled');
                    }
                    if(!selected.length)
                    {
                        $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().removeClass('disabled');
                        $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().removeClass('disabled');
                        $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().removeClass('disabled');
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().removeClass('disabled');
                    }
                    if(selected.length > 1)
                    {

                        if(selected.includes(".Cellar"))
                        {
                            e.preventDefault();
                            $('span:contains(".Cellar")').filter(function(){return $(this).text() === ".Cellar"}).parent().parent().removeClass('disabled');
                            
                           
                            
                        }
                        if(selected.includes(".Zinc House"))
                        {
                            e.preventDefault();
                            $('span:contains(".Zinc House")').filter(function(){return $(this).text() === ".Zinc House"}).parent().parent().removeClass('disabled');
                            
                            
                        }
                        if(selected.includes(".Flat"))
                        {
                            e.preventDefault();
                            $('span:contains(".Flat")').filter(function(){return $(this).text() === ".Flat"}).parent().parent().removeClass('disabled');
                            
                        }
                        
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().removeClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().removeClass('disabled');
                    }
                    if(selected.includes(".2 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".3 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".4 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".5 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".6 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".7 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".8 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".9 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".10 Storey")').filter(function(){return $(this).text() === ".10 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }
                    if(selected.includes(".10 Storey"))
                    {
                        
                        e.preventDefault();
                        $('span:contains(".2 Storey")').filter(function(){return $(this).text() === ".2 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".3 Storey")').filter(function(){return $(this).text() === ".3 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".4 Storey")').filter(function(){return $(this).text() === ".4 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".5 Storey")').filter(function(){return $(this).text() === ".5 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".6 Storey")').filter(function(){return $(this).text() === ".6 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".7 Storey")').filter(function(){return $(this).text() === ".7 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".8 Storey")').filter(function(){return $(this).text() === ".8 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".9 Storey")').filter(function(){return $(this).text() === ".9 Storey"}).parent().parent().addClass('disabled');
                        $('span:contains(".11 Storey")').filter(function(){return $(this).text() === ".11 Storey"}).parent().parent().addClass('disabled');
                        
                    }

                });
            });



        $(document).ready(function () {

            jQuery("a#deleteMeter").on('click', function () {
                var url = jQuery(this).attr('href');
                var $this = jQuery(this);

                $this.text('Processing...');

                jQuery.ajax({
                    method: 'get',
                    url: url,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success == true) {
                            $this.closest("#delete-row").remove();
                        }
                    }
                });

                return false;

            });


            $('#landloard-button').on('click', function () {
                $(this).hide();
                $('#landloard-save').show();
                $('#landloard-cancel').show();
                $('.landlord-edit').show();
                $('.landlord-view').hide();

                $("select#ward").trigger('change');

            });
            $('#landloard-cancel').on('click', function () {
                $(this).hide();
                $('#landloard-save').hide();
                $('#landloard-button').show();
                $('.landlord-edit').hide();
                $('.landlord-view').show();
            });


            $('#property-button').on('click', function () {
                $(this).hide();
                $('#property-save').show();
                $('#property-cancel').show();
                $('.property-edit').show();
                $('.property-view').hide();

                $("select#ward_1").trigger('change');


            });
            $('#property-cancel').on('click', function () {
                $(this).hide();
                $('#property-save').hide();
                $('#property-button').show();
                $('.property-edit').hide();
                $('.property-view').show();
            });

            $('#occupancy-button').on('click', function () {
                $(this).hide();
                $('#occupancy-save').show();
                $('#occupancy-cancel').show();
                $('.occupancy-edit').show();
                $('.occupancy-view').hide();

            });
            $('#occupancy-cancel').on('click', function () {
                $(this).hide();
                $('#occupancy-save').hide();
                $('#occupancy-button').show();
                $('.occupancy-edit').hide();
                $('.occupancy-view').show();
            });
            

            $('body #assessment-button').on('click', function () {
                $(this).hide();
                $(this).closest(".assessment-item").find('#assessment-save').show();
                $(this).closest(".assessment-item").find('#assessment-cancel').show();
                $(this).closest(".assessment-item").find('.assessment-edit').show();
                $(this).closest(".assessment-item").find('.assessment-view').hide();

            });
            $('body #assessment-cancel').on('click', function () {
                $(this).hide();
                $(this).closest(".assessment-item").find('#assessment-save').hide();
                $(this).closest(".assessment-item").find('#assessment-button').show();
                $(this).closest(".assessment-item").find('.assessment-edit').hide();
                $(this).closest(".assessment-item").find('.assessment-view').show();
            });

            $('#geo-registry-button').on('click', function () {
                $(this).hide();
                $('#geo-registry-save').show();
                $('#geo-registry-cancel').show();
                $('.geo-registry-edit').show();
                $('.geo-registry-view').hide();

            });
            $('#geo-registry-cancel').on('click', function () {
                $(this).hide();
                $('#geo-registry-save').hide();
                $('#geo-registry-button').show();
                $('.geo-registry-edit').hide();
                $('.geo-registry-view').show();
            });


            $("body #no_of_mast, body #no_of_shop, body #property_categories, body  #property_types, body #property_council_adjustments,body #property_wall_materials_2022, body #roofs_materials_2022, body #property_length_2022,body #property_window_type_2022,body #property_sanitation_2022,body #property_breadth_2022, body #property_value_added, body #property_use, body #zone, body #swimming_pool, body  #gated_community").on('change keyup', function () {

                var self = $(this);
                var context = self.closest('.assessment-edit');

                var val_cat = context.find("#property_categories").val();
                var val_added = context.find("#property_value_added").val();

                if (val_cat == 6) {
                    context.find("#div_no_of_compound_house").removeClass('hidden');
                    context.find("#div_compound_name").removeClass('hidden');
                } else {
                    context.find("#div_no_of_compound_house").addClass('hidden');
                    context.find("#div_compound_name").addClass('hidden');
                    context.find("#no_of_compound_house").val('');
                    context.find("#compound_name").val('');
                }
                if (Array.isArray(val_added) && val_added.indexOf('9') != -1) {
                    context.find("#div_no_of_shop").removeClass('hidden');
                } else {
                    context.find("#div_no_of_shop").addClass('hidden');
                    context.find("#no_of_shop").val('');
                }

                if (Array.isArray(val_added) && val_added.indexOf('8') != -1) {
                    context.find("#div_no_of_mast").removeClass('hidden');
                } else {
                    context.find("#div_no_of_mast").addClass('hidden');
                    context.find("#no_of_mast").val('');
                }


                var property_categories = context.find('#property_categories').val();

                var property_types = context.find('#property_types').val();
                var property_council_adjustments = context.find('#property_council_adjustments').val();
                var property_council_group_name = context.find('#property_council_group_name_2022').val();
                console.log('---------------------------');
                console.log(property_council_adjustments);
                console.log(property_council_group_name);
                console.log('---------------------------');
                var property_wall_materials = context.find('#property_wall_materials_2022').val();
                var roofs_materials = context.find('#roofs_materials_2022').val();
                //var property_dimension = context.find('#property_dimension').val();
                var property_length = context.find('#property_length_2022').val();
                var property_breadth = context.find('#property_breadth_2022').val();
                var property_dimension = context.find('#property_square_meter_2022').val();
                var property_value_added = context.find('#property_value_added').val();
                var property_window_type = context.find('#property_window_type_2022').val();
                var property_use = context.find('#property_use').val();
                var zone = context.find('#zone').val();
                var property_sanitation = context.find('#property_sanitation_2022').val();
                console.log("property_sanitation");
                console.log(property_sanitation);
                var total_shops = context.find('input[name="no_of_shop"]').val();
                var total_mast = context.find('input[name="no_of_mast"]').val();
                var swimming_pool = context.find('#swimming_pool').val();
                var gated_community = context.find('#gated_community').val();
                var property_id = jQuery('input[name="property_id"]').val();

                //var ward = jQuery(this).val();
                // if(!property_length || property_length==0){
                //     //alert("Property length can not be null")
                //     property_length = null;
                //     property_breadth = null;
                //     context.find('#property_square_meter').val('')
                //     return null;
                // }else if(!property_breadth || property_breadth==0){
                //     //alert("Property Breadth can not be null")
                //     property_length = null;
                //     property_breadth = null;
                //     context.find('#property_square_meter').val('')
                //     return null;
                // }else{
                //     context.find('#property_square_meter').val(property_length*property_breadth);
                // }
                if(!(property_length>0 && property_breadth>0)){
                    context.find('#property_square_meter').val('') 
                }else{
                     context.find('#property_square_meter').val(property_length*property_breadth);
                }

                if((property_length>0 && !(property_breadth>0)) || (property_breadth>0 && !(property_length>0))){
                    property_length = null;
                    property_breadth = null;
                    context.find('#property_square_meter').val(null)  
                    return null;                  
                }else{
                     context.find('#property_square_meter').val(property_length*property_breadth);
                }

                context.find('.property_rate_without_gst').html('....');
                context.find('.property_gst').html('....');
                context.find('.property_rate_with_gst').html('....');

                var url = '{{route('admin.calculation-new')}}';


                var posting = $.get(url, {
                    property_categories: property_categories,
                    property_types: property_types,
                    property_council_adjustments: property_council_adjustments,
                    property_council_group_name: property_council_group_name,
                    property_id:property_id,
                    property_wall_materials: property_wall_materials,
                    roofs_materials: roofs_materials,
                    property_length: property_length,
                    property_breadth: property_breadth,
                    property_dimension: property_dimension,
                    property_value_added: property_value_added,
                    property_use: property_use,
                    property_window_type: property_window_type,
                    zone: zone,
                    total_shops: total_shops,
                    total_mast: total_mast,
                    swimming_pool: swimming_pool,
                    gated_community: gated_community,
                    property_sanitation: property_sanitation
                });

                // Put the results in a div
                posting.done(function (data) {

                    context.find('.property_rate_without_gst').html('Le ' + data.formatWithoutGST);
                    context.find('.property_rate_without_gst_council').html('Le ' + data.formatWithoutGST_Council);
                    context.find('.property_gst').html('Le ' + data.formatGST);
                    context.find('.property_rate_with_gst').html('Le ' + data.formatWithGST);
                    context.find('input[name="property_rate_without_gst"]').val(data.rateWithoutGST);
                    context.find('input[name="property_gst"]').val(data.GST);
                    context.find('input[name="property_rate_with_gst"]').val(data.rateWithGST);
                    console.log("results----------------");
                    console.log(data);
                });
            });



            jQuery("#id_type").on('change', function () {

                if (jQuery(this).val() == 'other') {
                    jQuery('input[name="id_type"]').removeClass('hidden').removeAttr('disabled');
                } else {
                    jQuery('input[name="id_type"]').addClass('hidden').attr('disabled', true);
                }
            });

            jQuery("#organization_type").on('change', function () {

                if (jQuery(this).val() == 'Other') {
                    jQuery('input[name="organization_type"]').removeClass('hidden').removeAttr('disabled');
                } else {
                    jQuery('input[name="organization_type"]').attr("disabled", true);
                    jQuery('input[name="organization_type"]').addClass('hidden');
                }
            });

            @if(!empty(Session::get('id')))

            $('html, body').stop(true, true).delay(500).animate({
                scrollTop: ($("#{{Session::get('id')}}").offset().top - 100)
            }, 100);
            $("#{{Session::get('id')}}-button").trigger('click');

            @endif

            @if($errors->has('digital_address') || $errors->has('dor_lat_long'))
            context.find("#geo-registry-button").trigger('click');
            @endif
        


            jQuery('.pensioner_disc_check').click(function(){
                perdicyear = jQuery(this).attr('data-year');
                var assessment_id = '{{$property->assessment->id}}';
                var property_id = '{{$property->id}}';
                if(jQuery('#pensioner_disc_check_'+perdicyear).is(':checked'))
                {
                    const url_dev = "/mrms_git/mrms/public/apiv2/update/pensiondiscount";
                    const url_local = "/apiv2/update/pensiondiscount";
                    $.ajax({
                        url: url_local,
                        type: "get", //send it through get method
                        data: {
                            property_id: property_id,
                            assessment_id: assessment_id,
                            is_pension_set: 1
                        },
                        success: function(response) {
                            location.reload();
                            console.log(response);
                            //Do Something
                        },
                        error: function(xhr) {
                            //Do Something to handle error
                        }
                    });
                }else{
                    const url_dev = "/mrms_git/mrms/public/apiv2/update/pensiondiscount";
                    const url_local = "/apiv2/update/pensiondiscount";
                    $.ajax({
                        url: url_local,
                        type: "get", //send it through get method
                        data: { 
                            property_id: property_id,
                            assessment_id: assessment_id,
                            is_pension_set: 0
                        },
                        success: function(response) {
                            location.reload();
                            console.log(response);
                            //Do Something
                        },
                        error: function(xhr) {
                            //Do Something to handle error
                        }
                    });
                }
            })

            jQuery('.disability_disc_check').click(function(){
                perdicyear = jQuery(this).attr('data-year');
                var assessment_id = '{{$property->assessment->id}}';
                var property_id = '{{$property->id}}';
                if(jQuery('#disability_disc_check_'+perdicyear).is(':checked')) 
                {
                    const url_dev = "/mrms_git/mrms/public/apiv2/update/disabilitydiscount";
                    const url_local = "/apiv2/update/disabilitydiscount";
                    $.ajax({
                        url: url_local,
                        type: "get", //send it through get method
                        data: {
                            property_id: property_id,
                            assessment_id: assessment_id,
                            is_disability_set: 1
                        },
                        success: function(response) {
                            location.reload();
                            console.log(response);
                            //Do Something
                        },
                        error: function(xhr) {
                            //Do Something to handle error
                        }
                    });
                    
                }else {
                    const url_dev = "/mrms_git/mrms/public/apiv2/update/disabilitydiscount";
                    const url_local = "/apiv2/update/disabilitydiscount";
                    $.ajax({
                        url: url_local,
                        type: "get", //send it through get method
                        data: {
                            property_id: property_id,
                            assessment_id: assessment_id,
                            is_disability_set: 0
                        },
                        success: function(response) {
                            location.reload();
                            console.log(response);
                            //Do Something
                        },
                        error: function(xhr) {
                            //Do Something to handle error
                        }
                    });
                }
            })            

        });
        jQuery("#is_organization").on('click', function () {
            if (jQuery(this).is(':checked')) {
                jQuery(".organization").removeClass('hidden');
                jQuery(".personal").addClass('hidden');
            } else {
                jQuery(".organization").addClass('hidden');
                jQuery(".personal").removeClass('hidden');
            }
        });
    </script>

    <script>

        jQuery("#print-envelope-btn").on('click', function () {
            var year = jQuery('#demand-draft-year').val();
            var url = jQuery(this).attr('data-content');

            window.location.href = url + '/' + year

        });        

        jQuery("#print-demand-btn").on('click', function () {
            var year = jQuery('#demand-draft-year').val();
            var url = jQuery(this).attr('data-content');

            window.location.href = url + '/' + year

        });

        jQuery("#email-demand-btn").on('click', function () {
            var year = jQuery('#demand-draft-year').val();
            var url = jQuery(this).attr('data-content');

            window.location.href = url + '/' + year

        });
        jQuery(".sticker-btn").on('click', function () {
           // var year = jQuery('#demand-draft-year').val();
            var url = jQuery(this).attr('data-content');

            window.location.href = url

        });

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

        jQuery("#send-sms").on('click', function () {
          // if (confirm("Are you sure want to delete propertie(s)?")) {
          //   $('#download-form').submit();
          // }
          //   url = jQuery(this).attr('href');
            swal({
                    title: "Are you sure?",
                    text: "Do you want to send SMS to this landlord!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, send!",
                    closeOnConfirm: false,
                    confirmButtonColor: '#fb483a',
                },
                function () {
                    $('#landlord-sendsms').submit();
                    swal.close()
                });
            return false;

        });
    </script>


@endpush
