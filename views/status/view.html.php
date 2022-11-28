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
class BioDivViewStatus extends JViewLegacy
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

    $this->status = getSpotterStatistics();
	
	// Set the photo to zero on load and the classify option back to default 0
	$app = JFactory::getApplication();
    $app->setUserState('com_biodiv.photo_id', 0);
	$app->setUserState('com_biodiv.classify_only_project', 0);
    $app->setUserState('com_biodiv.classify_project', 0);
    $app->setUserState('com_biodiv.classify_self', 0);
    $app->setUserState('com_biodiv.project_id', 0);
	$app->setUserState('com_biodiv.animal_ids', 0);
    

	// call new biodiv.php function instead of myProjects()
	// Changed back argument to check redirect issue  
	$this->projects = mySpottingProjects( true );
	$this->mylikes = getLikes(1);
	
	// Do we want to pop up surveys?
	$this->survey = null;
	if ( getSetting("survey") === "yes" ) {
		
		$this->surveyId = BiodivSurvey::triggerSurvey($person_id, 'status');
		
		if ( $this->surveyId ) {
			$this->survey = new BiodivSurvey ( $this->surveyId );
			
			$this->surveyHook =  $this->survey->getHook();
			$this->surveyIntro =  $this->survey->getIntroText();
			$this->participantInfo =  $this->survey->getParticipantInfo();
			$this->consentHeading =  $this->survey->getConsentHeading();
			$this->consentInstructions =  $this->survey->getConsentInstructions();
			$this->consentText =  $this->survey->getConsentText();
			
			// NB Need to sort this for follow up questionnaires
			$this->requireSurveyConsent =  $this->survey->requireConsent();
		}
		
		
	}
	
		
    // Display the view
    parent::display($tpl);
  }
}



?>