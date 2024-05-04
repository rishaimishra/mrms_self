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
                    <h2>Property Characteristic Value </h2>

                </div>
                <div class="body">
                    {!! Form::model($propertycharacteristicvalue, ['route' => ['admin.propertyCharacteristicValues.update', $propertycharacteristicvalue->id], 'method' => 'put', 'files' => true]) !!}

                    <div class="row">
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Name<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('name',$propertycharacteristicvalue->propertyCharacteristic->name) }}" name="name" required readonly>


                                </div>
                                @if ($errors->has('name'))
                                    <label class="error">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Group Name<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('group_name',$propertycharacteristicvalue->group_name) }}" name="group_name" required readonly>


                                </div>
                                @if ($errors->has('group_name'))
                                    <label class="error">{{ $errors->first('group_name') }}</label>
                                @endif
                            </div>
                        </div>                        
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Good (%)<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('good',$propertycharacteristicvalue->good) }}" name="good" required>
                                </div>
                                @if ($errors->has('good'))
                                        <label class="error">{{ $errors->first('good') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Average (%)<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('average',$propertycharacteristicvalue->average) }}" name="average" required>
                                </div>
                                @if ($errors->has('average'))
                                        <label class="error">{{ $errors->first('average') }}</label>
                                    @endif
                            </div>
                        </div>   
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Bad (%)<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('bad',$propertycharacteristicvalue->bad) }}" name="bad" required>
                                </div>
                                @if ($errors->has('bad'))
                                        <label class="error">{{ $errors->first('bad') }}</label>
                                    @endif
                            </div>
                        </div>                                            
                        <div class="clearfix"></div>

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
