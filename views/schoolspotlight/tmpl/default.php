<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_LOGIN").'</div>';
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
	
	$teacherStr = '<big><strong>' . JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_NO_TEACHERS") . '</strong></big>';
	$studentStr = '<big><strong>' . JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_NO_STUDENTS") . '</strong></big>';
	foreach ( $this->school->userCount as $userCount ) {
		if ( $userCount->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
			
			if ( $userCount->num_users != 1 ) {
				$pluralise = 's';
			}
			else {
				$pluralise = '';
			}
			$teacherStr = '<big><strong>' . $userCount->num_users . ' ' . JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_TEACHER") . $pluralise . '</strong></big>';
		}
		else if ( $userCount->role_id == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
			
			if ( $userCount->num_users != 1 ) {
				$pluralise = 's';
			}
			else {
				$pluralise = '';
			}
			$studentStr = '<big><strong>' . $userCount->num_users . ' ' . JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_STUDENT") . $pluralise . '</strong></big>';
		}
		
	}
	
	
	print '<div class="col-md-12">'. JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_SCHOOL_HAS") . ' ' .$teacherStr . ' ' . 
			JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_AND") . ' ' . $studentStr .'</div>'; 
	print '<div class="col-md-12">'. JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_WE_SET") .' <big><strong>'.$this->school->numSites . " " . JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_CAM_SITES") . '</strong></big></div>';
	
	
	print '<div class="col-md-12">'. JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_WE_UPLOADED") .' <big><strong>'.$this->school->numUploaded . " " . 
			JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_SEQUENCES") . '</strong></big> ' . JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_AND_CLASS") . 
			' <big><strong>' . $this->school->numClassified .'</strong></big></div>';
	print '<div class="col-md-12">'. JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_SCH_CONTRIB") .' <big><strong>'.$this->school->numResources . "</strong></big> " . JText::_("COM_BIODIV_SCHOOLSPOTLIGHT_RESOURCES") . '</div>';
	
	
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