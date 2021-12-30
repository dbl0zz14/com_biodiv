jQuery(document).ready(function(){

	
	jQuery('#project_select').change(function (){
			
			// Remove any report as project has been changed
			jQuery('#report_display').empty();
			
			jQuery('.report-btn h4').removeClass('text-info');
			
			let projectId = jQuery(this).find(":selected").val();
			jQuery('*[data-project_id]').hide();
			jQuery('*[data-project_id="' + projectId + '"]').show();
			
	});
			
	
});
