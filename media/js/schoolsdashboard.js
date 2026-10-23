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
			jQuery("#schools_dash_summary").removeClass("collapsed");
			jQuery('.collapse').collapse('hide');
		}
		else {
			jQuery("#schools_dash_summary").addClass("collapsed");
		}
	}
	
	
	
	// -------------------------------  MWS Dashboard summary stuff
	
	displayMWSSummary = function () {
		
		let url = BioDiv.root + "&view=schoolsdashsummary&format=raw";
		
		jQuery('#report_display').load(url);
		
	};
			
	// -------------------------------  End of Spotter status stuff
	
	
	
	// -------------------------------  Spotter status stuff
	
	displayMWSSpotter = function () {
		
		let url = BioDiv.root + "&view=schoolsdashspotter&format=raw";
		
		jQuery('#report_display').load(url);
		
	};
			
	// -------------------------------  End of MWS Spotter status stuff
	
	
	
	// -------------------------------  MWS Trapper status stuff
	
	displayMWSTrapper = function () {
		
		let url = BioDiv.root + "&view=schoolsdashtrapper&format=raw";
		
		jQuery('#report_display').load(url, displayTrapperUploadChart);
		
	};
			
	// -------------------------------  End of MWS Trapper stuff
	
	
	
	
	// -------------------------------  Dashboard controls
	
	jQuery('#schools_dash_summary').click(function (){
		
		clearReport();
		setDashSummaryCollapsed ( true );
		displayMWSSummary();
		
	});
	
	
	jQuery('#schools_spotter_status').click(function (){
		
		clearReport();
		setDashSummaryCollapsed ( false );
		displayMWSSpotter();
		
	});
	
	
	jQuery('#schools_trapper_status').click(function (){
		
		clearReport();
		setDashSummaryCollapsed ( false );
		displayMWSTrapper();
		
	});
	
	
	
	// Show the schools summary page initially
	
	jQuery('#schools_dash_summary').trigger( "click" );
	
	// ------------------------  End of dashboard controls
	
});
