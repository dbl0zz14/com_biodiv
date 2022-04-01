<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "StudentProgress template called" );


if ( !$this->personId ) {
	// Please log in button
	print '<a type="button" href="'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {
	
	print '<div class="row">';

	
	foreach ( $this->students as $studentId=>$student ) {
		
		print '<div class="col-md-3 col-sm-4 col-xs-6">';
		print '<div class="panel">';
		print '<div class="panel-body">';
		print '<div class="row">';
		print '<div class="col-md-5 col-sm-5 col-xs-5">';
		print '<img class="img-responsive avatar" src="'.$student->avatar.'" />';
		print '</div>'; // col-5
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print '<div>'.$student->name.'</div>';
		print '<div>'.$student->username.'</div>';
		print '<div>'.$student->totalPoints.'</div>';
		print '</div>'; // col-7
		print '</div>'; // row
		
		foreach ( $student->progress as $badgeGroup=>$numPoints ) {
			
			$available = $this->availablePoints[$badgeGroup];
			$width = round(100*$numPoints/$available);
			
			
			
			print '<div class="row">';
		
			print '<div class="col-md-5 col-sm-5 col-xs-5 text-right">';
		
			print '<div>'.$this->badgeGroups[$badgeGroup].'</div>';
			
			print '</div>'; // col-5
		
			print '<div class="col-md-7 col-sm-7 col-xs-7">';
		
			print '<div class="progress studentProgress" >';
			print '<div class="progress-bar" role="progressbar" aria-valuenow="'.$numPoints.'" aria-valuemin="2" aria-valuemax="'.$available.'" style="width:'.$width.'%; background-color:'.$this->badgeGroupColour[$badgeGroup].'">'.$numPoints.'</div>';
			print '</div>';
			
			print '</div>'; // col-7
			
			print '</div>'; // row
		}
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</div>'; // col-3
	}

	print '</div>'; // col-12

	print '</div>'; // row

}

?>