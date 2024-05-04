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
                    <h2>Mill Rate </h2>

                </div>
                <div class="body">
                    {!! Form::model($millrate, ['route' => ['admin.millrates.update', $millrate->id], 'method' => 'put', 'files' => true]) !!}

                    <div class="row">
                        <div class="col-sm-7">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Rate<span class="red">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('name',$millrate->rate) }}" name="rate" required>


                                </div>
                                @if ($errors->has('rate'))
                                    <label class="error">{{ $errors->first('rate') }}</label>
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
