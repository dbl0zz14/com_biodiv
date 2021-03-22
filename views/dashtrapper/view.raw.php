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
class BioDivViewDashTrapper extends JViewLegacy
{
 
  
   /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  public function display($tpl = null) 
  {
    error_log ( "DashTrapper view display called" );
		
	$this->personId = (int)userID();
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("dashtrapper");
	
	
	if ( $this->personId ) {
		
		
		$app = JFactory::getApplication();	
		
		error_log ( "DashTrapper calling getTrapperStatistics" );
		
		// Spotter stats, repeat of part of Status view		
		$this->statRows = getTrapperStatistics();
		
		//$errMsg = print_r ( $this->statRows, true );
		//error_log ( "DashTrapper trapper statRows = " . $errMsg );
		
	}
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>