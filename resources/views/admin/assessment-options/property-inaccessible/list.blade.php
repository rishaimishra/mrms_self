@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <!-- progress bar (not required, but cool) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css" />

    <!-- date picker (required if you need date picker & date range filters) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <!-- grid's css (required) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}" />
@endpush

@section('content')
    <style type="text/css">
        div.laravel-grid {
            margin-top: 10px !important;
        }
    </style>
    {!! $grid !!}
@stop
@section('scripts')



    <script>
        // send csrf token (see https://laravel.com/docs/5.6/csrf#csrf-x-csrf-token) - this is required
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // for the progress bar (required for progress bar functionality)
        $(document).on('pjax:start', function () {
            NProgress.start();
        });
        $(document).on('pjax:end', function () {
            NProgress.done();
        });

        $(document).on('ready',function () {
            $(".delete-confirm").on('click',function () {

                let isBoss = confirm("Are you sure you want to delete this item?");

                return isBoss  // true if OK is pressed

            })

        })
    </script>

@stop
