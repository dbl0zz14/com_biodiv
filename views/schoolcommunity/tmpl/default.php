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
else if ( !$this->mySchoolId ) {
	print '<h2>'.$this->translations['no_school']['translation_text'].'</h2>';
}
else {
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("schoolcommunity");
		
		print '</div>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
		//Biodiv\SchoolCommunity::generateBackAndLogout();
		//Biodiv\SchoolCommunity::generateStudentMasthead();
	}
	
	// --------------------- Main content
	
	print '<div id="displayArea">';
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print '<span class="greenHeading">'.$this->translations['heading']['translation_text'].'</span> <small class="hidden-xs">'.$this->translations['subheading']['translation_text'].'</small>';
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
	
	
	print '<div class="row">';
	
	print '<div class="col-md-3 col-md-push-9 col-sm-12 col-xs-12">';
	
	// -------------------------- School search 
	
	print '<div class="form-group has-feedback">';
	print '  <div class="input-group">';
	print '    <span class="input-group-addon" style="background-color:#FFFFFF; border-bottom-left-radius:25px; border-top-left-radius:25px;"><span class="glyphicon glyphicon-search"></span></span>';
	print '    <input type="search" class="form-control" id="searchSchools" placeholder="'.
			$this->translations['search_schools']['translation_text'].
			'" style="border-left: 0px;border-bottom-right-radius:25px; border-top-right-radius:25px;">';
	print '  </div>';
	print '</div>	';
	
	
	print '<div class="list-group btn-group-vertical btn-block schoolList" role="group" aria-label="School Buttons">';
	
	// Pinned resources and latest upload
	
	foreach($this->schools as $school){
		
		$schoolId = $school->schoolId;
		$schoolName = $school->schoolName;
				
		print '<button type="button" id="school_'.$schoolId.'" class="list-group-item btn btn-block school_btn" style="white-space: normal;">';
		
		print '<h5>'.$schoolName.'</h5>';
		
		print '</button>';
	}

	print '</div>'; // list-group
	
	print '</div>'; // col-3
	
	print '<div class="col-md-9 col-md-pull-3 col-sm-12 col-xs-12">';
	
	print '<div class="row">'; 
	
	print '<div id="displaySchoolCharts_Awards" class="col-md-12 col-sm-12 col-xs-12 displaySchoolCharts">';
	
	print '<div class="panel panel-default">';
			
		print '<div class="panel-body">';
		
		print '<div class="row">';
		
		$icon = '<span class="gold"><i class="fa fa-trophy"></i></span>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">';
		print '<div class="communityGroupName">';
		print '<span class="gold"><i class="fa fa-trophy"></i></span> ';
		print $this->translations['awards']['translation_text'];
		print '</div>';
		print '</div>';

		//print '<div class="col-md-10 col-sm-10 col-xs-10 h3 text-left"><strong>'.$groupName.'</strong></div>';
		
		print '</div>'; // row
		
		
		//print '<div class="schoolsChartBox">';
		print '<div class="schoolCharts">';
		print '<table class="table table-condensed">';
		print '<thead>';
		//print '<th></th><th>School</th><th>% done</th>';
		print '<th>School</th>';
		
		foreach ( $this->modules as $module ) {
			print '<th class="text-center"><img class="img-responsive moduleAwardsIcon'.$module->name.'" src="'.$module->icon.'"></th>';
		}
		print '</thead>';
		print '<tbody>';
		
		print '<tr id="displaySelectedCharts_Awards" class="displaySelectedCharts"></tr>';
		
		foreach ( $this->schoolAwards as $schoolName=>$schoolAward ) {
			
			$schoolId = 0;
			if ( count($schoolAward) > 0 )
			{
				$firstModule = array_keys($schoolAward)[0];
				$schoolId = $schoolAward[$firstModule]->schoolId;
			}
			print '<tr class="schoolProgress_'.$schoolId.'">';
			
			//print '<td>'.$position.'</td>';
			//print '<td>'.$this->awardIcons[$awardType].'</td>';
			print '<td>'.$schoolName.'</td>';
			
			foreach ( $this->moduleIds as $moduleId ) {
				$awardIcon = '';
				if ( array_key_exists($moduleId, $schoolAward) ) {
					$awardType = $schoolAward[$moduleId]->awardType;
					$awardIcon = $this->awardIcons[$awardType];
				}
				print '<td class="text-center">'.$awardIcon.'</td>';
			}
		}
		
		print '</tr>';
		
		print '</tbody>';
		print '</table>';
		print '</div>'; //schoolsChartBox
		
		print '</div>'; // panel-body
		print '</div>'; // panel
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	


	print '<div class="row allSchoolsProgress">';
	//print '<div class="row">';
	foreach ( $this->badgeGroups as $badgeGroup ) {
		
		$groupId = $badgeGroup[0];
		$groupName = $badgeGroup[1];		
		$colorClass = $this->badgeColorClasses[$groupId];
		$icon = $this->badgeIcons[$groupId];
		
		print '<div id="displaySchoolCharts_'. $groupId .'" class="col-md-6 col-sm-12 col-xs-12 displaySchoolCharts">';
		
		print '<div class="panel panel-default">';
			
		print '<div class="panel-body">';
		
		print '<div class="row">';
		
		// print '<div class="col-md-2 col-sm-2 col-xs-2 communityTableImg '.$colorClass.'_text"><img src="'.$icon.'" class="img-responsive communityTableIcon" alt="'.$groupName. ' icon" /></div>';

		// print '<div class="col-md-10 col-sm-10 col-xs-10 h3 text-left"><strong>'.$groupName.'</strong></div>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12 '.$colorClass.'_text">';
		print '<div class="communityGroupName">';
		print '<img src="'.$icon.'" class="img-responsive communityTableIcon" alt="'.$groupName. ' icon" /> ';
		print $groupName;
		print '</div>';
		print '</div>';

		//print '<div class="col-md-10 col-sm-10 col-xs-10 h3 text-left"><strong>'.$groupName.'</strong></div>';
		
		print '</div>'; // row
		
		
		//print '<div class="schoolsChartBox">';
		print '<div class="schoolCharts">';
		print '<table class="table table-condensed">';
		print '<thead>';
		//print '<th></th><th>School</th><th>% done</th>';
		print '<th>School</th>';
		
		foreach ( $this->modules as $module ) {
			print '<th class="text-center"><img class="img-responsive moduleIcon'.$module->name.'" src="'.$module->icon.'"></th>';
		}
		print '</thead>';
		print '<tbody>';
		//print '<div id="displaySelectedCharts_'. $groupId .'" class="displaySelectedCharts"></div>';
		print '<tr id="displaySelectedCharts_'. $groupId .'" class="displaySelectedCharts"></tr>';
		$position = 1;
		//$maxPoints = $this->data[$groupId]["maxPoints"];
		
		// $errMsg = print_r ( $this->data[$groupId]["schools"], true );
		// error_log ( "SchoolCommunity: " . $errMsg );
		
		
		foreach ( $this->data[$groupId]["schools"] as $groupSchoolPoints ) {
			
			$schoolId =  $groupSchoolPoints->schoolId;
			$schoolName = substr($groupSchoolPoints->schoolName, 0, 20);
			$weightedPoints = $groupSchoolPoints->totalPoints;
			$pointsAvailable = $groupSchoolPoints->totalPointsAvail;
			
			if ( $pointsAvailable > 0 ) {
				$truePercentPointsAll = round(100*$weightedPoints/$pointsAvailable);
			}
			else {
				$truePercentPointsAll = 0;
			}
			
			
			print '<tr class="schoolProgress_'.$schoolId.'">';
			
			//print '<td>'.$position.'</td>';
			//print '<td>'.$this->awardIcons[$awardType].'</td>';
			print '<td>'.$schoolName.'</td>';
			
			foreach ( $this->moduleIds as $moduleId ) {
				
				$truePercentPoints = 0;
				if ( array_key_exists($moduleId, $groupSchoolPoints->modules) ) {
			
					$weightedPoints = $groupSchoolPoints->modules[$moduleId]->school->weightedPoints;
					$pointsAvailable = $groupSchoolPoints->modules[$moduleId]->school->pointsAvailable;
					
					if ( $pointsAvailable > 0 ) {
						$truePercentPoints = round(100*$weightedPoints/$pointsAvailable);
					}
					else {
						$truePercentPoints = 0;
					}
				}
			
				print '<td class="text-center">'.$truePercentPoints.'</td>';
				
			}
			
			print '</tr>';
			
			// print '<td class="text-center">'.$groupSchoolPoints->totalPoints.'</td>';
			// print '<td class="text-center">'.$groupSchoolPoints->totalPointsAvail.'</td>';
			
			/*
			print '<div class="row schoolProgress_'.$schoolId.'">';
			print '<div class="col-md-2 text-right schoolPosition_'.$schoolId.'">'.$position.'</div>';
			print '<div class="col-md-7 text-left">'.$schoolName.'</div>';
			print '<div class="col-md-3 text-right schoolProgressAmount_'.$schoolId.'">'.$truePercentPoints.'%</div>';
			print '</div>';
			*/
			$position += 1;
			/*
			print '<div class="progress schoolProgress_'.$schoolId.'" style="background-color:'.$badgeLightColor.'">';
			print '<div class="progress-bar" role="progressbar" aria-valuenow="'.$widthPercent.'" aria-valuemin="0" aria-valuemax="100" '.
    			' style="width:'.$widthPercent.'%; background-color:'.$badgeColor.'">'.$schoolName.
				' '. $truePercentPoints.'%</div>';
			// print '<div class="progress-bar" role="progressbar" aria-valuenow="'.$weightedPoints.'" aria-valuemin="100" aria-valuemax="'.
				// $maxPoints . '"  style="width:'.$widthPercent.'%; background-color:'.$badgeColor.'">'.$schoolName.
				// ' '.
				// $weightedPoints.'</div>';
				
			print '</div>'; // progress
			*/
		}
		
		print '</tbody>';
		print '</table>';
		print '</div>'; //schoolsChartBox
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-6 displaySchoolCharts
	}
	
	print '</div>'; // row  allSchoolsProgress
	
	print '</div>'; // col-9
	
	
	print '</div>'; // row
	
	
	print '</div>'; // displayArea
	
	print '</div>'; // col-10 or 12
	
	print '</div>'; // row
	
	

}


print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title">'.$this->translations['review']['translation_text'].'</h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';




JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schoolcommunity.js", true, true);


//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js", true, true);




?>





