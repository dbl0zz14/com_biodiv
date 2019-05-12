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
class BioDivViewStartkiosk extends JViewLegacy
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
	  
	  $this->my_project =
	    (int)$app->getUserStateFromRequest('com_biodiv.my_project', 'my_project', 0);
		
	  $this->project_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
	  if ( !$this->project_id ) {
		  $this->project_id = codes_getCode($this->my_project, "project" );
	  }
	  
	  if ( !$this->project_id ) die ("no project id given" );
		
	  $this->project = projectDetails($this->project_id);
	
	  $this->user_key = 
	    $app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);
		
	  if ( !$this->user_key ) {
		  $this->user_key = JRequest::getString("user_key");
		  $app->setUserState('com_biodiv.user_key', $this->user_key);
	  }
	  
	  error_log("Kiosk View: user_key = " . $this->user_key);
		
	  // Sidebar should start hidden, start classify count and associated animal ids again
	  
	  $app->setUserState('com_biodiv.toggled', 1);
	  $app->setUserState('com_biodiv.classify_count', 0);
	  $app->setUserState('com_biodiv.all_animal_ids', 0);
	  
	  // get the url for the project image
	  $this->projectImageUrl = projectImageURL($this->project_id);

	  // Display the view
	  parent::display($tpl);
    }
}



?>