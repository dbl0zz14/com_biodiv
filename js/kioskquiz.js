
let sequenceIds = 0;
let currentBeginnerSequence = 0;
let currentStandardSequence = 0;
let quizSpeciesAnswers = null;


function kioskQuizSuccess () {
		
	setBackgroundImage();
	
	
	jQuery('#beginner_quiz').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizbeginner&format=raw";
		jQuery('#kiosk').load(url, kioskBeginnerQuizSuccess);
	});
	
	jQuery('#intermediate_quiz').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizstandard&level=improver&format=raw";
		jQuery('#kiosk').load(url, kioskStandardQuizSuccess);
	});
	
	jQuery('#expert_quiz').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizstandard&level=expert&format=raw";
		jQuery('#kiosk').load(url, kioskStandardQuizSuccess);
	});
	
	jQuery('#beginner_quiz_audio').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizbeginneraudio&format=raw";
		jQuery('#kiosk').load(url, kioskBeginnerQuizSuccess);
	});
	
	jQuery('#intermediate_quiz_audio').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizstandardaudio&level=improver&format=raw";
		jQuery('#kiosk').load(url, kioskStandardQuizAudioSuccess);
	});
	
	jQuery('#expert_quiz_audio').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizstandardaudio&level=expert&format=raw";
		jQuery('#kiosk').load(url, kioskStandardQuizAudioSuccess);
	});
	
	
}


function speciesChosen () {
		
	pauseAllVideo();
	
	let thisEl = jQuery(this);
	
	if ( thisEl.hasClass('correct-species') ) {
		
		jQuery('.match_with').hide();
		jQuery('#try_again').hide();
		jQuery('#correct_species').show();
		
		let speciesChoicePanel = thisEl.closest(".species_group");
		
		let id = speciesChoicePanel.attr("id");
		let idbits = id.split("_");
		let question_id = idbits.pop();
		
		
		jQuery('#species_choices_' + question_id).hide();
		jQuery('#correct_species_' + question_id).show();
	}
	else {
		jQuery('.match_with').hide();
		jQuery('#correct_species').hide();
		jQuery('#try_again').show();
	}
	
	
}


// Image, video and audio
function kioskBeginnerQuizSuccess () {
	
	seqJson = jQuery('#seq_ids').attr('data-seq-ids');
	
	sequenceIds = JSON.parse(seqJson);
	
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	jQuery('.beginner-quiz-btn').click( speciesChosen );
	
	jQuery('.beginner-quiz-audio-btn').click ( speciesChosen );
	
	jQuery(".beginner_next").click(function () {
		
		jQuery(this).closest(".species_group").hide();
		
		//jQuery('.look_thro').hide();
		//jQuery('.match_with').hide();
		//jQuery('.whatsee_info').hide();
		jQuery('#photoCarousel').hide();
		jQuery('#quiz_progress').hide();
		
		currentBeginnerSequence += 1;
	
		// Update the media carousel with the next sequence
		let url = BioDiv.root + "&view=kioskmediacarousel&format=raw&sequence_id=" + sequenceIds[currentBeginnerSequence];
	
		jQuery('#media_carousel').load(url, nextBeginnerQuestionLoaded);
	});

	jQuery("#beginner_results").click(function () {
		
		currentBeginnerSequence = 0;
		
		jQuery('#quiz_whatsee').hide();
		
		jQuery('#beginner_quiz_lhs').hide();
		
		jQuery('#beginner_quiz_rhs').hide();
		
		jQuery('#whatsee_info_panel').hide();
		
		jQuery("#audioAttribution").empty();
		
		jQuery('#beginner_quiz_results').show();
	
		
	});
	
	jQuery('#play_again').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizbeginner&format=raw";
		jQuery('#kiosk').load(url, kioskBeginnerQuizSuccess);
		
	});
	
	jQuery('#play_again_audio').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizbeginneraudio&format=raw";
		jQuery('#kiosk').load(url, kioskBeginnerQuizSuccess);
		
	});
	
	jQuery('.back_to_home').click( function () {
		
		let url = BioDiv.root + "&view=kioskstart&format=raw";
	
		jQuery('#kiosk').load(url, kioskStartSuccess);
	});

	//jQuery('.beginner-audio-sono').focusout( pauseVideo );
	//jQuery('#media_carousel').focusout( pauseVideo );
	
	//jQuery('.beginner-audio-sono').click(pauseAllOtherVideo);
	//jQuery('#media_carousel').click(pauseAllOtherVideo);
	
	setMediaCarouselPause();
	setBeginnerSonoPause();

}

