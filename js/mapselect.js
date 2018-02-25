jQuery(document).ready(function () {
	var grid_ref;
	try{
	    grid_ref = OsGridRef.parse(BioDiv.grid_ref);
	}
	catch(err){
	    grid_ref = OsGridRef.parse("NZ 27 41");
	}

	var grid_ref = OsGridRef.parse(grid_ref.toString(8));
	var point = OsGridRef.osGridToLatLon(grid_ref);
	jQuery('#grid_ref').val(grid_ref.toString(8));

	var myLatlng = new google.maps.LatLng(point.lat, point.lon);
    var myOptions = {
	zoom: 9,
	center: myLatlng,
	mapTypeId: google.maps.MapTypeId.TERRAIN
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    
    var marker = new google.maps.Marker({
	    position: myLatlng, 
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
				      var grid = OsGridRef.latLonToOsGrid(p);
				      document.getElementById('grid_ref').value = grid.toString(8);
				  }
				  );

});
