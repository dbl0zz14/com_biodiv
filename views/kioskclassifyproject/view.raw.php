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
class BioDivViewKioskClassifyProject extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "BioDivViewKioskClassifyProject::display called" );
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		$app = JFactory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		error_log ( "Project id = " . $this->projectId );

		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);
		
		// Set the user state so classifies just for this project
		$app->setUserState('com_biodiv.classify_only_project', 1);

		$classifySecond = 
		$app->getUserStateFromRequest('com_biodiv.classify_second_project', 'classify_second_project', 0);

		error_log ( "classify second = " . $classifySecond );

		$this->secondProject = null;
		if ( $classifySecond == 1 ) {
			// Use MammalWeb for now
			$this->secondProject = 1;
		}
		
		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}
		
		// Ensure there are no classification held as we are starting a new kioskclassify
		$app->setUserState('com_biodiv.all_animal_ids', 0);

		// Get the text snippets - enables multilingual
		$this->translations = getTranslations("kioskclassifyproject");
		
		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);

		$this->mediaCarousel = new MediaCarousel();
		
		// Get a sequence - when first loaded, ajax call used for subsequent sequences
		// Note if second project null the user request project id is used ir primary project.
		$sequenceDetails = nextSequence( $this->secondProject );
		
		$errStr = print_r ( $sequenceDetails, true );
		error_log ( "KioskClassifyProject first sequence details: " . $errStr );
		
		$this->sequenceId = null;
		$this->sequence = null;
		
		if ( count($sequenceDetails) > 0 ) {
			$this->sequenceId = $sequenceDetails[0]['sequence_id'];
			$this->sequence = new Sequence ( $this->sequenceId );
		}
		
		error_log ( "BioDivViewKioskClassifyProject::display about to create speciesLists" );
		
		$this->kioskSpecies = new KioskSpecies($this->projectId);
		
		$this->maxSpeciesDisplayed = $this->kioskSpecies->getMaxSpeciesDisplayed();
		
		// Truncate the common species list if required.
		$this->commonMammals = array_slice($this->kioskSpecies->getCommonMammals(), 0, $this->maxSpeciesDisplayed);
		$this->allMammals = $this->kioskSpecies->getAllMammals();
		$this->commonBirds = array_slice($this->kioskSpecies->getCommonBirds(), 0, $this->maxSpeciesDisplayed);
		$this->allBirds = $this->kioskSpecies->getAllBirds();
		
		$this->nothingId = $this->kioskSpecies->getNothingId();
		$this->humanId = $this->kioskSpecies->getHumanId();
		$this->dkId = $this->kioskSpecies->getDkId();
		$this->otherId = $this->kioskSpecies->getOtherId();
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>