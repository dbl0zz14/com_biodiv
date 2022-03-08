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
* HTML View class for the Resource hub - dashboard style, view, upload and download files 
*
* @since 0.0.1
*/
class BioDivViewResourceHub extends JViewLegacy
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
	$this->translations = getTranslations("resourcehub");
	
	// Get the user type and school.
	
	// Get the resource types
	$this->resourceTypes = codes_getList ( "resource" );
	
	$this->myTotalPoints = Biodiv\Task::getTotalUserPoints();
	
/*	
	$schoolRoles = Biodiv\SchoolCommunity::getSchoolRoles();
	if ( count($schoolRoles) > 0 ) {
		$this->mySchoolId = $schoolRoles[0]["school_id"];
		$this->mySchoolName = $schoolRoles[0]["name"];
		$this->mySchoolRole = $schoolRoles[0]["role_id"];
	}
*/
	$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
	$this->schoolPoints = 0;
	$this->mySchoolId = 0;
	$this->mySchoolName = "";
	$this->mySchoolRole = 0;
	
	if ( $this->schoolUser ) {
		
		$this->helpOption = codes_getCode ( "resourcehub", "beshelp" );
		
		$this->mySchoolId = $this->schoolUser->school_id;
		$this->mySchoolName = $this->schoolUser->school;
		$this->mySchoolRole = $this->schoolUser->role_id;
	}

			
    // Display the view
    parent::display($tpl);
  }
}



?>