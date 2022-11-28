<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_LOGIN").'</div>';
}
else if ( !$this->schoolId ) {
	print '<h2>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_NO_SCHOOL").'</h2>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_WELCOME").'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_YOU_ARE").'</h2>';

	
	if ( ($this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE) or ($this->mySchoolRole == Biodiv\SchoolCommunity::ECOLOGIST_ROLE) ) {

		print '<h5 class="text-center">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_TEXT").'</h5>';
			
		print '<div class="text-center"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_LINK").'" target="_blank" rel="noopener noreferrer" class="btn btn-primary btnInSpace">'.
			JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_BTN").'</a></div>';
	}
	
	
	print '<div id="avatarArea">';
	
	print '<h3 class="text-center bigSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CHOOSE_AVATAR").'</h3>';
	
	//print '<div class="row">';
	
	$isFirst = true;
	$avatarCount = 0;
	foreach ( $this->avatars as $avatarId=>$avatar ) {
		
		$activeClass="";
		if ( $isFirst ) {
			$activeClass="active";
			$isFirst = false;
		}
		if ( $avatarCount%6 == 0 ) {
			print '<div class="row">';
		}
		print '<div class="col-md-2 text-center">';
		
		print '<button id="avatarBtn_'.$avatarId.'" class="avatarBtn '.$activeClass.'"><img src="'.$avatar->image.'" class="img-responsive" alt="'.$avatar->name.' avatar" /></button>';
		print '<h3>'.$avatar->name.'</h3>';
		
		print '</div>';
		
		$avatarCount += 1;
		
		if ( $avatarCount%6 == 0 ) {
			print '</div>'; // row
		}
	}
	if ( $avatarCount%6 != 0 ) {
		print '</div>'; // row
	}
	
	print '<button id="saveAvatar" class="btn btn-primary btn-lg spaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SAVE_AVATAR").'</button>';
	
	//print '</div>'; // row
	print '</div>'; // avatarArea
	
	if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_DASH_PAGE").'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_DASHBOARD").'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_STUDENT_DASH").'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_DASHBOARD").'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_ECOL_DASH").'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_DASHBOARD").'</button></a></div>';
	}

		
	print '</div>'; // col-12
	print '</div>'; // row
}
else {
	
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("schooldashboard");
		
		print '</div>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		//Biodiv\SchoolCommunity::generateBackAndLogout();
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
	}
	
	// -------------------------------  School name and total
	
	print '<div id="displayArea">';
	
	if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		$heading = JText::_("COM_BIODIV_SCHOOLDASHBOARD_WELCOME");
		$subheading = "";
	}
	else {
		$heading = JText::_("COM_BIODIV_SCHOOLDASHBOARD_HEADING");
		$subheading = JText::_("COM_BIODIV_SCHOOLDASHBOARD_SUBHEADING");
	}
	
	//print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-8 col-sm-6 col-xs-6 h2">';
	print '<span class="greenHeading">'.$heading.'</span> <small class="hidden-xs hidden-sm">'.$subheading.'</small>';
	print '</div>'; // col-8
	
	print '<div class="col-md-3 col-sm-5 col-xs-6 text-right">';
	if ( $this->kioskUrl ) {
		
		print '<a href="'.$this->kioskUrl.'" >';
		print '<button class="btn btn-success projectBtn">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TO_PROJECT").'</button>';
		print '</a>';
	}
	print '</div>'; // col-3
	print '<div class="col-md-1 col-sm-1 col-xs-12 text-right">';
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-lg fa-info"></i>';
		print '</div>'; // helpButton
	}
	print '</div>'; // col-2
	print '</div>'; // row
	
	
	// ----------------------------- Pillar progress plus target stuff
	
	print '<div class="row">';
	
	print '<div class="col-md-8">';
	
	print '<div class="panel panel-default schoolProgress">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	print '<div class="col-md-7">';
	
	print '<h3 class="panelHeading">'.$this->schoolName.'</h3>';
	print '<table class="table table-condensed">';
	
	print '<thead>';
	print '<tr class="schoolGroupData">';
		
	print '<th></th>';
	print '<th></th>';
	foreach ( $this->modules as $module ) {

		print '<th class="text-center"><img class="img-responsive moduleIcon'.$module->name.'" src="'.$module->icon.'"></th>';
	}
	
	print '</tr>';
	print '</thead>';
	print '<tbody>';
		
	foreach ( $this->badgeGroups as $badgeGroup ) {
		
		$groupId = $badgeGroup[0];
		$groupName = $badgeGroup[1];
		$icon = $this->badgeIcons[$groupId];
		
	
		print '<tr id="schoolGroupData_'. $groupId .'" class="schoolGroupData">';
		
		print '<td><img src="'.$icon.'" class="img-responsive tableGroupIcon" alt="'.$groupName. ' icon" /></td>';
		print '<td>'.$groupName.'</td>';
		
		foreach ( $this->moduleIds as $moduleId ) {
			$totalNumPoints = $this->badgeGroupSummary[$moduleId][$groupId]->school->weightedPoints;

			print '<td class="text-center">'.$totalNumPoints.'</td>';
		}
		
		print '</tr>';
	}
	
	print '<tr class="schoolGroupData">';
		
	print '<td></td>';
	print '<td>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TOTAL").'</td>';
	
	foreach ( $this->moduleIds as $moduleId ) {
		
		if ( array_key_exists ( $moduleId, $this->schoolPoints ) ) {
			print '<td class="text-center">'.$this->schoolPoints[$moduleId].'</td>';
		}
		else {
			print '<td></td>';
		}

	}
	
	print '<tr class="schoolGroupData">';
		
	print '<td></td>';
	print '<td>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_AWARDS").'</td>';
	
	foreach ( $this->moduleIds as $moduleId ) {
		
		if ( array_key_exists ( $moduleId, $this->moduleAwards ) ) {
			$awardType = $this->moduleAwards[$moduleId]->awardType;
			print '<td class="text-center">'.$this->moduleAwardIcons[$awardType].'</td>';
		}
		else {
			print '<td></td>';
		}

	}
	
	print '</tr>';
	
	print '</tbody>';
	print '</table>';
	
	print '</div>'; // col-7
	
	print '<div class="col-md-5">';
	
	
	// ---------------------------------- School target
	
	if ( $this->newAward ) {
		
		$awardType = $this->newAward->awardType;
		print '<div class="row">';
		print '<div class="col-md-12">';
		//print '<div class="panel panel-default coloredPanel">';
		print '<div class="panel panel-default yellowPanel ">';
		print '<div class="panel-body">';
	
		print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CONGRATS").'</h3>';
		
		print '<div class="row">';
		
		print '<div class="col-md-7">';
		print '<p class="spaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SCHOOL_REACHED").' '.$this->newAward->awardName. '</p>';
		print '</div>'; // col-7
		
		print '<div class="col-md-5">';
		print '<div class="spaced text-center">'.$this->awardIcons[$awardType].'</div>';
		print '</div>'; // col-5
		
		print '</div>'; // row
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-12
		print '</div>'; // row
	}
	else if ( $this->existingAward ) {
		$awardType = $this->existingAward->awardType;
		print '<div class="row">';
		print '<div class="col-md-12">';
		//print '<div class="panel panel-default coloredPanel">';
		print '<div class="panel panel-default yellowPanel">';
		print '<div class="panel-body">';
	
		print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CONGRATS").'</h3>';
		
		print '<div class="row">';
		
		print '<div class="col-md-7">';
		print '<p class="spaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SCHOOL_REACHED").' '.$this->existingAward->awardName. '</p>';
		print '</div>'; // col-7
		
		print '<div class="col-md-5">';
		print '<div class="spaced text-center">'.$this->awardIcons[$awardType].'</div>';
		print '</div>'; // col-5
		
		print '</div>'; // row
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-12
		print '</div>'; // row
	}
	
	if ( $this->targetAward ) {
		$targetModule = $this->targetAward->module_id;
		print '<div class="row">';
		print '<div class="col-md-12">';
		print '<div class="panel panel-default darkPanel">';
		print '<div class="panel-body">';
		
		$imgSrc = $this->modules[$targetModule]->white_icon;
		print '<div class="h3 panelHeading"><img class="img-responsive targetModuleIcon" src="'.$imgSrc.'"> '.$this->schoolPoints[$targetModule].' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POINTS").'</div>';
		
		print '<p>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TO_REACH").' '.$this->targetAward->awardName. ' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SCHOOL_NEEDS");
		
		print ' <strong>'.$this->targetAward->pointsNeeded.' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POINTS").'</strong>';
		
		print ' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_YOU_HELP").'</p>';
		
		if ( Biodiv\SchoolCommunity::isStudent() ) {
			print '<div class="text-center"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_BADGES_LINK").'" >';
		}
		else {
			print '<div class="text-center"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_ACTIVITY_LINK").'" >';
		}
	
		print '<button class="btn btn-default btn-lg">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_HELP_GET").'</button>';
		
		print '</a></div>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-12
		print '</div>'; // row
	}
	
	
	
	print '</div>'; // col-5
	
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
		
	print '</div>'; // col-8
	
		
	
	// ------------------- RHS event log
	
	print '<div class="col-md-4">';
	
	print '<div class="panel panel-default eventFeed">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12 h3 panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_EVENTS_HEADING").'</div>';
	
	print '</div>';

	
	print '<div id="eventLog"></div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // col-4
	
	print '</div>'; // row
	
	print '</div>'; // col-10 or 12
	
	print '</div>'; // row

}

print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';


JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schooldashboard.js", true, true);


?>





