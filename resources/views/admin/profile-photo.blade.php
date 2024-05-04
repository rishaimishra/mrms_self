@extends('admin.layout.main')

@section('content')
    <div class="block-header">
        <h2>Profile Update</h2>
    </div>
    <div class="row clearfix">
        <div class="col-sm-12">
            <div class="card">
                <div class="header">
                    Update your Profile Photo
                </div>
                <div class="body">
{!! Form::open(['method' => 'post','files' => true,]) !!}
                    <div class="form-group form-float">
                    {!! Form::file('image') !!}
                    <span class="small">Supported image type:jpeg,png,jpg</span>
                    @if ($errors->has('image'))
                        <label class="error">{{ $errors->first('image') }}</label>
                    @endif
                    </div>
                    <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
{!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    @stop