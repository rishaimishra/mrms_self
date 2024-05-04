@extends('admin.layout.main')


@section('content')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Property Wall Materials</h2>

                </div>
                <div class="body">
                    {!! Form::open(['id' => 'create_property_wall_materials']) !!}
                    <input type="hidden" name="id" value="{{$pc->id}}">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" value="{{ old('label',$pc->label) }}" name="label" required>
                                    <label class="form-label">Name</label>
                                    @if ($errors->has('label'))
                                        <label class="error">{{ $errors->first('label') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" value="{{ old('value',$pc->value) }}" name="value" required>
                                    <label class="form-label">Value</label>
                                    @if ($errors->has('value'))
                                        <label class="error">{{ $errors->first('value') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="radio" name="is_active" value="1" id="active_1" {{ old('is_active',$pc->is_active)=='1'?'checked="checked"': ''}} class="with-gap radio-col-blue" required>
                                <label for="active_1">Active</label>

                                <input type="radio" name="is_active" value="0" id="active_0" {{ old('is_active',$pc->is_active)=='0'?'checked="checked"': ''}} class="with-gap radio-col-blue" required>
                                <label for="active_0" class="m-l-20">Not Active</label>
                                @if ($errors->has('is_active'))
                                    <label class="error">{{ $errors->first('is_active') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary waves-effect" type="submit">SAVE</button>
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
