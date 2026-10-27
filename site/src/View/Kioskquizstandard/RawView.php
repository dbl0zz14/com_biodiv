<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskQuizStandard;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View used as kiosk intro page
 */
class RawView extends BaseHtmlView {

    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		
		$kioskModel = $this->getModel();

		// Assign data to the view
		$this->personId = $kioskModel->userID();
		
		if ( $this->personId ) {
			$app = Factory::getApplication();
			$input = $app->input;

			$this->projectId =
			(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
			
			//error_log ( "Project id = " . $this->projectId );

			if ( !$this->projectId ) die ("no project id given" );

			$this->project = $kioskModel->getProjectDetails($this->projectId);
			
			// get the url for the project image
			$this->projectImageUrl = $kioskModel->projectImageURL($this->projectId);
				
				
			// Set the user state so classifies just for this project
			//$app->setUserState('com_biodiv.classify_only_project', 1);

			$this->user_key = 
			$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

			if ( !$this->user_key ) {
				$this->user_key = $input->getString("user_key");
				$app->setUserState('com_biodiv.user_key', $this->user_key);
			}
			
			$this->topicId = $input->getInt('topic', 0);
			
			if ( !$this->topicId ) {
		
				$this->quizLevel =
				$app->getUserStateFromRequest('com_biodiv.level', 'level', 0);
				
				//error_log ( "Quiz level = " . $this->quizLevel );

				if ( !$this->quizLevel ) $this->quizLevel = 'improver';


				
				// $db = JDatabase::getInstance(dbOptions());
				
				// // Get the topic for this quiz level
				// $query = $db->getQuery(true)
						// ->select("OD.option_id")
						// ->from("OptionData OD")
						// ->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'topiclevel' and OD.value = '" . $this->quizLevel . "'" )
						// ->where("PO.project_id = " . $this->projectId);
				// $db->setQuery($query); 
				
				// $this->topicId = $db->loadResult();
				
				$this->topicId = $kioskModel->getTopic ( $this->projectId, $this->quizLevel );
			}
			
			$this->topicName = codes_getName($this->topicId, 'topictran');
			
			// New topic so unset the db write flag
			$app->setUserState('com_biodiv.written', '0');
			
			
		
			$this->mediaCarousel = $kioskModel->getMediaCarousel();
			
			// Get the gold standard sequences for this topic
			$this->sequenceIds = $kioskModel->getTrainingSequences($this->topicId, 4);
			
			// Get the details including correct species
			$this->sequences = array();
			foreach ( $this->sequenceIds as $seqId ) {
				$this->sequences[] = $kioskModel->getTrainingSequence($seqId, $this->topicId);
			}
			
			
			// And set the species lists up
			
			$this->kioskSpecies = $kioskModel->getKioskSpecies($this->projectId, $this->topicId);
			
			$this->maxSpeciesDisplayed = $this->kioskSpecies->getMaxSpeciesDisplayed();
			
			$this->speciesButtonCount = 0;
			
			// Truncate the common species list if required.
			$commonM = $this->kioskSpecies->getCommonMammals();
			if ( count($commonM) > 0 ) {
				$this->commonMammals = array_slice($commonM, 0, $this->maxSpeciesDisplayed);
				$this->speciesButtonCount += 1;
			}
			else {
				$this->commonMammals = null;
			}
			$allM = $this->kioskSpecies->getAllMammals();
			if ( count($allM) > 0 ) {
				$this->allMammals = $allM;
			}
			else {
				$this->allMammals = null;
			}
			
			$commonB = $this->kioskSpecies->getCommonBirds();
			if ( count($commonB) > 0 ) {
				$this->commonBirds = array_slice($commonB, 0, $this->maxSpeciesDisplayed);
				$this->speciesButtonCount += 1;
			}
			else {
				$this->commonBirds = null;
			}
			$allB = $this->kioskSpecies->getAllBirds();
			if ( count($allB) > 0 ) {
				$this->allBirds = $allB;
			}
			else {
				$this->allBirds = null;
			}
			
			$commonI = $this->kioskSpecies->getCommonInverts();
			if ( count($commonI) > 0 ) {
				$this->commonInverts = array_slice($commonI, 0, $this->maxSpeciesDisplayed);
				$this->speciesButtonCount += 1;
			}
			else {
				$this->commonInverts = null;
			}
			$allI = $this->kioskSpecies->getAllInverts();
			if ( count($allI) > 0 ) {
				$this->allInverts = $allI;
			}
			else {
				$this->allInverts = null;
			}
			
			// $this->commonBirds = array_slice($this->kioskSpecies->getCommonBirds(), 0, $this->maxSpeciesDisplayed);
			// $this->allBirds = $this->kioskSpecies->getAllBirds();
			// $this->commonInverts = array_slice($this->kioskSpecies->getCommonInverts(), 0, $this->maxSpeciesDisplayed);
			// $this->allInverts = $this->kioskSpecies->getAllInverts();
			
			$this->nothingId = $this->kioskSpecies->getNothingId();
			$this->humanId = $this->kioskSpecies->getHumanId();
			$this->dkId = $this->kioskSpecies->getDkId();
			$this->otherId = $this->kioskSpecies->getOtherId();
		}
		
		// Display the view
		parent::display($tpl);
    }
}



?>