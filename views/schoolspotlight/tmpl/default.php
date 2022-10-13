<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
}
else {

	print '<div class="spotlightBox">';
	
	if ( $this->displayName ) {
	
		print '<div class="row">';

		print '<div class="col-md-2">';

		print '<img class="img-responsive" src="'.$this->school->image.'"/>';

		print '</div>'; // col-2

		print '<div class="col-md-10">';

		print '<div class="h3 v_align_middle"><p></p>'.$this->school->name.'</div>';
		
		print '</div>'; // col-10
		
		print '</div>'; // row
	
	}
	
	print '<div id="schoolStatsRow" class="row">';

	//print '<div class="col-md-6">';

	
	//error_log ( "Printing user counts");
	
	$teacherStr = '<big><strong>' . $this->translations['no_teachers']['translation_text'] . '</strong></big>';
	$studentStr = '<big><strong>' . $this->translations['no_students']['translation_text'] . '</strong></big>';
	foreach ( $this->school->userCount as $userCount ) {
		if ( $userCount->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
			
			if ( $userCount->num_users != 1 ) {
				$pluralise = 's';
			}
			else {
				$pluralise = '';
			}
			$teacherStr = '<big><strong>' . $userCount->num_users . ' ' . $this->translations['teacher']['translation_text'] . $pluralise . '</strong></big>';
		}
		else if ( $userCount->role_id == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
			
			if ( $userCount->num_users != 1 ) {
				$pluralise = 's';
			}
			else {
				$pluralise = '';
			}
			$studentStr = '<big><strong>' . $userCount->num_users . ' ' . $this->translations['student']['translation_text'] . $pluralise . '</strong></big>';
		}
		
	}
	
	
	print '<div class="col-md-12">'. $this->translations['school_has']['translation_text'] . ' ' .$teacherStr . ' ' . 
			$this->translations['and']['translation_text'] . ' ' . $studentStr .'</div>'; 
	print '<div class="col-md-12">'. $this->translations['we_set']['translation_text'] .' <big><strong>'.$this->school->numSites . " " . $this->translations['cam_sites']['translation_text'] . '</strong></big></div>';
	
	
	print '<div class="col-md-12">'. $this->translations['we_uploaded']['translation_text'] .' <big><strong>'.$this->school->numUploaded . " " . 
			$this->translations['sequences']['translation_text'] . '</strong></big> ' . $this->translations['and_class']['translation_text'] . 
			' <big><strong>' . $this->school->numClassified .'</strong></big></div>';
	print '<div class="col-md-12">'. $this->translations['sch_contrib']['translation_text'] .' <big><strong>'.$this->school->numResources . "</strong></big> " . $this->translations['resources']['translation_text'] . '</div>';
	
	
	print '</div>'; // row


	print '<div class="row">'; 

	print '<div class="col-md-10 col-md-offset-1">';

	print '<div class="schoolQuote text-center"><i class= "fa fa-quote-left" aria-hidden= "true" ></i> '.$this->school->quote.
			' <i class= "fa fa-quote-right" aria-hidden= "true" ></i></div>';

	print '</div>'; // col-12

	print '</div>'; // row
	
	
	print '</div>'; // spotlightBox
}



?>