jQuery(document).ready(function(){
	
	const sequences = JSON.parse(BioDiv.sequences);
	const topic = parseInt(BioDiv.topic);
	
	const detail = BioDiv.detail == 1;
	
	const numSpeciesPerPage = 36;
	
	
	
	let currentSequence = 0; // Always starts as the first one
	
	let classifications = [];
	
	let resultImages = [];
	
	// Pick out the first image in the sequence so we can store it for the results
	addResultImage = function () {
		let imgEls = jQuery('#photoCarousel').find('img');
		if ( imgEls.length > 0 ) {
			resultImages.push ( imgEls[0].src );
		}
		else {
			let med = jQuery('#videoContainer');
			if ( med.length > 0 ) {
				resultImages.push(med.find('source:first').attr('src'));
			}
			else {
				med = jQuery('#audioContainer');
				resultImages.push(med.find('source:first').attr('src'));
			}
		}
		
	};
	
	// Add the first result image
	addResultImage();
	
	setNext = function () {
		// One of these three...
		jQuery('#photoCarousel').bind('slid.bs.carousel', function (e) {
			var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
			
			var lastId = jQuery('#photoCarouselInner').find(".last-photo").attr("data-photo-id");
			if ( activeId == lastId ) {
				jQuery('#control_nextseq').prop('disabled', false);
				if ( currentSequence == sequences.length - 1 ) {
					jQuery('#control_nextseq').hide();
					jQuery('#control_finish').show();
					jQuery('#control_finish_inpage').show();
				}
			}
		});
		jQuery('#classify-video').bind('ended', function (e) {
			jQuery('#control_nextseq').prop('disabled',false);
			if ( currentSequence == sequences.length - 1 ) {
				jQuery('#control_nextseq').hide();
				jQuery('#control_finish').show();
				jQuery('#control_finish_inpage').show();
			}
		});
		jQuery('#classify-audio').bind('ended', function (e) {
			jQuery('#control_nextseq').prop('disabled',false);
			if ( currentSequence == sequences.length - 1 ) {
				jQuery('#control_nextseq').hide();
				jQuery('#control_finish').show();
				jQuery('#control_finish_inpage').show();
			}
		});
	};
	
	// addFullScreenFnly = function () {
		// jQuery('#fullscreen-button').click(function (){
			// var photos = document.getElementById('photoCarousel');
			// if("requestFullscreen" in photos) 
			// {
				// photos.requestFullscreen();
			// } 
			// else if ("webkitRequestFullscreen" in photos) 
			// {
				// photos.webkitRequestFullscreen();
			// } 
			// else if ("mozRequestFullScreen" in photos) 
			// {
				// photos.mozRequestFullScreen();
			// } 
			// else if ("msRequestFullscreen" in photos) 
			// {
				// photos.msRequestFullscreen();
			// }
					
		// });
		
		// jQuery('#fullscreen-exit-button').click(function (){
			
			// if(document.exitFullscreen) 
			// {
				// document.exitFullscreen();
			// } 
			// else if (document.webkitExitFullscreen) 
			// {
				// document.webkitExitFullscreen();
			// } 
			// else if (document.mozCancelFullScreen) 
			// {
				// document.mozCancelFullScreen();
			// } 
			// else if (document.msExitFullscreen) 
			// {
				// document.msExitFullscreen();
			// }
			// else {
				// console.log("No exit found");
				
			// }		
		// });
	// }
		
		
	updateProgressBar = function () {
		let currnum = currentSequence + 1;
		let newtext = "" + currnum + "/" + sequences.length;
		let newwidth = currnum * 100 / sequences.length;
		jQuery("#seq_progress_bar").text(newtext);
		jQuery("#seq_progress_bar").attr("aria-valuenow", newwidth);
		jQuery("#seq_progress_bar").width(newwidth + "%");
	}
				
	
	addSelectedSpecies = function () {
		// Get the id and text.
		let id = jQuery('#species_value').attr('value');
		let name = jQuery('#species_header_' + id).text();
		
		let number = jQuery("input[name='number']").val();
		let age = jQuery("input[name='age']:checked").val();
		let gender = jQuery("input[name='gender']:checked").val();
		
		addSpecies(id, name, number, age, gender);
	}
	
	addSpecies = function (id, name, number, age, gender) {
		
		// Store the classification against the current sequence, check for < 5
		let maxReached = false;
		let newSpecies = {"id": id, "number": number, "age": age, "gender": gender};
		let showExtras = detail;
		
		// If there's already a nothing classification, remove it.
		if ( id == "86" ) {
			removeCurrentClassifications();
			showExtras = false;
		}
		else if ( id == "87" ) {
			showExtras = false;
		}
		else {
			removeNothing ();		
		}
		
		let isAudio = jQuery('#audioContainer');
		let maxClass = 6;
		if ( isAudio.length > 0 ) maxClass = 20;
			
			
		if ( classifications.length < currentSequence + 1 ) {
			// No classifications for this index yet.
			classifications[currentSequence] = [];
			classifications[currentSequence].push(newSpecies);
		}
		else if ( classifications[currentSequence].length < maxClass ) {
			
			classifications[currentSequence].push(newSpecies);
		}
		else {
			console.log("max classifications reached");
			maxReached = true;
		}
		
		if ( !maxReached ) {
			// Display the button.
			let btnString = name;
			
			if ( showExtras ) {			
				btnString += " (";
				btnString += number;
				
				let ageDefault = jQuery("input[name='age']:first").val();
				// Get the text of the label for this radio button!
				let ageText = jQuery("input[name='age']:checked + label").text();
				if ( age != ageDefault ) btnString += " " + ageText;
				
				let genderDefault = jQuery("input[name='gender']:first").val();
				// Get the text of the label for this radio button!
				let genderText = jQuery("input[name='gender']:checked + label").text();
				if ( gender != genderDefault ) btnString += " " + genderText;
				
				btnString += ")";
			}
			
			speciesIndex = classifications[currentSequence].length - 1;
			let idString = "remove_animal_" + speciesIndex;
			
			jQuery('#classifications').append( "<button id='" + idString + "' type='button' class='remove_animal btn btn-danger'>" + btnString + 
				" <span aria-hidden='true' class='fa fa-times-circle'></span><span class='sr-only'>Close</span></button>\n" );
			jQuery('#' + idString).click(function (){
				jQuery(this).remove();
				// And remove the classification.
				removeClassification(speciesIndex);
			});
		}
	}
	
	removeNothing = function () {
		let ind = -1;
		if ( classifications.length > currentSequence ) {
			let len = classifications[currentSequence].length;
			for( let i = 0; i < len; i++) { 
				if ( classifications[currentSequence][i].id === "86") { 
					ind = i; 
				}
			};
			if ( ind > -1 ) removeClassification(ind);
			jQuery('#remove_animal_' + ind).remove();
		}
	}
	
	removeClassification = function (speciesIndex) {
		if ( classifications.length > currentSequence ) {
			classifications[currentSequence].splice(speciesIndex, 1);
		}
	}
	
	removeCurrentClassifications = function () {
		classifications[currentSequence] = [];
		jQuery('#classifications').empty();
	}

	// drawMap = function (){
		
		// jQuery('#no_map').hide();
		
		// try{
			
			// let south = parseFloat(jQuery('#mediaLocation').attr('data-south'));
			// let west = parseFloat(jQuery('#mediaLocation').attr('data-west'));
			// let north = parseFloat(jQuery('#mediaLocation').attr('data-north'));
			// let east = parseFloat(jQuery('#mediaLocation').attr('data-east'));
			
			// let sw = new google.maps.LatLng(south, west);
			// let ne = new google.maps.LatLng(north, east);
			
			// let posBounds = new google.maps.LatLngBounds (sw, ne);
		
			// let mapOptions = {
				// zoom: 8,
				// center: sw,
				// mapTypeId: google.maps.MapTypeId.TERRAIN
			// }
			
			// var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			
			// let rectOptions = {
				// bounds: posBounds,
				// fillColor: "#00ba8a",
				// fillOpacity: 0.5,
				// strokeWeight: 1,
				// //strokeColour: "red",
				// //strokeOpacity: 0.5,
				// map: map,
				// draggable:false
			// }
			
			// var rect = new google.maps.Rectangle(
				// rectOptions
			// );
			
		// }
		// catch(err){
			// console.log(err.msg);
			// jQuery('#no_map').show();
		// }
		
	// };
	
	getSpeciesName = function ( id ) {
		return jQuery('#species_select_' + id).text();
	}
	
	jQuery('#control_map').click(function (){
		jQuery('#map_modal').modal('show');
		drawMap();
		
	});
	
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
	
	jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		
		// Check still logged in.
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
		}
		
		// Add species_id for the sequence and display the selected sequence as a button.
		addSelectedSpecies ();
		
		// If there's already a nothing classification, remove it.
		jQuery('.nothing-classification').remove();
		
	});
	
	jQuery('.classify_control').click(function (){
		let id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		
		let buttonText = jQuery(this).text();
		let namebits = buttonText.split(" ");
		let name = namebits[0];
		
		// Check still logged in.
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
		}
		
		// Add species_id for the sequence and display the selected sequence as a button.
		// Use default age, gender and number
		let number = 1;
		let age = jQuery("input[name='age']:first").val();
		let gender = jQuery("input[name='gender']:first").val();
		
		addSpecies (species_id, name, number, age, gender);
		
		
	});
	jQuery('#control_nextseq').click(function (){
		
		// If no species selected add a blank
		if ( jQuery('.remove_animal').length == 0 ) {
			addSpecies (0, "0", 0, 0, 0);
		}
		
		// Clear species buttons
		jQuery('.remove_animal').remove();
	
		currentSequence++;
		if ( currentSequence < sequences.length ) {
			let sequence_id = sequences[currentSequence];
			var url = BioDiv.root + "&view=mediacarousel&format=raw&invert=1&topic_id=" + topic + "&sequence_id=" + sequence_id;
			
			jQuery.ajax(url, {'success': function(data) {
				//window.location.reload(true);
				// Try all three media types
				jQuery('#photoCarousel').replaceWith(data);
				jQuery('#videoContainer').replaceWith(data);
				jQuery('#audioContainer').replaceWith(data);
				if (jQuery('#photoCarousel').find('img').length == 1 ) {
					jQuery('#control_nextseq').prop('disabled', false);
					
					// Need to change to Show Results if this is the final sequence and just one image as there is no slid event
					if ( currentSequence == sequences.length - 1 ) {
						jQuery('#control_nextseq').hide();
						jQuery('#control_finish').show();
						jQuery('#control_finish_inpage').show();
					}
				}
				else {
					jQuery('#control_nextseq').prop('disabled', true);
				}
				if (jQuery("#photoCarousel").length) {
                    jQuery("#invert_image").show();
                } else {
                    jQuery("#invert_image").hide();
                }
				jQuery('#fullscreen-invert-image').click(invertImage);
				addFullScreenFnly();
				updateProgressBar();
				setNext();
				addResultImage();
			}
			});
		}
		
	
	});
	
	jQuery('#control_finish').click(function (){
		
		let userAnimals = JSON.stringify(classifications);
		jQuery('#user_animals').val(JSON.stringify(classifications));
	
	});
	
	jQuery('#control_finish_inpage').click(function ( e ){
		
		e.preventDefault();
		
		let userAnimals = JSON.stringify(classifications);
		jQuery('#user_animals').val(JSON.stringify(classifications));
		
		// And call the ajax version
		
		let formData = jQuery('#control_finish_inpage').serialize();
		let url = BioDiv.root + "&view=trainingresultsajax&format=raw";
		jQuery('#t3-content').load(url, formData, quizResultsLoaded);
		
	});
	
	
	quizResultsLoaded = function () {
		
		// Activate buttons
		setReloadPage (); 
		addResultsHandlers ();
		
	}

	
	
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
	
	
	displaySpeciesPage( 0, numSpeciesPerPage );
	
	
	addFullScreenFnly();
	
	// Only allow for the detail in classifications if there is a parameter requesting it, otherwise just remove notes and certainty flag
	if ( !detail ) {
		jQuery('.species_classify').remove();
	}
	else {
		jQuery('.species_classify').get(3).remove();
		jQuery('.species_classify').get(3).remove();
	}
	
	jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
	jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});
	jQuery('.species-carousel-control').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#species-indicators li').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('.species-tab').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Filter list of species', 'placement': 'top'});
});

	
    
