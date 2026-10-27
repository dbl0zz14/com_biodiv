<?php

namespace MammalWeb\Component\Biodiv\Site\View\TrainingTopic;

 
// No direct access to this file
defined('_JEXEC') or die;
 

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the training topic page
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

		$spotterModel = $this->getModel();
		
		$personId = $spotterModel->userID();
		
		$app = Factory::getApplication();
    
		if ( !$personId ) {
			
			$currentUri = Uri::getInstance();
			
			$loginParam = $app->input->getString('login', 0);
			
			$defaultLoginPage = 'index.php?option=com_users&view=login';
			
			if ( $loginParam ) {
				
				// assume login page has specific routing?
				$url = Route::_($loginParam);
				
			}
			else {
				$url = Route::_($defaultLoginPage.'&return='.base64_encode($currentUri));
			}
			
			//$message = Text::_("COM_BIODIV_TRAINING_LOGIN_MSG");
			$app->redirect($url);

		}


		$this->inPageResults = $app->input->getInt('inpage', 0);

		$this->topic_id = 
			(int)$app->getUserStateFromRequest('com_biodiv.topic_id', 'topic_id', 0);
			
		$this->topicName = codes_getName($this->topic_id, 'topictran');

		$this->detail = 
			(int)$app->getUserStateFromRequest('com_biodiv.detail', 'detail', 0);
			
		// New topic so unset the db write flag
		$app->setUserState('com_biodiv.written', '0');

		// Get the species lists for this topic
		$this->filters = $spotterModel->getTopicFilters($this->topic_id);
		foreach ( $this->filters as $filterId=>$filter ) {
			$this->filters[$filterId]['species'] = $spotterModel->getSpecies ( $filterId, false );
		}

		// Create the classes used to generate the carousel html code
		$this->mediaCarousel = $spotterModel->getMediaCarousel();

		$this->speciesCarousel = $spotterModel->getSpeciesCarousel();
		$this->speciesCarousel->setFilters ( $this->filters );

		// Get the gold standard sequences for this topic
		$this->sequences = $spotterModel->getTrainingSequences($this->topic_id, 8);

		// And set up the first one
		$this->currentSequence = null;
		if ( count($this->sequences) > 0 ) {
			$this->currentSequence = $spotterModel->getTrainingSequence($this->sequences[0], $this->topic_id);
		}

		// Display the view
		parent::display($tpl);
	}
}



?>