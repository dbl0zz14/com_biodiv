<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "SchoolSpotlight template called" );

if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {

	print '<div class="schoolDataBox">';
	
	print '<div id="schoolStatsRow" class="row">';
	
	if ( $this->target->targetFound ) {
		print '<div class="col-md-12 text-center">';
		print $this->translations['to_reach']['translation_text'] . ' ' . $this->target->awardName . ' ' . $this->translations['school_needs']['translation_text'];
		print '</div>';
		
		print '<div class="col-md-12 text-center hugeText">';
		print $this->target->pointsNeeded;
		print '</div>';
		
		print '<div class="col-md-12 text-center">';
		print $this->translations['points']['translation_text'];
		print '</div>';	
	}
	else if ( $this->target->isLatest ) {
		print '<div class="col-md-12 text-center">';
		print $this->translations['school_reached']['translation_text'] . ' ' . $this->target->awardName;
		print '</div>';
		
		print '<div class="col-md-12 text-center">';
		print $this->translations['all_awards']['translation_text'];
		print '</div>';	
	}
	
	print '</div>'; // row

	print '</div>'; // schoolDataBox
}



?>