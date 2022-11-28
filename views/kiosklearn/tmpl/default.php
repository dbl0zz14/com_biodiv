<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


	print '<div class="col-md-12">';
	print '<div class="col-md-12">';

	// ----------------------------------------- Headings ----------------------------------------
	
	print '  <div class="h2 text-center slight_lower"><strong>'.JText::_("COM_BIODIV_KIOSKLEARN_LEARN").'</strong></div>';  
	print '  <div class=" h3 text-center slight_lower">'.JText::_("COM_BIODIV_KIOSKLEARN_CHOOSE_ANIMAL").'</div>';  
	
	
	// ----------------------------------------- Filter buttons -------------------------------------
	
	print '<div class="text-center">';
	
	print '	<button id="common_mammals" class="btn btn-lg btn-success filter_btn" type="submit">'.JText::_("COM_BIODIV_KIOSKLEARN_COMMON_MAMMALS").'</button>';
	print '	<button id="all_mammals" class="btn btn-lg btn-success filter_btn">'.JText::_("COM_BIODIV_KIOSKLEARN_ALL_MAMMALS").'</button>';
	print '	<button id="common_birds" class="btn btn-lg btn-success filter_btn" >'.JText::_("COM_BIODIV_KIOSKLEARN_COMMON_BIRDS").'</button>';
	print '	<button id="all_birds" class="btn btn-lg btn-success filter_btn" >'.JText::_("COM_BIODIV_KIOSKLEARN_ALL_BIRDS").'</button>';
	print '	<button id="all_species" class="btn btn-lg btn-success filter_btn" >'.JText::_("COM_BIODIV_KIOSKLEARN_ALL_SPECIES").'</button>';
	
	print '</div>';
	


	// ----------------------------------------- Species buttons with scrolling ------------------------------
	
	// Scroll up
	print '<div class="row"><button id="scroll_up_species" class="btn btn-lg btn-block scroll_btn" disabled><span class="fa fa-2x fa-chevron-up"></span></button></div>';
	

	$i = 0;
	foreach ( $this->allSpecies as $species ) {
		
		// Only include those with an article id
		if ( $species['article'] > 0 ) {
			
			$speciesId = $species['id'];
			$image = codes_getName($speciesId,'kioskimg');
			$imageText = "";
			$imageURL = "";
			if ( $image ) {
				$imageURL = JURI::root().$image;
				$imageText = "<img width='100%' src='".$imageURL."'>";
			}
			
			$longSpeciesNameClass = '';
			if ( strlen($species['name']) > 13 ) $longSpeciesNameClass = 'long_species_name';
			
			$listClasses = " species";
			if ( in_array ( $speciesId, $this->commonMammalIds ) ) $listClasses .= " common_mammal";
			if ( in_array ( $speciesId, $this->allMammalIds ) ) $listClasses .= " mammal";
			if ( in_array ( $speciesId, $this->commonBirdIds ) ) $listClasses .= " common_bird";
			if ( in_array ( $speciesId, $this->allBirdIds ) ) $listClasses .= " bird";
			
			// Only display 3 rows then scroll button
			if ( $i > 17 ) {
				print '<div class="col-md-2' . $listClasses . '"   style="display:none;">';
			}
			else {
				print '<div class="col-md-2' . $listClasses . '">';
			}
			
			print '	<button id="species_select_'.$speciesId.'" class="btn btn-lg btn-block btn-wrap-text btn-success full-img-btn learn-species-btn " data-toggle="modal" data-target="#learn_species_modal">'.$imageText.'<div class="species_name ' . $longSpeciesNameClass . '">'.$species['name'].'</div></button>';
			print '</div>';
			
						
			$i++;
		}
	}
	
	// Scroll down
	print '<div class="row"><button id="scroll_down_species" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';

	
	print '</div>'; // col-12
	print '</div>'; // col-12

		
	print '<div class="modal" id="learn_species_modal" tabindex="-1" role="dialog" aria-labelledby="speciesArticle" aria-hidden="true">';
	print ' <div class="modal-dialog modal-lg">';
	print '	<div class="modal-content">';
	print '	  <div class="modal-header text-right">';
	
	//print '     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	print '    <button type="button"  class="mwclose h4" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>';
	print '	 </div>'; // modal header
	print '	 <div class="modal-body">';
	print '    <div id="learn_species_helplet"></div>';
	print '  </div>'; // modal body
	print '  <div class="modal-footer">';
	print '    <div class="col-md-4 col-md-offset-8"> <button type="button" class="btn btn-lg btn-block btn-success filter_btn" data-dismiss="modal">'.JText::_("COM_BIODIV_KIOSKLEARN_CLOSE").'</button></div>';
    print '  </div>'; // modal footer
	print ' </div>'; // modal content
	print ' </div>'; // modal dialog
	print '</div>'; // modal 

?>


