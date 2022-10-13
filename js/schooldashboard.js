
	
	
jQuery(document).ready(function(){
	
	
	// ------------------------ Initialise
	
	// let eventsUrl = BioDiv.root + "&view=events&format=raw";
	// jQuery("#eventLog").load(eventsUrl);
	
	let url = BioDiv.root + "&view=schoolspotlight&format=raw";
	jQuery("#schoolSpotlight").load(url, setReloadPage);
	
	
});
