
var tutSequenceIds = null;
var tutSpeciesIds = null;
var tutSequenceTypes = null;

var beforeFullScreen = true;
var beforeExitFullScreen = true;

function kioskClassifyTutorialSuccess () {

	beforeFullScreen = true;
	beforeExitFullScreen = true;

	
	let seqAttr = jQuery("#sequences_species").attr( "data-sequences");
	let spAttr = jQuery("#sequences_species").attr( "data-species");
	let typeAttr = jQuery("#sequences_species").attr( "data-types");
	
	tutSequenceIds = JSON.parse(seqAttr);
	tutSpeciesIds = JSON.parse(spAttr);
	tutSequenceTypes = JSON.parse(typeAttr);
	
	if ( tutSequenceTypes[0] == "photo" ) {
		jQuery("#play_sequence").show();
	}
	else {
		jQuery("#play_video").show();
	}
	
	addPlayedCallback ( kioskPlayDone );
	
}



function kioskPlayDone () {
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	tutorialFullscreenExtras();
	
	jQuery('#fullscreen-button').one("click", function (){
		
		kioskFullScreenDone();
		
	});
		
	jQuery('#tut_video_fullscreen').one("click", function (){
		
		kioskFullScreenDone();
		
	});
	
	jQuery("#classify_tutorial").hide();
	jQuery("#classify_tutorial_vid").hide();
	jQuery("#play_sequence").hide();
	jQuery("#play_video").hide();
	jQuery('#next_button').hide();
	
	jQuery("#tut_zoom_in").show();
	jQuery("#fullscreen").show();
	
}


function kioskFullScreenDone () {
	
	jQuery('#tut_video_fullscreen').hide();
	jQuery('#tut_video_exitfullscreen').show();
	
	jQuery('#fullscreen-exit-button').one("click", function (){
		kioskFullScreenExitDone();
	});
	
	jQuery('#tut_video_exitfullscreen').one("click", function (){
		kioskFullScreenExitDone();
	});
	
	jQuery("#tut_zoom_in").hide();
	jQuery("#fullscreen").hide();
	
	let fullScreenExitDiv = jQuery(".fullscreen_exit").prop("outerHTML");
	//console.log ("html is: " + fullScreenExitDiv );
	
	if ( tutSequenceTypes[0] == "photo" ) {
		jQuery("#photoCarousel").append( fullScreenExitDiv );
	}
	else {
		jQuery("#videoContainer").append( fullScreenExitDiv );
	}
	
	jQuery("#tut_zoom_out").show();
	jQuery(".fullscreen_exit").show();
	
	
}


function kioskFullScreenExitDone () {
	
	jQuery("#tut_zoom_out").hide();
	jQuery(".fullscreen_exit").hide();
	jQuery('#tut_video_exitfullscreen').hide();

	
	jQuery('#classify_mammal').prop('disabled', false);
	
	jQuery("#tut_what_see").show();
	jQuery("#click_mammal").show();
	
	jQuery('#classify_mammal').one("click", function (){
		
		kioskChooseMammalDone();
		
	});
}


function kioskChooseMammalDone () {
	
	jQuery('#tut_what_see').hide();
	jQuery("#click_mammal").hide();
	
	jQuery('#tut_identify').show();
	jQuery('#might_be_red').show();


	jQuery('.species_group').hide();
	jQuery('#mammal_buttons').show();
	
	
	let sp = tutSpeciesIds[0];
	jQuery('#species_select_' + sp ).one("click",function (){
		
		kioskChooseFirstSpeciesDone();
		
	});
		
}


function kioskChooseFirstSpeciesDone () {
	
	jQuery("#might_be_red").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[0];
	
	populateSpeciesHelplet ( sp, kioskChooseFirstPopulated );
	
}

function kioskChooseFirstPopulated () {
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#not_sure').show();
	jQuery('#chosen_species').show();
	
	jQuery('.back_to_filter' ).one("click", function (){
		
		kioskNoGoBackDone();
		
	});
	
}


