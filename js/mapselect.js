

jQuery(document).ready(function () {
	
	updateMap = function (newLatLng){
		var myOptions = {
		zoom: 9,
		center: newLatLng,
		mapTypeId: google.maps.MapTypeId.TERRAIN
		}
		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
		var marker = new google.maps.Marker({
			position: newLatLng, 
			map: map,
			draggable:true
		});
		google.maps.event.addListener(
					  marker,
					  'drag',
					  function() {
						  var lat = marker.position.lat();
						  var lng = marker.position.lng();
						  var p = new LatLon(lat, lng);
						  var grid = 0;
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
				  
	var grid_ref;
	var lat;
	var lng;
	try{
	    lat = parseFloat(BioDiv.latitude);
		lng = parseFloat(BioDiv.longitude);
		console.log("lat = " + lat);
		console.log("lng = " + lng);
	}
	catch(err){
	    lat = 54.763213;
		lng = -1.581919; 
	}
	
	if ( (lat == 0 && lng == 0) || isNaN(lat) || isNaN(lng) ) {
		lat = 54.763213;
		lng = -1.581919;
	}
		
	var myLatlng = new google.maps.LatLng(lat, lng);
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
	
	updateMap(myLatlng);
	
	jQuery('#grid_ref').change(function(){
		console.log("Grid ref has been changed.");
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
		console.log("Latitude has been changed.");

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
		console.log("Longitude has been changed.");
		
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
