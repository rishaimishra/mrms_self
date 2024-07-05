@extends('admin.layout.main')


@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Update Details</h2>

                </div>
                <div class="body">
                    {!! Form::open(['novalidate' => 'novalidate', 'id' => 'update_user','route' => 'admin.system-user.update']) !!}
                    <input type="hidden" name="id" value="{{isset($admin_user->id)==true?$admin_user->id:""}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="first_name"
                                           value="{{isset($admin_user->first_name)==true?$admin_user->first_name:""}}"
                                           required>
                                    <label class="form-label">First Name</label>
                                    @if ($errors->has('first_name'))
                                        <label class="error">{{ $errors->first('first_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="last_name"
                                           value="{{isset($admin_user->last_name)==true?$admin_user->last_name:""}}"
                                           required>
                                    <label class="form-label">Last Name</label>
                                    @if ($errors->has('last_name'))
                                        <label class="error">{{ $errors->first('last_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="email" class="form-control"
                                           value="{{isset($admin_user->email)==true?$admin_user->email:""}}" disabled
                                           required>
                                    <label class="form-label">Email</label>
                                    @if ($errors->has('email'))
                                        <label class="error">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="radio" name="gender" value="M" id="male" class="with-gap radio-col-blue"
                                       {{(isset($admin_user->gender)==true and $admin_user->gender=='M') ?'checked="checked"':''}} required>
                                <label for="male">Male</label>

                                <input type="radio" name="gender" value="F" id="female" class="with-gap radio-col-blue"
                                       {{(isset($admin_user->gender)==true and $admin_user->gender=='F') ?'checked="checked"':''}} required>
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
                                    <input type="text" class="form-control"
                                           value="{{isset($admin_user->username)==true?$admin_user->username:""}}"
                                           disabled required>
                                    <label class="form-label">Username</label>
                                    @if ($errors->has('username'))
                                        <label class="error">{{ $errors->first('username') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="password" class="form-control" minlength="6" name="password" required>
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
                                           value="{{isset($admin_user->street_number)==true?$admin_user->street_number:""}}"
                                           name="street_number">
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
                                           value="{{isset($admin_user->street_name)==true?$admin_user->street_name:""}}">
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
                                        value="{{$ward['ward']}}" {{(isset($admin_user->ward)==true and $admin_user->ward==$ward['ward'] )?'selected':''}}>
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
                                        value="{{$ward['constituency']}}" {{(isset($admin_user->constituency)==true and $admin_user->constituency==$ward['constituency']) ?'selected':''}}>
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
                                        value="{{$ward['section']}}" {{(isset($admin_user->section)==true and $admin_user->section==$ward['section']) ?'selected':''}}>
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
                                        value="{{$ward['chiefdom']}}" {{(isset($admin_user->chiefdom)==true and $admin_user->chiefdom==$ward['chiefdom']) ?'selected':''}}>
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
                                        value="{{$ward['district']}}" {{(isset($admin_user->district)==true and $admin_user->district==$ward['district']) ?'selected':''}}>
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
                                        value="{{$ward['province']}}" {{(isset($admin_user->province)==true and $admin_user->province==$ward['province']) ?'selected':''}}>
                                        {{$ward['province']}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('province'))
                                <label class="error">{{ $errors->first('province') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">User Type</label>
                            <select name="user_role" id="user_role" class="form-control show-tick">
                                <option value="">-- Select User Type --</option>
                                @hasrole('Super Admin')
                                    @foreach(Spatie\Permission\Models\Role::select('name')->where('id','!=',1)->where('guard_name','!=',"guest_user")->get() as $role)
                                    <option
                                        value="{{$role['name']}}" {{$admin_user->getRoleNames()[0]==$role['name'] ?'selected':''}}>
                                        {{ucfirst($role['name'])}}
                                    </option>
                                    @endforeach
                                @else
                                @foreach(Spatie\Permission\Models\Role::select('name')->where('id','!=',2)->where('guard_name','!=',"guest_user")->where('id','!=',1)->get() as $role)
                                    <option
                                        value="{{$role['name']}}" {{$admin_user->getRoleNames()[0]==$role['name'] ?'selected':''}}>
                                        {{ucfirst($role['name'])}}
                                    </option>
                                @endforeach
                                @endhasrole

                            </select>
                            @if ($errors->has('user_role'))
                                <label class="error">{{ $errors->first('user_role') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Assign District</label>
                            @hasrole('Super Admin')
                                <select name="assign_district" id="assign_district" class="form-control show-tick">

                                    <option value="">-- Select District Type --</option>

                                        @foreach($district as $key => $distric)
                                            <option value="{{$key}}" {{ old('assign_district',$admin_user->assign_district_id)==$key?'selected': ''}}>{{ucfirst($distric)}}</option>
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
                                {!! Form::checkbox('is_active', 1, old('is_active', $admin_user->is_active), ['class' => 'filled-in chk-col-blue', 'id' => 'is_active']) !!}
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
