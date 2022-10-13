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