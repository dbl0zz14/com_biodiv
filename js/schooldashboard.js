
	
	
jQuery(document).ready(function(){
	
	
	// ------------------------ Initialise
	
	// let eventsUrl = BioDiv.root + "&view=events&format=raw";
	// jQuery("#eventLog").load(eventsUrl);
	
	let url = BioDiv.root + "&view=schoolspotlight&format=raw";
	jQuery("#schoolSpotlight").load(url, setReloadPage);
	
	
	jQuery("#policiesDone").click(function () {
		
		jQuery("#policiesArea").addClass("hidden");
		jQuery("#avatarArea").removeClass("hidden");
	});
	
	jQuery("#changeAvatar").click( function () {
		jQuery("#goToDash").addClass("hidden");
		jQuery("#avatarSavedArea").addClass("hidden");
		jQuery("#avatarArea").removeClass("hidden");
		
	});
});
