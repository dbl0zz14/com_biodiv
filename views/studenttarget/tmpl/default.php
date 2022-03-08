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

	print '<div class="studentTargetBox">';
	
	print '<div id="studentTargetRow" class="row">';
	
	print '<div class="col-md-12 text-center">';
	print $this->translations['to_reach']['translation_text'];
	print '</div>'; // col-12
	
	print '<div class="col-md-12 text-center bigText">';
	print $this->target->module;
	print '</div>'; // col-12
	
	$awardName = $this->target->awardName;
	if ( $this->target->awardType == "ONE_STAR" ) {
		$awardName = '<i class="fa fa-star"></i>';
	}
	else if ( $this->target->awardType == "TWO_STAR" ) {
		$awardName = '<i class="fa fa-star"></i><i class="fa fa-star"></i>';
	}
	else if ( $this->target->awardType == "THREE_STAR" ) {
		$awardName = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
	}
	print '<div class="col-md-12 text-center bigText">';
	print $awardName . ' ' . $this->target->badgeGroup;
	print '</div>'; // col-12
	
	print '<div class="col-md-12 text-center">';
	print $this->translations['you_need']['translation_text'] . ' ' . '<span class="hugeText">' . $this->target->pointsNeeded . 
			'</span>' . ' ' . $this->translations['more_points']['translation_text'];
	print '</div>'; // col-12
	
	print '</div>'; // row

	print '</div>'; // schoolDataBox
}



?>