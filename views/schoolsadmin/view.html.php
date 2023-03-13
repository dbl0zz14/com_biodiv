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
class BioDivViewSchoolsAdmin extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
   */
  
  public function display($tpl = null) 
  {
    $app = JFactory::getApplication();
	
	// Check user is an admin and get schools
	$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	$this->personId = $this->schoolUser->person_id;
	$this->isAdmin = $this->schoolUser->role_id == Biodiv\SchoolCommunity::ADMIN_ROLE;
	
	$this->helpOption = codes_getCode ( "schoolsadmin", "beshelp" );
	
	
	// Check whether first load
	$this->firstLoad = $this->schoolUser->new_user == 1;
	
	$this->notifications = null;
	
	$this->newSchools = array();
	
	if ( $this->isAdmin ) {
		
		$this->allSchools = Biodiv\SchoolCommunity::getAllSchools();
		
		$this->newSchools = Biodiv\SchoolCommunity::getUnapprovedSchools( $this->schoolUser );
				
	}
	
    // Display the view
    parent::display($tpl);
  }
}



?>