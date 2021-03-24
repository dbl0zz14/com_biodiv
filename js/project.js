jQuery(document).ready(function(){
	
	// Submit form on image click
	/*
	jQuery( "#subprojectimage_4" ).click(function() {
		jQuery( "#subprojectform_4" ).submit();
		});
		*/

	//Chart.defaults.global.maintainAspectRatio = false;
	
	if ( jQuery('#progressChartShort').length > 0 ) {
		project_id = jQuery('#progressChartShort').attr("data-project-id");
		url = BioDiv.root + "&view=projectdata&format=raw&project_id=" + project_id + "&months=6";
	
		jQuery.ajax(url, {'success': function(data) {
			//console.log("short chart data is " + data);
		
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			
			var ctx = document.getElementById('progressChartShort').getContext('2d');
			var chart = new Chart(ctx, {
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
						display: true,
						text: jsonObject.title //'Sequences Uploaded and Classified (6 months)'
					}
				}
			});
	
		}});
	}
	if ( jQuery('#progressChartMedium').length > 0  ) {
		project_id = jQuery('#progressChartMedium').attr("data-project-id");
		url = BioDiv.root + "&view=projectdata&format=raw&project_id=" + project_id + "&months=12";
	
		jQuery.ajax(url, {'success': function(data) {
			//console.log("medium chart data is " + data);
		
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			var ctx = document.getElementById('progressChartMedium').getContext('2d');
			var chart = new Chart(ctx, {
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
						display: true,
						text: jsonObject.title //'Sequences Uploaded and Classified (1 year)'
					}
				}
			});
	
		}});
	}
	
	if ( jQuery('#progressChartLong').length > 0  ) {
		project_id = jQuery('#progressChartLong').attr("data-project-id");
		url = BioDiv.root + "&view=projectdata&format=raw&project_id=" + project_id + "&months=36";
	
		jQuery.ajax(url, {'success': function(data) {
			//console.log("long chart data is " + data);
		
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			var ctx = document.getElementById('progressChartLong').getContext('2d');
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'line',

				// The data for our dataset
				data: {
					labels: jsonObject.labels,
					datasets: [{
						label: jsonObject.cla_label, // "Classified",
						backgroundColor: jsonObject.colormap[0],
						borderColor: jsonObject.colormap[0],
						data: jsonObject.classified
					},{
						label: jsonObject.upl_label, // "Uploaded",
						backgroundColor: jsonObject.colormap[1],
						borderColor: jsonObject.colormap[1],
						data: jsonObject.uploaded
					}
					]
				},

				// Configuration options go here
				options: {
					title: {
						display: true,
						text: jsonObject.title, // 'Sequences Uploaded and Classified (3 years)'
					}
				}
			});
	
		}});
	}
	
	if ( jQuery('#animalsChart').length > 0  ) {
		project_id = jQuery('#animalsChart').attr("data-project-id");
		url = BioDiv.root + "&view=projectanimals&format=raw&project_id=" + project_id;
	
		jQuery.ajax(url, {'success': function(data) {
			console.log("Animals data is: " + data);
		
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			
			var ctx = document.getElementById('animalsChart').getContext('2d');
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'doughnut',

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
						display: true,
						text: jsonObject.title // 'All Classifications by Species'
					}

				}
			});
	
		}});
	}
	
	if ( jQuery('#animalsBarChart').length > 0  ) {
		project_id = jQuery('#animalsBarChart').attr("data-project-id");
		url = BioDiv.root + "&view=projectanimals&format=raw&project_id=" + project_id;
	
		jQuery.ajax(url, {'success': function(data) {
			console.log(data);
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			//Chart.defaults.global.maintainAspectRatio = false;
			var ctx = document.getElementById('animalsBarChart').getContext('2d');
			var chart = new Chart(ctx, {
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
	
		}});
	}
	
	if ( jQuery('#animalsBarChartKiosk').length > 0  ) {
		project_id = jQuery('#animalsBarChartKiosk').attr("data-project-id");
		url = BioDiv.root + "&view=projectanimals&format=raw&project_id=" + project_id;
	
		jQuery.ajax(url, {'success': function(data) {
			console.log(data);
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			//Chart.defaults.global.maintainAspectRatio = false;
			var ctx = document.getElementById('animalsBarChartKiosk').getContext('2d');
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'bar',

				// The data for our dataset
				data: {
					labels: jsonObject.labels,
					datasets: [{
						label: "Number of classifications",
						backgroundColor: jsonObject.colormap,
						data: jsonObject.animals
					}
					]
				},
				
				// Configuration options go here
				options: {
					legend: {
						display: false
					},
					scales: {
						yAxes: [{
							ticks: {
								fontColor: "white",
								beginAtZero:true,
								autoSkip:false
							}
						}],
						xAxes: [{
							ticks: {
								fontColor: "white",
								autoSkip:false
							}
						}]
					}
				}
			});
	
		}});
	}
	
	
let tooltip_text = jQuery('.project-btn').attr('data-tooltip');
jQuery('.project-btn').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': tooltip_text, 'placement': 'top'});
			
});
