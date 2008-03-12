/*
 * Provide a map centred on an address, in the div#project_map element.
 */

var xar_location_map = null;
var xar_location_geocoder = null;

jQuery(document).ready(function(){
	if (GBrowserIsCompatible()) {
		xar_location_map = new GMap2(document.getElementById("location_map"));
		xar_location_geocoder = new GClientGeocoder();
	}
});

/*
 * Call up xar_MapsShowAddressLocation() on document ready with an address and HTML
 * description for the marker.
 *
 * e.g. As a minimum in a template, this will display a map for number 11 Downing Street:-
 *
 * <div id="location_map" style="width: 300px; height: 300px;"></div>
 * <xar:template file="xar.googlemaps" module="jquery" />
 * <xar:base-include-javascript filename="location_map.js" module="jquery" />
 * <xar:set name="address">'10 Downing Street, SW1A, UK'</xar:set>
 * <xar:set name="desc">'The British Prime Minister'</xar:set>
 * <xar:base-include-javascript type="code" code="jQuery(document).ready(function(){if(GBrowserIsCompatible()){xar_MapsShowAddressLocation('$address','$desc');}});" />
 *
 * You don't need to add anything else to the page or the theme.
 * Including a data-driven Google Location map could be easier ;-)
 *
 * Note that a valid Google Maps key must be added to xarquery/xardata/googlemapskeys.txt
 *
 * The map and marker will then be inserted into div#location_map
 */

function xar_MapsShowAddressLocation(address, description) {
	if (xar_location_geocoder) {
		xar_location_geocoder.getLatLng(
			address,
			function(point) {
				if (!point) {
					alert(address + " not found");
				} else {
					xar_location_map.setCenter(point, 13);
					var marker = new GMarker(point, {draggable: true});
					xar_location_map.addOverlay(marker);
					GEvent.addListener(marker, "dragend", function() {
						marker.openInfoWindowHtml(description);
					});
					GEvent.addListener(marker, "click", function() {
						marker.openInfoWindowHtml(description);
					});
					/* Enable the click to show the marker balloon in its open state. */
					/*GEvent.trigger(marker, "click");*/
				}
			}
		);
	}
}
