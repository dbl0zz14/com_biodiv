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
class BioDivViewTrainingResultsAjax extends JViewLegacy
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
	
	$this->helper = new TrainingHelper ();
	
	$this->helper->calculateScores();
	
	$this->helper->writeScores();
		
    // Display the view
    parent::display($tpl);
  }
}



?>