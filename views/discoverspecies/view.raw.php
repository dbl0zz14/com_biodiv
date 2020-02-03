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
class BioDivViewDiscoverSpecies extends JViewLegacy
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
		
		//print("DiscoverData display called");
		
		
		
		$app = JFactory::getApplication();
		
		$this->species_id =
		(int)$app->getUserStateFromRequest('com_biodiv.species', 'species');
		$this->year =
		(int)$app->getUserStateFromRequest('com_biodiv.year', 'year');
				
		$this->data = discoverSpecies ( $this->species_id, $this->year );
		
		//print("DiscoverData data set");
		

		// Display the view
		parent::display($tpl);
    }
}



?>