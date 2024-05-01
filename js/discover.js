

jQuery(document).ready(function () {
	
	setUpMap( BioDiv.areaCovered,
				BioDiv.mapCentre,
				BioDiv.initialZoom,
				BioDiv.showSitesOnLoad );
	
	// //const areaCovered = {min_lat:35, max_lat:65, min_lon:-15, max_lon:35, lat_spacing:5, lon_spacing:8, high_zoom:9, min_zoom:4};
	// const areaCovered = BioDiv.areaCovered;
	// const mapCentre = BioDiv.mapCentre;
	// const initialZoom = BioDiv.initialZoom;
	// const showSitesOnLoad = BioDiv.showSitesOnLoad;
	
	// var geojsonAreas;
	// var geojsonSpecies;
	// var legend;
	// var geojsonSites;
	// var sitesShown = false;
	// var areasShown = false;
	
	
	// //var discovermap = L.map('discovermap').setView([51, 10], 4);
	// var discovermap = L.map('discovermap').setView(mapCentre, initialZoom);
	// discovermap.options.minZoom = areaCovered.min_zoom;
	
	
	// discovermap.on('zoomend', function(e) {
		
		// let zoom = discovermap.getZoom();
		
		// let bounds = e.target.getBounds();
		
		// discovermap.fitBounds(bounds);
		
		// let lat_spacing = 4;
		// let lon_spacing = 8;
		// if ( zoom > 5 && zoom <= 6 ) {
			// lat_spacing = 2;
			// lon_spacing = 4;
		// }
		// else if ( zoom > 6 && zoom <= 7) {
			// lat_spacing = 1;
			// lon_spacing = 2;
		// }
		// else if ( zoom > 7 && zoom <= 9 ) {
			// lat_spacing = 0.5;
			// lon_spacing = 1;
		// }
		// else if (zoom > 9 ) {
			// lat_spacing = 0.1;
			// lon_spacing = 0.2;
		// }
		
		// areaCovered.lat_spacing = lat_spacing;
		// areaCovered.lon_spacing = lon_spacing;
	
		// if ( areasShown ) {
			// showAreas();			
		// }
	// });
	
	
	// L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        // maxZoom: 11,
		// crossOrigin: true,
        // attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
      // }).addTo(discovermap);
	  
		
	// jQuery('#discover_sites').click(function (){
		// toggleSites();
	// });
	
	// jQuery('#hide_sites').click(function (){
		// toggleSites();
	// });
	
	// jQuery('#discover_areas').click(function (){
		// clearCharts();
		// showAreas();
	// });
	
	// jQuery('#discover_species').click(function (){
		// showSpecies();
	// });
	
	// showAreas();
	
	// if ( showSitesOnLoad ) {
		// toggleSites();		  
	// }
	
});
