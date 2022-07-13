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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
	
}

else {
	
	print '<div class="row">';

	// $errMsg = print_r ( $this->students, true );
	// error_log ( "studentProgress: " . $errMsg );
		
	// $errMsg = print_r ( $this->badgeGroups, true );
	// error_log ( "badgeGroups: " . $errMsg );
	
	// $errMsg = print_r ( $this->availablePoints, true );
	// error_log ( "availablePoints: " . $errMsg );
		
	foreach ( $this->students as $studentId=>$student ) {
		
		print '<div class="col-md-4 col-sm-6 col-xs-12">';
		print '<div class="panel">';
		print '<div class="panel-body">';
		print '<div class="row">';
		print '<div class="col-md-5 col-sm-5 col-xs-5">';
		print '<img class="img-responsive avatar progressAvatar" src="'.$student->avatar.'" />';
		print '</div>'; // col-5
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print '<div>'.$student->name.'</div>';
		print '<div>'.$student->username.'</div>';
		print '<div>'.$student->grandTotal.' '.$this->translations['points']['translation_text'].'</div>';
		print '</div>'; // col-7
		print '</div>'; // row
		
		print '<div class="row small-gutter">';
				
		print '<div class="col-md-4 col-sm-4 col-xs-4">';	
		print '</div>';
		
		foreach ( $this->modules as $module ) {
			$moduleId = $module->module_id;
			$moduleImg = $module->icon;
			$imgClass = "moduleIcon" . $module->name;
			print '<div class="col-md-2 col-sm-2 col-xs-2">';	
			print '<img src="'.$moduleImg.'" class="'.$imgClass.'">';
			print '</div>';
		}
		
			
		print '</div>'; // row
	
	
		//foreach ( $student->progress as $badgeGroup=>$numPoints ) {
		//foreach ( $student->progress as $badgeGroup=>$studentPoints ) {
		foreach ( $this->badgeGroups as $badgeGroup=>$badgeGroupName ) {
			
			print '<div class="row small-gutter">';
				
			print '<div class="col-md-4 col-sm-4 col-xs-4 text-left">';
	
			print '<div>'.$this->badgeGroups[$badgeGroup].' </div>';
			
			print '</div>'; // col-4
			
			$studentPoints = null;
			
			if ( array_key_exists ($badgeGroup, $student->progress) ) {
				$studentPoints = $student->progress[$badgeGroup];
			}
			
			//foreach ($studentPoints as $moduleId=>$numPoints ) {
			foreach ( $this->moduleIds as $moduleId ) {
				
				$available = 0;
				if ( array_key_exists($badgeGroup, $this->availablePoints) && array_key_exists($moduleId, $this->availablePoints[$badgeGroup]) ) {
					$available = $this->availablePoints[$badgeGroup][$moduleId];
				}
				
				$numPoints = 0;
				if ( $studentPoints && array_key_exists($moduleId, $studentPoints) ) {
					$numPoints = $studentPoints[$moduleId];
				}
				if ( $available > 0 ) {
					$width = round(100*$numPoints/$available);
				}
				else {
					$width = 0;
				}
		
				print '<div class="col-md-2 col-sm-2 col-xs-2">';
	
				print '<div class="progress studentProgress" >';
				print '<div class="progress-bar" role="progressbar" aria-valuenow="'.$numPoints.'" aria-valuemin="2" aria-valuemax="'.$available.'" style="width:'.$width.'%; background-color:'.$this->badgeGroupColour[$badgeGroup].'">'.$numPoints.'</div>';
				print '</div>';
				
				print '</div>'; // col-2
				
			}
			
			print '</div>'; // row
			
			
		
		}
		print '<div class="row small-gutter">';
				
		print '<div class="col-md-4 col-sm-4 col-xs-4">';
		print $this->translations['total']['translation_text'];		
		print '</div>';
		
		foreach ( $this->moduleIds as $moduleId ) {
			print '<div class="col-md-2 col-sm-2 col-xs-2">';
			$totalForModule = 0;			
			if ( array_key_exists ( $moduleId, $student->totalPoints ) ) {
				$totalForModule = $student->totalPoints[$moduleId];
			}
			print $totalForModule;
			print '</div>';
		}
		
			
		print '</div>'; // row
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</div>'; // col-3
	}

	print '</div>'; // col-12

	print '</div>'; // row

}

?>