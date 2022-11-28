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
* Start the kiosk
*
* @since 0.0.1
*/
class BioDivViewKioskStart extends JViewLegacy
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

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		//error_log("Kiosk View: user_key = " . $this->user_key);
		
		// Make sure there are no stray animals
		$app->setUserState('com_biodiv.all_animal_ids', 0);

		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);
		//error_log ( "Project image url: " . $this->projectImageUrl );

		// Get the logos to be displayed for this project
		$this->logos = getLogos($this->projectId);
		
		$this->birdsOnly = getSetting("birds_only") == "yes";

		
		// Display the view
		parent::display($tpl);
    }
}



?>