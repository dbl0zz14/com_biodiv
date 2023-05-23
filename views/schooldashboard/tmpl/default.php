<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$extraClasses = false;
$extraStudents = false;

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_LOGIN").'</div>';
}
else if ( !$this->schoolId ) {
	print '<h2>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_NO_SCHOOL").'</h2>';
}
else if ( $this->firstLoad ) {
	
	Biodiv\SchoolCommunity::generateNonUserHeader ( 0 );
	
	$extraClasses = false;
	$extraStudents = false;
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<div class="panel vSpaced" >';
	print '<div class="panel-body">';

	print '<h1 class="text-center">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_WELCOME").'</h1>';
	
	//print '<h2 class="text-center bigSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_YOU_ARE").'</h2>';

	$hideAvatars = false;
	if ( ($this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE) or ($this->mySchoolRole == Biodiv\SchoolCommunity::ECOLOGIST_ROLE) ) {
		print '<div id="policiesArea">';
		$hideAvatars = true;
		print '<div class="row">';
		print '<div class="col-md-8 col-md-offset-2">';
		print '<div class="panel">';
		print '<div class="panel-body">';
		print '<h3 class="text-center vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_TEXT").'</h3>';
			
		print '<div class="text-center vSpaced">';
		//print '<a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_LINK").'" target="_blank" rel="noopener noreferrer" class="btn btn-primary btnInSpace">'.
		print '<a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_LINK").'" target="_blank" rel="noopener noreferrer" class="btn btn-primary spaced">'.
			JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_BTN").'</a>';
		print '<button id="policiesDone" class="btn btn-info spaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POLICIES_DONE").'</button>';
		print '</div>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // col-8
		print '</div>'; // row
		
		print '</div>'; // policiesArea
	}
	
	if ( $hideAvatars ) {
		print '<div id="avatarArea" class="hidden">';
	}
	else {
		print '<div id="avatarArea">';
	}
	
	print '<h2 class="text-center bigSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_YOU_ARE").'</h2>';
	
	print '<h3 class="text-center vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CHOOSE_AVATAR").'</h3>';
	
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
		
		print '<button id="avatarBtn_'.$avatarId.'" class="avatarBtn saveAvatar '.$activeClass.'"><img src="'.$avatar->image.'" class="img-responsive" alt="'.$avatar->name.' avatar" /></button>';
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
	
	//print '<button id="saveAvatar" class="btn btn-primary btn-lg spaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SAVE_AVATAR").'</button>';
	
	//print '</div>'; // row
	print '</div>'; // avatarArea
	
	print '<div id="avatarSavedArea" class="hidden"></div>';
	
	print '<div id="goToDash" class="text-center hidden" >';
	print '<button id="changeAvatar" class="btn btn-lg btn-info hSpaced vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CHANGE_AVATAR").'</button>';
	print '<button class="btn btn-primary btn-lg reloadPage hSpaced vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_DASHBOARD").'</button>';
	print '</div>';
	
	
	print '</div>'; // panel-body
	print '</div>'; // panel
		
	print '</div>'; // col-12
	print '</div>'; // row
}
else {
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 

	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "schooldashboard");
	
	print '</div>'; // col-12
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_schooladmin" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	
	// --------------------- Main content
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2 class="hidden-sm hidden-md hidden-lg">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_HEADING").'</span>';
	print '</h2>';
	
	// -------------------------------  School name and total
	
	print '<div id="displayArea">';
	
	print '<div class="schoolDashboardGrid">';
	
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::ADMIN_ROLE ) {
		
		print '<div class="schoolDashboardSchoolStatus">';
		
		print '<div class="panel actionPanel">';
		print '<div class="panel-body">';
		
		print '<div class="schoolStatusGrid">';
		
		//print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SCHOOL_PROGRESS").'</h3>';
		print '<div class="schoolStatusLogo text-center"><img src="'.$this->schoolUser->school_logo.'" class="img-responsive"></div>';
		print '<div class="schoolStatusName text-center vSpaced"><span class="greenHeading h3 thickFont">'.$this->schoolName.'</span></div>';
		
		if ( $this->schoolStatus ) {
			
			print '<div class="schoolStatusClassBadgeIcon h3">';
			print '<img src="'.$this->badgeImg.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusClassBadgesNum h3">';
			print $this->schoolStatus->class_badges;
			print '</div>';
			//print '<div class="schoolStatusClassBadges">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CLASS_BADGES").'</div>';
			
			print '<div class="schoolStatusClassBronzeIcon h3">';
			$award = $this->collectedAwards[Biodiv\SchoolCommunity::TEACHER_ROLE][1];
			print '<img src="'.$award->image.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusClassBronzeNum h3">';
			print $this->schoolStatus->class_awards[1];
			print '</div>';
			
			print '<div class="schoolStatusClassSilverIcon h3">';
			$award = $this->collectedAwards[Biodiv\SchoolCommunity::TEACHER_ROLE][2];
			print '<img src="'.$award->image.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusClassSilverNum h3">';
			print $this->schoolStatus->class_awards[2];
			print '</div>';
			
			print '<div class="schoolStatusClassGoldIcon h3">';
			$award = $this->collectedAwards[Biodiv\SchoolCommunity::TEACHER_ROLE][3];
			print '<img src="'.$award->image.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusClassGoldNum h3">';
			print $this->schoolStatus->class_awards[3];
			print '</div>';
			
			print '<div class="schoolStatusStudentBadgeIcon h3">';
			print '<img src="'.$this->badgeImg.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusStudentBadgesNum h3">';
			print $this->schoolStatus->student_badges;
			print '</div>';
			//print '<div class="schoolStatusStudentBadges">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_STUDENT_BADGES").'</div>';
			
			print '<div class="schoolStatusStudentBronzeIcon h3">';
			$award = $this->collectedAwards[Biodiv\SchoolCommunity::STUDENT_ROLE][1];
			print '<img src="'.$award->image.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusStudentBronzeNum h3">';
			print $this->schoolStatus->student_awards[1];
			print '</div>';
			
			print '<div class="schoolStatusStudentSilverIcon h3">';
			$award = $this->collectedAwards[Biodiv\SchoolCommunity::STUDENT_ROLE][2];
			print '<img src="'.$award->image.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusStudentSilverNum h3">';
			print $this->schoolStatus->student_awards[2];
			print '</div>';
			
			print '<div class="schoolStatusStudentGoldIcon h3">';
			$award = $this->collectedAwards[Biodiv\SchoolCommunity::STUDENT_ROLE][3];
			print '<img src="'.$award->image.'" class="img-responsive classStatusIcon" alt="badge icon">';
			print '</div>';
			print '<div class="schoolStatusStudentGoldNum h3">';
			print $this->schoolStatus->student_awards[3];
			print '</div>';
			
			
		}
		
		print '<div class="schoolStatusBadges text-center">';
		print '<a href="'.$this->badgesPage.'" >';
		print '<button class="btn btn-primary btn-lg vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_BADGES").'</button>';
		print '</a>';
		print '</div>';
		
		print '</div>'; // schoolStatusGrid
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // schoolDashboardSchoolStatus
	
	}
		
		
		
	print '<div class="schoolDashboardFeaturedSpecies">';
	
	print '<div class="panel actionPanel" data-toggle="modal" data-target="#speciesModal">';
	print '<div class="panel-body">';
	
	print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_FEATURED_SPECIES").'</h3>';
	
	print '<div class="featuredFace">';
	print '<img class="img-responsive featuredImage" src="'.$this->featuredSpecies->part_image.'">';
	print '</div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // schoolDashboardFeaturedSpecies
		
	

		
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::ADMIN_ROLE ) {	
		
		print '<div class="schoolDashboardClassStatus">';
		
		print '<div class="panel actionPanel">';
		print '<div class="panel-body">';
		
		
		$extraClasses = false;
		$extraStudents = false;
		if ( $this->classStatus ) {
			
			print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_BADGE_PROGRESS").'</h3>';
		
			print '<div class="classStatusGroup ">';
			
			$classCount = 1;
			foreach ( $this->classStatus as $classId=>$classObj ) {
				
				if ( $classCount > 3 ) {
					break;
				}
				
				print '<div class="panel">';
				print '<div class="panel-body">';
				
				print '<div class="classStatusGrid ">';
				
				print '<div class="classStatusAvatar">';
				print '<img src="'.$classObj->avatar.'" class="img-responsive" alt="avatar icon">';
				print '</div>';
				
				print '<div class="classStatusName h4">';
				print $classObj->name;
				print '</div>';
				
				print '<div class="classStatusBadgeIcon">';
				print '<img src="'.$this->badgeImg.'" class="img-responsive classStatusIcon" alt="badge icon">';
				print '</div>';
				
				print '<div class="classStatusNumBadges h4">';
				print $classObj->num_badges;
				print '</div>';
				
				$i=1;
				foreach ( $classObj->awards as $award ) {
					
					print '<div class="classStatusAward_'.$i.'">';
					print '<img src="'.$award->getDisplayImage().'" class="img-responsive classStatusIcon" alt="'.$award->getName().' icon">';
					print '</div>';
					$i++;
				}
				
				print '</div>'; // classStatusGrid
				
				print '</div>'; // panel-body
				print '</div>'; // panel
				
				$classCount += 1;
			}
			print '</div>';
			
			if ( $classCount > 3 ) {
				$extraClasses = true;
				print '<div class="text-center"><button class="btn btn-lg btn-info" data-toggle="modal" data-target="#classStatusModal">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_MORE").'</button></div>';
			}
		}
		else if ( $this->studentStatus ) {
			
			$awardRole = Biodiv\SchoolCommunity::STUDENT_ROLE;
			print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_STUDENT_PROGRESS").'</h3>';
		
			print '<div class="classStatusGroup ">';
			
			$studentCount = 1;
			foreach ( $this->studentStatus as $studentId=>$studentObj ) {
				
				if ( $studentCount > 3 ) {
					break;
				}
				
				print '<div class="panel">';
				print '<div class="panel-body">';
				
				print '<div class="classStatusGrid ">';
				
				print '<div class="classStatusAvatar">';
				print '<img src="'.$studentObj->avatar.'" class="img-responsive" alt="avatar icon">';
				print '</div>';
				
				print '<div class="classStatusName h4">';
				print $studentObj->name;
				print '</div>';
				
				print '<div class="classStatusBadgeIcon">';
				print '<img src="'.$this->badgeImg.'" class="img-responsive classStatusIcon" alt="badge icon">';
				print '</div>';
				
				print '<div class="classStatusNumBadges h4">';
				print $studentObj->num_badges;
				print '</div>';
				
				print '<div class="classStatusAward_1">';
				$award = $this->allAwards[$awardRole][1];
				if ( $studentObj->level1 ) {
					$awardImg = $award->image;
				}
				else {
					$awardImg = $award->uncollected_image;
				}
				print '<img src="'.$awardImg.'" class="img-responsive classStatusIcon" alt="'.$award->name.' icon">';
				print '</div>';
				
				print '<div class="classStatusAward_2">';
				$award = $this->allAwards[$awardRole][2];
				if ( $studentObj->level2 ) {
					$awardImg = $award->image;
				}
				else {
					$awardImg = $award->uncollected_image;
				}
				print '<img src="'.$awardImg.'" class="img-responsive classStatusIcon" alt="'.$award->name.' icon">';
				print '</div>';
				
				print '<div class="classStatusAward_3">';
				$award = $this->allAwards[$awardRole][3];
				if ( $studentObj->level3 ) {
					$awardImg = $award->image;
				}
				else {
					$awardImg = $award->uncollected_image;
				}
				print '<img src="'.$awardImg.'" class="img-responsive classStatusIcon" alt="'.$award->name.' icon">';
				print '</div>';
				
				print '</div>'; // classStatusGrid
				
				print '</div>'; // panel-body
				print '</div>'; // panel
				
				$studentCount += 1;
			}
			print '</div>';
			
			if ( $studentCount > 3 ) {
				$extraStudents = true;
				print '<div class="text-center"><button class="btn btn-lg btn-info" data-toggle="modal" data-target="#studentStatusModal">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_MORE").'</button></div>';
			}
		}
		else {
			print '<h4>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_NO_ACCOUNTS").'</h4>';
		}
		
		// print '<div class="text-center">';
		// print '<a href="'.$this->badgesPage.'" >';
		// print '<button class="btn btn-info btn-lg vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_BADGES").'</button>';
		// print '</a>';
		// print '</div>';
				
		
			
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // schoolDashboardClassStatus

	
	
		if ( $this->schoolProject ) {
			
			print '<div class="schoolDashboardProjectCharts">';
		
			print '<div class="panel actionPanel">';
			print '<div class="panel-body">';
			
			print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_PROJECT_DATA").'</h3>';
		
			print '<div class="projectChart topSpaced">';
			print '<canvas id="animalsBarChart" class="animals-bar" data-project-id="'.$this->schoolProject.'" ></canvas>';
			print '</div>';
			
			print '<div class="text-center">';
			if ( $this->kioskUrl ) {
				
				print '<a href="'.$this->kioskUrl.'" >';
				print '<button class="btn btn-info btn-lg projectBtn vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TO_PROJECT").'</button>';
				print '</a>';
				
			}
			if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
				
				print '<a href="'.$this->trapperPage.'" >';
				print '<button class="btn btn-info btn-lg projectBtn vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TO_TRAPPER").'</button>';
				print '</a>';
				
			}	
			print '</div>';

			print '</div>'; // panel-body
			print '</div>'; // panel
			
			print '</div>';
		}
	}
	else {
		
		print '<div class="schoolDashboardSchoolStatus">';
		
		print '<div class="panel actionPanel">';
		print '<div class="panel-body">';
		
		print '<div class="text-center vSpaced"><span class="greenHeading h3 thickFont">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_ADMIN_USER").'</span></div>';
		
		print '<div class="text-center">';
		print '<a href="'.$this->adminPage.'" >';
		print '<button class="btn btn-primary btn-lg vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_ADMIN_DASH").'</button>';
		print '</a>';
		print '</div>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // schoolDashboardSchoolStatus
	}
	
	print '<div class="schoolDashboardPost">';
	
	print '<div class="panel actionPanel">';
	print '<div class="panel-body">';
	
	print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POST").'</h3>';

	if ( $this->latestPost ) {
		print '<div id="postCol_'.$this->latestPost->set_id.'" class="postCol postCol_'.$this->latestPost->school_id.' topSpaced">';
		
		$post = new Biodiv\Post ($this->schoolUser, 
								$this->latestPost->person_id,
								$this->latestPost->set_id,
								$this->latestPost->text,
								$this->latestPost->school_id,
								$this->latestPost->name,
								$this->latestPost->image,
								$this->latestPost->num_likes,
								$this->latestPost->my_likes,
								$this->latestPost->files,
								$this->latestPost->tstamp								
								);
								
		$post->printPost();
		
		print '</div>';
		
		print '<div class="text-center">';
		print '<a href="'.$this->communityPage.'">';
		print '<button class="btn btn-info btn-lg vSpaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SEE_MORE").'</button>';
		print '</a>';
		print '</div>';
			
	}

	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>';
	
	// if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		// $heading = JText::_("COM_BIODIV_SCHOOLDASHBOARD_WELCOME");
		// $subheading = "";
	// }
	// else {
		// $heading = JText::_("COM_BIODIV_SCHOOLDASHBOARD_HEADING");
		// $subheading = JText::_("COM_BIODIV_SCHOOLDASHBOARD_SUBHEADING");
	// }
	
	// //print '<h2>';
	// print '<div class="row">';
	// print '<div class="col-md-8 col-sm-6 col-xs-6 h2">';
	// print '<span class="greenHeading">'.$heading.'</span> <small class="hidden-xs hidden-sm">'.$subheading.'</small>';
	// print '</div>'; // col-8
	
	// print '<div class="col-md-3 col-sm-5 col-xs-6 text-right">';
	// if ( $this->kioskUrl ) {
		
		// print '<a href="'.$this->kioskUrl.'" >';
		// print '<button class="btn btn-success projectBtn">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TO_PROJECT").'</button>';
		// print '</a>';
	// }
	// print '</div>'; // col-3
	// print '<div class="col-md-1 col-sm-1 col-xs-12 text-right">';
	// if ( $this->helpOption > 0 ) {
		// print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton" data-toggle="modal" data-target="#helpModal">';
		// print '<i class="fa fa-lg fa-info"></i>';
		// print '</div>'; // helpButton
	// }
	// print '</div>'; // col-2
	// print '</div>'; // row
	
	
	// // ----------------------------- Pillar progress plus target stuff
	
	// print '<div class="row">';
	
	// print '<div class="col-md-8">';
	
	// print '<div class="panel panel-default schoolProgress">';
	// print '<div class="panel-body">';
	
	// print '<div class="row">';
	// print '<div class="col-md-7">';
	
	// print '<h3 class="panelHeading">'.$this->schoolName.'</h3>';
	// print '<table class="table table-condensed">';
	
	// print '<thead>';
	// print '<tr class="schoolGroupData">';
		
	// print '<th></th>';
	// print '<th></th>';
	// foreach ( $this->modules as $module ) {

		// print '<th class="text-center"><img class="img-responsive moduleIcon'.$module->name.'" src="'.$module->icon.'"></th>';
	// }
	
	// print '</tr>';
	// print '</thead>';
	// print '<tbody>';
		
	// foreach ( $this->badgeGroups as $badgeGroup ) {
		
		// $groupId = $badgeGroup[0];
		// $groupName = $badgeGroup[1];
		// $icon = $this->badgeIcons[$groupId];
		
	
		// print '<tr id="schoolGroupData_'. $groupId .'" class="schoolGroupData">';
		
		// print '<td><img src="'.$icon.'" class="img-responsive tableGroupIcon" alt="'.$groupName. ' icon" /></td>';
		// print '<td>'.$groupName.'</td>';
		
		// foreach ( $this->moduleIds as $moduleId ) {
			// $totalNumPoints = $this->badgeGroupSummary[$moduleId][$groupId]->school->weightedPoints;

			// print '<td class="text-center">'.$totalNumPoints.'</td>';
		// }
		
		// print '</tr>';
	// }
	
	// print '<tr class="schoolGroupData">';
		
	// print '<td></td>';
	// print '<td>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TOTALS").'</td>';
	
	// foreach ( $this->moduleIds as $moduleId ) {
		
		// if ( array_key_exists ( $moduleId, $this->schoolPoints ) ) {
			// print '<td class="text-center">'.$this->schoolPoints[$moduleId].'</td>';
		// }
		// else {
			// print '<td></td>';
		// }

	// }
	
	// print '<tr class="schoolGroupData">';
		
	// print '<td></td>';
	// print '<td>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_AWARDS").'</td>';
	
	// foreach ( $this->moduleIds as $moduleId ) {
		
		// if ( array_key_exists ( $moduleId, $this->moduleAwards ) ) {
			// $awardType = $this->moduleAwards[$moduleId]->awardType;
			// print '<td class="text-center">'.$this->moduleAwardIcons[$awardType].'</td>';
		// }
		// else {
			// print '<td></td>';
		// }

	// }
	
	// print '</tr>';
	
	// print '</tbody>';
	// print '</table>';
	
	// print '</div>'; // col-7
	
	// print '<div class="col-md-5">';
	
	
	// // ---------------------------------- School target
	
	// if ( $this->newAward ) {
		
		// $awardType = $this->newAward->awardType;
		// print '<div class="row">';
		// print '<div class="col-md-12">';
		// //print '<div class="panel panel-default coloredPanel">';
		// print '<div class="panel panel-default yellowPanel ">';
		// print '<div class="panel-body">';
	
		// print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CONGRATS").'</h3>';
		
		// print '<div class="row">';
		
		// print '<div class="col-md-7">';
		// print '<p class="spaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SCHOOL_REACHED").' '.$this->newAward->awardName. '</p>';
		// print '</div>'; // col-7
		
		// print '<div class="col-md-5">';
		// print '<div class="spaced text-center">'.$this->awardIcons[$awardType].'</div>';
		// print '</div>'; // col-5
		
		// print '</div>'; // row
		
		// print '</div>'; // panel-body
		// print '</div>'; // panel
		
		// print '</div>'; // col-12
		// print '</div>'; // row
	// }
	// else if ( $this->existingAward ) {
		// $awardType = $this->existingAward->awardType;
		// print '<div class="row">';
		// print '<div class="col-md-12">';
		// //print '<div class="panel panel-default coloredPanel">';
		// print '<div class="panel panel-default yellowPanel">';
		// print '<div class="panel-body">';
	
		// print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CONGRATS").'</h3>';
		
		// print '<div class="row">';
		
		// print '<div class="col-md-7">';
		// print '<p class="spaced">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SCHOOL_REACHED").' '.$this->existingAward->awardName. '</p>';
		// print '</div>'; // col-7
		
		// print '<div class="col-md-5">';
		// print '<div class="spaced text-center">'.$this->awardIcons[$awardType].'</div>';
		// print '</div>'; // col-5
		
		// print '</div>'; // row
		
		// print '</div>'; // panel-body
		// print '</div>'; // panel
		
		// print '</div>'; // col-12
		// print '</div>'; // row
	// }
	
	// if ( $this->targetAward ) {
		// $targetModule = $this->targetAward->module_id;
		// print '<div class="row">';
		// print '<div class="col-md-12">';
		// print '<div class="panel panel-default darkPanel">';
		// print '<div class="panel-body">';
		
		// $imgSrc = $this->modules[$targetModule]->white_icon;
		// print '<div class="h3 panelHeading"><img class="img-responsive targetModuleIcon" src="'.$imgSrc.'"> '.$this->schoolPoints[$targetModule].' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POINTS").'</div>';
		
		// print '<p>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_TO_REACH").' '.$this->targetAward->awardName. ' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_SCHOOL_NEEDS");
		
		// print ' <strong>'.$this->targetAward->pointsNeeded.' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_POINTS").'</strong>';
		
		// print ' '.JText::_("COM_BIODIV_SCHOOLDASHBOARD_YOU_HELP").'</p>';
		
		// if ( Biodiv\SchoolCommunity::isStudent() ) {
			// print '<div class="text-center"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_BADGES_LINK").'" >';
		// }
		// else {
			// print '<div class="text-center"><a href="'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_ACTIVITY_LINK").'" >';
		// }
	
		// print '<button class="btn btn-default btn-lg">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_HELP_GET").'</button>';
		
		// print '</a></div>';
		
		// print '</div>'; // panel-body
		// print '</div>'; // panel
		
		// print '</div>'; // col-12
		// print '</div>'; // row
	// }
	
	
	
	// print '</div>'; // col-5
	
	// print '</div>'; // row
	
	// print '</div>'; // panel-body
	// print '</div>'; // panel
	
		
	// print '</div>'; // col-8
	
		
	
	// // ------------------- RHS event log
	
	// print '<div class="col-md-4">';
	
	// print '<div class="panel panel-default eventFeed">';
	// print '<div class="panel-body">';
	
	// print '<div class="row">';
	
	// print '<div class="col-md-12 h3 panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_EVENTS_HEADING").'</div>';
	
	// print '</div>';

	
	// print '<div id="eventLog"></div>';
	
	// print '</div>'; // panel-body
	// print '</div>'; // panel
	
	// print '</div>'; // col-4
	
	print '</div>'; // displayArea
	
	print '</div>'; // col-10 or 12
	
	print '</div>'; // row

}

