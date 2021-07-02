<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->sequence == null ) {
	print '  <h2 class="text-center classify_heading">'.$this->translations['no_sequences']['translation_text'].'</h2>';
}

else {

	print '  <h1 id="classify_whatsee" class="text-center classify_heading">'.$this->translations['what_see']['translation_text'].'</h1>';  

	print '  <h1 id="classify_select" class="text-center classify_heading" style="display:none">'.$this->translations['select']['translation_text'].'</h1>';  

	print '  <h1 id="classify_happy" class="text-center classify_heading" style="display:none">'.$this->translations['happy']['translation_text'].'</h1>';  

	
	// --------------------- Photos or video,  LHS --------------------------

	
	print '<div class="col-md-7">';
	
	print '<div class="classify_panel_left" >';

	print '<div id="media_carousel" class="carousel_lower">';
	
	// If it's a photo, add columns so that controls can be placed outside photo
	if ( $this->sequence->getMedia() == "photo" ) {
		print '<div class="col-md-12">';
		print '<div class="col-md-12">';

		$this->mediaCarousel->generateMediaCarousel($this->sequence);
		
		print '</div>'; // col-12
		print '</div>'; // col-12
	}
	else {
		$this->mediaCarousel->generateMediaCarousel($this->sequence);
	}

	print '</div>'; // media_carousel
	print '</div>'; // classify_panel_left
	print '</div>'; // col-7


	
	print '<div class="col-md-5">';
	print '<div class="classify_panel_right">';



	// --------------------- Filter panel --------------------------

	print '<div id="filter_buttons" class="species_group">';

	print '<div class="col-md-6">';
	print '	<button id="classify_mammal" class="btn btn-lg btn-block btn-success h3 control_btn">'.$this->translations['mammal']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="classify_bird" class="btn btn-lg btn-block btn-success h3 control_btn">'.$this->translations['bird']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->nothingId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['nothing']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->humanId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['human']['translation_text'].'</button>';
	print '</div>';


	print '<div class="col-md-6 col-md-offset-3">';
	print '	<button id="species_select_'.$this->dkId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['dk']['translation_text'].'</button>';
	print '</div>';

	print '</div>'; // filter_buttons


	// --------------------- Common Mammals panel --------------------------

	print '<div id="mammal_buttons" class="species_buttons species_group col-md-12" style="display:none">';

	// *********************  CHANGE THIS TO ONLY PRINT FIRST 20 TO ALLOW FOR ERRORS IN LISTS
	$i = 0;
	foreach ( $this->commonMammals as $species ) {
		
		$image = codes_getName($species['id'],'png');
		$imageText = "";
		if ( $image ) {
			$imageURL = JURI::root().$image;
			$imageText = "<img width='100%' src='".$imageURL."' style='object-fit: cover'>";
		}
		
		$isLongSpeciesName = false;
		if ( strlen($species['name']) > 13 ) $isLongSpeciesName = true;
		
		$newRow = ( $i%4 == 0 );
		$endOfRow = ( ($i+1)%4 == 0 );
		
		if ( $newRow ) print '<div class="row">';
					
		print '<div class="col-md-3">';
		if ( $isLongSpeciesName ) {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
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

	
	print '<div class="col-md-12 anchor_bottom">';
	
	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['other']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';
	
	print '</div>'; // anchor_bottom

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
	print '<div class="row"><button id="scroll_up_birds" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-up"></span></button></div>';
	
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
	print '<div class="row"><button id="scroll_down_birds" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';
	
	
	
	
	print '<div class="col-md-12 anchor_bottom">';
	print '<div class="col-md-6">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.$this->translations['other']['translation_text'].'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.$this->translations['back']['translation_text'].'</button>';
	print '</div>';
	
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
	
	
	if ( $this->sequence->getMedia() == "photo" ) {
		
		print '<div id="whatsee_info" class="info col-md-7">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.$this->translations['look_thro']['translation_text'].'</div>';
		print '</div>';
		
		print '<div id="whatsee_vid_info" class="info col-md-7" style="display:none">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.$this->translations['look_thro_vid']['translation_text'].'</div>';
		print '</div>';
	}
	else {
		print '<div id="whatsee_info" class="info col-md-7" style="display:none">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.$this->translations['look_thro']['translation_text'].'</div>';
		print '</div>';
		
		print '<div id="whatsee_vid_info" class="info col-md-7">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.$this->translations['look_thro_vid']['translation_text'].'</div>';
		print '</div>';
	}
	
	
}






?>


