jQuery(document).ready(function(){

	
	jQuery('#project_select').change(function (){
			
			// Remove any report as project has been changed
			jQuery('#report_display').empty();
			
			jQuery('.report-btn h4').removeClass('text-info');
			
	});
			
	
});
