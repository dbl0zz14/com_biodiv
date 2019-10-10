jQuery(document).ready(function(){

	removeClicks = function (){
		jQuery('.remove_animal').click(function (){
		    id = jQuery(this).attr("id");
		    idbits = id.split("_");
		    animal_id = idbits.pop();
		    removeurl = BioDiv.root + "&task=remove_animal_single_tag&format=raw&animal_id=" + animal_id;
			parentEl = document.getElementById('remove_animal_' + animal_id).parentElement.id;
		    if ( parentEl == 'first_classification' ) {
				jQuery('#first_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else if ( parentEl == 'second_classification' ) {
				jQuery('#second_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else if ( parentEl == 'third_classification'  ) {
				jQuery('#third_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else if ( parentEl == 'fourth_classification'  ) {
				jQuery('#fourth_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else if ( parentEl == 'fifth_classification'  ) {
				jQuery('#fifth_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else if ( parentEl == 'sixth_classification'  ) {
				jQuery('#sixth_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else {
				console.log("Error unexpected parent: parent element id = " + parentEl);
			}
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
			let lat = parseFloat(BioDiv.latitude);
			let lng = parseFloat(BioDiv.longitude);
			//console.log("lat = " + lat);
			//console.log("lng = " + lng);
			
			let myLatlng = new google.maps.LatLng(lat, lng);
		
			let myOptions = {
				zoom: 5,
				center: myLatlng,
				mapTypeId: google.maps.MapTypeId.TERRAIN
			}
			
			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			
			var marker = new google.maps.Marker({
				position: myLatlng, 
				map: map,
				draggable:false
			});
		}
		catch(err){
			console.log("No location given");
			jQuery('#no_map').show();
		}
		
	};
	
	jQuery('.species_select').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		jQuery('#species_value').attr('value', species_id);
		jQuery('#classify_number').attr('value', 1);
		jQuery('#classify_gender').val(84);
		jQuery('#classify_age').val(85);
		
		
		jQuery('#species_helplet').empty();
		jQuery('.species_classify').show();
		var url = BioDiv.root + "&view=ajax&format=raw&option_id=" + species_id;
		jQuery('#species_helplet').load(url);
		
	});


	jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		formData = jQuery('#classify-form').serialize();
		//url = BioDiv.root + "&task=add_animal&format=raw";
		url = BioDiv.root + "&task=add_animal_single_tag&format=raw";
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
		}
		// How many animals do we have so far?
		else if ( document.getElementById('first_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#first_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('second_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#second_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('third_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#third_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('fourth_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#fourth_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('fifth_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#fifth_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('sixth_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#sixth_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else {
			console.log("Error: already have six classifications and a seventh requested");
			jQuery('#too_many_modal').modal('show');
		}
		//jQuery('#classify_tags').load(url, formData, BioDiv.removeClick);
		
	});

		
	
	jQuery('.classify_control').click(function (){
		id = jQuery(this).attr("id");
		url = BioDiv.root + "&task=add_animal_single_tag&format=raw&species=" + id;
		// How many animals do we have so far?
		if ( document.getElementById('first_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#first_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('second_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#second_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('third_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#third_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('fourth_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#fourth_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('fifth_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#fifth_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('sixth_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#sixth_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else {
			console.log("Error: already have six classifications and a seventh requested");
			jQuery('#too_many_modal').modal('show');
		}
		
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

	
    
