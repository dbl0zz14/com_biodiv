
var tutSequenceIds = null;
var tutSpeciesIds = null;
var tutSequenceTypes = null;

var beforeFullScreen = true;
var beforeExitFullScreen = true;

function kioskClassifyTutorialSuccess () {

	console.log("Tutorial loaded - now in separate js file");
	
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


function populateSpeciesHelplet ( speciesId, nextFunction ) {
	
	jQuery('#species_helplet').empty();
	
	// Populate the helplet
	var url = BioDiv.root + "&view=kioskspecies&format=raw&option_id=" + speciesId;
	jQuery('#species_helplet').load(url, nextFunction);
	
	// Ensure that no hyperlinks can be clicked in kiosk mode
	jQuery('#species_helplet').on('click', 'a', function(e) {
		e.preventDefault();
		console.log(jQuery(this).attr('href'));
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




