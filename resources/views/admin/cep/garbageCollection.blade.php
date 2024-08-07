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
                Garbage details
            </div>
        </div>
    </div>
    <div class="body">
        <div class="row">
            <div class="col-sm-12">
                <label for="">Date</label>
                <p>{{ $gc->date }}</p>
            </div>
            <div class="col-sm-12">
                <label for="">Slot</label>
                <p>{{ $gc->slot }}</p>
            </div>
            <div class="col-sm-12">
                <label for="">Lat Long</label>
                <p>{{ $gc->latlng }}</p>
            </div>
            <div class="col-sm-4">
                <label for="">User</label>
                <p>{{ $gc->get_user ? $gc->get_user->first_name : 'No user found' }}</p>
            </div>
            <div class="col-sm-4">
                <label for="">Garbage Image 1</label>
                @if ($gc->garbage_image_1)
                    
               
                <img style="width:100px;height:100px;" src="{{ asset('storage/'.$gc->garbage_image_1) }}" alt="">
                @endif
            </div>
            <div class="col-sm-4">
                <label for="">Garbage Image 2</label>
                @if ($gc->garbage_image_2)
                    
               
                <img style="width:100px;height:100px;" src="{{ asset('storage/'.$gc->garbage_image_2) }}" alt="">
                @endif
            </div>
        </div>
    
    </div>
</div>
@stop