print '<div id="speciesModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>'; // modal-header
print '     <div class="modal-body">';
print '<div class="h3">';
print $this->featuredSpecies->name;
print '</div>';
print '<div class="vSpaced">';
print '<img src="'.$this->featuredSpecies->full_image.'" class="img-responsive">';
print '</div>';
print '<h3>'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_KNOW").'</h3>';
print '<div class="h4 vSpaced">';
print $this->featuredSpecies->fact;
print '</div>';

print '      </div>'; // modal-body
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>'; // modal-footer
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal-dialog
print '</div>'; // modal



if ( $extraClasses ) {
	
	print '<div id="classStatusModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';

	print '      <div class="modal-header text-right">';
	print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
	print '      </div>'; // modal-header

	print '     <div class="modal-body">';
	print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_BADGE_PROGRESS").'</h3>';
	print '<div class="row">';
	foreach ( $this->classStatus as $classId=>$classObj ) {
		
		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '<div class="panel">';
		print '<div class="panel-body">';
		
		print '<div class="classStatusGrid ">';
		
		print '<div class="classStatusAvatar">';
		print '<img src="'.$classObj->avatar.'" class="img-responsive" alt="avatar icon">';
		print '</div>';
		
		print '<div class="classStatusName h4">';
		print $classObj->name;
		print '</div>';
		
		print '<div class="classStatusBadgeIcon">';
		print '<img src="'.$this->badgeImg.'" class="img-responsive classStatusIcon" alt="badge icon">';
		print '</div>';
		
		print '<div class="classStatusNumBadges h4">';
		print $classObj->num_badges;
		print '</div>';
		
		$i=1;
		foreach ( $classObj->awards as $award ) {
			
			print '<div class="classStatusAward_'.$i.'">';
			print '<img src="'.$award->getDisplayImage().'" class="img-responsive classStatusIcon" alt="'.$award->getName().' icon">';
			print '</div>';
			$i++;
		}
		
		print '</div>'; // classStatusGrid
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</div>'; // col-6
	}
	print '</div>'; // row
	print '      </div>'; // modal-body
	print '	  <div class="modal-footer">';
	print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CLOSE").'</button>';
	print '      </div>'; // modal-footer
			  
	print '    </div>'; // modal-content
	print '  </div>'; // modal-dialog
	print '</div>'; // modal

}



