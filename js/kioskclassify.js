

const maxSequences = 5;
const maxSequencesAudio = 4;
const maxSpeciesDisplayed = 16;
let classifyCount = 0;

let currMammalsPage = 0;
let currBirdsPage = 0;

const maxSpeciesPerClip = 5;

const classifyPage = {
TOP: 'top',
FILTER: 'filter',
COMMONBIRDS: 'commonbirds',
ALLBIRDS: 'allbirds'
}

let currentClassifyPage = classifyPage.TOP;

  
function removeClick (){
	jQuery('.remove_animal').last().click(function (){
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let animalId = idbits.pop();
		let photoId = jQuery("#videoContainer").attr("data-photo-id");
		
		if ( !photoId ) {
			photoId = jQuery("#photoCarouselInner>.item:first").attr("data-photo-id");
		}
		
		if ( !photoId ) {
			photoId = jQuery("#audioContainer").attr("data-photo-id");
		}
		
		jQuery(this).remove();
		
		setNothingFinish();
		
		let removeurl = BioDiv.root + "&task=kiosk_remove_animal&format=raw&animal_id=" + animalId + "&photo_id=" + photoId;
		jQuery.get(removeurl, bioDivRemoveClick);
		
		/*
		let parenttag = jQuery('#remove_animal_' + animal_id).parent();
		parenttag.load(removeurl, "", bioDivRemoveClick);
		parenttag.remove();
		*/
	});
	
}


function bioDivRemoveClick ( data ){
	
	//removeClicks();
	
	if ( jQuery('.remove_animal').length == 0 ) {
		displayWhatHear();
	}
	else if ( jQuery('.remove_animal').length == maxSpeciesPerClip - 1 ) {
		displayWhatMoreHear();
	}
}


function setNothingFinish () {
	if ( jQuery('.remove_animal').length > 0 ) {
		jQuery('#nothing_button').hide();
		jQuery('#finish_clip').show();
	}
	else {
		jQuery('#nothing_button').show();
		jQuery('#finish_clip').hide();
	}
}


