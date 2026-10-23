

jQuery(document).ready(function () {
	
	
	
	jQuery('#calculate_lat_long').click(function(){
		console.log("Updating latlong.");
		
		jQuery(".site_input_field").each(function( index ) {
			var site_id = jQuery( this ).val();
			var grid = jQuery("input[name='grid[" + site_id + "]']").val();
			var grid_ref = "";
			var lat = "";
			var lon = "";
			try {
				grid_ref = OsGridRef.parse(grid);
				var point = OsGridRef.osGridToLatLon(grid_ref);		
				lat = point.lat.toPrecision(10);
				lon = point.lon.toPrecision(10);
			}
			catch (err) {
				console.log("Cannot parse grid ref, skipping site " + site_id );
			}
			jQuery("input[name='lat[" + site_id + "]']").val( lat );
			jQuery("input[name='lon[" + site_id + "]']").val( lon );
		});
		
	});
	
	
	
});
