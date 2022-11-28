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
class BioDivViewEcologistDashboard extends JViewLegacy
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
	
	// Check user is an ecologist and get schools
	$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
	
	if ( $this->schoolUser ) {
		$this->schoolId = $this->schoolUser->school_id;
		$this->schoolName = $this->schoolUser->school;
		$this->mySchoolRole = $this->schoolUser->role_id;
	}
		
	$this->isEcologist = $this->mySchoolRole == Biodiv\SchoolCommunity::ECOLOGIST_ROLE;
	
	$this->helpOption = codes_getCode ( "ecologistdashboard", "beshelp" );
	
	$this->mySchools = array();
	if ( $this->isEcologist ) {
		$this->mySchools = $this->schoolUser->schoolArray;
	}
	
	// Check whether first load
	$this->firstLoad = $this->schoolUser->new_user == 1;
	
	$this->notifications = null;
	
	if ( $this->firstLoad ) {
		Biodiv\SchoolCommunity::setNewUser(0);
		Biodiv\SchoolCommunity::addNotification(JText::_("COM_BIODIV_DASHSPOTTER_WELCOME_NOTE"));
		
		$this->avatars = Biodiv\SchoolCommunity::getAvatars();
		// Do the first unlock to get the badges through
		$this->completedBadgeGroups = Biodiv\Badge::unlockEcologistBadges();
	}
	
	
	// Check whether any system calculated tasks have been completed.
	Biodiv\Task::checkSystemTasks();
	
	// Ecologists don't have locked badges but this will copy any new badges for them
	Biodiv\Badge::unlockEcologistBadges();
	
	if ( !$this->firstLoad ) {
		$this->notifications = Biodiv\SchoolCommunity::getNotifications();
		Biodiv\SchoolCommunity::notificationsSeen();
		
		$messageList = new Biodiv\MessageList();
		$this->numNewMessages = $messageList->newMessageCount();
	}
	
	$this->myTotalPoints = Biodiv\Task::getTotalUserPoints();
	
	

    // Display the view
    parent::display($tpl);
  }
}



?>