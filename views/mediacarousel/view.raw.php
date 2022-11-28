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
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewMediaCarousel extends JViewLegacy
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
    
	$person_id or die("No person_id");

    $app = JFactory::getApplication();
	
	$this->topic_id = 
	    (int)$app->getUserStateFromRequest('com_biodiv.topic_id', 'topic_id', 0);
	
	$this->sequence_id = 
	    (int)$app->getUserStateFromRequest('com_biodiv.sequence_id', 'sequence_id', 0);
	
	// Create the classes used to generate the carousel html code
	$this->mediaCarousel = new MediaCarousel();
	
	// Get the gold standard sequences for this topic
	// NB if this is not an expert sequence the Sequence object is created but no species data is added
	$this->sequence = getTrainingSequence($this->sequence_id, $this->topic_id);
	
    // Display the view
    parent::display($tpl);
  }
}



?>