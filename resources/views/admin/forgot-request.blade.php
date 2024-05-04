@extends('admin.layout.main')

@section('content')
<div class="row clearfix">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="header">

                <h2>Forgot Password Request From App User</h2>

            </div>
            <div class="body">
                <div class="row">
                    {!! Form::open([ 'id' => 'search-request','method'=>'get']) !!}
                    <div class="col-sm-3">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text"name="user" value="{{$request->user}}" class="form-control" placeholder="Search By Email">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="username" value="{{$request->username}}" class="form-control" placeholder="Search By Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <select name="process">
                            <option value="">Select Status</option>
                            <option value="0" {{(isset($request->process) and $request->process==0)?'selected':''}}>Pending</option>
                            <option value="1" {{(isset($request->process) and $request->process==1)?'selected':''}}>Complete</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{route('admin.forgot.request')}}" class="btn btn-success">All Request</a>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="table-responsive">

                    @if(count($forgot_password_request)>0)
                        <table class="table table-hover dashboard-task-infos">
                            <thead>
                            <tr>

                                <th>Profile Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($forgot_password_request as $fg)
                                <tr>
                                    <td><img src="{{$fg->user->getImageUrl(50,50)}}"></td>
                                    <td>{{$fg->user->name}}</td>
                                    <td>{{$fg->user->email}}</td>
                                    <td>
                                        <span class="label {{$fg->process==1?'bg-green':'bg-red'}}">{{$fg->process==1?'Completed':'Pending'}}</span>
                                    </td>
                                    <td><button id="show-{{$fg->id}}" class="btn btn-primary waves-effect" data-type="prompt" {{$fg->process==1?'disabled':''}} >Reset Password</button> </td>
                                </tr>
                                @if($fg->process==0)

                                    <tr id="change-password-{{$fg->id}}" style="display: none;"><td colspan="6">{!! Form::open([ 'id' => 'change-password','route'=>'admin.change.password']) !!}<div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text"name="password" class="form-control" minlength="6" placeholder="Enter New Password" required>
                                                        <input type="hidden" name="id" value="{{$fg->user->id}}">
                                                        <script type="text/javascript">
                                                            $( document ).ready(function() {
                                                                $("#show-{{$fg->id}}").on('click',function () {
                                                                    $("#change-password-{{$fg->id}}").toggle();

                                                                })
                                                            });
                                                        </script>
                                                    </div>
                                                </div>
                                            </div><div class="col-sm-6"><button type="submit" class="btn btn-success  btn-lg waves-effect">Change</button></div> {!! Form::close() !!}</td> </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    @else

                        <p>There is no request.</p>

                    @endif
                </div>
            </div>
            <div class="header">
                {{ $forgot_password_request->links() }}
            </div>
        </div>
    </div>

</div>
@stop
