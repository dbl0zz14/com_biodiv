<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLTARGET_LOGIN").'</div>';
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
		print '<div class="h3 panelHeading"><img class="img-responsive targetModuleIcon" src="'.$imgSrc.'"> '.$this->schoolPoints[$targetModule].' '.JText::_("COM_BIODIV_SCHOOLTARGET_POINTS").'</div>';
		
		print '<p>'.JText::_("COM_BIODIV_SCHOOLTARGET_TO_REACH").' '.$this->targetAward->awardName. ' '.JText::_("COM_BIODIV_SCHOOLTARGET_SCHOOL_NEEDS");
		
		print ' <strong>'.$this->targetAward->pointsNeeded.' '.JText::_("COM_BIODIV_SCHOOLTARGET_POINTS").'</strong>';
		
		print '</p>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-12
		print '</div>'; // row
		
		
	}
	else if ( $this->isLatest ) {
		print '<div class="col-md-12 text-center">';
		print JText::_("COM_BIODIV_SCHOOLTARGET_SCHOOL_REACHED") . ' ' . $this->targetAward->awardName;
		print '</div>';
		
		print '<div class="col-md-12 text-center">';
		print JText::_("COM_BIODIV_SCHOOLTARGET_ALL_AWARDS");
		print '</div>';	
	}
	
	print '</div>'; // row

	print '</div>'; // schoolDataBox
}



?>