<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {

	print '<div class="awardsBox">';
	
	print_r ( $this->newAwards );
	
	/*
	print '<div id="awardsRow" class="row">';
	
	print '<div class="col-md-12 text-center">';
	print $this->translations['to_reach']['translation_text'];
	print '</div>';
	
	print '<div class="col-md-12 text-center hugeText">';
	print $this->pointsBreakdown->pointsNeeded;
	print '</div>';
	
	print '<div class="col-md-12 text-center">';
	print $this->translations['points']['translation_text'];
	print '</div>';
	
	print '</div>'; // row
	*/


	print '</div>'; // awardsBox
}



?>