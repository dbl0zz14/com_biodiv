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
class BioDivViewProjectData extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

    public function display($tpl = null) 
    {
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		
		
		
		$app = JFactory::getApplication();
		$this->project_id =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id');
		
		$this->months = (int)JRequest::getVar('months', 6);
		
		//$this->months = 8;
	  
	    // determine the interval
		if ( $this->months > 12 ) $this->interval = 4;
		else if ( $this->months > 6 ) $this->interval = 2;
		else $this->interval = 1;
		$this->data = projectData ( $this->project_id, $this->months, $this->interval );

		// Display the view
		parent::display($tpl);
    }
}



?>