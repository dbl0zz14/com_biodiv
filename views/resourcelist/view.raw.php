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
class BioDivViewResourceList extends JViewLegacy
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
		
		$this->isEcologist = false;
		
		if ( $this->personId ) {
			
			$this->isEcologist = Biodiv\SchoolCommunity::isEcologist();
	
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->heading = "";
			
			$this->setId = $input->getInt('set_id', 0);
			
			$this->getFav = $input->getInt('fav', 0);
			
			$this->getLatest = $input->getInt('latest', 0);
			
			$this->getApprove = $input->getInt('approve', 0);
			
			$this->student = $input->getInt('student', 0);
			
			$this->resType = $input->getInt('type', 0);
			
			$this->searchStr = $input->getString('search', 0);
			
			$this->includeSet = false;
			$this->includeDoneTasks = false;
			
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
										
			$errMsg = print_r ( $this->shareStatus, true );
			error_log ( "Share status: " );
			error_log ( $errMsg );
			
								
			$this->shareOptions = array(Biodiv\SchoolCommunity::PERSON=>JText::_("COM_BIODIV_RESOURCELIST_SHARE_PRIVATE"),
										Biodiv\SchoolCommunity::SCHOOL=>JText::_("COM_BIODIV_RESOURCELIST_SHARE_SCHOOL"),
										Biodiv\SchoolCommunity::COMMUNITY=>JText::_("COM_BIODIV_RESOURCELIST_SHARE_COMMUNITY"));
						
			
			if ( $this->setId ) {
				
				$this->resourceSet = new Biodiv\ResourceSet($this->setId);
				
				$this->resourceFiles = $this->resourceSet->getFiles();
				
				$this->heading = $this->resourceSet->getSetName();
				
			}
			else if ( $this->getFav ) {
				
				$this->resourceFiles = Biodiv\ResourceFile::getFavResources();
				
			}
			else if ( $this->getLatest ) {
				
				$this->setId = Biodiv\ResourceSet::getLastSetId();
				
				$this->resourceSet = new Biodiv\ResourceSet($this->setId);
				
				$this->resourceFiles = $this->resourceSet->getFiles();
				
				$this->heading = $this->resourceSet->getSetName();
				
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
				
				error_log ("Got resource type " . $this->resType );
				
				$this->resourceFiles = Biodiv\ResourceFile::getResourcesByType( $this->resType );
				
			}
			else if ( $this->searchStr ) {
				
				error_log ("Got search string " . $this->searchStr );
				
				$this->resourceFiles = Biodiv\ResourceFile::searchResources( $this->searchStr );
				
			}
			else {
				
				// If no parameters get the default resources for this person
				// Default is any pinned resources.
				// If no pinned resources, get last upload.
				//$this->setId = Biodiv\ResourceSet::getLastSetId();
				$this->resourceFiles = Biodiv\ResourceFile::getPinnedResources();
				
				$this->heading = JText::_("COM_BIODIV_RESOURCELIST_PINNED_RESOURCES");
				
			}
		
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>