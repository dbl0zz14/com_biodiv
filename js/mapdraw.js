
async function drawMap (){
	
	jQuery('#no_map').hide();
	
	try{
		
		const { Map } = await google.maps.importLibrary("maps");
		
		let south = parseFloat(BioDiv.south);
		let west = parseFloat(BioDiv.west);
		let north = parseFloat(BioDiv.north);
		let east = parseFloat(BioDiv.east);
		
		let sw = new google.maps.LatLng(south, west);
		let ne = new google.maps.LatLng(north, east);
		
		let posBounds = new google.maps.LatLngBounds (sw, ne);
	
		let mapOptions = {
			zoom: 8,
			center: sw,
			mapTypeId: google.maps.MapTypeId.TERRAIN
		}
		
		var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
		
		let rectOptions = {
			bounds: posBounds,
			fillColor: "#00ba8a",
			fillOpacity: 0.5,
			strokeWeight: 1,
			//strokeColour: "red",
			//strokeOpacity: 0.5,
			map: map,
			draggable:false
		}
		
		var rect = new google.maps.Rectangle(
			rectOptions
		);
		
	}
	catch(err){
		console.log("No location given");
		jQuery('#no_map').show();
	}
	
};
	
jQuery(document).ready(function(){
	
	
	jQuery('#control_map').click(function (){
		jQuery('#map_modal').modal('show');
		drawMap();
		
	});
	
	
	
});

	
    
