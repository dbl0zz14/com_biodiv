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
class BioDivViewHelpPage extends JViewLegacy
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
			
		$this->type = $input->getStr('type', 0);
		
		$this->schoolHelpLink = "bes-help";
		
		// $this->introLink = "";
		// $this->badgesLink = "";
		// $this->communityLink = "";
		// $this->faqsLink = "";
		// $this->teacherLink = "";
		
		// $allIcons = json_decode(getSetting("school_icons"));
		// $this->badgeSchemeIcon = $allIcons->all_badges;
		// $this->communityIcon = $allIcons->all_posts;
				
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

