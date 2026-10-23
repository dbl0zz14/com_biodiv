jQuery(document).ready(function(){
	
	// Pick up text string variables - could be non-English
	const waitText = BioDiv.waitText;
	const doneText = BioDiv.doneText;
	const genText = BioDiv.genText;
	
	
	clearReport = function () {
		
		jQuery('#report_display').empty();
		jQuery("#data_warning").hide();
	}
	
	setDashSummaryCollapsed = function ( isActive = false ) {
		
		if ( isActive ) {
			jQuery("#dash_summary").removeClass("collapsed");
			jQuery('.collapse').collapse('hide');
		}
		else {
			jQuery("#dash_summary").addClass("collapsed");
		}
	}
	
	
	
	// -------------------------------  Dashboard summary stuff
	
	displaySummary = function () {
		
		let url = BioDiv.root + "&view=dashsummary&format=raw";
		
		jQuery('#report_display').load(url);
		
	};
			
	// -------------------------------  End of Spotter status stuff
	
	
	
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
	
	// displayTrapperUploadChart = function () {
		
		// let siteId = jQuery('#site_select').val();

		// if ( jQuery('#userProgressChart').length > 0 ) {
			// //project_id = jQuery('#userProgressChart').attr("data-project-id");
			// url = BioDiv.root + "&view=discoveruseruploads&format=raw";
			
			// if ( siteId ) url += "&site=" + siteId;
		
			// jQuery.ajax(url, {'success': function(data) {
			
				// // Now get the json data into the chart and display it.
				// var jsonObject = JSON.parse ( data );
				
				// if(window.uploadChart !== undefined && window.uploadChart !== null){
					// window.uploadChart.destroy();
				// }
				
				// var ctx = document.getElementById('userProgressChart').getContext('2d');
				
				// window.uploadChart = new Chart(ctx, {
					// // The type of chart we want to create
					// type: 'line',

					// // The data for our dataset
					// data: {
						// labels: jsonObject.labels,
						// datasets: [{
							// label: jsonObject.cla_label, //"Classified",
							// backgroundColor: jsonObject.colormap[0],
							// borderColor: jsonObject.colormap[0],
							// data: jsonObject.classified
						// },{
							// label: jsonObject.upl_label, //"Uploaded",
							// backgroundColor: jsonObject.colormap[1],
							// borderColor: jsonObject.colormap[1],
							// data: jsonObject.uploaded
						// }
						// ]
					// },

					// // Configuration options go here
					// options: {
						// title: {
							// display: false,
							// text: jsonObject.title, //'Sequences Uploaded and Classified (6 months)'
							// position: 'bottom'
						// },
						// legend: {
							// position: 'bottom',
							// align: 'end'
						// },
						// scales: {
							// yAxes: [{
								// ticks: {
									// beginAtZero:true,
									// autoSkip:false,
									// precision:0
								// }
							// }],
							// xAxes: [{
								// ticks: {
									// autoSkip:false
								// }
							// }]
						// }
					// }
				// });		
			// }});
		// }
	// }
			
	// // -------------------------------  End of Trapper status stuff
	
	
	// // -------------------------------  Chart stuff
	
	// displayCharts = function () {
		
		// let url = BioDiv.root + "&view=dashcharts&format=raw";
		
		// jQuery('#report_display').load(url, updateCharts);
		
	// };
	
	
	// // Called once on Chart first load
	// updateCharts = function () {
		// getCharts();
		// addChartsEvents();
	// }
	
	
	// // Get the charts on chart load or site select
	// getCharts = function () {
		
		// // This is also displayed on trapper summary
		// displayTrapperUploadChart();

		// displayTopSpeciesChart();
		
		// displayRareSpeciesChart();
		
		// //displayNothingHumanChart();
		
		// displayTopSeqSpeciesChart();
		
	
	// }
			
			
	// addChartsEvents = function () {
		
		// jQuery('.charts_select').change(function (){
			
			// if(window.uploadChart !== undefined && window.uploadChart !== null){
				// window.uploadChart.destroy();
			// }
				
			// if(window.topChart !== undefined && window.topChart !== null){
				// window.topChart.destroy();
			// }
			
			// if(window.rareChart !== undefined && window.rareChart !== null){
				// window.rareChart.destroy();
			// }
			
			// if(window.nothHumChart !== undefined && window.nothHumChart !== null){
				// window.nothHumChart.destroy();
			// }
				
			// if(window.topSeqChart !== undefined && window.topSeqChart !== null){
				// window.topSeqChart.destroy();
			// }
				
			// getCharts();
		
		// });
		
		
	// }
	
	// displayTopSpeciesChart = function () {
		
		// let siteId = jQuery('#site_select').val();
	
		// if ( jQuery('#topSpeciesChart').length > 0  ) {
			// url = BioDiv.root + "&view=discoveruseranimals&format=raw" ;
			
			// if ( siteId ) url += "&site=" + siteId;
		
			// jQuery.ajax(url, {'success': function(data) {
				// //console.log(data);
				// console.log("Top species callback");
				
				// // Now get the json data into the chart and display it.
				// var jsonObject = JSON.parse ( data );
				// //Chart.defaults.global.maintainAspectRatio = false;
				
				// if(window.topChart !== undefined && window.topChart !== null){
					// window.topChart.destroy();
				// }
					
				// if ( jsonObject.labels.length > 0 ) {
				
					// var ctx = document.getElementById('topSpeciesChart').getContext('2d');
					
					// window.topChart = new Chart(ctx, {
						// // The type of chart we want to create
						// type: 'bar',

						// // The data for our dataset
						// data: {
							// labels: jsonObject.labels,
							// datasets: [{
								// label: jsonObject.ani_label, // "Number of classifications"
								// backgroundColor: jsonObject.colormap,
								// data: jsonObject.animals
							// }
							// ]
						// },
						
						// // Configuration options go here
						// options: {
							// title: {
								// display: false,
								// text: jsonObject.title, // 'All Classifications by Species'
								// position: 'bottom'
							// },
							// legend: {
								// display: false,
							// },
							// scales: {
								// yAxes: [{
									// ticks: {
										// beginAtZero:true,
										// autoSkip:false,
										// precision:0
									// }
								// }],
								// xAxes: [{
									// ticks: {
										// autoSkip:false
									// },
									// gridlines: {
										// display:false
									// }
								// }]
							// }

						// }
					// });
				// }
				// else {
					// console.log("No classifications");
				// }
			// }});
		// }	
	// }		
	

	// displayRareSpeciesChart = function () {
		
		// let siteId = jQuery('#site_select').val();
	
		// if ( jQuery('#rareSpeciesChart').length > 0  ) {
			// url = BioDiv.root + "&view=discoveruseranimals&format=raw&rare=1" ;
		
			// if ( siteId ) url += "&site=" + siteId;
		
			// jQuery.ajax(url, {'success': function(data) {
				// console.log(data);
				// // Now get the json data into the chart and display it.
				// var jsonObject = JSON.parse ( data );
				
				// if(window.rareChart !== undefined && window.rareChart !== null){
					// window.rareChart.destroy();
				// }
						
				// if ( jsonObject.labels.length > 0 ) {
				
					// var ctx = document.getElementById('rareSpeciesChart').getContext('2d');
					// window.rareChart = new Chart(ctx, {
						// // The type of chart we want to create
						// type: 'bar',

						// // The data for our dataset
						// data: {
							// labels: jsonObject.labels,
							// datasets: [{
								// label: jsonObject.ani_label, // "Number of classifications",
								// backgroundColor: jsonObject.colormap,
								// data: jsonObject.animals
							// }
							// ]
						// },
						
						// // Configuration options go here
						// options: {
							// title: {
								// display: false,
								// text: jsonObject.title, // 'All Classifications by Species'
								// position: 'bottom'
							// },
							// legend: {
								// display: false,
							// },
							// scales: {
								// yAxes: [{
									// ticks: {
										// beginAtZero:true,
										// autoSkip:false,
										// precision:0
									// }
								// }],
								// xAxes: [{
									// ticks: {
										// autoSkip:false
									// }
								// }]
							// }

						// }
					// });
				// }
				// else {
				// }
			// }});
		// }	
	// }


	// displayNothingHumanChart = function () {
		
		// let siteId = jQuery('#site_select').val();
	
		// if ( jQuery('#nothingHumanChart').length > 0  ) {
			// url = BioDiv.root + "&view=discoverusernothinghuman&format=raw" ;
		
			// if ( siteId ) url += "&site=" + siteId;
		
			// jQuery.ajax(url, {'success': function(data) {
				// console.log(data);
				// // Now get the json data into the chart and display it.
				// var jsonObject = JSON.parse ( data );
				
				// if(window.nothHumChart !== undefined && window.nothHumChart !== null){
					// window.nothHumChart.destroy();
				// }
					
				// if ( jsonObject.labels.length > 0 ) {
				
					// var ctx = document.getElementById('nothingHumanChart').getContext('2d');
					// window.nothHumChart = new Chart(ctx, {
						// // The type of chart we want to create
						// type: 'pie',

						// // The data for our dataset
						
						// data: {
							// labels: jsonObject.labels,
							// datasets: [{
								// label: jsonObject.ani_label, // "Animals",
								// backgroundColor: jsonObject.colormap,
								// data: jsonObject.animals
							// }
							// ]
						// },
						// // Configuration options go here
						// options: {
							// title: {
								// display: false,
								// text: jsonObject.title,
								// position: 'bottom'
							// },
							// legend: {
								// position: 'right',
								// align: 'end'
							// }

						// }
					// });	
						
					// jQuery('#nothingHumanChart').parent().width("350px");
				// }
				// else {
				// }
			// }});
		// }	
	// }	
	
	
	// displayTopSeqSpeciesChart = function () {
		
		// let siteId = jQuery('#site_select').val();
	
		// if ( jQuery('#topSeqSpeciesChart').length > 0  ) {
			// url = BioDiv.root + "&view=discoveruserseqanimals&format=raw" ;
			
			// if ( siteId ) url += "&site=" + siteId;
		
			// jQuery.ajax(url, {'success': function(data) {
				// //console.log(data);
				// console.log("Top species callback");
				
				// // Now get the json data into the chart and display it.
				// var jsonObject = JSON.parse ( data );
				// //Chart.defaults.global.maintainAspectRatio = false;
				
				// if(window.topSeqChart !== undefined && window.topSeqChart !== null){
					// window.topSeqChart.destroy();
				// }
					
				// if ( jsonObject.labels.length > 0 ) {
				
					// var ctx = document.getElementById('topSeqSpeciesChart').getContext('2d');
					
					// window.topSeqChart = new Chart(ctx, {
						// // The type of chart we want to create
						// type: 'bar',
						// indexAxis: 'y',

						// // The data for our dataset
						// data: {
							// labels: jsonObject.labels,
							// datasets: [{
								// label: jsonObject.ani_label, // "Number of classifications"
								// backgroundColor: jsonObject.colormap,
								// data: jsonObject.animals
							// }
							// ]
						// },
						
						// // Configuration options go here
						// options: {
							// title: {
								// display: false,
								// text: jsonObject.title, // 'All Classifications by Species'
								// position: 'bottom'
							// },
							// legend: {
								// display: false,
							// },
							// scales: {
								// xAxes: [{
									// ticks: {
										// beginAtZero:true,
										// autoSkip:false,
										// precision:0
									// }
								// }],
								// yAxes: [{
									// ticks: {
										// autoSkip:false
									// },
									// gridlines: {
										// display:false
									// }
								// }]
							// }

						// }
					// });
				// }
				// else {
					// console.log("No classifications");
				// }
			// }});
		// }	
	// }		
	

	
	// // -------------------------------  End of Chart stuff
	
	
	
	
	// -------------------------------  Analysis stuff
	
	displayAnalysis = function ( analysisType ) {
		
		const url = BioDiv.root + "&view=dashanalysis&format=raw&type=" + analysisType;
		
		jQuery('#report_display').load(url, setAnalysis);
		
	};
	
	
	function setAnalysis () {
		
		jQuery('#squirrelDensitySave').click(function (){
			
			formData = jQuery('#squirrelDensityForm').serialize();
			
			url = BioDiv.root + "&task=add_density_request&format=raw";
			
			// And send the report
			jQuery.post(url, formData, function(data) {
				
				jQuery('#squirrelDensityRequestDone').html(data);
			});
			
			//jQuery('#squirrelDensityRequestDone').post(url, formData);
			
		});
	}
			
	// -------------------------------  End of Analysis stuff
	
	
	
	
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
				
				
	jQuery('#dash_summary').click(function (){
		
		clearReport();
		setDashSummaryCollapsed ( true );
		displaySummary();
		
	});
	
	
	// jQuery('#spotter_status').click(function (){
		
		// clearReport();
		// setDashSummaryCollapsed ( false );
		// displaySpotter();
		
	// });
	
	
	// jQuery('#trapper_status').click(function (){
		
		// clearReport();
		// setDashSummaryCollapsed ( false );
		// displayTrapper();
		
	// });
	
	
	// jQuery('#all_my_likes').click(function (){
		
		// clearReport();
		// setDashSummaryCollapsed ( false );
		// displayLikes(0, false, true);
		
	// });
	
		
	// jQuery('#my_likes').click(function (){
		
		// clearReport();
		// setDashSummaryCollapsed ( false );
		// displayLikes(0);
		
	// });
	
		
	// jQuery('#others_likes').click(function (){
		
		// clearReport();
		// setDashSummaryCollapsed ( false );
		// displayLikes(0, true);
		
	// });
	
		
	// jQuery('#site_charts').click(function (){
		
		// clearReport();
		// setDashSummaryCollapsed ( false );
		// displayCharts();
		
	// });
	
	
	// jQuery('.analysis-btn').click(function (){
		
		// clearReport();
		// setDashSummaryCollapsed ( false );
		// const analysisType = jQuery(this).attr('data-analysis-type');
		// displayAnalysis( analysisType );
		
	// });
	
		
	// Show the spotter page initially
	//jQuery('.show_options').first().trigger("click");
	jQuery('#dash_summary').trigger( "click" );
	
	// ------------------------  End of dashboard controls
	
	
	
});
