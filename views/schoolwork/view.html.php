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
* Browse tasks by badge group
*
* @since 0.0.1
*/
class BioDivViewSchoolWork extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->personId = (int)userID();
		
		$this->badgeGroupId = 0;
		$this->data = "";

		if ( !$this->personId ) {
			
			error_log("SchoolWork view: no person id" );
			
		}
		else {
			
			$this->helpOption = codes_getCode ( "schoolwork", "beshelp" );
			$this->educatorPage = "bes-educator-zone";
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
			$app = JFactory::getApplication();
			$input = $app->input;
			$this->page = $input->getInt('page', 1);
			$this->searchStr = $input->getString('search', 0);
			
			if ( strlen($this->searchStr) == 0 ) {
				$this->searchStr = 0;
			}
			
			$settingsObj = json_decode(getSetting("school_icons"));
		
			$this->showAllImg = "";
			if (property_exists($settingsObj, 'clear_filters')) {
				$this->showAllImg = $settingsObj->clear_filters;
			}
			$this->showAllActiveImg = "";
			if (property_exists($settingsObj, 'clear_filters_inv')) {
				$this->showAllActiveImg = $settingsObj->clear_filters_inv;
			}
			$this->textSearchImg = "";
			if (property_exists($settingsObj, 'text_search')) {
				$this->textSearchImg = $settingsObj->text_search;
			}
			$this->textSearchActiveImg = "";
			if (property_exists($settingsObj, 'text_search_inv')) {
				$this->textSearchActiveImg = $settingsObj->text_search_inv;
			}
			
			if ( $this->searchStr ) {
				$this->activeBtn = "search";
			}
			else {
				$this->activeBtn = "all";
			}
			$schoolWork = Biodiv\ResourceSet::getSchoolWork ( $this->schoolUser, $this->searchStr, $this->page );
			
			$this->numSets = $schoolWork->total;
			$this->workSets = $schoolWork->sets;
			
			$numPerPage = Biodiv\ResourceSet::NUM_PER_PAGE;
			$this->pageLabelsShown = 6;
			$this->numPages = ceil($this->numSets/$numPerPage);
		}

		// Display the view
		parent::display($tpl);
    }
}



?>