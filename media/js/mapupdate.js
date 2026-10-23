
async function updateMap ( newLatLng, prefix = null ) {
	
	const { Map } = await google.maps.importLibrary("maps");
	const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
	// import LatLon from 'geodesy/latlon-ellipsoidal.js';
	// import OsGridRef from 'geodesy/osgridref.js';
	//import OsGridRef, { LatLon, Dms } from 'https://cdn.jsdelivr.net/npm/geodesy@2/osgridref.js';

	
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
		let gridRefId = 'grid_ref';
		let latitudeId = 'latitude';
		let longitudeId = 'longitude';
		if ( prefix ) {
			gridRefId = prefix + "_grid_ref";
			latitudeId = prefix + "_latitude";
			longitudeId = prefix + "_longitude";
		}
		jQuery(gridRefId).val(grid.toString(8));
		jQuery(latitudeId).val(lat.toPrecision(10));
		jQuery(longitudeId).val(lng.toPrecision(10));
	}
	
	const myOptions = {
		zoom: 9,
		center: newLatLng,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		mapId: "DEMO_MAP_ID"
	}
	let canvasId = "map_canvas";
	if ( prefix ) canvasId = prefix + "_map_canvas";
	const map = new google.maps.Map(document.getElementById(canvasId), myOptions);
	
	const marker = new google.maps.marker.AdvancedMarkerElement({
		position: newLatLng, 
		map: map,
		gmpDraggable: true
	});
	
	if ( prefix == null ) {
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
	}
	else if ( prefix == "add" ) {
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
				document.getElementById('add_grid_ref').value = grid.toString(8);
				document.getElementById('add_latitude').value = lat.toPrecision(10);
				document.getElementById('add_longitude').value = lng.toPrecision(10);
			}
		);
	}
	else if ( prefix == "edit" ) {
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
				document.getElementById('edit_grid_ref').value = grid.toString(8);
				document.getElementById('edit_latitude').value = lat.toPrecision(10);
				document.getElementById('edit_longitude').value = lng.toPrecision(10);
			}
		);
	}
	else if ( prefix == "partedit" ) {
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
				document.getElementById('partedit_grid_ref').value = grid.toString(8);
				document.getElementById('partedit_latitude').value = lat.toPrecision(10);
				document.getElementById('partedit_longitude').value = lng.toPrecision(10);
			}
		);
	}				  
};

async function updateMapFromGridRef ( gridRef, prefix ) {
	
	const { Map } = await google.maps.importLibrary("maps");
	
	var latId = '#latitude';
	var lonId = '#longitude';
	var gridRefId = '#grid_ref';
	if ( prefix ) {
		latId = '#'+prefix+'_latitude';
		lonId = '#'+prefix+'_longitude';
		gridRefId = '#' + prefix + '_grid_ref';
	}
	var grid_ref = 0;
	try {
		grid_ref = OsGridRef.parse(gridRef);
	}
	catch ( err ) {
		grid_ref = 0;
	}
	if ( grid_ref ) {
		var point = OsGridRef.osGridToLatLon(grid_ref);		
		var lat = point.lat.toPrecision(10);
		var lng = point.lon.toPrecision(10);
		jQuery(latId).val(lat);
		jQuery(lonId).val(lng);
		jQuery(gridRefId).val(gridRef);		
		
		var latlng = new google.maps.LatLng(lat, lng);
		
		updateMap(latlng, prefix);
	}
}


async function gridRefChange ( prefix = null ) {
	
	const { Map } = await google.maps.importLibrary("maps");
	
	var latId = '#latitude';
	var lonId = '#longitude';
	var gridRefId = '#grid_ref';
	if ( prefix ) {
		latId = '#'+prefix+'_latitude';
		lonId = '#'+prefix+'_longitude';
		gridRefId = '#' + prefix + '_grid_ref';
	}
	var new_grid_ref = jQuery(gridRefId).val();
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
		jQuery(latId).val(lat);
		jQuery(lonId).val(lng);
				
		var latlng = new google.maps.LatLng(lat, lng);
		updateMap(latlng, prefix);
	}
}

async function latitudeChange ( prefix = null ) {
	const { Map } = await google.maps.importLibrary("maps");
	
	var latId = '#latitude';
	var lonId = '#longitude';
	var gridRefId = '#grid_ref';
	if ( prefix ) {
		latId = '#'+prefix+'_latitude';
		lonId = '#'+prefix+'_longitude';
		gridRefId = '#' + prefix + '_grid_ref';
	}
	var new_lat = jQuery(latId).val();
	try {
		var lat = parseFloat(new_lat);
		var lng = parseFloat(jQuery(lonId).val());
		var p = new LatLon(lat, lng);
		var grid = OsGridRef.latLonToOsGrid(p);
		
		jQuery(gridRefId).val(grid.toString(8));	
		
		var latlng = new google.maps.LatLng(lat, lng);
		updateMap(latlng, prefix);
	}	
	catch (err) {
		console.log("Invalid latitude");
	}
}

async function longitudeChange ( prefix = null ) {
	const { Map } = await google.maps.importLibrary("maps");
	
	var latId = '#latitude';
	var lonId = '#longitude';
	var gridRefId = '#grid_ref';
	if ( prefix ) {
		latId = '#'+prefix+'_latitude';
		lonId = '#'+prefix+'_longitude';
		gridRefId = '#' + prefix + '_grid_ref';
	}
	var new_lng = jQuery(lonId).val();		
	try {
		var lng = parseFloat(new_lng);
		var lat = parseFloat(jQuery(latId).val());
		var p = new LatLon(lat, lng);
		var grid = OsGridRef.latLonToOsGrid(p);
		jQuery(gridRefId).val(grid.toString(8));	
		
		var latlng = new google.maps.LatLng(lat, lng);
		updateMap(latlng, prefix);
	}	
	catch (err) {
		console.log("Invalid longitude");
	}
}
	
jQuery(document).ready(function () {
	
	jQuery('#grid_ref').change(function(){
		gridRefChange();
	});
	
	jQuery('#latitude').change(function(){
		latitudeChange();
	});
	
	jQuery('#longitude').change(function(){
		longitudeChange();
	});
	
	
	jQuery('#add_grid_ref').change(function(){
		gridRefChange('add');
	});
	
	jQuery('#add_latitude').change(function(){
		latitudeChange('add');
	});
	
	jQuery('#add_longitude').change(function(){
		longitudeChange('add');
	});
	
	
	jQuery('#edit_grid_ref').change(function(){
		gridRefChange('edit');
	});
	
	jQuery('#edit_latitude').change(function(){
		latitudeChange('edit');
	});
	
	jQuery('#edit_longitude').change(function(){
		longitudeChange('edit');
	});
	
	
	jQuery('#partedit_grid_ref').change(function(){
		gridRefChange('partedit');
	});
	
	jQuery('#partedit_latitude').change(function(){
		latitudeChange('partedit');
	});
	
	jQuery('#partedit_longitude').change(function(){
		longitudeChange('partedit');
	});
	
});
