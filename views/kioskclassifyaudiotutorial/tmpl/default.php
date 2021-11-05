<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ("Template called" );

if ( $this->sequenceError != null ) {
	print '  <h2 class="text-center classify_heading">'.$this->sequenceError.'</h2>';
}
else {
	
	print '<div id="sequences_species" data-sequences="'.$this->seqIdsJson.'" data-species="'.$this->speciesIdsJson.'" data-types=\''.$this->mediaTypesInSeqsJson.'\' hidden></div>';
	
	
	print '  <h1 id="classify_tutorial_vid" class="text-center classify_heading">'.$this->translations['tutorial_vid']['translation_text'].'</h1>';
	print '  <h1 id="tut_zoom_in" class="text-center classify_heading" style="display:none">'.$this->translations['tut_zoom_in']['translation_text'].'</h1>';
	print '  <h1 id="tut_what_hear" class="text-center classify_heading" style="display:none">'.$this->translations['tut_what_hear']['translation_text'].'</h1>';
	print '  <h1 id="tut_hearagain" class="text-center classify_heading" style="display:none">'.$this->translations['hear_another']['translation_text'].'</h1>';  
	print '  <h1 id="tut_identify" class="text-center classify_heading" style="display:none">'.$this->translations['tut_identify']['translation_text'].'</h1>';
	print '  <h1 id="tut_happy" class="text-center classify_heading" style="display:none">'.$this->translations['tut_happy']['translation_text'].'</h1>';
	print '  <h1 id="tut_play_next" class="text-center classify_heading" style="display:none">'.$this->translations['tut_play_next']['translation_text'].'</h1>';
	
	
	
	// --------------------- Instruction panels -----------------------------
	
	print '<div id="play_video_audio" class="instructions topleft col-md-5" style="display:none">';
	print '<div class="h2">'.$this->translations['play_video_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_bird_audio" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['a_bird']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_bird_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="might_be_jackdaw" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_jackdaw']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_jackdaw']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="play_species_sono" class="instructions righttop col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['play_species_sono']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="not_sure_audio" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['not_sure_audio']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['go_back']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="might_be_bluetit" class="instructions righttop col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_bluetit']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_bluetit']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_save_audio" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['yes_def_audio']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_save_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="another_bird" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['another_bird']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_bird_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="might_be_crow" class="instructions righttop col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_crow']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_crow']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_save_remove" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['yes_crow']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_save_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_to_remove" class="instructions lefttop col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['click_to_remove']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="might_be_woodpigeon" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_woodpigeon']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_woodpigeon']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_save_second" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['yes_def_woodpigeon']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_save_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="finish_clip_audio" class="instructions topright col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['finish_clip_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="play_second_audio" class="instructions topleft col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['play_next_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="not_on_list_audio" class="instructions rightbottom col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_cuckoo']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['not_on_list_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="scroll_all_audio" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['on_longer']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['scroll_down_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_save_rare_audio" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['yes_def_rare_audio']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_save_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="might_be_wren" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_wren']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_wren']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_save_wren" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['yes_def_wren']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_save_audio']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="save_final_audio" class="instructions topright col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['save_final_audio']['translation_text'].'</div>';
	print '</div>';
	
	
	
	
	
	//print '<div id="next_div" class="col-md-2"> <button id="next_button" class="btn btn-block btn-info h2 small_btn" > '.$this->translations['next']['translation_text'].' </button> </div>';

	
	
	
	// --------------------- Photos or video,  LHS --------------------------

	
	print '<div class="col-md-7">';
	
	print '<div class="classify_panel_left" >';

	print '<div id="media_carousel" class="carousel_lower">';
	
	print '<div id="tutorial_videoContainer">';
	$this->mediaCarousel->generateMediaCarousel($this->trainingSequences[0]);
		
	// Add a transparent div so that we can hijack the full screen click.
	print '<div id="tut_video_fullscreen"></div>';
	print '<div id="tut_video_exitfullscreen" style="display:none"></div>';
	print '</div>';
	

	print '</div>'; // media_carousel
	
	print '<div id="current_species" class="btn-group" role="group" aria-label="classifications">';
	
	$tagCount = 0;
	foreach ( $this->allSequences as $seq ) {
		foreach ( $seq->species as $birdId ) {
			
			$label = codes_getName($birdId, 'contenttran');
			
			print "<button id='bird_tag_". $tagCount."' type='button' class='remove_animal btn btn-info'  style='display:none'>$label <span aria-hidden='true' class='fa fa-times-circle'></span><span class='sr-only'>Close</span></button>";
			
			$tagCount++;
		}
	}
	
	print '</div>';
	
	print '</div>'; // classify_panel_left
	print '</div>'; // col-7
	
	print '<div class="col-md-5">';
	print '<div class="classify_panel_right">';




	// --------------------- Filter panel --------------------------

	print '<div id="filter_buttons" class="species_group">';

	print '<div id="bird_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="classify_bird" class="btn btn-lg btn-block btn-success h3 control_btn">'.$this->translations['bird']['translation_text'].'</button>';
	print '</div>';


	print '<div id="nothing_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="species_select_'.$this->nothingId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['nothing']['translation_text'].'</button>';
	print '</div>';


	print '<div id="human_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="species_select_'.$this->humanId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['human']['translation_text'].'</button>';
	print '</div>';


	print '<div id="other_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['other']['translation_text'].'</button>';
	print '</div>';

	print '<div id="finish_clip" class="kiosk_filter_btn col-md-6" style="display:none">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn finish_clip">'.$this->translations['finish']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // filter_buttons


	// --------------------- Bird panel --------------------------

	print '<div id="bird_buttons" class="species_buttons species_group col-md-12" style="display:none">';

	$i = 0;
	foreach ( $this->commonBirds as $species ) {
		
		$image = codes_getName($species['id'],'kioskimg');
		$imageText = "";
		if ( $image ) {
			$imageURL = JURI::root().$image;
			$imageText = "<img width='100%' src='".$imageURL."'>";
		}
		
		$isLongSpeciesName = false;
		if ( strlen($species['name']) > 13 ) $isLongSpeciesName = true;
		
		$newRow = ( $i%4 == 0 );
		$endOfRow = ( ($i+1)%4 == 0 );
		
		if ( $newRow ) print '<div class="row">';
					
		print '<div class="col-md-3">';
		if ( $isLongSpeciesName ) {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
			print '	<button class="species_select_'.$species['id'].' btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
			print '	<button class="species_select_'.$species['id'].' btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
	}

	print '<div class="col-md-6">';
	print '	<button id="not_on_bird_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.$this->translations['notonlist']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn intelligent_back" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // bird_buttons


	// --------------------- All Birds panel --------------------------

	print '<div id="all_bird_buttons" class="species_buttons species_group col-md-12" style="display:none">';
		
		
	// Scroll up
	print '<div class="row"><button id="scroll_up_birds" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-up"></span></button></div>';
	
	$i = 0;
	$numSpecies = count($this->allBirds);
	
	foreach ( $this->allBirds as $species ) {
		$image = codes_getName($species['id'],'kioskimg');
		$imageText = "";
		if ( $image ) {
			$imageURL = JURI::root().$image;
			$imageText = "<img width='100%' src='".$imageURL."'>";
		}
		
		$isLongSpeciesName = false;
		if ( strlen($species['name']) > 13 ) $isLongSpeciesName = true;
		
		$newRow = ( $i%4 == 0 );
		$endOfRow =  ( ($i+1)%4 == 0 ) || ( $i+1 == $numSpecies ) ;
		
		if ( $newRow ) print '<div class="row">';
					
		print '<div class="col-md-3 all_birds_species">';
		if ( $isLongSpeciesName ) {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
			print '	<button class="species_select_'.$species['id'].' btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
			print '	<button class="species_select_'.$species['id'].' btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
		
		
	}
	
	// Scroll down
	print '<div class="row"><button id="scroll_down_birds" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';
	
	
	
	print '<div class="col-md-12 anchor_bottom">';

	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['other']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn intelligent_back" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // anchor_bottom
	
	print '</div>'; // all_bird_buttons


	// --------------- Chosen species -------------------------

	print '<div id="chosen_species"  class="species_group" style="display:none">';

	print "<div class='well species_well'><div id='species_helplet'></div></div>";

	print '<div class="col-md-6">';
	print '	<button id="classify_save_multi" class="btn btn-lg btn-block btn-success h3 control_btn" >'.$this->translations['yes_save']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 intelligent_back control_btn" >'.$this->translations['no_back']['translation_text'].'</button>';
	print '</div>';


	print '</div>'; // chosen_species


	print '</div>'; // classify_panel_right
	print '</div>'; // col-5 
	
	
	
	// --------------------- Thank you and what next ------------------------
	
	print '<div id="tut_feedback" class="col-md-12" style="display:none">';
	
	print '  <h1 id="thank_you" class="text-center extra_lower "><strong>'.$this->translations['thank_you']['translation_text'].'</strong></h1>';
	
	print '<h2 class="text-center classify_heading">'.$this->translations['you_spotted']['translation_text'] . '</h2>';
	
	print '<div class="row feedback_species_row">';
	
	print "<div class='col-md-2'></div>"; // offset
	
	foreach ( $this->feedbackBirds as $birdName=>$kioskImage ) {
		print "<div class='col-md-2'>";
		
		print '<div class="text-center h4">'.$birdName.'</div>';
		
		print "</div>"; // col-2
	}
	
	print "</div>"; // row
	
	print '<div class="row">';
	
	print "<div class='col-md-2'></div>"; // offset
	
	foreach ( $this->feedbackBirds as $birdName=>$kioskImage ) {
		
		print '<div class="col-md-2">';
		
		$imageURL = JURI::root().$kioskImage;
		
		print '<img class="img-responsive center-block" style="max-height:48vh;" src="' . $imageURL . '" />';
		
		print '</div>'; // col-2
	}
	
	print "</div>"; // row
	
	print '  <h2 class="text-center classify_heading">'.$this->translations['hope_enjoyed']['translation_text'].'</h2>';
	
	
	print '<div class="col-md-12">';
	print '<div class="col-md-12">';
	
	print '<div class="col-md-4">';
	print '	<button id="kiosk_quiz" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['quiz']['translation_text'].'</button>';
	print '</div>';



	print '<div class="col-md-4">';
	print '	<button id="classify_audio_project" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['classify_project']['translation_text'].'</button>';
	print '</div>';



	print '<div class="col-md-4">';
	print '	<button class="btn btn-lg btn-block btn-success h2 control_btn back_to_home" >'.$this->translations['home_page']['translation_text'].'</button>';
	print '</div>';
	
	print '</div>'; // col-12
	print '</div>'; // col-12

	
	
	print '</div>'; // col-12
	
		
}






?>


