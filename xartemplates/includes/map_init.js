var map;
var geocoder;
var button = 0;
var offset = 0;
function mapInit(mapdiv,centerLat,centerLong,zoom){
	if(GBrowserIsCompatible()){
		if(!mapdiv.id){
			mapdiv = document.getElementById(mapdiv);
		}
	map = new GMap2(mapdiv);
	map.addControl(new GLargeMapControl());
	var point = new GLatLng(centerLat,centerLong);
		map.setCenter(point, zoom);

		offset = Math.pow(2,(4 - map.getZoom()))
	}
}

////dragging engine
function move(marker){
	GEvent.addListener(map, "mousemove", function(pint){
		if (button ==1){
			marker.point = new GLatLng(pint.y-offset,pint.x);
			marker.redraw(true);
		}
	})
};

function createMarker(point){
	  var marker = new GMarker(point,{icon:G_DEFAULT_ICON,draggable: true});

	GEvent.addListener(marker, "dragend", function(point){
	document.getElementById("lat").value=marker.getPoint().lat();
	document.getElementById("lng").value=marker.getPoint().lng();
	});

	  return marker;
}

function addAddressToMap(response) {
      map.clearOverlays();
      if (!response || response.Status.code != 200) {
        alert("Sorry, we were unable to geocode that address");
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],
                            place.Point.coordinates[0]);
        marker = new GMarker(point,{draggable: true});
        map.addOverlay(marker);
        map.setCenter(point,place.AddressDetails.Accuracy + 6);
        marker.enableDragging();
    }
}
function getGeoCode(addressbox){
	if(!addressbox.id){
		addressbox = document.getElementById(addressbox);
	}

	geocoder = new GClientGeocoder();

	geocoder.getLocations(addressbox.value, addAddressToMap);
}