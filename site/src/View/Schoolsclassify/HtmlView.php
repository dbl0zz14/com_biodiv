<?php

namespace MammalWeb\Component\Biodiv\Site\View\SchoolsClassify;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;


 /**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the spotter classify page
 */
class HtmlView extends BaseHtmlView {
	
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		// if(!Session::checkToken()) {
			// throw new \Exception(Text::_('JINVALID_TOKEN_NOTICE'), 403);
		// }
		
		$app = Factory::getApplication();
		
		$input = $app->getInput();
		
		$clearPhoto = $input->getInt('clear', 0);
		if ( $clearPhoto ) {
			$this->photo_id = 0;
		}
		else {
			$this->photo_id =
			(int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id', 0);
		}

		// $this->self = 
		// (int)$app->getUserStateFromRequest('com_biodiv.classify_self', 'classify_self', 0);

		$this->classify_project = 
		(int)$app->getUserStateFromRequest('com_biodiv.classify_project', 'classify_project', 0);

		$this->classify_only_project = 
		(int)$app->getUserStateFromRequest('com_biodiv.classify_only_project', 'classify_only_project', 0);

		(int)$this->project_id = 
		$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

		$this->animal_ids = 
		$app->getUserStateFromRequest('com_biodiv.animal_ids', 'animal_ids', 0);

		// all_animal_ids not used in original classify mode so set to 0 here.
		$app->setUserState('com_biodiv.all_animal_ids', 0);

		$this->spotterModel = $this->getModel();

		// Check the user has access as this view can be loaded from project pages as well as Spotter status page
		$this->personId = $this->spotterModel->userID();
		
		$this->isSchoolUser = false;
		
		if ( !$this->personId ) {
		  
			$currentUri = Uri::getInstance();

			$loginParam = $app->input->getString('login', 0);

			$defaultLoginPage = Text::_("COM_BIODIV_MWSCLASSIFY_LOGIN_PAGE");

			if ( $loginParam ) {			
				// assume any passed in login page has specific routing
				$url = Route::_($loginParam);			
			}
			else {
				$url = Route::_($defaultLoginPage.'&return='.base64_encode($currentUri));
			}

			//$message = Text::_("COM_BIODIV_MWSCLASSIFY_LOG_CLASS");
			//$app->redirect($url, $message);
			$app->redirect($url);

		}
		else if ( $this->isSchoolUser = $this->spotterModel->isSchoolUser() )
		{
		
			// Check the user can access this project, if a project is specified.
			if ( $this->project_id ) {
				$fields = new \StdClass();
				$fields->project_id = $this->project_id;
				if ( !$this->spotterModel->canClassify("project", $fields) ) {
					$message = Text::_("COM_BIODIV_MWSCLASSIFY_NO_ACCESS");  //"Sorry you do not have access to classify on this project.";
					$url = "".BIODIV_ROOT."&view=mwsprojecthome";
					$url .= "&project_id=" . $this->project_id;
					$app->redirect($url);

				}
			}

			$this->projectPage = null;
			if ( $this->project_id ) {
				$projectPages = $this->spotterModel->getSingleProjectOptions ( $this->project_id, "projectpage");

				if ( count($projectPages) > 0 ) {
					$this->projectPage = $projectPages[0]["option_name"];
				}
			}

			$this->sequence = null;
			// Need to do a check here so that refresh doesn't load next sequence......
			// If there is a photo_id then get the sequence for that photo id.  If not, get a new sequence
			if(!$this->photo_id){
				
				$this->sequence = $this->spotterModel->nextSequence();
			}
			else {
				
				$this->sequence = $this->spotterModel->getSequenceDetails($this->photo_id);

				// Only check for existing classifications if we are refreshing...
				if ( $this->animal_ids ) {

					$this->animals = $this->spotterModel->getExistingClassifications ( $this->animal_ids );
					
				}
			}

			if (count($this->sequence) > 0) {
				$this->firstPhoto = $this->sequence[0];
				$this->photo_id = $this->firstPhoto["photo_id"];
			}

			$app->setUserState('com_biodiv.photo_id', $this->photo_id);
			$app->setUserState('com_biodiv.prev_photo_id', $this->photo_id);

			$this->photoDetails = null;
			// There might be nothing to classify...
			if ( count($this->sequence) > 0 ) {
				$this->photoDetails = $this->sequence[0];

				// Check for video
				$this->isVideo = $this->spotterModel->isVideo($this->photo_id);
				$this->isAudio = $this->spotterModel->isAudio($this->photo_id);

				$this->maxClassifications = 10;
				if ( $this->isAudio ) $this->maxClassifications = 20;
				 
				// Get the general location of the site to help spotters
				$site_id = $this->photoDetails['site_id'];
				$this->location = $this->spotterModel->getSiteLocation($site_id);

				// Get the filter ids and filter labels for this photo. 
				//$project_id = codes_getCode($this->my_project, 'project');

				$this->projectFilters = $this->spotterModel->getProjectFilters ( $this->project_id, $this->photo_id );

				foreach ( $this->projectFilters as $filterId=>$filter ) {
					$this->projectFilters[$filterId]['species'] = $this->spotterModel->getSpecies ( $filterId, false );
				}

				$this->allSpecies = array();
				$this->allSpecies = codes_getList ( "speciestran" );

				$this->sequenceId = $this->photoDetails['sequence_id'];
				$this->sequenceDetails = codes_getDetails($this->sequenceId, "sequence");
				$this->sequenceLength = $this->sequenceDetails['sequence_length'];
				$this->sequencePosition = $this->photoDetails['sequence_num'];
				$this->sequenceStartPhoto = $this->spotterModel->photoSequenceStart($this->photo_id);
				if($this->sequenceLength>0){
					$this->sequenceProgress = round($this->sequencePosition*100.0/$this->sequenceLength);
				}
				else{
					$this->sequenceProgress = 0;
				}

				//$this->invertimage = Text::_("COM_BIODIV_MWSCLASSIFY_INVERT_IMAGE") . " <span class='fa fa-adjust'/>";
				//$this->sequenceinfo = " <span class='fa fa-info'/>";
				//$this->showmap = Text::_("COM_BIODIV_MWSCLASSIFY_SHOW_MAP") . " <span class='fa fa-map-marker'/>";
				//$this->nextseq = Text::_("COM_BIODIV_MWSCLASSIFY_NEXT_SEQ") . " <span class='fa fa-arrow-circle-right'/>";

				$this->classifyInputs = $this->spotterModel->getClassifyInputs();

				$this->eggsId = codes_getCode('Eggs', 'species');
				$this->nestId = codes_getCode('Nest', 'species');
				
				$this->mediaCarousel = $this->spotterModel->getMediaCarousel();
				$this->speciesCarousel = $this->spotterModel->getSpeciesCarousel();
				$this->speciesCarousel->setFilters ( $this->projectFilters );
				$this->sequenceObj = $this->spotterModel->getSequence( $this->sequenceId );

			}
		}

		// Display the view
		parent::display($tpl);
    }
}



?>