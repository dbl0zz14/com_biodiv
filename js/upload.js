

// function uploadFiles(e) {
	
	// //let url = BioDiv.root + "&task=add_upload&site_id=".$this->site_id.
	
	// e.preventDefault();
	
	// //let formId = jQuery(this).attr('id');
	
	// const fd = new FormData(this);
	
	// let success = false;
	
	// success = validateUploadForm(fd);
	
	// if ( success ) {
		
		// const url = BioDiv.root + "&task=add_upload";
		// //url = BioDiv.root + "&view=newresourceset&format=raw";
		
		// //jQuery(".loader").removeClass("invisible");
	
		// jQuery.ajax({
			// type: 'POST',
			// url: url,
			// data: fd,
			// processData: false,
			// contentType: false
		// });
		
	// }
	// else {
		// jQuery("#uploadErrorMsg").show();
	// }
	
// }




function validateUploadForm ( fd ) {
	
	jQuery("#uploadDateErrorMsg").hide();
	
	let success = false;
	
	// For uploadForm, dates cannot be in the future and collection must be after deployment
	const collDate = jQuery('#collection_date').datepicker("getDate");
	const deplDate = jQuery('#deployment_date').datepicker("getDate");
	
	if ( collDate < deplDate ) {
		
		console.log("Error found");
		jQuery ('[name=deployment_date]').addClass('invalid');
		jQuery ('[name=collection_date]').addClass('invalid');
		jQuery("#uploadDateErrorMsg").show();
		
	}
	else {
		
		jQuery ('[name=deployment_date]').removeClass('invalid');
		jQuery ('[name=collection_date]').removeClass('invalid');
		success = true;
		
	}
	
	return success;
}




jQuery(document).ready(function(){
	
	// Get the browser timezone name
	const tzid = Intl.DateTimeFormat().resolvedOptions().timeZone;
	console.log("Timezone: " + tzid);
	
	// Default the camera timezone to the browser timezone
	//let zoneOptions = document.getElementById("timezone").options;
	let tzIndex = -1;
	let tzEl = document.getElementById(tzid);
	if ( tzEl ) {
		tzIndex = tzEl.index;
	}
	
	if ( tzIndex > -1 ) {
		document.getElementById("timezone").selectedIndex = tzIndex;
	}
	
	jQuery('.mw_help').click(function (){
		console.log("Display help");
		jQuery('#help_modal').modal('show');		
	});
	
	var pickerOptions = {"format": "yyyy-mm-dd",
			     "endDate": BioDiv.max_date};
				 

	var deploymentOptions = jQuery.extend({"todayHighlight": true}, pickerOptions);
	
	jQuery('#deployment_date').datepicker(deploymentOptions);
	jQuery('#deployment_date').datepicker('setDate', BioDiv.min_date);
	jQuery('#deployment_hours').val(BioDiv.min_hours);
	jQuery('#deployment_mins').val(BioDiv.min_mins);
	jQuery('#collection_date').datepicker(pickerOptions);
    jQuery('#collection_date').datepicker('setDate', BioDiv.max_date);
	jQuery('#collection_hours').val(BioDiv.max_hours);
	jQuery('#collection_mins').val(BioDiv.max_mins);
	
	
});
