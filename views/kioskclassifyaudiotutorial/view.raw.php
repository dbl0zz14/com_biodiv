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
class BioDivViewKioskClassifyAudioTutorial extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$app = JFactory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);
		
		// Set the user state so classifies just for this project
		$app->setUserState('com_biodiv.classify_only_project', 1);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);

		$this->mediaCarousel = new MediaCarousel();
		
		
		// We will use three sequences or videos.  These will be set in the project options
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->select("OD.value")->from("OptionData OD")
			->innerJoin("ProjectOptions PO on PO.option_id = OD.option_id")
			->innerJoin("Options O on PO.option_id = O.option_id and O.struc = 'kiosktutorial'")
			->where("OD.data_type = 'tutorialsequencejson' and PO.project_id = " . $this->projectId );
			
		
		$db->setQuery($query);
		$this->seqsJson = $db->loadResult();
		
		error_log ( "Sequence query complete: " . $this->seqsJson );

		$this->allSequences = json_decode ( $this->seqsJson );
		
		$errStr = print_r ( $this->allSequences, true );
		error_log ( "Tutorial sequences: " . $errStr );
		
		
		$this->sequenceError = null;
		
		// Check set up is correct.
		// This is fairly strict/prescriptive..We are using set clips where we know the species
		if ( count($this->allSequences) < 2 ) {
			$sequenceError = "Not enough sequences";
		}
		
		/*
		$cuckooId = codes_getCode( "Cuckoo", "content" );
		$wrenId = codes_getCode( "Wren", "content" );
		$chaffinchId = codes_getCode( "Chaffinch", "content" );
		$chiffchaffId = codes_getCode( "Chiffchaff", "content" );
		$treecreeperId = codes_getCode( "Treecreeper", "content" );
		$bluetitId = codes_getCode( "Blue tit", "content" );
		$woodpigeonId = codes_getCode( "Woodpigeon", "content" );
		$robinId = codes_getCode( "Robin", "content" );
		$willowWarblerId = codes_getCode( "Willow Warbler", "content" );
		
		
		$this->speciesInSeqsJson = '['.$cuckooId.','.$wrenId.','.$chaffinchId.','.$chiffchaffId.
			','.$treecreeperId.','.$chaffinchId.','.$bluetitId.','.$woodpigeonId.','.$willowWarblerId.']';
		
		error_log ( "Species in seqs json: " . $this->speciesInSeqsJson );
		*/
		
		/*
		$this->roeDeerSequence = getTrainingSequence ( $this->allSequences[0] );
		$roeDeerSpecies = $this->roeDeerSequence->getPrimarySpecies();
		if ( !in_array($roeDeerId, $roeDeerSpecies) ) {
			$sequenceError = "No roe deer in sequence " . $this->allSequences[0];
		}
		$this->roeDeerType =  $this->roeDeerSequence->getMedia();
		
		$this->nothingSequence = getTrainingSequence ( $this->allSequences[1] );
		$nothingSpecies = $this->nothingSequence->getPrimarySpecies();
		if ( !in_array($nothingId, $nothingSpecies) ) {
			$sequenceError = "No nothing in sequence " . $this->allSequences[1];
		}
		$nothingType =  $this->nothingSequence->getMedia();
		
		$this->wildBoarSequence = getTrainingSequence ( $this->allSequences[2] );
		$wildBoarSpecies = $this->wildBoarSequence->getPrimarySpecies();
		if ( !in_array($wildBoarId, $wildBoarSpecies) ) {
			$sequenceError = "No wild boar in sequence " . $this->allSequences[2];
		}
		$wildBoarType =  $this->wildBoarSequence->getMedia();
		
		$typesArray = array ( $this->roeDeerType, $nothingType, $wildBoarType );
		$this->typesInSeqsJson = json_encode($typesArray);
		
		error_log ( "Types in seqs json: " . $this->typesInSeqsJson );
		*/
		
		$this->trainingSequences = array();
		$this->feedbackBirds = array(); // as $birdName=>$kioskImage
		$seqIdsArray = array();
		$speciesIdsArray = array();
		$mediaTypesArray = array();
		
		
		foreach ( $this->allSequences as $seq ) {
			
			$nextTrainingSeq = getTrainingSequence ( $seq->sequenceId );
			$this->trainingSequences[] = $nextTrainingSeq;
			
			// Sanity check that the primary species matches the tutorial json
			$primarySpecies = $nextTrainingSeq->getPrimarySpecies();
			if ( count ( array_intersect($primarySpecies, $seq->species) ) < 1 ) {
				$sequenceError = "No matching species in " . $seq->sequenceId;
			}
			
			$seqIdsArray[] = $seq->sequenceId;
			
			$speciesIdsArray[] = $seq->species;
			
			foreach ( $seq->feedback as $feedbackId ) {
				$birdName = codes_getName($feedbackId,'contenttran');
				$image = codes_getName($feedbackId,'kioskimg');
				$this->feedbackBirds[$birdName] = $image;
			}
		
			$mediaTypesArray[] = $nextTrainingSeq->getMedia();
		}
		
		$this->seqIdsJson = json_encode($seqIdsArray);
		error_log ( "Seq ids json: " . $this->seqIdsJson );
		
		$this->speciesIdsJson = json_encode($speciesIdsArray);
		error_log ( "Species ids json: " . $this->speciesIdsJson );
		
		$this->mediaTypesInSeqsJson = json_encode($mediaTypesArray);
		error_log ( "Media types in seqs json: " . $this->mediaTypesInSeqsJson );
		
		//error_log ( "BioDivViewKioskClassifyTutorial::display about to create speciesLists" );
		
		$this->kioskSpecies = new KioskSpecies($this->projectId);
		
		$this->maxSpeciesDisplayed = $this->kioskSpecies->getMaxSpeciesDisplayed();
		
		// Truncate the common species list if required.
		$this->commonBirds = array_slice($this->kioskSpecies->getCommonBirds(), 0, $this->maxSpeciesDisplayed);
		$this->allBirds = $this->kioskSpecies->getAllBirds();
		
		$this->nothingId = $this->kioskSpecies->getNothingId();
		$this->humanId = $this->kioskSpecies->getHumanId();
		$this->dkId = $this->kioskSpecies->getDkId();
		$this->otherId = $this->kioskSpecies->getOtherId();
		
		
		error_log ("About to call parent display");
		// Display the view
		parent::display($tpl);
    }
}



?>