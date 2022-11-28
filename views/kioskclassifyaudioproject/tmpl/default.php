<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->sequence == null ) {
	print '  <h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_NO_SEQUENCES").'</h2>';
}

else {

	print '  <h1 id="classify_whathear" class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_WHAT_HEAR").'</h1>';  

	print '  <h1 id="classify_hearagain" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_HEAR_ANOTHER").'</h1>';  

	print '  <h1 id="classify_maxspecies" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_MAX_SPECIES").'</h1>';  

	print '  <h1 id="classify_select" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_SELECT").'</h1>';  

	print '  <h1 id="classify_happy" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_HAPPY").'</h1>';  

	
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
	
	print '<div id="current_species" class="btn-group" role="group" aria-label="classifications"></div>';
	print '</div>'; // classify_panel_left
	print '</div>'; // col-7


	
	print '<div class="col-md-5">';
	print '<div class="classify_panel_right">';



	// --------------------- Filter panel --------------------------

	print '<div id="filter_buttons" class="species_group">';

	
	print '<div id="bird_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="classify_bird" class="btn btn-lg btn-block btn-success h3 control_btn">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_BIRD").'</button>';
	print '</div>';


	print '<div id="nothing_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="species_select_'.$this->nothingId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_NOTHING").'</button>';
	print '</div>';


	print '<div id="human_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="species_select_'.$this->humanId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_HUMAN").'</button>';
	print '</div>';


	print '<div id="other_button" class="kiosk_filter_btn col-md-6">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_OTHER").'</button>';
	print '</div>';

	print '<div id="finish_clip" class="kiosk_filter_btn col-md-6" style="display:none">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn finish_clip">'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_FINISH").'</button>';
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
	print '	<button id="not_on_bird_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_NOTONLIST").'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn intelligent_back" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_BACK").'</button>';
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
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_OTHER").'</button>';
	print '</div>';


	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn intelligent_back" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_BACK").'</button>';
	print '</div>';
	
	print '</div>';

	print '</div>'; // all_bird_buttons


	// --------------- Chosen species -------------------------

	print '<div id="chosen_species"  class="species_group" style="display:none">';

	print "<div class='well species_well'><div id='species_helplet'></div></div>";
	
	print '<div class="row">';

	print '<div class="col-md-6">';
	print '	<button id="classify_save_multi" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_YES_SAVE").'</button>';
	print '</div>';

	print '<div class="col-md-6">';
	print '	<button class="btn btn-lg btn-block btn-success h3 intelligent_back control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_NO_BACK").'</button>';
	print '</div>';
	
	print '</div>'; // row
	
	
	print '</div>'; // chosen_species


	print '</div>'; // classify_panel_right
	print '</div>'; // col-5 
	
	
	print '<div id="whathear_info" class="mwinfo col-md-6">';
	print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_LOOK_THRO_SONO").'</div>';
	print '</div>';
	
	print '<div id="whatmore_info" class="mwinfo col-md-6" style="display:none">';
	print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_WHATMORE_INFO").'</div>';
	print '</div>';
	
	print '<div id="maxspecies_info" class="mwinfo col-md-6" style="display:none">';
	print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_MAXSPECIES_INFO").'</div>';
	print '</div>';
	
	print '<div id="select_info" class="mwinfo col-md-6" style="display:none">';
	print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_SELECT_INFO").'</div>';
	print '</div>';
	
	print '<div id="select_more_info" class="mwinfo col-md-6" style="display:none">';
	print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKCLASSIFYAUDIOPROJECT_SELECT_MORE_INFO").'</div>';
	print '</div>';
	
	
	// Circle of doom when loading next sequence/video
	print '<div class="loader invisible"></div>';
}






?>


