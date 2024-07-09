@extends('admin.layout.main')


@section('content')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Update Details</h2>

                </div>
                <div class="body">
                    {!! Form::open(['novalidate' => 'novalidate', 'id' => 'update_user','route' => 'admin.app-user.update']) !!}
                    <input type="hidden" name="id" value="{{isset($app_user->id)==true?$app_user->id:""}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name"
                                           value="{{isset($app_user->name)==true?$app_user->name:""}}" required>
                                    <label class="form-label">Full Name</label>
                                    @if ($errors->has('name'))
                                        <label class="error">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="radio" name="gender" value="M" id="male" class="with-gap radio-col-blue"
                                       {{(isset($app_user->gender)==true and $app_user->gender=='M') ?'checked="checked"':''}} required>
                                <label for="male">Male</label>

                                <input type="radio" name="gender" value="F" id="female" class="with-gap radio-col-blue"
                                       {{(isset($app_user->gender)==true and $app_user->gender=='F') ?'checked="checked"':''}} required>
                                <label for="female" class="m-l-20">Female</label>
                                @if ($errors->has('gender'))
                                    <label class="error">{{ $errors->first('gender') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email', $app_user->email ?? null)  }}"
                                           required />
                                    <label class="form-label">Email</label>
                                    @if ($errors->has('email'))
                                        <label class="error">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="password" class="form-control" minlength="6" name="password" required />
                                    <label class="form-label">Password</label>
                                    @if ($errors->has('password'))
                                        <label class="error">{{ $errors->first('password') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control"
                                           value="{{isset($app_user->street_number)==true?$app_user->street_number:""}}"
                                           name="street_number" />
                                    <label class="form-label">Street Number</label>
                                    @if ($errors->has('street_number'))
                                        <label class="error">{{ $errors->first('street_number') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" name="street_name" class="form-control"
                                           value="{{isset($app_user->street_name)==true?$app_user->street_name:""}}" />
                                    <label class="form-label">Street Name</label>
                                    @if ($errors->has('street_name'))
                                        <label class="error">{{ $errors->first('street_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="form-label">Ward</label>
                            <select name="ward" id="ward" class="form-control show-tick">
                                <option value="">-- Please Select Ward --</option>
                                @foreach(App\Models\BoundaryDelimitation::select('ward')->distinct()->get() as $ward)
                                    <option
                                        value="{{$ward['ward']}}" {{(isset($app_user->ward)==true and $app_user->ward==$ward['ward'] )?'selected':''}}>
                                        {{$ward['ward']}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('ward'))
                                <label class="error">{{ $errors->first('ward') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Constituency</label>
                            <select name="constituency" id="constituency" class="form-control show-tick">
                                <option value="">-- Select Constituency --</option>
                                @foreach(App\Models\BoundaryDelimitation::select('constituency')->distinct()->get() as $ward)
                                    <option
                                        value="{{$ward['constituency']}}" {{(isset($app_user->constituency)==true and $app_user->constituency==$ward['constituency']) ?'selected':''}}>
                                        {{$ward['constituency']}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('constituency'))
                                <label class="error">{{ $errors->first('constituency') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Section</label>
                            <select name="section" id="section" class="form-control show-tick">
                                <option value="">-- Select Section --</option>
                                @foreach(App\Models\BoundaryDelimitation::select('section')->distinct()->get() as $ward)
                                    <option
                                        value="{{$ward['section']}}" {{(isset($app_user->section)==true and $app_user->section==$ward['section']) ?'selected':''}}>
                                        {{$ward['section']}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('section'))
                                <label class="error">{{ $errors->first('section') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Chiefdom</label>
                            <select name="chiefdom" id="chiefdom" class="form-control show-tick">
                                <option value="">-- Select Chiefdom --</option>
                                @foreach(App\Models\BoundaryDelimitation::select('chiefdom')->distinct()->get() as $ward)
                                    <option
                                        value="{{$ward['chiefdom']}}" {{(isset($app_user->chiefdom)==true and $app_user->chiefdom==$ward['chiefdom']) ?'selected':''}}>
                                        {{$ward['chiefdom']}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('chiefdom'))
                                <label class="error">{{ $errors->first('chiefdom') }}</label>
                            @endif

                        </div>


                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="form-label">District</label>
                            <select name="district" id="district" class="form-control show-tick">
                                <option value="">-- Select District --</option>
                                @foreach(App\Models\BoundaryDelimitation::select('district')->distinct()->get() as $ward)
                                    <option
                                        value="{{$ward['district']}}" {{(isset($app_user->district)==true and $app_user->district==$ward['district']) ?'selected':''}}>
                                        {{$ward['district']}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('district'))
                                <label class="error">{{ $errors->first('district') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Province</label>
                            <select name="province" id="province" class="form-control show-tick">
                                <option value="">-- Select Province --</option>
                                @foreach(App\Models\BoundaryDelimitation::select('province')->distinct()->get() as $ward)
                                    <option
                                        value="{{$ward['province']}}" {{(isset($app_user->province)==true and $app_user->province==$ward['province']) ?'selected':''}}>
                                        {{$ward['province']}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('province'))
                                <label class="error">{{ $errors->first('province') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Assign Council</label>
                            @hasrole('Super Admin')
                                <select name="assign_district" id="assign_district" class="form-control show-tick">

                                    <option value="">-- Select District Type --</option>

                                        @foreach($district as $key => $distric)
                                            <option value="{{$key}}" {{ old('assign_district',$app_user->assign_district_id)==$key?'selected': ''}}>{{ucfirst($distric)}}</option>
                                        @endforeach
                                </select>
                            @else
                                <select name="assign_district" id="assign_district" class="form-control show-tick" >
                                    <option value="{{request()->user()->assign_district_id}}" {{'selected'}}>{{ucfirst(request()->user()->assign_district)}}</option>
                                </select>
                            @endhasrole
                            @if ($errors->has('assign_district'))
                                <label class="error">{{ $errors->first('assign_district') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::checkbox('is_active', 1, old('is_active', $app_user->is_active), ['class' => 'filled-in chk-col-blue', 'id' => 'is_active']) !!}
                                <label for="is_active">Status</label>
                            </div>
                        </div>

                    </div>
                    <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('admin/js/pages/forms/form-validation.js') }}"></script>
    <script>
        $(document).ready(function(){
            $("select#ward").val($("select#ward").val()).change();
        });
    </script>
@endpush
