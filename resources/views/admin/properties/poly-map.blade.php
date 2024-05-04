@extends('admin.layout.main')


@section('content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header ">
                    <h2>
                        Map View
                    </h2>

                </div>
                <div class="body">
                    <div class="row">
                        <div id="map" style="height: 800px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuA1HA0cE6VXwO48-VNstt7x00yz5H6tE"></script>
    <script>
        var locations = {!! $points !!};

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 16,
            center: new google.maps.LatLng({{ $center }}),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;

        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: {
                    url: locations[i][4]
                }
            });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }
    </script>


@endpush


{{--@push('scripts')--}}
{{--        <script>--}}

{{--            // This example creates a simple polygon representing the Bermuda Triangle.--}}
{{--            var map--}}
{{--            var infoWindow;--}}
{{--            function initMap() {--}}
{{--                 map = new google.maps.Map(document.getElementById('map'), {--}}
{{--                    zoom: 13,--}}
{{--                    center: {!! $centers !!},--}}
{{--                    mapTypeId: 'terrain'--}}
{{--                });--}}

{{--                // Define the LatLng coordinates for the polygon's path.--}}
{{--                var triangleCoords = {!! $points !!};--}}

{{--                // Construct the polygon.--}}
{{--                var bermudaTriangle = new google.maps.Polygon({--}}
{{--                    paths: triangleCoords,--}}
{{--                    strokeColor: '#FF0000',--}}
{{--                    strokeOpacity: 0.8,--}}
{{--                    strokeWeight: 2,--}}
{{--                    fillColor: '#FF0000',--}}
{{--                    fillOpacity: 0.35--}}
{{--                });--}}
{{--                bermudaTriangle.setMap(map);--}}
{{--                bermudaTriangle.addListener('click', showArrays);--}}

{{--                infoWindow = new google.maps.InfoWindow;--}}
{{--            }--}}

{{--            function showArrays(event) {--}}
{{--                // Since this polygon has only one path, we can call getPath() to return the--}}
{{--                // MVCArray of LatLngs.--}}
{{--                var vertices = this.getPath();--}}

{{--                var contentString = '<b>polygon</b><br>' +--}}
{{--                    'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() +--}}
{{--                    '<br>';--}}

{{--                // Iterate over the vertices.--}}
{{--                for (var i =0; i < vertices.getLength(); i++) {--}}
{{--                    var xy = vertices.getAt(i);--}}
{{--                    contentString += '<br>' + 'Coordinate ' + i + ':<br>' + xy.lat() + ',' +--}}
{{--                        xy.lng();--}}
{{--                }--}}

{{--                // Replace the info window's content and position.--}}
{{--                infoWindow.setContent(contentString);--}}
{{--                infoWindow.setPosition(event.latLng);--}}

{{--                infoWindow.open(map);--}}
{{--            }--}}
{{--        </script>--}}
{{--    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3x-3X5Lc44lYKQEf01dxF3lyGeCHyWGg&callback=initMap"></script>--}}

{{--@endpush--}}
