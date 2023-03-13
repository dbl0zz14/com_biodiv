
	
function approveRejectSchool () {
	
	let id = jQuery(this).attr("id");
	const idbits = id.split("_");
	let signupId = idbits.pop();
	
	jQuery("#approveSchoolForm input[name=signUpId]").attr('value', signupId);
	
}


function approveSchool ( e ) {
	
	let url = BioDiv.root + "&view=approveschool&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( success ) {
		
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(reloadCurrentPage);
		
	}
}


jQuery(document).ready(function(){

	jQuery(".approveSchool").click(approveRejectSchool);
	jQuery('#approveSchoolForm').submit(approveSchool);
	
});


