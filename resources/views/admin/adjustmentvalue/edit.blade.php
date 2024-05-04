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
                    <h2>Adjustment Value </h2>

                </div>
                <div class="body">
                    {!! Form::model($adjustmentvalue, ['route' => ['admin.adjustmentValues.update', $adjustmentvalue->id], 'method' => 'put', 'files' => true]) !!}

                    <div class="row">
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Name<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('name',$adjustmentvalue->adjustment->name) }}" name="name" required readonly>


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
                                    <input type="text" class="form-control" value="{{ old('group_name',$adjustmentvalue->group_name) }}" name="group_name" required readonly>


                                </div>
                                @if ($errors->has('group_name'))
                                    <label class="error">{{ $errors->first('group_name') }}</label>
                                @endif
                            </div>
                        </div>                        
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Percentage Value<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('percentage',$adjustmentvalue->percentage) }}" name="percentage" required>


                                </div>
                                @if ($errors->has('percentage'))
                                        <label class="error">{{ $errors->first('percentage') }}</label>
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
