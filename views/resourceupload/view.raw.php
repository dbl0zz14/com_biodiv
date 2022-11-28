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
class BioDivViewResourceUpload extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->resourceTypes = null;
		
		$this->personId = (int)userID();

		if ( !$this->personId ) {
			error_log("ResourceUpload view: no person id" );
			$this->resourceTypes = array();
		}
		else {
			
			// Get the resource types
			//$this->resourceTypes = Biodiv\ResourceFile::getResourceTypes();
			
			// Get the possible tags
			//$this->tagGroups = Biodiv\ResourceFile::getResourceTags();
			
			// What school id to use?
			//$this->schoolRoles = Biodiv\SchoolCommunity::getSchoolRoles();
			
			//$this->isEcologist = Biodiv\SchoolCommunity::isEcologist();
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>