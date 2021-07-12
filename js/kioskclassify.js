

const maxClassifications = 5;
const maxSpeciesDisplayed = 16;
var classifyCount = 0;

var currMammalsPage = 0;
var currBirdsPage = 0;

  


function kioskClassifySuccess () {
		
	setBackgroundImage();
	
	setTutorialButton ();	
	setClassifyProjectButton();
	setClassifySecondProjectButton();
	
}


function displayWhatSee () {
	
	jQuery('.classify_heading').hide();
	jQuery('#classify_whatsee').show();
	jQuery('#quiz_whatsee').show();
	
	
	jQuery('.species_group').hide();
	jQuery('#filter_buttons').show();
	
	let photoCarousel = jQuery("#photoCarousel");
	let isPhoto = (jQuery("#photoCarousel").length > 0);
	
	if ( isPhoto ) {
		jQuery('#whatsee_info').show();
		jQuery('#whatsee_vid_info').hide();
	}
	else {
		jQuery('#whatsee_info').hide();
		jQuery('#whatsee_vid_info').show();
	}
	
	
	
}

function displayAllMammals ( page ) {
	
	jQuery('#mammal_buttons').hide();
	
	let allMammals = jQuery(".all_mammals_species");
	allMammals.hide();
	
	let totalMammals = allMammals.length;
	let numPages = Math.ceil(totalMammals/maxSpeciesDisplayed);
		
	if ( page == 0 ) currMammalsPage = 0;
	else if ( page == -1 ){
		currMammalsPage = Math.max(0, currMammalsPage-1);
	}
	else if ( page == 1 ) {
		currMammalsPage = Math.min(currMammalsPage+1, numPages );
	}
	
	let start = currMammalsPage*maxSpeciesDisplayed;
	
	let displaySlice = jQuery(".all_mammals_species").slice(start, start + maxSpeciesDisplayed);
	displaySlice.show();
	
	if ( start == 0 ) {
		jQuery("#scroll_up_mammals").prop('disabled', true);
	}
	else {
		jQuery("#scroll_up_mammals").prop('disabled', false);
	}
	
	if ( start + maxSpeciesDisplayed >= totalMammals ) {
		jQuery("#scroll_down_mammals").prop('disabled', true);
	}
	else {
		jQuery("#scroll_down_mammals").prop('disabled', false);
	}
		
		
	jQuery('#all_mammal_buttons').show();
	
	
	jQuery('#scroll_info').show();
	
}

function displayAllBirds ( page ) {
	
	jQuery('#bird_buttons').hide();
	
	let allBirds = jQuery(".all_birds_species");
	allBirds.hide();
	
	let totalBirds = allBirds.length;
	let numPages = Math.ceil(totalBirds/maxSpeciesDisplayed);
		
	if ( page == 0 ) currBirdsPage = 0;
	else if ( page == -1 ){
		currBirdsPage = Math.max(0, currBirdsPage-1);
	}
	else if ( page == 1 ) {
		currBirdsPage = Math.min(currBirdsPage+1, numPages );
	}
	
	let start = currBirdsPage*maxSpeciesDisplayed;
	
	let displaySlice = jQuery(".all_birds_species").slice(start, start + maxSpeciesDisplayed);
	displaySlice.show();
	
	if ( start == 0 ) {
		jQuery("#scroll_up_birds").prop('disabled', true);
	}
	else {
		jQuery("#scroll_up_birds").prop('disabled', false);
	}
	
	if ( start + maxSpeciesDisplayed >= totalBirds ) {
		jQuery("#scroll_down_birds").prop('disabled', true);
	}
	else {
		jQuery("#scroll_down_birds").prop('disabled', false);
	}
		
		
	jQuery('#all_bird_buttons').show();
	
	
	jQuery('#scroll_info').show();
	
}

function displaySelectMammal () {
	
	// NB could be classify or quiz
	jQuery('.classify_heading').hide();
	jQuery('#classify_select').show();
	jQuery('#quiz_select').show();
	
	
	jQuery('.species_group').hide();
	jQuery('#mammal_buttons').show();
	
	jQuery('#whatsee_info').hide();
	
	
}

function displaySelectBird () {
	
	jQuery('.classify_heading').hide();
	jQuery('#classify_select').show();
	jQuery('#quiz_select').show();
	
	
	jQuery('.species_group').hide();
	jQuery('#bird_buttons').show();
	
	jQuery('#whatsee_info').hide();
	
}