function kioskNoGoBackDone () {
	
	jQuery('#tut_happy').hide();
	jQuery('#not_sure').hide();
	jQuery('#chosen_species').hide();
	jQuery('.back_to_filter' ).prop('disabled', true);
		
	jQuery('#tut_identify').show();
	jQuery("#might_be_roe").show();
	jQuery("#mammal_buttons").show();
	
	let sp = tutSpeciesIds[1];
	jQuery('#species_select_' + sp ).one("click",function (){
		
		kioskChooseFirstAgainDone();
		
	});
	
}


function kioskChooseFirstAgainDone () {
	
	jQuery("#might_be_roe").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[1];
	
	populateSpeciesHelplet ( sp, kioskChooseFirstAgainPopulated );
	
	

}


function kioskChooseFirstAgainPopulated() {
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#click_save').show();
	jQuery('#chosen_species').show();
	
	jQuery('#classify_save' ).one("click", function (){
		
		kioskSaveFirstDone();
		
	});
	
}


function kioskSaveFirstDone() {
	
	jQuery('#click_save').hide();
	
	let url = BioDiv.root + "&view=kioskmediacarousel&format=raw&sequence_id=" + tutSequenceIds[1];
	
	jQuery('#media_carousel').load(url, kioskNotInListLoaded);
	
}


function kioskNotInListLoaded (){
	
	jQuery('#tut_happy').hide();
	jQuery('#chosen_species').hide();
	
	jQuery("#filter_buttons").show();
	
	jQuery("#classify_mammal").prop('disabled', false);
	
	jQuery("#tut_what_see_here").show();
	
	if ( tutSequenceTypes[1] == "photo" ) {
		jQuery("#rare_animal").show();
	}
	else {
		jQuery("#rare_animal_vid").show();
	}
	
	
	addPlayedCallback ( rareMammalPlayed );
}

function rareMammalPlayed() {
	
	jQuery("#rare_animal").hide();
	jQuery("#rare_animal_vid").hide();
	jQuery("#mammal_again").show();
	
	jQuery("#classify_mammal").one("click",function (){
		
		kioskClassifyMammalRareDone();
		
	});
	
}


function kioskClassifyMammalRareDone () {
	
	jQuery("#tut_what_see_here").hide();
	jQuery("#mammal_again").hide();
	jQuery('.species_group').hide();
	
	jQuery("#tut_identify").show();
	jQuery("#mammal_buttons").show();
	jQuery("#click_not_on").show();
	
	jQuery("#not_on_mammal_list").prop('disabled', false);
	
	jQuery("#not_on_mammal_list").one("click",function (){
		
		kioskNotOnListDone();
		
	});
	
}


function kioskNotOnListDone () {
	
	jQuery("#click_not_on").hide();
	jQuery("#scroll_all").show();
	
	displayAllMammals(0);
	
	jQuery('#scroll_up_mammals').click(function (){
		displayAllMammals( -1 );
	});
	
	jQuery('#scroll_down_mammals').click(function (){
		displayAllMammals( 1 );
	});
	
	
	let sp = tutSpeciesIds[2];
	let buttonSelector = '#species_select_' + sp ;
	jQuery(buttonSelector).prop('disabled', false);
	
	jQuery(buttonSelector).one("click",function (){
		
		kioskAllMammalsDone();
		
	});
	
}


function kioskAllMammalsDone () {
	
	jQuery("#scroll_all").hide();
	jQuery('.species_group').hide();
	
	let sp = tutSpeciesIds[2];
	
	populateSpeciesHelplet ( sp, kioskAllMammalsPopulated );
	
	
}

function kioskAllMammalsPopulated () {
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#save_move').show();
	jQuery('#chosen_species').show();
	
	jQuery('#classify_save' ).one("click", function (){
		
		kioskSaveRareDone();
		
	});
}


function kioskSaveRareDone () {
	
	jQuery('#save_move').hide();
	
	jQuery('#chosen_species').hide();
	
	let url = BioDiv.root + "&view=kioskmediacarousel&format=raw&sequence_id=" + tutSequenceIds[2];
	
	jQuery('#media_carousel').load(url, kioskNothingLoaded);
	
	
}



