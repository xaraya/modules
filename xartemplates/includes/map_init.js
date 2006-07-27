	var map;
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