function nextBeginnerQuestionLoaded() {
	
	jQuery('.look_thro').hide();
	jQuery('.match_with').hide();
	jQuery('.whatsee_info').hide();
		
	
	jQuery('#correct_species').hide();
	jQuery('#try_again').hide();
				
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	updateProgressBar();
	
	jQuery('#quiz_progress').show();
	jQuery("#look_thro_" + currentBeginnerSequence).show();
	jQuery("#match_with_" + currentBeginnerSequence).show();
	jQuery("#whatsee_info_" + currentBeginnerSequence).show();
	jQuery("#species_choices_" + currentBeginnerSequence).show();
	
	updateAttribution();
	
	setMediaCarouselPause();
}


updateProgressBar = function () {
	let currnum = currentBeginnerSequence + 1;
	let newtext = "" + currnum + "/" + sequenceIds.length;
	let newwidth = currnum * 100 / sequenceIds.length;
	jQuery("#seq_progress_bar").text(newtext);
	jQuery("#seq_progress_bar").attr("aria-valuenow", newwidth);
	jQuery("#seq_progress_bar").width(newwidth + "%");
}


function updateAttribution () {
	let attrib = jQuery("#audioAttribution");
	attrib.empty();
	
	let newAttribs = jQuery(".attrib_" + currentBeginnerSequence);
	
	let newText = "";
	
	for ( let i = 0; i < newAttribs.length; i++ ) {
		newText += newAttribs[i].innerHTML;
	}
	
	attrib.text(newText);
}
				

function addQuizSpecies(photoId, speciesId) {
	
	let newSpecies = {"photoId": photoId, "speciesId": speciesId};
	
	quizSpeciesAnswers.push(newSpecies);

}


function addQuizSpeciesMulti(photoId, speciesId, speciesName) {
	
	let numAnswers = quizSpeciesAnswers.length;
	let firstAnswer = true;
	let speciesIndex = 0;
	
	if ( numAnswers > 0 ) {
		let prevPhotoId = quizSpeciesAnswers[numAnswers-1].photoId;
		
		if ( prevPhotoId == photoId ) {
			firstAnswer = false;
		}
	}
	if ( firstAnswer ) {
		let newSpecies = {"photoId": photoId, "speciesId": [speciesId]};
	
		quizSpeciesAnswers.push(newSpecies);
	}
	else {
		quizSpeciesAnswers[numAnswers-1].speciesId.push(speciesId);
		speciesIndex = quizSpeciesAnswers[numAnswers-1].speciesId.length - 1;
	}
	
	
	// Display the button.
	let idString = "remove_animal_" + speciesIndex;
	
	jQuery('#classifications').append( "<button id='" + idString + "' type='button' class='remove_animal btn btn-danger'>" + speciesName + 
		" <span aria-hidden='true' class='fa fa-times-circle'></span><span class='sr-only'>Close</span></button>\n" );
	jQuery('#' + idString).click(function (){
		
		jQuery(this).remove();
		// And remove the classification.
		quizSpeciesAnswers[numAnswers-1].speciesId.splice(speciesIndex, 1);
		
		
	});
	

}


function displayNextQuestionOrFinish() {
	
	jQuery("#classifications").empty();
	
	if ( currentStandardSequence + 1 == sequenceIds.length ) {
		
		let topicId = jQuery("#topic_id").attr("data-topic-id");
		let qs = JSON.stringify(sequenceIds);
		let as = JSON.stringify(quizSpeciesAnswers);
		
		let postData = {
			topic: topicId,
			questions: qs,
			answers: as
		};
		
		let resultsView = "kioskquizresults";
		if ( isAudioQuiz() ) {
			resultsView = "kioskquizresultsmulti";
		}			
		
		let url = BioDiv.root + "&view=" + resultsView + "&format=raw";
		
		jQuery.post ( url, postData, standardResultsLoaded );
		
	}
	else {
		currentStandardSequence += 1;
	
		// Update the media carousel with the next sequence
		let url = BioDiv.root + "&view=kioskmediacarousel&format=raw&sequence_id=" + sequenceIds[currentStandardSequence];

		jQuery('#media_carousel').load(url, nextStandardQuestionLoaded);
	}
}



