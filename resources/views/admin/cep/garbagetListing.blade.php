@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>

@endpush
@section('title')
    {{$title}}
@stop

@section('page_title') {{$title}} @stop

@section('content')
<style type="text/css">
    #search-property-grid{
        display: none;
    }
    div.laravel-grid {
        margin-top: 10px !important;
    }
    .pull-right{
        text-align: end;
    }
</style>
<div>
    {!! $grid !!}

</div>
@stop
