jQuery(document).ready(function(){

	if ( jQuery('#progressChartShort').length > 0 ) {
		project_id = jQuery('#progressChartShort').attr("data-project-id");
		url = BioDiv.root + "&view=projectdata&format=raw&project_id=" + project_id + "&months=6";
	
		jQuery.ajax(url, {'success': function(data) {
			console.log("short chart data is " + data);
		
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
						label: "Classified",
						backgroundColor: '#00ba8a',
						borderColor: '#00ba8a',
						data: jsonObject.classified
					},{
						label: "Uploaded",
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
						text: 'Sequences Uploaded and Classified (6 months)'
					}
				}
			});
	
		}});
	}
	if ( jQuery('#progressChartMedium').length > 0  ) {
		project_id = jQuery('#progressChartMedium').attr("data-project-id");
		url = BioDiv.root + "&view=projectdata&format=raw&project_id=" + project_id + "&months=12";
	
		jQuery.ajax(url, {'success': function(data) {
			console.log("medium chart data is " + data);
		
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
						label: "Classified",
						backgroundColor: '#00ba8a',
						borderColor: '#00ba8a',
						data: jsonObject.classified
					},{
						label: "Uploaded",
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
						text: 'Sequences Uploaded and Classified (1 year)'
					}
				}
			});
	
		}});
	}
	
	if ( jQuery('#progressChartLong').length > 0  ) {
		project_id = jQuery('#progressChartLong').attr("data-project-id");
		url = BioDiv.root + "&view=projectdata&format=raw&project_id=" + project_id + "&months=36";
	
		jQuery.ajax(url, {'success': function(data) {
			console.log("long chart data is " + data);
		
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
						label: "Classified",
						backgroundColor: '#00ba8a',
						borderColor: '#00ba8a',
						data: jsonObject.classified
					},{
						label: "Uploaded",
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
						text: 'Sequences Uploaded and Classified (3 years)'
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
						label: "Animals",
						backgroundColor: ["#32553f","#00ba8a","#66a381","#f6c67a","#d6da9c","#b4d0d0","#c9e6d1"],
						data: jsonObject.animals
					}
					]
				},
				// Configuration options go here
				options: {
					title: {
						display: true,
						text: 'All Classifications by Species'
					}

				}
			});
	
		}});
	}
	
	if ( jQuery('#animalsBarChart').length > 0  ) {
		project_id = jQuery('#animalsBarChart').attr("data-project-id");
		url = BioDiv.root + "&view=projectanimals&format=raw&project_id=" + project_id;
	
		jQuery.ajax(url, {'success': function(data) {
			
			// Now get the json data into the chart and display it.
			var jsonObject = JSON.parse ( data );
			
			var ctx = document.getElementById('animalsBarChart').getContext('2d');
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'bar',

				// The data for our dataset
				data: {
					labels: jsonObject.labels,
					datasets: [{
						label: "Number of classifications",
						backgroundColor: ["#32553f","#00ba8a","#66a381","#f6c67a","#d6da9c","#b4d0d0","#c9e6d1"],
						data: jsonObject.animals
					}
					]
				},
				
				// Configuration options go here
				options: {
					title: {
						display: true,
						text: 'All Classifications by Species'
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
	
		
});
