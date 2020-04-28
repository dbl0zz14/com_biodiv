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
class BioDivViewTraining extends JViewLegacy
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
	
	// New training session so allow write to db
	$app->setUserState('com_biodiv.written', '0');
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("training");
	
	// Get training topics
	$this->topics = codes_getList('topictran');
	
	// Get this users scores
	$this->currentScores = userExpertise($person_id);
	
	// Display the view
    parent::display($tpl);
  }
}



?>