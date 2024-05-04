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
                    <h2>Envalope Detail </h2>

                </div>
                <div class="body">
                    {!! Form::model($district, ['route' => ['admin.envelopes.update', $district->id], 'method' => 'put', 'files' => true]) !!}

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
                                    <input type="text" class="form-control" value="{{ old('council_name_envp',$district->council_name_envp) }}" name="council_name_envp" required>


                                </div>
                                @if ($errors->has('council_name_envp'))
                                        <label class="error">{{ $errors->first('council_name_envp') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Short Name<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('council_short_name_envp',$district->council_short_name_envp) }}" name="council_short_name_envp" required>


                                </div>
                                @if ($errors->has('council_short_name_envp'))
                                        <label class="error">{{ $errors->first('council_short_name_envp') }}</label>
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Address<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('council_address_envp',$district->council_address_envp) }}" name="council_address_envp" required>


                                </div>
                                @if ($errors->has('council_address_envp'))
                                    <label class="error">{{ $errors->first('council_address_envp') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Address 2</label>
                                    <input type="text" class="form-control" value="{{ old('council_address_envp2',$district->council_address_envp2) }}" name="council_address_envp2">
                                </div>
                                @if ($errors->has('council_address_envp2'))
                                    <label class="error">{{ $errors->first('council_address_envp2') }}</label>
                                @endif
                            </div>
                        </div>    
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Address 3</label>
                                    <input type="text" class="form-control" value="{{ old('council_address_envp3',$district->council_address_envp3) }}" name="council_address_envp3">
                                </div>
                                @if ($errors->has('council_address_envp3'))
                                    <label class="error">{{ $errors->first('council_address_envp3') }}</label>
                                @endif
                            </div>
                        </div>     
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Address 4</label>
                                    <input type="text" class="form-control" value="{{ old('council_address_envp4',$district->council_address_envp4) }}" name="council_address_envp4">
                                </div>
                                @if ($errors->has('council_address_envp4'))
                                    <label class="error">{{ $errors->first('council_address_envp4') }}</label>
                                @endif
                            </div>
                        </div>    
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Council Address 5</label>
                                    <input type="text" class="form-control" value="{{ old('council_address_envp5',$district->council_address_envp5) }}" name="council_address_envp5">
                                </div>
                                @if ($errors->has('council_address_envp5'))
                                    <label class="error">{{ $errors->first('council_address_envp5') }}</label>
                                @endif
                            </div>
                        </div>                                                                                   
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <span class="image">Primary Logo</span>

                                    <input type="file" name="primary_logo_envp">

                                </div>
                                @if ($errors->has('primary_logo_envp'))
                                        <label class="error">{{ $errors->first('primary_logo_envp') }}</label>
                                    @endif
                            </div>
                            <img src="{{$district->getPrimaryLogoEnvpUrl(100,100)}}" alt="">
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <span  class="image">Secondary Logo</span>
                                    <input type="file" name="secondary_logo_envp">

                                </div>
                                @if ($errors->has('secondary_logo_envp'))
                                        <label class="error">{{ $errors->first('secondary_logo_envp') }}</label>
                                    @endif
                            </div>
                            <img src="{{$district->getSecondaryLogoEnvpUrl(100,100)}}" alt="">
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
