
	
function addSchools () {
	
	let id = jQuery(this).attr("id");
	const idbits = id.split("_");
	let ecolId = idbits.pop();
	
	let ecolName = jQuery("#ecolName_" + ecolId).text();
	
	jQuery("#ecol").attr('value', ecolId);
	jQuery("#ecolName").data("ecolId", ecolId);
	jQuery("#ecolName").text(ecolName);
	
	jQuery(".schoolCheckbox").attr("checked", false);
	jQuery(".currSchool_" + ecolId).each ( function() {
		let elId = jQuery(this).attr("id");
		const elIdbits = elId.split("_");
		let schoolId = elIdbits.pop();
		
		jQuery("#school_" + schoolId).attr("checked", true);
	});
	
}


function setTaskUploadButton () {
	
	jQuery('#taskUploadForm').submit(createResourceSet);
}

function pairEcologist(e) {
	
	let url = BioDiv.root + "&task=pair_ecologist";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "resourceUploadForm" ) {
		
		success = validatePairForm(fd);
	}
	
	if ( success ) {
		
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(pairingComplete);
		
	}
	
	
}

function validatePairForm () {
	
	return success;
	
}

function pairingComplete () {
	
	reloadCurrentPage();
}

jQuery(document).ready(function(){

	jQuery(".addSchools").click(addSchools);
	jQuery('#pairForm').submit(pairEcologist);
	
});