function kioskNothingLoaded (){
	
	jQuery('#tut_happy').hide();
	jQuery('#chosen_species').hide();
	
	jQuery("#classify_mammal").prop('disabled', true);
	jQuery("#filter_buttons").show();
	jQuery("#tut_what_see_here").show();
	
	if ( tutSequenceTypes[2] == "photo" ) {
		jQuery("#no_animal").show();
	}
	else {
		jQuery("#no_animal_vid").show();
	}
		
	addPlayedCallback ( kioskNothingPlayed );
	
}


function kioskNothingPlayed () {
	
	jQuery("#no_animal").hide();
	jQuery("#no_animal_vid").hide();
	jQuery("#click_nothing").show();
	
	let sp = tutSpeciesIds[3];
	let buttonSelector = '#species_select_' + sp ;
	
	jQuery(buttonSelector).prop('disabled', false);
	
	jQuery(buttonSelector).one("click",function (){
		
		kioskChooseNothingDone();
		
	});
}


function kioskChooseNothingDone () {
	
	jQuery("#click_nothing").hide();
	jQuery('.species_group').hide();
	
	let sp = tutSpeciesIds[3];
	
	populateSpeciesHelplet ( sp, kioskChooseNothingPopulated );
	
	
}


function kioskChooseNothingPopulated () {
	
	jQuery('#tut_what_see_here').hide();
	jQuery('#tut_happy').show();
	jQuery('#save_final').show();
	jQuery('#chosen_species').show();
	
	jQuery('#classify_save' ).one("click", function (){
		
		kioskSaveNothingDone();
		
	});

}


function kioskSaveNothingDone () {
	
	
	jQuery('.classify_panel_right').hide();
	jQuery('.classify_panel_left').hide();
	jQuery('#save_final').hide();
	jQuery('#tut_happy').hide();
	
	
	setBackgroundImage();
	
	setQuizButton();
	
	setClassifyProjectButton();
	
	setHomeButton();
	
	jQuery('#tut_feedback').show();
	
}


function kioskClassifyAudioTutorialSuccess () {

	beforeFullScreen = true;
	beforeExitFullScreen = true;

	
	let seqAttr = jQuery("#sequences_species").attr( "data-sequences");
	let spAttr = jQuery("#sequences_species").attr( "data-species");
	let typeAttr = jQuery("#sequences_species").attr( "data-types");
	
	tutSequenceIds = JSON.parse(seqAttr);
	tutSpeciesIds = JSON.parse(spAttr);
	tutSequenceTypes = JSON.parse(typeAttr);
	
	setMediaCarouselPause();
	
	jQuery("#play_video_audio").show();
	
	addPlayedCallback ( kioskAudioPlayDone );
	
}



function kioskAudioPlayDone () {
	
	addFullScreenFnly();
	kioskFullscreenExtras();
	tutorialFullscreenExtras();
	
	jQuery("#classify_tutorial_vid").hide();
	jQuery("#play_video_audio").hide();
	
	jQuery('#classify_bird').prop('disabled', false);
	
	jQuery("#tut_what_hear").show();
	jQuery("#click_bird_audio").show();
	
	jQuery('#classify_bird').one("click", function (){
		
		kioskChooseBirdAudioDone();
		
	});

}




function kioskChooseBirdAudioDone () {
	
	jQuery('#tut_what_hear').hide();
	jQuery("#click_bird_audio").hide();
	
	jQuery('#tut_identify').show();
	jQuery('#might_be_jackdaw').show();


	jQuery('.species_group').hide();
	jQuery('#bird_buttons').show();
	
	
	let sp = tutSpeciesIds[0][0];
	jQuery('.species_select_' + sp ).one("click",function (){
		
		kioskChooseFirstSpeciesAudioDone();
		
	});
		
}

function kioskChooseFirstSpeciesAudioDone () {
	
	jQuery("#might_be_jackdaw").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[0][0];
	
	populateSpeciesHelpletAudio ( sp, kioskChooseFirstPopulatedAudio );
}


