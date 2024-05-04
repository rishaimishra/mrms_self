@extends('admin.layout.main')

@section('content')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Enter Details</h2>

                </div>
                <div class="body">
                    {!! Form::open(['id' => 'create_user']) !!}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <textarea rows="5" name="first_name" class="form-control">{{ old('first_name') }}</textarea>
                                    <label class="form-label">First Name(Enter comma-separated first name)</label>
                                    @if ($errors->has('first_name'))
                                        <label class="error">{{ $errors->first('first_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <textarea rows="5" name="surname" class="form-control">{{ old('surname') }}</textarea>
                                    <label class="form-label">Last Name(Enter comma-separated surname)</label>
                                    @if ($errors->has('last_name'))
                                        <label class="error">{{ $errors->first('last_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <textarea rows="5" name="street_name" class="form-control">{{ old('street_name') }}</textarea>
                                    <label class="form-label">Street Name(Enter comma-separated street name)</label>
                                    @if ($errors->has('street_name'))
                                        <label class="error">{{ $errors->first('street_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
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
