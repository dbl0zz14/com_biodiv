<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	error_log ("No personId in KioskQuizStandard");
	
	print '  <h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_NO_USER").'</h2>';
}
else {
	
	$seq_json = json_encode($this->sequenceIds);

	//error_log ( "seq_json = " . $seq_json );

	print "<div id='seq_ids' data-seq-ids='".$seq_json."'></div>";
	print "<div id='topic_id' data-topic-id='".$this->topicId."'></div>";

		

	if ( count($this->sequences) == 0 ) {
		print '  <h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_NO_SEQUENCES").'</h2>';
	}
	else {

		print '  <h1 id="quiz_whatsee" class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_WHAT_SEE").'</h1>';  

		print '  <h1 id="quiz_select" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_SELECT").'</h1>';  

		print '  <h1 id="quiz_happy" class="text-center classify_heading" style="display:none">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_HAPPY").'</h1>';  

		
		
		
		// --------------------- Photos or video,  LHS --------------------------

		
		print '<div id="quiz_lhs" class="col-md-7 col-sm-7">';
		
		
		print '<div class="classify_panel_left" >';


		print '<div id="media_carousel" class="carousel_lower" >';

		$seq = $this->sequences[0];
		
		// Add extra col to give space for controls outside photos
		if ( $seq->getMedia() == "photo" ) {
		
			print '<div class="col-md-12">';
			print '<div class="col-md-12">';
			
			$this->mediaCarousel->generateMediaCarousel($seq);

			print '</div>'; // col-12
			print '</div>'; // col-12
		}
		else {
			
			$this->mediaCarousel->generateMediaCarousel($seq);
			
		}

		print '</div>'; // media carousel
					
		print '</div>'; // classify_panel_left
		
		print '</div>'; // col-7


		
		// --------------------- Species choices and correct species display, RHS --------------------------

		
		print '<div id="quiz_rhs" class="col-md-5 col-sm-5">';
		
		print '<div class="classify_panel_right">';
		
		
		// --------------------- Filter panel --------------------------

		print '<div id="filter_buttons" class="species_group">';

		if ( $this->commonMammals ) {
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '	<button id="classify_mammal" class="btn btn-lg btn-block btn-success h3 control_btn">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_MAMMAL").'</button>';
			print '</div>';
		}


		if ( $this->commonBirds ) {
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '	<button id="classify_bird" class="btn btn-lg btn-block btn-success h3 control_btn">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_BIRD").'</button>';
			print '</div>';
		}


		if ( $this->commonInverts ) {
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '	<button id="classify_invert" class="btn btn-lg btn-block btn-success h3 control_btn">'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_INVERT").'</button>';
			print '</div>';
		}


		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button id="species_select_'.$this->nothingId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_NOTHING").'</button>';
		print '</div>';


		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button id="species_select_'.$this->humanId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_HUMAN").'</button>';
		print '</div>';


		if ( $this->speciesButtonCount == 2 ) {
			print '<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">';
		}
		else {
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
		}
		print '	<button id="species_select_'.$this->dkId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_DK").'</button>';
		print '</div>';

		print '</div>'; // filter_buttons


		// --------------------- Common Mammals panel --------------------------

		print '<div id="mammal_buttons" class="species_buttons species_group col-md-12" style="display:none">';

		// *********************  CHANGE THIS TO ONLY PRINT FIRST 20 TO ALLOW FOR ERRORS IN LISTS
		if ( $this->commonMammals ) {
			
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
							
				print '<div class="col-md-3 col-sm-3 col-xs-3">';
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
		}
		
		if ( $this->allMammals ) {
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '	<button id="not_on_mammal_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_NOTONLIST").'</button>';
			print '</div>';
		}

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_BACK").'</button>';
		print '</div>';

		print '</div>'; // mammal_buttons


		// --------------------- All Mammals panel --------------------------

		print '<div id="all_mammal_buttons" class="species_buttons species_group col-md-12" style="display:none">';
		
		// Scroll up
		print '<div class="row"><button id="scroll_up_mammals" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-up"></span></button></div>';
		
		if ( $this->allMammals ) {
			
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
							
				print '<div class="col-md-3 col-sm-3 col-xs-3 all_mammals_species">';
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
		}
		
		// Scroll down
		print '<div class="row"><button id="scroll_down_mammals" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';

		
		print '<div class="anchor_bottom">';
		
		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_OTHER").'</button>';
		print '</div>';

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_BACK").'</button>';
		print '</div>';

		print '</div>'; // anchor_bottom

		print '</div>'; // all_mammal_buttons


		// --------------------- Bird panel --------------------------

		print '<div id="bird_buttons" class="species_buttons species_group col-md-12" style="display:none">';

		if ( $this->commonBirds ) {
			
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
							
				print '<div class="col-md-3 col-sm-3 col-xs-3">';
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
		}

		if ( $this->allBirds ) {
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '	<button id="not_on_bird_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_NOTONLIST").'</button>';
			print '</div>';
		}

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_BACK").'</button>';
		print '</div>';


		print '</div>'; // bird_buttons


		// --------------------- All Birds panel --------------------------

		print '<div id="all_bird_buttons" class="species_buttons species_group col-md-12" style="display:none">';
		
		
		// Scroll up
		print '<div class="row"><button id="scroll_up_birds" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-up"></span></button></div>';
		
		if ( $this->allBirds ) {
			
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
					print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
				}
				else {
					print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
				}
				print '</div>';
				
				if ( $endOfRow ) print '</div>';
							
				$i++;
			}
		}
		
		// Scroll down
		print '<div class="row"><button id="scroll_down_birds" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';
		
		
		
		print '<div class="anchor_bottom">';

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_OTHER").'</button>';
		print '</div>';

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_BACK").'</button>';
		print '</div>';

		print '</div>'; // anchor_bottom
		
		print '</div>'; // all_bird_buttons
		
		
		// --------------------- Invert panel --------------------------
		
		print '<div id="invert_buttons" class="species_buttons species_group col-md-12" style="display:none">';

		if ( $this->commonInverts ) {

			$i = 0;
			$numCommon = count($this->commonInverts);
			foreach ( $this->commonInverts as $species ) {
				
				$image = codes_getName($species['id'],'png');
				$imageText = "";
				if ( $image ) {
					$imageURL = JURI::root().$image;
					$imageText = "<img width='100%' src='".$imageURL."'>";
				}
				
				$isLongSpeciesName = false;
				if ( strlen($species['name']) > 13 ) $isLongSpeciesName = true;
				
				$newRow = ( $i%4 == 0 );
				$endOfRow = ( (($i+1)%4 == 0) or (($i+1) == $numCommon ) );
				
				if ( $newRow ) print '<div class="row">';
							
				print '<div class="col-md-3 col-sm-3 col-xs-3">';
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
		}

		if ( $this->allInverts ) {
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '	<button id="not_on_invert_list" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_NOTONLIST").'</button>';
			print '</div>';
		}

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_BACK").'</button>';
		print '</div>';


		print '</div>'; // invert_buttons


		// --------------------- All Inverts panel --------------------------

		print '<div id="all_invert_buttons" class="species_buttons species_group col-md-12" style="display:none">';
		
		
		// Scroll up
		print '<div class="row"><button id="scroll_up_inverts" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-up"></span></button></div>';
		
		if ( $this->allInverts ) {
			$i = 0;
			$numSpecies = count($this->allInverts);
			
			foreach ( $this->allInverts as $species ) {
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
					print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name long_species_name">'.$species['name'].'</div></button>';
				}
				else {
					print '	<button id="species_select_'.$species['id'].'" class="btn btn-lg btn-block btn-wrap-text btn-success species_select species_button full-img-btn">'.$imageText.'<div class="species_name">'.$species['name'].'</div></button>';
				}
				print '</div>';
				
				if ( $endOfRow ) print '</div>';
							
				$i++;
				
				
			}
		}
		
		// Scroll down
		print '<div class="row"><button id="scroll_down_inverts" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';
		
		
		
		//print '<div class="anchor_bottom">';
		print '<div class="anchor_bottom">';

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button id="species_select_'.$this->otherId.'" class="btn btn-lg btn-block btn-success h3 control_btn species_select" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_OTHER").'</button>';
		print '</div>';

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_filter" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_BACK").'</button>';
		print '</div>';

		print '</div>'; // anchor_bottom
		
		print '</div>'; // all_invert_buttons





		
		// --------------- Chosen species -------------------------

		print '<div id="chosen_species"  class="species_group" style="display:none">';

		print "<div class='well'><div id='species_helplet'></div></div>";

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button id="classify_save" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_YES_SAVE").'</button>';
		print '</div>';

		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '	<button class="btn btn-lg btn-block btn-success h3 back_to_filter control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_NO_BACK").'</button>';
		print '</div>';


		print '</div>'; // chosen_species


		print '</div>'; // classify_panel_right
		print '</div>'; // col-5 
		
		
		// -------------------------------- Info panel ---------------------
		
		if ( $seq->getMedia() == "photo" ) {
			
			print '<div id="whatsee_info" class="mwinfo col-md-7 hidden-xs">';
			print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_LOOK_THRO").'</div>';
			print '</div>';
			
			print '<div id="whatsee_vid_info" class="mwinfo col-md-7 hidden-xs" style="display:none">';
			print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_LOOK_THRO_VID").'</div>';
			print '</div>';
		}
		else {
			print '<div id="whatsee_info" class="mwinfo col-md-7 hidden-xs" style="display:none">';
			print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_LOOK_THRO").'</div>';
			print '</div>';
			
			print '<div id="whatsee_vid_info" class="mwinfo col-md-7 hidden-xs">';
			print '<div class="h3"><span class="fa fa-info-circle"></span> '.JText::_("COM_BIODIV_KIOSKQUIZSTANDARD_LOOK_THRO_VID").'</div>';
			print '</div>';
		}
		
	}
	
		
}






?>


