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
class BioDivViewAdminDashboard extends JViewLegacy
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
	$this->isAdmin = Biodiv\SchoolCommunity::isAdmin();
	
	
	
	$this->helpOption = codes_getCode ( "admindashboard", "beshelp" );
	
	
	// Check whether first load
	$this->firstLoad = $this->schoolUser->new_user == 1;
	
	$this->notifications = null;
	
	if ( $this->firstLoad ) {
		Biodiv\SchoolCommunity::setNewUser(0);
		Biodiv\SchoolCommunity::addNotification(JText::_("COM_BIODIV_ADMINDASHBOARD_WELCOME_NOTE"));
		
		$this->avatars = Biodiv\SchoolCommunity::getAvatars();
	}
	else {
		
		// $this->modules = Biodiv\Module::getModules();
		// $this->moduleIds = array_keys ( $this->modules );
		$this->lockLevels = Biodiv\Badge::getLockLevels ( $this->schoolUser );
		
		$adminSettings = getSetting("school_admin");
		$adminSettingsObj = json_decode($adminSettings);
		$this->resourceReportId = $adminSettingsObj->resourcereport;
		
		$this->mySchools = array();
		$this->adminSummary = null;
		if ( $this->isAdmin ) {
			$this->mySchools = Biodiv\SchoolCommunity::getAllSchools();
			$this->adminSummary = Biodiv\SchoolCommunity::getAdminSummary();
			$this->reports = BiodivReport::listSchoolReports();
			
			$errMsg = print_r ( $this->adminSummary, true );
			error_log ( "Admin summary: " . $errMsg );
		}
	}
	
	
	$this->waitText = JText::_("COM_BIODIV_ADMINDASHBOARD_WAIT_TEXT");
	$this->doneText = JText::_("COM_BIODIV_ADMINDASHBOARD_DONE_TEXT");
	$this->genText = JText::_("COM_BIODIV_ADMINDASHBOARD_GEN_TEXT");


    // Display the view
    parent::display($tpl);
  }
}



?>