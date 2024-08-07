@extends('admin.layout.main')


@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>
    <!-- progress bar (not required, but cool) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css"/>
    <!-- bootstrap (required) -->
    <!-- date picker (required if you need date picker & date range filters) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <!-- grid's css (required) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>

@endpush

@section('content')
    <style type="text/css">
        div.laravel-grid {
            margin-top: 10px !important;
        }
    </style>
   
           


    <div>
        {!! $grid !!}
    </div>


    
@stop
