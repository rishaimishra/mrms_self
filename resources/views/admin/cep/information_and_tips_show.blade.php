@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}" />
@endpush


@section('content')
<div class="card">
    <div class="header bg-orange">
        <div class="row">
            <div class="col-md-3">
                Information and tips details
            </div>
        </div>
    </div>
    <div class="body">
        <div class="row">
            <div class="col-sm-12">
                <label for="">Tips</label>
                <p>{{ $information->headline }}</p>
            </div>
           
            <div class="col-sm-12">
                <label for="">Status</label>
                <p>{{ $information->status }}</p>
            </div>
            <div class="col-sm-4">
                <label for="">User</label>
                @if ($information->user_id)
                <p>{{ $information->user_id ? $information->get_user->first_name : 'No user found' }}</p>
                @endif
                
            </div>
          
        </div>
       
    
    </div>
</div>
@stop

