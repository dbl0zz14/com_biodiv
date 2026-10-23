


function kioskAboutSuccess () {
	
	jQuery('video').on("loadeddata", function() {
		jQuery('video').attr('controlsList', 'nodownload');
		jQuery('video').bind('contextmenu',function() { return false; });
		jQuery('video').attr('disablePictureInPicture', 'true');
	}); 
	
}



	
    
