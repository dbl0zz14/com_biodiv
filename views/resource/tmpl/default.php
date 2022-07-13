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

else if ( $this->resourceId ) {
	
	print '<div class="row">';
	
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("schooldashboard");
		
		print '</div>';
		
		 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
		
		print '</div>';
	}
	
	print '</div>'; // row
	
	print '<div class="row">';
				
	print '<div class="col-md-2 col-sm-4 col-xs-4">';

	error_log ( "Back = " . $this->translations['back']['translation_text'] );
	print '<button class="btn btn-primary backBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . $this->translations['back']['translation_text'];
	print '</button>';
	
	print '</div>'; // col-1

	print '</div>'; // row
				
	print '<div id="displayArea">';
				
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print '<span class="greenHeading">'.$this->translations['heading']['translation_text'].'</span> <small class="hidden-xs hidden-sm">'.$this->translations['subheading']['translation_text'].'</small> ';
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
	
	
	$this->resourceFile->printFull();
	
	print '</div>'; // displayArea

}
else {
	print ('<div class="col-md-12" >'.$this->translations['no_file']['translation_text'].'</div>');
}


JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resource.js", true, true);

?>