function kioskChooseFirstPopulatedAudio () {
	
	setSpeciesSonoPause();
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#play_species_sono').show();
	jQuery('#chosen_species').show();
	
	addPlayedSonoCallback ( kioskChooseFirstSonoPlayed );
	
	jQuery('.intelligent_back' ).one("click", function (){
		
		kioskNoGoBackDoneAudio();
		
	});
	
}


function kioskChooseFirstSonoPlayed () {
	
	jQuery('#play_species_sono').hide();
	
	jQuery('#not_sure_audio').show();
	
	/*
	jQuery('.intelligent_back' ).one("click", function (){
		
		kioskNoGoBackDoneAudio();
		
	});
	*/
}


function kioskNoGoBackDoneAudio () {
	
	jQuery("#species_helplet").empty();
	
	jQuery('#tut_happy').hide();
	jQuery('#play_species_sono').hide();
	jQuery('#not_sure_audio').hide();
	jQuery('#chosen_species').hide();
	jQuery('.back_to_filter' ).prop('disabled', true);
		
	jQuery('#tut_identify').show();
	jQuery("#might_be_bluetit").show();
	jQuery("#bird_buttons").show();
	
	let sp = tutSpeciesIds[0][1];
	jQuery('.species_select_' + sp ).one("click",function (){
		
		kioskChooseFirstAgainDoneAudio();
		
	});
	
}


function kioskChooseFirstAgainDoneAudio () {
	
	jQuery("#might_be_bluetit").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[0][1];
	
	populateSpeciesHelpletAudio ( sp, kioskChooseFirstAgainPopulatedAudio );
	
	

}


function kioskChooseFirstAgainPopulatedAudio() {
	
	setSpeciesSonoPause();
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#play_species_sono').show();
	jQuery('#chosen_species').show();
	
	addPlayedSonoCallback ( kioskPlayedFirstAgain );
	
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveFirstDoneAudio();
		
	});
}

function kioskPlayedFirstAgain () {
	
	jQuery('#play_species_sono').hide();
	jQuery('#click_save_audio').show();
	
	/*
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveFirstDoneAudio();
		
	});
	*/
}


function kioskSaveFirstDoneAudio() {
	
	jQuery("#species_helplet").empty();
	jQuery('#play_species_sono').hide();
	
	jQuery('#bird_tag_1').show();
	
	jQuery('#tut_happy').hide();
	jQuery('#click_save_audio').hide();
	jQuery('#chosen_species').hide();
	jQuery('#tut_happy').hide();
	
	jQuery("#tut_hearagain").show();
	jQuery('#filter_buttons').show();
	jQuery('#another_bird').show();
	
	jQuery('#classify_bird').prop('disabled', false);
	jQuery('#classify_bird').one("click", function (){
		
		kioskChosenSecondToRemove();
		
	});
	
}

function kioskChosenSecondToRemove () {
	
	
	jQuery("#tut_hearagain").hide();
	jQuery('#another_bird').hide();
	
	jQuery('#tut_identify').show();
	
	jQuery('.species_group').hide();
	jQuery("#bird_buttons").show();
	jQuery('#might_be_crow').show();
	
	let sp = tutSpeciesIds[0][2];
	jQuery('.species_select_' + sp ).one("click",function (){
		
		kioskRemoveBirdSpeciesSelected();
		
	});
	
}

function kioskRemoveBirdSpeciesSelected () {
	
	
	jQuery("#might_be_crow").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[0][2];
	
	populateSpeciesHelpletAudio ( sp, kioskRemoveBirdPopulatedAudio );
	
}


function kioskRemoveBirdPopulatedAudio () {
	
	setSpeciesSonoPause();
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#play_species_sono').show();
	jQuery('#chosen_species').show();
	
	addPlayedSonoCallback ( kioskPlayedRemove );
	
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveRemoveDoneAudio();
		
	});
}

function kioskPlayedRemove () {
	
	jQuery('#play_species_sono').hide();
	jQuery('#click_save_remove').show();
	
	/*
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveRemoveDoneAudio();
		
	});
	*/
}

