<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

print '<div class="row">';

//print '<div class="d-flex justify-content-evenly">';

$isFirst = true;
		
foreach ($this->contentList as $r) {
	
	if ( $isFirst ) {
		
		print '<div class="col-md-12">';
		print '<div class="text-center ms-5 me-5 ps-5 pe-5">';
		print $r->introtext;
		print '</div>';
		print '</div>'; // col
		print '</div>'; // row
		//print '<div class="d-flex justify-content-evenly ps-4 pe-4">';
		print '<div class="row ps-4 pe-4">';
		$isFirst = false;
	}
	else {
		//print '<div class="ps-4 pe-4">';
		print '<div class="col-md-4 ps-4 pe-4">';
		print $r->introtext;
		print '</div>';
	}
	
} 

print '</div>'; //row


?>


