@extends('admin.layout.edit')

@section('content')
    @include('admin.layout.partial.alert')


    {!! Form::open(['files' => true, 'route' => 'admin.boundary.import']) !!}

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>

                        Boundary Delimitation

                    </h2>
                </div>
                <div class="body">

                    <div class="row">

                        <div class="col-sm-6">
                            <h6>Uplolad Boundary Delimitation:</h6>

                            {!! Form::file('select_file',['class'=>'form-control']) !!}
                            @if ($errors->has('select_file'))
                                <label class="error">{{ $errors->first('select_file') }}</label>
                            @endif
                            <p>*xls File Allow Only</p>
                        </div>
                    </div>

                    <button class="btn btn-primary waves-effect" type="submit">Save</button>
                </div>
            </div>
        </div>

    </div>
    {!! Form::close() !!}

@stop
