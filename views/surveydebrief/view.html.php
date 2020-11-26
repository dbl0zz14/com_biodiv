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
class BioDivViewSurveyDebrief extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  
  public function display($tpl = null) 
  {
    

    // Get all the survey text snippets
	$this->translations = getTranslations("survey");
	
	$app = JFactory::getApplication();
    
	$this->surveyId = 
	    (int)$app->getUserStateFromRequest('com_biodiv.survey', 'survey', 0);
		
	error_log ( "survey id = " . $this->surveyId );
	
	$biodivSurvey = new BiodivSurvey ( $this->surveyId );
	
	$this->debriefArticle = $biodivSurvey->getDebriefArticle();
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>