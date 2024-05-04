@extends('admin.layout.main')

@section('title')
    {{$title}}
@stop

@section('page_title') {{$title}} @stop

@section('content')



        <div id="geo-registry" class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(['id'=>'assessment-form','route'=>'admin.properties.assign.save','files' => true]) !!}
                <div class="card">
                    <div class="header">
                        <div class="row">
                            <div class="col-md-8">
                                <h2>
                                    Property Assign
                                </h2>
                            </div>
                            <div class="col-md-4 text-right">
                                @hasanyrole('Super Admin|Admin|manager')

                                <button type="submit" id="geo-registry-save"
                                        class="btn btn-large btn-primary">Save
                                </button>

                                @endhasanyrole
                            </div>
                        </div>

                    </div>

                    <div  class="body geo-registry-edit">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Assessment Officer</label>
                                    <div class="form-line">
                                        {!! Form::select('assessment_officer', $assessmentOfficer ,$request->assessment_officer, ['class' => 'form-control']) !!}
                                    </div>
                                    {!! $errors->first('assessment_officer', '<span class="error">:message</span>'); !!}
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <h6>Dor Lat Long</h6>
                                <input type="test" name="dor_lat_long"
                                       value="{{ old('dor_lat_long') }}"
                                       class="form-control input">
                                {!! $errors->first('dor_lat_long', '<span class="error">:message</span>'); !!}
                            </div>

                            <div id="user_type_upload_file" class="form-group">
                                <label class="control-label">Upload File</label>
                                    <div class="upload-file">
                                        <input type="file" id="bulk_lat_long_file" name="bulk_lat_long_file"/>
                                        <label for="bulk_lat_long_file">
                                        <a href="{{ asset('uploads/sample_bulk_lat_long.xlsx') }}" download>Download Sample File</a>
                                        </label>
                                    </div>
                            </div>


                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>





@stop
