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
class BioDivViewBadgeKioskFeedback extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		if ( $this->schoolUser ) {
			$this->personId = $this->schoolUser->person_id;
		}
		else {
			$this->personId = (int)userID();
		}
		
		if ( !$this->personId ) {
		  error_log("Feedback view: no person id" );
		  die("Feedback view no person id");
		}


		$app = JFactory::getApplication();
		$input = $app->input;

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		$this->badge = $input->getInt('badge', 0);
		$this->classId = $input->getInt('class_id', 0);


		// Get the project name for using in text.
		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);

		
		// Set the user state so doesn't pick up secomdary project unless asked to
		$app->setUserState('com_biodiv.classify_second_project', 0);


		$this->user_key =
			$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
		  $this->user_key = JRequest::getString("user_key");
		}


		$this->all_animal_ids =
		$app->getUserStateFromRequest('com_biodiv.all_animal_ids', 'all_animal_ids', 0);

		$this->all_animals = 0;

		if ( $this->all_animal_ids ) {

		$all_animals = explode("_", $this->all_animal_ids);

		$db = JDatabase::getInstance(dbOptions());
		
		// If this is a class doing the classifying, add to their progress.
		if ( $this->classId ) {
			Biodiv\Badge::writeBadgeProgress ( $this->schoolUser, $this->classId, $this->badge, $all_animals);
		}

		$query = $db->getQuery(true);
		$query->select("A.animal_id as id, A.species as species, A.gender as gender, A.age as age, A.number as number, O.struc as struc, O.option_name as name, OD.value as kiosk_image")
		  ->from("Animal A")
		  ->innerJoin("Options O on A.species = O.option_id")
		  ->leftJoin("OptionData OD on O.option_id = OD.option_id and OD.data_type='kioskimg'")
		  ->where("A.animal_id in (" . implode(",", $all_animals) . ")")
		  ->order("A.animal_id");

		$db->setQuery($query);
		$this->all_animals = $db->loadObjectList();
		}
		
		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);

		// Unset the animal ids as this ends the classify session
		$app->setUserState('com_biodiv.all_animal_ids', 0);
		
		if ( $this->badge > 0 ) {
			
			$this->badgeResult = Biodiv\Badge::checkJustCompleted ( $this->schoolUser, $this->classId, $this->badge );
			
		}


		// Display the view
		parent::display($tpl);
    }
}



?>