<?php

namespace MammalWeb\Component\Biodiv\Site\View\MediaCarousel;

 
/// No direct access to this file
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
 * View for project animals data, used in charts
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
		  
		$spotterModel = $this->getModel();

		$personId = $spotterModel->userID();

		$personId or die("Please log in");

		$app = Factory::getApplication();

		$topicId = 
			(int)$app->getUserStateFromRequest('com_biodiv.topic_id', 'topic_id', 0);

		$sequenceId = 
			(int)$app->getUserStateFromRequest('com_biodiv.sequence_id', 'sequence_id', 0);

		$this->invertButton = 
			$app->input->getBool('invert');

		// Create the classes used to generate the carousel html code
		$this->mediaCarousel = $spotterModel->getMediaCarousel();

		// Get the gold standard sequences for this topic
		// NB if this is not an expert sequence the Sequence object is created but no species data is added
		$this->sequence = $spotterModel->getTrainingSequence($sequenceId, $topicId);

		// Display the view
		parent::display($tpl);
	}
}



?>