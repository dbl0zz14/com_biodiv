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
class BioDivViewSchoolCommunity extends JViewLegacy
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
		
		$input = JFactory::getApplication()->input;
		
		$this->badgeId = $input->getInt('badge', 0);
		$this->classId = $input->getInt('class_id', 0);
			
			
		$this->helpOption = codes_getCode ( "schoolcommunity", "beshelp" );
			
		$this->community = new Biodiv\SchoolCommunity();
		$this->schools = $this->community->getSchools();
		
		$allIcons = json_decode(getSetting("school_icons"));
		$this->allPostsImg = $allIcons->all_posts;
		$this->allPostsActiveImg = $allIcons->all_posts_inv;
		
		$this->searchSchoolsImg = $allIcons->more_filters;
		$this->searchSchoolsActiveImg = $allIcons->more_filters_inv;
				
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

