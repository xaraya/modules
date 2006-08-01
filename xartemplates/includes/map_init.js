var maps = new Array();
var gmaps__marker;
var gmaps__mapnumber;

function mapInit(mapdiv,centerLat,centerLong,zoom){
	var mapnumber = maps.length

	if(GBrowserIsCompatible()){
		if(!mapdiv.id){
			mapdiv = document.getElementById(mapdiv);
		}
	maps[mapnumber] = new GMap2(mapdiv);
	maps[mapnumber].addControl(new GLargeMapControl());
	var point = new GLatLng(centerLat,centerLong);
		maps[mapnumber].setCenter(point, zoom);

		offset = Math.pow(2,(4 - maps[mapnumber].getZoom()))
	}

	return mapnumber;
}

function createMarker(point){

	var marker = new GMarker(point,{icon:G_DEFAULT_ICON,draggable:true});

	marker.disableDragging();

	return marker;
}

function makeMarkerDraggable(marker){

	GEvent.addListener(marker, "dragend", function(point){
	document.getElementById(marker.latbox).value=marker.getPoint().lat();
	document.getElementById(marker.longbox).value=marker.getPoint().lng();
	});
	
	marker.enableDragging();
	
	return true;

}

function getGeoCode(mapnumber, addressbox, latbox, longbox){
// Uses addressbox value to lookup location and passes info to addAddressToMap function

	if(!addressbox.id){
		addressbox = document.getElementById(addressbox);
	}

	if(!latbox.id){
		latbox = document.getElementById(latbox);
	}

	if(!longbox.id){
		longbox = document.getElementById(longbox);
	}

	//Create global marker to be manipulated by callback, "addAddressToMap"
	gmaps__marker = createMarker(maps[mapnumber].getCenter());
	gmaps__marker.latbox = latbox.id;
	gmaps__marker.longbox = longbox.id;
	makeMarkerDraggable(gmaps__marker);
	maps[mapnumber].addOverlay(gmaps__marker);
	gmaps__mapnumber = mapnumber;
	geocoder = new GClientGeocoder();
	geocoder.getLocations(addressbox.value, addAddressToMap);
}

function addAddressToMap(response) {
//Adds new markers based on geocoder response.

      if (!response || response.Status.code != 200) {
        alert("Sorry, we were unable to geocode that address");
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],
                            place.Point.coordinates[0]);
	gmaps__marker.setPoint(point);
	if(gmaps__marker.latbox){
		document.getElementById(gmaps__marker.latbox).value = gmaps__marker.getPoint().lat();
		document.getElementById(gmaps__marker.longbox).value = gmaps__marker.getPoint().lng();
        }
        maps[gmaps__mapnumber].setCenter(point,place.AddressDetails.Accuracy + 6);
    }
}
