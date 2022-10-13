jQuery(document).ready(function(){
	
	
	function usersLoaded () {
		
		setReloadPage();
		jQuery("#saveUsers").click(saveUsers);
		
	}
	
	
	function displayUsersPage () {
		
		let currentProjectId = jQuery("#project_select").children("option:selected").val();
		
		let usersUrl = BioDiv.root + "&view=projectusers&format=raw&id=" + currentProjectId;
		jQuery('#report_display').load(usersUrl, usersLoaded);
		
	}
	
	
	function saveUsers () {
		
		let userEmails = jQuery('#emailsInput').val();
		
		let currentProjectId = jQuery("#project_select").children("option:selected").val();
		
		
		let postData = {
			id: currentProjectId,
			emails: userEmails
		};
		
		let url = BioDiv.root + "&task=addProjectUsers&format=raw";
		
		jQuery.post(url, postData, function( data ) {
			jQuery( "#report_display" ).html( data );
			usersLoaded();
		});
		
		
	}

	
	jQuery('#project_select').change(function (){
			
			// Remove any report as project has been changed
			jQuery('#report_display').empty();
			
			jQuery('.report-btn h4').removeClass('text-info');
			
			let projectId = jQuery(this).find(":selected").val();
			jQuery('*[data-project_id]').hide();
			jQuery('*[data-project_id="' + projectId + '"]').show();
			
	});
	

	jQuery("#projectUsersBtn").click(displayUsersPage);
	
});
