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
	
	$schoolSettings = getSetting ( "school_icons" );
	$settingsObj = json_decode ( $schoolSettings );
	$this->bookmarkedImg = "";
	if (property_exists($settingsObj, 'bookmarked')) {
		$this->bookmarkedImg = $settingsObj->bookmarked;
	}
	$this->myUploadsImg = "";
	if (property_exists($settingsObj, 'my_uploads')) {
		$this->myUploadsImg = $settingsObj->my_uploads;
	}
	$this->newImg = "";
	if (property_exists($settingsObj, 'new_resources')) {
		$this->newImg = $settingsObj->new_resources;
	}
	$this->featuredImg = "";
	if (property_exists($settingsObj, 'featured')) {
		$this->featuredImg = $settingsObj->featured;
	}
			
			
	// Get the resource types
	$allResourceTypes = Biodiv\ResourceFile::getResourceTypes();
	$this->resourceTypes = array_slice($allResourceTypes, 0, 7);
	
	//$this->myTotalPoints = Biodiv\Task::getTotalUserPoints();
	
	$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
	$this->schoolPoints = 0;
	$this->mySchoolId = 0;
	$this->mySchoolName = "";
	$this->mySchoolRole = 0;
	$this->featured = array();
	
	if ( $this->schoolUser ) {
		
		$this->helpOption = codes_getCode ( "resourcehub", "beshelp" );
		
		$this->mySchoolId = $this->schoolUser->school_id;
		$this->mySchoolName = $this->schoolUser->school;
		$this->mySchoolRole = $this->schoolUser->role_id;
		
		$searchResults = Biodiv\ResourceFile::getPinnedResources(null, 1, 1);
		//$searchResults = Biodiv\ResourceFile::getPinnedResources( $this->filter, $this->page );
				
		$this->featured = $searchResults->resources;
		
		
	}

			
    // Display the view
    parent::display($tpl);
  }
}



?>