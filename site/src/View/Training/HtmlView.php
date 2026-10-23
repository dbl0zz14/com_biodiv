<?php

namespace MammalWeb\Component\Biodiv\Site\View\Training;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;


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
		$this->spotterModel = $this->getModel();
		
		$personId = $this->spotterModel->userID();
    
		$personId or die("Please log in");

		$app = Factory::getApplication();

		// New training session so allow write to db
		$app->setUserState('com_biodiv.written', '0');

		// Get training topics
		$this->topics = $this->spotterModel->getTrainingTopics ();
		
		// Get this users scores
		$scores = $this->spotterModel->userExpertise($personId);

		$topics = array_column($scores, 'topic_id');

		$this->currentScores = array_combine($topics, $scores);

		// Display the view
		parent::display($tpl);
	}
}



?>