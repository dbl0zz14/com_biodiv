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
class BioDivViewSchoolUsers extends JViewLegacy
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
	
	
	
	$this->helpOption = codes_getCode ( "schoolusers", "beshelp" );
	
	
	// Check whether first load
	$this->firstLoad = $this->schoolUser->new_user == 1;
	
	$this->notifications = null;
	
	$this->mySchools = array();
	if ( $this->isAdmin ) {
		$this->allSchools = Biodiv\SchoolCommunity::getAllSchools();
		$this->ecologists = Biodiv\SchoolCommunity::getEcologists();
		//$this->ecologists = Biodiv\SchoolCommunity::getUsersByRole(Biodiv\SchoolCommunity::ECOLOGIST_ROLE);
		
	}
	
    // Display the view
    parent::display($tpl);
  }
}



?>