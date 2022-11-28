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
		print '  <h1 id="classify_tutorial" class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUTORIAL").'</h1>';
		print '  <h1 id="classify_tutorial_vid" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUTORIAL_VID").'</h1>';
	}
	else {
		print '  <h1 id="classify_tutorial" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUTORIAL").'</h1>';
		print '  <h1 id="classify_tutorial_vid" class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUTORIAL_VID").'</h1>';
	}
	print '  <h1 id="tut_zoom_in" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUT_ZOOM_IN").'</h1>';
	print '  <h1 id="tut_what_see" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUT_WHAT_SEE").'</h1>';
	print '  <h1 id="tut_what_see_here" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_WHAT_SEE_HERE").'</h1>';
	print '  <h1 id="tut_identify" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUT_IDENTIFY").'</h1>';
	print '  <h1 id="tut_happy" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_TUT_HAPPY").'</h1>';
	
	
	
	// --------------------- Instruction panels -----------------------------
	
	print '<div id="play_sequence" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_PLAY_SEQ").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_NEXT").'</div>';
	print '</div>';
	
	print '<div id="play_video" class="instructions topleft col-md-4" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_PLAY_VID").'</div>';
	print '</div>';
	
	print '<div id="fullscreen" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_ZOOM_IN").'</div>';
	print '</div>';
	
	print '<div class="fullscreen_exit instructions bottomright col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_ZOOM_OUT").'</div>';
	print '</div>';
	
	print '<div id="click_mammal" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_A_MAMMAL").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_MAMMAL").'</div>';
	print '</div>';
	
	print '<div id="might_be_red" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_MIGHT_BE_RED").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_RED").'</div>';
	print '</div>';
	
	print '<div id="not_sure" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_NOT_SURE").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_GO_BACK").'</div>';
	print '</div>';
	
	print '<div id="might_be_roe" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_MIGHT_BE_ROE").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_ROEDEER").'</div>';
	print '</div>';
	
	print '<div id="click_save" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_YES_DEF").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_SAVE").'</div>';
	print '</div>';
	
	print '<div id="rare_animal" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_LOOK_THRO_SECOND").'</div>';
	print '</div>';
	
	print '<div id="rare_animal_vid" class="instructions topleft col-md-4" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_LOOK_THRO_SECOND_VID").'</div>';
	print '</div>';
	
	print '<div id="no_animal" class="instructions top col-md-4" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_LOOK_THRO").'</div>';
	print '</div>';
	
	print '<div id="no_animal_vid" class="instructions topleft col-md-4" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_LOOK_THRO_VID").'</div>';
	print '</div>';
	
	print '<div id="click_nothing" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_NO_ANIMAL").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_NOTHING").'</div>';
	print '</div>';
	
	print '<div id="save_move" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_SAVE_MOVE").'</div>';
	print '</div>';
	
	print '<div id="mammal_again" class="instructions top col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_MAMMAL_AGAIN").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_MAMMAL").'</div>';
	print '</div>';
	
	print '<div id="click_not_on" class="instructions rightbottom col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_SPECIES_RARE").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLICK_NOT_ON").'</div>';
	print '</div>';
	
	print '<div id="scroll_all" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_ON_LONGER").'</div>';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_SCROLL_DOWN").'</div>';
	print '</div>';
	
	print '<div id="save_final" class="instructions right col-md-3" style="display:none">';
	print '<div class="h2">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_SAVE_FINAL").'</div>';
	print '</div>';
	
	
	
	
	
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
	print '	<button id="classify_mammal" class="btn btn-lg btn-block btn-success h3 control_btn" disabled>'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_MAMMAL").'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="classify_bird" class="btn btn-lg btn-block btn-success h3 control_btn" disabled>'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_BIRD").'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->nothingId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select"  disabled>'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_NOTHING").'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->humanId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select"  disabled>'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_HUMAN").'</button>';
	print '</div>';


	print '<div class="col-md-6 col-md-offset-3">';
	print '	<button id="species_select_'.$this->dkId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select"  disabled>'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_DK").'</button>';
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
	print '	<button id="not_on_mammal_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_NOTONLIST").'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_BACK").'</button>';
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
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_OTHER").'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_BACK").'</button>';
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
	print '	<button id="not_on_bird_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_NOTONLIST").'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_BACK").'</button>';
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
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_OTHER").'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_BACK").'</button>';
	print '</div>';

	print '</div>'; // all_bird_buttons
	
	



	// --------------- Chosen species -------------------------

	print '<div id="chosen_species"  class="species_group" style="display:none">';

	print "<div id='species_helplet'></div>";

	print '<div class="col-md-6">';
	print '	<button id="classify_save" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_YES_DEF").'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 back_to_filter control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_NO_BACK").'</button>';
	print '</div>';


	print '</div>'; // chosen_species


	print '</div>'; // classify_panel_right
	print '</div>'; // col-5 
	
	
	
	// --------------------- Thank you and what next ------------------------
	
	print '<div id="tut_feedback" class="col-md-12" style="display:none">';
	print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->projectId.'" data-user-key="'.$this->user_key.'" >';

	print '<div class="opaque-bg">';
	
	print '  <h2 id="thank_you" class="text-center classify_heading "><strong>'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_THANK_YOU").'</strong></h2>';
	
	print '<div class="col-md-12">';
	
	print '  <h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_HOPE_ENJOYED").'</h2>';
	
	print '<div class="col-md-4">';
	print '	<button id="kiosk_quiz" class="btn btn-lg btn-block btn-success h2 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_QUIZ").'</button>';
	print '</div>';



	print '<div class="col-md-4">';
	print '	<button id="classify_project" class="btn btn-lg btn-block btn-success h2 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_CLASSIFY_PROJECT").'</button>';
	print '</div>';



	print '<div class="col-md-4">';
	print '	<button class="btn btn-lg btn-block btn-success h2 control_btn back_to_home" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYTUTORIAL_HOME_PAGE").'</button>';
	print '</div>';



	
	print '</div>'; // col-12
	
	print '</div>'; // opaque
	
	print '</div>'; // jumbotron
	print '</div>'; // col-12
	
	
	
	
}






?>


