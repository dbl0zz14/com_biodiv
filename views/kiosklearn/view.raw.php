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
class BioDivViewKioskLearn extends JViewLegacy
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

		$this->kioskSpecies = new KioskSpecies($this->projectId);
		
		$this->maxSpeciesDisplayed = $this->kioskSpecies->getMaxSpeciesDisplayed();
		
		// Get these keyed on id
		$this->commonMammals = array_column($this->kioskSpecies->getCommonMammals(), NULL, "id");
		$this->allMammals = array_column($this->kioskSpecies->getAllMammals(), NULL, "id");
		$this->commonBirds = array_column($this->kioskSpecies->getCommonBirds(), NULL, "id");
		$this->allBirds = array_column($this->kioskSpecies->getAllBirds(), NULL, "id");
		
		$this->commonMammalIds = array_keys($this->commonMammals);
		$this->commonBirdIds = array_keys($this->commonBirds);
		$this->allMammalIds = array_keys($this->allMammals);
		$this->allBirdIds = array_keys($this->allBirds);
		
		// Need one long list to display, will filter using other lists
		$this->allSpecies = $this->commonMammals + $this->commonBirds;
		
		$keys = array_column($this->allSpecies, 'name');
		array_multisort($keys, SORT_ASC, $this->allSpecies);
		
		//$errStr = print_r ( $this->allSpecies , true );
		//error_log ( "KioskLearn, all species after sort: " . $errStr );
		
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>