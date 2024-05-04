@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>RESET PASSWORD </h2>
    </div>
@endsection

@section('form')

    {!! Form::open(['route' => 'admin.account.update-password']) !!}

    <div class="row">
        <div class="col-sm-8">
            <div class="card">
                <div class="header">
                    <h2>RESET PASSWORD</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="label">Old Password</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="old_password" type="password" id="label">
                                </div>
                                @if ($errors->has('old_password'))
                                    <label class="error">{{ $errors->first('old_password') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="label">New Password</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="password" type="password" id="label">
                                </div>
                                @if ($errors->has('password'))
                                    <label class="error">{{ $errors->first('password') }}</label>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="label">Confirm Password</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="password_confirmation" type="password" id="label">
                                </div>
                                @if ($errors->has('password_confirmation'))
                                    <label class="error">{{ $errors->first('password_confirmation') }}</label>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

