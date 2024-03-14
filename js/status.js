jQuery(document).ready(function(){
	
	
	const loadingMsg = BioDiv.loadingMsg;
	
	// Show a message while the image/video/audio loads
	jQuery(".classify_btn").click(function (){
		jQuery(this).text(String(loadingMsg));
		jQuery(".loader").removeClass('invisible');
			
	});
	
	
	
});

	
    
