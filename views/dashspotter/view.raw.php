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
* Ajax HTML View class for the Spotter status on User Dashboard 
*
* @since 0.0.1
*/
class BioDivViewDashSpotter extends JViewLegacy
{
 
  
   /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  public function display($tpl = null) 
  {
    error_log ( "Spotter view display called" );
		
	$this->personId = (int)userID();
	
	if ( $this->personId ) {
		
		
		$app = JFactory::getApplication();	
		
		// Spotter stats, repeat of part of Status view		
		$this->statRows = getSpotterStatistics();
		
		// Get this users quiz expertise
		// Get training topics
		$features = array();
		$features['restriction'] = "seq > 0";
		$topics = codes_getList( 'topictran', $features );
		$this->topicIds = array_column($topics, 0);
		
		// Get this users scores, avoid Gold standard or non-displayed ones when displaying
		$this->scores = userExpertise($this->personId);
		
		$scoreTopicIds = array_column($this->scores, 'topic_id');
		
		$this->numTopicScores = count(array_intersect($scoreTopicIds, $this->topicIds));
		
		$this->numMissingScores = count($this->topicIds) - $this->numTopicScores;
		
		
	}
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>