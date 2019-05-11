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
class BioDivViewFeedback extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "Feedback display function called" );
      // Assign data to the view
	  $this->person_id = (int)userID();
	  
	  if ( $this->person_id ) {
		  $app = JFactory::getApplication();
		  
		  //$this->photo_id = (int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id');

		  //$this->animal_id =
			//(int)$app->getUserStateFromRequest('com_biodiv.animal_id', 'animal_id');
		
		  //$this->my_project = 
			//$app->getUserStateFromRequest('com_biodiv.my_project', 'my_project', 0);
			
		  //error_log("Kiosk View: my_project = " . $this->my_project);
		
		  $this->project_id =
			(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
			
		  //if ( !$this->project_id ) {
			//  $this->project_id = codes_getCode($this->my_project, "project" );
		 // }
			
		  error_log("Kiosk View: project_id = " . $this->project_id);
		
		  //$this->project = projectDetails($this->project_id);
		  
		  $this->user_key =
			$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);
			
		  if ( !$this->user_key ) {
			  $this->user_key = JRequest::getString("user_key");
		  }
		  
		  error_log("Kiosk View: user_key = " . $this->user_key);
		  
	  
			
		  //error_log("Singletag view, animal_ids = " .   $app->getUserStateFromRequest('com_biodiv.animal_ids', 'animal_ids') );
			
		  /*
		  if ( $this->animal_id ) {

			$db = JDatabase::getInstance(dbOptions());

			$query = $db->getQuery(true);
			$query->select("animal_id, species, gender, age, number")
			  ->from("Animal")
			  ->where("animal_id = ".$this->animal_id);

			$db->setQuery($query);
			$this->animal = $db->loadObject();
		  }
		  */
	  }
	  else {
		  // back to start kiosk
	  }
	  
	  // get the url for the project image
	  $this->projectImageUrl = projectImageURL($this->project_id);


	  // Display the view
	  parent::display($tpl);
    }
}



?>