function kioskSaveRemoveDoneAudio () {
	
	jQuery("#species_helplet").empty();
	jQuery('#play_species_sono').hide();
	
	jQuery('#bird_tag_2').show();
	
	jQuery('#click_save_remove').hide();
	jQuery('#chosen_species').hide();
	jQuery('#tut_happy').hide();
	
	jQuery('#tut_hearagain').show();
	jQuery('#filter_buttons').show();
	jQuery('#other_button').hide();
	jQuery('#finish_clip').show();
	
	jQuery('#click_to_remove').show();
	
	jQuery('#bird_tag_2').one("click", function (){
		
		kioskRemovedDoneAudio();
		
	});
}

function kioskRemovedDoneAudio() {
	
	jQuery('#bird_tag_2').hide();
	
	jQuery('#tut_happy').hide();
	jQuery('#click_to_remove').hide();
	jQuery('#species_helplet').empty();
	jQuery('#chosen_species').hide();
	jQuery('#tut_happy').hide();
	
	jQuery("#tut_hearagain").show();
	jQuery('#filter_buttons').show();
	jQuery('#another_bird').show();
	
	jQuery('#classify_bird').prop('disabled', false);
	jQuery('#classify_bird').one("click", function (){
		
		kioskChosenSecondBird();
		
	});
	
}


function kioskChosenSecondBird () {
	
	
	jQuery("#tut_hearagain").hide();
	jQuery('#another_bird').hide();
	
	jQuery('#tut_identify').show();
	
	jQuery('.species_group').hide();
	jQuery("#bird_buttons").show();
	jQuery('#might_be_woodpigeon').show();
	
	let sp = tutSpeciesIds[0][3];
	jQuery('.species_select_' + sp ).one("click",function (){
		
		kioskSecondBirdSpeciesSelected();
		
	});
	
}

function kioskSecondBirdSpeciesSelected () {
	
	
	jQuery("#might_be_woodpigeon").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[0][3];
	
	populateSpeciesHelpletAudio ( sp, kioskChooseSecondPopulatedAudio );
	
}


function kioskChooseSecondPopulatedAudio () {
	
	setSpeciesSonoPause();
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#play_species_sono').show();
	jQuery('#chosen_species').show();
	
	addPlayedSonoCallback ( kioskPlayedSecond );
	
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveSecondDoneAudio();
		
	});
}

function kioskPlayedSecond () {
	
	jQuery('#play_species_sono').hide();
	jQuery('#click_save_second').show();
	
	/*
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveSecondDoneAudio();
		
	});
	*/
}

function kioskSaveSecondDoneAudio () {
	
	jQuery("#species_helplet").empty();
	jQuery('#play_species_sono').hide();
	
	jQuery('#bird_tag_3').show();
	
	jQuery('#click_save_second').hide();
	jQuery('#species_helplet').empty();
	jQuery('#chosen_species').hide();
	jQuery('#tut_happy').hide();
	
	jQuery('#tut_hearagain').show();
	jQuery('#filter_buttons').show();
	jQuery('#other_button').hide();
	jQuery('#finish_clip').show();
	
	jQuery('#finish_clip_audio').show();
	
	jQuery('#finish_clip').one("click", function (){
		
		kioskFinishClipDoneAudio();
		
	});
}

function kioskFinishClipDoneAudio() {
	
	jQuery('#finish_clip_audio').hide();
	jQuery('.remove_animal').hide();
	
	let url = BioDiv.root + "&view=kioskmediacarousel&format=raw&sequence_id=" + tutSequenceIds[1];
	
	jQuery('#media_carousel').load(url, kioskNotInListLoadedAudio);
	
}

function kioskNotInListLoadedAudio () {
	
	setMediaCarouselPause();
	
	jQuery('#tut_hearagain').hide();
	jQuery('#finish_clip').hide();
	jQuery('#other_button').show();
	
	jQuery("#tut_play_next").show();
	jQuery("#play_second_audio").show();
	
	
	addPlayedCallback ( kioskNotInListPlayedAudio );
}

function kioskNotInListPlayedAudio () {
	
	jQuery("#play_second_audio").hide();
	jQuery("#tut_play_next").hide();
	
	jQuery("#tut_what_hear").show();
	jQuery("#click_bird_audio").show();
	
	jQuery('#classify_bird').prop('disabled', false);
	jQuery('#classify_bird').one("click", function (){
		
		kioskChooseNotOnBirdDone();
		
	});
	
	
}

