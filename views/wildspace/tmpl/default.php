<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	
	// Please log in button
	print '<a type="button" href="'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div class="col-md-2 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("schoolcommunity");
		
		print '</div>';
		
		print '<div class="col-md-10 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
		//Biodiv\SchoolCommunity::generateBackAndLogout();
	}
	
	


	// ----------------------------------------- Headings ----------------------------------------
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print '<span class="greenHeading">'.$this->translations['heading']['translation_text'].'</span> <small class="hidden-xs">'.$this->translations['subheading']['translation_text'].'</small>';
	print '</div>'; // col-10
	print '<div class="col-md-2 col-sm-2 col-xs-2 text-right">';
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	print '</div>'; // col-2
	print '</div>'; // row
	print '</h2>'; 
	
	print '<a href="'.$this->translations['badges_link']['translation_text'].'" class="btn btn-primary" >'.$this->translations['badges_page']['translation_text'].'</button>'.'</a>';


	// // ----------------------------------------- Species buttons  ------------------------------
	
	// // Scroll up
	// print '<div class="row"><button id="scroll_up_species" class="btn btn-lg btn-block scroll_btn" disabled><span class="fa fa-2x fa-chevron-up"></span></button></div>';
	
	print '<div class="row speciesRow">';
	
	$i = 0;
	foreach ( $this->allSpecies as $species ) {
		
		print '<div class="col-md-2 col-sm-3 col-xs-6">';
		
		$longSpeciesNameClass = '';
		if ( strlen($species->name) > 13 ) $longSpeciesNameClass = 'long_species_name';
		
		$imageText = "";
		$imageURL = "";
		if ( $species->image ) {
			$imageURL = JURI::root().$species->image;
			$imageText = "<img width='100%' src='".$imageURL."' alt='image of ".$species->name."'>";
		}
		print '	<button id="species_btn_'.$species->task_id.'" class="btn btn-lg btn-block btn-wrap-text btn-default imageBtn species_btn learn_species_btn" data-toggle="modal" data-target="#species_modal">'.$imageText.'<div class="species_name ' . $longSpeciesNameClass . '">'.$species->name.'</div></button>';
			
		
		print '</div>'; // col-2
		
	}
	
	print '</div>'; // row

	print '</div>'; // col-10 or 12
	
	print '</div>'; // row
}
		
print '<div class="modal" id="species_modal" tabindex="-1" role="dialog" aria-labelledby="speciesArticle" aria-hidden="true">';
print ' <div class="modal-dialog modal-lg">';
print '	<div class="modal-content">';
print '	  <div class="modal-header text-right">';

print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '	 </div>'; // modal header
print '	 <div class="modal-body">';
print '    <div id="species_article"></div>';
print '  </div>'; // modal body
print '  <div class="modal-footer">';
print '    <div class="col-md-4 col-md-offset-8"> <button type="button" class="btn btn-primary" data-dismiss="modal">'.$this->translations['close']['translation_text'].'</button></div>';
print '  </div>'; // modal footer
print ' </div>'; // modal content
print ' </div>'; // modal dialog
print '</div>'; // modal 


print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title">'.$this->translations['review']['translation_text'].'</h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';

JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/wildspace.js", true, true);

?>


