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
        /* .zoom:hover {
  transform: scale(1.5); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
} */
    </style>
     <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


     
                <div class="card">
                    <div class="row" style="padding:20px;">
                    <br>
                        <a  href="{{ route('admin.verify.property.rejected') }}" class="btn btn-large btn-danger pull-right" style="padding:10px; margin-left:10px;">Rejected</a>
                        <a  href="{{ route('admin.verify.property.approved') }}" class="btn btn-large btn-success pull-right" style="padding:10px; margin-left:10px;">Approved</a>
                        <a  href="{{ route('admin.verify.property') }}" class="btn btn-large btn-warning pull-right" style="padding:10px; margin-left:10px;">Pending</a>
                        <br>

                        
                        @if( $state == -2)
                            {!! Form::open(['method'=>'GET','url'=>route('admin.verify.property.rejected'),'class'=>'navbar-form navbar-left','role'=>'search'])  !!}
                        @endif
                        @if( $state == 1)
                            {!! Form::open(['method'=>'GET','url'=>route('admin.verify.property.approved'),'class'=>'navbar-form navbar-left','role'=>'search'])  !!}
                        @endif 
                        @if( $state == 0)
                            {!! Form::open(['method'=>'GET','url'=>route('admin.verify.property'),'class'=>'navbar-form navbar-left','role'=>'search'])  !!}
                        @endif
    

                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" name="search" placeholder="Search...">
                            <span class="input-group-btn">
                                    <button class="btn btn-default-sm" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                        </span>
                        </div>
                            
                    </div>
                    <div class="header">
                        <h2>
                            Properties Details
                        </h2>
                        
                    </div>
                    @if($properties->count())
                    <div class="row">
                        <div class="body table-responsive">
                            
                                @foreach($properties as $user)
                                <div class="card" style="padding:10px;">
                                    <h5 style="color:red;">Requested By: {{ $user->requested_by }}
                                    <h5>Property ID: {{ $user->id }}</h5>
                                    <div class="row">
                                            <div class="card-body col-xs-6 border">
                                                <div class="card-header">
                                                    <h5>Existing Records</h5>
                                                </div>
                                                <p class="card-text"><b>Old Street Number: </b> {{ $user->street_number }}</p>
                                                <p class="card-text"><b>Street Number: </b> {{ $user->street_numbernew }}</p>
                                                <p class="card-text"><b>Street Name: </b> {{ $user->street_name }}</p>
                                            </div>
                                            <div class="card-body col-xs-6 border">
                                                <div class="card-header">
                                                    <h5>Requested Update</h5>
                                                </div>
                                                <p class="card-text"><b>Old Street Number: </b> {{ $user->temp_street_number }}</p>
                                                <p class="card-text"><b>Street Number: </b> {{ $user->temp_street_numbernew }}</p>
                                                <p class="card-text"><b>Street Name: </b> {{ $user->temp_street_name }}</p>
                                            </div>
                                    </div>
                                            @if( $state == -2)
                                                <a href="{{ route('admin.verify.property.approve', $user->id) }}" class="btn btn-large btn-success">Approve</a>
                                            @endif
                                            @if( $state == 1)
                                                <a href="{{ route('admin.verify.property.reject', $user->id) }}" class="btn btn-large btn-danger">Reject</a>
                                            @endif 
                                            @if( $state == 0)
                                                <a href="{{ route('admin.verify.property.approve', $user->id) }}" class="btn btn-large btn-success">Approve</a>
                                                <a href="{{ route('admin.verify.property.reject', $user->id) }}" class="btn btn-large btn-danger">Reject</a>
                                            @endif   
                                            <div class="row">
                                               
                                                <div class="col-xs-2 border">
                                                <h5>Address Proof</h5>
                                                <img src="{{ $user->getAddressImagePathAttribute() }}" style="width:100%;cursor:zoom-in" onclick="loadImage('{{ $user->getAddressImagePath() }}')">
                                                </div>
                                                @if ( $user->requested_by != "cashier" )
                                                    <div class="col-xs-2 border">
                                                    <h5>Conveyance Proof</h5>
                                                    <img src="{{ $user->getConveyanceImagePathAttribute() }}" style="width:100%;cursor:zoom-in" onclick="loadImage('{{ $user->getConveyanceImagePath() }}')">
                                                    </div>
                                                @endif
                                            </div>
                                    
                                    
                                </div>
                                    
                                    
                                @endforeach
                                
                                
                           
                        </div>

                    </div>
                </div>

                <div id="imageModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content zoom">
                        <img src="" id="document_Image" style="width:100%; height:100%">
                        </div>
                    </div>
                </div>
            @endif

            @if ($properties->links())
            <div class="mt-4 p-4 box has-text-centered">
                {{ $properties->links() }}
            </div>
            @endif


    </div>
    </div>
@stop
@section('scripts')


    <script>
        // send csrf token (see https://laravel.com/docs/5.6/csrf#csrf-x-csrf-token) - this is required
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function loadImage(path)
        {
            console.log(path);
            $("#document_Image").attr("src",path);
            $('#imageModal').modal('toggle');
        }
        
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
            @hasanyrole('Super Admin|Admin')
                jQuery(".btn-outline-danger").show();
            @else
                jQuery(".btn-outline-danger").hide();
            @endhasanyrole
        })
    </script>

    <style>
        .pull-right.pull-left-hard {
            float: left !important;
        }
    </style>

@stop
