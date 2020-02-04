

jQuery(document).ready(function () {
	
	// Should really have all this in an object
	var areaCovered = {min_lat:35, max_lat:65, min_lon:-15, max_lon:35, lat_spacing:5, lon_spacing:8};
	
	//var siteGroup;
	var geojsonAreas;
	var geojsonSpecies;
	var legend;
	var geojsonSites;
	var sitesShown = false;
	var areasShown = false;
	var sightingsChart;
	var uploadsChart;
	
	function getSpeciesColor(d) {
    return d > 100 ? '#800026' :
           d > 50  ? '#BD0026' :
           d > 20  ? '#E31A1C' :
           d > 10  ? '#FC4E2A' :
           d > 5   ? '#FD8D3C' :
           d > 2   ? '#FEB24C' :
           d > 1   ? '#FED976' :
                      '#FFEDA0';
	}
	
	function getSiteColor(d) {
		/*
	// RdPu
	return d > 100 ? '#49006a' :
           d > 50  ? '#7a0177' :
           d > 20  ? '#ae017e' :
           d > 5   ? '#dd3497' :
           d > 2   ? '#f768a1' :
                      '#fa9fb5';
					  */
					  /*
	// Pu
	return d > 100 ? '#3f007d' :
           d > 50  ? '#54278f' :
           d > 20  ? '#6a51a3' :
           d > 5   ? '#807dba' :
           d > 2   ? '#9e9ac8' :
                      '#bcbddc';
					  */
	// MammalWeb greens
	return d > 100 ? '#05785a' :
           d > 50  ? '#118c6c' :
           d > 20  ? '#21a382' :
           d > 5   ? '#36c29e' :
           d > 2   ? '#4fe0bb' :
                      '#6afcd7';
	}
	
	toggleSites = function () {
				
		if ( !sitesShown ) {
			showSites();
		}
		else {
			clearSites();
		}
		jQuery('#discover_sites').toggleClass('active');
		jQuery('#hide_sites').toggleClass('active'); 
		jQuery('#discover_sites').toggleClass('disabled');
		jQuery('#hide_sites').toggleClass('disabled'); 
		
		
	};
	
		
	showSites = function (){
		if ( geojsonSites ) {
			discovermap.addLayer(geojsonSites);
			discovermap.getPane('sites').style.zIndex = 402;
			if ( discovermap.getPane('species') ) {
				discovermap.getPane('species').style.zIndex = 401;
			}
			sitesShown = true;
		}
		else {
		
			url = BioDiv.root + "&view=discoversites&format=raw";
			
			jQuery.ajax(url, {'success': function(data) {
				// Now get the json data into the chart and display it.
				var jsonObject = JSON.parse ( data );
				
				var discoverAreas = {
				  "type": "FeatureCollection",
				  "features": jsonObject.features
				};
				
				function onEachFeature(feature, layer) {
					layer.bindTooltip("" + feature.properties.site_count + " " + jsonObject.sites);
				}
				
				function style(feature) {
					return {
						fillColor: getSiteColor(feature.properties.site_count),
						weight: 1,
						opacity: 1,
						color: getSiteColor(feature.properties.site_count),
						//dashArray: '3',
						fillOpacity: 0.8
					};
				}
				
				discovermap.createPane('sites');
				
				// Toggle panes so sites on top
				discovermap.getPane('sites').style.zIndex = 402;
				if ( discovermap.getPane('species') ) {
					discovermap.getPane('species').style.zIndex = 401;
				}

				geojsonSites = L.geoJson(discoverAreas, {
					style: style,
					pane: 'sites',
					onEachFeature: onEachFeature
				}).addTo(discovermap);
				
				sitesShown = true;
				
			}});
		}
			
	};
	
	clearSites = function () {
		
		discovermap.removeLayer(geojsonSites);
		sitesShown = false;
	};
	
	
	showAreas = function() {
		
		clearSpecies();
		clearAreas();
		
		var features = [];
		
		var i,j;
		for ( i = areaCovered.min_lat; i < areaCovered.max_lat; i += areaCovered.lat_spacing ) {
			for ( j = areaCovered.min_lon; j < areaCovered.max_lon; j += areaCovered.lon_spacing ) {
				let s = j;
				let n = j + areaCovered.lon_spacing;
				let w = i;
				let e = i + areaCovered.lat_spacing
				features.push( {"type": "Feature",  
								"properties": {
									"stroke": false,
									"popuptext": "[" + s + "," + w + "]-[" + n + ", " + e + "]",
									
								  },  
								"geometry": {	"type": "Polygon",	"coordinates": [  [[ s,w],[ n,w],[n,e],[s,e],[s,w]] ] }	
				});
			}
		}
		
		var discoverAreas = {
		  "type": "FeatureCollection",
		  "features": features
		};
		
		function onEachFeature(feature, layer) {
			layer.on({
				mouseover: highlightFeature,
				mouseout: resetHighlight,
				click: showAreaCharts
			});
			layer.bindPopup(feature.properties.popuptext);
		}
		
		function style(feature) {
			return {
				weight: 1,
				opacity: 1,
				color: 'white',
				//dashArray: '3',
				fillOpacity: 0
			};
		}

		geojsonAreas = L.geoJson(discoverAreas, {
			style: style,
			onEachFeature: onEachFeature
		}).addTo(discovermap);
		
		//Disable the areas button
		jQuery('#discover_areas').prop('disabled', true);
		
		areasShown = true;
	}
	
	clearAreas = function () {
		if ( geojsonAreas ) geojsonAreas.remove();
	}
	
	clearCharts = function() {
		
		let eleS = jQuery('#sightingschart');
		if ( eleS ) eleS.empty();
		let eleU = jQuery('#uploadschart');
		if ( eleU ) eleU.empty();
		
				/*
		if ( sightingsChart ) {
			sightingsChart.destroy();
		}
		if ( uploadsChart ) {
			uploadsChart.destroy();
		}	
		*/
	}
	
	showSpecies = function (){
		// Change display mode
		clearAreas();
		clearCharts();
		areasShown = false;
		
		// For now keep it simple by reloading for new species
		clearSpecies();
		
		var speciesSelect = document.getElementById( "species_select" );
		var speciesId = speciesSelect.options[speciesSelect.selectedIndex].value;
		
		if ( speciesId ) {
			url = BioDiv.root + "&view=discoverspecies&format=raw&species=" + speciesId;
	
			jQuery.ajax(url, {'success': function(data) {
				// Now get the json data into the chart and display it.
				var jsonObject = JSON.parse ( data );
				
				var discoverAreas = {
				  "type": "FeatureCollection",
				  "features": jsonObject.features
				};
				
				function onEachFeature(feature, layer) {
					layer.bindTooltip(feature.properties.all);
				}
				
				function style(feature) {
					return {
						fillColor: getSpeciesColor(feature.properties.all),
						weight: 1,
						opacity: 0.9,
						color: getSpeciesColor(feature.properties.all),
						//dashArray: '3',
						fillOpacity: 0.9
					};
				}
				
				if ( !discovermap.getPane('species') ) {
					discovermap.createPane('species');
				}
				
				// Toggle which pane is on top - species
				discovermap.getPane('species').style.zIndex = 402;
				if ( discovermap.getPane('site') ) {
					console.log("Setting site zindex to 401");
					discovermap.getPane('site').style.zIndex = 401;
				}

				geojsonSpecies = L.geoJson(discoverAreas, {
					style: style,
					pane: 'species',
					onEachFeature: onEachFeature
				}).addTo(discovermap);
				
				legend = L.control({position: 'bottomright'});

				legend.onAdd = function (discovermap) {

					var div = L.DomUtil.create('div', 'info legend'),
						grades = [0, 1, 2, 5, 10, 20, 50, 100],
						labels = [];

					// loop through our density intervals and generate a label with a colored square for each interval
					for (var i = 0; i < grades.length; i++) {
						div.innerHTML +=
							'<i style="background:' + getSpeciesColor(grades[i] + 1) + '"></i> ' +
							grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
					}

					return div;
				};

				legend.addTo(discovermap);
				
				showSpeciesTotalsChart(jsonObject.totals, jsonObject.title);
			}});
			
			
		}
		//Enable the areas button
		jQuery('#discover_areas').prop('disabled', false);
	};
	
	showSpeciesTotalsChart = function ( totalsObject, title ) {
		var ele = document.getElementById('sightingschart');
		var ctx = ele.getContext('2d');
		
		sightingsChart = new Chart(ctx, {
			// The type of chart we want to create
			type: 'bar',

			// The data for our dataset
			data: {
				labels: Object.keys(totalsObject),
				datasets: [{
					backgroundColor: ["#32553f","#00ba8a","#66a381","#f6c67a","#d6da9c","#b4d0d0","#c9e6d1"],
					data: Object.values(totalsObject)
				}
				]
			},
			
			// Configuration options go here
			options: {
				title: {
					display: true,
					text: title // 'All Classifications by Species'
				},
				legend: {
					display: false,
				},
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true,
							autoSkip:false
						}
					}],
					xAxes: [{
						ticks: {
							autoSkip:false
						}
					}]
				}

			}
		});
	}
	
	
	clearSpecies = function () {
		if ( geojsonSpecies ) geojsonSpecies.remove();
		if ( legend ) legend.remove();
	}
	
	var discovermap = L.map('discovermap').setView([51, 10], 4);
	discovermap.options.minZoom = 4;
	
	
	discovermap.on('zoomend', function(e) {
		
		console.log("checking areas after zoom");
	
		let zoom = discovermap.getZoom();
		
		let bounds = e.target.getBounds();
		
		//discovermap.fitBounds(bounds);
		
		let lat_spacing = 4;
		let lon_spacing = 8;
		if ( zoom > 5 && zoom <= 6 ) {
			lat_spacing = 2;
			lon_spacing = 4;
		}
		else if ( zoom > 6 ) {
			lat_spacing = 1;
			lon_spacing = 2;
		}
		
		areaCovered.lat_spacing = lat_spacing;
		areaCovered.lon_spacing = lon_spacing;
	
		if ( areasShown ) {
			showAreas();			
		}
	});
	
	
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 11,
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
      }).addTo(discovermap);
	  
		
	jQuery('#discover_sites').click(function (){
		toggleSites();
	});
	
	jQuery('#hide_sites').click(function (){
		toggleSites();
	});
	
	jQuery('#discover_areas').click(function (){
		clearCharts();
		showAreas();
	});
	
	jQuery('#discover_species').click(function (){
		showSpecies();
	});
	
	
	function highlightFeature(e) {
		var layer = e.target;

		layer.setStyle({
			weight: 1,
			color: '#666',
			dashArray: '',
			fillOpacity: 0.5
		});

		if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
			layer.bringToFront();
		}
	}
	
	function resetHighlight(e) {
		geojsonAreas.resetStyle(e.target);
	}
	
	function showAreaCharts(e) {
		let bounds = e.target.getBounds();
		
		url = BioDiv.root + "&view=discoveranimals&format=raw&latstart=" + bounds.getSouth() + "&latend=" + bounds.getNorth() + "&lonstart=" + bounds.getWest() + "&lonend=" + bounds.getEast();
	
		jQuery.ajax(url, {'success': function(data) {
			console.log("discover data is " + data);
		
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			
			var ele = document.getElementById('sightingschart');
			var ctx = ele.getContext('2d');
			sightingsChart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'bar',

				// The data for our dataset
				data: {
					labels: jsonObject.labels,
					datasets: [{
						label: jsonObject.ani_label, // "Number of classifications",
						backgroundColor: ["#32553f","#00ba8a","#66a381","#f6c67a","#d6da9c","#b4d0d0","#c9e6d1"],
						data: jsonObject.animals
					}
					]
				},
				
				// Configuration options go here
				options: {
					title: {
						display: true,
						text: jsonObject.title // 'All Classifications by Species'
					},
					legend: {
						display: false,
					},
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero:true,
								autoSkip:false
							}
						}],
						xAxes: [{
							ticks: {
								autoSkip:false
							}
						}]
					}

				}
			});
			
			
			
			
		} });
		
		url2 = BioDiv.root + "&view=discoverdata&format=raw&latstart=" + bounds.getSouth() + "&latend=" + bounds.getNorth() + "&lonstart=" + bounds.getWest() + "&lonend=" + bounds.getEast();
	
		jQuery.ajax(url2, {'success': function(data) {
			
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			
			var ctx = document.getElementById('uploadschart').getContext('2d');
			uploadsChart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'line',

				// The data for our dataset
				data: {
					labels: jsonObject.labels,
					datasets: [{
						label: jsonObject.cla_label, //"Classified",
						backgroundColor: '#00ba8a',
						borderColor: '#00ba8a',
						data: jsonObject.classified
					},{
						label: jsonObject.upl_label, //"Uploaded",
						backgroundColor: '#32553f',
						borderColor: '#32553f',
						data: jsonObject.uploaded
					}
					]
				},

				// Configuration options go here
				options: {
					title: {
						display: true,
						text: jsonObject.title //'Sequences Uploaded and Classified (6 months)'
					}
				}
			});
	
		}});
	}
	
	
	showAreas();
	
			  
	
});
