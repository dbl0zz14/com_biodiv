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
class BioDivViewKioskQuizStandardAudio extends JViewLegacy
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
		$this->personId = (int)userID();
		
		if ( $this->personId ) {
			$app = JFactory::getApplication();
			$input = $app->input;

			$this->projectId =
			(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
			
			//error_log ( "Project id = " . $this->projectId );

			if ( !$this->projectId ) die ("no project id given" );

			$this->project = projectDetails($this->projectId);
			
			$this->user_key = 
			$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

			if ( !$this->user_key ) {
				$this->user_key = JRequest::getString("user_key");
				$app->setUserState('com_biodiv.user_key', $this->user_key);
			}
			
			$this->quizLevel =
			$app->getUserStateFromRequest('com_biodiv.level', 'level', 0);
			
			if ( !$this->quizLevel ) $this->quizLevel = 'improver';


			
			// get the url for the project image
			$this->projectImageUrl = projectImageURL($this->projectId);
			
			$db = JDatabase::getInstance(dbOptions());
			
			// Get the topic for this quiz level
			$query = $db->getQuery(true)
					->select("OD.option_id")
					->from("OptionData OD")
					->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'topiclevel' and OD.value = '" . $this->quizLevel . "'" )
					->where("PO.project_id = " . $this->projectId);
			$db->setQuery($query); 
			
			$this->topicId = $db->loadResult();
			
			$this->topicName = codes_getName($this->topicId, 'topictran');
			
			//error_log ( "Topic = " . $this->topicName );
			
			// New topic so unset the db write flag
			$app->setUserState('com_biodiv.written', '0');
			
			
		
			$this->mediaCarousel = new MediaCarousel();
			
			// Get the gold standard sequences for this topic
			$this->sequenceIds = getTrainingSequences($this->topicId, 4);
			
			$errStr = print_r ( $this->sequenceIds, true );
			//error_log ( "Got topic sequences: " . $errStr );
			
			// Get the details including correct species
			$this->sequences = array();
			foreach ( $this->sequenceIds as $seqId ) {
				$this->sequences[] = getTrainingSequence($seqId, $this->topicId);
			}
			
			
			// And set the species lists up
			
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
		}
		//error_log ( "About to call template" );
		
		// Display the view
		parent::display($tpl);
    }
}



?>