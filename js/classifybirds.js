jQuery(document).ready(function(){
	
	const maxClassifications = BioDiv.maxclass;
	
	/*
	var wavesurfer = WaveSurfer.create({
    container: '#waveContainer'
	});
	
	wavesurfer.load('http://localhost/rhombus/naturesaudio/biodivimages/person_824/site_1/birdsong_bullfinch.mp3');
	*/
	//wavesurfer.load('../biodivimages/person_824/site_1/testfile.wav');
	

	removeClicks = function (){
		jQuery('.remove_animal').click(function (){
		    let id = jQuery(this).attr("id");
		    let idbits = id.split("_");
		    let animal_id = idbits.pop();
		    let removeurl = BioDiv.root + "&task=remove_animal_single_tag&format=raw&animal_id=" + animal_id;
			//parentEl = document.getElementById('remove_animal_' + animal_id).parentElement.id;
			let parenttag = jQuery('#remove_animal_' + animal_id).parent();
			parenttag.load(removeurl, "", BioDiv.removeClick);
			parenttag.remove();
			
		});
		if (document.getElementById('nothingDisabled')) {
			jQuery('#control_content_86').prop('disabled', true);
		}
		else {
			jQuery('#control_content_86').prop('disabled', false);
		}
	}

	BioDiv.removeClick = function (){
	if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
		}
		else {
			removeClicks();
		}
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
			console.log("No location given");
			jQuery('#no_map').show();
		}
		
	};
	
	addClassificationByForm = function () {
		// Check still logged in
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
			return;
		}
		
		// Check max classifications not yet reached
		let numClass = jQuery(".remove_animal").length;
		
		console.log("Current num classifications: " + numClass );
		
		if ( numClass == maxClassifications ) {
			console.log("Error: reached max classifications - " + maxClassifications);
			jQuery('#too_many_modal').modal('show');
			return;
		}
		
		// OK to add a new classn
		formData = jQuery('#classify-form').serialize();
		url = BioDiv.root + "&task=add_bird_single_tag&format=raw";
		
		let nextNum = numClass + 1;
		let newDivId = "classification_" + nextNum;
		
		// Create a new div
		let tags = jQuery("#classify_tags").append("<div class='tagcontainer singletag-classification'></div>" );
		
		// And load the button
		jQuery('.tagcontainer').last().load(url, formData, BioDiv.removeClick);
		jQuery('.nothing-classification').remove();
		
	}
	
	addClassificationById = function ( id, notes ) {
		// Check still logged in
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
			return;
		}
		
		// Check max classifications not yet reached
		let numClass = jQuery(".remove_animal").length;
		
		console.log("Current num classifications: " + numClass );
		
		if ( numClass == maxClassifications ) {
			console.log("Error: reached max classifications - " + maxClassifications);
			jQuery('#too_many_modal').modal('show');
			return;
		}
		
		// OK to add a new classn
		url = BioDiv.root + "&task=add_bird_single_tag&format=raw&species=" + id + "&notes=" + notes;
		
		let nextNum = numClass + 1;
		let newDivId = "classification_" + nextNum;
		
		// Create a new div
		let tags = jQuery("#classify_tags").append("<div class='tagcontainer singletag-classification'></div>" );
		
		// And load the button
		jQuery('.tagcontainer').last().load(url, BioDiv.removeClick);
		if ( id != "86" ) jQuery('.nothing-classification').remove();
		
	}
	
	jQuery('.species_select').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		jQuery('#species_value').attr('value', species_id);
		
		// Reset the defaults
		jQuery('#classify_number').attr('value', 1);
		checkRadioDefault ('gender');
		checkRadioDefault ('age');
		checkRadioDefault ('sure');
		jQuery('#classify_notes').val('');
		
		
		jQuery('#species_helplet').empty();
		jQuery('.species_classify').show();
		var url = BioDiv.root + "&view=ajax&format=raw&option_id=" + species_id;
		jQuery('#species_helplet').load(url);
		
	});
	
	jQuery('.song_select').click(function (){
				
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		addClassificationById(species_id, "song");
		
	});


	jQuery('.call_select').click(function (){
		
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		addClassificationById(species_id, "call");
		
	});


jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		
		addClassificationByForm();
		
	});

		
	
	jQuery('.classify_control').click(function (){
		
		let id = jQuery(this).attr("id");
		
		addClassificationById (id);
		
	});
	
	jQuery('#control_map').click(function (){
		console.log("Display location");
		jQuery('#map_modal').modal('show');
		drawMap();
		
	});
	
	jQuery('#control_nextseq').click(function (){
	id = jQuery(this).attr("id");
	var sideBarToggled = jQuery('#wrapper').is(".toggled");
	var extra = "";
	if ( sideBarToggled ) extra = "&toggled=" + "1";
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
	
	});
	

	// Add any remove click functions on refresh.
	removeClicks();
	
	
	
	jQuery('.species-carousel-control').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#species-indicators li').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to remove favourite status', 'placement': 'bottom'});
	jQuery('#not-favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to make this one of your favourites', 'placement': 'bottom'});
	jQuery('.species-tab').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Filter list of species', 'placement': 'top'});
	jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
	jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});

	
	
	
});

	
    