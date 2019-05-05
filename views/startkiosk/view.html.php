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
	  $this->photo_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id', 0);

	  $this->classify_only_project = 
	    (int)$app->getUserStateFromRequest('com_biodiv.classify_only_project', 'classify_only_project', 0);
		
	  //echo "BioDivViewClassify, this->classify_only_project = ", $this->classify_only_project;
	  
	  $this->my_project = 
	    $app->getUserStateFromRequest('com_biodiv.my_project', 'my_project', 0);
		
	  $this->project_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
	  if ( !$this->project_id ) {
		  $this->project_id = codes_getCode($this->my_project, "project" );
	  }
		
	  $this->project = projectDetails($this->project_id);
	
	  $this->user_key = 
	    $app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);
		
	  if ( !$this->user_key ) {
		  $this->user_key = JRequest::getString("user_key");
	  }
	  
	  error_log("Kiosk View: user_key = " . $this->user_key);
		
	  
	
		
	  // Check the user has access as this view can be loaded from project pages as well as Spotter status page
	  /*
	  if ( !userID() ) {
		$app = JFactory::getApplication();
		$message = "Please log in before classifying.";
		$url = "".BIODIV_ROOT."&view=kiosk";
		if ( $this->classify_only_project ) $url .= "&classify_only_project=" . $this->classify_only_project;
		if ( $this->my_project ) $url .= "&my_project=" . $this->my_project;
		if ( $this->self ) $url .= "&classify_self=" . $this->self;
		$url = urlencode(base64_encode($url));
		$url = JRoute::_('index.php?option=com_users&view=login&return=' . $url );
		$app->redirect($url, $message);
	  }

	  
	  // Check the user can access this project, if a project is specified.
	  if ( $this->my_project ) {
		  $fields = new StdClass();
		  $fields->project_id = $this->project_id;
		  if ( !canClassify("project", $fields) ) {
			$app = JFactory::getApplication();
			$message = "Sorry you do not have access to classify on this project.";
			$url = "".BIODIV_ROOT."&view=projecthome";
			$url .= "&project_id=" . $this->project_id;
			$app->redirect($url, $message);
			
		  }
	  }
	  */
	  
	  // get the url for the project image
	  $this->projectImageUrl = projectImageURL($this->project_id);

	  // Display the view
	  parent::display($tpl);
    }
}



?>