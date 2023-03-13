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
class BioDivViewTeacherZone extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
   */
  
  public function display($tpl = null) 
  {
    $this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	if ( $this->schoolUser ) {
		$this->personId = $this->schoolUser->person_id;
	}
	else {
		$this->personId = (int)userID();
	}
		
	if ( $this->personId ) {
		
		$this->helpOption = codes_getCode ( "teacherzone", "beshelp" );
			
		$this->resourceHubLink = "bes-search-resources";
		$this->badgeSchemeLink = "bes-badge-scheme";
		$this->schoolAdminLink = "bes-school-admin";
		$this->studentProgressLink = "bes-student-progress";
		$this->workLink = "bes-school-work";
		
		$allIcons = json_decode(getSetting("school_icons"));
		$this->badgeSchemeIcon = $allIcons->all_badges;
					
		
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

