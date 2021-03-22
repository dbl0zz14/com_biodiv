		
	addFullScreenFnly = function () {
		jQuery('#fullscreen-button').click(function (){
			var photos = document.getElementById('photoCarousel');
			if("requestFullscreen" in photos) 
			{
				photos.requestFullscreen();
			} 
			else if ("webkitRequestFullscreen" in photos) 
			{
				photos.webkitRequestFullscreen();
			} 
			else if ("mozRequestFullScreen" in photos) 
			{
				photos.mozRequestFullScreen();
			} 
			else if ("msRequestFullscreen" in photos) 
			{
				photos.msRequestFullscreen();
			}
					
		});
		
		jQuery('#fullscreen-exit-button').click(function (){
			
			if(document.exitFullscreen) 
			{
				document.exitFullscreen();
			} 
			else if (document.webkitExitFullscreen) 
			{
				document.webkitExitFullscreen();
			} 
			else if (document.mozCancelFullScreen) 
			{
				document.mozCancelFullScreen();
			} 
			else if (document.msExitFullscreen) 
			{
				document.msExitFullscreen();
			}
			else {
				console.log("No exit found");
				
			}		
		});
	}
	
	
	addPlayMedia = function () {
		jQuery('.media-btn').click(function (){
			
			// Clear media carousel
			jQuery('#media_carousel').empty();
		
			jQuery('#carousel_modal').modal('show');
			
			
			let sequence_id = jQuery(this).attr('data-seq_id');
			
			var url = BioDiv.root + "&view=mediacarousel&format=raw&sequence_id=" + sequence_id;
			
			jQuery.ajax(url, {'success': function(data) {
				//window.location.reload(true);
				// Try all three media types
				jQuery('#media_carousel').append(data);
				
				addFullScreenFnly();
				//jQuery('#carousel_modal').modal('show');
				
				jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
				jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});
			}
			});
			
		});
	}




	
    
