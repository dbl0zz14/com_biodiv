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
class BioDivViewResourceAdd extends JViewLegacy
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
			error_log("ResourceAdd view: no person id" );
			$this->resourceTypes = array();
		}
		else {
			
			$input = JFactory::getApplication()->input;
			$this->setId = $input->getInt('set', 0);
			
			$this->isStaff = Biodiv\SchoolCommunity::isStaff();
			
			$app = JFactory::getApplication();
			$app->setUserState('com_biodiv.resource_set_id', $this->setId);
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>