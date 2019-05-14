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
	  
	  if ( !$this->person_id ) {
		  error_log("Feedback view: no person id" );
		  die("Feedback view no person id");
	  }
	  

	  $app = JFactory::getApplication();
	  
	  $this->project_id =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
	  error_log("Feedback View: project_id = " . $this->project_id);
	
	  $this->user_key =
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);
		
	  if ( !$this->user_key ) {
		  $this->user_key = JRequest::getString("user_key");
	  }
	  
	  
	  error_log("Feedback View: user_key = " . $this->user_key);
	  
	  $this->all_animal_ids =
		$app->getUserStateFromRequest('com_biodiv.all_animal_ids', 'all_animal_ids', 0);
		
	  error_log("Feedback view, all_animal_ids = " .   $this->all_animal_ids );
	
	  $this->all_animals = 0;
	  
	  if ( $this->all_animal_ids ) {

		$all_animals = explode("_", $this->all_animal_ids);
	  
		$db = JDatabase::getInstance(dbOptions());

		$query = $db->getQuery(true);
		$query->select("A.animal_id as id, A.species as species, A.gender as gender, A.age as age, A.number as number, O.struc as struc, O.option_name as name, OD.value as png_image")
		  ->from("Animal A")
		  ->innerJoin("Options O on A.species = O.option_id")
		  ->leftJoin("OptionData OD on O.option_id = OD.option_id and OD.data_type='png'")
		  ->where("A.animal_id in (" . implode(",", $all_animals) . ")");

		$db->setQuery($query);
		$this->all_animals = $db->loadObjectList();
	  }
	  
	  // get the url for the project image
	  $this->projectImageUrl = projectImageURL($this->project_id);


	  // Display the view
	  parent::display($tpl);
    }
}



?>