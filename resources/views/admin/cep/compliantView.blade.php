@extends('admin.layout.main')

@section('title')
    {{$title}}
@stop

@section('page_title') {{$title}} @stop

@section('content')
        <div class="card">
            <div class="header bg-orange">
                <div class="row">
                    <div class="col-md-3">
                        Complaint details
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="">Complain Type</label>
                        <p>{{ $complaint->type }}</p>
                    </div>
                    <div class="col-sm-12">
                        <label for="">Reason</label>
                        <p>{{ $complaint->reason }}</p>
                    </div>
                    <div class="col-sm-12">
                        <label for="">Additional Description</label>
                        <p>{{ $complaint->additional_description ? $complaint->additional_description : 'No data' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <label for="">Tags</label>
                        <p>{{ $complaint->tag ? $complaint->tag : 'No data' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <label for="">User</label>
                        <p>{{ $complaint->get_user ? $complaint->get_user->first_name : 'No user found' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <label for="">Type</label>
                        <p>{{ $complaint->type }}</p>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left: 15px;"><label for="">Complaint Images</label></p>
                    
                    @foreach ($complaint['ComplainImages'] as $c_images)
                    <div class="col-md-4">
                        <img style="width:100px;height:100px;" src="{{ asset('storage/'.$c_images->complain_image) }}" alt="">
                    </div>
                    @endforeach
                    
                </div>
                <div class="row" style="margin-top:20px">
                    <div class="col-md-12">
                        <button style="width: 170px;margin:0px 30px;" class="btn btn-primary">ACKNOWLEDGE RECITPT</button>
                        <button style="width: 170px;margin:0px 30px;" class="btn btn-primary">INVITE</button>
                        <button style="width: 170px;margin:0px 30px;" class="btn btn-danger" onclick="redirectToGoogleMaps()">NAVIGATE TO LOCATION</button>
                        <button style="width: 120px;margin:0px 30px;" class="btn btn-success"></button>
                        <button style="width: 120px;margin:0px 30px;" class="btn btn-warning"></button>
                    </div>
                </div>
            </div>
        </div>
@stop
<script>
    function redirectToGoogleMaps() {
        var latlong = '{{ $complaint->tag }}'
        var latlongArray = latlong.split(',');
        var latitude = latlongArray[0].trim();
        var longitude = latlongArray[1].trim();
        var url = `https://www.google.com/maps?q=${latitude},${longitude}`;
        window.location.href = url;
    }
</script>