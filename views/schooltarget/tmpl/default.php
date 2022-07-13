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
	
	print '<div id="schoolStatsRow">';
	
	if ( $this->targetFound ) {
		
		$targetModule = $this->targetAward->module_id;
		print '<div class="row">';
		print '<div class="col-md-12">';
		print '<div class="panel panel-default darkPanel">';
		print '<div class="panel-body">';
		
		$imgSrc = $this->modules[$targetModule]->white_icon;
		print '<div class="h3 panelHeading"><img class="img-responsive targetModuleIcon" src="'.$imgSrc.'"> '.$this->schoolPoints[$targetModule].' '.$this->translations['points']['translation_text'].'</div>';
		
		print '<p>'.$this->translations['to_reach']['translation_text'].' '.$this->targetAward->awardName. ' '.$this->translations['school_needs']['translation_text'];
		
		print ' <strong>'.$this->targetAward->pointsNeeded.' '.$this->translations['points']['translation_text'].'</strong>';
		
		print '</p>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-12
		print '</div>'; // row
		
		
		
		// print '<p>'.$this->translations['school_has']['translation_text'].'</p>';
			
		// print '<div class="bigText spaced">'.$this->target->totalUserPoints.' '.$this->translations['points']['translation_text'].'</div>';
		
		// print '<p>'.$this->translations['to_reach']['translation_text'].' '.$this->target->awardName. ' '.$this->translations['school_needs']['translation_text'].'</p>';
		
		// print '<div class="bigText spaced">'.$this->target->pointsNeeded.' '.$this->translations['points']['translation_text'].'</div>';
		
		
		// print '<div class="col-md-12 h4 text-center">';
		// print $this->translations['school_has']['translation_text'];
		// print '</div>';
		
		// print '<div class="col-md-12 text-center bigText">';
		// print $this->target->totalUserPoints . ' ' . $this->translations['points']['translation_text'];
		// print '</div>';
		
		// print '<div class="col-md-12 h4 text-center">';
		// print $this->translations['to_reach']['translation_text'] . ' ' . $this->target->awardName . ' ' . $this->translations['school_needs']['translation_text'];
		// print '</div>';
		
		// print '<div class="col-md-12 text-center bigText">';
		// print $this->target->pointsNeeded . ' ' . $this->translations['points']['translation_text'];
		// print '</div>';
		
		
	}
	else if ( $this->isLatest ) {
		print '<div class="col-md-12 text-center">';
		print $this->translations['school_reached']['translation_text'] . ' ' . $this->targetAward->awardName;
		print '</div>';
		
		print '<div class="col-md-12 text-center">';
		print $this->translations['all_awards']['translation_text'];
		print '</div>';	
	}
	
	print '</div>'; // row

	print '</div>'; // schoolDataBox
}



?>