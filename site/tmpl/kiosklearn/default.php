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
 

	// ----------------------------------------- Headings ----------------------------------------
	
	print '  <div class="h2 text-center slight_lower"><strong>'.Text::_("COM_BIODIV_KIOSKLEARN_LEARN").'</strong></div>';  
	print '  <div class=" h3 text-center slight_lower">'.Text::_("COM_BIODIV_KIOSKLEARN_CHOOSE_ANIMAL").'</div>';  
	
	
	// ----------------------------------------- Filter buttons -------------------------------------
	
	print '<div class="text-center">';
	
	print '	<button id="common_mammals" class="btn btn-lg btn-primary filter_btn" type="submit">'.Text::_("COM_BIODIV_KIOSKLEARN_COMMON_MAMMALS").'</button>';
	print '	<button id="all_mammals" class="btn btn-lg btn-primary filter_btn">'.Text::_("COM_BIODIV_KIOSKLEARN_ALL_MAMMALS").'</button>';
	print '	<button id="common_birds" class="btn btn-lg btn-primary filter_btn" >'.Text::_("COM_BIODIV_KIOSKLEARN_COMMON_BIRDS").'</button>';
	print '	<button id="all_birds" class="btn btn-lg btn-primary filter_btn" >'.Text::_("COM_BIODIV_KIOSKLEARN_ALL_BIRDS").'</button>';
	print '	<button id="all_species" class="btn btn-lg btn-primary filter_btn" >'.Text::_("COM_BIODIV_KIOSKLEARN_ALL_SPECIES").'</button>';
	
	print '</div>';
	


	// ----------------------------------------- Species buttons with scrolling ------------------------------
	
	// Scroll up
	print '<div class="row"><button id="scroll_up_species" class="btn btn-lg btn-block scroll_btn" disabled><span class="fa fa-2x fa-chevron-up"></span></button></div>';
	
	print '<div class="row d-flex align-items-stretch ps-5 pe-5">';
	$i = 0;
	foreach ( $this->allSpecies as $species ) {
		
		// Only include those with an article id
		if ( $species['article'] > 0 ) {
			
			$speciesId = $species['id'];
			$image = codes_getName($speciesId,'kioskimg');
			$imageText = "";
			$imageURL = "";
			if ( $image ) {
				$imageURL = Uri::root().$image;
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
			
			print '	<button id="species_select_'.$speciesId.'" class="btn btn-lg btn-block btn-wrap-text btn-success full-img-btn learn-species-btn mt-1 mb-3" data-bs-toggle="modal" data-bs-target="#test_modal">'.$imageText.'<div class="species_name ' . $longSpeciesNameClass . '">'.$species['name'].'</div></button>';
			print '</div>';
			
						
			$i++;
		}
	}
	print '</div>';
	
	// Scroll down
	print '<div class="row"><button id="scroll_down_species" class="btn btn-lg btn-block scroll_btn"><span class="fa fa-2x fa-chevron-down"></span></button></div>';
	
	//print '<div id="learn_species_modal" class="modal fade hide" data-bs-backdrop="false" tabindex="-1" >';
	print '<div class="modal" id="learn_species_modal" tabindex="-1" aria-labelledby="speciesArticle" >';
	print ' <div class="modal-dialog modal-lg modal-dialog-scrollable">';
	print '	<div class="modal-content">';
	print '	  <div class="modal-header">';
	print '      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
	print '	 </div>'; // modal header
	print '	 <div class="modal-body">';
	print '    <div id="learn_species_helplet" class="m-4"></div>';
	print '  </div>'; // modal body
	print '  <div class="modal-footer">';
	print '    <button type="button" class="btn btn-primary btn-lg " data-bs-dismiss="modal">'.Text::_("COM_BIODIV_KIOSKLEARN_CLOSE").'</button>';
	print '  </div>'; // modal footer
	print ' </div>'; // modal content
	print ' </div>'; // modal dialog
	print '</div>'; // modal 

?>