function kioskChooseNotOnBirdDone () {
	
	jQuery("#tut_what_hear").hide();
	jQuery("#click_bird_audio").hide();
	
	jQuery('#tut_identify').show();
	
	jQuery('#not_on_list_audio').show();


	jQuery('.species_group').hide();
	jQuery('#bird_buttons').show();
	
	
	jQuery('#not_on_bird_list').one("click", function (){
		
		kioskChooseNotOnListAudioDone();
		
	});
	
}

function kioskChooseNotOnListAudioDone () {
	
	jQuery("#not_on_list_audio").hide();
	jQuery("#scroll_all_audio").show();
	
	displayAllBirds(0);
	
	jQuery('#scroll_up_birds').click(function (){
		displayAllBirds( -1 );
	});
	
	jQuery('#scroll_down_birds').click(function (){
		displayAllBirds( 1 );
	});
	
	
	let sp = tutSpeciesIds[1][0];
	let buttonSelector = '.species_select_' + sp ;
	jQuery(buttonSelector).prop('disabled', false);
	
	jQuery(buttonSelector).one("click",function (){
		
		kioskNotOnBirdSpeciesSelected();
		
	});
	
}

function kioskNotOnBirdSpeciesSelected () {
	
	jQuery("#scroll_all_audio").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[1][0];
	
	populateSpeciesHelpletAudio ( sp, kioskNotOnBirdSpeciesLoaded );
}

function kioskNotOnBirdSpeciesLoaded () {
	
	setSpeciesSonoPause();
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#play_species_sono').show();
	jQuery('#chosen_species').show();
	
	addPlayedSonoCallback ( kioskPlayedNotOnBird );
	
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveNotOnDoneAudio();
		
	});
}

function kioskPlayedNotOnBird () {
	
	jQuery('#play_species_sono').hide();
	jQuery('#click_save_rare_audio').show();
	
	/*
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveNotOnDoneAudio();
		
	});
	*/
}

function kioskSaveNotOnDoneAudio () {
	
	jQuery("#species_helplet").empty();
	jQuery('#play_species_sono').hide();
	
	jQuery('#bird_tag_4').show();
	
	jQuery('#click_save_rare_audio').hide();
	jQuery('#chosen_species').hide();
	jQuery('#tut_happy').hide();
	
	jQuery('#tut_hearagain').show();
	jQuery('#filter_buttons').show();
	jQuery('#other_button').hide();
	jQuery('#finish_clip').show();
	
	jQuery('#another_bird').show();
	
	jQuery('#classify_bird').prop('disabled', false);
	jQuery('#classify_bird').one("click", function (){
		
		kioskChosenLastBird();
		
	});
}

function kioskChosenLastBird () {
	
	
	jQuery("#tut_hearagain").hide();
	jQuery('#another_bird').hide();
	
	jQuery('#tut_identify').show();
	
	jQuery('.species_group').hide();
	jQuery("#bird_buttons").show();
	jQuery('#might_be_wren').show();
	
	let sp = tutSpeciesIds[1][1];
	jQuery('.species_select_' + sp ).one("click",function (){
		
		kioskLastBirdSpeciesSelected();
		
	});
	
}

function kioskLastBirdSpeciesSelected () {
	
	
	jQuery("#might_be_wren").hide();
	jQuery('.species_buttons').hide();
	
	let sp = tutSpeciesIds[1][1];
	
	populateSpeciesHelpletAudio ( sp, kioskChooseLastPopulatedAudio );
	
}


function kioskChooseLastPopulatedAudio () {
	
	setSpeciesSonoPause();
	
	jQuery('#tut_identify').hide();
	jQuery('#tut_happy').show();
	jQuery('#play_species_sono').show();
	jQuery('#chosen_species').show();
	
	addPlayedSonoCallback ( kioskPlayedLast );
	
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveLastDoneAudio();
		
	});
}

