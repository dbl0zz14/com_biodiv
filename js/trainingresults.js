jQuery(document).ready(function(){
	
		
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
		
		
	

	drawMap = function (){
		
		jQuery('#no_map').hide();
		
		try{
			
			let south = parseFloat(BioDiv.south);
			let west = parseFloat(BioDiv.west);
			let north = parseFloat(BioDiv.north);
			let east = parseFloat(BioDiv.east);
			
			let sw = new google.maps.LatLng(south, west);
			let ne = new google.maps.LatLng(north, east);
			
			let posBounds = new google.maps.LatLngBounds (sw, ne);
		
			let mapOptions = {
				zoom: 8,
				center: sw,
				mapTypeId: google.maps.MapTypeId.TERRAIN
			}
			
			var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			
			let rectOptions = {
				bounds: posBounds,
				fillColor: "#00ba8a",
				fillOpacity: 0.5,
				strokeWeight: 1,
				//strokeColour: "red",
				//strokeOpacity: 0.5,
				map: map,
				draggable:false
			}
			
			var rect = new google.maps.Rectangle(
				rectOptions
			);
			
		}
		catch(err){
			console.log(err.msg);
			jQuery('#no_map').show();
		}
		
	};
	
	
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
		}
		});
		
	});
	
	jQuery('.challenge-btn').click(function (){
		
		// Clear media carousel
		let sequence_id = jQuery(this).attr('data-seq_id');
		
		jQuery('#currSequenceId').val(sequence_id);
		
		let expertSpecies = jQuery('#expert_' + sequence_id).text();
		
		jQuery('#challenge_expert').val(expertSpecies);
	
		let userSpecies = jQuery('#user_' + sequence_id).text();
		
		jQuery('#challenge_suggestion').val(userSpecies);
	
		jQuery('#challenge_modal').modal('show');
		
	});
	
	jQuery('#challenge-save').click(function (){
		
		formData = jQuery('#challengeForm').serialize();
		
		let sequenceId = jQuery('#currSequenceId').val();
		/*
		url = BioDiv.root + "&task=get_photo&format=raw&action=" + id + extra;
		jQuery.ajax(url, {'success': function() {
			window.location.reload(true);
			if (document.getElementById('sub-photo-1')) {
				jQuery('#control_nextseq').prop('disabled', true);
			}
			else {
				jQuery('#control_nextseq').prop('disabled', false);
			}
		}});
		*/
		
		url = BioDiv.root + "&task=add_challenge&format=raw";
		
		// And send the challenge
		jQuery('#challengeDone_' + sequenceId).load(url, formData);
		
		// And disable the challenge button
		jQuery('#challengeBtn_' + sequenceId).attr("disabled", true);
		
	});
	
	addFullScreenFnly();
	
	
	jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
	jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});
});

	
    
