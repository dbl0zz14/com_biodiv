<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskClassifyProject;

 
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
 * View used as classify project page in kiosk
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
		
		$app = Factory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		if ( !$this->projectId ) die ("no project id given" );

		$kioskModel = $this->getModel();
		
		if ( !$kioskModel->userID() ) die ( "Please log in" );
		

		$this->project = $kioskModel->getProjectDetails($this->projectId);

		// Set the user state so classifies just for this project
		$app->setUserState('com_biodiv.classify_only_project', 1);

		$classifySecond = 
		$app->getUserStateFromRequest('com_biodiv.classify_second_project', 'classify_second_project', 0);

		$this->secondProject = null;
		if ( $classifySecond == 1 ) {
			// // Get secondary project or default to MammalWeb Britain
			// $db = JDatabase::getInstance(dbOptions());
			// $query = $db->getQuery(true)
					// ->select("OD.data_type as type, OD.value as value")
					// ->from("OptionData OD")
					// ->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
					// ->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'secondaryproject'")
					// ->where("PO.project_id = " . $this->projectId);
			// $db->setQuery($query); 
			
			// $secondary = $db->loadObject();
			// $this->secondProject = $secondary->value;
			
			// if ( $this->secondProject == null ) $this->secondProject = 1;
			
			$this->secondProject = $kioskModel->getSecondaryProject ( $this->projectId );
	
		}
		
		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$input = $app->getInput();
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}
		
		// Ensure there are no classification held as we are starting a new kioskclassify
		$app->setUserState('com_biodiv.all_animal_ids', 0);

		// get the url for the project image
		$this->projectImageUrl = $kioskModel->projectImageURL($this->projectId);

		$this->mediaCarousel = $kioskModel->getMediaCarousel();
		
		$this->sequence = $kioskModel->nextSequenceObject ( $this->secondProject );
		
		if ( $this->sequence ) { 
			
			$photoId = $this->sequence->getFirstPhotoId();
			$app->setUserState('com_biodiv.photo_id', $photoId);
		}
		
		$this->kioskSpecies = $kioskModel->getKioskSpecies($this->projectId);
		
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