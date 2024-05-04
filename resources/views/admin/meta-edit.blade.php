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
                    <input type="hidden" name="id" value="{{$meta->id}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="value" value="{{old('value',$meta->value)}}">
                                    <label class="form-label">{{strtoupper(str_replace('_',' ',$meta->name))}}</label>
                                    @if ($errors->has('value'))
                                        <label class="error">{{ $errors->first('value') }}</label>
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
