<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskMediaCarousel;

 
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
		$app = Factory::getApplication();
		$input = $app->input;

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		if ( !$this->projectId ) die ("no project id given" );

		$kioskModel = $this->getModel();

		$this->project = $kioskModel->getProjectDetails($this->projectId);

		// Set the user state so classifies just for this project
		$app->setUserState('com_biodiv.classify_only_project', 1);

		// Flag to say whether or not we're classifying the second project.
		$classifySecond = 
		$app->getUserStateFromRequest('com_biodiv.classify_second_project', 'classify_second_project', 0);

		$this->secondProject = null;
		if ( $classifySecond == 1 ) {
			// Use MammalWeb for now
			$this->secondProject = 1;
		}
		
		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$input = $app->getInput();
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}
		
		// get the url for the project image
		$this->projectImageUrl = $kioskModel->projectImageURL($this->projectId);

		$this->mediaCarousel = $kioskModel->getMediaCarousel();
		
		// Is a specific sequence requested?
		//error_log ( "getting sequence" );
		
		$this->sequenceId = null;
		$this->sequence = null;
		
		$this->sequenceId = $input->getInt('sequence_id', null);
		
		if ( $this->sequenceId == null ) {
			//error_log ( "sequence id parameter is null" );
			
			// May have asked for a second project, deafults to primary (from user request) if not
			$sequenceDetails = $kioskModel->nextSequence($this->secondProject);
			
			if ( count($sequenceDetails) > 0 ) {
				//error_log ("got details");
				$this->sequenceId = $sequenceDetails[0]['sequence_id'];
				$this->sequence = $kioskModel->getSequence ( $this->sequenceId );
				
				
			}
		}
		else {
			//error_log ( "sequence id parameter is set" );
			
			$this->sequence = $kioskModel->getSequence ( $this->sequenceId );
		}
		
		if ( $this->sequence ) {
			
			$photoId = $this->sequence->getFirstPhotoId();
			$app->setUserState('com_biodiv.photo_id', $photoId);
		}
		
		// Display the view
		parent::display($tpl);
    }
}



?>