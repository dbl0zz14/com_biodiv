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
class BioDivViewStudentDashboard extends JViewLegacy
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
	
	// Check user is a student and get school
	$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
	$this->isStudent = $this->schoolUser->role_id == Biodiv\SchoolCommunity::STUDENT_ROLE;
	if ( !$this->isStudent ) {
		$this->school = "";
	}
	else {
		$this->school = $this->schoolUser->school;
	}
	
	// $this->studentDetails = Biodiv\SchoolCommunity::getStudentDetails();
	
	// $this->isStudent = true;
	// if ( !$this->studentDetails ) {
		// $this->isStudent = false;
		// $this->school = "";
	// }
	// else {
		// $this->school = $this->studentDetails[0]["school"];
	// }
	
	// Check whether first load
	$this->firstLoad = Biodiv\SchoolCommunity::isNewUser();
	
	$this->notifications = null;
	if ( $this->firstLoad ) {
		Biodiv\SchoolCommunity::setNewUser(0);
		Biodiv\SchoolCommunity::addNotification(JText::_("COM_BIODIV_STUDENTDASHBOARD_WELCOME_NOTE"));
		// Do the first unlock to get the badges through
		$this->completedBadgeGroups = Biodiv\Badge::unlockBadges();
		$this->avatars = Biodiv\SchoolCommunity::getAvatars();
	}
	
	
	// Check whether any system calculated tasks have been completed.
	error_log ( "Checking system tasks" );
	Biodiv\Task::checkSystemTasks();
	
	// Unlock any badges where previous is complete.  Get any fully completed badge groups.
	$this->completedBadgeGroups = Biodiv\Badge::unlockBadges();
	
	// Check for award thresholds being reached
	Biodiv\Award::updateAwards();
	
	//$this->myTotalPoints = Biodiv\Task::getTotalUserPoints();
	
	$this->totalBadges = Biodiv\Badge::getTotalBadges();
		
	//$this->totalStars = Biodiv\Award::getTotalStars();
	
	if ( !$this->firstLoad ) {
		
		$this->helpOption = codes_getCode ( "studentdashboard", "beshelp" );
		
		$this->numToCollect = Biodiv\Task::countStudentTasks( Biodiv\Badge::COMPLETE );
		
		$this->encourage = null;
	
		$this->notifications = Biodiv\SchoolCommunity::getNotifications();
		Biodiv\SchoolCommunity::notificationsSeen();
		
		if ( $this->notifications && count($this->notifications) > 0 ) {
			
			$negatives = array();
			for ( $i=0; $i<count($this->notifications); $i++ ) {
				
				$note = array_pop($this->notifications);
				if ( $note->is_positive == 1 ) {
					$this->encourage = '<i class="fa fa-smile-o"></i> ' . $note->message;					
				}
				else {
					array_push ( $negatives, $note );
				}
			}
			$this->notifications = array_merge ( $this->notifications, $negatives );
		}
		
		if ( !$this->encourage ) {
			if ( $this->totalBadges > 0 ) {
				error_log ( "Getting target" );
				$target = Biodiv\SchoolCommunity::getStudentTarget();
				$errMsg = print_r ( $target, true );
				error_log ( "StudentDashboard got target: " . $errMsg );
				if ( $target ) {
					$this->encourage = '<i class="fa fa-info-circle"></i> ' . JText::_("COM_BIODIV_STUDENTDASHBOARD_TO_REACH") . ' ' . $target->module . ' ' .
						$target->seq . ' <i class="fa fa-star"></i> ' . ' ' . $target->badgeGroup . ' ' . 
						JText::_("COM_BIODIV_STUDENTDASHBOARD_YOU_NEED") . ' ' . $target->pointsNeeded . ' ' . 
						JText::_("COM_BIODIV_STUDENTDASHBOARD_MORE_POINTS");
				}
			}
		}
		
		if ( !$this->encourage ) {
			$this->encourage = '<i class="fa fa-info-circle"></i> ' . Biodiv\SchoolCommunity::getEncouragement();
		}
		
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

