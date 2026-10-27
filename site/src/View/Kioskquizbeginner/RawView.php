<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskQuizBeginner;

 
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
 * View used as kiosk beginner quiz page
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
		// Assign data to the view
		$app = Factory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		if ( !$this->projectId ) die ("no project id given" );

		$kioskModel = $this->getModel();

		$this->project = $kioskModel->getProjectDetails($this->projectId);

		// Set the user state so classifies just for this project
		$app->setUserState('com_biodiv.classify_only_project', 1);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$input = $app->getInput();
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		// get the url for the project image
		$this->projectImageUrl = $kioskModel->projectImageURL($this->projectId);
		
		// Get the topic for this project beginner quiz
		$this->quiz = $kioskModel->getBeginnerQuiz ( $this->projectId );
		
		// $db = JDatabase::getInstance(dbOptions());
		
		// $query = $db->getQuery(true)
				// ->select("OD.option_id")
				// ->from("OptionData OD")
				// ->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'topiclevel' and OD.value = 'beginner'")
				// ->where("PO.project_id = " . $this->projectId);
		// $db->setQuery($query); 
		
		// $this->topicId = $db->loadResult();
		
		// // use the BeginnerQuiz class to get the sequences and options
		
		// $this->quiz = new Biodiv\BeginnerQuiz($this->topicId, 5);
		
		$this->sequenceIds = $this->quiz->getSequenceIds();
		$this->sequences = $this->quiz->getSequences();
		

		$this->mediaCarousel = $kioskModel->getMediaCarousel();
		
		// Display the view
		parent::display($tpl);
    }
}



?>