
async function updateMap ( newLatLng ) {
	
	const { Map } = await google.maps.importLibrary("maps");
	const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
	
	if ( newLatLng == null ) {
		
		var grid_ref;
		var lat;
		var lng;
		try{
			lat = parseFloat(BioDiv.latitude);
			lng = parseFloat(BioDiv.longitude);
		}
		catch(err){
			lat = 54.763213;
			lng = -1.581919; 
		}
		
		if ( (lat == 0 && lng == 0) || isNaN(lat) || isNaN(lng) ) {
			lat = 54.763213;
			lng = -1.581919;
		}
			
		newLatLng = new google.maps.LatLng(lat, lng);
		var p = new LatLon(lat, lng);
		var grid;
		try {
			grid = OsGridRef.latLonToOsGrid(p);
		}
		catch (err) {
			
		}
		jQuery('#grid_ref').val(grid.toString(8));
		jQuery('#latitude').val(lat.toPrecision(10));
		jQuery('#longitude').val(lng.toPrecision(10));
	}
	
	const myOptions = {
		zoom: 9,
		center: newLatLng,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		mapId: "DEMO_MAP_ID"
	}
	const map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	const marker = new google.maps.marker.AdvancedMarkerElement({
		position: newLatLng, 
		map: map,
		gmpDraggable: true
	});
	
	marker.addListener(
		'drag',
		function() {
			const lat = marker.position.lat;
			const lng = marker.position.lng;
			const p = new LatLon(lat, lng);
			let grid = 0;
			try {
					grid = OsGridRef.latLonToOsGrid(p);
			}
			catch (err) {
				console.log("Position does not have an OS equivalent");
				grid = 0;
			}
			document.getElementById('grid_ref').value = grid.toString(8);
			document.getElementById('latitude').value = lat.toPrecision(10);
			document.getElementById('longitude').value = lng.toPrecision(10);
		}
	);
				  
};
	
	
jQuery(document).ready(function () {
	
	jQuery('#grid_ref').change(function(){
		var new_grid_ref = jQuery('#grid_ref').val();
		var grid_ref = 0;
		try {
			grid_ref = OsGridRef.parse(new_grid_ref);
		}
		catch ( err ) {
			grid_ref = 0;
		}
		if ( grid_ref ) {
			var point = OsGridRef.osGridToLatLon(grid_ref);		
			var lat = point.lat.toPrecision(10);
			var lng = point.lon.toPrecision(10);
			jQuery('#latitude').val(lat);
			jQuery('#longitude').val(lng);	
			
			var latlng = new google.maps.LatLng(lat, lng);
			updateMap(latlng);
		}
	});
	
	jQuery('#latitude').change(function(){
		
		var new_lat = jQuery('#latitude').val();
		try {
			var lat = parseFloat(new_lat);
			var lng = parseFloat(jQuery('#longitude').val());
			var p = new LatLon(lat, lng);
			var grid = OsGridRef.latLonToOsGrid(p);
			jQuery('#grid_ref').val(grid.toString(8));	
			
			var latlng = new google.maps.LatLng(lat, lng);
			updateMap(latlng);
		}	
		catch (err) {
			console.log("Invalid latitude");
		}
	});
	
	jQuery('#longitude').change(function(){
		
		var new_lng = jQuery('#longitude').val();		
		try {
			var lng = parseFloat(new_lng);
			var lat = parseFloat(jQuery('#latitude').val());
			var p = new LatLon(lat, lng);
			var grid = OsGridRef.latLonToOsGrid(p);
			jQuery('#grid_ref').val(grid.toString(8));	
			
			var latlng = new google.maps.LatLng(lat, lng);
			updateMap(latlng);
		}	
		catch (err) {
			console.log("Invalid longitude");
		}
	});
	
	
});
