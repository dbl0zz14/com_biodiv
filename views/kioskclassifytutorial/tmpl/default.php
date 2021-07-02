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
	
	print '<div id="sequences_species" data-sequences="'.$this->seqsJson.'" data-species="'.$this->speciesInSeqsJson.'" data-types=\''.$this->typesInSeqsJson.'\' hidden></div>';
	if ( $this->roeDeerType == "photo" ) {
		print '  <h1 id="classify_tutorial" class="text-center classify_heading">'.$this->translations['tutorial']['translation_text'].'</h1>';
		print '  <h1 id="classify_tutorial_vid" class="text-center classify_heading" style="display:none">'.$this->translations['tutorial_vid']['translation_text'].'</h1>';
	}
	else {
		print '  <h1 id="classify_tutorial" class="text-center classify_heading" style="display:none">'.$this->translations['tutorial']['translation_text'].'</h1>';
		print '  <h1 id="classify_tutorial_vid" class="text-center classify_heading">'.$this->translations['tutorial_vid']['translation_text'].'</h1>';
	}
	print '  <h1 id="tut_zoom_in" class="text-center classify_heading" style="display:none">'.$this->translations['tut_zoom_in']['translation_text'].'</h1>';
	print '  <h1 id="tut_what_see" class="text-center classify_heading" style="display:none">'.$this->translations['tut_what_see']['translation_text'].'</h1>';
	print '  <h1 id="tut_what_see_here" class="text-center classify_heading" style="display:none">'.$this->translations['what_see_here']['translation_text'].'</h1>';
	print '  <h1 id="tut_identify" class="text-center classify_heading" style="display:none">'.$this->translations['tut_identify']['translation_text'].'</h1>';
	print '  <h1 id="tut_happy" class="text-center classify_heading" style="display:none">'.$this->translations['tut_happy']['translation_text'].'</h1>';
	
	
	
	// --------------------- Instruction panels -----------------------------
	
	print '<div id="play_sequence" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['play_seq']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_next']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="play_video" class="instructions topleft col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['play_vid']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="fullscreen" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['zoom_in']['translation_text'].'</div>';
	print '</div>';
	
	print '<div class="fullscreen_exit instructions bottomright col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['zoom_out']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_mammal" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['a_mammal']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_mammal']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="might_be_red" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_red']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_red']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="not_sure" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['not_sure']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['go_back']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="might_be_roe" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['might_be_roe']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_roedeer']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_save" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['yes_def']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_save']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="rare_animal" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['look_thro_second']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="rare_animal_vid" class="instructions topleft col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['look_thro_second_vid']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="no_animal" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['look_thro']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="no_animal_vid" class="instructions topleft col-md-4" style="display:none">';
	print '<div class="h2">'.$this->translations['look_thro_vid']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_nothing" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['no_animal']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_nothing']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="save_move" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['save_move']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="mammal_again" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['mammal_again']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_mammal']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="click_not_on" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['species_rare']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['click_not_on']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="scroll_all" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['on_longer']['translation_text'].'</div>';
	print '<div class="h2">'.$this->translations['scroll_down']['translation_text'].'</div>';
	print '</div>';
	
	print '<div id="save_final" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.$this->translations['save_final']['translation_text'].'</div>';
	print '</div>';
	
	
	
	
	
	//print '<div id="next_div" class="col-md-2"> <button id="next_button" class="btn btn-block btn-info h2 small_btn" > '.$this->translations['next']['translation_text'].' </button> </div>';

	
	
	
	// --------------------- Photos or video,  LHS --------------------------

	
	print '<div class="col-md-7">';
	
	print '<div class="classify_panel_left" >';

	print '<div id="media_carousel" class="carousel_lower">';
	
	// If it's a photo, add columns so that controls can be placed outside photo
	if ( $this->roeDeerType == "photo" ) {
		print '<div class="col-md-12">';
		print '<div class="col-md-12">';

		$this->mediaCarousel->generateMediaCarousel($this->roeDeerSequence);
		
		print '</div>'; // col-12
		print '</div>'; // col-12
	}
	else {
		print '<div id="tutorial_videoContainer">';
		$this->mediaCarousel->generateMediaCarousel($this->roeDeerSequence);
		
		// Add a transparent div so that we can hijack the full screen click.
		print '<div id="tut_video_fullscreen"></div>';
		print '<div id="tut_video_exitfullscreen" style="display:none"></div>';
		print '</div>';
	}

	print '</div>'; // media_carousel
	print '</div>'; // classify_panel_left
	print '</div>'; // col-7


	
	print '<div class="col-md-5">';
	print '<div class="classify_panel_right">';




	// --------------------- Filter panel --------------------------

	print '<div id="filter_buttons" class="species_group">';

	print '<div class="col-md-6">';
	print '	<button id="classify_mammal" class="btn btn-lg btn-block btn-success h3 control_btn" disabled>'.$this->translations['mammal']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="classify_bird" class="btn btn-lg btn-block btn-success h3 control_btn" disabled>'.$this->translations['bird']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->nothingId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select"  disabled>'.$this->translations['nothing']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->humanId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select"  disabled>'.$this->translations['human']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6 col-md-offset-3">';
	print '	<button id="species_select_'.$this->dkId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select"  disabled>'.$this->translations['dk']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // filter_buttons




	// --------------------- Common Mammals panel --------------------------

	print '<div id="mammal_buttons" class="species_buttons species_group col-md-12" style="display:none">';


	$i = 0;
	foreach ( $this->commonMammals as $species ) {
		
		$image = codes_getName($species['id'],'png');
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
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
	}
	

	print '<div class="col-md-6">';
	print '	<button id="not_on_mammal_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.$this->translations['notonlist']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // mammal_buttons


	// --------------------- All Mammals panel --------------------------

	print '<div id="all_mammal_buttons" class="species_buttons species_group col-md-12" style="display:none">';
	
	// Scroll up
	print '<div class="row"><button id="scroll_up_mammals" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-up"></span></button></div>';
	
	$i = 0;
	$numSpecies = count($this->allMammals);
	
	foreach ( $this->allMammals as $species ) {
		$image = codes_getName($species['id'],'png');
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
					
		print '<div class="col-md-3 all_mammals_species">';
		if ( $isLongSpeciesName ) {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
		
		
	}
	
	// Scroll down
	print '<div class="row"><button id="scroll_down_mammals" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';

	

	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['other']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // all_mammal_buttons


	// --------------------- Bird panel --------------------------

	print '<div id="bird_buttons" class="species_buttons species_group col-md-12" style="display:none">';

	$i = 0;
	foreach ( $this->commonBirds as $species ) {
		
		$image = codes_getName($species['id'],'png');
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
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
	}
	
	// foreach ( $this->commonBirds as $species ) {
		// print '<div class="col-md-3">';
		// print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-success species_select species_button">'.$species['name'].'</button>';
		// print '</div>';
	// }

	print '<div class="col-md-6">';
	print '	<button id="not_on_bird_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.$this->translations['notonlist']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';


	print '</div>'; // bird_buttons


	// --------------------- All Birds panel --------------------------

	print '<div id="all_bird_buttons" class="species_buttons species_group col-md-12" style="display:none">';
	
	
	// Scroll up
	print '<div class="row"><button id="scroll_up_birds" class="btn btn-lg btn-block btn-success scroll_btn"><span class="fa fa-2x fa-chevron-up"></span></button></div>';
	
	$i = 0;
	$numSpecies = count($this->allBirds);
	
	foreach ( $this->allBirds as $species ) {
		$image = codes_getName($species['id'],'png');
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
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
		
		
	}
	
	// Scroll down
	print '<div class="row"><button id="scroll_down_birds" class="btn btn-lg btn-block btn-success scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';
	
	
	
	
	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['other']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // all_bird_buttons
	
	



	// --------------- Chosen species -------------------------

	print '<div id="chosen_species"  class="species_group" style="display:none">';

	print "<div id='species_helplet'></div>";

	print '<div class="col-md-6">';
	print '	<button id="classify_save" class="btn btn-lg btn-block btn-success h3 control_btn" >'.$this->translations['yes_save']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 back_to_filter control_btn" >'.$this->translations['no_back']['translation_text'].'</button>';
	print '</div>';


	print '</div>'; // chosen_species


	print '</div>'; // classify_panel_right
	print '</div>'; // col-5 
	
	
	
	// --------------------- Thank you and what next ------------------------
	
	print '<div id="tut_feedback" class="col-md-12" style="display:none">';
	print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->projectId.'" data-user-key="'.$this->user_key.'" >';

	print '<div class="opaque-bg">';
	
	print '  <h2 id="thank_you" class="text-center classify_heading "><strong>'.$this->translations['thank_you']['translation_text'].'</strong></h2>';
	
	print '<div class="col-md-12">';
	
	print '  <h2 class="text-center classify_heading">'.$this->translations['hope_enjoyed']['translation_text'].'</h2>';
	
	print '<div class="col-md-4">';
	print '	<button id="kiosk_quiz" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['quiz']['translation_text'].'</button>';
	print '</div>';



	print '<div class="col-md-4">';
	print '	<button id="classify_project" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['classify_project']['translation_text'].'</button>';
	print '</div>';



	print '<div class="col-md-4">';
	print '	<button class="btn btn-lg btn-block btn-success h2 control_btn back_to_home" >'.$this->translations['home_page']['translation_text'].'</button>';
	print '</div>';



	
	print '</div>'; // col-12
	
	print '</div>'; // opaque
	
	print '</div>'; // jumbotron
	print '</div>'; // col-12
	
	/*
	print '<div class="col-md-12">';
	print '<div class="col-md-12">';
	print '<div id="whatsee_info" class="panel panel-default">';
	print '<div class="panel-heading">';
	print '<h3><span class="fa fa-info-circle"></span> '.$this->translations['look_thro']['translation_text'] . '</h3>';  
	print '</div>'; // panel body
	print '</div>'; // panel 
	print '</div>'; // col-12
	print '</div>'; // col-12
	*/
	
	
}






?>


