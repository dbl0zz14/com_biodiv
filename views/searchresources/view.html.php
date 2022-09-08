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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewSearchResources extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "ResourceList display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("searchresources");
		
		$this->personId = userID();
		
		$this->isEcologist = false;
		
		if ( $this->personId ) {
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
			
			$this->isEcologist = Biodiv\SchoolCommunity::isEcologist();
			
			$this->helpOption = codes_getCode ( "searchresources", "beshelp" );
			
			$this->resourceTypes = Biodiv\ResourceFile::getResourceTypes();
			
			$this->allTags = Biodiv\ResourceFile::getResourceTags();
			
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
			$this->newResourcesImg = "";
			if (property_exists($settingsObj, 'new_resources')) {
				$this->newResourcesImg = $settingsObj->new_resources;
			}
			$this->featuredImg = "";
			if (property_exists($settingsObj, 'featured')) {
				$this->featuredImg = $settingsObj->featured;
			}
			$this->moreFiltersImg = "";
			if (property_exists($settingsObj, 'more_filters')) {
				$this->moreFiltersImg = $settingsObj->more_filters;
			}
			$this->clearFiltersImg = "";
			if (property_exists($settingsObj, 'clear_filters')) {
				$this->clearFiltersImg = $settingsObj->clear_filters;
			}
			
			$this->totalNumResources = 0;
			$this->numPages = 1;
			$this->href = "";
				
				
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->heading = "";
			
			$this->setId = $input->getInt('set_id', 0);
			
			$this->getFav = $input->getInt('fav', 0);
			
			$this->getLatest = $input->getInt('latest', 0);
			
			$this->getMine = $input->getInt('mine', 0);
			
			$this->getNew = $input->getInt('new', 0);
			
			$this->getApprove = $input->getInt('approve', 0);
			
			$this->student = $input->getInt('student', 0);
			
			$this->resType = $input->getInt('type', 0);
			
			$this->searchStr = $input->getString('search', 0);
			
			//error_log ("Search str = " . $this->searchStr );
			
			$this->page = $input->getInt('page', 1);
			
			$jsonFilter = $input->getString('filter',0);
			
			$this->filter = null;
			if ( $jsonFilter ) {
				$this->filter = json_decode ( $jsonFilter );
			}
			
			$errMsg = print_r ( $this->filter, true );
			error_log ( "Filter array: " . $errMsg );
			
			$this->includeSet = false;
			$this->includeDoneTasks = false;
			$this->noArgs = false;
			
			$this->resourceFiles = array();
			$this->doneTasksNoFiles = array();
			
			$this->pinOptions = array('Private <h4 class="text-right"><i class="fa fa-lock fa-lg"></i></h4>', 
										'Share with my school <h4 class="text-right"><i class="fa fa-school fa-lg"></i></h4>', 
										'Share with community <h4 class="text-right"><i class="fa fa-globe fa-lg"></i></h4>', 
										'Share with ecologists <h4 class="text-right"><i class="fa fa-lock fa-lg"></i></h4>');
										/*, 
										"Pin for myself", 
										"Pin for students",
										"Pin for teachers", 
										"Pin for ecologists");*/
									
			$this->shareStatus = array(Biodiv\SchoolCommunity::PERSON=>"fa fa-lock fa-lg",
										Biodiv\SchoolCommunity::SCHOOL=>"fa fa-building-o fa-lg",
										Biodiv\SchoolCommunity::COMMUNITY=>"fa fa-globe fa-lg",
										Biodiv\SchoolCommunity::ECOLOGISTS=>"fa fa-leaf fa-lg");
										
			// $errMsg = print_r ( $this->shareStatus, true );
			// error_log ( "Share status: " );
			// error_log ( $errMsg );
			
								
			$this->shareOptions = array(Biodiv\SchoolCommunity::PERSON=>$this->translations['share_private']['translation_text'],
										Biodiv\SchoolCommunity::SCHOOL=>$this->translations['share_school']['translation_text'],
										Biodiv\SchoolCommunity::COMMUNITY=>$this->translations['share_community']['translation_text']);			
			
			//error_log ("Getting const" );
			//$testConst = Biodiv\SchoolCommunity::PERSON;
			//error_log ( "Test const = " . $testConst );
			
			if ( $this->setId ) {
				
				$this->resourceSet = new Biodiv\ResourceSet($this->setId);
				
				$this->resourceFiles = $this->resourceSet->getFiles();
				
				$this->heading = $this->resourceSet->getSetName();
				
			}
			else if ( $this->getFav ) {
				
				$searchResults = Biodiv\ResourceFile::getFavResources( $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceFiles = $searchResults->resources;
				
				$numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = $this->translations['search_page']['translation_text'].'?fav=1';
				
			}
			else if ( $this->getLatest ) {
				
				$this->setId = Biodiv\ResourceSet::getLastSetId();
				
				$this->resourceSet = new Biodiv\ResourceSet($this->setId);
				
				$this->resourceFiles = $this->resourceSet->getFiles();
				
				$this->heading = $this->resourceSet->getSetName();
				
			}
			else if ( $this->getMine ) {
				
				//$this->resourceFiles = Biodiv\ResourceFile::getMyResources();
				
				$searchResults = Biodiv\ResourceFile::getMyResources( $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceFiles = $searchResults->resources;
				
				$numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = $this->translations['search_page']['translation_text'].'?mine=1';
			}
			else if ( $this->getNew ) {
				
				//$this->resourceFiles = Biodiv\ResourceFile::getNewResources();
				
				$searchResults = Biodiv\ResourceFile::getNewResources( $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceFiles = $searchResults->resources;
				
				$numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = $this->translations['search_page']['translation_text'].'?new=1';
				
			}
			else if ( $this->getApprove ) {
				
				// Should have tasklist view and refactor the common code
				$this->resourceFiles = Biodiv\ResourceFile::getResourcesForApproval();
				$this->includeSet = true;
				$this->includeDoneTasks = true;
				
				$this->doneTasksNoFiles = Biodiv\Task::getNoFileTasksForApproval();
				
			}
			else if ( $this->student ) {
				
				// Should have tasklist view and refactor the common code
				$this->resourceFiles = Biodiv\ResourceFile::getStudentResources();
				$this->includeSet = true;
				$this->includeDoneTasks = true;
				
				
			}
			else if ( $this->resType ) {
				
				//error_log ("Got resource type " . $this->resType );
				
				$searchResults = Biodiv\ResourceFile::getResourcesByType( $this->resType, $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceFiles = $searchResults->resources;
				
				$numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = $this->translations['search_page']['translation_text'].'?type='.$this->resType;
				
			}
			else if ( $this->searchStr ) {
				
				//error_log ("Got search string " . $this->searchStr );
				
				$searchResults = Biodiv\ResourceFile::searchResources( $this->searchStr, $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceFiles = $searchResults->resources;
				
				$numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = $this->translations['search_page']['translation_text'].'?search='.$this->searchStr;
				
			}
			else {
				
				$this->noArgs = true;
				
				// If no parameters get the default resources for this person
				// Default is any pinned resources.
				// If no pinned resources, get last upload.
				//$this->setId = Biodiv\ResourceSet::getLastSetId();
				$searchResults = Biodiv\ResourceFile::getPinnedResources( $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceFiles = $searchResults->resources;
				
				$numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = $this->translations['search_page']['translation_text'];
				
				$this->heading = $this->translations['pinned_resources']['translation_text'];
				
			}
		
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>