function kioskClassifySuccess () {
		
	setBackgroundImage();
	
	setTutorialButton ();
	setAudioTutorialButton ();	
	
	setClassifyProjectButton();
	setClassifySecondProjectButton();
	
	
	setClassifyAudioProjectButton();
	setClassifySecondAudioProjectButton();
	
	currentClassifyPage = classifyPage.TOP;
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

function displayWhatHear () {
	
	jQuery('#species_helplet').empty();
	
	jQuery('.classify_heading').hide();
	jQuery('#classify_whathear').show();
	jQuery('#quiz_whathear').show();
	
	jQuery('.species_group').hide();
	
	jQuery('.kiosk_filter_btn').show();
	jQuery('#finish_clip').hide();
	jQuery('#filter_buttons').show();
	
	jQuery(".mwinfo").hide();
	jQuery('#whathear_info').show();
	
	currentClassifyPage = classifyPage.FILTER;
}


function displayWhatMoreHear () {
	
	jQuery('#species_helplet').empty();
	
	jQuery('.classify_heading').hide();
	
	jQuery('#classify_hearagain').show();
	jQuery('#quiz_hearagain').show();
	
	jQuery('.species_group').hide();
	
	jQuery('#bird_button').show();
	jQuery('#human_button').show();
	jQuery('#other_button').show();
	jQuery('#nothing_button').hide();
	jQuery('#finish_clip').show();
	jQuery('#filter_buttons').show();
	
	
	jQuery(".mwinfo").hide();
	jQuery('#whatmore_info').show();
		
	currentClassifyPage = classifyPage.FILTER;		
}


function displayReachedMaxSpecies () {
	
	jQuery('.classify_heading').hide();
	
	jQuery('#classify_maxspecies').show();
	jQuery('#quiz_maxspecies').show();
	
	jQuery('.species_group').hide();
	
	jQuery('.kiosk_filter_btn').hide();
	jQuery('#finish_clip').show();
	jQuery('#filter_buttons').show();
	
	jQuery(".mwinfo").hide();
	jQuery('#maxspecies_info').show();
		
	currentClassifyPage = classifyPage.FILTER;		
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
	
	jQuery('#species_helplet').empty();
	
	jQuery('#bird_buttons').hide();
	jQuery('.species_group').hide();
	
	
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
	
	currentClassifyPage = classifyPage.ALLBIRDS;
	
}

function displayAllInverts ( page ) {
	
	jQuery('#invert_buttons').hide();
	
	let allInverts = jQuery(".all_inverts_species");
	allInverts.hide();
	
	let totalInverts = allInverts.length;
	let numPages = Math.ceil(totalInverts/maxSpeciesDisplayed);
		
	if ( page == 0 ) currMammalsPage = 0;
	else if ( page == -1 ){
		currInvertsPage = Math.max(0, currInvertsPage-1);
	}
	else if ( page == 1 ) {
		currInvertsPage = Math.min(currInvertsPage+1, numPages );
	}
	
	let start = currInvertsPage*maxSpeciesDisplayed;
	
	let displaySlice = jQuery(".all_inverts_species").slice(start, start + maxSpeciesDisplayed);
	displaySlice.show();
	
	if ( start == 0 ) {
		jQuery("#scroll_up_inverts").prop('disabled', true);
	}
	else {
		jQuery("#scroll_up_inverts").prop('disabled', false);
	}
	
	if ( start + maxSpeciesDisplayed >= totalInverts ) {
		jQuery("#scroll_down_inverts").prop('disabled', true);
	}
	else {
		jQuery("#scroll_down_inverts").prop('disabled', false);
	}
		
		
	jQuery('#all_invert_buttons').show();
	
	
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
	
	jQuery('#species_helplet').empty();
	
	jQuery('.classify_heading').hide();
	jQuery('#classify_select').show();
	jQuery('#quiz_select').show();
	
	
	jQuery('.species_group').hide();
	jQuery('#bird_buttons').show();
	
	jQuery(".mwinfo").hide();
	jQuery('#select_info').show();
	
	currentClassifyPage = classifyPage.COMMONBIRDS;
}


function displaySelectInvert () {
	
	jQuery('#species_helplet').empty();
	
	jQuery('.classify_heading').hide();
	jQuery('#classify_select').show();
	jQuery('#quiz_select').show();
	
	
	jQuery('.species_group').hide();
	jQuery('#invert_buttons').show();
	
	jQuery(".mwinfo").hide();
	jQuery('#select_info').show();
	
	currentClassifyPage = classifyPage.COMMONINVERTS;
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
	
	// Add the species id to the classify save button and/or the multi button
	jQuery('#classify_save').attr( "data-species-id", species_id);
	jQuery('#classify_save_multi').attr( "data-species-id", species_id);
	
	// Populate the helplet
	var url = BioDiv.root + "&view=kioskspecies&format=raw&option_id=" + species_id;
	jQuery('#species_helplet').load(url, kioskSpeciesLoaded);
	
	
}


function audioSpeciesSelected () {
	
	jQuery('#species_helplet').empty();
		
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let species_id = idbits.pop();
	let species_name = jQuery(this).children(".species_name").first().text();
	if ( !species_name ) species_name = jQuery(this).text();
	
	jQuery('.species_group').hide();
	
	jQuery('#whatsee_info').hide();
	
	// Add the species id to the classify save button and/or the multi button
	jQuery('#classify_save').attr( "data-species-id", species_id);
	jQuery('#classify_save_multi').attr( "data-species-id", species_id);
	jQuery('#classify_save_multi').attr( "data-species-name", species_name);
	
	
	// Populate the helplet
	var url = BioDiv.root + "&view=kioskspeciesaudio&format=raw&option_id=" + species_id;
	jQuery('#species_helplet').load(url, kioskSpeciesLoaded);
	
	
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
	setMediaCarouselPause();
	setSpeciesSonoPause();
}



function kioskClassifyAudioProjectSuccess () {
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	jQuery('.species_select').click( audioSpeciesSelected );
	
	jQuery('#classify_bird').click( displaySelectBird );
	
	jQuery('#classify_save_multi').click(function (){
		
		let photoId = jQuery("#videoContainer").attr("data-photo-id");
		
		if ( !photoId ) {
			photoId = jQuery("#audioContainer").attr("data-photo-id");
		}
		
		let speciesId = jQuery(this).attr("data-species-id");
		
		addClassificationMulti(photoId, speciesId);
		
	} );
	
	jQuery('#finish_clip').click( finishClip );
	
	jQuery('#not_on_bird_list').click(function (){
		displayAllBirds( 0 );
	});
	
	jQuery('#scroll_up_birds').click(function (){
		displayAllBirds( -1 );
	});
	
	jQuery('#scroll_down_birds').click(function (){
		displayAllBirds( 1 );
	});
	
	setMediaCarouselPause();
	setSpeciesSonoPause();
	
	jQuery('.intelligent_back').click( backClicked );
		
	setSwipeImages ();
	
}


function kioskSpeciesLoaded () {
	
	jQuery('.classify_heading').hide();
	jQuery('#classify_happy').show();
	jQuery('#quiz_happy').show();
	jQuery('#chosen_species').show();
	
	// Ensure that no hyperlinks can be clicked in kiosk mode
	jQuery('#species_helplet').on('click', 'a', function(e) {
		e.preventDefault();
		console.log(jQuery(this).attr('href'));
	});
	
	disableIframeLinks ();
	
	setSpeciesSonoPause();
	
}


function kioskAddAnimalSuccess () {
		
	//setBackgroundImage();
	jQuery(".loader").addClass("invisible");
	
	displayWhatSee();
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	setSwipeImages ();
}


function kioskAddAnimalMultiSuccess ( data ) {
	
	jQuery('#current_species').append(data);
	
	removeClick();
	
	jQuery(".loader").addClass("invisible");
	
	if ( jQuery(".remove_animal").length >= maxSpeciesPerClip ) {
		displayReachedMaxSpecies();
	}
	else {
		displayWhatMoreHear();
	}
	
}


function kioskNextClipSuccess () {
		
	jQuery(".loader").addClass("invisible");
	
	displayWhatHear();
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	setSwipeImages ();
	
	setMediaCarouselPause();
}



function feedbackSuccess () {
	
	setBackgroundImage();
	setReloadPage();
	
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
	
	if ( classifyCount < maxSequences ) {
	
		let url = BioDiv.root + "&task=kiosk_add_animal_next&format=raw&photo_id=" + photoId + "&species=" + speciesId;
		
		if ( BioDiv.badge > 0 ) {
			url = BioDiv.root + "&task=badge_kiosk_add_animal_next&format=raw&photo_id=" + photoId + "&species=" + speciesId + "&badge=" + BioDiv.badge;
			if ( BioDiv.classId ) {
				url += "&class_id=" + BioDiv.classId;
			}
		}
		
		jQuery(".loader").removeClass("invisible");
		
		jQuery('#media_carousel').load(url, kioskAddAnimalSuccess);
	}
	else {
		
		let url = BioDiv.root + "&task=kiosk_add_animal_finish&format=raw&photo_id=" + photoId + "&species=" + speciesId;
		
		if ( BioDiv.badge > 0 ) {
			url = BioDiv.root + "&task=badge_kiosk_add_animal_finish&format=raw&photo_id=" + photoId + "&species=" + speciesId + "&badge=" + BioDiv.badge;
			if ( BioDiv.classId ) {
				url += "&class_id=" + BioDiv.classId;
			}
		}
		
		
		jQuery('#kiosk').load(url, feedbackSuccess);
	}
	
}


addClassificationMulti = function ( photoId, speciesId ) {
	
		
	let url = BioDiv.root + "&task=kiosk_add_animal_multi&format=raw&photo_id=" + photoId + "&species=" + speciesId;
	
	jQuery(".loader").removeClass("invisible");
	
	//jQuery('#current_species').load(url, kioskAddAnimalMultiSuccess);
	jQuery.get(url, kioskAddAnimalMultiSuccess);

}


function finishClip () {
	
	pauseVideo();
	
	// Want to get the next sequence/video.  
	// Unless reached the max, when want to display results
	classifyCount += 1;
	
	if ( classifyCount < maxSequencesAudio ) {
		
		jQuery('#current_species').empty();
	
		let url = BioDiv.root + "&task=kiosk_next_clip&format=raw";
		
		jQuery(".loader").removeClass("invisible");
		
		jQuery('#media_carousel').load(url, kioskNextClipSuccess);
	}
	else {
		
		console.log("Reached max clips");
		
		let url = BioDiv.root + "&task=kiosk_get_feedback&format=raw";
		
		jQuery('#kiosk').load(url, feedbackSuccess);
	}
}


function backClicked () {
	
	if ( currentClassifyPage == classifyPage.ALLBIRDS ) {
		if ( jQuery("#all_bird_buttons").is(":hidden") ) {
			displayAllBirds(0);
		}
		else {
			displaySelectBird();
		}
	}
	else if ( currentClassifyPage == classifyPage.COMMONBIRDS ){
		if ( jQuery("#bird_buttons").is(":hidden") ) {
			displaySelectBird(0);
		}
		else if ( jQuery(".remove_animal").length > 0 ) {
			displayWhatMoreHear ();
		}
		else {
			displayWhatHear();
		}
	}
	else {
		if ( jQuery(".remove_animal").length > 0 ) {
			displayWhatMoreHear ();
		}
		else {
			displayWhatHear();
		}
	}
	
}

/*
function pauseVideo () {
	let video = document.querySelector("video");
	video.pause();
}
*/
/*
function pauseVideo () {
	let video = jQuery(this);
	video.trigger('pause');
}
*/
	
    
