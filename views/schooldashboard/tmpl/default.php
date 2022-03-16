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
else if ( !$this->schoolId ) {
	print '<h2>'.$this->translations['no_school']['translation_text'].'</h2>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.$this->translations['welcome']['translation_text'].'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.$this->translations['you_are']['translation_text'].'</h2>';

	
	if ( ($this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE) or ($this->mySchoolRole == Biodiv\SchoolCommunity::ECOLOGIST_ROLE) ) {

		print '<h5 class="text-center">'.$this->translations['policies_text']['translation_text'].'</h5>';
			
		print '<div class="text-center"><a href="'.$this->translations['policies_link']['translation_text'].'" target="_blank" rel="noopener noreferrer" class="btn btn-primary btnInSpace">'.
			$this->translations['policies_btn']['translation_text'].'</a></div>';
	}
	
	
	print '<div id="avatarArea">';
	
	print '<h3 class="text-center bigSpaced">'.$this->translations['choose_avatar']['translation_text'].'</h3>';
	
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
	
	print '<button id="saveAvatar" class="btn btn-primary btn-lg spaced">'.$this->translations['save_avatar']['translation_text'].'</button>';
	
	//print '</div>'; // row
	print '</div>'; // avatarArea
	
	if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['dash_page']['translation_text'].'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['student_dash']['translation_text'].'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['ecol_dash']['translation_text'].'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
	}

		
	print '</div>'; // col-12
	print '</div>'; // row
}
else {
	
	
	// //print_r ( $this->badgeGroups );
	// $param = new StdClass();
	// $param->points = $this->myTotalPoints;
	// $param->slogan = "Awesome Observer";
	// $param->school = $this->schoolName;
	// $param->username = Biodiv\SchoolCommunity::getUsername();
	// //$param->avatar = "http://localhost/rhombus/images/Projects/BES/avatars/dance-147030_640.png";
	// $param->avatar = $this->schoolUser->avatar;
	
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		// print '<div class="col-md-2 col-sm-12 col-xs-12">'; 
	
		// Biodiv\SchoolCommunity::generateNav("schooldashboard");
		
		// print '</div>';
		
		// print '<div class="col-md-10 col-sm-12 col-xs-12">'; 
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("schooldashboard");
		
		print '</div>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateBackAndLogout();
	}
	
	// -------------------------------  School name and total
	
	print '<div id="displayArea">';
	
	if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<h1 class="text-center teacherWelcome"  >'.$this->translations['welcome']['translation_text'].'</h1>';
	}
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print $this->translations['heading']['translation_text'].' <small class="hidden-xs">'.$this->translations['subheading']['translation_text'].'</small>';
	print '</div>'; // col-10
	print '<div class="col-md-2 col-sm-2 col-xs-2 text-right">';
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	print '</div>'; // col-2
	print '</div>'; // row
	print '</h2>';  
	
	// print '<div class="row">';	
	
	// print '<div class="col-md-10 col-sm-9 col-xs-12">'; 
	
	
	// print '<div class="row" >';
	// print '<div class="col-md-9">';
	// print '<div class="h1">'.$this->schoolName.'</div>';
	// print '</div>'; // col-10
	// print '<div class="col-md-3">';
	// print '<div class="dashboardBox schoolPointsBox">';
	// print '<div class="schoolPoints h5">';
	// print '<span class="bigText">' . $this->schoolPoints . "</span> " . $this->translations['points']['translation_text'];
	// print '</div>'; // schoolPoints
	// print '</div>'; // schoolPointsBox
	// print '</div>'; // col-3
	// print '</div>'; // row
	
	// print '</div>'; // col-10
	
	// print '</div>'; // row
	
	
	// ----------------------------- Pillar progress
	
	print '<div class="row">';
	
	print '<div class="col-md-4">';
	
	print '<div class="panel panel-default schoolProgress">';
	print '<div class="panel-body">';
	/*
	print '<div class="row">';
	
	print '<div class="col-md-7 h3">'.$this->translations['school_progress']['translation_text'].'</div>';
	
	print '<div class="col-md-5 text-right"><span class="h3">' . $this->schoolPoints . "</span> " . $this->translations['points']['translation_text'].'</div>';
	
	print '</div>'; // row

	print '<div class="row">';
	foreach ( $this->badgeGroups as $badgeGroup ) {
		
		$groupId = $badgeGroup[0];
		$groupName = $badgeGroup[1];
		$colorClass = $this->badgeColorClasses[$groupId];
		$icon = $this->badgeIcons[$groupId];
		
		print '<div id="displayBadges_'. $groupId .'" class="col-md-6 col-sm-6 col-xs-6 btn displayBadges">';
		
				
		$totalTasksDone = $this->badgeGroupSummary[$groupId]->student->numTasks + $this->badgeGroupSummary[$groupId]->teacher->numTasks;
		$totalTasksAvailable = $this->badgeGroupSummary[$groupId]->student->totalTasks + $this->badgeGroupSummary[$groupId]->teacher->totalTasks;
		$totalNumPoints = $this->badgeGroupSummary[$groupId]->school->weightedPoints;


		$progressFrac = 0;
		if ( $totalTasksAvailable != 0 )  {
			$progressFrac = $totalTasksDone / $totalTasksAvailable;
		}


		print '<div class="dashboardBox progressBox">';
		
		
		// print '<div class="row">';
		
		// //print '<div class="col-md-7 h4 '.$colorClass.'_text">'.$groupName.'</div>';
		
		// //print '<div class="col-md-5 '.$colorClass.'_text">'.$totalNumPoints.' '.$this->translations['points']['translation_text'].'</div>';
		
		// print '<div class="col-md-7 h4 ">'.$groupName.'</div>';
		
		// print '<div class="col-md-5 ">'.$totalNumPoints.' '.$this->translations['points']['translation_text'].'</div>';

		// print '</div>'; // row
		
		
		print '<h4><span class="'.$colorClass.'_text"><i class= "fa '.$icon.'" aria-hidden= "true" ></i></span> '.$groupName.'</h4>';
		print '<p>'.$totalNumPoints.' '.$this->translations['points']['translation_text'].'</p>';
		

		print '</div>'; // progressBox
		
		print '</div>'; // col-2 displayBadges
		
	}
	
	print '</div>'; // row with progress by pillar
	*/
	print '<h3>'.$this->schoolName.'</h3>';
	print '<table class="table table-condensed">';
	
	print '<thead>';
	print '<tr class="schoolGroupData">';
		
	print '<th></th>';
	print '<th>'.$this->translations['school_total']['translation_text'].'</th>';
	print '<th class="text-right">'.$this->schoolPoints.'</th>';
	
	print '</tr>';
	print '</thead>';
	print '<tbody>';
		
	foreach ( $this->badgeGroups as $badgeGroup ) {
		
		$groupId = $badgeGroup[0];
		$groupName = $badgeGroup[1];
		$icon = $this->badgeIcons[$groupId];
		$totalNumPoints = $this->badgeGroupSummary[$groupId]->school->weightedPoints;

	
		print '<tr id="schoolGroupData_'. $groupId .'" class="schoolGroupData">';
		
		print '<td><img src="'.$icon.'" class="img-responsive tableGroupIcon" alt="'.$groupName. ' icon" /></td>';
		print '<td>'.$groupName.'</td>';
		print '<td class="text-right">'.$totalNumPoints.'</td>';
		
		print '</tr>';
	}
	
	print '</tbody>';
	print '</table>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
		
	//print '<div class="row">';
	
	// ---------------------------------- School spotlight
	
	// print '<div class="col-md-12">';
	
	// print '<div class="panel panel-default">';
	// print '<div class="panel-body">';
	
	// print '<div id="schoolSpotlight"></div>';
	
	// print '</div>'; // panel-body
	// print '</div>'; // panel
	
	// print '</div>'; // col-12
	
	
	
	//print '</div>'; // row
	
	
	print '</div>'; // col-4
	
	// ---------------------------------- School target
	
	print '<div class="col-md-4">';
	
	
	if ( $this->newAward ) {
		
		$awardType = $this->newAward->awardType;
		print '<div class="row">';
		print '<div class="col-md-12">';
		//print '<div class="panel panel-default coloredPanel">';
		print '<div class="panel panel-default ">';
		print '<div class="panel-body text-center">';
	
		print '<h3>'.$this->translations['congrats']['translation_text'].'</h3>';
		print '<div class="spaced">'.$this->awardIcons[$awardType].'</div>'; //<i class="fa fa-3x fa-trophy"></i>
		print '<p>'.$this->translations['school_reached']['translation_text'].' '.$this->newAward->awardName. '</p>';
		
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
		print '<div class="panel panel-default ">';
		print '<div class="panel-body text-center">';
	
		print '<h3>'.$this->translations['congrats']['translation_text'].'</h3>';
		print '<div class="spaced">'.$this->awardIcons[$awardType].'</div>';
		print '<p>'.$this->translations['school_reached']['translation_text'].' '.$this->existingAward->awardName. '</p>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-12
		print '</div>'; // row
	}
	
	if ( $this->targetAward ) {
		print '<div class="row">';
		print '<div class="col-md-12">';
		//print '<div class="panel panel-default coloredPanel">';
		print '<div class="panel panel-default ">';
		print '<div class="panel-body text-center">';
		
		print '<p>'.$this->translations['current_total']['translation_text'].'</p>';
			
		print '<div class="bigText spaced">'.$this->schoolPoints.' '.$this->translations['points']['translation_text'].'</div>';
		
		print '<p>'.$this->translations['to_reach']['translation_text'].' '.$this->targetAward->awardName. ' '.$this->translations['school_needs']['translation_text'].'</p>';
		
		print '<div class="bigText spaced">'.$this->targetAward->pointsNeeded.' '.$this->translations['points']['translation_text'].'</div>';
		
		print '<p>'.$this->translations['you_help']['translation_text'].'</p>';
		
		if ( Biodiv\SchoolCommunity::isStudent() ) {
			print '<a href="'.$this->translations['badges_link']['translation_text'].'" >';
		}
		else {
			print '<a href="'.$this->translations['activity_link']['translation_text'].'" >';
		}
	
		print '<button class="btn btn-primary">'.$this->translations['help_get']['translation_text'].'</button>';
		
		print '</a>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-12
		print '</div>'; // row
	}
	
	
	print '</div>'; // col-4
	
	
	
	// ------------------- RHS event log
	
	print '<div class="col-md-4">';
	
	print '<div class="panel panel-default eventFeed">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12 h3">'.$this->translations['events_heading']['translation_text'].'</div>';
	
	print '</div>';

	
	print '<div id="eventLog"></div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // col-4
	
	print '</div>'; // row
	
	print '</div>'; // col-10 or 12
	
	print '</div>'; // row
	
	/*
	print '<div class="row">';
	print '<div class="col-md-9">';
	print '<div class="row">';
	print '<div class="col-md-4 col-md-offset-2 text-center">';
	print '<button class="btn btn-primary btn-lg dash_btn">'.$this->translations['suggest_task']['translation_text'].'</button>';
	print '</div>'; // col-4
	print '<div class="col-md-4 text-center">';
	print '<button class="btn btn-primary btn-lg dash_btn">'.$this->translations['top_species']['translation_text'].'</button></a>';
	print '</div>'; // col-4
	print '</div>'; // row
	print '</div>'; // col-9
	print '</div>'; // row
	*/

}

print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
//print '        <h4 class="modal-title">'.$this->translations['review']['translation_text'].'</h4>';
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


JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schooldashboard.js", true, true);


?>





