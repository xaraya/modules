var maps = new Array();
var markers = new Array();
var polylines = new Array();
var gmaps__markernumber;
var gmaps__mapnumber;

function mapInit(mapdiv,centerLat,centerLong,zoom){
	var mapnumber = maps.length

	if(!GBrowserIsCompatible()){
		alert("Your Browser is not compatable with Google Maps");
		return false;
	}
	
	if(!mapdiv.id){
		mapdiv = document.getElementById(mapdiv);
	}
	
	map = new GMap2(mapdiv);
	map.addControl(new GLargeMapControl());
	var point = new GLatLng(centerLat,centerLong);
	map.setCenter(point, zoom);
	map.mapnumber = mapnumber;	
	
	maps[mapnumber] = map;	

	return mapnumber;
}

function createMarker(mapnumber,point){
	var markernumber = markers.length;

	var marker = new GMarker(point,{icon:G_DEFAULT_ICON,draggable:true});
	marker.disableDragging();
	marker.markernumber = markernumber;
	marker.mapnumber = mapnumber;

	markers[markernumber] = marker;

	return marker;
}

function makeMarkerDraggable(marker){

	GEvent.addListener(marker, "dragend", function(point){
	updateDataEntry(marker.mapnumber);
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
	var marker = createMarker(mapnumber,maps[mapnumber].getCenter());
	makeMarkerDraggable(marker);
	maps[mapnumber].addOverlay(marker);

	//Set up variables for callback to know which objects to manipulate	
	gmaps__mapnumber = mapnumber;
	gmaps__markernumber = marker.markernumber;

	geocoder = new GClientGeocoder();
	geocoder.getLocations(addressbox.value, getGeoCodeCallback__moveMarkerToAddress);
}

function getGeoCodeCallback__moveMarkerToAddress(response) {
//Adds new markers based on geocoder response.

      if (!response || response.Status.code != 200) {
        alert("Sorry, we were unable to geocode that address");
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],
                            place.Point.coordinates[0]);
	markers[gmaps__markernumber].setPoint(point);
        maps[gmaps__mapnumber].setCenter(point,place.AddressDetails.Accuracy + 6);

	//Update the form boxes
        updateDataEntry(markers[gmaps__markernumber].mapnumber);
    }
}

function makeMapDataEntryMap(mapnumber,address,latbox,longbox,latbox2,longbox2){
	//Create a GBounds object around the markers on the map
	//Create a polyline object which can be changed
	//Tie entry boxes to the map which are set everytime a marker is added, removed, or moved
	//Set up listeners
	if(!latbox.id){
		latbox = document.getElementById(latbox);
	}
	if(!longbox.id){
		longbox = document.getElementById(longbox);
	}
	if(!latbox2.id){
		latbox2 = document.getElementById(latbox2);
	}
	if(!longbox2.id){
		longbox2 = document.getElementById(longbox2);
	}

	maps[mapnumber].latbox = latbox.id;
	maps[mapnumber].longbox = longbox.id;
	maps[mapnumber].latbox2 = latbox2.id;
	maps[mapnumber].longbox2 = longbox2.id;
	maps[mapnumber].polyline = false;
	GEvent.addListener(map, "click", function(overlay,point){
	if(!overlay){
		var marker = createMarker(this.mapnumber,point);
		makeMarkerDraggable(marker);
		this.addOverlay(marker);
	}
	});

	//A New Marker Regenerates the Bounding box
	GEvent.addListener(maps[mapnumber], "addoverlay", function(overlay){
		//
		if(overlay.getPoint()){
			updateDataEntry(this.mapnumber);
		}
	});

}

function updateDataEntry(mapnumber){
	//Call this everytime there's a change.
		
	//Get the location of each marker on the map
	var points = new Array();
	for(i=0;i<markers.length;i++){
		if(markers[i].mapnumber == mapnumber){
			points[i] = markers[i].getPoint(); 
		}
	}
	
	//Define the bounds of the area
	var bounds = new GBounds(points);

	//Set the form elements
	document.getElementById(map.latbox).value = bounds.maxY;
	document.getElementById(map.longbox).value = bounds.maxX;
	if(markers.length == 1){
		//If there's only one marker fill these with 0, else we get static
		document.getElementById(map.latbox2).value = 0;
		document.getElementById(map.longbox2).value = 0;
	} else {
		document.getElementById(map.latbox2).value = bounds.minY;
		document.getElementById(map.longbox2).value = bounds.minX;
	}

	//Remove the old Polyline	
	if(map.polyline){
		map.removeOverlay(map.polyline);
	}

	//Draw the new Polyline
	map.polyline = new GPolyline([new GPoint(bounds.maxX, bounds.maxY), new GPoint(bounds.maxX, bounds.minY), new GPoint(bounds.minX, bounds.minY), new GPoint(bounds.minX,bounds.maxY), new GPoint(bounds.maxX,bounds.maxY)], "#00ff00", 5, 0.5);
	map.addOverlay(map.polyline);
  }