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

	print '<div class="studentCelebrationBox">';
	
	print '<div id="studentCelebrationRow" class="row">';
	
	if ( $this->celebration->type == "badge" ) {
		
		print '<div class="col-md-12 text-center">';
		print $this->translations['you_achieved']['translation_text'];
		print '</div>'; // col-12
		
		print '<div class="col-md-12 text-center bigText">';
		print $this->celebration->badge_name . ' ' . $this->translations['badge']['translation_text'];
		print '</div>'; // col-12
		
		print '<div class="col-md-12 text-center">';
		print $this->translations['contributed']['translation_text'].'<span class="hugeText">'.$this->celebration->points . '</span> ' . $this->translations['points']['translation_text'];;
		print '</div>'; // col-12
		
	}
	else {
	}
	
	
	print '</div>'; // row

	print '</div>'; // schoolDataBox
}



?>