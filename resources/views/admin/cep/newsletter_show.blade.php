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
                <label for="">Headline</label>
                <p>{{ $hd->headline }}</p>
            </div>
            <div class="col-sm-12">
                <label for="">Headline Image</label>
                <br>
                @if ($hd->headline_img)
                    
               
                <img style="width:100px;height:100px;" src="{{ asset('storage/'.$hd->headline_img) }}" alt="">
                @endif
            </div>
            <div class="col-sm-12">
                <label for="">Story</label>
                <p>{{ $hd->story }}</p>
            </div>
            {{--  <div class="col-sm-4">
                <label for="">User</label>
                <p>{{ $hd->user_id ? $hd->user_id->first_name : 'No user found' }}</p>
            </div>  --}}
          
        </div>
        <div class="row">
            <p style="margin-left: 15px;"><label for="">Story Images</label></p>
            
            @foreach ($hd['HeadingImages'] as $s_images)
            <div class="col-md-4">
                <img style="width:100px;height:100px;" src="{{ asset('storage/'.$s_images->images) }}" alt="">
            </div>
            @endforeach
            
            
        </div>
    
    </div>
</div>
@stop