function nextStandardQuestionLoaded () {
	
	if ( isAudioQuiz() ) {
		displayWhatHear();
	}
	else {
		displayWhatSee();
	}
	
	jQuery("#chosen_species").hide();
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	setMediaCarouselPause ();
	
}


function isAudioQuiz () {
	
	let isAudio = false;
	if ( jQuery("#classify_whathear").length > 0 ) {
		isAudio = true;
	}
	return isAudio;
}



function standardResultsLoaded ( data ) {
	jQuery('#kiosk').html(data);
	
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
	
	jQuery('#play_again_multi').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizstandardaudio&format=raw";
		jQuery('#kiosk').load(url, kioskStandardQuizAudioSuccess);
		
	});
	
	jQuery('#play_again').click(function (){
		
		var url = BioDiv.root + "&view=kioskquizstandard&format=raw";
		jQuery('#kiosk').load(url, kioskStandardQuizSuccess);
		
	});
	
	jQuery('.back_to_home').click( function () {
		
		let url = BioDiv.root + "&view=kioskstart&format=raw";
	
		jQuery('#kiosk').load(url, kioskStartSuccess);
	});

}


function kioskStandardQuizSuccess () {
	
	currentStandardSequence = 0;
	
	seqJson = jQuery('#seq_ids').attr('data-seq-ids');
	
	sequenceIds = JSON.parse(seqJson);
	
	quizSpeciesAnswers = [];
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	
	jQuery('#classify_mammal').click( displaySelectMammal );
	
	jQuery('#classify_bird').click( displaySelectBird );
	
	jQuery('.species_select').click( speciesSelected );
	
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
	
	jQuery('.intelligent_back').click( backClicked );
	
	jQuery('#classify_save').click(function (){
		
		let photoId = jQuery("#videoContainer").attr("data-photo-id");
		
		if ( !photoId ) {
			photoId = jQuery("#photoCarouselInner>.item:first").attr("data-photo-id");
		}
		
		if ( !photoId ) {
			photoId = jQuery("#audioContainer").attr("data-photo-id");
		}
		
		let speciesId = jQuery(this).attr("data-species-id");
		
		addQuizSpecies(photoId, speciesId);
		
		displayNextQuestionOrFinish();
		
	} );
	
	
	setMediaCarouselPause ();
	//setSpeciesSonoPause ();
}

function kioskStandardQuizAudioSuccess () {
	
	currentStandardSequence = 0;
	
	seqJson = jQuery('#seq_ids').attr('data-seq-ids');
	
	sequenceIds = JSON.parse(seqJson);
	
	quizSpeciesAnswers = [];
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	
	
	jQuery('#classify_mammal').click( displaySelectMammal );
	
	jQuery('#classify_bird').click( displaySelectBird );
	
	jQuery('.species_select').click( audioSpeciesSelected );
	
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
	
	jQuery('.intelligent_back').click( backClicked );
	
	jQuery('#classify_save_multi').click(function (){
		
		let photoId = jQuery("#videoContainer").attr("data-photo-id");
		
		if ( !photoId ) {
			photoId = jQuery("#audioContainer").attr("data-photo-id");
		}
		
		let speciesId = jQuery(this).attr("data-species-id");
		let speciesName = jQuery(this).attr("data-species-name");
		
		addQuizSpeciesMulti(photoId, speciesId, speciesName);
		
		if ( jQuery(".remove_animal").length >= maxSpeciesPerClip ) {
			displayReachedMaxSpecies();
		}
		else {
			displayWhatMoreHear();
		}
		
	} );
	
	jQuery('#finish_clip').click(displayNextQuestionOrFinish);
	
	setMediaCarouselPause ();
	//setSpeciesSonoPause ();
	
	
		
}