if ( $extraStudents ) {
	
	print '<div id="studentStatusModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';

	print '      <div class="modal-header text-right">';
	print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
	print '      </div>'; // modal-header

	print '     <div class="modal-body">';
	print '<h3 class="panelHeading">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_STUDENT_PROGRESS").'</h3>';
	
	$maxStudents = 5;
	$studentDisplayCount = 1;
	foreach ( $this->studentStatus as $studentId=>$studentObj ) {
		
		if ( $studentDisplayCount > $maxStudents ) break;
		
		print '<div class="panel">';
		print '<div class="panel-body">';
		
		print '<div class="studentStatusGrid ">';
		
		print '<div class="studentStatusAvatar">';
		print '<img src="'.$studentObj->avatar.'" class="img-responsive" alt="avatar icon">';
		print '</div>';
		
		print '<div class="studentStatusName h4">';
		print $studentObj->name;
		print '</div>';
		
		print '<div class="studentStatusBadgeIcon">';
		print '<img src="'.$this->badgeImg.'" class="img-responsive classStatusIcon" alt="badge icon">';
		print '</div>';
		
		print '<div class="studentStatusNumBadges h4">';
		print $studentObj->num_badges;
		print '</div>';
		
		print '<div class="studentStatusAward_1">';
		$award = $this->allAwards[$awardRole][1];
		if ( $studentObj->level1 ) {
			$awardImg = $award->image;
		}
		else {
			$awardImg = $award->uncollected_image;
		}
		print '<img src="'.$awardImg.'" class="img-responsive classStatusIcon" alt="'.$award->name.' icon">';
		print '</div>';
		
		print '<div class="studentStatusAward_2">';
		$award = $this->allAwards[$awardRole][2];
		if ( $studentObj->level2 ) {
			$awardImg = $award->image;
		}
		else {
			$awardImg = $award->uncollected_image;
		}
		print '<img src="'.$awardImg.'" class="img-responsive classStatusIcon" alt="'.$award->name.' icon">';
		print '</div>';
		
		print '<div class="studentStatusAward_3">';
		$award = $this->allAwards[$awardRole][3];
		if ( $studentObj->level3 ) {
			$awardImg = $award->image;
		}
		else {
			$awardImg = $award->uncollected_image;
		}
		print '<img src="'.$awardImg.'" class="img-responsive classStatusIcon" alt="'.$award->name.' icon">';
		print '</div>';
		
		print '</div>'; // studentStatusGrid
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		$studentDisplayCount += 1;
	}
	
	print '      </div>'; // modal-body
	print '	  <div class="modal-footer">';
	print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CLOSE").'</button>';
	print '      </div>'; // modal-footer
			  
	print '    </div>'; // modal-content
	print '  </div>'; // modal-dialog
	print '</div>'; // modal

}

print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>'; // modal-header
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>'; // modal-body
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLDASHBOARD_CLOSE").'</button>';
print '      </div>'; // modal-footer
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal-dialog
print '</div>'; // modal


JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schooldashboard.js", true, true);

JHTML::script("com_biodiv/pdfjs/pdf.js", true, true);

JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", true, true);
JHTML::script("com_biodiv/project.js", true, true);


?>





