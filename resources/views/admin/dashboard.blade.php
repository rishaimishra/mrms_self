@extends('admin.layout.main')

@section('content')
    <div class="block-header">
        <h2>DASHBOARD</h2>
    </div>

    <!-- Widgets -->
    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
            <a href="{{ url('back-admin/properties#properties') }}">
            @else
            <a href="#">
            @endrole
                <div class="info-box bg-blue hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">assessment</i>
                    </div>
                    <div class="content">
                        <div class="text">Total Assessments</div>
                        <div class="number count-to" data-from="0" data-to="125" data-speed="15"
                             data-fresh-interval="20">{{$total}}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ url('back-admin/properties?is_completed=yes&is_draft_delivered=1#properties') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-light-green hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">assignment_turned_in</i>
                    </div>
                    <div class="content">
                        <div class="text">Completed & Demand Note Delivered</div>
                        <div class="number count-to" data-from="0" data-to="257" data-speed="1000"
                             data-fresh-interval="20">{{$complete}}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ url('back-admin/properties?is_completed=no&is_draft_delivered=1#properties') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-orange hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">assignment_turned_in</i>
                    </div>
                    <div class="content">
                        <div class="text">Incompleted & Demand Note Delivered</div>
                        <div class="number count-to" data-from="0" data-to="257" data-speed="1000"
                             data-fresh-interval="20">{{$Incomplete_draft_delivered}}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ url('back-admin/properties?is_completed=yes&is_draft_delivered=0#properties') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-yellow hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">assignment_turned_in</i>
                    </div>
                    <div class="content">
                        <div class="text">Completed & Demand Note Not Delivered</div>
                        <div class="number count-to" data-from="0" data-to="257" data-speed="1000"
                             data-fresh-interval="20">{{$complete_not_delivered}}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ url('back-admin/properties?is_completed=no&is_draft_delivered=0#properties') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-red hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">assignment_late</i>
                    </div>
                    <div class="content">
                        <div class="text">In-Complete Assessment</div>
                        <div class="number count-to" data-from="0" data-to="243" data-speed="1000"
                             data-fresh-interval="20">{{$in_complete}}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ route('admin.app-user.list') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-indigo hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">person_add</i>
                    </div>
                    <div class="content">
                        <div class="text">Total Assessment Agent</div>
                        <div class="number count-to" data-from="0" data-to="1225" data-speed="1000"
                             data-fresh-interval="20">{{$app_user}}</div>
                    </div>
                </div>
            </a>
        </div>
        @role('Super Admin')
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ route('admin.tax-payer') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-grey hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">accessibility</i>
                    </div>
                    <div class="content">
                        <div class="text">Unique Property Owners</div>
                        <div class="number count-to" data-from="0" data-to="1225" data-speed="1000"
                             data-fresh-interval="20">{{$unique_property_owners}}</div>
                    </div>
                </div>
            </a>
        </div>
        @endrole
        @role('Super Admin')
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ route('admin.tax-payer') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-grey hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">accessibility</i>
                    </div>
                    <div class="content">
                        <div class="text">Total paid Properties</div>
                        <div class="number count-to" data-from="0" data-to="1225" data-speed="1000"
                             data-fresh-interval="20">{{$paid}}</div>
                    </div>
                </div>
            </a>
        </div>
        @endrole
        @role('Super Admin')
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            @role('Super Admin|Admin|manager')
                <a href="{{ route('admin.tax-payer') }}">
            @else
                <a href="#">
            @endrole
                <div class="info-box bg-grey hover-expand-effect" style="cursor: pointer">
                    <div class="icon">
                        <i class="material-icons">accessibility</i>
                    </div>
                    <div class="content">
                        <div class="text">Total un_paid Properties</div>
                        <div class="number count-to" data-from="0" data-to="1225" data-speed="1000"
                             data-fresh-interval="20">{{$un_paid}}</div>
                    </div>
                </div>
            </a>
        </div>
        @endrole
    </div>

    <style>
        .info-box-4 {
            cursor: pointer !important;
        }

        a {
            text-decoration: none !important;
        }
    </style>
@endsection

@push('scripts')

    <script src="{{ url('admin/js/pages/ui/dialogs.js') }}"></script>
    <script src="{{ url('admin/js/pages/app.js') }}"></script>
@endpush
