<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewSchoolDashboard extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
   */
  
  public function display($tpl = null) 
  {
    $this->personId = (int)userID();
    
    $app = JFactory::getApplication();
	
	// Check whether set id 
			
	$input = JFactory::getApplication()->input;
	
	$this->schoolId = $input->getInt('school', 0);
	
	if ( $this->personId ) {
		
		$this->helpOption = codes_getCode ( "schooldashboard", "beshelp" );
			
		$this->moduleAwardIcons = array( 'NONE'=>'',
									'SCHOOL_BRONZE'=>'<span class="bronze"><i class="fa fa-lg fa-trophy"></i></span>',
									'SCHOOL_SILVER'=>'<span class="bronze"><i class="fa fa-lg fa-trophy"></i></span><span class="silver"><i class="fa fa-lg fa-trophy"></i></span>',
									'SCHOOL_GOLD'=>'<span class="bronze"><i class="fa fa-lg fa-trophy"></i></span><span class="silver"><i class="fa fa-lg fa-trophy"></i></span><span class="gold"><i class="fa fa-lg fa-trophy"></i></span>');
		
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
		$this->schoolPoints = array();
		$this->schoolId = 0;
		$this->school = "";
		$this->mySchoolRole = 0;
		
		if ( $this->schoolUser ) {
			$this->schoolId = $this->schoolUser->school_id;
			$this->schoolName = $this->schoolUser->school;
			$this->mySchoolRole = $this->schoolUser->role_id;
			$this->schoolProject = $this->schoolUser->project_id;
		}
		
		// Check whether first load - this is teacher home page
		
		$this->firstLoad = Biodiv\SchoolCommunity::isNewUser();
		
		$this->notifications = null;
		if ( $this->firstLoad ) {
			Biodiv\SchoolCommunity::setNewUser(0);
			Biodiv\SchoolCommunity::addNotification(JText::_("COM_BIODIV_SCHOOLDASHBOARD_WELCOME_NOTE"));
			// Do the first unlock to get the badges through
			
			if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
				$this->completedBadgeGroups = Biodiv\Badge::unlockTeacherBadges();
			}
			else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
				$this->completedBadgeGroups = Biodiv\Badge::unlockBadges();
			}
			
			$this->avatars = Biodiv\SchoolCommunity::getAvatars();
		}
		
		
		// Check whether any system calculated tasks have been completed.
		Biodiv\Task::checkSystemTasks();
		
		// Teachers don't have locked badges but this will copy any new badges for them
		if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
			Biodiv\Badge::unlockTeacherBadges();
		}
		else {
			Biodiv\Badge::unlockBadges();
		}
		
		if ( !$this->firstLoad ) {
			$this->notifications = Biodiv\SchoolCommunity::getNotifications();
			Biodiv\SchoolCommunity::notificationsSeen();
		}
	
		//$this->myTotalPoints = Biodiv\Task::getTotalUserPoints();
	
	
		if ( $this->schoolId != 0 ) {
			
			$this->kioskUrl = null;
			if ( $this->schoolProject ) {
				$projectOptions = getSingleProjectOptions ( $this->schoolProject, "kiosk" );
				if ( count($projectOptions) > 0 ) { 
					$this->kioskUrl = $projectOptions[0]["option_name"];
				}
			}
			
			//$this->schoolName = Biodiv\SchoolCommunity::getSchoolName ( $this->schoolId );
			
			$this->awardIcons = array( 'NONE'=>'',
									'SCHOOL_BRONZE'=>'<span class="bronze"><i class="fa fa-5x fa-trophy"></i></span>',
									'SCHOOL_SILVER'=>'<span class="silver"><i class="fa fa-5x fa-trophy"></i></span>',
									'SCHOOL_GOLD'=>'<span class="gold"><i class="fa fa-5x fa-trophy"></i></span>');
		
			$this->moduleAwards = Biodiv\Award::getSchoolModuleAwards ( $this->schoolId );
			
			$this->modules = Biodiv\Module::getModules();
			$this->moduleIds = array_keys ( $this->modules );
			
			// Get the pillars: Quizzer etc
			$this->badgeGroups = codes_getList ( "badgegroup" );
			
			// Get the current status for each badge group.
			$this->badgeGroupSummary = array();
			$this->badgeColorClasses = array();
			$this->badgeIcons = array();
			
			$this->existingModuleAward = array();
			$this->newModuleAward = array();
			$this->targetModuleAward = array();
			
			$this->existingAward = null;
			$this->newAward = null;
			$this->targetAward = null;
			
			$this->badgeIcons = Biodiv\BadgeGroup::getGroupIcons();
			
			foreach ( $this->badgeGroups as $badgeGroup ) {
				$groupId = $badgeGroup[0];
				
				// --------------------------- Colors
				$colorClassArray = getOptionData ( $groupId, "colorclass" ); 

				$colorClass = "";
			
				if ( count($colorClassArray) > 0 ) {
					$colorClass = $colorClassArray[0];
				}
				
				$this->badgeColorClasses[$groupId] = $colorClass;
				
				// ----------------------------- Icons
				// $iconArray = getOptionData ( $groupId, "icon" ); 

				// $icon = "";
			
				// if ( count($iconArray) > 0 ) {
					// $icon = $iconArray[0];
				// }
				
			}
			
			/*
			foreach ( $this->moduleIds as $moduleId ) {
				
				$this->badgeGroupSummary[$moduleId] = array();
				$this->schoolPoints[$moduleId] = 0;
				
				foreach ( $this->badgeGroups as $badgeGroup ) {
					
					$groupId = $badgeGroup[0];
				
					$newBadgeGroup = new Biodiv\BadgeGroup ( $groupId, $moduleId );
					
					if ( !array_key_exists ( $groupId, $this->badgeIcons ) ) {
						$imageData = $newBadgeGroup->getImageData();
					
						$this->badgeIcons[$groupId] = $imageData->icon;
					}
					
					//$this->badgeGroupSummary[$moduleId][$groupId] = $badgeGroup->getSummary();
					
					$this->badgeGroupSummary[$moduleId][$groupId] = Biodiv\BadgeGroup::getSchoolSummary ( $this->schoolId, $groupId, $moduleId );
					
					$this->schoolPoints[$moduleId] += $this->badgeGroupSummary[$moduleId][$groupId]->school->weightedPoints;
				}
				
				$targetAwards = Biodiv\SchoolCommunity::checkSchoolAwards($this->schoolId, $this->schoolPoints[$moduleId], $moduleId);
				
				$errMsg = print_r ( $targetAwards, true );
				error_log ( "Target awards for module " . $moduleId . ": " . $errMsg );
				
				if ( property_exists ( $targetAwards, "existingAward" ) ) {
					$this->existingModuleAward[$moduleId] = $targetAwards->existingAward;
					
					if ( $targetAwards->existingAward ) {
						if ( $this->existingAward == null ) {
							$this->existingAward = $targetAwards->existingAward;
						}
						else if ( $targetAwards->existingAward->award_time > $this->existingAward->award_time  ) {
							$this->existingAward = $targetAwards->existingAward;
						}
					}
				}
				
				if ( property_exists ( $targetAwards, "latestAward" ) ) {
					$this->newModuleAward[$moduleId] = $targetAwards->latestAward;
					
					if ( $targetAwards->latestAward ) {
						if ( $this->newAward == null ) {
							$this->newAward = $targetAwards->latestAward;
						}
						else if ( $targetAwards->latestAward->award_time > $this->newAward->award_time  ) {
							$this->newAward = $targetAwards->latestAward;
						}
					}
				}
				
				if ( property_exists ( $targetAwards, "targetAward" ) ) {
					$this->targetModuleAward[$moduleId] = $targetAwards->targetAward;
					
					if ( $targetAwards->targetAward ) {
						if ( $this->targetAward == null ) {
							$this->targetAward = $targetAwards->targetAward;
						}
						else if ( $targetAwards->targetAward->threshold_per_user < $this->targetAward->threshold_per_user  ) {
							$this->targetAward = $targetAwards->targetAward;
						}
					}
				}
			}
			*/
			
			$schoolStatus = Biodiv\SchoolCommunity::getSchoolStatus($this->schoolId);
			
			$this->badgeGroupSummary = $schoolStatus->badgeGroupSummary;
			$this->schoolPoints = $schoolStatus->points;
			$this->existingAward = $schoolStatus->existingAward;
			$this->newAward = $schoolStatus->newAward;
			$this->targetAward = $schoolStatus->targetAward;
			
			
			//$errMsg = print_r ( $this->badgeGroupSummary, true );
			//error_log ( "Badge groupsummary: " . $errMsg );
		}
		
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

