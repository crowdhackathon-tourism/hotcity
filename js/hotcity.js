var map;
var bounds;
var center;
var ne;
var sw;
var markers1 = [];

var drawingManager;
//var rectangle = null;
var staticlink = document.getElementById('staticurl');
var notification = document.getElementById('alertsuccess');
var donebtn = document.getElementById('done');
var loading = document.getElementById('loading');

var markers = [];
var info = [];


donebtn.onclick = function(){
 //if(rectangle!=null){
	 //console.log(bounds);
	 loading.className = "visible";
	 var rectanglepoints = [];
	 var zero_point = {lat:0,lng:0};
	 var median = {lat:0,lng:0};
	 
	 bounds = map.getBounds();
	 center = map.getCenter();
	 ne = bounds.getNorthEast();
	 sw = bounds.getSouthWest();
	 zero_point.lat = ne.lat() - sw.lat();
	 zero_point.lng = ne.lng() - sw.lng();
	 median.lat = (ne.lat() + sw.lat())/2;
	 median.lng = (ne.lng() + sw.lng())/2;
	 var latlng = {userid:localStorage.getItem('user'),nelat:ne.lat(),nelng:ne.lng(),swlat:sw.lat(),swlng:sw.lng(),centerlat:center.lat(),
	 centerlng:center.lng(),zero_point_lat:zero_point.lat,zero_point_lng:zero_point.lng,mlat:median.lat,mlng:median.lng,boundsnelat:ne.lat(),boundsnelng:ne.lng(),boundsswlat:sw.lat(),boundsswlng:sw.lng()};
	 uploadDataToServer(latlng);
}


function uploadDataToServer(latlng) {
	donebtn.className += " disabled";
    var xmlhttp;

    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            //document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
      			staticlink.href =  xmlhttp.responseText.split("/targets")[1];
      			//var fsqdetails = {userid:localStorage.getItem('user'),centerlat:latlng.centerlat,centerlng:latlng.centerlng};
      			//sendToFSQ(fsqdetails);
            requestPOIsAJAX();

        }
    }
    xmlhttp.open("POST", "utility.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/json");
    xmlhttp.send(JSON.stringify(latlng));
}


function requestPOIsAJAX() {
  console.log('requestPOIsAJAX');
    var xmlhttp;

    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		        notification.className = 'visible';
      			donebtn.className = "btn btn-success";
            var pois = JSON.parse(xmlhttp.responseText);
            //console.log(pois);
            showMarkersOnMap(pois);
            loading.className = "hidden";


        }
    }
    xmlhttp.open("POST", "POI.php", true);
    //xmlhttp.setRequestHeader("Content-type", "application/json");
    xmlhttp.send(null);
}


function showMarkersOnMap(pois) {
    //var bounds = new google.maps.LatLngBounds(sw, ne);
    console.log(pois);
    //console.log(pois.length);
    /*
    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 12,
      center: new google.maps.LatLng(38.2494, 21.7452),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
   */
    var i, marker;
    var counter = 0;

   for(i=0; i<pois.length; i++){
    //console.log(pois[i].id);

    var pos = new google.maps.LatLng( pois[i].lat, pois[i].lng);
    console.log(pos);
    //if(bounds.contains(pos)){
            console.log('point is inside bounds');
      
            marker = new google.maps.Marker({
              position: pos,
              map: map,
              icon: 'img/markers/'+pois[i].mastercat+'.png'
            });

            var infowindow = new google.maps.InfoWindow();

            var content = '<p><b>Name: </b>'+pois[i].name+'</p><br>'+
                          '<p><b>Category: </b>'+pois[i].categoryName+'</p><br>'+
                          '<p><b>Stars: </b>'+pois[i].rating+'/10</p><br>'+
                          '<p><b>Total checkins: </b>'+pois[i].checkins+'</p><br>';
                          

            google.maps.event.addListener(marker,'click', (function(marker, content, i){ 
              return function() {
                 infowindow.setContent(content);
                 infowindow.open(map,marker);
              };
            })(marker, content, i));

      //} 

   }//end for
   

}

google.maps.event.addDomListener(window, "load", function () {

	var mapOptions = {
			center: new google.maps.LatLng(38.246893,21.744196), //dasilio: 38.246893,21.744196  // Pl.Georgiou: 38.246708,21.734519
			zoom: 16,
			maxZoom: 16,
			minZoom: 16,
			draggable: true,
			panControl: false,
			scrollwheel: false,
			zoomControl: false,
			mapTypeControl: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	var input = document.getElementById('pac-input');
	//var createImageTarget = document.getElementById('done');
	var searchBox = new google.maps.places.SearchBox(input);
	map = new google.maps.Map(document.getElementById('map'), mapOptions);
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
	map.controls[google.maps.ControlPosition.TOP_RIGHT].push(donebtn);
	map.addListener('bounds_changed', function() {
		searchBox.setBounds(map.getBounds());
		notification.className = 'hidden';
	});

	searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();

		if (places.length == 0) {
		  return;
		}

    // Clear out the old markers.
    markers1.forEach(function(marker) {
      marker.setMap(null);
    });
    markers1 = [];

    // For each place, get the icon, name and location.
    var bounds = new google.maps.LatLngBounds();
    places.forEach(function(place) {
      var icon = {
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(25, 25)
      };

      // Create a marker for each place.
      markers1.push(new google.maps.Marker({
      map: map,
      icon: icon,
      title: place.name,
      position: place.geometry.location
      }));

      if (place.geometry.viewport) {
      // Only geocodes have viewport.
      bounds.union(place.geometry.viewport);
      } else {
      bounds.extend(place.geometry.location);
      }
    });
    map.fitBounds(bounds);
  });
  // [END region_getplaces]
});

