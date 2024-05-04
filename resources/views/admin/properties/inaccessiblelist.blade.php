




@extends('admin.layout.main')


@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>
    <!-- progress bar (not required, but cool) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css"/>
    <!-- bootstrap (required) -->
    <!-- date picker (required if you need date picker & date range filters) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <!-- grid's css (required) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}"/>
    
@endpush

@section('content')
    <style type="text/css">
        div.laravel-grid {
            margin-top: 10px !important;
        }
        .zoom {
        padding: 50px;
        transition: transform .2s; /* Animation */
        width: 200px;
        height: 200px;
        margin: 0 auto;
}

.zoom:hover {
  transform: scale(6); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
}
    </style>

     <div class="table-responsive grid-wrapper">
      
<h1> Inaccessible Properties</h1>

<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>


@if($property->count())
<button id="btnExport" onclick="exportReportToExcel(this)">EXPORT Properties</button>


<table class="table table-bordered table-hover" style="background-color: #FFF;">
    <thead class="thead">
        <tr>
            <th> id</th>
            <th> Enumerator</th>
            <th> Reason</th>
            <th> Image</th>
            <th> Location </th>
            <th>Open Location Code</th>
            <th> Visited On</th>
            <th> Action </th>
        </tr>
    </thead>
    <tbody>
       
    @foreach($property as $any_variable)
    <tr>
              <td> {{$any_variable->id}} </td>
              <td>{{$any_variable->enumerator}}</td>
              <td> {{$any_variable->reason}} </td>
              <td> <img class="zoom" width="250" height="250" src="{{$any_variable->getInaccessbileImagePathAttribute()}}" alt="Image"/>  </td>
              <td> <a type="button" class="btn btn-primary"  target="_blank" href="https://www.google.com/maps/place/{{$any_variable->inaccessbile_property_lat}},{{$any_variable->inaccessbile_property_long}}">Open Location</a></td>
              <td> <p>{{$any_variable->inaccessbile_property_lat}},{{$any_variable->inaccessbile_property_long}}</p></td>
              <td> {{$any_variable->created_at}} </td>
              <td> <button type="button" class="btn btn-success"> Close</button> </td>
          </tr>
    @endforeach
   </tbody>
</table>
@endif
    </div>

<script>

function exportReportToExcel() {
  let table = document.getElementsByTagName("table"); // you can use document.getElementById('tableId') as well by providing id to the table tag
  TableToExcel.convert(table[0], { // html code may contain multiple tables so here we are refering to 1st table tag
    name: `export.xlsx`, // fileName you could use any name
    sheet: {
      name: 'Sheet 1' // sheetName
    }
  });
}
    </script>
@stop