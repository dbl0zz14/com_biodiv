


jQuery(document).ready(function(){

	// Set up the kiosk page
	kioskPage = BioDiv.kiosk;
	

	// On start up, load the start-kiosk page
	let url = BioDiv.root + "&view=kioskstart&format=raw";

	jQuery('#kiosk').load(url, kioskStartSuccess);


	setupTimers();
	
	// Stop kiosk users right clicking using long press
	document.addEventListener('contextmenu', event => event.preventDefault());
	
	jQuery('#home_button').click( function () {
		
		classifyCount = 0;
		
		let url = BioDiv.root + "&view=kioskstart&format=raw";
	
		jQuery('#kiosk').load(url, kioskStartSuccess);
	});
	
	
	
});

	
    
