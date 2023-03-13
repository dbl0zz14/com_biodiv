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
		$this->personId = userID();
		
		//$this->isEcologist = false;
		
		if ( $this->personId ) {
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
			
			//$this->isEcologist = Biodiv\SchoolCommunity::isEcologist();
			
			$this->helpOption = codes_getCode ( "resourcehub", "beshelp" );
			
			$this->educatorPage = "bes-educator-zone";
			$this->badgeSchemeLink = "bes-badge-scheme";
			
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
			$this->featuredActiveImg = "";
			if (property_exists($settingsObj, 'featured_inv')) {
				$this->featuredActiveImg = $settingsObj->featured_inv;
			}
			$this->moreFiltersImg = "";
			if (property_exists($settingsObj, 'more_filters')) {
				$this->moreFiltersImg = $settingsObj->more_filters;
			}
			$this->moreFiltersActiveImg = "";
			if (property_exists($settingsObj, 'more_filters_inv')) {
				$this->moreFiltersActiveImg = $settingsObj->more_filters_inv;
			}
			$this->clearFiltersImg = "";
			if (property_exists($settingsObj, 'clear_filters')) {
				$this->clearFiltersImg = $settingsObj->clear_filters;
			}
			$this->clearFiltersActiveImg = "";
			if (property_exists($settingsObj, 'clear_filters_inv')) {
				$this->clearFiltersActiveImg = $settingsObj->clear_filters_inv;
			}
			$this->textSearchImg = "";
			if (property_exists($settingsObj, 'text_search')) {
				$this->textSearchImg = $settingsObj->text_search;
			}
			$this->textSearchActiveImg = "";
			if (property_exists($settingsObj, 'text_search_inv')) {
				$this->textSearchActiveImg = $settingsObj->text_search_inv;
			}
			
			$this->totalNumResources = 0;
			$this->numPages = 1;
			$this->href = "";
				
				
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->heading = "";
			
			$this->help = $input->getInt('help', 0);
			
			$this->setId = $input->getInt('set_id', 0);
			
			$this->getFav = $input->getInt('fav', 0);
			
			$this->getLatest = $input->getInt('latest', 0);
			
			$this->getMine = $input->getInt('mine', 0);
			
			$this->getNew = $input->getInt('new', 0);
			
			$this->badgeId = $input->getInt('badge', 0);
			
			$this->getApprove = $input->getInt('approve', 0);
			
			$this->student = $input->getInt('student', 0);
			
			$this->resType = $input->getInt('type', 0);
			
			$searchInput = $input->getString('search', 0);
			
			$this->searchStr = null;
			
			if ( $searchInput ) {
				$this->searchStr = filter_var($searchInput, FILTER_SANITIZE_STRING);
			}
			
			//error_log ("Search str = " . $this->searchStr );
			
			$this->page = $input->getInt('page', 1);
			
			$this->jsonFilter = $input->getString('filter',0);
			
			$this->filter = null;
			if ( $this->jsonFilter ) {
				$this->filter = json_decode ( $this->jsonFilter );
			}
			
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
										
			
								
			$this->shareOptions = array(Biodiv\SchoolCommunity::PERSON=>JText::_("COM_BIODIV_SEARCHRESOURCES_SHARE_PRIVATE"),
										Biodiv\SchoolCommunity::SCHOOL=>JText::_("COM_BIODIV_SEARCHRESOURCES_SHARE_SCHOOL"),
										Biodiv\SchoolCommunity::COMMUNITY=>JText::_("COM_BIODIV_SEARCHRESOURCES_SHARE_COMMUNITY"));			
			
			
			// if ( $this->setId ) {
				
				// $this->resourceSet = new Biodiv\ResourceSet($this->setId);
				
				// $this->resourceFiles = $this->resourceSet->getFiles();
				
				// $this->heading = $this->resourceSet->getSetName();
				
			// }
			// else if ( $this->getFav ) {
				
				// $searchResults = Biodiv\ResourceFile::getFavResources( $this->filter, $this->page );
				
				// $this->totalNumResources = $searchResults->total;
				// $this->resourceFiles = $searchResults->resources;
				
				// $numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				// $this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				// $this->href = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE").'?fav=1';
				
			// }
			// else if ( $this->getLatest ) {
				
				// $this->setId = Biodiv\ResourceSet::getLastSetId();
				
				// $this->resourceSet = new Biodiv\ResourceSet($this->setId);
				
				// $this->resourceFiles = $this->resourceSet->getFiles();
				
				// $this->heading = $this->resourceSet->getSetName();
				
			// }
			// else if ( $this->getMine ) {
				
				// $searchResults = Biodiv\ResourceFile::getMyResources( $this->filter, $this->page );
				
				// $this->totalNumResources = $searchResults->total;
				// $this->resourceFiles = $searchResults->resources;
				
				// $numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				// $this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				// $this->href = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE").'?mine=1';
			// }
			// else if ( $this->getNew ) {
				
				// $searchResults = Biodiv\ResourceFile::getNewResources( $this->filter, $this->page );
				
				// $this->totalNumResources = $searchResults->total;
				// $this->resourceFiles = $searchResults->resources;
				
				// $numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				// $this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				// $this->href = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE").'?new=1';
				
			// }
			// else if ( $this->getApprove ) {
				
				// // Should have tasklist view and refactor the common code
				// $this->resourceFiles = Biodiv\ResourceFile::getResourcesForApproval();
				// $this->includeSet = true;
				// $this->includeDoneTasks = true;
				
				// $this->doneTasksNoFiles = Biodiv\Task::getNoFileTasksForApproval();
				
			// }
			// else if ( $this->student ) {
				
				// // Should have tasklist view and refactor the common code
				// $this->resourceFiles = Biodiv\ResourceFile::getStudentResources();
				// $this->includeSet = true;
				// $this->includeDoneTasks = true;
				
				
			// }
			// else if ( $this->resType ) {
				
				// //error_log ("Got resource type " . $this->resType );
				
				// $searchResults = Biodiv\ResourceFile::getResourcesByType( $this->resType, $this->filter, $this->page );
				
				// $this->totalNumResources = $searchResults->total;
				// $this->resourceFiles = $searchResults->resources;
				
				// $numPerPage = Biodiv\ResourceFile::NUM_PER_PAGE;
				// $this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				// $this->href = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE").'?type='.$this->resType;
				
			// }
			if ( $this->badgeId ) {
				
				$searchResults = Biodiv\ResourceSet::getBadgeResourceSets( $this->schoolUser, $this->badgeId, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceSets = $searchResults->sets;
				
				$numPerPage = Biodiv\ResourceSet::NUM_PER_PAGE;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE").'?filter=["badge_'.$this->badgeId.'"]';
				
				$this->activeBtn = "filter";
				
			}
			else if ( $this->searchStr ) {
				
				//error_log ("Got search string " . $this->searchStr );
				
				$searchResults = Biodiv\ResourceSet::searchResourceSets( $this->schoolUser, $this->searchStr, $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceSets = $searchResults->sets;
				
				$numPerPage = Biodiv\ResourceSet::NUM_PER_PAGE;
				$this->pageLabelsShown = 6;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE").'?search='.$this->searchStr;
				
				$this->activeBtn = "search";
			}
			else {
				
				$this->noArgs = true;
				
				// If no parameters get all resources
				$searchResults = Biodiv\ResourceSet::searchResourceSets( $this->schoolUser, null, $this->filter, $this->page );
				
				$this->totalNumResources = $searchResults->total;
				$this->resourceSets = $searchResults->sets;
				
				$numPerPage = Biodiv\ResourceSet::NUM_PER_PAGE;
				$this->pageLabelsShown = 6;
				$this->numPages = ceil($this->totalNumResources/$numPerPage);
				
				$this->href = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE");
				
				$this->heading = JText::_("COM_BIODIV_SEARCHRESOURCES_PINNED_RESOURCES");
				
				if ( $this->filter ) {
					if ( (count($this->filter) == 1) and in_array("pin", $this->filter) ) {
						$this->activeBtn = "featured";
					}
					else {
						$this->activeBtn = "filter";
					}
				}
				else {
					$this->activeBtn = "all";
				}
				
			}
			
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>