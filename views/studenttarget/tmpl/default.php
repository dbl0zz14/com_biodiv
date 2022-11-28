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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_STUDENTTARGET_LOGIN").'</div>';
}
else {

	print '<div class="studentTargetBox">';
	
	print '<div id="studentTargetRow" class="row">';
	
	print '<div class="col-md-12 text-center">';
	print JText::_("COM_BIODIV_STUDENTTARGET_TO_REACH");
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
	print JText::_("COM_BIODIV_STUDENTTARGET_YOU_NEED") . ' ' . '<span class="hugeText">' . $this->target->pointsNeeded . 
			'</span>' . ' ' . JText::_("COM_BIODIV_STUDENTTARGET_MORE_POINTS");
	print '</div>'; // col-12
	
	print '</div>'; // row

	print '</div>'; // schoolDataBox
}



?>