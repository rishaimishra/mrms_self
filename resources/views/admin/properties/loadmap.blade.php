
    

   <!DOCTYPE html>
   <html>
      
       
      <head>
      <meta name="viewport" content="width=device-width, initial-scale=1">
       <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
      <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuA1HA0cE6VXwO48-VNstt7x00yz5H6tE"></script>
       <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuA1HA0cE6VXwO48-VNstt7x00yz5H6tE&v=3&sensor=false&libraries=geometry"></script>
       <script src="https://unpkg.com/measuretool-googlemaps-v3"></script>
         
         
      </head>
      
      <body>
         <!-- <div id = "map_canvas" style = "width:580px; height:400px;"></div> -->
         <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                   <div class="card">
                       <div class="body">
                           <div class="row">
                               <div id="map_canvas" style="height: 100vh"></div>
                           </div>
                       </div>
                       <button type="button"  style="display:none;" id="open" onClick="addruler()">
                                   Open Ruler
                       </button>
                      
                       <!-- <button type="button"  id="open" onClick="startMeasure()">
                               Start
                       </button>
                       <button type="button"  id="open" onClick="endMeasure()">
                               End
                       </button> -->
                       <br/>
                       <div class="modal-body">
                       <!-- <form class="w3-container">
                       <p>
                       <label for="exampleFormControlInput1">Enter Total Length</label>
                       <input class="w3-input" type="text" class="form-control" id="total_map_length" placeholder="Length in meters"></p>
                       <p>
                       <label for="exampleFormControlInput1">Enter Total Area</label>
                       <input class="w3-input" type="text" class="form-control" id="total_map_area" placeholder="Length in Sq Meters"></p>
                       </form> -->
                       
                   </div>
                   <!-- <div class="modal-footer">
                       <button type="button" class="btn btn-primary" onClick="get()">Save</button>
                   </div> -->
                   </div>
               </div>
           </div>
   
      </body>
      
   
   
      <script>
         console.log(location.href);
         var lat = location.search.split('&')[0].replace('?','');
         var long = location.search.split('&')[1];
         console.log(lat);
         console.log(long);
         console.log(document.getElementById('map_canvas'));
         
   
         var map = new google.maps.Map(document.getElementById('map_canvas'), {
               zoom: 16,
               center: new google.maps.LatLng(lat,long),
               mapTypeId: google.maps.MapTypeId.ROADMAP
           });
   
         var  measuretool = new MeasureTool(map, {
               showSegmentLength: true,
               tooltip: true,
               unit: MeasureTool.UnitTypeId.METRIC
           });
           var infowindow = new google.maps.InfoWindow();
               marker = new google.maps.Marker({
                       position: new google.maps.LatLng(lat, long),
                       map: map
                   });
   
                   google.maps.event.addListener(marker, 'click', (function (marker, i) {
                       return function () {
                           infowindow.setContent(lat,long);
                           infowindow.open(map, marker);
                           console.log(event.latLng.lat());
                           console.log(event.latLng.lng());
                       }
                   })(marker, i));
           
   
   function startMeasure() {
       measuretool.start();
   }
   
   function endMeasure() {
       measuretool.end();
   }
            function addruler() {
   
   var ruler1 = new google.maps.Marker({
       position: map.getCenter() ,
       map: map,
       draggable: true
   });
   
   var ruler2 = new google.maps.Marker({
       position: map.getCenter() ,
       map: map,
       draggable: true
   });
   
   var ruler1label = new Label({ map: map });
   var ruler2label = new Label({ map: map });
   ruler1label.bindTo('position', ruler1, 'position');
   ruler2label.bindTo('position', ruler2, 'position');
   
   var rulerpoly = new google.maps.Polyline({
       path: [ruler1.position, ruler2.position] ,
       strokeColor: "#FFFF00",
       strokeOpacity: .7,
       strokeWeight: 7
   });
   
   rulerpoly.setMap(map);
   
   ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
   ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
   
   
   google.maps.event.addListener(ruler1, 'drag', function() {
       rulerpoly.setPath([ruler1.getPosition(), ruler2.getPosition()]);
       ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
       ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
   });
   
   google.maps.event.addListener(ruler2, 'drag', function() {
       rulerpoly.setPath([ruler1.getPosition(), ruler2.getPosition()]);
       ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
       ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
   });
   
   google.maps.event.addListener(ruler1, 'dblclick', function() {
       ruler1.setMap(null);
       ruler2.setMap(null);
       ruler1label.setMap(null);
       ruler2label.setMap(null);
       rulerpoly.setMap(null);
   });
   
   google.maps.event.addListener(ruler2, 'dblclick', function() {
       ruler1.setMap(null);
       ruler2.setMap(null);
       ruler1label.setMap(null);
       ruler2label.setMap(null);
       rulerpoly.setMap(null);
   });
   
   
   }
   
   
   function distance(lat1,lon1,lat2,lon2) {
   var um = "km"; // km | ft (change the constant)
   var R = 6371;
   if (um=="ft") { R = 20924640; /* ft constant */ }
   var dLat = (lat2-lat1) * Math.PI / 180;
   var dLon = (lon2-lon1) * Math.PI / 180; 
   var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
       Math.cos(lat1 * Math.PI / 180 ) * Math.cos(lat2 * Math.PI / 180 ) * 
       Math.sin(dLon/2) * Math.sin(dLon/2); 
   var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
   var d = R * c;
   if(um=="km") {
       if (d>1) return Math.round(d)+"km";
       else if (d<=1) return Math.round(d*1000)+"m";
   }
   if(um=="ft"){
       if ((d/5280)>=1) return Math.round((d/5280))+"mi";
       else if ((d/5280)<1) return Math.round(d)+"ft";
   }
   return d;
   }
   
   
   
   
   window.postMessage("message", function(event) {
               var area = measuretool.area;
               var length = measuretool.length;
               // console.log(length);
               // console.log(area);
               var obj = {
                   area: area,
                   length: length
               }
               console.log(obj);
               var area = "example";
               return area;
   }, false);
   
   
           // function postMessage() {
           //     var area = measuretool.area;
           //     var length = measuretool.length;
           //     console.log(length);
           //     console.log(area);
           //     var obj = {
           //         area: area,
           //         length: length
           //     }
           //     console.log(obj);
           //     return obj;
           // }
   
           // function returnArea(area, length){
           //     var obj = {
           //         area: area,
           //         length: length
           //     }
           //     console.log(obj);
           //     return obj;
           // }
   </script>
   </html>