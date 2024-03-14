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
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewSurvey extends JViewLegacy
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
    
	$this->surveyId = 
	    (int)$app->getUserStateFromRequest('com_biodiv.survey', 'survey', 0);
		
	$this->haveConsent = BiodivSurvey::haveConsent ( $person_id, $this->surveyId );
	
	if ( $this->haveConsent ) {
		// Use the survey object to set up the questions
		$survey = new BiodivSurvey ( $this->surveyId );
		
		$this->sections = $survey->getSections();
		
		$this->questions = $survey->getQuestions();
	}
	
    // Display the view
    parent::display($tpl);
  }
}



?>