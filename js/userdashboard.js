jQuery(document).ready(function(){
	
	// Pick up text string variables - could be non-English
	const waitText = BioDiv.waitText;
	const doneText = BioDiv.doneText;
	const genText = BioDiv.genText;
	
	
	clearReport = function () {
		
		jQuery('#report_display').empty();
		jQuery("#data_warning").hide();
	}
	
	
	
	// ------------------------   The likes report stuff - maybe move to separate file..
	
	displayLikes = function ( pageNum, isOthersLikes = false ) {
		
		let url = BioDiv.root + "&view=dashlikes&format=raw&page=" + pageNum;
		
		if ( isOthersLikes ) {
			url += "&other=" + 1;
		}
		
		let numPerPage = jQuery('#num_select').val();
		if ( numPerPage ) {
			url += "&number=" + numPerPage;
		}
			
		let sortBy = jQuery('#sort_select').val();
		if ( sortBy ) {
			url += "&sort=" + sortBy;
		}
			
		let site = jQuery('#site_select').val();
		if ( site ) {
			url += "&site=" + site;
		}	
		
		let year = jQuery('#year_select').val();
		if ( year ) {
			url += "&year=" + year;
		}
		
		let species = jQuery('#species_select').val();
		if ( species ) {
			url += "&species=" + species;
		}
		
		if ( isOthersLikes ) {
			jQuery('#report_display').load(url, updateOthersLikesControls);
		}
		else {
			jQuery('#report_display').load(url, updateLikesControls);
		}
		
	};
	
	updateLikesControls = function () {
		
		addLikesEvents();
		addPlayMedia();
		updateLikesPagination();
		
	}
	
	updateOthersLikesControls = function () {
		
		addLikesEvents(true);
		addPlayMedia();
		updateLikesPagination(true);
		
	}
	
	addLikesEvents = function ( isOthersLikes = false ) {
		
		jQuery('.likes_select').change(function (){
		
			displayLikes(0, isOthersLikes);
		
		});
		
		
	}
	
	
	updateLikesPagination = function ( isOthersLikes = false ) {
		
		jQuery('.pagination li').click(function (){
			let pageText = null;
			let page = null;
			if ( jQuery(this).hasClass('prev-page') ) {
				pageText = jQuery('.pagination li.active').text();
				page = parseInt(pageText) -2;
				if ( page < 0 ) page = 0;
			}
			else if ( jQuery(this).hasClass('next-page') ) {
				pageText = jQuery('.pagination li.active').text();
				page = parseInt(pageText);
				
				let lastPageText = jQuery('.last-page').text();
				let lastPage = parseInt(lastPageText);
				if ( page == lastPage ) page = lastPage-1;
				
			}
			else {
				pageText = jQuery(this).text();
				page = parseInt(pageText) -1;
			}
			displayLikes( page, isOthersLikes );
	
		});
		
	};
	
	// -------------------------------  End of Likes display stuff
	
	
	
	// -------------------------------  Spotter status stuff
	
	displaySpotter = function () {
		
		let url = BioDiv.root + "&view=dashspotter&format=raw";
		
		jQuery('#report_display').load(url);
		
	};
			
	// -------------------------------  End of Spotter status stuff
	
	
	
	// -------------------------------  Trapper status stuff
	
	displayTrapper = function () {
		
		let url = BioDiv.root + "&view=dashtrapper&format=raw";
		
		jQuery('#report_display').load(url, displayTrapperUploadChart);
		
	};
	
	displayTrapperUploadChart = function () {
		
		let siteId = jQuery('#site_select').val();

		if ( jQuery('#userProgressChart').length > 0 ) {
			//project_id = jQuery('#userProgressChart').attr("data-project-id");
			url = BioDiv.root + "&view=discoveruseruploads&format=raw";
			
			if ( siteId ) url += "&site=" + siteId;
		
			jQuery.ajax(url, {'success': function(data) {
			
				// Now get the json data into the chart and display it.
				var jsonObject = JSON.parse ( data );
				
				if(window.uploadChart !== undefined && window.uploadChart !== null){
					window.uploadChart.destroy();
				}
				
				var ctx = document.getElementById('userProgressChart').getContext('2d');
				
				window.uploadChart = new Chart(ctx, {
					// The type of chart we want to create
					type: 'line',

					// The data for our dataset
					data: {
						labels: jsonObject.labels,
						datasets: [{
							label: jsonObject.cla_label, //"Classified",
							backgroundColor: jsonObject.colormap[0],
							borderColor: jsonObject.colormap[0],
							data: jsonObject.classified
						},{
							label: jsonObject.upl_label, //"Uploaded",
							backgroundColor: jsonObject.colormap[1],
							borderColor: jsonObject.colormap[1],
							data: jsonObject.uploaded
						}
						]
					},

					// Configuration options go here
					options: {
						title: {
							display: false,
							text: jsonObject.title, //'Sequences Uploaded and Classified (6 months)'
							position: 'bottom'
						},
						legend: {
							position: 'bottom',
							align: 'end'
						},
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero:true,
									autoSkip:false,
									precision:0
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
			}});
		}
	}
			
	// -------------------------------  End of Trapper status stuff
	
	
	// -------------------------------  Chart stuff
	
	displayCharts = function () {
		
		let url = BioDiv.root + "&view=dashcharts&format=raw";
		
		jQuery('#report_display').load(url, updateCharts);
		
	};
	
	
	// Called once on Chart first load
	updateCharts = function () {
		getCharts();
		addChartsEvents();
	}
	
	
	// Get the charts on chart load or site select
	getCharts = function () {
		
		// This is also displayed on trapper summary
		displayTrapperUploadChart();

		displayTopSpeciesChart();
		
		displayRareSpeciesChart();
		
		//displayNothingHumanChart();
		
		displayTopSeqSpeciesChart();
		
	
	}
			
			
	addChartsEvents = function () {
		
		jQuery('.charts_select').change(function (){
			
			if(window.uploadChart !== undefined && window.uploadChart !== null){
				window.uploadChart.destroy();
			}
				
			if(window.topChart !== undefined && window.topChart !== null){
				window.topChart.destroy();
			}
			
			if(window.rareChart !== undefined && window.rareChart !== null){
				window.rareChart.destroy();
			}
			
			if(window.nothHumChart !== undefined && window.nothHumChart !== null){
				window.nothHumChart.destroy();
			}
				
			if(window.topSeqChart !== undefined && window.topSeqChart !== null){
				window.topSeqChart.destroy();
			}
				
			getCharts();
		
		});
		
		
	}
	
	displayTopSpeciesChart = function () {
		
		let siteId = jQuery('#site_select').val();
	
		if ( jQuery('#topSpeciesChart').length > 0  ) {
			url = BioDiv.root + "&view=discoveruseranimals&format=raw" ;
			
			if ( siteId ) url += "&site=" + siteId;
		
			jQuery.ajax(url, {'success': function(data) {
				//console.log(data);
				console.log("Top species callback");
				
				// Now get the json data into the chart and display it.
				var jsonObject = JSON.parse ( data );
				//Chart.defaults.global.maintainAspectRatio = false;
				
				if(window.topChart !== undefined && window.topChart !== null){
					window.topChart.destroy();
				}
					
				if ( jsonObject.labels.length > 0 ) {
				
					var ctx = document.getElementById('topSpeciesChart').getContext('2d');
					
					window.topChart = new Chart(ctx, {
						// The type of chart we want to create
						type: 'bar',

						// The data for our dataset
						data: {
							labels: jsonObject.labels,
							datasets: [{
								label: jsonObject.ani_label, // "Number of classifications"
								backgroundColor: jsonObject.colormap,
								data: jsonObject.animals
							}
							]
						},
						
						// Configuration options go here
						options: {
							title: {
								display: false,
								text: jsonObject.title, // 'All Classifications by Species'
								position: 'bottom'
							},
							legend: {
								display: false,
							},
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:true,
										autoSkip:false,
										precision:0
									}
								}],
								xAxes: [{
									ticks: {
										autoSkip:false
									},
									gridlines: {
										display:false
									}
								}]
							}

						}
					});
				}
				else {
					console.log("No classifications");
				}
			}});
		}	
	}		
	

	displayRareSpeciesChart = function () {
		
		let siteId = jQuery('#site_select').val();
	
		if ( jQuery('#rareSpeciesChart').length > 0  ) {
			url = BioDiv.root + "&view=discoveruseranimals&format=raw&rare=1" ;
		
			if ( siteId ) url += "&site=" + siteId;
		
			jQuery.ajax(url, {'success': function(data) {
				console.log(data);
				// Now get the json data into the chart and display it.
				var jsonObject = JSON.parse ( data );
				
				if(window.rareChart !== undefined && window.rareChart !== null){
					window.rareChart.destroy();
				}
						
				if ( jsonObject.labels.length > 0 ) {
				
					var ctx = document.getElementById('rareSpeciesChart').getContext('2d');
					window.rareChart = new Chart(ctx, {
						// The type of chart we want to create
						type: 'bar',

						// The data for our dataset
						data: {
							labels: jsonObject.labels,
							datasets: [{
								label: jsonObject.ani_label, // "Number of classifications",
								backgroundColor: jsonObject.colormap,
								data: jsonObject.animals
							}
							]
						},
						
						// Configuration options go here
						options: {
							title: {
								display: false,
								text: jsonObject.title, // 'All Classifications by Species'
								position: 'bottom'
							},
							legend: {
								display: false,
							},
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:true,
										autoSkip:false,
										precision:0
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
				else {
				}
			}});
		}	
	}


	displayNothingHumanChart = function () {
		
		let siteId = jQuery('#site_select').val();
	
		if ( jQuery('#nothingHumanChart').length > 0  ) {
			url = BioDiv.root + "&view=discoverusernothinghuman&format=raw" ;
		
			if ( siteId ) url += "&site=" + siteId;
		
			jQuery.ajax(url, {'success': function(data) {
				console.log(data);
				// Now get the json data into the chart and display it.
				var jsonObject = JSON.parse ( data );
				
				if(window.nothHumChart !== undefined && window.nothHumChart !== null){
					window.nothHumChart.destroy();
				}
					
				if ( jsonObject.labels.length > 0 ) {
				
					var ctx = document.getElementById('nothingHumanChart').getContext('2d');
					window.nothHumChart = new Chart(ctx, {
						// The type of chart we want to create
						type: 'pie',

						// The data for our dataset
						
						data: {
							labels: jsonObject.labels,
							datasets: [{
								label: jsonObject.ani_label, // "Animals",
								backgroundColor: jsonObject.colormap,
								data: jsonObject.animals
							}
							]
						},
						// Configuration options go here
						options: {
							title: {
								display: false,
								text: jsonObject.title,
								position: 'bottom'
							},
							legend: {
								position: 'right',
								align: 'end'
							}

						}
					});	
						
					jQuery('#nothingHumanChart').parent().width("350px");
				}
				else {
				}
			}});
		}	
	}	
	
	
	displayTopSeqSpeciesChart = function () {
		
		let siteId = jQuery('#site_select').val();
	
		if ( jQuery('#topSeqSpeciesChart').length > 0  ) {
			url = BioDiv.root + "&view=discoveruserseqanimals&format=raw" ;
			
			if ( siteId ) url += "&site=" + siteId;
		
			jQuery.ajax(url, {'success': function(data) {
				//console.log(data);
				console.log("Top species callback");
				
				// Now get the json data into the chart and display it.
				var jsonObject = JSON.parse ( data );
				//Chart.defaults.global.maintainAspectRatio = false;
				
				if(window.topSeqChart !== undefined && window.topSeqChart !== null){
					window.topSeqChart.destroy();
				}
					
				if ( jsonObject.labels.length > 0 ) {
				
					var ctx = document.getElementById('topSeqSpeciesChart').getContext('2d');
					
					window.topSeqChart = new Chart(ctx, {
						// The type of chart we want to create
						type: 'horizontalBar',

						// The data for our dataset
						data: {
							labels: jsonObject.labels,
							datasets: [{
								label: jsonObject.ani_label, // "Number of classifications"
								backgroundColor: jsonObject.colormap,
								data: jsonObject.animals
							}
							]
						},
						
						// Configuration options go here
						options: {
							title: {
								display: false,
								text: jsonObject.title, // 'All Classifications by Species'
								position: 'bottom'
							},
							legend: {
								display: false,
							},
							scales: {
								xAxes: [{
									ticks: {
										beginAtZero:true,
										autoSkip:false,
										precision:0
									}
								}],
								yAxes: [{
									ticks: {
										autoSkip:false
									},
									gridlines: {
										display:false
									}
								}]
							}

						}
					});
				}
				else {
					console.log("No classifications");
				}
			}});
		}	
	}		
	

	
	// -------------------------------  End of Chart stuff
	
	
	
	
	// -------------------------------  Dashboard controls
	
	jQuery('.hide_options').hide();
	jQuery('.panel_options').hide();
	
	
	jQuery('.show_options').click(function (){
		
		jQuery(this).hide();
		jQuery(this).siblings( ".hide_options" ).show();
		
		jQuery(this).parent().parent().siblings( ".panel_options" ).show();
		
	});
	
	
	jQuery('.hide_options').click(function (){
		
		jQuery(this).hide();
		jQuery(this).siblings( ".show_options" ).show();
			
		jQuery(this).parent().parent().siblings( ".panel_options" ).hide();
		
	});
				
				
	jQuery('#spotter_status').click(function (){
		
		clearReport();
		displaySpotter();
		
	});
	
	
	jQuery('#trapper_status').click(function (){
		
		clearReport();
		displayTrapper();
		
	});
	
	
	jQuery('#my_likes').click(function (){
		
		clearReport();
		displayLikes(0);
		
	});
	
		
	jQuery('#others_likes').click(function (){
		
		clearReport();
		displayLikes(0, true);
		
	});
	
		
	jQuery('#site_charts').click(function (){
		
		clearReport();
		displayCharts();
		
	});
	
	// Show the spotter page initially
	jQuery('.show_options').first().trigger("click");
	jQuery('#spotter_status').trigger( "click" );
	
	// ------------------------  End of dashboard controls
	
	
	
});
