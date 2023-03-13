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
class BioDivViewSchoolHelp extends JViewLegacy
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
		
		$this->helpOption = codes_getCode ( "schoolhelp", "beshelp" );
		
		$this->studentIntroLink = "bes-help-page?type=studentintro";
		$this->studentBadgesLink = "bes-help-page?type=studentbadges";
		$this->studentCommunityLink = "bes-help-page?type=studentcommunity";
		$this->studentFaqsLink = "bes-help-page?type=studentfaqs";
		$this->introLink = "bes-help-page?type=intro";
		$this->badgesLink = "bes-help-page?type=badges";
		$this->communityLink = "bes-help-page?type=schoolcommunity";
		$this->faqsLink = "bes-help-page?type=faqs";
		$this->contactLink = "bes-help-page?type=contact";
		$this->teacherZoneLink = "bes-help-page?type=teacherzone";
		$this->schemeLink = "bes-help-page?type=badgescheme";
		$this->adminLink = "bes-help-page?type=schooladmin";
		$this->progressLink = "bes-help-page?type=studentprogress";
		$this->workLink = "bes-help-page?type=schoolwork";
		$this->resourceHubLink = "bes-help-page?type=resourcehub";
		$this->resourceSetLink = "bes-help-page?type=resourceset";
		$this->resourceLink = "bes-help-page?type=resource";
		
		$allIcons = json_decode(getSetting("school_icons"));
		$this->badgeSchemeIcon = $allIcons->all_badges;
		$this->communityIcon = $allIcons->all_posts;
				
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

