<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

 
/**
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewTrainingTopic extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  
  public function display($tpl = null) 
  {
    
	$person_id = (int)userID();
    
	if ( !$person_id ) {
		
		$app = JFactory::getApplication();
		
		$currentUri = Uri::getInstance();
		
		$loginParam = $app->input->getString('login', 0);
		
		$defaultLoginPage = 'index.php?option=com_users&view=login';
		
		if ( $loginParam ) {
			
			// assume login page has specific routing?
			$url = JRoute::_($loginParam);
			
		}
		else {
			$url = JRoute::_($defaultLoginPage.'&return='.base64_encode($currentUri));
		}
		
		$message = JText::_("COM_BIODIV_TRAINING_LOGIN_MSG");
		$app->redirect($url, $message);

    }


    $app = JFactory::getApplication();
	
	$this->inPageResults = $app->input->getInt('inpage', 0);
	
	$this->topic_id = 
	    (int)$app->getUserStateFromRequest('com_biodiv.topic_id', 'topic_id', 0);
		
	$this->topicName = codes_getName($this->topic_id, 'topictran');
	
	$this->detail = 
	    (int)$app->getUserStateFromRequest('com_biodiv.detail', 'detail', 0);
		
	// New topic so unset the db write flag
	$app->setUserState('com_biodiv.written', '0');
	
	// Get the species lists for this topic
	$this->filters = getTopicFilters($this->topic_id);
	foreach ( $this->filters as $filterId=>$filter ) {
		$this->filters[$filterId]['species'] = getSpecies ( $filterId, false );
	}
	
	// Create the classes used to generate the carousel html code
	$this->mediaCarousel = new MediaCarousel();
	
	$this->speciesCarousel = new SpeciesCarousel();
	$this->speciesCarousel->setFilters ( $this->filters );
	
	// Get the gold standard sequences for this topic
	$this->sequences = getTrainingSequences($this->topic_id, 8);
	
	// And set up the first one
	$this->currentSequence = null;
	if ( count($this->sequences) > 0 ) {
		$this->currentSequence = getTrainingSequence($this->sequences[0], $this->topic_id);
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>