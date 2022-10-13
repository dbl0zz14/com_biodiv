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