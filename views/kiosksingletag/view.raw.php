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
class BioDivViewKioskSingletag extends JViewLegacy
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
	  $this->person_id = (int)userID();
	  
	  if ( $this->person_id ) {
		  $app = JFactory::getApplication();
		  
		  //$this->photo_id = (int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id');

		  $this->animal_id =
			(int)$app->getUserStateFromRequest('com_biodiv.animal_id', 'animal_id');
			
			
		  if ( $this->animal_id ) {

			$db = JDatabase::getInstance(dbOptions());

			$query = $db->getQuery(true);
			$query->select("animal_id, species, gender, age, number")
			  ->from("Animal")
			  ->where("animal_id = ".$this->animal_id);

			$db->setQuery($query);
			$this->animal = $db->loadObject();
		  }
	  }
	  // Display the view
	  parent::display($tpl);
    }
}



?>