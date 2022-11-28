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
class BioDivViewKioskClassifyTutorial extends JViewLegacy
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
			->where("OD.data_type = 'expertsequencejson' and PO.project_id = " . $this->projectId );
			
		
		$db->setQuery($query);
		$this->seqsJson = $db->loadResult();
		
		$this->allSequences = json_decode ( $this->seqsJson );
		
		$this->sequenceError = null;
		
		// Check set up is correct.
		// This is fairly strict/prescriptive, needs to be Roe deer, nothing, wild boar, will prob need to change this for birds..
		if ( count($this->allSequences) < 3 ) {
			$sequenceError = "Not enough sequences";
		}
		
		$redDeerId = codes_getCode( "Red deer", "content" );
		$roeDeerId = codes_getCode( "Roe deer", "content" );
		$wildBoarId = codes_getCode( "Wild boar or feral pig", "content" );
		$nothingId = codes_getCode( "Nothing", "content" );
		
		
		$this->speciesInSeqsJson = '['.$redDeerId.','.$roeDeerId.','.$wildBoarId.','.$nothingId.']';
		
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