function speciesSelected () {
	
	jQuery('#species_helplet').empty();
		
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let species_id = idbits.pop();
	
	jQuery('.species_group').hide();
	//jQuery('#chosen_species').show();
	
	jQuery('#whatsee_info').hide();
	//jQuery('#classify_whatsee').hide();
	//jQuery('#classify_select').hide();
	//jQuery('#classify_happy').show();
	
	// Add the species id to the classify save button
	jQuery('#classify_save').attr( "data-species-id", species_id);
	
	// Populate the helplet
	var url = BioDiv.root + "&view=kioskspecies&format=raw&option_id=" + species_id;
	jQuery('#species_helplet').load(url, kioskSpeciesLoaded);
	
	// Ensure that no hyperlinks can be clicked in kiosk mode
	jQuery('#species_helplet').on('click', 'a', function(e) {
		e.preventDefault();
		console.log(jQuery(this).attr('href'));
	});
}


function setSwipeImages () {
	jQuery(".carousel").on("touchstart", function(event){
        var xClick = event.originalEvent.touches[0].pageX;
		jQuery(this).one("touchmove", function(event){
			var xMove = event.originalEvent.touches[0].pageX;
			if( Math.floor(xClick - xMove) > 5 ){
				jQuery(this).carousel('next');
			}
			else if( Math.floor(xClick - xMove) < -5 ){
				jQuery(this).carousel('prev');
			}
    });
    jQuery(".carousel").on("touchend", function(){
            jQuery(this).off("touchmove");
		});
	});
}



function kioskClassifyProjectSuccess () {
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	jQuery('#classify_mammal').click( displaySelectMammal );
	
	jQuery('#classify_bird').click( displaySelectBird );
	
	jQuery('.species_select').click( speciesSelected );
	
	jQuery('#classify_save').click(function (){
		
		let photoId = jQuery("#videoContainer").attr("data-photo-id");
		
		if ( !photoId ) {
			photoId = jQuery("#photoCarouselInner>.item:first").attr("data-photo-id");
		}
		
		if ( !photoId ) {
			photoId = jQuery("#audioContainer").attr("data-photo-id");
		}
		
		let speciesId = jQuery(this).attr("data-species-id");
		
		addClassification(photoId, speciesId);
		
	} );
	
	jQuery('#not_on_mammal_list').click(function (){
		displayAllMammals( 0 );
	});
	
	jQuery('#scroll_up_mammals').click(function (){
		displayAllMammals( -1 );
	});
	
	jQuery('#scroll_down_mammals').click(function (){
		displayAllMammals( 1 );
	});
	
	jQuery('#not_on_bird_list').click(function (){
		displayAllBirds( 0 );
	});
	
	jQuery('#scroll_up_birds').click(function (){
		displayAllBirds( -1 );
	});
	
	jQuery('#scroll_down_birds').click(function (){
		displayAllBirds( 1 );
	});
	
	
		
	jQuery('.back_to_filter').click( displayWhatSee );
	
	setSwipeImages ();
	
}

function kioskSpeciesLoaded () {
	
	jQuery('.classify_heading').hide();
	jQuery('#classify_happy').show();
	jQuery('#quiz_happy').show();
	jQuery('#chosen_species').show();
	
}


function kioskAddAnimalSuccess () {
		
	//setBackgroundImage();
	jQuery(".loader").addClass("invisible");
	
	displayWhatSee();
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	setSwipeImages ();
}


function feedbackSuccess () {
	
	setBackgroundImage();
	
	jQuery('#classify_again').click(function (){
		
		var url = BioDiv.root + "&view=kioskclassify&format=raw";
		jQuery('#kiosk').load(url, kioskClassifySuccess);
		
	});
	
	setHomeButton();	
	classifyCount = 0;
}


addClassification = function ( photoId, speciesId ) {
	
	// Want to add a classification and get the next sequence/video.  
	// Unless reached the max, when want to display results
	
	classifyCount += 1;
	
	if ( classifyCount < maxClassifications ) {
	
		let url = BioDiv.root + "&task=kiosk_add_animal_next&format=raw&photo_id=" + photoId + "&species=" + speciesId;
		
		jQuery(".loader").removeClass("invisible");
		
		jQuery('#media_carousel').load(url, kioskAddAnimalSuccess);
	}
	else {
		
		console.log("Reached max classifications");
		
		let url = BioDiv.root + "&task=kiosk_add_animal_finish&format=raw&photo_id=" + photoId + "&species=" + speciesId;
		
		jQuery('#kiosk').load(url, feedbackSuccess);
	}
	
}




	
    
