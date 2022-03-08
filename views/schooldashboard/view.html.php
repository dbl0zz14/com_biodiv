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
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("schooldashboard");
	
	// Check whether set id 
			
	$input = JFactory::getApplication()->input;
	
	$this->schoolId = $input->getInt('school', 0);
	
	if ( $this->personId ) {
		
		$this->helpOption = codes_getCode ( "schooldashboard", "beshelp" );
			
		$this->myTotalPoints = Biodiv\Task::getTotalUserPoints();
		
		/*
		$schoolRoles = Biodiv\SchoolCommunity::getSchoolRoles();
			
		$errMsg = print_r ( $schoolRoles, true );
		error_log ( "Got school roles:" );
		error_log ( $errMsg );
		
		if ( count($schoolRoles) > 0 ) {
			error_log ( "Getting school id" );
			$this->mySchoolId = $schoolRoles[0]["school_id"];
			$this->mySchoolRole = $schoolRoles[0]["role_id"];
			error_log ( "School id = " . $this->schoolId );
		}
		
		if ( $this->schoolId == 0 ) {
			$this->schoolId = $this->mySchoolId;
		}
		*/
		
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
		$this->schoolPoints = 0;
		$this->schoolId = 0;
		$this->school = "";
		$this->mySchoolRole = 0;
		
		if ( $this->schoolUser ) {
			$this->schoolId = $this->schoolUser->school_id;
			$this->schoolName = $this->schoolUser->school;
			$this->mySchoolRole = $this->schoolUser->role_id;
		}
		
		// Check whether first load - this is teacher home page
		
		$this->firstLoad = Biodiv\SchoolCommunity::isNewUser();
		
		$this->notifications = null;
		if ( $this->firstLoad ) {
			Biodiv\SchoolCommunity::setNewUser(0);
			Biodiv\SchoolCommunity::addNotification($this->translations['welcome_note']['translation_text']);
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
			
			//$this->schoolName = Biodiv\SchoolCommunity::getSchoolName ( $this->schoolId );
			
			$this->awardIcons = array( 'NONE'=>'',
									'SCHOOL_BRONZE'=>'<span class="bronze"><i class="fa fa-3x fa-trophy"></i></span>',
									'SCHOOL_SILVER'=>'<span class="silver"><i class="fa fa-3x fa-trophy"></i></span>',
									'SCHOOL_GOLD'=>'<span class="gold"><i class="fa fa-3x fa-trophy"></i></span>');
		
			
			// Get the pillars: Quizzer etc
			$this->badgeGroups = codes_getList ( "badgegroup" );
			
			// Get the current status for each badge group.
			$this->badgeGroupSummary = array();
			$this->badgeColorClasses = array();
			$this->badgeIcons = array();
			
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
				$iconArray = getOptionData ( $groupId, "icon" ); 

				$icon = "";
			
				if ( count($iconArray) > 0 ) {
					$icon = $iconArray[0];
				}
				
				//$this->badgeIcons[$groupId] = $icon;
				
				$badgeGroup = new Biodiv\BadgeGroup ( $groupId );
				
				$imageData = $badgeGroup->getImageData();
				
				$this->badgeIcons[$groupId] = $imageData->icon;
				
				$this->badgeGroupSummary[$groupId] = $badgeGroup->getSummary();
				
				$this->badgeGroupSummary[$groupId] = Biodiv\BadgeGroup::getSchoolSummary ( $this->schoolId, $groupId );
				
				$this->schoolPoints += $this->badgeGroupSummary[$groupId]->school->weightedPoints;
				
			}
			
			$targetAwards = Biodiv\SchoolCommunity::checkSchoolAwards($this->schoolId, $this->schoolPoints);
			
			$this->existingAward = null;
			$this->newAward = null;
			$this->targetAward = null;
			
			if ( property_exists ( $targetAwards, "existingAward" ) ) {
				$this->existingAward = $targetAwards->existingAward;
			}
			
			if ( property_exists ( $targetAwards, "latestAward" ) ) {
				$this->newAward = $targetAwards->latestAward;
			}
			
			if ( property_exists ( $targetAwards, "targetAward" ) ) {
				$this->targetAward = $targetAwards->targetAward;
			}
			
		}
		
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

