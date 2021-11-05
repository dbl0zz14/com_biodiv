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
* Kiosk Classify top level
*
* @since 0.0.1
*/
class BioDivViewKioskLearnBirds extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		//error_log("KioskLearnBirds View: display called");
		
		
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		$app = JFactory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

		//error_log("KioskLearnBirds View: project_id = " . $this->projectId);

		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		//error_log("KioskLearnBirds View: user_key = " . $this->user_key);

		// Get the text snippets - enables multilingual
		$this->translations = getTranslations("kiosklearn");

		$this->kioskSpecies = new KioskSpecies($this->projectId);
		
		//error_log("KioskLearnBirds View: KioskSpecies object created");

		$this->maxSpeciesDisplayed = $this->kioskSpecies->getMaxSpeciesDisplayed();
		
		$this->commonBirds = array_column($this->kioskSpecies->getCommonBirds(), NULL, "id");
		$this->allBirds = array_column($this->kioskSpecies->getAllBirds(), NULL, "id");
		
		
		$this->commonBirdIds = array_keys($this->commonBirds);
		$this->allBirdIds = array_keys($this->allBirds);
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>