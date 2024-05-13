jQuery(document).ready(function(){
	
	const maxClassifications = BioDiv.maxclass;
	const loadingMsg = BioDiv.loadingMsg;
	
	const numSpeciesPerPage = 36;
	
	removeClicks = function (){
		jQuery('.remove_animal').click(function (){
		    let id = jQuery(this).attr("id");
		    let idbits = id.split("_");
		    let animal_id = idbits.pop();
		    let removeurl = BioDiv.root + "&task=remove_animal_single_tag&format=raw&animal_id=" + animal_id;
			let parenttag = jQuery('#remove_animal_' + animal_id).parent();
			parenttag.load(removeurl, "", BioDiv.removeClick);
			parenttag.remove();
			
		});
		if (document.getElementById('nothingDisabled')) {
			jQuery('.nothing').prop('disabled', true);
		}
		else {
			jQuery('.nothing').prop('disabled', false);
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
		
		//let id = jQuery(this).attr("id");
		
		//addClassificationById (id);
		
		
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		addClassificationById(species_id);
		
	});
	
	
	jQuery('#control_nextseq').click(function (){
	
	// Add loading indications
	jQuery(".loader").removeClass("invisible");
	jQuery(this).text(loadingMsg);
	jQuery(".loader").removeClass('invisible');
	
	id = jQuery(this).attr("id");
	var sideBarToggled = jQuery('#wrapper').is(".toggled");
	var extra = "";
	if ( sideBarToggled ) extra = "&toggled=" + "1";
	url = BioDiv.root + "&task=get_photo&format=raw&action=" + id + extra;
	jQuery.ajax(url, {'success': function() {
		
			jQuery(".loader").addClass("invisible");
			window.location.reload(true);
			if (document.getElementById('sub-photo-1')) {
				jQuery('#control_nextseq').prop('disabled', true);
			}
			else {
				jQuery('#control_nextseq').prop('disabled', false);
			}
		}});
	
	});
	
	displaySpeciesPage = function ( start, len ) {
		
		jQuery(".species_group").hide();
		
		let displaySlice = jQuery(".tab-pane.active .match").slice(start, start + len);
		displaySlice.show();
		
		jQuery(".tab-pane.active .alwaysmatch").show();
		
		jQuery('.pagination li').removeClass('active');
		// NB have prev and next buttons too
		var activeChild = Math.round(start/len) + 2;
		jQuery('.pagination li:nth-child(' + activeChild + ')').addClass('active');
		
		// How many pages are there in the match list?
		let numMatches = jQuery(".tab-pane.active .match").length;
		let numPages = Math.ceil(numMatches/len);
		jQuery('.last-page').removeClass('last-page');
		
		if ( numPages == 1 ) {
			jQuery('.species_pagination').hide();
		}
		else {
			let lastDisplayedChild = parseInt(numPages+1);
			jQuery('.pagination li:nth-child(' + lastDisplayedChild + ')').addClass('last-page');
			
			jQuery('.species_pagination').show();
		
			// Show all then hide any pagination controls we don't need.
			jQuery('.pagination li').show();
			let formula = 'n + ' + parseInt(numPages+2);
			jQuery('.pagination li:nth-child(' + formula + ')').hide();
			jQuery('.next-page').show();
		}
		
		// Hide the blank padding cell if even number of matches
		let numDisplayed = displaySlice.length;
		if ( numDisplayed % 2 == 0 ) jQuery('.tab-pane.active .species_group_blank').hide();
		else jQuery('.tab-pane.active .species_group_blank').show();
	}
	
	// Filter the species list on search
	jQuery("#search_species").on("keyup", function() {
		var value = jQuery(this).val().toLowerCase();
		
		jQuery(".species_select_name").filter(function() {
		  toToggle = jQuery(this).text().toLowerCase().indexOf(value) > -1;
		  //jQuery(this).parent().toggle(toToggle)
		  // Add class to parent of non matching elements
		  if ( toToggle ) {
			  jQuery(this).parent().addClass ('match');
		  }
		  else {
			  jQuery(this).parent().removeClass ('match');
		  }
		});
		
		displaySpeciesPage( 0, numSpeciesPerPage );
		
	});
	
	
	jQuery('.species-tab').on('shown.bs.tab', function (e) {
		displaySpeciesPage( 0, numSpeciesPerPage );
  
	});
	
	jQuery('.pagination li').click(function (){
		let pageText = null;
		let page = null;
		let currPage = null;
		if ( jQuery(this).hasClass('prev-page') ) {
			pageText = jQuery('.pagination li.active').text();
			page = parseInt(pageText) -2;
			if ( page < 0 ) page = 0;
		}
		else if ( jQuery(this).hasClass('next-page') ) {
			pageText = jQuery('.pagination li.active').text();
			page = parseInt(pageText);
			
			let lastPageText = jQuery('.last-page').text();
			let lastPage = parseInt(lastPageText);
			if ( page == lastPage ) page = lastPage-1;
			
		}
		else {
			pageText = jQuery(this).text();
			page = parseInt(pageText) -1;
		}
		displaySpeciesPage( page*numSpeciesPerPage, numSpeciesPerPage );
	
	});
	
	
	

	// Add any remove click functions on refresh.
	removeClicks();
	
	displaySpeciesPage( 0, numSpeciesPerPage );
	
	jQuery('.species-carousel-control').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#species-indicators li').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to remove favourite status', 'placement': 'bottom'});
	jQuery('#not-favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to make this one of your favourites', 'placement': 'bottom'});
	jQuery('.species-tab').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Filter list of species', 'placement': 'top'});
	jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
	jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});

	
	
	
});

	
    
