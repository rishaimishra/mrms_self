@extends('admin.layout.main')


@section('content')
    <style>
        .image{

            font-weight: normal;
            color: #aaa;
            font-size: 12px;
        }
        h5{
            font-weight: normal;
            color: #aaa;
            font-size: 12px;
        }

    </style>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>District Detail </h2>

                </div>
                <div class="body">
                    {!! Form::model($district, ['route' => ['admin.districts.update', $district->id], 'method' => 'put', 'files' => true]) !!}

                    <div class="row">
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Name<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('name',$district->name) }}" name="name" required readonly>


                                </div>
                                @if ($errors->has('name'))
                                    <label class="error">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Name<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('council_name',$district->council_name) }}" name="council_name" required>


                                </div>
                                @if ($errors->has('council_name'))
                                        <label class="error">{{ $errors->first('council_name') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Short Name<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('council_short_name',$district->council_short_name) }}" name="council_short_name" required>


                                </div>
                                @if ($errors->has('council_short_name'))
                                        <label class="error">{{ $errors->first('council_short_name') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Address<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('council_address',$district->council_address) }}" name="council_address" required>


                                </div>
                                @if ($errors->has('council_address'))
                                    <label class="error">{{ $errors->first('council_address') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <span class="image">Primary Logo</span>

                                    <input type="file" name="primary_logo">

                                </div>
                                @if ($errors->has('primary_logo'))
                                        <label class="error">{{ $errors->first('primary_logo') }}</label>
                                    @endif
                            </div>
                            <img src="{{$district->getPrimaryLogoUrl(100,100)}}" alt="">
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <span  class="image">Secondary Logo</span>
                                    <input type="file" name="secondary_logo">

                                </div>
                                @if ($errors->has('secondary_logo'))
                                        <label class="error">{{ $errors->first('secondary_logo') }}</label>
                                    @endif
                            </div>
                            <img src="{{$district->getSecondaryLogoUrl(100,100)}}" alt="">
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Penalties Note</label><br>
                                    <textarea rows="2" name="penalties_note" class="form-control">{{ old('penalties_note',$district->penalties_note) }}</textarea>


                                </div>
                                    @if ($errors->has('penalties_note'))
                                        <label class="error">{{ $errors->first('penalties_note') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Warning Note</label><br>
                                    <textarea rows="2" name="warning_note" class="form-control">{{ old('warning_note',$district->warning_note) }}</textarea>


                                </div>
                                @if ($errors->has('warning_note'))
                                    <label class="error">{{ $errors->first('warning_note') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <span class="image">Chief Administrator Sign</span>

                                    <input type="file" name="chif_administrator_sign">

                                </div>
                                @if ($errors->has('chif_administrator_sign'))
                                        <label class="error">{{ $errors->first('chif_administrator_sign') }}</label>
                                    @endif
                            </div>
                            <img src="{{$district->getChifAdministratorSignUrl(100,100)}}" alt="">
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <span class="image">Ceo Sign</span>
                                    <input type="file" name="ceo_sign">

                                </div>
                                @if ($errors->has('ceo_sign'))
                                        <label class="error">{{ $errors->first('ceo_sign') }}</label>
                                    @endif
                            </div>
                            <img src="{{$district->getCeoSignUrl(100,100)}}" alt="">
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Enquiries Email<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('enquiries_email',$district->enquiries_email) }}" name="enquiries_email" required>


                                </div>
                                @if ($errors->has('enquiries_email'))
                                    <label class="error">{{ $errors->first('enquiries_email') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Enquiries Phone<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('enquiries_phone',$district->enquiries_phone) }}" name="enquiries_phone" required>


                                </div>
                                @if ($errors->has('enquiries_phone'))
                                        <label class="error">{{ $errors->first('enquiries_phone') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Enquiries Phone2<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('enquiries_phone2',$district->enquiries_phone2) }}" name="enquiries_phone2">


                                </div>
                                @if ($errors->has('enquiries_phone2'))
                                        <label class="error">{{ $errors->first('enquiries_phone2') }}</label>
                                    @endif
                            </div>
                        </div>
                        {{-- <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Feedback</label><br>
                                    <textarea rows="2" name="feedback" class="form-control">{{ old('feedback',$district->feedback) }}</textarea>

                                    @if ($errors->has('feedback'))
                                        <label class="error">{{ $errors->first('feedback') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" value="{{ old('sq_meter_value',$district->sq_meter_value) }}" name="sq_meter_value" required>
                                    <label class="form-label">Sq. Meter Value<span class="red">*</span></label>

                                </div>
                                @if ($errors->has('sq_meter_value'))
                                        <label class="error">{{ $errors->first('sq_meter_value') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                        <h5>Bank Details<span class="red">*</span></h5>
                        </div>
                        @if(old('bank_details') || $district->bank_details)
                            @foreach(old('bank_details',$district->bank_details) as $bkey => $bank_detail)

                                <div class="col-lg-12">
                                    @include('admin.common.input.bankdetails', ['key' => $bkey,'name' => $bank_detail['name'], 'account_name' => $bank_detail['account_name'], 'account_number' => $bank_detail['account_number'],'location' => isset($bank_detail['location'])? $bank_detail['location']: ''])
                                    {!! $errors->first('bank_details.0', '<p class="error">:message</p>') !!}
                                </div>
                            @endforeach
                        @else
                            <div class="col-lg-12">
                                @include('admin.common.input.bankdetails', ['key' => 0,'name' => "", 'account_name' => "", 'account_number' => "",'location' => "" ])
                                {!! $errors->first('bank_details.0', '<p class="error">:message</p>') !!}
                            </div>
                        @endif


                        @if(old('collection_point') || $district->collection_point)

                            @foreach(old('collection_point',$district->collection_point) as $ekey => $collection)

                                <div class="col-lg-12 collection-point">
                                    @include('admin.common.input.collection', ['collection_point' => $collection, 'key' => $ekey])
                                    {!! $errors->first('emails.' . $ekey, '<p class="error">:message</p>') !!}
                                </div>
                            @endforeach
                        @else
                            <div class="col-lg-12 collection-point">
                                @include('admin.common.input.collection')
                                {!! $errors->first('emails.*', '<p class="error">:message</p>') !!}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <div class="add-more-field">
                            <h5 for="">Other Collection Point2</h5>
                        </div>

                        @if(old('collection_point2') || $district->collection_point2)
                            @foreach(old('collection_point',$district->collection_point2) as $ekey2 => $collection2)
                                <div class="form-line">
                                    <input name="collection_point2[]" type="text" value="{{ !isset($collection2) ? '' : $collection2 }}" class="form-control" placeholder="Other Collection Point 2">
                                </div>
                            @endforeach
                        @else
                            <div class="form-line">
                                <input name="collection_point2[]" type="text" value="{{ !isset($collection_point) ? '' : $collection_point }}" class="form-control" placeholder="Other Collection Point 2">
                            </div>
                            <div class="form-line">
                                <input name="collection_point2[]" type="text" value="{{ !isset($collection_point) ? '' : $collection_point }}" class="form-control" placeholder="Other Collection Point 2">
                            </div>
                            <div class="form-line">
                                <input name="collection_point2[]" type="text" value="{{ !isset($collection_point) ? '' : $collection_point }}" class="form-control" placeholder="Other Collection Point 2">
                            </div>
                            <div class="form-line">
                                <input name="collection_point2[]" type="text" value="{{ !isset($collection_point) ? '' : $collection_point }}" class="form-control" placeholder="Other Collection Point 2">
                            </div>
                        @endif
                    </div>

                    <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('admin/js/pages/forms/form-validation.js') }}"></script>

@endpush