function kioskPlayedLast () {
	
	jQuery('#play_species_sono').hide();
	jQuery('#click_save_wren').show();
	
	/*
	jQuery('#classify_save_multi' ).one("click", function (){
		
		kioskSaveLastDoneAudio();
		
	});
	*/
}

function kioskSaveLastDoneAudio () {
	
	jQuery("#species_helplet").empty();
	jQuery('#play_species_sono').hide();
	
	jQuery('#bird_tag_5').show();
	
	jQuery('#click_save_wren').hide();
	jQuery('#chosen_species').hide();
	jQuery('#tut_happy').hide();
	
	jQuery('#tut_hearagain').show();
	jQuery('#filter_buttons').show();
	jQuery('#other_button').hide();
	jQuery('#finish_clip').show();
	
	jQuery('#save_final_audio').show();
	
	jQuery('#finish_clip').one("click", function (){
		
		kioskFinishAllClipsDoneAudio();
		
	});
}

function kioskFinishAllClipsDoneAudio () {
	
	jQuery('.remove_animal').hide();
	
	jQuery('.classify_panel_right').hide();
	jQuery('.classify_panel_left').hide();
	jQuery('#tut_hearagain').hide();
	
	jQuery('#save_final_audio').hide();
	
	//setBackgroundImage();
	
	setQuizButton();
	
	setClassifyAudioProjectButton();
	
	setHomeButton();
	
	jQuery('#tut_feedback').show();
}



function addPlayedCallback ( playedFunction ) {
	
	//jQuery('#classify-video').bind('ended', function (e) {
	jQuery('#classify-video').one('ended', function (e) {
		
		playedFunction();
		
	});
	
	jQuery('#photoCarousel').one('slid.bs.carousel', function (e) {
		
		let activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		
		let lastId = jQuery('#photoCarouselInner').find(".last-photo").attr("data-photo-id");
		
		if ( activeId == lastId ) playedFunction();
			
	});
	
}

function addPlayedSonoCallback ( playedFunction ) {
	
	//jQuery('#classify-video').bind('ended', function (e) {
	//jQuery('#species_helplet > video').one('ended', function (e) {
	let speciesVid = jQuery('#species_helplet').find('video');
	//jQuery('video').one('ended', function (e) {
	speciesVid.one('ended', function (e) {
		
		playedFunction();
		
	});
	/*
	jQuery('video').one('pause', function (e) {
		
		playedFunction();
		
	});
	*/
}
	
	
function populateSpeciesHelplet ( speciesId, nextFunction ) {
	
	jQuery('#species_helplet').empty();
	
	// Populate the helplet
	var url = BioDiv.root + "&view=kioskspecies&format=raw&option_id=" + speciesId;
	jQuery('#species_helplet').load(url, nextFunction);
	
	// Ensure that no hyperlinks can be clicked in kiosk mode
	jQuery('#species_helplet').on('click', 'a', function(e) {
		e.preventDefault();
	});	
		

}
    
function populateSpeciesHelpletAudio ( speciesId, nextFunction ) {
	
	jQuery('#species_helplet').empty();
	
	// Populate the helplet
	var url = BioDiv.root + "&view=kioskspeciesaudio&format=raw&option_id=" + speciesId;
	jQuery('#species_helplet').load(url, nextFunction);
	
	// Ensure that no hyperlinks can be clicked in kiosk mode
	jQuery('#species_helplet').on('click', 'a', function(e) {
		e.preventDefault();
	});	
		

}
    
function tutorialFullscreenExtras() {
	
	jQuery('#tut_video_fullscreen').click(function (){
	
		var video = document.getElementById('tutorial_videoContainer');
		if("requestFullscreen" in video) 
		{
			video.requestFullscreen();
		} 
		else if ("webkitRequestFullscreen" in video) 
		{
			video.webkitRequestFullscreen();
		} 
		else if ("mozRequestFullScreen" in video) 
		{
			video.mozRequestFullScreen();
		} 
		else if ("msRequestFullscreen" in video) 
		{
			video.msRequestFullscreen();
		}
		
				
	});
	
	jQuery('#tut_video_exitfullscreen').click(function (){
		
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




