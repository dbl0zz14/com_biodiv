<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;


if ( $this->sequence == null ) {
	print '  <h2 class="text-center classify_heading">'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_NO_SEQUENCES").'</h2>';
}

else {

	print '  <h1 id="classify_whatsee" class="text-center classify_heading">'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_WHAT_SEE").'</h1>';  

	print '  <h1 id="classify_select" class="text-center classify_heading" style="display:none">'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_SELECT").'</h1>';  

	print '  <h1 id="classify_happy" class="text-center classify_heading" style="display:none">'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_HAPPY").'</h1>';  

	
	// --------------------- Photos or video,  LHS --------------------------

	print '<div class="row">';
	
	print '<div class="col-md-7 col-sm-7">';
	
	print '<div class="classify_panel_left ps-5 pe-2" >';

	print '<div id="media_carousel" class="carousel_lower">';
	
	if ( $this->sequence->getMedia() == "photo" ) {
		// print '<div class="col-md-12">';
		// print '<div class="col-md-12">';

		$this->mediaCarousel->generateMediaCarousel($this->sequence, false);
		
		// print '</div>'; // col-12
		// print '</div>'; // col-12
	}
	else {
		$this->mediaCarousel->generateMediaCarousel($this->sequence);
	}

	print '</div>'; // media_carousel
	print '</div>'; // classify_panel_left
	print '</div>'; // col-7


	
	print '<div class="col-md-5 col-sm-5">';
	print '<div class="classify_panel_right ps-2 pe-5">';



	// --------------------- Filter panel --------------------------

	print '<div id="filter_buttons" class="species_group pt-5">';

	//print '<div class="row">';
	print '<div class="d-flex justify-content-evenly">';

	//print '<div class="col-md-6 col-sm-6 col-xs-6">';
	print '	<button id="classify_mammal" class="btn btn-lg btn-primary h3 control_btn w-25">'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_MAMMAL").'</button>';
	//print '</div>';


	//print '<div class="col-md-6 col-sm-6 col-xs-6">';
	print '	<button id="classify_bird" class="btn btn-lg btn-primary h3 control_btn w-25">'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_BIRD").'</button>';
	//print '</div>';
	
	print '</div>';

	print '<div class="d-flex justify-content-evenly">';

	//print '<div class="col-md-6 col-sm-6 col-xs-6">';
	print '	<button id="species_select_'.$this->nothingId.'" class="btn btn-lg btn-primary h3 control_btn species_select w-25" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_NOTHING").'</button>';
	//print '</div>';


	//print '<div class="col-md-6 col-sm-6 col-xs-6">';
	print '	<button id="species_select_'.$this->humanId.'" class="btn btn-lg btn-primary h3 control_btn species_select w-25" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_HUMAN").'</button>';
	//print '</div>';

	print '</div>';

	print '<div class="d-flex justify-content-evenly">';

	//print '<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">';
	print '	<button id="species_select_'.$this->dkId.'" class="btn btn-lg btn-primary h3 control_btn species_select w-25" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_DK").'</button>';
	//print '</div>';
	
	print '</div>';

	print '</div>'; // filter_buttons


	// --------------------- Common Mammals panel --------------------------


	print '<div id="mammal_buttons" class="species_buttons species_group col-md-12 pt-4" style="display:none">';

	// *********************  CHANGE THIS TO ONLY PRINT FIRST 20 TO ALLOW FOR ERRORS IN LISTS
	$i = 0;
	foreach ( $this->commonMammals as $species ) {
		
		$image = codes_getName($species['id'],'png');
		$imageText = "";
		if ( $image ) {
			$imageURL = Uri::root().$image;
			$imageText = "<img width='100%' src='".$imageURL."' style='object-fit: cover'>";
		}
		
		$isLongSpeciesName = false;
		if ( strlen($species['name']) > 13 ) $isLongSpeciesName = true;
		
		$newRow = ( $i%4 == 0 );
		$endOfRow = ( ($i+1)%4 == 0 );
		
		if ( $newRow ) print '<div class="row">';
					
		print '<div class="col-md-3 col-sm-3 col-xs-3">';
		if ( $isLongSpeciesName ) {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			//print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
	}
	
	print '<div class="d-flex justify-content-evenly">';
	print '	<button id="not_on_mammal_list" class="btn btn-lg btn-block btn-primary h3 control_btn" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_NOTONLIST").'</button>';
	print '	<button class="btn btn-lg btn-block btn-primary h3 control_btn back_to_filter" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_BACK").'</button>';
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
			$imageURL = Uri::root().$image;
			$imageText = "<img width='100%' src='".$imageURL."'>";
		}
		
		$isLongSpeciesName = false;
		if ( strlen($species['name']) > 13 ) $isLongSpeciesName = true;
		
		$newRow = ( $i%4 == 0 );
		$endOfRow =  ( ($i+1)%4 == 0 ) || ( $i+1 == $numSpecies ) ;
		
		if ( $newRow ) print '<div class="row">';
					
		print '<div class="col-md-3 col-sm-3 col-xs-3 all_mammals_species">';
		if ( $isLongSpeciesName ) {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
		
		
	}
	
	// Scroll down
	print '<div class="row"><button id="scroll_down_mammals" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';

	
	//print '<div class="anchor_bottom">';
	
	print '<div class="d-flex justify-content-evenly">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-primary h3 control_btn species_select" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_OTHER").'</button>';
	print '	<button class="btn btn-lg btn-block btn-primary h3 control_btn back_to_filter" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_BACK").'</button>';
	print '</div>';
	
	//print '</div>'; // anchor_bottom

	print '</div>'; // all_mammal_buttons


	// --------------------- Bird panel --------------------------

	print '<div id="bird_buttons" class="species_buttons species_group col-md-12 pt-4" style="display:none">';

	$i = 0;
	foreach ( $this->commonBirds as $species ) {
		
		$image = codes_getName($species['id'],'png');
		$imageText = "";
		if ( $image ) {
			$imageURL = Uri::root().$image;
			$imageText = "<img width='100%' src='".$imageURL."'>";
		}
		
		$isLongSpeciesName = false;
		if ( strlen($species['name']) > 13 ) $isLongSpeciesName = true;
		
		$newRow = ( $i%4 == 0 );
		$endOfRow = ( ($i+1)%4 == 0 );
		
		if ( $newRow ) print '<div class="row">';
					
		print '<div class="col-md-3 col-sm-3 col-xs-3">';
		if ( $isLongSpeciesName ) {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
	}

	print '<div class="d-flex justify-content-evenly">';
	print '	<button id="not_on_bird_list" class="btn btn-lg btn-block btn-primary h3 control_btn" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_NOTONLIST").'</button>';
	print '	<button class="btn btn-lg btn-block btn-primary h3 control_btn back_to_filter" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_BACK").'</button>';
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
					
		print '<div class="col-md-3 col-sm-3 col-xs-3 all_birds_species">';
		if ( $isLongSpeciesName ) {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
		}
		else {
			print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-primary species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
		}
		print '</div>';
		
		if ( $endOfRow ) print '</div>';
					
		$i++;
		
		
	}
	
	// Scroll down
	print '<div class="row"><button id="scroll_down_birds" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';
	
	
	
	
	//print '<div class="anchor_bottom">';
	print '<div class="d-flex justify-content-evenly">';
	print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-primary h3 control_btn species_select" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_OTHER").'</button>';
	print '	<button class="btn btn-lg btn-block btn-primary h3 control_btn back_to_filter" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_BACK").'</button>';
	//print '</div>';
	print '</div>';

	print '</div>'; // all_bird_buttons


	// --------------- Chosen species -------------------------

	print '<div id="chosen_species"  class="species_group pt-5" style="display:none">';

	print "<div id='species_helplet'></div>";

	print '<div class="d-flex justify-content-evenly">';
	print '	<button id="classify_save" class="btn btn-lg btn-block btn-primary h3 control_btn" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_YES_SAVE").'</button>';
	print '	<button class="btn btn-lg btn-block btn-primary h3 back_to_filter control_btn" >'.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_NO_BACK").'</button>';
	print '</div>';

	print '</div>'; // chosen_species


	print '</div>'; // classify_panel_right
	print '</div>'; // col-5 
	
	print '</div>'; // row
	
	
	if ( $this->sequence->getMedia() == "photo" ) {
		
		print '<div id="whatsee_info" class="mwinfo col-md-7 hidden-xs">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_LOOK_THRO").'</div>';
		print '</div>';
		
		print '<div id="whatsee_vid_info" class="mwinfo col-md-7 hidden-xs" style="display:none">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_LOOK_THRO_VID").'</div>';
		print '</div>';
	}
	else {
		print '<div id="whatsee_info" class="mwinfo col-md-7 hidden-xs" style="display:none">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_LOOK_THRO").'</div>';
		print '</div>';
		
		print '<div id="whatsee_vid_info" class="mwinfo col-md-7 hidden-xs">';
		print '<div class="h3"><span class="fa fa-info-circle"></span> '.Text::_("COM_BIODIV_KIOSKCLASSIFYPROJECT_LOOK_THRO_VID").'</div>';
		print '</div>';
	}
	
	// Circle of doom when loading next sequence/video
	print '<div class="loader invisible"></div>';
}






?>


