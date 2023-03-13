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
class BioDivViewBadgeScheme extends JViewLegacy
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
		
		$this->helpOption = codes_getCode ( "badgescheme", "beshelp" );
		
		$this->educatorPage = "bes-educator-zone";
		$this->badgesLink = "bes-badges";
		
		$this->badges = Biodiv\Badge::getBadgeScheme ( $this->schoolUser );
		
		$this->awards = Biodiv\Award::getCollectedAwards ( $this->schoolUser );	
		
		$this->badgeGroups = Biodiv\BadgeGroup::getBadgeGroups();
		
		$this->modules = Biodiv\Module::getModules();
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

