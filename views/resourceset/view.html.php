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
class BioDivViewResourceSet extends JViewLegacy
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
			
			$input = JFactory::getApplication()->input;
			$this->setId = $input->getInt('set_id', 0);
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
			
			//$this->isEcologist = Biodiv\SchoolCommunity::isEcologist();
			
			$this->helpOption = codes_getCode ( "searchresources", "beshelp" );
			
			$this->badgeSchemeLink = "bes-badge-scheme";
			
			$this->canEdit = false;
			
			if ( $this->setId ) {
				
				$this->resourceSet = Biodiv\ResourceSet::createFromId ( $this->setId );
				
				$this->resourceFiles = $this->resourceSet->getFiles();
				
				$this->canEdit = Biodiv\ResourceSet::canEdit( $this->setId );
				
				$this->gotMessages = totalUploadMessages() > 0;
			}
			
			
		}
		
		// Display the view
		parent::display($tpl);
		
    }
}



?>